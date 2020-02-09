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

namespace phpDocumentor\Reflection\Php\Factory;

use phpDocumentor\Reflection\Location;
use phpDocumentor\Reflection\Php\Argument as ArgumentDescriptor;
use phpDocumentor\Reflection\Php\Function_ as FunctionDescriptor;
use phpDocumentor\Reflection\Php\StrategyContainer;
use phpDocumentor\Reflection\Types\Context;
use PhpParser\Node\Stmt\Function_ as FunctionNode;
use Webmozart\Assert\Assert;

/**
 * Strategy to convert Function_ to FunctionDescriptor
 *
 * @see FunctionDescriptor
 * @see \PhpParser\Node\
 */
final class Function_ extends AbstractFactory
{
    public function matches($object) : bool
    {
        return $object instanceof FunctionNode;
    }

    /**
     * Creates a FunctionDescriptor out of the given object including its child elements.
     *
     * @param FunctionNode $object object to convert to an Element
     * @param StrategyContainer $strategies used to convert nested objects.
     * @param Context $context of the created object
     *
     * @return FunctionDescriptor
     */
    protected function doCreate($object, StrategyContainer $strategies, ?Context $context = null)
    {
        $function = new FunctionDescriptor(
            $object->fqsen,
            $this->createDocBlock($strategies, $object->getDocComment(), $context),
            new Location($object->getLine()),
            (new Type())->fromPhpParser($object->getReturnType())
        );

        foreach ($object->params as $param) {
            $strategy = $strategies->findMatching($param);
            $argument = $strategy->create($param, $strategies, $context);
            Assert::isInstanceOf($argument, ArgumentDescriptor::class);
            $function->addArgument($argument);
        }

        return $function;
    }
}
