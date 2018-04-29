<?php
/*
 * (c) John Fallis <mrjohnfallis@gmail.com>
 */
namespace JohnFallis\Bundle\ScrapingBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use JohnFallis\Bundle\ScrapingBundle\Entity\Meta;

/**
 * Meta Entity.
 *
 * @package  JohnFallis\Bundle\ScrapingBundle\Entity\ScrapingCollection
 * @author   John Fallis <mrjohnfallis@gmail.com>
 * @since    28/04/18
 */
class ScrapingCollection extends ArrayCollection
{
    /**
     * Initialise Meta element with scraping URL.
     *
     * @param string $url
     * @return Collection
     */
    public function addScrapingUrl(string $url) : Collection
    {
        $meta = new Meta();
        $meta->setUrl($url);
        $this->add($meta);

        return $this;
    }

    /**
     * Get total filesize.
     *
     * @return float
     */
    public function totalFilesize()
    {
        $total = 0.0;
        $this->map(function ($item) use (&$total) {
            $total += $item->getFilesize();
        });

        return $total;
    }
}
