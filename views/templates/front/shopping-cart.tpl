{*
* @Module Name: Presta Cart
* @Website: prestayar.com - prestashop cart
* @author Hashem Afkhami <hashem_afkhami@yahoo.com>
* @copyright  2013-2017 prestayar.com
*}
<!-- @file modules\psf_prestacart\views\templates\front\shopping-cart -->
{if $PSCA_CSS_CUSTOMIZE != ''} <style>{$PSCA_CSS_CUSTOMIZE}</style>	 {/if}
{if !isset($empty)}
	{* include file="$tpl_dir./order-steps-dbs.tpl" *} {* #dbs# check version 3.1 *}
{/if}

{if $PSCA_ALERT_FLAG_TOP == 1 }
	<div class="alert alert_cart_text_top" style="background-color: {$PSCA_ALERT_COLOR_BOX};border-color:{$PSCA_ALERT_COLOR_BORDER};color: {$PSCA_ALERT_COLOR_TEXT};">
		{$PSCA_ALERT_TEXT_TOP}
	</div>                        
{/if}

<section class="PSCart">

	<div id="messageError">
        {include file="$tpl_dir./errors.tpl"}
	</div>

	
	{if isset($empty)}
		<p class="warning">{l s='سبد خرید شما خالی است.'}</p>
	{elseif $PS_CATALOG_MODE}
		<p class="warning">{l s='این فروشگاه سفارش جدید شما را قبول نکرده است.'}</p>
	{else}
		<div id="step1" style="position: relative;">
			<h6 class="big-title dbs-steps">
				<div class="dbs-step-done prev">
					<a href="#step1"><i class="icon-right-open"></i><span>{l s='خلاصه سبد خرید'}</span></a>
				</div>		
				<div class="dbs-step-title"><span>{l s='خلاصه سبد خرید'}</span></div>
				<div class="dbs-step-todo next"><span>{l s='مشخصات پستی'}</span> <i class="icon-left-open"></i></div>
			</h6>
			<div id="order-detail-content">
				<table>
					<thead>
						<tr>
							<th class="cart_product">{l s='محصول'}</th>
							<th class="cart_description">{l s='توضیحات'}</th>
							<th class="cart_unit">{l s='قیمت واحد'}</th>
							<th class="cart_quantity">{l s='تعداد'}</th>
							<th class="cart_total">{l s='مجموع'}</th>
							<th class="cart_delete">&nbsp;</th>
						</tr>
					</thead>
					<tfoot>
						{if $voucherAllowed}
							<tr class="cart_voucher discount">
								<td colspan="4">
									{if isset($errors_discount) && $errors_discount}
										<ul class="error">
										{foreach $errors_discount as $k=>$error}
											<li>{$error|escape:'htmlall':'UTF-8'}</li>
										{/foreach}
										</ul>
									{/if}
									<form action="{$link->getModuleLink('psf_prestacart', 'order', array(), true )}" method="post" id="voucher">
											<p class="discount_name_block">
												<label for="discount_name">{l s='در صورت در اختیار داشتن کد تخفیف در کادر مقابل وارد نموده و اعمال کنید:'}</label>
												<input type="submit" name="submitAddDiscount" value="{l s='اعمال'}" class="Button" />
												<input type="text" class="discount_name_1" id="discount_name_1" name="discount_name" value="{if isset($discount_name) && $discount_name}{$discount_name}{/if}" />
												<input type="hidden" name="submitDiscount" />
											</p>
									</form>
								</td>
								<td colspan="2">
									{if $displayVouchers}
										<p id="title" class="title_offers">{l s='استفاده از پیشنهادات ما :'}</p>
										<div id="display_cart_vouchers">
										{foreach $displayVouchers as $voucher}
											{if $voucher.code != ''}<span onclick="$('#discount_name').val('{$voucher.code}');return false;" class="voucher_name" data-code="{$voucher.code}">{$voucher.code}</span> - {/if}{$voucher.name}
										{/foreach}
										</div>
									{/if}
								</td>
							</tr>
						{/if}
						<tr>
							<td colspan="4" class="choose">
								{include file="./citystate.tpl"}

							</td>
							<td colspan="3" id="total_price_container" class="total_price_container">
								<div id="total_shipping_text" ><span>{l s='هزینه ارسال :'}</span><span id="total_shipping"></span></div>
								<span id="total_price_text">{l s='جمع کل ( بدون احتساب هزینه ارسال )'}</span>
								<span id="total_price_without_tax">
									{if isset($free_shipping) && $free_shipping }
                                        {displayPrice price=$total_price_without_tax}
									{else}
                                        {displayPrice price=$total_price_without_tax-$total_shipping_tax_exc}
									{/if}
								</span>
							</td>
						</tr>
					</tfoot>
					<tbody>
					{foreach $products as $product}
						{assign var='productId' value=$product.id_product}
						{assign var='productAttributeId' value=$product.id_product_attribute}
						{assign var='quantityDisplayed' value=0}
						{assign var='odd' value=$product@iteration%2}
						{assign var='ignoreProductLast' value=isset($customizedDatas.$productId.$productAttributeId) || count($gift_products)}
						{* Display the product line *}
						{include file="./shopping-cart-product-line.tpl" productLast=$product@last productFirst=$product@first}
						{* Then the customized datas ones*}
						{if isset($customizedDatas.$productId.$productAttributeId)}
							{foreach $customizedDatas.$productId.$productAttributeId[$product.id_address_delivery] as $id_customization=>$customization}
								<tr id="product_{$product.id_product}_{$product.id_product_attribute}_{$id_customization}_{$product.id_address_delivery|intval}" class="product_customization_for_{$product.id_product}_{$product.id_product_attribute}_{$product.id_address_delivery|intval}{if $odd} odd{else} even{/if} customization alternate_item {if $product@last && $customization@last && !count($gift_products)}last_item{/if}">
									<td></td>
									<td colspan="2">
										{foreach $customization.datas as $type => $custom_data}
											{if $type == $CUSTOMIZE_FILE}
												<div class="customizationUploaded">
													<ul class="customizationUploaded">
														{foreach $custom_data as $picture}
															<li><img src="{$pic_dir}{$picture.value}_small" alt="" class="customizationUploaded" /></li>
														{/foreach}
													</ul>
												</div>
											{elseif $type == $CUSTOMIZE_TEXTFIELD}
												<ul class="typedText">
													{foreach $custom_data as $textField}
														<li>
															{if $textField.name}
																{$textField.name}
															{else}
																{l s='متن #'}{$textField@index+1}
															{/if}
															{l s=':'} {$textField.value}
														</li>
													{/foreach}

												</ul>
											{/if}

										{/foreach}
									</td>
									<td class="cart_quantity" colspan="2">
										{if isset($cannotModify) AND $cannotModify == 1}
											<span style="float:left">{if $quantityDisplayed == 0 AND isset($customizedDatas.$productId.$productAttributeId)}{$customizedDatas.$productId.$productAttributeId|@count}{else}{$product.cart_quantity-$quantityDisplayed}{/if}</span>
										{else}
											<div class="cart_quantity_button">
												<a rel="nofollow" class="cart_quantity_up" id="cart_quantity_up_{$product.id_product}_{$product.id_product_attribute}_{$id_customization}_{$product.id_address_delivery|intval}" href="{$link->getPageLink('cart', true, NULL, "add=1&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_address_delivery={$product.id_address_delivery}&amp;id_customization={$id_customization}&amp;token={$token_cart}")}" title="{l s='افزودن'}">+</a>
												<input type="hidden" value="{$customization.quantity}" name="quantity_{$product.id_product}_{$product.id_product_attribute}_{$id_customization}_{$product.id_address_delivery|intval}_hidden"/>
												<input type="text" value="{$customization.quantity}" class="cart_quantity_input" name="quantity_{$product.id_product}_{$product.id_product_attribute}_{$id_customization}_{$product.id_address_delivery|intval}"/>
												{if $product.minimal_quantity < ($customization.quantity -$quantityDisplayed) OR $product.minimal_quantity <= 1}
												<a rel="nofollow" class="cart_quantity_down" id="cart_quantity_down_{$product.id_product}_{$product.id_product_attribute}_{$id_customization}_{$product.id_address_delivery|intval}" href="{$link->getPageLink('cart', true, NULL, "add=1&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_address_delivery={$product.id_address_delivery}&amp;id_customization={$id_customization}&amp;op=down&amp;token={$token_cart}")}" title="{l s='کاستن'}">-</a>
												{else}
												<a class="cart_quantity_down" style="opacity: 0.3;" id="cart_quantity_down_{$product.id_product}_{$product.id_product_attribute}_{$id_customization}" href="#" title="{l s='کاستن'}">-</a>
												{/if}
											</div>

										{/if}
									</td>
									<td class="cart_delete">
										{if isset($cannotModify) AND $cannotModify == 1}
										{else}
												<a rel="nofollow" class="cart_quantity_delete" id="{$product.id_product}_{$product.id_product_attribute}_{$id_customization}_{$product.id_address_delivery|intval}" href="{$link->getPageLink('cart', true, NULL, "delete=1&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_customization={$id_customization}&amp;id_address_delivery={$product.id_address_delivery}&amp;token={$token_cart}")}">{l s='حذف'}</a>
										{/if}
									</td>
								</tr>
								{assign var='quantityDisplayed' value=$quantityDisplayed+$customization.quantity}
							{/foreach}
							{* If it exists also some uncustomized products *}
							{if $product.quantity-$quantityDisplayed > 0}{include file="./shopping-cart-product-line.tpl" productLast=$product@last productFirst=$product@first}{/if}
						{/if}
					{/foreach}
					{assign var='last_was_odd' value=$product@iteration%2}
					{foreach $gift_products as $product}
						{assign var='productId' value=$product.id_product}
						{assign var='productAttributeId' value=$product.id_product_attribute}
						{assign var='quantityDisplayed' value=0}
						{assign var='odd' value=($product@iteration+$last_was_odd)%2}
						{assign var='ignoreProductLast' value=isset($customizedDatas.$productId.$productAttributeId)}
						{assign var='cannotModify' value=1}
						{* Display the gift product line *}
						{include file="./shopping-cart-product-line.tpl" productLast=$product@last productFirst=$product@first}
					{/foreach}
					{if sizeof($discounts)}
						{foreach $discounts as $discount}
							<tr class="cart_discount {if $discount@last}last_item{elseif $discount@first}first_item{else}item{/if}" id="cart_discount_{$discount.id_discount}">
								<td class="cart_discount_name" colspan="4">{$discount.name}</td>
								<td class="cart_discount_price">
									<span class="price-discount price">
									{if isset($free_shipping) && $free_shipping }
                                        {if !$priceDisplay}
                                            {assign var='total_discounts_negative' value=($discount.value_real-$total_shipping_tax_exc)*-1}
                                        {else}
                                            {assign var='total_discounts_negative' value=($discount.value_tax_exc-$total_shipping_tax_exc)*-1}
                                        {/if}
                                        {if $total_discounts_negative == '0'}
                                            {l s='ارسال رایگان' mod='psf_prestacart'}
                                        {else}
                                            {displayPrice price=$total_discounts_negative}
                                        {/if}

                                    {else}
                                        {if !$priceDisplay}
                                            {displayPrice price=$discount.value_real*-1}
                                        {else}
                                            {displayPrice price=$discount.value_tax_exc*-1}
                                        {/if}
                                    {/if}


									</span>
								</td>
								<td class="price_discount_del">
									{if strlen($discount.code)}<a href="{$link->getModuleLink('psf_prestacart', 'order', array(), true  )}?deleteDiscount={$discount.id_discount}" class="price_discount_delete" title="{l s='حذف'}">{l s='حذف'}</a>
									{/if}
								</td>
							</tr>
						{/foreach}
					{/if}
					</tbody>
				</table>
				<div class="msg" style="display:none"></div>			
				<div id="ajax_black" class="ajax_black">
					<span>{l s='در حال ارسال اطلاعات ....'}</span>
				</div>
			</div>
			{if $show_option_allow_separate_package}
				<p>
					<input type="checkbox" name="allow_seperated_package" id="allow_seperated_package" {if $cart->allow_seperated_package}checked="checked"{/if} />
					<label for="allow_seperated_package">{l s='ابتدا محصولات در دسترس فرستاده شود'}</label>
				</p>
			{/if}
            {if isset($HOOK_SHOPPING_CART)}
				<div id="HOOK_SHOPPING_CART">{$HOOK_SHOPPING_CART}</div>
            {/if}
            {if isset($HOOK_SHOPPING_CART_EXTRA)}
				<div id="HOOK_SHOPPING_CART_EXTRA">{$HOOK_SHOPPING_CART_EXTRA}</div>
            {/if}
		</div>
	{/if}
		
	<div id="step2" style="position: relative;"></div>
	<div id="step3" style="position: relative;"></div>
	{if $PSCA_ALERT_CART_FLAG == 1 }
		<div class="alert alert_cart_text"  style="background-color: {$PSCA_ALERT_COLOR_BOX};border-color:{$PSCA_ALERT_COLOR_BORDER};color: {$PSCA_ALERT_COLOR_TEXT};">
            {$PSCA_ALERT_CART_TEXT}
		</div>
    {/if}

</section>
{strip}
    {addJsDef currency=$currency}
    {addJsDef currencyRate=$currencyRate|floatval}
    {addJsDef currencySign=$currency->sign|html_entity_decode:2:"UTF-8"}
    {addJsDef currencyFormat=$currency->format|intval}
    {addJsDef currencyBlank=$currency->blank|intval}

    {addJsDef deliveryAddress=$cart->id_address_delivery|intval}
    {addJsDefL name=txtProduct}{l s='product' js=1}{/addJsDefL}
    {addJsDefL name=txtProducts}{l s='products' js=1}{/addJsDefL}
{/strip}