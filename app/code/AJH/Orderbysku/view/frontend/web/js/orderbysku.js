define([
    "jquery"
], function ($) {
    'use strict';    
    
    var orderbysku = function (config, node) {    
        return {
            numberic: 'test'
        };
    };
    
//    var orderbysku = (function(config, node){
//        console.log(config);
//        return {
//            isNumberic: function (value) {
//                if ($.isNumeric(value)) {
//                    return true;
//                }
//                
//                return false;
//            }
//        };
//    })();

    //
///************************** Define Global Variable *****************************/
//var currentid;
///************************** Define Global Variable *****************************/
//
///***************** Add only numeric in the qty field ************************/
//function isNumberKey(evt)
//{
//    var charCode = (evt.which) ? evt.which : event.keyCode
//    if (charCode > 31 && (charCode < 48 || charCode > 57))
//        return false;
//    return true;
//}
///***************** Complete only numeric in the qty field *********************/
//
///***************** Fetch Dropdown value in textbox *********************/
////jQuery('.input').focusout(function () {
//// jQuery(document).on('focusout', '.ordersku', function (event) {
////    var currentid = jQuery(this).attr('id');   
////    alert(currentid);
//// });
//
//jQuery(function () {
//    //jQuery(document).find(".resultdata").css('display','none');
//    jQuery(document).on('focusout', '.ordersku', function (event) {
//        currentid = jQuery(this).attr('id');
//    });
//    jQuery("#muliSelect6").change(function () {
//        var selectedText = jQuery(this).find("option:selected").text();
//        //alert(selectedText);
//        var selectedValue = jQuery(this).val();
//        // var textboxcount = jQuery('.ordersku').length;
//        // var id = "#TextBox";
//        // var finalid = id.concat(textboxcount);       	
//        var id = "#";
//        var finalid = id.concat(currentid);
//        jQuery(finalid).val(selectedValue);
//        var skuData = selectedValue;
//        var divId = jQuery(finalid).closest("div").prop("id");
//        var divId = id.concat(divId);
//        //alert(divId);
//        jQuery("#muliSelect6").css("display", "none");
//        var baseUrl = "<?php echo $block->getUrl('', array('_secure' => true)); ?>";
//        jQuery.ajax({
//            type: "POST",
//            url: baseUrl + "orderbysku/index/fetchattr",
//            data: {sku: skuData},
//            // dataType: dataType,
//            success: function (response) {
//                jQuery(divId).find(".resultdata").css('display', 'block');
//                jQuery(divId).find(".resultdata").css('display', 'inline-block');
//                jQuery(divId).find(".resultdata").html(response);
//            },
//            error: function (response) {
//                //alert(response);
//            }
//        });
//    });
//});
//
///***************** Complete Fetch Dropdown value in textbox ********************/
//
///***************** Function for the ajax call and response *********************/
//
//jQuery(document).ready(function () {
//    jQuery(".resultdata").css('display', 'none');
//    jQuery("#muliSelect6").css("display", "none");
//    jQuery("#ajax_loader").css("display", "none");
//    //jQuery(".error").css("display","block");
//    // Ajax Call for fing product data based on sku
//
//    jQuery(document).on('keyup', '.ordersku', function (event) {
//        jQuery("#ajax_loader").css("display", "block");
//        var data = jQuery(this).closest('div').find(".ordersku").val();
//        var baseUrl = "<?php echo $block->getUrl('', array('_secure' => true)); ?>";
//        jQuery.ajax({
//            type: "POST",
//            url: baseUrl + "orderbysku/index/orderdata",
//            data: {ordersku: data},
//            // dataType: dataType,
//            success: function (response) {
//                console.log(response);
//
//                jQuery("#muliSelect6").css("display", "block");
//                jQuery("#muliSelect6").html('');
//                jQuery("#muliSelect6").append('<option value="">Please Select Product</option>');
//                jQuery("#muliSelect6").append(response);
//                jQuery("#ajax_loader").css("display", "none");
//                jQuery(".error").css("display", "none");
//                var length = jQuery('#muliSelect6 > option').length;
//                if (length > 1) {
//
//                } else {
//                    jQuery(".error").css("display", "block");
//                    jQuery("#muliSelect6").css("display", "none");
//                    jQuery(".error").html('');
//                    jQuery(".error").append('There is no product found');
//                }
//
//
//            },
//            error: function (response) {
//                console.log(response);
//            }
//        });
//    });
//
//    /************** Complete Function for the ajax call and response ****************/
//
//    /*********************** Add new textbox dynamically ****************************/
//
//    var counter = 6;
//    jQuery("#addButton").click(function () {
//        //jQuery("#orderbyskuform .resultdata").css('display','none');
//        jQuery("#muliSelect6").css("display", "none");
//        // if(counter>10){
//        //            alert("Only 10 textboxes allow");
//        //            return false;
//        // }		
//        var newTextBoxDiv = jQuery(document.createElement('div')).attr("id", 'TextBoxDiv' + counter);
//        newTextBoxDiv.html('<label>Sku #' + counter + ' : </label>' +
//                '<input autocomplete="off" type="text" id="TextBox' + counter + '" class="ordersku" required="true" placeholder="Enter Sku" name="sku[]' +
//                '" id="textbox' + counter + '" >' + '<label>Qty #' + counter + ' : </label>' +
//                '<input type="text" required="true" placeholder="Enter Qty" onkeypress="return isNumberKey(event)" name="qty[]' +
//                '" id="qty' + counter + '"  >' +
//                '<a href="#" class="removeid" id="' + counter + '"><i class="fa fa-trash"></i></a>' +
//                '<div class ="resultdata" style="display:none"></div>');
//
//
//        newTextBoxDiv.appendTo("#TextBoxesGroup");
//        counter++;
//    });
//    /********************* Complete new textbox dynamically *************************/
//
//    /************************** Remove sku text box *********************************/
//    jQuery(document).on('click', '.removeid', function (event) {
//        var currentremoveid = jQuery(this).attr('id');
//        //alert(currentremoveid);
//        jQuery("#TextBoxDiv" + currentremoveid).remove();
//    });
//    jQuery("#removeButton").click(function () {
//        jQuery("#muliSelect6").val('');
//        if (counter == 1) {
//            alert("No more textbox to remove");
//            return false;
//        }
//        counter--;
//        jQuery("#TextBoxDiv" + counter).remove();
//    });
//
//    /*********************** Complete Remove sku text box *************************/
//
//    //    jQuery("#getButtonValue").click(function () {
//    // 	var msg = '';
//    // 	for(i=1; i<counter; i++){
//    //    	  msg += "\n Textbox #" + i + " : " + jQuery('#textbox' + i).val();
//    // 	}
//    // });
//});

    return orderbysku;

});
