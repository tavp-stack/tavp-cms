<?php

declare(strict_types=1);

use Tavp\Core\Database\Migrations\Migration;
use Tavp\Core\Database\Migrations\SchemaBuilder;

return new class extends Migration
{
    public function up(SchemaBuilder $schema): void
    {
        $schema->createTable('outbound_links', function (SchemaBuilder\TableDefinition $table) {
            $table->add($schema->column('id', 'bigInteger', ['identity' => true, 'primary' => true]));
            $table->add($schema->column('content_type', 'string', ['size' => 100]));
            $table->add($schema->column('content_id', 'bigInteger'));
            $table->add($schema->column('url', 'string', ['size' => 500]));
            $table->add($schema->column('status_code', 'integer', ['null' => true]));
            $table->add($schema->column('is_broken', 'boolean'));
            $table->add($schema->column('last_checked_at', 'timestamp', ['null' => true]));
            $table->add($schema->column('created_at', 'timestamp'));
        });

        $schema->addIndex('outbound_links', ['url'], 'outbound_links_url_index');
        $schema->addIndex('outbound_links', ['is_broken'], 'outbound_links_broken_index');
    }

    public function down(SchemaBuilder $schema): void
    {
        $schema->dropTable('outbound_links');
    }
};
