<?php

namespace lbs\command\model;

class Commande extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'commande';
    protected $primary_key = 'id';
    protected $timestamp = true;

    public $incrementing = false;
    public $keyType = 'string';

    public function commandeItems()
    {
        return $this->hasMany('lbs\command\model\Item', 'command_id');
    }
}