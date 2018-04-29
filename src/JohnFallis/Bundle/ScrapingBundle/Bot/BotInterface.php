<?php
/*
 * (c) John Fallis <mrjohnfallis@gmail.com>
 */
namespace JohnFallis\Bundle\ScrapingBundle\Bot;

use RuntimeException;
use JohnFallis\Bundle\ScrapingBundle\Component\ScrapingTool\ScrapingClientInterface;
use JohnFallis\Bundle\ScrapingBundle\Entity\ScrapingCollection;

/**
 * Bot interface.
 *
 * @package  JohnFallis\Bundle\ScrapingBundle\Bot\BotInterface
 * @author   John Fallis <mrjohnfallis@gmail.com>
 * @since    28/04/18
 */
interface BotInterface
{
    /**
     * Crawl black-ink.org homepage links related to "Digitalia".
     *
     * @return ScrapingCollection
     */
    public function crawl() : ScrapingCollection;
}
