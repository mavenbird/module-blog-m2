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

namespace Mavenbird\Blog\Block\Adminhtml\Tag\Edit\Tab;

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
use Magento\Framework\Registry;
use Magento\Store\Model\System\Store;
use Mavenbird\Blog\Model\Config\Source\CategoryLayout;

/**
 * Class Tag
 * @package Mavenbird\Blog\Block\Adminhtml\Tag\Edit\Tab
 */
class Tag extends Generic implements TabInterface
{
    /**
     * Wysiwyg config
     *
     * @var Config
     */
    public $wysiwygConfig;

    /**
     * Country options
     *
     * @var Yesno
     */
    public $booleanOptions;

    /**
     * @var Enabledisable
     */
    protected $enableDisable;

    /**
     * @var Store
     */
    public $systemStore;

    /**
     * @var Robots
     */
    public $metaRobots;

    /**
     * @var CategoryLayout
     */
    protected $categoryLayout;

    /**
     * Tag constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Config $wysiwygConfig
     * @param Yesno $booleanOptions
     * @param Enabledisable $enableDisable
     * @param Store $systemStore
     * @param Robots $metaRobotsOptions
     * @param CategoryLayout $categoryLayout
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Config $wysiwygConfig,
        Yesno $booleanOptions,
        Enabledisable $enableDisable,
        Store $systemStore,
        Robots $metaRobotsOptions,
        CategoryLayout $categoryLayout,
        array $data = []
    ) {
        $this->wysiwygConfig  = $wysiwygConfig;
        $this->booleanOptions = $booleanOptions;
        $this->enableDisable  = $enableDisable;
        $this->systemStore    = $systemStore;
        $this->metaRobots     = $metaRobotsOptions;
        $this->categoryLayout = $categoryLayout;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @inheritdoc
     */
    protected function _prepareForm()
    {
        /** @var \Mavenbird\Blog\Model\Tag $tag */
        $tag = $this->_coreRegistry->registry('mavenbird_blog_tag');

        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('tag_');
        $form->setFieldNameSuffix('tag');

        $fieldset = $form->addFieldset('base_fieldset', [
            'legend' => __('Tag Information'),
            'class'  => 'fieldset-wide'
        ]);
        if ($tag->getId()) {
            $fieldset->addField('tag_id', 'hidden', ['name' => 'tag_id']);
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
        $fieldset->addField('enabled', 'select', [
            'name'   => 'enabled',
            'label'  => __('Status'),
            'title'  => __('Status'),
            'values' => $this->enableDisable->toOptionArray(),
        ]);
        if (!$tag->hasData('enabled')) {
            $tag->setEnabled(1);
        }

        $fieldset->addField('layout', 'select', [
            'name'   => 'layout',
            'label'  => __('Layout'),
            'title'  => __('Layout'),
            'values' => $this->categoryLayout->toOptionArray(),
        ]);

        $fieldset->addField('description', 'editor', [
            'name'   => 'description',
            'label'  => __('Description'),
            'title'  => __('Description'),
            'class'  => 'wysiwyg-editor',
            'config' => $this->wysiwygConfig->getConfig([
                'add_variables'  => false,
                'add_widgets'    => true,
                'add_directives' => true
            ])
        ]);

        if (!$this->_storeManager->isSingleStoreMode()) {
            /** @var RendererInterface $rendererBlock */
            $rendererBlock = $this->getLayout()->createBlock(
                Element::class
            );
            $fieldset->addField('store_ids', 'multiselect', [
                'name'   => 'store_ids',
                'label'  => __('Store Views'),
                'title'  => __('Store Views'),
                'values' => $this->systemStore->getStoreValuesForForm(false, true)
            ])->setRenderer($rendererBlock);

            if (!$tag->hasData('store_ids')) {
                $tag->setStoreIds(0);
            }
        } else {
            $fieldset->addField('store_ids', 'hidden', [
                'name'  => 'store_ids',
                'value' => $this->_storeManager->getStore()->getId()
            ]);
        }
        $fieldset->addField('meta_title', 'text', [
        'name'  => 'meta_title',
        'label' => __('Meta Title'),
        'title' => __('Meta Title')
        ]);
        $fieldset->addField('meta_description', 'text', [
        'name'  => 'meta_description',
        'label' => __('Meta Description'),
        'title' => __('Meta Description')
        ]);
        $fieldset->addField('meta_keywords', 'text', [
        'name'  => 'meta_keywords',
        'label' => __('Meta Keywords'),
        'title' => __('Meta Keywords')
        ]);
        $fieldset->addField('meta_robots', 'select', [
        'name'  => 'meta_robots',
        'label' => __('Meta Robots'),
        'title' => __('Meta Robots'),
        'values' => $this->metaRobots->toOptionArray(),
        ]);


        $form->addValues($tag->getData());
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
        return __('Tag');
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
