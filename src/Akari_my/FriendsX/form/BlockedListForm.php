<?php

namespace Akari_my\FriendsX\form;

use Akari_my\FriendsX\command\arguments\unblockFriend;
use Akari_my\FriendsX\libs\FormX\SimpleForm;
use Akari_my\FriendsX\Main;
use Akari_my\FriendsX\manager\LangManager;
use pocketmine\player\Player;

class BlockedListForm {

    private const PER_PAGE = 10;

    public static function open(Player $player, int $page = 0): void {
        $plugin = Main::getInstance();
        if (!$plugin->areFormsEnabled()) return;

        $blockManager = $plugin->getBlockManager();
        $playerName = $player->getName();
        $blocked = array_values($blockManager->getBlockedList($playerName));
        $total = count($blocked);
        $maxPage = (int)ceil($total / self::PER_PAGE) - 1;
        if ($maxPage < 0) $maxPage = 0;
        if ($page > $maxPage) $page = $maxPage;
        $start = $page * self::PER_PAGE;
        $slice = array_slice($blocked, $start, self::PER_PAGE);

        $form = new SimpleForm(function (Player $player, ?int $data) use ($blocked, $page, $maxPage, $total): void {
            if ($data === null) {
                MainForm::open($player);
                return;
            }

            $friendCount = count(array_slice($blocked, $page * self::PER_PAGE, self::PER_PAGE));
            $idx = 0;

            if ($total > 0 && $data < $friendCount) {
                $globalIdx = $page * self::PER_PAGE + $data;
                if (isset($blocked[$globalIdx])) {
                    (new unblockFriend())->execute($player, [$blocked[$globalIdx]]);
                    BlockedListForm::open($player);
                }
                return;
            }
            $idx = $friendCount;

            if ($page > 0 && $data === $idx) {
                BlockedListForm::open($player, $page - 1);
                return;
            }
            if ($page > 0) $idx++;

            if ($page < $maxPage && $data === $idx) {
                BlockedListForm::open($player, $page + 1);
                return;
            }

            MainForm::open($player);
        });

        $title = LangManager::raw("ui-blocked-title");
        if ($total > 0) $title .= " §7(" . ($page + 1) . "/" . ($maxPage + 1) . ")";
        $form->setTitle($title);

        if ($total === 0) {
            $form->setContent(LangManager::raw("ui-blocked-empty"));
            $form->addButton(LangManager::raw("ui-button-back"));
        } else {
            $form->setContent(LangManager::raw("ui-blocked-content"));
            foreach ($slice as $name) {
                $form->addButton(LangManager::raw("ui-blocked-entry", ["name" => $name]));
            }
            if ($page > 0) $form->addButton(LangManager::raw("ui-prev-page"));
            if ($page < $maxPage) $form->addButton(LangManager::raw("ui-next-page"));
            $form->addButton(LangManager::raw("ui-button-back"));
        }

        $form->sendTo($player);
    }
}
