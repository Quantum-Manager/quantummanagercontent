<?php
/**
 * @package    quantummanagercontent
 * @author     Dmitry Tsymbal <cymbal@delo-design.ru>
 * @copyright  Copyright © 2019 Delo Design & NorrNext. All rights reserved.
 * @license    GNU General Public License version 3 or later; see license.txt
 * @link       https://www.norrnext.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Session\Session;

class PlgButtonQuantummanagercontent extends CMSPlugin
{
	/**
	 * Load the language file on instantiation.
	 *
	 * @var  boolean
	 *
	 * @since   1.1.0
	 */
	protected $autoloadLanguage = true;


	/**
	 * @param $context
	 * @param $article
	 * @param $params
	 * @param $page
	 *
	 *
	 * @since version
	 */
	public function onContentPrepare($context, $article, $params, $page)
	{

	}

}
