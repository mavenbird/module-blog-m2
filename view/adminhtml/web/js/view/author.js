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
    'jquery',
    'Magento_Ui/js/modal/modal',
    'mage/translate'
], function ($, modal, $t) {
    "use strict";

    $.widget('mavenbird.mbBlogAuthor', {
        options: {
            url: ''
        },
        isloaded: false,

        /**
         * This method constructs a new widget.
         * @private
         */
        _create: function () {
            this.initCustomerGrid();
            this.selectCustomer();
        },

        /**
         * Init popup
         * Popup will automatic open
         */
        initPopup: function () {
            var options = {
                type: 'popup',
                responsive: true,
                innerScroll: true,
                title: $t('Select Customer'),
                buttons: []
            },
            customerGridEl = $('#customer-grid');

            modal(options, customerGridEl);
            customerGridEl.modal('openModal');
        },

        /**
         * Init select customer
         */
        selectCustomer: function () {
            $('body').delegate('#customer-grid_table tbody tr', 'click', function () {
                var first_name = $(this).find('td:nth-child(3)').text().trim(),
                    last_name = $(this).find('td:nth-child(4)').text().trim();

                $("#author_customer_id").val($(this).find('input').val().trim());
                $("#author_customer").val(first_name+' '+last_name);
                $('#customer-grid').data('mageModal').closeModal();
            });
        },

        /**
         * Init customer grid
         */
        initCustomerGrid: function () {
            var self = this;

            $("#author_customer").click(function () {
                $.ajax({
                    method: 'POST',
                    url: self.options.url,
                    data: {form_key: window.FORM_KEY},
                    showLoader: true
                }).done(function (response) {
                    $('#customer-grid').html(response);
                    self.initPopup();
                });
            });
        }
    });

    return $.mavenbird.mbBlogAuthor;
});

