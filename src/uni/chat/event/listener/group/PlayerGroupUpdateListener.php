<?php declare(strict_types = 1); namespace uni\chat\event\listener\group;

use uni\chat\event\listener\ManagedListener;

use uni\group\event\group\PlayerGroupUpdateEvent as Event;

class PlayerGroupUpdateListener extends ManagedListener {

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

		$player = $event->getPlayer();

		if(!isset($player)) {
			return;
		}

		$this->getManager()->getGroupAdapter()->updateNameTag($player, $event->getPlayerGroup());
	}
}