{*
* @Module Name: Presta Cart
* @Website: prestayar.com - prestashop cart
* @author Hashem Afkhami <hashem_afkhami@yahoo.com>
* @copyright  2013-2017 prestayar.com
*}
<!-- @file modules\prestacart\views\templates\front\register -->
{if !$ajax}
<section class="PSCart">
	<div id="step2">
{/if}
{if $PSCA_CSS_CUSTOMIZE != ''} <style>{$PSCA_CSS_CUSTOMIZE}</style>	 {/if}
<h6 class="big-title dbs-steps">
	<div class="dbs-step-done prev">
		<a href="{$urlPageBack}" data-step="#step1" ><i class="icon-right-open"></i><span>{l s='خلاصه سبد خرید'}</span></a>
	</div>
	<div class="dbs-step-title"><span>{l s='مشخصات' mod='psf_prestacart'}</span></div>
	<div class="dbs-step-todo next">
		{if isset($virtual) and $virtual}
		<span>{l s='تایید سفارش'}</span> 
		{else}
		<span>{l s='روش ارسال و پرداخت'}</span> 
		{/if}
		<i class="icon-left-open"></i>
	</div>
</h6>

{if $PSCA_TAB_ADDRESS == 1}
<a href="#new" class="tabhead{if !$login} active{/if}">{l s='مشتری جدید هستید؟' mod='psf_prestacart'}</a>
<a href="#old" class="tabhead{if $login} active{/if}">{l s='قبلا خرید کرده اید؟' mod='psf_prestacart'}</a>
<div style="clear: both;"></div>	
{/if}

