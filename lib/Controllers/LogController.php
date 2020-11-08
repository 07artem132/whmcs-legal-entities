<?php
namespace WHMCS\Module\Addon\LegalEntities\Controllers;


use WHMCS\Module\Addon\LegalEntities\Models\LogModel;

class LogController
{
    protected static $namespaceToModule = [
        'general' => '111',
    ];

    static function addSuccess(string $module, string $action): void
    {
        $log = new LogModel();
        $log->status = 1;
        $log->module = self::namespaceToModule($module);
        $log->message = $action;
        $log->saveOrFail();
    }

    static function addError(string $module, string $action, \Throwable $e = null): void
    {
        $log = new LogModel();
        $log->status = 0;
        $log->module = self::namespaceToModule($module);
        $log->message = $action . PHP_EOL . self::formatException($e);
        $log->saveOrFail();
    }
    protected static function namespaceToModule(string $module): string
    {
        if (preg_match('/((?:\\\\{1,2}\w+|\w+\\\\{1,2})(?:\w+\\\\{0,2})+)/', $module, $matches, PREG_OFFSET_CAPTURE, 0) !== 0) {
            $actionName = basename(str_replace('\\', '/', $module));
            foreach (self::$namespaceToModule as $key => $val) {
                if ($actionName == $key) {
                    return $val;
                }
            }
        }
        return $module;
    }

    protected static function formatException(\Throwable $e = null): string
    {
        if ($e == null) return '';
        return 'message->' . $e->getMessage() . PHP_EOL .
            'trace->' . $e->getTraceAsString();
    }
}