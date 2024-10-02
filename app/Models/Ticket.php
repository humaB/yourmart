<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $table = 'tickets';

    protected $fillable = [
        'order_no',
        'ticket_type',
        'message',
        'expected_result',
        'file_path',
        'status',
        'added_by',
        'updated_by',
    ];

    public function added_by_name(){
        return $this->belongsTo(User::class, 'added_by', 'id');
    }

}
