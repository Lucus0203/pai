var alertBoxTimeSet=0;
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
    jQuery.validator.addMethod("compareDate", function(value, element,param) {
        var startDate = jQuery(param).val() + ":00";
        value = value + ":00";
        var date1 = new Date(Date.parse(startDate.replace(/\-/g, "/")));
        var date2 = new Date(Date.parse(value.replace(/\-/g, "/")));
        return date1 < date2;
    }, "结束时间不能小于开始时间");
    $('input, textarea').placeholder();
    $('.alert-remove').live('click',function(){$('.alertBox').hide();return false;});
    alertBoxTimeSet=setTimeout(function(){$('.alertBox').fadeOut(500);},2000);

    $('a.back-to-top').click(function() {
        $('html, body').animate({
            scrollTop: 0
        }, 300);
        return false;
    });
});

$(window).scroll(function() {
    if ( $(window).scrollTop() > 300 ) {
        $('a.back-to-top').fadeIn('slow');
    } else {
        $('a.back-to-top').fadeOut('slow');
    }
});