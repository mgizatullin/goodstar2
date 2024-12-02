<?php
if (file_exists(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php')) {
    /** @noinspection PhpIncludeInspection */
    require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
} else {
    require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/config.core.php';
}

require_once MODX_CORE_PATH.'model/modx/modx.class.php';
$modx = new modX();
$modx->initialize('web');
$modx->getService('error','error.modError', '', '');

/** @var goodStar $goodStar */
$goodStar = $modx->getService('goodStar', 'goodStar', $modx->getOption('goodstar_core_path', '', MODX_CORE_PATH . 'components/goodstar/') . 'model/');
if (!$goodStar) {;
    return 'Could not load goodStar class!';
}

if (empty($_POST['group']) || preg_match("/^[a-zA-Z0-9]+$/", $_POST['group']) !== 1) {
	return;
}
else {
	$group = $_POST['group'];
}


if (!empty($_POST['thread']) && is_numeric($_POST['thread']) && !empty($_POST['vote']) && is_numeric($_POST['vote'])) {
	$thread = (int) $_POST['thread'];
	$vote = (int) $_POST['vote'];
	
	$data = array(
		'thread' => $thread,
		'group' => $group,
		'vote' => $vote
	);
	
    if ($thread > 1 && $vote > 0 && $vote < 6 && $goodStar->saveVoice($data)) {
        $newRating = $goodStar->getCurrentRating($_POST['thread'], 'average');
    	$newVoite = $goodStar->getCountVoite($_POST['thread']);
    	
    	if (!empty($newRating) && !empty($newVoite)) {
    		$output = array($newRating,$newVoite);
    		die(json_encode($output));
    	}
    }
}

// Early it was simple return $goodStar->saveVoice($_POST)