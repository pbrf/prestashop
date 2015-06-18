<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
include(dirname(__FILE__) . '/../../config/config.inc.php');
include(dirname(__FILE__) . '/../../init.php');
$context = Context::getContext();
if (Tools::getValue('action') != 'pbrf') {
    exit();
}
$date = new stdClass();

$order_id = Tools::getValue("order_id");
$blank = Tools::getValue("blank");
$db = DB::getInstance();
$date->order = $db->getRow('SELECT * FROM `ps_orders` WHERE id_order = ' . $order_id);

//$date->customer = $db->getRow('SELECT * FROM `ps_customer` WHERE id_customer = ' . pSQL($date->order['id_customer']));

$date->address = $db->getRow('SELECT * FROM `ps_address` WHERE id_address = ' . pSQL($date->order['id_address_delivery']));

$date->address['state'] = $db->getValue('SELECT name FROM `ps_state` WHERE id_state = ' . pSQL($date->address['id_state']));

$date->address['country'] = $db->getValue('SELECT name FROM `ps_country_lang` WHERE id_country = ' . pSQL($date->address['id_country']));

$date->params->key = Configuration::get('PS_BLOCK_PBRF_KEY');
$date->params->l_name = Configuration::get('PS_BLOCK_PBRF_L_NAME');
$date->params->f_name = Configuration::get('PS_BLOCK_PBRF_F_NAME');
$date->params->m_name = Configuration::get('PS_BLOCK_PBRF_M_NAME');
$date->params->document = Configuration::get('PS_BLOCK_PBRF_DOCUMENT');
$date->params->document_serial = Configuration::get('PS_BLOCK_PBRF_DOCUMENT_SERIAL');
$date->params->document_number = Configuration::get('PS_BLOCK_PBRF_DOCUMENT_NUMBER');
$date->params->document_day = Configuration::get('PS_BLOCK_PBRF_DOCUMENT_DAY');
$date->params->document_year = Configuration::get('PS_BLOCK_PBRF_DOCUMENT_YEAR');
$date->params->document_issued_by = Configuration::get('PS_BLOCK_PBRF_DOCUMENT_ISSUED_BY');
$date->params->unit_code = Configuration::get('PS_BLOCK_PBRF_UNIT_CODE');
$date->params->message_part1 = Configuration::get('PS_BLOCK_PBRF_MESSAGE_PART1');
$date->params->from_country = Configuration::get('PS_BLOCK_PBRF_FROM_COUNTRY');
$date->params->from_region = Configuration::get('PS_BLOCK_PBRF_FROM_REGION');
$date->params->from_city = Configuration::get('PS_BLOCK_PBRF_FROM_CITY');
$date->params->from_street = Configuration::get('PS_BLOCK_PBRF_FROM_STREET');
$date->params->from_build = Configuration::get('PS_BLOCK_PBRF_FROM_BUILD');
$date->params->from_appartment = Configuration::get('PS_BLOCK_PBRF_FROM_APPARTMENT');
$date->params->from_zip = Configuration::get('PS_BLOCK_PBRF_FROM_ZIP');
$date->params->inn = Configuration::get('PS_BLOCK_PBRF_INN');
$date->params->kor_account = Configuration::get('PS_BLOCK_PBRF_KOR_ACCOUNT');
$date->params->bank_name = Configuration::get('PS_BLOCK_PBRF_BANK_NAME');
$date->params->current = Configuration::get('PS_BLOCK_PBRF_CURRENT');
$date->params->bik = Configuration::get('PS_BLOCK_PBRF_BIK');
$date->params->naim = Configuration::get('PS_BLOCK_PBRF_NAIM');
$date->params->nalojka = Tools::getValue("nalojka", '');
$date->params->cen = Tools::getValue("cen", '');




switch ($blank) {
    case ('7a'): $result = print_f7('a', $date);
        break;
    case ('7b'): $result = print_f7('b', $date);
        break;
    case ('7p'): $result = print_f7('p', $date);
        break;
    case ('113'): $result = print_f112($date);
        break;
    case ('116'): $result = print_f116($date);
        break;
    case ('107'): $result = print_f107($date);
        break;
    case ('adr'): $result = print_adr($date);
        break;
}

