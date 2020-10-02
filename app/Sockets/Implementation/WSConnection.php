<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Sockets\Implementation;

/**
 * Description of WSConnection
 *
 * @author Pablo Ramírez <pablor21@gmail.com>
 */
class WSConnection {

    private $conn;
    private $user;

    function __construct($conn, $user) {
        $this->conn = $conn;
        $this->user = $user;
    }

    function getConn() {
        return $this->conn;
    }

    function getUser() {
        return $this->user;
    }

    function setConn($conn) {
        $this->conn = $conn;
    }

    function setUser($user) {
        $this->user = $user;
    }

}
