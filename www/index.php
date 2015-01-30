<?php

set_time_limit(0);
include 'simple_html_dom.php';

// Функция, удаляющая из строки всё после встречи с определённым символом
function delete44($str,$symbol='') 
{ 
    return($strpos=mb_strpos($str,$symbol))!==false?mb_substr($str,0,$strpos,'utf8'):$str;
}

/* Соединяемся с базой данных */
$hostname = "localhost"; // rscx.ru - название/путь сервера, с MySQL
$username = "parser44"; // poly_test - имя пользователя
$password = "parser44"; // y4mHO0Jf - пароль пользователя
$dbName = "parser44"; // poly_test - название базы данных
 
/* Таблица MySQL, в которой будут храниться данные */
$table = "prefix_topic_content"; // prefix_topic_content
$table_image = "prefix_topic_image"; // prefix_topic_image
 
/* Создаем соединение */
mysql_connect($hostname, $username, $password) or die ("Не могу создать соединение");
 
/* Выбираем базу данных. Если произойдет ошибка - вывести ее */
mysql_select_db($dbName) or die (mysql_error());
 
// Create DOM from URL or file
$html = file_get_html('http://web.archive.org/web/20060826102742/http://www.cgtalk.ru/index.php?id=menu');

// Сбор анонсов статей в массив $massiv_anons[]

$count = 0;
foreach($html->find('//table.page/tbody/tr/td/table.tborder/tbody/tr/td.alt2/') as $element_anons)
{
      $count++;
	  //echo $count ;
	  $massiv_anons[$count] = $element_anons ;
	  $massiv_anons[$count] = str_replace('<td width="400" height="100" align="center" valign="middle" class="alt2">', '', $massiv_anons[$count]);
	  $massiv_anons[$count] = str_replace('</td>', '', $massiv_anons[$count]);
	  // Перевод кодировки информации элемента массива $massiv_anons[$count] из CP1251 в UTF-8
	  $massiv_anons[$count] = iconv('UTF-8', 'CP1251', $massiv_anons[$count]) ;
	  //echo $massiv_anons[$count] . '<br /><br />';
}


// Сбор мини-картинок анонсов статей - копирование их в папку /mini-img/ , сохранение в БД компиляции (в смысле совмещенной вёрстки) анонса с миникартинкой
$count = 0;
// $count_topic_id = 100;
foreach($html->find('//table.page/tbody/tr/td/table.tborder/tbody/tr/td.alt1/a') as $element)
{
// обнуление идентификаторов расширения файлов превью-иллюстраций
	$if_jpg = 0;
	$if_gif = 0;
	//echo $element->href . '<br />';
	$element_img = $element->children(0)->src;
	$link_article = "http://web.archive.org".$element->href ;
	// Выявление какая папка article или exp в адресе ссылки на статью присутствует
    $article_or_exp_mini = delete44(str_replace("/web/20060826102742im_/http://www.cgtalk.ru/", "", $element_img) ,'/');
	//   echo  $article_or_exp_mini , '<br />';
	   
	   // Создание сохраняемого имени мини-картинки
		$src_img = str_replace("/web/20060826102742im_/http://www.cgtalk.ru/".$article_or_exp_mini."/", "", $element_img);
		$src_img = str_replace("/", "-", $src_img);
	//	echo $src_img , "<br />" ;
		
	// Имя файла изображения без расширения
	/*
			if (substr_count($src_img,".jpg")>0)
			{
				$src_img_br = str_replace(".jpg", "", $src_img);
				$if_jpg = 1;
			}
			if (substr_count($src_img,".gif")>0)
			{
				$src_img_br = str_replace(".gif", "", $src_img);
				$if_gif = 1;
			}
    */
	// Формирование нового анонса мини-статьи, с учётом взятом мини-картинки и анонса статьи
    $count++;
  //echo $count ;
 // $count_topic_id++;
	
		$filepath = '/mini-img/';	  
		$topic_text_short[$count] = '<p><img style="float:left; margin-bottom:10px; margin-right:10px; margin-left:5px;" src="'.$filepath.$src_img.'"/>'.$massiv_anons[$count].'</p><div style="clear: both"></div>' ;
	//	echo $topic_text_short[$count] , "<br />" ;

		// Сохранение мини-картинок анонсов статей в папку /mini-img/
//		$s = file_get_contents("http://web.archive.org".$element_img);	
//	    file_put_contents($_SERVER['DOCUMENT_ROOT'].$filepath.$src_img, $s);
/*
			if ($if_jpg == 1)
			{
				file_put_contents($_SERVER['DOCUMENT_ROOT'].$filepath.$src_img_br."_original.jpg", $s);
				file_put_contents($_SERVER['DOCUMENT_ROOT'].$filepath.$src_img_br."_preview.jpg", $s);
			}
			if ($if_gif == 1)
			{
				file_put_contents($_SERVER['DOCUMENT_ROOT'].$filepath.$src_img_br."_original.gif", $s);
				file_put_contents($_SERVER['DOCUMENT_ROOT'].$filepath.$src_img_br."_preview.gif", $s);
			}
*/

//    $src_img_filepath = $filepath.$src_img ;  
//	  $query = "INSERT INTO $table_image (topic_id, path) VALUES ('".$count_topic_id."', '".$src_img_filepath."')";
	  
//	  $query = "UPDATE $table SET topic_text_short = '".$topic_text_short[$count]."' WHERE topic_id='".$count."'";
//	  $query = "INSERT INTO $table (id, img_src) VALUES ('".$count."', '".$src_img."')";
//	  $query = "UPDATE $table SET img_src = '".$src_img."' WHERE id='".$count."'";

//	   mysql_query($query) or die(mysql_error());

}

