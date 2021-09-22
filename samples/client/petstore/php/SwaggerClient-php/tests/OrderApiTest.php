<?php
declare(strict_types=1);

namespace Swagger\Client;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Swagger\Client\Model\Order;

class OrderApiTest extends TestCase
{
    // test get inventory
    public function testOrderEnum(): void
    {
        $this->assertSame(Model\Order::STATUS_PLACED, "placed");
        $this->assertSame(Model\Order::STATUS_APPROVED, "approved");
    }

    // test get inventory
    public function testOrder(): void
    {
        // initialize the API client
        $order = new Model\Order();

        $order->setStatus("placed");
        $this->assertSame("placed", $order->getStatus());
    }

    public function testOrderException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        // initialize the API client
        $order = new Model\Order();
        $order->setStatus("invalid_value");
    }

    // test deseralization of order
    public function testDeserializationOfOrder(): void
    {
        $order_json = <<<ORDER
{
  "id": 10,
  "petId": 20,
  "quantity": 30,
  "shipDate": "2015-08-22T07:13:36.613Z",
  "status": "placed",
  "complete": false
}
ORDER;
        $order = ObjectSerializer::deserialize(
            json_decode($order_json, false, 512, JSON_THROW_ON_ERROR),
            Order::class
        );

        $this->assertOrder($order);
    }
  
    // test deseralization of array of array of order
    public function testDeserializationOfArrayOfArrayOfOrder(): void
    {
        $order_json = <<<ORDER
[[{
  "id": 10,
  "petId": 20,
  "quantity": 30,
  "shipDate": "2015-08-22T07:13:36.613Z",
  "status": "placed",
  "complete": false
}]]
ORDER;
        $order = ObjectSerializer::deserialize(
            json_decode($order_json, false, 512, JSON_THROW_ON_ERROR),
            'Swagger\Client\Model\Order[][]'
        );

        $this->assertArrayHasKey(0, $order);
        $this->assertArrayHasKey(0, $order[0]);
        $_order = $order[0][0];
        $this->assertOrder($_order);
    }

    // test deseralization of map of map of order
    public function testDeserializationOfMapOfMapOfOrder(): void
    {
        $order_json = <<<ORDER
{
  "test": {
    "test2": {
      "id": 10,
      "petId": 20,
      "quantity": 30,
      "shipDate": "2015-08-22T07:13:36.613Z",
      "status": "placed",
      "complete": false
    }
  }
}
ORDER;
        $order = ObjectSerializer::deserialize(
            json_decode($order_json, false, 512, JSON_THROW_ON_ERROR),
            'map[string,map[string,\Swagger\Client\Model\Order]]'
        );

        $this->assertArrayHasKey('test', $order);
        $this->assertArrayHasKey('test2', $order['test']);
        $_order = $order['test']['test2'];
        $this->assertOrder($_order);
    }

    private function assertOrder($order): void
    {
        $this->assertInstanceOf(Order::class, $order);
        $this->assertSame(10, $order->getId());
        $this->assertSame(20, $order->getPetId());
        $this->assertSame(30, $order->getQuantity());
        $this->assertEquals(new \DateTime("2015-08-22T07:13:36.613Z"), $order->getShipDate());
        $this->assertSame("placed", $order->getStatus());
        $this->assertFalse($order->getComplete());
    }
}
