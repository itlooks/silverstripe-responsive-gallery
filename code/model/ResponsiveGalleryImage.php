<?php
/**
 * @author      Christoph Hauff <hauff@itlooks.de>
 * @copyright   Copyright (c) 2017 Christoph Hauff
 * @version     3.3.0
 * @license     Licensed under the MIT license: https://opensource.org/licenses/MIT
 */

/**
 * Class to handle image objects of this gallery page type
 *
 * @package silverstripe-responsive-gallery
 * @subpackage model
 */
class ResponsiveGalleryImage extends DataObject
{
    /**
     * @var array
     */
    private static $db = array(
        'Title' => 'Varchar',
        'Text' => 'Text'
    );

    /**
     * @var array
     */
    private static $has_one = array(
        'GalleryImage' => 'Image'
    );

    /**
     * @var array
     */
    public static $extraStatics = array(
        'belongs_many_many' => array(
            'GalleryPage' => 'ResponsiveGalleryExtension'
        )
    );

    /**
     * @var array
     */
    private static $summary_fields = array(
        'Title',
        'Text.Summary'
    );

    /**
     * Add cms form for image data object
     *
     * @return FieldList
     */
    public function getCMSFields() {
        $oFields = parent::getCMSFields();
        $oFields->removeByName('SortOrder');
        $oFields->removeByName('GalleryPageID');
        $oFields->removeByName('Title');
        $oFields->removeByName('Text');

        $oFields->addFieldsToTab(
            'Root.Main',
            array(
                UploadField::create(
                    'GalleryImage',
                    _t('ResponsiveGallery.GALLERY_IMAGE', 'Gallery Image')
                ),
                TextField::create(
                    'Title',
                    _t('ResponsiveGallery.IMAGE_TITLE', 'Image Title')
                ),
                TextareaField::create(
                    'Text',
                    _t('ResponsiveGallery.IMAGE_TEXT', 'Image Text')
                )
            )
        );

        return $oFields;
    }
}
