<?php

namespace JohnFallis\Bundle\ScrapingBundle\Test\Component\ScrapingTool;

use PHPUnit\Framework\TestCase;
use JohnFallis\Bundle\ScrapingBundle\Component\ScrapingTool\ImmutableDom;

/**
 * Unit test ImmutableDom object.
 *
 * @package  JohnFallis\Bundle\ScrapingBundle\Test\Component\ScrapingTool\ImmutableDomTest
 * @author   John Fallis <mrjohnfallis@gmail.com>
 * @since    28/04/18
 */
class ImmutableDomTest extends TestCase
{
    /**
     * Test basic DOM initialisation.
     *
     * @return void
     */
    public function testBasicDomInitialisation()
    {
        $dom = new ImmutableDom();

        $sourceOne = file_get_contents(__DIR__ . '/sample.1.html');
        $sourceTwo = file_get_contents(__DIR__ . '/sample.2.html');

        $resultOne = $dom->initialise()->loadHtml($sourceOne)->saveHtml();
        $this->assertEquals($sourceOne, $resultOne);

        $resultTwo = $dom->initialise()->loadHtml($sourceTwo)->saveHtml();
        $this->assertNotEquals($sourceTwo, $resultTwo);
    }

    /**
     * Test Html No Implied DOM initialisation.
     *
     * @return void
     */
    public function testNoImpliedHtml()
    {
        $dom = new ImmutableDom();

        $sourceOne = file_get_contents(__DIR__ . '/sample.1.html');
        $sourceTwo = file_get_contents(__DIR__ . '/sample.2.html');

        $resultOne = $dom
            ->sethtmlNoImplied(true)
            ->initialise()
            ->loadHtml($sourceOne)
            ->saveHtml();
        $this->assertEquals($sourceOne, $resultOne);

        $resultTwo = $dom
            ->sethtmlNoImplied(true)
            ->initialise()
            ->loadHtml($sourceTwo)
            ->saveHtml();
        $this->assertEquals($sourceTwo, $resultTwo);
    }

    /**
     * Test Xpath.
     *
     * @return void
     */
    public function testXpath()
    {
        $dom = new ImmutableDom();
    
        $source = file_get_contents(__DIR__ . '/sample.1.html');

        $dom = $dom->initialise()->loadHtml($source);
        $headerOne = $dom->getXpath()->query('//h1')->item(0);
        $paragraph = $dom->getXpath()->query('//p')->item(0);

        $this->assertEquals('Sample data .1', $headerOne->nodeValue);
        $this->assertContains('Lorem ipsum dolor sit amet, consectetur adipiscing elit.', $paragraph->nodeValue);
    }
}
