<?php
/**
 * Created by PhpStorm.
 * User: Vadim Palgov
 */

namespace Bitrix\GoogleImport\Profiles;

use Bitrix\Iblock\InheritedProperty\ElementTemplates;
use CIBlockElement;

/**
 * Class Cartridge
 * @package Bitrix\GoogleImport\Profiles
 */
class Cartridge implements ProfileInterface
{
    public $iBlockId;

    public $data = [];

    public $dataMap = [
        'id',
        'model',
        'text',
        'date_add',
        'title',
        'description',
        'keys',
        'date_text_update',
        'primary_key_frequency',
        'refill_keywords',
        'refill_title',
        'refill_description',
        'refill_text',
    ];

    public function __construct($iBlockId, $data)
    {
        $this->iBlockId = $iBlockId;

        foreach ($this->dataMap as $i=>$key) {
            if(isset($data[$i])) {
                $this->data[$key] = $data[$i];
            }
        }
    }

    public function run()
    {
        if($this->data['model'] == 'Модель') return;

        if($id = $this->find()){

            $element = new CIBlockElement;

            $arElement = [
                'IBLOCK_ID' => $this->iBlockId,
                'PREVIEW_TEXT' => $this->data['text']
            ];

            $iProp = [];

            if($this->data['title']){
                $iProp['ELEMENT_META_TITLE'] = $this->data['title'];
                $iProp['ELEMENT_PAGE_TITLE'] = $this->data['title'];
            }

            if($this->data['description']){
                $iProp['ELEMENT_META_DESCRIPTION'] = $this->data['description'];
            }

            if($this->data['keys']){
                $iProp['ELEMENT_META_KEYWORDS'] = $this->data['keys'];
            }

            if($iProp) {
                $ipropElementTemplates = new ElementTemplates($this->iBlockId, $id);
                $ipropElementTemplates->set($iProp);
            }
            $element->Update($id, $arElement);

        }

    }

    public function isUpdated()
    {
        return true;
    }

    protected function find()
    {
        $res = CIBlockElement::GetList(
            [],
            ['IBLOCK_ID' => $this->iBlockId, 'PROPERTY_ARTICLE' => $this->data['id']],
            false,
            Array("nPageSize" => 1),
            ['IBLOCK_ID', "ID", "NAME", "DATE_ACTIVE_FROM"]
        );
        if($ob = $res->GetNextElement())
        {
            $arElement = $ob->GetFields();
            return $arElement['ID'];
        }

        return false;
    }
}