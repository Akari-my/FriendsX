<?php

namespace Akari_my\FriendsX\data;

use Akari_my\FriendsX\Main;
use pocketmine\utils\Config;

class YAMLProvider implements DataProvider {

    private Config $config;

    public function __construct(private Main $plugin) {
        $this->load();
    }

    public function load(): void {
        $this->config = new Config($this->plugin->getDataFolder() . "data/friends.yml", Config::YAML);
    }

    public function save(): void {
        $this->config->save();
    }

    public function getFriends(string $player): array {
        $player = strtolower($player);
        return $this->config->get($player, []);
    }

    public function setFriends(string $player, array $friends): void {
        $player = strtolower($player);
        $normalized = [];
        foreach ($friends as $friend) {
            $name = strtolower($friend);
            if (!in_array($name, $normalized, true)) {
                $normalized[] = $name;
            }
        }
        $this->config->set($player, $normalized);
    }
}