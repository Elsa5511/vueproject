<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Models;

/**
 * Description of Notification
 *
 * @author pramirez
 */
class Notification {

    private $action = null;
    private $sender_id = 'null';
    private $title = "";
    private $message = "";
    private $type = 'success';
    private $sound = null;
    private $attributes = [];
    private $icon;
    private $openInNewWindow = false;
    private $destType = 'all';

    public function __construct($title = '') {
        $this->title = $title;
    }

    function getAction() {
        return $this->action;
    }

    function getMessage() {
        return $this->message;
    }

    function getSenderId() {
        return $this->sender_id;
    }

    function getType() {
        return $this->type;
    }

    function getIcon($includeDefaultIfNotExists = false) {
        if (!$this->icon && $includeDefaultIfNotExists) {
            switch ($this->getType()) {
                case 'success':
                    $this->icon = 'check-circle';
                    break;
                case 'info':
                    $this->icon = 'info';
                    break;
                case 'warning':
                    $this->icon = 'exclamation-triangle';
                    break;
                case 'danger':
                case 'error':
                    $this->icon = 'times';
                    break;
            }
        }
        return $this->icon;
    }

    function getSound($includeDefaultIfNotExists = false) {
        if (!$this->sound && $includeDefaultIfNotExists) {
            switch ($this->getType()) {
                default:
                    $this->sound = 'notification';
                    break;
            }
        }
        return $this->sound;
    }

    function setAction($action) {
        $this->action = $action;
    }

    function setSenderId($sender_id) {
        $this->sender_id = $sender_id;
    }

    function setMessage($message) {
        $this->message = $message;
    }

    function setType($type) {
        $this->type = $type;
    }

    function setIcon($icon) {
        $this->icon = $icon;
    }

    function getTitle() {
        return $this->title;
    }

    function setTitle($title) {
        $this->title = $title;
    }

    function getAttributes() {
        return $this->attributes;
    }

    function setAttributes($attributes) {
        $this->attributes = $attributes;
    }

    function addAttribute($key, $value) {
        $this->attributes[$key] = $value;
    }

    function getOpenInNewWindow() {
        return $this->openInNewWindow;
    }

    function setOpenInNewWindow($openInNewWindow) {
        $this->openInNewWindow = $openInNewWindow;
    }

    function setSound($sound) {
        $this->sound = $sound;
    }

    function getDestType() {
        return $this->destType;
    }

    function setDestType($destType) {
        $this->destType = $destType;
    }

    public function toArray() {
        return [
            'action' => $this->getAction(),
            'sender_id' => $this->getSenderId(),
            'message' => $this->getMessage(),
            'title' => $this->getTitle(),
            'icon' => $this->getIcon(true),
            'sound' => $this->getSound(true),
            'type' => $this->getType(),
            'attributes' => $this->getAttributes(),
            'destType' => $this->destType
        ];
    }

}
