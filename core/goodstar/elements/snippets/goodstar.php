<?php
/** @var modX $modx */
/** @var array $scriptProperties */
/** @var goodStar $goodStar */
$goodStar = $modx->getService('goodStar', 'goodStar', $modx->getOption('goodstar_core_path' ,'', MODX_CORE_PATH . 'components/goodstar/') . 'model/', $scriptProperties);
if (!$goodStar) {
    return 'Could not load goodStar class!';
}

$thread = !empty($thread) ? $thread : $modx->resource->id;
$tpl = !empty($tpl) ? $tpl : 'tpl.goodStar';
$group = !empty($group) ? $group : '';
$conclusion = !empty($conclusion) ? $conclusion : 'wilson';
$onlyAuth = !empty($onlyAuth) ? $onlyAuth : false;
$readonly = !empty($readonly) ? $readonly : false;

$current_rating = $goodStar->getCurrentRating($thread, $conclusion);
$count_voite = $goodStar->getCountVoite($thread);

$readonly  = $goodStar->getReadOnly($thread, $readonly, $onlyAuth);

$arr = array(
    'id' => $thread,
    'current_rating' => $current_rating,
    'user_rating' => $goodStar->getCurrentRating($thread, 'user'),
    'group' => $group,
    'count_voite' => $count_voite,
    'readonly' => $readonly,
    'name' => $goodStar->getName($thread)
);

/**
 * @var pdoTools $pdoTools
 */
if($pdoTools = $modx->getService('pdotools')){
    $output = $pdoTools->getChunk($tpl, $arr);
}else{
    $output = $modx->getChunk($tpl, $arr);
}

return $output;