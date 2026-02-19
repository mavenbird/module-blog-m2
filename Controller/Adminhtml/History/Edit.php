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

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Mavenbird\Blog\Controller\Adminhtml\History;
use Mavenbird\Blog\Model\PostFactory;
use Mavenbird\Blog\Model\PostHistory;
use Mavenbird\Blog\Model\PostHistoryFactory;

/**
 * Class Edit
 * @package Mavenbird\Blog\Controller\Adminhtml\History
 */
class Edit extends History
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
     * @param PostHistoryFactory $postHistoryFactory
     * @param PostFactory $postFactory
     * @param Registry $coreRegistry
     * @param DateTime $date
     * @param PageFactory $resultPageFactory
     * @param Context $context
     */
    public function __construct(
        PostHistoryFactory $postHistoryFactory,
        PostFactory $postFactory,
        Registry $coreRegistry,
        DateTime $date,
        PageFactory $resultPageFactory,
        Context $context
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($postHistoryFactory, $postFactory, $coreRegistry, $date, $context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page|Redirect|Page
     */
    public function execute()
    {
        /** @var PostHistory $history */
        $history = $this->initPostHistory();

        if (!$history) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*');

            return $resultRedirect;
        }

        $this->coreRegistry->register('mavenbird_blog_post', $history);

        /** @var \Magento\Backend\Model\View\Result\Page|Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Mavenbird_Blog::history');
        $resultPage->getConfig()->getTitle()->set(__('Post History'));

        $resultPage->getConfig()->getTitle()->prepend(__('Edit Post History'));

        return $resultPage;
    }
}
