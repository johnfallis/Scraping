<?php

namespace JohnFallis\Bundle\ScrapingBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use JohnFallis\Bundle\ScrapingBundle\Entity\ScrapingCollection;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonDecoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use JohnFallis\Bundle\ScrapingBundle\NameConverter\MetaNameConverter;

/**
 * Meta Entity.
 *
 * @package  JohnFallis\Bundle\ScrapingBundle\Entity\ScrapingCollection
 * @author   John Fallis <mrjohnfallis@gmail.com>
 * @since    28/04/18
 */
class ExampleScrapingControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $client->request('GET', '/johnfallis');
        $response = $client->getResponse();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('content-type'));
        $responseData = json_decode($response->getContent(), true);
        $this->assertContains('kb', $responseData['total']);
        foreach ($responseData['results'] as $item) {
            $this->assertContains('http', $item['url']);
            $this->assertContains('kb', $item['filesize']);
        }
    }
}
