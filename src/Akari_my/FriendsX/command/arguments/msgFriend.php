<?php

declare(strict_types=1);

namespace Akari_my\FriendsX\command\arguments;

use Akari_my\FriendsX\command\SubCommand;
use Akari_my\FriendsX\Main;
use Akari_my\FriendsX\manager\LangManager;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class msgFriend implements SubCommand {

    public function execute(CommandSender $sender, array $args): void {
        if (!$sender instanceof Player) return;

        if (!isset($args[0]) || !isset($args[1])) {
            $sender->sendMessage(LangManager::get("msg-usage"));
            return;
        }

        $plugin = Main::getInstance();
        $friendsManager = $plugin->getFriendsManager();

        $playerName = $sender->getName();
        $player = strtolower($playerName);
        $targetInput = $args[0];
        $target = strtolower($targetInput);

        if (!in_array($target, $friendsManager->getFriends($player), true)) {
            $sender->sendMessage(LangManager::get("not-in-friend-list", ["target" => $targetInput]));
            return;
        }

        $targetPlayer = $plugin->getServer()->getPlayerByPrefix($targetInput);
        if ($targetPlayer === null || !$targetPlayer->isOnline()) {
            $sender->sendMessage(LangManager::get("player-not-online", ["target" => $targetInput]));
            return;
        }

        $message = implode(" ", array_slice($args, 1));
        $targetPlayer->sendMessage(LangManager::get("msg-received", ["sender" => $playerName, "message" => $message]));
        $sender->sendMessage(LangManager::get("msg-sent", ["target" => $targetPlayer->getName(), "message" => $message]));
    }
}