$res = json_decode($result);

//если есть ошибка по апи вывести ее
if (isset($res->error)) {
    if ($res->message == 'Not access token')
        exit('Неверный ключ доступа к сервису pbrf.ru. Получите ключ в Личном кабинете и сохраните его в настройках плагина');
    else
        exit($result);
}

echo $result;
exit;

function print_f112($d) {

    $url = 'http://pbrf.ru/pdf.F112';
    $key = $d->params->key;
    $data = array();
    $data['whom_name'] = $d->params->l_name . ' ' . $d->params->f_name . ' ' . $d->params->m_name;
    $data['whom_city'] = $d->params->from_city;
    $data['whom_region '] = $d->params->from_region;
    $data['whom_street'] = $d->params->from_street;
    $data['whom_build'] = $d->params->from_build;
    $data['whom_appartment'] = $d->params->from_appartment;
    $data['whom_zip'] = $d->params->from_zip;
    //if (!empty($order->d_l_name))
    //$data['from_name'] = $order->d_f_name . ' ' . $order->d_m_name;
    //else
    $data['from_name'] = $d->address['firstname'];
    //if (!empty($order->d_l_name))
    //$data['from_surname'] = $order->d_l_name;
    //else
    $data['from_surname'] = $d->address['lastname'];
    //if (!empty($order->d_city))
    //$data['from_city'] = $order->d_city;
    //else
    $data['from_city'] = $d->address['city'];
    //if (!empty($order->d_state))
    //$data['from_region'] = $order->d_state;
    //else
    $data['from_region'] = $d->address['country'];
    $data['from_region'] = $d->address['state'];
    //if (!empty($order->d_street))
    //$data['from_street'] = $order->d_street;
    //else
    $data['from_street'] = $d->address['address1'];
    //if (!empty($order->d_home))
    //$data['from_build'] = $order->d_home;
    //else
    $data['from_build'] = $d->address['address2'];
    //if (!empty($order->d_apartment))
    //$data['from_appartment'] = $order->d_apartment;
    //else
    //$data['from_appartment'] = $order->apartment;
    //if (!empty($order->d_zip))
    //$data['from_zip'] = $order->d_zip;
    //else
    $data['from_zip'] = $d->address['postcode'];



    $data['sum_num'] = round($d->params->cen, 2);
    $data['inn'] = $d->params->inn;
    $data['kor_account'] = $d->params->kor_account;
    $data['current_account'] = $d->params->current_account;
    $data['bik'] = $bik;
    $data['bank_name'] = $d->params->bank_name;
    $data['document'] = $d->params->document;
    $data['document_serial'] = $d->params->document_serial;
    $data['document_number'] = $d->params->document_number;
    $data['document_day'] = $d->params->document_day;
    $data['document_year'] = $d->params->document_year;
    $data['document_issued_by'] = $d->params->document_issued_by;
    $data['message_part1'] = $d->params->message_part1;
    return responce($data, $key, $url);
}

function print_f107($d) {

    $url = 'http://pbrf.ru/pdf.F107';
    $key = $d->params->key;
    $data = array();

    //if (!empty($order->d_l_name))
    //$data['whom'] = $order->d_l_name . ' ' . $order->d_f_name . ' ' . $order->d_m_name;
    //else
    $data['whom'] = $d->address['lastname'] . ' ' . $d->address['firstname'];

    //if (!empty($order->d_city)) {
    //$data['whom_country'] = $order->d_zip . ', Россия, ' . $order->d_state;
    //$data['whom_city'] = $order->d_city . ', ' . $order->d_street . ', д.' . $order->d_home . ', кв.' . $order->d_apartment;
    //} else {
    $data['whom_country'] = $d->address['postcode'] . ', ' . $d->address['country'] . ', ' . $d->address['state'];
    $data['whom_city'] = $d->address['city'] . ', ' . $d->address['address1'] . $d->address['address2'];
    //}
    $data['investment'] = "Заказ №" . $order->id_order;

    $data['object'] = array(array('name' => $d->params->naim, 'count' => $order->product_quantity, 'price' => round($d->params->cen, 2)));
    return responce($data, $key, $url);
}

