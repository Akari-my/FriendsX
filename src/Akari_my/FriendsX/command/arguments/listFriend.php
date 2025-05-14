<?php

namespace Akari_my\FriendsX\command\arguments;

use Akari_my\FriendsX\command\SubCommand;
use Akari_my\FriendsX\Main;
use Akari_my\FriendsX\manager\LangManager;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class listFriend implements SubCommand {

    public function execute(CommandSender $sender, array $args): void {
        if (!$sender instanceof Player) return;

        $player = strtolower($sender->getName());
        $manager = Main::getInstance()->getFriendsManager();
        $friends = $manager->getFriends($player);

        if (empty($friends)) {
            $sender->sendMessage(LangManager::get("no-friends"));
            return;
        }

        $formatted = [];
        $server = Main::getInstance()->getServer();

        foreach ($friends as $friendName) {
            $isOnline = $server->getPlayerExact($friendName)?->isOnline() ?? false;
            $status = $isOnline ? "Online" : "Offline";
            $formatted[] = "- $friendName §7($status)";
        }

        $message = LangManager::get("friends-list", ["list" => implode("\n", $formatted)]);
        $sender->sendMessage($message);
    }
}