require([
    "jquery",
    "mage/mage"
], function ($) {
    'use strict';

//    function vinYearCheck() {
//        $('#vehicleYear-msg').text('');
//        var year = parseInt($("#vehicleYear").val());
//        var vin = $("#TPMS_VIN").val();
//        var ten = vin.charAt(9).toUpperCase();
//        var map = {
//            "A": [1980, 2010],
//            "B": [1981, 2011],
//            "C": [1982, 2012],
//            "D": [1983, 2013],
//            "E": [1984, 2014],
//            "F": [1985, 2015],
//            "G": [1986, 2016],
//            "H": [1987, 2017],
//            "J": [1988, 2018],
//            "K": [1989, 2019],
//            "L": [1990],
//            "M": [1991],
//            "N": [1992],
//            "P": [1993],
//            "R": [1994],
//            "S": [1995],
//            "T": [1996],
//            "V": [1997],
//            "W": [1998],
//            "X": [1999],
//            "Y": [2000],
//            "1": [2001],
//            "2": [2002],
//            "3": [2003],
//            "4": [2004],
//            "5": [2005],
//            "6": [2006],
//            "7": [2007],
//            "8": [2008],
//            "9": [2009]
//        };
//
//        if ($.inArray(year, map[ten]) !== -1) {
//            $('#vehicleYear-msg').text('');
//        } else {
//            $('#vehicleYear-msg').text('WARNING! - Selected year does not match VIN');
//        }
//    }
//    function objectifyForm(formArray) {//serialize data function
//        var res = {};
//        for (var i = 0; i < formArray.length; i++) {
//            res[formArray[i]['name']] = formArray[i]['value'];
//        }
//        return res;
//    }
//
//    function submitTPMSForm() {
//        var form = $('#myform2');
//        if (!form.valid())
//            return;
//        var ob = objectifyForm(form.serializeArray());
//        var data = {
//            form_key: ob.form_key,
//            customerName: ob.TPMSCustName,
//            customerPhone: ob.TPMSPhone,
//            customerEmail: ob.TPMSEmail,
//            distributorName: $('#distributorName option:selected').text(),
//            distributorId: $('#distributorName').val(),
//            installerShop: ob.InstallerShop,
//            installerName: ob.InstallerName,
//            installerPhone: ob.InstallerPhone,
//            installerFax: ob.InstallerFax,
//            installerEmail: ob.InstallerEmail,
//            tireSystem: $("input[name='tires']:checked").val(),
//            placardFront: ob.PlacardFRONT,
//            placardRear: ob.PlacardREAR,
//            placardSpare: ob.PlacardSPARE,
//            tireLF: ob.TireLF,
//            tireRF: ob.TireRF,
//            tireLR: ob.TireLR,
//            tireRR: ob.TireRR,
//            tireSpare: ob.TireSpare,
//            vin: ob.TPMS_VIN,
//            vehicleYear: $('#vehicleYear option:selected').text(),
//            vehicleMake: $('#vehicleMake option:selected').text(),
//            vehicleModel: $('#vehicleModel option:selected').text(),
//            vehicleSubmodel: $('#vehicleSubmodel option:selected').text(),
//            relearnProc: $('#TPMSRelearnProc option:selected').text(),
//            relearnProcOther: ob.RelearnOther,
//            sensorPartNo: ob.TPMSPartNo,
//            resetToolUsed: ob.TPMSResetToolUsed,
//            relearnToolUsed: ob.relearnToolUsed,
//            softwareVersion: ob.softwareVersion,
//            wheelType: $("input[name='wheeltype']:checked").attr('title'),
//            sensorProximity: $('#TPMSProximity option:selected').val(),
//            tpmsNotes: $('#tpmsNotes').val(),
//            vehicleYearId: $('#vehicleYear option:selected').val(),
//            vehicleMakeId: $('#vehicleMake option:selected').val(),
//            vehicleModelId: $('#vehicleModel option:selected').val(),
//            vehicleSubmodelId: $('#vehicleSubmodel option:selected').val()
//        };
//
//        $.ajax({
//            url: TPMS_SETTINGS.submitURL,
//            type: "POST",
//            data: data,
//            success: function (response) {
////            $('#myform2').html(response);
//                $('#myform2').hide();
//                $('#tpms-result').html(response);
//            }
//        });
//    }
//
//    function resetDropdown(type) {
//        switch (type) {
//            case 'make':
//                $('#vehicleMake').html('<option value="">Make</option>');
//
//            case 'model':
//                $('#vehicleModel').html('<option value="">Model</option>');
//
//            case 'submodel':
//                $('#vehicleSubmodel').html('<option value="">Sub Model</option>');
//
//            default:
//                $('#recommendedSensorsResult').html('');
//        }
//    }
    
//    $("#tpms-help-form").mage("validation", {});


//    $(document).ready(function () {

//        $("#btnSend").on("click", function () {            
//            submitTPMSForm();
//        });

//        $('.tpms-tireSwitch input').on('change', function (e) {
//            if (e.target.value == 4) {
//                $(e.target).closest('.tpms-step').addClass('tpms-wheels-4');
//            } else {
//                $(e.target).closest('.tpms-step').removeClass('tpms-wheels-4');
//            }
//        });
//
//        $('#tpms_tires_4').click();
//
//        $('#vehicleYear').change(function () {
//            resetDropdown('make');
//            var year = $(this).val();
//            if (year) {
//                $.ajax({
//                    url: TPMS_SETTINGS.vehicleURLs.make,
//                    data: {year: year},
//                    success: function (data) {
//                        $('#vehicleMake').html(data);
//                    }
//                });
//            }
//        });
//
//        $('#vehicleMake').change(function () {
//            resetDropdown('model');
//            var year = $('#vehicleYear').val();
//            var make = $('#vehicleMake').val();
//            var img = $("#vehicleMake option:selected").text();
//            if (year && make) {
//                $.ajax({
//                    url: TPMS_SETTINGS.vehicleURLs.model,
//                    data: {year: year, make: make},
//                    success: function (data) {
//                        $('#vehicleModel').html(data);
//                    }
//                });
//            }
//        });
//
//        $('#vehicleModel').change(function () {
//            resetDropdown('submodel');
//            var year = $('#vehicleYear').val();
//            var make = $('#vehicleMake').val();
//            var model = $('#vehicleModel').val();
//            if (year && make && model) {
//                $.ajax({
//                    url: TPMS_SETTINGS.vehicleURLs.submodel,
//                    data: {year: year, make: make, model: model},
//                    success: function (data) {
//                        $('#vehicleSubmodel').html(data);
//                    }
//                });
//            }
//        });
//
//        $('#vehicleSubmodel').change(function () {
//            resetDropdown();
//            var year = $('#vehicleYear').val();
//            var make = $('#vehicleMake').val();
//            var model = $('#vehicleModel').val();
//            var submodel = $('#vehicleSubmodel').val();
//            if (year && make && model && submodel) {
//                $.ajax({
//                    url: TPMS_SETTINGS.vehicleURLs.search,
//                    data: {year: year, make: make, model: model, submodel: submodel},
//                    success: function (response) {
//                        $('#recommendedSensorsResult').html(response);
//                    }
//                });
//            }
//        });
//
//        $("#TPMSProximity").change(function (e) {
//            var prox = document.getElementsByName("TPMSProximity")[0].value;
//            if (prox !== 'More than 100 ft from vehicle') {
//                $('#TPMSProximity-msg').text('Original sensors must be more than 100 feet from vehicle during relearn procedure');
//            } else {
//                $('#TPMSProximity-msg').text('');
//            }
//        });
//
//        $("#vehicleYear").change(function (e) {
//            if (parseInt($('#vehicleYear option:selected').val()) >= 1980) {
//                vinYearCheck();
//            } else {
//                $('#vehicleYear-msg').text('');
//            }
//        });
//
//        $("#TPMS_VIN").on('input', function () { // 1FMHK7F89CGA19141
//            var v = parseInt($('#vehicleYear option:selected').val());
//            if (v && v >= 1980) {
//                vinYearCheck();
//            }
//        });
//
//        $("#TPMSPhone, #InstallerPhone, #InstallerFax").on('change blur keypress', function () {
//            var number = $(this).val().replace(/[^\d]/g, '');
//            if (number.length) {
//                number = '(' + number.substr(0, 3) + ') ' + number.substr(3, 3) + '-' + number.substr(6);
//            }
//            $(this).val(number);
//        });

        //vehicle make
        $.validator.addMethod("notEqual", function (value, element, param) {
            return this.optional(element) || value !== param;
        }, "Select the make of vehicle");

        //Sensor proximity 100ft
        $.validator.addMethod("isEqual", function (value, element, param) {
            return this.optional(element) || value == param;
        }, "Original sensors must be more than 100 feet from vehicle during relearn procedure");

        //Front Tire Pressure 25%
        $.validator.addMethod("isFrontPressure", function (value, element, param) {
            var p = parseInt($('#PlacardFRONT').val());
            return (value >= 0.75 * p && value <= 1.25 * p);
        }, "Front tire pressure must be within 25% +/- of front placard pressure");

        //Rear Tire Pressure 25%
        $.validator.addMethod("isRearPressure", function (value, element, param) {
            var p = parseInt($('#PlacardREAR').val());
            return (value >= 0.75 * p && value <= 1.25 * p);
        }, "Rear tire pressure must be within 25% +/- of rear placard pressure");

        //Spare Tire Pressure 25%
        $.validator.addMethod("isSparePressure", function (value, element, param) {
            var p = parseInt($('#PlacardSPARE').val());
            return (value >= 0.75 * p && value <= 1.25 * p);
        }, "Spare tire pressure must be within 25% +/- of spare placard pressure");

        //VIN check
        $.validator.addMethod('vinNum', function (value) {
            if (value.length != 17)
                return false;
            return /[0-9A-Z]{17}/g.test(value.toUpperCase());
        }, 'The VIN number is not correct');

        // Purchased part is one of the recommended
        $.validator.addMethod('isRecommendedSensor', function (value, element, param) {
            var v = value.toUpperCase(); // $('#TPMSPartNo').val()
            var sensors = $('#recommendedSensorsResult .tpms-sensor');
            if (!sensors.length) {
                return true;
            }
            for (var i = 0; i < sensors.length; i++) {
                if ($.trim(sensors[i].innerHTML).toUpperCase() == v) {
                    return true;
                }
            }
            return false;
        }, 'You should have used one of the recommended sensors');

        // If other is picked from "What relearn procedure is was used" Make Other Required
        $.validator.addMethod('relearnOtherProc', function (value, element, param) {
            return ('Other' != $('#TPMSRelearnProc option:selected').val())
                    || $.trim(value);
        }, 'Describe the relearn procedure you used');


//        $('#myform21').mage('validation', {
//            rules: {
//                softwareVersion: {required: true},
//                tires: {required: true},
//                PlacardFRONT: {required: true},
//                PlacardREAR: {required: true},
//                PlacardSPARE: {required: '#tpms_tires_5:checked'},
//                TireLF: {required: true, isFrontPressure: true},
//                TireRF: {required: true, isFrontPressure: true},
//                TireLR: {required: true, isRearPressure: true},
//                TireRR: {required: true, isRearPressure: true},
//                TireSpare: {required: '#tpms_tires_5:checked', isSparePressure: true},
//                TPMS_VIN: {required: true, vinNum: true},
//                vehicleYear: {required: true},
//                vehicleMake: {required: true},
//                vehicleModel: {required: true},
//                vehicleSubmodel: {required: true},
//                TPMSRelearnProc: {required: true},
//                RelearnOther: {relearnOtherProc: true},
//                TPMSPartNo: {isRecommendedSensor: true},
//                TPMSReset: {required: true, minlength: 1},
//                TPMSResetToolUsed: {required: true},
//                wheeltype: {required: true},
//                TPMSProximity: {required: true, isEqual: "More than 100 ft from vehicle"},
//                TPMSCustName: {required: true, minlength: 1},
//                TPMSPhone: {required: true, phoneUS: true},
//                TPMSEmail: {required: true, email: true},
//                distributorName: {required: true},
//                InstallerShop: {required: true, minlength: 1},
//                InstallerName: {required: true, minlength: 1},
//                InstallerPhone: {required: true, phoneUS: true},
//                InstallerFax: {},
//                InstallerEmail: {required: true, email: true}
//            },
//            messages: {
//                distributorName: {required: "Distributor name required"},
//                InstallerName: {required: "Name of the person who installed"},
//                TPMS_tires: {required: "Select either 4 tire or 5 tire system"},
//                TPMSReset: {required: "Reset tool, use NA if not applicable"},
//                wheeltype: {required: "Select a wheel type"},
//                tires: {required: "Select either 4 tire or 5 tire system"},
//                make: {required: "Select make of vehicle"}
//            },
//            errorPlacement: function (error, element) {
//                if (element.is(":radio") && element.attr('name') === 'wheeltype') {
//                    error.appendTo(element.parents('.oe-after'));
//                } else if (element.is(":radio") && element.attr('name') === 'tires') {
//                    error.appendTo(element.parents('.tire-list'));
//                } else { // This is the default behavior 
//                    error.insertAfter(element);
//                }
//            }
//        });

//    });
});


