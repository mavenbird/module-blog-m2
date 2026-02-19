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

namespace Mavenbird\Blog\Controller\Adminhtml\Tag;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\Layout;
use Magento\Framework\View\Result\LayoutFactory;
use Mavenbird\Blog\Controller\Adminhtml\Tag;
use Mavenbird\Blog\Model\TagFactory;

/**
 * Class Posts
 * @package Mavenbird\Blog\Controller\Adminhtml\Tag
 */
class Posts extends Tag
{
    /**
     * @var LayoutFactory
     */
    public $resultLayoutFactory;

    /**
     * Posts constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param LayoutFactory $resultLayoutFactory
     * @param TagFactory $postFactory
     */
    public function __construct(
        Context $context,
        Registry $registry,
        LayoutFactory $resultLayoutFactory,
        TagFactory $postFactory
    ) {
        $this->resultLayoutFactory = $resultLayoutFactory;

        parent::__construct($context, $registry, $postFactory);
    }

    /**
     * @return Layout
     */
    public function execute()
    {
        $this->initTag(true);

        return $this->resultLayoutFactory->create();
    }
}
