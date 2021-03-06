<?php

/**
 * Copyright (C) 2011 by Pieter Vogelaar (pietervogelaar.nl) and Kees Schepers (keesschepers.nl)
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
 *
 * @category   PiKe
 * @copyright  Copyright (C) 2011 by Pieter Vogelaar (pietervogelaar.nl) and Kees Schepers (keesschepers.nl)
 * @author     Nico Vogelaar
 * @license    MIT
 */

namespace Pike;

use Pike\DataTable\Adapter\AdapterInterface;
use Pike\DataTable\DataSource\DataSourceInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\View\Model\ViewModel;

class DataTable
{

    /**
     * @var \Pike\DataTable\Adapter\AdapterInterface
     */
    protected $adapter;

    /**
     * @var \Pike\DataTable\DataSource\DataSourceInterface
     */
    protected $dataSource;

    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected $sm;

    /**
     * Constructor
     *
     * @param AdapterInterface    $adapter
     * @param DataSourceInterface $dataSource
     * @param ServiceManager      $sm
     */
    public function __construct(AdapterInterface $adapter,
            DataSourceInterface $dataSource, ServiceManager $sm
    ) {
        $this->adapter = $adapter;
        $this->dataSource = $dataSource;
        $this->sm = $sm;

        $columnBag = $this->adapter->getColumnBag();
        foreach ($this->dataSource->getFields() as $field) {
            $columnBag->add($field);
        }
    }

    /**
     * Returns the adapter
     *
     * @return AdapterInterface
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * Returns the data source
     *
     * @return DataSourceInterface
     */
    public function getDataSource()
    {
        return $this->dataSource;
    }

    /**
     * Returns the service manager
     *
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->sm;
    }

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->adapter->getResponse($this);
    }

    /**
     * @return string
     */
    public function render()
    {
        $response = $this->adapter->render($this);
        if ($response instanceof ViewModel) {
            $response = $this->sm->get('ViewRenderer')->render($response);
        }

        return $response;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

}
