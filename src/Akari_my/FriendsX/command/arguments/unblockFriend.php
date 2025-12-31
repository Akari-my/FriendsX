<?php

namespace Akari_my\FriendsX\command\arguments;

use Akari_my\FriendsX\command\SubCommand;
use Akari_my\FriendsX\Main;
use Akari_my\FriendsX\manager\LangManager;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class unblockFriend implements SubCommand {

    public function execute(CommandSender $sender, array $args): void {
        if (!$sender instanceof Player) {
            return;
        }

        if (!isset($args[0])) {
            $sender->sendMessage(LangManager::get("unblock-usage"));
            return;
        }

        $plugin = Main::getInstance();
        $blockManager = $plugin->getBlockManager();

        $playerName = $sender->getName();
        $player = strtolower($playerName);
        $targetInput = $args[0];
        $target = strtolower($targetInput);

        if (!$blockManager->isBlocked($player, $target)) {
            $sender->sendMessage(LangManager::get("not-blocked", ["target" => $targetInput]));
            return;
        }

        $blockManager->unblock($player, $target);
        $sender->sendMessage(LangManager::get("unblocked-success", ["target" => $targetInput]));
    }
}