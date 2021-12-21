<?php
/**
 * Created by PhpStorm.
 * User: Vadim Palgov
 */
namespace Bitrix\GoogleImport\Profiles;

interface ProfileInterface
{

    public function __construct($iBlockId, $data);

    public function run();

    public function isUpdated();

}