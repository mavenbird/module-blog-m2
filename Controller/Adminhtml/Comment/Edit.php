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

namespace Mavenbird\Blog\Controller\Adminhtml\Comment;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Mavenbird\Blog\Controller\Adminhtml\Comment;
use Mavenbird\Blog\Model\CommentFactory;

/**
 * Class Edit
 * @package Mavenbird\Blog\Controller\Adminhtml\Comment
 */
class Edit extends Comment
{
    /**
     * Page factory
     *
     * @var PageFactory
     */
    public $resultPageFactory;

    /**
     * Edit constructor.
     *
     * @param PageFactory $pageFactory
     * @param CommentFactory $commentFactory
     * @param Registry $coreRegistry
     * @param Context $context
     */
    public function __construct(
        PageFactory $pageFactory,
        CommentFactory $commentFactory,
        Registry $coreRegistry,
        Context $context
    ) {
        $this->resultPageFactory = $pageFactory;

        parent::__construct($commentFactory, $coreRegistry, $context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page|Redirect|Page
     */
    public function execute()
    {
        /** @var \Mavenbird\Blog\Model\Comment $comment */
        $comment = $this->initComment();

        if (!$comment) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*');

            return $resultRedirect;
        }

        $data = $this->_session->getData('mavenbird_blog_comment_data', true);

        if (!empty($data)) {
            $comment->setData($data);
        }

        $this->coreRegistry->register('mavenbird_blog_comment', $comment);

        /** @var \Magento\Backend\Model\View\Result\Page|Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Mavenbird_Blog::comment');

        $title = __('Edit Comment');
        $resultPage->getConfig()->getTitle()->prepend($title);

        return $resultPage;
    }
}
