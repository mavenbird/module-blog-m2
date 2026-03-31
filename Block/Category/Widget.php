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
use Magento\Framework\App\RequestInterface;

/**
 * Class Widget
 * @package Mavenbird\Blog\Block\Category
 */
class Widget extends Frontend
{
    protected $request;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Mavenbird\Blog\Model\CommentFactory $commentFactory,
        \Mavenbird\Blog\Model\LikeFactory $likeFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\Registry $coreRegistry,
        \Mavenbird\Blog\Helper\Data $helperData,
        \Magento\Customer\Model\Url $customerUrl,
        \Mavenbird\Blog\Model\CategoryFactory $categoryFactory,
        \Mavenbird\Blog\Model\PostFactory $postFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Mavenbird\Blog\Model\PostLikeFactory $postLikeFactory,
        \Mavenbird\Blog\Block\Adminhtml\Post\Edit\Tab\Renderer\Category $category,
        \Mavenbird\Blog\Block\Adminhtml\Post\Edit\Tab\Renderer\Topic $topic,
        \Mavenbird\Blog\Block\Adminhtml\Post\Edit\Tab\Renderer\Tag $tag,
        \Magento\Framework\View\Design\Theme\ThemeProviderInterface $themeProvider,
        \Magento\Framework\Encryption\EncryptorInterface $enc,
        \Mavenbird\Blog\Model\Config\Source\AuthorStatus $authorStatus,
        RequestInterface $request, // <-- your dependency
        array $data = []
    ) {
        $this->request = $request;

        parent::__construct(
            $context,
            $filterProvider,
            $commentFactory,
            $likeFactory,
            $customerRepository,
            $coreRegistry,
            $helperData,
            $customerUrl,
            $categoryFactory,
            $postFactory,
            $dateTime,
            $postLikeFactory,
            $category,
            $topic,
            $tag,
            $themeProvider,
            $enc,
            $authorStatus,
            $data
        );
    }

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

    public function getCategoryTreeHtmlLuma(array $tree, bool $accordion = false, int $level = 0): string
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
            $html .= '<li class="category-item">';
            if ($level > 0) {
                $html .= '<i class="fa-regular fa-circle"></i>';
            }
            if ($accordion && $hasChild) {
                $html .= '<span class="mb-category-toggle">
                        <i class="fa-solid fa-plus"></i>
                      </span>';
            }
            $html .= '<a href="' . $this->getCategoryUrl($value['url']) . '" class="list-categories">';
            $html .= ucfirst($value['text']) . '</a>';
            if ($hasChild) {
                $childHtml = $this->getCategoryTreeHtmlLuma($enabledChildren, $accordion, $level + 1);

                if ($accordion) {
                    $childHtml = str_replace(
                        '<ul class="category-children">',
                        '<ul class="category-children" style="display:none;">',
                        $childHtml
                    );
                }
                $html .= $childHtml;
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

    /**
     * @return bool
     */
    public function isCategorySidebarOneColumn()
    {
        $fullActionName = $this->request->getFullActionName();

        if ($fullActionName === 'mbblog_post_index') {
            return $this->helperData->getPostViewPageConfig('blog_list_layout') === '1column';
        }

        if ($fullActionName === 'mbblog_post_view') {
            return $this->helperData->getPostViewPageConfig('blog_view_layout') === '1column';
        }

        return $this->helperData->getSidebarConfig('sidebar_left_right') === '1column';
    }
}
