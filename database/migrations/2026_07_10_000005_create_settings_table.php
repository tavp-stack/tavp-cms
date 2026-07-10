<?php

declare(strict_types=1);

use Tavp\Core\Database\Migrations\Migration;
use Tavp\Core\Database\Migrations\SchemaBuilder;

/**
 * Key/value site settings (site title, logo, per-group config).
 */
return new class extends Migration
{
    public function up(SchemaBuilder $schema): void
    {
        $schema->createTable('settings', function (SchemaBuilder\TableDefinition $table) use ($schema) {
            $table->add($schema->column('id', 'bigInteger', ['identity' => true, 'primary' => true]));
            $table->add($schema->column('group', 'string', ['size' => 64, 'default' => 'general']));
            $table->add($schema->column('key', 'string', ['size' => 128]));
            $table->add($schema->column('value', 'text', ['null' => true]));
            $table->add($schema->column('type', 'string', ['size' => 32, 'default' => 'text']));
            $table->add($schema->column('created_at', 'timestamp'));
            $table->add($schema->column('updated_at', 'timestamp'));
        });
    }

    public function down(SchemaBuilder $schema): void
    {
        $schema->dropTable('settings');
    }
};
