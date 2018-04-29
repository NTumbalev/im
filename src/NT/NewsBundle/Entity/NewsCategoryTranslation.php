<?php
/**
 * This file is part of the ApplicationNTNewsBundle.
 *
 * (c) Nikolay Tumbalev <n.tumbalev@nt.bg>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NT\NewsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Entity\MappedSuperclass\AbstractTranslation;

/**
 * NT\NewsBundle\Entity\NewsCategoryTranslation.php
 *
 * @ORM\Entity
 * @ORM\Table(name="news_categories_i18n", uniqueConstraints={@ORM\UniqueConstraint(name="lookup_unique_idx", columns={
 *     "locale", "object_id"
 *   }), @ORM\UniqueConstraint(name="slug_unique_idx", columns={"slug", "locale"})}
 * )
 */
class NewsCategoryTranslation extends AbstractTranslation
{
    /**
     * @ORM\ManyToOne(targetEntity="NT\NewsBundle\Entity\NewsCategory", inversedBy="translations")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $object;

    /**
     * @Gedmo\Versioned
     * @Gedmo\Slug(fields={"title"}, separator="-", updatable=false, unique=false)
     * @ORM\Column(name="slug", type="string", length=255, nullable=true)
     */
    protected $slug;

    /**
     * @var string
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="title", type="string", length=250, nullable=true)
     */
    protected $title;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id",  onDelete="SET NULL")
     */
    protected $image;

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

    /**
     * Gets the value of title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the value of title.
     *
     * @param string $title the title
     *
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the value of Description
     *
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the value of Description
     *
     * @param mixed description
     *
     * @return self
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the value of Image
     *
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set the value of Image
     *
     * @param mixed image
     *
     * @return self
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }
}
