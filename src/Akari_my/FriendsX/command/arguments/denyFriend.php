<?php

namespace Akari_my\FriendsX\command\arguments;

use Akari_my\FriendsX\command\SubCommand;
use Akari_my\FriendsX\Main;
use Akari_my\FriendsX\manager\LangManager;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class denyFriend implements SubCommand {

    public function execute(CommandSender $sender, array $args): void {
        if (!$sender instanceof Player) {
            return;
        }

        $plugin = Main::getInstance();
        $requests = $plugin->getRequestManager();

        $playerName = $sender->getName();
        $player = strtolower($playerName);

        if (!isset($args[0])) {
            $pending = $requests->getRequests($player);
            if (empty($pending)) {
                $sender->sendMessage(LangManager::get("no-requests"));
                return;
            }
            if (count($pending) === 1) {
                $target = $pending[0];
            } else {
                $listLines = [];
                foreach ($pending as $name) {
                    $listLines[] = "§7- §e" . $name;
                }
                $sender->sendMessage(LangManager::get("multiple-requests-deny", [
                    "list" => implode("\n", $listLines)
                ]));
                return;
            }
        } else {
            $target = strtolower($args[0]);
        }

        if (!$requests->hasRequest($player, $target)) {
            $sender->sendMessage(LangManager::get("player-not-in-requests", ["target" => $target]));
            return;
        }

        $requests->removeRequest($player, $target);
        $sender->sendMessage(LangManager::get("request-denied", ["target" => $target]));
    }
}