<?php

namespace Drupal\news\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'News' Block.
 *
 * @Block(
 *   id = "news_block",
 *   admin_label = @Translation("News Block"),
 * )
 */
class NewsBlock extends BlockBase {
    public function build() {
        return [
            '#markup' => $this->t('This is a news block'),
        ];
    }
}
