<?php

namespace Akari_my\FriendsX\command\arguments;

use Akari_my\FriendsX\command\SubCommand;
use Akari_my\FriendsX\Main;
use Akari_my\FriendsX\manager\LangManager;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class acceptFriend implements SubCommand {

    public function execute(CommandSender $sender, array $args): void {
        if (!$sender instanceof Player) return;

        if (!isset($args[0])) {
            $sender->sendMessage("§cUse: /friend accept <player>");
            return;
        }

        $target = strtolower($args[0]);
        $player = strtolower($sender->getName());

        $manager = Main::getInstance()->getFriendsManager();
        $requests = Main::getInstance()->getRequestManager();

        if (!$requests->hasRequest($player, $target)) {
            $sender->sendMessage(LangManager::get("request-expired", ["target" => $target]));
            return;
        }

        $requests->removeRequest($player, $target);
        $manager->addFriend($player, $target);
        $manager->addFriend($target, $player);

        $sender->sendMessage(LangManager::get("request-accepted", ["target" => $target]));

        $targetPlayer = Main::getInstance()->getServer()->getPlayerExact($target);
        if ($targetPlayer !== null && $targetPlayer->isOnline()) {
            $targetPlayer->sendMessage(LangManager::get("request-accepted-notify", [
                "player" => $sender->getName()
            ]));
        }
    }
}