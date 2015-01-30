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
	  
// Create DOM from URL or file
$html = str_get_html(curliandia_get('http://www.kinopoisk.ru/top/'));
//var_dump($html);

// Сбор ссылок на подробное описание фильмов в массив $massiv_link_film[]
$count = 0;
foreach($html->find("//td[id@='block_left']//table//table//table/tbody/tr/td[2]/a") as $element_link_film)
{
    $count++;
    echo $count . '<br />' ;
	echo '  &lt;page&gt;<br />    &lt;title&gt;' ;

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
	
	$name_film = $element_link_film->innertext() ;
	$name_film = preg_replace('/\s+\(\d{4}\)$/', '', $name_film) ;
	$massiv_name_film[$count] = $name_film ;
	echo $massiv_name_film[$count] . '&lt;/title&gt;' ;
	//echo $count . '&nbsp;' . $element_link_film . '<br />';
	$massiv_link_film[$count] = 'http://www.kinopoisk.ru' . $element_link_film->href ;
	  
	//echo '&nbsp;' . $massiv_link_film[$count] . '<br />' ;
    echo '<br />' ;
    // Составление XML-документа для импорта на Вики-ТестПоликлуд
	echo '    &lt;ns&gt;0&lt;/ns&gt;<br />    &lt;id&gt;&lt;/id&gt;<br />    &lt;revision&gt;<br />      &lt;contributor&gt;<br />        &lt;username&gt;Poly test&lt;/username&gt;<br />        &lt;id&gt;1&lt;/id&gt;<br />      &lt;/contributor&gt;<br />      &lt;text xml:space="preserve"&gt;<br />' ;
	  
	if (($count>0) && ($count<251))
	{
	
		// Блок выкачки подробной информации о фильме
		// $html_podr = file_get_html('http://www.kinopoisk.ru/film/326/'); //$massiv_link_film[$count]
		$html_podr = str_get_html(curliandia_get($massiv_link_film[$count]));
		// $html_podr = str_get_html(curliandia_get('http://www.kinopoisk.ru/film/474/'));
		
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
			echo '<br />' . $opisanie_filma ;
		}

		$film_podr = $html_podr->find("//div.shadow/div[id@='content_block']/div[id@='viewFilmInfoWrapper']//div[id@='infoTable']/table.info ");
		//var_dump($html_podr);
		
		// Блок Переработки полученной информации в виде HTML-кода в XML-формат
		$film_podr_str = implode("", $film_podr);
		
		// Удаляем все ссылки в ХТМЛ-коде , оставляя только их анкоры
		$film_podr_str = preg_replace('/<a(.+?)href="(.+?)"(.*?)>/i', '', $film_podr_str);
		$film_podr_str = preg_replace('/<\/a>/i', '', $film_podr_str);
		// Удаляем все картинки в ХТМЛ-коде
		$film_podr_str = preg_replace('/<img(.+?)src="(.+?)"(.*?)>/i', '', $film_podr_str);
		
             // Удаляем все подряд стоящие табы и пробелы в ХТМЛ-коде
             $film_podr_str = preg_replace('/\s+/', '&nbsp;', $film_podr_str);
			 // Заменяем в ХТМЛ-коде двойной пробел на одинарный
             //$film_podr_str = str_replace('&nbsp;&nbsp;', '&nbsp;', $film_podr_str);
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
			{
				echo '<br /><br />==VFX-сдудии:==<br />';
				$link_na_page_studi = 'http://www.kinopoisk.ru' . $film_studi->href ;
				// Проходим на страницу студий
				$html_page_studi = str_get_html(curliandia_get($link_na_page_studi));
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

						//$string_about_vfx = '$$ ' . $studi_name_str . ' ## ' . $studi_name->href . "\n" ;
						// Пишем содержимое в файл 'studi.txt'
						//file_put_contents('c:/WebServers/home/parser44.ru/www/studi.txt', $string_about_vfx, FILE_APPEND | LOCK_EX);
					}
				}
			}
		}
	}

	echo '&lt;/text&gt;<br />    &lt;/revision&gt;<br />  &lt;/page&gt;<br /><br />' ;
	//sleep (5000) ;
//break;
}