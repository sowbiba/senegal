<?php
/**
 * Author: Florent Coquel
 * Date: 31/10/13
 */

namespace Api\Sdk\Mediator;

interface MediatorInterface
{

    /**
     * Sets the colleague list
     *
     * @param array $colleagueList
     *
     * @throws \Exception
     */
    public function setColleagueList(array $colleagueList);

    /**
     * Adds the given colleague to the current mediator
     *
     * @param ColleagueInterface $colleague
     */
    public function addColleague(ColleagueInterface $colleague);

    /**
     * Retrieve the connector matching the given name
     *
     * @param string $colleagueName
     */
    public function getColleague($colleagueName);
}
