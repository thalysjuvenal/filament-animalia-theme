<?php

namespace ThalysJuvenal\FilamentAnimalia\Enums;

enum RestyleGroup: string
{
    case Buttons = 'buttons';
    case Forms = 'forms';
    case Tables = 'tables';
    case Navigation = 'navigation';
    case Overlays = 'overlays';

    public function assetId(): string
    {
        return 'animalia-'.$this->value;
    }

    public function stylesheetPath(): string
    {
        return 'resources/css/groups/'.$this->value.'.css';
    }
}
