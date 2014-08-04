<?php
/**
 * Author: Florent Coquel
 * Date: 30/10/13
 */

namespace Api\Sdk\Contract\Connector;

use Api\Sdk\Connector\AbstractConnector;
use Api\Sdk\Document\Query\DocumentQuery;
use Api\Sdk\Model\Contract;
use Api\Sdk\Model\Revision;
use Api\Sdk\Query\QueryInterface;
use Api\Sdk\Query\SortQuery;
use Api\Sdk\Revision\Query\RevisionQuery;

class ContractConnector extends AbstractConnector implements ContractConnectorInterface
{

    public function getProductLine(Contract $contract)
    {
        return $this->getMediator()->getColleague("productLine")->getById($contract->getProductLineId());
    }

    public function getDocuments(Contract $contract, QueryInterface $query = null)
    {
        $filters = null === $query ? array('contract' => $contract) : array_merge(array('contract' => $contract), $query->toArray());

        $query = new SortQuery(new DocumentQuery($filters), [['releasedAt', 'DESC']]);

        return $this->getMediator()->getColleague("document")->getCollection($query);
    }

    public function getRevisions(Contract $contract)
    {
        $revisionQuery = new RevisionQuery();
        $revisionQuery->filterContract($contract);

        $query = new SortQuery($revisionQuery, [['createdAt', 'DESC']]);

        return $this->getMediator()->getColleague("revision")->getCollection($query);
    }

    public function isMirror(Contract $contract)
    {
        return $this->getConnectorToUse("isMirror")->isMirror($contract);
    }

    public function getRevisionByNumber(Contract $contract, $revisionNumber)
    {
        return $this->getMediator()->getColleague("revision")->getRevisionForContractWithNumber($contract, $revisionNumber);
    }

    /**
     * This method is use for the legacy notation edit page
     *
     * @param $notationId
     */
    public function getContractIdByNotationId($notationId)
    {
        return $this->getConnectorToUse("getContractIdByNotationId")->getContractIdByNotationId($notationId);
    }

    /**
     * Released a contract
     *
     * @param Contract $contract
     * @param Revision $revision
     */
    public function release(Contract $contract)
    {
        if (null === $contract->getPublishedRevision()) {
            return null;
        }

        $this->getConnectorToUse("release")->release($contract);
    }

    /**
     * Returns identifiants of inherited fields in a contract
     *
     * @param \Api\Sdk\Model\Contract $contract The contract
     *
     * @return array
     */
    public function getInheritedFieldsIds(Contract $contract)
    {
        return $this->getConnectorToUse('getInheritedFieldsIds')->getInheritedFieldsIds($contract);
    }

    private function compareObjects($oldChapter, $newChapter) {
        return $oldChapter->getId() - $newChapter->getId();
    }

    /**
     * Link chapters to a contract
     *
     * @param \Api\Sdk\Model\Contract $contract
     * @param array $chapters \Api\Sdk\Model\Chapter[]
     *
     * @return bool|array True when success, false when error or an array of
     *                    errors messages when integration rules errors
     */
    public function linkChapters(Contract $contract, array $chapters)
    {
        $oldInheritedChapters = $contract->getInheritedChapters();

        if(count($chapters) > count($oldInheritedChapters)) {
            $newInheritedChapters = array_udiff($chapters, $oldInheritedChapters, array($this, 'compareObjects'));
        } elseif(count($chapters) < count($oldInheritedChapters)) {
            $newInheritedChapters = array_udiff($oldInheritedChapters, $chapters, array($this, 'compareObjects'));
        } else {
            $newInheritedChapters = array();
        }

        // if no changes, do nothing
        if(!count($newInheritedChapters)) {
            return true;
        }

        $doLinks  = true;
        $conflicts   = $this->getConnectorToUse("getInheritanceChapterConflicts")->getInheritanceChapterConflicts($contract, $chapters);

        if(sizeof($conflicts) > 0){
            if(isset($conflicts["error"]) && sizeof($conflicts["error"]) > 0){
                $doLinks = false;
            }
        }

        if($doLinks){
            if($this->getConnectorToUse("linkChapters")->linkChapters($contract, $chapters)){
                return isset($conflicts["warning"]) && sizeof($conflicts["warning"]) > 0 ? $conflicts : true;
            }
        } else {
            return $conflicts;
        }
    }

    /**
     * Return the contract's inherited chapters
     *
     * @param \Api\Sdk\Model\Contract $contract
     *
     * @return array Chapters values in an array of array
     */
    public function getInheritedChapters(Contract $contract)
    {
        return $this->getConnectorToUse("getInheritedChapters")->getInheritedChapters($contract);
    }

    /**
     * Set a parent to a contract
     *
     * @param \Api\Sdk\Model\Contract $contract
     * @param \Api\Sdk\Model\Contract $parent
     */
    public function setParent(Contract $contract, Contract $parent)
    {
        $this->getConnectorToUse('setParent')->setParent($contract, $parent);
    }

    /**
     * Update the contract funds
     *
     * @param \Api\Sdk\Model\Contract $contract
     * @param bool $inheritFunds
     */
    public function inheritFunds(Contract $contract, $inheritFunds)
    {
        //if unchanged, do nothing
        if(false == ($contract->inheritsFunds() === $inheritFunds)) {
            $this->getConnectorToUse("inheritFunds")->inheritFunds($contract, $inheritFunds);
        }
    }

    /**
     * Update the contract documents
     *
     * @param \Api\Sdk\Model\Contract $contract
     * @param bool $inheritDocs
     */
    public function inheritDocuments(Contract $contract, $inheritDocs)
    {
        //if unchanged or contract product line isRevisionable, do nothing
        if (false == ($contract->inheritsDocuments() === $inheritDocs)) {
            $this->getConnectorToUse("inheritDocuments")->inheritDocuments($contract, $inheritDocs);
        }
    }

    /**
     * @see \Api\Sdk\Contract\Connector\Doctrine\ContractDoctrineConnector::getCurrent
     */
    public function getCurrent(Contract $contract)
    {
        return $this->getConnectorToUse("getCurrent")->getCurrent($contract);
    }
}
