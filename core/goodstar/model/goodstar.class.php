<?php

class goodStar
{
    /** @var modX $modx */
    public $modx;


    /**
     * @param modX $modx
     * @param array $config
     */
    function __construct(modX &$modx, array $config = [])
    {
        $this->modx =& $modx;
        $corePath = $this->modx->getOption('goodstar_core_path', '', MODX_CORE_PATH . 'components/goodstar/');
        $assetsUrl = $this->modx->getOption('goodstar_assets_url', '', MODX_ASSETS_URL . 'components/goodstar/');

        $this->config = array_merge([
            'corePath' => $corePath,
            'modelPath' => $corePath . 'model/',

            'connectorUrl' => $assetsUrl . 'connector.php',
            'assetsUrl' => $assetsUrl,
            'cssUrl' => $assetsUrl . 'css/',
            'jsUrl' => $assetsUrl . 'js/',
        ], $config);

        $this->modx->addPackage('goodstar', $this->config['modelPath']);
        $this->modx->lexicon->load('goodstar:default');
    }

    function initialize($ctx = 'web', $scriptProperties = array()){

        $this->modx->regClientScript($this->modx->getOption('goodstar_jsUrl'));
        $this->modx->regClientScript($this->modx->getOption('goodstar_jsCustom'));
        if($this->modx->getOption('goodstar_theme')){
            $this->modx->regClientCSS('/assets/components/goodstar/js/jquery-bar-rating/dist/themes/' . $this->modx->getOption('goodstar_theme') . '.css');
        }else{
            $this->modx->regClientCSS('/assets/components/goodstar/js/jquery-bar-rating/dist/themes/css-stars.css');
        }

        if($this->modx->getOption('goodstar_hide_scheme') == 1){
            $this->modx->regClientCSS('/assets/components/goodstar/css/goodstar.css');
        }
        $data = json_encode(array_merge($this->config, array(
            'theme' => $this->modx->getOption('goodstar_theme'),
            'selector' => $this->modx->getOption('goodstar_selector')
        )));

        $this->modx->regClientStartupScript('<script type="text/javascript">goodStarConfig='.$data.';</script>');
    }

    function saveVoice(array $data, $cli = false){

        /**
         * Можно передавать юзера в голос. По сути необходимо только для накрутки голосов.
         * Можно сделать и через собыите, но через консоль будет проще передать уже готового рандомного пользователя
         */

        if($cli === true){
            $user = md5(time() . md5(rand(1,10000)));
        }else{
            $user = $this->modx->user->id ? $this->modx->user->id : md5(time() . md5(rand(1,10000)));
        }

        $save = array(
            'thread' => $data['thread'],
            'vote' => $data['vote'],
            'group' => $data['group'],
            'user' => $user
        );

        $save = $this->returnedEventValues($save);

        $where = array('thread' => $save['thread'], 'user' => $save['user']);
        if(!$this->modx->getCount('goodStarItem', $where)){
            $q = $this->modx->newObject('goodStarItem');
            $q->fromArray($save);
            $q->save();
        }else{
            $q = $this->modx->getObject('goodStarItem', $where);
            $q->set('vote', $save['vote']);
            $q->save();
        }

        // Считаем сумму голосов
        $q = $this->modx->newQuery('goodStarItem');
        $q->where(array(
            'thread' => $save['thread']
        ));
        $q->select('SUM(vote) as sum');
        $sum = $this->modx->getIterator('goodStarItem', $q);

        foreach ($sum as $s){
            $sumVotes = $s->get('sum');
        }

        // Считаем всего голосов
        $totalVotes = $this->modx->getCount('goodStarItem', $q);

        $wilson = $this->wilsonScore($sumVotes, $totalVotes);

        $where = array('thread' => $save['thread']);

        if(!$this->modx->getCount('goodStarVoteCount', $where)){
            $arr = array(
                'thread' => $save['thread'],
                'count' => $wilson,
                'countaverage' => $save['vote']
            );
            $q = $this->modx->newObject('goodStarVoteCount');
            foreach ($arr as $key => $val){
                $q->set($key, $val);
            }
            $q->save();
        }else{
            $countaverage = $sumVotes/$totalVotes;
            $q = $this->modx->getObject('goodStarVoteCount', $where);
            $q->set('count', $wilson);
            $q->set('countaverage', $countaverage);
            $q->save();
        }

        //Ставим куку от повторного голосования
        if($cli === false){
            $coockie = json_encode(array(
                'thread' => $save['thread'],
                'user' => $save['user']
            ));
            $name = 'goodStar-'.$data['thread'];
            setcookie($name, $coockie, time()+(60*60*24*30*6), '/');
        }

        return true;

    }

