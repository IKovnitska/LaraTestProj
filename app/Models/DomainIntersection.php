<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DomainIntersection extends Model
{
    use HasFactory;
    protected $table = 'domain_intersections';
    protected $fillable = [
        'excluded_target',
        'target_domain',
        'referring_domain',
        'rank',
        'backlinks',
    ];
    protected $primaryKey = 'id';
}
