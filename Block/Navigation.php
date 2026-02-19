<?php
/**
 * Mavenbird
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mavenbird.com license that is
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

namespace Mavenbird\Blog\Block;

use Magento\Framework\View\Element\Html\Links;

/**
 * Class Navigation
 * @package Mavenbird\Blog\Block
 */
class Navigation extends Links
{
    /**
     * {@inheritdoc}
     */
    public function getLinks()
    {
        $links = parent::getLinks();

        usort($links, [$this, "compare"]);

        return $links;
    }

    /**
     * @param $firstLink
     * @param $secondLink
     *
     * @return bool
     */
    private function compare($firstLink, $secondLink)
    {
        return $firstLink->getData('sortOrder') - $secondLink->getData('sortOrder');
    }
}
