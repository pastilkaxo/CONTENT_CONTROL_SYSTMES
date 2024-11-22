<?php

namespace Drupal\slogan_module\Controller;

use Drupal\Core\Controller\ControllerBase;

class SloganController extends ControllerBase {
    public function sloganPage() {
        $slogan = \Drupal::config('system.site')->get('slogan');
        return [
            '#markup' => $this->t('The site slogan is: @slogan', ['@slogan' => $slogan]),
        ];
    }
}
