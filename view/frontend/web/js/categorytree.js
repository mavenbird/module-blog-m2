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

define(['jquery'], function ($) {
    'use strict';

    return function (config, element) {
        var $root = $(element);

        $root.on('click', '.mb-category-toggle', function (e) {
            e.preventDefault();

            var $toggle = $(this);
            var $children = $toggle.closest('li').children('.category-children');

            if (!$children.length) {
                return;
            }

            $children.slideToggle(200);

            if ($toggle.hasClass('active')) {
                $toggle.removeClass('active')
                       .html('<i class="fa-solid fa-plus"></i>');
            } else {
                $toggle.addClass('active')
                       .html('<i class="fa-solid fa-minus"></i>');
            }
        });
    };
});