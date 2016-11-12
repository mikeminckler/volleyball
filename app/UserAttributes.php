<?php

namespace App;

trait UserAttributes
{

    public function getFirstNameAttribute()
    {
        return $this->user->first_name;
    }

    public function getLastNameAttribute()
    {
        return $this->user->last_name;
    }

    public function getCommonFirstNameAttribute()
    {
        return $this->user->common_first_name;
    }

    public function getMiddleNameAttribute()
    {
        return $this->user->middle_name;
    }

	public function getFullNameLastFirstAttribute() {
        $name = $this->user->last_name;
        $name .= ', '.$this->user->first_name;
        if ($this->user->common_first_name) {
            $name .= ' ('.$this->user->common_first_name.')';
        }
        return $name;
    }

    public function getShortFullNameAttribute() {
        $name = substr($this->user->first_name, 0, 1).'. '.$this->user->last_name;
        return $name;
    }

	public function getFullNameAttribute() {
        $name = $this->user->first_name;
        if ($this->user->common_first_name) {
            $name .= ' ('.$this->user->common_first_name.')';
        }
        $name .= ' '.$this->user->last_name;
        return $name;
    }

	public function getEmailAttribute() {
        return $this->user->email;
    }
}
