<?php
/*
 * The MIT License
 *
 * Copyright 2018 David Schoenbauer.
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
namespace DSchoenbauer\Orm\Events\Guard;

use DSchoenbauer\Exception\Http\ClientError\ForbiddenException;
use DSchoenbauer\Orm\Enum\EventPriorities;
use DSchoenbauer\Orm\Events\AbstractModelEvent;
use DSchoenbauer\Orm\Events\Guard\GuardInterface;
use DSchoenbauer\Orm\ModelInterface;
use DSchoenbauer\Orm\VisitorInterface;

/**
 * Description of GuardEvent
 *
 * @author David Schoenbauer
 */
class GuardEvent extends AbstractModelEvent
{

    private $guards = [];

    public function __construct(array $events = [], array $guards = [], $priority = EventPriorities::ON_TIME)
    {
        parent::__construct($events, $priority);
        foreach ($guards as $guard) {
            $this->add($guard);
        }
    }

    public function execute(ModelInterface $model)
    {
        if (!$this->authenticate($model)) {
            throw new ForbiddenException();
        }
        return true;
    }

    public function authenticate(ModelInterface $model)
    {
        /* @var $guard GuardInterface */
        foreach ($this->guards as $guard) {
            if ($guard instanceof VisitorInterface) {
                $model->accept($guard);
            }
            if ($guard->authenticate()) {
                return true;
            }
        }
        return false;
    }

    public function add(GuardInterface $guard)
    {
        $this->guards[] = $guard;
        return $this;
    }

    /**
     *
     * @return array
     */
    public function getGuards()
    {
        return $this->guards;
    }
}
