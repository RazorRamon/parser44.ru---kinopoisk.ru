<?php

set_time_limit(0);
include 'simple_html_dom.php';

// �������, ��������� �� ������ �� ����� ������� � ����������� ��������
function delete44($str,$symbol='') 
{ 
    return($strpos=mb_strpos($str,$symbol))!==false?mb_substr($str,0,$strpos,'utf8'):$str;
}

/* ����������� � ����� ������ */
$hostname = "rscx.ru"; // rscx.ru - ��������/���� �������, � MySQL
$username = "poly_test"; // poly_test - ��� ������������
$password = "y4mHO0Jf"; // y4mHO0Jf - ������ ������������
$dbName = "poly_test"; // poly_test - �������� ���� ������
 
/* ������� MySQL, � ������� ����� ��������� ������ */
$table = "prefix_topic_content"; // prefix_topic_content
 
/* ������� ���������� */
mysql_connect($hostname, $username, $password) or die ("�� ���� ������� ����������");
 
/* �������� ���� ������. ���� ���������� ������ - ������� �� */
mysql_select_db($dbName) or die (mysql_error());
 
// Create DOM from URL or file
$html = file_get_html('http://web.archive.org/web/20060826102742/http://www.cgtalk.ru/index.php?id=menu');

// ���� ������� ������ � ������ $massiv_anons[]
$count = 0;
foreach($html->find('//table.page/tbody/tr/td/table.tborder/tbody/tr/td.alt2/') as $element_anons)
{
      $count++;
	  //echo $count ;
	  $massiv_anons[$count] = $element_anons ;
	  // ������� ��������� ���������� �������� ������� $massiv_anons[$count] �� CP1251 � UTF-8
	  $massiv_anons[$count] = iconv('UTF-8', 'CP1251', $massiv_anons[$count]) ;
	  echo $massiv_anons[$count] . '<br /><br />';
}
/*
// ���� ����-�������� ������� ������ - ����������� �� � ����� /mini-img/ , ���������� � �� ���������� (� ������ ����������� ������) ������ � �������������
$count = 0;
foreach($html->find('//table.page/tbody/tr/td/table.tborder/tbody/tr/td.alt1/a') as $element)
{
	echo $element->href . '<br />';
	$element_img = $element->children(0)->src;
	$link_article = "http://web.archive.org".$element->href ;
	// ��������� ����� ����� article ��� exp � ������ ������ �� ������ ������������
    $article_or_exp_mini = delete44(str_replace("/web/20060826102742im_/http://www.cgtalk.ru/", "", $element_img) ,'/');
	//   echo  $article_or_exp_mini , '<br />';
	   
	   // �������� ������������ ����� ����-��������
		$src_img = str_replace("/web/20060826102742im_/http://www.cgtalk.ru/".$article_or_exp_mini."/", "", $element_img);
		$src_img = str_replace("/", "-", $src_img);
	//	echo $src_img , "<br />" ;

	// ������������ ������ ������ ����-������, � ������ ������ ����-�������� � ������ ������
    $count++;
	//echo $count ;
	
		$filepath = '/mini-img/';	  
$topic_text_short[$count] = '<p><img style="float:left; margin-bottom:10px; margin-right:10px; margin-left:5px;" src="'.$filepath.$src_img.'"/>'.$massiv_anons[$count].'</p><div style="clear: both"></div>' ;
		echo $topic_text_short[$count] , "<br />" ;

		// ���������� ����-�������� ������� ������ � ����� /mini-img/
//		$s = file_get_contents("http://web.archive.org".$element_img);	
//	    file_put_contents($_SERVER['DOCUMENT_ROOT'].$filepath.$src_img, $s);

	  $query = "UPDATE $table SET topic_text_short = '".$topic_text_short[$count]."' WHERE topic_id='".$count."'";
//	  $query = "INSERT INTO $table (id, img_src) VALUES ('".$count."', '".$src_img."')";
//	  $query = "UPDATE $table SET img_src = '".$src_img."' WHERE id='".$count."'";
	   mysql_query($query) or die(mysql_error());

}
*/
// ���� ������ ���������� �������� ������:
// - ������ � �������� (���������� �� ���������� ����������� ������);
// - ���������� �������������� ����� �������� �� ��������
// - �������������, ���������� �������� - ���������� � �� � ������� "topic_text"

