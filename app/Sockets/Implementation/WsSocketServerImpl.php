<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Sockets\Implementation;

use Ratchet\ConnectionInterface;
use Illuminate\Support\Facades\DB;

/**
 * Description of WsSocketServerImpl
 *
 * @author Pablo Ramírez <pablor21@gmail.com>
 */
class WsSocketServerImpl extends \App\Sockets\AbstractWsSocketServer {

    protected $connections;
    protected $notificators;
    protected $users;
    protected $port;
    protected $channels;

    public function __construct($console, $port) {
        parent::__construct($console);
        $this->port = $port;
        $this->connections = [];
        $this->notificators = [];
        $this->users = [];
        $this->channels = [];
        DB::update("UPDATE users SET presence='OFFLINE'");
        $this->subscribeToEventsChannel();
        $this->console->info("Websocket started!");
    }

    public function subscribeToEventsChannel() {
        $url = 'ws://localhost:' . $this->port . '?token=' . config('app.key');
        $this->console->info($url);
        exec(__DIR__ . '/../../../artisan redis:subscribe --url=' . $url . '  > ./storage/logs/websockets/notificator.log &');
    }

    /**
     * Perform action on open.
     *
     * @param ConnectionInterface $conn [description]
     *
     * @return [type] [description]
     */
    public function onOpen(ConnectionInterface $conn) {

        $this->connections[$conn->resourceId] = ['conn' => $conn, 'user' => null, 'subscriptions' => []];

        if ($this->isNotificator($conn)) {
            $this->console->info('Notificator connected!');
            $this->notificators[$conn->resourceId] = $conn;
        } else {
            $user = $this->getUserFromConnection($conn);
            if ($user) {
                if (!isset($this->users[$user->id])) {
                    $this->users[$user->id] = ['user' => $user->toArray(), 'connections' => []];
                }
                $this->users[$user->id]['connections'][$conn->resourceId] = $conn;
                $this->connections[$conn->resourceId]['user'] = $user->id;
                if ($user->presence != 'ONLINE') {
                    $user->presence = 'ONLINE';
                    $user->save();
                }
                $this->console->info(sprintf('User %s has connected!', $user->id . '=>' . $user->name));
            }
            $response = $this->getResponseInstance();
            $response->setStatusMessage("Welcome to " . config('app.name') . " socket server!");

            $this->sendResponse($conn, $response);
        }

        $this->console->info(sprintf('Connected: %d', $conn->resourceId));

        $this->displayInfoToConsole($conn);
    }

    public function displayInfoToConsole(ConnectionInterface $conn) {

        $connections = count($this->connections);
        $users = count($this->users);
        $this->console->info(sprintf('%d %s (%d %s)', $connections, str_plural('connection', $connections), $users, str_plural('user', $users)));
    }

    public function proccessNotification($input) {
        $message = json_decode($input, true);
        $this->console->info('Sending notification to ' . $message['payload']['data']['notifiable']['name']);
    }

    /**
     * 
     * @param type $channelId
     * @param type $create
     * @return WsChannel
     */
    public function getChannel($channelId, $create = false) {
        $channel = null;
        if (!isset($this->channels[$channelId])) {
            if ($create) {
                $channel = new WsChannel($this, $channelId);
            }
        } else {
            $channel = $this->channels[$channelId];
        }
        return $channel;
    }

    public function getUserOfConnection(ConnectionInterface $conn) {
        return isset($this->connections[$conn->resourceId]['user']) && $this->connections[$conn->resourceId]['user'] != null ? $this->users[$this->connections[$conn->resourceId]['user']] : null;
    }

    public function joinChannel(ConnectionInterface $conn, $request) {
        $channelId = $request['channel'];
        $channel = $this->getChannel($channelId, true);
        $user = $this->getUserFromConnection($conn);
        if ($channel->canJoin($user)) {
            $channel->join($conn, $user);
        } else {
            unset($this->channels[$channelId]);
        }
        return null;
    }

    public function proccessMessage(ConnectionInterface $conn, $input) {
        $response = $this->getResponseInstance();
        $responseData = [];
        try {
            $request = json_decode($input, true);
            $responseData = ['input' => $request];

            $command = $request['command'];
            switch ($command) {
                case 'JOIN':
                    $this->joinChannel($conn, $request);
                    break;
            }
        } catch (\Exception $ex) {
            $response->setHttpCode(400);
            $response->setStatusMessage("We aren't speaking the same language! :(");
        } finally {
            $response->setPayload($responseData);
        }

        $this->send($conn, $response);
    }

