<?php

namespace Akari_my\FriendsX\command\arguments;

use Akari_my\FriendsX\command\SubCommand;
use Akari_my\FriendsX\Main;
use Akari_my\FriendsX\manager\LangManager;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class addFriend implements SubCommand {

    public function execute(CommandSender $sender, array $args): void {
        if (!$sender instanceof Player) return;

        if (!isset($args[0])) {
            $sender->sendMessage("§cUse: /friend add <player>");
            return;
        }

        $target = strtolower($args[0]);
        $player = strtolower($sender->getName());

        if ($target === $player) {
            $sender->sendMessage(LangManager::get("self-friend"));
            return;
        }

        $targetPlayer = Main::getInstance()->getServer()->getPlayerExact($target);
        if ($targetPlayer === null || !$targetPlayer->isOnline()) {
            $sender->sendMessage(LangManager::get("player-not-online", ["target" => $target]));
            return;
        }

        $friendsManager = Main::getInstance()->getFriendsManager();
        $requestManager = Main::getInstance()->getRequestManager();

        if (in_array($target, $friendsManager->getFriends($player))) {
            $sender->sendMessage(LangManager::get("already-friends"));
            return;
        }

        if (!$requestManager->sendRequest($player, $target)) {
            $time = $requestManager->getRemainingCooldown($target, $player);
            $sender->sendMessage(LangManager::get("request-cooldown", [
                "target" => $target,
                "time" => (string)$time
            ]));
            return;
        }

        $sender->sendMessage(LangManager::get("friend-request-sent", ["target" => $target]));
        $targetPlayer->sendMessage(LangManager::get("friend-request-received", [
            "sender" => $player,
            "name" => $player
        ]));
    }
}