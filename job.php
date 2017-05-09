<?php

include 'Mycurl.php';
include 'ProgressBar.php';
include 'Db.php';
include 'Repository.php';

/**
 * @param $array
 *
 * @return null|string
 */
function arrayToUrl($array)
{
    $return = null;
    foreach ($array as $key => $value) {
        $return .= $key . '=' . $value . '&';
    }

    return $return;
}

/**
 * @param $oMycurl
 * @param $aList
 * @param $levelDetail
 *
 * @return mixed
 */
function getCarInfos(Mycurl $oMycurl, $aList, $levelDetail)
{
    $repository = null;
    if ($levelDetail === 1) {
        $repository = new Repository();
    }

    // get results HTML
    preg_match(
        '#(<div\s*class="\s*resultList.*<!-- end file tb_item_listing.php -->)#s',
        $oMycurl->getWebPage(),
        $matches
    );	

    libxml_use_internal_errors(true);
    $dom_ban = new domDocument;
    $dom_ban->loadHTML($matches[0]);
    $xpath = new DOMXPath($dom_ban);

    $rows = $xpath->query(
        "//div[contains(attribute::class, 'resultList')]" .
        "//div[contains(attribute::class, 'adContainer')]"
    );

    foreach ($rows as $cols) {
        $brandModelTitle = $cols->getElementsByTagName('h3')->item(0)->getElementsByTagName('span');

        $marque      = trim($brandModelTitle->item(0)->nodeValue);
        $modele      = trim($brandModelTitle->item(1)->nodeValue);
        $version     = trim($brandModelTitle->item(2)->nodeValue);

        if ($levelDetail == 2) {
            if (!isset($aList[$marque][$modele])) {
                $aList[$marque][$modele] = 0;
            }

            $aList[$marque][$modele] ++;
        } elseif ($levelDetail == 3) {
            if (!isset($aList[$marque])) {
                $aList[$marque] = 0;
            }

            $aList[$marque] ++;
        } elseif ($levelDetail == 4) {
            if (!isset($aList[$marque][$modele][$version])) {
                $aList[$marque][$modele][$version] = 0;
            }

            $aList[$marque][$modele][$version] ++;
        } else {
            $idAnnonce = (int) trim(
                $cols
                    ->getElementsByTagName('p')->item(0)
                    ->getElementsByTagName('a')->item(0)
                    ->getAttribute('data-annid')
            );

            $aList[$idAnnonce]['idAnnonce'] = $idAnnonce;
            $aList[$idAnnonce]['idSearch']  = 0;

            $aList[$idAnnonce]['brand']  = $marque;
            $aList[$idAnnonce]['model']  = $modele;
            $aList[$idAnnonce]['version'] = $version;

            $aList[$idAnnonce]['seller'] = trim($cols->getElementsByTagName('p')->item(2)->nodeValue);
            $aList[$idAnnonce]['dept'] = trim($cols->getElementsByTagName('i')->item(0)->nodeValue);
            $aList[$idAnnonce]['year']       = trim($cols->getElementsByTagName('div')->item(10)->nodeValue);

            $aList[$idAnnonce]['km'] = trim(
                str_replace(
                    [' ', 'km'],
                    '',
                    $cols->getElementsByTagName('div')->item(11)->nodeValue
                )
            );
            $aList[$idAnnonce]['price'] = trim(
                str_replace(
                    [' ', '€'],
                    '',
                    $cols->getElementsByTagName('div')->item(12)->nodeValue
                )
            );

            $repository->insertHistory($aList[$idAnnonce]);
        }
    }

    return $aList;
}

/**
 * @param $aParams
 *
 * @return array
 */
