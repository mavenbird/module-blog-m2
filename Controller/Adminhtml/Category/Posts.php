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

namespace Mavenbird\Blog\Controller\Adminhtml\Category;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\Layout;
use Magento\Framework\View\Result\LayoutFactory;
use Mavenbird\Blog\Controller\Adminhtml\Category;
use Mavenbird\Blog\Model\CategoryFactory;

/**
 * Class Posts
 * @package Mavenbird\Blog\Controller\Adminhtml\Category
 */
class Posts extends Category
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
     * @param Context $context
     * @param Registry $coreRegistry
     * @param CategoryFactory $categoryFactory
     * @param LayoutFactory $resultLayoutFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        CategoryFactory $categoryFactory,
        LayoutFactory $resultLayoutFactory
    ) {
        $this->resultLayoutFactory = $resultLayoutFactory;

        parent::__construct($context, $coreRegistry, $categoryFactory);
    }

    /**
     * @return Layout
     */
    public function execute()
    {
        $this->initCategory(true);

        return $this->resultLayoutFactory->create();
    }
}
