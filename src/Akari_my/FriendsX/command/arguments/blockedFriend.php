<?php

namespace Akari_my\FriendsX\command\arguments;

use Akari_my\FriendsX\command\SubCommand;
use Akari_my\FriendsX\Main;
use Akari_my\FriendsX\manager\LangManager;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class blockedFriend implements SubCommand {

    public function execute(CommandSender $sender, array $args): void {
        if (!$sender instanceof Player) {
            return;
        }

        $plugin = Main::getInstance();
        $blockManager = $plugin->getBlockManager();

        $playerName = $sender->getName();
        $player = strtolower($playerName);

        $blocked = $blockManager->getBlockedList($player);

        if (empty($blocked)) {
            $sender->sendMessage(LangManager::get("blocked-list-empty"));
            return;
        }

        $lines = [];
        foreach ($blocked as $name) {
            $lines[] = "§7- §e" . $name;
        }

        $sender->sendMessage(LangManager::get("blocked-list", [
            "list" => implode("\n", $lines)
        ]));
    }
}