function print_adr($d) {
    global $paramsplug, $order;
}

function print_f7($b, $d) {
    $url = 'http://pbrf.ru/pdf.F7';
    $key = $d->params->key;
    $data = array();
    $data['type_blank'] = $b; //отправление 1 класса

    $data['from_surname'] = $d->params->l_name;
    $data['from_name'] = $d->params->f_name . ' ' . $d->params->m_name;
    $data['from_city'] = $d->params->from_city;
    $data['from_street'] = $d->params->from_street;
    $data['from_build'] = $d->params->from_build;
    $data['from_appartment'] = $d->params->from_appartment;
    $data['from_zip'] = $d->params->from_zip;
    //if (!empty($order->d_l_name))
    //$data['whom_surname'] = $order->d_l_name;
    //else
    $data['whom_surname'] = $d->address['lastname'];
    //if (!empty($order->d_f_name))
    //$data['whom_name'] = $order->d_f_name . ' ' . $order->d_m_name;
    //else
    $data['whom_name'] = $d->address['firstname'];
    //if (!empty($order->d_city))
    //$data['whom_city'] = $order->d_city;
    //else
    $data['whom_city'] = $d->address['city'];
    //if (!empty($order->d_street))
    //$data['whom_street'] = $order->d_street;
    //else
    $data['whom_street'] = $d->address['address1'];
    //if (!empty($order->d_home))
    //$data['whom_build'] = $order->d_home;
    //else
    $data['whom_build'] = $d->address['address2'];
    //if (!empty($order->d_zip))
    //$data['whom_zip'] = $order->d_zip;
    //else
    $data['whom_zip'] = $d->address['postcode'];

    $data['declared_value'] = round($d->params->cen, 2);
    $data['COD_amount'] = round($d->params->nalojka, 2);
    return responce($data, $key, $url);
}

function print_f116($d) {
    $url = 'http://pbrf.ru/pdf.F116';
    $key = $d->params->key;
    $data = array();
    $data['from_surname'] = $d->params->l_name . ' ' . $d->params->f_name . ' ' . $d->params->m_name;
    $data['from_country'] = $d->params->from_country;
    $data['from_city'] = $d->params->from_city . ', ' . $d->params->from_street . ', ' . $d->params->from_build . ', ' . $d->params->from_appartment;
    $data['from_zip'] = $d->params->from_zip;
    $data['document'] = $d->params->document;
    $data['document_serial'] = $d->params->document_serial;
    $data['document_number'] = $d->params->document_number;
    $data['document_day'] = $d->params->document_day;
    $data['document_year'] = $d->params->document_year;
    $data['document_issued_by'] = $d->params->document_issued_by;
    //if (!empty($order->d_l_name))
    //$data['whom'] = $order->d_l_name . ' ' . $order->d_f_name . ' ' . $order->d_m_nam;
    //else
    $data['whom'] = $d->address['lastname'] . ' ' . $d->address['firstname'];
    $data['whom_country'] = $d->address['country'];
    //if (!empty($order->d_city))
    //$data['whom_city'] = $order->d_city;
    //else
    $data['whom_city'] = $d->address['city'];
    //if (!empty($order->d_street)) {
    //$data['whom_street'] = $order->d_street;
    //if (!empty($order->d_home))
    //$data['whom_street'] .=', д.' . $order->d_home;
    //if (!empty($order->d_apartment))
    //$data['whom_street'] .=', кв.' . $order->d_apartment;
    //}else {
    $data['whom_street'] = $d->address['address1'];
    //if (!empty($order->home))
    // $data['whom_street'] .=', д.' . $order->home;
    //if (!empty($order->apartment))
    $data['whom_street'] .= $d->address['address2'];
    //}
    //if (!empty($order->d_zip))
    //$data['whom_zip'] = $order->d_zip;
    //else
    $data['whom_zip'] = $d->address['postcode'];


    $data['declared_value'] = round($d->params->cen, 2);
    $data['COD_amount'] = round($d->params->nalojka, 2);
    return responce($data, $key, $url);
}

function responce($data, $key, $url) {
    $post = array(
        'access_token' => $key,
        'data' => json_encode($data)
    );
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

?>