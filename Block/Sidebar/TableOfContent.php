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

namespace Mavenbird\Blog\Block\Sidebar;

use Magento\Framework\View\Element\Template;
use Mavenbird\Blog\Helper\Data as BlogHelper;

class TableOfContent extends Template
{
    protected $blockFactory;
    protected $blogHelper;
    protected $registry;

    public function __construct(
        Template\Context $context,
        BlogHelper $blogHelper,
        array $data = []
    ) {
        $this->blogHelper = $blogHelper;
        parent::__construct($context, $data);
    }

    public function canShowTableOfContent()
    {
        return (bool) $this->blogHelper->getSidebarConfig('show_table_of_content');
    }

    public function isStickyTableOfContent()
    {
        return (bool) $this->blogHelper->getSidebarConfig('sticky_table_of_content');
    }

}
