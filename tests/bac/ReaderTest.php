<<?php

use PHPUnit\Framework\TestCase;
use Mokka\Bac\FidorReader;

final class ReaderTest extends TestCase 
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

    public function testGetOrderId()
    {
        $toTest = self::$csv[2];
        $fidorReader = new FidorReader($toTest);
        $orderId = $fidorReader->getOrderId($toTest);
        $this->assertSame(88688, (int) $orderId);
    }

    public function testGetName() 
    {
        $toTest = self::$csv[2];
        $fidorReader = new FidorReader($toTest);
        $name = $fidorReader->getName($toTest);

        $this->assertSame("Huber", $name[0]);
        $this->assertSame("Thomas", $name[1]);
    }

    public function testGetAmount()
    {
        $toTest = self::$csv[2];
        $fidorReader = new FidorReader($toTest);
        $amount = $fidorReader->getAmount();
        $this->assertSame($amount, 46.49);

    }

}