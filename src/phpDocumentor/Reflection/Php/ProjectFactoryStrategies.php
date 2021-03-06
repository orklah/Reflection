<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Reflection\Php;

use OutOfBoundsException;
use function get_class;
use function sprintf;

final class ProjectFactoryStrategies implements StrategyContainer
{
    /** @var ProjectFactoryStrategy[] */
    private $strategies;

    /**
     * Initializes the factory with a number of strategies.
     *
     * @param ProjectFactoryStrategy[] $strategies
     */
    public function __construct(array $strategies)
    {
        foreach ($strategies as $strategy) {
            $this->addStrategy($strategy);
        }

        $this->strategies = $strategies;
    }

    /**
     * Find the ProjectFactoryStrategy that matches $object.
     *
     * @throws OutOfBoundsException When no matching strategy was found.
     */
    public function findMatching(object $object) : ProjectFactoryStrategy
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->matches($object)) {
                return $strategy;
            }
        }

        throw new OutOfBoundsException(
            sprintf(
                'No matching factory found for %s',
                get_class($object)
            )
        );
    }

    /**
     * Add a strategy to this container.
     */
    public function addStrategy(ProjectFactoryStrategy $strategy) : void
    {
        $this->strategies[] = $strategy;
    }
}
