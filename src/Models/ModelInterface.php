<?php
namespace App\Models;

/*
    Interface dasar untuk model.
    Setiap model minimal punya method save() dan findById().
*/

interface ModelInterface {
    public function save();
    public static function findById($id);
}