<?php
namespace NT\NewsBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use NT\SEOBundle\SeoAwareInterface;
use NT\SEOBundle\SeoAwareTrait;
use NT\PublishWorkflowBundle\PublishWorkflowTrait;

/**
 * News's entity
 *
 * @ORM\Table(name="news")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="NewsRepository")
 * @Gedmo\Loggable
 *
 */
class News implements SeoAwareInterface
{
    use SeoAwareTrait;
    use PublishWorkflowTrait;
    use \NT\FrontendBundle\Traits\SocialIconsTrait;
    use \A2lix\TranslationFormBundle\Util\Gedmo\GedmoTranslatable;

    /** @ORM\Id @ORM\GeneratedValue @ORM\Column(type="integer") */
    protected $id;

    /**
     * @Gedmo\Versioned
     * @Gedmo\Translatable
     * @ORM\Column(name="slug", type="string", length=255, nullable=true)
     */
    protected $slug;

    /**
     * @Gedmo\Versioned
     * @Gedmo\Translatable
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    protected $title;

    /**
     * @Gedmo\Versioned
     * @Gedmo\Translatable
     * @ORM\Column(name="simple_description", type="text", nullable=true)
     */
    protected $simpleDescription;

    /**
     * @Gedmo\Versioned
     * @Gedmo\Translatable
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(name="published_date", type="datetime")
     */
    protected $publishedDate;

    /**
     * @Gedmo\Versioned
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @Gedmo\Versioned
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_at", type="datetime")
     */
    protected $updatedAt;

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
     * @ORM\Column(name="is_homepage", type="boolean")
     */
    protected $isHomepage = false;

    /**
     * @ORM\Column(name="is_top", type="boolean")
     */
    protected $isTop = false;

    /**
     * @ORM\ManyToMany(targetEntity="NewsCategory", inversedBy="posts")
     * @ORM\JoinTable(name="news_categories_m2m")
     */
    protected $postsCategories;

    /**
     * @ORM\OneToMany(targetEntity="NT\NewsBundle\Entity\NewsTranslation", mappedBy="object", cascade={"persist", "remove"}, indexBy="locale")
     */
    protected $translations;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    public function getRoute()
    {
        if ($this->getPostsCategories()->count() > 0) {
            return 'posts_category_post_view';
        } else {
            return 'post_without_category';
        }
    }

    public function getRouteParams($params = array())
    {
        return array_merge(array('slug' => $this->getSlug()), $params);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        if($this->title === NULL || strlen($this->title) == 0 && $this->translations !== null) {
            foreach ($this->translations as $translation) {
                if($translation->getTitle() && strlen($translation->getTitle()) != 0) {
                    return $translation->getTitle();
                }
            }
        }
        return $this->title;
    }

    public function setTitle($value)
    {
        $this->title = $value;
    }

    public function getSimpleDescription()
    {
        return $this->simpleDescription;
    }

    public function setSimpleDescription($value)
    {
        $this->simpleDescription = $value;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($value)
    {
        $this->description = $value;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function setSlug($value)
    {
        $this->slug = $value;
    }

    public function getPublishedDate()
    {
        return $this->publishedDate;
    }

    public function setPublishedDate($publishedDate)
    {
        $this->publishedDate = $publishedDate;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image)
    {
        $this->image = $image;
    }

    public function __toString()
    {
        return $this->getTitle() ?: 'n/a';
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
    }

    /**
     * Get the value of Is Homepage
     *
     * @return mixed
     */
    public function getIsHomepage()
    {
        return $this->isHomepage;
    }

    /**
     * Set the value of Is Homepage
     *
     * @param mixed isHomepage
     *
     * @return self
     */
    public function setIsHomepage($isHomepage)
    {
        $this->isHomepage = $isHomepage;

        return $this;
    }

    /**
     * Get the value of Is Top
     *
     * @return mixed
     */
    public function getIsTop()
    {
        return $this->isTop;
    }

    /**
     * Set the value of Is Top
     *
     * @param mixed isTop
     *
     * @return self
     */
    public function setIsTop($isTop)
    {
        $this->isTop = $isTop;

        return $this;
    }


    /**
     * Get the value of Posts Categories
     *
     * @return mixed
     */
    public function getPostsCategories()
    {
        return $this->postsCategories;
    }

    /**
     * Set the value of Posts Categories
     *
     * @param mixed postsCategories
     *
     * @return self
     */
    public function setPostsCategories($postsCategories)
    {
        $this->postsCategories = $postsCategories;

        return $this;
    }

}