<div id="address" class="full">
	{if $PSCA_TAB_ADDRESS == 0}
	<a href="#old" class="tabhead{if $login} active{/if}">{l s='قبلا خرید کرده اید؟' mod='psf_prestacart'}</a>
	<div style="clear: both;"></div>	
	{/if}

	<div id="old" class="tabbox" {if $login}style="display: block;"{/if}>
		<div class="contentBlock" >
			<form action="index.php?controller=authentication" method="post" class="fromDBS {if $ajaxPSCart}ajaxPSCart{/if}" id="formLogin" >
				<div class="clearfix">
                    {$HOOK_CREATE_ACCOUNT_TOP}
                    {if isset($smarty.get.email)}
						<div class="alert" id="login_error" >{l s='برای ثبت سفارش با این ایمیل نیاز به ورود به حساب کاریری خود دارید.' mod='psf_prestacart'}</div>
					{else}
					<div class="alert" id="login_error" style="display: none" ></div>
					{/if}
					<label for="email" >{l s='آدرس ایمیل :' mod='psf_prestacart'}</label>
					<input type="text" id="email" name="email" value="{if isset($smarty.get.email)}{$smarty.get.email|escape:'html':'UTF-8'}{else}{if isset($smarty.post.email)}{$smarty.post.email|escape:'html':'UTF-8'}{/if}{/if}" placeholder="{l s='example@site.com' mod='psf_prestacart'}" class="ltr" />
					<label for="passwd" >{l s='رمز عبور :' mod='psf_prestacart'}</label>
					<input type="password" id="passwd" name="passwd" value="{if isset($smarty.post.passwd)}{$smarty.post.passwd|stripslashes}{/if}" class="ltr" />
					<input type="submit" id="SubmitLogin" name="SubmitLogin" placeholder="ramzoboor" class="btn" value="{l s='ورود' mod='psf_prestacart'}" />
					
					{if $PSCA_ALERT_LOGIN_FLAG == 1}
						<div class="alert" style="background-color: {$PSCA_ALERT_COLOR_BOX};border-color:{$PSCA_ALERT_COLOR_BORDER};color: {$PSCA_ALERT_COLOR_TEXT};" >{$PSCA_ALERT_LOGIN_TEXT}</div>
					{/if}
                    {$HOOK_CREATE_ACCOUNT_FORM}
				</div>
			</form>			
		</div>	
	</div>
	{if $PSCA_TAB_ADDRESS == 0}
	<a href="#new" class="tabhead {if !$login} active{/if}">{l s='مشتری جدید هستید؟' mod='psf_prestacart'}</a>
	<div style="clear: both;"></div>	
	{/if}

	<div id="new" class="tabbox" {if !$login}style="display: block;"{/if}>
		{include file="$tpl_dir./errors.tpl"}
		<div class="contentBlock">
			<form method="post" id="formRegister" action="{$actionUrl}" {if $ajaxPSCart} class="ajaxPSCart" {/if}>
				<div class="clearfix">

						{foreach $PSCA_FIELDS_ADDRESS as $key => $field}
							{if !$typeCart}
								{assign var='enable' value=$field.data.enable_virtual}
								{assign var='required' value=$field.data.required_virtual}
							{else}
								{assign var='enable' value=$field.data.enable}
								{assign var='required' value=$field.data.required}
							{/if}						
						
							{if $enable == '1' && ($field.type == 'text' or $field.type == 'email' or $field.type == 'password')}
								<label for="{$key}">{$field.label}{if $required =='1'}<sup>*</sup>{/if}</label>
								<input type="{$field.type}" name="{$key}" id="{$key}" placeholder="{$field.placeholder}"
								{if isset($field.class)}class="{$field.class}"{/if}
									   value="{if isset($smarty.post.$key)}{$smarty.post.$key|stripslashes}{/if}"
								/>							
							{elseif $enable == '1' && $field.type == 'radio'}
								<div class="form-radio">
									<label class="control-label">{$field.label}{if $required =='1'}<sup>*</sup>{/if}</label>
									{foreach $field.value as $k => $value}
									<label class="top" for="{$key}{$k}">
										<input type="radio" value="{$k}" id="{$key}{$k}" name="{$key}">
										<span>{$value}</span>
									</label>
									{/foreach}
								</div>	
							{elseif $enable == '1' && $field.type == 'checkbox'}	
								<div class="checkbox">
									<label for="{$key}">
										<input type="checkbox" value="{$field.value}" id="{$key}" name="{$key}" {if $field.data.checked =='1'}checked="checked"{/if}>
										{$field.label}
									</label>
								</div>								
							{/if}
						{/foreach}
                        <input type="hidden" value="{$typeCart}" name="typeCart" id="typeCart" />
						
						{if !$typeCart}	
							<label for="message" style="margin-top: 1.5em;">{l s='پیغام خریدار :' mod='psf_prestacart'}</label>
							<div class="textarea">
								 <textarea  id="message" name="message" rows="2" cols="5"></textarea>
							</div>	
							{if $PSCA_ALERT_GUEST_FLAG == 1}
							<div class="alert"  style="background-color: {$PSCA_ALERT_COLOR_BOX};border-color:{$PSCA_ALERT_COLOR_BORDER};color: {$PSCA_ALERT_COLOR_TEXT};">{$PSCA_ALERT_GUEST_TEXT}</div>
							{/if}							
							<p class="submit">
								<input type="submit" id="SubmitCreate" name="SubmitCreate" class="btn" value="{l s='خرید خود را ثبت کنید' mod='psf_prestacart'}" />
							</p>
							
						{else}	
							{if $PSCA_ALERT_GUEST_FLAG == 1}
							<div class="alert"  style="background-color: {$PSCA_ALERT_COLOR_BOX};border-color:{$PSCA_ALERT_COLOR_BORDER};color: {$PSCA_ALERT_COLOR_TEXT};">{$PSCA_ALERT_GUEST_TEXT}</div>
							{/if}
							<p class="submit">
								<input type="submit" id="SubmitCreate" name="SubmitCreate" class="btn" value="{l s='ثبت اطلاعات و ادامه خرید' mod='psf_prestacart'}" />
							</p>
						{/if}
					<div class="alert alert-danger" id="create_account_error" style="display: none"></div>
				</div>
			</form>	
		</div>
	</div>

</div>
<div id="ajax_black_register" class="ajax_black">
	<span style="color:#fff">{l s='در حال ارسال اطلاعات ....' mod='psf_prestacart'}</span>
</div>	

{if !$typeCart}
	<form action="" method="post" id="payment_form" class="hidden">
		<input type="hidden" name="orderId" value="" />
	</form>
{/if}

