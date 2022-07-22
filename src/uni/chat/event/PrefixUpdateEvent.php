<?php declare(strict_types = 1); namespace uni\chat\event;

use uni\chat\Manager;

use pocketmine\event\plugin\PluginEvent;
use pocketmine\event\Cancellable;

use pocketmine\Player;

use function strtolower;

class PrefixUpdateEvent extends PluginEvent implements Cancellable {

	static $handlerList = null;

	/**
	 * @var string
	 */
	private $nickname;

	/**
	 * @var string
	 */
	private $prefix;

	/**
	 *                        _
	 *   _____    _____ _ __ | |__
	 *  / _ \ \  / / _ \ '_ \|  _/
	 * |  __/\ \/ /  __/ | | | |_
	 *  \___/ \__/ \___|_| |_|\__\
	 *
	 *
	 * @param Manager $main
	 * @param string  $nick
	 * @param string  $prefix
	 */
	public function __construct(Manager $main, string $nick, string $prefix) {
		parent::__construct($main);

		$this->nickname = strtolower($nick);
		$this->prefix   = $prefix;
	}

	/**
	 * @return string
	 */
	public function getNickname(): string {
		return $this->nickname;
	}

	/**
	 * @return string
	 */
	public function getPrefix(): string {
		return $this->prefix;
	}

	/**
	 * @return Player|null
	 */
	public function getPlayer(): ?Player {
		return $this->getPlugin()->getServer()->getPlayerExact($this->getNickname());
	}
}