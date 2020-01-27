<?php
namespace lbs\command;

class Client extends \Illuminate\Database\Eloquent\Model {
	protected $table = 'client';
	protected $primary_key = 'id';
	protected $timestamps = true;
}