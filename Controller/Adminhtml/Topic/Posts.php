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

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\Layout;
use Magento\Framework\View\Result\LayoutFactory;
use Mavenbird\Blog\Controller\Adminhtml\Topic;
use Mavenbird\Blog\Model\TopicFactory;

/**
 * Class Posts
 * @package Mavenbird\Blog\Controller\Adminhtml\Topic
 */
class Posts extends Topic
{
    /**
     * Result layout factory
     *
     * @var LayoutFactory
     */
    public $resultLayoutFactory;

    /**
     * Posts constructor.
     *
     * @param LayoutFactory $resultLayoutFactory
     * @param TopicFactory $postFactory
     * @param Registry $registry
     * @param Context $context
     */
    public function __construct(
        Context $context,
        Registry $registry,
        LayoutFactory $resultLayoutFactory,
        TopicFactory $postFactory
    ) {
        $this->resultLayoutFactory = $resultLayoutFactory;

        parent::__construct($context, $registry, $postFactory);
    }

    /**
     * @return Layout
     */
    public function execute()
    {
        $this->initTopic(true);

        return $this->resultLayoutFactory->create();
    }
}