/*
// Блок забора конкретной страницы статьи:
// - вёрстки и картинок (сохранение по специально создаваемым папкам);
// - дальнейшее редактирование путей картинок на странице
// - перекодировка, перезапись страницы - сохранение в БД в столбец "topic_text"
$count = 0;
foreach($html->find('//table.page/tbody/tr/td/table.tborder/tbody/tr/td.alt1/a') as $element)
{
	echo $element->href . '<br />';
	$element_img = $element->children(0)->src;
	$link_article = "http://web.archive.org".$element->href ;

    $count++;
	$html2 = file_get_html($link_article);
//$html2 = file_get_html('http://web.archive.org/web/20120404155049/http://www.cgtalk.ru/exc.php?id=vlad');

$_res = $html2->find('//div.page/div/table.page/tbody/tr/td/');
$comma_separated[$count] = implode("", $_res);
$comma_separated[$count] = iconv('UTF-8', 'CP1251', $comma_separated[$count]);

	foreach($html2->find('//div.page/div/table.page/tbody/tr/td/img') as $element_article)
	{
		   echo $element_article->src . '<br />';
		   $element_article_src1 = $element_article->src ;
		   
		   $src_img_nomer_article = delete44(str_replace("/web/", "", $element_article_src1) ,'im_');
		   echo $src_img_nomer_article , '<br />';
		   
		   $element_article_src2 = $element_article->src ;
		// Условие исходя из наличия www. http://www.cgtalk.ru/ в адресе src картинки внутри статьи
		if (substr_count($element_article_src2,"http://www.cgtalk.ru/")>0)
		{  
		   // Выявление какая папка article или exp в адресе src картинки статьи присутствует
		   $article_or_exp = delete44(str_replace("/web/".$src_img_nomer_article."im_/http://www.cgtalk.ru/", "", $element_article_src2) ,'/');
		   echo  $article_or_exp , '<br />';
		   
			$src_img_article = str_replace("/web/".$src_img_nomer_article."im_/http://www.cgtalk.ru/".$article_or_exp."/", "", $element_article->src);
			$src_img_article = str_replace("images/", "", $src_img_article);
			$src_img_article = str_replace("preview/", "", $src_img_article);
			$src_img_article = str_replace("pics/sm/", "", $src_img_article);
			$src_img_article = str_replace("sm/", "", $src_img_article);
			$src_img_article = str_replace("pic/", "", $src_img_article);
			$src_img_article = str_replace("img/", "", $src_img_article);
		   echo $src_img_article . '<br />';

		   $papka_article = delete44($src_img_article ,'/');
//		   echo $papka_article . "<br />";
		   $img_article_name = str_replace("$papka_article/", "", $src_img_article);
//		   echo $img_article_name . "<br />";
		   
//		   $s_article = file_get_contents("http://web.archive.org".$element_article->src);	
		   $imagess = '/imagess/';
//		   mkdir($_SERVER['DOCUMENT_ROOT'].$imagess.$papka_article, 0777, true);
//		   file_put_contents($_SERVER['DOCUMENT_ROOT'].$imagess.$papka_article."/".$img_article_name, $s_article);

		   $count_reurl_img_articles = substr_count($comma_separated[$count], 'img');
			for ($counter = 0; $counter < $count_reurl_img_articles; $counter++)
			{
	$comma_separated[$count] = str_replace("/web/".$src_img_nomer_article."im_/http://www.cgtalk.ru/".$article_or_exp."/".$papka_article."/images/".$img_article_name , $imagess.$papka_article."/".$img_article_name, $comma_separated[$count]);
	$comma_separated[$count] = str_replace("/web/".$src_img_nomer_article."im_/http://www.cgtalk.ru/".$article_or_exp."/".$papka_article."/preview/".$img_article_name , $imagess.$papka_article."/".$img_article_name, $comma_separated[$count]);
	$comma_separated[$count] = str_replace("/web/".$src_img_nomer_article."im_/http://www.cgtalk.ru/".$article_or_exp."/".$papka_article."/sm/".$img_article_name , $imagess.$papka_article."/".$img_article_name, $comma_separated[$count]);
	$comma_separated[$count] = str_replace("/web/".$src_img_nomer_article."im_/http://www.cgtalk.ru/".$article_or_exp."/".$papka_article."/pics/sm/".$img_article_name , $imagess.$papka_article."/".$img_article_name, $comma_separated[$count]);
	$comma_separated[$count] = str_replace("/web/".$src_img_nomer_article."im_/http://www.cgtalk.ru/".$article_or_exp."/".$papka_article."/pic/".$img_article_name , $imagess.$papka_article."/".$img_article_name, $comma_separated[$count]);
	$comma_separated[$count] = str_replace("/web/".$src_img_nomer_article."im_/http://www.cgtalk.ru/".$article_or_exp."/".$papka_article."/img/".$img_article_name , $imagess.$papka_article."/".$img_article_name, $comma_separated[$count]);
	$comma_separated[$count] = str_replace("/web/".$src_img_nomer_article."im_/http://www.cgtalk.ru/".$article_or_exp."/".$papka_article."/".$img_article_name , $imagess.$papka_article."/".$img_article_name, $comma_separated[$count]);
			}
		}
		// Если в адресе src картинки внутри статьи нет www. в домене http://cgtalk.ru/
		elseif (substr_count($element_article_src2,"http://cgtalk.ru/")>0)
		{
		   // Выявление какая папка article или exp в адресе src картинки статьи присутствует
		   $article_or_exp = delete44(str_replace("/web/".$src_img_nomer_article."im_/http://cgtalk.ru/", "", $element_article_src2) ,'/');
		   echo  $article_or_exp , '<br />';
		   
			$src_img_article = str_replace("/web/".$src_img_nomer_article."im_/http://cgtalk.ru/".$article_or_exp."/", "", $element_article->src);
			$src_img_article = str_replace("images/", "", $src_img_article);
			$src_img_article = str_replace("preview/", "", $src_img_article);
			$src_img_article = str_replace("pics/sm/", "", $src_img_article);
			$src_img_article = str_replace("sm/", "", $src_img_article);
			$src_img_article = str_replace("pic/", "", $src_img_article);
			$src_img_article = str_replace("img/", "", $src_img_article);
		   echo $src_img_article . '<br />';

		   $papka_article = delete44($src_img_article ,'/');
		 echo $papka_article . "<br />";
		   $img_article_name = str_replace("$papka_article/", "", $src_img_article);
		   echo $img_article_name . "<br />";
		   
//		   $s_article = file_get_contents("http://web.archive.org".$element_article->src);	
		   $imagess = '/imagess/';
//		   mkdir($_SERVER['DOCUMENT_ROOT'].$imagess.$papka_article, 0777, true);
//		   file_put_contents($_SERVER['DOCUMENT_ROOT'].$imagess.$papka_article."/".$img_article_name, $s_article);

		   $count_reurl_img_articles = substr_count($comma_separated[$count], 'img');
			for ($counter = 0; $counter < $count_reurl_img_articles; $counter++)
			{
	$comma_separated[$count] = str_replace("/web/".$src_img_nomer_article."im_/http://cgtalk.ru/".$article_or_exp."/".$papka_article."/images/".$img_article_name , $imagess.$papka_article."/".$img_article_name, $comma_separated[$count]);
	$comma_separated[$count] = str_replace("/web/".$src_img_nomer_article."im_/http://cgtalk.ru/".$article_or_exp."/".$papka_article."/preview/".$img_article_name , $imagess.$papka_article."/".$img_article_name, $comma_separated[$count]);
	$comma_separated[$count] = str_replace("/web/".$src_img_nomer_article."im_/http://cgtalk.ru/".$article_or_exp."/".$papka_article."/sm/".$img_article_name , $imagess.$papka_article."/".$img_article_name, $comma_separated[$count]);
	$comma_separated[$count] = str_replace("/web/".$src_img_nomer_article."im_/http://cgtalk.ru/".$article_or_exp."/".$papka_article."/pics/sm/".$img_article_name , $imagess.$papka_article."/".$img_article_name, $comma_separated[$count]);
	$comma_separated[$count] = str_replace("/web/".$src_img_nomer_article."im_/http://cgtalk.ru/".$article_or_exp."/".$papka_article."/pic/".$img_article_name , $imagess.$papka_article."/".$img_article_name, $comma_separated[$count]);
	$comma_separated[$count] = str_replace("/web/".$src_img_nomer_article."im_/http://cgtalk.ru/".$article_or_exp."/".$papka_article."/img/".$img_article_name , $imagess.$papka_article."/".$img_article_name, $comma_separated[$count]);
	$comma_separated[$count] = str_replace("/web/".$src_img_nomer_article."im_/http://cgtalk.ru/".$article_or_exp."/".$papka_article."/".$img_article_name , $imagess.$papka_article."/".$img_article_name, $comma_separated[$count]);
			}
		}
	}
	
$topic_text[$count] = $topic_text_short[$count].'<br/><p><a name="cut" rel="nofollow"></a> </p><br/>'.$comma_separated[$count] ;
	
	  $query = "UPDATE $table SET topic_text = '".$topic_text[$count]."' WHERE topic_id='".$count."'";
//	  $query = "INSERT INTO $table (id, img_src) VALUES ('".$count."', '".$src_img."')";
//	  $query = "UPDATE $table SET img_src = '".$src_img."' WHERE id='".$count."'";
	   mysql_query($query) or die(mysql_error());

echo $topic_text[$count] , "<br /><br />";

}
*/

