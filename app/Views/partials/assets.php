<?php
// Dynamic asset loader for the frontend bundle.
// It prefers a Vite manifest (manifest.json) in app/public/frontend.
$frontendDir = __DIR__ . '/../../public/frontend';
$frontendUrlBase = '/TCC-etec/app/public/frontend';

function findAssetsFromManifest($manifestPath) {
	$result = ['js' => null, 'css' => null];
	if (!file_exists($manifestPath)) return $result;
	$json = json_decode(file_get_contents($manifestPath), true);
	if (!is_array($json)) return $result;
	// Find the first entry that is an entry (has 'isEntry' true) or the first key
	foreach ($json as $key => $entry) {
		if (!empty($entry['isEntry'])) {
			// main file
			if (!empty($entry['file'])) $result['js'] = $entry['file'];
			if (!empty($entry['css']) && is_array($entry['css'])) $result['css'] = $entry['css'][0];
			return $result;
		}
	}
	// fallback: pick first entry
	reset($json);
	$first = current($json);
	if (!empty($first['file'])) $result['js'] = $first['file'];
	if (!empty($first['css']) && is_array($first['css'])) $result['css'] = $first['css'][0];
	return $result;
}

$manifestPath = $frontendDir . '/manifest.json';
$assets = ['js' => null, 'css' => null];
if (file_exists($manifestPath)) {
	$assets = findAssetsFromManifest($manifestPath);
} else {
	// fallback: scan directory for main.*.js and style.*.css or any .js/.css
	if (is_dir($frontendDir)) {
		$files = scandir($frontendDir);
		foreach ($files as $f) {
			if (preg_match('/\.js$/i', $f) && $assets['js'] === null) $assets['js'] = $f;
			if (preg_match('/\.css$/i', $f) && $assets['css'] === null) $assets['css'] = $f;
		}
	}
}

// Render tags
if (!empty($assets['css'])) {
	$cssUrl = $frontendUrlBase . '/' . ltrim($assets['css'], '/');
	echo "<link rel=\"stylesheet\" href=\"" . htmlspecialchars($cssUrl, ENT_QUOTES) . "\">\n";
}
if (!empty($assets['js'])) {
	$jsUrl = $frontendUrlBase . '/' . ltrim($assets['js'], '/');
	echo "<script type=\"module\" src=\"" . htmlspecialchars($jsUrl, ENT_QUOTES) . "\" defer></script>\n";
}

