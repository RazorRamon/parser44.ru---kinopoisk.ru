<?php

set_time_limit(0);
include 'simple_html_dom.php';

// Функция, удаляющая из строки всё после встречи с определённым символом
function delete44($str,$symbol='') 
{ 
    return($strpos=mb_strpos($str,$symbol))!==false?mb_substr($str,0,$strpos,'utf8'):$str;
}

// Функция, забора дом-дерева указанной страницы
function curliandia_get($url)
{
//Инициализация cURL и задание адреса
$ch = curl_init($url);
//Установка опций
usleep(rand (1000, 3000)) ;
//usleep(7000) ;
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; rv:21.0) Gecko/20100101 Firefox/21.0');
curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION,true);
curl_setopt($ch, CURLOPT_COOKIEFILE, 'c:/WebServers/home/parser44.ru/www/cookies.txt'); //Подставляем куки раз 
curl_setopt($ch, CURLOPT_COOKIEJAR, 'c:/WebServers/home/parser44.ru/www/cookies.txt'); //Подставляем куки два 
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_REFERER, 'http://test.polycloud.ru/');
//выполнение (результат отобразится на экран)
$out = curl_exec($ch);
//Закрытие соединения
curl_close($ch);
return $out;
}
 
// Create DOM from URL or file
$html = str_get_html(curliandia_get('http://test.polycloud.ru/mediawiki/index.php/855'));
// Формирование таблицы для вики-разметки
echo '{| border="1" cellpadding="5" cellspacing="0"<br />| №<br />| Название<br />| год<br />|-<br />' ;
// Цикл для сбора ссылок на разделы сфер заболеваний со своими списками заболеваний
$count = 2051;
foreach($html->find("//div[id@='mw-content-text']/ol/li/a") as $element_link_film)
{
    $count++;
    echo '| ' . $count . '<br />' ;
	$massiv_link_film[$count] = 'http://test.polycloud.ru' . $element_link_film->href ;
	// echo $massiv_link_film[$count] . '<br />';
	
	// Наименование фильма
	$massiv_name_film[$count] = $element_link_film->innertext() ;
	$massiv_name_film[$count] = iconv('UTF-8', 'CP1251', $massiv_name_film[$count]);
	// Удаляем лишние пробелы в заголовке фильма
	//$massiv_name_film[$count] = preg_replace('/\s+/i', ' ', $massiv_name_film[$count]);
	echo '| [[' . $massiv_name_film[$count] . ']] <br />';
	
	// Спаршивание года выхода фильма
	$html_vn = str_get_html(curliandia_get($massiv_link_film[$count]));
	$massiv_film_god[$count] = implode("", $html_vn->find("//div.mw-content-ltr/table/tbody/tr[1]/td[2]"));
	// Удаляем всё кроме года
	$massiv_film_god[$count] = preg_replace('/\s+\(\d{4}\).*$/', '\1', $massiv_film_god[$count]) ;
	echo '| ' . $massiv_film_god[$count] . '<br />';
	echo '|-<br />';
//break;
}

echo '|}';