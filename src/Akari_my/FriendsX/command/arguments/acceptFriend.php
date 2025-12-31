<?php

namespace Akari_my\FriendsX\command\arguments;

use Akari_my\FriendsX\command\SubCommand;
use Akari_my\FriendsX\Main;
use Akari_my\FriendsX\manager\LangManager;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class acceptFriend implements SubCommand {

    public function execute(CommandSender $sender, array $args): void {
        if (!$sender instanceof Player) {
            return;
        }

        $plugin = Main::getInstance();
        $requests = $plugin->getRequestManager();
        $friendsManager = $plugin->getFriendsManager();

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
                $sender->sendMessage(LangManager::get("multiple-requests-accept", [
                    "list" => implode("\n", $listLines)
                ]));
                return;
            }
        } else {
            $target = strtolower($args[0]);
        }

        if (!$requests->hasRequest($player, $target)) {
            $sender->sendMessage(LangManager::get("request-expired", ["target" => $target]));
            return;
        }

        $max = $plugin->getMaxFriendsFor($sender);
        if (count($friendsManager->getFriends($player)) >= $max) {
            $sender->sendMessage(LangManager::get("friend-limit-reached", ["limit" => (string)$max]));
            return;
        }

        $requests->removeRequest($player, $target);
        $friendsManager->addFriend($player, $target);
        $friendsManager->addFriend($target, $player);

        $sender->sendMessage(LangManager::get("request-accepted", ["target" => $target]));

        $targetPlayer = $plugin->getPlayerByName($target);
        if ($targetPlayer !== null && $targetPlayer->isOnline()) {
            $targetPlayer->sendMessage(LangManager::get("request-accepted-notify", [
                "player" => $playerName
            ]));
        }
    }
}