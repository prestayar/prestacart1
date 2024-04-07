{*
* @Module Name: Presta Cart
* @Website: prestayar.com - prestashop cart
* @author Hashem Afkhami <hashem_afkhami@yahoo.com>
* @copyright  2013-2017 prestayar.com
*}
<!-- @file modules\psf_prestacart\views\templates\admin\carriers_payment -->
{if $display_restrictions}
    {foreach $lists as $list}
        {include file='./restrictions.tpl'}
    {/foreach}
{else}
    <div class="alert alert-warning">{l s='No payment module installed' mod='psf_prestacart'}</div>
{/if}