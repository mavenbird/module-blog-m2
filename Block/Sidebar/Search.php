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

use Magento\Framework\Exception\NoSuchEntityException;
use Mavenbird\Blog\Block\Frontend;
use Mavenbird\Blog\Helper\Data;

/**
 * Class Search
 * @package Mavenbird\Blog\Block\Sidebar
 */
class Search extends Frontend
{
    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getSearchBlogData()
    {
        $result = [];
        $posts = $this->helperData->getPostList();
        $limitDesc = (int)$this->getSearchDescription();
        if (!empty($posts)) {
            foreach ($posts as $item) {
                $shortDescription = ($item->getShortDescription() && $limitDesc > 0) ?
                    $item->getShortDescription() : '';
                if (strlen($shortDescription) > $limitDesc) {
                    $shortDescription = mb_substr($shortDescription, 0, $limitDesc, 'UTF-8') . '...';
                }

                $result[] = [
                    'value' => $item->getName(),
                    'url' => $item->getUrl(),
                    'image' => $this->resizeImage($item->getImage(), '100x'),
                    'desc' => $shortDescription
                ];
            }
        }

        return Data::jsonEncode($result);
    }

    public function getSearchShowImage($storeId = null)
    {
        return $this->helperData->getShowImage($storeId);
    }

    public function getSearchMinChars($storeId = null)
    {
        return $this->helperData->getMinChars($storeId);
    }

    public function getSearchLimit($storeId = null)
    {
        return $this->helperData->getSearchLimit($storeId);
    }

    public function getSearchDescription($storeId = null)
    {
        return $this->helperData->getSearchDescription($storeId);
    }
}
