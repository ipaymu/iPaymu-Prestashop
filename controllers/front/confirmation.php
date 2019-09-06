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
 * @since 1.5.0
 */
class IpaymuConfirmationModuleFrontController extends ModuleFrontController {

    public $ssl = true;
    public $display_column_left = false;

    /**
     * @see FrontController::initContent()
     */
    public function initContent() {
        parent::initContent();

        $cart = $this->context->cart;
        $customer = new Customer($cart->id_customer);
        $currency = $this->context->currency;
        $total = (float) $cart->getOrderTotal(true, Cart::BOTH);
        $mailVars = array();
        
        if(Tools::getValue('status') == 'berhasil')
            $this->module->validateOrder($cart->id, Configuration::get('PS_OS_PAYMENT'), $total, $this->module->displayName, NULL, $mailVars, (int) $currency->id, false, $customer->secure_key);
        else
            $this->module->validateOrder($cart->id, Configuration::get('PS_OS_ERROR'), $total, $this->module->displayName, NULL, $mailVars, (int) $currency->id, false, $customer->secure_key);
        Tools::redirect('index.php?controller=order-confirmation&id_cart='.$cart->id.'&id_module='.$this->module->id.'&id_order='.$this->module->currentOrder.'&key='.$customer->secure_key);
    }
}
