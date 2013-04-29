<?php

namespace Pike\DataTable\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DataTableHelper extends AbstractHelper implements ServiceLocatorAwareInterface
{

    private $serviceLocator;

    /**
     * Set the service locator.
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return CustomHelper
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;

        return $this;
    }

    /**
     * Get the service locator.
     *
     * @return \Zend\ServiceManager\ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    public function __invoke($dataTableService)
    {
        $serviceManager = $this->getServiceLocator()->getServiceLocator();
        $service = $serviceManager->get($dataTableService);

        if (false === $service instanceof \Pike\DataTable) {
            throw new \RuntimeException(sprintf('%s is not a Pike\DataTable', get_class($service)));
        }

        return $service->render();
    }

}
