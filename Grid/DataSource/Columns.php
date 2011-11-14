<?php
/**
 * Copyright (C) 2011 by Pieter Vogelaar (platinadesigns.nl) and Kees Schepers (keesschepers.nl)
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

/**
 * The column class keeps in track of all column details (including dynamic ones)
 *
 */
class Pike_Grid_DataSource_Columns extends ArrayObject
{
    /**
     * If specified, only these columns will be visible in the grid
     * 
     * @var array
     */
    public $showColumns = array();

    /**
     * Constructor
     *
     * @param array $columns
     */
    public function __construct(array $columns = array())
    {
        foreach ($columns as $column) {
            $this->add($column);
        }
    }

    /**
     * Adds a column to the internal index
     *
     * @param mixed   $column   Either the name or an array with options
     * @param string  $label    The friendlyname used for this column as heading
     * @param string  $sidx     The fieldname to be used when sorting is isseud thru the grid
     * @param integer $position The position number, can be any number.
     */
    public function add($column, $label = null, $sidx = null, $position = null, $data = null)
    {
        if (!is_array($column)) {
            $name = $column;

            $column = array();
            $column['name'] = $name;
            $column['label'] = (is_null($label) ? $name : $label);
            if (null !== $sidx)
                $column['index'] = $sidx;

            $column['position'] = (is_null($position) ? $this->count() : $position);

            /**
             * Default data drawing callback
             */
            if (!is_callable($data)) {
                $column['data'] = function($row) use ($column, $data) {
                    if (null !== $data) {
                        return $data;
                    } else {
                        return isset($row[$column['name']]) ? $row[$column['name']] : null;
                    }
                };
            } else {
                $column['data'] = $data;
            }
        }

        $this->offsetSet($column['name'], $column); //array object access

        return $this->offsetGet($column['name']);
    }

    /**
     * @param  string $name
     * @return boolean
     */
    public function __isset($name)
    {
        return $this->offsetExists($name);
    }

    /**
     * Retrieves a column like it's an object property
     *
     * @param  string $column
     * @return array
     */
    public function __get($column)
    {
        if ($this->offsetExists($column)) {
            return $this->offsetGet($column);
        } else {
            throw new Pike_Exception('Unknown column (' . $column . ')');
        }
    }

    /**
     * Sorts items as we would expect them, just before the iterator is returned
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        if (count($this->showColumns) > 0) {
            $columns = $this->getArrayCopy();

            foreach ($columns as &$column) {
                if (!in_array($column['name'], $this->showColumns)) {
                    $column['position'] = 0;
                } else {
                    $column['position'] = array_search($column['name'], $this->showColumns);
                }
            }

            $this->exchangeArray($columns);
        }
        
        $this->uasort(function($first, $second) {
            if (isset($first['position']) && isset($second['position'])) {
                if ($first['position'] > $second['position']) {
                    return 1;
                } elseif($first['position'] < $second['position']) {
                    return -1;
                } else {
                    return 0;
                }
            } else {
                return 0;
            }
        });

        return parent::getIterator();
    }

    /**
     * Returns the columns
     *
     * @return array
     */
    public function getColumns()
    {
        return $this->getArrayCopy();
    }
}