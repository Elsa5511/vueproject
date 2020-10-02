<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Lib\Api;

use Validator;

/**
 * Description of JSONResponse
 *
 * @author pramirez
 */
class JSONResponse {

    private $payload;
    private $httpCode = 200;
    private $messages = [];
    private $validationErrors = [];
    private $statusMessage = "OK";
    private $success = true;
    private $notifications = [];
    private $nextAction = '';

    public function __construct($payload = array(), $success = true, $httpCode = 200, $statusMessage = "OK") {
        $this->payload = $payload;
        $this->success = $success;
        $this->httpCode = $httpCode;
        $this->statusMessage = $statusMessage;
    }

    public function addMessage(\App\Lib\Message $message) {
        $this->messages[] = $message->toArray();
    }

    public function addNotification($message, $category = 'GENERAL', $type = 'danger') {
        $this->notifications[] = ['type' => $type, 'category' => $category, 'message' => $message];
    }

    public function setValidator($validator) {
        $this->validationErrors = $validator->messages();
    }

    function getPayload() {
        return $this->payload;
    }

    function getHttpCode() {
        return $this->httpCode;
    }

    function getMessages() {
        return $this->messages;
    }

    function getValidationErrors() {
        return $this->validationErrors;
    }

    function getStatusMessage() {
        return $this->statusMessage;
    }

    function getSuccess() {
        return $this->success;
    }

    function getNotifications() {
        return $this->notifications;
    }

    function setPayload($payload) {
        $this->payload = $payload;
    }

    function setHttpCode($httpCode) {
        $this->httpCode = $httpCode;
    }

    function setMessages($messages) {
        $this->messages = $messages;
    }

    function setValidationErrors($validationErrors) {
        $this->validationErrors = $validationErrors;
    }

    function setStatusMessage($statusMessage) {
        $this->statusMessage = $statusMessage;
    }

    function setSuccess($success) {
        $this->success = $success;
    }

    function setNotifications($notifications) {
        $this->notifications = $notifications;
    }

    function getNextAction() {
        return $this->nextAction;
    }

    function setNextAction($nextAction) {
        $this->nextAction = $nextAction;
    }

    public function toArray() {
        return [
            'code' => $this->httpCode,
            'success' => $this->success,
            'message' => $this->statusMessage,
            'validationErrors' => $this->validationErrors,
            'payload' => $this->payload,
            'messages' => $this->messages,
            'notifications' => $this->notifications,
            'user' => (auth('api')->user()) ? auth('api')->user()->id : null,
            'time' => \Illuminate\Support\Carbon::now(),
            'nextAction' => $this->nextAction,
        ];
    }

    public function render() {
        return response()->json($this->toArray(), $this->httpCode);
    }

}
