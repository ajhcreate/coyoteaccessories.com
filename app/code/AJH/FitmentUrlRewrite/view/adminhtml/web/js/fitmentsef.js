define([
    "jquery",
    "jquery/ui"
], function ($) {
    "use strict";
//    var years = [];
//    var interval = 5000;
    
    console.log('in here');

    return function (config, element) {
        alert(config.message);
    };
//        var fitment = (function ($) {
//            return {
//                makeready: true,
//                modelready: true,
//                submodelready: true,
//                sitemapready: true,
//                years: [],
//                frequency: 10000,
//                init: function (years, frequency) {
//                    this.years = years;
//
//                    if (this.years.length > 0) {
//
//                        this.frequency = frequency >= 5000 ? frequency : 5000;
//
//                        setInterval(function () {
//                            $("#fitment-ajax-output").scrollTop($("#fitment-ajax-output").height());
//                        }, frequency);
//
//                        console.log(this.years.length);
//
//                        $("#fitment-ajax-output").append('<div>Start processing all fitment URLs for year [' + this.years.join(',') + ']</div>');
//                        this.loadMakes();
//
//                    } else {
//                        $("#fitment-ajax-output").append('<div style="color: #f00;">Please select at least 1 fitment year.</div>');
//                    }
//
//                },
//                loadMakes: function () {
//                    var years = this.years;
//
//                    var interval = setInterval((function () {
//
//                        if (this.makeready) {
//                            $("#fitment-ajax-output").append('<div>' + 'Start processing fitment urls... ' + fitment.makeready + '</div>');
//
//                            if (years.length > 0) {
//                                this.makeready = false;
//                                var year = years[0];
//                                $.ajax({
//                                    method: 'POST',
//                                    url: "<?php /* @escapeNotVerified */ echo $block->getAjaxUrl(); ?>" + "?isAjax=true", // <-- you need to add 'isAjax=true' to your URL
//                                    data: {
//                                        request: 'make',
//                                        year: year,
//                                        form_key: window.FORM_KEY  // <-- you need to add the current form key to your POST data
//                                    },
//                                    dataType: "json",
//                                    beforeSend: function () {
//                                        $("#fitment-ajax-output").append('<div>&nbsp;</div>');
//                                        $("#fitment-ajax-output").append('<div>' + 'Year: ' + years[0] + '</div>');
//                                        $("#fitment-ajax-output").append('<div>' + 'Loading MAKES...' + '</div>');
//
//                                        console.log('Loading MAKES from API...');
//                                    },
//                                    success: function (resp) {
//                                        years.splice(0, 1);
//                                        fitment.saveMakes(resp);
//                                    },
//                                    error: function (jqXHR, textStatus, errorThrown) {
//                                        console.log(textStatus);
//                                        console.log(errorThrown);
//                                    }
//                                });
//
//                            } else {
//                                clearInterval(interval);
//                            }
//                        }
//
//                    }).bind(fitment), 5000);
//                },
//                loadModels: function (resp) {
//                    var makeNames = [];
//                    var makeIDs = [];
//                    var data = $.parseJSON(resp.makes);
//
//
//                    $.each(data, function (key, value) {
//                        makeIDs.push(value.MakeID);
//                        makeNames.push(value.MakeName);
//                    });
//
//                    var interval = setInterval(function () {
//                        if (fitment.modelready) {
//
//                            if (makeIDs.length > 0) {
//                                fitment.modelready = false;
//                                var makeID = makeIDs[0];
//                                var makeName = makeNames[0];
//                                $.ajax({
//                                    method: 'POST',
//                                    url: "<?php /* @escapeNotVerified */ echo $block->getAjaxUrl(); ?>" + "?isAjax=true", // <-- you need to add 'isAjax=true' to your URL
//                                    data: {
//                                        request: 'models',
//                                        year: resp.year,
//                                        makeName: makeName,
//                                        makeID: makeID,
//                                        form_key: window.FORM_KEY  // <-- you need to add the current form key to your POST data
//                                    },
//                                    dataType: "json",
//                                    beforeSend: function () {
//                                        console.log('FORM_KEY: ' + window.FORM_KEY);
//                                        console.log('Year: ' + resp.year);
//                                        console.log('MakeID: ' + makeID);
//                                        console.log('MakeName: ' + makeName);
//                                        console.log('Loading Models...');
//
//                                        $("#fitment-ajax-output").append('<div>&nbsp;</div>');
//                                        $("#fitment-ajax-output").append('<div>Year: ' + resp.year + ' | Make: ' + makeName + '[' + makeID + ']' + ' </div>');
//                                        $("#fitment-ajax-output").append('<div>' + 'Loading MODELS from API...' + '</div>');
//                                    },
//                                    success: function (resp) {
//                                        makeIDs.splice(0, 1);
//                                        makeNames.splice(0, 1);
//
//                                        fitment.saveModels(resp);
//                                    },
//                                    error: function (jqXHR, textStatus, errorThrown) {
//                                        $("#fitment-ajax-output").append('<div style="color: #f00;">' + textStatus + '. ' + errorThrown + '</div>');
//                                        console.log(jqXHR);
//                                        console.log(textStatus);
//                                        console.log(errorThrown);
//                                    }
//                                });
//
//                            } else {
//                                clearInterval(interval);
//                                fitment.makeready = true;
//                            }
//                        }
//
//                    }, 5000);
//                },
//                loadSubModels: function (resp) {
//                    var modelIDs = [];
//                    var modelNames = [];
//                    var data = $.parseJSON(resp.models);
//
//                    $.each(data, function (key, value) {
//                        modelIDs.push(value.ModelID);
//                        modelNames.push(value.ModelName);
//                    });
//
//                    var interval = setInterval(function () {
//
//                        if (fitment.submodelready) {
//                            if (modelIDs.length > 0) {
//                                fitment.submodelready = false;
//                                var modelID = modelIDs[0];
//                                var modelName = modelNames[0];
//                                $.ajax({
//                                    method: 'POST',
//                                    url: "<?php /* @escapeNotVerified */ echo $block->getAjaxUrl(); ?>" + "?isAjax=true", // <-- you need to add 'isAjax=true' to your URL
//                                    data: {
//                                        request: 'submodels',
//                                        year: resp.year,
//                                        makeID: resp.makeID,
//                                        makeName: resp.makeName,
//                                        modelID: modelID,
//                                        modelName: modelName,
//                                        form_key: window.FORM_KEY  // <-- you need to add the current form key to your POST data
//                                    },
//                                    dataType: "json",
//                                    beforeSend: function () {
//                                        console.log('FORM_KEY: ' + window.FORM_KEY);
//                                        console.log('Year: ' + resp.year);
//                                        console.log('MakeID: ' + resp.makeID);
//                                        console.log('MakeName: ' + resp.makeName);
//                                        console.log('ModelID: ' + modelID);
//                                        console.log('ModelName: ' + modelName);
//
//                                        console.log('Loading SubModels...');
//
//                                        $("#fitment-ajax-output").append('<div>&nbsp;</div>');
//                                        $("#fitment-ajax-output").append('<div>Year: ' + resp.year + ' | Make: ' + resp.makeName + '[' + resp.makeID + ']' + ' | Model: ' + modelName + '[' + modelID + ']' + ' </div>');
//                                        $("#fitment-ajax-output").append('<div>' + 'Loading SubModels from API...' + '</div>');
//
//                                    },
//                                    success: function (resp) {
//                                        console.log(resp);
//                                        modelIDs.splice(0, 1);
//                                        modelNames.splice(0, 1);
//
//                                        fitment.saveSubModels(resp);
//                                    },
//                                    error: function (jqXHR, textStatus, errorThrown) {
//                                        console.log(textStatus);
//                                        console.log(errorThrown);
//                                    }
//                                });
//
//                            } else {
//                                clearInterval(interval);
//                                fitment.modelready = true;
//                            }
//                        }
//
//                    }, 10000);
//                },
//                saveMakes: function (resp) {
//                    $.ajax({
//                        method: 'POST',
//                        url: "<?php /* @escapeNotVerified */ echo $block->getAjaxUrl('savemakes'); ?>" + "?isAjax=true", // <-- you need to add 'isAjax=true' to your URL
//                        data: {
//                            makes: resp.response,
//                            year: resp.year,
//                            form_key: window.FORM_KEY  // <-- you need to add the current form key to your POST data
//                        },
//                        dataType: "json",
//                        beforeSend: function () {
//                            console.log('saving makes to database');
//                            $("#fitment-ajax-output").append('<span>' + 'Saving MAKES to database... ' + '</span>');
//                        },
//                        success: function (resp) {
//                            $("#fitment-ajax-output").append('<span>' + 'SUCCESSFUL' + '</span>');
//                            fitment.loadModels(resp);
//                        },
//                        error: function (jqXHR, textStatus, errorThrown) {
//                            console.log(textStatus);
//                            console.log(errorThrown);
//                        }
//                    });
//                },
//                saveModels: function (resp) {
//                    $.ajax({
//                        method: 'POST',
//                        url: "<?php /* @escapeNotVerified */ echo $block->getAjaxUrl('savemodels'); ?>" + "?isAjax=true", // <-- you need to add 'isAjax=true' to your URL
//                        data: {
//                            models: resp.response,
//                            makeID: resp.makeID,
//                            makeName: resp.makeName,
//                            year: resp.year,
//                            form_key: window.FORM_KEY  // <-- you need to add the current form key to your POST data
//                        },
//                        dataType: "json",
//                        beforeSend: function () {
//                            console.log('saving models to database');
//                            console.log('FORM_KEY: ' + window.FORM_KEY);
//                            console.log('Year: ' + resp.year);
//                            console.log('MakeID: ' + resp.makeID);
//                            console.log('MakeName: ' + resp.makeName);
//                            console.log('Loading Models...');
//
//                            $("#fitment-ajax-output").append('<span>Saving MODELS to database... </span>');
//                        },
//                        success: function (resp) {
//                            $("#fitment-ajax-output").append('<span>SUCCESSFUL</span>');
//
//                            fitment.loadSubModels(resp);
//                        },
//                        error: function (jqXHR, textStatus, errorThrown) {
//                            console.log(textStatus);
//                            console.log(errorThrown);
//                        }
//                    });
//                },
//                saveSubModels: function (resp) {
//                    $.ajax({
//                        method: 'POST',
//                        url: "<?php /* @escapeNotVerified */ echo $block->getAjaxUrl('savesubmodels'); ?>" + "?isAjax=true", // <-- you need to add 'isAjax=true' to your URL
//                        data: {
//                            submodels: resp.response,
//                            makeID: resp.makeID,
//                            makeName: resp.makeName,
//                            modelID: resp.modelID,
//                            modelName: resp.modelName,
//                            year: resp.year,
//                            form_key: window.FORM_KEY  // <-- you need to add the current form key to your POST data
//                        },
//                        dataType: "json",
//                        beforeSend: function () {
//                            console.log('FORM_KEY: ' + window.FORM_KEY);
//                            console.log('Year: ' + resp.year);
//
//                            console.log('MakeID: ' + resp.makeID);
//                            console.log('MakeName: ' + resp.makeName);
//
//                            console.log('ModelID: ' + resp.modelID);
//                            console.log('ModelName: ' + resp.modelName);
//
//                            $("#fitment-ajax-output").append('<span>Saving SUBMODELS to database... </span>');
//                        },
//                        success: function (resp) {
//                            $("#fitment-ajax-output").append('<span>SUCCESSFUL</span>');
//
////                                fitment.submodelready = true;
//                            fitment.saveSitemap(resp);
//                        },
//                        error: function (jqXHR, textStatus, errorThrown) {
//                            console.log(textStatus);
//                            console.log(errorThrown);
//                        }
//                    });
//                },
//                saveSitemap: function (resp) {
//                    $.ajax({
//                        method: 'POST',
//                        url: "<?php /* @escapeNotVerified */ echo $block->getAjaxUrl('savesitemap'); ?>" + "?isAjax=true", // <-- you need to add 'isAjax=true' to your URL
//                        data: {
//                            year: resp.year,
//                            makeName: resp.makeName,
//                            makeID: resp.makeID,
//                            modelName: resp.modelName,
//                            modelID: resp.modelID,
//                            submodels: resp.submodels,
//                            form_key: window.FORM_KEY  // <-- you need to add the current form key to your POST data
//                        },
//                        dataType: "json",
//                        beforeSend: function () {
//                            console.log('FORM_KEY: ' + window.FORM_KEY);
//                            console.log('Year: ' + resp.year);
//                            console.log('Make: ' + resp.makeID);
//                            console.log('Model: ' + resp.makeID);
//                            console.log('SubModel: ' + resp.submodels);
//
//                            $("#fitment-ajax-output").append('<div>&nbsp;</div>');
//                            $("#fitment-ajax-output").append('<div>&nbsp;</div>');
//                            $("#fitment-ajax-output").append('<span>Saving URL Rewrite & Sitemap to database... </span>');
//                        },
//                        success: function (resp) {
//                            $("#fitment-ajax-output").append('<span>SUCCESSFUL</span>');
//
//                            console.log(resp);
//
//                            fitment.submodelready = true;
//                        },
//                        error: function (jqXHR, textStatus, errorThrown) {
//                            console.log(textStatus);
//                            console.log(errorThrown);
//                        }
//                    });
//                }
//            }
//        })($);
    

});