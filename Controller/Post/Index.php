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

namespace Mavenbird\Blog\Controller\Post;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Mavenbird\Blog\Helper\Data;

/**
 * Class Index
 * @package Mavenbird\Blog\Controller\Post
 */
class Index extends Action
{
    /**
     * @var PageFactory
     */
    public $resultPageFactory;

    /**
     * @var Data
     */
    protected $_helperBlog;

    /**
     * Index constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Data $helperData
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Data $helperData
    ) {
        $this->_helperBlog = $helperData;
        $this->resultPageFactory = $resultPageFactory;

        parent::__construct($context);
    }

    /**
     * @return Page
     */
    public function execute()
    {
        $page = $this->resultPageFactory->create();

        $page->getConfig()->setPageLayout($this->_helperBlog->getBlogListingLayout());

        $page->getConfig()->getTitle()->set(
            $this->_helperBlog->getSeoConfig('blog_seo_meta_title')
        );
        $page->getConfig()->setMetadata(
            'description',
            $this->_helperBlog->getSeoConfig('blog_seo_meta_description')
        );
        $page->getConfig()->setMetadata(
            'keywords',
            $this->_helperBlog->getSeoConfig('blog_seo_meta_keywords')
        );
        $page->getConfig()->setMetadata(
            'robots',
            $this->_helperBlog->getSeoConfig('blog_seo_meta_robots')
        );
        return $page;
    }
}
