<?php

declare(strict_types=1);

namespace Akari_my\FriendsX\manager;

use Akari_my\FriendsX\Main;

class FriendsMetadataManager {

    private array $data = [];
    private string $file;

    private const GROUPS = ["default", "close", "family", "best"];

    public function __construct(private Main $plugin) {
        $this->file = $plugin->getDataFolder() . "data/friends_meta.json";
        $this->load();
    }

    public function load(): void {
        if (file_exists($this->file)) {
            $this->data = json_decode(file_get_contents($this->file), true) ?? [];
        }
    }

    public function save(): void {
        $dir = dirname($this->file);
        if (!is_dir($dir)) @mkdir($dir, 0777, true);
        $tmp = $this->file . '.tmp';
        file_put_contents($tmp, json_encode($this->data, JSON_PRETTY_PRINT), LOCK_EX);
        @rename($tmp, $this->file);
    }

    public static function getGroups(): array {
        return self::GROUPS;
    }

    public function getFriendMeta(string $player, string $friend): array {
        $player = strtolower($player);
        $friend = strtolower($friend);
        return $this->data[$player][$friend] ?? ["group" => "default", "favorite" => false];
    }

    private function setFriendMeta(string $player, string $friend, array $meta): void {
        $player = strtolower($player);
        $friend = strtolower($friend);
        $this->data[$player][$friend] = $meta;
        $this->save();
    }

    public function getGroup(string $player, string $friend): string {
        return $this->getFriendMeta($player, $friend)["group"] ?? "default";
    }

    public function setGroup(string $player, string $friend, string $group): void {
        if (!in_array($group, self::GROUPS, true)) $group = "default";
        $meta = $this->getFriendMeta($player, $friend);
        $meta["group"] = $group;
        $this->setFriendMeta($player, $friend, $meta);
    }

    public function isFavorite(string $player, string $friend): bool {
        return (bool)($this->getFriendMeta($player, $friend)["favorite"] ?? false);
    }

    public function toggleFavorite(string $player, string $friend): bool {
        $meta = $this->getFriendMeta($player, $friend);
        $meta["favorite"] = !($meta["favorite"] ?? false);
        $this->setFriendMeta($player, $friend, $meta);
        return $meta["favorite"];
    }

    public static function getGroupLabel(string $group): string {
        return match ($group) {
            "close" => "§bClose Friends",
            "family" => "§dFamily",
            "best" => "§6Best Friends",
            default => "§7Default",
        };
    }
}
