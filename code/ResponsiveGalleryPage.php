<?php

class ResponsiveGalleryPage extends Page {

    /**
     * @var array
     */
    public static $db = array();

    /**
     * @var array
     */
    public static $has_one = array();

    /**
     * @var array
     */
    public static $has_many = array();

    /**
     * @var string
     */
    static $singular_name = 'Galerie Seite';

    /**
     * @var string
     */
    static $plural_name = 'Galerie Seiten';

    /**
     * @var string
     */
    public static $description = 'Seite mit einer Bildergalerie.';

    /**
     * @var string
     */
    private static $icon = 'mysite/images/sitetree/ResponsiveGalleryPage.png';

    /**
     * Standard function to generate cms-form
     * 
     * @return FieldSet
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();
        return $fields;
    }
}

class ResponsiveGalleryPage_Controller extends Page_Controller {

    /**
     * Get requirements of a single responsive gallery object, id is needed to create unique html boxes to handle
     *  multiple galleries on a single page. 
     */
	  public function init() {
          parent::init();
          ResponsiveGalleryExtension::getRequirements($this->ID);
	  }
}
