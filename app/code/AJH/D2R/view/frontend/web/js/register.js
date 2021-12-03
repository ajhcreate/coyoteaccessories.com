define([
    'jquery',
    'Magento_Ui/js/modal/alert',
    'mage/validation',
    "jquery/ui"
], function ($, alert) {

    'use strict';
//    $.widget('mage.registerjs', {
//        options: {
//            confirmMsg: ('testing')
//        },
//        init: function (configValues) {
//            $.extend(true, this.config, configValues);
//            this.config.baseURL = this.config.baseURL.replace('http:', location.protocol);
//            if (window.hasOwnProperty('loader')) {
//                this.loader = window.loader;
//            }
//            if (window.hasOwnProperty('map')) {
//                this.config.map = window.map;
//            }
//        },
//        _create: function () {
//            var self = this;
//            
//            return {
//              init: function(){                  
//                alert({
//                    content: self.options.confirmMsg
//                });
//              }  
//            };
//
//

//
//        }
//    });
//    
//    return $.mage.registerjs;



    return {
        config: {
            baseURL: '',
            map: null,
            dataForm: null,
            loaderTargetSelector: '',
            templates: {
            },
            messages: {
                error: 'Some error occurred, please contact site admin'
            },
            selectors: {
                form: '#form-validate',
                success: '#d2rRR-success',
                distributorId: '#distributorId'
            }
        },
        loader: null,
        _init: function (configValues) {
            console.log($("select[name=reset_tool_use]").length);

            $.extend(true, this.config, configValues);
            this.config.baseURL = this.config.baseURL.replace('http:', location.protocol);
            if (window.hasOwnProperty('loader')) {
                this.loader = window.loader;
            }
            if (window.hasOwnProperty('map')) {
                this.config.map = window.map;
            }

            this._events();
        },
        _events: function () {

            $("select[name=reset_tool_use]").on('change', function () {
                if (($(this).val()).toLowerCase() === 'pdq') {

                    $(".other-brand").slideUp('fast', function () {
                        $(this).find("input").val('');
                        $(this).children("label").removeClass("required");
                        $(this).find("input[type=text]").removeClass("required-entry");
                    });

                    $(".pdq-serial-number").slideDown('fast', function () {
                        $(this).children("label").addClass("required");
                        $(this).find("input[type=text]").addClass("required-entry");
                    });

                    if ($("button.d2rRR-submit-register-tool").is(":visible") || $("button.d2rRR-submit-register").is(":visible")) {
                        $("button.d2rRR-submit-register-tool").css({display: 'inline'});
                        $("button.d2rRR-submit-register").css({display: 'none'});
                    }

                } else if (($(this).val()).toLowerCase() === 'other') {
                    $(".pdq-serial-number").slideUp('fast', function () {
                        $(this).find("input").val('');
                        $(this).children("label").removeClass("required");
                        $(this).find("input[type=text]").removeClass("required-entry");
                    });
                    $(".other-brand").slideDown('fast', function () {
                        $(this).children("label").addClass("required");
                        $(this).find("input[type=text]").addClass("required-entry");
                    });
                    if ($("button.d2rRR-submit-register-tool").is(":visible") || $("button.d2rRR-submit-register").is(":visible")) {
                        $("button.d2rRR-submit-register-tool").css({display: 'none'});
                        $("button.d2rRR-submit-register").css({display: 'inline'});
                    }
                } else {
                    if ($("button.d2rRR-submit-register-tool").is(":visible") || $("button.d2rRR-submit-register").is(":visible")) {
                        $("button.d2rRR-submit-register-tool").css({display: 'none'});
                        $("button.d2rRR-submit-register").css({display: 'inline'});
                    }
                    $(".pdq-serial-number").slideUp('fast', function () {
                        $(this).children("label").removeClass("required");
                        $(this).find("input[type=text]").removeClass("required-entry");
                    });
                    $(".other-brand").slideUp('fast', function () {
                        $(this).children("label").removeClass("required");
                        $(this).find("input[type=text]").removeClass("required-entry");
                    });
                }
            });
        },
        showLoader: function (targetSelector) {
            if (this.loader) {
                if ('undefined' == typeof (targetSelector)) {
                    targetSelector = this.config.selectors.self;
                }
                this.config.loaderTargetSelector = targetSelector;
                this.loader.show(targetSelector);
            }
        },
        hideLoader: function () {
            if (this.loader) {
                this.loader.hide(this.config.loaderTargetSelector);
            }
        },
        inform: function (message, delayed) {
            if (window.hasOwnProperty('informer')) {
                if ('undefined' == typeof (delayed)) {
                    delayed = true;
                }
                if (delayed)
                    window.informer.appear(message, this.config.messageTimeoutInterval);
                else
                    window.informer.show(message);
            } else
                alert({content: message });
            
            
        },
        _processResponse: function (response) {
            if (response.debug) {
                for (var i in response.debug) {
                    $('#debug').append(response.debug[i]);
                }
            }
            if (response.errorMessage) {
                alert({content:response.errorMessage});
                return false;
            }
            if (response.errorMessages) {
                for (var i = 0; i < response.errorMessages.length; i++) {
                    alert({content:response.errorMessages[i]});
                }
                return false;
            }
            if (response.message) {
                this.inform(response.message);
            }
            if (response.messages) {
                for (var i = 0; i < response.messages.length; i++) {
                    alert({content:response.messages[i]});
                }
            }
            if (response.stop) {
                return false;
            }
            return true;
        },
        submit: function (url) {
            var dataForm = this.config.dataForm;
            var ignore = null;

            dataForm.mage('validation', {
                ignore: ignore ? ':hidden:not(' + ignore + ')' : ':hidden'
            }).find('input:text').attr('autocomplete', 'off');

//            $('button#my-button').click(function () { //can be replaced with any event
//                dataForm.validation('isValid'); //validates form and returns boolean
//            });
            
            if (!dataForm.validation('isValid'))
                return false;

            var d = this.config.map.selected;
            if (!d) {
                this.inform('Please select a distributor on the map');
                $(this.config.map.config.selectors.self)[0].scrollIntoView();
                return;
            }
            $(this.config.selectors.distributorId).val(d.ID);
            var data = $(this.config.selectors.form).serialize();                        

//            $(".retailer-register-form").submit();

            $.ajax({
                url: this.config.baseURL,
                data: data,
                dataType: 'json',
//			type: 'POST',
                cache: false,
                beforeSend: this._beforeSend.bind(this),
                success: this._submit.bind(this),
                complete: this.hideLoader.bind(this),
                error: function (x, y, z) {
                    console.log(x);
                    console.log(y);
                    console.log(z);
                }
            });
            
//            if(url!==''){
//                window.location=url;
//            }
        },
        _submit: function (response) {
            if (!this._processResponse(response))
                return;
            if (response.html) {
                $('#exampleModal').modal('show');
                $('#exampleModal').on('shown.bs.modal', function () {
                    $('#exampleModal').find(".modal-body").html('<iframe width="865" height="450" src="https://www.youtube.com/embed/s0xxDrRahfo?rel=0&amp;start=38&amp;autoplay=1" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>');
                    console.log('video loaded!');
                });

                $(this.config.selectors.form).hide();
                $(this.config.selectors.success).html(response.html);
            }
        },
        _beforeSend: function(){
            console.log('init before send');
        }
    };


});