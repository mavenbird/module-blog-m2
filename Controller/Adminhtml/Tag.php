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
use Mavenbird\Blog\Model\TagFactory;

/**
 * Class Tag
 * @package Mavenbird\Blog\Controller\Adminhtml
 */
abstract class Tag extends Action
{
    /** Authorization level of a basic admin session */
    const ADMIN_RESOURCE = 'Mavenbird_Blog::tag';

    /**
     * Tag Factory
     *
     * @var TagFactory
     */
    public $tagFactory;

    /**
     * Core registry
     *
     * @var Registry
     */
    public $coreRegistry;

    /**
     * Tag constructor.
     *
     * @param Context $context
     * @param Registry $coreRegistry
     * @param TagFactory $tagFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        TagFactory $tagFactory
    ) {
        $this->tagFactory = $tagFactory;
        $this->coreRegistry = $coreRegistry;

        parent::__construct($context);
    }

    /**
     * @param bool $register
     *
     * @return bool|\Mavenbird\Blog\Model\Tag
     */
    protected function initTag($register = false)
    {
        $tagId = (int)$this->getRequest()->getParam('id');

        /** @var \Mavenbird\Blog\Model\Tag $tag */
        $tag = $this->tagFactory->create();
        if ($tagId) {
            $tag->load($tagId);
            if (!$tag->getId()) {
                $this->messageManager->addErrorMessage(__('This tag no longer exists.'));

                return false;
            }
        }

        if ($register) {
            $this->coreRegistry->register('mavenbird_blog_tag', $tag);
        }

        return $tag;
    }
}
