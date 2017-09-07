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
namespace DSchoenbauer\Orm\Builder\Component;

use DSchoenbauer\Orm\Enum\EventPriorities;
use DSchoenbauer\Orm\Enum\ModelEvents;
use DSchoenbauer\Orm\Events\Persistence\File\Create;
use DSchoenbauer\Orm\Events\Persistence\File\Delete;
use DSchoenbauer\Orm\Events\Persistence\File\Select;
use DSchoenbauer\Orm\Events\Persistence\File\SelectAll;
use DSchoenbauer\Orm\Events\Persistence\File\Update;
use DSchoenbauer\Orm\ModelInterface;
use DSchoenbauer\Orm\VisitorInterface;

/**
 * Description of FilePersistence
 *
 * @author David Schoenbauer
 */
class FilePersistence implements VisitorInterface
{

    private $path;

    public function __construct($path)
    {
        $this->setPath($path);
    }

    public function visitModel(ModelInterface $model)
    {
        $path = $this->getPath();
        $model
            ->accept(new Create([ModelEvents::CREATE], EventPriorities::ON_TIME, $path))
            ->accept(new Select([ModelEvents::FETCH], EventPriorities::ON_TIME, $path))
            ->accept(new SelectAll([ModelEvents::FETCH_ALL], EventPriorities::ON_TIME, $path))
            ->accept(new Update([ModelEvents::UPDATE], EventPriorities::ON_TIME, $path))
            ->accept(new Delete([ModelEvents::DELETE], EventPriorities::ON_TIME, $path))

        ;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }
}
