<?php
/** @var modX $modx */
switch ($modx->event->name) {

    case 'OnLoadWebDocument':
        /**
         * @var goodStar $goodStar
         */

        $goodStar = $modx->getService('goodStar', 'goodStar', $modx->getOption('goodstar_core_path' ,'', MODX_CORE_PATH . 'components/goodstar/') . 'model/', $scriptProperties);
        if (!$goodStar) {
            return 'Could not load goodStar class!';
        }

        $goodStar->initialize();

        break;

}