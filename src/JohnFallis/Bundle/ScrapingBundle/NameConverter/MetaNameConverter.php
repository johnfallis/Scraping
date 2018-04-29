<?php
/*
 * (c) John Fallis <mrjohnfallis@gmail.com>
 */
namespace JohnFallis\Bundle\ScrapingBundle\NameConverter;

use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

/**
 * Meta name converter.
 *
 * @package  JohnFallis\Bundle\ScrapingBundle\NameConverter\MetaNameConverter
 * @author   John Fallis <mrjohnfallis@gmail.com>
 * @since    28/04/18
 */
class MetaNameConverter implements NameConverterInterface
{
    /**
     * {@inheritdoc}
     */
    public function normalize($propertyName)
    {
        return strtolower(preg_replace('/[A-Z]/', ' \\0', lcfirst($propertyName)));
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($propertyName)
    {
        $camelCasedName = preg_replace_callback('/(^| |\.)+(.)/', function ($match) {
            return ('.' === $match[1] ? ' ' : '').strtoupper($match[2]);
        }, $propertyName);

        $camelCasedName = lcfirst($camelCasedName);

        return $camelCasedName;
    }
}