    function getCurrentRating($thread = '', $conclusion){
        if($thread){
            $currentRating = $this->modx->getObject('goodStarVoteCount', array('thread' => $thread));
            if(is_object($currentRating)){
                switch ($conclusion){

                    //Проверям, какой рейтинг выводить
                    case 'wilson':
                        return number_format(round($currentRating->get('count'), 1), 1, '.', ',');
                        break;

                    case 'average':
                        return $currentRating->get('countaverage');
                        break;

                    case 'user':
                        $coockie = json_decode($_COOKIE['goodStar-'.$thread], true);
                        $countVoite = $this->modx->getObject('goodStarItem', array(
                            'thread' => $coockie['thread'],
                            'user' => $coockie['user']
                        ));
                        if(is_object($countVoite)){
                            return $countVoite->get('vote');
                        }else{
                            return 0;
                        }
                        break;

                    default:
                        return 0;
                        break;
                }
            }
        }else{
            return 0;
        }
    }

    function getCountVoite($thread = ''){
        if($thread){
            $count = $this->modx->getCount('goodStarItem', array('thread' => $thread));
            return $count;
        }else{
            return 0;
        }
    }

    function getReadOnly($thread = '', $readonly = false, $onlyAuth = false){
        if($thread){

            // Проверяем юзера

            if($readonly){
                return $readonly = 1;
            }

            $user = $this->modx->user->id;
            if($onlyAuth && $user == 0){
                return $readonly = 1;
            }

            $coockie = json_decode($_COOKIE['goodStar-'.$thread], true);

            if($coockie['thread'] == $thread && $this->modx->getCount('goodStarItem', array('thread' => $thread, 'user' => $coockie['user']))){
                return "true";
            }
        }

        return;
    }

    function getName($thread = ''){
        if(!$thread){
            return '';
        }

        $resource = $this->modx->getObject('modResource', (int) $thread);
        if(is_object($resource)){
            return $resource->get('pagetitle');
        }else{
            return '';
        }
    }

    /**
     * Расчет рейтинга на основе шкалы голосов
     *
     * @param $sumVotes сумма всех голосов
     * @param $totalVotes Кол-во голосов
     * @param array $votesRange Диапазон возможных значений голосов
     * @return float|int
     */
    function wilsonScore($sumVotes, $totalVotes, $votesRange = [1, 2, 3, 4, 5])
    {
        if ($sumVotes > 0 && $totalVotes > 0) {
            $z = 1.64485;
            $vMin = min($votesRange);
            $vWidth = floatval(max($votesRange) - $vMin);
            $phat = ($sumVotes - $totalVotes * $vMin) / $vWidth / floatval($totalVotes);
            $rating = ($phat + $z * $z / (2 * $totalVotes) - $z * sqrt(($phat * (1 - $phat) + $z * $z / (4 * $totalVotes)) / $totalVotes)) / (1 + $z * $z / $totalVotes);
            return round($rating * $vWidth + $vMin, 6);
        }
        return 0;
    }

    public function returnedEventValues($save){
        if(isset($this->modx->event->returnedValues)){
            $this->modx->event->returnedValues = null;
        }

        $this->modx->invokeEvent('OnGoodStarVoice', array(
            'voice' => $save
        ));

        // Получаем значения с события
        if($this->modx->event->returnedValues){
            $data = $this->modx->event->returnedValues;
        }else{
            return $save;
        }

        foreach ($data as $key){
            return $key;
        }
    }



}