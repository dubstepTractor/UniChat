<?php declare(strict_types = 1); namespace uni\chat\command;

use uni\chat\Manager;
use uni\chat\command\ManagedCommand;

use pocketmine\command\ConsoleCommandSender;
use pocketmine\command\CommandSender;

use function array_shift;

class CcCommand extends ManagedCommand {

	protected const NAME = 'cc';

	protected const ARGUMENT_ALL = 'all';

	protected const PERMISSION     = 'unichat.command.cc';
	protected const PERMISSION_ALL = 'unichat.command.cc-all';

	protected const DESCRIPTION     = '§eОчищает чат';
	protected const DESCRIPTION_ALL = 'Очищает чат всем игрокам';

	protected const PERMISSION_LIST = [
		self::PERMISSION     => self::DESCRIPTION,
		self::PERMISSION_ALL => self::DESCRIPTION_ALL
	];

	private const SPAM_SIZE = 32;

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
		if(empty($args)) {
			if(!$sender->hasPermission(self::PERMISSION)) {
				$sender->sendMessage(Manager::PREFIX_ERROR. "Недостаточно прав");
				return true;
			}

			for($i = 0; $i < self::SPAM_SIZE; $i++) {
				$sender->sendMessage(PHP_EOL);
			}

			$sender->sendMessage(Manager::PREFIX_SUCCESS. "Чат очищен");
			return true;
		}

		if(array_shift($args) !== self::ARGUMENT_ALL) {
			$sender->sendMessage("/cc all");
			return true;
		}

		if(!$sender->hasPermission(self::PERMISSION_ALL)) {
			$sender->sendMessage(Manager::PREFIX_ERROR. "Недостаточно прав");
			return true;
		}

		$list   = $this->getManager()->getServer()->getOnlinePLayers();
		$list[] = new ConsoleCommandSender();

		foreach($list as $player) {
			for($i = 0; $i < self::SPAM_SIZE; $i++) {
				$sender->sendMessage(PHP_EOL);
			}
		}

		$sender->sendMessage(Manager::PREFIX_SUCCESS. "Чат сервера очищен");
		return true;
	}
}