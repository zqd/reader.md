<?php



// START SETTINGS
if ( is_readable(__DIR__.'/settings.php') ) {
	require_once __DIR__.'/settings.php';
} else if ( is_readable(__DIR__.'/settings.custom.php') ) {
	require_once __DIR__.'/settings.custom.php';
} else {
	require_once __DIR__.'/settings.example.php';
} // END SETTINGS



// START READABLE
if (
	is_readable($Request['Directory']) ||
	is_readable($Request['Markdown'])
) {



	// START INDEX
	if ( is_dir($Request['Directory']) ) {



		// START INDEX-MD
		// Load `index.md` instead if available
		$Request['Index'] = $Request['Directory'].'/index.md';
		if ( is_readable($Request['Index']) ) {
			require_once $MDR['Core'].'/function.render_markdown.php';
			Render_Markdown($Request['Index']);
		// END INDEX-MD



		// START AUTO-INDEX
		} else {

			// Index Header
			include $Templates['Header'];
			require_once $MDR['Core'].'/function.url_to_title.php';
			$Title = url_to_title($Request['Trimmed']);
			if ( !empty($Title) ) {
				echo '<h2>'.$Title.'</h2>';
			}

			// Find Suitable Files
			require_once $MDR['Core'].'/function.find_files.php';
			$Files = Find_Files($Request['Directory']);
			ksort($Files);

			require_once $MDR['Core'].'/function.title_files.php';
			$Files = Title_Files($Files);

			// List suitable files, or error accordingly.
			if ( empty($Files) ) {
				// Don't 404, because the directory does exist.
				echo '<h3>'.$Lang['en']['NO_FILES_IN_DIRECTORY'].'</h3>';
			} else {
				require_once $MDR['Core'].'/function.list_files.php';
				echo List_Files($Files);
			}

			// Footer
			include $Templates['Footer'];

		} // END AUTO-INDEX



	// END INDEX



	// START FILE
	} else {

		// START FALSE-DIRECTORY
		if ( is_readable($Request['Directory']) ) {
			// Apparently this isn't a directory, just a poorly named file.
			require_once $MDR['Core'].'/function.render_markdown.php';
			Render_Markdown($Request['Directory']);
		// END FALSE-DIRECTORY

		// START ACTUAL
		} else {
			require_once $MDR['Core'].'/function.render_markdown.php';
			Render_Markdown($Request['Markdown']);
		} // END ACTUAL

	} // END FILE



// END READABLE



// START NON-EXISTANT
} else {

	// Headers MUST be sent before any content.
	header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');

	include $Templates['Header'];

	require_once $MDR['Core'].'/function.url_to_title.php';
	$Title = url_to_title($Request['Trimmed']);
	if ( !empty($Title) ) {
		echo '<h2>'.$Title.'</h2>';
	}

	echo '<h3>'.$Lang[$Settings['Language']]['FILE_NOT_FOUND'].'</h3>';

	include $Templates['Footer'];

} // END NON-EXISTANT