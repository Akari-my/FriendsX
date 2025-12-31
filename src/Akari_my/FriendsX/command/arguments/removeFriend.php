<?php

namespace Akari_my\FriendsX\command\arguments;

use Akari_my\FriendsX\command\SubCommand;
use Akari_my\FriendsX\Main;
use Akari_my\FriendsX\manager\LangManager;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class removeFriend implements SubCommand {

    public function execute(CommandSender $sender, array $args): void {
        if (!$sender instanceof Player) {
            return;
        }

        if (!isset($args[0])) {
            $sender->sendMessage(LangManager::get("friend-remove-usage"));
            return;
        }

        $plugin = Main::getInstance();
        $manager = $plugin->getFriendsManager();

        $target = strtolower($args[0]);
        $player = strtolower($sender->getName());

        if ($manager->removeFriend($player, $target)) {
            if ($plugin->getConfig()->get("two-sided-friends", true)) {
                $manager->removeFriend($target, $player);
            }
            $sender->sendMessage(LangManager::get("friend-removed", ["target" => $args[0]]));
        } else {
            $sender->sendMessage(LangManager::get("not-in-friend-list", ["target" => $args[0]]));
        }
    }
}