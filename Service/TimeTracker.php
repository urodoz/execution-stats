<?php

namespace Urodoz\TimeExecutionStatsBundle\Service;

use Urodoz\TimeExecutionStatsBundle\Lib\StoreImplementationInterface;

/**
 * Holds the public functions to spawn time elapse counters
 * and store the results on database
 *
 * @author Albert Lacarta <urodoz@gmail.com>
 */
class TimeTracker
{

    /**
     * @var array
     */
    private $timersBag;

    /**
     * @var StoreImplementationInterface
     */
    private $storeInterface;

    /**
     * @param StoreImplementationInterface
     */
    public function __construct(StoreImplementationInterface $storeInterface)
    {
        $this->storeInterface = $storeInterface;
    }

    /**
     * Returns the microtime based on the
     * server microtime
     */
    private function getMicrotime()
    {
        $mtime = microtime();
        $mtime = explode(" ",$mtime);
        $mtime = $mtime[1] + $mtime[0];

        return $mtime;
    }

    /**
     * Start the tracking of a name service
     *
     * @param string $name
     * @param string $tag
     * @param string $version
     */
    public function start($name, $tag=null, $version=null)
    {
        if(!isset($this->timersBag[$name])) $this->timersBag[$name] = array();

        $this->timersBag[$name][] = array(
            "time" => $this->getMicrotime(),
            "tag" => $tag,
            "version" => $version,
        );
    }

    /**
     * Stop the tracking and stores the results
     *
     * @param string $name
     */
    public function stop($name)
    {
        if ((isset($this->timersBag[$name])) && (isset($this->timersBag[$name][0]))) {
            $ds = array_shift($this->timersBag[$name]);
            $ds["time"] = ($this->getMicrotime() - $ds["time"])*1000;
            $this->storeInterface->storeLog($ds["time"], $name, $ds["tag"], $ds["version"]);
        }

    }

}
