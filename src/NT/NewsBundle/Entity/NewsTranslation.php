<?php

namespace NT\NewsBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Translatable\Entity\MappedSuperclass\AbstractTranslation;

/**
 *  Entity holding News's translations
 *
 * @ORM\Entity
 * @ORM\Table(name="news_i18n", uniqueConstraints={@ORM\UniqueConstraint(name="lookup_unique_idx", columns={
 *     "locale", "object_id"
 *   }), @ORM\UniqueConstraint(name="slug_unique_idx", columns={"slug", "locale"})}
 * )
 * @Gedmo\Loggable
 *
 */
class NewsTranslation extends AbstractTranslation
{

    /**
     * @ORM\ManyToOne(targetEntity="NT\NewsBundle\Entity\News", inversedBy="translations")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $object;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(name="title", length=255, nullable=true)
     */
    protected $title;

    /**
     * @Gedmo\Versioned
     * @Gedmo\Slug(fields={"title"}, separator="-", updatable=false, unique=false)
     * @ORM\Column(name="slug", type="string", length=255, nullable=true)
     */
    protected $slug;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(name="simpleDescription", type="text", nullable=true)
     */
    protected $simpleDescription;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id",  onDelete="SET NULL")
     */
    protected $image;

    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Gallery")
     * @ORM\JoinColumn(name="gallery_id", referencedColumnName="id",  onDelete="SET NULL")
     */
    protected $gallery;

    /**
     * Convinient constructor
     *
     * @param string $locale
     * @param string $title
     * @param string $slug
     * @param string $simple_description
     * @param string $description
     */
    public function __construct($locale = null, $title = null, $slug = null, $simpleDescription = null, $description = null, $image = null, $gallery = null)
    {
        $this->locale = $locale;
        $this->title = $title;
        $this->slug = $slug;
        $this->simpleDescription = $simpleDescription;
        $this->description = $description;
        $this->gallery = $gallery;
    }

    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;
        return $this;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function setSimpleDescription($simpleDescription)
    {
        $this->simpleDescription = $simpleDescription;
        return $this;
    }

    public function getSimpleDescription()
    {
        return $this->simpleDescription;
    }

    /**
     * Gets the value of image.
     *
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Sets the value of image.
     *
     * @param mixed $image the image
     *
     * @return self
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Gets the value of gallery.
     *
     * @return mixed
     */
    public function getGallery()
    {
        return $this->gallery;
    }

    /**
     * Sets the value of gallery.
     *
     * @param mixed $gallery the gallery
     *
     * @return self
     */
    public function setGallery($gallery)
    {
        $this->gallery = $gallery;

        return $this;
    }
}
