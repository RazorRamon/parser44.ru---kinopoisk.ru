<?php

/* ������, ��� ������ �������� ������������ ����� �� ������������� vfx-������,
�� ������� ������������� � ����������� � ���� ������ �� ����� ������ �� ���������
*/

set_time_limit(0);
include 'simple_html_dom.php';

// �������, ��������� �� ������ �� ����� ������� � ����������� ��������
function delete44($str,$symbol='') 
{ 
    return($strpos=mb_strpos($str,$symbol))!==false?mb_substr($str,0,$strpos,'utf8'):$str;
}

// �������, ������ ���-������ ��������� ��������
function curliandia_get($url)
{
//������������� cURL � ������� ������
$ch = curl_init($url);
//��������� �����
usleep(rand (1000, 3000)) ;
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; rv:21.0) Gecko/20100101 Firefox/21.0');
curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION,true);
curl_setopt($ch, CURLOPT_COOKIEFILE, 'c:/WebServers/home/parser44.ru/www/cookies.txt'); //����������� ���� ��� 
curl_setopt($ch, CURLOPT_COOKIEJAR, 'c:/WebServers/home/parser44.ru/www/cookies.txt'); //����������� ���� ��� 
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_REFERER, 'http://www.kinopoisk.ru/');
//���������� (��������� ����������� �� �����)
$out = curl_exec($ch);
//�������� ����������
curl_close($ch);
return $out;
}

// ������� file() ��������� ���������� ����� � ��������� ���������� � ������.
$array_do = file("c:/WebServers/home/parser44.ru/www/studi.txt");
$count = 0 ;
$array_posle[$count] = $array_do[$count] ;
foreach ($array_do as $stroka_do_vfx) {
	$count++ ;
	$count2 = 0 ;
	// ������������ ������ vfx-������ �� ���������� �������
	foreach ($array_posle as $stroka_posle_vfx) {
		if ($stroka_posle_vfx == $stroka_do_vfx) {
			$count2++ ;
		}
	}
	if ($count2 == 0) {
		$array_posle[$count] = $stroka_do_vfx ;
	}
}

