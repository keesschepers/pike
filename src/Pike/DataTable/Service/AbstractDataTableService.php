<?php

namespace PsOrder\DataTable;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

abstract class AbstractDataTableService implements FactoryInterface
{

    protected $config;

    private $sm;

    abstract public function getName();

    /**
     * @param ServiceLocatorInterface|ServiceManager $serviceLocator
     * @return \Pike\DataTable
     */
    public static function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->config = $serviceLocator->get('config');
        $this->sm = $serviceLocator;

        return new \Pike\dataTable($this->getAdapter(), $this->getDataSource(), $serviceLocator);
    }

    protected function getConfig()
    {
        if (false === isset($this->config['pike']['datatable'])) {
            throw new \RuntimeException('No [pike][datatable] section configured');
        }

        if (false === isset($this->config['pike']['datatable'][$this->getName()])) {
            throw new \RuntimeException(sprinf('No datatable configuration found for %s', $this->getName()));
        }

        return $this->config['pike']['datatable'][$this->getName()];
    }

    protected function getAdapter()
    {
        $configuration = $this->getConfig();

        if (false === isset($configuration['adapter'])) {
            throw new \RuntimeException(sprintf('No adapter configured for %s', $this->getName()));
        }

        $strategy = $configuration['adapter'];

        if (false === class_exists($strategy, true)) {
            throw new \RuntimeException(sprintf('%s is not a loadable class', $strategy));
        }

        $adapter = new $strategy();
        $adapter->setParameters($this->getServiceManager()->get('request')->getQuery()->toArray());

        if (false === $adapter instanceof Pike\DataTable\Adapter\AdapterInterface) {
            throw new \RuntimeException(sprintf('%s is not a valid adapter', $strategy);
        }

        return $adapter;
    }

    protected function getDataSource()
    {
        $configuration = $this->getConfig();

        if (false === isset($configuration['datasource'])) {
            throw new \RuntimeException(sprintf('datasource should be configured for %s', $this->getName()));
        }

        if (false === isset($configuration['datasource_callback'])) {
            throw new \RuntimeException(sprintf('datasource should be configured for %s', $this->getName()));
        }

        $strategy = $configuration['datasource'];

        if (false === class_exists($strategy, true)) {
            throw new \RuntimeException(sprintf('%s is not a loadable class', $strategy));
        }

        $dataSource = new $strategy($configuration['datasource_callback']($this->getServiceManager()));

        if (false === $dataSource instanceof Pike\DataTable\DataSource\DataSourceInterface) {
            throw new \RuntimeException(sprintf('%s is not a valid datasource', $strategy);
        }

        return $dataSource;
    }

    public function getServiceManager()
    {
        return $this->sm;
    }
}
