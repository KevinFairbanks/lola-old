<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
use \DTS\eBaySDK\Constants;
use \DTS\eBaySDK\Trading\Services;
use \DTS\eBaySDK\Trading\Types;
use \DTS\eBaySDK\Trading\Enums;

class Main extends CI_Controller {
 
    function __construct(){
        parent::__construct();

        $this->load->database();
        
    }

    public function index(){
        
        $this->load->template('body');
        
        $service = new Services\TradingService([
            'credentials' => [
                'devId'  => EBAY_SDK_DEV_ID,
                'appId'  => EBAY_SDK_APP_ID,
                'certId' => EBAY_SDK_CERT_ID
            ],
            'siteId' => Constants\SiteIds::US
        ]);
        $request = new Types\GetCategoriesRequestType();
        
        $request->DetailLevel = ['ReturnAll'];
        $request->OutputSelector = [
            'CategoryArray.Category.CategoryID',
            'CategoryArray.Category.CategoryParentID',
            'CategoryArray.Category.CategoryLevel',
            'CategoryArray.Category.CategoryName'
        ];

        $response = $service->getCategories($request);
        
        if (isset($response->Errors)) {
            foreach ($response->Errors as $error) {
                printf(
                    "%s: %s\n%s\n\n",
                    $error->SeverityCode === Enums\SeverityCodeType::C_ERROR ? 'Error' : 'Warning',
                    $error->ShortMessage,
                    $error->LongMessage
                );
            }
        }
        if ($response->Ack !== 'Failure') {
            foreach ($response->CategoryArray->Category as $category) {
                printf(
                    "Level %s : %s (%s) : Parent %s\n",
                    $category->CategoryLevel,
                    $category->CategoryName,
                    $category->CategoryID,
                    $category->CategoryParentID[0]
                );
            }
        }
        
    }
}
 
/* End of file Main.php */
/* Location: ./application/controllers/Main.php */