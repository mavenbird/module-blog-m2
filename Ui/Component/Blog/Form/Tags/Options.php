<?php
declare(strict_types=1);

namespace Mavenbird\Blog\Ui\Component\Blog\Form\Tags;

use Magento\Framework\Data\OptionSourceInterface;
use Mavenbird\Blog\Model\ResourceModel\Tag\CollectionFactory;

class Options implements OptionSourceInterface
{
    public function __construct(
        private readonly CollectionFactory $collectionFactory
    ) {
    }

    public function toOptionArray(): array
    {
        $collection = $this->collectionFactory->create();

        $options = [];
        foreach ($collection as $tag) {
            $options[] = [
                'value' => (int) $tag->getId(),
                'label' => (string) $tag->getName(),
            ];
        }

        return $options;
    }
}

