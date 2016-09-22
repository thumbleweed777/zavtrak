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
 Файл: userfields.php
-----------------------------------------------------
 Назначение: дополнительные поля профиля
=====================================================
*/
if(!defined('DATALIFEENGINE'))
{
  die("Hacking attempt!");
}
if (isset ($xfieldssubactionadd))
if ($xfieldssubactionadd == "add") {
  $xfieldssubaction = $xfieldssubactionadd;
}

if (!isset($xf_inited)) $xf_inited = "";

if ($xf_inited !== true) { // Prevent "Cannot redeclare" error

function profilesave($data) {
	global $lang, $dle_login_hash;

	if ($_REQUEST['user_hash'] == "" OR $_REQUEST['user_hash'] != $dle_login_hash) {

		  die("Hacking attempt! User not found");

	}

    $data = array_values($data);
    foreach ($data as $index => $value) {
      $value = array_values($value);
      foreach ($value as $index2 => $value2) {
        $value2 = stripslashes($value2);
        $value2 = str_replace("|", "&#124;", $value2);
        $value2 = str_replace("\r\n", "__NEWL__", $value2);
        $filecontents .= $value2 . ($index2 < count($value) - 1 ? "|" : "");
      }
      $filecontents .= ($index < count($data) - 1 ? "\r\n" : "");
    }
  
    $filehandle = fopen(ENGINE_DIR.'/data/xprofile.txt', "w+");
    if (!$filehandle)
    msg("error", $lang['xfield_error'], "$lang[xfield_err_1] \"".ENGINE_DIR."/data/xprofile.txt\", $lang[xfield_err_1]");
    fwrite($filehandle, $filecontents);
    fclose($filehandle);
    header("Location: http://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] .
        "?mod=userfields&xfieldsaction=configure");
    exit;
  }

function profileload() {
  global $lang;
  $path = ENGINE_DIR.'/data/xprofile.txt';
  $filecontents = file($path);

    if (!is_array($filecontents))
    msg("error", $lang['xfield_error'], "$lang[xfield_err_3] \"engine/data/xprofile.txt\". $lang[xfield_err_4]");
  
    foreach ($filecontents as $name => $value) {
      $filecontents[$name] = explode("|", trim($value));
      foreach ($filecontents[$name] as $name2 => $value2) {
        $value2 = str_replace("&#124;", "|", $value2); 
        $value2 = str_replace("__NEWL__", "\r\n", $value2);
        $filecontents[$name][$name2] = $value2;
      }
    }
    return $filecontents;
  }

function array_move(&$array, $index1, $dist) {
    $index2 = $index1 + $dist;
    if ($index1 < 0 or
        $index1 > count($array) - 1 or
        $index2 < 0 or
        $index2 > count($array) - 1) {
      return false;
    }
    $value1 = $array[$index1];
  
    $array[$index1] = $array[$index2];
    $array[$index2] = $value1;
  
    return true;
  }

  $xf_inited = true;
}

$xfields = profileload();

