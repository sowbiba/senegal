<?php

namespace Api\Sdk\Contract\Connector\Doctrine;

use Doctrine\ORM\EntityManager;

use Api\Sdk\Connector\AbstractDoctrineConnector;
use Api\Sdk\Contract\Connector\ContractConnectorInterface;
use Api\Sdk\Model\Contract;
use Api\Sdk\Model\Field;
use Api\SdkBundle\Entity\Contract as ContractEntity;
use Api\Sdk\Query\QueryInterface;

/**
 * This class allows to use sf2 contract entity model
 *
 * Class ContractDoctrineConnector
 * @package Api\Sdk\Connector\DoctrineConnector
 * @author  Florent Coquel
 * @since   19/06/13
 *
 * Can't test it without a context (database)
 * @codeCoverageIgnore
 *
 */
class ContractDoctrineConnector extends AbstractDoctrineConnector implements ContractConnectorInterface
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em);

        $this->setRepository('Contract');
    }

    /**
     * Returns the contract matching the given id
     *
     * @param int $id
     *
     * @return array|null
     * @throws \BadMethodCallException
     */
    public function getById($id)
    {
        if (!is_int($id)) {
            throw new \BadMethodCallException(__METHOD__ . ' expects an int as parameter, ' . gettype($id) . ' given');
        }

        return $this->getOne($id);
    }

    /**
     * Returns the contracts matching the given query
     *
     * @param QueryInterface $query
     *
     * @return array collection of contracts
     */
    public function getCollection(QueryInterface $query)
    {
        $contracts = $this->getResult($query);

        return array_map(function ($contract) {
            if ($contract instanceof ContractEntity) {
                return $this->convert($contract->postLoadHydrate());
            }

            // In case we added special selects to the query, the getResult can return
            // [0: {Contract}, 1: [special selects]]
            if (is_array($contract) && isset($contract[0]) && $contract[0] instanceof ContractEntity) {
                return $this->convert($contract[0]->postLoadHydrate());
            }

            throw new \LogicException('Query results format unknown');
        }, $contracts);
    }

    /**
     * Returns the number of contracts matching the given query
     *
     * @param QueryInterface $query
     *
     * @return int
     */
    public function count(QueryInterface $query)
    {
        $count = $this->getCount($query);

        return $count;
    }

    public function isMirror(Contract $contract)
    {

        $stmt = $this->em->getConnection()->executeQuery(sprintf(
            'SELECT count(*) FROM  contract_has_contract WHERE  contract_right_id = %d AND relationship_id = (SELECT id FROM relationship WHERE slug =  "mirror-%d-%d" )',
            $contract->getId(),
            $contract->getProductLineId(),
            $contract->getProductLineId()
        ));

        $count = (int) $stmt->fetchColumn();

        return $count > 0;

    }

    public function getContractIdByNotationId($notationId)
    {
        $stmt = $this->em->getConnection()->executeQuery(sprintf(
            'SELECT contrats_id FROM  notationmagazine WHERE  id = "%d" ',
            $notationId
        ));

        $results = $stmt->fetch();

        return (int) $results["contrats_id"];

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
        if(!$contract->isChild()) {
            return [];
        }

        $supportedFieldTypes = implode(', ', Field::getSupportedTypes());

        $query =
<<<SQL
SELECT c.id
FROM linkedchapitre l
INNER JOIN chapitres c1 ON l.chapitres_id = c1.id, chapitres c2
INNER JOIN champs c on c2.id = c.chapitres_id
WHERE l.contrats_id = ?
AND c2.tree_left >= c1.tree_left
AND c2.tree_right <= c1.tree_right
AND c1.gammes_id = c2.gammes_id
AND c.typechamps_id IN ($supportedFieldTypes)
ORDER BY c2.tree_left
SQL;

        $sth = $this->em->getConnection()->prepare($query);
        $sth->execute(array($contract->getId()));

        return $sth->fetchAll(\PDO::FETCH_COLUMN, 0);
    }

    /**
     * Set a parent to a contract
     *
     * @param \Api\Sdk\Model\Contract $contract
     * @param \Api\Sdk\Model\Contract $parent
     */
    public function setParent(Contract $contract, Contract $parent)
    {
        $this->em->flush($this->repository->find($contract->getId())->setParentId($parent->getId()));
        $this->em->flush($this->repository->find($parent->getId())->setIsParent(true));

        $contract->setParentId($parent->getId());
        $parent->setIsParent(true);
    }

    /**
     * Returns current contract of a future contract
     *
     * @param \Api\Sdk\Model\Contract $futureContract Future contract
     *
     * @return array|null Current contract data or null if not found
     */
    public function getCurrent(Contract $futureContract)
    {
        return $this->getById($this->getById($futureContract->getId())['currentId']);
    }
}
