<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class ProductObserver
{


    public function updated(Product $product): void
    {
        if ($product->getOriginal('stock') === $product->stock) {
            return;
        }

        if ($product->stock <= $product->minimum_stock) {

            $users = User::where('id', 1)->get();

            foreach ($users as $user) {
                $user->notify(
                    Notification::make()
                        ->title('Stok Menipis ⚠️')
                        ->body("{$product->nama} tersisa {$product->stock}")
                        ->warning()
                        ->actions([
                            Action::make('Lihat')
                                ->url('/admin/products')
                                ->button(),
                        ])
                        ->toDatabase()
                );
            }
        }
    }
}
