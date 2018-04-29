<?php

/**
 * This file is part of the <name> project.
 *
 * (c) <yourname> <youremail>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\Sonata\MediaBundle\Entity;

use Sonata\MediaBundle\Entity\BaseGallery as BaseGallery;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * This file has been generated by the Sonata EasyExtends bundle ( http://sonata-project.org/bundles/easy-extends )
 *
 * References :
 *   working with object : http://www.doctrine-project.org/projects/orm/2.0/docs/reference/working-with-objects/en
 *
 * @author <yourname> <youremail>
 */
class Gallery extends BaseGallery
{
    use \A2lix\TranslationFormBundle\Util\Gedmo\GedmoTranslatable;
    /**
     * @var integer $id
     */
    protected $id;

    protected $title;

    protected $customDescription;

    protected $translations;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Gets the value of title.
     *
     * @return mixed
     */
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

    public function getName()
    {
        return $this->getTitle();
    }

    /**
     * Gets the value of customDescription.
     *
     * @return mixed
     */
    public function getCustomDescription()
    {
        return $this->customDescription;
    }

    /**
     * Sets the value of customDescription.
     *
     * @param mixed $customDescription the custom description
     *
     * @return self
     */
    public function setCustomDescription($customDescription)
    {
        $this->customDescription = $customDescription;

        return $this;
    }
}