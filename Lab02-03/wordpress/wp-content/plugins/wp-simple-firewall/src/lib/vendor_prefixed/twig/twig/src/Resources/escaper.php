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

use AptowebDeps\Twig\Environment;
use AptowebDeps\Twig\Extension\EscaperExtension;
use AptowebDeps\Twig\Node\Node;
use AptowebDeps\Twig\Runtime\EscaperRuntime;

/**
 * @internal
 *
 * @deprecated since Twig 3.9
 */
function twig_raw_filter($string)
{
    trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);

    return $string;
}

/**
 * @internal
 *
 * @deprecated since Twig 3.9
 */
function twig_escape_filter(Environment $env, $string, $strategy = 'html', $charset = null, $autoescape = false)
{
    trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);

    return $env->getRuntime(EscaperRuntime::class)->escape($string, $strategy, $charset, $autoescape);
}

/**
 * @internal
 *
 * @deprecated since Twig 3.9
 */
function twig_escape_filter_is_safe(Node $filterArgs)
{
    trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);

    return EscaperExtension::escapeFilterIsSafe($filterArgs);
}
