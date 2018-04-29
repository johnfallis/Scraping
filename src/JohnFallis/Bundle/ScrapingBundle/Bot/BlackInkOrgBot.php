<?php
/*
 * (c) John Fallis <mrjohnfallis@gmail.com>
 */
namespace JohnFallis\Bundle\ScrapingBundle\Bot;

use RuntimeException;
use JohnFallis\Bundle\ScrapingBundle\Component\ScrapingTool\ScrapingClientInterface;
use JohnFallis\Bundle\ScrapingBundle\Entity\ScrapingCollection;

/**
 * Bot: black-ink.org
 *
 * @package  JohnFallis\Bundle\ScrapingBundle\Bot\BlackInkOrgBot
 * @author   John Fallis <mrjohnfallis@gmail.com>
 * @since    28/04/18
 */
class BlackInkOrgBot implements BotInterface
{
    /**
     * Public constructor.
     *
     * @param ScrapingClientInterface $client
     */
    public function __construct(ScrapingClientInterface $scrapingClient)
    {
        $this->scrapingClient = $scrapingClient;
    }

    /**
     * Crawl black-ink.org homepage links related to "Digitalia".
     *
     * @return ScrapingCollection
     */
    public function crawl() : ScrapingCollection
    {
        $htmlContent = $this->scrapingClient->request('https://www.black-ink.org/');

        if (!$htmlContent) {
            throw new RuntimeException('Black Ink Request failed');
        }

        $xpathQuery = implode('', [
            '//main[@role="main"]',
            '/descendant::article',
            '/descendant::*[@itemprop="articleSection" and contains(text(), "Posted in")]',
            '/descendant-or-self::*[contains(text(), "Digitalia")]',
            '/ancestor::article',
            '/descendant-or-self::*[@itemprop="description"]',
            '/descendant-or-self::a[@href]',
        ]);

        $collection = $this->scrapingClient->buildCollection($htmlContent, $xpathQuery);
        $responseCollection = $this->scrapingClient->crawlCollection($collection);

        return $responseCollection;
    }
}
