<?php

class ResponsiveGalleryExtension extends DataExtension {

    /**
     * @var array
     */
    public static $db = array(
        'Source' => 'Varchar(2)',
    );

    /**
     * @var array
     */
    public static $has_one = array(
        'UploadFolder' => 'Folder',
        'SourceFolder' => 'Folder',
    );

    /**
     * @var array
     */
    public static $many_many = array(
        'GalleryImages' => 'ResponsiveGalleryImage',
    );

    /**
     * @var array
     */
    static $many_many_extraFields = array( 
	      'GalleryImages' => array( 
		        'SortOrder' => "Int",
	      )
    );

    /**
     * Handle requirements of an Responsive Gallery object 
     *
     * @param $id id of calling dataObject or Page
     */
    public static function getRequirements($id) {
        Requirements::css("responsive-gallery/thirdparty/blueimp/Gallery/css/blueimp-gallery.min.css");
        Requirements::css("responsive-gallery/thirdparty/blueimp/Gallery/css/blueimp-gallery-indicator.css");
        Requirements::css("responsive-gallery/thirdparty/blueimp/Gallery/css/blueimp-gallery-video.css");
        Requirements::css("responsive-gallery/css/responsive-gallery.css");
        Requirements::javascript("responsive-gallery/thirdparty/blueimp/Gallery/js/blueimp-helper.js");
        Requirements::javascript("responsive-gallery/thirdparty/blueimp/Gallery/js/blueimp-gallery.js");
        Requirements::javascript("responsive-gallery/thirdparty/blueimp/Gallery/js/blueimp-gallery-fullscreen.js");
        Requirements::javascript("responsive-gallery/thirdparty/blueimp/Gallery/js/blueimp-gallery-indicator.js");
        Requirements::javascript("responsive-gallery/thirdparty/blueimp/Gallery/js/jquery.blueimp-gallery.min.js");
        Requirements::customScript(<<<JS
blueimp.Gallery(
    document.getElementById('links{$id}').getElementsByTagName('a'),
    {
        container: '#blueimp-gallery-carousel{$id}',
        carousel: true
    }
);
JS
          ,
          $id
        );
    }

    /**
     * Standard function to generate cms-form
     *
     * @param \FieldSet
     * 
     * @return \FieldSet
     */
    public function updateCMSFields(FieldList $fields) {
        parent::updateCMSFields($fields);

        // create assets/responsive-gallery folder if not exists
        Folder::find_or_make('responsive-gallery');

        $aFields = array();

        $aFields[] = new HeaderField(
            "Choose your desired image source"
        );

        $aFields[] = new OptionsetField(
            "Source",
            "Source",
            array(
                "sf" => "Source Folder",
                "dl" => "Data List",
            ),
            "sf"
        );

        if ($this->owner->ID > 0) {
            switch($this->owner->Source) {
                case "dl":
                    $oGridFieldConfig = GridFieldConfig_RelationEditor::create()->addComponents(
                        new GridFieldEditButton(),
                        new GridFieldDeleteAction(),
                        new GridFieldDetailForm(),
                        new GridFieldBulkUpload('GalleryImage'),
                        new GridFieldSortableRows('SortOrder')
                    );

                    $oGridFieldConfig->getComponentByType('GridFieldBulkUpload')
                        ->setUfSetup('setFolderName', $this->getUploadFolder())
                        ->setUfConfig('sequentialUploads', true);

                    $aFields[] = new HeaderField(
                        _t(
                            'ResponsiveGallery.DATALIST_HEADER',
                            'Create your image list'
                        )
                    );

                    $aFields[] = LiteralField::create(
                        "DataListInfo",
                        '<div class="field"><p>'.
                        _t(
                            'ResponsiveGallery.DATALIST_INFO',
                            'You can select images from files or upload images and add them to your customized image list. '.
                            'Use "Target Folder" field to select a default target folder for your image uploads.'
                        ).
                        '</p></div>'
                    );

                    $aFields[] = new TreeDropdownField(
                        'UploadFolderID',
                        _t('ResponsiveGallery.UPLOADFOLDER_LABEL', 'Target Folder'),
                        'Folder'
                    );

                    $aFields[] = new GridField(
                        "GalleryImages",
                        _t('ResponsiveGallery.IMAGES_LABEL', 'Images'),
                        $this->sortedGalleryImages(),
                        $oGridFieldConfig
                    );

                    break;

                case "sf":
                    $iImageCount = $this->countImages();

                    $aFields[] = new HeaderField(
                        _t(
                            'ResponsiveGallery.SOURCEFOLDER_HEADER',
                            'Select source folder of gallery'
                        )
                    );

                    $aFields[] = new TreeDropdownField(
                        'SourceFolderID',
                        _t('ResponsiveGallery.SOURCEFOLDER_LABEL', 'Source Folder'),
                        'Folder'
                    );
                    
                    if($this->isSourcefolderSelected()) {
                        $aFields[] = LiteralField::create(
                            "ImageCountInfo",
                            '<div class="field">'.
                            '<p class="info-message">'.
                            _t(
                                'ResponsiveGallery.IMAGECOUNTINFO',
                                'There are {imageCount} images in your selected folder.',
                                'The number of images in this gallery',
                                array('imageCount' => $iImageCount)
                            ).
                            '</p></div>'
                        );
                    } else {
                        $aFields[] = LiteralField::create(
                            "NoSelectedFolderInfo",
                            '<div class="field">'.
                            '<p><span class="info-message">'.
                            _t(
                                'ResponsiveGallery.NOSELECTEDFOLDERINFO',
                                'Please select a folder that contains the images to be displayed in this gallery.'
                            ).
                            '</p></div>'
                        );
                    }

                    break;
                
                default:
                    $aFields[] = LiteralField::create(
                        "SelectSourceInfo",
                        '<div class="field">'.
                        '<p><span class="info-message">'.
                        _t(
                            'ResponsiveGallery.SELECTSOURCEINFO_HEADER',
                            'Please select your desired image source type in field above.'
                        ).
                        '</span><br/>'.
                        _t(
                            'ResponsiveGallery.SELECTSOURCEINFO',
                            'Then click on save button below, to be able to configure this gallery.'
                        ).
                        '</p></div>'
                    );
            }
        }
        
        $fields->findOrMakeTab('Root.Gallery');
        $fields->addFieldsToTab(
            'Root.Gallery',
            $aFields
        );

        return $fields;
    }

    /**
     * Get a sorted set of image objects
     *
     * @return \DataList | \ManyManyList
     */
    public function getImages() {
        if($this->owner->Source == "sf") {
            return DataObject::get("Image", "ParentID = '".$this->owner->SourceFolderID."'");
        } else {
            return $this->owner->GalleryImages();
        }
    }

    /**
     * Return true, if an image source folder is selected, false otherwise
     *
     * @return bool
     */
    public function isSourcefolderSelected() {
        return ($this->owner->SourceFolderID > 0);
    }

    /**
     * Return the number of items in this gallery
     *
     * @return int 
     */
    public function countImages() {
        return $this->getImages()->count();
    }

    /**
     * Get target upload folder filename
     *
     * @return string
     */
    public function getUploadFolder() {
        if ($this->owner->UploadFolder()->ID == 0) {
            return "responsive-gallery";
        }

        return str_replace(
            "assets/",
            "",
            $this->owner->UploadFolder()->Filename
        );
    }
}
