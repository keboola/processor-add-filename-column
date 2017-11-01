<?php
require_once(__DIR__ . "/../vendor/autoload.php");

$testFolder = __DIR__;

$finder = new \Symfony\Component\Finder\Finder();
$finder->directories()->sortByName()->in($testFolder)->depth(0);
foreach ($finder as $testSuite) {
    print "Test " . $testSuite->getPathName() . "\n";
    $temp = new \Keboola\Temp\Temp("processor-add-filename-column");
    $temp->initRunFolder();

    $copyCommand = "cp -R " . $testSuite->getPathName() . "/source/data/* " . $temp->getTmpFolder();
    (new \Symfony\Component\Process\Process($copyCommand))->mustRun();

    mkdir($temp->getTmpFolder() . "/out/tables", 0777, true);
    mkdir($temp->getTmpFolder() . "/out/files", 0777, true);

    $setEnv = '';
    if (file_exists($testSuite->getPathName() . "/source/env.ini")) {
        $envs = parse_ini_file($testSuite->getPathName() . "/source/env.ini");
        if ($envs) {
            foreach ($envs as $env => $value) {
                $setEnv .= "export {$env}=\"" . str_replace('"', '\"', $value) . "\" && ";
            }
        }
    }


    $runCommand = "{$setEnv} php /code/main.php --data=" . $temp->getTmpFolder();
    $runProcess = new \Symfony\Component\Process\Process($runCommand);
    $runProcess->mustRun();

    if ($runProcess->getOutput()) {
        print "\n" . $runProcess->getOutput() . "\n";
    }

    $diffCommand = "diff --exclude=.gitkeep --ignore-all-space --recursive " . $testSuite->getPathName() . "/expected/data/out " . $temp->getTmpFolder() . "/out";
    $diffProcess = new \Symfony\Component\Process\Process($diffCommand);
    $diffProcess->run();
    if ($diffProcess->getExitCode() > 0) {
        if ($diffProcess->getOutput()) {
            print "\n" . $diffProcess->getOutput() . "\n";
        }
        if ($diffProcess->getErrorOutput()) {
            print "\n" . $diffProcess->getErrorOutput() . "\n";
        }
        exit(1);
    }
}
