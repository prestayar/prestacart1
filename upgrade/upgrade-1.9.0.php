<?php
if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_1_9_0($module)
{
    $parent_id = $module->createOrFindDefaultParentTab();
    if (!$parent_id)
        return false;

    if (!empty($module->moduleTabClassName))
    {
        $tabId = Tab::getIdFromClassName($module->moduleTabClassName);

        $tab = new Tab($tabId);
        $tab->id_parent = $parent_id;
        $tab->save();
    }

	return true;
}
