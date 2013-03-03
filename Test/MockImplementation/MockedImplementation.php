<?php

namespace Urodoz\TimeExecutionStatsBundle\Test\MockImplementation;

use Urodoz\TimeExecutionStatsBundle\Lib\StoreImplementationInterface;

class MockedImplementation implements StoreImplementationInterface
{

    public $time;

    public $name;

    public $tag;

    public $version;

    public function storeLog($time, $name, $tag, $version)
    {
        $this->time = $time;
        $this->name = $name;
        $this->tag = $tag;
        $this->version = $version;
    }

}
