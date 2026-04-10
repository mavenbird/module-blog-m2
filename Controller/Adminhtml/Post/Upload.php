<?php

namespace Mavenbird\Blog\Controller\Adminhtml\Post;

use Magento\Framework\Controller\ResultFactory;

class Upload extends \Magento\Backend\App\Action
{
    /**
     * @var \Mavenbird\Blog\Model\ImageUploader
     */
    public $imageUploader;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Mavenbird\Blog\Model\ImageUploader $imageUploader
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Mavenbird\Blog\Model\ImageUploader $imageUploader
    ) {
        parent::__construct($context);
        $this->imageUploader = $imageUploader;
    }

    /**
     * Check if allowed to manage industries
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Mavenbird_Blog::post');
    }

    /**
     * Upload file controller action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if (isset($_FILES['post']['name']['image'])) {
            $_FILES['image'] = [
                'name'     => $_FILES['post']['name']['image'],
                'type'     => $_FILES['post']['type']['image'],
                'tmp_name' => $_FILES['post']['tmp_name']['image'],
                'error'    => $_FILES['post']['error']['image'],
                'size'     => $_FILES['post']['size']['image'],
            ];
        }
        try {
            $result = $this->imageUploader->saveFileToTmpDir('image');
            $result['cookie'] = [
                'name' => $this->_getSession()->getName(),
                'value' => $this->_getSession()->getSessionId(),
                'lifetime' => $this->_getSession()->getCookieLifetime(),
                'path' => $this->_getSession()->getCookiePath(),
                'domain' => $this->_getSession()->getCookieDomain(),
            ];
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
