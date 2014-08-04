<?php

namespace Api\Sdk\Contract\Connector\Propel;

use Api\Sdk\Connector\AbstractPropelConnector;
use Api\Sdk\Model\Contract;
use Api\Sdk\Model\Field;

class ContractPropelConnector extends AbstractPropelConnector
{
    /**
     * Release a contract
     *
     * @param Contract $contract
     */
    public function release(Contract $contract)
    {
        $this->getBridge()->permissiveTransaction(function () use ($contract) {
            $contractLegacy = \ContratsPeer::retrieveByPk($contract->getId());
            $publishedRevision = $contract->getPublishedRevision();
            $updatedAt = $publishedRevision->getUpdatedAt()->getTimestamp();

            foreach ($publishedRevision->getValues() as $fieldId => $value) {
                // Transform value to '#NC'
                $value = 0 === strlen($value) ? Field::VALUE_NC : $value;
                $contractLegacy->setChampsValeur($fieldId, $value, Field::VALUE_NA == $value);
            }
            $contractLegacy->setUpdatedAt($updatedAt);
            $contractLegacy->setReleasedAt(time());
            $contractLegacy->save();
        });
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
        if(!$contract->isChild()) {
            return [];
        }

        // Saving a full contract each time we set a value may be fun, but it's a bit time consuming...
        $linkedChaptersIds = $this->getBridge()->permissiveTransaction(function () use ($contract) {
            $c = new \Criteria();
            $c->add(\LinkedchapitrePeer::CONTRATS_ID, $contract->getId());
            $linkedChapters = \LinkedchapitrePeer::doSelect($c);

            $linkedChaptersIds = array();

            foreach ($linkedChapters as $linkedChapter) {
                $linkedChaptersIds[] = $linkedChapter->getChapitresId();
            }

            return $linkedChaptersIds;
        });
        $chapterTree = $this->getMediator()->getColleague('chapterDoctrine')
            ->getChapterTree($contract->getProductLineId());

        $linkedChapters = array();

        foreach ($chapterTree['children'] as $chapter) {
            if (in_array($chapter['id'], $linkedChaptersIds)) {
                $linkedChapters[] = $chapter;
            }
        }

        return $linkedChapters;
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
        return $this->getBridge()->permissiveTransaction(function () use ($contract, $chapters) {
            $criteria = new \Criteria();
            $criteria->add(\ChapitresPeer::ID,
                array_map(function ($chapter) {
                    return $chapter->getId();
                }, $chapters), \Criteria::IN
            );
            $chapters = \ChapitresPeer::doSelect($criteria);
            $contract = \ContratsPeer::retrieveByPk($contract->getId());

            return false !== $contract->linkChapitres($chapters);
        });
    }

    /**
     * @param Contract $contract
     * @param array $chapters
     * @return array
     */
    public function getInheritanceChapterConflicts(Contract $contract, array $chapters)
    {
        return $this->getBridge()->permissiveTransaction(function () use ($contract, $chapters) {
            $criteria = new \Criteria();
            $criteria->add(\ChapitresPeer::ID,
                array_map(function ($chapter) {
                    return $chapter->getId();
                }, $chapters), \Criteria::IN
            );
            $chapters = \ChapitresPeer::doSelect($criteria);
            $rulesIds = array();
            foreach ($chapters as $chapter) {
                $rulesIds = array_merge($chapter->getDataEntryRulesIdsWithDescendant(), $rulesIds);
            }
            $conflicts = array("error" => [], "warning" => []);
            foreach ($rulesIds as $ruleId) {
                $oRCC_Manager = new \RuleConflictsCheckerManager($ruleId, null, null, \RuleConflictsCheckerManager::INHERITANCE_ONLY, $contract->getId(), array(), array(), true, $chapters);
                $rule = \DataEntryRulePeer::retrieveByPK($ruleId);
                $result = $oRCC_Manager->humanFeedBack();
                if (isset($result[0]) && $result[0] != "") {
                    if ($rule->getIsActive()) {
                        $conflicts["error"][] = $result[0];
                    } else {
                        $conflicts["warning"][] = $result[0];
                    }
                }
            }

            return $conflicts;
        });
    }

    /**
     * Update the contract funds
     *
     * @param \Api\Sdk\Model\Contract $contract
     */
    public function inheritFunds(Contract $contract, $inheritFunds)
    {
        $this->getBridge()->permissiveTransaction(function () use ($contract, $inheritFunds) {
            $contractLegacy = \ContratsPeer::retrieveByPk($contract->getId());

            $contractLegacy->setContratspereFonds($inheritFunds);
            $contractLegacy->save();

            if ($inheritFunds) {
                //synchronize parent funds
                $contractLegacy->updateMyFondsFromMyFather();
            }
        });
    }

    /**
     * Update the contract documents
     *
     * @param \Api\Sdk\Model\Contract $contract
     */
    public function inheritDocuments(Contract $contract, $inheritDocs)
    {
        $this->getBridge()->permissiveTransaction(function () use ($contract, $inheritDocs) {
            $contractLegacy = \ContratsPeer::retrieveByPk($contract->getId());

            $contractLegacy->setContratspereDocs($inheritDocs);
            $contractLegacy->save();

            if ($inheritDocs) {
                //synchronize parent documents
                $contractLegacy->updateMyDocumentsFromMyFather();
            }
        });
    }
}
