<?php

if (!defined('_CAN_LOAD_FILES_'))
    exit;

class BlockPbrf extends Module {

    private $_html = '';
    private $_postErrors = array();
    private $hooks = array('displayBackOfficeHeader');

    public function __construct() {
        $this->name = 'blockpbrf';
        $this->tab = 'other';
        $this->version = '1.0.0';
        $this->author = 'pbrf.ru';
        $this->module_key = "";
        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Block PBRF.ru');
        $this->description = $this->l('Print form Russian Post from you order');
    }

    public function registerHooks() {

        foreach ($this->hooks as $hook) {
            if (!$this->registerHook($hook)) {
                $this->_errors[] = "Failed to install hook '$hook'<br />\n";
                return false;
            }
        }

        return true;
    }

    public function unregisterHooks() {

        foreach ($this->hooks as $hook) {
            if (!$this->unregisterHook($hook)) {
                $this->_errors[] = "Failed to uninstall hook '$hook'<br />\n";
                return false;
            }
        }

        return true;
    }

    public function install() {
        return (parent::install() && $this->registerHooks()
                );
    }

    public function uninstall() {
        return (parent::uninstall() && $this->unregisterHooks()
                );
    }

    /**
     * Returns module content for top hook
     *
     * @param array $params Parameters
     * @return string Content
     */
    public function hookdisplayBackOfficeHeader($params) {
        if ($this->context->controller->controller_name != 'AdminOrders') {
            return;
        }
        $suf='';
        if (isset($_REQUEST['id_order'])) {
            $suf='_one';
        }
        if (method_exists($this->context->controller, 'addJquery')) {
            $this->context->controller->addJquery();
        }

        if (version_compare(_PS_VERSION_, '1.6.0', '>=') === true) {
            if (method_exists($this->context->controller, 'addCss')) {
                $this->context->controller->addCss($this->_path . 'views/css/pbrf_bt.css');
            }
            if (method_exists($this->context->controller, 'addJs')) {
                $this->context->controller->addJs($this->_path . 'views/js/pbrf_bt'.$suf.'.js', 'all');
            }
        } else {
            if (method_exists($this->context->controller, 'addCss')) {
                $this->context->controller->addCss($this->_path . 'views/css/pbrf.css');
            }
            if (method_exists($this->context->controller, 'addJs')) {
                $this->context->controller->addJs($this->_path . 'views/js/pbrf'.$suf.'.js', 'all');
            }
        }


        return;
    }

    public function getContent() {
        $output = null;

        if (Tools::isSubmit('submit' . $this->name)) {
            Configuration::updateValue('PS_BLOCK_PBRF_KEY', Tools::getValue('key'));
            Configuration::updateValue('PS_BLOCK_PBRF_L_NAME', Tools::getValue('l_name'));
            Configuration::updateValue('PS_BLOCK_PBRF_F_NAME', Tools::getValue('f_name'));
            Configuration::updateValue('PS_BLOCK_PBRF_M_NAME', Tools::getValue('m_name'));
            Configuration::updateValue('PS_BLOCK_PBRF_DOCUMENT', Tools::getValue('document'));
            Configuration::updateValue('PS_BLOCK_PBRF_DOCUMENT_SERIAL', Tools::getValue('document_serial'));
            Configuration::updateValue('PS_BLOCK_PBRF_DOCUMENT_NUMBER', Tools::getValue('document_number'));
            Configuration::updateValue('PS_BLOCK_PBRF_DOCUMENT_DAY', Tools::getValue('document_day'));
            Configuration::updateValue('PS_BLOCK_PBRF_DOCUMENT_YEAR', Tools::getValue('document_year'));
            Configuration::updateValue('PS_BLOCK_PBRF_DOCUMENT_ISSUED_BY', Tools::getValue('document_issued_by'));
            Configuration::updateValue('PS_BLOCK_PBRF_UNIT_CODE', Tools::getValue('unit_code'));
            Configuration::updateValue('PS_BLOCK_PBRF_MESSAGE_PART1', Tools::getValue('message_part1'));
            Configuration::updateValue('PS_BLOCK_PBRF_FROM_COUNTRY', Tools::getValue('from_country'));
            Configuration::updateValue('PS_BLOCK_PBRF_FROM_REGION', Tools::getValue('from_region'));
            Configuration::updateValue('PS_BLOCK_PBRF_FROM_CITY', Tools::getValue('from_city'));
            Configuration::updateValue('PS_BLOCK_PBRF_FROM_STREET', Tools::getValue('from_street'));
            Configuration::updateValue('PS_BLOCK_PBRF_FROM_BUILD', Tools::getValue('from_build'));
            Configuration::updateValue('PS_BLOCK_PBRF_FROM_APPARTMENT', Tools::getValue('from_appartment'));
            Configuration::updateValue('PS_BLOCK_PBRF_FROM_ZIP', Tools::getValue('from_zip'));
            Configuration::updateValue('PS_BLOCK_PBRF_INN', Tools::getValue('inn'));
            Configuration::updateValue('PS_BLOCK_PBRF_KOR_ACCOUNT', Tools::getValue('kor_account'));
            Configuration::updateValue('PS_BLOCK_PBRF_BANK_NAME', Tools::getValue('bank_name'));
            Configuration::updateValue('PS_BLOCK_PBRF_CURRENT', Tools::getValue('current'));
            Configuration::updateValue('PS_BLOCK_PBRF_BIK', Tools::getValue('bik'));
            Configuration::updateValue('PS_BLOCK_PBRF_NAIM', Tools::getValue('naim'));
            Configuration::updateValue('PS_BLOCK_PBRF_BARCODE', Tools::getValue('barcode'));
            Configuration::updateValue('PS_BLOCK_PBRF_TO_TYPE', Tools::getValue('to_type'));
            Configuration::updateValue('PS_BLOCK_PBRF_TO_PHONE', Tools::getValue('to_phone'));
            $output .= $this->displayConfirmation($this->l('Settings updated'));
        }
        return $output . $this->displayForm();
    }

