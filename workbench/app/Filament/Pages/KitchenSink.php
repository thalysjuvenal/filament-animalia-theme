<?php

namespace Workbench\App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Callout;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class KitchenSink extends Page
{
    protected static ?string $slug = 'kitchen-sink';

    protected static ?string $navigationLabel = 'Kitchen Sink';

    protected static string|\UnitEnum|null $navigationGroup = 'Demonstração';

    /** @var array<string, mixed> */
    public array $data = [];

    public function mount(): void
    {
        $this->data = ['invalid' => 'abc', 'billing' => 'yearly', 'billing_grouped' => 'yearly', 'plan' => 'basic', 'news' => true, 'notifications' => true,
            'items' => [['title' => 'Primeiro item'], ['title' => 'Segundo item']],
            'blocks' => [['type' => 'text', 'data' => ['body' => 'Bloco de texto']]],
            'meta' => [['key' => 'cor', 'value' => 'azul'], ['key' => 'tamanho', 'value' => 'médio']],
        ];

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
            Action::make('primary')->label('Primário')->icon(Heroicon::Plus),
            Action::make('secondary')->label('Secundário')->outlined(),
            Action::make('tertiary')->label('Terciário')->link(),
            Action::make('danger')->label('Perigo')->color('danger'),
            Action::make('dangerSecondary')->label('Perigo secundário')->color('danger')->outlined(),
            Action::make('successSecondary')->label('Sucesso secundário')->color('success')->outlined(),
            Action::make('customSecondary')->label('Marca secundária')->color('secondary')->outlined()->icon(Heroicon::Star),
            Action::make('cancel')->label('Cancelar')->color('gray'),
            Action::make('modal')
                ->label('Abrir modal')
                ->requiresConfirmation()
                ->schema([TextInput::make('motivo')->label('Motivo')])
                ->action(fn () => null),
            Action::make('notify')
                ->label('Notificar')
                ->action(fn () => Notification::make()->title('Pedido salvo')->success()->persistent()->send()),
            ActionGroup::make([
                Action::make('groupOne')->label('Item um')->icon(Heroicon::PencilSquare),
                Action::make('groupTwo')->label('Item dois')->icon(Heroicon::Trash)->color('danger'),
            ]),
            ActionGroup::make([
                Action::make('bgOne')->label('Dia'),
                Action::make('bgTwo')->label('Semana'),
            ])->buttonGroup(),
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
                        Toggle::make('notifications')->label('Notificações'),
                        Checkbox::make('terms')->label('Aceito os termos'),
                        Checkbox::make('news')->label('Receber novidades'),
                        Radio::make('plan')->label('Plano')->options(['basic' => 'Básico', 'pro' => 'Profissional']),
                        ToggleButtons::make('billing')->label('Cobrança')->options(['monthly' => 'Mensal', 'yearly' => 'Anual', 'lifetime' => 'Vitalício'])->icons(['monthly' => Heroicon::Calendar, 'yearly' => Heroicon::CheckCircle])->inline(),
                        ToggleButtons::make('billing_grouped')->label('Cobrança agrupada')->options(['monthly' => 'Mensal', 'yearly' => 'Anual', 'lifetime' => 'Vitalício'])->inline()->grouped(),
                        DatePicker::make('date')->label('Data'),
                        Textarea::make('notes')->label('Observações'),
                        TextInput::make('invalid')->label('Campo inválido'),
                    ]),
                Tabs::make('Abas')
                    ->tabs([
                        Tab::make('Geral')->schema([Text::make('Conteúdo da aba geral')]),
                        Tab::make('Avançado')->schema([Text::make('Conteúdo da aba avançada')]),
                    ]),
                Tabs::make('Abas verticais')
                    ->vertical()
                    ->tabs([
                        Tab::make('Perfil')->schema([Text::make('Conteúdo da aba perfil')]),
                        Tab::make('Segurança')->schema([Text::make('Conteúdo da aba segurança')]),
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
                Section::make('Preferências')
                    ->description('Seção lateral compacta')
                    ->aside()
                    ->compact()
                    ->schema([
                        Fieldset::make('Contato')
                            ->schema([
                                TextInput::make('contact_email')->label('E-mail'),
                                TextInput::make('contact_phone')->label('Telefone'),
                            ]),
                    ]),
                Section::make('Estruturas')
                    ->schema([
                        Repeater::make('items')
                            ->label('Itens')
                            ->schema([TextInput::make('title')->label('Título')])
                            ->addActionLabel('Adicionar item'),
                        Builder::make('blocks')
                            ->label('Blocos')
                            ->blocks([
                                Block::make('text')->label('Texto')->schema([TextInput::make('body')->label('Conteúdo')]),
                            ])
                            ->addActionLabel('Adicionar bloco'),
                        KeyValue::make('meta')->label('Metadados'),
                        Callout::make('Pedido aprovado')->description('O pagamento foi confirmado.')->success(),
                        Callout::make('Falha no envio')->description('Não foi possível enviar o e-mail.')->danger(),
                    ]),
            ]);
    }
}
