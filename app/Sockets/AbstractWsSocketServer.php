<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Sockets;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use GrahamCampbell\Throttle\Facades\Throttle;

/**
 * Description of AbstractWsSocketServer
 *
 * @author Pablo Ramírez <pablor21@gmail.com>
 */
abstract class AbstractWsSocketServer implements MessageComponentInterface {

    /**
     * Clients.
     *
     * @var [type]
     */
    protected $clients;

    /**
     * Console.
     *
     * @var [type]
     */
    protected $console;

    /**
     * Total connections.
     *
     * @var [type]
     */
    protected $connections;

    /**
     * Current connection.
     *
     * @var ConnectionInterface
     */
    protected $conn;

    /**
     * Throttled.
     *
     * @var [type]
     */
    protected $throttled = false;

    /**
     * Set clients and console.
     *
     * @param [type] $console [description]
     */
    public function __construct($console) {
        $this->clients = new \SplObjectStorage();
        $this->console = $console;
    }

    /**
     * Perform action on open.
     *
     * @param ConnectionInterface $conn [description]
     *
     * @return [type] [description]
     */
    public function onOpen(ConnectionInterface $conn) {
        $this->conn = $conn;
        $this->attach()->throttle()->limit();
    }

    protected function attach() {
        $this->clients->attach($this->conn);
        $this->console->info(sprintf('Connected: %d', $this->conn->resourceId));
        $this->connections = count($this->clients);
        $this->console->info(sprintf('%d %s', $this->connections, str_plural('connection', $this->connections)));
        return $this;
    }

    /**
     * Throttle connections.
     *
     * @return [type] [description]
     */
    protected function throttle() {
        if ($this->isThrottled($this->conn, 'onOpen')) {
            $this->console->info(sprintf('Connection throttled: %d', $this->conn->resourceId));
            $this->conn->send(trans('ratchet::messages.tooManyConnectionAttempts'));
            $this->throttled = true;
            $this->conn->close();
        }
        return $this;
    }

    /**
     * Limit connections.
     *
     * @return [type] [description]
     */
    protected function limit() {
        if ($connectionLimit = config('ratchet.connectionLimit') && $this->connections - 1 >= $connectionLimit) {
            $this->console->info(sprintf('To many connections: %d of %d', $this->connections - 1, $connectionLimit));
            $this->conn->send(trans('ratchet::messages.tooManyConnections'));
            $this->conn->close();
        }
        return $this;
    }

    /**
     * Check if the called function is throttled.
     *
     * @param [type] $conn    [description]
     * @param [type] $setting [description]
     *
     * @return bool [description]
     */
    protected function isThrottled($conn, $setting) {
        $connectionThrottle = explode(':', config(sprintf('ratchet.throttle.%s', $setting)));
        return false;
        return !Throttle::attempt(
                        [
                    'ip' => $conn->remoteAddress,
                    'route' => $setting,
                        ], (int) $connectionThrottle[0], (int) $connectionThrottle[1]
        );
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
        if ($this->isThrottled($conn, 'onMessage')) {
            $this->console->info(sprintf('Message throttled: %d', $conn->resourceId));
            $this->send($conn, trans('ratchet::messages.tooManyMessages'));
            $this->throttled = true;
            if (config('ratchet.abortOnMessageThrottle')) {
                $this->abort($conn);
            }
        }
    }

    /**
     * Perform action on close.
     *
     * @param ConnectionInterface $conn [description]
     *
     * @return [type] [description]
     */
    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        $this->console->error(sprintf('Disconnected: %d', $conn->resourceId));
        $this->connections = count($this->clients);
        $this->console->info(sprintf('%d %s', $this->connections, str_plural('connection', $this->connections)));
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
        $this->clients->detach($conn);
        $conn->close();

        $this->connections = count($this->clients);
        $this->console->info(sprintf('%d %s', $this->connections, str_plural('connection', $this->connections)));
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

}
