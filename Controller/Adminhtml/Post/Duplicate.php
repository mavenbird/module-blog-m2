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

namespace Mavenbird\Blog\Controller\Adminhtml\Post;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Forward;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Mavenbird\Blog\Controller\Adminhtml\Post;
use Mavenbird\Blog\Model\PostFactory;

/**
 * Class Edit
 * @package Mavenbird\Blog\Controller\Adminhtml\Post
 */
class Duplicate extends Post
{
    /**
     * Redirect result factory
     *
     * @var ForwardFactory
     */
    public $resultForwardFactory;

    /**
     * Duplicate constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param PostFactory $postFactory
     * @param ForwardFactory $resultForwardFactory
     */
    public function __construct(
        Context $context,
        Registry $registry,
        PostFactory $postFactory,
        ForwardFactory $resultForwardFactory
    ) {
        $this->resultForwardFactory = $resultForwardFactory;

        parent::__construct($postFactory, $registry, $context);
    }

    /**
     * @return Forward|ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $postId = $this->getRequest()->getParam('id');
        if (!$postId) {
            $this->messageManager->addErrorMessage(__('Post ID is missing.'));
            return $this->_redirect('*/*/');
        }

        try {
            // Load original post
            $originalPost = $this->postFactory->create()->load($postId);
            if (!$originalPost->getId()) {
                $this->messageManager->addErrorMessage(__('Original post not found.'));
                return $this->_redirect('*/*/');
            }

            // Create new post and copy data
            $newPost = $this->postFactory->create();
            $newPost->setData($originalPost->getData());
            $newPost->setId(null); // Ensure new entity
            $newPost->setTitle($originalPost->getTitle() . ' (Duplicate)'); // Optionally modify title

            $newPost->save();

            $this->messageManager->addSuccessMessage(__('Post duplicated successfully.'));
            return $this->_redirect('*/*/edit', ['id' => $newPost->getId()]);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Error duplicating post: %1', $e->getMessage()));
            return $this->_redirect('*/*/');
        }
    }
}
