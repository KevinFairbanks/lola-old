<?php
defined('BASEPATH') OR exit('No direct script access allowed');


//use \DTS\eBaySDK\Shopping\Services;
//use \DTS\eBaySDK\Shopping\Types;
use \DTS\eBaySDK\Constants;
use \DTS\eBaySDK\Finding\Services;
use \DTS\eBaySDK\Finding\Types;
use \DTS\eBaySDK\Finding\Enums;

class Ebaytest extends CI_Controller {
        
	public function index()
	{
            $service = new Services\ShoppingService();
            $request = new Types\GeteBayTimeRequestType();
            
            $response = $service->geteBayTime($request);
            
            printf("The official eBay time is: %s\n", $response->Timestamp->format('H:i (\G\M\T) \o\n l jS Y'));
            
	}
        
        public function search($keyword){
            $service = new Services\FindingService(['globalId' => Constants\GlobalIds::US]);
            $request = new Types\FindCompletedItemsRequest();
            $request->keywords = urldecode($keyword);
            $response = $service->findCompletedItems($request);
            
            if(isset($response->errorMessage)){
                foreach($response->errorMessage->error as $error){
                    printf(
                            "%s: %s\n\n",
                            $error->severity=== Enums\ErrorSeverity::C_ERROR ? 'Error' : 'Warning',
                            $error->message
                    );
                }
            }
            
            if($response->ack !== 'Failure'){
                echo '<table border=1>';
                foreach($response->searchResult->item as $item){
                    if($item->sellingStatus->sellingState == "EndedWithSales"){
                        echo '<tr><td><img src="'.$item->galleryURL.'"></td>'
                                . '<td><strong>'.$item->itemId.'</strong><br>'.$item->title.'<br><strong>Ended:</strong> '.date('d M Y', strtotime($item->listingInfo->endTime->format('H:i (\G\M\T) \o\n l jS F Y'))).' - <strong>Type:</strong> '.$item->listingInfo->listingType.'</td>'
                                . '<td><strong>$'.number_format($item->sellingStatus->convertedCurrentPrice->value, 2, '.','').'</strong></td></tr>';
                    }
                }
                echo '</table>';
            }
        }
}