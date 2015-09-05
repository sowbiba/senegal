<?php

namespace Senegal\ApiBundle\Event;

/**
 * The class that contains event names.
 */
final class ApiEvents
{
    const
        /*
         * This event is thrown after CRUDManager::create() method is processed
         */
        POST_MANAGER_CREATE = 'post.manager.create',

        /*
         * This event is thrown before CRUDManager::insert() method is processed
         */
        PRE_MANAGER_INSERT = 'pre.manager.insert',

        /*
         * This event is thrown before CRUDManager::update() method is processed
         */
        PRE_MANAGER_CRUD_UPDATE = 'pre.manager.crud.update',

        /*
         * This event is thrown before CRUDManager::update() method is processed
         */
        PRE_MANAGER_UPDATE = 'pre.manager.update',

        /*
         * This event is thrown after CRUDManager::insertSave() method is processed
         */
        POST_MANAGER_INSERT_SAVE = 'post.manager.insert_save',

        /*
         * This event is thrown after CRUDManager::updateSave() method is processed
         */
        POST_MANAGER_UPDATE_SAVE = 'post.manager.update_save',

        /*
         * This event is thrown before ContractSetManager::create() method is processed
         */
        POST_MANAGER_CONTRACT_SET_CREATE = 'post.manager.contract_set.create',

        /*
         * This event is thrown before ContractSetManager::insert() method is processed
         */
        PRE_MANAGER_CONTRACT_SET_INSERT = 'pre.manager.contract_set.insert',

        /*
         * This event is thrown after ContractSetManager::insertSave() method is processed
         */
        POST_MANAGER_CONTRACT_SET_INSERT_SAVE = 'post.manager.contract_set.insert_save',

        /*
         * This event is thrown before ContractSetManager::insert() method is processed
         */
        PRE_CONTRACT_SET_IDENTITY_POST = 'pre.contract_set_identity.post',

        /*
         * This event is thrown before TypePageManager::create() method is processed
         */
        PRE_MANAGER_TYPEPAGE_INSERT = 'pre.manager.typepage.insert',

        /*
         * This event is thrown before TypePageManager::update() method is processed
         */
        PRE_MANAGER_TYPEPAGE_UPDATE = 'pre.manager.typepage.update',

        /*
         * This event is thrown before Forfait::create() method is processed
         */
        PRE_MANAGER_FORFAIT_INSERT = 'pre.manager.forfait.insert',

        /*
         * This event is thrown before Forfait::update() method is processed
         */
        PRE_MANAGER_FORFAIT_UPDATE = 'pre.manager.forfait.update',

        /*
         * This event is thrown before ContractSetManager::create() method is processed
         */
        PRE_MANAGER_USER_INSERT = 'pre.manager.user.insert',

        /*
         * This event is thrown before ContractSetManager::create() method is processed
         */
        PRE_MANAGER_USER_UPDATE = 'pre.manager.user.update',

        /*
         * This event is thrown after ContractSetIdentityManager::create() method is processed
         */
        POST_MANAGER_CONTRACT_SET_IDENTITY_CREATE = 'post.manager.contract_set_identity.create',

        /*
         * This event is thrown before ContractSetIdentityManager::insert() method is processed
         */
        PRE_MANAGER_CONTRACT_SET_IDENTITY_INSERT = 'pre.manager.contract_set_identity.insert',

        /*
         * This event is thrown before ContractSetIdentityManager::update() method is processed
         */
        PRE_MANAGER_CONTRACT_SET_IDENTITY_UPDATE = 'pre.manager.contract_set_identity.update',

        /*
         * This event is thrown before ContractSetIdentityManager::insertSave() method is processed
         */
        POST_MANAGER_CONTRACT_SET_IDENTITY_INSERT_SAVE = 'pre.manager.contract_set_identity.insert_save',

        /*
         * This event is thrown before ContractSetIdentityManager::updateSave() method is processed
         */
        POST_MANAGER_CONTRACT_SET_IDENTITY_UPDATE_SAVE = 'post.manager.contract_set_identity.update_save',

        /*
         * This event is thrown before RapprochementSetManager::insertSave() method is processed
         */
        POST_MANAGER_RAPPROCHEMENT_SET_INSERT_SAVE = 'post.manager.rapprochement_set.insert_save',

        /*
         * This event is thrown before RapprochementSetManager::updateMultiple() method is processed
         */
        POST_MANAGER_RAPPROCHEMENT_SET_UPDATE_MULTIPLE = 'post.manager.rapprochement_set.update_multiple'
    ;
}
