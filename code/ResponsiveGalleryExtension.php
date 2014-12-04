<?php

class ResponsiveGalleryExtension extends DataExtension {

    /**
     * @var array
     */
    public static $has_one = array(
        'UploadFolder' => 'Folder'
    );

    /**
     * @var array
     */
    public static $many_many = array(
        'GalleryImages' => 'ResponsiveGalleryImage'
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
     * @return FieldSet
     */
    public function updateCMSFields(FieldList $fields) {
        parent::updateCMSFields($fields);

        // create assets/responsive-gallery folder if not exists
        Folder::find_or_make('responsive-gallery');
        $folderObject = Folder::find('responsive-gallery');

        if ($this->owner->ID > 0) {

            $gridFieldConfig = GridFieldConfig_RelationEditor::create()->addComponents(
                new GridFieldEditButton(),
                new GridFieldDeleteAction(),
                new GridFieldDetailForm(),
                new GridFieldBulkUpload('GalleryImage')
            );

            $gridFieldConfig->getComponentByType('GridFieldBulkUpload')
                ->setUfSetup('setFolderName', $this->getUploadFolder())
                ->setUfConfig('sequentialUploads', true);

            $fields->addFieldsToTab(
                'Root.Gallery',
                array(
                    $folderField = new TreeDropdownField(
                        'UploadFolderID',
                        _t('ResponsiveGallery.FOLDER_LABEL', 'Target Folder'),
                        'Folder'
                    ),
                    new GridField(
                        "GalleryImages",
                        _t('ResponsiveGallery.IMAGES_LABEL', 'Images'),
                        $this->sortedGalleryImages(),
                        $gridFieldConfig
                    )
                )
            );
            
            $folderField->setTreeBaseID($folderObject->ID);
        }

        return $fields;
    }

    /**
     * Get target upload folder filename
     *
     * @return string
     */
    public function getUploadFolder() {
        if ($this->useDefaultUploadFolder()) {
            return "responsive-gallery";
        }
        
        return $this->getSettedFolderName();
    }

    /**
     * Should uploaded pictures be moved into default folder "responsive-gallery"
     * Returns true, if pictures should be uploaded into default folder
     *
     * @return bool
     */
    public function useDefaultUploadFolder() {
        return ($this->owner->UploadFolder()->ID == 0);
    }

    /**
     * Get the folder name of configured upload folder
     *
     * @return string
     */
    public function getSettedFolderName() {
        return "responsive-gallery/".$this->owner->UploadFolder()->getTitle();
    }

    /**
     * Get a sorted set of image objects
     *
     * @return \DataObjectList
     */
    public function sortedGalleryImages() {
        $galleryImages = $this->owner->GalleryImages();

        return $galleryImages;
    }
}
