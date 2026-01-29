<?php

namespace Akari_my\FriendsX\form;

use Akari_my\FriendsX\libs\FormX\SimpleForm;
use Akari_my\FriendsX\Main;
use Akari_my\FriendsX\manager\LangManager;
use pocketmine\player\Player;

class MainForm {

    public static function open(Player $player): void {
        $plugin = Main::getInstance();
        if (!$plugin->areFormsEnabled()) {
            return;
        }

        $form = new SimpleForm(function (Player $player, ?int $data) {
            if ($data === null) {
                return;
            }
            switch ($data) {
                case 0:
                    FriendsListForm::open($player);
                    break;
                case 1:
                    RequestsForm::open($player);
                    break;
                case 2:
                    SettingsForm::open($player);
                    break;
                case 3:
                    BlockedListForm::open($player);
                    break;
            }
        });

        $form->setTitle(LangManager::raw("ui-main-title"));
        $form->setContent(LangManager::raw("ui-main-content"));
        $form->addButton(LangManager::raw("ui-main-button-friends"));
        $form->addButton(LangManager::raw("ui-main-button-requests"));
        $form->addButton(LangManager::raw("ui-main-button-settings"));
        $form->addButton(LangManager::raw("ui-main-button-blocked"));

        $form->sendTo($player);
    }
}