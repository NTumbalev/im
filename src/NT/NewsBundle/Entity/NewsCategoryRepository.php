<?php
/**
 * This file is part of the NTNewsBundle.
 *
 * (c) Nikolay Tumbalev <n.tumbalev@nt.bg>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NT\NewsBundle\Entity;

use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use NT\PublishWorkflowBundle\PublishWorkflowQueryBuilderTrait;

/**
 *  Repository helps getting posts from the db
 *
 * @package NTNewsBundle
 * @author  Nikolay Tumbalev <n.tumbalev@nt.bg>
 */
class NewsCategoryRepository extends NestedTreeRepository
{
    use PublishWorkflowQueryBuilderTrait;

    /**
     * Find all posts by locale
     * @var string  $locale
     * @var integer $limit
     * @var integer $offset
     * @var boolean $isHomepage
     * @var string  $type
     * @return array
     */
    public function findAllByLocale($locale, $limit = null, $offset = null)
    {
        $qb = $this->getPublishWorkFlowQueryBuilder();
        $qb
            ->leftJoin('c.translations', 't')
            ->andWhere('t.locale = :locale')
            ->andWhere('t.title IS NOT NULL')
            ->setParameter('locale', $locale)
            ->setParameter('now', new \DateTime())
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->orderBy('c.rank', 'ASC');

        $query = $qb->getQuery();

        return $query->getResult();
    }

    /**
     * Find a post by slug and locale
     * @var string $slug
     * @var string $locale
     * @return array
     */
    public function findOneBySlugAndLocale($slug, $locale)
    {
        $qb = $this->getPublishWorkFlowQueryBuilder();
        $qb
            ->leftJoin('c.translations', 't')
            ->andWhere('t.locale = :locale')
            ->andWhere('t.slug = :slug')
            ->andWhere('t.title IS NOT NULL')
            ->setParameter('slug', $slug)
            ->setParameter('locale', $locale)
            ->setParameter('now', new \DateTime());
        $query = $qb->getQuery();

        return $query->getOneOrNullResult();
    }

    /**
     * Get categories listing query
     * @var    parentCategoryId integer
     * @var    locale string
     * @return array
     */
    public function getCategoriesListingQuery($parentCategoryId, $locale, $page, $pageSize)
    {
        $qb = $this->getPublishWorkFlowQueryBuilder();
        $qb->leftJoin('c.translations', 't');

        if ($parentCategoryId === null) {
            $qb->andWhere('c.parent IS NULL');
        } else {
            $qb
                ->andWhere('c.parent = :parentCategoryId')
                ->setParameter('parentCategoryId', $parentCategoryId)
            ;
        }
        $qb
            ->andWhere('t.locale = :locale')
            ->andWhere('t.title IS NOT NULL')
            ->setParameter('locale', $locale)
            ->setParameter('now', new \DateTime())
            ->orderBy('c.lft', 'ASC')
            ->setFirstResult($pageSize * ($page-1))
            ->setMaxResults($pageSize);

        return $qb->getQuery();
    }

    /**
     * Find all main categories by locale
     * @var    locale string
     * @var    limit  integer
     * @var    offset integer
     * @return array
     */
    public function findAllMainCategoriesByLocale($locale, $limit = null, $offset = null)
    {
        $qb = $this->getPublishWorkFlowQueryBuilder();
        $qb
            ->leftJoin('c.translations', 't')
            ->andWhere('c.parent IS NULL')
            ->andWhere('t.locale = :locale')
            ->andWhere('t.title IS NOT NULL')
            ->setParameter('locale', $locale)
            ->setParameter('now', new \DateTime())
            ->setMaxResults($limit)
            ->setFirstResult($offset)
        ->orderBy('c.lft', 'ASC');
        $query = $qb->getQuery();

        return $query->getResult();
    }

    /**
     * Find all children categories by parent id and by locale
     * @var    parentCategoryId integer
     * @var    locale string
     * @var    limit  integer
     * @var    offset integer
     * @return array
     */
    public function findAllChildrenCategoriesByLocale($parentCategoryId, $locale, $limit = null, $offset = null)
    {
        $qb = $this->getPublishWorkFlowQueryBuilder();
        $qb
            ->leftJoin('c.translations', 't')
            ->andWhere('c.parent = :parentCategoryId')
            ->andWhere('t.locale = :locale')
            ->andWhere('t.title IS NOT NULL')
            ->setParameter('locale', $locale)
            ->setParameter('now', new \DateTime())
            ->setParameter('parentCategoryId', $parentCategoryId)
            ->setMaxResults($limit)
            ->setFirstResult($offset)
        ->orderBy('c.lft', 'ASC');
        $query = $qb->getQuery();

        return $query->getResult();
    }
}
