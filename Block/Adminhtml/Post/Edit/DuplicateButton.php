<?php
namespace Mavenbird\Blog\Block\Adminhtml\Post\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class DuplicateButton implements ButtonProviderInterface
{
    protected $context;

    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    public function getButtonData()
    {
        $postId    = $this->context->getRequest()->getParam('id');
        $duplicate = $this->context->getRequest()->getParam('duplicate');

        if (!$postId || $duplicate) {
            return [];
        }

        return [
            'label'      => __('Duplicate'),
            'class'      => 'duplicate',
            'on_click'   => sprintf(
                "location.href = '%s';",
                $this->context->getUrlBuilder()->getUrl(
                    '*/*/duplicate',
                    ['id' => $postId, 'duplicate' => true]
                )
            ),
            'sort_order' => 60,
        ];
    }
}