    public function displayForm() {
        // Get default language
        $default_lang = (int) Configuration::get('PS_LANG_DEFAULT');

        // Init Fields form array
        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Settings'),
            ),
            'description' => $this->l('This block print forms Russian Post from service PBRF.ru.') . '<br/><br/>' .
            $this->l('And receive a key in a private office and save this') . '<br/><br/>',
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Key PBRF'),
                    'name' => 'key',
                    'size' => 40,
                    'required' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Last name'),
                    'name' => 'l_name',
                    'size' => 40,
                    'required' => false
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('First name'),
                    'name' => 'f_name',
                    'size' => 40,
                    'required' => false
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Middle name'),
                    'name' => 'm_name',
                    'size' => 40,
                    'required' => false
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Document'),
                    'name' => 'document',
                    'size' => 40,
                    'required' => false
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Document serial'),
                    'name' => 'document_serial',
                    'size' => 40,
                    'required' => false
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Document number'),
                    'name' => 'document_number',
                    'size' => 40,
                    'required' => false
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Document day'),
                    'name' => 'document_day',
                    'size' => 40,
                    'required' => false
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Document year'),
                    'name' => 'document_year',
                    'size' => 40,
                    'required' => false
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Document issued by'),
                    'name' => 'document_issued_by',
                    'size' => 40,
                    'required' => false
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Unit_code'),
                    'name' => 'unit_code',
                    'size' => 40,
                    'required' => false
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Message for F112'),
                    'name' => 'message_part1',
                    'size' => 40,
                    'required' => false
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('From country'),
                    'name' => 'from_country',
                    'size' => 40,
                    'required' => false
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('From region'),
                    'name' => 'from_region',
                    'size' => 40,
                    'required' => false
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('From city'),
                    'name' => 'from_city',
                    'size' => 40,
                    'required' => false
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('From street'),
                    'name' => 'from_street',
                    'size' => 40,
                    'required' => false
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('From build'),
                    'name' => 'from_build',
                    'size' => 40,
                    'required' => false
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('From appartment'),
                    'name' => 'from_appartment',
                    'size' => 40,
                    'required' => false
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('From zip'),
                    'name' => 'from_zip',
                    'size' => 40,
                    'required' => false
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('INN'),
                    'name' => 'inn',
                    'size' => 40,
                    'required' => false
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Kor account'),
                    'name' => 'kor_account',
                    'size' => 40,
                    'required' => false
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Bank name'),
                    'name' => 'bank_name',
                    'size' => 40,
                    'required' => false
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Current account'),
                    'name' => 'current',
                    'size' => 40,
                    'required' => false
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('BIK'),
                    'name' => 'bik',
                    'size' => 40,
                    'required' => false
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Product'),
                    'name' => 'naim',
                    'size' => 40,
                    'required' => false
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Barcode'),
                    'name' => 'barcode',
                    'size' => 40,
                    'required' => false
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('To_type'),
                    'name' => 'to_type',
                    'size' => 40,
                    'required' => false
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('To_phone'),
                    'name' => 'to_phone',
                    'size' => 40,
                    'required' => false
                )
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'button'
            )
        );

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;

        // Language
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;

        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;        // false -> remove toolbar
        $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = 'submit' . $this->name;
        $helper->toolbar_btn = array(
            'save' =>
            array(
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&save' . $this->name .
                '&token=' . Tools::getAdminTokenLite('AdminModules'),
            ),
            'back' => array(
                'href' => AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            )
        );

        // Load current value
        $helper->fields_value['key'] = Configuration::get('PS_BLOCK_PBRF_KEY');
        $helper->fields_value['l_name'] = Configuration::get('PS_BLOCK_PBRF_L_NAME');
        $helper->fields_value['f_name'] = Configuration::get('PS_BLOCK_PBRF_F_NAME');
        $helper->fields_value['m_name'] = Configuration::get('PS_BLOCK_PBRF_M_NAME');
        $helper->fields_value['document'] = Configuration::get('PS_BLOCK_PBRF_DOCUMENT');
        $helper->fields_value['document_serial'] = Configuration::get('PS_BLOCK_PBRF_DOCUMENT_SERIAL');
        $helper->fields_value['document_number'] = Configuration::get('PS_BLOCK_PBRF_DOCUMENT_NUMBER');
        $helper->fields_value['document_day'] = Configuration::get('PS_BLOCK_PBRF_DOCUMENT_DAY');
        $helper->fields_value['document_year'] = Configuration::get('PS_BLOCK_PBRF_DOCUMENT_YEAR');
        $helper->fields_value['document_issued_by'] = Configuration::get('PS_BLOCK_PBRF_DOCUMENT_ISSUED_BY');
        $helper->fields_value['unit_code'] = Configuration::get('PS_BLOCK_PBRF_UNIT_CODE');
        $helper->fields_value['message_part1'] = Configuration::get('PS_BLOCK_PBRF_MESSAGE_PART1');
        $helper->fields_value['from_country'] = Configuration::get('PS_BLOCK_PBRF_FROM_COUNTRY');
        $helper->fields_value['from_region'] = Configuration::get('PS_BLOCK_PBRF_FROM_REGION');
        $helper->fields_value['from_city'] = Configuration::get('PS_BLOCK_PBRF_FROM_CITY');
        $helper->fields_value['from_street'] = Configuration::get('PS_BLOCK_PBRF_FROM_STREET');
        $helper->fields_value['from_build'] = Configuration::get('PS_BLOCK_PBRF_FROM_BUILD');
        $helper->fields_value['from_appartment'] = Configuration::get('PS_BLOCK_PBRF_FROM_APPARTMENT');
        $helper->fields_value['from_zip'] = Configuration::get('PS_BLOCK_PBRF_FROM_ZIP');
        $helper->fields_value['inn'] = Configuration::get('PS_BLOCK_PBRF_INN');
        $helper->fields_value['kor_account'] = Configuration::get('PS_BLOCK_PBRF_KOR_ACCOUNT');
        $helper->fields_value['bank_name'] = Configuration::get('PS_BLOCK_PBRF_BANK_NAME');
        $helper->fields_value['current'] = Configuration::get('PS_BLOCK_PBRF_CURRENT');
        $helper->fields_value['bik'] = Configuration::get('PS_BLOCK_PBRF_BIK');
        $helper->fields_value['naim'] = Configuration::get('PS_BLOCK_PBRF_NAIM');
        $helper->fields_value['barcode'] = Configuration::get('PS_BLOCK_PBRF_BARCODE');
        $helper->fields_value['to_type'] = Configuration::get('PS_BLOCK_PBRF_TO_TYPE');
        $helper->fields_value['to_phone'] = Configuration::get('PS_BLOCK_PBRF_TO_PHONE');
        
     
        return $helper->generateForm($fields_form);
    }

}
