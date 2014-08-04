<?php
namespace Api\Sdk\Contract;

use Api\Sdk\AbstractSdk;
use Api\Sdk\Contract\Query\ContractQuery;
use Api\Sdk\Contract\Query\ContractSortQuery;
use Api\Sdk\Model\Contract;
use Api\Sdk\Model\Chapter;
use Api\Sdk\Model\Document;
use Api\Sdk\Model\ProductLine;
use Api\Sdk\Model\Revision;
use Api\Sdk\Query\QueryInterface;
use Api\Sdk\QueryableSdkInterface;
use Api\Sdk\SdkException;

/**
 * This class lists all the high-level methods for Contract objects.
 *
 * This class can only use POPO classes (\Api\Sdk\Model)
 * To use this class you have to initialize a connector (\Api\Sdk\Connector) and pass it to the constructor
 * These connectors work with POPO objects, to save an object you have to pass it
 * Only connectors can use entities (\Api\SdkBundle\Entity)
 *
 * Class ContractSdk
 * @package Api\Sdk\Contract
 * @author Florent Coquel
 * @since 17/09/13
 */
class ContractSdk extends AbstractSdk implements QueryableSdkInterface
{
    /**
     * Returns the contract matching the given id
     *
     * @param int $id
     *
     * @return null|Contract
     * @throws \BadMethodCallException
     */
    public function getById($id)
    {
        if (!is_int($id)) {
            throw new \BadMethodCallException(__METHOD__ . ' expects an int as parameter, ' . gettype($id) . ' given');
        }

        $contractData = $this->connector->getById($id);

        return empty($contractData) ? null : new Contract($this, $contractData);
    }

    /**
     * Returns the contracts list matching the given criteria
     *
     * @param QueryInterface $query
     *
     * @return array
     */
    public function getCollection(QueryInterface $query)
    {
        $contractsData = $this->connector->getCollection($query);

        return array_map(function ($contract) {
            return new Contract($this, $contract);
        }, $contractsData);
    }

    /**
     * Returns the number of contracts matching the given criteria
     *
     * @param QueryInterface $query
     *
     * @return int
     */
    public function count(QueryInterface $query)
    {
        return $this->connector->count($query);
    }

    /**
     * Returns the product line the given contract belongs to
     *
     * @see Api\Sdk\Contract\Connector\ContractConnectorInterface::getProductLine()
     *
     * @param Contract $contract
     *
     * @return null|ProductLine
     * @throws \BadMethodCallException
     */
    public function getProductLine(Contract $contract)
    {
        return $this->getMediator()->getColleague("productLine")->getById($contract->getProductLineId());
    }

    /**
     * Returns the Nth revision for the given Contract
     *
     * @see Api\Sdk\Contract\Connector\ContractConnectorInterface::getRevisionForContractWithNumber()
     *
     * @param Contract $contract
     * @param int $revisionNumber
     *
     * @return null|Revision
     * @throws \BadMethodCallException
     */
    public function getRevisionByNumber(Contract $contract, $revisionNumber)
    {
        $revisionData = $this->connector->getRevisionByNumber($contract, $revisionNumber);

        return empty($revisionData) ? null : new Revision($this->getMediator()->getColleague("revision"), $revisionData);
    }

    /**
     * Returns the list of revisions belonging to the contract
     *
     * @see Api\Sdk\Contract\Connector\ContractConnectorInterface::getRevisions()
     *
     * @param \Api\Sdk\Model\Contract $contract
     *
     * @return array                   array of \Api\Sdk\Model\Contract
     * @throws \BadMethodCallException
     */
    public function getRevisions(Contract $contract)
    {
        $revisionsData = $this->connector->getRevisions($contract);

        return array_map(function ($revision) {
            return new Revision($this->getMediator()->getColleague("revision"), $revision);
        }, $revisionsData);
    }

