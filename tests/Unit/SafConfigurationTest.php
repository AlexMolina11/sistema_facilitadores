<?php

namespace Tests\Unit;

use Tests\TestCase;

class SafConfigurationTest extends TestCase
{
    public function test_saf_configuration_is_available(): void
    {
        $this->assertIsArray(config('saf'));
    }

    public function test_saf_is_disabled_by_default(): void
    {
        $this->assertFalse(config('saf.enabled'));
    }

    public function test_saf_uses_database_mode_by_default(): void
    {
        $this->assertSame(
            'database',
            config('saf.mode')
        );
    }

    public function test_saf_source_is_defined(): void
    {
        $this->assertSame(
            'SAF',
            config('saf.source')
        );
    }

    public function test_saf_chunk_size_is_a_positive_integer(): void
    {
        $chunkSize = config('saf.processing.chunk_size');

        $this->assertIsInt($chunkSize);
        $this->assertGreaterThan(0, $chunkSize);
    }

    public function test_saf_external_keys_are_defined(): void
    {
        $this->assertSame(
            'id_instructor',
            config('saf.consultants.external_key')
        );

        $this->assertSame(
            'codigo_evento_externo',
            config('saf.trainings.external_key')
        );
    }

    public function test_saf_hash_algorithm_is_supported(): void
    {
        $algorithm = config('saf.hash.algorithm');

        $this->assertContains(
            $algorithm,
            hash_algos()
        );
    }

    public function test_saf_sensitive_fields_are_defined(): void
    {
        $sensitiveFields = config('saf.sensitive_fields');

        $this->assertIsArray($sensitiveFields);
        $this->assertContains('password', $sensitiveFields);
        $this->assertContains('token', $sensitiveFields);
    }
}