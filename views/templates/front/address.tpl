{*
* @Module Name: Presta Cart
* @Website: prestayar.com - prestashop cart
* @author Hashem Afkhami <hashem_afkhami@yahoo.com>
* @copyright  2013-2017 prestayar.com
*}
<!-- @file modules\psf_prestacart\views\templates\front\address -->
{if !$ajax} <section class="PSCart"><div id="step2"> {/if}
{if $PSCA_CSS_CUSTOMIZE != ''} <style>{$PSCA_CSS_CUSTOMIZE}</style>	 {/if}

<div id="addressForm">
	<h6 class="big-title dbs-steps">
		<!--div class="dbs-step-done prev">
			<a href="#step21"><i class="icon-right-open"></i><span>{l s='لیست آدرس ها'}</span></a>
		</div-->
		<div class="dbs-step-title"><span>{l s='مشخصات' mod='psf_prestacart'}</span></div>
	</h6>
	<div id="contentBlock">
		{include file="$tpl_dir./errors.tpl"}
		<form action="{$actionUrl}" method="post" id="add_address" {if $ajaxPSCart} class="ajaxPSCart" {/if}>
			<fieldset>
				<div class="clearfix">				

					
					{foreach $PSCA_FIELDS_ADDRESS as $key => $field}
						{if $field.view == 'address'}
                        	{if $key == 'name_merged'}
								{assign var="keyValue" value="lastname" }
                            {else}
                                {assign var="keyValue" value=$key }
                            {/if}
							{if !$typeCart}
								{if $field.data.enable_virtual == '1' && ($field.type == 'text' or $field.type == 'email')}
									<label for="{$key}">{$field.label}{if $field.data.required =='1'}<sup>*</sup>{/if}</label>
									<input type="{$field.type}" name="{$key}" id="{$key}" placeholder="{$field.placeholder}" {if isset($field.class)}class="{$field.class}"{/if} value="{if isset($id_address)}{$address->$keyValue}{elseif !empty($defaultValues.$key)}{$defaultValues.$key}{/if}"/>
								{/if}								
							{else}
								{if $field.data.enable == '1' && ($field.type == 'text' or $field.type == 'email')}
									<label for="{$key}">{$field.label}{if $field.data.required =='1'}<sup>*</sup>{/if}</label>
									<input type="{$field.type}" name="{$key}" id="{$key}" placeholder="{$field.placeholder}" {if isset($field.class)}class="{$field.class}"{/if} value="{if isset($id_address)}{$address->$keyValue}{elseif !empty($defaultValues.$key)}{$defaultValues.$key}{/if}"/>
								{/if}
							{/if}
						{/if}
					{/foreach}
                    {*{if !empty($is_address) }
                        لینک بازگشت
                    {/if}*}
					<input type="hidden" value="{$typeCart}" name="typeCart" />
					{if isset($id_address) }
						<input type="hidden" value="{$id_address}" name="id_address" />
					{/if}		
					<p class="submit">
						<input type="submit" id="SubmitCreate" name="SubmitCreate" class="btn" value="{l s='ذخیره' mod='psf_prestacart'}" />
					</p>

					<div class="alert" id="create_account_error" style="display: none"></div>
				</div>
			</fieldset>
		</form>
		
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
        scrollToStep('#step2');
		$('#add_address').submit(function(){
			if ($(this).hasClass('ajaxPSCart'))
			{
				var data = $(this).serialize(); 
				submitAddress(data);
				return false;			
			}
		});
        {if $ajaxPSCart}
       	 window.history.replaceState("", "", '{$urlPage}' );
        {/if}
        /*$('.dbs-step-done').click(function(){
            $('#step22').html('<p></p>').hide();
            $("#step21").show();
            return false;
        });*/
	});
	function submitAddress(data){
		$.ajax({
			type: 'POST',
			url:"{$actionUrl}",
			async: true,
			cache: false,
			dataType : "json",
			data: data,
			beforeSend: function(){
				$('#ajax_black_address').show();
				$('#create_account_error').html('').hide();
			},				
			success: function(jsonData){
				$('#ajax_black_address').hide();
				if(jsonData.hasError){
					var errors = '';
					for(error in jsonData.errors)
						//IE6 bug fix
						if(error != 'indexOf')
							errors += '<li>'+jsonData.errors[error]+'</li>';
					$('#add_address #create_account_error').html('<ol>'+errors+'</ol>').show();
				}else{
					$('#step22').html('<p></p>').hide();
					$('#step2').html(jsonData.page).show();
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown){
				$('#ajax_black_address').hide();
				alert("{l s='خطا رخ داده است ، دوباره امتحان کنید...' mod='psf_prestacart'}");
			}
		});
	}	
</script>
<div id="ajax_black_address"  class="ajax_black">
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