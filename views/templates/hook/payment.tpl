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
<div class="row">
	<div class="col-xs-12 col-md-6">
            <p class="payment_module">
                    <a class="ipaymu" href="{$link->getModuleLink('ipaymu', 'payment')|escape:'html'}" title="{l s='Ipaymu' mod='ipaymu'}">
                            {l s='Ipaymu' mod='ipaymu'}&nbsp;<span>{l s='(Transaksi online lebih mudah)' mod='ipaymu'}</span>
                    </a>
            </p>
        </div>
</div>
{else}
<p class="payment_module">
	<a href="{$link->getModuleLink('ipaymu', 'payment')|escape:'html'}" title="{l s='Ipaymu' mod='ipaymu'}">
		<img src="{$this_path_bw}logo.png" alt="{l s='ipaymu' mod='ipaymu'}" width="64" height="64"/>
		{l s='Ipaymu' mod='ipaymu'}&nbsp;<span>{l s='(Transaksi online lebih mudah)' mod='ipaymu'}</span>
	</a>
</p>
{/if}