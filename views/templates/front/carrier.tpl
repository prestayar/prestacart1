<!-- @file modules\psf_prestacart\views\templates\front\carrier -->
{if !$ajax}<section class="PSCart"><div id="step3">{/if}
{if $PSCA_CSS_CUSTOMIZE != ''} <style>{$PSCA_CSS_CUSTOMIZE}</style>	 {/if}
<h6 class="big-title dbs-steps">
	<div class="dbs-step-done prev">
		<a href="{$urlPageBack}" data-step="#step2" ><i class="icon-right-open"></i><span>{l s='مشخصات'}</span></a>
	</div>
	<div class="dbs-step-title"><span>{l s='روش تحویل مورد نظرتان را انتخاب کنید' mod='psf_prestacart'}</span></div>
	<div class="dbs-step-todo next"><span>{l s='تایید سفارش'}</span> <i class="icon-left-open"></i></div>
</h6>
<div id="content_block">
	<form action="{$actionUrl}" {if $ajaxPSCart} class="ajaxPSCart" {/if} method="post"  id="formRegOrder" name="formRegOrder">
		<div class="response" style="display: none;"></div>
		<div class="orderCarrierContent" >
            {assign var='countCarrier' value=1}
			{if isset($errors) && $errors}
				{include file="$tpl_dir./errors.tpl"}
			{elseif !$delivery_option_list|@count }
                {assign var=errors value=[{l s='هیچ حاملی برای ارسال به آدرس انتخابی شما موجود نمی‎باشد.' mod='psf_prestacart'}]}
                {include file="$tpl_dir./errors.tpl"}
			{else}

				{foreach $delivery_option_list as $id_address => $option_list}
                	{foreach $option_list as $key => $option}
                        {if $key|substr:0:2 == "0," }
							{assign var=errors value=[{l s='هیچ حاملی برای ارسال به آدرس انتخابی شما موجود نمی‎باشد.' mod='psf_prestacart'}]}
                            {assign var='countCarrier' value=0}
							{include file="$tpl_dir./errors.tpl"}
                        {/if}
					{/foreach}
					{if $countCarrier == 1  }
	    			<table>
						<thead>
							<tr>
                                {if $typePayment == 'Merger'}
									<th>{l s='روش ارسال و پرداخت' mod='psf_prestacart'}</th>
                                {else}
									<th>{l s='روش ارسال' mod='psf_prestacart'}</th>
                                {/if}
			                    <th>{l s='مجموع کالا' mod='psf_prestacart'}</th>
								<th>{l s='هزینه ارسال' mod='psf_prestacart'}</th>
			                    <th>{l s='جمع کل' mod='psf_prestacart'}</th>
	            			</tr>
						</thead>
						<tbody>
							{if $giftAllowed}
								{if $priceDisplay == 1}
									{assign var='price_gift' value=$total_wrapping_tax_exc_cost}
								{else}
									{assign var='price_gift' value=$total_wrapping_cost}
								{/if}
							{else}
								{assign var='price_gift' value=0 }
							{/if}

							{foreach $option_list as $key => $option}
								{if $key=="0,"}

								{elseif $key|substr:0:2 == "0," }
                                    {assign var=errors value=[{l s='هیچ حاملی برای ارسال به آدرس انتخابی شما موجود نمی‎باشد.' mod='psf_prestacart'}]}
                                    {include file="$tpl_dir./errors.tpl"}
								{else}
								<tr>
				                    <td {if !$option.unique_carrier}class="carrierSpec"{/if} >
										<div {if !$option.unique_carrier}class="hide"{/if}>
											<input class="deliveryOptionRadio delivery_option_radio" type="radio" name="delivery_option[{$id_address}]"
											id="delivery_option_{$id_address}_{$option@index}" value="{$key}"
											{if isset($delivery_option[$id_address]) && $delivery_option[$id_address] == $key}checked="checked"{/if} />
											<label for="delivery_option_{$id_address}_{$option@index}" class="deliveryOptionLabel">
												{if $option.unique_carrier}
													{foreach $option.carrier_list as $carrier}
														{if $carrier.logo}
															<img class="order_carrier_logo" src="{$carrier.logo|escape:'htmlall':'UTF-8'}" alt="{$carrier.instance->name|escape:'htmlall':'UTF-8'}"/>
														{*{elseif !$option.unique_carrier}
															{$carrier.instance->name|escape:'htmlall':'UTF-8'}
															{if !$carrier@last} - {/if}*}
														{/if}
														<strong>{$carrier.instance->name|escape:'htmlall':'UTF-8'}</strong>

													{/foreach}
													{if isset($carrier.instance->delay[$cookie->id_lang])}
														 ({$carrier.instance->delay[$cookie->id_lang]|escape:'htmlall':'UTF-8'})
													{/if}
												{/if}
											</label>
										</div>
                                        {if !$option.unique_carrier}
											<table class="delivery_option_carrier{if isset($delivery_option[$id_address]) && $delivery_option[$id_address] == $key} selected{/if} resume table {if $option.unique_carrier} hide{/if}">
												<tr>
                                                    {if !$option.unique_carrier}
														<td rowspan="{$option.carrier_list|@count}" class="delivery_option_radio first_item">
															<input id="delivery_option_{$id_address|intval}_{$option@index}" class="delivery_option_radio" type="radio" name="delivery_option[{$id_address|intval}]" data-key="{$key}" data-id_address="{$id_address|intval}" value="{$key}"{if isset($delivery_option[$id_address]) && $delivery_option[$id_address] == $key} checked="checked"{/if} />
															<label for="delivery_option_{$id_address|intval}_{$option@index}"></label>
														</td>
                                                    {/if}
                                                    {assign var="first" value=current($option.carrier_list)}
                                                    {assign var="carrier_list" value=current($first.product_list[0].carrier_list)}
													<td class="delivery_option_logo{if $carrier_list eq 0} hide{/if}">
                                                        {if $first.logo}
															<img class="order_carrier_logo" src="{$first.logo|escape:'htmlall':'UTF-8'}" alt="{$first.instance->name|escape:'htmlall':'UTF-8'}"/>
                                                        {elseif !$option.unique_carrier}
                                                            {$first.instance->name|escape:'htmlall':'UTF-8'}
                                                        {/if}
													</td>
													<td class="delivery_option_delay{if $option.unique_carrier} first_item{/if}{if $carrier_list eq 0} hide{/if}">
														<input type="hidden" value="{$first.instance->id|intval}" name="id_carrier" />
                                                        {if isset($first.instance->delay[$cookie->id_lang])}
															<i class="icon-info-sign"></i>
                                                            {strip}
                                                                {$first.instance->delay[$cookie->id_lang]|escape:'htmlall':'UTF-8'}
																&nbsp;
                                                                {if count($first.product_list) <= 1}
																	({l s='برای این محصول:'}
                                                                {else}
																	({l s='برای محصولات:'}
                                                                {/if}
                                                            {/strip}
                                                            {foreach $first.product_list as $product}
                                                                {if $product@index == 4}
																	<acronym title="
																{/if}
																{strip}
																	{if $product@index >= 4}
																		{$product.name|escape:'htmlall':'UTF-8'}
																		{if isset($product.attributes) && $product.attributes}
																			{$product.attributes|escape:'htmlall':'UTF-8'}
																		{/if}
																		{if !$product@last}
																			,&nbsp;
																		{else}
																			">&hellip;</acronym>)
                                                            {/if}
                                                            {else}
                                                                {$product.name|escape:'htmlall':'UTF-8'}
                                                                {if isset($product.attributes) && $product.attributes}
                                                                    {$product.attributes|escape:'htmlall':'UTF-8'}
                                                                {/if}
                                                                {if !$product@last}
																	,&nbsp;
                                                                {else}
																	)
                                                                {/if}
                                                            {/if}
                                                            {/strip}
                                                            {/foreach}
                                                        {/if}
													</td>
												</tr>
                                                {foreach $option.carrier_list as $carrier}
                                                    {if $carrier@iteration != 1}
                                                        {assign var="carrier_list" value=current($carrier.product_list[0].carrier_list)}
														<tr>
															<td class="delivery_option_logo{if $carrier_list eq 0} hide{/if}">
                                                                {if $carrier.logo}
																	<img class="order_carrier_logo" src="{$carrier.logo|escape:'htmlall':'UTF-8'}" alt="{$carrier.instance->name|escape:'htmlall':'UTF-8'}"/>
                                                                {elseif !$option.unique_carrier}
                                                                    {$carrier.instance->name|escape:'htmlall':'UTF-8'}
                                                                {/if}
															</td>
															<td class="delivery_option_delay{if $option.unique_carrier} first_item{/if}{if $carrier_list eq 0} hide{/if}">
																<input type="hidden" value="{$first.instance->id|intval}" name="id_carrier" />
                                                                {if isset($carrier.instance->delay[$cookie->id_lang])}
																	<i class="icon-info-sign"></i>
                                                                    {strip}
                                                                        {$carrier.instance->delay[$cookie->id_lang]|escape:'htmlall':'UTF-8'}
																		&nbsp;
                                                                        {if count($carrier.product_list) <= 1}
																			({l s='برای این محصول:'}
                                                                        {else}
																			({l s='برای محصولات:'}
                                                                        {/if}
                                                                    {/strip}
                                                                    {foreach $carrier.product_list as $product}
                                                                        {if $product@index == 4}
																			<acronym title="
																	{/if}
																	{strip}
																		{if $product@index >= 4}
																			{$product.name|escape:'htmlall':'UTF-8'}
																			{if isset($product.attributes) && $product.attributes}
																				{$product.attributes|escape:'htmlall':'UTF-8'}
																			{/if}
																			{if !$product@last}
																				,&nbsp;
																			{else}
																				">&hellip;</acronym>)
                                                                    {/if}
                                                                    {else}
                                                                        {$product.name|escape:'htmlall':'UTF-8'}
                                                                        {if isset($product.attributes) && $product.attributes}
                                                                            {$product.attributes|escape:'htmlall':'UTF-8'}
                                                                        {/if}
                                                                        {if !$product@last}
																			,&nbsp;
                                                                        {else}
																			)
                                                                        {/if}
                                                                    {/if}
                                                                    {/strip}
                                                                    {/foreach}
                                                                {/if}
															</td>
														</tr>
                                                    {/if}
                                                {/foreach}
											</table>
                                        {/if}
				                    </td>
									<td>
										<span class="labelMobile">{l s='مجموع کالا : ' mod='psf_prestacart'}</span>
										{displayPrice price=$total_products}
									</td>
				                    <td id="price_shipping_{$key|trim:','}">
										<span class="labelMobile">{l s='هزینه ارسال : ' mod='psf_prestacart'}</span>
                                        {if $option.total_price_with_tax && !$option.is_free && (!isset($free_shipping) || (isset($free_shipping) && !$free_shipping))}
                                            {if $use_taxes == 1}
                                                {if $priceDisplay == 1}
                                                    {convertPrice price=$option.total_price_without_tax}
                                                    {assign var='price_shipping' value=$option.total_price_without_tax }
                                                {else}
                                                    {convertPrice price=$option.total_price_with_tax}
                                                    {assign var='price_shipping' value=$option.total_price_with_tax }
                                                {/if}
                                            {else}
                                                {convertPrice price=$option.total_price_without_tax}
                                                {assign var='price_shipping' value=$option.total_price_without_tax }
                                            {/if}
                                        {else}
                                            {l s='رایگان'  mod='psf_prestacart'}
                                            {assign var='price_shipping' value=0 }
                                        {/if}
									</td>
				                    <td id="price_{$key|trim:','}" class="price">
										<span class="labelMobile">{l s='جمع کل : ' mod='psf_prestacart'}</span>
										{convertPrice price=$price_shipping+$total_products}
									</td>
				                </tr>
								{/if}
							{/foreach}

						</tbody>
						<tfoot>
							<tr class="cart_total_voucher{if $total_discounts == 0} unvisible{/if}">
								<td colspan="2" class="text-right choose">
									{if $display_tax_label}
										{if $use_taxes && $priceDisplay == 0}
											{l s='تخفیف (با مالیات)' mod='psf_prestacart'}
										{else}
                                            {l s='تخفیف (بدون مالیات)' mod='psf_prestacart'}
										{/if}
									{else}
										{l s='تخفیف' mod='psf_prestacart'}
									{/if}
								</td>
								<td colspan="3" class="price-discount price total_price_container" id="totalDiscount">

                                    {if isset($free_shipping) && $free_shipping }
                                        {if $use_taxes && $priceDisplay == 0}
                                            {assign var='total_discounts_negative' value=($total_discounts-$total_shipping_tax_exc)* -1}
                                        {else}
                                            {assign var='total_discounts_negative' value=($total_discounts_tax_exc-$total_shipping_tax_exc)* -1}
                                        {/if}
                                        {if $total_discounts_negative == '0'}
                                            {l s='ارسال رایگان' mod='psf_prestacart'}
                                        {else}
                                            {displayPrice price=$total_discounts_negative}
                                        {/if}

                                    {else}
                                        {if $use_taxes && $priceDisplay == 0}
                                            {displayPrice price=$total_discounts*-1}
                                        {else}
                                            {displayPrice price=$total_discounts_tax_exc*-1}
                                        {/if}
                                    {/if}

								</td>
							</tr>
							<tr class="cart_total_wrapping{if $total_wrapping == 0} unvisible{/if}">
								<td colspan="2" class="text-right choose">
                                    {if $use_taxes}
                                        {if $display_tax_label}
											{l s='کادوپیچی (با مالیات)' mod='psf_prestacart'}
										{else}
											{l s='کادوپیچی' mod='psf_prestacart'}
										{/if}
                                    {else}
                                        {l s='کادوپیچی' mod='psf_prestacart'}
                                    {/if}
								</td>
								<td colspan="3" class="price-discount price total_price_container" id="totalWrapping">
                                    {if $use_taxes}
                                        {if $priceDisplay}
                                            {displayPrice price=$total_wrapping_tax_exc}
                                        {else}
                                            {displayPrice price=$total_wrapping}
                                        {/if}
                                    {else}
                                        {displayPrice price=$total_wrapping_tax_exc}
                                    {/if}
								</td>
							</tr>
                            {if $use_taxes && $show_taxes && $total_tax != 0 }
								<tr class="cart_total_tax">
									<td colspan="2" class="choose">{l s='مالیات' mod='psf_prestacart'}</td>
									<td colspan="3" class="price total_price_container" id="totalTax">{displayPrice price=$total_tax}</td>
								</tr>
                            {/if}
							<tr class="cart_total">
								<td colspan="2" class="choose">
									{l s='جمع کل (با احتساب هزینه ارسال)' mod='psf_prestacart'}
								</td>
								<td id="totalPrice" colspan="3" class="total_price_container" style="font-size: 18px;"  >
									{if $use_taxes}
                                        {displayPrice price=$total_price}
                                    {else}
                                        {displayPrice price=$total_price_without_tax}
                                    {/if}
								</td>
							</tr>
						</tfoot>
					</table>
					{/if}
					{break}
				{/foreach}
				<div class="hook_extracarrier" id="HOOK_EXTRACARRIER_{$id_address}">
                    {if isset($HOOK_EXTRACARRIER_ADDR) &&  isset($HOOK_EXTRACARRIER_ADDR.$id_address)}{$HOOK_EXTRACARRIER_ADDR.$id_address}{/if}
				</div>
				{if $countCarrier == 1  }
					<div id="gift_content" >
					{if $giftAllowed}
						<div class="carrier_title">{l s='هدیه' mod='psf_prestacart'}</div>
						<div class="checkbox gift">
							<input type="checkbox" name="gift" id="gift" value="1" {if $cart->gift == 1}checked="checked"{/if} data-price="{$price_gift}" />
							<label for="gift">
								{l s='می‌خواهم سفارشم کادوپیچی شود.' mod='psf_prestacart'}
								{if $gift_wrapping_price > 0}
									&nbsp;
									<i>({l s='هزینه اضافی' mod='psf_prestacart'}
										<span class="price" id="gift-price">
											{convertPrice price=$price_gift}
										</span>
										{if $use_taxes}
											{if $priceDisplay == 1}
												{l s='(بدون ماليات)' mod='psf_prestacart'}
											{else}
												{l s='(با مالیات)' mod='psf_prestacart'}
											{/if}
										{/if})
									</i>
								{/if}
							</label>
						</div>
						<div id="gift_div" class="form-group">
							<label for="gift_message">{l s='در صورت تمایل، می‌توانید یک یادداشت در کنار هدیه اضافه کنید:' mod='psf_prestacart'}</label>
							<textarea rows="5" cols="35" id="gift_message" class="form-control" name="gift_message">{$cart->gift_message|escape:'html':'UTF-8'}</textarea>
						</div>
					{else}
						<input type="hidden" name="gift" value="0" />
						<input type="hidden" name="gift_message" value="" />
					{/if}

                    {if $recyclablePackAllowed}
                        <p class="carrier_title">{l s='بسته‌بندی قابل بازیافت‌' mod='psf_prestacart'}</p>
                        <div class="checkbox recyclable">
                            <label for="recyclable">
                                <input type="checkbox" name="recyclable" id="recyclable" value="1"{if $recyclable == 1} checked="checked"{/if} />
                                {l s='من موافقم که سفارشم با بسته بندی قابل بازیافت ارسال شود ' mod='psf_prestacart'}
                            </label>
                        </div>
                    {else}
                        <input type="hidden" name="recyclable" value="0" />
                    {/if}

					</div>
                    {if $conditions && $cms_id && (! isset($advanced_payment_api) || !$advanced_payment_api)}
                        {if isset($override_tos_display) && $override_tos_display}
                            {$override_tos_display}
                        {else}
							<div class="cgvbox">
								<p class="checkbox">
									<label for="cgv">
										<input type="checkbox" name="cgv" id="cgv" value="1" {if $checkedTOS}checked="checked"{/if} />
										{l s='شرایط خدمات را مطالعه کرده و بدون قید و شرط با آن موافقم.'}
									</label>
									<a href="{$link_conditions|escape:'html':'UTF-8'}" class="iframe" rel="nofollow">{l s='(خواندن شرایط خدمات)'}</a>
								</p>
							</div>
                        {/if}
                    {/if}
					{if $PSCA_ALERT_CART_STEP3_FLAG }
					<div class="alert"  style="background-color: {$PSCA_ALERT_COLOR_BOX};border-color:{$PSCA_ALERT_COLOR_BORDER};color: {$PSCA_ALERT_COLOR_TEXT};" >{$PSCA_ALERT_CART_STEP3_TEXT}</div>
					{/if}
					{if $PSCA_BOX_MESSAGE_ORDER }
					<label for="message" style="margin-top: 1.5em;">{l s='پیغام خریدار :' mod='psf_prestacart'}</label>
					<div class="textarea">
						<textarea  id="message" name="message" rows="2" cols="5"></textarea>
					</div>
					{/if}
				{/if}
			{/if}

		</div>
		{if $typePayment == 'Merger'}
			{if !isset($errors) || !$errors}
				<div class="submit" style="text-align:left;">
					<input type="submit" id="SubmitPayment" name="SubmitPayment" class="btn" value="{l s='خرید خود را ثبت کنید' mod='psf_prestacart'}" />
				</div>
			{/if}
		{/if}
	</form>
    {if $countCarrier == 1  }
		{if $typePayment != 'Merger'}
			<div id="HOOK_PAYMENT">{$HOOK_PAYMENT}</div>
		{/if}
	{/if}
</div>

<form action="" method="post" id="payment_form" class="hidden">
    <input type="hidden" name="orderId" value="" />
</form>


<script type="text/javascript">
	var $btnValue = $('#SubmitPayment').val();

    $(document).ready( function () {
        scrollToStep('#step3');
        bindInputsPSCart();
        {if $ajaxPSCart}
        	window.history.replaceState("", "", '{$urlPage}' );
        	{if $ajax}backPSCartStep();{/if}
        {/if}

        $('#formRegOrder').submit(function(event) {
			$('#messageOrder').html('').removeClass('msg error').show();
            if ($(this).hasClass('ajaxPSCart'))
            {
                event.preventDefault();
				var data = $(this).serialize();
				submitPayment(data);
				return false;
            }
        });

        updateCarrierSelectionAndGiftPSCart();
    });

    function bindInputsPSCart() {
        // Order message update
        $('#message').blur(function() {
            $('#opc_delivery_methods-overlay').fadeIn('slow');
            $.ajax({
                type: 'POST',
                headers: { "cache-control": "no-cache" },
                url: '{$actionUrlAjaxMessages}&rand=' + new Date().getTime(),
                async: false,
                cache: false,
                dataType : "json",
                data: 'ajax=true&method=updateMessage&message=' + encodeURIComponent($('#message').val()) + '&token=' + static_token ,
                success: function(jsonData)
                {
                    if (jsonData.hasError)
                    {
                        var errors = '';
                        for(var error in jsonData.errors)
                            //IE6 bug fix
                            if(error !== 'indexOf')
                                errors += $('<div />').html(jsonData.errors[error]).text() + "\n";
                        alert(errors);
                    }
                    else
                        $('#opc_delivery_methods-overlay').fadeOut('slow');
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    if (textStatus !== 'abort')
                        alert("TECHNICAL ERROR: unable to save message \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
                    $('#opc_delivery_methods-overlay').fadeOut('slow');
                }
            });
            if (typeof bindUniform !=='undefined')
                bindUniform();
        });

        // Change Selection Carrier
        $(document).on('change', 'input.deliveryOptionRadio', function(){
            updateCarrierSelectionAndGiftPSCart();
        });

        // Recyclable checkbox
        $('#recyclable').on('click', function(e){
            updateCarrierSelectionAndGiftPSCart();
        });

        // Gift checkbox update
        $('#gift').off('click').on('click', function(e){
            if ($('#gift').is(':checked'))
                $('#gift_div').show();
            else
                $('#gift_div').hide();
            updateCarrierSelectionAndGiftPSCart();
        });

        if ($('#gift').is(':checked'))
            $('#gift_div').show();
        else
            $('#gift_div').hide();
        // Gift message update
        $('#gift_message').on('change', function() {
            updateCarrierSelectionAndGiftPSCart();
        });

        // Term Of Service (TOS)
        $('#cgv').on('click', function(e){
		 updateCarrierSelectionAndGiftPSCart();
        });
    }

    function updateCarrierSelectionAndGiftPSCart() {
        var recyclablePackage = 0;
        var gift = 0;
        var giftMessage = '';
        var cgv = 0;

        var delivery_option_radio = $('.deliveryOptionRadio');
        var delivery_option_params = '&';
        $.each(delivery_option_radio, function(i) {
            if ($(this).prop('checked'))
                delivery_option_params += $(delivery_option_radio[i]).attr('name') + '=' + $(delivery_option_radio[i]).val() + '&';
        });
        if (delivery_option_params == '&')
            delivery_option_params = '&delivery_option=&';

        if ($('input#cgv:checked').length)
            cgv = 1;
        if ($('input#recyclable:checked').length)
            recyclablePackage = 1;
        if ($('input#gift:checked').length)
        {
            gift = 1;
            giftMessage = encodeURIComponent($('#gift_message').val());
        }

        $.ajax({
            type: 'POST',
            headers: { "cache-control": "no-cache" },
            url: '{$actionUrlAjaxPayments}&rand=' + new Date().getTime(),
            async: true,
            cache: false,
            dataType : "json",
            data: 'ajax=true&method=updateCarrierAndGetPayments' + delivery_option_params + 'recyclable=' + recyclablePackage + '&gift=' + gift + '&cgv=' + cgv + '&gift_message=' + giftMessage + '&token=' + static_token ,
            beforeSend: function(){
                $('#HOOK_PAYMENT').html('<p>در حال دریافت روش های پرداخت ...</p>');

                if ($('#SubmitPayment').length) {
					$('#SubmitPayment').val('در حال بروز رسانی ...');
					$('#SubmitPayment').addClass('disabled');
					$('#SubmitPayment').attr('disabled', 'disabled');
				}
            },
			success: function(jsonData)
            {
                if (jsonData.hasError)
                {
                    var errors = '';
                    for(var error in jsonData.errors)
                        //IE6 bug fix
                        if(error !== 'indexOf')
                            errors += $('<div />').html(jsonData.errors[error]).text() + "\n";

					$('#HOOK_PAYMENT').html('<div class="msg error">'+errors+'</div>');
                }
                else
                {
                    $('#HOOK_PAYMENT').html(jsonData.HOOK_PAYMENT);
                    updateCartSummaryPSCart( jsonData.summary, jsonData.free_shipping );
                }

				if ($('#SubmitPayment').length) {
					$('#SubmitPayment').removeClass('disabled');
					$('#SubmitPayment').removeAttr('disabled', '');
					$('#SubmitPayment').val($btnValue);
				}

            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                if (textStatus !== 'abort') {
                    console.log("TECHNICAL ERROR: unable to save carrier");
                    console.log(XMLHttpRequest);
                    console.log(textStatus);
				}
                $('#opc_delivery_methods-overlay, #opc_payment_methods-overlay').fadeOut('slow');
            }
        });
    }

    function updateCartSummaryPSCart(json, free_shipping ) {
		// set price carrier
	    var carrierID = json.carrier.id;
	    if( json.total_shipping_tax_exc == 0 )
            $("#price_shipping_"+carrierID).html("{l s='رایگان' mod='psf_prestacart'}");
	    $("#price_"+carrierID).html(formatCurrency(json.total_shipping_tax_exc+json.total_products, currencyFormat, currencySign, currencyBlank));

	    // set price discounts
        var total_discounts;
        if ( free_shipping )
		{
            {if $use_taxes && $priceDisplay == 0}
            	total_discounts = (json.total_discounts-json.total_shipping_tax_exc);
            {else}
            	total_discounts = (json.total_discounts_tax_exc-json.total_shipping_tax_exc);
            {/if}

            if (total_discounts == '0') {
                $("#price_"+carrierID).html(formatCurrency(json.total_products, currencyFormat, currencySign, currencyBlank));
                $('#totalDiscount').html('ارسال رایگان');
            }
            else
            	$('#totalDiscount').html('-' + formatCurrency(total_discounts, currencyFormat, currencySign, currencyBlank));

		}
		else{
            {if $use_taxes && $priceDisplay == 0}
            	total_discounts = json.total_discounts;
            {else}
            	total_discounts = json.total_discounts_tax_exc;
            {/if}
            $('#totalDiscount').html('-' + formatCurrency(total_discounts, currencyFormat, currencySign, currencyBlank));
		}
		if( total_discounts != 0 )
		    $(".cart_total_voucher").removeClass("unvisible");
		else
            $(".cart_total_voucher").addClass("unvisible");


        if( json.total_wrapping_tax_exc != 0 ){
            $('#totalWrapping ').html(formatCurrency(json.total_wrapping_tax_exc, currencyFormat, currencySign, currencyBlank));
            $(".cart_total_wrapping").removeClass("unvisible");
		}
        else
            $(".cart_total_wrapping").addClass("unvisible");

        $('#totalPrice').html(formatCurrency(json.total_price, currencyFormat, currencySign, currencyBlank));
        $('#totalTax').html(formatCurrency(json.total_tax, currencyFormat, currencySign, currencyBlank));
    }

    function submitPayment(data){
        $.ajax({
            type: 'POST',
            url:"{$actionUrl}",
            dataType : "json",
            data: data,
            cache: false,
            beforeSend: function(){
                $('#ajax_black_step3').show();
                $('#create_account_error').html('').hide();
            },
            success:function(jsonData){
                $('#ajax_black_step3').hide();
                if(jsonData.hasError){
                    var errors = '';
                    for(error in jsonData.errors)
                        //IE6 bug fix
                        if(error != 'indexOf')
                            errors += '<li>'+jsonData.errors[error]+'</li>';

                    $('#messageOrder').html('<ol>'+errors+'</ol>').addClass('msg error').show();
                }
                else{
                    if( jsonData.urlPayment )
                        $('#payment_form').attr('action',jsonData.urlPayment).submit();
					else{
                        $('#step2').hide();
                        $('#step3').html(jsonData.page).show();
					}
                }

            }
        })
    }
</script>


<div id="ajax_black_step3" class="ajax_black">
	<span>{l s='در حال ارسال اطلاعات ....' mod='psf_prestacart'}</span>
</div>
<div id="messageOrder"></div>

{if !$ajax}
	</div>
    {if $PSCA_ALERT_CART_FLAG == 1 }
		<div class="alert alert_cart_text"  style="background-color: {$PSCA_ALERT_COLOR_BOX};border-color:{$PSCA_ALERT_COLOR_BORDER};color: {$PSCA_ALERT_COLOR_TEXT};">
            {$PSCA_ALERT_CART_TEXT}
		</div>
    {/if}
	</section>
{/if}

{strip}
    {addJsDef currency=$currency}
    {addJsDef currencyRate=$currencyRate|floatval}
    {addJsDef currencySign=$currency->sign|html_entity_decode:2:"UTF-8"}
    {addJsDef currencyFormat=$currency->format|intval}
    {addJsDef currencyBlank=$currency->blank|intval}
{/strip}
{if $conditions}
    {addJsDefL name=msg_order_carrier}{l s='شما باید برای ثبت سفارش، شرایط خدمات را بپذیرید.' js=1}{/addJsDefL}
{/if}
