<?php

namespace Akari_my\FriendsX\libs\FormX;

use Closure;
use pocketmine\form\Form;
use pocketmine\player\Player;

class ModalForm implements Form {

    private Closure $callback;
    private string $title = "";
    private string $content = "";
    private string $button1 = "Yes";
    private string $button2 = "No";

    public function __construct(Closure $callback) {
        $this->callback = $callback;
    }

    public function setTitle(string $title): void {
        $this->title = $title;
    }

    public function setContent(string $content): void {
        $this->content = $content;
    }

    public function setButton1(string $text): void {
        $this->button1 = $text;
    }

    public function setButton2(string $text): void {
        $this->button2 = $text;
    }

    public function sendTo(Player $player): void {
        $player->sendForm($this);
    }

    public function handleResponse(Player $player, $data): void {
        if (!is_bool($data)) return;
        ($this->callback)($player, $data);
    }

    public function jsonSerialize(): mixed {
        return [
            'type' => 'modal',
            'title' => $this->title,
            'content' => $this->content,
            'button1' => $this->button1,
            'button2' => $this->button2
        ];
    }
}