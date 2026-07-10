<?php

declare(strict_types=1);

use Tavp\Core\Database\Migrations\Migration;
use Tavp\Core\Database\Migrations\SchemaBuilder;

/**
 * Content revisions — a point-in-time snapshot of a record on every save.
 * Enables history browsing and one-click rollback from the admin.
 */
return new class extends Migration
{
    public function up(SchemaBuilder $schema): void
    {
        $schema->createTable('content_revisions', function (SchemaBuilder\TableDefinition $table) use ($schema) {
            $table->add($schema->column('id', 'bigInteger', ['identity' => true, 'primary' => true]));
            $table->add($schema->column('content_type', 'string', ['size' => 64]));
            $table->add($schema->column('content_id', 'bigInteger'));
            $table->add($schema->column('data', 'text'));
            $table->add($schema->column('author', 'string', ['size' => 191, 'null' => true]));
            $table->add($schema->column('note', 'string', ['size' => 255, 'null' => true]));
            $table->add($schema->column('created_at', 'timestamp'));
        });
    }

    public function down(SchemaBuilder $schema): void
    {
        $schema->dropTable('content_revisions');
    }
};
