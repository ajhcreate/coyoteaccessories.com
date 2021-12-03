define(['jquery', 'criteriajs'], function ($) {
    var criteria = (function ($) {
        return {
            evaluate: function (element, resp) {
                var _criteria = $("#partMasterIds").val();
                var criteria_q = $("#addl-criteria-question").val();                
                
                if (resp === 'no') {
                    _criteria = "no";
                }

                $("#addl-criteria").val(_criteria);
                $("#addl-criteria-q").val(criteria_q);

                $("button.criteria-option").removeClass("option-selected");
                $(element).addClass("option-selected");
            }
        };
    })($);

    return function (config, element) {
        $(element).on("click", function () {
            criteria.evaluate(element, $(element).data("value"));

        });
//        console.log(config.option);
//        console.log($(element).data("value"));
    };
});