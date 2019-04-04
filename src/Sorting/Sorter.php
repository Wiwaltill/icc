<?php

namespace App\Sorting;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Sorter implements ContainerAwareInterface {

    /** @var ContainerInterface|null */
    private $container;

    /**
     * @inheritDoc
     */
    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }

    public function sort(&$array, string $strategyService, SortDirection $direction = null) {
        if($this->container === null) {
            throw new \RuntimeException('Container was not injected properly');
        }

        $strategy = $this->container->get($strategyService);

        if(!$strategy instanceof SortingStrategyInterface) {
            throw new \RuntimeException(sprintf('Service "%s" must implement "%s" in order to be used as sorting strategy!', $strategyService, SortingStrategyInterface::class));
        }

        usort($array, [ $strategy, 'compare' ]);

        if(SortDirection::Descending()->equals($direction)) {
            $array = array_reverse($array);
        }
    }
}