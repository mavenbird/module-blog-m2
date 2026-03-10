<?php

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
