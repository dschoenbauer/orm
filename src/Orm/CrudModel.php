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

/**
 * Extends core model adding a basic set of methods triggering key events for crud operations
 *
 * @author David Schoenbauer
 */
class CrudModel extends Model {

    public function create($data) {
        $this->setData($data);
        $events = [ModelEvents::CREATE, ModelEvents::FETCH];
        $this->getEventManager()->trigger(ModelEvents::VALIDATE, $this, compact('events'));
        $this->getEventManager()->trigger(ModelEvents::CREATE, $this);
        $this->getEventManager()->trigger(ModelEvents::FETCH, $this);
        return $this->getData();
    }

    public function fetch($id) {
        $this->setId($id);
        $events = [ModelEvents::FETCH];
        $this->getEventManager()->trigger(ModelEvents::VALIDATE, $this, compact('events'));
        $this->getEventManager()->trigger(ModelEvents::FETCH, $this);
        return $this->getData();
    }

    public function fetchAll() {
        $events = [ModelEvents::FETCH_ALL];
        $this->getEventManager()->trigger(ModelEvents::VALIDATE, $this, compact('events'));
        $this->getEventManager()->trigger(ModelEvents::FETCH_ALL, $this);
        return $this->getData();
    }

    public function update($id, $data) {
        $this->setId($id)->setData($data);
        $events = [ModelEvents::UPDATE, ModelEvents::FETCH];
        $this->getEventManager()->trigger(ModelEvents::VALIDATE, $this, compact('events'));
        $this->getEventManager()->trigger(ModelEvents::UPDATE, $this);
        $this->getEventManager()->trigger(ModelEvents::FETCH, $this);
        return $this->getData();
    }
    
    public function delete($id){
        $this->setId($id);
        $events = [ModelEvents::DELETE];
        $this->getEventManager()->trigger(ModelEvents::VALIDATE, $this, compact('events'));
        $this->getEventManager()->trigger(ModelEvents::DELETE, $this);
        return true;
    }

}
