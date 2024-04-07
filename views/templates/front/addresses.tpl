{*
* @Module Name: Presta Cart
* @Website: prestayar.com - prestashop cart
* @author Hashem Afkhami <hashem_afkhami@yahoo.com>
* @copyright  2013-2017 prestayar.com
*}
<!-- @file modules\psf_prestacart\views\templates\front\addresses -->
{if !$ajax} <section class="PSCart"><div id="step2"> {/if}
{if $PSCA_CSS_CUSTOMIZE != ''} <style>{$PSCA_CSS_CUSTOMIZE}</style>	 {/if}
		<div id="step21">
			<h6 class="big-title dbs-steps">
				<div class="dbs-step-done prev">
					<a href="{$urlPageBack}" data-step="#step1" ><i class="icon-right-open"></i><span>{l s='خلاصه سبد خرید'}</span></a>
				</div>
				<div class="dbs-step-title"><span>{l s='مشخصات' mod='psf_prestacart'}</span></div>
				<div class="dbs-step-todo next">
					{if isset($typeCart) and !$typeCart}
					<span>{l s='تایید سفارش'}</span> 
					{else}
					<span>{l s='روش ارسال و پرداخت'}</span> 
					{/if}
					<i class="icon-left-open"></i>
				</div>
			</h6>		
			
			<div id="contentBlock">
			<form method="post" id="Form_Step3" action="{$actionUrl}" {if $ajaxPSCart} class="ajaxPSCart" {/if}>
				<fieldset>
					<div class="clearfix">	
						{if $addresses|@count > 1}
							<div class="address_delivery select">
								<label for="id_address_delivery">
								{if !$typeCart}
									{l s='لطفا مشخصات مورد نظر خود را انتخاب کنید : ' mod='psf_prestacart'}
								{else}
									{l s='لطفا آدرس مورد نظر خود را انتخاب کنید : ' mod='psf_prestacart'}
								{/if}
								</label>
								<select name="id_address_delivery" id="id_address_delivery" class="address_select1" onchange="updateAddressesDisplay();">
								
								{foreach from=$addresses key=k item=address}
									<option value="{$address.id_address|intval}" 
										{if isset($id_address) and $id_address == $address.id_address}
										selected="selected"
										{/if}
									>{$address.alias|escape:'htmlall':'UTF-8'}</option>
								{/foreach}
								</select>
							</div>
						{else}
							{foreach from=$addresses name=bar item=address}
							<input type="hidden" name="id_address_delivery" id="id_address_delivery" value="{$address.id_address}" />
							{/foreach}
						{/if}
						<div class="addresses clearfix" style="border:none;">
							{foreach from=$addresses name=bar item=address}	
								{if isset($id_address) and $id_address ==$address.id_address} 
								<div id="address{$address.id_address}" class="addressBlock" style="display:block" >
								{elseif !isset($id_address) and $smarty.foreach.bar.first} 
								<div id="address{$address.id_address}" class="addressBlock" style="display:block" >
								{else} 
								<div id="address{$address.id_address}" class="addressBlock" style="display:none" >
								{/if}
									<ul class="address">	
										<li class="address_title">{$address.alias|escape:'htmlall':'UTF-8'}</li>
										
										{foreach $PSCA_FIELDS_ADDRESS as $key => $field}
											{if !$typeCart}
												{if $field.view == 'address' && $field.data.enable_virtual == '1' }
													{if $key == 'lastname'}
														<li class="address_name">
															{if $address.firstname != '-' }{$address.firstname} {/if}{$address.lastname}
														</li>
                                                    {elseif $key == 'firstname'}
													{elseif $key == 'name_merged'}
														<li class="address_name">{$address.firstname} {$address.lastname}</li>
													{else}
														<li>{$field.title}: {$address.$key}</li>
													{/if}
												{/if}											
											{else}
												{if $field.view == 'address' && $field.data.enable == '1' }
                                                    {if $key == 'lastname'}
														<li class="address_name">
                                                            {if $address.firstname != '-' }{$address.firstname} {/if}{$address.lastname}
														</li>
                                                    {elseif $key == 'firstname'}
                                                    {elseif $key == 'name_merged'}
														<li class="address_name">{$address.firstname} {$address.lastname}</li>
													{elseif $key == 'address1'}
														<li class="address_address1">{$field.title}: {$address.address1}{$address.address2}</li>
													{else}
														<li>{$field.title}: {$address.$key}</li>
													{/if}
												{/if}
											{/if}
										{/foreach}	
									</ul>
									<a href="{$actionUrlProcess}&id_address={$address.id_address}" class="btn editAddress{if $ajaxPSCart} ajaxPSCart{/if}" style="margin:15px 0;" >
									{if !$typeCart}
										{l s='ویرایش مشخصات' mod='psf_prestacart'}
									{else}
										{l s='ویرایش آدرس' mod='psf_prestacart'}
									{/if}									
									</a>
								</div>
							{/foreach}
						</div>
						<div class="submit">
							<a href="{$actionUrlProcess}" class="btn{if $ajaxPSCart} ajaxPSCart{/if}" style="margin:15px 0;" id="addAddress" >
									{if !$typeCart}
										{l s='افزودن مشخصات جدید' mod='psf_prestacart'}
									{else}
										{l s='افزودن آدرس جدید' mod='psf_prestacart'}
									{/if}								
							</a>
						</div>	
						<input type="hidden" value="{$typeCart}" name="typeCart" id="typeCart" />
						{if !$typeCart}	
							<label for="message" style="margin-top: 1.5em;">{l s='پیغام خریدار :' mod='psf_prestacart'}</label>
							<div class="textarea">
								 <textarea  id="message" name="message" rows="2" cols="5"></textarea>
							</div>	
							<div class="submit">
								<input type="submit" id="pay" name="pay" class="btn" value="{l s='خرید خود را ثبت کنید' mod='psf_prestacart'}" />
							</div>
							
						{else}	
							<div class="submit">
								<input type="submit" name="Step3" class="btn" value="{l s='مرحله بعد' mod='psf_prestacart'}" />
							</div>
						{/if}
                        <div class="msg error" id="create_account_error" style="display: none"></div>
					</div>
				</fieldset>
			</form>
			</div>
		</div>
		<div id="step22">
		</div>
{if !$typeCart}
<form action="" method="post" id="payment_form" class="hidden">
    <input type="hidden" name="orderId" value="" />