    /**
     * Perform action on message.
     *
     * @param ConnectionInterface $conn  [description]
     * @param [type] $input [description]
     *
     * @return [type] [description]
     */
    public function onMessage(ConnectionInterface $conn, $input) {
        if (isset($this->notificators[$conn->resourceId])) {
            $this->proccessNotification($input);
            return;
        }
        $this->proccessMessage($conn, $input);


        $this->console->comment(sprintf('Message from %d: %s', $conn->resourceId, $input));

        $response = $this->getResponseInstance();
        $dataResponse = ['original' => $input];
        try {
            $jsonMessageRequest = json_decode($input, true);
            $this->sendOthers($conn, $input);
        } catch (\Exception $ex) {
            $response->setHttpCode(400);
            $response->setStatusMessage("We aren't speaking the same language! :(");
        } finally {
            $response->setPayload($dataResponse);
        }

        //response back to user
        $this->sendResponse($conn, $response);
    }

    /**
     * Perform action on close.
     *
     * @param ConnectionInterface $conn [description]
     *
     * @return [type] [description]
     */
    public function onClose(ConnectionInterface $conn) {
        $this->detachConnection($conn);
    }

    public function detachConnection(ConnectionInterface $conn) {

        $saved = $this->connections[$conn->resourceId];
        if ($saved['user']) {
            $userId = $saved['user'];
            unset($this->users[$userId]['connections'][$conn->resourceId]);
            $this->console->error(sprintf('Disconnected: %d => %s', $userId, $this->users[$userId]['user']['name']));
            if (!count($this->users[$userId]['connections'])) {
                unset($this->users[$userId]);
                $user = \App\Models\User::find($userId);
                if ($user) {
                    $user->presence = 'OFFLINE';
                    $user->save();
                    $this->console->error('The user is offline now!');
                }
            } else {
                $this->console->error(sprintf('The user has: %d %s', count($this->users[$userId]['connections']), str_plural('connection', count($this->users[$userId]['connections']))));
            }
        } elseif (isset($this->notificators[$conn->resourceId])) {
            $this->console->error('Notificator disconnected!');
            unset($this->notificators[$conn->resourceId]);
        }
        $this->console->error(sprintf('Disconnected: %d', $conn->resourceId));

        unset($this->connections[$conn->resourceId]);
        $this->displayInfoToConsole($conn);
    }

    /**
     * Perform action on error.
     *
     * @param ConnectionInterface $conn      [description]
     * @param Exception           $exception [description]
     *
     * @return [type] [description]
     */
    public function onError(ConnectionInterface $conn, \Exception $exception) {
        $message = $exception->getMessage();
        $conn->close();
        $this->console->error(sprintf('Error: %s', $message));
    }

    /**
     * Close the current connection.
     *
     * @return [type] [description]
     */
    public function abort(ConnectionInterface $conn) {
        $this->detachConnection($conn);
        $conn->close();
    }

    /**
     * Send a message to the current connection.
     *
     * @param [type] $message [description]
     *
     * @return [type] [description]
     */
    public function send(ConnectionInterface $conn, $message) {
        $conn->send($message);
    }

    /**
     * Send a message to all connections.
     *
     * @param [type] $message [description]
     *
     * @return [type] [description]
     */
    public function sendAll($message) {
        foreach ($this->connections as $id => $client) {
            $client['conn']->send($message);
        }
    }

    public function sendOthers(ConnectionInterface $conn, $message) {
        foreach ($this->connections as $id => $client) {
            if ($conn->resourceId != $id) {
                $client['conn']->send($message);
            }
        }
    }

    public function getResponseInstance(ConnectionInterface $conn = null) {
        return new WSResponseMessage();
    }

    public function sendResponse(ConnectionInterface $conn, WSResponseMessage $response) {
        $this->send($conn, json_encode($response->toArray()));
    }

    public function getUserFromConnection(ConnectionInterface $conn) {
        parse_str($conn->httpRequest->getUri()->getQuery(), $query);
        $info = "";
        if (isset($query['token'])) {
            $token = $query['token'];
            $payload = auth('api')->setToken($token)->getPayload();
            $userToken = \App\Models\UserToken::where(['token' => $token])->firstOrFail();
            return $userToken->user;
        }
    }

    public function isNotificator(ConnectionInterface $conn) {
        parse_str($conn->httpRequest->getUri()->getQuery(), $query);
        $info = "";
        if (isset($query['token'])) {
            $token = $query['token'];
            return $token == config('app.key');
        }
        return false;
    }

}
