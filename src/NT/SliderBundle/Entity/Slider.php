<?php
namespace NT\SliderBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContextInterface;

use NT\PublishWorkflowBundle\PublishWorkflowInterface;
use NT\PublishWorkflowBundle\PublishWorkflowTrait;

/**
 * Slider's entity
 *
 * @ORM\Table(name="slider")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="SliderRepository")
 * @Gedmo\Loggable
 * @Assert\Callback(methods={"checkImage"})
 *
 */
class Slider
{
    use \A2lix\TranslationFormBundle\Util\Gedmo\GedmoTranslatable;
    use PublishWorkflowTrait;

    /** @ORM\Id @ORM\GeneratedValue @ORM\Column(type="integer") */
    protected $id;

    /**
     * @var string
     * @Gedmo\Translatable
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    protected $title;

    /**
     * @var string
     * @Gedmo\Translatable
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @var string
     * @Gedmo\Translatable
     * @ORM\Column(name="url", type="string", length=255, nullable=true)
     */
    protected $url;

    /**
     * @var string
     * @Gedmo\Translatable
     * @ORM\Column(name="target", type="string", length=255, nullable=true)
     */
    protected $target;

    /**
     * @Gedmo\Versioned
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id",  onDelete="SET NULL")
     */
    protected $image;

    /**
     * @var string
     * @Gedmo\Translatable
     * @ORM\Column(name="alt", type="string", length=255, nullable=true)
     */
    protected $alt;

    /**
     * @var integer
     * @Gedmo\Sortable
     * @Gedmo\Versioned
     * @ORM\Column(name="rank", type="integer")
     */
    protected $rank;

    /**
     * @var string
     *
     * @Gedmo\Translatable
     * @Gedmo\Versioned
     * @ORM\Column(name="button_title", type="string", length=250, nullable=true)
     */
    protected $buttonTitle;

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
     * @ORM\OneToMany(targetEntity="NT\SliderBundle\Entity\SliderTranslation", mappedBy="object", cascade={"persist", "remove"}, indexBy="locale")
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

    public function getTitle()
    {
        if($this->title === NULL || strlen($this->title) == 0) {
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

    /**
     * Get the value of Description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the value of Description
     *
     * @param string description
     *
     * @return self
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    public function getAlt()
    {
        return $this->alt;
    }

    public function setAlt($value)
    {
        $this->alt = $value;
    }

    /**
     * Set rank
     *
     * @param integer $rank
     * @return Banner
     */
    public function setRank($rank)
    {
        $this->rank = $rank;

        return $this;
    }

    /**
     * Get rank
     *
     * @return boolean
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * Set target
     *
     * @param string $target
     * @return Banner
     */
    public function setTarget($target)
    {
        $this->target = $target;

        return $this;
    }

    /**
     * Get target
     *
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
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


    public function __toString()
    {
        return $this->getTitle() ?: 'n/a';
    }

    /**
     * Gets the value of url.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Sets the value of url.
     *
     * @param string $url the url
     *
     * @return self
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
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
    * Get buttonTitle
    * @return
    */
    public function getButtonTitle()
    {
        return $this->buttonTitle;
    }

    /**
    * Set buttonTitle
    * @return $this
    */
    public function setButtonTitle($buttonTitle)
    {
        $this->buttonTitle = $buttonTitle;
        return $this;
    }

    /**
     * Check if the uploaded banner fit to the associated zone
     *
     * @param ExecutionContextInterface $context
     */
    public function checkImage(ExecutionContextInterface $context)
    {
        foreach ($this->getTranslations() as $translation) {
            if (!$translation->getImage() && $translation->getTitle()) {
                $context->addViolationAt('image', "");
            }
        }
    }
}
