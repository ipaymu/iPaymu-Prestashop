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


{capture name=path}{l s='Shipping' mod='ipaymu'}{/capture}
{*{include file="$tpl_dir./breadcrumb.tpl"}*}

<h2>{l s='Order summary' mod='ipaymu'}</h2>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

<h3>{l s='Ipaymu Payment' mod='ipaymu'}</h3>

<form action="{$link->getModuleLink('ipaymu', 'validation', [], true)|escape:'html'}" method="post">
    {if $smarty.const._PS_VERSION_ >= 1.6}
        <div class="box">
    {/if}
	<input type="hidden" name="confirm" value="1" />
	<p>
		<img src="{$this_path_bw}logo-ipaymu.png" alt="{l s='Ipaymu Payment' mod='ipaymu'}" style="float:left; margin: 0px 10px 5px 0px;" />
		{l s='You have chosen the Ipaymu payment method.' mod='ipaymu'}
		<br/><br />
		{l s='The total amount of your order is' mod='ipaymu'}
		<span id="amount_{$currencies.0.id_currency}" class="price">{convertPrice price=$total}</span>
		{if $use_taxes == 1}
		    {l s='(tax incl.)' mod='ipaymu'}
		{/if}
	</p>
	<p>
            {if $smarty.const._PS_VERSION_ < 1.6}
                <br /><br />
		<br /><br />
            {/if}		
		<b>{l s='Please confirm your order by clicking \'I confirm my order\'.' mod='ipaymu'}.</b>
	</p>
        {if $smarty.const._PS_VERSION_ >= 1.6}
            </div>
        {/if}
	<p class="cart_navigation" id="cart_navigation">
            {if $smarty.const._PS_VERSION_ >= 1.6}
		<a href="{$link->getPageLink('order', true)}?step=3" class="button-exclusive btn btn-default"><i class="icon-chevron-left"></i>{l s='Other payment methods' mod='ipaymu'}</a>
                <button type="submit" class="button btn btn-default button-medium" ><span>{l s='I confirm my order' mod='ipaymu'}</span></button>
            {else}
                <a href="{$link->getPageLink('order', true)}?step=3" class="button_large">{l s='Other payment methods' mod='ipaymu'}</a>
                <input type="submit" value="{l s='I confirm my order' mod='ipaymu'}" class="exclusive_large" />
            {/if}
	</p>
</form>
