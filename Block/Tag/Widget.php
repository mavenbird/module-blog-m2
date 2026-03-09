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

namespace Mavenbird\Blog\Block\Tag;

use Exception;
use Magento\Framework\Exception\NoSuchEntityException;
use Mavenbird\Blog\Block\Frontend;
use Mavenbird\Blog\Helper\Data;
use Mavenbird\Blog\Model\ResourceModel\Post\Collection;
use Mavenbird\Blog\Model\ResourceModel\Author\Collection as AuthorCollection;
use Mavenbird\Blog\Model\ResourceModel\Category\Collection as CategoryCollection;
use Mavenbird\Blog\Model\ResourceModel\Tag\Collection as TagCollection;
use Mavenbird\Blog\Model\ResourceModel\Topic\Collection as TopicCollection;
use Mavenbird\Blog\Model\Tag;

/**
 * Class Widget
 * @package Mavenbird\Blog\Block\Tag
 */
class Widget extends Frontend
{
    protected $_tagList;
    protected $tagPostCounts = [];
    protected $maxPostCount = 0;

    public function getTagList()
    {
        if (!$this->_tagList) {
            $this->_tagList = $this->helperData->getObjectList(Data::TYPE_TAG);
            $this->prepareTagCounts();
        }

        return $this->_tagList;
    }

    protected function prepareTagCounts()
    {
        foreach ($this->_tagList as $tag) {
            $collection = $this->helperData->getPostCollection(Data::TYPE_TAG, $tag->getId());
            $count = $collection->getSize();

            $this->tagPostCounts[$tag->getId()] = $count;

            if ($count > $this->maxPostCount) {
                $this->maxPostCount = $count;
            }
        }
    }

    public function getTagSize($tag)
    {
        $countTagPost = $this->tagPostCounts[$tag->getId()] ?? 0;

        if ($this->maxPostCount > 1 && $countTagPost > 1) {
            $maxSize = 22;
            $size = $maxSize * $countTagPost / $this->maxPostCount;

            return round($size) + 8;
        }

        return 8;
    }

    public function getTagUrl($tag)
    {
        return $this->helperData->getBlogUrl($tag, Data::TYPE_TAG);
    }
}
