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

namespace Mavenbird\Blog\Controller\Adminhtml\History;

use Exception;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Mavenbird\Blog\Controller\Adminhtml\History;

/**
 * Class Delete
 * @package Mavenbird\Blog\Controller\Adminhtml\History
 */
class Delete extends History
{
    /**
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $postId = null;
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $history = $this->postHistoryFactory->create()
                    ->load($id);
                $postId = $history->getPostId();
                $history->delete();

                $this->messageManager->addSuccessMessage(__('The Post History has been deleted.'));
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        } else {
            $this->messageManager->addErrorMessage(__('Post History to delete was not found.'));
        }
        if ($postId) {
            $resultRedirect->setPath('mavenbird_blog/post/edit', ['id' => $postId]);
        } else {
            $resultRedirect->setPath('mavenbird_blog/post/index');
        }

        return $resultRedirect;
    }
}
