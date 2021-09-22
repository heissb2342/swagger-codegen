<?php

namespace Swagger\Client;

use PHPUnit\Framework\TestCase;
use Swagger\Client\Model\FormatTest;

class DateTimeSerializerTest extends TestCase
{
    public function testDateTimeSanitation(): void
    {
        $dateTime = new \DateTime('April 30, 1973 17:05 CEST');

        $input = new FormatTest([
            'date_time' => $dateTime,
        ]);

        $data = ObjectSerializer::sanitizeForSerialization($input);

        $this->assertEquals('1973-04-30T17:05:00+02:00', $data->dateTime);
    }

    public function testDateSanitation(): void
    {
        $dateTime = new \DateTime('April 30, 1973 17:05 CEST');

        $input = new FormatTest([
            'date' => $dateTime,
        ]);

        $data = ObjectSerializer::sanitizeForSerialization($input);

        $this->assertEquals('1973-04-30', $data->date);
    }
}
