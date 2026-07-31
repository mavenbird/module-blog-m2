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

namespace Mavenbird\Blog\Controller\Adminhtml\Post;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Helper\Js;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Mavenbird\Blog\Controller\Adminhtml\Post;
use Mavenbird\Blog\Helper\Data;
use Mavenbird\Blog\Helper\Image;
use Mavenbird\Blog\Model\Post as PostModel;
use Mavenbird\Blog\Model\PostFactory;
use Mavenbird\Blog\Model\PostHistoryFactory;
use RuntimeException;

/**
 * Class Save
 * @package Mavenbird\Blog\Controller\Adminhtml\Post
 */
class Save extends Post
{
    /**
     * JS helper
     *
     * @var Js
     */
    public $jsHelper;

    /**
     * @var DateTime
     */
    public $date;

    /**
     * @var Image
     */
    protected $imageHelper;

    /**
     * @var Data
     */
    protected $_helperData;

    /**
     * @var PostHistoryFactory
     */
    protected $_postHistory;

    /**
     * @var TimezoneInterface
     */
    protected $timezone;

    /**
     * Save constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param PostFactory $postFactory
     * @param Js $jsHelper
     * @param Image $imageHelper
     * @param Data $helperData
     * @param PostHistoryFactory $postHistory
     * @param DateTime $date
     * @param TimezoneInterface $timezone
     */
    public function __construct(
        Context $context,
        Registry $registry,
        PostFactory $postFactory,
        Js $jsHelper,
        Image $imageHelper,
        Data $helperData,
        PostHistoryFactory $postHistory,
        DateTime $date,
        TimezoneInterface $timezone
    ) {
        $this->jsHelper     = $jsHelper;
        $this->_helperData  = $helperData;
        $this->_postHistory = $postHistory;
        $this->imageHelper  = $imageHelper;
        $this->date         = $date;
        $this->timezone     = $timezone;

        parent::__construct($postFactory, $registry, $context);
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $logger = $objectManager->get(\Psr\Log\LoggerInterface::class);
        $logger->debug('Save Controller triggered. POST data: ' . print_r($this->getRequest()->getPost(), true));

        if ($data = $this->getRequest()->getPost('post')) {
            /** @var PostModel $post */
            $post = $this->initPost(false, true);

            // "action" can be sent either as top-level request param or inside "post" payload.
            // For UI additional data, sometimes it becomes post[action] => 'add'/'draft'.
            $action = (string)($this->getRequest()->getParam('action') ?? '');
            if ($action === '' && isset($data['action'])) {
                $action = (string) $data['action'];
                unset($data['action']); // don't try to save it to the DB model
            }

            $this->prepareData($post, $data);
            try {
                if (empty($action) || $action === 'add' || $action === 'draft') {
                    if ($action === 'draft') {
                        // Draft should be saved as "Pending" (enabled = 0)
                        $data['enabled'] = 0;
                        $post->setEnabled(0);
                    }
                    $post->save();
                    // Hard-sync product relations to guarantee persistence from admin grid selections.
                    $this->syncPostProductRelations((int)$post->getId(), (array)$post->getProductsData());
                    $this->_eventManager->dispatch(
                        'mavenbird_blog_post_prepare_save',
                        ['post' => $post, 'request' => $this->getRequest()]
                    );
                    $this->messageManager->addSuccessMessage(__('The post has been saved.'));
                    // Always create a history snapshot after a successful save
                    // so that the History tab is reliably populated.
                    $this->addHistory($post, 'add');
                }

                $this->_getSession()->setData('mavenbird_blog_post_data', false);

                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath('mavenbird_blog/*/edit', ['id' => $post->getId(), '_current' => true]);
                } else {
                    $resultRedirect->setPath('mavenbird_blog/*/');
                }

                return $resultRedirect;
            } catch (RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Post.'));
            }

            $this->_getSession()->setData('mavenbird_blog_post_data', $data);

            $resultRedirect->setPath('mavenbird_blog/*/edit', ['id' => $post->getId(), '_current' => true]);

            return $resultRedirect;
        }

        $resultRedirect->setPath('mavenbird_blog/*/');

