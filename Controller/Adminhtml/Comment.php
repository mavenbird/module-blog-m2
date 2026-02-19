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

namespace Mavenbird\Blog\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Mavenbird\Blog\Model\CommentFactory;

/**
 * Class Comment
 * @package Mavenbird\Blog\Controller\Adminhtml
 */
abstract class Comment extends Action
{
    /** Authorization level of a basic admin session */
    const ADMIN_RESOURCE = 'Mavenbird_Blog::comment';

    /**
     * @var CommentFactory
     */
    public $commentFactory;

    /**
     * Core registry
     *
     * @var Registry
     */
    public $coreRegistry;

    /**
     * Comment constructor.
     *
     * @param CommentFactory $commentFactory
     * @param Registry $coreRegistry
     * @param Context $context
     */
    public function __construct(
        CommentFactory $commentFactory,
        Registry $coreRegistry,
        Context $context
    ) {
        $this->commentFactory = $commentFactory;
        $this->coreRegistry = $coreRegistry;

        parent::__construct($context);
    }

    /**
     * @param bool $register
     *
     * @return bool|\Mavenbird\Blog\Model\Comment
     */
    protected function initComment($register = false)
    {
        $cmtId = $this->getRequest()->getParam("id");

        /** @var \Mavenbird\Blog\Model\Post $post */
        $comment = $this->commentFactory->create();

        if ($cmtId) {
            $comment->load($cmtId);
            if (!$comment->getId()) {
                $this->messageManager->addErrorMessage(__('This comment no longer exists.'));

                return false;
            }
        }

        if ($register) {
            $this->coreRegistry->register('mavenbird_blog_comment', $comment);
        }

        return $comment;
    }
}
