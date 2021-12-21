<?php

namespace Bitrix\GoogleImport;

use Google_Client;

/**
 * Class Client
 * @package Bitrix\GoogleImport
 */
class Client extends Google_Client
{
    const MODULE_ID = 'google.import';

    protected $options = [
        'type',
        'project_id',
        'private_key_id',
        'private_key',
        'client_email',
        'client_id',
        'auth_uri',
        'token_uri',
        'auth_provider_x509_cert_url',
        'client_x509_cert_url'
    ];

    /**
     * @return Client
     * @throws \Google_Exception
     */
    public static function factory()
    {
        $obj = new self();

        $apiKeyPath = __DIR__ . '/../google-api-key.json';

        if(file_exists($apiKeyPath)){
            $obj->setAuthConfig($apiKeyPath);
        }

        $obj->setScopes(array(Sheets::SPREADSHEETS_READONLY));

        return $obj;
    }


    public function loadAuthConfig()
    {
        $config = [];

        foreach ($this->options as $option)
        {
            $config[$option] = \COption::GetOptionString(self::MODULE_ID,$option);
        }

        $this->setAuthConfig($config);

    }


}