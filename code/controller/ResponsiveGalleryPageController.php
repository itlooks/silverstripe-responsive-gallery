<?php
/**
 * @author      Christoph Hauff <hauff@itlooks.de>
 * @copyright   Copyright (c) 2017 Christoph Hauff
 * @version     3.3.0
 * @license     Licensed under the MIT license: https://opensource.org/licenses/MIT
 */

/**
 * Controller class of ResponsiveGalleryPage
 *
 * @package silverstripe-responsive-gallery
 * @subpackage model
 */
class ResponsiveGalleryPage_Controller extends Page_Controller
{
    /**
     * Init method of this controller
     */
    public function init() {
        parent::init();
        ResponsiveGalleryExtension::getRequirements($this->ID);
    }
}
