<?php

require_once __DIR__ . '/lib/functions.php';

use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;

Kirby::plugin('sylvainjule/annotator', array(
	'sections' => array(
        'annotator' => array(
        	'props' => array(
        		'debug' => function($debug = false) {
        			return $debug;
        		},
        		'theme' => function($theme = 'light') {
        			return $theme;
        		},
        		'tools' => function($tools = ['pin', 'rect', 'circle']) {
        			return $tools;
        		},
        		'colors' => function($colors = ['orange', 'yellow', 'green', 'blue', 'purple', 'pink']) {
        			return $colors;
        		},
        		'storage' => function($storage = []) {
                    return $storage;
                },
              'imageMethod' => function($imageMethod = null) {
                return $imageMethod;
              },
        	),
        	'computed' => array(
                'image' => function() {
            $imageMethod = $this->imageMethod();
            $path = $this->model()->$imageMethod();

					// Get the blob container and the prefix we are looking for
					$blob = explode('/', $path)[0];
					$prefix = str_replace($blob . '/', '', $path);

					// Connect to blob storage
					$blobClient = BlobRestProxy::createBlobService(kirby()->option('AZURE_BLOB_MEDIA_CONNECTION_STRING'));

					// Find the file
					$listBlobsOptions = new ListBlobsOptions();
					$listBlobsOptions->setPrefix($prefix);

					$blobList = $blobClient->listBlobs($blob , $listBlobsOptions);

					foreach ($blobList->getBlobs() as $blob) {
						// If found, return url of the file
						return cloudinary_url($blob->getUrl(), [
						'type'    => 'fetch',
						'width'   => 800
						]);
					}
                }
            )
        ),
    ),
    'fieldMethods' => require_once __DIR__ . DS . 'lib' . DS . 'fieldMethods.php',
));
