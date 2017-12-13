<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
use \DTS\eBaySDK\Shopping\Services;
use \DTS\eBaySDK\Shopping\Types;

class Main extends CI_Controller {
 
    function __construct(){
        parent::__construct();

        $this->load->database();
        
    }

    public function index(){
        
        $this->load->template('body');
        
        $service = new Services\ShoppingService();
        $request = new Types\GetCategoryInfoRequestType();
        
        $request->CategoryID = '-1';
        $request->IncludeSelector = 'ChildCategories';

        $response = $service->getCategoryInfo($request);
        
        if (isset($response->Errors)) {
            foreach ($response->Errors as $error) {
                printf(
                    "%s: %s\n%s\n\n",
                    $error->SeverityCode === DTS\eBaySDK\Shopping\Enums\SeverityCodeType::C_ERROR ? 'Error' : 'Warning',
                    $error->ShortMessage,
                    $error->LongMessage
                );
            }
        }
        if ($response->Ack !== 'Failure') {
            foreach ($response->CategoryArray->Category as $category) {
                printf(
                    "Category (%s) %s\n",
                    $category->CategoryID,
                    $category->CategoryName
                );
            }
        }
        
    }
}
 
/* End of file Main.php */
/* Location: ./application/controllers/Main.php */