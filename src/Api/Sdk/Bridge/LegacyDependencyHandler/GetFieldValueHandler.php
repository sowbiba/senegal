<?php
namespace Api\Sdk\Bridge\LegacyDependencyHandler;
use Api\Sdk\Model\Revision;

/**
 *
 * This class is use in PropelConnector to be injected in a \RuleConflictsCheckerManager
 * This injection allow to retrieve the value of a field of a revision in the legacy code
 *
 * Class GetFieldValueHandler
 * @package Api\Sdk\Bridge\LegacyDependencyHandler
 * @author  Florent Coquel
 * @since   10/10/13
 */
class GetFieldValueHandler
{
    private $revision;

    public function __construct(Revision $revision)
    {
        $this->revision = $revision;
    }

    /**
     * Returns field value for the given revision
     *
     * @param   $contractId
     * @param   $fieldId
     *
     * @return mixed
     */
    public function getValue($contractId, $fieldId)
    {
        return $this->revision->getFieldValue($fieldId);
    }
}
