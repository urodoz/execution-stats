<?php

namespace Urodoz\TimeExecutionStatsBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

/**
 * @author Albert Lacarta <urodoz@gmail.com>
 */
class ShowDataCommand extends PackDataCommand
{

    protected function configure()
    {
        $this
            ->setName('urodoz:statsbundle:show')
            ->setDescription('Show statistics of tracked times')
            ->addArgument("name", InputArgument::REQUIRED)
            ->addArgument("version", InputArgument::OPTIONAL)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get("doctrine")->getEntityManager();

        $this->addNewStyles($output);
        $this->pack($output);

        $name = $input->getArgument("name");
        $version = $input->getArgument("version");

        $dql = " SELECT l FROM UrodozTimeExecutionStatsBundle:Log l "
        . " WHERE l.name = :name "
        ;
        $parameters = array("name" => $name);

        if ($version) {
            $dql .= " AND l.version = :version ";
            $parameters["version"] = $version;
        }
        $dql .= " ORDER BY l.creationDateTime DESC ";
        $result = $em->createQuery($dql)->setParameters($parameters)->setMaxResults(20)->getResult();

        /*
         * Printing results
         */
        $output->writeln("<keyname>".$name."</keyname> Showing [".count($result)."] last versions");
        $output->writeln("");

        $this->echoHeaders($output);

        //Retrieve data from result
        $dataToEcho = array();
        foreach ($result as $log) {
            $dataToEcho[] = array(
                "version" => $log->getVersion(),
                "calls" => $log->getExecutions(),
                "time" => $log->getTime(),
                "diff" => null,
            );
        }

        //Calculate differences on performance
        for ($i=0;$i<count($dataToEcho);$i++) {
            if (isset($dataToEcho[$i+1])) {
                $newAvgTime = $dataToEcho[$i]["time"];
                $prevAvgTime = $dataToEcho[$i+1]["time"];
                $diff = (($newAvgTime-$prevAvgTime)/$prevAvgTime)*100;
                $dataToEcho[$i]["diff"] = number_format($diff, 4);
            }
        }

        //Print rows
        foreach($dataToEcho as $dataRow) $this->echoDataLine ($output, $dataRow);

        $output->writeln("");
    }

    protected function echoDataLine(OutputInterface $output, array $dataRow)
    {
        $version = str_pad($dataRow["version"], 19, " ", STR_PAD_LEFT);
        $calls = str_pad($dataRow["calls"], 19, " ", STR_PAD_LEFT);
        $avg = str_pad($dataRow["time"]." ms", 19, " ", STR_PAD_LEFT);
        $perfString = ($dataRow["diff"]) ? $dataRow["diff"]." %" : $dataRow["diff"];
        $performace = str_pad($perfString, 19, " ", STR_PAD_LEFT);

        //Adding style to performance
        if ($dataRow["diff"]>0) {
            $performace = "<decreaseperf>".$performace."</decreaseperf>";
        } elseif ($dataRow["diff"]<0) {
            $performace = "<improveperf>".$performace."</improveperf>";
        }

        $output->writeln("<version>".$version."</version> ".$calls." ".$avg." ".$performace." ");
    }

    protected function echoHeaders(OutputInterface $output)
    {
        $headUnderscore = str_pad("", 19, "-", STR_PAD_BOTH);
        $headVersion = str_pad("Version", 19, " ", STR_PAD_BOTH);
        $headCalls = str_pad("Calls", 19, " ", STR_PAD_BOTH);
        $headAvg = str_pad("Avg.Time", 19, " ", STR_PAD_BOTH);
        $headPerformace = str_pad("Diff.Performance", 19, " ", STR_PAD_BOTH);

        $output->writeln("<header>".$headVersion."</header> "
                ."<header>".$headCalls."</header> "
                ."<header>".$headAvg."</header> "
                ."<header>".$headPerformace."</header> "
                );

        $output->writeln("<header_us>".$headUnderscore."</header_us> "
                ."<header_us>".$headUnderscore."</header_us> "
                ."<header_us>".$headUnderscore."</header_us> "
                ."<header_us>".$headUnderscore."</header_us> "
                );
    }

}
