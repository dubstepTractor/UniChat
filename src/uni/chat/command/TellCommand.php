<?php declare(strict_types = 1); namespace uni\chat\command;

use uni\chat\Manager;
use uni\chat\event\MessageSendEvent;
use uni\chat\command\ManagedCommand;

use pocketmine\command\ConsoleCommandSender;
use pocketmine\command\CommandSender;

use pocketmine\Player;

use function implode;
use function mb_strlen;
use function strtolower;
use function array_shift;

class TellCommand extends ManagedCommand {

	protected const NAME = 'tell';

	protected const ARGUMENT_SERVER = 'server';

	protected const PERMISSION  = 'unichat.command.tell';
	protected const DESCRIPTION = '§eОтправляет личное сообщение игроку';

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
			$sender->sendMessage(Manager::PREFIX_INFO. "Используйте: §e/tell <игрок|server> <сообщение...>");
			return true;
		}

		$nick = strtolower(array_shift($args));

		if(mb_strlen($nick) > 16) {
			$sender->sendMessage(Manager::PREFIX_ERROR. "Никнейм не является действительным");
			return true;
		}

		$main   = $this->getManager();
		$target = $nick !== self::ARGUMENT_SERVER ? $main->getServer()->getPlayer($nick) : new ConsoleCommandSender();

		if(!isset($target)) {
			$sender->sendMessage(Manager::PREFIX_ERROR. "Указанный игрок не найден");
			return true;
		}

		if($sender === $target) {
			$sender->sendMessage(Manager::PREFIX_ERROR. "Вы не можете писать сами себе");
			return true;
		}

		if(empty($args)) {
			$sender->sendMessage(Manager::PREFIX_INFO. "Используйте: §e/tell <игрок|server> <сообщение...>");
			return true;
		}

		$nick    = 'Сервер';
		$message = implode(' ', $args);

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
		}

		$target->sendMessage("§7" . $nick . " §rпишет Вам: §e" . $message);

		$nick = $target instanceof Player ? $target->getName() : 'Серверу';

		$sender->sendMessage("§rВы пишете §7" . $nick . "§r: §e" . $message);
		return true;
	}
}