    /**
     * Returns the documents linked to the given contract
     *
     * @param  \Api\Sdk\Model\Contract $contract
     * @return array                   array of \Api\Sdk\Model\Document
     * @throws \BadMethodCallException
     */
    public function getDocuments(Contract $contract, QueryInterface $query =null)
    {
        $documentsData = $this->connector->getDocuments($contract, $query);

        return array_map(function ($document) {
            return new Document($this->getMediator()->getColleague("document"), $document);
        }, $documentsData);
    }

    /**
     * Returns the first contract revision with the given status
     *
     * @param \Api\Sdk\Model\Contract $contract
     * @param int $status
     *
     * @return \Api\Sdk\Model\Revision|null
     *
     * @throws \BadMethodCallException
     */
    public function getRevisionWithStatus(Contract $contract, $status)
    {
        if (!is_int($status)) {
            throw new \BadMethodCallException(__METHOD__ . ' expects an int as parameter, ' . gettype($status) . ' given');
        }

        $revisions = $contract->getRevisions();

        foreach ($revisions as $revision) {
            if ($status === $revision->getStatus()) {
                return $revision;
            }
        }

        return null;
    }

    public function isMirror(Contract $contract)
    {
        return $this->connector->isMirror($contract);
    }

    /**
     * Released a contract and its potential childs
     *
     * @param Contract $contract
     */
    public function release(Contract $contract)
    {
        //when we release an parent contract, try to do it for each child
        foreach ($contract->getChildren() as $child) {
            $this->connector->release($child);
        }

        $this->connector->release($contract);
    }

    /**
     * @param string $classname
     *
     * @return bool
     */
    public function supports($classname)
    {
        return $classname === 'Api\Sdk\Model\Contract';
    }

    /**
     * @param array $filters
     *
     * @return QueryInterface
     */
    public function getQuery(array $filters = array())
    {
        return new ContractQuery($filters);
    }

    /**
     * @param QueryInterface $query
     * @param array $sorts
     *
     * @return QueryInterface
     */
    public function getSortQuery(QueryInterface $query, array $sorts = array())
    {
        return new ContractSortQuery($query, $sorts);
    }

    /**
     * This method is use for the legacy notation edit page
     *
     * @param $notationId
     *
     * @return Contract
     */
    public function getContractByNotationId($notationId)
    {
        return $this->getById($this->connector->getContractIdByNotationId($notationId));
    }

    /**
     * Return all contract's children
     *
     * @param \Api\Sdk\Model\Contract $contract Contract
     *
     * @return array \Api\Sdk\Model\Contract[]
     */
    public function getChildren(Contract $contract)
    {
        $children = $this->connector->getCollection($this->getQuery(['parent' => $contract]));

        return array_map(function ($child) {
            return new Contract($this, $child);
        }, $children);
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
        return $this->connector->getInheritedFieldsIds($contract);
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
        if(!$contract->isChild() && !empty($chapters)) {
            return $this->createSdkException("Cannot inherit chapters if contract dont have a parent");
        }

        if (true !== $result = $this->connector->linkChapters($contract, $chapters)) {
            return $result;
        }

        if (!$contract->getProductLine()->isRevisionable()) {
            return true;
        }

        $revisionSdk = $this->getMediator()->getColleague('revision');
        $parentRevisions = $contract->getParent()->getRevisions();

        // Update inherited field source of revisions family by calling parent revision
        foreach($parentRevisions as $parentRevision) {
            if(!$revisionSdk->updateFieldsSources($parentRevision)) {
                return false;
            }
        }

        // Update revisions family values by calling parent revision
        foreach($parentRevisions as $parentRevision) {
            if(!$revisionSdk->updateValues($parentRevision, $parentRevision->getValues())) {
                return false;
            }
        }

        return true;
    }

    /**
     * Return the contract's inherited chapters
     *
     * @param \Api\Sdk\Model\Contract $contract
     *
     * @return array \Api\Sdk\Model\Chapter[]
     */
    public function getInheritedChapters(Contract $contract)
    {
        $inheritedChapters = $this->connector->getInheritedChapters($contract);

        return array_map(function ($inheritedChapter) {
            return new Chapter($this->getMediator()->getColleague("chapter"), $inheritedChapter);
        }, $inheritedChapters);

    }

