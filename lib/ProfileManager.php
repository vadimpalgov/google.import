<?php

namespace Bitrix\GoogleImport;

/**
 * Class ProfileManager
 * @package Bitrix\GoogleImport
 */
class ProfileManager
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var integer
     */
    public $document_id;

    /**
     * @var integer
     */
    public $iblock_id;

    /**
     * @var string
     */
    public $profile_class;

    /**
     * @var Sheets
     */
    public $service;

    /**
     * @var array
     */
    protected $_sheet_ids = [];

    /**
     * @var \Google_Service_Sheets[]
     */
    protected $_sheets = [];

    /**
     * @var \Google_Service_Sheets_ValueRange[]
     */
    protected $_sheetsValues;

    /**
     * @var int
     */
    protected $_offset = 0;

    /**
     * @var int
     */
    protected $_limit = 100;

    public function __construct($params, $service)
    {
        $this->name = $params['name'];
        $this->document_id = $params['document_id'];
        $this->iblock_id = $params['iblock_id'];
        $this->profile_class = $params['profile_class'];

        if (!is_array($params['sheet_id'])) {
            $params['sheet_id'] = [$params['sheet_id']];
        }
        $this->_sheet_ids = $params['sheet_id'];

        $this->service = $service;

    }

    /**
     * @return int
     */
    public function count()
    {
        $count = 0;
        foreach ($this->getSheetsValues() as $sheet) {
            $count += $sheet->count();
        }

        return $count;
    }

    /**
     * @return \Google_Service_Sheets[]
     */
    public function getSheets()
    {
        if (!$this->_sheets) {
            $sheets = $this->service->spreadsheets->get($this->document_id);

            foreach ($sheets->getSheets() as $sheet) {
                $this->_sheets[] = $sheet;
                $this->_sheet_ids[] = $sheet->getProperties()->title;
            }
        }

        return $this->_sheets;
    }

    /**
     * @return array
     */
    public function getSheetsIds()
    {
        if (!$this->_sheet_ids) {
            $this->getSheets();
        }

        return $this->_sheet_ids;
    }

    /**
     * @return \Google_Service_Sheets_ValueRange[]
     */
    public function getSheetsValues()
    {
        if (is_null($this->_sheetsValues)) {
            foreach ($this->getSheetsIds() as $sheet_id) {
                $this->_sheetsValues[] = $this->service->spreadsheets_values->get($this->document_id, $sheet_id);
            }
        }

        return $this->_sheetsValues;
    }

    /**
     * @param $data
     * @return Profiles\ProfileInterface
     */
    public function getProfile($data)
    {
        return Profile::factory(
            $this->profile_class,
            $this->iblock_id,
            $data
        );
    }

    public function getValues()
    {
        $values = [];
        $offset = 0;

        foreach ($this->getSheetsValues() as $sheetsValue) {
            foreach ($sheetsValue->getValues() as $value) {
                if ($this->_offset > 0 && $offset < $this->_offset) {
                    $offset++;
                    continue;
                }
                if ($this->_limit > 0 && count($values) >= $this->_limit) {
                    continue;
                }

                $values[] = $value;
            }
        }
        return $values;
    }

    /**
     * @param $offset int
     */
    public function setOffset($offset)
    {
        $this->_offset = (int)$offset;
    }

    /**
     * @param $limit int
     */
    public function setLimit($limit)
    {
        $this->_limit = (int)$limit;
    }
}