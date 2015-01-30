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

/* Сортировка исходного списка ссылок на подробные описания "новых фильмов" в Кинопоиске,
Устранение повторяющихся записей - формирование нового списка уникальных ссылок; */
/*
// Команда file() считывает содержимое файла и построчно записывает в массив.
$array_do = file("c:/WebServers/home/parser44.ru/www/film_new.txt");
$count = 0 ;
$array_posle[$count] = $array_do[$count] ;
foreach ($array_do as $stroka_do_new_film) {
	$count++ ;
	$count2 = 0 ;
	// Формирование списка ссылок на "новые фильмы" из уникальных записей
	foreach ($array_posle as $stroka_posle_new_film) {
		if ($stroka_posle_new_film == $stroka_do_new_film) {
			$count2++ ;
		}
	}
	if ($count2 == 0) {
		$array_posle[$count] = $stroka_do_new_film ;
	}
}

// Вывод получившегося списка из уникальных ссылок на "новые фильмы" и запись в файл 'film_new_unik.txt'
$kino_count = 0;
for ($counter2 = 0; $counter2 < $count + 1; $counter2++)
{
	// Цикл, очищающий массив от пустых строк
	for ($counter = 0; $counter < $count + 1; $counter++)
	{
		if ($array_posle[$counter] == '') {
			$array_posle[$counter] = $array_posle[$counter + 1] ;
			$array_posle[$counter + 1] = '';
		}
	} 
	$stroka_posle_new_film = $array_posle[$counter2] ;
	echo $counter2 . ' ' . $stroka_posle_new_film . '<br />' ;
	
	if ($stroka_posle_new_film <> '') {
	
			// $string_about_film_new = '-- ' . $element_link_film_new->innertext() . ' ## ' . $link_film_new . "\n" ;
			// Пишем содержимое в файл 'film_new_unik.txt'
			file_put_contents('c:/WebServers/home/parser44.ru/www/film_new_unik.txt', $stroka_posle_new_film, FILE_APPEND | LOCK_EX);
	
	}

	//else echo $counter2 . ' - ' . $array_posle[$counter2] . "<br />";
	//break;
}
*/

