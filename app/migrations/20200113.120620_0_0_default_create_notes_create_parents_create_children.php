<?php

namespace Migration;

use Spiral\Migrations\Migration;

class OrmDefaultB2dff0f0445524b45df595d8c8640367 extends Migration
{
    protected const DATABASE = 'default';

    public function up()
    {
        $this->table('notes')
            ->addColumn('id', 'primary', [
                'nullable' => false,
                'default'  => null
            ])
            ->addColumn('text', 'string', [
                'nullable' => false,
                'default'  => null
            ])
            ->addColumn('created_at', 'timestamp', [
                'nullable' => false,
                'default'  => null
            ])
            ->addColumn('updated_at', 'timestamp', [
                'nullable' => true,
                'default'  => null
            ])
            ->addColumn('parent_id', 'integer', [
                'nullable' => false,
                'default'  => null
            ])
            ->setPrimaryKeys(["id"])
            ->create();
        
        $this->table('parents')
            ->addColumn('version', 'primary', [
                'nullable' => false,
                'default'  => null
            ])
            ->addColumn('id', 'integer', [
                'nullable' => false,
                'default'  => null
            ])
            ->addColumn('name', 'string', [
                'nullable' => false,
                'default'  => null
            ])
            ->addColumn('created_at', 'timestamp', [
                'nullable' => false,
                'default'  => null
            ])
            ->addColumn('updated_at', 'timestamp', [
                'nullable' => true,
                'default'  => null
            ])
            ->addColumn('deleted_at', 'timestamp', [
                'nullable' => true,
                'default'  => null
            ])
            ->addColumn('child_id', 'integer', [
                'nullable' => false,
                'default'  => null
            ])
            ->setPrimaryKeys(["version"])
            ->create();
        
        $this->table('children')
            ->addColumn('version', 'primary', [
                'nullable' => false,
                'default'  => null
            ])
            ->addColumn('id', 'integer', [
                'nullable' => false,
                'default'  => null
            ])
            ->addColumn('name', 'string', [
                'nullable' => false,
                'default'  => null
            ])
            ->addColumn('created_at', 'timestamp', [
                'nullable' => false,
                'default'  => null
            ])
            ->addColumn('updated_at', 'timestamp', [
                'nullable' => true,
                'default'  => null
            ])
            ->addColumn('deleted_at', 'timestamp', [
                'nullable' => true,
                'default'  => null
            ])
            ->setPrimaryKeys(["version"])
            ->create();
    }

    public function down()
    {
        $this->table('children')->drop();
        
        $this->table('parents')->drop();
        
        $this->table('notes')->drop();
    }
}
