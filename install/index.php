<?php

/**
 * Import data from Google docs
 * Class google_import
 */
class google_import extends \CModule
{
    public $MODULE_ID = 'google.import';

    public $MODULE_VERSION = '1.0.0';

    public $MODULE_VERSION_DATE = '2021-02-22 11:30:00';

    public $MODULE_NAME = 'Импорт данных из Google Docs';

    public $MODULE_DESCRIPTION = '';

    public $PARTNER_NAME = 'Vadim Palgov';

    public $MODULE_PATH = '/local/modules/google.import';

    public function __construct()
    {

    }

    /**
     * Устанавливаем модуль
     */
    public function doInstall()
    {
        $this->installDB();

        // Register module in bitrix
        \Bitrix\Main\ModuleManager::registerModule($this->MODULE_ID);
    }

    /**
     * Удаляем модуль
     */
    public function doUninstall()
    {
        $this->uninstallDB();

        // Unregister module in bitrix
        \Bitrix\Main\ModuleManager::unRegisterModule($this->MODULE_ID);
    }

}