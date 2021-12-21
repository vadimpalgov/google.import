<?php

namespace Bitrix\GoogleImport;

use Bitrix\GoogleImport\Profiles\ProfileInterface;


class Profile
{
    public $name;

    public $iblockId;

    public $spreadsheetId;

    public $sheets = [];

    const MODULE_ID = 'google.import';

    /**
     * @param $profile_class
     * @param $iblockId
     * @param $data
     * @return ProfileInterface
     */
    static function factory($profile_class, $iblockId, $data)
    {
        if(class_exists($profile_class))
        {
            return new $profile_class($iblockId, $data);
        }

        return null;
    }
}