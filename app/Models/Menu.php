<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $nama
 * @property int $harga
 * @property string $kategori
 * @property int $rasa_manis
 * @property string $icon
 * @property string|null $image
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Menu extends Model
{
    /** @var list<string> */
    protected $fillable = [
        'nama',
        'harga',
        'kategori',
        'rasa_manis',
        'icon',
        'image',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'harga' => 'integer',
            'rasa_manis' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
