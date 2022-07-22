<?php declare(strict_types = 1); namespace uni\chat\adapter;

use uni\chat\Manager;

use pocketmine\plugin\PluginBase;

use Exception;

abstract class Adapter {

	protected const PLUGIN_NAME = '';

	/**
	 * @var Manager
	 */
	private $manager;

	/**
	 * @var PluginBase|null
	 */
	private $plugin;

	/**
	 *            _             _
	 *   __ _  __| | __ _ _ __ | |_____ _ __
	 *  / _' |/ _' |/ _' | '_ \|  _/ _ \ '_/
	 * | (_) | (_) | (_) | (_) | ||  __/ |
	 *  \__,_|\__,_|\__,_| ,__/ \__\___|_|
	 *                   |_|
	 *
	 * @param Manager $main
	 * @param bool    $soft
	 */
	public function __construct(Manager $main, bool $soft = false) {
		$name   = static::PLUGIN_NAME;
		$plugin = $main->getServer()->getPluginManager()->getPlugin($name);

		if(!$soft and !isset($plugin)) {
			throw new Exception("Adapter::__construct() - Hard dependency $name does not loaded!");
		}

		$this->manager = $main;
		$this->plugin  = $plugin;
	}

	/**
	 * @return Manager
	 */
	protected function getManager(): Manager {
		return $this->manager;
	}

	/**
	 * @return PluginBase|null
	 */
	protected function getPlugin(): ?PluginBase {
		return $this->plugin;
	}

	/**
	 * @return bool
	 */
	public function isEnabled(): bool {
		return $this->getPlugin() !== null;
	}
}