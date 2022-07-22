<?php declare(strict_types = 1); namespace uni\chat\event\listener\block;

use uni\chat\Manager;
use uni\chat\event\listener\ManagedListener;

use pocketmine\event\block\SignChangeEvent as Event;

class SignChangeListener extends ManagedListener {

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
	 * @priority        LOWEST
	 * @ignoreCancelled FALSE
	 */
	public function onCall(Event $event): void {
		if($event->isCancelled()) {
			return;
		}

		$player = $event->getPlayer();
		$main   = $this->getManager();

		if(!$main->getBaseConfiguration()->isSignFilterEnabled()) {
			return;
		}

		if($player->hasPermission(Manager::PERMISSION_FILTER_IGNORE_SIGN)) {
			return;
		}

		foreach($event->getLines() as $line => $string) {
			if(empty($string)) {
				continue;
			}

			$event->setLine($line, $this->getManager()->filterString($string, true));
		}
	}
}