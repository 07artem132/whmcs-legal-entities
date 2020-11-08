<?php

namespace WHMCS\Module\Addon\LegalEntities\Configs;

class ModuleConfig
{
    private const defaultLanguage = 'russian';
    private const whmcsRootDir = ROOTDIR;
    private const tempPath = self::whmcsRootDir . '/modules/addons/' . self::moduleName . '/temp';
    private const backupPath = self::whmcsRootDir . '/modules/addons/' . self::moduleName . '/backup';
    private const uploadDir = 'upload';
    private const uploadPath = self::whmcsRootDir . '/modules/addons/' . self::moduleName . '/' . self::uploadDir;
    private const relativePath = '/modules/addons/' . self::moduleName;
    private const moduleName = 'LegalEntities';

    /**
     * @return string
     */
    public static function geTempPath(): string
    {
        return self::tempPath;
    }

    /**
     * @return string
     */
    public static function geUploadPath(): string
    {
        return self::uploadPath;
    }

    /**
     * @return string
     */
    public static function geLinkUploadedFile($file_name): string
    {
        return self::relativePath . '/' . self::uploadDir . '/' . $file_name;
    }

    /**
     * @return string
     */
    public static function geRelativePath(): string
    {
        return self::relativePath;
    }

    /**
     * @return string
     */
    public static function geBackupPath(): string
    {
        return self::backupPath;
    }

    /**
     * @return string
     */
    public static function getWhmcsRootDir(): string
    {
        return self::whmcsRootDir;
    }

    /**
     * @return string
     */
    public static function getDefaultLanguage(): string
    {
        return self::defaultLanguage;
    }

    /**
     * @return string
     */
    public static function getModuleName(): string
    {
        return self::moduleName;
    }

    public static function getBaseFullPath(): string
    {
        return self::getWhmcsRootDir() . '/modules/addons/' . self::getModuleName();
    }

    /**
     * @return string
     */
    public static function getModuleLink(): string
    {
        global $customadminpath;

        return '/' . $customadminpath . '/addonmodules.php?module=' . self::getModuleName();
    }
    /**
     * @return string
     */
    public static function getModuleLinkClient(): string
    {

        return   '/?m=' . self::getModuleName();
    }
}