<?php

declare(strict_types=1);

use Tavp\Core\Database\Migrations\Migration;
use Tavp\Core\Database\Migrations\SchemaBuilder;

/**
 * Single table for all content types. Custom fields live in the JSON `data`
 * column so new content types need no schema change.
 */
return new class extends Migration
{
    public function up(SchemaBuilder $schema): void
    {
        $schema->createTable('contents', function (SchemaBuilder\TableDefinition $table) use ($schema) {
            $table->add($schema->column('id', 'bigInteger', ['identity' => true, 'primary' => true]));
            $table->add($schema->column('type', 'string', ['size' => 64]));
            $table->add($schema->column('slug', 'string', ['size' => 191]));
            $table->add($schema->column('status', 'string', ['size' => 32, 'default' => 'draft']));
            $table->add($schema->column('data', 'text', ['null' => true]));
            $table->add($schema->column('author_id', 'bigInteger', ['null' => true]));
            $table->add($schema->column('published_at', 'timestamp', ['null' => true]));
            $table->add($schema->column('created_at', 'timestamp'));
            $table->add($schema->column('updated_at', 'timestamp'));
        });
    }

    public function down(SchemaBuilder $schema): void
    {
        $schema->dropTable('contents');
    }
};
