<?php
/*
 * (c) John Fallis <mrjohnfallis@gmail.com>
 */
namespace JohnFallis\Bundle\ScrapingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JohnFallis\Bundle\ScrapingBundle\Entity\ScrapingCollection;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use JohnFallis\Bundle\ScrapingBundle\NameConverter\MetaNameConverter;

/**
 * Scrape the first page of http://www.black-ink.org/, find all the posts in the category “Digitalia”.
 *
 * @package  JohnFallis\Bundle\ScrapingBundle\Controller\ExampleScrapingController
 * @author   John Fallis <mrjohnfallis@gmail.com>
 * @since    28/04/18
 * @Route    ("/johnfallis", name="johnfallis")
 */
class ExampleScrapingController extends Controller
{
    /**
     * Index controller.
     *
     * @return void
     * @Route("", name="johnfallis-index")
     */
    public function indexAction()
    {
        $blackInk = $this->get('fuse_aware_bundle_scraping.bot.black_ink_org_bot');
        $collection = $blackInk->crawl();

        $encoder = new JsonEncoder();
        $normalizer = new ObjectNormalizer(null, new MetaNameConverter());
        $normalizer->setCallbacks(['filesize' => function ($filesize) {
            $kb = $filesize > 0 ? ($filesize / 1024) : $filesize;
            return sprintf('%.2fkb', $kb);
        }]);

        $serializer = new Serializer([$normalizer], [$encoder]);
        $serialisedContent = $serializer->serialize([
            'results' => $collection,
            'total' => sprintf('%.2fkb', $collection->totalFilesize() / 1024),
        ], 'json');

        $response = new Response();
        $response->setContent($serialisedContent);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
