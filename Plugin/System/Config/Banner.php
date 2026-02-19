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

namespace Mavenbird\Blog\Plugin\System\Config;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Module\Manager;
use Mavenbird\Blog\Block\Adminhtml\System\Config\Docs;

/**
 * Class Banner
 * @package Mavenbird\Blog\Plugin\System\Config
 */
class Banner
{
    /**
     * @var Manager
     */
    protected $_moduleManager;

    /**
     * Banner constructor.
     *
     * @param Manager $moduleManager
     */
    public function __construct(
        Manager $moduleManager
    ) {
        $this->_moduleManager = $moduleManager;
    }

    /**
     * @param Docs $subject
     * @param $result
     * @param AbstractElement $element
     *
     * @return mixed
     */
    public function afterRender(Docs $subject, $result, AbstractElement $element)
    {
        if ($this->isHideBanner($element)) {
            return $result;
        }
        $bannerImg = $subject->getViewFileUrl('Mavenbird_Blog::media/banner/banner.png');
        $html      = <<<HTML
        <script>
            require([ 'jquery'], function ($) {
                var session = $(".accordion" );
                $("<a target='_blank' href='https://www.mavenbird.com/magento-2-better-blog/?utm_source=dashboard&utm_medium=admin&utm_campaign=blogpro'>" +
                 "<img src='{$bannerImg}'></a>").insertBefore(session);
            })
        </script>
        HTML;

        $result = $html . $result;

        return $result;
    }

    /**
     * @param $element
     * @return bool
     */
    protected function isHideBanner($element)
    {
        if ($element->getOriginalData()['module_name'] !== 'Mavenbird_Blog') {
            return true;
        }

        if ($this->_moduleManager->isOutputEnabled('Mavenbird_BlogPro')) {
            return true;
        }

        return false;
    }
}
