<?php
/**
 * Created by PhpStorm.
 * User: Vadim Palgov
 */

namespace Bitrix\GoogleImport\Profiles;

use Bitrix\Iblock\ElementTable;
use CIBlockElement;
use Bitrix\Main\Type\DateTime;

/**
 * Class Review
 * @package Bitrix\GoogleImport\Profiles
 */
class Review implements ProfileInterface
{
    /**
     * @var integer
     */
    protected $iBlockId;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var array
     */
    protected $dataMap = [
        'urlCartridge',
        'name',
        'rating',
        'text',
        'date_review',
        'time_review',
        'date_create',
    ];

    public function __construct($iBlockId, $data)
    {
        $this->iBlockId = $iBlockId;

        foreach ($this->dataMap as $i=>$key) {
            $this->data[$key] = $data[$i];
        }
    }

    public function run()
    {
        if($this->data['name'] == 'Имя') return;

        if(!$this->data['date_review']) return;

        // Получаем id принтера по URL
        $printerId = $this->findPrinter();

        // Создаем массив для отзыва
        $PROP = array();
        $PROP['PRODUCT'] = $printerId;
        $PROP['REVIEWER'] = $this->data['name'];
        $PROP['EVALUATION'] = $this->data['rating']+8;

        $arElement = Array(
            'IBLOCK_SECTION_ID' => false,
            'IBLOCK_ID' => $this->iBlockId,
            'PROPERTY_VALUES' => $PROP,
            'NAME' => $this->data['name'] . ' ' . $this->data['date_review'],
            'CODE' => $this->getCode(),
            'ACTIVE' => 'Y',
            'DATE_CREATE' => DateTime::createFromPhp(new \DateTime($this->data['date_review'].' '.$this->data['time_review'])),
            'PREVIEW_TEXT' => $this->data['text'],
        );

        // Ищем отзыв в базе
        $elementId = $this->find();

        $element = new CIBlockElement;
        if($elementId){
            $element->Update($elementId, $arElement);
        } else {
            $element->Add($arElement);
        }
    }

    /**
     * @param $url
     * @return bool
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    protected function findPrinter()
    {
        preg_match('/http[s]{0,1}:\/\/[w]{0,3}[\.]{0,1}tmshop.ru\/cartridge\/([^\/]+)\/([^\/]+)/', $this->data['urlCartridge'], $output_array);
        array_shift($output_array);

        $code = $output_array[1];

        $res = ElementTable::getList([
           'select' => ['ID'],
           'filter' => [
               'IBLOCK_ID' => 17,
               'CODE' => $code
           ]
        ]);

        if($arElement = $res->fetch())
        {
            return $arElement['ID'];
        }

        return false;
    }

    protected function find()
    {
        $code = $this->getCode();

        $res = ElementTable::getList([
            'select' => ['ID', 'NAME'],
            'filter' => [
                'IBLOCK_ID' => $this->iBlockId,
                'CODE' => $code
            ]
        ]);

        if($arElement = $res->fetch())
        {
            return $arElement['ID'];
        }

        return false;
    }

    /**
     * @return string
     */
    protected function getCode()
    {
        return md5($this->data['urlCartridge'].$this->data['name']);
    }

    /**
     * @return bool
     */
    public function isUpdated()
    {
        return true;
    }
}