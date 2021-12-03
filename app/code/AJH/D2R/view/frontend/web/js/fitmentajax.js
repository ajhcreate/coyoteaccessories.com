define([
    "jquery",
    "jquery/ui"
], function ($) {
    "use strict";

    function main(config, element) {
        var $element = $(element);
        var AjaxUrl = config.AjaxUrl;

        $(document).ready(function () {
            $("#fitment_year").on('change', function () {
                $.ajax({
                    showLoader: true,
                    url: AjaxUrl,
                    data: {
                        year: $('#fitment_year').val()
                    },
                    beforeSend: function () {
                        $('#fitment_make').prop('selectedIndex', 0).attr('disabled', true);
                        $('#fitment_model').prop('selectedIndex', 0).attr('disabled', true);
                        $('#fitment_submodel').prop('selectedIndex', 0).attr('disabled', true);
                        $('#fitment_parts').attr('disabled', true);
                        $('#qualifier_wrap').html('');
                    },
                    success: function (resp) {
//                        console.log(resp);
                        console.log('fitment makes');
//                        fitment.setMakes(resp);
                        $('#fitment_make').attr('disabled', false);
                        $('#fitment_make').html(resp);
                        $('.make-wrapper').find(".fitment-loader").remove();
                    },
                    error: function (jqXHR, error, status) {

                    }
                });
            });

            $("#fitment_model").on('change', function () {
                var _AjaxUrl = AjaxUrl.replace("makes", "submodels");
                $.ajax({
                    showLoader: true,
                    url: _AjaxUrl,
                    data: {
                        year: $('#fitment_year').val(),
                        make: $('#fitment_make').val(),
                        model: $('#fitment_model').val()
                    },
                    beforeSend: function () {
                        $('#fitment_submodel').prop('selectedIndex', 0).attr('disabled', true);
                        $('#fitment_parts').attr('disabled', true);
                        $('#qualifier_wrap').html('');
                    },
                    success: function (resp) {
                        $('#fitment_submodel').attr('disabled', false);
                        $('#fitment_submodel').html(resp);
                    },
                    error: function (jqXHR, error, status) {

                    }
                });
            });
            $("#fitment_submodel").on('change', function () {
                var _AjaxUrl = AjaxUrl.replace("makes", "qualifiers");
                $.ajax({
                    showLoader: true,
                    url: _AjaxUrl,
                    data: {
                        year: $('#fitment_year').val(),
                        make: $('#fitment_make').val(),
                        model: $('#fitment_model').val(),
                        submodel: $('#fitment_submodel').val()
                    },
                    beforeSend: function () {
                        $('#fitment_parts').attr('disabled', false);
                    },
                    success: function (resp) {
                        $('#qualifier_wrap').html(resp);
                    },
                    error: function (jqXHR, error, status) {

                    }
                });
            });

            $("#fitment_make").on('change', function () {
                var _AjaxUrl = AjaxUrl.replace("makes", "models");
                $.ajax({
                    showLoader: true,
                    url: _AjaxUrl,
                    data: {
                        year: $('#fitment_year').val(),
                        make: $('#fitment_make').val()
                    },
                    beforeSend: function () {
                        $('#fitment_model').prop('selectedIndex', 0).attr('disabled', true);
                        $('#fitment_submodel').prop('selectedIndex', 0).attr('disabled', true);
                        $('#fitment_parts').attr('disabled', true);
                        $('#qualifier_wrap').html('');
                    },
                    success: function (resp) {
                        $('#fitment_model').attr('disabled', false);
                        $('#fitment_model').html(resp);
                        $('.make-wrapper').find(".fitment-loader").remove();
                    },
                    error: function (jqXHR, error, status) {

                    }
                });
            });


            $("#fitment_parts").on('click', function () {
                var qualifiers = [];
                var _qualifiers = [];


                $('select[name="qualifiers[]"]').each(function (index) {
                    qualifiers.push($(this).val());
                    _qualifiers.push($(this).children("option:selected").data('title'));
                });

                var newurl = window.location.protocol + "//" + window.location.host + '/fitment/index/categories/?year=' + $('#fitment_year').val() + '&make=' + $('#fitment_make').val() + '&model=' + $('#fitment_model').val() + '&submodel=' + $('#fitment_submodel').val() + '&qualifiers[]=' + qualifiers + '&_qualifiers[]=' + _qualifiers;
                window.location = newurl;
            });

        });



//        var dataForm = $('#contact-form');
//        dataForm.mage('validation', {});
//
//        $(document).on('click', '.submit', function () {
//
//            if (dataForm.valid()) {
//                event.preventDefault();
//                var param = dataForm.serialize();
//                alert(param);
//                $.ajax({
//                    showLoader: true,
//                    url: AjaxUrl,
//                    data: param,
//                    type: "POST"
//                }).done(function (data) {
//                    $('.note').html(data);
//                    $('.note').css('color', 'red');
//                    document.getElementById("contact-form").reset();
//                    return true;
//                });
//            }
//        });
    }
    ;
    return main;
});