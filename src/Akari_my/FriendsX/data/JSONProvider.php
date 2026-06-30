<?php

declare(strict_types=1);

namespace Akari_my\FriendsX\data;

use Akari_my\FriendsX\Main;

class JSONProvider implements DataProvider {

    private array $data = [];
    private string $file;

    public function __construct(private Main $plugin) {
        $this->file = $plugin->getDataFolder() . "data/friends.json";
        $dir = dirname($this->file);
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }
        $this->load();
    }

    public function load(): void {
        if (file_exists($this->file)) {
            $this->data = json_decode(file_get_contents($this->file), true) ?? [];
        } else {
            $this->data = [];
        }
    }

    public function save(): void {
        $dir = dirname($this->file);
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }

        $json = json_encode($this->data, JSON_PRETTY_PRINT);
        if ($json === false) {
            return;
        }

        $tmp = $this->file . '.tmp';
        file_put_contents($tmp, $json);
        @rename($tmp, $this->file);
    }

    public function getFriends(string $player): array {
        $player = strtolower($player);
        return $this->data[$player] ?? [];
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
        $this->data[$player] = $normalized;
    }
}