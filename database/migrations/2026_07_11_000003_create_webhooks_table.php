<?php

declare(strict_types=1);

use Tavp\Core\Database\Migrations\Migration;
use Tavp\Core\Database\Migrations\SchemaBuilder;

/**
 * Webhooks — fire an HTTP POST to a target URL on CMS events.
 */
return new class extends Migration
{
    public function up(SchemaBuilder $schema): void
    {
        $schema->createTable('webhooks', function (SchemaBuilder\TableDefinition $table) use ($schema) {
            $table->add($schema->column('id', 'bigInteger', ['identity' => true, 'primary' => true]));
            $table->add($schema->column('url', 'string', ['size' => 512]));
            $table->add($schema->column('events', 'text')); // comma-separated
            $table->add($schema->column('secret', 'string', ['size' => 64, 'null' => true]));
            $table->add($schema->column('active', 'boolean', ['default' => true]));
            $table->add($schema->column('created_at', 'timestamp'));
            $table->add($schema->column('updated_at', 'timestamp'));
        });

        $schema->createTable('webhook_deliveries', function (SchemaBuilder\TableDefinition $table) use ($schema) {
            $table->add($schema->column('id', 'bigInteger', ['identity' => true, 'primary' => true]));
            $table->add($schema->column('webhook_id', 'bigInteger'));
            $table->add($schema->column('event', 'string', ['size' => 64]));
            $table->add($schema->column('status', 'integer', ['null' => true]));
            $table->add($schema->column('response', 'text', ['null' => true]));
            $table->add($schema->column('created_at', 'timestamp'));
            $table->foreignKey('webhook_id', 'webhooks', 'id', 'CASCADE', 'CASCADE');
        });
    }

    public function down(SchemaBuilder $schema): void
    {
        $schema->dropTable('webhook_deliveries');
        $schema->dropTable('webhooks');
    }
};
