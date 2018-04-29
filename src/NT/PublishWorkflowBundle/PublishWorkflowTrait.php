<?php
/**
 * This file is part of the NTPublishWorkflowBundle.
 *
 * (c) Nikolay Tumbalev <ntumbalev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NT\PublishWorkflowBundle;

/**
 *  A plugin making connection with the PublishWorkflow entity
 *
 * @package NTPublishWorkflowBundle
 * @author  Nikolay Tumbalev <ntumbalev@gmail.com>
 */

trait PublishWorkflowTrait
{
    /**
     * A variable making connection PublishWorkflow entity.
     * {@link \NT\PublishWorkflowBundle\Entity\PublishWorkflow object}
     *
     * @var integer
     *
     * @ORM\OneToOne(targetEntity="\NT\PublishWorkflowBundle\Entity\PublishWorkflow", cascade={"remove","persist"})
     */
    protected $publishWorkflow;

    /**
     * Gets PublishWorkflow entity
     * {@link \NT\PublishWorkflowBundle\Entity\PublishWorkflow object}.
     *
     * @return integer
     */
    public function getPublishWorkflow()
    {
        return $this->publishWorkflow;
    }

    /**
     * Sets PublishWorkflow entity
     * {@link \NT\PublishWorkflowBundle\Entity\PublishWorkflow object}.
     *
     * @param integer $publishWorkflow
     *
     * @return self
     */
    public function setPublishWorkflow($publishWorkflow)
    {
        $this->publishWorkflow = $publishWorkflow;

        return $this;
    }
}
