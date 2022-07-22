<?php declare(strict_types = 1); namespace uni\chat\data\configuration;

use uni\chat\Manager;

use pocketmine\utils\Config;

use function mkdir;
use function is_dir;
use function strtolower;

/**
 * @todo check alternatives
 */
class BaseConfiguration {

	private const FILENAME = 'config.yml';

	private const INDEX_CHAT_FILTER_ENABLED = 'chat_filter_enabled';
	private const INDEX_SIGN_FILTER_ENABLED = 'sign_filter_enabled';

	private const INDEX_LOCAL_CHAT_ENABLED = 'local_chat_enabled';
	private const INDEX_CHAR_LOCAL         = 'char_local';
	private const INDEX_CHAR_GLOBAL        = 'char_global';

	/**
	 * @var Config
	 */
	private $config;

	/**
	 * @param Manager $main
	 */
	public function __construct(Manager $main) {
		$location = $main->getDataFolder();

		if(!is_dir($location)) {
			mkdir($location, 0777, true);
		}

		$main->saveResource(static::FILENAME);

		$this->config = new Config($location. static::FILENAME);
	}

	/**
	 * @param  string $index
	 *
	 * @return mixed
	 */
	private function getValue(string $index) {
		$index  = strtolower($index);
		$result = $this->getConfig()->get($index, null);

		if(!isset($result)) {
			throw new Exception("BaseConfiguration::getValue() - Index $index does not exists!");
		}

		return $result;
	}

	/**
	 * @return Config
	 */
	private function getConfig(): Config {
		return $this->config;
	}

	/**
	 *                    __ _                      _   _
	 *   ___  ___  _ __ _/ _(_) __ _ _   _ _ ____ _| |_(_) ___  _ __
	 *  / __\/ _ \| '_ \   _| |/ _' | | | | '_/ _' |  _| |/ _ \| '_ \
	 * | (__| (_) | | | | | | | (_) | |_| | || (_) | |_| | (_) | | | |
	 *  \___/\___/|_| |_|_| |_|\__  |\___/|_| \__,_|\__|_|\___/|_| |_|
	 *                         /___/
	 *
	 * @return bool
	 */
	public function isChatFilterEnabled(): bool {
		return $this->getValue(self::INDEX_CHAT_FILTER_ENABLED);
	}

	/**
	 * @return bool
	 */
	public function isSignFilterEnabled(): bool {
		return $this->getValue(self::INDEX_SIGN_FILTER_ENABLED);
	}

	/**
	 * @return bool
	 */
	public function isLocalChatEnabled(): bool {
		return $this->getValue(self::INDEX_LOCAL_CHAT_ENABLED);
	}

	/**
	 * @return string
	 */
	public function getLocalChar(): string {
		return $this->getValue(self::INDEX_CHAR_LOCAL);
	}

	/**
	 * @return string
	 */
	public function getGlobalChar(): string {
		return $this->getValue(self::INDEX_CHAR_GLOBAL);
	}
}