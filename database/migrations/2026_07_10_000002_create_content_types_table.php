<?php

declare(strict_types=1);

use Tavp\Core\Database\Migrations\Migration;
use Tavp\Core\Database\Migrations\SchemaBuilder;

/**
 * Persisted content-type definitions (BREAD) created from the admin UI.
 * Config-defined types are merged with these at runtime.
 */
return new class extends Migration
{
    public function up(SchemaBuilder $schema): void
    {
        $schema->createTable('content_types', function (SchemaBuilder\TableDefinition $table) use ($schema) {
            $table->add($schema->column('id', 'bigInteger', ['identity' => true, 'primary' => true]));
            $table->add($schema->column('name', 'string', ['size' => 64]));
            $table->add($schema->column('label', 'string', ['size' => 128]));
            $table->add($schema->column('singular', 'string', ['size' => 128]));
            $table->add($schema->column('icon', 'string', ['size' => 64, 'default' => 'document']));
            $table->add($schema->column('route', 'string', ['size' => 191, 'default' => '/{slug}']));
            $table->add($schema->column('fields', 'text'));
            $table->add($schema->column('created_at', 'timestamp'));
            $table->add($schema->column('updated_at', 'timestamp'));
        });
    }

    public function down(SchemaBuilder $schema): void
    {
        $schema->dropTable('content_types');
    }
};
