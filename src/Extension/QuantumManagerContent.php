<?php

namespace Joomla\Plugin\Content\QuantumManagerContent\Extension;

/**
 * @package    quantummanagercontent
 * @author     Dmitry Tsymbal <cymbal@delo-design.ru>
 * @copyright  Copyright © 2019 Delo Design & NorrNext. All rights reserved.
 * @license    GNU General Public License version 3 or later; see license.txt
 * @link       https://www.norrnext.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\Event\Content\ContentPrepareEvent;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\SubscriberInterface;
use Joomla\Plugin\Button\QuantumManagerButton\Helper\ButtonHelper;

class QuantumManagerContent extends CMSPlugin implements SubscriberInterface
{
	protected $autoloadLanguage = true;

	public static function getSubscribedEvents(): array
	{
		return [
			'onContentPrepare' => 'onContentPrepare',
		];
	}

	public function onContentPrepare(ContentPrepareEvent $event)
	{
		$context = $event->getContext();
		$item    = $event->getItem();

		if (isset($item->text))
		{
			$item->text = $this->prepare($item->text, $context, $item);
		}

		if (isset($item->introtext))
		{
			$item->introtext = $this->prepare($item->introtext, $context, $item);
		}
	}

	private function prepare(mixed $string, mixed $context, mixed $item): string
	{

		if (!is_string($string))
		{
			return $string;
		}

		if (strpos($string, '[qmcontent]') === false)
		{
			return $string;
		}

		$regex  = "/\[qmcontent\](.*?)\[\/qmcontent\]/i";
		$string = preg_replace_callback($regex, static function ($matches) {
			$output    = '';
			$content   = &$matches[1];
			$before    = '';
			$variables = '';
			$item      = '';
			$after     = '';

			preg_replace_callback("/\[before\](.*?)\[\/before\]/i", function ($matchesBefore) use (&$before, &$output) {
				if (preg_match("#^\{\{.*?\}\}$#isu", $matchesBefore[1]))
				{
					$before = str_replace(['{', '}'], '', $matchesBefore[1]);
					$output .= ButtonHelper::renderLayout($before);
				}
				else
				{
					$output .= $before;
				}

			}, $content);

			preg_replace_callback("/\[template\](.*?)\[\/template\]/i", function ($matchesBefore) use (&$item) {
				if (preg_match("#^\{\{.*?\}\}$#isu", $matchesBefore[1]))
				{
					$item = str_replace(['{', '}'], '', $matchesBefore[1]);
					$item = ButtonHelper::renderLayout($item);
				}
				else
				{
					$item = $matchesBefore[1];
				}
			}, $content);

			preg_replace_callback("/\[variables\](.*?)\[\/variables\]/i", function ($matchesBefore) use (&$variables) {
				$variables = $matchesBefore[1];
			}, $content);

			if (!empty($variables) && !empty($item))
			{

				$variables = json_decode($variables, JSON_OBJECT_AS_ARRAY);

				if (is_array($variables) && count($variables) > 0)
				{
					foreach ($variables as $variable)
					{
						$outputItem       = $item;
						$variablesFind    = [];
						$variablesReplace = [];

						foreach ($variable as $key => $value)
						{
							$variablesFind[]    = $key;
							$variablesReplace[] = $value;
						}

						$outputItem = str_replace($variablesFind, $variablesReplace, $outputItem);
						$outputItem = preg_replace("#[a-zA-Z]{1,}\=\"\"#isu", '', $outputItem);
						$output     .= $outputItem;
					}
				}
			}

			preg_replace_callback("/\[after\](.*?)\[\/after\]/i", function ($matchesBefore) use (&$after, &$output) {
				if (preg_match("#^\{\{.*?\}\}$#isu", $matchesBefore[1]))
				{
					$after  = str_replace(['{', '}'], '', $matchesBefore[1]);
					$output .= ButtonHelper::renderLayout($after);
				}
				else
				{
					$output .= $after;
				}

			}, $content);

			return $output;
		}, $string);

		return $string;
	}

}
