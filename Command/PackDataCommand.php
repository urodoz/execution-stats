<?php

namespace Urodoz\TimeExecutionStatsBundle\Command;

use Urodoz\TimeExecutionStatsBundle\Lib\Pack\PackData;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Albert Lacarta <urodoz@gmail.com>
 */
class PackDataCommand extends ContainerAwareCommand
{

    protected function addNewStyles(OutputInterface $output)
    {
        $style = new OutputFormatterStyle('cyan', 'black', array('bold'));
        $output->getFormatter()->setStyle('fire', $style);

        $style2 = new OutputFormatterStyle('black', 'cyan');
        $output->getFormatter()->setStyle('keyname', $style2);

        $style3 = new OutputFormatterStyle('yellow', null, array("bold"));
        $output->getFormatter()->setStyle('header', $style3);

        $style3 = new OutputFormatterStyle('yellow', null);
        $output->getFormatter()->setStyle('header_us', $style3);

        $style4 = new OutputFormatterStyle('white', null, array("bold"));
        $output->getFormatter()->setStyle('version', $style4);

        $style4 = new OutputFormatterStyle('white', "green", array("bold"));
        $output->getFormatter()->setStyle('improveperf', $style4);

        $style5 = new OutputFormatterStyle('white', "red", array("bold"));
        $output->getFormatter()->setStyle('decreaseperf', $style5);

        $style6 = new OutputFormatterStyle('green', "black", array("bold"));
        $output->getFormatter()->setStyle('rankingtime', $style6);
    }

    protected function configure()
    {
        $this
            ->setName('urodoz:statsbundle:pack')
            ->setDescription('Packs database log data from TimeExecutionStatsBundle')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->addNewStyles($output);
        $this->pack($output);
    }

    protected function pack(OutputInterface $output)
    {
        $output->writeln("");
        $output->writeln("<fire>Packing database data...</fire>");
        $output->writeln("");
        $em = $this->getContainer()->get("doctrine")->getEntityManager();

        //Retrieve the keys of service names to be packed
        $dql = " SELECT DISTINCT l.name FROM UrodozTimeExecutionStatsBundle:Log l ";
        $result = $em->createQuery($dql)->getResult();

        foreach($result as $name) $this->packName ($name["name"]);
    }

    protected function packName($name)
    {
        $em = $this->getContainer()->get("doctrine")->getEntityManager();
        $dql = " SELECT COUNT(l) FROM UrodozTimeExecutionStatsBundle:Log l "
        . " WHERE l.name = :name "
        ;
        $initialCount = $em->createQuery($dql)->setParameter("name", $name)->getSingleScalarResult();

        //Checking number of versions for this service
        $dql = " SELECT DISTINCT l.version FROM UrodozTimeExecutionStatsBundle:Log l "
        ;
        $result = $em->createQuery($dql)->getResult();

        $dqlRetrNameVersioned = " SELECT COUNT(l) FROM UrodozTimeExecutionStatsBundle:Log l "
        . " WHERE l.name = :name "
        . " AND l.version = :version "
        ;
        foreach ($result as $versionData) {
            $version = $versionData["version"];
            $params = array(
                "version" => $version,
                "name" => $name,
            );
            while ($em->createQuery($dqlRetrNameVersioned)->setParameters($params)->getSingleScalarResult()>1) {
                $dqlSelectLogs = " SELECT l FROM UrodozTimeExecutionStatsBundle:Log l "
                . " WHERE l.name = :name "
                . " AND l.version = :version "
                ;
                $result = $em->createQuery($dqlSelectLogs)->setParameters($params)->setMaxResults(PackData::MAX_PACKING_SIZE)->getResult();

                $pack = new PackData($result);
                $packedData = $pack->getDataPacked();

                //Remove results
                $em->beginTransaction();

                try {
                    foreach($result as $log) $em->remove($log);
                    foreach($packedData as $keyr => $packedLog) $em->persist($packedLog);
                    $em->flush();

                    $em->commit();
                } catch (\Exception $e) {
                    $em->rollback();
                }

            }
        }

    }

}
