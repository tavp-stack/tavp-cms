<?php

declare(strict_types=1);

use Tavp\Core\Database\Migrations\Migration;
use Tavp\Core\Database\Migrations\SchemaBuilder;

return new class extends Migration
{
    public function up(SchemaBuilder $schema): void
    {
        $schema->createTable('redirects', function (SchemaBuilder\TableDefinition $table) {
            $table->add($schema->column('id', 'bigInteger', ['identity' => true, 'primary' => true]));
            $table->add($schema->column('from_url', 'string', ['size' => 500]));
            $table->add($schema->column('to_url', 'string', ['size' => 500]));
            $table->add($schema->column('status_code', 'integer'));
            $table->add($schema->column('is_active', 'boolean'));
            $table->add($schema->column('is_regex', 'boolean'));
            $table->add($schema->column('hits', 'integer'));
            $table->add($schema->column('last_hit_at', 'timestamp', ['null' => true]));
            $table->add($schema->column('created_at', 'timestamp'));
            $table->add($schema->column('updated_at', 'timestamp'));
        });

        $schema->addIndex('redirects', ['from_url'], 'redirects_from_index');
        $schema->addIndex('redirects', ['is_active'], 'redirects_active_index');
    }

    public function down(SchemaBuilder $schema): void
    {
        $schema->dropTable('redirects');
    }
};
