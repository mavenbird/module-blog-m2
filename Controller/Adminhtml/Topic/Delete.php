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

namespace Mavenbird\Blog\Controller\Adminhtml\Topic;

use Exception;
use Magento\Framework\Controller\Result\Redirect;
use Mavenbird\Blog\Controller\Adminhtml\Topic;

/**
 * Class Delete
 * @package Mavenbird\Blog\Controller\Adminhtml\Topic
 */
class Delete extends Topic
{
    /**
     * @return Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $this->topicFactory->create()
                    ->load($id)
                    ->delete();

                $this->messageManager->addSuccessMessage(__('The Topic has been deleted.'));
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $resultRedirect->setPath('mavenbird_blog/*/edit', ['id' => $id]);

                return $resultRedirect;
            }
        } else {
            $this->messageManager->addErrorMessage(__('Topic to delete was not found.'));
        }

        $resultRedirect->setPath('mavenbird_blog/*/');

        return $resultRedirect;
    }
}
