<?php declare(strict_types = 1); namespace uni\chat\command\argument;

use uni\chat\Manager;
use uni\chat\command\argument\Argument;

use pocketmine\command\CommandSender;
use pocketmine\Player;

use function strlen;
use function strtolower;
use function array_shift;

class RemoveArgument extends Argument {

	protected const NAME = 'remove';

	protected const PERMISSION        = 'unichat.command.prefix-remove';
	protected const PERMISSION_PLAYER = 'unichat.command.prefix-remove-player';

	protected const DESCRIPTION        = 'Убирает префикс отправителю';
	protected const DESCRIPTION_PLAYER = 'Убирает префикс указанному игроку';

	protected const PERMISSION_LIST = [
		self::PERMISSION        => self::DESCRIPTION,
		self::PERMISSION_PLAYER => self::DESCRIPTION_PLAYER
	];

	/**
	 *                                          _
	 *   __ _ _ ____ _ _   _ _ __ _   ___ _ __ | |__
	 *  / _' | '_/ _' | | | | '  ' \ / _ \ '_ \|  _/
	 * | (_) | || (_) | |_| | || || |  __/ | | | |_
	 *  \__,_|_| \__, |\___/|_||_||_|\___|_| |_|\__\
	 *           /___/
	 *
	 * @param  CommandSender $sender
	 * @param  string[]      $args
	 *
	 * @return mixed
	 */
	public function execute(CommandSender $sender, array $args) {
		if(!$sender->hasPermission(self::PERMISSION)) {
			$sender->sendMessage(Manager::PREFIX_ERROR. "Недостаточно прав");
			return true;
		}

		$main = $this->getManager();

		if(empty($args)) {
			if(!$sender instanceof Player) {
				$sender->sendMessage(Manager::PREFIX_INFO. "Используйте: §e/prefix remove <игрок>");
				return true;
			}

			$main->removePrefix($sender->getLowerCaseName());

			$sender->sendMessage(Manager::PREFIX_SUCCESS. "Ваш префикс очищен");
			return true;
		}

		if(!$sender->hasPermission(self::PERMISSION_PLAYER)) {
			$sender->sendMessage(Manager::PREFIX_ERROR. "Недостаточно прав");
			return true;
		}

		$nick = strtolower(array_shift($args));

		if(strlen($nick) > 16) {
			$sender->sendMessage(Manager::PREFIX_ERROR. "Никнейм не является действительным");
			return true;
		}

		if(strlen($nick) < 3) {
			$player = $main->getServer()->getPlayer($nick);

			if(!isset($player)) {
				$sender->sendMessage(Manager::PREFIX_ERROR. "Указанный игрок не найден. Введите никнейм полностью");
				return true;
			}

			$nick = $player->getLowerCaseName();
		}

		$main->removePrefix($nick);

		$sender->sendMessage(Manager::PREFIX_SUCCESS. "Префикс игрока §a$nick §rочищен");
		return true;
	}
}