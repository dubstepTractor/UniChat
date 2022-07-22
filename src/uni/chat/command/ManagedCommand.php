<?php declare(strict_types = 1); namespace uni\chat\command;

use uni\chat\Manager;

use pocketmine\permission\PermissionManager;
use pocketmine\permission\Permission;

use pocketmine\command\CommandSender;
use pocketmine\command\Command;

abstract class ManagedCommand extends Command {

	protected const NAME        = '';
	protected const PERMISSION  = '';
	protected const DESCRIPTION = '';

	protected const PERMISSION_LIST = [];

	/**
	 * @var Manager
	 */
	private $manager;

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
		parent::__construct(static::NAME, static::DESCRIPTION);

		$this->manager = $main;

		foreach(static::PERMISSION_LIST as $permission => $description) {
			$permission = new Permission($permission, $description);

			PermissionManager::getInstance()->addPermission($permission);
		}

		$this->setPermission(static::PERMISSION);
	}

	/**
	 * @param  CommandSender $sender
	 * @param  string        $label
	 * @param  string[]      $argument
	 *
	 * @return mixed
	 */
	abstract public function execute(CommandSender $sender, string $label, array $argument);

	/**
	 * @return Manager
	 */
	protected function getManager(): Manager {
		return $this->manager;
	}
}