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
     * Edit constructor.
     *
     * @param PostHistoryFactory $postHistoryFactory
     * @param PostFactory $postFactory
     * @param Registry $coreRegistry
     * @param DateTime $date
     * @param Context $context
     */
    public function __construct(
        PostHistoryFactory $postHistoryFactory,
        PostFactory $postFactory,
        Registry $coreRegistry,
        DateTime $date,
        Context $context
    ) {
        parent::__construct($postHistoryFactory, $postFactory, $coreRegistry, $date, $context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page|Redirect|Page
     */
    public function execute()
    {
        /** @var PostHistory $history */
        $history = $this->initPostHistory();
        $resultRedirect = $this->resultRedirectFactory->create();

        if (!$history) {
            $resultRedirect->setPath('*');
            return $resultRedirect;
        }

        // Force History edit to use the new Post editor (UI form/PageBuilder).
        $resultRedirect->setPath('mavenbird_blog/post/edit', [
            'id' => $history->getPostId(),
            'history_id' => $history->getId(),
            '_current' => true
        ]);

        return $resultRedirect;
    }
}