</form>
{/if}
<script type="text/javascript">
	$(document).ready(function(){
        scrollToStep('#step2');
		$('#addAddress').click(function(){
			if ($(this).hasClass('ajaxPSCart'))
			{
                var urlAddress = $(this).attr('href');
				submitAddressGet(urlAddress);
				return false;
			}
		});	
		$('.editAddress').click(function(){
			if ($(this).hasClass('ajaxPSCart'))
			{
				var urlEditAddress = $(this).attr('href');
				submitAddressGet(urlEditAddress);
				return false;
			}
		});
		$('#Form_Step3').submit(function(event) {
			if ($(this).hasClass('ajaxPSCart'))
			{
                event.preventDefault();
                var data = $(this).serialize();
                submitStep3(data);
                return false;
			}
		});
        {if $ajaxPSCart}
			window.history.replaceState("", "", '{$urlPage}' );
        	{if $ajax}backPSCartStep();{/if}
        {/if}
	});

	function updateAddressesDisplay(){
		$(".addressBlock").hide('fast');
		var addressID = '#address'+ $("#id_address_delivery").val();
		$(addressID).show('fast');
	}

	function submitAddressGet(urlAddress){
		//send the ajax request to the server
		$.ajax({
			type: 'GET',
			url: urlAddress,
			dataType : "json",
			beforeSend: function(){
				$('#ajax_black_addresses').show();
				$('#create_account_error').html('').hide();
			},				
			success: function(jsonData){
				$('#ajax_black_addresses').hide();
				if(jsonData.hasError){
					var errors = '';
					for(error in jsonData.errors)
						//IE6 bug fix
						if(error != 'indexOf')errors += '<li>'+jsonData.errors[error]+'</li>';
					$('#create_account_error').html('<ol>'+errors+'</ol>').show();
				}else{
					$('#step21').hide();
					$('#step22').html(jsonData.page);
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown){
				$('#ajax_black_addresses').hide();
				alert("{l s='خطا رخ داده است ، دوباره امتحان کنید...' mod='psf_prestacart'}");
			}
		});
	}

    function submitStep3(data){
        $('#create_account_error').html('').hide();
        //send the ajax request to the server
        $.ajax({
            type: 'POST',
            url:"{$actionUrl}",
            dataType : "json",
            data: data,
            beforeSend: function(){
                $('#ajax_black_addresses').show();
                $('#create_account_error').html('').hide();
            },
            success: function(jsonData){
                $('#ajax_black_addresses').hide();
                if(jsonData.hasError){
                    var errors = '';
                    for(error in jsonData.errors)
                        //IE6 bug fix
                        if(error != 'indexOf')
                            errors += '<li>'+jsonData.errors[error]+'</li>';
                    $('#create_account_error').html('<ol>'+errors+'</ol>').show();
                }
                else{
                    if( jsonData.urlPayment )
                        $('#payment_form').attr('action',jsonData.urlPayment).submit();
                    else{
                        $('#step2').hide();
                        $('#step3').html(jsonData.page).show();
                    }
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown){
                $('#ajax_black_addresses').hide();
                alert("{l s='خطا رخ داده است ، دوباره امتحان کنید...' mod='psf_prestacart'}");
            }
        });
    }


</script>
	<div id="ajax_black_addresses" class="ajax_black">   
		<span style="color:#fff">{l s='در حال ارسال اطلاعات ....' mod='psf_prestacart'}</span>
	</div>

{if !$ajax}
	</div>
	<div id="step3"></div>
    {if $PSCA_ALERT_CART_FLAG == 1 }
		<div class="alert alert_cart_text"  style="background-color: {$PSCA_ALERT_COLOR_BOX};border-color:{$PSCA_ALERT_COLOR_BORDER};color: {$PSCA_ALERT_COLOR_TEXT};">
            {$PSCA_ALERT_CART_TEXT}
		</div>
    {/if}
	</section>
{/if}