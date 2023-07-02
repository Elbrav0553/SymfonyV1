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
        

        
    }


    protected function getDefaults(): array
    {
        return [
            'status' => random_int(0, 10),
            'description' => substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(10 / strlen($x)))), 1, 10),

        ];
    }
}
