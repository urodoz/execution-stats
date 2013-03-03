<?php

namespace Urodoz\TimeExecutionStatsBundle\Command;

use Urodoz\TimeExecutionStatsBundle\Entity\Log;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Albert Lacarta <urodoz@gmail.com>
 */
class ShowRankingDataCommand extends PackDataCommand
{

    protected function configure()
    {
        $this
            ->setName('urodoz:statsbundle:ranking')
            ->setDescription('Show ranking of slowest tracked optionally filtered by "tag"')
            ->addArgument("tag", InputArgument::OPTIONAL)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->addNewStyles($output);
        $em = $this->getContainer()->get("doctrine")->getEntityManager();

        $tag = $input->getArgument("tag");

        $lastVersion = $this->getLastVersion();
        if(is_null($lastVersion)) $output->writeln ("<error>No tracking data</error>");

        $dql = " SELECT l FROM UrodozTimeExecutionStatsBundle:Log l "
        . " WHERE l.version = :version "
        ;
        $parameters = array("version" => $lastVersion);

        if ($tag) {
            $dql .= " AND l.tag = :tag ";
            $parameters["tag"] = $tag;
        }

        $dql .= " ORDER BY l.time DESC ";

        $result = $em->createQuery($dql)->setParameters($parameters)->setMaxResults(10)->getResult();

        $output->writeln("");
        $output->writeln("<fire>Showing version</fire> : ".$lastVersion);
        $output->writeln("");

        $output->writeln("");
        foreach($result as $log) $this->echoLog ($output, $log);
        $output->writeln("");
    }

    private function echoLog(OutputInterface $output, Log $log, $showGroup=true)
    {
        $name = str_pad($log->getName(), 29, " ", STR_PAD_LEFT);
        if ($showGroup) {
            $tag = str_pad($log->getTag(), 19, " ", STR_PAD_LEFT);
        } else {
            $tag = "";
        }
        $time = str_pad($log->getTime()." ms", 19, " ", STR_PAD_LEFT);
        $output->writeln("<version>".$name."</version> "."<header_us>".$tag."</header_us> "."<rankingtime>".$time."</rankingtime> ");
    }

    private function getLastVersion()
    {
        $em = $this->getContainer()->get("doctrine")->getEntityManager();
        $dqlSelectLastVersion = " SELECT l.version FROM UrodozTimeExecutionStatsBundle:Log l ORDER BY l.creationDateTime DESC ";
        $result = $em->createQuery($dqlSelectLastVersion)->setMaxResults(1)->getResult();

        if (isset($result[0])) {
            return $result[0]["version"];
        }

        return null;
    }

}
