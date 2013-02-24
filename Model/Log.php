<?php

namespace Lexik\Bundle\LexikMonologDoctrineBundle\Model;

/**
 * @author Jeremy Barthe <j.barthe@lexik.fr>
 */
class Log
{
    protected $id;
    protected $channel;
    protected $level;
    protected $levelName;
    protected $message;
    protected $date;
    protected $context;
    protected $extra;
    protected $serverData;
    protected $postData;
    protected $getData;

    public function __construct(array $data)
    {
        if (!isset($data['id'])) {
            throw new \InvalidArgumentException();
        }

        $this->id         = $data['id'];
        $this->channel    = $data['channel'];
        $this->level      = $data['level'];
        $this->levelName  = $data['level_name'];
        $this->message    = $data['message'];
        $this->date       = new \DateTime($data['datetime']);
        $this->context    = isset($data['context']) ? json_decode($data['context'], true) : array();
        $this->extra      = isset($data['extra'])   ? json_decode($data['extra'], true)   : array();
        $this->serverData = isset($data['server'])  ? json_decode($data['server'], true)  : array();
        $this->postData   = isset($data['post'])    ? json_decode($data['post'], true)    : array();
        $this->getData    = isset($data['get'])     ? json_decode($data['get'], true)     : array();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getChannel()
    {
        return $this->channel;
    }

    public function getLevel()
    {
        return $this->level;
    }

    public function getLevelName()
    {
        return $this->levelName;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function getContext()
    {
        return $this->context;
    }

    public function getExtra()
    {
        return $this->extra;
    }

    public function getServerData()
    {
        return $this->serverData;
    }

    public function getPostData()
    {
        return $this->postData;
    }

    public function getGetData()
    {
        return $this->getData;
    }
}
