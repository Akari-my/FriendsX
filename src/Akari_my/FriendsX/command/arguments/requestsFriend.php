<?php

namespace Akari_my\FriendsX\command\arguments;

use Akari_my\FriendsX\command\SubCommand;
use Akari_my\FriendsX\Main;
use Akari_my\FriendsX\manager\LangManager;
use Akari_my\FriendsX\util\TimeUtils;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class requestsFriend implements SubCommand {

    public function execute(CommandSender $sender, array $args): void {
        if (!$sender instanceof Player) {
            return;
        }

        $plugin = Main::getInstance();
        $requestsManager = $plugin->getRequestManager();

        $playerName = $sender->getName();
        $player = strtolower($playerName);

        $requests = $requestsManager->getRequests($player);

        if (empty($requests)) {
            $sender->sendMessage(LangManager::get("no-requests"));
            return;
        }

        $lines = [];
        foreach ($requests as $from) {
            $remaining = $requestsManager->getRemainingCooldown($player, $from);
            $timeString = TimeUtils::formatDuration($remaining);
            $info = LangManager::raw("request-expires-in", ["time" => $timeString]);
            $lines[] = "§7- §e" . $from . " §7(" . $info . "§7)";
        }

        $sender->sendMessage(LangManager::get("requests-list", [
            "list" => implode("\n", $lines)
        ]));
    }
}