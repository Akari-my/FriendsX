<?php

namespace Akari_my\FriendsX\manager;

use Akari_my\FriendsX\Main;
use pocketmine\utils\Config;

class BlockManager {

    private Config $config;

    public function __construct(private Main $plugin) {
        $this->config = new Config($this->plugin->getDataFolder() . "data/blocked.yml", Config::YAML);
    }

    public function getBlockedList(string $player): array {
        $player = strtolower($player);
        return $this->config->get($player, []);
    }

    public function isBlocked(string $owner, string $target): bool {
        $owner = strtolower($owner);
        $target = strtolower($target);
        $blocked = $this->config->get($owner, []);
        return in_array($target, $blocked, true);
    }

    public function block(string $owner, string $target): bool {
        $owner = strtolower($owner);
        $target = strtolower($target);
        $blocked = $this->config->get($owner, []);
        if (in_array($target, $blocked, true)) {
            return false;
        }
        $blocked[] = $target;
        $this->config->set($owner, $blocked);
        $this->config->save();
        return true;
    }

    public function unblock(string $owner, string $target): bool {
        $owner = strtolower($owner);
        $target = strtolower($target);
        $blocked = $this->config->get($owner, []);
        if (!in_array($target, $blocked, true)) {
            return false;
        }
        $blocked = array_values(array_diff($blocked, [$target]));
        if (empty($blocked)) {
            $this->config->remove($owner);
        } else {
            $this->config->set($owner, $blocked);
        }
        $this->config->save();
        return true;
    }
}