<?php
/**
 * @var object $APPLICATION;
 * @var string $DOCUMENT_ROOT;
 * @var string $REQUEST_METHOD;
 * @var string $MODE;
 * @var string $ACTION;
 * @var integer $STEP;
 * @var integer $PROFILE_ID;
 */

require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

use Bitrix\Main\Loader;
use Bitrix\GoogleImport\Client;
use Bitrix\GoogleImport\Sheets;
use Bitrix\GoogleImport\Profile;
use Bitrix\GoogleImport\ProfileManager;

$moduleId = 'google.import';
$moduleFilePrefix = 'google_import';

Loader::includeModule($moduleId);
Loader::includeModule('iblock');
CJSCore::Init([$moduleFilePrefix]);

$STEP = intval($STEP);

if ($STEP <= 0)
    $STEP = 1;

$arProfiles = [
    '1dXJXdQLTFzIqMYEwgHFx3FSh0Aod20kS8KhgTxnmyHQ' => 'Принтеры',
    'reviews' => [
        'name' => 'Отзывы',
        'document_id' => '1-6Tl0X5xt8y8-jofy5O3lh-tYxNl03JZBy0OF904U9A',
        'sheet_id' => 'ОТЗЫВЫ',
        'iblock_id' => '9',
        'profile_class' => '\Bitrix\GoogleImport\Profiles\Review',
    ],
    'printers' => [
        'name' => 'Принтеры',
        'document_id' => '1-6Tl0X5xt8y8-jofy5O3lh-tYxNl03JZBy0OF904U9A',
        'sheet_id' => [
            'Epson_принтеры',
            'Konica_принтеры',
            'Kyocera_принтеры',
            'Brother_принтеры',
            'Canon_принтеры',
            'Xerox_принтеры',
            'HP_принтеры',
            'Samsung_принтеры',
            'Ricoh_принтеры'
        ],
        'iblock_id' => 2,
        'profile_class' => '\Bitrix\GoogleImport\Profiles\Printer',
    ],
    'cartridge' => [
        'name' => 'Картриджи',
        'document_id' => '1-6Tl0X5xt8y8-jofy5O3lh-tYxNl03JZBy0OF904U9A',
        'sheet_id' => [
            'Konica_картриджи',
            'Epson_картриджи',
            'Samsung_картриджи',
            'Brother_картриджи',
            'Kyocera_картриджи',
            'Canon_картриджи',
            'Xerox_картриджи',
            'HP_картриджи',
            'Ricoh_картриджи',
        ],
        'iblock_id' => 17,
        'profile_class' => '\Bitrix\GoogleImport\Profiles\Cartridge',
    ]
];

$client = Client::factory();
$service = new Sheets($client);

if ($REQUEST_METHOD == "POST" && $MODE=='AJAX' && $ACTION == 'IMPORT')
{
    /**
     * @var $OFFSET int
     * @var $STEP_SIZE int
     * @var $STEP_TIME int
     * @var $PROFILE_ID string
     * @var $REQUEST_METHOD
     * @var $MODE
     * @var $ACTION
     * @var $LIMIT
     * @var $ALL_COUNT int
     */

    $manager = new ProfileManager($arProfiles[$PROFILE_ID], $service);

    $manager->setOffset($OFFSET);
    $manager->setLimit($LIMIT);

    $updatedCount = 0;

    foreach ($manager->getValues() as $value)
    {
        $profile = $manager->getProfile($value);
        $profile->run();

        if($profile->isUpdated())
        {
            $updatedCount++;
        }
    }

    $allUpdatedCount = $OFFSET + $updatedCount;

    $percent = (int)$allUpdatedCount/($manager->count()/100);

    $RESULT = [
        'OFFSET' => $allUpdatedCount,
        'PERCENT' => $percent,
        'PROCESSED_COUNT' => $allUpdatedCount,
        'UPDATED_ELEMENT' => $allUpdatedCount,
    ];

    echo json_encode($RESULT);

    die();
}


$APPLICATION->SetTitle('Импорт из Google Cloud - Шаг ' . $STEP);
require ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

if($STEP > 1)
{
    $manager = new ProfileManager($arProfiles[$PROFILE_ID], $service);

}


?>
<form method="POST" action="?lang=<?echo LANG ?>" ENCTYPE="multipart/form-data" name="dataload" id="dataload" class="google-import-step-<?=$STEP?>-form">
<?php

