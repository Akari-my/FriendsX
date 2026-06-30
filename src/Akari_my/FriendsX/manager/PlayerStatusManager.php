<?php

declare(strict_types=1);

namespace Akari_my\FriendsX\manager;

use Akari_my\FriendsX\Main;
use pocketmine\utils\Config;

class PlayerStatusManager {

    private Config $config;

    private const STATUSES = ["online", "away", "busy", "invisible"];

    public function __construct(private Main $plugin) {
        $this->config = new Config($plugin->getDataFolder() . "data/statuses.yml", Config::YAML);
    }

    public static function getStatuses(): array {
        return self::STATUSES;
    }

    public function getStatus(string $player): string {
        $player = strtolower($player);
        return $this->config->get($player, "online");
    }

    public function setStatus(string $player, string $status): void {
        $player = strtolower($player);
        if (!in_array($status, self::STATUSES, true)) $status = "online";
        $this->config->set($player, $status);
        $this->config->save();
    }

    public static function getStatusLabel(string $status): string {
        return match ($status) {
            "away" => "§eAway",
            "busy" => "§cBusy",
            "invisible" => "§8Invisible",
            default => "§aOnline",
        };
    }

    public function isVisible(string $player): bool {
        return $this->getStatus($player) !== "invisible";
    }
}
