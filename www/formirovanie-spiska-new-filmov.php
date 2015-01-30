<?php

/* Парсер, при помощи которого производится обход по фильмографиям vfx-студий,
из которых вытаскиваются и сохраняются в файл ссылок на новые фильмы на Кинопоике
*/

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

// Команда file() считывает содержимое файла и построчно записывает в массив.
$array_do = file("c:/WebServers/home/parser44.ru/www/studi.txt");
$count = 0 ;
$array_posle[$count] = $array_do[$count] ;
foreach ($array_do as $stroka_do_vfx) {
	$count++ ;
	$count2 = 0 ;
	// Формирование списка vfx-студий из уникальных записей
	foreach ($array_posle as $stroka_posle_vfx) {
		if ($stroka_posle_vfx == $stroka_do_vfx) {
			$count2++ ;
		}
	}
	if ($count2 == 0) {
		$array_posle[$count] = $stroka_do_vfx ;
	}
}

// Вывод получившегося списка из уникальных записей vfx-студий и формирование XML-импортируемого документа с описанием всех снабжённых спецэффектами фильмов
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
	$stroka_posle_vfx = $array_posle[$counter2] ;
	
	if ($stroka_posle_vfx <> '') {
	
		// Ссылка на фильмографию vfx-студии в Кинопоиске
		$ssylka_vfx_study = preg_replace("/\\$\\$.*?##/", "", $stroka_posle_vfx);
		$ssylka_vfx_study = preg_replace("/\s+/", "", $ssylka_vfx_study) ;
		$ssylka_vfx_study = 'http://www.kinopoisk.ru' . $ssylka_vfx_study . 'm_act[all]/ok/' ;
		//echo $counter2 . ' - ' . $ssylka_vfx_study . "<br />";

		$html_film_new = str_get_html(curliandia_get($ssylka_vfx_study));
		foreach($html_film_new->find("//div[id@='itemList']/div.info/div.name/a") as $element_link_film_new) // прежний путь: //td[id@='block_left']/div//table/tbody/tr[5]/td/div/div/div.info/div.name
		{
			$kino_count++ ;
			// Составление XML-документа для импорта в МедиаВики
			echo $kino_count . '<br />' ;
			/*
			// (
			$link_film_new = 'http://www.kinopoisk.ru' . $element_link_film_new->href ;
			$string_about_film_new = '-- ' . $element_link_film_new->innertext() . ' ## ' . $link_film_new . "\n" ;
			// Пишем содержимое в файл 'film_new.txt'
			file_put_contents('c:/WebServers/home/parser44.ru/www/film_new.txt', $string_about_film_new, FILE_APPEND | LOCK_EX);
			//  )
			*/
			
			echo '  &lt;page&gt;<br />    &lt;title&gt;' ;
			echo $element_link_film_new->innertext() . '&lt;/title&gt;' ;
			$link_film_new = 'http://www.kinopoisk.ru' . $element_link_film_new->href ;
			echo $link_film_new . "<br />";
			
			echo '<br />' ;
			echo '    &lt;ns&gt;0&lt;/ns&gt;<br />    &lt;id&gt;&lt;/id&gt;<br />    &lt;revision&gt;<br />      &lt;contributor&gt;<br />        &lt;username&gt;Poly test&lt;/username&gt;<br />        &lt;id&gt;1&lt;/id&gt;<br />      &lt;/contributor&gt;<br />      &lt;text xml:space="preserve"&gt;<br />' ;
			if (($kino_count>0) && ($kino_count<101))
			{
				// Блок выкачки подробной информации о фильме
				$html_podr = str_get_html(curliandia_get($link_film_new));
				//$html_podr = str_get_html(curliandia_get('http://www.kinopoisk.ru/film/474/'));
				
				// Забор картинки постера
				// Синтаксис картинки в вики-разметке [File:example.jpg|left|подпись]]
				//$img_poster = $html_podr->find("//div.shadow/div/div/div/div/div/a.popupBigImage/img"); //div[id@='photoBlock']/div.film-img-box/a.popupBigImage/img
				
				//foreach($html_podr->find("//div[id@='photoBlock']/div.film-img-box/a.popupBigImage/img") as $element_a_poster)
				//{
				//	echo '[' . $element_a_poster->src . '|left]<br />' ;
				//}
				
				// Забор краткого описания о фильм
				foreach($html_podr->find("//table[id@='syn']/tbody/tr/td/table/tbody/tr/td.news/span._reachbanner_/div.brand_words") as $opisanie_filma)
				{
					echo '<br />' . $opisanie_filma ;
				}
				//$opisanie_filma = $html_podr->find("//table[id@='syn']/tbody/tr[1]/td/table/tbody/tr[1]/td/span/div");
				
				// Забор информации из таблицы
				$film_podr = $html_podr->find("//div.shadow/div[id@='content_block']/div[id@='viewFilmInfoWrapper']//div[id@='infoTable']/table.info ");
				
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

				echo '<br />=[[' . $element_link_film_new->innertext() . ']]=<br />' ;
				echo $film_podr_str ;
				
				// Если вдруг в конце подробной информации о фильме не окажется вики-разметочного закрывающего тега таблицы
					if (substr_count($film_podr_str,"|}")==0)
					{
					echo '    <br /><br />|}<br />' ;	
					}

				// Вытаскиваем из подробного описания к фильму ссылку на страницу студий, работавших над ним:
				//$film_studi = $html_podr->find("//ul[id@='newMenuSub']/li");
				$count_link_vfx = 1;
				foreach($html_podr->find("//div[id@='content_block']/table/tbody/tr/td/div/ul/li/a") as $film_studi)
				{
				  $count_link_vfx++;
					if ($count_link_vfx == 8)
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
			}

			echo '&lt;/text&gt;<br />    &lt;/revision&gt;<br />  &lt;/page&gt;<br /><br />' ;
			
			//break;	
		}

	}
	//else echo $counter2 . ' - ' . $array_posle[$counter2] . "<br />";
	break;
}