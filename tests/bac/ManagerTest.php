<<?php

use PHPUnit\Framework\TestCase;
use Mokka\Bac\WpManager;
use Mokka\Utils\Logger;

final class ManagerTest extends TestCase 
{
    private static $csv;
    public static function setUpBeforeClass(): void
    {
        $array = [];
        $i = 0;
        $fields = [];        
        $path = __DIR__ . '/sample-input.csv';
        $file = fopen($path, "r");
        if ($file) {
            while (($row = fgetcsv($file, 4096, ';')) !== false) {
                if ($i === 0) {
                    $fields = $row;
                }

                foreach ($row as $k=>$value) {
                    if ($i === 0)  {
                        continue;
                    }
                    
                    $array[$i][$fields[$k]] = $value;
                }

                $i++;
            }
            if (!feof($file)) {
                echo "Error: unexpected fgets() fail\n";
            }
            fclose($file);
        }
        self::$csv = $array;
    }

    public static function tearDownAfterClass(): void
    {
        self::$csv = null;
    }

    public function testGetPositivesOnly()
    {
        $manager = new WpManager(self::$csv);
        $positivesRows = $manager->getPositiveTransfers();

        $this->assertSame(count($positivesRows), 5);
    }


}