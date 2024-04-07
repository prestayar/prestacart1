$(document).ready( function () {
    boxHelp = {
        init: function (elementID, classEditor) {
            $('#'+ elementID +'_on').click(function() {
                $('.'+ classEditor).removeClass('hide');
            });
            $('#'+ elementID +'_off').click(function() {
                $('.'+ classEditor).addClass('hide');
            });
        }
    };
	$('#PSCA_STATUS_COD_on').click(function() {
		$('.type-cod').removeClass('hide');
	});	
	$('#PSCA_STATUS_COD_off').click(function() {
		$('.type-cod').addClass('hide');
	});
    boxHelp.init('PSCA_ALERT_FLAG_TOP','alertFT');
    boxHelp.init('PSCA_ALERT_CART_FLAG','alertFB');
    boxHelp.init('PSCA_ALERT_LOGIN_FLAG','alertFLO');
    boxHelp.init('PSCA_ALERT_GUEST_FLAG','alertFGU');
    boxHelp.init('PSCA_ALERT_CART_STEP3_FLAG','alertFS3');
	
	module_payment = {
		init: function(){
			module_payment.banks($('#PSCA_MODULE_PAYMENT'));
			$('#PSCA_MODULE_PAYMENT').change(function(e) {
				module_payment.banks($(this));
			});			
			module_payment.modules($('#PSCA_TYPE_PAYMENT'));
			$('#PSCA_TYPE_PAYMENT').change(function(e) {
				module_payment.modules($(this));
			});
		},		
		
		modules: function( selectID ){
			if ( selectID.val() == 'Merger')
			{
				$('.online-carrier').removeClass('hide');
				$('#form_carrier').addClass('hide');
				module_payment.banks($('#PSCA_MODULE_PAYMENT'));
			}
			else{
				$('.online-carrier').addClass('hide');
                $('#form_carrier').removeClass('hide');
			} 
		},		
		
		banks: function( selectID ){
			if ( selectID.val() == 'dmtbanks')
			{
				$('.module-payment-bank').removeClass('hide');
			}else{
				$('.module-payment-bank').addClass('hide');
			}

            if ( selectID.val() == 'paymentLink')
            {
                $('.payment-link').removeClass('hide');
            }else{
                $('.payment-link').addClass('hide');
            }

            if ( selectID.val() == 'psf_prestapay')
            {
                $('.module-payment-gate').removeClass('hide');
            }else{
                $('.module-payment-gate').addClass('hide');
            }

		}
	};
	module_payment.init();
});