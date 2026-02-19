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
], function (jQuery) {

    var categorySubmit = function () {
        var activeTab = $('active_tab_id');
        if (activeTab) {
            if (activeTab.tabsJsObject && activeTab.tabsJsObject.tabs('activeAnchor')) {
                activeTab.value = activeTab.tabsJsObject.tabs('activeAnchor').prop('id');
            }
        }

        // Submit form
        jQuery('#category_edit_form').trigger('submit');
    };

    return function (config, element) {
        config = config || {};
        jQuery(element).on('click', function (event) {
            categorySubmit(config.url, config.ajax);
        });
    };
});
