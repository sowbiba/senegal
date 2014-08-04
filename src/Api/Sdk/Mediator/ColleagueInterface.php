<?php
/**
 * Author: Florent Coquel
 * Date: 31/10/13
 */

namespace Api\Sdk\Mediator;

interface ColleagueInterface
{
    public function setMediator(MediatorInterface $mediator);

    public function getMediator();
}
