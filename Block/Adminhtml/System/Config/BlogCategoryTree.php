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

namespace Mavenbird\Blog\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Tree\Node;
use Mavenbird\Blog\Model\CategoryFactory as BlogCategoryFactory;
use Mavenbird\Blog\Model\ResourceModel\Category\TreeFactory as BlogCategoryTreeFactory;

/**
 * Class BlogCategoryTree
 * @package Mavenbird\Blog\Block\Adminhtml\System\Config
 */
class BlogCategoryTree extends Field
{
    /**
     * @var BlogCategoryTreeFactory
     */
    protected $blogCategoryTreeFactory;

    /**
     * @var BlogCategoryFactory
     */
    protected $_categoryFactory;

    /**
     * @var array
     */
    protected $_categories = [];

    /**
     * BlogCategoryTree constructor.
     *
     * @param Context $context
     * @param BlogCategoryTreeFactory $blogCategoryTreeFactory
     * @param BlogCategoryFactory $categoryFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        BlogCategoryTreeFactory $blogCategoryTreeFactory,
        BlogCategoryFactory $categoryFactory,
        array $data = []
    ) {
        $this->blogCategoryTreeFactory = $blogCategoryTreeFactory;
        $this->_categoryFactory = $categoryFactory;
        parent::__construct($context, $data);
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        try {
            // Log element details for debugging (matches instruction's requirement to check actual element name)
            $logFile = BP . '/var/log/featured_categories.log';
            $logMessage = date('Y-m-d H:i:s') . " Element name: " . $element->getName() . ", HTML ID: " . $element->getHtmlId() . ", Current value: " . $element->getValue() . PHP_EOL;
            file_put_contents($logFile, $logMessage, FILE_APPEND);
            
            /** @var \Mavenbird\Blog\Model\ResourceModel\Category\Tree $categoryTree */
            $categoryTree = $this->blogCategoryTreeFactory->create();
            $categoryTree->load(); // Load the tree first
            $categoryTree->addCollectionData(null, true, [], true, false);
            
            // Load selected categories to ensure we have them all
            $selectedArray = !empty($element->getValue()) ? explode(',', $element->getValue()) : [];
            foreach ($selectedArray as $categoryId) {
                $category = $this->_categoryFactory->create()->load($categoryId);
                if ($category->getId()) {
                    $this->_categories[$categoryId] = $category;
                }
            }
            
            // Get root node by ID (root category ID is always 1 for blog categories)
            $rootNode = $categoryTree->getNodeById(1);
            
            if (!$rootNode || !$rootNode->hasChildren()) {
                return '<div class="blog-category-tree"><p>No enabled blog categories found. Please create some categories first.</p></div>';
            }

            // Use parent's default element HTML first to ensure correct input naming (ends with [value])
            $html = $element->getElementHtml();
            // Add category tree container after default input
            $html .= '<div class="blog-category-tree" style="padding: 15px; background: #ffffff; border: 1px solid #adadad; border-radius: 2px; margin-top: 5px;">';
            $html .= '<ul style="margin-left: 0; padding-left: 0; font-family: "Open Sans", "Helvetica Neue", Helvetica, Arial, sans-serif; font-size: 14px;">';
            
            $html .= $this->renderTreeNode($rootNode, $element->getValue(), 0);
            
            $html .= '</ul></div>';
            $html .= $this->getJs($element->getHtmlId());
            
