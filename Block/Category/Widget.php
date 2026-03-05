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

public function getCategoryTreeHtml(array $tree, bool $accordion = false, int $level = 0): string
{
    if (!$tree) {
        return '';
    }

    $ulClass = $level === 0 ? 'menu-categories' : 'category-children';

    $html = '<ul class="' . $ulClass . '">';

    foreach ($tree as $value) {

        if (!$value || empty($value['enabled'])) {
            continue;
        }

        $enabledChildren = array_filter(
            $value['children'] ?? [],
            fn($child) => !empty($child['enabled'])
        );

        $hasChild = !empty($enabledChildren);

        $html .= '<li class="category-item" ' . ($hasChild ? 'x-data="{open:false}"' : '') . '>';

        if ($level > 0) {
            $html .= '<i class="fa-regular fa-circle"></i>';
        }

        if ($accordion && $hasChild) {

            $html .= '<span class="mb-category-toggle"
                        @click.prevent="open = !open">
                        <i class="fa-solid"
                           :class="open ? \'fa-minus\' : \'fa-plus\'"></i>
                      </span>';
        }

        $html .= '<a href="' . $this->getCategoryUrl($value['url']) . '" class="list-categories">';
        $html .= ucfirst($value['text']) . '</a>';

        if ($hasChild) {

            if ($accordion) {

                $html .= '<div x-show="open"
                               x-transition
                               style="display:none;">';

                $html .= $this->getCategoryTreeHtml($enabledChildren, $accordion, $level + 1);

                $html .= '</div>';

            } else {

                $html .= $this->getCategoryTreeHtml($enabledChildren, $accordion, $level + 1);

            }
        }

        $html .= '</li>';
    }

    $html .= '</ul>';

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

    /**
     * @return bool
     */
    public function isCategoryAccordionEnabled()
    {
        return (bool)$this->helperData->getConfigValue('blog/display/category_accordion');
    }
}
