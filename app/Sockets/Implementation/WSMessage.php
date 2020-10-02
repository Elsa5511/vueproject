<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Sockets\Implementation;

/**
 * Description of WSMessage
 *
 * @author Pablo Ramírez <pablor21@gmail.com>
 */
class WSMessage {

    const TYPES = [
        'PRESENCE_LEAVE',
        'PRESENCE_JOINED',
        'PRESENCE_TYPING',
        'PRESENCE_UNTYPING',
        'MESSAGE_SENT',
    ];

    private $id;
    private $userId;
    private $type;
    private $data;

    public function __construct($userId = null, $type = null, $userId = null, $data = []) {
        $this->id = \Ramsey\Uuid\Uuid::uuid4();
        $this->userId = $userId;
        $this->type = $type;
        $this->data = $data;
    }

    function getUserId() {
        return $this->userId;
    }

    function setUserId($userId) {
        $this->userId = $userId;
    }

    function getId() {
        return $this->id;
    }

    function getType() {
        return $this->type;
    }

    function getData() {
        return $this->data;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setType($type) {
        $this->type = $type;
    }

    function setData($data) {
        $this->data = $data;
    }

}
