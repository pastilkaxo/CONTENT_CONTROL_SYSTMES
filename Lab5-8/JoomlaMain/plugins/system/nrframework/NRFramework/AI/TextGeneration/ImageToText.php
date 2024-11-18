<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            https://www.tassos.gr
 * @copyright       Copyright © 2024 Tassos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

namespace NRFramework\AI\TextGeneration;

defined('_JEXEC') or die;

use Joomla\CMS\Http\HttpFactory;

class ImageToText
{
    /**
     * Generate a caption for an image
     * 
     * @param   string  $imageUrl
     * 
     * @return  array
     */
    public function generate($imageUrl = '')
    {
        if (!$imageUrl)
        {
            return [
                'error' => true,
                'message' => 'Image URL is required',
            ];
        }

		$apiKey = \NRFramework\Helpers\Settings::getValue('openai_api_key');
		$apiEndpoint = 'https://api.openai.com/v1/chat/completions';

		// Disable SSL verification for local files
		$context = stream_context_create([
			'ssl' => [
				'verify_peer' => false,
				'verify_peer_name' => false,
			],
		]);
		
		$imageData = base64_encode(file_get_contents($imageUrl, false, $context));
		$url = "data:image/jpeg;base64,{$imageData}";

		$data = [
			'model' => 'gpt-4o-mini',
			'messages' => [
				[
					'role' => 'user',
					'content' => [
						[
							'type' => 'text',
							'text' => 'Describe this image with maximum 120 characters, spaces included.'
						],
						[
							'type' => 'image_url',
							'image_url' => [
								'url' => $url,
							],
						],
					],
				],
			],
			'max_tokens' => 50,
		];

		$headers = [
			'Content-Type' => 'application/json',
			'Authorization' => 'Bearer ' . $apiKey,
		];

		$error = false;
		$message = '';

		try {
			$http = HttpFactory::getHttp();
			$response = $http->post($apiEndpoint, json_encode($data), $headers);

			if ($response->code == 200)
			{
				$result = json_decode($response->body);
				$message = $result->choices[0]->message->content;
			}
			else
			{
				$error = true;

				$decodedResponse = json_decode($response->body, true);

				$message = isset($decodedResponse['error']['message']) ? $decodedResponse['error']['message'] : 'An error occurred while generating the caption.';
			}
		}
		catch (\Exception $e)
		{
			$error = true;
			$message = "Error: " . $e->getMessage();
		}

		return [
			'error' => $error,
			'message' => $message
		];
    }
}