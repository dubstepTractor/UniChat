<?php declare(strict_types = 1); namespace uni\chat\event\listener\player;

use uni\chat\event\listener\ManagedListener;

use pocketmine\event\player\PlayerLoginEvent as Event;

class PlayerLoginListener extends ManagedListener {

	/**
	 *  _ _      _
	 * | (_)____| |_____ _ __   ___ _ __
	 * | | / __/   _/ _ \ '_ \ / _ \ '_/
	 * | | \__ \| ||  __/ | | |  __/ |
	 * |_|_|___/ \__\___|_| |_|\___|_|
	 *
	 *
	 * @param Event $event
	 *
	 * @priority        NORMAL
	 * @ignoreCancelled FALSE
	 */
	public function onCall(Event $event): void {
		if($event->isCancelled()) {
			return;
		}

		$this->getManager()->updateNameTag($event->getPlayer());
	}
}