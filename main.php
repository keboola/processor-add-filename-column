<?php

require('vendor/autoload.php');

/**
 * @param SplFileInfo $sourceFile
 * @param $destinationFolder
 * @param $delimiter
 * @param $enclosure
 * @param $columnName
 */
function processFile(SplFileInfo $sourceFile, $destinationFolder, $delimiter, $enclosure)
{
    $sourceCsv = new \Keboola\Csv\CsvFile($sourceFile->getPathname(), $delimiter, $enclosure);
    $destinationCsv = new \Keboola\Csv\CsvFile($destinationFolder . $sourceFile->getFilename(), $delimiter, $enclosure);
    $fileName = $sourceFile->getFilename();
    foreach ($sourceCsv as $index => $row) {
        $row[] = $fileName;
        $destinationCsv->writeRow($row);
    }
}

$arguments = getopt("", ["data:"]);
if (!isset($arguments["data"])) {
    $dataDir = "/data";
} else {
    $dataDir = $arguments["data"];
}
$destination = $dataDir . '/out/tables/';

try {
    $columnName = getenv('KBC_PARAMETER_COLUMN_NAME') === false ? 'filename' : getenv('KBC_PARAMETER_COLUMN_NAME');

    $fs = new \Symfony\Component\Filesystem\Filesystem();
    $jsonDecode = new \Symfony\Component\Serializer\Encoder\JsonDecode(true);
    $jsonEncode = new \Symfony\Component\Serializer\Encoder\JsonEncode();

    $finder = new \Symfony\Component\Finder\Finder();
    $finder->notName("*.manifest")->in($dataDir . "/in/tables")->depth(0);
    foreach ($finder as $file) {
        $columnsInManifest = false;

        $manifestFile = $file->getPathname() . ".manifest";
        if (!$fs->exists($manifestFile)) {
            throw new \Keboola\Processor\AddFilenameColumn\Exception(
                "Table " . $file->getBasename() . " does not have a manifest file."
            );
        }

        $manifest = $jsonDecode->decode(
            file_get_contents($manifestFile),
            \Symfony\Component\Serializer\Encoder\JsonEncoder::FORMAT
        );
        if (!isset($manifest["columns"])) {
            throw new \Keboola\Processor\AddFilenameColumn\Exception(
                "Manifest file for table " . $file->getBasename() . " does not specify columns."
            );
        }
        if (!isset($manifest["delimiter"])) {
            throw new \Keboola\Processor\AddFilenameColumn\Exception(
                "Manifest file for table " . $file->getBasename() . " does not specify delimiter."
            );
        }
        if (!isset($manifest["enclosure"])) {
            throw new \Keboola\Processor\AddFilenameColumn\Exception(
                "Manifest file for table " . $file->getBasename() . " does not specify enclosure."
            );
        }

        $manifest["columns"][] = $columnName;
        $targetManifest = $destination . $file->getFilename() . ".manifest";
        file_put_contents(
            $targetManifest,
            $jsonEncode->encode($manifest, \Symfony\Component\Serializer\Encoder\JsonEncoder::FORMAT)
        );

        if (is_dir($file->getPathname())) {
            // sliced file
            $slicedFiles = new FilesystemIterator($file->getPathname(), FilesystemIterator::SKIP_DOTS);
            $slicedDestination = $destination . $file->getFilename() . '/';
            if (!$fs->exists($slicedDestination)) {
                $fs->mkdir($slicedDestination);
            }
            foreach ($slicedFiles as $slicedFile) {
                processFile(
                    $slicedFile,
                    $slicedDestination,
                    $manifest["delimiter"],
                    $manifest["enclosure"]
                );
            }
        } else {
            processFile($file, $destination, $manifest["delimiter"], $manifest["enclosure"]);
        }
    }
} catch (\Keboola\Processor\AddFilenameColumn\Exception $e) {
    echo $e->getMessage();
    exit(1);
} catch (\Keboola\Csv\InvalidArgumentException $e) {
    echo $e->getMessage();
    exit(1);
} catch (\Exception $e) {
    echo $e->getMessage();
    exit(2);
}