switch ($xfieldsaction) {
  case "configure":
    switch ($xfieldssubaction) {
      case "delete":
        if (!isset($xfieldsindex)) {
          msg("error", $lang['xfield_error'], $lang['xfield_err_5'],"javascript:history.go(-1)");
        }
        msg("options", "info", "$lang[xfield_err_6]<br /><br /><a href=\"$PHP_SELF?mod=userfields&amp;xfieldsaction=configure&amp;xfieldsindex=$xfieldsindex&amp;xfieldssubaction=delete2&user_hash={$dle_login_hash}\">[$lang[opt_sys_yes]]</a>&nbsp;&nbsp;<a href=\"$PHP_SELF?mod=userfields&amp;xfieldsaction=configure\">[$lang[opt_sys_no]]</a>");
        break;
      case "delete2":
        if (!isset($xfieldsindex)) {
          msg("error", $lang['xfield_error'], $lang['xfield_err_5'],"javascript:history.go(-1)");
        }
        unset($xfields[$xfieldsindex]);
        @profilesave($xfields);
        break;
      case "moveup":
        if (!isset($xfieldsindex)) {
          msg("error", $lang['xfield_error'], $lang['xfield_err_7'],"javascript:history.go(-1)");
        }
        array_move($xfields, $xfieldsindex, -1);
        @profilesave($xfields);
        break;
      case "movedown":
        if (!isset($xfieldsindex)) {
          msg("error", $lang['xfield_error'], $lang['xfield_err_7'],"javascript:history.go(-1)");
        }
        array_move($xfields, $xfieldsindex, +1);
        @profilesave($xfields);
        break;
      case "add":
        $xfieldsindex = count($xfields);
        // Fall trough to edit
      case "edit":
        if (!isset($xfieldsindex)) {
          msg("error", $lang['xfield_error'], $lang['xfield_err_8'],"javascript:history.go(-1)");
        }
    
        if (!$editedxfield) {
          $editedxfield = $xfields[$xfieldsindex];
        } elseif (strlen(trim($editedxfield[0])) > 0 and
            strlen(trim($editedxfield[1])) > 0) {
          foreach ($xfields as $name => $value) {
            if ($name != $xfieldsindex and
                $value[0] == $editedxfield[0]) {
              msg("error", $lang['xfield_error'], $lang['xfield_err_9'],"javascript:history.go(-1)");
            }
          }
          $editedxfield[0] = totranslit(trim($editedxfield[0]));
          $editedxfield[1] = htmlspecialchars(trim($editedxfield[1]));
          $editedxfield[2] = intval($editedxfield[2]);
          $editedxfield[4] = intval($editedxfield[4]);
          $editedxfield[5] = intval($editedxfield[5]);

          ksort($editedxfield);
          
          $xfields[$xfieldsindex] = $editedxfield;
          ksort($xfields);
          @profilesave($xfields);
          break;
        } else {
          msg("error", $lang['xfield_error'], $lang['xfield_err_11'],"javascript:history.go(-1)");
        }
        echoheader("options", (($xfieldssubaction == "add") ? $lang['xfield_addh'] : $lang['xfield_edith']) . " " . $lang['xfield_fih']);
        $checked = ($editedxfield[5] ? " checked" : "");

?>
    <form action="<? echo $_SERVER['PHP_SELF']; ?>" method="post" name="xfieldsform">
      <input type="hidden" name="mod" value="userfields">
      <input type="hidden" name="user_hash" value="<? echo $dle_login_hash; ?>">
      <input type="hidden" name="xfieldsaction" value="configure">
      <input type="hidden" name="xfieldssubaction" value="edit">
      <input type="hidden" name="xfieldsindex" value="<?=$xfieldsindex?>">

<div style="padding-top:5px;padding-bottom:2px;">
<table width="100%">
    <tr>
        <td width="4"><img src="engine/skins/images/tl_lo.gif" width="4" height="4" border="0"></td>
        <td background="engine/skins/images/tl_oo.gif"><img src="engine/skins/images/tl_oo.gif" width="1" height="4" border="0"></td>
        <td width="6"><img src="engine/skins/images/tl_ro.gif" width="6" height="4" border="0"></td>
    </tr>
    <tr>
        <td background="engine/skins/images/tl_lb.gif"><img src="engine/skins/images/tl_lb.gif" width="4" height="1" border="0"></td>
        <td style="padding:5px;" bgcolor="#FFFFFF">
<table width="100%">
    <tr>
        <td bgcolor="#EFEFEF" height="29" style="padding-left:10px;"><div class="navigation"><?=$lang['xfield_title']?></div></td>
    </tr>
</table>
<div class="unterline"></div>
<table width="100%">
    <tr>
        <td width="260" style="padding:4px;"><?=$lang['xfield_xname']?></td>
        <td><input class=edit style="width: 200px;" type="text" name="editedxfield[0]" value="<? echo $editedxfield[0];?>" />&nbsp;&nbsp;&nbsp;(<?=$lang['xf_lat']?>)</td>
    </tr>
    <tr>
        <td style="padding:4px;"><?=$lang['xfield_xdescr']?></td>
        <td><input  class=edit style="width: 200px;" type="text" name="editedxfield[1]" value="<? echo $editedxfield[1];?>" /></td>
    </tr>
    <tr>
        <td style="padding:4px;"><?=$lang['xfield_xtype']?></td>
        <td><select name="editedxfield[3]" />
          <option value="text"<?=($editedxfield[3] != "text") ? " selected" : ""?>><?=$lang['xfield_xstr']?></option>
          <option value="textarea"<?=($editedxfield[3] == "textarea") ? " selected" : ""?>><?=$lang['xfield_xarea']?></option>
        </select></td>
    </tr>
    <tr>
        <td style="padding:4px;"><?=$lang['xp_reg']?></td>
        <td><input type="radio" name="editedxfield[2]" <?=($editedxfield[2]) ? "checked" : ""?> value="1"> <?=$lang['opt_sys_yes']?> <input type="radio" name="editedxfield[2]" <?=(!$editedxfield[2]) ? "checked" : ""?> value="0"> <?=$lang['opt_sys_no']?> <a href="#" class="hintanchor" onMouseover="showhint('<?=$lang['xp_reg_hint']?>', this, event, '220px')">[?]</a>
		</td>
    </tr>
    <tr>
        <td style="padding:4px;"><?=$lang['xp_edit']?></td>
        <td><input type="radio" name="editedxfield[4]" <?=($editedxfield[4]) ? "checked" : ""?> value="1"> <?=$lang['opt_sys_yes']?> <input type="radio" name="editedxfield[4]" <?=(!$editedxfield[4]) ? "checked" : ""?> value="0"> <?=$lang['opt_sys_no']?> <a href="#" class="hintanchor" onMouseover="showhint('<?=$lang['xp_edit_hint']?>', this, event, '220px')">[?]</a>
		</td>
    </tr>
    <tr>
        <td style="padding:4px;"><?=$lang['xp_privat']?></td>
        <td><input type="radio" name="editedxfield[5]" <?=($editedxfield[5]) ? "checked" : ""?> value="1"> <?=$lang['opt_sys_yes']?> <input type="radio" name="editedxfield[5]" <?=(!$editedxfield[5]) ? "checked" : ""?> value="0"> <?=$lang['opt_sys_no']?> <a href="#" class="hintanchor" onMouseover="showhint('<?=$lang['xp_privat_hint']?>', this, event, '220px')">[?]</a>
		</td>
    </tr>
    <tr>
        <td colspan=2><div class="hr_line"></div></td>
    </tr>
    <tr>
        <td colspan=2 style="padding:4px;"><input type="submit" class="buttons" value=" <?=$lang['user_save']?> "></td>
    </tr>
</table>
</td>
        <td background="engine/skins/images/tl_rb.gif"><img src="engine/skins/images/tl_rb.gif" width="6" height="1" border="0"></td>
    </tr>
    <tr>
        <td><img src="engine/skins/images/tl_lu.gif" width="4" height="6" border="0"></td>
        <td background="engine/skins/images/tl_ub.gif"><img src="engine/skins/images/tl_ub.gif" width="1" height="6" border="0"></td>
        <td><img src="engine/skins/images/tl_ru.gif" width="6" height="6" border="0"></td>
    </tr>
</table>
</div>
    </form>
<?php
        echofooter();
        break;

      default:
        echoheader("options", "Дополнительные поля");
?>
<form action="<? echo $_SERVER["PHP_SELF"]; ?>" method="post" name="xfieldsform">
<input type="hidden" name="mod" value="userfields">
<input type="hidden" name="user_hash" value="<? echo $dle_login_hash; ?>">
<input type="hidden" name="xfieldsaction" value="configure">
<input type="hidden" name="xfieldssubactionadd" value="">
<div style="padding-top:5px;padding-bottom:2px;">
<table width="100%">
    <tr>
        <td width="4"><img src="engine/skins/images/tl_lo.gif" width="4" height="4" border="0"></td>
        <td background="engine/skins/images/tl_oo.gif"><img src="engine/skins/images/tl_oo.gif" width="1" height="4" border="0"></td>
        <td width="6"><img src="engine/skins/images/tl_ro.gif" width="6" height="4" border="0"></td>
    </tr>
    <tr>
        <td background="engine/skins/images/tl_lb.gif"><img src="engine/skins/images/tl_lb.gif" width="4" height="1" border="0"></td>
        <td style="padding:5px;" bgcolor="#FFFFFF">
<table width="100%">
    <tr>
        <td bgcolor="#EFEFEF" height="29" style="padding-left:10px;"><div class="navigation"><?=$lang['xp_xlist']?></div></td>
    </tr>
</table>
<div class="unterline"></div>
<table width="100%">
  <tr>
    <td style="padding:5px;">
      <B><?=$lang['xfield_xname']?></B>
    </td>
    <td>
      <B><?=$lang['xp_descr']?></B>
    </td>
    <td>
      <B><?=$lang['xfield_xtype']?></B>
    </td>
    <td>
      <B><?=$lang['xp_regh']?></B>
    </td>
    <td>
      <B><?=$lang['xp_edith']?></B>
    </td>
    <td>
      <B><?=$lang['xp_privath']?></B>
    </td>
    <td width=10>&nbsp;
    </td>
  </tr>
    <tr>
        <td colspan=7><div class="hr_line"></div></td>
    </tr>
<?php
        if (count($xfields) == 0) {
          echo "<tr><td colspan=\"7\" align=\"center\"><br /><br />$lang[xfield_xnof]</td></tr>";
        } else {
          foreach ($xfields as $name => $value) {
?>
        <tr>
          <td style="padding:2px;">
            <? echo $value[0]; ?>
          </td>
          <td style="padding:2px;">
            <? echo $value[1]; ?>
          </td>
          <td>
            <?=(($value[3] == "text") ? $lang['xfield_xstr'] : "")?>
            <?=(($value[3] == "textarea") ? $lang['xfield_xarea'] : "")?>
          </td>
          <td>
            <?=($value[2] != 0 ? $lang['opt_sys_yes'] : $lang['opt_sys_no'])?>
          </td>
          <td>
            <?=($value[4] != 0 ? $lang['opt_sys_yes'] : $lang['opt_sys_no'])?>
          </td>
          <td>
            <?=($value[5] != 0 ? $lang['opt_sys_yes'] : $lang['opt_sys_no'])?>
          </td>
          <td>
            <input type="radio" name="xfieldsindex" value="<?=$name?>">
          </td>
        </tr><tr><td background="engine/skins/images/mline.gif" height=1 colspan=7></td></tr>
<?php
          }
        }
?>
    <tr>
        <td colspan=7><div class="hr_line"></div></td>
    </tr>
      <tr>
		<td ><a class=main onClick="javascript:Help('xprofile')" href="#"><?=$lang['xfield_xhelp']?></a></td>
        <td colspan="4" class="main" style="text-align: right; padding-top: 10px;">
          <?php if (count($xfields) > 0) { ?>
          <?=$lang['xfield_xact']?>: 
          <select name="xfieldssubaction">
            <option value="edit"><?=$lang['xfield_xedit']?></option>
            <option value="delete"><?=$lang['xfield_xdel']?></option>
            <option value="moveup"><?=$lang['xfield_xo']?></option>
            <option value="movedown"><?=$lang['xfield_xu']?></option>
          </select>
          <input type="submit" class="buttons" value=" <?=$lang['b_start']?> " onclick="document.forms['xfieldsform'].xfieldssubactionadd.value = '';">
          <?php } ?>
          <input type="submit" class="buttons" value=" <?=$lang['b_create']?> " onclick="document.forms['xfieldsform'].xfieldssubactionadd.value = 'add';">
        </td>
      </tr>
</table>
</td>
        <td background="engine/skins/images/tl_rb.gif"><img src="engine/skins/images/tl_rb.gif" width="6" height="1" border="0"></td>
    </tr>
    <tr>
        <td><img src="engine/skins/images/tl_lu.gif" width="4" height="6" border="0"></td>
        <td background="engine/skins/images/tl_ub.gif"><img src="engine/skins/images/tl_ub.gif" width="1" height="6" border="0"></td>
        <td><img src="engine/skins/images/tl_ru.gif" width="6" height="6" border="0"></td>
    </tr>
</table>
</div>
  </form>

<?php
      echofooter();
    }
    break;
case "list":
    $output = "";
    if (!isset($xfieldsid)) $xfieldsid = "";
    $xfieldsdata = xfieldsdataload ($xfieldsid);
    foreach ($xfields as $name => $value) {
      $fieldname = $value[0];

      if (!$xfieldsadd) {
        $fieldvalue = $xfieldsdata[$value[0]];
        $fieldvalue = $parse->decodeBBCodes($fieldvalue, false);
		if ((!$xfieldsadd) AND !intval($value[4]) AND ($is_logged AND $member_id['user_group'] != 1)) continue;
      }

if (intval($value[2]) OR (!$xfieldsadd)) {
     if ($value[3] == "textarea") {      
      $output .= <<<HTML
<tr>
<td>$value[1]:</td>
<td class="xprofile" colspan="2"><textarea name="xfield[$fieldname]" id="xf_$fieldname"{$readonly}>$fieldvalue</textarea></td></tr>
HTML;
      } elseif ($value[3] == "text") {
        $output .= <<<HTML
<tr>
<td>$value[1]:</td>
<td class="xprofile" colspan="2"><input type="text" name="xfield[$fieldname]" id="xfield[$fieldname]" value="$fieldvalue"{$readonly} /></td>
</tr>
HTML;
      }
}

    }
    break;
case "admin":
    $output = "";
    if (!isset($xfieldsid)) $xfieldsid = "";
    $xfieldsdata = xfieldsdataload ($xfieldsid);
    foreach ($xfields as $name => $value) {
      $fieldname = $value[0];

        $fieldvalue = $xfieldsdata[$value[0]];
        $fieldvalue = $parse->decodeBBCodes($fieldvalue, false);


     if ($value[3] == "textarea") {      
      $output .= <<<HTML
<tr>
<td>$value[1]:</td>
<td class="xprofile"><textarea name="xfield[$fieldname]" id="xf_$fieldname"{$readonly}>$fieldvalue</textarea></td></tr>
HTML;
      } elseif ($value[3] == "text") {
        $output .= <<<HTML
<tr>
<td>$value[1]:</td>
<td class="xprofile"><input type="text" name="xfield[$fieldname]" id="xfield[$fieldname]" value="$fieldvalue"{$readonly} />
</tr>
HTML;
      }

    }
    break;
  case "init":
    $postedxfields = $_POST["xfield"];
    $newpostedxfields = array();
    if (!isset($xfieldsid)) $xfieldsid = "";
    $xfieldsdata = xfieldsdataload ($xfieldsid);

    foreach ($xfields as $name => $value) {
      if ((!$value[2] AND $xfieldsadd)) {
        continue;
      }
	if (intval($value[4]) OR $member_id['user_group'] == 1 OR ($value[2] AND $xfieldsadd))
      $newpostedxfields[$value[0]] = substr($postedxfields[$value[0]], 0, 1000);
	else
      $newpostedxfields[$value[0]] = $xfieldsdata[$value[0]];
    }

    $postedxfields = $newpostedxfields;
    break;
  case "init_admin":
    $postedxfields = $_POST["xfield"];
    $newpostedxfields = array();

    foreach ($xfields as $name => $value) {
      $newpostedxfields[$value[0]] = substr($postedxfields[$value[0]], 0, 1000);
	}

    $postedxfields = $newpostedxfields;
    break;
  default:
  if (function_exists('msg'))
    msg("error", $lang['xfield_error'], $lang['xfield_xerr2']);
}
?>