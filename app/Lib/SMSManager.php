<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Lib;

use Twilio\Rest\Client;

/**
 * Description of SMSManager
 *
 * @author Pablo Ramírez <pablor21@gmail.com>
 */
class SMSManager {
    /**
     *
     * @var \Twilio\Rest\Api\V2010\Account\MessageInstance
     */
    private $client;
    
    private static $_INSTANCE=null;
    
    private function __construct() {
        $this->client=new Client(config()->get('twilio.account'), config()->get('twilio.token'));
    }
    
    /**
     * 
     * @return SMSManager
     */
    public static function getInstance(){
        if(self::$_INSTANCE==null){
            self::$_INSTANCE=new self();
        }
        
        return self::$_INSTANCE;
    }
    
    /**
     * 
     * @param type $to
     * @param type $text
     * @return \Twilio\Rest\Api\V2010\Account\MessageInstancea
     */
    public function sendSMS($to, $text){       
       $response= $this->client->messages->create($to, ['from'=>config()->get('twilio.from'), 'body'=>$text]);
       $arrResponse= $response->toArray();
       if($arrResponse['errorMessage']){
           throw new \Exception($arrResponse['errorMessage']);
       }
       return $arrResponse;
    }
}
