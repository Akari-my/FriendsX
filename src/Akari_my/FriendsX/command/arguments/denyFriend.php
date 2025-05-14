<?php

namespace Akari_my\FriendsX\command\arguments;

use Akari_my\FriendsX\command\SubCommand;
use Akari_my\FriendsX\Main;
use Akari_my\FriendsX\manager\LangManager;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class denyFriend implements SubCommand{

    public function execute(CommandSender $sender, array $args): void {
        if (!$sender instanceof Player) return;

        if (!isset($args[0])) {
            $sender->sendMessage("§cUse: /friend deny <player>");
            return;
        }

        $target = strtolower($args[0]);
        $player = strtolower($sender->getName());

        $requests = Main::getInstance()->getRequestManager();

        if (!$requests->hasRequest($player, $target)) {
            $sender->sendMessage(LangManager::get("player-not-in-requests", ["target" => $target]));
            return;
        }

        $requests->removeRequest($player, $target);
        $sender->sendMessage(LangManager::get("request-denied", ["target" => $target]));
    }
}