// ����� ������������� ������ �� ���������� ������� vfx-������ � ������������ XML-�������������� ��������� � ��������� ���� ��������� ������������� �������
$kino_count = 0;
for ($counter2 = 0; $counter2 < $count + 1; $counter2++)
{
	// ����, ��������� ������ �� ������ �����
	for ($counter = 0; $counter < $count + 1; $counter++)
	{
		if ($array_posle[$counter] == '') {
			$array_posle[$counter] = $array_posle[$counter + 1] ;
			$array_posle[$counter + 1] = '';
		}
	} 
	$stroka_posle_vfx = $array_posle[$counter2] ;
	
	if ($stroka_posle_vfx <> '') {
	
		// ������ �� ������������ vfx-������ � ����������
		$ssylka_vfx_study = preg_replace("/\\$\\$.*?##/", "", $stroka_posle_vfx);
		$ssylka_vfx_study = preg_replace("/\s+/", "", $ssylka_vfx_study) ;
		$ssylka_vfx_study = 'http://www.kinopoisk.ru' . $ssylka_vfx_study . 'm_act[all]/ok/' ;
		//echo $counter2 . ' - ' . $ssylka_vfx_study . "<br />";

		$html_film_new = str_get_html(curliandia_get($ssylka_vfx_study));
		foreach($html_film_new->find("//div[id@='itemList']/div.info/div.name/a") as $element_link_film_new) // ������� ����: //td[id@='block_left']/div//table/tbody/tr[5]/td/div/div/div.info/div.name
		{
			$kino_count++ ;
			// ����������� XML-��������� ��� ������� � ���������
			echo $kino_count . '<br />' ;
			/*
			// (
			$link_film_new = 'http://www.kinopoisk.ru' . $element_link_film_new->href ;
			$string_about_film_new = '-- ' . $element_link_film_new->innertext() . ' ## ' . $link_film_new . "\n" ;
			// ����� ���������� � ���� 'film_new.txt'
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
				// ���� ������� ��������� ���������� � ������
				$html_podr = str_get_html(curliandia_get($link_film_new));
				//$html_podr = str_get_html(curliandia_get('http://www.kinopoisk.ru/film/474/'));
				
				// ����� �������� �������
				// ��������� �������� � ����-�������� [File:example.jpg|left|�������]]
				//$img_poster = $html_podr->find("//div.shadow/div/div/div/div/div/a.popupBigImage/img"); //div[id@='photoBlock']/div.film-img-box/a.popupBigImage/img
				
				//foreach($html_podr->find("//div[id@='photoBlock']/div.film-img-box/a.popupBigImage/img") as $element_a_poster)
				//{
				//	echo '[' . $element_a_poster->src . '|left]<br />' ;
				//}
				
				// ����� �������� �������� � �����
				foreach($html_podr->find("//table[id@='syn']/tbody/tr/td/table/tbody/tr/td.news/span._reachbanner_/div.brand_words") as $opisanie_filma)
				{
					echo '<br />' . $opisanie_filma ;
				}
				//$opisanie_filma = $html_podr->find("//table[id@='syn']/tbody/tr[1]/td/table/tbody/tr[1]/td/span/div");
				
				// ����� ���������� �� �������
				$film_podr = $html_podr->find("//div.shadow/div[id@='content_block']/div[id@='viewFilmInfoWrapper']//div[id@='infoTable']/table.info ");
				
			  // ���� ����������� ���������� ���������� � ���� HTML-���� � XML-������
				$film_podr_str = implode("", $film_podr);
				
				// ������� ��� ������ � ����-���� , �������� ������ �� ������
				$film_podr_str = preg_replace('/<a(.+?)href="(.+?)"(.*?)>/i', '', $film_podr_str);
				$film_podr_str = preg_replace('/<\/a>/i', '', $film_podr_str);
				// ������� ��� �������� � ����-����
				$film_podr_str = preg_replace('/<img(.+?)src="(.+?)"(.*?)>/i', '', $film_podr_str);
				
				// ������� ��� ������ ������� ���� � ������� � ����-����
				$film_podr_str = preg_replace('/\s+/', '&nbsp;', $film_podr_str);
				// �������� � ����-���� ����������� ���� <td> �� ����-����������� ������ ���������� ������� '|'
				$film_podr_str = str_replace("<td>", "| ", $film_podr_str) ; 
				// �������� � ����-���� ����������� ���� <table ...> �� ����-����������� ������ ������� '{|'
				$film_podr_str = preg_replace('/<table(.+?)(.*?)>/i', '{| border="1" cellpadding="5" cellspacing="0"<br />', $film_podr_str);
				// �������� � ����-���� ������������� ���� </table> �� ����-����������� ����� ������� '|}'
				$film_podr_str = preg_replace('/<\/table>/i', '|}<br />', $film_podr_str);
				// �������� � ����-���� ����������� ���� <tr ...> �� ����-����������� ������ ��������� ������ '|-'
				// $film_podr_str = preg_replace('/<tr(.+?)(.*?)>/i', '|-<br />', $film_podr_str);
				// $film_podr_str = preg_replace('/<tr(.+?)(.*?)>/i', '', $film_podr_str);
				// ������� � ����-���� ������������� ���� </tr>
				$film_podr_str = preg_replace('/<\/tr>/i', '|-<br />', $film_podr_str);
				// �������� � ����-���� ����������� ���� <td class="type"> �� ����-����������� ������ ���������� ������� '|'
				//$film_podr_str = preg_replace('/<td(.+?)class="(.+?)"(.*?)>/i', '| ', $film_podr_str);
				// �������� � ����-���� ����������� ���� <td ...> �� ����-����������� ������ ���������� ������� '|'
				$film_podr_str = preg_replace('/<td(.+?)(.*?)>/i', '| ', $film_podr_str);
				// ������� � ����-���� ������������� ���� </td>
				$film_podr_str = preg_replace('/<\/td>/i', '<br />', $film_podr_str);
				// ������� � ����-���� ��� ������������� ���� <div>
				$film_podr_str = preg_replace('/<div(.+?)(.*?)>/i', '', $film_podr_str);
				// ������� � ����-���� ��� ������������� ���� </div>
				$film_podr_str = preg_replace('/<\/div>/i', '', $film_podr_str);

				echo '<br />=[[' . $element_link_film_new->innertext() . ']]=<br />' ;
				echo $film_podr_str ;
				
				// ���� ����� � ����� ��������� ���������� � ������ �� �������� ����-������������ ������������ ���� �������
					if (substr_count($film_podr_str,"|}")==0)
					{
					echo '    <br /><br />|}<br />' ;	
					}

				// ����������� �� ���������� �������� � ������ ������ �� �������� ������, ���������� ��� ���:
				//$film_studi = $html_podr->find("//ul[id@='newMenuSub']/li");
				$count_link_vfx = 1;
				foreach($html_podr->find("//div[id@='content_block']/table/tbody/tr/td/div/ul/li/a") as $film_studi)
				{
				  $count_link_vfx++;
					if ($count_link_vfx == 8)
					{
					  //echo $film_studi->href . '<br />';
					echo '<br /><br />==VFX-������:==<br />';
					  $link_na_page_studi = 'http://www.kinopoisk.ru' . $film_studi->href ;
					  // �������� �� �������� ������
					  $html_page_studi = str_get_html(curliandia_get($link_na_page_studi));
					  //var_dump($html_page_studi);
					  // ������� � ��������� �������� � ������ � ����-������� ������ �� vfx-������ , ���������� �� ����� � ������ �� �������� � ��������� ��������� ������
					  foreach($html_page_studi->find("//td[id@='block_left']/div/table/tbody/tr[2]/td/table[1]/tbody/tr[2]/td/div/table[2]/tbody/tr/td[2]/a.all") as $studi_name)
						{
							$studi_str_href = $studi_name->href ;
							// ���������, ���� ����� ����� ���������� �� vfx-������, ����� ��������� ������� ������ ����������
							if (substr_count($studi_str_href,"studio%")>0)
							{
								$studi_name_str = $studi_name->innertext() ;
									// �������� � ������ �������� vfx-������ ��� ������������� '&' �� ASCII-������
									$studi_name_str = str_replace("&", "&#038;", $studi_name_str) ;
									// �������� � ������ �������� vfx-������ ��� ������������� '&nbsp;' �� ' '
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