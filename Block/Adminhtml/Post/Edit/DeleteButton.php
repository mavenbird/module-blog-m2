<?php
namespace Mavenbird\Blog\Block\Adminhtml\Post\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class DeleteButton implements ButtonProviderInterface
{
    protected $context;

    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    public function getButtonData()
    {
        $postId = $this->context->getRequest()->getParam('id');
        if (!$postId) {
            return [];
        }

        return [
            'label'      => __('Delete Post'),
            'class'      => 'delete',
            'on_click'   => sprintf(
                "deleteConfirm('%s', '%s')",
                __('Are you sure you want to delete this post?'),
                $this->context->getUrlBuilder()->getUrl('*/*/delete', ['id' => $postId])
            ),
            'sort_order' => 20,
        ];
    }
}