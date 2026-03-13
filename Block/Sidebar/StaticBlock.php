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

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\BlockFactory;
use Magento\Framework\Registry;
use Mavenbird\Blog\Helper\Data as BlogHelper;

class StaticBlock extends Template
{
    protected $blockFactory;
    protected $blogHelper;
    protected $registry;

    public function __construct(
        Template\Context $context,
        BlockFactory $blockFactory,
        BlogHelper $blogHelper,
        Registry $registry,
        array $data = []
    ) {
        $this->blockFactory = $blockFactory;
        $this->blogHelper = $blogHelper;
        $this->registry = $registry;
        parent::__construct($context, $data);
    }

       public function canShowStaticBlock()
    {
        return (bool) $this->blogHelper->getSidebarConfig('show_sidebar_static_block');
    }

    public function getStaticBlockIdentifier()
    {
        $post = $this->registry->registry('current_post');

        if ($post && $post->getStaticBlockIdentifier()) {
            return $post->getStaticBlockIdentifier();
        }

        return $this->blogHelper->getSidebarConfig('list_of_static_block');
    }

    public function renderSidebarStaticBlock()
    {
        $identifier = $this->getStaticBlockIdentifier();

        if (!$identifier) {
            return '';
        }

        return $this->blockFactory
            ->createBlock(\Magento\Cms\Block\Block::class)
            ->setBlockId($identifier)
            ->toHtml();
    }
}