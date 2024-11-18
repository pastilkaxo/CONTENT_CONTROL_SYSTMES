<?php

defined('_JEXEC') or die;

class NicepageModelProduct extends JModelForm
{
    /**
     * @param array $data
     * @param bool  $loadData
     *
     * @return false
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm('com_nicepage.product', 'display', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }
        return $form;
    }
}
