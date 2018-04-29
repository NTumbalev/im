<?php
/**
 * This file is part of the NTPublishWorkflow.
 *
 * (c) Nikolay Tumbalev <ntumbalev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NT\PublishWorkflowBundle;

/**
 *  Interface for allowing publish
 *
 * @package NTPublishWorkflow
 * @author  Nikolay Tumbalev <ntumbalev@gmail.com>
 */
interface PublishWorkflowInterface
{
    /**
     * Gets PublishWorkflow entity
     * {@link \NT\PublishWorkflowBundle\Entity\PublishWorkflow object}.
     *
     * @return integer
     */
    public function getPublishWorkflow();

    /**
     * Sets PublishWorkflow entity
     * {@link \NT\PublishWorkflowBundle\Entity\PublishWorkflow object}.
     *
     * @param integer $publishWorkflow
     *
     * @return self
     */
    public function setPublishWorkflow($publishWorkflow);
}
