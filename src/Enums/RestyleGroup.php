<?php

namespace ThalysJuvenal\FilamentAnimalia\Enums;

enum RestyleGroup: string
{
    case Buttons = 'buttons';

    public function assetId(): string
    {
        return 'animalia-'.$this->value;
    }

    public function stylesheetPath(): string
    {
        return 'resources/css/groups/'.$this->value.'.css';
    }
}
