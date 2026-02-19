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
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\LayoutFactory;
use Mavenbird\Blog\Controller\Adminhtml\Post;
use Mavenbird\Blog\Model\PostFactory;
use Mavenbird\Blog\Model\PostHistoryFactory;

/**
 * Class Products
 * @package Mavenbird\Blog\Controller\Adminhtml\Post
 */
class Products extends Post
{
    /**
     * @var LayoutFactory
     */
    protected $resultLayoutFactory;

    /**
     * @var PostHistoryFactory
     */
    protected $postHistoryFactory;

    /**
     * Products constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param PostFactory $productFactory
     * @param PostHistoryFactory $postHistoryFactory
     * @param LayoutFactory $resultLayoutFactory
     */
    public function __construct(
        Context $context,
        Registry $registry,
        PostFactory $productFactory,
        PostHistoryFactory $postHistoryFactory,
        LayoutFactory $resultLayoutFactory
    ) {
        parent::__construct($productFactory, $registry, $context);

        $this->resultLayoutFactory = $resultLayoutFactory;
        $this->postHistoryFactory = $postHistoryFactory;
    }

    /**
     * Save action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        if ($this->getRequest()->getParam('history')) {
            $history = $this->postHistoryFactory->create()->load($this->getRequest()->getParam('id'));
            $this->coreRegistry->register('mavenbird_blog_post', $history);
        } else {
            $this->initPost(true);
        }

        return $this->resultLayoutFactory->create();
    }
}
