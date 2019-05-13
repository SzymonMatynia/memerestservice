<?php


namespace App\tests\Controller;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MemeApiControllerTest extends WebTestCase
{

    public function testGetMemesIfExist()
    {
        $client = static::createClient();

        $client->request('GET', '/api/meme');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testGetMemeIfExistsGivenId()
    {
        $client = static::createClient();

        $client->request('GET', '/api/meme/1');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testGetMemeIfNotExistsGivenId()
    {
        $client = static::createClient();

        $client->request('GET', '/api/meme/1681115154154151651511151551');

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }


    public function testAddMemeIfNotProperFormat()
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/meme/add',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"title":"Fabien", "image": "kmdSKDMad"}'
        );
        $decodedContent = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals(false, $decodedContent['success']);
    }

    public function testAddMemeIfNotProperTitle()
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/meme/add',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"title":"", "image": "kmdSKDMad"}'
        );
        $decodedContent = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals(false, isset($decodedContent['title']));
    }



}