$aTabs = array(
    array(
        'DIV' => 'edit1',
        'TAB' => 'Настройки',
        'ICON' => 'iblock',
        'TITLE' => 'Настройки',
    ) ,
);

$tabControl = new CAdminTabControl("tabControl", $aTabs, false, true);
$tabControl->Begin();

$tabControl->BeginNextTab();
?>

<?php if($STEP == 1){?>

    <tr class="heading">
        <td colspan="2" class="kda-ie-profile-header">
            <div>
                Выберете профиль
            </div>
        </td>
    </tr>
    <tr>
        <td class="col-4">Профиль:</td>
        <td class="col-8">
            <select name="PROFILE_ID" id="">
                <?php foreach ($arProfiles as $profileId => $arProfile){?>
                <option value="<?=$profileId?>"><?=$arProfile['name']?></option>
                <?php }?>
            </select>
        </td>
    </tr>
    <tr>
        <td class="col-4">Размер шага:</td>
        <td class="col-8">
            <input type="text" name="STEP_SIZE" value="100">
        </td>
    </tr>
    <tr>
        <td class="col-4">Ожидание между шагами:</td>
        <td class="col-8">
            <input type="text" name="STEP_TIME" value="10">
        </td>
    </tr>

<?php }?>

<?php if($STEP == 2){?>

    <tr class="heading">
        <td colspan="2" class="kda-ie-profile-header">
            <div>
                Анализ
            </div>
        </td>
    </tr>
    <tr>
        <td class="col-4">Найденно:</td>
        <td class="col-8">
            <?=count($manager->getSheetsIds());?> листов
        </td>
    </tr>
    <tr>
        <td class="col-4"></td>
        <td class="col-8">
            <ul  style="padding: 0px;">
                <?php foreach ($manager->getSheetsIds() as $sheet){?>
                <li><?=$sheet?></li>
                <?php }?>
            </ul>
        </td>
    </tr>
    <tr>
        <td class="col-4">Всего:</td>
        <td class="col-8">
            <?=$manager->count()?> записей
        </td>
    </tr>
    <input type="hidden" name="PROFILE_ID" value="<?=$PROFILE_ID?>">
    <input type="hidden" name="STEP_SIZE" value="<?=$STEP_SIZE?>">
    <input type="hidden" name="STEP_TIME" value="<?=$STEP_TIME?>">
    <input type="hidden" name="OFFSET" value="0">
    <input type="hidden" name="ALL_COUNT" value="<?=$allCount?>">

<?php }?>

<?php if($STEP == 3){?>
    <tr>
        <td id="resblock" class="google-import-result" style="width: 80%">
            <div id="progressbar">
                <span class="pline"></span>
                <span class="presult">
                    <b>0%</b>
                    <span></span>
                </span>
            </div>
        </td>
        <td>
            <div id="infoblock" class="adm-info-message">
                <div class="result-block">
                    <span class="total_line">Всего элементов: <b><?=$manager->count()?></b></span>
                    <span class="processed_line">Обработано элементов: <b></b></span>
                    <span class="updated_line">Обновлено элементов: <b></b></span>
                </div>
            </div>
        </td>
    </tr>

<?}?>



<?php $tabControl->EndTab();

$tabControl->Buttons();

echo bitrix_sessid_post();

if($STEP < 3)
{
    ?>
    <input type="hidden" name="STEP" value="<?echo $STEP + 1; ?>">
    <input type="submit" value="<?echo ($STEP == 2) ? 'Импорт' : 'Следующий шаг'; ?> &gt;&gt;" name="submit_btn" class="adm-btn-save">
    <?
}
else
{
    ?>
    <input type="hidden" name="PROFILE_ID" value="<?=$PROFILE_ID?>">
    <input type="hidden" name="STEP" value="1">
    <input type="hidden" name="ALL_COUNT" value="<?=$manager->count()?>">
    <input type="submit" name="backButton2" value="Назад" class="adm-btn-save">
    <?
}


$tabControl->End();
?>

</form>
    <script language="JavaScript">
        <?php
        if($STEP === 3){
            $arPost = $_POST;
        ?>
        GImport.init(<?=CUtil::PhpToJSObject($arPost);?>);
        <?php }?>
    </script>
<?php

require ($DOCUMENT_ROOT."/bitrix/modules/main/include/epilog_admin.php");