/*
$count = 0;
foreach($html->find('//table.page/tbody/tr/td/table.tborder/tbody/tr/td.alt1/a') as $element)
{
	echo $element->href . '<br />';
	$element_img = $element->children(0)->src;
	$link_article = "http://web.archive.org".$element->href ;

	$html2 = file_get_html($link_article);
//$html2 = file_get_html('http://web.archive.org/web/20120404155049/http://www.cgtalk.ru/exc.php?id=vlad');

$_res = $html2->find('//div.page/div/table.page/tbody/tr/td/');
$comma_separated = implode("", $_res);
$comma_separated = iconv('UTF-8', 'CP1251', $comma_separated);
	
	foreach($html2->find('//div.page/div/table.page/tbody/tr/td/img') as $element_article)
	{
		   echo $element_article->src . '<br />';
		   $element_article_src1 = $element_article->src ;
		   
		   $src_img_nomer_article = delete44(str_replace("/web/", "", $element_article_src1) ,'im_');
		   echo $src_img_nomer_article , '<br />';
		   
		   $element_article_src2 = $element_article->src ;
		// ������� ������ �� ������� www. http://www.cgtalk.ru/ � ������ src �������� ������ ������
		if (substr_count($element_article_src2,"http://www.cgtalk.ru/")>0)
		{  
		   // ��������� ����� ����� article ��� exp � ������ src �������� ������ ������������
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
		   echo $papka_article . "<br />";
		   $img_article_name = str_replace("$papka_article/", "", $src_img_article);
		   echo $img_article_name . "<br />";
		   
		   $s_article = file_get_contents("http://web.archive.org".$element_article->src);	
		   $imagess = '/imagess/';
		   mkdir($_SERVER['DOCUMENT_ROOT'].$imagess.$papka_article, 0777, true);
		   file_put_contents($_SERVER['DOCUMENT_ROOT'].$imagess.$papka_article."/".$img_article_name, $s_article);

		   $count_reurl_img_articles = substr_count($comma_separated, 'img');
			for ($counter = 0; $counter < $count_reurl_img_articles; $counter++)
			{
	$comma_separated = str_replace("/web/".$src_img_nomer_article."im_/http://www.cgtalk.ru/".$article_or_exp."/".$papka_article."/images/".$img_article_name , $imagess.$papka_article."/".$img_article_name, $comma_separated);
	$comma_separated = str_replace("/web/".$src_img_nomer_article."im_/http://www.cgtalk.ru/".$article_or_exp."/".$papka_article."/preview/".$img_article_name , $imagess.$papka_article."/".$img_article_name, $comma_separated);
	$comma_separated = str_replace("/web/".$src_img_nomer_article."im_/http://www.cgtalk.ru/".$article_or_exp."/".$papka_article."/sm/".$img_article_name , $imagess.$papka_article."/".$img_article_name, $comma_separated);
	$comma_separated = str_replace("/web/".$src_img_nomer_article."im_/http://www.cgtalk.ru/".$article_or_exp."/".$papka_article."/pics/sm/".$img_article_name , $imagess.$papka_article."/".$img_article_name, $comma_separated);
	$comma_separated = str_replace("/web/".$src_img_nomer_article."im_/http://www.cgtalk.ru/".$article_or_exp."/".$papka_article."/pic/".$img_article_name , $imagess.$papka_article."/".$img_article_name, $comma_separated);
	$comma_separated = str_replace("/web/".$src_img_nomer_article."im_/http://www.cgtalk.ru/".$article_or_exp."/".$papka_article."/img/".$img_article_name , $imagess.$papka_article."/".$img_article_name, $comma_separated);
	$comma_separated = str_replace("/web/".$src_img_nomer_article."im_/http://www.cgtalk.ru/".$article_or_exp."/".$papka_article."/".$img_article_name , $imagess.$papka_article."/".$img_article_name, $comma_separated);
			}
		}
		// ���� � ������ src �������� ������ ������ ��� www. � ������ http://cgtalk.ru/
		elseif (substr_count($element_article_src2,"http://cgtalk.ru/")>0)
		{
		   // ��������� ����� ����� article ��� exp � ������ src �������� ������ ������������
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
		   
		   $s_article = file_get_contents("http://web.archive.org".$element_article->src);	
		   $imagess = '/imagess/';
		   mkdir($_SERVER['DOCUMENT_ROOT'].$imagess.$papka_article, 0777, true);
		   file_put_contents($_SERVER['DOCUMENT_ROOT'].$imagess.$papka_article."/".$img_article_name, $s_article);

		   $count_reurl_img_articles = substr_count($comma_separated, 'img');
			for ($counter = 0; $counter < $count_reurl_img_articles; $counter++)
			{
	$comma_separated = str_replace("/web/".$src_img_nomer_article."im_/http://cgtalk.ru/".$article_or_exp."/".$papka_article."/images/".$img_article_name , $imagess.$papka_article."/".$img_article_name, $comma_separated);
	$comma_separated = str_replace("/web/".$src_img_nomer_article."im_/http://cgtalk.ru/".$article_or_exp."/".$papka_article."/preview/".$img_article_name , $imagess.$papka_article."/".$img_article_name, $comma_separated);
	$comma_separated = str_replace("/web/".$src_img_nomer_article."im_/http://cgtalk.ru/".$article_or_exp."/".$papka_article."/sm/".$img_article_name , $imagess.$papka_article."/".$img_article_name, $comma_separated);
	$comma_separated = str_replace("/web/".$src_img_nomer_article."im_/http://cgtalk.ru/".$article_or_exp."/".$papka_article."/pics/sm/".$img_article_name , $imagess.$papka_article."/".$img_article_name, $comma_separated);
	$comma_separated = str_replace("/web/".$src_img_nomer_article."im_/http://cgtalk.ru/".$article_or_exp."/".$papka_article."/pic/".$img_article_name , $imagess.$papka_article."/".$img_article_name, $comma_separated);
	$comma_separated = str_replace("/web/".$src_img_nomer_article."im_/http://cgtalk.ru/".$article_or_exp."/".$papka_article."/img/".$img_article_name , $imagess.$papka_article."/".$img_article_name, $comma_separated);
	$comma_separated = str_replace("/web/".$src_img_nomer_article."im_/http://cgtalk.ru/".$article_or_exp."/".$papka_article."/".$img_article_name , $imagess.$papka_article."/".$img_article_name, $comma_separated);
			}
		}
	}

echo $comma_separated;
}
*/
//var_dump($_res);


