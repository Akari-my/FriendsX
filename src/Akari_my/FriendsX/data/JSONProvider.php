<?php

namespace Akari_my\FriendsX\data;

use Akari_my\FriendsX\Main;

class JSONProvider implements DataProvider {

    private array $data = [];
    private string $file;

    public function __construct(private Main $plugin) {
        $this->file = $plugin->getDataFolder() . "data/friends.json";
        $this->load();
    }

    public function load(): void {
        if (file_exists($this->file)) {
            $this->data = json_decode(file_get_contents($this->file), true) ?? [];
        }
    }

    public function save(): void {
        file_put_contents($this->file, json_encode($this->data, JSON_PRETTY_PRINT));
    }

    public function getFriends(string $player): array {
        return $this->data[$player] ?? [];
    }

    public function setFriends(string $player, array $friends): void {
        $this->data[$player] = $friends;
    }
}