<?php

namespace nsqphp\Connection;

use nsqphp\Exception\ConnectionException;
use nsqphp\Exception\SocketException;

/**
 * Represents a pool of connections to one or more NSQD servers
 */
class ConnectionPool implements \Iterator, \Countable, \ArrayAccess
{
    /**
     * Connections
     * 
     * @var array [] = ConnectionInterface $connection
     */
    private $connections = array();

    /**
     * Add connection
     * 
     * @param ConnectionInterface $connection
     */
    public function add(ConnectionInterface $connection)
    {
        $this->connections[] = $connection;
    }
    
    /**
     * Test if has connection
     * 
     * Remember that the sockets are lazy-initialised so we can create
     * connection instances to test with without incurring a socket connection.
     * 
     * @param ConnectionInterface $connection
     * 
     * @return boolean
     */
    public function hasConnection(ConnectionInterface $connection)
    {
        return $this->find($connection->getSocket()) ? TRUE : FALSE;
    }
    
    /**
     * Find connection from socket/host
     * 
     * @param Resource|string $socketOrHost
     * 
     * @return ConnectionInterface|NULL Will return NULL if not found
     */
    public function find($socketOrHost)
    {
        foreach ($this->connections as $conn) {
            if (is_string($socketOrHost) && (string)$conn === $socketOrHost) {
                return $conn;
            } elseif ($conn->getSocket() === $socketOrHost) {
                return $conn;
            }
        }
        return NULL;
    }
    
    /**
     * @inheritdoc
     */
    public function key()
    {
        return key($this->connections);
    }

    /**
     * @inheritdoc
     */
    public function valid()
    {
        return (current($this->connections) === FALSE) ? FALSE : TRUE;
    }

    /**
     * @inheritdoc
     */
    public function current()
    {
        return current($this->connections);
    }

    /**
     * @inheritdoc
     */
    public function next()
    {
        next($this->connections);
    }

    /**
     * @inheritdoc
     */
    public function rewind()
    {
        reset($this->connections);
    }

    /**
     * Move to end
     */
    public function end()
    {
        end($this->connections);
    }

    /**
     * @inheritdoc
     */
    public function count()
    {
        return count($this->connections);
    }
    
    /**
     * Shuffle connections
     */
    public function shuffle()
    {
        shuffle($this->connections);
    }

    /**
     * @inheritdoc
     */
    public function offsetSet($offset, $value)
    {
        if (null === $offset) {
            $this->connections[] = $value;
        } else {
            $this->connections[$offset] = $value;
        }
    }

    /**
     * @inheritdoc
     */
    public function offsetExists($offset)
    {
        return isset($this->connections[$offset]);
    }

    /**
     * @inheritdoc
     */
    public function offsetUnset($offset)
    {
        unset($this->connections[$offset]);
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($offset)
    {
        return isset($this->connections[$offset]) ? $this->connections[$offset] : null;
    }
}