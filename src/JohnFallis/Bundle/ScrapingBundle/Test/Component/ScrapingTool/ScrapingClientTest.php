<?php

namespace JohnFallis\Bundle\ScrapingBundle\Test\Component\ScrapingTool;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Stream;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Promise;
use JohnFallis\Bundle\ScrapingBundle\Component\ScrapingTool\ImmutableDom;
use JohnFallis\Bundle\ScrapingBundle\Component\ScrapingTool\ScrapingClient;
use JohnFallis\Bundle\ScrapingBundle\Component\ScrapingTool\SimpleDomInterface;
use JohnFallis\Bundle\ScrapingBundle\Entity\ScrapingCollection;
use JohnFallis\Bundle\ScrapingBundle\Entity\Meta;

/**
 * Unit test ScrapingClient object.
 *
 * @package  JohnFallis\Bundle\ScrapingBundle\Test\Component\ScrapingTool\ScrapingClientTest
 * @author   John Fallis <mrjohnfallis@gmail.com>
 * @since    28/04/18
 */
class ScrapingClientTest extends TestCase
{
    /**
     * @var string
     */
    private $sampleHtml;

    /**
     * Test simple request.
     *
     * @return void
     */
    public function testRequest()
    {
        $stream = $this->mockStream($this->sampleHtml);
        $response = $this->mockResponse($stream);
        $client = $this->mockClient($response);

        $dom = $this->createMock(SimpleDomInterface::class);
        $scraping = new ScrapingClient($client, $dom);
        $htmlContent = $scraping->request('https://www.black-ink.org/');

        $this->assertEquals($this->sampleHtml, $htmlContent);
    }

    /**
     * Test building scrape collection.
     *
     * @return void
     */
    public function testBuildCollection()
    {
        $stream = $this->mockStream($this->sampleHtml);
        $response = $this->mockResponse($stream);
        $client = $this->mockClient($response);

        $dom = new ImmutableDom();
        $scraping = new ScrapingClient($client, $dom);
        $collection = $scraping->buildCollection($this->sampleHtml, '//a[@href]');
        $meta = $collection->get(0);

        $this->assertCount(1, $collection);
        $this->assertEquals('http://dummy.com/test', $meta->getUrl());
    }

    /**
     * Test crawling collection.
     *
     * @return void
     */
    public function testCrawlCollection()
    {
        $stream = $this->mockStream($this->sampleHtml);
        $response = $this->mockResponse($stream);

        $collection = new ScrapingCollection();
        $collection->addScrapingUrl('http://domainone.com/');
        $collection->addScrapingUrl('http://domaintwo.com/');
        $collection->addScrapingUrl('http://domainthree.com/');

        $promise = new Promise\Promise(function () use (&$promise) {
            $promise->resolve(new Response(200, [], 'html contents'));
        });

        $client = $this->mockClient($response, $promise);

        $dom = new ImmutableDom();
        $scraping = new ScrapingClient($client, $dom);
        $collection = $scraping->crawlCollection($collection);
        $meta = $collection->get(0);

        $this->assertCount(3, $collection);
        $this->assertEquals('http://domainone.com/', $meta->getUrl());
    }

    /**
     * Mock Stream object.
     *
     * @param string $htmlContent
     * @return Stream
     */
    protected function mockStream(string $htmlContent) : Stream
    {
        $stream = $this->createMock(Stream::class);
        $stream->expects($this->any())
            ->method('isReadable')
            ->willReturn(true);
        $stream->expects($this->any())
            ->method('getContents')
            ->willReturn($htmlContent);

        return $stream;
    }

    /**
     * Mock Response object.
     *
     * @param Stream $stream
     * @return Response
     */
    protected function mockResponse(Stream $stream) : Response
    {
        $response = $this->createMock(Response::class);
        $response->expects($this->any())
            ->method('getBody')
            ->willReturn($stream);

        return $response;
    }

    /**
     * Mock Client object.
     *
     * @param Response $response
     * @param Promise|null $promise
     * @return ClientInterface
     */
    protected function mockClient(Response $response, $promise = null) : ClientInterface
    {
        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->any())
            ->method('request')
            ->willReturn($response);
        $client->expects($this->any())
            ->method('requestAsync')
            ->willReturn($promise);

        return $client;
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->sampleHtml = file_get_contents(__DIR__ . '/sample.1.html');
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        $this->sampleHtml = null;
    }
}
