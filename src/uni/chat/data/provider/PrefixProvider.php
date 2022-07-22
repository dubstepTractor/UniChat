<?php declare(strict_types = 1); namespace uni\chat\data\provider;

use uni\chat\Manager;

use pocketmine\utils\Config;

use function mkdir;
use function is_dir;
use function strtolower;

class PrefixProvider {

	protected const LOCATION = 'data/';
	protected const FILENAME = 'prefix.json';

	/**
	 * @var bool
	 */
	private $synchronized;

	/**
	 * @var Config
	 */
	private $storage;

	/**
	 *                       _     _
	 *  _ __  _ _______    _(_) __| | ___ _ __
	 * | '_ \| '_/ _ \ \  / | |/ _' |/ _ \ '_/
	 * | (_) | || (_) \ \/ /| | (_) |  __/ |
	 * | ,__/|_| \___/ \__/ |_|\__,_|\___|_|
	 * |_|
	 *
	 * @param Manager $main
	 */
	public function __construct(Manager $main, bool $sync = false) {
		$this->synchronized = $sync;

		$location = $main->getDataFolder(). static::LOCATION;

		if(!is_dir($location)) {
			/**
			 * @todo reformat
			 */
			mkdir($location, 0777, true);
		}

		$this->storage = new Config($location. static::FILENAME);

		/**
		 * @todo is this fixed already?
		 */
		$this->getStorage()->reload();
	}

	/**
	 * @param  string $nick
	 *
	 * @return string|null
	 */
	public function getPrefix(string $nick): ?string {
		$nick   = strtolower($nick);
		$prefix = $this->getStorage()->get($nick);

		if($prefix === false) {
			return null;
		}

		return $prefix;
	}

	/**
	 * @param  string $nick
	 * @param  string $prefix
	 *
	 * @return PrefixProvider
	 */
	public function setPrefix(string $nick, string $prefix): PrefixProvider {
		$this->getStorage()->set(strtolower($nick), $prefix);
		$this->getStorage()->save($this->isSynchronized());

		return $this;
	}

	/**
	 * @param  string $nick
	 *
	 * @return PrefixProvider
	 */
	public function removePrefix(string $nick): PrefixProvider {
		$this->getStorage()->remove(strtolower($nick));
		$this->getStorage()->save($this->isSynchronized());

		return $this;
	}

	/**
	 * @return bool
	 */
	private function isSynchronized(): bool {
		return $this->synchronized;
	}

	/**
	 * @return Config
	 */
	private function getStorage(): Config {
		return $this->storage;
	}

	/**
	 * @return PrefixProvider
	 */
	public function clearStorage(): PrefixProvider {
		$this->getStorage()->setAll([]);
		$this->getStorage()->save($this->isSynchronized());

		return $this;
	}
}