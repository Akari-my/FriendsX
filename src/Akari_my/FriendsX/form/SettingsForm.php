<?php

namespace Akari_my\FriendsX\form;

use Akari_my\FriendsX\libs\FormX\SimpleForm;
use Akari_my\FriendsX\Main;
use Akari_my\FriendsX\manager\LangManager;
use pocketmine\player\Player;

class SettingsForm {

    public static function open(Player $player): void {
        $plugin = Main::getInstance();
        if (!$plugin->areFormsEnabled()) {
            return;
        }

        $settings = $plugin->getSettingsManager();
        $playerLower = strtolower($player->getName());

        $receive = $settings->canReceiveRequests($playerLower);
        $notify = $settings->canReceiveFriendNotifications($playerLower);

        $receiveText = $receive ? LangManager::raw("settings-on") : LangManager::raw("settings-off");
        $notifyText = $notify ? LangManager::raw("settings-on") : LangManager::raw("settings-off");

        $form = new SimpleForm(function (Player $player, ?int $data): void {
            if ($data === null) {
                MainForm::open($player);
                return;
            }

            $plugin = Main::getInstance();
            $settings = $plugin->getSettingsManager();
            $playerLower = strtolower($player->getName());

            if ($data === 0) {
                $new = $settings->toggleReceiveRequests($playerLower);
                $player->sendMessage(LangManager::get("settings-toggled-requests", [
                    "value" => $new ? LangManager::raw("settings-on") : LangManager::raw("settings-off")
                ]));
                SettingsForm::open($player);
            } elseif ($data === 1) {
                $new = $settings->toggleFriendNotifications($playerLower);
                $player->sendMessage(LangManager::get("settings-toggled-notifications", [
                    "value" => $new ? LangManager::raw("settings-on") : LangManager::raw("settings-off")
                ]));
                SettingsForm::open($player);
            } elseif ($data === 2) {
                MainForm::open($player);
            }
        });

        $form->setTitle(LangManager::raw("ui-settings-title"));
        $content = LangManager::raw("ui-settings-content", [
            "requests" => $receiveText,
            "notifications" => $notifyText
        ]);
        $form->setContent($content);
        $form->addButton(LangManager::raw("ui-settings-toggle-requests", ["value" => $receiveText]));
        $form->addButton(LangManager::raw("ui-settings-toggle-notifications", ["value" => $notifyText]));
        $form->addButton(LangManager::raw("ui-button-back"));

        $form->sendTo($player);
    }

}