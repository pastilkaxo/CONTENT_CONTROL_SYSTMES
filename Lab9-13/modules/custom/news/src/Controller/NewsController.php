<?php

namespace Drupal\news\Controller;

use Drupal\Core\Controller\ControllerBase;

class NewsController extends ControllerBase {
    public function newsPage() {
        return [
            '#markup' => '<h1>News</h1>',
        ];
    }
}
