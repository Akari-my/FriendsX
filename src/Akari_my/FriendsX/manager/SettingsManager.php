<?php

namespace Akari_my\FriendsX\manager;

use Akari_my\FriendsX\Main;
use pocketmine\utils\Config;

class SettingsManager {

    private Config $config;

    public function __construct(private Main $plugin) {
        $this->config = new Config($this->plugin->getDataFolder() . "data/settings.yml", Config::YAML);
    }

    private function getAll(string $player): array {
        $player = strtolower($player);
        return $this->config->get($player, []);
    }

    private function setAll(string $player, array $data): void {
        $player = strtolower($player);
        $this->config->set($player, $data);
        $this->config->save();
    }

    public function canReceiveRequests(string $player): bool {
        $data = $this->getAll($player);
        return isset($data["receive_requests"]) ? (bool)$data["receive_requests"] : true;
    }

    public function setReceiveRequests(string $player, bool $value): void {
        $data = $this->getAll($player);
        $data["receive_requests"] = $value;
        $this->setAll($player, $data);
    }

    public function toggleReceiveRequests(string $player): bool {
        $new = !$this->canReceiveRequests($player);
        $this->setReceiveRequests($player, $new);
        return $new;
    }

    public function canReceiveFriendNotifications(string $player): bool {
        $data = $this->getAll($player);
        return isset($data["friend_notifications"]) ? (bool)$data["friend_notifications"] : true;
    }

    public function setReceiveFriendNotifications(string $player, bool $value): void {
        $data = $this->getAll($player);
        $data["friend_notifications"] = $value;
        $this->setAll($player, $data);
    }

    public function toggleFriendNotifications(string $player): bool {
        $new = !$this->canReceiveFriendNotifications($player);
        $this->setReceiveFriendNotifications($player, $new);
        return $new;
    }
}