    /**
     * Return parent contract
     *
     * @param \Api\Sdk\Model\Contract $contract
     *
     * @return \Api\Sdk\Model\Contract|null
     */
    public function getParent(Contract $contract)
    {
        return $this->getById($contract->getParentId());
    }

    /**
     * Set a parent to a contract
     *
     * Create automatically a revision for the contract it has not opened revision and the parent has one
     *
     * @param \Api\Sdk\Model\Contract $contract
     * @param \Api\Sdk\Model\Contract $parent
     *
     * @throws \Api\Sdk\SdkException When :
     *     - Product line of the contract is not same than parent chosen
     *     - The parent chosen has already a parent
     *     - The contract has already a parent
     *     - The contract has an opened revision but not the parent chosen (when product line is revisionnable)
     *     - The contract and the parent chosen have opened revision (when product line is revisionnable)
     */
    public function setParent(Contract $contract, Contract $parent)
    {
        if($contract->getProductLineId() !== $parent->getProductLineId()) {
            throw $this->createSdkException('Product line of the contract is not same than parent chosen');
        }

        if($contract->isParent()) {
            throw $this->createSdkException('The parent chosen has already a parent');
        }

        if($contract->isChild()) {
            throw $this->createSdkException(sprintf('The contract has already a parent'));
        }

        if($contract->getProductLine()->isRevisionable()) {
            $contractOpenedRevision = $contract->getOpenedRevision();
            $parentOpenedRevision = $parent->getOpenedRevision();

            if(null !== $contractOpenedRevision && null === $parentOpenedRevision) {
                throw $this->createSdkException('The contract has an opened revision but not the parent chosen');
            }

            if(null !== $contractOpenedRevision && null !== $parentOpenedRevision) {
                throw $this->createSdkException('The contract and the parent chosen have opened revision');
            }

            if(null !== $parentOpenedRevision && null === $contractOpenedRevision) {
                $contract->createRevision();
            }
        }

        $this->connector->setParent($contract, $parent);
    }

    /**
     * Creates a revision to a contract and returns it
     *
     * @param \Api\Sdk\Model\Contract $contract
     *
     * @return \Api\Sdk\Model\Revision
     */
    public function createRevision(Contract $contract)
    {
        $revisionSdk = $this->getMediator()->getColleague('revision');

        return $revisionSdk->create(new Revision($revisionSdk, ['contractId' => $contract->getId()]));
    }

    /**
     * Update the contract funds
     *
     * @param \Api\Sdk\Model\Contract $contract
     * @param bool $inheritFunds
     */
    public function inheritFunds(Contract $contract, $inheritFunds)
    {
        if(!$contract->isChild() && $inheritFunds) {
            throw new SdkException("Cannot inherit funds if contract dont have a parent");
        }

        return $this->connector->inheritFunds($contract, $inheritFunds);
    }

    /**
     * Update the contract documents
     *
     * @param \Api\Sdk\Model\Contract $contract
     * @param bool $inheritDocs
     */
    public function inheritDocuments(Contract $contract, $inheritDocs)
    {
        if(!$contract->isChild() && $inheritFunds) {
            throw new SdkException("Cannot inherit documents if contract dont have a parent");
        }

        //if contract product line isRevisionable, send sdkException and do nothing
        if ($contract->getProductLine()->isRevisionable()) {
            throw $this->createSdkException('Contracts with revisionable productLine already inherits parent documents');
        }

        return $this->connector->inheritDocuments($contract, $inheritDocs);
    }

    /**
     * Returns the current contract if current contract is future
     *
     * @params Contract $contract A future contract
     *
     * @return \Api\Sdk\Model\Contract
     *
     * @throws \Api\Sdk\SdkException When contract is not future
     */
    public function getCurrent(Contract $contract)
    {
        if(!$contract->isFuture()) {
            throw $this->createSdkException(sprintf('Contract #%d is not a future contract', $contract->getId()));
        }

        return new Contract($this, $this->connector->getCurrent($contract));
    }
}
