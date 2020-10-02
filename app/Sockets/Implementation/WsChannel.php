<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Sockets\Implementation;

use \Ratchet\ConnectionInterface;
use App\Models\User;

/**
 * Description of WsChannel
 *
 * @author Pablo Ramírez <pablor21@gmail.com>
 */
class WsChannel {

    private $private = false;
    private $name;
    private $connections;
    private $users;
    private $socket;

    public function __construct(\App\Sockets\AbstractWsSocketServer $socket, $name) {
        $this->socket = $socket;
        $this->private = strpos('private-', $name) === 0;
        $this->name = $name;
        $this->connections = [];
        $this->users = [];
    }

    function getPrivate() {
        return $this->private;
    }

    function getName() {
        return $this->name;
    }

    function getSubscribers() {
        return $this->subscribers;
    }

    function getSocket() {
        return $this->socket;
    }

    function setPrivate($private) {
        $this->private = $private;
    }

    function setName($name) {
        $this->name = $name;
    }

    function setSubscribers($subscribers) {
        $this->subscribers = $subscribers;
    }

    function setSocket($socket) {
        $this->socket = $socket;
    }

    function canJoin(User $user = null) {
        return true;
    }

    function subscribe(ConnectionInterface $conn, User $user = null) {
        //prevent duplicated subscriptions
        if (issset($this->connections[$conn->resourceId])) {
            return;
        }

        //check if user can join the channel
        if (!$this->canJoin($user)) {
            return;
        }

        $this->connections[$conn->resourceId] = $conn;
        if ($user) {
            $holder = null;
            if (isset($this->users[$user->id])) {
                $holder = $this->users[$user->id];
            } else {
                $this->users[$user->id] = ['user' => $user, 'connections' => []];
                $this->onJoin($conn, $user);
            }
            $holder['connections'][$conn->resourceId] = $conn;
        } else {
            $this->onJoin($conn);
        }
    }

    function onJoin(ConnectionInterface $conn, User $user = null) {
        $message = new WSMessage();
        $message->setType(WSMessage::TYPES['PRESENCE_JOIN']);
        if ($user) {
            $message->setUserId($user->id);
        }
        $this->broadcastToOthers($conn, $message);
    }

    function onLeave(ConnectionInterface $conn, User $user = null) {
        $message = new WSMessage();
        $message->setType(WSMessage::TYPES['PRESENCE_LEAVE']);
        if ($user) {
            $message->setUserId($user->id);
        }
        $this->broadcastToOthers($conn, $message);
    }

    function presence(ConnectionInterface $conn, $data) {
        $message = new WSMessage();
        $message->setType($data);
        $this->broadcastToOthers($conn, $message);
    }

    function unsubscribe(ConnectionInterface $conn, User $user = null) {
        $this->onLeave($conn);
        $this->connections[$conn->resourceId];
    }

    function onMessage(ConnectionInterface $conn, $data) {
        
    }

    function broadcast(ConnectionInterface $conn, $data) {
        
    }

    function broadcastToOthers(ConnectionInterface $conn, $data) {
        
    }

}
