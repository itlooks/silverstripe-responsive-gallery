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
	   * An array of actions that can be accessed via a request. Each array element should be an action name, and the
	   * permissions or conditions required to allow the user to access it.
	   *
	   * <code>
	   * array (
	   *     'action', // anyone can access this action
	   *     'action' => true, // same as above
	   *     'action' => 'ADMIN', // you must have ADMIN permissions to access this action
	   *     'action' => '->checkAction' // you can only access this action if $this->checkAction() returns true
	   * );
	   * </code>
	   *
	   * @var array
	   */
	  public static $allowed_actions = array ();

	  public function init() {
          parent::init();
          ResponsiveGalleryExtension::getRequirements($this->ID);
	  }
}
