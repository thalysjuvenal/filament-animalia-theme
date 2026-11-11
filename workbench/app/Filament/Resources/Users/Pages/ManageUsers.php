<?php

namespace Workbench\App\Filament\Resources\Users\Pages;

use Filament\Resources\Pages\ManageRecords;
use Workbench\App\Filament\Resources\Users\UserResource;

class ManageUsers extends ManageRecords
{
    protected static string $resource = UserResource::class;
}
