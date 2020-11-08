<?php


namespace WHMCS\Module\Addon\LegalEntities\Controllers;

use WHMCS\Database\Capsule;
use WHMCS\Module\Addon\LegalEntities\Configs\ModuleConfig;

class UninstallController
{
    public static function deleteGatewayModule()
    {
        $serverModulePath = ModuleConfig::getBaseFullPath() . '/gatewayModule/LegalEntities.php';
        $targetPath = ModuleConfig::getWhmcsRootDir() . '/modules/gateways/LegalEntities.php';

        if (unlink($targetPath)) {
            delete_query("tblpaymentgateways", array( "gateway" => 'LegalEntities' ));
            return [];
        } else {
            return [
                'status' => 'error',
                'description' => 'При удалении символической ссылки возникла ошибка. Цель: ' .
                    $serverModulePath . ' Ссылка:' . $targetPath
            ];
        }
    }  public static function dropTable($tableName)
    {
        try {
            Capsule::schema()->dropIfExists($tableName);
        } catch (\Exception $e) {
            return array(
                'status' => 'error',
                'description' => sprintf("Ошибка при удалении таблицы %s: %s", $tableName, $e->getMessage())
            );
        }

        return [];
    }
}