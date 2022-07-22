<?php declare(strict_types = 1); namespace uni\chat\command;

use uni\chat\Manager;
use uni\chat\command\ManagedCommand;

use uni\chat\command\argument\Argument;
use uni\chat\command\argument\SetArgument;
use uni\chat\command\argument\RemoveArgument;

use pocketmine\command\CommandSender;

use function strtolower;
use function array_shift;

class PrefixCommand extends ManagedCommand {

	protected const NAME        = 'prefix';
	protected const PERMISSION  = 'unichat.command.prefix';
	protected const DESCRIPTION = '§eПоказывает помощь или список команд управления префиксами';

	protected const PERMISSION_LIST = [
		self::PERMISSION => self::DESCRIPTION
	];

	/**
	 * @var Argument[]
	 */
	private $argument_list = [
		SetArgument::class,
		RemoveArgument::class
	];

	/**
	 *                                             _
	 *   ___  ___  _ __ _  _ __ _   __ _ _ __   __| |
	 *  / __\/ _ \| '  ' \| '  ' \ / _' | '_ \ / _' |
	 * | (__| (_) | || || | || || | (_) | | | | (_) |
	 *  \___/\___/|_||_||_|_||_||_|\__,_|_| |_|\__,_|
	 *
	 *
	 * @param Manager $main
	 */
	public function __construct(Manager $main) {
		parent::__construct($main);

		foreach($this->argument_list as $index => $class) {
			$this->argument_list[$index] = new $class($main);
		}
	}

	/**
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

		$main = $this->getManager();

		if(!empty($args)) {
			$argument = $this->getArgument(array_shift($args));

			if(isset($argument)) {
				return $argument->execute($sender, $args);
			}
		}

		$sender->sendMessage(Manager::PREFIX_INFO. "Используйте: §e/prefix <remove/set>");
		return true;
	}

	/**
	 * @param  string $name
	 *
	 * @return Argument|null
	 */
	private function getArgument(string $name): ?Argument {
		$name = strtolower($name);

		foreach($this->getArgumentList() as $argument) {
			if($argument->getName() !== $name) {
				continue;
			}

			return $argument;
		}

		return null;
	}

	/**
	 * @return Argument[]
	 */
	private function getArgumentList(): array {
		return $this->argument_list;
	}
}