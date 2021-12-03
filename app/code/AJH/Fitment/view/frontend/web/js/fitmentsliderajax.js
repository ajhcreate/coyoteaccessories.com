define([
    "jquery",
    "jquery/ui"
], function ($) {
    "use strict";

    var ModelsUrl;

    function main(config, element) {
//        var $element = $(element);
//        var AjaxUrl = config.AjaxUrl;
//        var StoreUrl = config.StoreUrl;
//        var YearsUrl = config.YearsUrl;
//        var MakesUrl = config.MakesUrl;
//        ModelsUrl = config.ModelsUrl;
//        var SubModelsUrl = config.SubModelsUrl;

        var slider_carousel = carouselSlider();
        slider_carousel.trigger('refresh.owl.carousel');

        if (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent)
                || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0, 4))) {
            $(".page-top").append($(".fitment-slider-wrapper"));
        }

        reloadElements(config, slider_carousel);
    }

    function reloadElements(config, slider_carousel) {
        $(window).on("resize", function () {
            if (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent)
                    || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0, 4))) {
                $(".page-top").append($(".fitment-slider-wrapper"));
            } else {
                $(".slider-pro-wrapper").prepend($(".fitment-slider-wrapper"));
            }
        });

        $(".fitment-slider-make").on("click", function (e) {
            e.preventDefault();

            var _url = $(this).data("url");

            $.ajax({
                url: _url,
                dataType: 'json',
                showLoader: true,
                data: {
                    make: parseInt($(this).data("makeid"))
                },
                beforeSend: function () {
//                      $(".homepage-slider-loading-wrapper").show("fast");                        
                },
                success: function (resp) {
                    $(".fitment-to-select").text("Year");
                    $("#fitmentSlider").html(resp.outputHtml);
                    slider_carousel.trigger('destroy.owl.carousel');
                    slider_carousel.html(slider_carousel.find('.owl-stage-outer').html()).removeClass('owl-loaded');
                    carouselSlider();
                    reloadElements(config, slider_carousel);
//                      $(".homepage-slider-loading-wrapper").hide("fast");
                },
                error: function (jqXHR, error, status) {
                    console.log("Slider Years:" + status);
                }
            });
        });

        $(".fitment-slider-year").on("click", function (e) {
            e.preventDefault();
            var year = $(this).data("year");
            var make = $(this).data("makeid");
            var _url = $(this).data("url");
            $.ajax({
                url: _url,
                dataType: 'json',
                showLoader: true,
                data: {
                    year: parseInt(year),
                    make: parseInt(make)
                },
                beforeSend: function () {
//                    $(".homepage-slider-loading-wrapper").show("fast");
                },
                success: function (resp) {
                    $(".fitment-to-select").text("Model");
                    $("#fitmentSlider").html(resp.outputHtml);
                    slider_carousel.trigger('destroy.owl.carousel');
                    slider_carousel.html(slider_carousel.find('.owl-stage-outer').html()).removeClass('owl-loaded');
                    carouselSlider();
                    reloadElements(config, slider_carousel);
                },
                error: function (jqXHR, error, status) {
                    console.log(error);
                }
            });
        });

        $(".fitment-slider-model").on("click", function (e) {
            e.preventDefault();

            var year = $(this).data("year");
            var make = $(this).data("makeid");
            var model = $(this).data("modelid");

            $.ajax({
                url: $(this).data("url"),
                dataType: 'json',
                showLoader: true,
                data: {
                    year: parseInt(year),
                    make: parseInt(make),
                    model: parseInt(model),
                    qualifiers: ''
                },
                beforeSend: function () {
//                    $(".homepage-slider-loading-wrapper").show("fast");
                },
                success: function (resp) {
                    $(".fitment-to-select").text("Submodel");
                    $("#fitmentSlider").html(resp.outputHtml);
                    slider_carousel.trigger('destroy.owl.carousel');
                    slider_carousel.html(slider_carousel.find('.owl-stage-outer').html()).removeClass('owl-loaded');
                    carouselSlider();

                    reloadElements(config, slider_carousel);
                },
                error: function (jqXHR, error, status) {
                    console.log(error);
                }
            });
        });

        $(".fitment-slider-submodel").on("click", function (e) {
            e.preventDefault();

            $('body').trigger('show.loader');

            var url = $(this).data("url");
            var year = $(this).data("year");
            var make = $(this).data("makeid");
            var model = $(this).data("modelid");
            var submodel = $(this).data("submodelid");

            var category_url = url + "?year=" + year + "&make=" + make + "&model=" + model + "&submodel=" + submodel;

            console.log(category_url);

            window.location = category_url;
        });
    }

    function fitmentSlider() {
        return {
            year: 0,
            makeID: 0,
            make: 0,
            loadYears: function (makeID) {
                fitmentSlider.makeID = parseInt(makeID);
                $.ajax({
                    url: YearsUrl,
                    dataType: 'json',
                    data: {
                        make: parseInt(makeID)
                    },
                    beforeSend: function () {
//                            $(".homepage-slider-loading-wrapper").show("fast");
                    },
                    success: function (resp) {
                        fitmentSlider.setYears(resp.outputHtml);
                        slider_caoursel.trigger('refresh.owl.carousel');
//                            $(".homepage-slider-loading-wrapper").hide("fast");
                    },
                    error: function (jqXHR, error, status) {

                    }
                });
            },
            setYears: function (resp) {
//                    $(".fitment-slider-row .fitment-group-makes").hide();
//                    $(".fitment-slider-row .fitment-group-years").remove();
                $("#fitmentSlider").html(resp);
            },
            loadModels: function (year, make) {
                fitmentSlider.year = year;
                fitmentSlider.make = make;
                $.ajax({
                    url: ModelsUrl,
                    dataType: 'json',
                    data: {
                        year: parseInt(year),
                        make: parseInt(make)
                    },
                    beforeSend: function () {
                        $(".homepage-slider-loading-wrapper").show("fast");
                    },
                    success: function (resp) {
                        fitmentSlider.setModels(resp.outputHtml);
                        $(".homepage-slider-loading-wrapper").hide("fast");
                    },
                    error: function (jqXHR, error, status) {
                        console.log(error);
                    }
                });
            },
            setModels: function (resp) {
                $(".fitment-slider-row .fitment-group-years").hide();
                $(".fitment-slider-row .fitment-group-models").remove();
                $(".fitment-slider-row").prepend(resp);
            },
            loadSubModels: function (model, url) {
                var year = fitmentSlider.year;
                var make = fitmentSlider.make;

                fitmentSlider.model = model;

                $.ajax({
                    url: SubModelsUrl,
                    data: {
                        year: parseInt(year),
                        make: parseInt(make),
                        model: parseInt(model)
                    },
                    dataType: 'json',
                    beforeSend: function () {
                        $(".homepage-slider-loading-wrapper").show("fast");
                    },
                    success: function (resp) {
                        fitmentSlider.setSubModels(resp.outputHtml);
                        $(".homepage-slider-loading-wrapper").hide("fast");
                    },
                    error: function (jqXHR, error, status) {

                    }
                });
            },
            setSubModels: function (resp) {
                $(".fitment-slider-row .fitment-group-models").hide();
                $(".fitment-slider-row .fitment-group-submodels").remove();
                $(".fitment-slider-row").prepend(resp);
            },
            loadParts: function (submodel, url, cat) {
                var year = fitmentSlider.year;
                var make = fitmentSlider.make;
                var model = fitmentSlider.model;

                $(".homepage-slider-loading-wrapper").show("fast");

                window.location = url + '?year=' + year + '&make=' + make + '&model=' + model + '&submodel=' + submodel + '&cat=' + cat;
            },
            prev: function (ths) {
                var elements = $(ths).parents(".fitment-slider-row").children(".fitment-group-wrapper");

                elements.each(function (indx) {
                    var $this = $(this);
                    if ($this.is(":visible")) {
                        $this.hide('fast', function () {
                            $this.next(".fitment-group-wrapper").toggle();
                        });
                    }
                });
            },
            next: function (ths) {
                var elements = $(ths).parents(".fitment-slider-row").children(".fitment-group-wrapper");

                elements.each(function (indx) {
                    var $this = $(this);
                    if ($this.is(":visible")) {
                        $this.hide('fast', function () {
                            $this.next(".fitment-group-wrapper").show();
                        });
                    }
                });
            }
        };

    }

    function carouselSlider() {
        var carousel = $("#fitmentSlider").owlCarousel({
            autoPlay: true,
            margin: 10,
            items: 5,
            itemsDesktop: [1199, 5],
            itemsDesktopSmall: [979, 5],
            itemsTablet: [768, 5],
            dots: false,
            pagination: false,
            loop: true,
            responsiveClass: true,
            navigation: false,
            nav: true,
            navText: [
                '<i class="fa fa-angle-left" aria-hidden="true"></i>',
                '<i class="fa fa-angle-right" aria-hidden="true"></i>'
            ],
            navContainer: '.main-content .custom-nav',
            responsive: {
                0: {
                    items: 1,
                    nav: true
                },
                414: {
                    items: 2,
                    nav: false,
                    slideBy: 2
                },
                560: {
                    items: 3,
                    nav: false
                },
                768: {
                    items: 4,
                    nav: false,
                    slideBy: 4
                },
                1000: {
                    items: 5,
                    nav: true,
                    loop: false,
                    slideBy: 5
                }
            }
        });

        return carousel;
    }

    return main;
});
