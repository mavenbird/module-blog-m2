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

namespace Mavenbird\Blog\Block\Widget;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Widget\Block\BlockInterface;
use Mavenbird\Blog\Block\Frontend;
use Mavenbird\Blog\Helper\Data;
use Mavenbird\Blog\Model\ResourceModel\Post\Collection;

/**
 * Class Posts
 * @package Mavenbird\Blog\Block\Widget
 */
class Posts extends Frontend implements BlockInterface
{
    /**
     * @var string
     */
    protected $_template = "widget/posts.phtml";

    /**
     * @return Collection
     * @throws NoSuchEntityException
     */
    public function getCollection()
    {
        if ($this->hasData('show_type') && $this->getData('show_type') === 'category') {
            $collection = $this->helperData->getObjectByParam($this->getData('category_id'), null, Data::TYPE_CATEGORY)
                ->getSelectedPostsCollection();
            $this->helperData->addStoreFilter($collection);
        } else {
            $collection = $this->helperData->getPostList();
        }

        $collection->setPageSize($this->getData('post_count'));

        return $collection;
    }

    /**
     * @return Data
     */
    public function getHelperData()
    {
        return $this->helperData;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->getData('title');
    }

    /**
     * @param $code
     *
     * @return string
     */
    public function getBlogUrl($code)
    {
        return $this->helperData->getBlogUrl($code);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->helperData->checkHyvaTheme()) {
            $this->setTemplate('Mavenbird_Blog::hyva/widget/posts.phtml');
        }
        return parent::_toHtml();
    }

    /**
     * Check if post info should be shown
     *
     * @return bool
     */
    public function showPostInfo()
    {
        return (bool)$this->getData('show_post_info');
    }

    /**
     * Check if short description should be shown
     *
     * @return bool
     */
    public function showShortDescription()
    {
        return (bool)$this->getData('show_short_description');
    }

    /**
     * Check if read more link should be shown
     *
     * @return bool
     */
    public function showReadMore()
    {
        return (bool)$this->getData('show_read_more');
    }
}