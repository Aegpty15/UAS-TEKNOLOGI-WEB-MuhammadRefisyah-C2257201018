<?php

namespace App\Models;

use CodeIgniter\Model;

class DML extends Model
{
    protected $db; // Instance koneksi database

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function dataRead($tableName, $where = null)
    {
        $builder = $this->db->table($tableName);
        if ($where) {
            $builder->where($where);
        }
        return $builder->get()->getResultArray(); // Mengembalikan array asosiatif
        // Atau return $builder->get()->getResultObject(); jika Anda lebih suka object
    }

    public function dataInsert($tableName, $data)
    {
        $builder = $this->db->table($tableName);
        return $builder->insert($data);
    }

    public function dataUpdate($tableName, $data, $where)
    {
        $builder = $this->db->table($tableName);
        $builder->where($where);
        return $builder->update($data);
    }

    public function dataDelete($tableName, $where)
    {
        $builder = $this->db->table($tableName);
        $builder->where($where);
        return $builder->delete();
    }
}