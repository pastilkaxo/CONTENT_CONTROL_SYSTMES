<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AdminTools\Administrator\Controller;

defined('_JEXEC') || die;

use Akeeba\Component\AdminTools\Administrator\Mixin\ControllerCustomACLTrait;
use Akeeba\Component\AdminTools\Administrator\Mixin\ControllerEventsTrait;
use Akeeba\Component\AdminTools\Administrator\Mixin\ControllerRegisterTasksTrait;
use Akeeba\Component\AdminTools\Administrator\Mixin\ControllerReusableModelsTrait;
use Akeeba\Component\AdminTools\Administrator\Model\JupdateModel;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;

class JupdateController extends BaseController
{
	use ControllerEventsTrait;
	use ControllerCustomACLTrait;
	use ControllerRegisterTasksTrait;
	use ControllerReusableModelsTrait;

	public function main()
	{
		$this->display(false);
	}

	public function reset()
	{
		$this->checkToken();

		/** @var JupdateModel $model */
		$model = $this->getModel();

		$model->resetJoomlaUpdate();

		$message = Text::_('COM_ADMINTOOLS_JUPDATE_LBL_RESET');
		$this->setMessage($message, 'success');

		$redirectUrl = Route::_('index.php?option=com_admintools&view=Jupdate', false);
		$this->setRedirect($redirectUrl);
	}
}