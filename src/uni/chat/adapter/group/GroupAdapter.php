<?php declare(strict_types = 1); namespace uni\chat\adapter\group;

use uni\chat\Manager;
use uni\chat\adapter\Adapter;

use uni\group\data\PlayerGroup;
use uni\group\data\Group;

use pocketmine\Player;

class GroupAdapter extends Adapter {

	protected const PLUGIN_NAME = 'UniGroup';

	/**
	 * @todo  it's not supposed to be here
	 *
	 * @param Player     $player
	 * @param Group|null $group
	 */
	public function updateNameTag(Player $player, ?Group $group = null): void {
		$name = $player->getName();

		$player->setDisplayName($this->formatDisplayName($name, $group));
		$player->setNameTag($this->formatNameTag($name, $group));
	}

	/**
	 * @param  string     $nick
	 * @param  Group|null $group
	 *
	 * @return string
	 */
	public function formatDisplayName(string $nick, ?Group $group = null): string {
		$main  = $this->getManager();
		$group = $group ?? $this->getPlayerGroup($nick);

		if(!isset($group)) {
			return $main->formatString(Manager::DEFAULT_DISPLAY_NAME, $nick);
		}

		return $main->formatString($group->getDisplayName(), $nick, $group->getName());
	}

	/**
	 * @param  string     $nick
	 * @param  Group|null $group
	 *
	 * @return string
	 */
	public function formatNameTag(string $nick, ?Group $group = null): string {
		$main  = $this->getManager();
		$group = $group ?? $this->getPlayerGroup($nick);

		if(!isset($group)) {
			return $main->formatString(Manager::DEFAULT_NAMETAG, $nick);
		}

		return $main->formatString($group->getNameTag(), $nick, $group->getName());
	}

	/**
	 * @param  string     $nick
	 * @param  Group|null $group
	 *
	 * @return string
	 */
	public function formatChat(string $nick, ?Group $group = null): string {
		$main  = $this->getManager();
		$group = $group ?? $this->getPlayerGroup($nick);

		if(!isset($group)) {
			return $main->formatString(Manager::DEFAULT_CHAT_FORMAT, $nick);
		}

		return $main->formatString($group->getChatFormat(), $nick, $group->getName());
	}

	/**
	 * @param  string $nick
	 *
	 * @return PlayerGroup|null
	 */
	private function getPlayerGroup(string $nick): ?PlayerGroup {
		return $this->isEnabled() ? $this->getPlugin()->getPlayerGroup($nick) : null;
	}
}