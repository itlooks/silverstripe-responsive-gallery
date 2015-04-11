<?php

/**
 * Responsive gallery page, to be extended by ResponsiveGalleryExtension class
 *
 * @package responsive-gallery
 * @author Christoph Hauff <hauff@itlooks.de>
 */
class ResponsiveGalleryPage extends Page {

    /**
     * @var string
     */
	  private static $description = 'Adds a customizable image gallery page.';
	  
    /**
     * @var string
     */
    private static $icon = "responsive-gallery/images/sitetree/ResponsiveGalleryPage.png";
}

class ResponsiveGalleryPage_Controller extends Page_Controller {

    /**
     * Get requirements of a single responsive gallery object, id is needed to create unique html boxes to handle
     * multiple galleries on a single page.
     * {@inheritDoc}
     */
	  public function init() {
          parent::init();
          ResponsiveGalleryExtension::getRequirements($this->ID);
	  }
}
