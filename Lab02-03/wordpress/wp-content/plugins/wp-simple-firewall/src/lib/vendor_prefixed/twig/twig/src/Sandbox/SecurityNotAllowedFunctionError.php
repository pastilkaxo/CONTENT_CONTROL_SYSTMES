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

namespace AptowebDeps\Twig\Sandbox;

/**
 * Exception thrown when a not allowed function is used in a template.
 *
 * @author Martin Hasoň <martin.hason@gmail.com>
 */
final class SecurityNotAllowedFunctionError extends SecurityError
{
    private $functionName;

    public function __construct(string $message, string $functionName)
    {
        parent::__construct($message);
        $this->functionName = $functionName;
    }

    public function getFunctionName(): string
    {
        return $this->functionName;
    }
}
