<?php
namespace Keboola\Processor\AddFilenameColumn;

use Keboola\Csv\CsvFile;

/**
 * @param \SplFileInfo $sourceFile
 * @param $destinationFolder
 * @param $delimiter
 * @param $enclosure
 */
function processFile(\SplFileInfo $sourceFile, $destinationFolder, $delimiter, $enclosure)
{
    $sourceCsv = new CsvFile($sourceFile->getPathname(), $delimiter, $enclosure);
    $destinationCsv = new CsvFile($destinationFolder . $sourceFile->getFilename(), $delimiter, $enclosure);
    $destinationCsv->openFile('w+');
    $fileName = $sourceFile->getFilename();
    foreach ($sourceCsv as $index => $row) {
        $row[] = $fileName;
        $destinationCsv->writeRow($row);
    }
}
