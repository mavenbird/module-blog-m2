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

namespace Mavenbird\Blog\Helper;

use Mavenbird\Blog\Helper\Media;

/**
 * Class Image
 * @package Mavenbird\Blog\Helper
 */
class Image extends Media
{
    const TEMPLATE_MEDIA_PATH = 'mavenbird/blog';
    const TEMPLATE_MEDIA_TYPE_AUTH = 'auth';
    const TEMPLATE_MEDIA_TYPE_POST = 'post';
}