function paramsFormatter($aParams)
{
    $aNewParams = [];
    foreach ($aParams as $key => $value) {
        if ($value !== '') {
            $aNewParams[$key] = $value;
        }
    }

    /**
     * Categories
     */
    if (isset($aNewParams['SS_CATEGORIE'])) {
        $aNewParams['SS_CATEGORIE'] = implode(',', $aNewParams['SS_CATEGORIE']);
    }

    /**
     * pdin
     */
    if (isset($aNewParams['pdin1'])) {
        $aNewParams['pdin'] = $aNewParams['pdin1'];
        unset($aNewParams['pdin1']);
    } else {
        $aNewParams['pdin'] = 0;
    }

    if (isset($aNewParams['pdin2'])) {
        $aNewParams['pdin'] .= '|' . $aNewParams['pdin2'];
        unset($aNewParams['pdin2']);
    } else {
        $aNewParams['pdin'] .= '|-1';
    }

    if ($aNewParams['pdin'] === '0|-1') {
        unset($aNewParams['pdin']);
    }

    /**
     * pfisc
     */
    if (isset($aNewParams['pfisc1'])) {
        $aNewParams['pfisc'] = $aNewParams['pfisc1'];
        unset($aNewParams['pfisc1']);
    } else {
        $aNewParams['pfisc'] = 0;
    }

    if (isset($aNewParams['pfisc2']) && isset($aNewParams['pfisc2']) < $aNewParams['pfisc']) {
        unset($aNewParams['pfisc2']);
    }

    if (isset($aNewParams['pfisc2'])) {
        for ($i = $aNewParams['pfisc'] + 1; $i <= $aNewParams['pfisc2']; $i ++) {
            $aNewParams['pfisc'] .= ',' . $i;
        }
        unset($aNewParams['pfisc2']);
    } else {
        for ($i = $aNewParams['pfisc'] + 1; $i <= 50; $i ++) {
            $aNewParams['pfisc'] .= ',' . $i;
        }
    }

    if (substr($aNewParams['pfisc'], - 2) == 50 && substr($aNewParams['pfisc'], 0, 1) == 0) {
        unset($aNewParams['pfisc']);
    }

    if (isset($aNewParams['prix_mini'])) {
        $aNewParams['prix_mini'] = str_replace(' ', '', $aNewParams['prix_mini']);
    }
    if (isset($aNewParams['prix_maxi'])) {
        $aNewParams['prix_maxi'] = str_replace(' ', '', $aNewParams['prix_maxi']);
    }
    if (isset($aNewParams['km_mini'])) {
        $aNewParams['km_mini'] = str_replace(' ', '', $aNewParams['km_mini']);
    }
    if (isset($aNewParams['km_maxi'])) {
        $aNewParams['km_maxi'] = str_replace(' ', '', $aNewParams['km_maxi']);
    }

    return $aNewParams;
}

/**
 * @return string
 */
function getDomain()
{
    return 'http://www.lacentrale.fr/';
}

/**
 * @param $aParams
 *
 * @return string
 */
function getUri($aParams)
{
    return 'listing_auto.php?' . arrayToUrl($aParams);
}

/**
 * @param $aParams
 *
 * @return array|mixed
 */
function getForLive($aParams)
{
    $oMycurl = new Mycurl;

    $levelDetail = 2;

    $aParams['num'] = '1';

    $aList = initCarsGet($oMycurl, $aParams, $levelDetail);

    $fileProgressBar = new ProgressBar(session_id());

    //boucle sur les autres pages si besoin
    if ($aList['numberPage'] > 1) {
        $fileProgressBar->setValue(round($aParams['num'] * 100 / $aList['numberPage']));

        for ($i = 2; $i <= $aList['numberPage']; $i ++) {
            $aParams['num'] = $i;

            $fileProgressBar->setValue(round($aParams['num'] * 100 / $aList['numberPage']));

            $aList = getCars($oMycurl, $aParams, $levelDetail, $aList);
        }
    }

    $fileProgressBar->reset();

    $oMycurl->curlClose();

    return $aList;
}

/**
 * @param $aParams
 *
 * @return array|mixed
 */
function getForData($aParams)
{
    $oMycurl = new Mycurl;

    $levelDetail = 1;
    echo '<pre>';

    $aParams['num'] = '1';

    $aList = initCarsGet($oMycurl, $aParams, $levelDetail);

    //boucle sur les autres pages si besoin
    if ($aList['numberPage'] > 1) {
        for ($i = 2; $i <= $aList['numberPage']; $i ++) {
            $aParams['num'] = $i;

            $aList = getCars($oMycurl, $aParams, $levelDetail, $aList);
        }
    }

    $oMycurl->curlClose();

    return $aList;
}

/**
 * @param Mycurl $oMycurl
 * @param array  $aParams
 * @param int    $levelDetail
 * @param array  $aList
 *
 * @return mixed
 */
function getCars(Mycurl $oMycurl, array $aParams, int $levelDetail, array $aList)
{
    $oMycurl->createCurl(getUri($aParams));
    return getCarInfos($oMycurl, $aList, $levelDetail);
}

/**
 * @param Mycurl $oMycurl
 * @param array  $aParams
 * @param int    $levelDetail
 *
 * @return array
 */
function initCarsGet(Mycurl $oMycurl, array $aParams, int $levelDetail) :array
{
    $oMycurl->createCurl(getUri($aParams));
    $aList = getCarInfos($oMycurl, [], $levelDetail);

    //Get number of the last page
    preg_match_all('#<a.*href="\?.*num=([0-9]*)&.*".*>.*</a>#U', $oMycurl->getWebPage(), $aMatches);

    $iLastPage = null;
    foreach ($aMatches[1] as $sNum) {
        if ($sNum > $iLastPage) {
            $iLastPage = $sNum;
        }
    }

    $aList['numberPage'] = $iLastPage;

    return $aList;
}
