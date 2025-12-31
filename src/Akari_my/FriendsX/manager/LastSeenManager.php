<?php

namespace Akari_my\FriendsX\manager;

use Akari_my\FriendsX\Main;
use pocketmine\utils\Config;

class LastSeenManager {

    private Config $config;

    public function __construct(private Main $plugin) {
        $this->config = new Config($this->plugin->getDataFolder() . "data/last_seen.yml", Config::YAML);
    }

    public function setLastSeen(string $player, int $time): void {
        $player = strtolower($player);
        $this->config->set($player, $time);
        $this->config->save();
    }

    public function getLastSeen(string $player): ?int {
        $player = strtolower($player);
        $value = $this->config->get($player, null);
        if ($value === null) {
            return null;
        }
        return (int)$value;
    }
}