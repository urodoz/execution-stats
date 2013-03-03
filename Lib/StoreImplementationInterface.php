<?php

namespace Urodoz\TimeExecutionStatsBundle\Lib;

/**
 * Handles the storage action of logged data
 *
 * @author Albert Lacarta <urodoz@gmail.com>
 */
interface StoreImplementationInterface
{

    /**
     *
     * @param float  $time    Time in ms
     * @param string $name    Name of the key to track time
     * @param string $tag     Tag of the key (to be grouped)
     * @param string $version Version of the key
     */
    public function storeLog($time, $name, $tag, $version);

}
