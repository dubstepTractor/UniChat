<?php declare(strict_types = 1); namespace uni\chat\event;

use uni\chat\Manager;

use pocketmine\event\plugin\PluginEvent;
use pocketmine\event\Cancellable;

use pocketmine\Player;

class MessageSendEvent extends PluginEvent implements Cancellable {

	static $handlerList = null;

	/**
	 * @var Player
	 */
	private $player;

	/**
	 * @var string
	 */
	private $message;

	/**
	 *                        _
	 *   _____    _____ _ __ | |__
	 *  / _ \ \  / / _ \ '_ \|  _/
	 * |  __/\ \/ /  __/ | | | |_
	 *  \___/ \__/ \___|_| |_|\__\
	 *
	 *
	 * @param Manager $main
	 * @param Player  $player
	 * @param string  $message
	 */
	public function __construct(Manager $main, Player $player, string $message) {
		parent::__construct($main);

		$this->player  = $player;
		$this->message = $message;
	}

	/**
	 * @return Player
	 */
	public function getPlayer(): Player {
		return $this->player;
	}

	/**
	 * @return string
	 */
	public function getMessage(): string {
		return $this->message;
	}

	/**
	 * @param  string $message
	 *
	 * @return MessageSendEvent
	 */
	public function setMessage(string $message): MessageSendEvent {
		$this->message = $message;

		return $this;
	}
}