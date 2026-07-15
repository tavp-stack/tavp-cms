<?php

declare(strict_types=1);

use Tavp\Core\Database\Migrations\Migration;
use Tavp\Core\Database\Migrations\SchemaBuilder;

return new class extends Migration
{
    public function up(SchemaBuilder $schema): void
    {
        $schema->createTable('seo_meta', function (SchemaBuilder\TableDefinition $table) {
            $table->add($schema->column('id', 'bigInteger', ['identity' => true, 'primary' => true]));
            $table->add($schema->column('content_type', 'string', ['size' => 100]));
            $table->add($schema->column('content_id', 'bigInteger'));
            $table->add($schema->column('meta_title', 'string', ['size' => 255, 'null' => true]));
            $table->add($schema->column('meta_description', 'text', ['null' => true]));
            $table->add($schema->column('meta_keywords', 'string', ['size' => 500, 'null' => true]));
            $table->add($schema->column('og_title', 'string', ['size' => 255, 'null' => true]));
            $table->add($schema->column('og_description', 'text', ['null' => true]));
            $table->add($schema->column('og_image', 'string', ['size' => 500, 'null' => true]));
            $table->add($schema->column('og_type', 'string', ['size' => 50, 'null' => true]));
            $table->add($schema->column('twitter_title', 'string', ['size' => 255, 'null' => true]));
            $table->add($schema->column('twitter_description', 'text', ['null' => true]));
            $table->add($schema->column('twitter_image', 'string', ['size' => 500, 'null' => true]));
            $table->add($schema->column('twitter_card', 'string', ['size' => 50, 'null' => true]));
            $table->add($schema->column('canonical_url', 'string', ['size' => 500, 'null' => true]));
            $table->add($schema->column('robots', 'string', ['size' => 100, 'null' => true]));
            $table->add($schema->column('schema_type', 'string', ['size' => 100, 'null' => true]));
            $table->add($schema->column('schema_data', 'text', ['null' => true]));
            $table->add($schema->column('seo_score', 'integer', ['null' => true]));
            $table->add($schema->column('focus_keyword', 'string', ['size' => 255, 'null' => true]));
            $table->add($schema->column('created_at', 'timestamp'));
            $table->add($schema->column('updated_at', 'timestamp'));
        });

        $schema->addIndex('seo_meta', ['content_type', 'content_id'], 'seo_meta_content_unique', true);
    }

    public function down(SchemaBuilder $schema): void
    {
        $schema->dropTable('seo_meta');
    }
};
