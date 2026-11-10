<?php

namespace Workbench\App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;

class KitchenSink extends Page
{
    protected static ?string $slug = 'kitchen-sink';

    protected static ?string $navigationLabel = 'Kitchen Sink';

    protected static string|\UnitEnum|null $navigationGroup = 'Demonstração';

    /** @var array<string, mixed> */
    public array $data = [];

    public function mount(): void
    {
        $this->data = ['invalid' => 'abc'];

        $this->addError('data.invalid', 'Valor inválido');
    }

    public function getTitle(): string
    {
        return 'Kitchen Sink';
    }

    /**
     * @return array<Action|ActionGroup>
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('primary')->label('Primário'),
            Action::make('secondary')->label('Secundário')->outlined(),
            Action::make('tertiary')->label('Terciário')->link(),
            Action::make('danger')->label('Perigo')->color('danger'),
            Action::make('dangerSecondary')->label('Perigo secundário')->color('danger')->outlined(),
            Action::make('cancel')->label('Cancelar')->color('gray'),
            Action::make('modal')->label('Abrir modal')->requiresConfirmation(),
            Action::make('notify')
                ->label('Notificar')
                ->action(fn () => Notification::make()->title('Pedido salvo')->success()->persistent()->send()),
            ActionGroup::make([
                Action::make('groupOne')->label('Item um'),
                Action::make('groupTwo')->label('Item dois'),
            ]),
            Action::make('tooltip')->label('Com dica')->tooltip('Dica'),
        ];
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Campos')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')->label('Nome')->required()->helperText('Texto de ajuda do campo'),
                        Select::make('role')->label('Perfil')->options(['a' => 'Administrador', 'b' => 'Editor']),
                        Toggle::make('active')->label('Ativo'),
                        Checkbox::make('terms')->label('Aceito os termos'),
                        Radio::make('plan')->label('Plano')->options(['basic' => 'Básico', 'pro' => 'Profissional']),
                        DatePicker::make('date')->label('Data'),
                        Textarea::make('notes')->label('Observações'),
                        TextInput::make('invalid')->label('Campo inválido'),
                    ]),
                Tabs::make('Abas')
                    ->tabs([
                        Tab::make('Geral')->schema([Text::make('Conteúdo da aba geral')]),
                        Tab::make('Avançado')->schema([Text::make('Conteúdo da aba avançada')]),
                    ]),
                Wizard::make([
                    Step::make('Passo 1')->schema([TextInput::make('wizard_name')->label('Título')]),
                    Step::make('Passo 2')->schema([Textarea::make('wizard_notes')->label('Detalhes')]),
                ]),
                Section::make('Status')
                    ->schema([
                        Text::make('Pago')->badge()->color('success'),
                        Text::make('Pendente')->badge()->color('warning'),
                        Text::make('Cancelado')->badge()->color('danger'),
                        Text::make('Info')->badge()->color('info'),
                        Text::make('Rascunho')->badge()->color('gray'),
                        Text::make('Novo')->badge()->color('primary'),
                    ]),
            ]);
    }
}
