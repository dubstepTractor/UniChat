<?php declare(strict_types = 1); namespace uni\chat;

use uni\chat\command\CcCommand;
use uni\chat\command\MeCommand;
use uni\chat\command\SayCommand;
use uni\chat\command\TellCommand;
use uni\chat\command\PrefixCommand;

use uni\chat\adapter\group\GroupAdapter;

use uni\chat\data\provider\PrefixProvider;
use uni\chat\data\configuration\BaseConfiguration;

use uni\chat\event\PrefixUpdateEvent;
use uni\chat\event\listener\player\PlayerChatListener;
use uni\chat\event\listener\player\PlayerLoginListener;

use uni\chat\event\listener\block\SignChangeListener;
use uni\chat\event\listener\group\PlayerGroupUpdateListener;

use pocketmine\permission\PermissionManager;
use pocketmine\permission\Permission;

use pocketmine\plugin\PluginBase;
use pocketmine\Player;

use function preg_match;
use function preg_replace;
use function str_replace;

class Manager extends PluginBase {

	public const DELIMITER_PREFIX = ' ';

	public const PLACEHOLDER_THREAD           = '{thread}';
	public const PLACEHOLDER_GROUP            = '{group}';
	public const PLACEHOLDER_PREFIX           = '{prefix}';
	public const PLACEHOLDER_PREFIX_DELIMITER = '{prefix_delimiter}';
	public const PLACEHOLDER_NICKNAME         = '{nickname}';
	public const PLACEHOLDER_MESSAGE          = '{message}';

	public const DEFAULT_DISPLAY_NAME = self::PLACEHOLDER_PREFIX. self::PLACEHOLDER_NICKNAME;
	public const DEFAULT_NAMETAG      = self::PLACEHOLDER_PREFIX. self::PLACEHOLDER_NICKNAME;
	public const DEFAULT_CHAT_FORMAT  = self::PLACEHOLDER_PREFIX. self::PLACEHOLDER_NICKNAME. ': '. self::PLACEHOLDER_MESSAGE;

	public const PERMISSION_FILTER_IGNORE      = 'unichat.ignore-filter';
	public const PERMISSION_FILTER_IGNORE_CHAT = 'unichat.ignore-filter.chat';
	public const PERMISSION_FILTER_IGNORE_SIGN = 'unichat.ignore-filter.sign';

	public const DESCRIPTION_FILTER_IGNORE      = 'Отключает фильтр текста для игрока';
	public const DESCRIPTION_FILTER_IGNORE_CHAT = 'Отключает фильтр текста чата для игрока';
	public const DESCRIPTION_FILTER_IGNORE_SIGN = 'Отключает фильтр текста табличек для игрока';

	public const PERMISSION_LIST = [
		self::PERMISSION_FILTER_IGNORE      => self::DESCRIPTION_FILTER_IGNORE,
		self::PERMISSION_FILTER_IGNORE_CHAT => self::DESCRIPTION_FILTER_IGNORE_CHAT,
		self::PERMISSION_FILTER_IGNORE_SIGN => self::DESCRIPTION_FILTER_IGNORE_SIGN
	];

	/**
	 * @todo implement Formatter
	 */
	public const PREFIX_SUCCESS = "§l§a(!)§r ";
	public const PREFIX_ERROR   = "§l§c(!)§r ";
	public const PREFIX_INFO    = "§l§e(!)§r ";

	/**
	 * @var BaseConfiguration
	 */
	private $base_configuration;

	/**
	 * @var PrefixProvider
	 */
	private $prefix_provider;

	/**
	 * @var GroupAdapter
	 */
	private $group_adapter;

	/**
	 *
	 *  _ __ _   __ _ _ __   __ _  __ _  ___ _ __
	 * | '  ' \ / _' | '_ \ / _' |/ _' |/ _ \ '_/
	 * | || || | (_) | | | | (_) | (_) |  __/ |
	 * |_||_||_|\__,_|_| |_|\__,_|\__, |\___|_|
	 *                            /___/
	 *
	 */
	public function onEnable(): void {
		$this->loadConfiguration();
		$this->loadPermission();
		$this->loadPrefixProvider();
		$this->loadGroupAdapter();
		$this->loadListener();
		$this->loadCommand();
	}

	private function loadConfiguration(): void {
		$this->base_configuration = new BaseConfiguration($this);
	}

	private function loadPermission(): void {
		foreach(self::PERMISSION_LIST as $permission => $description) {
			$permission = new Permission($permission, $description);

			PermissionManager::getInstance()->addPermission($permission);
		}
	}

	private function loadPrefixProvider(): void {
		$this->prefix_provider = new PrefixProvider($this);
	}

	private function loadGroupAdapter(): void {
		$this->group_adapter = new GroupAdapter($this, true);
	}

	private function loadListener(): void {
		$list = [
			new PlayerChatListener($this),
			new PlayerLoginListener($this),

			new SignChangeListener($this)
		];

		if($this->getGroupAdapter()->isEnabled()) {
			$list[] = new PlayerGroupUpdateListener($this);
		}

		foreach($list as $listener) {
			$this->getServer()->getPluginManager()->registerEvents($listener, $this);
		}
	}

