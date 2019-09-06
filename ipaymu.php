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

if (!defined('_PS_VERSION_'))
    exit;

class Ipaymu extends PaymentModule {

    private $_html = '';
    private $_postErrors = array();
    public $username;
    public $api_key;

    public function __construct() {
        $this->name = 'ipaymu';
        $this->tab = 'payments_gateways';
        $this->version = '2.0.0';
        $this->author = 'PrestaShop';
        $this->controllers = array('payment', 'validation');

        $this->currencies = true;
        $this->currencies_mode = 'checkbox';

        $config = Configuration::getMultiple(array('IPAYMU_USERNAME', 'IPAYMU_APIKEY'));
        if (!empty($config['IPAYMU_USERNAME']))
            $this->username = $config['IPAYMU_USERNAME'];
        if (!empty($config['IPAYMU_APIKEY']))
            $this->api_key = $config['IPAYMU_APIKEY'];

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Ipaymu');
        $this->description = $this->l('Accept payments for your products via Ipaymu.');
        $this->confirmUninstall = $this->l('Are you sure about removing these details?');
        if (!isset($this->username) || !isset($this->api_key))
            $this->warning = $this->l('Username and API_KEY details must be configured before using this module.');
        if (!count(Currency::checkPaymentCurrencies($this->id)))
            $this->warning = $this->l('No currency has been set for this module.');
    }

    public function install() {
        if (!parent::install() || !$this->registerHook('payment') || !$this->registerHook('paymentReturn'))
            return false;
        return true;
    }

    public function uninstall() {
        if (!Configuration::deleteByName('IPAYMU_USERNAME') || !Configuration::deleteByName('IPAYMU_APIKEY') || !parent::uninstall())
            return false;
        return true;
    }

    private function _postValidation() {
        if (Tools::isSubmit('btnSubmit')) {
            if (!Tools::getValue('IPAYMU_USERNAME'))
                $this->_postErrors[] = $this->l('Username is required.');
            elseif (!Tools::getValue('IPAYMU_APIKEY'))
                $this->_postErrors[] = $this->l('Api key is required.');
        }
    }

    private function _postProcess() {
        if (Tools::isSubmit('btnSubmit')) {
            Configuration::updateValue('IPAYMU_USERNAME', Tools::getValue('IPAYMU_USERNAME'));
            Configuration::updateValue('IPAYMU_APIKEY', Tools::getValue('IPAYMU_APIKEY'));
        }
        $this->_html .= $this->displayConfirmation($this->l('Settings updated'));
    }

    private function _displayIpaymu() {
        return $this->display(__FILE__, 'infos.tpl');
    }

    public function getContent() {
        if (Tools::isSubmit('btnSubmit')) {
            $this->_postValidation();
            if (!count($this->_postErrors))
                $this->_postProcess();
            else
                foreach ($this->_postErrors as $err)
                    $this->_html .= $this->displayError($err);
        } else {
            $this->_html .= '<br />';
        }

        $this->_html .= $this->_displayIpaymu();
        $this->_html .= $this->renderForm();

        return $this->_html;
    }

    public function hookPayment($params) {
        if (!$this->active)
            return;
        if (!$this->checkCurrency($params['cart']))
            return;

        $this->context->controller->addCSS($this->_path . 'ipaymu.css', 'all');
        $this->smarty->assign(array(
            'this_path' => $this->_path,
            'this_path_bw' => $this->_path,
            'this_path_ssl' => Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__ . 'modules/' . $this->name . '/'
        ));
        return $this->display(__FILE__, 'payment.tpl');
    }

    public function hookPaymentReturn($params) {
        if (!$this->active)
            return;

        return $this->display(__FILE__, 'payment_return.tpl');
    }

    public function checkCurrency($cart) {
        $currency_order = new Currency($cart->id_currency);
        $currencies_module = $this->getCurrency($cart->id_currency);

        if (is_array($currencies_module))
            foreach ($currencies_module as $currency_module)
                if ($currency_order->id == $currency_module['id_currency'])
                    return true;
        return false;
    }

    public function renderForm() {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('IPAYMU API DETAILS'),
                    'icon' => 'icon-envelope'
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('USERNAME'),
                        'name' => 'IPAYMU_USERNAME',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('API KEY'),
                        'name' => 'IPAYMU_APIKEY',
//                        'desc' => $this->l('Such as bank branch, IBAN number, BIC, etc.')
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                )
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();
        $helper->id = (int) Tools::getValue('id_carrier');
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'btnSubmit';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        return $helper->generateForm(array($fields_form));
    }

    public function getConfigFieldsValues() {
        return array(
            'IPAYMU_USERNAME' => Tools::getValue('IPAYMU_USERNAME', Configuration::get('IPAYMU_USERNAME')),
            'IPAYMU_APIKEY' => Tools::getValue('IPAYMU_APIKEY', Configuration::get('IPAYMU_APIKEY')),
        );
    }

}
