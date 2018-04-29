<?php
namespace NT\DealersBundle\Entity;

use Doctrine\ORM\EntityRepository;

class DealersRepository extends EntityRepository
{
    use \NT\PublishWorkflowBundle\PublishWorkflowQueryBuilderTrait;

    /**
     * Find all by locale
     * @var locale string
     * @var limit integer
     * @var offset integer
     * @return array
     */
    public function findAllByLocale($locale, $limit = null, $offset = null)
    {
        $qb = $this->getPublishWorkFlowQueryBuilder()
            ->leftJoin('c.translations', 't')
            ->andWhere('t.locale = :locale')
            ->andWhere('c.notInDistributors = 0')
            ->andWhere('t.title IS NOT NULL')
            ->setParameter('locale', $locale)
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->addOrderBy('c.rank', 'ASC');
        $query = $qb->getQuery();

        return $query->getResult();
    }

    /**
     * Find all on contacts by locale
     * @var locale string
     * @var limit integer
     * @var offset integer
     * @return array
     */
    public function findAllOnContactsByLocale($locale, $limit = null, $offset = null)
    {
        $qb = $this->getPublishWorkFlowQueryBuilder()
            ->leftJoin('c.translations', 't')
            ->andWhere('t.locale = :locale')
            ->andWhere('c.isContact = 1')
            ->andWhere('t.title IS NOT NULL')
            ->setParameter('locale', $locale)
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->addOrderBy('c.title');
        $query = $qb->getQuery();

        return $query->getResult();
    }

    /**
     * Find Dealer by given slug and locale
     * @var locale string
     * @var slug string
     * @return mixed
     */
    public function findOneBySlugAndLocale($slug, $locale)
    {
        $qb = $this->getPublishWorkFlowQueryBuilder(null)
            ->leftJoin('c.translations', 't')
            ->andWhere('t.locale = :locale')
            ->andWhere('t.slug = :slug')
            ->andWhere('t.title IS NOT NULL')
            ->setParameter('locale', $locale)
            ->setParameter('slug', $slug)
            ->setMaxResults(1);
        $query = $qb->getQuery();

        $result = $query->getOneOrNullResult();

        return $result;
    }
}
