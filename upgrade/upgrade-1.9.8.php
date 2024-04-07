<?php
if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_1_9_8($module)
{
    Configuration::updateValue('PSCA_CHECK_LANG','0');
	return true;
}