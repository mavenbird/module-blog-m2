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

namespace Mavenbird\Blog\Block\Category;

use Exception;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Phrase;
use Mavenbird\Blog\Block\Adminhtml\Category\Tree;
use Mavenbird\Blog\Block\Frontend;
use Mavenbird\Blog\Helper\Data;

/**
 * Class Widget
 * @package Mavenbird\Blog\Block\Category
 */
class Widget extends Frontend
{
    /**
     * @return mixed|null
     */
    public function getTree()
    {
        try {
            $tree = ObjectManager::getInstance()->create(Tree::class);
            $tree = $tree->getTree(null, $this->store->getStore()->getId());

            return $tree;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * @param array $tree
     *
     * @return Phrase|string
     */
    public function getCategoryTreeHtml($tree)
    {
        if (!$tree) {
            return __('No Categories.');
        }

        $html = '';
        foreach ($tree as $value) {
            if (!$value) {
                continue;
            }
            if ($value['enabled']) {
                $level = count(explode('/', ($value['path'])));
                $hasChild = isset($value['children']) && $level < 4;
                $html .= '<ul class="block-content menu-categories category-level'
                    . $level . '" style="margin-bottom:0px;margin-top:8px;">';
                $html .= '<li class="category-item">';
                $html .= $hasChild ? '<i class="fa fa-plus-square-o mb-blog-expand-tree-' . $level . '"></i>' : '';
                $html .= '<a class="list-categories" href="' . $this->getCategoryUrl($value['url']) . '">';
                $html .= '<i class="fa fa-folder-open-o">&nbsp;&nbsp;</i>';
                $html .= ucfirst($value['text']) . '</a>';
                $html .= $hasChild ? $this->getCategoryTreeHtml($value['children']) : '';
                $html .= '</li>';
                $html .= '</ul>';
            }
        }

        return $html;
    }

    /**
     * @param string $category
     *
     * @return string
     */
    public function getCategoryUrl($category)
    {
        return $this->helperData->getBlogUrl($category, Data::TYPE_CATEGORY);
    }
}
