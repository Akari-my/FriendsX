<?php

declare(strict_types=1);

namespace Akari_my\FriendsX\command\arguments;

use Akari_my\FriendsX\command\SubCommand;
use Akari_my\FriendsX\Main;
use Akari_my\FriendsX\manager\LangManager;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class statusFriend implements SubCommand {

    public function execute(CommandSender $sender, array $args): void {
        if (!$sender instanceof Player) return;

        $plugin = Main::getInstance();
        $statusManager = $plugin->getPlayerStatusManager();
        $player = strtolower($sender->getName());

        if (!isset($args[0])) {
            $current = $statusManager->getStatus($player);
            $sender->sendMessage(LangManager::get("status-current", [
                "status" => \Akari_my\FriendsX\manager\PlayerStatusManager::getStatusLabel($current)
            ]));
            $sender->sendMessage(LangManager::get("status-usage"));
            return;
        }

        $new = strtolower($args[0]);
        $valid = \Akari_my\FriendsX\manager\PlayerStatusManager::getStatuses();

        if (!in_array($new, $valid, true)) {
            $sender->sendMessage(LangManager::get("status-invalid"));
            return;
        }

        $statusManager->setStatus($player, $new);
        $sender->sendMessage(LangManager::get("status-set", [
            "status" => \Akari_my\FriendsX\manager\PlayerStatusManager::getStatusLabel($new)
        ]));
    }
}
