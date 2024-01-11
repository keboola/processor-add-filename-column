<?php
namespace Keboola\Processor\AddFilenameColumn;

use Keboola\Csv\CsvReader;
use Keboola\Csv\CsvWriter;

/**
 * @param \SplFileInfo $sourceFile
 * @param $destinationFolder
 * @param $delimiter
 * @param $enclosure
 */
function processFile(\SplFileInfo $sourceFile, $destinationFolder, $delimiter, $enclosure)
{
    $sourceCsv = new CsvReader($sourceFile->getPathname(), $delimiter, $enclosure);
    $destinationCsv = new CsvWriter($destinationFolder . $sourceFile->getFilename(), $delimiter, $enclosure);
    $fileName = $sourceFile->getFilename();
    foreach ($sourceCsv as $row) {
        $row[] = $fileName;
        $destinationCsv->writeRow($row);
    }
}
