<?php

namespace Akari_my\FriendsX\command\arguments;

use Akari_my\FriendsX\command\SubCommand;
use Akari_my\FriendsX\Main;
use Akari_my\FriendsX\manager\LangManager;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class settingsFriend implements SubCommand {

    public function execute(CommandSender $sender, array $args): void {
        if (!$sender instanceof Player) {
            return;
        }

        $plugin = Main::getInstance();
        $settings = $plugin->getSettingsManager();

        $playerName = $sender->getName();
        $player = strtolower($playerName);

        if (!isset($args[0])) {
            $receive = $settings->canReceiveRequests($player);
            $notify = $settings->canReceiveFriendNotifications($player);

            $on = LangManager::raw("settings-on");
            $off = LangManager::raw("settings-off");

            $sender->sendMessage(LangManager::get("settings-header"));
            $sender->sendMessage(LangManager::get("settings-receive-requests", [
                "value" => $receive ? $on : $off
            ]));
            $sender->sendMessage(LangManager::get("settings-notifications", [
                "value" => $notify ? $on : $off
            ]));
            $sender->sendMessage(LangManager::get("settings-usage"));
            return;
        }

        $sub = strtolower($args[0]);

        if ($sub === "togglerequests") {
            $new = $settings->toggleReceiveRequests($player);
            $sender->sendMessage(LangManager::get("settings-toggled-requests", [
                "value" => $new ? LangManager::raw("settings-on") : LangManager::raw("settings-off")
            ]));
        } elseif ($sub === "togglenotify" || $sub === "togglenotifications") {
            $new = $settings->toggleFriendNotifications($player);
            $sender->sendMessage(LangManager::get("settings-toggled-notifications", [
                "value" => $new ? LangManager::raw("settings-on") : LangManager::raw("settings-off")
            ]));
        } else {
            $sender->sendMessage(LangManager::get("settings-usage"));
        }
    }
}