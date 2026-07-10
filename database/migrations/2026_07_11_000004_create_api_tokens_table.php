<?php

declare(strict_types=1);

use Tavp\Core\Database\Migrations\Migration;
use Tavp\Core\Database\Migrations\SchemaBuilder;

/**
 * API tokens — bearer tokens for the headless REST API.
 */
return new class extends Migration
{
    public function up(SchemaBuilder $schema): void
    {
        $schema->createTable('api_tokens', function (SchemaBuilder\TableDefinition $table) use ($schema) {
            $table->add($schema->column('id', 'bigInteger', ['identity' => true, 'primary' => true]));
            $table->add($schema->column('name', 'string', ['size' => 191]));
            $table->add($schema->column('token', 'string', ['size' => 64]));
            $table->add($schema->column('abilities', 'text', ['null' => true])); // e.g. "content.read,content.write"
            $table->add($schema->column('last_used_at', 'timestamp', ['null' => true]));
            $table->add($schema->column('created_at', 'timestamp'));
        });
    }

    public function down(SchemaBuilder $schema): void
    {
        $schema->dropTable('api_tokens');
    }
};
