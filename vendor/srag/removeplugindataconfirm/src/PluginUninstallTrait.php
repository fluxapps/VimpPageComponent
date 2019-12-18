<?php

namespace srag\RemovePluginDataConfirm\VimpPageComponent;

use srag\RemovePluginDataConfirm\VimpPageComponent\Exception\RemovePluginDataConfirmException;

/**
 * Trait PluginUninstallTrait
 *
 * @package srag\RemovePluginDataConfirm\VimpPageComponent
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
trait PluginUninstallTrait {

	use AbstractPluginUninstallTrait;


	/**
	 * @return bool
	 * @throws RemovePluginDataConfirmException
	 *
	 * @internal
	 */
	protected final function beforeUninstall()/*: bool*/ {
		return $this->pluginUninstall();
	}


	/**
	 * @internal
	 */
	protected final function afterUninstall()/*: void*/ {

	}
}
