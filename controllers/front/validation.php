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
class IpaymuValidationModuleFrontController extends ModuleFrontController {

    /**
     * @see FrontController::postProcess()
     */
    public function postProcess() {
        $cart = $this->context->cart;
        if ($cart->id_customer == 0 || $cart->id_address_delivery == 0 || $cart->id_address_invoice == 0 || !$this->module->active)
            Tools::redirect('index.php?controller=order&step=1');

        // Check that this payment option is still available in case the customer changed his address just before the end of the checkout process
        $authorized = false;
        foreach (Module::getPaymentModules() as $module)
            if ($module['name'] == 'ipaymu') {
                $authorized = true;
                break;
            }
        if (!$authorized)
            die($this->module->l('This payment method is not available.', 'validation'));

        $customer = new Customer($cart->id_customer);
        if (!Validate::isLoadedObject($customer))
            Tools::redirect('index.php?controller=order&step=1');

        // URL Payment IPAYMU
        $url = 'https://my.ipaymu.com/payment.htm';

        // Prepare Parameters
        $domain = Tools::getShopDomainSsl(true, true);
        $params = array(
            'key' => Configuration::get('IPAYMU_APIKEY'), // API Key Merchant / Penjual
            'action' => 'payment',
            'product' => 'Order #' . $cart->id,
            'price' => $cart->getOrderTotal(true, Cart::BOTH), // Total Harga
            'quantity' => 1,
            'comments' => 'Pembelian dari ' . $domain, // Optional           
            'ureturn' => $domain . __PS_BASE_URI__ . 'index.php?fc=module&module=ipaymu&controller=confirmation',
            'unotify' => $domain . __PS_BASE_URI__ . 'index.php?fc=module&module=ipaymu&controller=payment',
            'ucancel' => $domain . __PS_BASE_URI__ . 'index.php?controller=order',
            /* Parameter untuk pembayaran lain menggunakan PayPal 
             * ----------------------------------------------- */
//            'paypal_email' => 'test@mail.com',
//            'paypal_price' => 1, // Total harga dalam kurs USD
//            'invoice_number' => uniqid('INV-'), // Optional
            /* ----------------------------------------------- */
            'format' => 'json' // Format: xml / json. Default: xml 
        );

        $params_string = http_build_query($params);

//open connection
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, count($params));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

//execute post
        $request = curl_exec($ch);

        if ($request === false) {
            echo 'Curl Error: ' . curl_error($ch);
        } else {

            $result = json_decode($request, true);

            if (isset($result['url']))
                header('location: ' . $result['url']);
            else {
                echo "Request Error " . $result['Status'] . ": " . $result['Keterangan'];
            }
        }

//close connection
        curl_close($ch);
    }

}
