<?php
namespace Mavenbird\Blog\Ui\DataProvider\Post\History;

use Mavenbird\Blog\Model\ResourceModel\PostHistory\CollectionFactory;
use Magento\Ui\DataProvider\AbstractDataProvider;

class DataProvider extends AbstractDataProvider
{
    protected $collection;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
    }

    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        if ($filter->getField() === 'post_id') {
            $this->collection->addFieldToFilter('post_id', $filter->getValue());
            return;
        }
        parent::addFilter($filter);
    }
}