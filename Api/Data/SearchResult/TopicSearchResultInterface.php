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

namespace Mavenbird\Blog\Api\Data\SearchResult;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface TopicSearchResultInterface
 * @package Mavenbird\Blog\Api\Data\SearchResult
 */
interface TopicSearchResultInterface extends SearchResultsInterface
{
    /**
     * @return \Mavenbird\Blog\Api\Data\TopicInterface[]
     */
    public function getItems();

    /**
     * @param \Mavenbird\Blog\Api\Data\TopicInterface[] $items
     * @return $this
     */
    public function setItems(?array $items = null);
}
