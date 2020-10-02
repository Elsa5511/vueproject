<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Sockets\Implementation;

use Ratchet\ConnectionInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

/**
 * Description of WsSocketServerImpl
 *
 * @author Pablo Ramírez <pablor21@gmail.com>
 */
class WsSocketServerImpl extends \App\Sockets\AbstractWsSocketServer {

    protected $usersConnections;
    protected $usersByConnection;
    protected $usersCache;
    protected $users;
    protected $port;

    public function __construct($console, $port) {
        parent::__construct($console);
        $this->port = $port;
        $this->usersConnections = [];
        $this->usersByConnection = [];
        $this->usersCache = [];
        DB::update("UPDATE users SET presence='OFFLINE'");
        $this->subscribeToEventsChannel();
        $this->console->info("Websocket started!");
    }

    public function subscribeToEventsChannel() {
        exec(__DIR__ . '/../../../artisan redis:subscribe --url=ws://localhost:' . $this->port . '  > /dev/null 2>&1 &');
    }

    /**
     * Perform action on open.
     *
     * @param ConnectionInterface $conn [description]
     *
     * @return [type] [description]
     */
    public function onOpen(ConnectionInterface $conn) {
        $user = $this->getUserFromConnection($conn);
        if ($user) {
            if (!isset($this->usersConnections[$user->id])) {
                $this->usersConnections[$user->id] = ['details' => $user->toArray(), 'storage' => new \SplObjectStorage()];
            }
            $this->usersConnections[$user->id]['storage']->attach($conn);
            $this->usersByConnection[$conn->resourceId] = $user->id;

            if ($user->presence != 'ONLINE') {
                $user->presence = 'ONLINE';
                $user->save();
            }

            $this->console->info(sprintf('User %s has connected!', $user->id . '=>' . $user->name));
        }
        $response = $this->getResponseInstance();
        $response->setStatusMessage("Welcome to " . config('app.name') . " socket server!");

        $this->clients->attach($conn);
        $this->console->info(sprintf('Connected: %d', $conn->resourceId));

        $this->displayInfoToConsole($conn);
        $this->sendResponse($conn, $response);
    }

    public function displayInfoToConsole(ConnectionInterface $conn) {

        $this->connections = count($this->clients);
        $this->users = count($this->usersConnections);
        $this->console->info(sprintf('%d %s (%d %s)', $this->connections, str_plural('connection', $this->connections), $this->users, str_plural('user', $this->users)));
    }

    /**
     * Perform action on message.
     *
     * @param ConnectionInterface $conn  [description]
     * @param [type]              $input [description]
     *
     * @return [type] [description]
     */
    public function onMessage(ConnectionInterface $conn, $input) {
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


        if (isset($this->usersByConnection[$conn->resourceId])) {
            $userId = $this->usersByConnection[$conn->resourceId];
            unset($this->usersByConnection[$conn->resourceId]);
            $this->usersConnections[$userId]['storage']->detach($conn);
            $this->console->error(sprintf('Disconnected: %d => %s', $userId, $this->usersConnections[$userId]['details']['name']));

            if (!count($this->usersConnections[$userId]['storage'])) {
                unset($this->usersConnections[$userId]);
                $user = \App\Models\User::find($userId);
                $this->console->info($userId);
                if ($user) {
                    $user->presence = 'OFFLINE';
                    $user->save();
                }
            }
        }
        $this->clients->detach($conn);
        $this->console->error(sprintf('Disconnected: %d', $conn->resourceId));

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
        foreach ($this->clients as $client) {
            $client->send($message);
        }
    }

    public function sendOthers(ConnectionInterface $conn, $message) {
        foreach ($this->clients as $client) {
            if ($conn != $client) {
                $client->send($message);
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

}
