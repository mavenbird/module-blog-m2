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

namespace Mavenbird\Blog\Block\Sidebar;

use Mavenbird\Blog\Block\Frontend;
use Mavenbird\Blog\Model\ResourceModel\Post\Collection;

/**
 * Class MostView
 * @package Mavenbird\Blog\Block\Sidebar
 */
class MostView extends Frontend
{
    /**
     * @return Collection
     */
    public function getMostViewPosts()
    {
        $collection = $this->helperData->getPostList();
        $collection->getSelect()
            ->joinLeft(
                ['traffic' => $collection->getTable('mavenbird_blog_post_traffic')],
                'main_table.post_id=traffic.post_id',
                'numbers_view'
            )
            ->order('numbers_view DESC')
            ->limit((int)$this->helperData->getNumberMostviewPosts() ?: 4);

        return $collection;
    }

    /**
     * @return Collection
     */
    public function getRecentPost()
    {
        $collection = $this->helperData->getPostList();
        $collection->getSelect()
            ->limit((int)$this->helperData->getNumberRecentPosts() ?: 4);

        return $collection;
    }
}
