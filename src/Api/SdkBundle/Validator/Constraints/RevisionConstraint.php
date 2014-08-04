<?php
/**
 * Author: Florent Coquel
 * Date: 25/11/13
 */

namespace Api\SdkBundle\Validator\Constraints;

use Api\Sdk\Model\Revision;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\MissingOptionsException;

class RevisionConstraint extends Constraint
{

    public $fieldsMessage = 'Field errors';
    public $valuesMessage = 'Values errors';
    public $ruleMessage   = 'Data entry rules errors';

    public $onlyEnabledFieldsIds = true;
    public $revision;

    public $validateFields  = true;
    public $validateValues  = true;
    public $validateRules   = true;
    public $validateSources = true;

    public function __construct($options = null)
    {
        if (is_object($options) && $options instanceof Revision) {
            $options = array("revision" => $options);
        }
        parent::__construct($options);

        if (null === $this->revision || !$this->revision instanceof Revision) {
            throw new MissingOptionsException(sprintf('We must have a revision for constraint %s', __CLASS__), array('productLine'));
        }

        if (null === $this->validateRules && null === $this->validateFields && null === $this->validateValues && null === $this->validateSources) {
            throw new MissingOptionsException(sprintf('Either option "validateFields" or "validateValues" or "validateRules" or "validateSources" must be given for constraint %s', __CLASS__), array('validateFields', 'validateValues', 'validateRules', 'validateSources'));
        }
    }

    public function validatedBy()
    {
        return 'revision_validator';
    }

}
