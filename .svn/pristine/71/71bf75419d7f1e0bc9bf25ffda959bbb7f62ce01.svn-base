<?php
/**
 * @copyright Copyright (c) 2016
 * @version   Beta 1.0
 * @author    kevin
 */

namespace Transport\Service;

/**
 * redis 连接服务
 * Class Redis
 * @package OrderTask\Vendor
 */
class Redis
{
    /**
     * @var bool client
     */
    public $client = false;

    /**
     * @var array redis options
     */
    private $options = [];

    /**
     * Redis constructor.
     * @param $options
     * @param $pconnect
     * @throws \Exception
     */
    public function __construct($options, $pconnect = false)
    {
        $this->options = $options;
        $this->client($options);
        return $this->client;
    }

    /**
     * 是否长连接
     * @param bool $pconnect
     * @return bool|\Redis
     * @throws \Exception
     */
    public function client($pconnect = true)
    {
        if ($this->client) {
            return $this->client;
        }
        // 未安装扩展
        if (!in_array('redis', get_loaded_extensions())) {
            throw new \Exception('Redis extension isn\'t installed.', 500);
        }

        try {
            $redis = new \Redis();
            if ($pconnect) {
                $redis->pconnect($this->options['host'], $this->options['port'], 0, "x");
            } else {
                $redis->connect($this->options['host'], $this->options['port'], 0, "x");
            }
            $redis->select($this->options['database']);
            $redis->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_PHP);
            $this->client = $redis;
            return $this->client;

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @return bool
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * select db
     * @param $db
     * @return bool
     */
    public function select($db)
    {
        if (!$this->client) {
            return false;
        }
        $this->client->select($db);
        return $this->client;
    }
}