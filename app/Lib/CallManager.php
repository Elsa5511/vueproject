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
class CallManager {

    /**
     *
     * @var \Twilio\Rest\Api\V2010\Account\MessageInstance
     */
    private $client;
    private static $_INSTANCE = null;

    private function __construct() {
        $this->client = new Client(config()->get('twilio.account'), config()->get('twilio.token'));
    }

    /**
     * 
     * @return CallManager
     */
    public static function getInstance() {
        if (self::$_INSTANCE == null) {
            self::$_INSTANCE = new self();
        }

        return self::$_INSTANCE;
    }

    /**
     * 
     * @param type $from
     * @param type $to
     * @return \Twilio\Rest\Api\V2010\Account\CallInstance
     */
    public function makeCall($from, $to) {
        $twilioNumber = config()->get('twilio.from');
        try {
            // Initiate a new outbound call
            $call = $this->client->account->calls->create(
                    $from, // connect this number(Agent)
                    // that you've purchased or verified with Twilio.
                    $twilioNumber, // caller id for call
                    // Set the URL Twilio will request when the call is answered.
                    array("url" => url(config('app.url') . '/api/v1/callcenter/dialto.xml?to=' . $to))
            );
            return $call;
        } catch (Exception $e) {
            throw $e;
        }
    }

}
