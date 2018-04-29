<?php
/*
 * (c) John Fallis <mrjohnfallis@gmail.com>
 */
namespace JohnFallis\Bundle\ScrapingBundle\Component\ScrapingTool;

use DOMDocument;
use DOMXPath;
use Throwable;
use InvalidArgumentException;
use Psr\Log\LoggerAwareTrait;

/**
 * Immutable DOM parser.
 *
 * @package  JohnFallis\Bundle\ScrapingBundle\Component\ScrapingTool\ImmutableDom
 * @author   John Fallis <mrjohnfallis@gmail.com>
 * @since    28/04/18
 */
class ImmutableDom implements SimpleDomInterface
{
    use LoggerAwareTrait;

    /**
     * @var DOMDocument
     */
    private $domDocument;

    /**
     * @var DOMXPath
     */
    private $xpath;

    /**
     * @var bool
     */
    private $isHtmlNoImplied = false;

    /**
     * Initialise the DOM Document.
     *
     * @return SimpleDomInterface
     */
    public function initialise() : SimpleDomInterface
    {
        libxml_use_internal_errors(true);
        $this->domDocument = new DOMDocument('1.0', 'UTF-8');

        return clone $this;
    }

    /**
     * Load HTML from string.
     *
     * @param string $source
     * @return SimpleDomInterface
     */
    public function loadHtml(string $source) : SimpleDomInterface
    {
        try {
            $charConvertSource = $this->safeCharacterConvert($source);
            $this->getNode()->loadHTML(
                $charConvertSource,
                $this->hasLatestOsLibXml() && $this->isHtmlNoImplied() ? (LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD) : 0
            );
            libxml_clear_errors();
            $this->xpath = new DOMXPath($this->getNode());
        } catch (Throwable $ex) {
            if ($this->logger) {
                $this->logger->error('Failed to load Html', [
                    'source' => $source,
                    'message' => $ex->getMessage(),
                    'code' => $ex->getCode(),
                    'file' => $ex->getFile(),
                    'line' => $ex->getLine(),
                ]);
            }
        }

        return clone $this;
    }

    /**
     * Dumps the internal document into a string using HTML formatting.
     *
     * @return string
     */
    public function saveHtml() : string
    {
        try {
            $saveHtml = $this->getNode()->saveHTML();
            $trimmedHtml = trim($saveHtml);
            if (!$this->hasLatestOsLibXml() && $this->isHtmlNoImplied()) {
                return $this->removeImpliedHtmlBody($trimmedHtml);
            }
        } catch (Throwable $ex) {
            if ($this->logger) {
                $this->logger->error('Failed to save Html', [
                    'message' => $ex->getMessage(),
                    'code' => $ex->getCode(),
                    'file' => $ex->getFile(),
                    'line' => $ex->getLine(),
                ]);
            }
        }

        return $saveHtml;
    }

    /**
     * Get DOMDocument.
     *
     * @return DOMDocument
     */
    public function getNode() : DOMDocument
    {
        return $this->domDocument;
    }

    /**
     * Get XPath for document.
     *
     * @return DOMXPath
     */
    public function getXpath() : DOMXPath
    {
        return $this->xpath;
    }

    /**
     * Set HTML_PARSE_NOIMPLIED flag, which turns off the automatic adding of implied html/body.
     *
     * @param boolean $isHtmlNoImplied
     * @return SimpleDomInterface
     */
    public function sethtmlNoImplied(bool $isHtmlNoImplied) : SimpleDomInterface
    {
        $this->isHtmlNoImplied = $isHtmlNoImplied;

        return $this;
    }

    /**
     * Is HTML_PARSE_NOIMPLIED flag, which turns off the automatic adding of implied html/body.
     *
     * @return boolean
     */
    public function isHtmlNoImplied() : bool
    {
        return $this->isHtmlNoImplied;
    }

    /**
     * Remove automatic implied html/body.
     *
     * @param string $html
     * @return string
     */
    protected function removeImpliedHtmlBody(string $html) : string
    {
        $matches = [];
        preg_match('/<body?[^<>]+?>(.*)<\/body?[^<>]+?>/s', $html, $matches);
        $innerBodyHtml = isset($bodyMatches[0]) ? $bodyMatches[0] : $html;

        return $innerBodyHtml;
    }

    /**
     * Safe character convert for HTML string.
     *
     * @param string $html
     * @return string
     */
    protected function safeCharacterConvert(string $html) : string
    {
        return mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
    }

    /**
     * OS has Libxml >= 2.7.7 for additioanl features.
     *
     * @return boolean
     */
    protected function hasLatestOsLibXml() : bool
    {
        return defined('LIBXML_HTML_NOIMPLIED');
    }
}