/* Формирование XML-импортируемого документа с описанием всех снабжённых спецэффектами "новых фильмов"

	// Синтаксис XML-документа для импорта на Вики-ТестПоликлуд
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

// Команда file() считывает содержимое файла и построчно записывает в массив.
$array_new_film = file("c:/WebServers/home/parser44.ru/www/film_new_unik.txt");
$kino_count = 0 ;
foreach ($array_new_film as $stroka_new_film)
{
	if ($stroka_new_film <> '')
	{
		// Ссылка на подробное описание фильма в Кинопоиске
		$kino_count++ ;
		
		$ssylka_new_film = preg_replace("/.*?## /", "", $stroka_new_film);
		$ssylka_new_film = str_replace("/level/1", "", $ssylka_new_film) ;
		$ssylka_new_film = preg_replace("/\s+/", "", $ssylka_new_film) ;
		//echo $ssylka_new_film . "<br />";
		$name_new_film = preg_replace("/-- /", "", $stroka_new_film) ;
		$name_new_film = preg_replace("/ ##.*/", "", $name_new_film) ;
		//$name_new_film = str_replace("&", "&#038;", $name_new_film) ;
		//$name_new_film = preg_replace("/\s+/", "", $name_new_film) ;
		//echo $name_new_film  . "<br />";

		// Составление XML-документа для импорта в МедиаВики
		if (($kino_count>2905) && ($kino_count<2907))
		{
			echo $kino_count . '<br />' ;
			echo '  &lt;page&gt;<br />    &lt;title&gt;' ;
			echo $name_new_film . '&lt;/title&gt;<br />' ;
			echo '    &lt;ns&gt;0&lt;/ns&gt;<br />    &lt;id&gt;&lt;/id&gt;<br />    &lt;revision&gt;<br />      &lt;contributor&gt;<br />        &lt;username&gt;Poly test&lt;/username&gt;<br />        &lt;id&gt;1&lt;/id&gt;<br />      &lt;/contributor&gt;<br />      &lt;text xml:space="preserve"&gt;<br />' ;
			// Блок выкачки подробной информации о фильме
			$html_podr = str_get_html(curliandia_get($ssylka_new_film));
			//$html_podr = str_get_html(curliandia_get('http://www.kinopoisk.ru/film/7111/'));
			// var_dump($html_podr);
			
			$dirr_img = '/uploads/';
			$papka_images = "films";
			$papka_mini = "/mini/";
			// Забор картинки постера
			foreach($html_podr->find("//div[id@='photoBlock']/div.film-img-box/a.popupBigImage/img") as $element_a_poster)
			{
				// Выделение имени файла
				$src_img = str_replace("http://st.kinopoisk.ru/images/film_iphone/iphone360_", "", $element_a_poster->src) ;
				$poster = file_get_contents("http://st.kinopoisk.ru/images/film_big/".$src_img);	
				// mkdir($_SERVER['DOCUMENT_ROOT'].$dirr_img.$papka_images, 0777, true);
				file_put_contents($_SERVER['DOCUMENT_ROOT'].$dirr_img.$papka_images."/".$src_img, $poster);
				echo '<br />http://test.polycloud.ru' . $dirr_img . $papka_images . $papka_mini . $src_img . '<br />' ;
			}
			
			// Забор краткого описания о фильм
			foreach($html_podr->find("//table[id@='syn']/tbody/tr/td/table/tbody/tr/td.news/span._reachbanner_/div.brand_words") as $opisanie_filma)
			{
				// Заменяем все встречающиеся '&' на ASCII-аналог
				//$opisanie_filma = str_replace("&", "&#038;", $opisanie_filma) ;
				//$opisanie_filma = str_replace("&nbsp;", " ", $opisanie_filma) ;
				echo '<br />' . $opisanie_filma ;
			}
			//$opisanie_filma = $html_podr->find("//table[id@='syn']/tbody/tr/td/table/tbody/tr/td.news/span._reachbanner_/div.brand_words");
			
			// Забор информации из таблицы
			$film_podr = $html_podr->find("//div.shadow/div[id@='content_block']/div[id@='viewFilmInfoWrapper']//div[id@='infoTable']/table.info");
			
		  // Блок Переработки полученной информации в виде HTML-кода в XML-формат
			$film_podr_str = implode("", $film_podr);
			
			// Удаляем все ссылки в ХТМЛ-коде , оставляя только их анкоры
			$film_podr_str = preg_replace('/<a(.+?)href="(.+?)"(.*?)>/i', '', $film_podr_str);
			$film_podr_str = preg_replace('/<\/a>/i', '', $film_podr_str);
			// Удаляем все картинки в ХТМЛ-коде
			$film_podr_str = preg_replace('/<img(.+?)src="(.+?)"(.*?)>/i', '', $film_podr_str);
			
			// Удаляем все подряд стоящие табы и пробелы в ХТМЛ-коде
			$film_podr_str = preg_replace('/\s+/', '&nbsp;', $film_podr_str);
			// Заменяем в ХТМЛ-коде открывающие тэги <td> на вики-разметочное начало табличного столбца '|'
			$film_podr_str = str_replace("<td>", "| ", $film_podr_str) ; 
			// Заменяем в ХТМЛ-коде открывающие тэги <table ...> на вики-разметочное начало таблицы '{|'
			$film_podr_str = preg_replace('/<table(.+?)(.*?)>/i', '{| border="1" cellpadding="5" cellspacing="0"<br />', $film_podr_str);
			// Заменяем в ХТМЛ-коде закрывающиеся тэги </table> на вики-разметочный конец таблицы '|}'
			$film_podr_str = preg_replace('/<\/table>/i', '|}<br />', $film_podr_str);
			// Заменяем в ХТМЛ-коде открывающие тэги <tr ...> на вики-разметочное начало табличной строки '|-'
			// $film_podr_str = preg_replace('/<tr(.+?)(.*?)>/i', '|-<br />', $film_podr_str);
			// $film_podr_str = preg_replace('/<tr(.+?)(.*?)>/i', '', $film_podr_str);
			// Удаляем в ХТМЛ-коде закрывающиеся тэги </tr>
			$film_podr_str = preg_replace('/<\/tr>/i', '|-<br />', $film_podr_str);
			// Заменяем в ХТМЛ-коде открывающие тэги <td class="type"> на вики-разметочное начало табличного столбца '|'
			//$film_podr_str = preg_replace('/<td(.+?)class="(.+?)"(.*?)>/i', '| ', $film_podr_str);
			// Заменяем в ХТМЛ-коде открывающие тэги <td ...> на вики-разметочное начало табличного столбца '|'
			$film_podr_str = preg_replace('/<td(.+?)(.*?)>/i', '| ', $film_podr_str);
			// Удаляем в ХТМЛ-коде закрывающиеся тэги </td>
			$film_podr_str = preg_replace('/<\/td>/i', '<br />', $film_podr_str);
			// Удаляем в ХТМЛ-коде все открывающиеся тэги <div>
			$film_podr_str = preg_replace('/<div(.+?)(.*?)>/i', '', $film_podr_str);
			// Удаляем в ХТМЛ-коде все закрывающиеся тэги </div>
			$film_podr_str = preg_replace('/<\/div>/i', '', $film_podr_str);
			// Заменяем все встречающиеся '&' на ASCII-аналог
			//$film_podr_str = str_replace("&", "&#038;", $film_podr_str) ;
			//$film_podr_str = str_replace("&nbsp;", " ", $film_podr_str) ;
			echo '<br />==Информация==<br />' ;
			
			echo $film_podr_str ;
			
			// Если вдруг в конце подробной информации о фильме не окажется вики-разметочного закрывающего тега таблицы
				if (substr_count($film_podr_str,"|}")==0)
				{
				echo '    <br /><br />|}<br />' ;	
				}

			// Вытаскиваем из подробного описания к фильму ссылку на страницу студий, работавших над ним:
			//$film_studi = $html_podr->find("//ul[id@='newMenuSub']/li");
			foreach($html_podr->find("//div[id@='content_block']/table/tbody/tr/td/div/ul/li/a") as $film_studi)
			{
				if (substr_count($film_studi->href,"/studio/")>0)
				//if ($count_link_vfx == 8)
				{
					//echo $film_studi->href . '<br />';
					echo '<br /><br />==VFX-сдудии:==<br />';
					$link_na_page_studi = 'http://www.kinopoisk.ru' . $film_studi->href ;
					// Проходим на страницу студий
					$html_page_studi = str_get_html(curliandia_get($link_na_page_studi));
					//var_dump($html_page_studi);
					// Выводим в подробном описании к фильму в вики-формате ссылки на vfx-студии , запоминаем их имена и ссылки на страницы с подробным описанием студий
					foreach($html_page_studi->find("//td[id@='block_left']/div/table/tbody/tr[2]/td/table[1]/tbody/tr[2]/td/div/table[2]/tbody/tr/td[2]/a.all") as $studi_name)
					{
						$studi_str_href = $studi_name->href ;
						// Проверяем, если точно взята информация об vfx-студии, тогда выполняем процесс записи информации
						if (substr_count($studi_str_href,"studio%")>0)
						{
							$studi_name_str = $studi_name->innertext() ;
								// Заменяем в строке названия vfx-студии все встречающиеся '&' на ASCII-аналог
								$studi_name_str = str_replace("&", "&#038;", $studi_name_str) ;
								// Заменяем в строке названия vfx-студии все встречающиеся '&nbsp;' на ' '
								$studi_name_str = str_replace("&nbsp;", " ", $studi_name_str) ;
							echo '# [[' . $studi_name_str . ']] <br />';
						}
					}
				}
			}
			echo '&lt;/text&gt;<br />    &lt;/revision&gt;<br />  &lt;/page&gt;<br /><br />' ;
		}
	}
//break;
}