<?php

class ResponsiveGalleryImage extends DataObject {

    private static $db = array(
        'Title' => 'Varchar',
        'Text' => 'Text'
    );

    private static $has_one = array(
        'GalleryImage' => 'Image'
    );

    public static $extraStatics = array(
        'belongs_many_many' => array(
            'GalleryPage' => 'ResponsiveGalleryExtension'
        )
    );

    private static $summary_fields = array(
        'Title',
        'Text.Summary'
    );

    public function getCMSFields() {
        $fields = parent::getCMSFields();
        $fields->removeByName('SortOrder');
        $fields->removeByName('GalleryPageID');
        $fields->removeByName('Title');
        $fields->removeByName('Text');

        $fields->addFieldsToTab(
            'Root.Main',
            array(
                new UploadField(
                    'GalleryImage',
                    _t('ResponsiveGallery.GALLERY_IMAGE', 'Gallery Image')
                ),
                new TextField(
                    'Title',
                    _t('ResponsiveGallery.IMAGE_TITLE', 'Image Title')
                ),
                new TextareaField(
                    'Text',
                    _t('ResponsiveGallery.IMAGE_TEXT', 'Image Text')
                )
            )
        );

        return $fields;
    }
}
