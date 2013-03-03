<?php

namespace Urodoz\TimeExecutionStatsBundle\Lib\Implementation;

use Urodoz\TimeExecutionStatsBundle\Entity\Log;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Urodoz\TimeExecutionStatsBundle\Lib\StoreImplementationInterface;

/**
 * Uses doctrine to store the data on persistence layer
 *
 * @author Albert Lacarta <urodoz@gmail.com>
 */
class DoctrineImplementation implements StoreImplementationInterface
{

    /**
     * @var Registry
     */
    private $doctrine;

    /**
     * @param Registry $doctrine
     */
    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @see StoreImplementationInterface::storeLog()
     */
    public function storeLog($time, $name, $tag, $version)
    {
        $em = $this->doctrine->getEntityManager();

        $log = new Log();
        $log->setTime(number_format($time, 4));
        $log->setName($name);
        $log->setTag ($tag);
        $log->setVersion ($version);
        $log->setExecutions(1);

        $em->persist($log);
        $em->flush();
    }

}
