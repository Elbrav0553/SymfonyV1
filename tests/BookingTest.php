<?php

namespace App\Tests;

use App\Entity\Booking;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PostControllerTest extends WebTestCase
{

    public function testShow()
    {



        $client = static::createClient();

        $crawler = $client->request('GET', '/booking/');
        $this->assertResponseIsSuccessful();
        $this->assertJson('{"message":"Welcome to your new booking controller! OK","data":[{"id":2,"status":1,"createdAt":"2023-06-29T15:21:30+00:00","deletedAt":null,"description":"test update"},{"id":10,"status":6,"createdAt":"2023-06-29T19:57:10+00:00","deletedAt":null,"description":"John123"},{"id":19,"status":1,"createdAt":"2023-06-30T16:24:47+00:00","deletedAt":null,"description":"test"},{"id":20,"status":99,"createdAt":"2023-06-30T16:25:17+00:00","deletedAt":null,"description":"test"}],"path":"src\/Controller\/BookingController.php"}');
        //$this->assertSelectorTextContains('h1', 'test title 2');
        /**
         $this->seeJsonStructure([
    'data' => [
        '*' => [
            'id',
            'type',
            'name',
            'surname',
            'location',
        ]
    ]
]);

         */
    }


    protected function getDefaults(): array
    {
        return [
            'status' => random_int(0, 10),
            'description' => substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(10 / strlen($x)))), 1, 10),

        ];
    }
}
