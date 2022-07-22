<?php declare(strict_types = 1); namespace uni\chat\event\listener\player;

use uni\chat\Manager;

use uni\chat\event\MessageSendEvent;
use uni\chat\event\listener\ManagedListener;

use pocketmine\event\player\PlayerChatEvent as Event;
use pocketmine\command\ConsoleCommandSender;

use function substr;
use function mt_rand;

class PlayerChatListener extends ManagedListener {

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
		$main   = $this->getManager();
		$ev     = new MessageSendEvent($main, $player, $event->getMessage());

		$main->getServer()->getPluginManager()->callEvent($ev);

		if($ev->isCancelled()) {
			$event->setCancelled();
			return;
		}

		$message  = $ev->getMessage();
		$is_local = false;

		if($main->getBaseConfiguration()->isLocalChatEnabled()) {
			$is_local = true;

			if(!empty($message) and $message[0] === '!') {
				$is_local = false;
				$message  = substr($message, 1);
			}
		}

		if($is_local) {
			$list = [new ConsoleCommandSender()];

			foreach($player->getLevel()->getPlayers() as $recipient) {
				$list[] = $recipient;
			}

			$event->setRecipients($list);

			if(mt_rand(1, 30) === 1) {
				$player->sendMessage(Manager::PREFIX_INFO. "§eL §r- локальное сообщение (видно в вашем мире)");
				$player->sendMessage(Manager::PREFIX_INFO. "§eG §r- глобальное сообщение (видно всем игрокам)");
			}
		}

		if(!$player->hasPermission(Manager::PERMISSION_FILTER_IGNORE_CHAT)) {
			$message = $main->filterString($message, true, true);
		}

		$event->setFormat($main->formatMessage($player, $message, $is_local));
	}
}