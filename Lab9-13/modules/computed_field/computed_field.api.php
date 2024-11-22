<?php

/**
 * @file
 * Hooks provided by the Computed field module.
 */

/**
 * @addtogroup hooks
 * @{
 */

/**
 * Perform alterations on Computed Field definitions.
 *
 * @param array &$info
 *   Array of information on Computed Field plugins.
 */
function hook_computed_field_info_alter(array &$info) {
  // Change the class of the 'foo' plugin.
  $info['foo']['class'] = SomeOtherClass::class;
}

/**
 * @} End of "addtogroup hooks".
 */
