<?php
/*
 * (c) John Fallis <mrjohnfallis@gmail.com>
 */
namespace JohnFallis\Bundle\ScrapingBundle\Component\ScrapingTool;

use DOMDocument;
use DOMXPath;

/**
 * Simple DOM interface.
 *
 * @package  JohnFallis\Bundle\ScrapingBundle\Component\ScrapingTool\SimpleDomInterface
 * @author   John Fallis <mrjohnfallis@gmail.com>
 * @since    28/04/18
 */
interface SimpleDomInterface
{
    /**
     * Initialise the DOM Document.
     *
     * @return SimpleDomInterface
     */
    public function initialise() : SimpleDomInterface;

    /**
     * Load HTML from string.
     *
     * @param string $source
     * @return SimpleDomInterface
     */
    public function loadHtml(string $source) : SimpleDomInterface;

    /**
     * Dumps the internal document into a string using HTML formatting.
     *
     * @return string
     */
    public function saveHtml() : string;


    /**
     * Get DOMDocument.
     *
     * @return DOMDocument
     */
    public function getNode() : DOMDocument;

    /**
     * Get XPath for document.
     *
     * @return DOMXPath
     */
    public function getXpath() : DOMXPath;

    /**
     * Set HTML_PARSE_NOIMPLIED flag, which turns off the automatic adding of implied html/body.
     *
     * @param boolean $isHtmlNoImplied
     * @return SimpleDomInterface
     */
    public function sethtmlNoImplied(bool $isHtmlNoImplied) : SimpleDomInterface;

    /**
     * Is HTML_PARSE_NOIMPLIED flag, which turns off the automatic adding of implied html/body.
     *
     * @return boolean
     */
    public function isHtmlNoImplied() : bool;
}
