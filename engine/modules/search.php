<?php
/*
=====================================================
 DataLife Engine Nulled by M.I.D-Team
-----------------------------------------------------
 http://www.mid-team.ws/
-----------------------------------------------------
 Copyright (c) 2004,2009 SoftNews Media Group
=====================================================
 Данный код защищен авторскими правами
=====================================================
 Файл: search.php
-----------------------------------------------------
 Назначение: поиск по сайту
=====================================================
*/
if( ! defined( 'DATALIFEENGINE' ) ) {
	die( "Hacking attempt!" );
}

if( ! $user_group[$member_id['user_group']]['allow_search'] ) {
	
	$lang['search_denied'] = str_replace( '{group}', $user_group[$member_id['user_group']]['group_name'], $lang['search_denied'] );
	msgbox( $lang['all_info'], $lang['search_denied'] );

} else {
	
	function strip_data($text) {
		$quotes = array ("\x27", "\x22", "\x60", "\t", "\n", "\r", "'", ",", "/", "¬", ";", ":", "@", "~", "[", "]", "{", "}", "=", ")", "(", "*", "&", "^", "%", "$", "<", ">", "?", "!", '"' );
		$goodquotes = array ("-", "+", "#" );
		$repquotes = array ("\-", "\+", "\#" );
		$text = stripslashes( $text );
		$text = trim( strip_tags( $text ) );
		$text = str_replace( $quotes, '', $text );
		$text = str_replace( $goodquotes, $repquotes, $text );
		return $text;
	}
	
	$count_result = 0;
	$sql_count = "";
	$tpl->load_template( 'search.tpl' );
	
	// Минимальное количество статей на страницу
	$config['result_num_def'] = $config['news_number'] * 2;
	// Максимальное количество статей на страницу
	$config['result_num_max'] = $config['result_num_def'] * 10;
	// Минимальное количество символов в слове поиска
	$config['search_length_min'] = 3;
	// Нечетное число, указывающее количество линков на страницы в каждой из 3-х секций - начальной, активной, конечной
	// Нечетное число, указывающее количество линков на страницы в секции
	$config['pages_per_section'] = 7;
	
	$this_date = date( "Y-m-d H:i:s", $_TIME );
	if( intval( $config['no_date'] ) ) $this_date = " AND " . PREFIX . "_post.date < '" . $this_date . "'"; else $this_date = "";
	
	if( isset( $_REQUEST['story'] ) ) $story = substr( strip_data( $_REQUEST['story'] ), 0, 90 ); else $story = "";
	if( isset( $_REQUEST['search_start'] ) ) $search_start = intval( $_REQUEST['search_start'] ); else $search_start = 0;
	if( isset( $_REQUEST['titleonly'] ) ) $titleonly = intval( $_REQUEST['titleonly'] ); else $titleonly = 0;
	if( isset( $_REQUEST['searchuser'] ) ) $searchuser = substr( strip_data( $_REQUEST['searchuser'] ), 0, 40 ); else $searchuser = "";
	if( isset( $_REQUEST['exactname'] ) ) $exactname = $_REQUEST['exactname']; else $exactname = "";
	if( isset( $_REQUEST['postonly'] ) ) $postonly = intval( $_REQUEST['postonly'] ); else $postonly = 0;
	if( isset( $_REQUEST['replyless'] ) ) $replyless = intval( $_REQUEST['replyless'] ); else $replyless = 0;
	if( isset( $_REQUEST['replylimit'] ) ) $replylimit = intval( $_REQUEST['replylimit'] ); else $replylimit = 0;
	if( isset( $_REQUEST['searchdate'] ) ) $searchdate = intval( $_REQUEST['searchdate'] ); else $searchdate = 0;
	if( isset( $_REQUEST['beforeafter'] ) ) $beforeafter = strip_data( $_REQUEST['beforeafter'] ); else $beforeafter = "after";
	if( isset( $_REQUEST['sortby'] ) ) $sortby = strip_data( $_REQUEST['sortby'] ); else $sortby = "date";
	if( isset( $_REQUEST['resorder'] ) ) $resorder = strip_data( $_REQUEST['resorder'] ); else $resorder = "desc";
	if( isset( $_REQUEST['showposts'] ) ) $showposts = intval( $_REQUEST['showposts'] ); else $showposts = 0;
	if( isset( $_REQUEST['result_num'] ) ) $result_num = intval( $_REQUEST['result_num'] ); else $result_num = $config['result_num_def'];
	if( isset( $_REQUEST['result_from'] ) ) $result_from = intval( $_REQUEST['result_from'] ); else $result_from = 1; // Показать страницу с результатом № ХХХ
	if( isset( $_REQUEST['catlist'] ) ) $category_list = $db->safesql( implode( ',', $_REQUEST['catlist'] ) ); else $category_list = "";
	$full_search = intval( $_REQUEST['full_search'] );
	
	$story = preg_replace( "#^(\s*OR\s+)*#i", '', $story );
	$story = preg_replace( "#(\s+OR\s*)*$#i", '', $story );
	
	$findstory = stripslashes( $story ); // Для вывода в поле поиска
	

	if( empty( $story ) and ! empty( $searchuser ) ) $story = "___SEARCH___ALL___"; // Для поиска всех статей
	if( $search_start < 0 ) $search_start = 0; // Начальная страница поиска
	if( $titleonly < 0 or $titleonly > 6 ) $titleonly = 0; // Искать в заголовках, статьях, дополнительных полях, комментариях или везде
	if( $postonly < 0 or $postonly > 2 ) $postonly = 0; // Найти статьи, созданные пользователем
	if( $replyless < 0 or $replyless > 1 ) $replyless = 0; // Искать больше или меньше ответов
	if( $replylimit < 0 ) $replylimit = 0; // Лимит ответов
	if( $showposts < 0 or $showposts > 1 ) $showposts = 0; // Искать в статьях или комментариях юзера
	if( $result_num < 1 ) $result_num = $config['result_num_def']; // Количество результатов на страницу
	if( $result_num > $config['result_num_max'] ) $result_num = $config['result_num_max'];
	$config_search_numbers = $result_num;
	
	$listdate = array (0, - 1, 1, 7, 14, 30, 90, 180, 365 ); // Искать за период ХХХ дней
	if( ! (in_array( $searchdate, $listdate )) ) $searchdate = 0;
	if( $beforeafter != "after" and $beforeafter != "before" ) $beforeafter = "after"; // Искать до или после периода дней
	$listsortby = array ("date", "title", "comm_num", "news_read", "autor", "category", "rating" );
	if( ! (in_array( $sortby, $listsortby )) ) $sortby = "date"; // Сортировать по полям
	$listresorder = array ("desc", "asc" );
	if( ! (in_array( $resorder, $listresorder )) ) $resorder = "desc"; // Сортировать по возрастающей или убывающей
	

	// Определение выбранных ранее опций, переданных в форме
	$titleonly_sel = array ('0' => '', '1' => '', '2' => '', '3' => '', '4' => '', '5' => '' );
	$titleonly_sel[$titleonly] = 'selected="selected"';
	$postonly_sel = array ('0' => '', '1' => '', '2' => '' );
	$postonly_sel[$postonly] = 'selected="selected"';
	$replyless_sel = array ('0' => '', '1' => '' );
	$replyless_sel[$replyless] = 'selected="selected"';
	$searchdate_sel = array ('0' => '', '-1' => '', '1' => '', '7' => '', '14' => '', '30' => '', '90' => '', '180' => '', '365' => '' );
	$searchdate_sel[$searchdate] = 'selected="selected"';
	$beforeafter_sel = array ('after' => '', 'before' => '' );
	$beforeafter_sel[$beforeafter] = 'selected="selected"';
	$sortby_sel = array ('date' => '', 'title' => '', 'comm_num' => '', 'news_read' => '', 'autor' => '', 'category' => '', 'rating' => '' );
	$sortby_sel[$sortby] = 'selected="selected"';
	$resorder_sel = array ('desc' => '', 'asc' => '' );
	$resorder_sel[$resorder] = 'selected="selected"';
	$showposts_sel = array ('0' => '', '1' => '' );
	$showposts_sel[$showposts] = 'checked="checked"';
	if( $exactname == "yes" ) $exactname_sel = 'checked="checked"';
	else $exactname_sel = '';
	
	// Вывод формы поиска
	if( $category_list == "" or $category_list == "0" ) {
		$catselall = "selected";
	} else {
		$catselall = "";
		$category_list = preg_replace( "/^0\,/", '', $category_list );
	}
	
	// Определение и вывод доступных категорий
	$cats = "<select class=\"rating\" style=\"width:95%;height:200px;\" name=\"catlist[]\" size=\"13\" multiple=\"multiple\">";
	$cats .= "<option " . $catselall . " value=\"0\">" . $lang['s_allcat'] . "</option>";
	$cats .= CategoryNewsSelection( explode( ',', $category_list ), 0, false );
	$cats .= "</select>";
	
	$tpl->copy_template .= <<<HTML
<script type="text/javascript" language="javascript">
<!-- begin
function clearform(frmname){
  var frm = document.getElementById(frmname);
  for (var i=0;i<frm.length;i++) {
    var el=frm.elements[i];
    if (el.type=="checkbox" || el.type=="radio") {
    	if (el.name=='showposts') {document.getElementById('rb_showposts_0').checked=1; } else {el.checked=0; }
    }
    if ((el.type=="text") || (el.type=="textarea") || (el.type == "password")) { el.value=""; continue; }
    if ((el.type=="select-one") || (el.type=="select-multiple")) { el.selectedIndex=0; }
  }
  document.getElementById('result_num').value = "{$config['result_num_def']}";
  document.getElementById('replylimit').value = 0;
  document.getElementById('search_start').value = 0;
  document.getElementById('result_from').value = 1;
}
function list_submit(prm){
  var frm = document.getElementById('fullsearch');
	if (prm == -1) {
		prm=Math.ceil(frm.result_from.value / frm.result_num.value);
	} else {
		frm.result_from.value=(prm-1) * frm.result_num.value + 1;
	}
	frm.search_start.value=prm;

  showBusyLayer();

  frm.submit();
  return false;
}
function full_submit(prm){
    document.getElementById('fullsearch').full_search.value=prm;
    list_submit(-1);
}
function reg_keys(key) {
	var code;
	if (!key) var key = window.event;
	if (key.keyCode) code = key.keyCode;
	else if (key.which) code = key.which;

	if (code == 13) {
		list_submit(-1);
	}
};

document.onkeydown = reg_keys;
// end -->
</script>
HTML;
	
	$searchtable = <<<HTML
<form name="fullsearch" id="fullsearch" action="{$config['http_home_url']}index.php?do=search" method="post">
<input type="hidden" name="do" id="do" value="search">
<input type="hidden" name="subaction" id="subaction" value="search">
<input type="hidden" name="search_start" id="search_start" value="$search_start">
<input type="hidden" name="full_search" id="full_search" value="$full_search">
HTML;
	
	if( $full_search ) {
		
		$searchtable .= <<<HTML
<table cellpadding="0" cellspacing="0" width="100%">
  <tr>
    <td class="search">
      <div align="center">
        <table cellpadding="0" cellspacing="2" width="100%">

        <tr style="vertical-align: top;">
				<td class="search">
					<fieldset style="margin:0px">
						<legend>{$lang['s_con']}</legend>
						<table cellpadding="0" cellspacing="3" border="0">
						<tr>
						<td class="search">
							<div>{$lang['s_word']}</div>
							<div><input type="text" name="story" size="35" id="searchinput" value="$findstory" class="textin" style="width:250px" /></div>
						</td>
						</tr>
						<tr>
						<td class="search">
							<select class="textin" name="titleonly" id="titleonly">
								<option {$titleonly_sel['0']} value="0">{$lang['s_ncom']}</option>
								<option {$titleonly_sel['1']} value="1">{$lang['s_ncom1']}</option>
                                <option {$titleonly_sel['6']} value="6">{$lang['s_static']}</option>
								<option {$titleonly_sel['3']} value="3">{$lang['s_tnews']}</option>
								<option {$titleonly_sel['5']} value="5">{$lang['s_nid']}</option>
							</select>
						</td>
						</tr>
						</table>
					</fieldset>
				</td>

				<td class="search" valign="top">					
					<fieldset style="margin:0px">
						<legend>{$lang['s_mname']}</legend>
						<table cellpadding="0" cellspacing="3" border="0">
						<tr>
						<td class="search">
							<div>{$lang['s_fname']}</div>
							<div id="userfield"><input type="text" name="searchuser" id="searchuser" size="35"  value="$searchuser" class="textin" style="width:250px" /><br /><label for="lbexactname"><input type="checkbox" name="exactname" value="yes" id="exactname" {$exactname_sel} />{$lang['s_fgname']}</label>
							</div>
						</td>
						</tr>
						</table>
					</fieldset>
				</td>
				</tr>

				<tr style="vertical-align: top;">

				<td width="50%" class="search">
					<fieldset style="margin:0px">
						<legend>{$lang['s_fart']}</legend>
						<div style="padding:3px">
							<select class="textin" name="replyless" id="replyless" style="width:200px">
								<option {$replyless_sel['0']} value="0">{$lang['s_fmin']}</option>
								<option {$replyless_sel['1']} value="1">{$lang['s_fmax']}</option>
							</select>
							<input type="text" name="replylimit" id="replylimit" size="5" value="$replylimit" class="textin" /> {$lang['s_wcomm']}
						</div>
					</fieldset>

					<fieldset style="padding-top:10px">
						<legend>{$lang['s_fdaten']}</legend>

						<div style="padding:3px">					
							<select name="searchdate" id="searchdate" class="textin" style="width:200px">
								<option {$searchdate_sel['0']} value="0">{$lang['s_tall']}</option>
								<option {$searchdate_sel['-1']} value="-1">{$lang['s_tlast']}</option>
								<option {$searchdate_sel['1']} value="1">{$lang['s_tday']}</option>
								<option {$searchdate_sel['7']} value="7">{$lang['s_tweek']}</option>
								<option {$searchdate_sel['14']} value="14">{$lang['s_ttweek']}</option>
								<option {$searchdate_sel['30']} value="30">{$lang['s_tmoth']}</option>
								<option {$searchdate_sel['90']} value="90">{$lang['s_tfmoth']}</option>
								<option {$searchdate_sel['180']} value="180">{$lang['s_tsmoth']}</option>
								<option {$searchdate_sel['365']} value="365">{$lang['s_tyear']}</option>
							</select>
							<select name="beforeafter" id="beforeafter" class="textin">
								<option {$beforeafter_sel['after']} value="after">{$lang['s_fnew']}</option>
								<option {$beforeafter_sel['before']} value="before">{$lang['s_falt']}</option>
							</select>
						</div>
					</fieldset>

					<fieldset style="padding-top:10px">
						<legend>{$lang['s_fsoft']}</legend>
							<div style="padding:3px">
								<select name="sortby" id="sortby" class="textin" style="width:200px">
									<option {$sortby_sel['date']} value="date" selected="selected">{$lang['s_fsdate']}</option>
									<option {$sortby_sel['title']} value="title" >{$lang['s_fstitle']}</option>
									<option {$sortby_sel['comm_num']} value="comm_num" >{$lang['s_fscnum']}</option>
									<option {$sortby_sel['news_read']} value="news_read" >{$lang['s_fsnnum']}</option>
									<option {$sortby_sel['autor']} value="autor" >{$lang['s_fsaut']}</option>
									<option {$sortby_sel['category']} value="category" >{$lang['s_fscat']}</option>
									<option {$sortby_sel['rating']} value="rating" >{$lang['s_fsrate']}</option>
								</select>
								<select name="resorder" id="resorder" class="textin">
									<option {$resorder_sel['desc']} value="desc">{$lang['s_fsdesc']}</option>
									<option {$resorder_sel['asc']} value="asc">{$lang['s_fsasc']}</option>
								</select>
							</div>
					</fieldset>

					<fieldset style="padding-top:10px">
						<legend>{$lang['s_vlegend']}</legend>

						<table cellpadding="0" cellspacing="3" border="0">
						<tr align="left" valign="middle">
						<td class="search">
							<span>{$lang['s_vnum']} </span><input type="text" name="result_num" id="result_num" size="3" value="$result_num" class="textin" />
						</td>
						<td align="right" class="search">{$lang['s_vwie']}</td>
						</tr>
						<tr align="left" valign="middle">
						<td class="search">
							<span>{$lang['s_vjump']} </span><input type="text" name="result_from" id="result_from" size="6" value="$result_from" class="textin" />
						</td>
						<td align="right" class="search">
							<label for="rb_showposts_0"><input type="radio" name="showposts" value="0" id="rb_showposts_0" {$showposts_sel['0']} />{$lang['s_vnews']}</label>
							<label for="rb_showposts_1"><input type="radio" name="showposts" value="1" id="rb_showposts_1" {$showposts_sel['1']} />{$lang['s_vtitle']}</label>
						</td>
						</tr>

						</table>
					</fieldset>
				</td>

				<td width="50%" class="search" valign="top">
					<fieldset style="margin:0px">
						<legend>{$lang['s_fcats']}</legend>
							<div style="padding:3px">
								<div>$cats</div>
							</div>

					</fieldset>
				</td>
				</tr>

        <tr>
                <td class="search" colspan="2">
                    <div style="margin-top:6px">
                        <input type="button" class="bbcodes" style="margin:0px 20px 0 0px;" name="dosearch" id="dosearch" value="{$lang['s_fstart']}" onClick="javascript:list_submit(-1); return false;" />
                        <input type="button" class="bbcodes" style="margin:0px 20px 0 20px;" name="doclear" id="doclear" value="{$lang['s_fstop']}" onClick="javascript:clearform('fullsearch'); return false;" />
                        <input type="reset" class="bbcodes" style="margin:0px 20px 0 20px;" name="doreset" id="doreset" value="{$lang['s_freset']}">
                    </div>

                </td>
                </tr>

        </table>
      </div>
    </td>
  </tr>
</table>
HTML;
	
	} else {

	if ( $smartphone_detected ) {

		$link_full_search = "";

	} else {

		$link_full_search = "<input type=\"button\" class=\"bbcodes\" style=\"width:150px\" name=\"dofullsearch\" id=\"dofullsearch\" value=\"{$lang['s_ffullstart']}\" onClick=\"javascript:full_submit(1); return false;\" />";

	}
		
		$searchtable .= <<<HTML
<input type="hidden" name="result_from" id="result_from" value="$result_from">
<input type="hidden" name="result_num" id="result_num" value="$result_num">

<table cellpadding="4" cellspacing="0" width="100%">
  <tr>
    <td class="search">
      <div style="margin:10px;">
                <input type="text" name="story" id="searchinput" value="$findstory" class="textin" style="width:250px" /><br /><br />
                <input type="button" class="bbcodes" name="dosearch" id="dosearch" value="{$lang['s_fstart']}" onClick="javascript:list_submit(-1); return false;" />
                {$link_full_search}
            </div>

        </td>
    </tr>
</table>
HTML;
	
	}
	
	$searchtable .= <<<HTML

</form>
HTML;
	
	$tpl->set( '{searchtable}', $searchtable );
	// По умолчанию, выводится только форма поиска
	if( $subaction != "search" ) {
		$tpl->set_block( "'\[searchmsg\](.*?)\[/searchmsg\]'si", "" );
		$tpl->compile( 'content' );
	}
	// Конец вывода формы поиска
	

	if( $subaction == "search" ) {
		// Вывод результатов поиска
		

		$story = preg_replace( "#\s+OR\s+#i", '__OR__', trim( $story ) );
		$storywords = explode( "__OR__", $story );
		
		$story = preg_replace( "#(\s+|__OR__)#i", '%', $story );
		
		$arr = explode( '%', $story );
		$story_maxlen = 0;
		foreach ( $arr as $word ) {
			$wordlen = strlen( trim( $word ) );
			if( $wordlen > $story_maxlen ) {
				$story_maxlen = $wordlen;
			}
		}
		
		if( (empty( $story ) or ($story_maxlen < $config['search_length_min'])) and (empty( $searchuser ) or (strlen( $searchuser ) < $config['search_length_min'])) ) {
			
			msgbox( $lang['all_info'], $lang['search_err_3'] );
			
			$tpl->set( '{searchmsg}', '' );
			$tpl->set( '[searchmsg]', "" );
			$tpl->set( '[/searchmsg]', "" );
			$tpl->compile( 'content' );
		
		} else {
			// Начало подготовки поиска
			if( $search_start ) {
				$search_start = $search_start - 1;
				$search_start = $search_start * $config_search_numbers;
			}
			
			// Проверка разрешенных категорий из списка выбранных категорий
			$allow_cats = $user_group[$member_id['user_group']]['allow_cats'];
			$allow_list = explode( ',', $allow_cats );
			$stop_list = "";
			if( $allow_list[0] == "all" ) {
				// Все категории доступны для группы
				if( $category_list == "" or $category_list == "0" ) {
					// Выбран поиск по всем категориям
					;
				} else {
					// Выбран поиск по некоторым категориям
					$stop_list = str_replace( ',', '|', $category_list );
				}
			} else {
				// Не все категории доступны для группы
				if( $category_list == "" or $category_list == "0" ) {
					// Выбран поиск по всем категориям
					$stop_list = str_replace( ',', '|', $allow_cats );
				} else {
					// Выбран поиск по некоторым категориям
					$cats_list = explode( ',', $category_list );
					foreach ( $cats_list as $id ) {
						if( in_array( $id, $allow_list ) ) $stop_list .= $id . '|';
					}
					$stop_list = substr( $stop_list, 0, strlen( $stop_list ) - 1 );
				}
			}
			// Ограничение по категориям
			$where_category = "";
			if( ! empty( $stop_list ) ) {
				
				if( $config['allow_multi_category'] ) {
					
					$where_category = "category regexp '[[:<:]](" . $stop_list . ")[[:>:]]'";
				
				} else {
					
					$stop_list = str_replace( "|", "','", $stop_list );
					$where_category = "category IN ('" . $stop_list . "')";
				
				}
			}
			
			if( $story == "___SEARCH___ALL___" ) $story = '';
			$thistime = date( "Y-m-d H:i:s", (time() + $config['date_adjust'] * 60) );
			
			if( $exactname == 'yes' ) $likename = '';
			else $likename = '%';
			if( $searchdate != '0' ) {
				if( $searchdate != '-1' ) {
					$qdate = date( "Y-m-d H:i:s", (time() + $config['date_adjust'] * 60 - $searchdate * 86400) );
				} else {
					if( $is_logged and isset( $_SESSION['member_lasttime'] ) ) $qdate = date( "Y-m-d H:i:s", $_SESSION['member_lasttime'] );
					else $qdate = $thistime;
				}
			}
			
			// Поиск по автору статьи или комментария
			$autor_posts = '';
			$autor_comms = '';
			if( ! empty( $searchuser ) ) {
				switch ($titleonly) {
					case 2 :
						// Искать в статьях и комментариях
						$autor_posts = PREFIX . "_post.autor like '$searchuser$likename'";
						$autor_comms = PREFIX . "_comments.autor like '$searchuser$likename'";
						break;
					case 0 :
						// Искать только в статьях
						$autor_posts = PREFIX . "_post.autor like '$searchuser$likename'";
						break;
					case 1 :
						// Искать только в комментариях
						$autor_comms = PREFIX . "_comments.autor like '$searchuser$likename'";
						break;
				}
			}
			
			$where_reply = "";
			if( ! empty( $replylimit ) ) {
				if( $replyless == 0 ) $where_reply = PREFIX . "_post.comm_num >= '" . $replylimit . "'";
				else $where_reply = PREFIX . "_post.comm_num <= '" . $replylimit . "'";
			}
			
			// Поиск по ключевым словам
			if( ! empty( $story ) ) {
				$titleonly_where = array ('0' => "short_story LIKE '%{story}%' OR full_story LIKE '%{story}%' OR " . PREFIX . "_post.xfields LIKE '%{story}%' OR title LIKE '%{story}%'", // Искать только в статьях
										  '1' => "text LIKE '%{story}%'", // Искать только в комментариях
										  '2' => "short_story LIKE '%{story}%' OR full_story LIKE '%{story}%' OR " . PREFIX . "_post.xfields LIKE '%{story}%' OR title LIKE '%{story}%'", // Искать в статьях и комментариях
										  '3' => "title LIKE '%{story}%'", // Искать только в заголовках статей
										  '5' => "id LIKE '{story}%'", // Искать по номеру статьи
										  '6' => PREFIX . "_static.template LIKE '%{story}%'" ); // Искать только в статических страницах
				
				foreach ( $titleonly_where as $name => $value ) {
					$value2 = '';
					foreach ( $storywords as $words ) {
						$words = preg_replace( "#\s+#i", '%', $words );
						$value2 .= str_replace( "{story}", $words, $value );
						$value2 .= " OR ";
					}
					$value2 = preg_replace( "# OR $#i", '', $value2 );
					$titleonly_where[$name] = $value2;
				}
			}
			
			// Поиск по статьям
			if( in_array( $titleonly, array (0, 2, 3, 5 ) ) ) {
				$where_posts = "WHERE " . PREFIX . "_post.approve" . $this_date;
				if( ! empty( $where_category ) ) $where_posts .= " AND " . $where_category;
				if( ! empty( $story ) ) $where_posts .= " AND (" . $titleonly_where[$titleonly] . ")";
				if( ! empty( $autor_posts ) ) $where_posts .= " AND " . $autor_posts;
				$sdate = PREFIX . "_post.date";
				if( $searchdate != '0' ) {
					if( $beforeafter == 'before' ) $where_date = $sdate . " < '" . $qdate . "'";
					else $where_date = $sdate . " between '" . $qdate . "' and '" . $thistime . "'";
					$where_posts .= " AND " . $where_date;
				}
				if( ! empty( $where_reply ) ) $where_posts .= " AND " . $where_reply;
				$where = $where_posts;
				$posts_fields = "SELECT id, autor, " . PREFIX . "_post.date AS newsdate, " . PREFIX . "_post.date AS date, short_story AS story, " . PREFIX . "_post.xfields AS xfields, title, descr, keywords, category, alt_name, comm_num AS comm_in_news, allow_comm, rating, news_read, flag, editdate, editor, reason, view_edit, tags, '' AS output_comms";
				$posts_from = "FROM " . PREFIX . "_post";
				$sql_fields = $posts_fields;
				$sql_find = "$sql_fields $posts_from $where";
				$posts_count = "SELECT COUNT(*) AS count $posts_from $where";
				$sql_count = $posts_count;
			}
			// Поиск по комментариям
			if( $titleonly == 1 or $titleonly == 2 ) {
				$where_comms = "WHERE " . PREFIX . "_post.approve" . $this_date;
				if( ! empty( $where_category ) ) $where_comms .= " AND " . $where_category;
				if( ! empty( $story ) ) $where_comms .= " AND (" . $titleonly_where['1'] . ")";
				if( ! empty( $autor_comms ) ) $where_comms .= " AND " . $autor_comms;
				$sdate = PREFIX . "_comments.date";
				if( $searchdate != '0' ) {
					if( $beforeafter == 'before' ) $where_date = $sdate . " < '" . $qdate . "'";
					else $where_date = $sdate . " between '" . $qdate . "' and '" . $thistime . "'";
					$where_comms .= " AND " . $where_date;
				}
				if( ! empty( $where_reply ) ) $where_comms .= " AND " . $where_reply;
				$where = $where_comms;
				$comms_fields = "SELECT  " . PREFIX . "_comments.id AS coms_id, post_id AS id, " . PREFIX . "_comments.date, " . PREFIX . "_comments.autor AS autor, " . PREFIX . "_comments.email AS gast_email, " . PREFIX . "_comments.text AS story, ip, is_register, name, " . USERPREFIX . "_users.email, news_num, " . USERPREFIX . "_users.comm_num, reg_date, banned, signature, foto, fullname, land, icq, " . PREFIX . "_post.date AS newsdate, " . PREFIX . "_post.title, " . PREFIX . "_post.category, " . PREFIX . "_post.alt_name, " . PREFIX . "_post.comm_num AS comm_in_news, " . PREFIX . "_post.allow_comm, " . PREFIX . "_post.rating, " . PREFIX . "_post.rating, '1' AS output_comms, " . PREFIX . "_post.flag";
				$comms_from = "FROM " . PREFIX . "_comments LEFT JOIN " . PREFIX . "_post ON " . PREFIX . "_comments.post_id=" . PREFIX . "_post.id LEFT JOIN " . USERPREFIX . "_users ON " . PREFIX . "_comments.user_id=" . USERPREFIX . "_users.user_id";
				$sql_fields = $comms_fields;
				$sql_find = "$sql_fields $comms_from $where";
				$comms_count = "SELECT COUNT(*) AS count $comms_from $where";
				$sql_count = $comms_count;
			}
			
			$order_by = $sortby . " " . $resorder;
			
			// Поиск в статических страницах
			if( $titleonly == 6 ) {
				$sql_from = "FROM " . PREFIX . "_static";
				$sql_fields = "SELECT id, name AS static_name, descr AS title, template AS story, allow_template, grouplevel";
				$where = "WHERE " . $titleonly_where[$titleonly];
				$sql_find = "$sql_fields $sql_from $where";
				$sql_count = "SELECT COUNT(*) AS count $sql_from $where";
				$order_by = "id";
			}
			
			// ------ Запрос к базе
			

			$result_count = $db->super_query( $sql_count, true );
			$count_result = $result_count[0]['count'] + $result_count[1]['count'];
			
			$min_search = (@ceil( $count_result / $config_search_numbers ) - 1) * $config_search_numbers;
			
			if( $min_search < 0 ) $min_search = 0;
			if( $search_start > $min_search ) {
				$search_start = $min_search;
			}
			$from_num = $search_start + 1;
			
			$sql_request = "$sql_find ORDER BY $order_by LIMIT $search_start,$config_search_numbers";
			
			$sql_result = $db->query( $sql_request );
			$found_result = $db->num_rows( $sql_result );
			
			// Не найдено
			if( ! $found_result ) {
				msgbox( $lang['all_info'], $lang[search_err_2] );
				$tpl->set( '{searchmsg}', '' );
				$tpl->set( '[searchmsg]', "" );
				$tpl->set( '[/searchmsg]', "" );
				$tpl->compile( 'content' );
			} else {
				$to_num = $search_start + $found_result;
				
				// Вывод информации о количестве найденных результатов
				$searchmsg = "$lang[search_ok] " . $count_result . " $lang[search_ok_1] ($lang[search_ok_2] " . $from_num . " - " . $to_num . ") :";
				$tpl->set( '{searchmsg}', $searchmsg );
				$tpl->set( '[searchmsg]', "" );
				$tpl->set( '[/searchmsg]', "" );
				$tpl->compile( 'content' );
				
				$tpl->load_template( 'searchresult.tpl' );
				$xfields = xfieldsload();
				
				function hilites($search, $txt) {
					
					$r = preg_split( '((>)|(<))', $txt, - 1, PREG_SPLIT_DELIM_CAPTURE );
					
					for($i = 0; $i < count( $r ); $i ++) {
						if( $r[$i] == "<" ) {
							$i ++;
							continue;
						}
						$r[$i] = preg_replace( "#($search)#i", "<span style='background-color:yellow;'><font color='red'>\\1</font></span>", $r[$i] );
					}
					return join( "", $r );
				}
				
				// Вывод текста статьи или комментария во всплывающей подсказке при выводе только заголовков
				function create_description($txt) {
					$fastquotes = array ("\x27", "\x22", "\x60", "\t", "\n", "\r" );
					$quotes = array ('"', "'" );
					$maxchr = 80;
					$txt = preg_replace( "/\[hide\](.*?)\[\/hide\]/ims", "", $txt );
					$txt = stripslashes( $txt );
					$txt = trim( strip_tags( $txt ) );
					$txt = str_replace( $fastquotes, ' ', $txt );
					$txt = str_replace( $quotes, '', $txt );
					$txt = preg_replace( "#\s+#i", ' ', $txt );
					$txt = substr( $txt, 0, 300 );
					$txt = wordwrap( $txt, $maxchr, "  " );
					return $txt;
				}
				
				// Вывод результатов поиска
				$search_id = $search_start;
				while ( $row = $db->get_row( $sql_result ) ) {
					
					// Порядковый номер результата поиска
					$search_id ++;
					
					$attachments[] = $row['id'];
					$row['newsdate'] = strtotime( $row['newsdate'] );
					$row['date'] = strtotime( $row['date'] );
					$row['story'] = stripslashes( $row['story'] );
					
					$arr = explode( "%", $story );
					
					foreach ( $arr as $word ) {
						if( strlen( trim( $word ) ) >= $config['search_length_min'] ) {
							$row['story'] = hilites( $word, $row['story'] );
						}
						;
					}
					
					if( $titleonly == 6 ) {
						// Результаты поиска в статических страницах
						$row['grouplevel'] = explode( ',', $row['grouplevel'] );
						if( $row['grouplevel'][0] != "all" and ! in_array( $member_id['user_group'], $row['grouplevel'] ) ) {
							$tpl->result['content'] .= $lang['static_denied'];
						} else {
							
							$template = stripslashes( $row['template'] );
							$title = stripslashes( strip_tags( $row['title'] ) );
							
							if( $row['allow_template'] ) {
								$tpl->load_template( 'static.tpl' );
								if( $config['allow_alt_url'] == "yes" ) $static_descr = "<a title=\"" . $title . "\" href=\"" . $config['http_home_url'] . $row['static_name'] . ".html\" >" . $title . "</a>";
								else $static_descr = "<a title=\"" . $title . "\" href=\"$PHP_SELF?do=static&page" . $row['static_name'] . "\" >" . $title . "</a>";
								$tpl->set( '{description}', $static_descr );
								$tpl->set( '{static}', $row['story'] );
								$tpl->set( '{pages}', '' );
								
								if( $config['allow_alt_url'] == "yes" ) $print_link = $config['http_home_url'] . "print:" . $row['static_name'] . ".html";
								else $print_link = $config['http_home_url'] . "engine/print.php?do=static&amp;page=" . $row['static_name'];
								
								$tpl->set( '[print-link]', "<a href=\"" . $print_link . "\">" );
								$tpl->set( '[/print-link]', "</a>" );
								
								$tpl->compile( 'content' );
								$tpl->clear();
							} else
								$tpl->result['content'] .= $row['story'];
							
							if( $config['files_allow'] == "yes" ) {
								if( strpos( $tpl->result['content'], "[attachment=" ) !== false ) {
									$tpl->result['content'] = show_attach( $tpl->result['content'], $attachments, true );
								}
							}
						
						}
					} else {
						// Результаты поиска в статьях и комментариях
						

						$tpl->set( '{result-date}', langdate( $config['timestamp_active'], $row['date'] ) );
						
						$row_title = stripslashes( $row['title'] );
						$tpl->set( '{result-title}', $row_title );
						
						if( $config['allow_alt_url'] == "yes" ) $tpl->set( '{result-author}', "<a href=\"" . $config['http_home_url'] . "user/" . urlencode( $row['autor'] ) . "/\">" . $row['autor'] . "</a>" );
						else $tpl->set( '{result-author}', "<a href=\"$PHP_SELF?subaction=userinfo&amp;user=" . urlencode( $row['autor'] ) . "\">" . $row['autor'] . "</a>" );
						
						$tpl->set( '{result-comments}', $row['comm_in_news'] );
						$my_news_id = "<a title=\"" . $row_title . "\" href=\"$PHP_SELF?newsid=" . $row['id'] . "\">№ " . $row['id'] . "</a>";
						$tpl->set( '{news-id}', $my_news_id );
						
						if( ! $row['category'] ) {
							$my_cat = "---";
							$my_cat_link = "---";
						} else {
							
							$my_cat = array ();
							$my_cat_link = array ();
							$cat_list = explode( ',', $row['category'] );
							
							if( count( $cat_list ) == 1 ) {
								
								$my_cat[] = $cat_info[$cat_list[0]]['name'];
								
								$my_cat_link = get_categories( $cat_list[0] );
							
							} else {
								
								foreach ( $cat_list as $element ) {
									if( $element ) {
										$my_cat[] = $cat_info[$element]['name'];
										if( $config['ajax'] ) $go_page = "onclick=\"DlePage('do=cat&category={$cat_info[$element]['alt_name']}'); return false;\" ";
										else $go_page = "";
										if( $config['allow_alt_url'] == "yes" ) $my_cat_link[] = "<a {$go_page}href=\"" . $config['http_home_url'] . get_url( $element ) . "/\">{$cat_info[$element]['name']}</a>";
										else $my_cat_link[] = "<a {$go_page}href=\"$PHP_SELF?do=cat&category={$cat_info[$element]['alt_name']}\">{$cat_info[$element]['name']}</a>";
									}
								}
								
								$my_cat_link = stripslashes( implode( ', ', $my_cat_link ) );
							}
							
							$my_cat = stripslashes( implode( ', ', $my_cat ) );
						}
						
						$row['category'] = intval( $row['category'] );
						
						if( $row['view_edit'] and $row['editdate'] ) {
							
							if( date( Ymd, $row['editdate'] ) == date( Ymd, $_TIME ) ) {
								
								$tpl->set( '{edit-date}', $lang['time_heute'] . langdate( ", H:i", $row['editdate'] ) );
							
							} elseif( date( Ymd, $row['editdate'] ) == date( Ymd, ($_TIME - 86400) ) ) {
								
								$tpl->set( '{edit-date}', $lang['time_gestern'] . langdate( ", H:i", $row['editdate'] ) );
							
							} else {
								
								$tpl->set( '{edit-date}', langdate( $config['timestamp_active'], $row['editdate'] ) );
							
							}
							
							$tpl->set( '{editor}', $row['editor'] );
							$tpl->set( '{edit-reason}', $row['reason'] );
							
							if( $row['reason'] ) {
								
								$tpl->set( '[edit-reason]', "" );
								$tpl->set( '[/edit-reason]', "" );
							
							} else
								$tpl->set_block( "'\\[edit-reason\\](.*?)\\[/edit-reason\\]'si", "" );
							
							$tpl->set( '[edit-date]', "" );
							$tpl->set( '[/edit-date]', "" );
						
						} else {
							
							$tpl->set( '{edit-date}', "" );
							$tpl->set( '{editor}', "" );
							$tpl->set( '{edit-reason}', "" );
							$tpl->set_block( "'\\[edit-date\\](.*?)\\[/edit-date\\]'si", "" );
							$tpl->set_block( "'\\[edit-reason\\](.*?)\\[/edit-reason\\]'si", "" );
						}
						
						if( $config['allow_tags'] and $row['tags'] ) {
							
							$tpl->set( '[tags]', "" );
							$tpl->set( '[/tags]', "" );
							
							$tags = array ();
							
							$row['tags'] = explode( ",", $row['tags'] );
							
							foreach ( $row['tags'] as $value ) {
								
								$value = trim( $value );
								
								if( $config['allow_alt_url'] == "yes" ) $tags[] = "<a href=\"" . $config['http_home_url'] . "tags/" . urlencode( $value ) . "/\">" . $value . "</a>";
								else $tags[] = "<a href=\"$PHP_SELF?do=tags&amp;tag=" . urlencode( $value ) . "\">" . $value . "</a>";
							
							}
							
							$tpl->set( '{tags}', implode( ", ", $tags ) );
						
						} else {
							
							$tpl->set_block( "'\\[tags\\](.*?)\\[/tags\\]'si", "" );
							$tpl->set( '{tags}', "" );
						
						}
						
						$tpl->set( '{link-category}', $my_cat_link );
						$tpl->set( '{views}', $row['news_read'] );
						
						if( $row['output_comms'] == '1' ) {
							
							// Обработка и вывод комментариев
							

							if( ! $row['is_register'] ) {
								if( $row['gast_email'] != "" ) {
									if( preg_match( "/^[\.A-z0-9_\-]+[@][A-z0-9_\-]+([.][A-z0-9_\-]+)+[A-z]{1,4}$/", $row['gast_email'] ) ) {
										$url_target = "";
										$mail_or_url = "mailto:";
									} else {
										$url_target = "target=\"_blank\"";
										$mail_or_url = "";
										if( substr( $row[email], 0, 3 ) == "www" ) {
											$mail_or_url = "http://";
										}
									}
									
									if( $mail_or_url == "mailto:" ) {
										$email = explode( "@", stripslashes( $row['gast_email'] ), 2 );
										$tpl->set( '{result-author}', "<script>var em0 = '$email[0]'; document.write('<a href=\"mailto:' + em0 + '@$email[1]\">" . htmlspecialchars( stripslashes( $row['autor'] ), ENT_QUOTES ) . "</a>');</script>" );
									} else {
										$tpl->set( '{result-author}', "<a $url_target href=\"$mail_or_url" . stripslashes( $row[gast_email] ) . "\">" . stripslashes( $row['autor'] ) . "</a>" );
									}
								
								} else {
									$tpl->set( '{result-author}', $row['autor'] );
								}
							} else {
								if( $config['allow_alt_url'] == "yes" ) $tpl->set( '{result-author}', "<a href=\"" . $config['http_home_url'] . "user/" . urlencode( $row['name'] ) . "/\">" . stripslashes( $row['autor'] ) . "</a>" );
								else $tpl->set( '{result-author}', "<a href=\"$PHP_SELF?subaction=userinfo&amp;user=" . urlencode( $row['name'] ) . "\">" . stripslashes( $row['autor'] ) . "</a>" );
							}
							
							if( $is_logged and $member_id['user_group'] == '1' ) $tpl->set( '{ip}', "IP: <a onClick=\"return dropdownmenu(this, event, IPMenu('" . $row['ip'] . "', '" . $lang['ip_info'] . "', '" . $lang['ip_tools'] . "', '" . $lang['ip_ban'] . "'), '190px')\" onMouseout=\"delayhidemenu()\" href=\"http://www.nic.ru/whois/?ip={$row['ip']}\" target=\"_blank\">{$row['ip']}</a>" );
							else $tpl->set( '{ip}', '' );
							
							if( $is_logged and (($member_id['name'] == $row['name'] and $row['is_register'] and $user_group[$member_id['user_group']]['allow_editc']) or $user_group[$member_id['user_group']]['edit_allc']) ) {
								$tpl->set( '[com-edit]', "<a onClick=\"return dropdownmenu(this, event, MenuCommBuild('" . $row['coms_id'] . "'), '170px')\" onMouseout=\"delayhidemenu()\" href=\"" . $config['http_home_url'] . "?do=comments&action=comm_edit&id=" . $row['coms_id'] . "\">" );
								$tpl->set( '[/com-edit]', "</a>" );
								$allow_comments_ajax = true;
							} else
								$tpl->set_block( "'\\[com-edit\\](.*?)\\[/com-edit\\]'si", "" );
							
							if( $is_logged and (($member_id['name'] == $row['name'] and $row['is_register'] and $user_group[$member_id['user_group']]['allow_delc']) or $member_id['user_group'] == '1' or $user_group[$member_id['user_group']]['del_allc']) ) {
								$tpl->set( '[com-del]', "<a href=\"javascript:confirmDelete('" . $config['http_home_url'] . "index.php?do=comments&action=comm_del&id=" . $row['coms_id'] . "&amp;dle_allow_hash=" . $dle_login_hash . "')\">" );
								$tpl->set( '[/com-del]', "</a>" );
							} else
								$tpl->set_block( "'\\[com-del\\](.*?)\\[/com-del\\]'si", "" );
							
							$tpl->set_block( "'\\[fast\\](.*?)\\[/fast\\]'si", "" );
							
							$tpl->set( '{mail}', $row['email'] );
							$tpl->set( '{comment-id}', $row_count['count'] - $cstart - $s + 1 );
							
							if( $row['banned'] == 'yes' or $row['name'] == '' or ! $row['is_register'] ) {
								$tpl->set( '{foto}', "{THEME}/images/noavatar.png" );
							} else {
								if( $row['foto'] ) $tpl->set( '{foto}', $config['http_home_url'] . "uploads/fotos/" . $row['foto'] );
								else $tpl->set( '{foto}', "{THEME}/images/noavatar.png" );
							}
							
							if( $row['is_register'] and $row['icq'] ) $tpl->set( '{icq}', "ICQ: " . stripslashes( $row['icq'] ) );
							else $tpl->set( '{icq}', '' );
							
							if( $row['is_register'] ) $tpl->set( '{registration}', langdate( "d.m.Y", $row['reg_date'] ) );
							else $tpl->set( '{registration}', '--' );
							
							if( $row['is_register'] and $row['news_num'] ) $tpl->set( '{news_num}', $row['news_num'] );
							else $tpl->set( '{news_num}', '0' );
							
							if( $row['is_register'] and $row['comm_num'] ) $tpl->set( '{comm_num}', $row['comm_num'] );
							else $tpl->set( '{comm_num}', '0' );
							
							$tpl->set_block( "'\\[signature\\](.*?)\\[/signature\\]'si", "" );
							$tpl->set( '{result-text}', "<div id='comm-id-" . $row['coms_id'] . "'>" . $row['story'] . "</div>" );
						
						} else {
							// Обработка дополнительных полей
							$xfieldsdata = xfieldsdataload( $row['xfields'] );
							
							foreach ( $xfields as $value ) {
								$preg_safe_name = preg_quote( $value[0], "'" );
								
								//    	  if ($value[5] != 0) {
								if( empty( $xfieldsdata[$value[0]] ) ) {
									$tpl->copy_template = preg_replace( "'\\[xfgiven_{$preg_safe_name}\\](.*?)\\[/xfgiven_{$preg_safe_name}\\]'is", "", $tpl->copy_template );
								} else {
									$tpl->copy_template = preg_replace( "'\\[xfgiven_{$preg_safe_name}\\](.*?)\\[/xfgiven_{$preg_safe_name}\\]'is", "\\1", $tpl->copy_template );
								}
								//    	  }
								$tpl->copy_template = preg_replace( "'\\[xfvalue_{$preg_safe_name}\\]'i", stripslashes( $xfieldsdata[$value[0]] ), $tpl->copy_template );
							}
							// Обработка дополнительных полей
							

							if( $is_logged and (($member_id['name'] == $row['autor'] and $user_group[$member_id['user_group']]['allow_edit']) or $user_group[$member_id['user_group']]['allow_all_edit']) ) {
								$tpl->set( '[edit]', "<a onClick=\"return dropdownmenu(this, event, MenuNewsBuild('" . $row['id'] . "', 'short'), '170px')\" href=\"" . $config['http_home_url'] . $config['admin_path'] . "?mod=editnews&action=editnews&id=" . $row['id'] . "\" target=\"_blank\">" );
								$tpl->set( '[/edit]', "</a>" );
								$allow_comments_ajax = true;
							} else {
								$tpl->set_block( "'\\[edit\\](.*?)\\[/edit\\]'si", "" );
							}

							if ($smartphone_detected AND !$config['allow_smart_images']) {
				
								$row['story'] = preg_replace( "#<!--TBegin-->(.+?)<!--TEnd-->#is", "", $row['story'] );
								$row['story'] = preg_replace( "#<img(.+?)>#is", "", $row['story'] );
				
							}
							
							$tpl->set( '{result-text}', "<div id='news-id-" . $row['id'] . "'>" . $row['story'] . "</div>" );
						
						}
						
						$tpl->set( '{search-id}', $search_id );
						
						if( $user_group[$member_id['user_group']]['allow_hide'] ) $tpl->set_block( "'\[hide\](.*?)\[/hide\]'si", "\\1" );
						else $tpl->set_block( "'\\[hide\\](.*?)\\[/hide\\]'si", "" );
						
						if( $showposts == 0 ) {
							// Показать короткую новость
							$tpl->set_block( "'\\[shortresult\\].*?\\[/shortresult\\]'si", "" );
							$tpl->set( '[fullresult]', "" );
							$tpl->set( '[/fullresult]', "" );
							$alt_text = $row_title;
						} else {
							// Показать только заголовок
							$tpl->set_block( "'\\[fullresult\\].*?\\[/fullresult\\]'si", "" );
							$tpl->set( '[shortresult]', "" );
							$tpl->set( '[/shortresult]', "" );
							$alt_text = create_description( $row['story'] );
						}
						
						if( $config['allow_alt_url'] == "yes" ) {
							
							if( $row['flag'] and $config['seo_type'] ) {
								
								if( $row['category'] and $config['seo_type'] == 2 ) {
									
									$full_link = $config['http_home_url'] . get_url( $row['category'] ) . "/" . $row['id'] . "-" . $row['alt_name'] . ".html";
								
								} else {
									
									$full_link = $config['http_home_url'] . $row['id'] . "-" . $row['alt_name'] . ".html";
								
								}
							
							} else {
								
								$full_link = $config['http_home_url'] . date( 'Y/m/d/', $row['newsdate'] ) . $row['alt_name'] . ".html";
							}
						
						} else {
							
							$full_link = $config['http_home_url'] . "index.php?newsid=" . $row['id'];
						
						}
						
						$tpl->set( '[result-link]', "<a href=\"" . $full_link . "\" >" );
						$tpl->set( '[/result-link]', "</a>" );
						
						if( $row['output_comms'] == '1' ) {
							// Для вывода комментариев
							$tpl->set_block( "'\\[searchposts\\].*?\\[/searchposts\\]'si", "" );
							$tpl->set( '[searchcomments]', "" );
							$tpl->set( '[/searchcomments]', "" );
						} else {
							// Для вывода статей
							$tpl->set_block( "'\\[searchcomments\\].*?\\[/searchcomments\\]'si", "" );
							$tpl->set( '[searchposts]', "" );
							$tpl->set( '[/searchposts]', "" );
						}
						
						$tpl->compile( 'content' );
						
						if( $config['files_allow'] == "yes" ) {
							if( strpos( $tpl->result['content'], "[attachment=" ) !== false ) {
								$tpl->result['content'] = show_attach( $tpl->result['content'], $attachments );
							}
						}
					} // Результаты поиска в статьях и комментариях
				} // while
				

				$tpl->clear();
				$db->free( $sql_result );
			}
		}
	}
	
	$tpl->clear();
	
	//####################################################################################################################
	//         Навигация по новостям
	//####################################################################################################################
	if( $found_result > 0 ) {
		$tpl->load_template( 'navigation.tpl' );
		
		//----------------------------------
		// Previous link
		//----------------------------------
		if( isset( $search_start ) and $search_start != "" and $search_start > 0 ) {
			$prev = $search_start / $config_search_numbers;
			$prev_page = "<a name=\"prevlink\" id=\"prevlink\" onClick=\"javascript:list_submit($prev); return(false)\" href=#>";
			$tpl->set_block( "'\[prev-link\](.*?)\[/prev-link\]'si", $prev_page . "\\1</a>" );
		
		} else {
			$tpl->set_block( "'\[prev-link\](.*?)\[/prev-link\]'si", "<span>\\1</span>" );
			$no_prev = TRUE;
		}
		
		//----------------------------------
		// Pages
		//----------------------------------
		if( $config_search_numbers ) {
			$pages_count = @ceil( $count_result / $config_search_numbers );
			$pages_start_from = 0;
			$pages = "";
			$pages_per_side = ($config['pages_per_section'] - 1) / 2;
			$pages_to_display = ($config['pages_per_section'] * 3) + 1;
			if( $pages_count > $pages_to_display ) {
				for($j = 1; $j <= $config['pages_per_section']; $j ++) {
					if( $pages_start_from != $search_start ) {
						$pages .= "<a onClick=\"javascript:list_submit($j); return(false)\" href=#>$j</a> ";
					} else {
						$pages .= " <span>$j</span> ";
					}
					$pages_start_from += $config_search_numbers;
				}
				if( ((($search_start / $config_search_numbers) + 1) > ($pages_per_side + 1)) && ((($search_start / $config_search_numbers) + 1) < ($pages_count - $pages_per_side)) ) {
					$pages .= ((($search_start / $config_search_numbers) + 1) > ($config['pages_per_section'] + $pages_per_side + 1)) ? '... ' : ' ';
					$page_min = ((($search_start / $config_search_numbers) + 1) > ($config['pages_per_section'] + $pages_per_side)) ? (($search_start / $config_search_numbers) - $pages_per_side + 1) : ($config['pages_per_section'] + 1);
					$page_max = ((($search_start / $config_search_numbers) + 1) < ($pages_count - ($config['pages_per_section'] + $pages_per_side - 1))) ? (($search_start / $config_search_numbers) + $pages_per_side + 1) : ($pages_count - $config['pages_per_section']);
					
					$pages_start_from = ($page_min - 1) * $config_search_numbers;
					
					for($j = $page_min; $j < $page_max + 1; $j ++) {
						if( $pages_start_from != $search_start ) {
							$pages .= "<a onClick=\"javascript:list_submit($j); return(false)\" href=#>$j</a> ";
						} else {
							$pages .= " <span>$j</span> ";
						}
						$pages_start_from += $config_search_numbers;
					}
					$pages .= ((($search_start / $config_search_numbers) + 1) < $pages_count - ($config['pages_per_section'] + $pages_per_side)) ? '... ' : ' ';
				
				} else {
					$pages .= '... ';
				}
				
				$pages_start_from = ($pages_count - $config['pages_per_section']) * $config_search_numbers;
				for($j = ($pages_count - ($config['pages_per_section'] - 1)); $j <= $pages_count; $j ++) {
					if( $pages_start_from != $search_start ) {
						$pages .= "<a onClick=\"javascript:list_submit($j); return(false)\" href=#>$j</a> ";
					} else {
						$pages .= " <span>$j</span> ";
					}
					$pages_start_from += $config_search_numbers;
				}
			
			} else {
				for($j = 1; $j <= $pages_count; $j ++) {
					if( $pages_start_from != $search_start ) {
						$pages .= "<a onClick=\"javascript:list_submit($j); return(false)\" href=#>$j</a> ";
					} else {
						$pages .= " <span>$j</span> ";
					}
					$pages_start_from += $config_search_numbers;
				}
			}
			$tpl->set( '{pages}', $pages );
		}
		
		//----------------------------------
		// Next link
		//----------------------------------
		if( $config_search_numbers < $count_result and $to_num < $count_result ) {
			$next_page = $to_num / $config_search_numbers + 1;
			$next = "<a name=\"nextlink\" id=\"nextlink\" onClick=\"javascript:list_submit($next_page); return(false)\" href=#>";
			$tpl->set_block( "'\[next-link\](.*?)\[/next-link\]'si", $next . "\\1</a>" );
		} else {
			$tpl->set_block( "'\[next-link\](.*?)\[/next-link\]'si", "<span>\\1</span>" );
			$no_next = TRUE;
		}
		
		if( ! $no_prev or ! $no_next ) {
			$tpl->compile( 'content' );
		}
		
		$tpl->clear();
	}
}
?>