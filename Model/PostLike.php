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

/**
 * Class PostLike
 * @package Mavenbird\Blog\Model
 */
class PostLike extends AbstractModel
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'mavenbird_blog_post_like';

    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'mavenbird_blog_post_like';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'mavenbird_blog_post_like';

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
        $this->_init(ResourceModel\PostLike::class);
    }

    /**
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}
