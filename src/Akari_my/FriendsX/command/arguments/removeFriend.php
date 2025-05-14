<?php

namespace Akari_my\FriendsX\command\arguments;

use Akari_my\FriendsX\command\SubCommand;
use Akari_my\FriendsX\Main;
use Akari_my\FriendsX\manager\LangManager;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class removeFriend implements SubCommand{

    public function execute(CommandSender $sender, array $args): void {
        if (!$sender instanceof Player) return;

        if (!isset($args[0])) {
            $sender->sendMessage("§cUse: /friend remove <player>");
            return;
        }

        $target = strtolower($args[0]);
        $player = strtolower($sender->getName());

        $manager = Main::getInstance()->getFriendsManager();

        if ($manager->removeFriend($player, $target)) {
            $sender->sendMessage(LangManager::get("friend-removed", ["target" => $target]));
        } else {
            $sender->sendMessage(LangManager::get("not-in-friend-list", ["target" => $target]));
        }
    }
}