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

namespace Mavenbird\Blog\Model;

use Magento\Framework\Model\AbstractModel;
use Mavenbird\Blog\Helper\Data;

/**
 * Class PostLike
 * @package Mavenbird\Blog\Model
 */
class PostHistory extends AbstractModel
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'mavenbird_blog_post_history';

    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'mavenbird_blog_post_history';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'mavenbird_blog_post_history';

    /**
     * @var string
     */
    protected $_idFieldName = 'like_id';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\PostHistory::class);
    }

    /**
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @param $postId
     *
     * @return int
     */
    public function getSumPostHistory($postId)
    {
        return $this->getCollection()->addFieldToFilter('post_id', $postId)->count();
    }

    /**
     * @param $postId
     */
    public function removeFirstHistory($postId)
    {
        $this->getCollection()->addFieldToFilter('post_id', $postId)->getFirstItem()->delete();
    }

    /**
     * @return array|mixed
     */
    public function getProductsPosition()
    {
        if (!$this->getId()) {
            return [];
        }
        $data = [];
        foreach (Data::jsonDecode($this->getProductIds()) as $key => $value) {
            $data[$key] = $value['position'];
        }

        return $data;
    }
}
