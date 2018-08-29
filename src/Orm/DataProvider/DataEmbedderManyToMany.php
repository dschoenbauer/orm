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
namespace DSchoenbauer\Orm\DataProvider;

/**
 * Description of DataEmbedderManyToMany
 *
 * @author David Schoenbauer
 */
class DataEmbedderManyToMany implements DataProviderInterface
{
    /* @var $targetDataProvider DataProviderInterface */

    private $targetDataProvider;
    private $targetLinkField;

    /* @var $embeddedDataProvider DataProviderInterface */
    private $embeddedDataProvider;
    private $embeddedLinkField;
    private $name;
    private $embeddedKey;

    public function __construct(
        $targetDataProvider,
        $targetLinkField,
        $embeddedDataProvider,
        $embeddedLinkField,
        $name = 'item',
        $embeddedKey = '_embedded'
    ) {
    
        $this->setTargetDataProvider($targetDataProvider)->setTargetLinkField($targetLinkField);
        $this->setEmbeddedDataProvider($embeddedDataProvider)->setEmbeddedLinkField($embeddedLinkField);
        $this->setName($name)->setEmbeddedKey($embeddedKey);
    }

    public function getData()
    {
        $index = $this->buildIndex($this->getEmbeddedDataProvider(), $this->getEmbeddedLinkField());
        $items = $this->getTargetDataProvider()->getData();
        foreach ($items as &$item) {
            $id = $this->getTargetLinkField();
            $value = [];
            if (array_key_exists($id, $item) && array_key_exists($item[$id], $index)) {
                $value = $index[$item[$id]];
            }
            $item[$this->getEmbeddedKey()][$this->getName()] = $value;
        }
        return $items;
    }

    public function buildIndex(DataProviderInterface $dataProvider, $field)
    {
        $output = [];
        $rows = $dataProvider->getData();
        foreach ($rows as $row) {
            if (!isset($row[$field])) {
                continue;
            }
            $output[$row[$field]][] = $row;
        }
        return $output;
    }

    /**
     *
     * @return DataProviderInterface
     */
    public function getTargetDataProvider()
    {
        return $this->targetDataProvider;
    }

    public function setTargetDataProvider(DataProviderInterface $targetDataProvider)
    {
        $this->targetDataProvider = $targetDataProvider;
        return $this;
    }

    public function getTargetLinkField()
    {
        return $this->targetLinkField;
    }

    public function setTargetLinkField($targetLinkField)
    {
        $this->targetLinkField = $targetLinkField;
        return $this;
    }

    /**
     *
     * @return DataProviderInterface
     */
    public function getEmbeddedDataProvider()
    {
        return $this->embeddedDataProvider;
    }

    public function setEmbeddedDataProvider(DataProviderInterface $embeddedDataProvider)
    {
        $this->embeddedDataProvider = $embeddedDataProvider;
        return $this;
    }

    public function getEmbeddedLinkField()
    {
        return $this->embeddedLinkField;
    }

    public function setEmbeddedLinkField($embeddedLinkField)
    {
        $this->embeddedLinkField = $embeddedLinkField;
        return $this;
    }

    public function setEmbeddedKey($embeddedKey)
    {
        $this->embeddedKey = $embeddedKey;
        return $this;
    }

    public function getEmbeddedKey()
    {
        return $this->embeddedKey;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }
}
