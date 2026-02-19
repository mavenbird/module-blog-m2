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

namespace Mavenbird\Blog\Model\ResourceModel\Like;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Mavenbird\Blog\Model\Like;
use Mavenbird\Blog\Model\ResourceModel\Like as LikeResourceModel;

/**
 * Class Collection
 * @package Mavenbird\Blog\Model\ResourceModel\Like
 */
class Collection extends AbstractCollection
{
    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init(Like::class, LikeResourceModel::class);
    }
}