        return $resultRedirect;
    }

    /**
     * @param PostModel $post
     * @param null $action
     */
    protected function addHistory($post, $action = null)
    {
        if (!empty($action)) {
            $history      = $this->_postHistory->create();
            $historyCount = $history->getSumPostHistory($post->getPostId());
            $limitHistory = (int) $this->_helperData->getHistoryLimit();
            try {
                $data = $post->getData();
                unset(
                    $data['is_changed_tag_list'],
                    $data['is_changed_topic_list'],
                    $data['is_changed_category_list'],
                    $data['is_changed_product_list']
                );
                if ($isSave = $this->checkHistory($data)) {
                    $this->messageManager->addErrorMessage(__(
                        'Record Id %1 like the one you want to save.',
                        $isSave->getId()
                    ));
                } else {
                    if ($historyCount >= $limitHistory) {
                        $history->removeFirstHistory($post->getPostId());
                    }
                    $history->addData($data);
                    $history->save();
                }
            } catch (RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while saving the Post History.')
                );
            }
        }
    }

    /**
     * @param array $data
     *
     * @return DataObject|null
     */
    protected function checkHistory($data)
    {
        unset($data['updated_at']);
        $historyItems = $this->_postHistory->create()->getCollection()
            ->addFieldToFilter('post_id', $data['post_id'])->getItems();

        if (count($historyItems) < 1) {
            return null;
        }
        $data['category_ids'] = implode(',', $data['categories_ids']);
        $data['topic_ids']    = implode(',', $data['topics_ids']);
        $data['tag_ids']      = implode(',', $data['tags_ids']);
        $data['product_ids']  = Data::jsonEncode($data['products_data']);

        $result = null;
        foreach ($historyItems as $historyItem) {
            $subReturn = false;
            foreach ($historyItem->getData() as $key => $value) {
                if (array_key_exists($key, $data)) {
                    if (is_array($data[$key])) {
                        $data[$key] = trim(implode(',', $data[$key]), ',');
                    }
                    if ($data[$key] === null) {
                        $data[$key] = '';
                    }
                    if ($value === null) {
                        $value = '';
                    }
                    if ($data[$key] !== $value) {
                        $subReturn = true;
                        break;
                    }
                }
            }

            if (!$subReturn) {
                $result = $historyItem;
                break;
            }
        }

        return $result;
    }

    /**
     * @param PostModel $post
     * @param array $data
     *
     * @return $this
     * @throws LocalizedException
     */
    protected function prepareData($post, $data = [])
    {
        // Match Adminhtml Post/Upload: UI submits files as post[image], but Media uploader expects $_FILES['image'].
        if (isset($_FILES['post']['name']['image']) && !isset($_FILES['image'])) {
            $_FILES['image'] = [
                'name'     => $_FILES['post']['name']['image'],
                'type'     => $_FILES['post']['type']['image'],
                'tmp_name' => $_FILES['post']['tmp_name']['image'],
                'error'    => $_FILES['post']['error']['image'],
                'size'     => $_FILES['post']['size']['image'],
            ];
        }

        if (isset($data['image']) && is_array($data['image']) && !empty($data['image'])) {
            // imageUploader stores the saved relative path in "file"; "name" may be display-only.
            if (isset($data['image'][0]['file'])) {
                $data['image'] = $data['image'][0]['file'];
            } elseif (isset($data['image'][0]['name'])) {
                $data['image'] = $data['image'][0]['name'];
            } elseif (isset($data['image']['name'])) {
                $data['image'] = $data['image']['name'];
            } elseif (isset($data['image']['value'])) {
                $data['image'] = $data['image']['value'];
            } else {
                $data['image'] = '';
            }
        } elseif (isset($data['image']) && is_string($data['image'])) {
            // Already a string, do nothing
        } else {
            $data['image'] = '';
        }

        // Upload image if needed
        if (!empty($data['image']) && isset($_FILES['image'])) {
            try {
                $this->imageHelper->uploadImage($data, 'image', Image::TEMPLATE_MEDIA_TYPE_POST, $post->getImage());
            } catch (Exception $exception) {
                $data['image'] = '';
            }
        }

        /** Set specify field data */
        try {
            $data['publish_date'] = $this->timezone->convertConfigTimeToUtc($data['publish_date']);
        } catch (Exception $e) {
            $data['publish_date'] = $this->timezone->convertConfigTimeToUtc($this->date->date());
        }

        $data['modifier_id']    = $this->_auth->getUser()->getId();
        $data['categories_ids'] = $this->normalizeIdsField($data['categories_ids'] ?? null);
        $data['tags_ids']       = $this->normalizeIdsField($data['tags_ids'] ?? null);
        $data['topics_ids']     = $this->normalizeIdsField($data['topics_ids'] ?? null);

        if ($post->getCreatedAt() == null) {
            $data['created_at'] = $this->date->date();
        }
        $data['updated_at'] = $this->date->date();
        $this->_helperData->handleSeoValueBeforeSave($data);

        $post->addData($data);

        if ($tags = $this->getRequest()->getPost('tags', false)) {
            $post->setTagsData(
                $this->jsHelper->decodeGridSerializedInput($tags)
            );
        }

        if ($topics = $this->getRequest()->getPost('topics', false)) {
            $post->setTopicsData(
                $this->jsHelper->decodeGridSerializedInput($topics)
            );
        }

        $products = $this->getRequest()->getPost('products', false);
        if ($products === false) {
            // Fallback key used by some legacy grid serializers
            $products = $this->getRequest()->getPost('post_products', false);
        }

        if ($products || $products === '' || is_array($products)) {
            if (!is_array($products)) {
                $products = $this->jsHelper->decodeGridSerializedInput((string)$products);
            }

            $normalizedProducts = $this->normalizeProductsData((array)$products);
            $post->setProductsData($normalizedProducts);
        } else {
            $productData = [];
            foreach ($post->getProductsPosition() as $key => $value) {
                $productData[$key] = ['position' => $value];
            }
            $post->setProductsData($productData);
        }

        return $this;
    }

    /**
     * Normalize incoming ids field (UI multiselect posts array, legacy renderer posts comma string).
     *
     * @param mixed $value
     * @return array
     */
    private function normalizeIdsField($value): array
    {
        if ($value === null || $value === '' || $value === false) {
            return [];
        }

        if (is_array($value)) {
            $value = array_filter($value, static fn($v) => $v !== '' && $v !== null);
            return array_values(array_map('intval', $value));
        }

        $value = trim((string) $value);
        if ($value === '') {
            return [];
        }

        return array_values(array_map('intval', array_filter(explode(',', $value), static fn($v) => $v !== '')));
    }

    /**
     * Normalize product relation data to [entityId => ['position' => int]].
     *
     * @param array $products
     * @return array
     */
    private function normalizeProductsData(array $products): array
    {
        $normalized = [];

        foreach ($products as $entityId => $positionData) {
            // Simplified format from serializer: [0 => 51, 1 => 52]
            if (is_int($entityId) && !is_array($positionData)) {
                $id = (int)$positionData;
                if ($id > 0) {
                    $normalized[$id] = ['position' => 0];
                }
                continue;
            }

            // Full format: [51 => ['position' => 0]]
            if (is_array($positionData)) {
                $id = (int)$entityId;
                if ($id > 0) {
                    $normalized[$id] = [
                        'position' => (int)($positionData['position'] ?? 0)
                    ];
                }
                continue;
            }

            // Fallback scalar keyed by entity id.
            $id = (int)$entityId;
            if ($id > 0) {
                $normalized[$id] = ['position' => 0];
            }
        }

        return $normalized;
    }

    /**
     * Directly synchronize post-product relation table.
     *
     * @param int $postId
     * @param array $productsData
     * @return void
     */
    private function syncPostProductRelations(int $postId, array $productsData): void
    {
        if ($postId <= 0) {
            return;
        }

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $resource = $objectManager->get(\Magento\Framework\App\ResourceConnection::class);
        $connection = $resource->getConnection();
        $table = $resource->getTableName('mavenbird_blog_post_product');

        $connection->delete($table, ['post_id = ?' => $postId]);

        if (!$productsData) {
            return;
        }

        $rows = [];
        foreach ($productsData as $entityId => $rowData) {
            $entityId = (int)$entityId;
            if ($entityId <= 0) {
                continue;
            }
            $rows[] = [
                'post_id' => $postId,
                'entity_id' => $entityId,
                'position' => (int)($rowData['position'] ?? 0),
            ];
        }

        if ($rows) {
            $connection->insertMultiple($table, $rows);
        }
    }
}
