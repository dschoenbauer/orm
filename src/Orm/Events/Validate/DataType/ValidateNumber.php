<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace DSchoenbauer\Orm\Events\Validate\DataType;

use DSchoenbauer\Orm\Entity\HasNumericFieldsInterface;

/**
 * Description of ValidateNumber
 *
 * @author David Schoenbauer <dschoenbauer@gmail.com>
 */
class ValidateNumber extends AbstractValidate
{
    
    public function getFields($entity)
    {
        return $entity->getNumericFields();
    }

    public function getTypeInterface()
    {
        return HasNumericFieldsInterface::class;
    }

    public function validateValue($value, $field  = null)
    {
        return is_numeric($value);
    }
}
