<?php

declare(strict_types=1);

use Tavp\Core\Database\Migrations\Migration;
use Tavp\Core\Database\Migrations\SchemaBuilder;

return new class extends Migration
{
    public function up(SchemaBuilder $schema): void
    {
        $schema->createTable('media', function (SchemaBuilder\TableDefinition $table) use ($schema) {
            $table->add($schema->column('id', 'bigInteger', ['identity' => true, 'primary' => true]));
            $table->add($schema->column('name', 'string', ['size' => 191]));
            $table->add($schema->column('file_name', 'string', ['size' => 191]));
            $table->add($schema->column('mime_type', 'string', ['size' => 128]));
            $table->add($schema->column('path', 'string', ['size' => 255]));
            $table->add($schema->column('disk', 'string', ['size' => 32, 'default' => 'public']));
            $table->add($schema->column('size', 'bigInteger', ['default' => 0]));
            $table->add($schema->column('alt', 'string', ['size' => 255, 'null' => true]));
            $table->add($schema->column('meta', 'text', ['null' => true]));
            $table->add($schema->column('created_at', 'timestamp'));
            $table->add($schema->column('updated_at', 'timestamp'));
        });
    }

    public function down(SchemaBuilder $schema): void
    {
        $schema->dropTable('media');
    }
};
