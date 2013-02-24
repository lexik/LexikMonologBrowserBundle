<?php

namespace Lexik\Bundle\LexikMonologDoctrineBundle\Processor;

class WebExtendedProcessor
{
    /**
     * @var array
     */
    protected $serverData;

    /**
     * @var array
     */
    protected $postData;

    /**
     * @var array
     */
    protected $getData;

    /**
     * @param array $serverData
     * @param array $postData
     * @param array $getData
     */
    public function __construct(array $serverData = array(), array $postData = array(), array $getData = array())
    {
        $this->serverData = $serverData ?: $_SERVER;
        $this->postData   = $postData   ?: $_POST;
        $this->getData    = $getData    ?: $_GET;
    }

    /**
     * @param  array $record
     * @return array
     */
    public function __invoke(array $record)
    {
        // skip processing if for some reason request data
        // is not present (CLI or wonky SAPIs)
        if (!isset($this->serverData['REQUEST_URI'])) {
            return $record;
        }

        $record['server'] = $this->serverData;
        $record['post']   = $this->postData;
        $record['get']    = $this->getData;

        return $record;
    }
}
