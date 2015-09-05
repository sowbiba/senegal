<?php

namespace Senegal\ApiBundle\Listener\Entity;

use Senegal\ApiBundle\Entity\Version;
use Senegal\ApiBundle\Workflow\Versioning\VersioningWorkflow;

class VersionListener
{
    /**
     * @param Version $version
     */
    public function postLoad(Version $version)
    {
        $version->setFiniteState(VersioningWorkflow::getStateByProperties(
            $version->getUpdatable(),
            $version->getVisible(),
            $version->getZone()->getSlug())
        );
    }
}
