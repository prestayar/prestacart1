{*
* @Module Name: Presta Cart
* @Website: prestayar.com - prestashop cart
* @author Hashem Afkhami <hashem_afkhami@yahoo.com>
* @copyright  2013-2017 prestayar.com
*}
<!-- @file modules\psf_prestacart\views\templates\front\validation -->
{if $PSCA_CSS_CUSTOMIZE != ''} <style>{$PSCA_CSS_CUSTOMIZE}</style>	 {/if}
<div class="PSCart">
	<div id="messageConfirmation">
        <h6 class="big-title yep">سفارش شما با موفقیت ثبت شد!</h6>
        {$messageConfirmation}
    </div>
    {assign var='virtual' value='1'}
    {foreach from=$products item=product key=k}
    {if !isset($product.deleted)}
    {if $product.download_hash  && $product.display_filename != '' && $product.product_quantity_refunded == 0 && $product.product_quantity_return == 0}
    {if $virtual == 1}
		<h6>محصولات را دانلود کنید : </h6>
        {assign var='virtual' value='0'}
    {/if}
    {if isset($is_guest) && $is_guest}
	<a href="{$link->getPageLink('get-file', true, NULL, "key={$product.filename|escape:'html':'UTF-8'}-{$product.download_hash|escape:'html':'UTF-8'}&amp;id_order={$order->id}&secure_key={$order->secure_key}")|escape:'html':'UTF-8'}" title="{l s='Download this product'  mod='psf_prestacart'}">
        {else}
		<a href="{$link->getPageLink('get-file', true, NULL, "key={$product.filename|escape:'html':'UTF-8'}-{$product.download_hash|escape:'html':'UTF-8'}")|escape:'html':'UTF-8'}" title="{l s='Download this product' mod='psf_prestacart'}">
            {/if}
			<img src="{$img_dir}icon/download_product.gif" class="icon" alt="{l s='Download product'  mod='psf_prestacart'}" />
		</a>
        {if isset($is_guest) && $is_guest}
			<a href="{$link->getPageLink('get-file', true, NULL, "key={$product.filename|escape:'html':'UTF-8'}-{$product.download_hash|escape:'html':'UTF-8'}&id_order={$order->id}&secure_key={$order->secure_key}")|escape:'html':'UTF-8'}" title="{l s='Download this product'  mod='psf_prestacart'}">دانلود : {$product.product_name|escape:'html':'UTF-8'} 	</a>
        {else}
			<a href="{$link->getPageLink('get-file', true, NULL, "key={$product.filename|escape:'html':'UTF-8'}-{$product.download_hash|escape:'html':'UTF-8'}")|escape:'html':'UTF-8'}" title="{l s='Download this product'  mod='psf_prestacart'}">دانلود : {$product.product_name|escape:'html':'UTF-8'} 	</a>
        {/if}
        {/if}
        {/if}
        {/foreach}
	<br />
	<p class="bold">
		<a href="{$link->getPageLink('history', true)}">» {l s='نمایش سفارش های من' mod='psf_prestacart'}</a>
	</p>
	<p>
		{l s='در صورتی که هرگونه سوال، نظر یا مشکلی دارید با بخش' mod='psf_prestacart'} <a href="{$link->getPageLink('contact', true)}"><strong>{l s='پشتیبانی مشتریان تماس بگیرید' mod='psf_prestacart'}</strong></a>.
	</p>	
</div>

 
 