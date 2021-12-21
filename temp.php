<?php
require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require __DIR__ . '/vendor/autoload.php';


class GoogleSheets
{
    /**
     * @var Google_Service_Sheets
     */
    protected $service;

    protected $spreadsheetId = '1dXJXdQLTFzIqMYEwgHFx3FSh0Aod20kS8KhgTxnmyHQ';

    // список листов в гугле-доке и slug соответствующей компании
    protected $sheets = array(
        'Brother' => 'brother',
        'Canon' => 'canon',
        'Epson' => 'epson',
        'HP' => 'hp',
        'Konica Minolta' => 'konica_minolta',
        'Kyocera Mita' => 'kyocera_mita',
        'Samsung' => 'samsung',
        'Xerox' => 'xerox',
    );

    public function run(): void
    {
        $this->connect();

        foreach ($this->sheets as $sheetName => $slug) {

            $response = $this->service->spreadsheets_values->get(
                $this->spreadsheetId,
                $sheetName
            );

            $values = $response->getValues();


            print_r($values);

        }
    }


    protected function connect(): void
    {
        $client = new Google_Client();
        $client->setAuthConfig('google-api-key.json');
        $client->setScopes(array(Google_Service_Sheets::SPREADSHEETS_READONLY));

        $this->service = new Google_Service_Sheets($client);
    }


}

require ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
(new GoogleSheets())->run();