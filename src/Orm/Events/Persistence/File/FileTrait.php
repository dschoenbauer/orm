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
namespace DSchoenbauer\Orm\Events\Persistence\File;

use DSchoenbauer\Orm\Entity\EntityInterface;
use DSchoenbauer\Orm\Exception\InvalidPathException;

/**
 * Description of FileNameTrait
 *
 * @author David Schoenbauer
 */
trait FileTrait
{

    protected $path = "." . DIRECTORY_SEPARATOR;

    /**
     * loads data from an array
     * @param EntityInterface $entity
     * @return array
     */
    public function loadFile(EntityInterface $entity)
    {
        $fileName = $this->getFileName($entity);
        if (!file_exists($fileName)) {
            return [];
        }
        return \json_decode(file_get_contents($fileName), true) ?: [];
    }

    public function saveFile(array $data, EntityInterface $entity)
    {
        return file_put_contents($this->getFileName($entity), json_encode($data)) !== false;
    }

    public function getFileName(EntityInterface $entity, $ext = "json")
    {
        return $this->getPath() . $entity->getTable() . "." . $ext;
    }

    /**
     * Path to be used for file
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Path to be used for a file
     * @param string $path
     * @return $this
     */
    public function setPath($path)
    {
        $path = trim(str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $path), '\\/');
        $path .= DIRECTORY_SEPARATOR;
        if (!is_dir($path)) {
            throw new InvalidPathException();
        }
        $this->path = $path;
        return $this;
    }
}
