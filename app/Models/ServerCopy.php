<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class ServerCopy extends Model
{
    protected $table = 'server_copies';

    protected $fillable = [
        'nid',
        'dob',
        'name',
        'name_en',
        'pin',
        'father',
        'father_nid',
        'mother',
        'mother_nid',
        'spouse',
        'blood_group',
        'gender',
        'birth_place',
        'religion',
        'occupation',
        'mobile',
        'voter_no',
        'voter_area',
        'voter_area_code',
        'sl_no',
        'pre_address_line',
        'per_address_line',
        'photo',
        'raw_data',
    ];

    protected $casts = [
        'raw_data'       => 'array',
        'voter_area_code'=> 'integer',
        'sl_no'          => 'integer',
    ];

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeByNidAndDob($query, string $nid, string $dob)
    {
        return $query->where('nid', $nid)->where('dob', $dob);
    }
}
