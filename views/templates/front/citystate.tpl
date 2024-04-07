{*
* @Module Name: Presta Cart
* @Website: prestayar.com - prestashop cart
* @author Hashem Afkhami <hashem_afkhami@yahoo.com>
* @copyright  2013-2017 prestayar.com
*}
<!-- @file modules\psf_prestacart\views\templates\front\citystate -->
<form name="FormPSCartStep1" id="FormPSCartStep1" action="{$actionUrl}" method="post">
	{if !$typeCart}
		<style>
		#order_stepDBS .third{
			display:none;
		}
		#order_stepDBS li{
			width:33%;
		}
		#stepDBS_end em{ 
			display:none;
		}
		#stepDBS_end em.vir{
			display:inline;
		}
		@media (max-width: 598px) {
			#order_stepDBS li{
				width:100%;
			}	
		}
		</style>
	{elseif $typeCart == 3 }
		{$PSCA_ALERT_VIRTUAL}
	{else}
		<script type="text/javascript" src="{$jsCity}"></script>
		{$StatesOptions}
		<select id="id_city" class="id_city" name="PSCA_ID_CITY" style="display: none;" >
			<option value="0">{l s='لطفااستان خود را انتخاب کنید...' mod='psf_prestacart'}</option>
		</select>
		<input type="hidden" name="state" id="state" value="نامشخص" />
		<input type="hidden" name="city" id="city" value="نامشخص" />

    	{if $city && $state }
			<script type="text/javascript" >
				var city = "{$city}", state = "{$state}";
				$(document).ready(function() {
                    $("#id_state").val(state);

                    {if $panelCodeIndex }
						var select_box = document.getElementById("id_state");
						if (select_box.value != 0) cityList(select_box.selectedIndex);

					{else}
                    	cityList(state);
					{/if}

					$("#id_city").show().val(city);
				});
			</script>
		{/if}

    {/if}
	
	{if $typeCart != 3 }
		<input type="submit" class="button btn" name="Submit" id="getsend_price" value="{l s='ادامه خرید' mod='psf_prestacart'}" />
	{/if}
	
	<div class="loading" style="display:none">
		<br />{l s='درحال بارگذاری....' mod='psf_prestacart'}
	</div>
</form>

<script type="text/javascript">
	$("#id_state").change(function () {
        $('#id_city').show();
    });
	if( $("#id_state").val() != '0' )
        $('#id_city').show();

	$('#FormPSCartStep1').submit(function(){
		$('.msg').fadeOut('fast');
		{if !$typeCart}
			var data = {ldelim}'ajax':'1'{rdelim};
		{else}
			if($('#id_state').val()==0){
				$('.loading').fadeOut('fast');
				$('.msg').addClass('error').text("{l s='لطفا استان خود را انتخاب کنید' mod='psf_prestacart'}").fadeIn('fast');
				return false;
			}
			if($('#id_city').val()==0){
				$('.loading').fadeOut('fast');
				$('.msg').addClass('error').text("{l s='لطفا شهر خود را انتخاب کنید' mod='psf_prestacart'}").fadeIn('fast');
				return false;
			}
			var city = $('#id_city option:selected').html(),state = $('#id_state option:selected').html(),
				id_city = $('#id_city').val(),id_state = $('#id_state').val();
				
			$('#state').val(state);
			$('#city').val(city);
			
			var data = {ldelim}'ajax':'1','PSCA_ID_STATE':id_state,'PSCA_ID_CITY':id_city,'city':city,'state':state{rdelim};
		{/if}
		
		{if $ajaxPSCart == true }
			$(function () {
				$.ajax({
					url:"{$actionUrl}",
					type:"POST",
					dataType : "json",
					data:data,
					beforeSend: function(){
						$('#ajax_black').show();
						$('.msg').html('').hide();
					},		
					success:function(jsonData){
						$('#ajax_black').hide();
						if( jsonData.hasError ){
							var errors = '';
							for(error in jsonData.errors)
								//IE6 bug fix
								if(error != 'indexOf')
									errors += '<li>'+jsonData.errors[error]+'</li>';
							$('.msg').addClass('error').html('<ol>'+errors+'</ol>').show();
						}else{
						    if (jsonData.linkRedirect) {
                                setTimeout(function() {
                                    window.location.href = jsonData.linkRedirect;
                                }, 500);
							} else {
                                $('#step1').hide();
                                $('#step2').html(jsonData.page).show();
                                $('#messageError').hide();
                                if(jsonData.totalprice) $('#total_price').html(jsonData.totalprice);
							}

						}		
					},
					error: function (xhr, ajaxOptions, thrownError) {
                        $('#ajax_black').hide();
                        alert("{l s='خطا رخ داده است ، دوباره امتحان کنید...' mod='psf_prestacart'}");
                    }
				})
			});
			return false;
		{else}
        	return true;
		{/if}
	});
</script>