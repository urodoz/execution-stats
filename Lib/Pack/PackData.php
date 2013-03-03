<?php

namespace Urodoz\TimeExecutionStatsBundle\Lib\Pack;

use Urodoz\TimeExecutionStatsBundle\Exception\ActionNotPerformedException;
use Urodoz\TimeExecutionStatsBundle\Entity\Log;

/**
 * @author Albert Lacarta <urodoz@gmail.com>
 */
class PackData
{

    /**
     * The max number of Logs to pack
     *
     * @var integer
     */
    const MAX_PACKING_SIZE = 100;

    /**
     * The data packed to return
     *
     * @var array
     */
    private $dataPacked = array();

    /**
     * This method will return packed entity array
     * from given array of logs
     *
     * @param  array $logs
     * @return array Array of Log entities packed
     */
    public function __construct(array $logs)
    {
        if(count($logs)>static::MAX_PACKING_SIZE) throw new ActionNotPerformedException("Max size to pack is ".static::MAX_PACKING_SIZE." registers");

        //Start packing
        foreach($logs as $log) $this->packItem ($log);
    }

    public function getDataPacked()
    {
        return $this->dataPacked;
    }

    private function packItem(Log $log)
    {
        $keyData = $log->getName().$log->getVersion();
        if (!isset($this->dataPacked[$keyData])) {
            /*
             * Creating the first item of the pack
             */
            $packedLog = new Log();
            $packedLog->setName($log->getName());
            $packedLog->setExecutions($log->getExecutions());
            $packedLog->setTag($log->getTag());
            $packedLog->setTime($log->getTime());
            $packedLog->setVersion($log->getVersion());

            $this->dataPacked[$keyData] = $packedLog;
        } else {

            $packedLog = $this->dataPacked[$keyData];

            //Just updating the average time
            $x = ($packedLog->getExecutions()*$packedLog->getTime()) + ($log->getExecutions()*$log->getTime());
            $w = $packedLog->getExecutions() + $log->getExecutions();
            $avgPond = ($x/$w);
            $packedLog->setTime(number_format($avgPond, 4));
            $packedLog->setExecutions($w);
        }
    }

}
