/*
 * PrestaCart
 * @version  3.0
 *
 * @author Hashem Afkhami @ prestayar.com <hashem_afkhami@yahoo.com>
 * @copyright  2016 prestayar.com
 *
 */
$(document).ready( function () {
    $('#total_price_without_tax').bind("DOMSubtreeModified" ,function(){
        //location.reload();
        $("#total_shipping_text").show();
        $("#total_price_text").html('جمع کل');
    });

    if (!!$.prototype.fancybox)
        $("a.iframe").fancybox({
            'type': 'iframe',
            'width': 600,
            'height': 600
        });

    $(document).on('submit', 'form[name=formRegOrder]', function(){
        return acceptCGV();
    });

});

function backPSCartStep(){
    $('.dbs-step-done.prev a').click(function(){
        var step = $(this).data('step');
        $("#step1,#step2,#step3").hide();

        if( $(step).length ){
            $(step).show();
        }else return true;

        var urlPageBack = $(this).attr('href');
        window.history.replaceState("", "", urlPageBack );

        return false;
    });
}
function scrollToStep(stepID) {
    var offset = $(stepID).offset().top - 30;
    $('html,body').animate({ scrollTop : offset }, 'slow');
}

function acceptCGV()
{
    if (typeof msg_order_carrier != 'undefined' && $('#cgv').length && !$('input#cgv:checked').length)
    {
        if (!!$.prototype.fancybox)
            $.fancybox.open([
                    {
                        type: 'inline',
                        autoScale: true,
                        minHeight: 30,
                        content: '<p class="fancybox-error">' + msg_order_carrier + '</p>'
                    }],
                {
                    padding: 0
                });
        else
            alert(msg_order_carrier);
    }
    else
        return true;
    return false;
}

function PsyNumberToEnglish(value) {
    if (!value) {
        return;
    }
    var arabicNumbers = ["١", "٢", "٣", "٤", "٥", "٦", "٧", "٨", "٩", "٠"],
        persianNumbers = ["۱", "۲", "۳", "۴", "۵", "۶", "۷", "۸", "۹", "۰"];

    for (var i = 0, numbersLen = arabicNumbers.length; i < numbersLen; i++) {
        value = value.replace(new RegExp(arabicNumbers[i], "g"), persianNumbers[i]);
    }

    var persianNumbers = ["۱", "۲", "۳", "۴", "۵", "۶", "۷", "۸", "۹", "۰"],
        englishNumbers = ["1", "2", "3", "4", "5", "6", "7", "8", "9", "0"];

    for (var i = 0, numbersLen = persianNumbers.length; i < numbersLen; i++) {
        value = value.replace(new RegExp(persianNumbers[i], "g"), englishNumbers[i]);
    }

    return value;
}