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

use Magento\Framework\Exception\LocalizedException;
use Mavenbird\Blog\Block\Category\Menu;
use Mavenbird\Blog\Helper\Data;

/**
 * Class Topmenu
 * @package Mavenbird\Blog\Plugin
 */
class Topmenu
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * Topmenu constructor.
     *
     * @param Data $helper
     */
    public function __construct(
        Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Theme\Block\Html\Topmenu $subject
     * @param $html
     *
     * @return string
     * @throws LocalizedException
     */
    public function afterGetHtml(
        \Magento\Theme\Block\Html\Topmenu $subject,
        $html
    ) {
        if ($this->helper->isEnabled() && $this->helper->getBlogConfig('display/toplinks')) {
            $blogHtml = $subject->getLayout()->createBlock(Menu::class)
                ->setTemplate('Mavenbird_Blog::category/topmenu.phtml')->toHtml();

            return $html . $blogHtml;
        }

        return $html;
    }
}
