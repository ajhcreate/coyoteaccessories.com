define([
    "jquery",
    "jquery/ui"
], function ($) {
    "use strict";
    function main(config, element) {        
        var yearsUrl = config.yearsUrl;
        var makesUrl = config.makesUrl;
        var modelsUrl = config.modelsUrl;
        var submodelsUrl = config.submodelsUrl;
        var qualifiersUrl = config.qualifiersUrl;
        var categoriesUrl = config.categoriesUrl;        
        $(document).ready(function () {

            $(".select-fitment-label").on("click", function () {
                if ($(".fitment-label-direction").is(":visible")) {
                    $(".fitment-label-direction").toggleClass("fa-angle-right");
                    $(".fitment-label-direction").toggleClass("fa-angle-down");
                    $(".fitment-main-wrap").slideToggle("fast");
                }
            });
            
             $.ajax({                    
                    url: yearsUrl,
                    data: {},
                    beforeSend: function () {
                        $('#fitment_year').attr('disabled', true);
                        $('#fitment_make').prop('selectedIndex', 0).attr('disabled', true);
                        $('#fitment_model').prop('selectedIndex', 0).attr('disabled', true);
                        $('#fitment_submodel').prop('selectedIndex', 0).attr('disabled', true);
                        $('#fitment_parts').attr('disabled', true);
                        $('#qualifier_wrap').html('');
                    },
                    success: function (resp) {                          
                        $('#fitment_year').html(resp);   
                        $('#fitment_year').attr('disabled', false);
                    },
                    error: function (jqXHR, error, status) {

                        console.log('Fitment Years:' + status);
                        console.log(jqXHR);
                        console.log(error);                        
                    }
                });

            $("#fitment_year").on('change', function () {
                $.ajax({
                    showLoader: true,
                    url: makesUrl,
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
                        $('#fitment_make').html(resp);
                        $('#fitment_make').attr('disabled', false);
                        $('.make-wrapper').find(".fitment-loader").remove();
                    },
                    error: function (jqXHR, error, status) {

                    }
                });
            });

            $("#fitment_make").on('change', function () {
                $.ajax({
                    showLoader: true,
                    url: modelsUrl,
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

            $("#fitment_model").on('change', function () {
                $.ajax({
                    showLoader: true,
                    url: submodelsUrl,
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
                $.ajax({
                    showLoader: true,
                    url: qualifiersUrl,
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
                        $(".select-fitment-label span").hide("slide", {direction: "left"}, 1000);
                    },
                    error: function (jqXHR, error, status) {
                        console.log('error');
                        console.log(error);
                        console.log(status);
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

                $.ajax({
                    showLoader: true,
                    url: categoriesUrl,
                    dataType: "json",
                    data: {
                        year: $('#fitment_year').val(),
                        make: $('#fitment_make').val(),
                        model: $('#fitment_model').val(),
                        submodel: $('#fitment_submodel').val(),
                        qualifiers: qualifiers,
                        _qualifiers: _qualifiers
                    },
                    beforeSend: function () {
                    
                    },
                    success: function (resp) {                        

                        if ($(".slideshow-wrapper-additional").length) {
                            $(".slideshow-wrapper-additional").remove();
                        }
                        $("#maincontent .column.main").html(resp.categories);
                        window.history.pushState('', 'Fitment Categories', resp.url_path);
                    },
                    error: function (jqXHR, error, status) {
                        console.log(jqXHR);
                        console.log(error);
                        console.log(status);
                    }
                });
            });
        });
    }
    ;
    return main;
});