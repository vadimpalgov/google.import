<?php
/** @global CUser $USER */
/** @global CMain $APPLICATION */
/** @global string $mid */

use Bitrix\Main,
    Bitrix\Main\Loader;

if(!$USER->IsAdmin())
    return;

if (!Loader::includeModule('google.import'))
    return;

$aTabs = [
    ["DIV" => "edit0", "TAB" => 'Настройки', "ICON" => "", "TITLE" => 'Настройки'],
    ["DIV" => "edit2", "TAB" => 'Доступ', "ICON" => "form_settings", "TITLE" => 'Доступ'],
];

$arOptions = [
    'type' => 'Type',
    'project_id' => 'Project id',
    'private_key_id' => 'Private key id',
    'private_key' => 'Private key',
    'client_email' => 'Client email',
    'client_id' => 'Client id',
    'auth_uri' => 'Auth URI',
    'token_uri' => 'Token URI',
    'auth_provider_x509_cert_url' => 'Auth provider x509 cert url',
    'client_x509_cert_url' => 'Client x509 cert url'
];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && check_bitrix_sessid())
{
    if(isset($_POST['Update']) && $_POST['Update'] === 'Y' && is_array($_POST['SETTINGS']))
    {
        foreach($_POST['SETTINGS'] as $k=>$v)
        {
            COption::SetOptionString($mid, $k, (is_array($v) ? serialize($v) : $v));
        }

        LocalRedirect($APPLICATION->GetCurPage().'?lang='.LANGUAGE_ID.'&mid_menu=1&mid='.$moduleId.'&'.$tabControl->ActiveTabParam());
    }
}



$tabControl = new CAdminTabControl("kdaImportexcelTabControl", $aTabs, true, true);

$tabControl->Begin();
?>
<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?lang=<?echo LANGUAGE_ID?>&mid_menu=1&mid=<?=$mid?>" name="kda_importexacel_settings">
<?php  echo bitrix_sessid_post();

$tabControl->BeginNextTab();
?>
    <tr class="heading">
        <td colspan="2">Доступ к API Google Cloud</td>
    </tr>

    <?php foreach ($arOptions as $optionKey=>$optionName){?>
        <tr>
            <td class="col-6"><?=$optionName?></td>
            <td class="col-6">
                <?php if($optionKey == 'private_key'){?>
                <textarea name="SETTINGS[<?=$optionKey?>]" id="" cols="30" rows="10"><?=htmlspecialcharsex(COption::GetOptionString($mid, $optionKey));?></textarea>
                <?php } else {?>
                <input type="text" name="SETTINGS[<?=$optionKey?>]" value="<?=htmlspecialcharsex(COption::GetOptionString($mid, $optionKey));?>"/>
                <?php }?>
            </td>
        </tr>
    <?php }?>

<?php


$tabControl->BeginNextTab();

$module_id = $mid;
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");


$tabControl->Buttons();
?>
<input type="submit" name="Update" value="Сохранить">
<input type="hidden" name="Update" value="Y">
<input type="reset" name="reset" value="Сбросить">
<input type="button" title="По умолчанию" onclick="RestoreDefaults();" value="По умолчанию">

</form>
<?php $tabControl->End();
