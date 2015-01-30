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
curl_setopt($ch, CURLOPT_REFERER, 'http://www.kinopoisk.ru/');
//выполнение (результат отобразится на экран)
$out = curl_exec($ch);
//Закрытие соединения
curl_close($ch);
return $out;
}

// Команда file() считывает содержимое файла и построчно записывает в массив.
$array_do = file("c:/WebServers/home/parser44.ru/www/studi.txt");
$count = 0 ;
$array_posle[$count] = $array_do[$count] ;
//echo $array_posle[$count] ;
foreach ($array_do as $stroka_do_vfx) {
	$count++ ;
    //echo $count . ' ' . $stroka_do_vfx . '<br />';
	$count2 = 0 ;
	// Формирование списка vfx-студий из уникальных записей
	foreach ($array_posle as $stroka_posle_vfx) {
		if ($stroka_posle_vfx == $stroka_do_vfx) {
			$count2++ ;
		}
	}
	//echo $count2 . '<br />' ;
	if ($count2 == 0) {
		$array_posle[$count] = $stroka_do_vfx ;
	}
}
// Вывод получившегося списка vfx-студий из уникальных записей и формирование XML-импортируемого документа с описанием vfx-студий
$count = 0 ;
foreach ($array_posle as $stroka_posle_vfx) {
	//echo $count . ' ' . $stroka_posle_vfx . '<br />';
	echo $count . '<br />';
	if (($count >207) && ($count < 227))
	{
		// Имя vfx-студии
		$name_vfx_study = str_replace("$$ ", "", $stroka_posle_vfx) ;
		$name_vfx_study = delete44($name_vfx_study, ' ## ') ;
		$name_vfx_study = str_replace(" ## ", "", $name_vfx_study) ;
		echo '  &lt;page&gt;<br />    &lt;title&gt;' . $name_vfx_study . '&lt;/title&gt;' . '<br />' ;
		// Составление XML-документа для импорта на Вики-ТестПоликлуд
		echo '    &lt;ns&gt;0&lt;/ns&gt;<br />    &lt;id&gt;&lt;/id&gt;<br />    &lt;revision&gt;<br />      &lt;contributor&gt;<br />        &lt;username&gt;Poly test&lt;/username&gt;<br />        &lt;id&gt;1&lt;/id&gt;<br />      &lt;/contributor&gt;<br />      &lt;text xml:space="preserve"&gt;<br />' ;
		
		// Ссылка на vfx-студию в Кинопоиске
		$ssylka_vfx_study = str_replace("$$ " . $name_vfx_study . ' ## ' , "", $stroka_posle_vfx) ;
		$ssylka_vfx_study = str_replace(" ", "", $ssylka_vfx_study) ;
		$ssylka_vfx_study = 'http://www.kinopoisk.ru' . $ssylka_vfx_study . 'm_act[all]/ok/' ;
		$ssylka_vfx_study = str_replace("/ ", "/", $ssylka_vfx_study) ;
		//echo $ssylka_vfx_study . '<br />';
		
		/* Синтаксис XML-документа для импорта на Вики-ТестПоликлуд
		  <page>
			<title>Голова44</title>
			<ns>0</ns>
			<id></id>
			<revision>
			  <contributor>
				<username>Poly test</username>
				<id>1</id>
			  </contributor>
			  <text xml:space="preserve">Гальгузавра33</text>
			</revision>
		  </page>
		*/
		
		// Create DOM from URL or file
		$html = str_get_html(curliandia_get($ssylka_vfx_study));
		//var_dump($html);

		echo '==Фильмы, над которыми работала данная FVX-сдудия:==<br />';
		foreach($html->find("//td[id@='block_left']/div//table/tbody/tr[5]/td/div/div/div.info/div.name  ") as $element_link_film) // прежний путь: td[id@='block_left']/div/table/tbody/tr/td/table/tbody/tr[5]/td/div[id@='itemList']/div/div[2]/div/a[1]
		{
			// Удаляем все ссылки в ХТМЛ-коде , оставляя только их анкоры
			$element_link_film = preg_replace('/<a(.+?)href="(.+?)"(.*?)>/i', '# [[', $element_link_film);
			$element_link_film = preg_replace('/<\/a>/i', ']] - ', $element_link_film);
			echo $element_link_film ;
		}
		echo '&lt;/text&gt;<br />    &lt;/revision&gt;<br />  &lt;/page&gt;<br /><br />' ;
	}
	$count++ ;
//break;
}
