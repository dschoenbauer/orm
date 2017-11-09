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
namespace DSchoenbauer\Orm\DataProvider;

use PDO;

/**
 * Description of PDODataProvider
 *
 * @author David Schoenbauer
 */
abstract class AbstractPDODataProvider implements DataProviderInterface
{

    private $fetchFlat = false;
    private $fetchStyle = PDO::FETCH_ASSOC;
    private $parameters = null;
    private $defaultValue = [];
    private $adapter;

    public function __construct(\PDO $adapter)
    {
        $this->setAdapter($adapter);
    }

    abstract public function getSql();

    public function getData()
    {
        $stmt = $this->getAdapter()->prepare($this->getSql());
        if ($this->getParameters() === null) {
            $result = $stmt->execute();
        } else {
            $result = $stmt->execute($this->getParameters());
        }
        if (!$result) {
            return $this->getDefaultValue();
        }
        $stmt->setFetchMode($this->getFetchStyle());
        if ($this->getFetchFlat()) {
            return $stmt->fetch();
        }
        return $stmt->fetchAll();
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

    public function getFetchFlat()
    {
        return $this->fetchFlat;
    }

    public function setFetchFlat($fetchFlat = true)
    {
        $this->fetchFlat = $fetchFlat;
        return $this;
    }

    public function getFetchStyle()
    {
        return $this->fetchStyle;
    }

    public function setFetchStyle($fetchStyle)
    {
        $this->fetchStyle = $fetchStyle;
        return $this;
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
        return $this;
    }

    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;
        return $this;
    }
}
