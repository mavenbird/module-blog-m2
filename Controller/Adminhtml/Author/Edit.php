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

namespace Mavenbird\Blog\Controller\Adminhtml\Author;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Mavenbird\Blog\Controller\Adminhtml\Author;
use Mavenbird\Blog\Model\AuthorFactory;

/**
 * Class Edit
 * @package Mavenbird\Blog\Controller\Adminhtml\Author
 */
class Edit extends Author
{
    /**
     * @var PageFactory
     */
    public $resultPageFactory;

    /**
     * Edit constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param AuthorFactory $authorFactory
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        Registry $registry,
        AuthorFactory $authorFactory,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;

        parent::__construct($context, $registry, $authorFactory);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page|ResponseInterface|ResultInterface|Page
     */
    public function execute()
    {
        /** @var \Mavenbird\Blog\Model\Author $author */
        $author = $this->initAuthor();
        if (!$author) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*');

            return $resultRedirect;
        }

        /** Set entered data if was error when we do save */
        $data = $this->_session->getData('mavenbird_blog_author_data', true);
        if (!empty($data)) {
            $author->addData($data);
        }

        $this->coreRegistry->register('mavenbird_blog_author', $author);

        /** @var \Magento\Backend\Model\View\Result\Page|Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Mavenbird_Blog::author');
        $resultPage->getConfig()->getTitle()->set(__('Author Management'));

        $resultPage->getConfig()->getTitle()->prepend($author->getId() ? $author->getName() : __('New Author'));

        return $resultPage;
    }
}
