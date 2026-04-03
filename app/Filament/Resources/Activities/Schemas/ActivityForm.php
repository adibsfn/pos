<?php

namespace App\Filament\Resources\Activities\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ActivityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Activity Detail')
                    ->schema([

                        Grid::make(2)->schema([

                            Placeholder::make('user')
                                ->label('👤 User')
                                ->content(fn ($record) => $record->causer?->name ?? '-'),

                            Placeholder::make('event')
                                ->label('⚡ Event')
                                ->content(fn ($record) => $record->event),

                            Placeholder::make('subject')
                                ->label('📦 Subject')
                                ->content(fn ($record) => class_basename($record->subject_type)),

                            Placeholder::make('ip')
                                ->label('🌐 IP Address')
                                ->content(fn ($record) => $record->properties['ip'] ?? '-'),

                            Placeholder::make('device')
                                ->label('💻 Device')
                                ->content(fn ($record) => Str::limit($record->properties['user_agent'] ?? '-', 1000))
                                ->columnSpanFull(),

                            Placeholder::make('created_at')
                                ->label('🕒 Waktu')
                                ->content(fn ($record) => $record->created_at->diffForHumans()),

                        ]),
                    ]),

                Section::make('Perubahan Data')
                    ->schema([

                Placeholder::make('changes')
                    ->label('')
                    ->content(function ($record) {

                        $old = $record->properties['old'] ?? [];
                        $new = $record->properties['attributes'] ?? [];

                        if (!$old && !$new) {
                            return 'Tidak ada perubahan data';
                        }

                        $output = '';

                        foreach ($new as $key => $value) {

                            $oldValue = $old[$key] ?? '-';

                            // 🔥 DETECT FIELD WAKTU
                            if (str_ends_with($key, '_at')) {

                                // FORMAT NEW VALUE
                                if ($value && $value !== '-') {
                                    try {
                                        $value = Carbon::parse($value)
                                            ->timezone('Asia/Jakarta')
                                            ->translatedFormat('d M Y H:i') . ' WIB';
                                    } catch (\Exception $e) {}
                                }

                                // FORMAT OLD VALUE
                                if ($oldValue && $oldValue !== '-') {
                                    try {
                                        $oldValue = Carbon::parse($oldValue)
                                            ->timezone('Asia/Jakarta')
                                            ->translatedFormat('d M Y H:i') . ' WIB';
                                    } catch (\Exception $e) {}
                                }
                            }

                            if ($oldValue != $value) {
                                $output .= "
                                <div style='margin-bottom:10px'>
                                    <strong style='text-transform:capitalize'>{$key}</strong><br>
                                    <span style='color:#ef4444'>Old: {$oldValue}</span><br>
                                    <span style='color:#22c55e'>New: {$value}</span>
                                </div>
                                ";
                            }
                        }

                        return new \Illuminate\Support\HtmlString($output);
                    })
                    ->columnSpanFull(),

                    ]),
            ]);
    }
}
