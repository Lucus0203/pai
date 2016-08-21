$(document).ready(function(){
    $('.listBox').delegate('.listCont','hover',function(){
        $(this).toggleClass('hover');
    });
    $('.delBtn').click(function(){
        return confirm('确定删除吗?');
    });
    $('.loginT').hover(function(){
        $(this).find('.logoList').show();
    },function(){
        $(this).find('.logoList').hide();
    });
    $('.DTdate').appendDtpicker({"locale": "cn","calendarMouseScroll": false,"autodateOnStart": false,"closeOnSelected": true});
    jQuery.validator.methods.compareDate = function(value, element, param) {
        var startDate = jQuery(param).val() + ":00";
        value = value + ":00";
        var startDate = jQuery(param).val();
        var date1 = new Date(Date.parse(startDate.replace("-", "/")));
        var date2 = new Date(Date.parse(value.replace("-", "/")));
        return date1 < date2;
    };
    $('.alert-remove').click(function(){$('.alertBox').hide()});
});