<script type="text/javascript">
	$(document).ready(function() {
        scrollToStep('#step2');
	
		$('.tabhead').click(function() {
			$('.tabhead').removeClass('active');
			$(this).addClass('active');
			$('.tabbox').hide();
			$($('.tabhead.active').attr('href')).show();
			return false;
		});
	
		$('#formLogin').submit(function() {
			submitFunction();
			return false;
		});
		
		$('#formRegister').submit(function(event) {
			if ($(this).hasClass('ajaxPSCart'))
			{
				event.preventDefault();
				var data = $(this).serialize(); 
				submitRegister(data);	
				return false;				
			}
		});

        {if $ajaxPSCart}
        	window.history.replaceState("", "", '{$urlPage}' );
        	{if $ajax}backPSCartStep();{/if}
		{/if}

	});
	
	function submitRegister(data){
		$.ajax({
			type: 'POST',
			url:"{$actionUrl}",
			dataType : "json",
			data: data,
            cache: false,
			beforeSend: function(){
				$('#ajax_black_register').show();
				$('#create_account_error').html('').hide();
			},			
			success: function(jsonData){
				$('#ajax_black_register').hide();
				if(jsonData.hasError){
					var errors = '';
					for(error in jsonData.errors)
						//IE6 bug fix
						if(error != 'indexOf')
							errors += '<li>'+jsonData.errors[error]+'</li>';
					$('#create_account_error').html('<ol>'+errors+'</ol>').show();
				}
				else{
                    if(jsonData.urlPayment ){
						$('#payment_form').attr('action',jsonData.urlPayment).submit();
                    }
                    else if(jsonData.view == 'login' || jsonData.action == 'addresses' ) {
                        $('#step2').html(jsonData.page).show();
                    }else{
                        $('#step2').hide();
                        $('#step3').html(jsonData.page).show();
                    }
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown){
				$('#ajax_black_register').hide();
				alert('خطا رخ داده است ، دوباره امتحان کنید...');
			}
		});
	}		
	
	function submitFunction(){
		//send the ajax request to the server
		$.ajax({
			type: 'POST',
			url: baseDir + 'index.php?controller=authentication',
			async: true,
			cache: false,
			dataType : "json",
			data: {
				controller: 'authentication',
				SubmitLogin: 1,
				ajax: true,
				email: $('#email').val(),
				passwd: $('#passwd').val(),
			},
			beforeSend: function(){
				$('#ajax_black_register').show();
				$('#login_error').html('').hide();
			},				
			success: function(jsonData){
				$('#ajax_black_register').hide();
				if (jsonData.hasError){
					var errors = '';
					for(error in jsonData.errors)
						//IE6 bug fix
						if(error != 'indexOf')
							errors += '<li>'+jsonData.errors[error]+'</li>';
					$('#login_error').html('<ol>'+errors+'</ol>').show();
				}else{
					$('#tabs1').html('  <p class="title_block">مشخصات</p>\
												<div id="contentBlock"><div id="login_success" class="alert">\
													<ol>با موفقیت وارد شدید ! درحال بارگذاری اطلاعات .... </ol>\
												</div></div>').show();

					$('#tabs2').hide().remove();
					submitGetAddress();
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown){
				alert("TECHNICAL ERROR: unable to load form.\n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
			}
		});
	}	
	
	function submitGetAddress(){
		var virtual = $("#virtual").val();
		$.ajax({
			type: 'GET',
			url:"{$actionUrlAddresses}",
			dataType : "json",
            cache: false,
			beforeSend: function(){
				$('#ajax_black_register').show();
				$('#create_account_error').html('').hide();
			},			
			success: function(jsonData){
				$('#ajax_black_register').hide();
				if(jsonData.hasError){
					var errors = '';
					for(error in jsonData.errors)
						//IE6 bug fix
						if(error != 'indexOf')
							errors += '<li>'+jsonData.errors[error]+'</li>';
					$('#create_account_error').html('<ol>'+errors+'</ol>').show();
				}else{
					$('#step2').html(jsonData.page);
					
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown){
				$('#ajax_black_register').hide();
				alert("{l s='خطا رخ داده است ، دوباره امتحان کنید...' mod='psf_prestacart'}");
			}
		});
	}		

</script>

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