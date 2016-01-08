<?php

class ResponsiveGalleryExtension extends DataExtension
{

    public static $db = array(
        'Source' => 'Varchar(2)',
        'ShowAllComponents' => 'Varchar(1)',
        'DisplayCarousel' => 'Boolean',
        'DisplayCarouselTitle' => 'Boolean',
        'DisplayCarouselPrevNext' => 'Boolean',
        'DisplayCarouselPlayPause' => 'Boolean',
        'DisplayCarouselIndicator' => 'Boolean',
        'DisplayModal' => 'Boolean',
        'DisplayModalTitle' => 'Boolean',
        'DisplayModalPrevNext' => 'Boolean',
        'DisplayModalPlayPause' => 'Boolean',
        'DisplayModalIndicator' => 'Boolean',
    );

    public static $has_one = array(
        'UploadFolder' => 'Folder',
        'SourceFolder' => 'Folder',
    );

    public static $many_many = array(
        'GalleryImages' => 'ResponsiveGalleryImage',
    );

    public static $many_many_extraFields = array(
          'GalleryImages' => array(
                'SortOrder' => "Int",
          )
    );

    /**
     * Handle requirements of a Responsive Gallery object 
     *
     * @param $id id of calling dataObject or Page
     */
    public static function getRequirements($iId)
    {
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
    document.getElementById('links{$iId}').getElementsByTagName('a'),
    {
        container: '#blueimp-gallery-carousel{$iId}',
        carousel: true
    }
);
JS
          ,
          $iId
        );
    }

    public function updateCMSFields(\FieldList $oFields)
    {
        Folder::find_or_make('responsive-gallery');
        $aGalleryImagesFields = array();

        if ($this->owner->ID > 0) {
            $oFields->addFieldsToTab(
                'Root.'._t('ResponsiveGalleryExtension.GALLERYIMAGES_TAB', 'Gallery Images'),
                $this->getFieldsForImagesTab()
            );

            $oFields->addFieldsToTab(
                'Root.'._t('ResponsiveGalleryExtension.GALLERYSETTINGS_TAB', 'Gallery Settings'),
                $this->getFieldsForSettingsTab()
            );
        }
    }

    /**
     * Returns array of fields to be added to Gallery Images Tab
     *
     * @return array
     */
    public function getFieldsForImagesTab()
    {
        $aFields[] = new HeaderField(
            _t(
                'ResponsiveGalleryExtension.SOURCE_HEADER',
                'Choose your desired image source'
            )
        );

        $aFields[] = new OptionsetField(
            "Source",
            _t(
                'ResponsiveGalleryExtension.SOURCE_LABEL',
                'Source'
            ),
            array(
                "sf" => _t(
                    'ResponsiveGalleryExtension.SOURCEFOLDER_LABEL',
                    'Source Folder'
                ),
                "dl" => _t(
                    'ResponsiveGalleryExtension.DATALIST_LABEL',
                    'Data List'
                ),
            ),
            "sf"
        );

        switch ($this->owner->Source) {
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
                        'ResponsiveGalleryExtension.DATALIST_HEADER',
                        'Create your image list'
                    )
                );

                $aFields[] = LiteralField::create(
                    "DataListInfo",
                    '<div class="field"><p>'.
                    _t(
                        'ResponsiveGalleryExtension.DATALIST_INFO',
                        'You can select images from files or upload images and add them to your customized image list. '.
                        'Use "Target Folder" field to select a default target folder for your image uploads.'
                    ).
                    '</p></div>'
                );

                $aFields[] = new TreeDropdownField(
                    'UploadFolderID',
                    _t('ResponsiveGalleryExtension.UPLOADFOLDER_LABEL', 'Target Folder'),
                    'Folder'
                );

                $aFields[] = new GridField(
                    "GalleryImages",
                    _t('ResponsiveGalleryExtension.IMAGES_LABEL', 'Images'),
                    $this->getImages(),
                    $oGridFieldConfig
                );

                break;

            case "sf":
                $iImageCount = $this->countImages();

                $aFields[] = new HeaderField(
                    _t(
                        'ResponsiveGalleryExtension.SOURCEFOLDER_HEADER',
                        'Select source folder of gallery'
                    )
                );

                $aFields[] = new TreeDropdownField(
                    'SourceFolderID',
                    _t('ResponsiveGalleryExtension.SOURCEFOLDER_LABEL', 'Source Folder'),
                    'Folder'
                );
                
                if ($this->isSourcefolderSelected()) {
                    $aFields[] = LiteralField::create(
                        "ImageCountInfo",
                        '<div class="field">'.
                        '<p class="info-message">'.
                        _t(
                            'ResponsiveGalleryExtension.IMAGECOUNTINFO',
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
                            'ResponsiveGalleryExtension.NOSELECTEDFOLDER_INFO',
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
                        'ResponsiveGalleryExtension.SELECTSOURCEINFO_HEADER',
                        'Please select your desired image source type in field above.'
                    ).
                    '</span><br/>'.
                    _t(
                        'ResponsiveGalleryExtension.SELECTSOURCE_INFO',
                        'Then click on save button below, to be able to configure this gallery.'
                    ).
                    '</p></div>'
                );
        }

        return $aFields;
    }
    
    /**
     * Returns array of fields to be added to Gallery Settings Tab
     *
     * @return array
     */
    public function getFieldsForSettingsTab()
    {
        $aFields = array(
            new OptionsetField(
                'ShowAllComponents',
                _t(
                    'ResponsiveGalleryExtension.COMPONENTSETTINGS_LABEL',
                    'Display Components'
                ),
                $source = array(
                    "1" => _t(
                      'ResponsiveGalleryExtension.SHOWALLCOMPONENTS_LABEL',
                      'Display all components'
                    ),
                    "0" => _t(
                      'ResponsiveGalleryExtension.SHOWCUSTOMIZEDCOMPONENTS_LABEL',
                      'Customized ...'
                    ),
                ),
               $value = "1"
            ),
        );

        $aFields[] = DisplayLogicWrapper::create(
            new LiteralField(
                'EmptySettingsWarning',
                '<p class="message warning">' . _t('ResponsiveGalleryExtension.EMPTY_SETTINGS_WARNING',
                'Warning: You should choose at least one option.')
                . '</p>'
            )
        )->setName('EmptySettingsWarningWrapper')
            ->hideIf("ShowAllComponents")->isEqualTo('1')
            ->orIf("DisplayCarousel")->isChecked()
            ->orIf("DisplayModal")->isChecked()
            ->end();

        $aFields[] = DisplayLogicWrapper::create(
            HeaderField::create(
                '<img src="'.RESPONSIVE_GALLERY_PATH.'/images/settings/show-carousel.png" title="Display Carousel?"> '.
                _t(
                    'ResponsiveGalleryExtension.SETTINGS_CAROUSEL_HEADER',
                    'Carousel settings'
                )
            )
        )->setName('SettingsCarouselHeaderWrapper')
            ->displayIf("ShowAllComponents")->isEqualTo('0')->end();

        $aFields[] = CheckboxField::create(
            'DisplayCarousel',
            '<strong>'.
            _t(
                'ResponsiveGalleryExtension.SETTINGS_SHOWCAROUSEL_LABEL',
                'Display Carousel'
            ).
            '</strong>'
        )->displayIf("ShowAllComponents")->isEqualTo('0')->end();

        $aFields[] = CheckboxField::create(
            'DisplayCarouselTitle',
            _t(
                'ResponsiveGalleryExtension.SETTINGS_SHOWCAROUSELTITLE_LABEL',
                'Display Carousel Title'
            )
        )->displayIf("ShowAllComponents")->isEqualTo('0')
            ->andIf('DisplayCarousel')->isChecked()
            ->end();

        $aFields[] = CheckboxField::create(
            'DisplayCarouselPrevNext',
            _t(
                'ResponsiveGalleryExtension.SETTINGS_SHOWCAROUSELPREVNEXT_LABEL',
                'Display Carousel Prev/Next Buttons'
            )
        )->displayIf("ShowAllComponents")->isEqualTo('0')
            ->andIf('DisplayCarousel')->isChecked()
            ->end();

        $aFields[] = CheckboxField::create(
            'DisplayCarouselPlayPause',
            _t(
                'ResponsiveGalleryExtension.SETTINGS_SHOWCAROUSELPLAYPAUSE_LABEL',
                'Display Carousel Play/Pause Button'
            )
        )->displayIf("ShowAllComponents")->isEqualTo('0')
            ->andIf('DisplayCarousel')->isChecked()
            ->end();

        $aFields[] = CheckboxField::create(
            'DisplayCarouselIndicator',
            _t(
                'ResponsiveGalleryExtension.SETTINGS_SHOWCAROUSELINDICATOR_LABEL',
                'Display Indicator List in Carousel Footer'
            )
        )->displayIf("ShowAllComponents")->isEqualTo('0')
            ->andIf('DisplayCarousel')->isChecked()
            ->end();

        $aFields[] = DisplayLogicWrapper::create(
            HeaderField::create(
                '<img src="'.RESPONSIVE_GALLERY_PATH.'/images/settings/show-imagelist.png" title="Display image list and enable fullscreen mode?"> '.
                _t(
                    'ResponsiveGalleryExtension.SETTINGS_IMAGELIST_HEADER',
                    'Image list and fullscreen settings'
                )
            )
        )->setName('SettingsModalHeaderWrapper')
            ->displayIf("ShowAllComponents")->isEqualTo('0')->end();

        $aFields[] = CheckboxField::create(
            'DisplayModal',
            '<strong>'.
            _t(
                'ResponsiveGalleryExtension.SETTINGS_SHOWMODAL_LABEL',
                'Display Modal'
            ).
            '</strong>'
        )->displayIf("ShowAllComponents")->isEqualTo('0')->end();

        $aFields[] = CheckboxField::create(
            'DisplayModalTitle',
            _t(
                'ResponsiveGalleryExtension.SETTINGS_SHOWMODALTITLE_LABEL',
                'Display Modal Title'
            )
        )->displayIf("ShowAllComponents")->isEqualTo('0')
            ->andIf('DisplayModal')->isChecked()
            ->end();

        $aFields[] = CheckboxField::create(
            'DisplayModalPrevNext',
            _t(
                'ResponsiveGalleryExtension.SETTINGS_SHOWMODALPREVNEXT_LABEL',
                'Display Modal Prev/Next Buttons'
            )
        )->displayIf("ShowAllComponents")->isEqualTo('0')
            ->andIf('DisplayModal')->isChecked()
            ->end();

        $aFields[] = CheckboxField::create(
            'DisplayModalPlayPause',
            _t(
                'ResponsiveGalleryExtension.SETTINGS_SHOWMODALPLAYPAUSE_LABEL',
                'Display Modal Play/Pause Button'
            )
        )->displayIf("ShowAllComponents")->isEqualTo('0')
            ->andIf('DisplayModal')->isChecked()
            ->end();

        $aFields[] = CheckboxField::create(
            'DisplayModalIndicator',
            _t(
                'ResponsiveGalleryExtension.SETTINGS_SHOWMODALINDICATOR_LABEL',
                'Display Modal Play/Pause Button'
            )
        )->displayIf("ShowAllComponents")->isEqualTo('0')
            ->andIf('DisplayModal')->isChecked()
            ->end();
        
        return $aFields;
    }

    /**
     * Get a sorted set of image objects
     *
     * @return \DataList|\ManyManyList
     */
    public function getImages()
    {
        if ($this->owner->Source == "sf") {
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
    public function isSourcefolderSelected()
    {
        return ($this->owner->SourceFolderID > 0);
    }

    /**
     * Return the number of items in this gallery
     *
     * @return int 
     */
    public function countImages()
    {
        return $this->getImages()->count();
    }

    /**
     * Get target upload folder filename
     *
     * @return string
     */
    public function getUploadFolder()
    {
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
