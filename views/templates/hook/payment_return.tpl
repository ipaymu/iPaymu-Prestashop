{*
* 2013-2014 Ipaymu
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.ipaymu.com for more information.
*
*  @author Ipaymu <support@ipaymu.com>
*  @copyright  2013-2014 Ipaymu
*  International Registered Trademark & Property of Ipaymu
*}

{if $smarty.const._PS_VERSION_ >= 1.6}
    <div class="box">
        <p>{l s='Your order on %s is complete.' sprintf=$shop_name mod='ipaymu'}
                <br />
                {l s='You have chosen the ipaymu method.' mod='ipaymu'}
                <br /><span class="bold">{l s='Your order will be sent very soon.' mod='ipaymu'}</span>
                <br />{l s='For any questions or for further information, please contact our' mod='ipaymu'} <a href="{$link->getPageLink('contact-form', true)|escape:'html'}">{l s='customer support' mod='ipaymu'}</a>.
        </p>
    </div>
{else}
<p>{l s='Your order on %s is complete.' sprintf=$shop_name mod='ipaymu'}
	<br /><br />
	{l s='You have chosen the ipaymu method.' mod='ipaymu'}
	<br /><br /><span class="bold">{l s='Your order will be sent very soon.' mod='ipaymu'}</span>
	<br /><br />{l s='For any questions or for further information, please contact our' mod='ipaymu'} <a href="{$link->getPageLink('contact-form', true)|escape:'html'}">{l s='customer support' mod='ipaymu'}</a>.
</p>
{/if}