<?php
/**
 * DBSCore      A Great Package For Prestashop Developers!
 * @version		V11Cart
 *
 * @file        DBSCore
 * @website     DBSTheme.com
 * @copyright	(c) 2015 - DBSTHEME Team
 * @author      Ali Shareei <alishareei@gmail.com>
 * @since       30 July 2015
 */
 
if(!class_exists('DBSCore\\V11Cart\\DBSModule'))
	require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR .'DBSModule.php');

if(!class_exists('DBSCore\\V11Cart\\DBSHelperForm'))
	require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR .'DBSHelperForm.php');

if(!class_exists('DBSCore\\V11Cart\\DBSValidator'))
	require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR .'DBSValidator.php');

if(!class_exists('DBSCore\\V11Cart\\DBSAdminController'))
    require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR .'DBSAdminController.php');

if(!class_exists('DBSCore\\V11Cart\\DBSGlobal'))
    require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR .'DBSGlobal.php');

if(!class_exists('DBSCore\\V11Cart\\DBSTemplateEngine'))
    require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR .'DBSTemplateEngine.php');