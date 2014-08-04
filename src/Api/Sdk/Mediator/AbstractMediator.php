<?php
/**
 * Author: Florent Coquel
 * Date: 31/10/13
 */

namespace Api\Sdk\Mediator;

use Psr\Log\LoggerInterface;

class AbstractMediator implements MediatorInterface
{
    protected $logger = null;
    private $colleagues;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Sets the colleague list
     *
     * @param array $colleagueList
     *
     * @throws \Exception
     */
    public function setColleagueList(array $colleagueList)
    {
        foreach ($colleagueList as $colleague) {
            if (!$colleague instanceof ColleagueInterface) {
                throw new \Exception(__CLASS__ . ": Wrong given connectors list");
            }
            $this->addColleague($colleague);
        }
    }

    /**
     * Adds the given colleague to the current mediator
     *
     * @param ColleagueInterface $colleague
     */
    public function addColleague(ColleagueInterface $colleague)
    {
        $colleagueName = $this->getName($colleague);
        if (!isset($this->colleague[$colleagueName])) {
            $this->colleagues[$colleagueName] = $colleague;
            $colleague->setMediator($this);
            $this->logger->debug("Add colleague : " . $colleagueName);
        }
    }

    /**
     * @param ColleagueInterface $colleague
     *
     * @throws \Exception
     */
    protected function getName(ColleagueInterface $colleague)
    {
        throw new \Exception(__CLASS__ . ": You must implement getName() method in " . $colleague);
    }

    /**
     * Retrieve the connector matching the given name
     *
     * @param string $colleagueName
     *
     * @return SdkInterface
     * @throws \Exception
     */
    public function getColleague($colleagueName)
    {
        if (!isset($this->colleagues[$colleagueName])) {
            throw new \Exception(__CLASS__ . ": " . $colleagueName . " colleague is not in my list !");
        }
        $this->logger->debug("Call colleague : " . $colleagueName);

        return $this->colleagues[$colleagueName];
    }

    public function getColleagues()
    {
        return $this->colleagues;
    }
}
