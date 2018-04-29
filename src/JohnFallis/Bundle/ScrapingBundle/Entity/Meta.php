<?php
/*
 * (c) John Fallis <mrjohnfallis@gmail.com>
 */
namespace JohnFallis\Bundle\ScrapingBundle\Entity;

/**
 * Meta Entity.
 *
 * @package  JohnFallis\Bundle\ScrapingBundle\Entity\Meta
 * @author   John Fallis <mrjohnfallis@gmail.com>
 * @since    28/04/18
 */
class Meta
{
    /**
     * @var string
     */
    private $url = '';

    /**
     * @var string
     */
    private $link = '';

    /**
     * @var string
     */
    private $metaDescription = '';

    /**
     * @var string
     */
    private $keywords = '';

    /**
     * @var float
     */
    private $filesize = 0.0;


    /**
     * Get URL.
     *
     * @return string
     */
    public function getUrl() : string
    {
        return $this->url;
    }

    /**
     * Set URL.
     *
     * @param string $url
     * @return Meta
     */
    public function setUrl(string $url) : Meta
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get link.
     *
     * @return string
     */
    public function getLink() : string
    {
        return $this->link;
    }

    /**
     * Set link.
     *
     * @param string $link
     * @return Meta
     */
    public function setLink(string $link) : Meta
    {
        $this->link = trim($link);

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getMetaDescription() : string
    {
        return $this->metaDescription;
    }

    /**
     * Set description.
     *
     * @param string $description
     * @return Meta
     */
    public function setMetaDescription(string $description) : Meta
    {
        $this->metaDescription = trim($description);

        return $this;
    }

    /**
     * Get keywords.
     *
     * @return string
     */
    public function getKeywords() : string
    {
        return $this->keywords;
    }

    /**
     * Set keywords.
     *
     * @param string $keywords
     * @return Meta
     */
    public function setKeywords(string $keywords) : Meta
    {
        $this->keywords = trim($keywords);

        return $this;
    }

    /**
     * Get filesize.
     *
     * @return float
     */
    public function getFilesize() : float
    {
        return $this->filesize;
    }

    /**
     * Set filesize.
     *
     * @param float $filesize
     * @return Meta
     */
    public function setFilesize(float $filesize) : Meta
    {
        $this->filesize = $filesize;

        return $this;
    }
}
