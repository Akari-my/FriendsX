<?php

namespace Akari_my\FriendsX\libs\FormX;

use Closure;
use pocketmine\form\Form;
use pocketmine\player\Player;

class SimpleForm implements Form {

    private Closure $mainCallback;
    private string $title = "";
    private string $content = "";
    private array $buttons = [];
    private array $buttonCallbacks = [];

    public function __construct(?Closure $mainCallback = null) {
        $this->mainCallback = $mainCallback ?? function() {};
    }

    public function setTitle(string $title): void {
        $this->title = $title;
    }

    public function setContent(string $content): void {
        $this->content = $content;
    }

    public function addButton(string $text, ?string $imageType = null, ?string $imagePath = null): void {
        $button = ['text' => $text];
        if ($imageType !== null && $imagePath !== null) {
            $button['image'] = ['type' => $imageType, 'data' => $imagePath];
        }
        $this->buttons[] = $button;
    }

    public function addActionButton(string $text, Closure $callback, ?string $imageType = null, ?string $imagePath = null): void {
        $button = ['text' => $text];
        if ($imageType !== null && $imagePath !== null) {
            $button['image'] = ['type' => $imageType, 'data' => $imagePath];
        }

        $index = count($this->buttons);
        $this->buttons[] = $button;
        $this->buttonCallbacks[$index] = $callback;
    }

    public function sendTo(Player $player): void {
        $player->sendForm($this);
    }

    public function handleResponse(Player $player, $data): void {
        if ($data === null) return;

        if (isset($this->buttonCallbacks[$data])) {
            ($this->buttonCallbacks[$data])($player);
        } else {
            ($this->mainCallback)($player, $data);
        }
    }

    public function jsonSerialize(): mixed {
        return [
            'type' => 'form',
            'title' => $this->title,
            'content' => $this->content,
            'buttons' => $this->buttons
        ];
    }
}