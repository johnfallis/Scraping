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
 * Scraping tool interface.
 *
 * @package  JohnFallis\Bundle\ScrapingBundle\Component\ScrapingTool\ScrapingClientInterface
 * @author   John Fallis <mrjohnfallis@gmail.com>
 * @since    28/04/18
 */
interface ScrapingClientInterface
{
    /**
     * Request URL and return HTML content string.
     *
     * @param string $url
     * @return string
     */
    public function request(string $url) : string;

    /**
     * Build collection of links to crawl.
     *
     * @param string $content
     * @param string $xpathQuery
     * @return ScrapingCollection
     */
    public function buildCollection(string $content, string $xpathQuery = '//a[@href]') : ScrapingCollection;

    /**
     * Crawl collection links.
     *
     * @param ScrapingCollection $collection
     * @return ScrapingCollection
     */
    public function crawlCollection(ScrapingCollection $collection) : ScrapingCollection;
}
