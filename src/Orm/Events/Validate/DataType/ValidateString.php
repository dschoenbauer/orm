<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace DSchoenbauer\Orm\Events\Validate\DataType;

use DSchoenbauer\Orm\Entity\HasStringFieldsInterface;

/**
 * Description of ValidateString
 *
 * @author David Schoenbauer <dschoenbauer@gmail.com>
 */
class ValidateString extends AbstractValidate
{

    /**
     *
     * @param HasStringFieldsInterface $entity
     * @return array of fields that are this type
     */
    public function getFields($entity)
    {
        return $entity->getStringFields();
    }

    public function getTypeInterface()
    {
        return HasStringFieldsInterface::class;
    }

    public function validateValue($value, $field = null)
    {
        return is_string($value);
    }
}
