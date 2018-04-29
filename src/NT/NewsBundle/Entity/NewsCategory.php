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
use NT\NewsBundle\Entity\News as BaseNews;
use Doctrine\Common\Collections\ArrayCollection;
use NT\PublishWorkflowBundle\PublishWorkflowTrait;
use NT\PublishWorkflowBundle\PublishWorkflowInterface;
use NT\SEOBundle\SeoAwareInterface;
use NT\SEOBundle\SeoAwareTrait;

/**
 * @ORM\Table(name="news_categories")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="NewsCategoryRepository")
 * @Gedmo\Tree(type="nested")
 *
 */
class NewsCategory implements PublishWorkflowInterface, SeoAwareInterface
{
    use \A2lix\TranslationFormBundle\Util\Gedmo\GedmoTranslatable;
    use PublishWorkflowTrait;
    use SeoAwareTrait;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
    * @Gedmo\Versioned
    * @Gedmo\TreeRoot
    * @ORM\Column(name="root", type="integer", nullable=true)
    */
    protected $root;

    /**
     * @Gedmo\Versioned
     * @Gedmo\TreeLeft
     * @ORM\Column(name="lft", type="integer")
     */
    protected $lft;

    /**
     * @Gedmo\Versioned
     * @Gedmo\TreeLevel
     * @ORM\Column(name="lvl", type="integer")
     */
    protected $lvl;

    /**
     * @Gedmo\Versioned
     * @Gedmo\TreeRight
     * @ORM\Column(name="rgt", type="integer")
     */
    protected $rgt;

    /**
     * @Gedmo\Versioned
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="NewsCategory", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="NewsCategory", mappedBy="parent")
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    protected $children;

    /**
     * @Gedmo\Versioned
     * @Gedmo\Translatable
     * @ORM\Column(name="slug", type="string", length=255, nullable=true)
     */
    protected $slug;

    /**
     * @var string
     *
     * @Gedmo\Translatable
     * @Gedmo\Versioned
     * @ORM\Column(name="title", type="string", length=250, nullable=true)
     */
    protected $title;

    /**
     * @Gedmo\Versioned
     * @Gedmo\Translatable
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @var integer
     * @Gedmo\Sortable
     * @Gedmo\Versioned
     * @ORM\Column(name="rank", type="integer")
     */
    protected $rank;

    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id",  onDelete="SET NULL")
     */
    protected $image;

    /**
     * @ORM\ManyToMany(targetEntity="News", mappedBy="postsCategories")
     */
    protected $posts;

    /**
     * @ORM\OneToMany(targetEntity="NT\NewsBundle\Entity\NewsCategoryTranslation", mappedBy="object", cascade={"persist", "remove"}, indexBy="locale")
     */
    protected $translations;

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

    function __construct() {
        $this->translations = new ArrayCollection();
    }

    public function getRoute()
    {
        if ($this->getPosts()->filter(function($item){
            if ($item->getPublishWorkflow()->getIsActive() == true) {
                return true;
            } else {
                return false;
            }
        })) {
            return 'posts_categories_category_view';
        } else {
            return 'posts_categories_list';
        }
    }

    public function getRouteParams($params = array())
    {
        return array_merge(array('categorySlug' => $this->getSlug()), $params);
    }

    /**
     * Gets the value of id.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Gets the value of root.
     *
     * @return mixed
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * Sets the value of root.
     *
     * @param mixed $root the root
     *
     * @return self
     */
    public function setRoot($root)
    {
        $this->root = $root;

        return $this;
    }

    /**
     * Gets the value of lft.
     *
     * @return mixed
     */
    public function getLft()
    {
        return $this->lft;
    }

    /**
     * Sets the value of lft.
     *
     * @param mixed $lft the lft
     *
     * @return self
     */
    public function setLft($lft)
    {
        $this->lft = $lft;

        return $this;
    }

    /**
     * Gets the value of lvl.
     *
     * @return mixed
     */
    public function getLvl()
    {
        return $this->lvl;
    }

    /**
     * Sets the value of lvl.
     *
     * @param mixed $lvl the lvl
     *
     * @return self
     */
    public function setLvl($lvl)
    {
        $this->lvl = $lvl;

        return $this;
    }

    /**
     * Gets the value of rgt.
     *
     * @return mixed
     */
    public function getRgt()
    {
        return $this->rgt;
    }

    /**
     * Sets the value of rgt.
     *
     * @param mixed $rgt the rgt
     *
     * @return self
     */
    public function setRgt($rgt)
    {
        $this->rgt = $rgt;

        return $this;
    }

    /**
     * Gets the value of parent.
     *
     * @return mixed
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Sets the value of parent.
     *
     * @param mixed $parent the parent
     *
     * @return self
     */
    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Gets the value of children.
     *
     * @return mixed
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Sets the value of children.
     *
     * @param mixed $children the children
     *
     * @return self
     */
    public function setChildren($children)
    {
        $this->children = $children;

        return $this;
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
     * Gets the value of posts.
     *
     * @return mixed
     */
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * Sets the value of posts.
     *
     * @param mixed $posts the posts
     *
     * @return self
     */
    public function setPosts($posts)
    {
        foreach ($posts as $n) {
            $this->addPosts($n);
        }

        return $this;
    }

    public function addPosts($n)
    {
        if (!$this->posts->contains($n)) {
            $n->setPostsCategory($this);
            $this->posts->add($n);
        }
    }

    public function removePosts($n)
    {
        if ($this->posts->contains($n)) {
            $n->setPostsCategory(NULL);
            $this->posts->removeElement($n);
        }
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

    public function __toString()
    {
        return $this->getTitle() ? $this->getTitle() : 'N/A';
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


    /**
     * Get the value of Created At
     *
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set the value of Created At
     *
     * @param mixed createdAt
     *
     * @return self
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get the value of Updated At
     *
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set the value of Updated At
     *
     * @param mixed updatedAt
     *
     * @return self
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

}
