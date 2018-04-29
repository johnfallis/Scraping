<?php
/*
 * (c) John Fallis <mrjohnfallis@gmail.com>
 */
namespace JohnFallis\Bundle\ScrapingBundle\Test\NameConverter;

use PHPUnit\Framework\TestCase;
use JohnFallis\Bundle\ScrapingBundle\NameConverter\MetaNameConverter;

/**
 * Unit test MetaNameConverter object.
 *
 * @package  JohnFallis\Bundle\ScrapingBundle\Test\NameConverter\MetaNameConverterTest
 * @author   John Fallis <mrjohnfallis@gmail.com>
 * @since    28/04/18
 */
class MetaNameConverterTest extends TestCase
{
    /**
     * Test name converter normaliser.
     *
     * @return void
     */
    public function testNormalize()
    {
        $normalised = $this->object->normalize('metaDescription');
        $this->assertEquals('meta description', $normalised);
    }

    /**
     * Test name converter denormaliser.
     *
     * @return void
     */
    public function testDenormalize()
    {
        $normalised = $this->object->denormalize('meta description');
        $this->assertEquals('metaDescription', $normalised);
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->object = new MetaNameConverter();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        $this->object = null;
    }
}
