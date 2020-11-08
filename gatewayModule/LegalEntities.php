<?php

use WHMCS\Module\Addon\LegalEntities\Configs\ModuleConfig;

if (!function_exists('LegalEntities_config')) {
    function LegalEntities_config()
    {
        return [
            'FriendlyName' => array(
                'Type' => 'System',
                'Value' => 'Оплата для юридических лиц'
            ),
        ];
    }
}

function LegalEntities_link($params)
{
    global $_LANG;
    $id = urlencode(base64_encode(encrypt((string)$params['invoiceid'])));
    $fileName = sprintf('%s - счет %s от %s.pdf', $params['companyname'], $params['invoicenum'], substr($params['dueDate'], 0, -9));
    $code = '<a  href="' . ModuleConfig::getModuleLinkClient() . '&pdf=1&id=' . $id . '"  download="' . $fileName . '" /> <button>Скачать</button></a>' . PHP_EOL;
    $code .= '<a  href="' . ModuleConfig::getModuleLinkClient() . '&pdf=1&id=' . $id . '"   /> <button>Просмотреть</button></a>' . PHP_EOL;
    return $code;
}