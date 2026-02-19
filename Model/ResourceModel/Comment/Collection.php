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

namespace Mavenbird\Blog\Model\ResourceModel\Comment;

use Magento\Sales\Model\ResourceModel\Collection\AbstractCollection;
use Mavenbird\Blog\Api\Data\SearchResult\CommentSearchResultInterface;
use Mavenbird\Blog\Model\Comment;

/**
 * Class Collection
 * @package Mavenbird\Blog\Model\ResourceModel\Comment
 */
class Collection extends AbstractCollection implements CommentSearchResultInterface
{
    /**
     * @var string
     */
    protected $_idFieldName = 'comment_id';

    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init(Comment::class, \Mavenbird\Blog\Model\ResourceModel\Comment::class);
    }
}
