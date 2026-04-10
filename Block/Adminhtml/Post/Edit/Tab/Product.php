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

namespace Mavenbird\Blog\Block\Adminhtml\Post\Edit\Tab;

use Exception;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Helper\Data;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Mavenbird\Blog\Model\Tag;

/**
 * Class Product
 * @package Mavenbird\Blog\Block\Adminhtml\Post\Edit\Tab
 */
class Product extends Extended implements TabInterface
{
    /**
     * @var CollectionFactory
     */
    public $productCollectionFactory;

    /**
     * @var Registry
     */
    public $coreRegistry;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * Product constructor.
     *
     * @param Context $context
     * @param Registry $coreRegistry
     * @param Data $backendHelper
     * @param RequestInterface $request
     * @param CollectionFactory $productCollectionFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        Data $backendHelper,
        RequestInterface $request,
        CollectionFactory $productCollectionFactory,
        array $data = []
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->coreRegistry             = $coreRegistry;
        $this->request                  = $request;

        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Set grid params
     */
    public function _construct()
    {
        parent::_construct();

        $this->setId('product_grid');
        $this->setDefaultSort('position');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(false);
        $this->setUseAjax(true);

        // Show all products by default; admins can filter "in_products" manually.
    }

    /**
     * @inheritdoc
     */
    protected function _prepareCollection()
    {
        /** @var Collection $collection */
        $collection = $this->productCollectionFactory->create();
        $collection->clear();

        $collection->getSelect()->joinLeft(
            ['mb_p' => $collection->getTable('mavenbird_blog_post_product')],
            'e.entity_id = mb_p.entity_id',
            ['position']
        )->group('e.entity_id');

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('in_products', [
            'header_css_class' => 'a-center',
            'type'             => 'checkbox',
            'name'             => 'in_product',
            'values'           => $this->_getSelectedProducts(),
            'align'            => 'center',
            'index'            => 'entity_id'
        ]);
        $this->addColumn('entity_id', [
            'header'           => __('ID'),
            'sortable'         => true,
            'index'            => 'entity_id',
            'type'             => 'number',
            'header_css_class' => 'col-id',
            'column_css_class' => 'col-id'
        ]);
        $this->addColumn('title', [
            'header'           => __('Sku'),
            'index'            => 'sku',
            'header_css_class' => 'col-name',
            'column_css_class' => 'col-name'
        ]);

        if ($this->request->getParam('post_id') || $this->getPost()->getId()) {
            $this->addColumn('position', [
                'header' => __('Position'),
                'name' => 'position',
                'width' => 60,
                'type' => 'number',
                'validate_class' => 'validate-number',
                'index' => 'position',
                'editable' => true,
                'edit_only' => true,
            ]);
        } else {
            $this->addColumn('position', [
                'header' => __('Position'),
                'name' => 'position',
                'width' => 60,
                'type' => 'number',
                'validate_class' => 'validate-number',
                'index' => 'position',
                'editable' => true,
                'filter' => false,
                'edit_only' => true,
            ]);
        }

        return $this;
    }

    /**
     * Retrieve selected Tags
     *
     * @return array
     */
    protected function _getSelectedProducts()
    {
        $products = $this->getRequest()->getPost('post_products', null);
        if (!is_array($products) || !$products) {
            $products = $this->getPost()->getProductsPosition();

            return array_keys($products);
        }

        return $products;
    }

    /**
     * Retrieve selected Tags
     *
     * @return array
     */
    public function getSelectedProducts()
    {
        $selected = $this->getPost()->getProductsPosition();
        if (!is_array($selected)) {
            $selected = [];
        } else {
            foreach ($selected as $key => $value) {
                $selected[$key] = ['position' => $value];
            }
        }

        return $selected;
    }

    /**
     * @param Tag|Object $item
     *
     * @return string
     */
    public function getRowUrl($item)
    {
        return '#';
    }

    /**
     * get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/productsGrid', ['post_id' => $this->getPost()->getId()]);
    }

    /**
     * @return \Mavenbird\Blog\Model\Post
     */
    public function getPost()
    {
        return $this->coreRegistry->registry('mavenbird_blog_post');
    }

    /**
     * @param Column $column
     *
     * @return $this
     * @throws LocalizedException
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() === 'in_products') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', ['in' => $productIds]);
            } else {
                if ($productIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', ['nin' => $productIds]);
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getTabLabel()
    {
        return __('Products');
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @return string
     */
    public function getTabUrl()
    {
        return $this->getUrl('mavenbird_blog/post/products', ['_current' => true]);
    }

    /**
     * @return string
     */
    public function getTabClass()
    {
        return 'ajax only';
    }

    /**
     * Append serializer fallback so selected products are posted as "products".
     *
     * @return string
     */
    protected function _toHtml()
    {
        $html = parent::_toHtml();

        $html .= '<script>
            (function () {
                var bindProductSync = function () {
                    var form = document.getElementById("mavenbird_blog_post_form")
                        || document.getElementById("edit_form")
                        || document.querySelector("form");
                    if (!form || form.dataset.mbProductsBound === "1") {
                        return;
                    }

                    form.dataset.mbProductsBound = "1";

                    var hiddenProducts = document.getElementById("mb_post_products");
                    if (!hiddenProducts) {
                        hiddenProducts = document.createElement("input");
                        hiddenProducts.type = "hidden";
                        hiddenProducts.id = "mb_post_products";
                        hiddenProducts.name = "products";
                        hiddenProducts.setAttribute("data-form-part", "mavenbird_blog_post_form");
                        form.appendChild(hiddenProducts);
                    }

                    var syncSelected = function () {
                        var grid = document.getElementById("product_grid_table");
                        if (!grid) {
                            return;
                        }

                        var checked = grid.querySelectorAll("tbody input.checkbox:checked");
                        var ids = [];
                        checked.forEach(function (checkbox) {
                            var id = parseInt(checkbox.value || "", 10);
                            if (id > 0) {
                                ids.push(id);
                            }
                        });

                        // Simplified legacy serializer format expected by decodeGridSerializedInput.
                        hiddenProducts.value = ids.join("&");
                    };

                    // Initial sync and live updates.
                    syncSelected();
                    document.addEventListener("change", function (e) {
                        if (e.target && e.target.closest && e.target.closest("#product_grid_table")) {
                            syncSelected();
                        }
                    }, true);
                };

                if (document.readyState === "loading") {
                    document.addEventListener("DOMContentLoaded", bindProductSync);
                } else {
                    bindProductSync();
                }
            })();
        </script>';

        return $html;
    }
}
