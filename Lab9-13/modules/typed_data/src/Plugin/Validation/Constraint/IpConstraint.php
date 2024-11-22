<?php

namespace Drupal\typed_data\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraints\Ip;

/**
 * IP address constraint.
 *
 * @Constraint(
 *   id = "Ip",
 *   label = @Translation("IP", context = "Validation"),
 *   type = {"ip_address"}
 * )
 */
class IpConstraint extends Ip {

  /**
   * {@inheritdoc}
   */
  public function validatedBy() {
    return '\Symfony\Component\Validator\Constraints\IpValidator';
  }

}
