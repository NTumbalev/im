<?php
namespace NT\NewsBundle\Entity;

use Doctrine\ORM\EntityRepository;
use NT\PublishWorkflowBundle\PublishWorkflowQueryBuilderTrait;

class NewsRepository extends EntityRepository
{
    use PublishWorkflowQueryBuilderTrait;
    /**
     * Find all by locale
     * @var locale string
     * @var limit integer
     * @var offset integer
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
            ->addOrderBy('c.isTop', 'DESC')
            ->addOrderBy('c.publishedDate', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset);
        $query = $qb->getQuery();

        return $query->getResult();
    }

    /**
     * Find one by slug and locale
     * @var slug string
     * @var locale string
     * @return \NT\ContentBundle\Entity\Content|null
     */
    public function findOneBySlugAndLocale($slug, $locale)
    {
        $qb = $this->getPublishWorkFlowQueryBuilder(null);
        $qb
            ->leftJoin('c.translations', 't')
            ->andWhere('t.locale = :locale')
            ->andWhere('t.slug = :slug')
            ->andWhere('t.title IS NOT NULL')
            ->setParameter('locale', $locale)
            ->setParameter('slug', $slug)
            ->setParameter('now', new \DateTime());
        $query = $qb->getQuery();
        $results = $query->getResult();

        if (count($results)) {
            return $results[0];
        }

        return;
    }

    /**
     * Find all on homepage by locale
     * @var locale string
     * @var limit integer
     * @var offset integer
     * @return array
     */
    public function findAllOnHomepageByLocale($locale, $limit = null, $offset = null)
    {
        $qb = $this->getPublishWorkFlowQueryBuilder();
        $qb
            ->leftJoin('c.translations', 't')
            ->andWhere('t.locale = :locale')
            ->andWhere('c.isHomepage = 1')
            ->andWhere('t.title IS NOT NULL')
            ->setParameter('locale', $locale)
            ->setParameter('now', new \DateTime())
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->addOrderBy('c.isTop', 'DESC')
            ->addOrderBy('c.publishedDate', 'DESC');
        $query = $qb->getQuery();

        return $query->getResult();
    }

    /**
     * Query for posts listing
     * @var postsCategoryId integer
     * @var locale string
     * @var limit integer
     * @var offset integer
     * @return array
     */
    public function getPostsListingQuery($postsCategoryId, $locale, $page, $pageSize)
    {
        $qb = $this->getPublishWorkFlowQueryBuilder(null);
        $qb
            ->leftJoin('c.translations', 't')
            ->leftJoin('c.postsCategories', 'cat')
            ->andWhere('t.locale = :locale')
            ->andWhere('t.slug IS NOT NULL');
            if ($postsCategoryId != null) {
                $qb
                ->andWhere('cat.id = :postsCategoryId')
                ->setParameter('postsCategoryId', $postsCategoryId)
                ;
            }
        $qb
            ->setParameter('locale', $locale)
            ->addOrderBy('c.isTop', 'DESC')
            ->addOrderBy('c.publishedDate', 'DESC')
            ->setFirstResult($pageSize * ($page-1))
            ->setMaxResults($pageSize);

        return $qb->getQuery();
    }
}
