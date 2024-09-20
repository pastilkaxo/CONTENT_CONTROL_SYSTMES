<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Modified by Paul Goodchild on 19-July-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace AptowebDeps\Twig\Node\Expression;

use AptowebDeps\Twig\Compiler;
use AptowebDeps\Twig\Extension\CoreExtension;
use AptowebDeps\Twig\Node\Node;

class FunctionExpression extends CallExpression
{
    public function __construct(string $name, Node $arguments, int $lineno)
    {
        parent::__construct(['arguments' => $arguments], ['name' => $name, 'is_defined_test' => false], $lineno);
    }

    public function compile(Compiler $compiler)
    {
        $name = $this->getAttribute('name');
        $function = $compiler->getEnvironment()->getFunction($name);

        $this->setAttribute('name', $name);
        $this->setAttribute('type', 'function');
        $this->setAttribute('needs_charset', $function->needsCharset());
        $this->setAttribute('needs_environment', $function->needsEnvironment());
        $this->setAttribute('needs_context', $function->needsContext());
        $this->setAttribute('arguments', $function->getArguments());
        $callable = $function->getCallable();
        if ('constant' === $name && $this->getAttribute('is_defined_test')) {
            $callable = [CoreExtension::class, 'constantIsDefined'];
        }
        $this->setAttribute('callable', $callable);
        $this->setAttribute('is_variadic', $function->isVariadic());

        $this->compileCallable($compiler);
    }
}
