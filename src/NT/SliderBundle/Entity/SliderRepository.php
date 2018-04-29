<?php
namespace NT\SliderBundle\Entity;

use Doctrine\ORM\EntityRepository;
use NT\PublishWorkflowBundle\PublishWorkflowQueryBuilderTrait;

class SliderRepository extends EntityRepository
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
            ->andWhere('t.image is not null')
            ->setParameter('locale', $locale)
            ->setParameter('now', new \DateTime())
            ->orderBy('c.rank')
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
}
