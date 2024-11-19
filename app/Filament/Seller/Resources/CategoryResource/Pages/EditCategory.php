<?php

namespace App\Filament\Seller\Resources\CategoryResource\Pages;

use App\Filament\Seller\Resources\CategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\Category;
use App\Models\SellerProduct;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    public function getTitle(): string 
    {
        return 'Edit Category "' . $this->record->name . '"';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->visible(function (Category $record): bool {
                    return !Category::where('parent_id', $record->id)->exists() && 
                               !SellerProduct::where('category_id', $record->id)->exists();
                }),
        ];
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }
}
