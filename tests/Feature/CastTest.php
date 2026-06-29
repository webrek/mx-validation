<?php

namespace Webrek\MxValidation\Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\DataProvider;
use Webrek\MxValidation\Exceptions\InvalidIdentifierException;
use Webrek\MxValidation\Tests\LaravelTestCase;
use Webrek\MxValidation\Tests\Support\Taxpayer;
use Webrek\MxValidation\ValueObjects\Clabe;
use Webrek\MxValidation\ValueObjects\Curp;
use Webrek\MxValidation\ValueObjects\Nss;
use Webrek\MxValidation\ValueObjects\Rfc;

class CastTest extends LaravelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('taxpayers', function (Blueprint $table): void {
            $table->id();
            $table->string('rfc')->nullable();
            $table->string('curp')->nullable();
            $table->string('clabe')->nullable();
            $table->string('nss')->nullable();
        });
    }

    public function test_it_hydrates_value_objects_and_stores_normalized_strings(): void
    {
        $taxpayer = Taxpayer::create([
            'rfc' => ' gode-561231-gr8 ',
            'curp' => 'hegg560427mvzrrl04',
            'clabe' => '0020 1007 7777 7777 71',
            'nss' => '12345678903',
        ])->refresh();

        $this->assertInstanceOf(Rfc::class, $taxpayer->rfc);
        $this->assertInstanceOf(Curp::class, $taxpayer->curp);
        $this->assertInstanceOf(Clabe::class, $taxpayer->clabe);
        $this->assertInstanceOf(Nss::class, $taxpayer->nss);

        $this->assertSame('GODE561231GR8', (string) $taxpayer->rfc);
        $this->assertSame('HEGG560427MVZRRL04', (string) $taxpayer->curp);
        $this->assertSame('002010077777777771', (string) $taxpayer->clabe);

        $this->assertDatabaseHas('taxpayers', [
            'rfc' => 'GODE561231GR8',
            'clabe' => '002010077777777771',
        ]);
    }

    public function test_it_accepts_value_object_instances(): void
    {
        $taxpayer = Taxpayer::create([
            'rfc' => Rfc::parse('GODE561231GR8'),
            'curp' => Curp::parse('HEGG560427MVZRRL04'),
            'clabe' => Clabe::parse('002010077777777771'),
            'nss' => Nss::parse('12345678903'),
        ])->refresh();

        $this->assertSame('GODE561231GR8', (string) $taxpayer->rfc);
        $this->assertSame('HEGG560427MVZRRL04', (string) $taxpayer->curp);
        $this->assertSame('002010077777777771', (string) $taxpayer->clabe);
        $this->assertSame('12345678903', (string) $taxpayer->nss);
    }

    public function test_null_stays_null(): void
    {
        $taxpayer = Taxpayer::create([
            'rfc' => null,
            'curp' => null,
            'clabe' => null,
            'nss' => null,
        ])->refresh();

        $this->assertNull($taxpayer->rfc);
        $this->assertNull($taxpayer->curp);
        $this->assertNull($taxpayer->clabe);
        $this->assertNull($taxpayer->nss);
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function invalidValues(): array
    {
        return [
            'rfc' => ['rfc', 'GODE561231GR9'],
            'curp' => ['curp', 'HEGG560427MVZRRL05'],
            'clabe' => ['clabe', '002010077777777770'],
            'nss' => ['nss', '12345678904'],
        ];
    }

    #[DataProvider('invalidValues')]
    public function test_setting_an_invalid_value_throws(string $column, string $value): void
    {
        $this->expectException(InvalidIdentifierException::class);

        Taxpayer::create([$column => $value]);
    }
}
