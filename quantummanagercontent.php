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

class PlgContentQuantummanagercontent extends CMSPlugin
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
	 * @param $item
	 * @param $params
	 * @param int $page
	 *
	 *
	 * @since version
	 */
	public function onContentPrepare($context, &$item, &$params, $page = 0)
	{
		// Prepare the text
		if (isset($item->text))
		{
			$item->text = $this->prepare($item->text, $context, $item);
		}

		// Prepare the intro text
		if (isset($item->introtext))
		{
			$item->introtext = $this->prepare($item->introtext, $context, $item);
		}

	}


	/**
	 * @param $string
	 * @param $context
	 * @param $item
	 *
	 * @return string|string[]|null
	 *
	 * @since version
	 */
	private function prepare($string, $context, $item)
	{

		if(strpos($string, '[qmcontent]') === false)
		{
			return $string;
		}

		JLoader::register('QuantummanagerbuttonHelper', JPATH_ROOT . '/plugins/editors-xtd/quantummanagerbutton/helper.php');
		$regex = "/\[qmcontent\](.*?)\[\/qmcontent\]/i";
		$render = $this->render;
		$string = preg_replace_callback($regex, static function ($matches) use ($render) {
			$output = '';
			$content = &$matches[1];
			$before = '';
			$variables = '';
			$item = '';
			$after = '';
			preg_replace_callback("/\[before\](.*?)\[\/before\]/i", function ($matchesBefore) use (&$before) {
				$before = str_replace(['{', '}'], '', $matchesBefore[1]);
			}, $content);

			preg_replace_callback("/\[template\](.*?)\[\/template\]/i", function ($matchesBefore) use (&$item) {
				$item = str_replace(['{', '}'], '', $matchesBefore[1]);
			}, $content);

			preg_replace_callback("/\[variables\](.*?)\[\/variables\]/i", function ($matchesBefore) use (&$variables) {
				$variables = $matchesBefore[1];
			}, $content);

			preg_replace_callback("/\[after\](.*?)\[\/after\]/i", function ($matchesBefore) use (&$after) {
				$after = str_replace(['{', '}'], '', $matchesBefore[1]);
			}, $content);

			if(!empty($before) && !empty($variables) && !empty($item) && !empty($after))
			{


				$output = QuantummanagerbuttonHelper::renderLayout($before);

				$variables = json_decode($variables, JSON_OBJECT_AS_ARRAY);

				if(is_array($variables) && count($variables) > 0)
				{
					foreach ($variables as $variable)
					{
						$outputItem = QuantummanagerbuttonHelper::renderLayout($item);
						$variablesFind = [];
						$variablesReplace = [];

						foreach ($variable as $key => $value)
						{
							$variablesFind[] = $key;
							$variablesReplace[] = $value;
						}

						$outputItem = str_replace($variablesFind, $variablesReplace, $outputItem);
						$outputItem = preg_replace("#[a-zA-Z]{1,}\=\"\"#isu", '', $outputItem);
						$output .= $outputItem;
					}
				}

				$output .= QuantummanagerbuttonHelper::renderLayout($after);
			}

			return $output;
		}, $string);

		return $string;
	}


}
