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

namespace AptowebDeps\Twig\TokenParser;

use AptowebDeps\Twig\Node\EmbedNode;
use AptowebDeps\Twig\Node\Expression\ConstantExpression;
use AptowebDeps\Twig\Node\Expression\NameExpression;
use AptowebDeps\Twig\Node\Node;
use AptowebDeps\Twig\Token;

/**
 * Embeds a template.
 *
 * @internal
 */
final class EmbedTokenParser extends IncludeTokenParser
{
    public function parse(Token $token): Node
    {
        $stream = $this->parser->getStream();

        $parent = $this->parser->getExpressionParser()->parseExpression();

        [$variables, $only, $ignoreMissing] = $this->parseArguments();

        $parentToken = $fakeParentToken = new Token(/* Token::STRING_TYPE */ 7, '__parent__', $token->getLine());
        if ($parent instanceof ConstantExpression) {
            $parentToken = new Token(/* Token::STRING_TYPE */ 7, $parent->getAttribute('value'), $token->getLine());
        } elseif ($parent instanceof NameExpression) {
            $parentToken = new Token(/* Token::NAME_TYPE */ 5, $parent->getAttribute('name'), $token->getLine());
        }

        // inject a fake parent to make the parent() function work
        $stream->injectTokens([
            new Token(/* Token::BLOCK_START_TYPE */ 1, '', $token->getLine()),
            new Token(/* Token::NAME_TYPE */ 5, 'extends', $token->getLine()),
            $parentToken,
            new Token(/* Token::BLOCK_END_TYPE */ 3, '', $token->getLine()),
        ]);

        $module = $this->parser->parse($stream, [$this, 'decideBlockEnd'], true);

        // override the parent with the correct one
        if ($fakeParentToken === $parentToken) {
            $module->setNode('parent', $parent);
        }

        $this->parser->embedTemplate($module);

        $stream->expect(/* Token::BLOCK_END_TYPE */ 3);

        return new EmbedNode($module->getTemplateName(), $module->getAttribute('index'), $variables, $only, $ignoreMissing, $token->getLine(), $this->getTag());
    }

    public function decideBlockEnd(Token $token): bool
    {
        return $token->test('endembed');
    }

    public function getTag(): string
    {
        return 'embed';
    }
}
