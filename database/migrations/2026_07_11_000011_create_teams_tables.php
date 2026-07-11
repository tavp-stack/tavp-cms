<?php

declare(strict_types=1);

use Tavp\Core\Database\Migrations\Migration;

return new class extends Migration
{
    public function up($schema): void
    {
        $schema->createTable('teams', function ($table) use ($schema) {
            $table->add($schema->column('id', 'bigInteger', ['identity' => true, 'primary' => true]));
            $table->add($schema->column('name', 'string', ['size' => 100]));
            $table->add($schema->column('owner_id', 'bigInteger'));
            $table->add($schema->column('created_at', 'timestamp'));
            $table->add($schema->column('updated_at', 'timestamp'));
        });

        $schema->createTable('team_members', function ($table) use ($schema) {
            $table->add($schema->column('id', 'bigInteger', ['identity' => true, 'primary' => true]));
            $table->add($schema->column('team_id', 'bigInteger'));
            $table->add($schema->column('user_id', 'bigInteger'));
            $table->add($schema->column('role', 'string', ['size' => 50, 'default' => 'member']));
            $table->add($schema->column('created_at', 'timestamp'));
        });

        $schema->createTable('subscriptions', function ($table) use ($schema) {
            $table->add($schema->column('id', 'bigInteger', ['identity' => true, 'primary' => true]));
            $table->add($schema->column('user_id', 'bigInteger'));
            $table->add($schema->column('plan', 'string', ['size' => 100]));
            $table->add($schema->column('status', 'string', ['size' => 50, 'default' => 'active']));
            $table->add($schema->column('gateway', 'string', ['size' => 50]));
            $table->add($schema->column('gateway_subscription_id', 'string', ['size' => 255, 'null' => true]));
            $table->add($schema->column('current_period_start', 'timestamp', ['null' => true]));
            $table->add($schema->column('current_period_end', 'timestamp', ['null' => true]));
            $table->add($schema->column('created_at', 'timestamp'));
            $table->add($schema->column('updated_at', 'timestamp'));
        });

        $schema->createTable('invoices', function ($table) use ($schema) {
            $table->add($schema->column('id', 'bigInteger', ['identity' => true, 'primary' => true]));
            $table->add($schema->column('user_id', 'bigInteger'));
            $table->add($schema->column('subscription_id', 'bigInteger', ['null' => true]));
            $table->add($schema->column('amount', 'decimal'));
            $table->add($schema->column('currency', 'string', ['size' => 3, 'default' => 'USD']));
            $table->add($schema->column('status', 'string', ['size' => 50, 'default' => 'pending']));
            $table->add($schema->column('gateway', 'string', ['size' => 50]));
            $table->add($schema->column('gateway_invoice_id', 'string', ['size' => 255, 'null' => true]));
            $table->add($schema->column('created_at', 'timestamp'));
            $table->add($schema->column('updated_at', 'timestamp'));
        });
    }

    public function down($schema): void
    {
        $schema->dropTable('invoices');
        $schema->dropTable('subscriptions');
        $schema->dropTable('team_members');
        $schema->dropTable('teams');
    }
};
