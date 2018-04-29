<?php
/*
 * (c) John Fallis <mrjohnfallis@gmail.com>
 */
namespace JohnFallis\Bundle\ScrapingBundle\Component\ScrapingTool;

use Closure;
use Psr\Log\LoggerAwareTrait;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Promise;
use GuzzleHttp\Psr7\Stream;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\RequestException;
use JohnFallis\Bundle\ScrapingBundle\Component\ScrapingTool\SimpleDomInterface;
use JohnFallis\Bundle\ScrapingBundle\Entity\ScrapingCollection;
use JohnFallis\Bundle\ScrapingBundle\Entity\Meta;

/**
 * Scraping tool.
 *
 * @package  JohnFallis\Bundle\ScrapingBundle\Component\ScrapingTool\ScrapingClient
 * @author   John Fallis <mrjohnfallis@gmail.com>
 * @since    28/04/18
 */
class ScrapingClient implements ScrapingClientInterface
{
    use LoggerAwareTrait;

    /**
     * @var ClientInterface;
     */
    private $httpClient;

    /**
     * @var SimpleDomInterface
     */
    private $htmlParser;

    /**
     * Public constructor.
     *
     * @param ClientInterface $httpClient
     * @param SimpleDomInterface $htmlParser
     */
    public function __construct(ClientInterface $httpClient, SimpleDomInterface $htmlParser)
    {
        $this->httpClient = $httpClient;
        $this->htmlParser = $htmlParser;
    }

    /**
     * Request URL and return HTML content string.
     *
     * @param string $url
     * @return string
     */
    public function request(string $url) : string
    {
        try {
            $response = $this->getClient()->request('GET', $url);
            if (!($response->getBody() instanceof Stream) && !$response->getBody()->isReadable()) {
                return '';
            }

            return $response->getBody()->getContents();
        } catch (Throwable $ex) {
            if ($this->logger) {
                $this->logger->error('Failed to request URL', [
                    'url' => $url,
                    'message' => $ex->getMessage(),
                    'code' => $ex->getCode(),
                    'file' => $ex->getFile(),
                    'line' => $ex->getLine(),
                ]);
            }
        }
    }

    /**
     * Build collection of links to crawl.
     *
     * @param string $content
     * @param string $xpathQuery
     * @return ScrapingCollection
     */
    public function buildCollection(string $content, string $xpathQuery = '//a[@href]') : ScrapingCollection
    {
        $collection = new ScrapingCollection();
        try {
            $xpath = $this->htmlParser->initialise()->loadHtml($content)->getXpath();
            $urls = $xpath->query($xpathQuery);

            foreach ($urls as $url) {
                if (!$url->hasAttribute('href') && $this->logger) {
                    $this->logger->info('Missing link', [
                        'title' => $url->nodeValue,
                    ]);
                    continue;
                }
                $collection->addScrapingUrl($url->getAttribute('href'));
            }
        } catch (Throwable $ex) {
            if ($this->logger) {
                $this->logger->error('Failed to build collection', [
                    'message' => $ex->getMessage(),
                    'code' => $ex->getCode(),
                    'file' => $ex->getFile(),
                    'line' => $ex->getLine(),
                ]);
            }
        }

        return $collection;
    }

    /**
     * Crawl collection links.
     *
     * @param ScrapingCollection $collection
     * @return ScrapingCollection
     */
    public function crawlCollection(ScrapingCollection $collection) : ScrapingCollection
    {
        $client = $this->getClient();
        $promises = [];
        foreach ($collection as &$meta) {
            $promises[] = $client->requestAsync('GET', $meta->getUrl())->then(
                $this->handleSuccessfulResponse($meta),
                $this->handleFailedResponse()
            );
        }

        Promise\unwrap($promises);

        return $collection;
    }

    /**
     * Get HTTP client.
     *
     * @return ClientInterface
     */
    protected function getClient() : ClientInterface
    {
        return clone $this->httpClient;
    }

    /**
     * Parse HTML contents.
     *
     * @param Response $response
     * @param Meta $meta
     * @return Meta
     */
    protected function parseHtmlContent(Response $response, Meta $meta) : Meta
    {
        $body = $response->getBody();
        $body->rewind();
        $contents = $body->getContents();
        $contentFileSize = $body->getSize();

        if (!$contents) {
            return $meta;
        }

        $xpath = $this->htmlParser->initialise()->loadHtml($contents)->getXpath();
        $xpathTitle = $xpath->query('//head/title[1]');
        $xpathMetaDescription = $xpath->query('//head/meta[(@name="description" or @property="og:description") and @content][1]');
        $xpathMetaKeyword = $xpath->query('//head/meta[@name="keywords" and @content][1]');

        if ($xpathTitle->length) {
            $meta->setLink($xpathTitle->item(0)->nodeValue);
        }
        if ($xpathMetaDescription->length) {
            $metaDescription = $xpathMetaDescription->item(0)->getAttribute('content');
            $meta->getMetaDescription($metaDescription);
        }
        if ($xpathMetaKeyword->length) {
            $metaKeyword = $xpathMetaKeyword->item(0)->getAttribute('content');
            $meta->setKeywords($metaKeyword);
        }
        $meta->setFilesize($contentFileSize);

        return $meta;
    }

    /**
     * Handle request async successful response.
     *
     * @param Meta $meta
     * @return Closure
     */
    protected function handleSuccessfulResponse(Meta $meta) : Closure
    {
        return function (Response $response) use ($meta) {
            $contents = $response->getBody()->getContents();
            if ($this->logger) {
                $this->logger->info('Success: Page crawl', [
                    'url' => $meta->getUrl(),
                    'reasonPhrase' => $response->getReasonPhrase(),
                    'body' => $contents,
                ]);
            }
            $meta = $this->parseHtmlContent($response, $meta);
        };
    }

    /**
     * Handle request async failed response.
     *
     * @return Closure
     */
    protected function handleFailedResponse() : Closure
    {
        return function (RequestException $exception) {
            if ($this->logger) {
                $this->logger->warn('Failed: Page crawl', [
                    'request' => $exception->getRequest(),
                    'response' => $exception->getResponse(),
                    'message' => $exception->getMessage(),
                ]);
            }
        };
    }
}
