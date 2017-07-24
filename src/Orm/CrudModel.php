<?php
/*
 * The MIT License
 *
 * Copyright 2017 David Schoenbauer.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
namespace DSchoenbauer\Orm;

use DSchoenbauer\Orm\Enum\ModelEvents;
use Exception;

/**
 * Extends core model adding a basic set of methods triggering key events for crud operations
 *
 * @author David Schoenbauer
 */
class CrudModel extends Model
{

    /**
     * Process the addition of a new record
     * @param array $data
     * @return array
     * @since v1.0.0
     */
    public function create($data)
    {
        try {
            $this->setData($data);
            $this->getEventManager()->trigger(ModelEvents::CREATE, $this);
        } catch (\Exception $exc) {
            $this->manageExceptions($exc, ModelEvents::CREATE);
        }
        return $this->getData();
    }

    /**
     * Process the return of a single record
     * @param integer $idx
     * @return array
     * @since v1.0.0
     */
    public function fetch($idx)
    {
        try {
            $this->setId($idx);
            $this->getEventManager()->trigger(ModelEvents::FETCH, $this);
        } catch (\Exception $exc) {
            $this->manageExceptions($exc, ModelEvents::FETCH);
        }
        return $this->getData();
    }

    /**
     * Process the return of a collection of data
     * @return array
     * @since v1.0.0
     */
    public function fetchAll()
    {
        try {
            $this->getEventManager()->trigger(ModelEvents::FETCH_ALL, $this);
        } catch (Exception $exc) {
            $this->manageExceptions($exc, ModelEvents::FETCH_ALL);
        }
        return $this->getData();
    }

    /**
     * Process the update of data for a given id
     * @param integer $idx primary id number of the record to be updated
     * @param array $data an associative array of the data to be updated
     * @return array
     * @since v1.0.0
     */
    public function update($idx, $data)
    {
        try {
            $this->setId($idx)->setData($data);
            $this->getEventManager()->trigger(ModelEvents::UPDATE, $this);
        } catch (Exception $exc) {
            $this->manageExceptions($exc, ModelEvents::UPDATE);
        }
        return $this->getData();
    }

    /**
     * Process the removal of a given record
     * @param integer $idx primary ID of a value to be removed
     * @return boolean returns true on success
     * @since v1.0.0
     */
    public function delete($idx)
    {
        try {
            $this->setId($idx);
            $this->getEventManager()->trigger(ModelEvents::DELETE, $this);
            return true;
        } catch (Exception $exc) {
            $this->manageExceptions($exc, ModelEvents::DELETE);
            return false;
        }
    }

    private function manageExceptions(\Exception $exception, $event)
    {
        $this->getEventManager()->trigger(ModelEvents::ERROR, $this, compact('event', 'exception'));
    }
}