	private function loadCommand(): void {
		$list = [
			new CcCommand($this),
			new MeCommand($this),
			new SayCommand($this),
			new TellCommand($this),
			new PrefixCommand($this)
		];

		foreach($list as $command) {
			$map     = $this->getServer()->getCommandMap();
			$replace = $map->getCommand($command->getName());

			if(isset($replace)) {
				$replace->setLabel('');
				$replace->unregister($map);
			}

			$map->register($this->getName(), $command);
		}
	}

	/**
	 * @return BaseConfiguration
	 */
	public function getBaseConfiguration(): BaseConfiguration {
		return $this->base_configuration;
	}

	/**
	 * @return PrefixProvider
	 */
	private function getPrefixProvider(): PrefixProvider {
		return $this->prefix_provider;
	}

	/**
	 * @return GroupAdapter
	 */
	public function getGroupAdapter(): GroupAdapter {
		return $this->group_adapter;
	}

	/**
	 * @todo   what about ukrainian?
	 *
	 * @param  string $string
	 * @param  bool   $filter_domain
	 * @param  bool   $filter_char
	 *
	 * @return string
	 */
	public function filterString(string $string, bool $filter_domain = false, bool $filter_char = false): string {
		if($filter_domain) {
			$list = [
				'/[\s\pP]\s*[tт]\s*[kк](?![а-яa-z])|(?<![а-яa-z])[tт]\s*[kк](?![а-яa-z])/ui',
				'/[\s\pP]\s*[pр]\s*[eе](?![а-яa-z])|(?<![а-яa-z])[pр]\s*[eе](?![а-яa-z])/ui',
				'/[\s\pP]\s*[cс]\s*[cс](?![а-яa-z])|(?<![а-яa-z])[cс]\s*[cс](?![а-яa-z])/ui',
				'/[\s\pP]\s*[rгpр]\s*[uyу](?![а-яa-z])|(?<![а-яa-z])[rгpр]\s*[uyу](?![а-яa-z])/ui',
				'/[\pP]\s*[nпн]\s*[eе]\s*[tт](?![а-яa-z])/ui',
				'/[\s\pP]\s*[cсkк]\s*[oо0]\s*[mм](?![а-яa-z])|(?<![а-яa-z])[cсkк]\s*[oо0]\s*[mм](?![а-яa-z])/ui',
				'/[\pP]\s*[fф]\s*[uyуа]\s*[nhн](?![а-яa-z])/ui',
				'/[\pP]\s*[pрп]\s*[rгpр]\s*[oо0](?![а-яa-z])/ui'
			];

			foreach($list as $regex) {
				if(!preg_match($regex, $string)) {
					continue;
				}

				return 'я люблю этот сервер';
			}
		}

		if($filter_char) {
			$string = preg_replace('/[^а-яa-z0-9\t\n ё.,~\/><?;:"\'`!@#$%^&*()\[\]{}_+=|\\-]/ui', '*', $string);
		}

		return $string;
	}

	/**
	 * @param  Player $player
	 * @param  string $message
	 * @param  bool   $is_local
	 *
	 * @return string
	 */
	public function formatMessage(Player $player, string $message, bool $is_local = false): string {
		$format = $this->getGroupAdapter()->formatChat($player->getName());
		$config = $this->getBaseConfiguration();

		if($config->isLocalChatEnabled()) {
			$thread = $is_local ? $config->getLocalChar() : $config->getGlobalChar();
			$format = str_replace(self::PLACEHOLDER_THREAD, $thread, $format);
		}

		return str_replace(self::PLACEHOLDER_MESSAGE, $message, $format);
	}

	/**
	 * @param  string      $string
	 * @param  string      $nick
	 * @param  string|null $group
	 *
	 * @return string
	 */
	public function formatString(string $string, string $nick, ?string $group = null): string {
		if(isset($group)) {
			$string = str_replace(self::PLACEHOLDER_GROUP, $group, $string);
		}

		$prefix    = $this->getPrefix($nick);
		$delimiter = self::DELIMITER_PREFIX;

		if(!isset($prefix)) {
			$prefix = $delimiter = '';
		}

		$string = str_replace(self::PLACEHOLDER_PREFIX,           $prefix,    $string);
		$string = str_replace(self::PLACEHOLDER_PREFIX_DELIMITER, $delimiter, $string);
		$string = str_replace(self::PLACEHOLDER_NICKNAME,         $nick,      $string);

		return $string;
	}

	/**
	 * @param Player $player
	 */
	public function updateNameTag(Player $player): void {
		$this->getGroupAdapter()->updateNameTag($player);
	}

	/**
	 *              _
	 *   __ _ ____ (_)
	 *  / _' |  _ \| |
	 * | (_) | (_) | |
	 *  \__,_|  __/|_|
	 *       |_|
	 *
	 * @param  string $nick
	 *
	 * @return string|null
	 */
	public function getPrefix(string $nick): ?string {
		return $this->getPrefixProvider()->getPrefix($nick);
	}

	/**
	 * @param string $nick
	 * @param string $prefix
	 */
	public function setPrefix(string $nick, string $prefix): void {
		$this->getPrefixProvider()->setPrefix($nick, $prefix);

		$player = $this->getServer()->getPlayerExact($nick);

		if(isset($player)) {
			$this->updateNameTag($player);
		}
	}

	/**
	 * @param string $nick
	 */
	public function removePrefix(string $nick): void {
		$this->getPrefixProvider()->removePrefix($nick);

		$player = $this->getServer()->getPlayerExact($nick);

		if(isset($player)) {
			$this->updateNameTag($player);
		}
	}
}