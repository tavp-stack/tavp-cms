<?php

declare(strict_types=1);

use Tavp\Core\Database\Migrations\Migration;
use Tavp\Core\Database\Migrations\SchemaBuilder;

/**
 * Taxonomy terms: categories (hierarchical) and tags (flat).
 * Stored in one table; the "type" column distinguishes them.
 */
return new class extends Migration
{
    public function up(SchemaBuilder $schema): void
    {
        $schema->createTable('taxonomy_terms', function (SchemaBuilder\TableDefinition $table) use ($schema) {
            $table->add($schema->column('id', 'bigInteger', ['identity' => true, 'primary' => true]));
            $table->add($schema->column('type', 'string', ['size' => 32])); // category | tag
            $table->add($schema->column('name', 'string', ['size' => 191]));
            $table->add($schema->column('slug', 'string', ['size' => 191]));
            $table->add($schema->column('description', 'text', ['null' => true]));
            $table->add($schema->column('parent_id', 'bigInteger', ['null' => true]));
            $table->add($schema->column('sort', 'integer', ['default' => 0]));
            $table->add($schema->column('created_at', 'timestamp'));
            $table->add($schema->column('updated_at', 'timestamp'));
            $table->foreignKey('parent_id', 'taxonomy_terms', 'id', 'CASCADE', 'CASCADE');
        });

        $schema->createTable('content_taxonomy', function (SchemaBuilder\TableDefinition $table) use ($schema) {
            $table->add($schema->column('content_id', 'bigInteger'));
            $table->add($schema->column('content_type', 'string', ['size' => 64]));
            $table->add($schema->column('term_id', 'bigInteger'));
            $table->add($schema->column('term_type', 'string', ['size' => 32]));
            $table->foreignKey('term_id', 'taxonomy_terms', 'id', 'CASCADE', 'CASCADE');
        });
    }

    public function down(SchemaBuilder $schema): void
    {
        $schema->dropTable('content_taxonomy');
        $schema->dropTable('taxonomy_terms');
    }
};
