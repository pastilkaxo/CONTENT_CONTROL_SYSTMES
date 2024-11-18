<?php

/**
 * @package         Google Structured Data
 * @version         5.6.5 Free
 *
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2022 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

namespace GSD\Schemas\Schemas;

// No direct access
defined('_JEXEC') or die;

use GSD\Helper;
use NRFramework\DOMCrawler;

class FAQ extends \GSD\Schemas\Base
{
    /**
     * Return all the schema properties
     *
     * @return void
     */
    protected function initProps()
    {
        $mode = $this->data->get('mode', 'auto');

        $faq = $this->data['faq_repeater_fields'];
        
        $allowed_tags = '<h1><h2><h3><h4><h5><h6><br><ol><ul><li><p><a><div><b><strong><i><em>';
        
        $faqData = [];

        switch ($mode)
        {
            // Manual Mode
            case 'manual':
                foreach ($faq as $item)
                {
                    $question = trim($item->question);
                    $question = preg_replace('/\s\s+/', ' ', $question);
                    $question = strip_tags($question);

                    $answer = trim($item->answer);
                    $answer = strip_tags($answer, $allowed_tags);

                    $faqData[] = [
                        'question' => $question,
                        'answer'   => $answer
                    ];
                }
                break;

            // Auto Mode
            case 'auto':
                $question_selector = $this->data->get('question_selector', '.question');
                $answer_selector = $this->data->get('answer_selector', '.answer');

                // Find questions and answersr
                $questions = $this->crawl($question_selector);
                $answers = $this->crawl($answer_selector);

                // Combine the Q&A
                if (count($questions) && count($answers))
                {
                    $counter = 0;
                    foreach ($questions as $q)
                    {
                        $question = trim($q['value']);

                        $answer = isset($answers[$counter]['html']) ? $answers[$counter]['html'] : '';

                        // Remove spaces, new lines, invalid HTML tags and empty paragraphs.
                        $answer = preg_replace('/\s\s+/', ' ', $answer);
                        $answer = strip_tags($answer, $allowed_tags);
                        $answer = preg_replace('/<p>\s*<\/p>/', '', $answer);
                        $answer = trim($answer);

                        $faqData[] = [
                            'question' => $question,
                            'answer' => $answer
                        ];

                        $counter++;
                    }
                } else 
                {
                    Helper::log([
                        'Error'             => 'No FAQs found',
                        'Question Selector' => $question_selector,
                        'Questions Found'   => count($questions),
                        'Answer Selector'   => $answer_selector,
                        'Answers Found'     => count($answers)
                    ]);
                }
        }
        
        $this->data->set('faqs', $faqData);

        parent::initProps();
    }

    /**
	 * Find the FAQ content using XPath based on the provided selector
	 * 
	 * @param  string  $content   The content to search
	 * @param  string  $selector  The selector used for the search
	 * 
	 * @return array
	 */
	private function crawl($selector)
	{
		$data = [];
		$crawler = new DOMCrawler();

		if ($nodes = $crawler->filter($selector)->nodes)
		{
			foreach ($nodes as $node)
			{
				// Remove attributes from node
				$html = $this->removeAttributesFromNode($node);

				$data[] = [
					'html' => $html,
					'value' => $node->nodeValue
                ];
			}
		}
		return $data;
	}

    /**
	 * Loop through all elements in the node that have an attribute
	 * and remove them
	 * 
	 * @param   DOMElement  $domNode
	 * 
	 * @return  string
	 */
	private function removeAttributesFromNode($domNode)
	{
		// Loop through all elements that have an attribute and remove it
		$dom = new \DOMDocument;
		$content = $domNode->ownerDocument->saveHTML($domNode);
		$dom->loadHTML(self::stringToUTF8($content));
		$xpath = new \DOMXPath($dom);
		$nodes = $xpath->query('//@*');
		foreach ($nodes as $node)
		{
			// Skip the href attribute which is allowed
			if ($node->nodeName == 'href')
			{
				// Fix relative URLs.
				$url = Helper::absURL($node->nodeValue);
				$node->parentNode->setAttribute('href', $url);
				continue;
			}

			$node->parentNode->removeAttribute($node->nodeName);
		}
		
		// return inner child only
		$html = '';
		foreach($dom->getElementsByTagName('body')->item(0)->firstChild->childNodes as $node) {
			$html .= $dom->saveHTML($node);
		}

		return $html;
	}

    /**
	 * Convert a string to UTF8 encoding
	 * 
	 * @param  string
	 * 
	 * @return string
	 */
	private function stringToUTF8($string)
	{
		if (!function_exists('mb_convert_encoding'))
		{
			return $string;
		}

		return mb_encode_numericentity(iconv('UTF-8', 'UTF-8', $string), [0x80, 0x10FFFF, 0, 0x1FFFFF], 'UTF-8');
	}
}