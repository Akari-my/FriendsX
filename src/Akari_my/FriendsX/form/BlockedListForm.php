<?php

namespace Akari_my\FriendsX\form;

use Akari_my\FriendsX\command\arguments\unblockFriend;
use Akari_my\FriendsX\libs\FormX\SimpleForm;
use Akari_my\FriendsX\Main;
use Akari_my\FriendsX\manager\LangManager;
use pocketmine\player\Player;

class BlockedListForm {

    public static function open(Player $player): void {
        $plugin = Main::getInstance();
        if (!$plugin->areFormsEnabled()) {
            return;
        }

        $blockManager = $plugin->getBlockManager();
        $playerName = $player->getName();
        $blocked = array_values($blockManager->getBlockedList($playerName));

        $form = new SimpleForm(function (Player $player, ?int $data) use ($blocked): void {
            if ($data === null) {
                MainForm::open($player);
                return;
            }

            if (empty($blocked)) {
                if ($data === 0) {
                    MainForm::open($player);
                }
                return;
            }

            if (!isset($blocked[$data])) {
                return;
            }

            $target = $blocked[$data];
            (new unblockFriend())->execute($player, [$target]);
            BlockedListForm::open($player);
        });

        $form->setTitle(LangManager::raw("ui-blocked-title"));

        if (empty($blocked)) {
            $form->setContent(LangManager::raw("ui-blocked-empty"));
            $form->addButton(LangManager::raw("ui-button-back"));
        } else {
            $form->setContent(LangManager::raw("ui-blocked-content"));
            foreach ($blocked as $name) {
                $label = LangManager::raw("ui-blocked-entry", ["name" => $name]);
                $form->addButton($label);
            }
        }

        $form->sendTo($player);
    }

}