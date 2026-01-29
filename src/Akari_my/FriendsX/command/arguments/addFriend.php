<?php

namespace Akari_my\FriendsX\command\arguments;

use Akari_my\FriendsX\command\SubCommand;
use Akari_my\FriendsX\Main;
use Akari_my\FriendsX\manager\LangManager;
use Akari_my\FriendsX\util\Functions;
use Akari_my\FriendsX\util\TimeUtils;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class addFriend implements SubCommand {

    public function execute(CommandSender $sender, array $args): void {
        if (!$sender instanceof Player) {
            return;
        }

        if (!isset($args[0])) {
            $sender->sendMessage(LangManager::get("friend-add-usage"));
            return;
        }

        $plugin = Main::getInstance();
        $friendsManager = $plugin->getFriendsManager();
        $requestManager = $plugin->getRequestManager();
        $settingsManager = $plugin->getSettingsManager();
        $blockManager = $plugin->getBlockManager();

        $playerName = $sender->getName();
        $player = strtolower($playerName);
        $targetInput = $args[0];

        if (strcasecmp($targetInput, $playerName) === 0) {
            $sender->sendMessage(LangManager::get("self-friend"));
            return;
        }

        $targetPlayer = Functions::getPlayerByName($targetInput);
        if ($targetPlayer === null || !$targetPlayer->isOnline()) {
            $sender->sendMessage(LangManager::get("player-not-online", ["target" => $targetInput]));
            return;
        }

        $targetName = $targetPlayer->getName();
        $target = strtolower($targetName);

        if (in_array($target, $friendsManager->getFriends($player), true)) {
            $sender->sendMessage(LangManager::get("already-friends"));
            return;
        }

        if (!$settingsManager->canReceiveRequests($target)) {
            $sender->sendMessage(LangManager::get("target-not-accepting-requests", ["target" => $targetName]));
            return;
        }

        if ($blockManager->isBlocked($target, $player)) {
            $sender->sendMessage(LangManager::get("you-are-blocked", ["target" => $targetName]));
            return;
        }

        if ($blockManager->isBlocked($player, $target)) {
            $sender->sendMessage(LangManager::get("you-blocked-target", ["target" => $targetName]));
            return;
        }

        $maxFriends = Functions::getMaxFriendsFor($sender);
        if (count($friendsManager->getFriends($player)) >= $maxFriends) {
            $sender->sendMessage(LangManager::get("friend-limit-reached", ["limit" => (string)$maxFriends]));
            return;
        }

        if ($requestManager->hasRequest($player, $target)) {
            $requestManager->removeRequest($player, $target);
            $friendsManager->addFriend($player, $target);
            $friendsManager->addFriend($target, $player);

            $sender->sendMessage(LangManager::get("request-accepted", ["target" => $targetName]));

            if ($targetPlayer->isOnline()) {
                $targetPlayer->sendMessage(LangManager::get("request-accepted-notify", [
                    "player" => $playerName
                ]));
            }
            return;
        }

        if (!$requestManager->sendRequest($player, $target)) {
            $time = $requestManager->getRemainingCooldown($target, $player);
            $sender->sendMessage(LangManager::get("request-cooldown", [
                "target" => $targetName,
                "time" => TimeUtils::formatDuration($time)
            ]));
            return;
        }

        $sender->sendMessage(LangManager::get("friend-request-sent", ["target" => $targetName]));
        $targetPlayer->sendMessage(LangManager::get("friend-request-received", [
            "sender" => $playerName,
            "name" => $playerName
        ]));
    }
}