<?php

/*
* 2013-2014 Ipaymu
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.ipaymu.com for more information.
*
*  @author Ipaymu <support@ipaymu.com>
*  @copyright  2013-2014 Ipaymu
*  International Registered Trademark & Property of Ipaymu
*/

/**
 * @deprecated 1.5.0 This file is deprecated, use moduleFrontController instead
 */
include(dirname(__FILE__) . '/../../config/config.inc.php');
include(dirname(__FILE__) . '/../../header.php');
include(dirname(__FILE__) . '/ipaymu.php');

$context = Context::getContext();
$cart = $context->cart;
$ipaymu = new Ipaymu();

if ($cart->id_customer == 0 OR $cart->id_address_delivery == 0 OR $cart->id_address_invoice == 0 OR ! $ipaymu->active)
    Tools::redirect('index.php?controller=order&step=1');

// Check that this payment option is still available in case the customer changed his address just before the end of the checkout process
$authorized = false;
foreach (Module::getPaymentModules() as $module)
    if ($module['name'] == 'ipaymu') {
        $authorized = true;
        break;
    }
if (!$authorized)
    die($ipaymu->l('This payment method is not available.', 'validation'));

$customer = new Customer((int) $cart->id_customer);

if (!Validate::isLoadedObject($customer))
    Tools::redirect('index.php?controller=order&step=1');

$currency = $context->currency;
$total = (float) ($cart->getOrderTotal(true, Cart::BOTH));

$ipaymu->validateOrder($cart->id, Configuration::get('PS_OS_PAYMENT'), $total, $ipaymu->displayName, NULL, array(), (int) $currency->id, false, $customer->secure_key);

$order = new Order($ipaymu->currentOrder);
Tools::redirect('index.php?controller=order-confirmation&id_cart=' . $cart->id . '&id_module=' . $ipaymu->id . '&id_order=' . $ipaymu->currentOrder . '&key=' . $customer->secure_key);
