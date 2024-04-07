{*
* @Module Name: Presta Cart
* @Website: prestayar.com - prestashop cart
* @author Hashem Afkhami <hashem_afkhami@yahoo.com>
* @copyright  2013-2017 prestayar.com
*}
<!-- @file modules\psf_prestacart\views\templates\hook\payment -->
<p class="payment_module">
    <a class="dbscod" href="{$link->getModuleLink('psf_prestacart', 'validation', array(), true )|escape:'html'}">
        {if !empty($free_order)}
            {l s='ثبت سفارش را تایید می کنم' mod='psf_prestacart'}
        {else}
            {l s='پرداخت در محل' mod='psf_prestacart'}
            <span>{l s='(شما مبلغ سفارش را هنگام تحویل کالا پرداخت خواهید کرد)' mod='psf_prestacart'}</span>
        {/if}
    </a>
</p>