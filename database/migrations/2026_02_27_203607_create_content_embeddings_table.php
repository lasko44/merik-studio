<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $isPostgres = DB::connection()->getDriverName() === 'pgsql';

        // Ensure vector extension is enabled (PostgreSQL only)
        if ($isPostgres) {
            DB::statement('CREATE EXTENSION IF NOT EXISTS vector');
        }

        Schema::create('content_embeddings', function (Blueprint $table) use ($isPostgres) {
            $table->id();
            $table->string('type'); // 'component', 'page_template', 'content_example'
            $table->string('name'); // e.g., 'hero', 'features', 'landing'
            $table->string('category')->nullable(); // e.g., 'service_page', 'home_page'
            $table->string('industry')->nullable(); // e.g., 'restaurant', 'saas'
            $table->text('description'); // Human description of this component/template
            if ($isPostgres) {
                $table->jsonb('example_content'); // Example JSON structure
                $table->jsonb('metadata')->nullable(); // Additional metadata
            } else {
                $table->json('example_content'); // Example JSON structure
                $table->json('metadata')->nullable(); // Additional metadata
            }
            $table->timestamps();
        });

        // Add vector column for embeddings (1536 dimensions for text-embedding-3-small)
        // PostgreSQL only - skip for SQLite testing
        if ($isPostgres) {
            DB::statement('ALTER TABLE content_embeddings ADD COLUMN embedding vector(1536)');

            // Create index for similarity search
            DB::statement('CREATE INDEX content_embeddings_embedding_idx ON content_embeddings USING ivfflat (embedding vector_cosine_ops) WITH (lists = 100)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_embeddings');
    }
};
