{*
* @Module Name: Presta Cart
* @Website: prestayar.com - prestashop cart
* @author Hashem Afkhami <hashem_afkhami@yahoo.com>
* @copyright  2013-2017 prestayar.com
*}
<!-- @file modules\psf_prestacart\views\templates\hook\header -->
{if isset($hideGuestToCustomer) && $hideGuestToCustomer }
    <script type='text/javascript'>
        $(document).ready(function(){
            var elementGuest = $('#guestToCustomer');
            if ( elementGuest.length ) {
                elementGuest.next('form').remove();
                elementGuest.remove();
            }
        });
    </script>
{/if}