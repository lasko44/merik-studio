<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Stores vector embeddings for page components, templates, and content examples
 * to enable RAG-based content generation.
 */
class ContentEmbedding extends Model
{
    protected $fillable = [
        'type',
        'name',
        'category',
        'industry',
        'description',
        'example_content',
        'metadata',
        'embedding',
    ];

    protected $casts = [
        'example_content' => 'array',
        'metadata' => 'array',
    ];

    /**
     * Find similar embeddings using cosine similarity.
     *
     * @param array $queryEmbedding The embedding vector to search with
     * @param int $limit Number of results to return
     * @param string|null $type Filter by type (component, page_template, content_example)
     * @param string|null $industry Filter by industry
     * @return \Illuminate\Support\Collection
     */
    public static function findSimilar(
        array $queryEmbedding,
        int $limit = 5,
        ?string $type = null,
        ?string $industry = null
    ) {
        $embeddingString = '[' . implode(',', $queryEmbedding) . ']';

        $query = static::query()
            ->select('*')
            ->selectRaw('1 - (embedding <=> ?) as similarity', [$embeddingString])
            ->orderByRaw('embedding <=> ?', [$embeddingString]);

        if ($type) {
            $query->where('type', $type);
        }

        if ($industry) {
            $query->where(function ($q) use ($industry) {
                $q->where('industry', $industry)
                    ->orWhereNull('industry');
            });
        }

        return $query->limit($limit)->get();
    }

    /**
     * Find similar components for a given context.
     */
    public static function findSimilarComponents(array $queryEmbedding, int $limit = 10, ?string $industry = null)
    {
        return static::findSimilar($queryEmbedding, $limit, 'component', $industry);
    }

    /**
     * Find similar page templates for a given context.
     */
    public static function findSimilarTemplates(array $queryEmbedding, int $limit = 5, ?string $industry = null)
    {
        return static::findSimilar($queryEmbedding, $limit, 'page_template', $industry);
    }

    /**
     * Find similar content examples for a given context.
     */
    public static function findSimilarExamples(array $queryEmbedding, int $limit = 10, ?string $industry = null)
    {
        return static::findSimilar($queryEmbedding, $limit, 'content_example', $industry);
    }
}