/*
foreach($html->find('//table.page/tbody/tr/td/table.tborder/tbody/tr/td.alt1/a/img') as $element)
{
//	echo $mini_img .= $element->children(0)->src . '<br />';

	if (substr_count($element->src,"/web/20060826102742im_/http://www.cgtalk.ru/exc/")>0)
	{
		$src_img = str_replace("/web/20060826102742im_/http://www.cgtalk.ru/exc/", "", $element->src);
		$src_img = str_replace("/", "-", $src_img);
	//	echo $src_img , "<br />" ;	
	//  echo "http://web.archive.org".$element->src , "<br />" ;

//		$s = file_get_contents("http://web.archive.org".$element->src);	
		$filepath = '/mini-img/';
        //mkdir($_SERVER['DOCUMENT_ROOT'].$filepath, 0777, true);
//      file_put_contents($_SERVER['DOCUMENT_ROOT'].$filepath.$src_img, $s);

		$query = "UPDATE $table (img_src)
		SET ('".$src_img."')";
	    mysql_query($query) or die(mysql_error());

	}
	elseif (substr_count($element->src,"/web/20060826102742im_/http://www.cgtalk.ru/articles/")>0)
	{
		$src_img = str_replace("/web/20060826102742im_/http://www.cgtalk.ru/articles/", "", $element->src);
		$src_img = str_replace("/", "-", $src_img);
	//	echo $src_img , "<br />" ;
	//	$s = file_get_contents("http://web.archive.org".$element->src);	
		$filepath = '/mini-img/';
	//  file_put_contents($_SERVER['DOCUMENT_ROOT'].$filepath.$src_img, $s);

		$query = "UPDATE $table (img_src)
		SET ('".$src_img."')";
	    mysql_query($query) or die(mysql_error());

	}
}
*/
/* ��������� ���������� */
mysql_close();