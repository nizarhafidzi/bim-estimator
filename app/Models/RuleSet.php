<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RuleSet extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'name',
        'description',
    ];

    // Relasi: RuleSet milik Project
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // Relasi: RuleSet punya banyak Aturan Detail
    public function rules()
    {
        return $this->hasMany(ComplianceRule::class);
    }
}