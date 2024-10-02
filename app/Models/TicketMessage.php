<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketMessage extends Model
{
    use HasFactory;

    protected $table = 'ticket_messages';

    protected $fillable = [
        'ticket_id',
        'chat_message',
        'status',
        'file_path',
        'reply_to',
        'added_by',
        'updated_by',
    ];

    // Define the relationship with Ticket (if needed)
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function added_by_name(){
        return $this->belongsTo(User::class, 'added_by', 'id');
    }

}
