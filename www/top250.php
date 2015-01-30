<?php

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
//usleep(7000) ;
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
	  
// Create DOM from URL or file
$html = str_get_html(curliandia_get('http://www.kinopoisk.ru/top/'));
//var_dump($html);

// ���� ������ �� ��������� �������� ������� � ������ $massiv_link_film[]
$count = 0;
foreach($html->find("//td[id@='block_left']//table//table//table/tbody/tr/td[2]/a") as $element_link_film)
{
    $count++;
    echo $count . '<br />' ;
	echo '  &lt;page&gt;<br />    &lt;title&gt;' ;

/* ��������� XML-��������� ��� ������� �� ����-������������
  <page>
    <title>������44</title>
    <ns>0</ns>
    <id></id>
    <revision>
      <contributor>
        <username>Poly test</username>
        <id>1</id>
      </contributor>
      <text xml:space="preserve">�����������33</text>
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
    // ����������� XML-��������� ��� ������� �� ����-������������
	echo '    &lt;ns&gt;0&lt;/ns&gt;<br />    &lt;id&gt;&lt;/id&gt;<br />    &lt;revision&gt;<br />      &lt;contributor&gt;<br />        &lt;username&gt;Poly test&lt;/username&gt;<br />        &lt;id&gt;1&lt;/id&gt;<br />      &lt;/contributor&gt;<br />      &lt;text xml:space="preserve"&gt;<br />' ;
	  
	if (($count>0) && ($count<251))
	{
	
		// ���� ������� ��������� ���������� � ������
		// $html_podr = file_get_html('http://www.kinopoisk.ru/film/326/'); //$massiv_link_film[$count]
		$html_podr = str_get_html(curliandia_get($massiv_link_film[$count]));
		// $html_podr = str_get_html(curliandia_get('http://www.kinopoisk.ru/film/474/'));
		
		$dirr_img = '/uploads/';
		$papka_images = "films";
		$papka_mini = "/mini/";
		// ����� �������� �������
		foreach($html_podr->find("//div[id@='photoBlock']/div.film-img-box/a.popupBigImage/img") as $element_a_poster)
		{
			// ��������� ����� �����
			$src_img = str_replace("http://st.kinopoisk.ru/images/film_iphone/iphone360_", "", $element_a_poster->src) ;
			$poster = file_get_contents("http://st.kinopoisk.ru/images/film_big/".$src_img);	
			// mkdir($_SERVER['DOCUMENT_ROOT'].$dirr_img.$papka_images, 0777, true);
			file_put_contents($_SERVER['DOCUMENT_ROOT'].$dirr_img.$papka_images."/".$src_img, $poster);
			echo '<br />http://test.polycloud.ru' . $dirr_img . $papka_images . $papka_mini . $src_img . '<br />' ;
		}	

		// ����� �������� �������� � �����
		foreach($html_podr->find("//table[id@='syn']/tbody/tr/td/table/tbody/tr/td.news/span._reachbanner_/div.brand_words") as $opisanie_filma)
		{
			echo '<br />' . $opisanie_filma ;
		}

		$film_podr = $html_podr->find("//div.shadow/div[id@='content_block']/div[id@='viewFilmInfoWrapper']//div[id@='infoTable']/table.info ");
		//var_dump($html_podr);
		
		// ���� ����������� ���������� ���������� � ���� HTML-���� � XML-������
		$film_podr_str = implode("", $film_podr);
		
		// ������� ��� ������ � ����-���� , �������� ������ �� ������
		$film_podr_str = preg_replace('/<a(.+?)href="(.+?)"(.*?)>/i', '', $film_podr_str);
		$film_podr_str = preg_replace('/<\/a>/i', '', $film_podr_str);
		// ������� ��� �������� � ����-����
		$film_podr_str = preg_replace('/<img(.+?)src="(.+?)"(.*?)>/i', '', $film_podr_str);
		
             // ������� ��� ������ ������� ���� � ������� � ����-����
             $film_podr_str = preg_replace('/\s+/', '&nbsp;', $film_podr_str);
			 // �������� � ����-���� ������� ������ �� ���������
             //$film_podr_str = str_replace('&nbsp;&nbsp;', '&nbsp;', $film_podr_str);
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

		echo '<br />==����������==<br />' ;
		echo $film_podr_str ;
		
		// ���� ����� � ����� ��������� ���������� � ������ �� �������� ����-������������ ������������ ���� �������
		if (substr_count($film_podr_str,"|}")==0)
		{
			echo '    <br /><br />|}<br />' ;	
		}

		// ����������� �� ���������� �������� � ������ ������ �� �������� ������, ���������� ��� ���:
		//$film_studi = $html_podr->find("//ul[id@='newMenuSub']/li");
		foreach($html_podr->find("//div[id@='content_block']/table/tbody/tr/td/div/ul/li/a") as $film_studi)
		{
			if (substr_count($film_studi->href,"/studio/")>0)
			{
				echo '<br /><br />==VFX-������:==<br />';
				$link_na_page_studi = 'http://www.kinopoisk.ru' . $film_studi->href ;
				// �������� �� �������� ������
				$html_page_studi = str_get_html(curliandia_get($link_na_page_studi));
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

						//$string_about_vfx = '$$ ' . $studi_name_str . ' ## ' . $studi_name->href . "\n" ;
						// ����� ���������� � ���� 'studi.txt'
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