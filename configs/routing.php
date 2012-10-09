<?php

// Define all of our routes (we could do this automatically, but we'd like more control)
$load_config = function() {
	return array(
		'load_all_automatically' => false,
		'routes' => array(
			'sample',
		),
	);
};
