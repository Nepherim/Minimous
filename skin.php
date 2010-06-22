<?php if (!defined('PmWiki')) exit();
/* PmWiki Minimous skin
 *
 * Examples at: http://pmwiki.com/Cookbook/Minimous and http://solidgone.org/Skins/
 * Copyright (c) 2010 David Gilbert
 * This work is licensed under a Creative Commons Attribution-Share Alike 3.0 United States License.
 * Please retain the links in the footer.
 * http://creativecommons.org/licenses/by-sa/3.0/us/
 */
global $FmtPV;
$FmtPV['$SkinName'] = '"Minimous"';
$FmtPV['$SkinVersion'] = '"1.0.0"';

global $PageLogoUrl,$PageLogoUrlHeight,$PageLogoUrlWidth,$HTMLStylesFmt,$SkinTheme,$action,$bi_EntryType,$bi_BlogIt_Enabled;
if (!empty($PageLogoUrl)) {
	dg_SetLogoHeightWidth(15, 0);
	$HTMLStylesFmt['minimous'] .=
		'#headerimg {height:' .$PageLogoUrlHeight .'; background: url(' .$PageLogoUrl .') left no-repeat} '.
		'#headerimg .sitetitle a, #headerimg .sitetag {padding-left: ' .$PageLogoUrlWidth .'} ';
}
#Indent the header elements to match the indented blogit content  TODO: Tie into BlogIt active mechanism
global $bi_BlogItActive;
$bi_BlogItActive = $action=='browse' && isset($bi_EntryType) && $bi_EntryType=='blog';
if ($bi_BlogItActive)  $HTMLStylesFmt['minimous'] .= '#header, #footer {margin-left: 155px;}';

global $SkinWidth,$SkinSidebarWidth,$SkinWidthUnit;
SDV($SkinWidth,900);
SDV($SkinSidebarWidth,175);  #good percentage width is 25
SDV($SkinWidthUnit,'px');  #only use 'px' or '%'
$HTMLStylesFmt['minimous'] .=
	'#page { width: '.$SkinWidth.$SkinWidthUnit.'; }'.
	'#content, #wikitext { width: '.($SkinWidthUnit=='px' ?($SkinWidth-$SkinSidebarWidth-70) :(100-$SkinSidebarWidth-5)) .$SkinWidthUnit.'; }'.
	'#sidebar { width: '.$SkinSidebarWidth .$SkinWidthUnit.'; }';
$SkinColor = dg_SetSkinColor('copper', array('copper','teal','blue','green','purple','orange','yellow','pink'));

# ----------------------------------------
# - Standard Skin Setup
# ----------------------------------------
$FmtPV['$WikiTitle'] = '$GLOBALS["WikiTitle"]';
$FmtPV['$WikiTag'] = '$GLOBALS["WikiTag"]';

# Move any (:noleft:) or SetTmplDisplay('PageLeftFmt', 0); directives to variables for access in jScript.
$FmtPV['$LeftColumn'] = "\$GLOBALS['TmplDisplay']['PageLeftFmt']";
Markup('noleft', 'directives',  '/\\(:noleft:\\)/ei', "SetTmplDisplay('PageLeftFmt',0)");
$FmtPV['$RightColumn'] = "\$GLOBALS['TmplDisplay']['PageRightFmt']";
Markup('noright', 'directives',  '/\\(:noright:\\)/ei', "SetTmplDisplay('PageRightFmt',0)");
$FmtPV['$ActionBar'] = "\$GLOBALS['TmplDisplay']['PageActionFmt']";
Markup('noaction', 'directives',  '/\\(:noaction:\\)/ei', "SetTmplDisplay('PageActionFmt',0)");
$FmtPV['$TabsBar'] = "\$GLOBALS['TmplDisplay']['PageTabsFmt']";
Markup('notabs', 'directives',  '/\\(:notabs:\\)/ei', "SetTmplDisplay('PageTabsFmt',0)");
$FmtPV['$SearchBar'] = "\$GLOBALS['TmplDisplay']['PageSearchFmt']";
Markup('nosearch', 'directives',  '/\\(:nosearch:\\)/ei', "SetTmplDisplay('PageSearchFmt',0)");
$FmtPV['$TitleGroup'] = "\$GLOBALS['TmplDisplay']['PageTitleGroupFmt']";
Markup('notitlegroup', 'directives',  '/\\(:notitlegroup:\\)/ei', "SetTmplDisplay('PageTitleGroupFmt',0)");
Markup('notitle', 'directives',  '/\\(:notitle:\\)/ei', "dg_NoTitle()");
Markup('fieldset', 'inline', '/\\(:fieldset:\\)/i', "<fieldset>");
Markup('fieldsetend', 'inline', '/\\(:fieldsetend:\\)/i', "</fieldset>");

#Required to move page H2 header under blogit control, so it can be placed within blogit div structure.
global $bi_EntryType;
if ($bi_BlogItActive)  dg_NoTitle();

# Override pmwiki styles otherwise they will override styles declared in css
global $HTMLStylesFmt;
$HTMLStylesFmt['pmwiki'] = '';

# Add a custom page storage location
global $WikiLibDirs;
$PageStorePath = dirname(__FILE__)."/wikilib.d/{\$FullName}";
$where = count($WikiLibDirs);
if ($where>1) $where--;
array_splice($WikiLibDirs, $where, 0, array(new PageStore($PageStorePath)));

# ----------------------------------------
# - Standard Skin Functions
# ----------------------------------------
function dg_SetSkinColor($default, $valid_colors){
global $SkinColor, $ValidSkinColors, $_GET;
	if ( !is_array($ValidSkinColors) ) $ValidSkinColors = array();
	$ValidSkinColors = array_merge($ValidSkinColors, $valid_colors);
	if ( isset($_GET['color']) && in_array($_GET['color'], $ValidSkinColors) )
		$SkinColor = $_GET['color'];
	elseif ( !in_array($SkinColor, $ValidSkinColors) )
		$SkinColor = $default;
	return $SkinColor;
}
function dg_PoweredBy(){
	print ('<a href="http://pmwiki.com/'.($GLOBALS['bi_BlogIt_Enabled']?'Cookbook/BlogIt">BlogIt':'">PmWiki').'</a>');
}
# Determine logo height and width and add padding, unless already set by config.php
function dg_SetLogoHeightWidth ($wPad, $hPad=0){
global $PageLogoUrl, $PageLogoUrlHeight, $PageLogoUrlWidth;
	if (!isset($PageLogoUrlWidth) || !isset($PageLogoUrlHeight)){
		$size = @getimagesize($PageLogoUrl);
		if (!isset($PageLogoUrlWidth))  SDV($PageLogoUrlWidth, ($size ?$size[0]+$wPad :0) .'px');
		if (!isset($PageLogoUrlHeight))  SDV($PageLogoUrlHeight, ($size ?($size[1]+$hPad) :0) .'px');
	}
}
function dg_NoTitle(){
	SetTmplDisplay('PageTitleGroupFmt',0);SetTmplDisplay('PageTitleFmt',0);
}
