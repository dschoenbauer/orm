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

use DSchoenbauer\Exception\Platform\InvalidArgumentException;

/**
 *
 * @author David Schoenbauer
 */
abstract class AbstractDataEmbedderMany implements DataProviderInterface
{

    private $embedKey = '_embedded';
    private $name = 'item';
    private $linkField = null;
    private $embeddedDataProvider;
    private $targetDataProvider;

    abstract public function getData();

    public function __construct(
        DataProviderInterface $target = null,
        DataProviderInterface $embedded = null,
        $linkField = null,
        $name = 'item',
        $embeddedKey = '_embedded'
    ) {
    
        $this
            ->setTargetDataProvider($target)
            ->setEmbeddedDataProvider($embedded)
            ->setLinkField($linkField)
            ->setName($name)
            ->setEmbedKey($embeddedKey)
        ;
    }

    /**
     *
     * @param array $data an array of arrays
     * @param mixed $defaultValue if multiple values are expected use an array
     * @return array
     */
    public function prepData(array $data, $defaultValue)
    {
        foreach ($data as &$row) {
            if (!array_key_exists($this->getEmbedKey(), $row)) {
                $row[$this->getEmbedKey()] = [];
            }
            if (!array_key_exists($this->getName(), $row[$this->getEmbedKey()])) {
                $row[$this->getEmbedKey()][$this->getName()] = $defaultValue;
            }
        }
        return $data;
    }

    public function getEmbedKey()
    {
        return $this->embedKey;
    }

    public function setEmbedKey($embedKey)
    {
        $this->embedKey = $embedKey;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return DataProviderInterface
     */
    public function getEmbeddedDataProvider()
    {
        if ($this->embeddedDataProvider === null) {
            throw new InvalidArgumentException("No embedded data provider set");
        }
        return $this->embeddedDataProvider;
    }

    public function setEmbeddedDataProvider(DataProviderInterface $embeddedDataProvider = null)
    {
        $this->embeddedDataProvider = $embeddedDataProvider;
        return $this;
    }

    /**
     * @return DataProviderInterface
     */
    public function getTargetDataProvider()
    {
        if ($this->targetDataProvider === null) {
            throw new InvalidArgumentException("No target data provider set");
        }
        return $this->targetDataProvider;
    }

    public function setTargetDataProvider(DataProviderInterface $targetDataProvider = null)
    {
        $this->targetDataProvider = $targetDataProvider;
        return $this;
    }

    public function getLinkField()
    {
        if ($this->linkField === null) {
            throw new InvalidArgumentException("No link field set");
        }
        return $this->linkField;
    }

    public function setLinkField($linkField)
    {
        $this->linkField = $linkField;
        return $this;
    }
}
