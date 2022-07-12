<?php

function attachemntMetaDataMock() {
	return [
		'width' => 1180,
		'height' => 1860,
		'file' => '2022/05/test.png',
		'sizes' => [
			'thumbnail' => [
				'file' => 'test-150x150.png',
				'width' => 150,
				'height' => 150,
				'mime-type' => 'image/png',
			],
		],
	];
}

function attachemntMetaDataBrokenMock() {
	return [
		'width' => 1180,
		'height' => 1860,
		'sizes' => [
			'thumbnail' => [
				'width' => 150,
				'height' => 150,
				'mime-type' => 'image/png',
			],
		],
	];
}
