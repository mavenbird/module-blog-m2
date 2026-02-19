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

define([
        'jquery'
    ], function ($) {
    "use strict";

        var parentCategory = $(".mb-blog-expand-tree-2");
        var childCategory  = $(".mb-blog-expand-tree-3");

        parentCategory.click(function () {
            if ($(this).hasClass("mb-blog-expand-tree-2")) {
                $(this).parent().find(".category-level3").slideDown("fast");
                $(this).removeClass("mb-blog-expand-tree-2 fa fa-plus-square-o")
                .addClass("mb-blog-narrow-tree-2 fa fa-minus-square-o");
            } else {
                $(this).parent().find(".category-level4").slideUp("fast");
                $(this).parent().find(".category-level3").slideUp("fast");
                $(this).removeClass("mb-blog-narrow-tree-2 fa fa-minus-square-o")
                .addClass("mb-blog-expand-tree-2 fa fa-plus-square-o");
                $(this).parent().find(".mb-blog-narrow-tree-3")
                .removeClass("mb-blog-narrow-tree-3 fa fa-minus-square-o")
                .addClass("mb-blog-expand-tree-3 fa fa-plus-square-o");
            }

        });

        childCategory.click(function () {
            if ($(this).hasClass("mb-blog-expand-tree-3")) {
                $(this).parent().find(".category-level4").slideDown("fast");
                $(this).removeClass("mb-blog-expand-tree-3 fa fa-plus-square-o")
                .addClass("mb-blog-narrow-tree-3 fa fa-minus-square-o");
            } else {
                $(this).parent().find(".category-level4").slideUp("fast");
                $(this).removeClass("mb-blog-narrow-tree-3 fa fa-minus-square-o")
                .addClass("mb-blog-expand-tree-3 fa fa-plus-square-o");
            }
        });
    }
);