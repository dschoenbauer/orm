<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace DSchoenbauer\Orm\Events\Validate\DataType;

use DSchoenbauer\Orm\Entity\HasNumericFieldsInterface;

/**
 * validates number fields are numbers
 *
 * @author David Schoenbauer <dschoenbauer@gmail.com>
 */
class ValidateNumber extends AbstractValidate
{

    /**
     * returns the fields affected by the entity interface
     * @param mixed $entity an entity object implements the getTypeInterface
     * @return array an array of fields that are relevant to the interface
     * @since v1.0.0
     */
    public function getFields($entity)
    {
        return $entity->getNumericFields();
    }

    /**
     * full name space of an interface that defines a given field type
     * @return string
     * @since v1.0.0
     */
    public function getTypeInterface()
    {
        return HasNumericFieldsInterface::class;
    }

    /**
     * Validates that the value is of the proper type
     * @param mixed $value value to validate
     * @param string $field field name
     * @return boolean
     * @since v1.0.0
     */
    public function validateValue($value, $field = null)
    {
        return is_numeric($value);
    }
}
