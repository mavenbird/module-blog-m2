<?php

namespace Mavenbird\Blog\Block\Adminhtml\Post\Edit\Tab\Renderer\History;

use Magento\Backend\Block\Context;
use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;
use Mavenbird\Blog\Helper\Image;

/**
 * Post image thumbnail for Edit History grid.
 */
class ImageColumn extends AbstractRenderer
{
    /**
     * @var Image
     */
    private $imageHelper;

    public function __construct(
        Context $context,
        Image $imageHelper,
        array $data = []
    ) {
        $this->imageHelper = $imageHelper;
        parent::__construct($context, $data);
    }

    /**
     * @param DataObject $row
     * @return string
     */
    public function render(DataObject $row)
    {
        $file = $row->getData('image');
        if ($file === null || $file === '') {
            return '';
        }

        $relative = $this->imageHelper->getMediaPath($file, Image::TEMPLATE_MEDIA_TYPE_POST);
        $url = $this->imageHelper->getMediaUrl($relative);

        return sprintf(
            '<img src="%s" alt="" style="max-width:96px;max-height:96px;height:auto;vertical-align:middle;" />'
            . '<br/><span class="muted">%s</span>',
            $this->escapeUrl($url),
            $this->escapeHtml((string) $file)
        );
    }
}
