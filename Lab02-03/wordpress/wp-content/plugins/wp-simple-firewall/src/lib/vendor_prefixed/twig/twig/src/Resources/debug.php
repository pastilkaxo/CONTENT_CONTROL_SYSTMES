<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Modified by Paul Goodchild on 19-July-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

use AptowebDeps\Twig\Environment;
use AptowebDeps\Twig\Extension\DebugExtension;

/**
 * @internal
 *
 * @deprecated since Twig 3.9
 */
function twig_var_dump(Environment $env, $context, ...$vars)
{
    trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);

    DebugExtension::dump($env, $context, ...$vars);
}
