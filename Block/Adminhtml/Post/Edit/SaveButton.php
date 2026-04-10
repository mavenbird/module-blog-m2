<?php
 
namespace Mavenbird\Blog\Block\Adminhtml\Post\Edit;
 
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
 
class SaveButton implements ButtonProviderInterface
{
    protected $context;
 
    public function __construct(Context $context)
    {
        $this->context = $context;
    }
 
    public function getButtonData()
    {
        return [
            'label' => __('Save'),
            'class' => 'save primary',
            'class_name' => \Magento\Ui\Component\Control\Container::SPLIT_BUTTON,
            'options' => $this->getSplitButtonOptions(),
            'data_attribute' => [
                'mage-init' => [
                    'buttonAdapter' => [
                        'actions' => [
                            [
                                'targetName' => 'mavenbird_blog_post_form.post_form_data_source',
                                'actionName' => 'save',
                                'params' => [true]
                            ]
                        ]
                    ]
                ]
            ],
            'sort_order' => 90,
        ];
    }
 
    protected function getSplitButtonOptions()
    {
        return [
            [
                'label' => __('Save as Draft'),
                'id_hard' => 'save_as_draft',
                'data_attribute' => [
                    'mage-init' => [
                        'buttonAdapter' => [
                            'actions' => [
                                [
                                    'targetName' => 'mavenbird_blog_post_form.post_form_data_source',
                                    'actionName' => 'save',
                                    'params' => [true, ['action' => 'draft']]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                'label' => __('Save & add History'),
                'id_hard' => 'save_add_history',
                'data_attribute' => [
                    'mage-init' => [
                        'buttonAdapter' => [
                            'actions' => [
                                [
                                    'targetName' => 'mavenbird_blog_post_form.post_form_data_source',
                                    'actionName' => 'save',
                                    'params' => [true, ['action' => 'add']]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}
 