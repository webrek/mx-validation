# MX Validation

[![Última versión](https://img.shields.io/github/v/tag/webrek/mx-validation?sort=semver&label=versión&style=flat-square)](https://github.com/webrek/mx-validation/releases)
[![Tests](https://img.shields.io/github/actions/workflow/status/webrek/mx-validation/tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/webrek/mx-validation/actions/workflows/tests.yml)
[![PHP](https://img.shields.io/badge/php-%5E8.2-777bb4?style=flat-square)](https://php.net)
[![Licencia](https://img.shields.io/github/license/webrek/mx-validation?style=flat-square)](LICENSE)

Valida y **genera** los identificadores mexicanos que toca cualquier app —
**RFC, CURP, CLABE, NSS** y **código postal** — con **verificación real del
dígito verificador**, no solo una expresión regular.

Un **núcleo independiente de framework** (solo PHP y
[nesbot/carbon](https://github.com/briannesbitt/Carbon)) con un **puente opcional
para Laravel**: reglas de validación, *casts* de Eloquent y un proveedor de
Faker.

```php
use Webrek\MxValidation\ValueObjects\Rfc;

Rfc::isValid('GODE561231GR8');   // true  — estructura + fecha + dígito verificador
Rfc::isValid('GODE561231GR9');   // false — el dígito verificador no coincide
```

## Instalación

```bash
composer require webrek/mx-validation
```

No requiere ningún framework. En Laravel, el *service provider* se descubre solo
y registra las reglas y el proveedor de Faker.

## Value objects (núcleo, sin framework)

Cada identificador tiene un value object inmutable con `isValid()`, `tryParse()`
(null al fallar) y `parse()` (lanza `InvalidIdentifierException`), más
*accessors*:

```php
use Webrek\MxValidation\ValueObjects\{Rfc, Curp, Clabe};

$rfc = Rfc::parse('  xaxx-010101-000 ');
$rfc->value;        // "XAXX010101000"  (normalizado)
$rfc->isFisica();   // true
$rfc->isGeneric();  // true  (RFC genérico del SAT)

$curp = Curp::parse('PEPJ900101HDFRRN09');
$curp->sex();            // "H"
$curp->stateName();      // "Ciudad de México"
$curp->birthDate();      // CarbonImmutable 1990-01-01
$curp->isForeignBorn();  // false  (true cuando el código de entidad es "NE")

$clabe = Clabe::parse('002010077777777771');
$clabe->bankName();   // "Banamex"
```

## Generar un RFC o CURP a partir de un nombre

```php
use Webrek\MxValidation\ValueObjects\{Rfc, Curp};

Rfc::fromName('Gómez', 'Díaz', 'Emma', '1956-12-31');
// GODE561231GR8  (el propio ejemplo del SAT)

Curp::fromName('Pérez', 'López', 'Juan', '1990-05-15', sex: 'H', state: 'JC');
// PELJ900515HJCRPN03
```

Aplican las reglas del SAT/RENAPO (quita partículas, salta nombre común
María/José, filtro de palabras altisonantes) y el algoritmo de homoclave está
verificado contra el ejemplo publicado por el SAT.

> **Presuntivo, no autoritativo.** La homoclave del RFC y el diferenciador de la
> CURP (posición 17) sirven para desempatar homónimos; la autoridad puede asignar
> otro valor. Trátalo como un buen candidato para precargar un formulario, nunca
> como sustituto del documento oficial.

## En Laravel

### Reglas de validación

```php
$request->validate([
    'rfc'           => ['required', 'rfc'],
    'curp'          => ['required', 'curp'],
    'clabe'         => ['required', 'clabe'],
    'nss'           => ['required', 'nss'],
    'codigo_postal' => ['required', 'codigo_postal'],
]);
```

…o como objetos de regla:

```php
use Webrek\MxValidation\Laravel\Rules;

$request->validate(['rfc' => ['required', new Rules\Rfc]]);
```

Los mensajes de error vienen en español de fábrica y respetan las sustituciones
de mensajes personalizados de Laravel.

### Casts de Eloquent

```php
use Webrek\MxValidation\Laravel\Casts\{RfcCast, CurpCast, ClabeCast, NssCast};

class Taxpayer extends Model
{
    protected $casts = [
        'rfc'   => RfcCast::class,
        'curp'  => CurpCast::class,
        'clabe' => ClabeCast::class,
        'nss'   => NssCast::class,
    ];
}

$taxpayer->rfc = 'xaxx-010101-000';   // se guarda "XAXX010101000"
$taxpayer->rfc->isGeneric();          // true — al leer es una instancia de Rfc
```

Asignar un valor inválido lanza `InvalidIdentifierException`; `null` queda `null`.

## Faker

Un proveedor de Faker genera identificadores válidos (pero ficticios) con
dígitos verificadores correctos. En Laravel se registra solo; en otro lado:

```php
$faker->addProvider(new \Webrek\MxValidation\Faker\MxProvider($faker));

$faker->rfc();             // persona física
$faker->rfc(moral: true);  // persona moral
$faker->curp();
$faker->clabe();           // o $faker->clabe('012') para fijar el banco
$faker->nss();
```

## Qué verifica y qué no

Verifica **estructura, la fecha embebida, códigos de entidad/banco válidos y el
dígito verificador oficial**. **No** confirma que un valor esté registrado ante
el SAT, el IMSS o un banco, y `codigo_postal` es solo un chequeo de formato
(la validación completa necesita el catálogo SEPOMEX, que este paquete no
incluye). Para generar CFDI, combínalo con
[webrek/cfdi](https://github.com/webrek/cfdi).

## Pruebas

```bash
composer test
```

La suite del núcleo (`tests/Unit`) corre sin framework; la de Laravel
(`tests/Feature`) ejercita las reglas, los *casts* y el *service provider*.

## Contribuir

Consulta [CONTRIBUTING.md](CONTRIBUTING.md). Corre `make check` antes de abrir un
*pull request*.

## Seguridad

Reporta vulnerabilidades a través del
[formulario de avisos de seguridad](https://github.com/webrek/mx-validation/security/advisories/new),
no como *issues* públicos. Consulta [SECURITY.md](SECURITY.md).

## Licencia

Licencia MIT (MIT). Consulta [LICENSE](LICENSE).
