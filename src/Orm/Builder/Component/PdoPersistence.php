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

use DSchoenbauer\Orm\Enum\ModelEvents;
use DSchoenbauer\Orm\Events\Persistence\PdoCreate;
use DSchoenbauer\Orm\Events\Persistence\PdoDelete;
use DSchoenbauer\Orm\Events\Persistence\PdoSelect;
use DSchoenbauer\Orm\Events\Persistence\PdoUpdate;
use DSchoenbauer\Orm\ModelInterface;
use DSchoenbauer\Orm\VisitorInterface;
use PDO;

/**
 * Description of PdoPersistence
 *
 * @author David Schoenbauer
 */
class PdoPersistence implements VisitorInterface
{

    protected $adapter;

    public function __construct($adapter)
    {
        $this->setAdapter($adapter);
    }

    public function visitModel(ModelInterface $model)
    {
        $adapter = $this->getAdapter();
        $model
            ->accept(new PdoCreate([ModelEvents::CREATE], $adapter))
            ->accept(new PdoSelect([ModelEvents::FETCH], $adapter))
            ->accept(new PdoUpdate([ModelEvents::UPDATE], $adapter))
            ->accept(new PdoDelete([ModelEvents::DELETE], $adapter));
    }

    /**
     * @return PDO
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    public function setAdapter(PDO $adapter)
    {
        $this->adapter = $adapter;
        return $this;
    }
}
