<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgentAuditLogModel extends Model
{
    protected $table = 'agent_audit_logs';
    protected $guarded = [];
    public $timestamps = false;
    protected $casts = ['payload' => 'array', 'before' => 'array'];
}
