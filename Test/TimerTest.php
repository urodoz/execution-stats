<?php

namespace Urodoz\TimeExecutionStatsBundle\Test;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Urodoz\TimeExecutionStatsBundle\Test\MockImplementation\MockedImplementation;

class TimerTest extends WebTestCase
{

    public function testStoredDataOnImplementation()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $em = $container->get("doctrine")->getEntityManager();
        if(!$container->has("urodoz.timeTracker")) $this->markTestSkipped("Bundle [TimeExecutionStatsBundle] not enabled");

        $mock = new MockedImplementation();

        //Replacing service of store with mocked
        $container->set("urodoz.timeTracker.storageImplementation", $mock);

        $task = uniqid();
        $group = "test";
        $version = uniqid();

        //Start tracking
        $container->get("urodoz.timeTracker")->start($task, $group, $version);
        $container->get("urodoz.timeTracker")->stop($task);
        //End tracking

        $this->assertEquals($mock->name, $task);
        $this->assertEquals($mock->tag, "test");
        $this->assertTrue(is_float($mock->time));
        $this->assertEquals($mock->version, $version);
    }

}
