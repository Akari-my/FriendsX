<?php

namespace Akari_my\FriendsX\libs\FormX;

use Closure;
use pocketmine\form\Form;
use pocketmine\player\Player;

class CustomForm implements Form {

    private Closure $callback;
    private string $title = "";
    private array $elements = [];

    public function __construct(Closure $callback) {
        $this->callback = $callback;
    }

    public function setTitle(string $title): void {
        $this->title = $title;
    }

    public function addLabel(string $text): void {
        $this->elements[] = ['type' => 'label', 'text' => $text];
    }

    public function addInput(string $text, string $placeholder = "", string $default = ""): void {
        $this->elements[] = [
            'type' => 'input',
            'text' => $text,
            'placeholder' => $placeholder,
            'default' => $default
        ];
    }

    public function addToggle(string $text, bool $default = false): void {
        $this->elements[] = [
            'type' => 'toggle',
            'text' => $text,
            'default' => $default
        ];
    }

    public function addDropdown(string $text, array $options, int $default = 0): void {
        $this->elements[] = [
            'type' => 'dropdown',
            'text' => $text,
            'options' => $options,
            'default' => $default
        ];
    }

    public function addSlider(string $text, float $min, float $max, float $step = 1.0, float $default = 0): void {
        $this->elements[] = [
            'type' => 'slider',
            'text' => $text,
            'min' => $min,
            'max' => $max,
            'step' => $step,
            'default' => $default
        ];
    }

    public function sendTo(Player $player): void {
        $player->sendForm($this);
    }

    public function handleResponse(Player $player, $data): void {
        if (!is_array($data)) return;
        ($this->callback)($player, $data);
    }

    public function jsonSerialize(): mixed {
        return [
            'type' => 'custom_form',
            'title' => $this->title,
            'content' => $this->elements
        ];
    }
}