            return $html;
        } catch (\Exception $e) {
            return '<div class="blog-category-tree"><p>Error loading categories: ' . $this->escapeHtml($e->getMessage()) . '</p></div>';
        }
    }

    /**
     * Render tree node recursively
     *
     * @param Node $node
     * @param mixed $selectedValues
     * @param int $level
     * @return string
     */
    protected function renderTreeNode(Node $node, $selectedValues, $level = 0)
    {
        $html = '';
        $padding = $level * 20;
        $selectedArray = !empty($selectedValues) ? explode(',', $selectedValues) : [];
        
        // Skip root node itself, start with its children
        if ($level > 0) {
            $categoryId = $node->getId();
            $categoryName = $node->getName();
            $isEnabled = $node->getData('enabled');
            // For disabled categories, always uncheck and disable the checkbox
            $checked = !$isEnabled ? '' : (in_array($categoryId, $selectedArray) ? 'checked="checked"' : '');
            $disabledAttr = !$isEnabled ? 'disabled="disabled"' : '';
            $hasChildren = $node->hasChildren();
            $disabledStyle = !$isEnabled ? 'color: #999; font-style: italic;' : 'color: #333; font-weight:500;';
            $cursorStyle = !$isEnabled ? 'cursor:not-allowed;' : 'cursor:pointer;';
            
            $html .= '<li style="margin-left: ' . $padding . 'px; list-style: none; padding: 6px 0; line-height: 1.5;">';
            if ($hasChildren) {
                $html .= '<span class="category-toggle" style="cursor:pointer; display:inline-block; width:16px; height:16px; line-height:16px; text-align:center; font-weight:bold; color:#514943; background:#f0f0f0; border:1px solid #ccc; border-radius:2px; margin-right:5px; font-size:12px;">+</span>';
            } else {
                $html .= '<span style="display:inline-block; width:21px;"></span>';
            }
            $labelClass = !$isEnabled ? 'disabled-label ' : '';
            $html .= '<label class="' . $labelClass . '" style="margin-left:2px; ' . $cursorStyle . ' ' . $disabledStyle . '"><input type="checkbox" name="blog_category_select" value="' . $this->escapeHtml($categoryId) . '" ' . $checked . ' ' . $disabledAttr . ' style="margin:0 5px 0 0; vertical-align:middle;"/> ' . $this->escapeHtml($categoryName);
            if (!$isEnabled) {
                $html .= ' <span style="color: #e03030; font-size: 11px;">(Disabled)</span>';
            }
            $html .= '</label>';
        }
        
        // Render children
        if ($node->hasChildren()) {
            if ($level > 0) {
                $html .= '<ul style="margin-left: 21px; padding-left: 0; border-left: 1px dashed #ccc; display:none;" class="category-children">';
            }
            foreach ($node->getChildren() as $child) {
                $html .= $this->renderTreeNode($child, $selectedValues, $level + 1);
            }
            if ($level > 0) {
                $html .= '</ul>';
            }
        }
        
        if ($level > 0) {
            $html .= '</li>';
        }
        
        return $html;
    }

    /**
     * Get JavaScript to handle multiple selections and expand/collapse
     *
     * @param string $htmlId
     * @return string
     */
    protected function getJs($htmlId)
    {
        return <<<HTML
         <style type="text/css">
             /* Hide the default text input, we only need the category tree */
             #$htmlId {
                 display: none !important;
             }
             .blog-category-tree .category-toggle:hover {
                 background: #e0e0e0 !important;
                 border-color: #999 !important;
             }
             .blog-category-tree label:hover:not(.disabled-label) {
                 color: #007bdb !important;
             }
             .blog-category-tree input[type="checkbox"]:disabled {
                 opacity: 0.5;
                 cursor: not-allowed;
                 pointer-events: none;
             }
             .blog-category-tree .disabled-label {
                 pointer-events: none;
             }
         </style>
        <script type="text/javascript">
            require(['jquery'], function($) {
                $(document).ready(function() {
                    // Add disabled-label class to labels containing disabled checkboxes
                    $('input[name="blog_category_select"]:disabled').each(function() {
                        $(this).closest('label').addClass('disabled-label');
                    });
                    
                    function updateHiddenField() {
                        var selected = [];
                        // Only include enabled and checked checkboxes
                        $('input[name="blog_category_select"]:checked:not(:disabled)').each(function() {
                            selected.push($(this).val());
                        });
                        $('#$htmlId').val(selected.join(','));
                        console.log('Updated hidden field value:', $('#$htmlId').val());
                    }
                    
                    function toggleCategory(element) {
                        var children = $(element).closest('li').children('.category-children');
                        if (children.is(':visible')) {
                            children.hide();
                            $(element).text('+');
                        } else {
                            children.show();
                            $(element).text('-');
                        }
                    }
                    
                    // Prevent any interaction with disabled checkboxes
                    $('input[name="blog_category_select"]:disabled').on('click change', function(e) {
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        return false;
                    });
                    
                    // Only attach change handler to enabled checkboxes
                    $('input[name="blog_category_select"]:not(:disabled)').on('change', function() {
                        updateHiddenField();
                    });
                    
                    // Attach click handler to category toggles
                    $('.category-toggle').on('click', function() {
                        toggleCategory(this);
                    });
                    
                    // Update hidden field before form submission - use correct Magento admin config form ID
                    $('#config-edit-form').on('submit', function() {
                        // Expand all categories to ensure all checkboxes are accessible
                        $('.category-children').show();
                        $('.category-toggle').text('-');
                        // Update the hidden field with all selected categories
                        updateHiddenField();
                        console.log('Form submitted, hidden field value after update:', $('#$htmlId').val());
                        return true;
                    });
                });
            });
        </script>
HTML;
    }
}