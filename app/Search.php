<?php

namespace App;

use Illuminate\Support\Collection;

use App\User;
use App\Player;

class Search {

    protected $user;
    protected $player;

    public function __construct(
            User $user,
            Player $player
        )
    {
        $this->user = $user;
        $this->player = $player;
    }

    public function search($object, $input)
    {
        if (array_key_exists('terms', $input)) {
            $terms = $input['terms'];
            $terms = explode(' ', $terms);
            $objects_array = array();
            $objects = new Collection;

            foreach($terms as $search) {
                if (strlen($search) > 2) {

                    $results = $this->{$object}->search($search, $input);

                    foreach ($results as $result) {
                        if (array_key_exists($result->id, $objects_array)) {
                            $objects_array[$result->id] ++;
                        } else {
                            $objects_array[$result->id] = 1;
                        }
                    }
                }
            }

            if (count($objects_array) > 0) {
                $hit_count = max($objects_array);
                $filtered = array_filter($objects_array, function ($x) use ($hit_count) { return $x >= $hit_count; });
                $object_ids = array();
                foreach ($filtered as $item => $foo) {
                    $object_ids[] = $item;
                }
                $objects = $this->{$object}->whereIn('id', $object_ids)->orderBy('updated_at', 'desc')->limit(250)->get();
            }

            if ($objects->count()) {
                return $this->{$object}->searchResultsArray($objects, $input);
            } else {
                return $objects;
            }

        } 
    }

}
