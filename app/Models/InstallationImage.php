<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstallationImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'installation_detail_id',
        'image_path',
        'uploaded_by',
    ];

    public function installationDetail()
    {
        return $this->belongsTo(InstallationDetail::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
