<?php

namespace Akari_my\FriendsX\command\arguments;

use Akari_my\FriendsX\command\SubCommand;
use Akari_my\FriendsX\Main;
use Akari_my\FriendsX\manager\LangManager;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class blockFriend implements SubCommand {

    public function execute(CommandSender $sender, array $args): void {
        if (!$sender instanceof Player) {
            return;
        }

        if (!isset($args[0])) {
            $sender->sendMessage(LangManager::get("block-usage"));
            return;
        }

        $plugin = Main::getInstance();
        $blockManager = $plugin->getBlockManager();
        $friendsManager = $plugin->getFriendsManager();
        $requestsManager = $plugin->getRequestManager();

        $playerName = $sender->getName();
        $player = strtolower($playerName);
        $targetInput = $args[0];

        if (strcasecmp($targetInput, $playerName) === 0) {
            $sender->sendMessage(LangManager::get("block-self"));
            return;
        }

        $target = strtolower($targetInput);

        if ($blockManager->isBlocked($player, $target)) {
            $sender->sendMessage(LangManager::get("already-blocked", ["target" => $targetInput]));
            return;
        }

        $blockManager->block($player, $target);

        if (in_array($target, $friendsManager->getFriends($player), true)) {
            $friendsManager->removeFriend($player, $target);
        }
        if (in_array($player, $friendsManager->getFriends($target), true)) {
            $friendsManager->removeFriend($target, $player);
        }

        $requestsManager->removeRequest($player, $target);
        $requestsManager->removeRequest($target, $player);

        $sender->sendMessage(LangManager::get("blocked-success", ["target" => $targetInput]));
    }
}