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

use Exception;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Mavenbird\Blog\Helper\Data;
use Mavenbird\Blog\Model\PostFactory;

/**
 * Class Manage
 * @package Mavenbird\Blog\Controller\Post
 */
class DeletePost extends Action
{
    /**
     * @var PostFactory
     */
    protected $postFactory;

    /**
     * @var DateTime
     */
    protected $date;

    /**
     * @var Data
     */
    protected $_helperBlog;

    /**
     * DeletePost constructor.
     *
     * @param Context $context
     * @param PostFactory $postFactory
     * @param Data $helperData
     */
    public function __construct(
        Context $context,
        PostFactory $postFactory,
        Data $helperData
    ) {
        $this->_helperBlog = $helperData;
        $this->postFactory = $postFactory;

        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface|null
     */
    public function execute()
    {
        $postId = $this->getRequest()->getParam('post_id');
        $this->_helperBlog->setCustomerContextId();
        $author = $this->_helperBlog->getCurrentAuthor();
        $post = $this->postFactory->create();

        if (!$author || !$postId) {
            return null;
        }

        try {
            $post->load($postId)->delete();
            $this->messageManager->addSuccessMessage(__('The post has been deleted.'));

            return $this->getResponse()->representJson(Data::jsonEncode([
                'status' => 1,
                'post_id' => $postId
            ]));
        } catch (Exception $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());

            return $this->getResponse()->representJson(Data::jsonEncode([
                'status' => 0
            ]));
        }
    }
}
