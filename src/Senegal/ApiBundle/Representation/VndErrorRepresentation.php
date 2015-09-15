<?php

namespace Senegal\ApiBundle\Representation;

use Hateoas\Representation\VndErrorRepresentation as HateoasVndErrorRepresentation;

class VndErrorRepresentation extends HateoasVndErrorRepresentation
{
    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'message' => $this->getMessage(),
            'ref' => $this->getLogref(),
            'help' => null, // todo
            'describes' => null, // todo
        ];
    }
}
