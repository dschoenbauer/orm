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
namespace DSchoenbauer\Orm\Events\Persistence\Pdo;

use DSchoenbauer\Orm\Entity\EntityInterface;
use DSchoenbauer\Orm\Enum\EventPriorities;
use DSchoenbauer\Orm\Events\AbstractEvent;
use PDO;
use Zend\EventManager\EventInterface;

/**
 * Description of AbstractPdoEvent
 *
 * @author David Schoenbauer
 */
abstract class AbstractPdoEvent extends AbstractEvent
{

    private $adapter;

    /**
     *
     * @param array $events An array of event names to bind to
     * @param PDO $adapter PDO connection to a db of some sort.
     * be lazy loaded for you
     * @since v1.0.0
     */
    public function __construct(array $events, PDO $adapter, $priority = EventPriorities::ON_TIME)
    {

        parent::__construct($events, $priority);
        $this->setAdapter($adapter);
    }

    public function onExecute(EventInterface $event)
    {
        if (!$this->validateModel($event->getTarget(), EntityInterface::class)) {
            return;
        }
        return $this->commit($event);
    }

    public function reduceFields(array $data = [], array $fields = [])
    {
        $reduced = array_intersect_key($data, array_flip($fields));

        return array_filter($reduced, function ($value) {
            if (is_scalar($value) || (is_object($value) && method_exists($value, '__toString'))) {
                return true;
            }
            return false;
        });
    }

    abstract protected function commit(EventInterface $event);

    /**
     * Returns a PHP Data Object
     * @return PDO
     * @since v1.0.0
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * PDO connection to a db of some sort.
     * @param PDO $adapter
     * @return $this
     * @since v1.0.0
     */
    public function setAdapter(PDO $adapter)
    {
        $this->adapter = $adapter;
        return $this;
    }
}
