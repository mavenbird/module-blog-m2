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

namespace Mavenbird\Blog\Block\Adminhtml\Category\Edit\Tab;

use Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Cms\Model\Wysiwyg\Config;
use Magento\Config\Model\Config\Source\Design\Robots;
use Magento\Config\Model\Config\Source\Enabledisable;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Store\Model\System\Store;

/**
 * Class Category
 * @package Mavenbird\Blog\Block\Adminhtml\Category\Edit\Tab
 */
class Category extends Generic implements TabInterface
{
    /**
     * Wysiwyg config
     *
     * @var Config
     */
    protected $wysiwygConfig;

    /**
     * Country options
     *
     * @var Yesno
     */
    protected $booleanOptions;

    /**
     * @var Enabledisable
     */
    protected $enableDisable;

    /**
     * @var Robots
     */
    protected $metaRobotsOptions;

    /**
     * @var Store
     */
    protected $systemStore;

    /**
     * @var \Mavenbird\Blog\Model\Config\Source\CategoryLayout
     */
    protected $categoryLayoutOptions;

    /**
     * Category constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Config $wysiwygConfig
     * @param Yesno $booleanOptions
     * @param Enabledisable $enableDisable
     * @param Robots $metaRobotsOptions
     * @param Store $systemStore
     * @param \Mavenbird\Blog\Model\Config\Source\CategoryLayout $categoryLayoutOptions
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Config $wysiwygConfig,
        Yesno $booleanOptions,
        Enabledisable $enableDisable,
        Robots $metaRobotsOptions,
        Store $systemStore,
        \Mavenbird\Blog\Model\Config\Source\CategoryLayout $categoryLayoutOptions,
        array $data = []
    ) {
        $this->wysiwygConfig     = $wysiwygConfig;
        $this->booleanOptions    = $booleanOptions;
        $this->enableDisable     = $enableDisable;
        $this->metaRobotsOptions = $metaRobotsOptions;
        $this->systemStore       = $systemStore;
        $this->categoryLayoutOptions = $categoryLayoutOptions;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return Generic
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function _prepareForm()
    {
        /** @var \Mavenbird\Blog\Model\Category $category */
        $category = $this->_coreRegistry->registry('category');

        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('category_');
        $form->setFieldNameSuffix('category');

        $fieldset = $form->addFieldset('base_fieldset', [
            'legend' => __('Category Information'),
            'class'  => 'fieldset-wide'
        ]);

        if ($category->getId()) {
            $fieldset->addField('category_id', 'hidden', ['name' => 'id', 'value' => $category->getId()]);
            $fieldset->addField('path', 'hidden', ['name' => 'path', 'value' => $category->getPath()]);
        } else {
            $fieldset->addField(
                'path',
                'hidden',
                ['name' => 'path', 'value' => $this->getRequest()->getParam('parent') ?: 1]
            );
        }

        $fieldset->addField('name', 'text', [
            'name'     => 'name',
            'label'    => __('Name'),
            'title'    => __('Name'),
            'required' => true,
        ]);
        $fieldset->addField('url_key', 'text', [
            'name'  => 'url_key',
            'label' => __('URL Key'),
            'title' => __('URL Key')
        ]);
        $fieldset->addField('description', 'editor', [
            'name'   => 'description',
            'label'  => __('Description'),
            'title'  => __('Description'),
            'required' => false,
            'class' => 'wysiwyg-editor',
            'config' => $this->wysiwygConfig->getConfig([
                'add_variables'  => false,
                'add_widgets'    => true,
                'add_directives' => true
            ])
        ]);
        $fieldset->addField('enabled', 'select', [
            'name'   => 'enabled',
            'label'  => __('Status'),
            'title'  => __('Status'),
            'values' => $this->enableDisable->toOptionArray(),
        ]);
        $fieldset->addField('layout', 'select', [
            'name'   => 'layout',
            'label'  => __('Layout'),
            'title'  => __('Layout'),
            'values' => $this->categoryLayoutOptions->toOptionArray(),
        ]);

        if ($this->_storeManager->isSingleStoreMode()) {
            $storeId = $this->_storeManager->getStore()->getId();
            $fieldset->addField('store_ids', 'hidden', [
                'name'  => 'store_ids',
                'value' => $storeId
            ]);
        } else {
            /** @var RendererInterface $rendererBlock */
            $rendererBlock = $this->getLayout()->createBlock(Element::class);

            $fieldset->addField('store_ids', 'multiselect', [
                'name'   => 'store_ids',
                'label'  => __('Store Views'),
                'title'  => __('Store Views'),
                'values' => $this->systemStore->getStoreValuesForForm(false, true)
            ])->setRenderer($rendererBlock);

            if (!$category->hasData('store_ids')) {
                $category->setStoreIds(0);
            }
        }

        $fieldset->addField('meta_title', 'text', [
        'name'  => 'meta_title',
        'label' => __('Meta Title'),
        'title' => __('Meta Title')
        ]);
        $fieldset->addField('meta_description', 'textarea', [
            'name'  => 'meta_description',
            'label' => __('Meta Description'),
            'title' => __('Meta Description')
        ]);
        $fieldset->addField('meta_keywords', 'textarea', [
            'name'  => 'meta_keywords',
            'label' => __('Meta Keywords'),
            'title' => __('Meta Keywords')
        ]);
        $fieldset->addField('meta_robots', 'select', [
            'name'   => 'meta_robots',
            'label'  => __('Meta Robots'),
            'title'  => __('Meta Robots'),
            'values' => $this->metaRobotsOptions->toOptionArray(),
        ]);

        $form->addValues($category->getData());
        $this->setForm($form);

        $this->_eventManager->dispatch('adminhtml_blog_edit_form_prepare_form', ['block' => $this]);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Category');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
}
