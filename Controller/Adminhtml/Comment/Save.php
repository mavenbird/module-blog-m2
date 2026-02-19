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

use Exception;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use Mavenbird\Blog\Controller\Adminhtml\Comment;
use RuntimeException;

/**
 * Class Save
 * @package Mavenbird\Blog\Controller\Adminhtml\Comment
 */
class Save extends Comment
{
    /**
     * @return Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($data = $this->getRequest()->getPost('comment')) {
            /** @var \Mavenbird\Blog\Model\Comment $post */
            $comment = $this->initComment();

            $this->prepareData($comment, $data);

            $this->_eventManager->dispatch(
                'mavenbird_blog_comment_prepare_save',
                ['comment' => $comment, 'request' => $this->getRequest()]
            );

            try {
                $comment->save();
                $this->_eventManager->dispatch('blog_post_comment', ['comment_data' => $comment]);

                $this->messageManager->addSuccessMessage(__('The comment has been saved.'));
                $this->_getSession()->setData('mavenbird_blog_comment_data', false);

                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath('mavenbird_blog/*/edit', ['id' => $comment->getId(), '_current' => true]);
                } else {
                    $resultRedirect->setPath('mavenbird_blog/*/');
                }

                return $resultRedirect;
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Comment.'));
            }

            $this->_getSession()->setData('mavenbird_blog_comment_data', $data);

            $resultRedirect->setPath('mavenbird_blog/*/edit', ['id' => $comment->getId(), '_current' => true]);

            return $resultRedirect;
        }

        $resultRedirect->setPath('mavenbird_blog/*/');

        return $resultRedirect;
    }

    /**
     * @param $comment
     * @param array $data
     *
     * @return $this
     */
    protected function prepareData($comment, $data = [])
    {
        $comment->addData($data);

        return $this;
    }
}
