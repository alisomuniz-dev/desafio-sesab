<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Address extends Model
{
    use HasFactory;

    protected $fillable = ['logradouro', 'cep'];

    /**
     * Um endereço pode pertencer a vários usuários (N:N).
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
