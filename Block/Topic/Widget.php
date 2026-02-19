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

namespace Mavenbird\Blog\Block\Topic;

use Exception;
use Mavenbird\Blog\Model\ResourceModel\Author\Collection as AuthorCollection;
use Mavenbird\Blog\Model\ResourceModel\Category\Collection as CategoryCollection;
use Mavenbird\Blog\Model\ResourceModel\Post\Collection;
use Mavenbird\Blog\Model\ResourceModel\Tag\Collection as TagCollection;
use Mavenbird\Blog\Model\ResourceModel\Topic\Collection as TopicCollection;
use Mavenbird\Blog\Block\Frontend;
use Mavenbird\Blog\Helper\Data;
use Mavenbird\Blog\Model\Topic;

/**
 * Class Widget
 * @package Mavenbird\Blog\Block\Topic
 */
class Widget extends Frontend
{
    /**
     * @return AuthorCollection|CategoryCollection|Collection|TagCollection|TopicCollection|null
     */
    public function getTopicList()
    {
        try {
            return $this->helperData->getObjectList(Data::TYPE_TOPIC);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * @param Topic $topic
     *
     * @return string
     */
    public function getTopicUrl($topic)
    {
        return $this->helperData->getBlogUrl($topic, Data::TYPE_TOPIC);
    }
}
