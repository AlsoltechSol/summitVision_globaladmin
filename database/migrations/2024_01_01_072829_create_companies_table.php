<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->nullable();
            $table->string('email', 50)->nullable();
            $table->string('mobile', 20)->nullable();
            $table->string('password', 255)->nullable();
            $table->string('url', 255)->nullable();
            $table->string('is_verified', 1)->default('0');
            $table->string('server_config_status', 1)->default('0');
            $table->string('verification_token', 255)->nullable();
            $table->string('DB_DATABASE', 100)->nullable();
            $table->string('DB_USERNAME', 100)->nullable();
            $table->string('DB_HOST', 100)->default('localhost:3306');
            $table->string('sub_domain', 255)->nullable();
            $table->string('DB_PASSWORD', 255)->nullable();
            $table->string('create_domain_and_dir', 1)->default('0');
            $table->string('database_create_and_config', 1)->default('0');
            $table->string('fileop', 1)->default('0');
            $table->string('modify_env', 1)->default('0');
            $table->string('company_name', 255)->nullable();
            $table->json('server_setup_json')->nullable();
            $table->timestamp('server_setup_started_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('companies');
    }
};
