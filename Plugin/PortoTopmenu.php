<?php
/**
 * Mavenbird
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mavenbird.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mavenbird.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mavenbird
 * @package     Mavenbird_Blog
 * @copyright   Copyright (c) Mavenbird (https://www.mavenbird.com/)
 * @license     https://www.mavenbird.com/LICENSE.txt
 */

namespace Mavenbird\Blog\Plugin;

use Mavenbird\Blog\Block\Category\Menu;
use Mavenbird\Blog\Helper\Data;

/**
 * Class PortoTopmenu
 * @package Mavenbird\Blog\Plugin
 */
class PortoTopmenu
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * PortoTopmenu constructor.
     *
     * @param Data $helper
     */
    public function __construct(
        Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * @param \Smartwave\Megamenu\Block\Topmenu $topmenu
     * @param $html
     *
     * @return string
     */
    public function afterGetMegamenuHtml(\Smartwave\Megamenu\Block\Topmenu $topmenu, $html)
    {
        if ($this->helper->isEnabled() && $this->helper->getToplinks()) {
            $blogHtml = $topmenu->getLayout()->createBlock(Menu::class)
                ->setTemplate('Mavenbird_Blog::category/topPortoMenu.phtml')->toHtml();

            return $html . $blogHtml;
        }

        return $html;
    }
}