// Те же процессы, что и в предыдущем блоке, только распространённые на одну конкретно указанную страницу

$html2 = file_get_html('http://web.archive.org/web/20060718105324/http://www.cgtalk.ru/exc.php?id=sds');
$count69 = 42 ; // 43 6
$count_topic_id = 142 ; // 143 106
$_res = $html2->find('//div.page/div/table.page/tbody/tr/td/');
$comma_separated2 = implode("", $_res);
$comma_separated2 = str_replace('<td valign="top">', '', $comma_separated2);
$comma_separated2 = str_replace('</td>', '', $comma_separated2);
$comma_separated2 = str_replace('color="#FFFFFF"', '', $comma_separated2);
$comma_separated2 = str_replace('/web/20061010130009im_/http://www.cgtalk.ru/forum/images/smilies/smile.gif', '/imagess/smilies/smile.gif', $comma_separated2);
$comma_separated2 = iconv('UTF-8', 'CP1251', $comma_separated2);
//$comma_separated2 = delete44($comma_separated2, 'Copyright');

	foreach($html2->find('//div.page/div/table.page/tbody/tr/td/img') as $element_article)
	{
// обнуление идентификаторов расширения файлов иллюстраций
	$if_jpg = 0;
	$if_gif = 0;
		  // echo $element_article->src . '<br />';
		   $element_article_src1 = $element_article->src ;
		   
		   $src_img_nomer_article = delete44(str_replace("/web/", "", $element_article_src1) ,'im_');
		 //  echo $src_img_nomer_article , '<br />';
		   
		   $element_article_src2 = $element_article->src ;
		// Условие исходя из наличия www. http://www.cgtalk.ru/ в адресе src картинки внутри статьи
		if (substr_count($element_article_src2,"http://www.cgtalk.ru/")>0)
		{  
		   // Выявление какая папка article или exp в адресе src картинки статьи присутствует
		   $article_or_exp = delete44(str_replace("/web/".$src_img_nomer_article."im_/http://www.cgtalk.ru/", "", $element_article_src2) ,'/');
		//   echo  $article_or_exp , '<br />';
		   
			$src_img_article = str_replace("/web/".$src_img_nomer_article."im_/http://www.cgtalk.ru/".$article_or_exp."/", "", $element_article->src);
			$src_img_article = str_replace("images/", "", $src_img_article);
			$src_img_article = str_replace("preview/", "", $src_img_article);
			$src_img_article = str_replace("pics/sm/", "", $src_img_article);
			$src_img_article = str_replace("sm/", "", $src_img_article);
			$src_img_article = str_replace("pic/", "", $src_img_article);
			$src_img_article = str_replace("img/", "", $src_img_article);
		//   echo $src_img_article . '<br />';

		   $papka_article = delete44($src_img_article ,'/');
//		   echo $papka_article . "<br />";
		   $img_article_name = str_replace("$papka_article/", "", $src_img_article);
//		   echo $img_article_name . "<br />";

	// Имя файла изображения без расширения
			if (substr_count($img_article_name,".jpg")>0)
			{
				$img_article_name_br = str_replace(".jpg", "", $img_article_name);
				$if_jpg = 1;
			}
			if (substr_count($img_article_name,".gif")>0)
			{
				$img_article_name_br = str_replace(".gif", "", $img_article_name);
				$if_gif = 1;
			}
		   
		   $s_article = file_get_contents("http://web.archive.org".$element_article->src);	
		   $imagess = '/imagess/';
//		   mkdir($_SERVER['DOCUMENT_ROOT'].$imagess.$papka_article, 0777, true);
//		   file_put_contents($_SERVER['DOCUMENT_ROOT'].$imagess.$papka_article."/".$img_article_name, $s_article);

			if ($if_jpg == 1)
			{
				file_put_contents($_SERVER['DOCUMENT_ROOT'].$imagess.$papka_article."/".$img_article_name_br."_original.jpg", $s_article);
				file_put_contents($_SERVER['DOCUMENT_ROOT'].$imagess.$papka_article."/".$img_article_name_br."_preview.jpg", $s_article);
			}
			if ($if_gif == 1)
			{
				file_put_contents($_SERVER['DOCUMENT_ROOT'].$imagess.$papka_article."/".$img_article_name_br."_original.gif", $s_article);
				file_put_contents($_SERVER['DOCUMENT_ROOT'].$imagess.$papka_article."/".$img_article_name_br."_preview.gif", $s_article);
			}

        $src_img_filepath2 = $imagess.$papka_article."/".$img_article_name ;
	    $query = "INSERT INTO $table_image (topic_id, path) VALUES ('".$count_topic_id."', '".$src_img_filepath2."')";
	    mysql_query($query) or die(mysql_error());

		   $count_reurl_img_articles = substr_count($comma_separated2, 'img');
			for ($counter = 0; $counter < $count_reurl_img_articles; $counter++)
			{
	$comma_separated2 = str_replace("/web/".$src_img_nomer_article."im_/http://www.cgtalk.ru/".$article_or_exp."/".$papka_article."/images/".$img_article_name , $imagess.$papka_article."/".$img_article_name, $comma_separated2);
	$comma_separated2 = str_replace("/web/".$src_img_nomer_article."im_/http://www.cgtalk.ru/".$article_or_exp."/".$papka_article."/preview/".$img_article_name , $imagess.$papka_article."/".$img_article_name, $comma_separated2);
	$comma_separated2 = str_replace("/web/".$src_img_nomer_article."im_/http://www.cgtalk.ru/".$article_or_exp."/".$papka_article."/sm/".$img_article_name , $imagess.$papka_article."/".$img_article_name, $comma_separated2);
	$comma_separated2 = str_replace("/web/".$src_img_nomer_article."im_/http://www.cgtalk.ru/".$article_or_exp."/".$papka_article."/pics/sm/".$img_article_name , $imagess.$papka_article."/".$img_article_name, $comma_separated2);
	$comma_separated2 = str_replace("/web/".$src_img_nomer_article."im_/http://www.cgtalk.ru/".$article_or_exp."/".$papka_article."/pic/".$img_article_name , $imagess.$papka_article."/".$img_article_name, $comma_separated2);
	$comma_separated2 = str_replace("/web/".$src_img_nomer_article."im_/http://www.cgtalk.ru/".$article_or_exp."/".$papka_article."/img/".$img_article_name , $imagess.$papka_article."/".$img_article_name, $comma_separated2);
	$comma_separated2 = str_replace("/web/".$src_img_nomer_article."im_/http://www.cgtalk.ru/".$article_or_exp."/".$papka_article."/".$img_article_name , $imagess.$papka_article."/".$img_article_name, $comma_separated2);
	// Удаляем лишние пробелы
	$comma_separated2 = str_replace("  " , " ", $comma_separated2);

			}
		}
		// Если в адресе src картинки внутри статьи нет www. в домене http://cgtalk.ru/
		elseif (substr_count($element_article_src2,"http://cgtalk.ru/")>0)
		{
		   // Выявление какая папка article или exp в адресе src картинки статьи присутствует
		   $article_or_exp = delete44(str_replace("/web/".$src_img_nomer_article."im_/http://cgtalk.ru/", "", $element_article_src2) ,'/');
		   //echo  $article_or_exp , '<br />';
		   
			$src_img_article = str_replace("/web/".$src_img_nomer_article."im_/http://cgtalk.ru/".$article_or_exp."/", "", $element_article->src);
			$src_img_article = str_replace("images/", "", $src_img_article);
			$src_img_article = str_replace("preview/", "", $src_img_article);
			$src_img_article = str_replace("pics/sm/", "", $src_img_article);
			$src_img_article = str_replace("sm/", "", $src_img_article);
			$src_img_article = str_replace("pic/", "", $src_img_article);
			$src_img_article = str_replace("img/", "", $src_img_article);
		   //echo $src_img_article . '<br />';

		   $papka_article = delete44($src_img_article ,'/');
		// echo $papka_article . "<br />";
		   $img_article_name = str_replace("$papka_article/", "", $src_img_article);
		   //echo $img_article_name . "<br />";
		   
	// Имя файла изображения без расширения
			if (substr_count($img_article_name,".jpg")>0)
			{
				$img_article_name_br = str_replace(".jpg", "", $img_article_name);
				$if_jpg = 1;
			}
			if (substr_count($img_article_name,".gif")>0)
			{
				$img_article_name_br = str_replace(".gif", "", $img_article_name);
				$if_gif = 1;
			}
		   
		   $s_article = file_get_contents("http://web.archive.org".$element_article->src);	
		   $imagess = '/imagess/';
//		   mkdir($_SERVER['DOCUMENT_ROOT'].$imagess.$papka_article, 0777, true);
//		   file_put_contents($_SERVER['DOCUMENT_ROOT'].$imagess.$papka_article."/".$img_article_name, $s_article);

			if ($if_jpg == 1)
			{
				file_put_contents($_SERVER['DOCUMENT_ROOT'].$imagess.$papka_article."/".$img_article_name_br."_original.jpg", $s_article);
				file_put_contents($_SERVER['DOCUMENT_ROOT'].$imagess.$papka_article."/".$img_article_name_br."_preview.jpg", $s_article);
			}
			if ($if_gif == 1)
			{
				file_put_contents($_SERVER['DOCUMENT_ROOT'].$imagess.$papka_article."/".$img_article_name_br."_original.gif", $s_article);
				file_put_contents($_SERVER['DOCUMENT_ROOT'].$imagess.$papka_article."/".$img_article_name_br."_preview.gif", $s_article);
			}

        $src_img_filepath2 = $imagess.$papka_article."/".$img_article_name ;
	    $query = "INSERT INTO $table_image (topic_id, path) VALUES ('".$count_topic_id."', '".$src_img_filepath2."')";
	    mysql_query($query) or die(mysql_error());

		   $count_reurl_img_articles = substr_count($comma_separated2, 'img');
			for ($counter = 0; $counter < $count_reurl_img_articles; $counter++)
			{
	$comma_separated2 = str_replace("/web/".$src_img_nomer_article."im_/http://cgtalk.ru/".$article_or_exp."/".$papka_article."/images/".$img_article_name , $imagess.$papka_article."/".$img_article_name, $comma_separated2);
	$comma_separated2 = str_replace("/web/".$src_img_nomer_article."im_/http://cgtalk.ru/".$article_or_exp."/".$papka_article."/preview/".$img_article_name , $imagess.$papka_article."/".$img_article_name, $comma_separated2);
	$comma_separated2 = str_replace("/web/".$src_img_nomer_article."im_/http://cgtalk.ru/".$article_or_exp."/".$papka_article."/sm/".$img_article_name , $imagess.$papka_article."/".$img_article_name, $comma_separated2);
	$comma_separated2 = str_replace("/web/".$src_img_nomer_article."im_/http://cgtalk.ru/".$article_or_exp."/".$papka_article."/pics/sm/".$img_article_name , $imagess.$papka_article."/".$img_article_name, $comma_separated2);
	$comma_separated2 = str_replace("/web/".$src_img_nomer_article."im_/http://cgtalk.ru/".$article_or_exp."/".$papka_article."/pic/".$img_article_name , $imagess.$papka_article."/".$img_article_name, $comma_separated2);
	$comma_separated2 = str_replace("/web/".$src_img_nomer_article."im_/http://cgtalk.ru/".$article_or_exp."/".$papka_article."/img/".$img_article_name , $imagess.$papka_article."/".$img_article_name, $comma_separated2);
	$comma_separated2 = str_replace("/web/".$src_img_nomer_article."im_/http://cgtalk.ru/".$article_or_exp."/".$papka_article."/".$img_article_name , $imagess.$papka_article."/".$img_article_name, $comma_separated2);
	// Удаляем лишние пробелы
	$comma_separated2 = str_replace("  " , " ", $comma_separated2);
			}
		}
	}

$topic_text[$count69] = $topic_text_short[$count69].'<p><cut></p>'.$comma_separated2 ;
$topic_text[$count69] = str_replace(".jpg" , "_original.jpg", $topic_text[$count69]);
$topic_text[$count69] = str_replace(".gif" , "_original.gif", $topic_text[$count69]);
	
//	  $query = "UPDATE $table SET topic_text = '".$topic_text[$count69]."' WHERE topic_id='".$count69."'";
//	  $query = "INSERT INTO $table (id, img_src) VALUES ('".$count."', '".$src_img."')";
//	  $query = "UPDATE $table SET img_src = '".$src_img."' WHERE id='".$count."'";
//	   mysql_query($query) or die(mysql_error());

echo $topic_text[$count69] , "<br /><br />";


//Вариант вывода
//var_dump($_res);

/* Закрываем соединение */
mysql_close();