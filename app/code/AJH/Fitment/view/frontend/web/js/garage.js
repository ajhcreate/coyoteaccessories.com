define([
    'jquery',
    'Magento_Ui/js/modal/alert',
    'mage/validation',
    "jquery/ui"
], function ($, alert) {
    'use strict';

    $.widget('mage.garagejs', {
        options: {
            content: ('')
        },
        _create: function () {
            var self = this;
            console.log(self.options.AjaxUrl);
            $("#header-my-garage, #mobile-menu-my-garage").on("click", function () {
                $.ajax({
                    showLoader: true,
                    url: self.options.AjaxUrl,
                    data: {},
                    dataType: 'json',
                    beforeSend: function(){
                        console.log('load garage');
                    },
                    success: function (resp) {
                        alert({
                            title: '',
                            content: resp.outputHtml,
                            modalClass: 'garage-modal-container',
                            actions: {
                                always: function () {
                                    
                                }
                            },
                            buttons: []
                        });
                    },
                    error: function (jqXHR, error, status) {
                        console.log(error);
                        console.log(status);
                    }
                });

            });


        }
    });

    return $.mage.garagejs;
});