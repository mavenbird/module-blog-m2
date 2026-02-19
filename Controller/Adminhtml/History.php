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
use Magento\Framework\Stdlib\DateTime\DateTime;
use Mavenbird\Blog\Model\PostFactory;
use Mavenbird\Blog\Model\PostHistory;
use Mavenbird\Blog\Model\PostHistoryFactory;

/**
 * Class Post
 * @package Mavenbird\Blog\Controller\Adminhtml
 */
abstract class History extends Action
{
    /** Authorization level of a basic admin session */
    const ADMIN_RESOURCE = 'Mavenbird_Blog::post';

    /**
     * Post History Factory
     *
     * @var PostHistoryFactory
     */
    public $postHistoryFactory;

    /**
     * Core registry
     *
     * @var Registry
     */
    public $coreRegistry;

    /**
     * @var PostFactory
     */
    protected $postFactory;

    /**
     * @var DateTime
     */
    protected $date;

    /**
     * Post constructor.
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
        $this->postHistoryFactory = $postHistoryFactory;
        $this->postFactory = $postFactory;
        $this->coreRegistry = $coreRegistry;
        $this->date = $date;

        parent::__construct($context);
    }

    /**
     * @param bool $register
     *
     * @return bool|PostHistory
     */
    protected function initPostHistory($register = false)
    {
        $historyId = (int)$this->getRequest()->getParam('id');

        /** @var \Mavenbird\Blog\Model\Post $post */
        $history = $this->postHistoryFactory->create();
        if ($historyId) {
            $history->load($historyId);
            if (!$history->getId()) {
                $this->messageManager->addErrorMessage(__('This History no longer exists.'));

                return false;
            }
        }

        if ($register) {
            $this->coreRegistry->register('mavenbird_blog_post_history', $history);
        }

        return $history;
    }
}
