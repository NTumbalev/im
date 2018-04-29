<?php
namespace NT\DealersBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use NT\SEOBundle\SeoAwareInterface;
use NT\PublishWorkflowBundle\PublishWorkflowInterface;

/**
 * Event
 *
 * @ORM\Table(name="dealer")
 * @ORM\Entity(repositoryClass="DealersRepository")
 * @Gedmo\Loggable
 */
class Dealer implements SeoAwareInterface, PublishWorkflowInterface
{
    use \NT\SEOBundle\SeoAwareTrait;
    use \NT\PublishWorkflowBundle\PublishWorkflowTrait;
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
     * @ORM\Column(name="title", type="string", length=255)
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
     * @Gedmo\Translatable
     * @ORM\Column(name="pin_description", type="text", nullable=true)
     */
    protected $pinDescription;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(name="latitude", type="string", length=255, nullable=true)
     */
    protected $latitude;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(name="longitude", type="string", length=255, nullable=true)
     */
    protected $longitude;

    /**
     * @var integer
     * @Gedmo\Sortable
     * @Gedmo\Versioned
     * @ORM\Column(name="rank", type="integer")
     */
    protected $rank;

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
     * @Gedmo\Versioned
     * @ORM\Column(name="isContact", type="boolean", options={"default" : 1})
     */
    protected $isContact = true;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(name="not_in_distributors", type="boolean", nullable=true)
     */
    protected $notInDistributors = true;

    /**
     * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="gallery_id", referencedColumnName="id",  onDelete="SET NULL")
     */
    protected $image;

     /**
     * @ORM\OneToMany(targetEntity="DealerTranslation", mappedBy="object", cascade={"persist", "remove"}, indexBy="locale")
     */
    protected $translations;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

        /**
     * Gets the value of description.
     *
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets the value of description.
     *
     * @param mixed $description the description
     *
     * @return self
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }


    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return ProductCategory
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return ProductCategory
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set latitude
     *
     * @param integer $latitude
     * @return ProductCategory
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return integer
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param integer $longitude
     * @return ProductCategory
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return integer
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    public function __toString()
    {
        return $this->getTitle() ? : 'n/a';
    }

    /**
     * Gets the value of title.
     *
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the value of title.
     *
     * @param mixed $title the title
     *
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    public function isNew()
    {
        return $this->id == null ? true : false;
    }

    /**
     * Gets the value of slug.
     *
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Sets the value of slug.
     *
     * @param mixed $slug the slug
     *
     * @return self
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    public function getRoute()
    {
        return 'dealer_view';
    }

    public function getRouteParams($locale = 'bg', $params = array())
    {
        $entity = null;
        foreach ($this->getTranslations() as $key => $translationEntity) {
            if ($translationEntity->getLocale() == $locale) {
                $entity = $translationEntity;
            }
        }

        if ($entity == null) {
            $entity = $this->getSlug();
        }

        return array_merge(array('slug' => $entity->getSlug()), $params);
    }

    /**
    * Get simpleDescription
    * @return
    */
    public function getSimpleDescription()
    {
        return $this->simpleDescription;
    }

    /**
    * Set simpleDescription
    * @return $this
    */
    public function setSimpleDescription($simpleDescription)
    {
        $this->simpleDescription = $simpleDescription;
        return $this;
    }

    /**
    * Get image
    * @return
    */
    public function getImage()
    {
        return $this->image;
    }

    /**
    * Set image
    * @return $this
    */
    public function setImage($image)
    {
        $this->image = $image;
        return $this;
    }

    /**
    * Get pinDescription
    * @return
    */
    public function getPinDescription()
    {
        return $this->pinDescription;
    }

    /**
    * Set pinDescription
    * @return $this
    */
    public function setPinDescription($pinDescription)
    {
        $this->pinDescription = $pinDescription;
        return $this;
    }

    /**
    * Get isContact
    * @return
    */
    public function getIsContact()
    {
        return $this->isContact;
    }

    /**
    * Set isContact
    * @return $this
    */
    public function setIsContact($isContact)
    {
        $this->isContact = true;
        return $this;
    }

    /**
     * Gets the value of rank.
     *
     * @return integer
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * Sets the value of rank.
     *
     * @param integer $rank the rank
     *
     * @return self
     */
    public function setRank($rank)
    {
        $this->rank = $rank;

        return $this;
    }

    /**
     * Gets the value of notInDistributors.
     *
     * @return mixed
     */
    public function getNotInDistributors()
    {
        return $this->notInDistributors;
    }

    /**
     * Sets the value of notInDistributors.
     *
     * @param mixed $notInDistributors the not in distributors
     *
     * @return self
     */
    public function setNotInDistributors($notInDistributors)
    {
        $this->notInDistributors = $notInDistributors;

        return $this;
    }
}
