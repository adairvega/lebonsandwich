<?php

namespace lbs\command\model;

class Client extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'client';
    protected $primary_key = 'id';
    public $timestamps = true;

    public function getCommandes()
    {
        return $this->hasMany('lbs\command\model\Commande', 'client_id');
    }
}