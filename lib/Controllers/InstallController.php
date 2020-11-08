<?php

namespace WHMCS\Module\Addon\LegalEntities\Controllers;

use Illuminate\Database\Schema\Blueprint;
use WHMCS\Database\Capsule;
use Exception;
use WHMCS\Module\Addon\LegalEntities\Configs\ModuleConfig;

class InstallController
{
    public static function installGatewayModule()
    {
        $serverModulePath = ModuleConfig::getBaseFullPath() . '/gatewayModule/LegalEntities.php';
        $targetPath = ModuleConfig::getWhmcsRootDir() . '/modules/gateways/LegalEntities.php';

        if (symlink($serverModulePath, $targetPath)) {
            delete_query("tblpaymentgateways", array( "gateway" => 'LegalEntities' ));
            insert_query("tblpaymentgateways", array( "gateway" => 'LegalEntities', "setting" => "name", "value" => 'Оплата для юридических лиц', "order" => 999 ));
            insert_query("tblpaymentgateways", array( "gateway" => 'LegalEntities', "setting" => "type", "value" => 'Invoices' ));
            insert_query("tblpaymentgateways", array( "gateway" => 'LegalEntities', "setting" => "visible", "value" => "on" ));
            return [];
        } else {
            return [
                'status' => 'error',
                'description' => 'При создании символической ссылки возникла ошибка. Цель: ' .
                    $serverModulePath . ' Ссылка:' . $targetPath
            ];
        }
    }

    public static function createTableSettings()
    {
        try {
            $tbl_name = 'mod_addon_legal_entities_setting';
            if (!Capsule::schema()->hasTable($tbl_name)) {
                Capsule::schema()->create($tbl_name, function ($table) {
                    /** @var Blueprint $table */
                    $table->increments('id');
                    $table->string('key');
                    $table->string('val');
                    $table->timestamps();
                });
            }
        } catch (Exception $e) {
            return array(
                'status' => 'error',
                'description' => sprintf('Ошибка при создании таблицы: %s , %s', $tbl_name, $e->getMessage())
            );
        }
        return [];
    }

    public static function createTableLog()
    {
        try {
            $tbl_name = 'mod_addon_legal_entities_log';
            if (!Capsule::schema()->hasTable($tbl_name)) {
                Capsule::schema()->create($tbl_name, function ($table) {
                    /** @var Blueprint $table */
                    $table->increments('id');
                    $table->boolean('status');
                    $table->string('module');
                    $table->text('message');
                    $table->timestamps();
                });
            }
        } catch (Exception $e) {
            return array(
                'status' => 'error',
                'description' => sprintf('Ошибка при создании таблицы: %s , %s', $tbl_name, $e->getMessage())
            );
        }
        return [];
    }

    public static function createTableDoc()
    {
        try {
            $tbl_name = 'mod_addon_legal_entities_doc';
            if (!Capsule::schema()->hasTable($tbl_name)) {
                Capsule::schema()->create($tbl_name, function ($table) {
                    /** @var Blueprint $table */
                    $table->increments('id');
                    $table->string('name');
                    $table->string('type');
                    $table->string('rel_id');
                    $table->integer('rel_type');
                    $table->boolean('send_edf');
                    $table->boolean('send_mail');
                    $table->text('file');
                    $table->timestamps();
                });
            }
        } catch (Exception $e) {
            return array(
                'status' => 'error',
                'description' => sprintf('Ошибка при создании таблицы: %s , %s', $tbl_name, $e->getMessage())
            );
        }
        return [];
    }
}