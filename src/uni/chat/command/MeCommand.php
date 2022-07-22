<?php declare(strict_types = 1); namespace uni\chat\command;

use uni\chat\Manager;
use uni\chat\event\MessageSendEvent;
use uni\chat\command\ManagedCommand;

use pocketmine\command\ConsoleCommandSender;
use pocketmine\command\CommandSender;

use pocketmine\Player;

use function implode;

class MeCommand extends ManagedCommand {

	protected const NAME        = 'me';
	protected const PERMISSION  = 'unichat.command.me';
	protected const DESCRIPTION = '§eОтображает сообщение о Вас';

	protected const PERMISSION_LIST = [
		self::PERMISSION => self::DESCRIPTION
	];

	/**
	 *                                             _
	 *   ___  ___  _ __ _  _ __ _   __ _ _ __   __| |
	 *  / __\/ _ \| '  ' \| '  ' \ / _' | '_ \ / _' |
	 * | (__| (_) | || || | || || | (_) | | | | (_) |
	 *  \___/\___/|_||_||_|_||_||_|\__,_|_| |_|\__,_|
	 *
	 *
	 * @param  CommandSender $sender
	 * @param  string        $label
	 * @param  string[]      $args
	 *
	 * @return mixed
	 */
	public function execute(CommandSender $sender, string $label, array $args) {
		if(!$sender->hasPermission(self::PERMISSION)) {
			$sender->sendMessage(Manager::PREFIX_ERROR. "Недостаточно прав");
			return true;
		}

		if(empty($args)) {
			$sender->sendMessage(Manager::PREFIX_INFO. "Используйте: §e/me <сообщение...>");
			return true;
		}

		$main = $this->getManager();

		$nick    = 'Сервер';
		$message = implode(' ', $args);
		$list    = $main->getServer()->getOnlinePlayers();

		if($sender instanceof Player) {
			$event = new MessageSendEvent($main, $sender, $message);

			$event->call();

			if($event->isCancelled()) {
				return true;
			}

			$nick    = $sender->getName();
			$message = $event->getMessage();

			if(!$sender->hasPermission(Manager::PERMISSION_FILTER_IGNORE_CHAT)) {
				$message = $main->filterString($message, true, true);
			}

			if($main->getBaseConfiguration()->isLocalChatEnabled()) {
				$list = $sender->getLevel()->getPlayers();
			}
		}

		$list[] = new ConsoleCommandSender();

		$main->getServer()->broadcastMessage("* §7" . $nick . " " . $message, $list);
		return true;
	}
}