<?php

namespace Shift;

use BackedEnum;
use DateTime;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Casts\AsEncryptedArrayObject;
use Illuminate\Database\Eloquent\Casts\AsEncryptedCollection;
use Illuminate\Database\Eloquent\Casts\AsEnumArrayObject;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use Illuminate\Database\Eloquent\Casts\AsStringable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use RuntimeException;

class RealTimeFactory extends Factory
{
    /**
     * An instance of the factory's corresponding model.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $modelInstance;

    /**
     * The database schema manager for the model.
     *
     * @var Builder
     */
    protected $schema;

    /**
     * The table name for the model.
     *
     * @var string
     */
    protected $table;

    /**
     * Configure the factory.
     */
    public function configure(): self
    {
        $modelName = $this->modelName();
        $this->modelInstance = new $modelName;
        $this->table = $this->modelInstance->getConnection()->getTablePrefix().$this->modelInstance->getTable();
        $this->schema = $this->modelInstance->getConnection()->getSchemaBuilder();

        return $this;
    }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return $this->configure()
            ->getColumnsFromModel()
            ->reject(fn ($column) => $this->isAutoIncrement($column) || $this->isForeignKey($column) || $this->isPrimaryKey($column))
            ->map(fn ($column) => $this->value($column))
            ->all();
    }

    /**
     * Set the model for the factory.
     */
    public function forModel(string $model): self
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Get a new factory instance for the given attributes.
     *
     * @param  (callable(array<string, mixed>): array<string, mixed>)|array<string, mixed>  $attributes
     * @return static
     */
    public static function new($attributes = [])
    {
        throw new RuntimeException('Real-time factories cannot be instantiated with new()');
    }

    /**
     * Generate an array value.
     */
    protected function arrayValue(): array
    {
        return fake()->words(5);
    }

    /**
     * Generate a boolean value.
     */
    protected function booleanValue(): bool
    {
        return fake()->boolean();
    }

    /**
     * Generate a date value.
     */
    protected function dateValue(): DateTime
    {
        return fake()->dateTime();
    }

    /**
     * Generate a decimal value.
     */
    protected function decimalValue(int $precision = 2, int $max = 100): float
    {
        return fake()->randomFloat($precision, 0, $max);
    }

    /**
     * Generate an enum collection value.
     */
    protected function enumCollectionValue(string $enum): mixed
    {
        return collect(range(1, 5))
            ->map(fn () => $this->enumValue($enum))
            ->all();
    }

    /**
     * Generate an enum value.
     */
    protected function enumValue(string $enum): mixed
    {
        $enum = Str::after($enum, ':');
        $case = Arr::random($enum::cases());

        return $case instanceof BackedEnum ? $case->value : $case->name;
    }

    /**
     * Get the table columns from the model.
     */
    protected function getColumnsFromModel(): Collection
    {
        $columns = $this->schema->getColumns($this->table);

        return collect($columns)->keyBy('name');
    }

    /**
     * Guess the value of a column based on its name.
     */
    protected function guessValue(string $column): mixed
    {
        $guessable = $this->guessableValues();

        return isset($guessable[$column]) ? $guessable[$column]() : null;
    }

    /**
     * Get a list of guessable values.
     *
     * @return array<string, callable>
     */
    protected function guessableValues(): array
    {
        return [
            'email' => fn () => fake()->safeEmail(),
            'e_mail' => fn () => fake()->safeEmail(),
            'email_address' => fn () => fake()->safeEmail(),
            'name' => fn () => fake()->name(),
            'first_name' => fn () => fake()->firstName(),
            'last_name' => fn () => fake()->lastName(),
            'login' => fn () => fake()->userName(),
            'username' => fn () => fake()->userName(),
            'dob' => fn () => fake()->date(),
            'date_of_birth' => fn () => fake()->date(),
            'uuid' => fn () => fake()->uuid(),
            'url' => fn () => fake()->url(),
            'website' => fn () => fake()->url(),
            'phone' => fn () => fake()->phoneNumber(),
            'phone_number' => fn () => fake()->phoneNumber(),
            'telephone' => fn () => fake()->phoneNumber(),
            'tel' => fn () => fake()->phoneNumber(),
            'town' => fn () => fake()->city(),
            'city' => fn () => fake()->city(),
            'zip' => fn () => fake()->postcode(),
            'zip_code' => fn () => fake()->postcode(),
            'zipcode' => fn () => fake()->postcode(),
            'postal_code' => fn () => fake()->postcode(),
            'postalcode' => fn () => fake()->postcode(),
            'post_code' => fn () => fake()->postcode(),
            'postcode' => fn () => fake()->postcode(),
            'state' => fn () => fake()->state(),
            'province' => fn () => fake()->state(),
            'county' => fn () => fake()->state(),
            'country' => fn () => fake()->country(),
            'currency_code' => fn () => fake()->currencyCode(),
            'currency' => fn () => fake()->currencyCode(),
            'company' => fn () => fake()->company(),
            'company_name' => fn () => fake()->company(),
            'companyname' => fn () => fake()->company(),
            'employer' => fn () => fake()->company(),
            'title' => fn () => fake()->title(),
        ];
    }

    /**
     * Generate an integer value.
     */
    protected function integerValue(): int
    {
        return fake()->randomDigit();
    }

    /**
     * Determine whether the given cast is an array cast.
     */
    protected function isArrayCastable(string $key): bool
    {
        return in_array($key, ['array', 'json', 'object', 'collection', 'encrypted:array', 'encrypted:collection', 'encrypted:json', 'encrypted:object', AsArrayObject::class, AsCollection::class, AsEncryptedArrayObject::class, AsEncryptedCollection::class]);
    }

    /**
     * Determine whether the given cast is a boolean cast.
     */
    protected function isBooleanCastable(string $key): bool
    {
        return in_array($key, ['bool', 'boolean']);
    }

    /**
     * Determine whether the given cast is a date cast.
     */
    protected function isDateCastable(string $key): bool
    {
        return in_array($key, ['date', 'datetime', 'immutable_date', 'immutable_datetime']);
    }

    /**
     * Determine whether the given cast is a decimal cast.
     */
    protected function isDecimalCastable(string $key): int|bool
    {
        if (Str::startsWith($key, 'decimal')) {
            return (int) Str::after($key, ':');
        }

        return false;
    }

    /**
     * Determine whether the given cast is an enum cast.
     */
    protected function isEnumCastable(string $key): bool
    {
        return enum_exists(Str::after($key, ':'));
    }

    /**
     * Determine whether the given cast is an enum collection cast.
     */
    protected function isEnumCollectionCastable(string $key): bool
    {
        return in_array(Str::before($key, ':'), [AsEnumCollection::class, AsEnumArrayObject::class]) &&
            $this->isEnumCastable($key);
    }

    /**
     * Determine whether the given column is a foreign key.
     */
    protected function isForeignKey(array $column): bool
    {
        return collect($this->schema->getForeignKeys($this->table))
            ->filter(fn ($foreignKey) => in_array($column['name'], $foreignKey['columns']))
            ->isNotEmpty();
    }

    /**
     * Determine whether the given cast is an integer cast.
     */
    protected function isIntegerCastable(string $key): bool
    {
        return in_array($key, ['int', 'integer']);
    }

    /**
     * Determine whether the given column is the primary key.
     */
    protected function isPrimaryKey(array $column): bool
    {
        return collect($this->schema->getIndexes($this->table))
            ->some(fn ($index) => $index['primary'] && in_array($column['name'], $index['columns']));
    }

    /**
     * Determine whether the given cast is a real number cast.
     */
    protected function isRealCastable(string $key): bool
    {
        return in_array($key, ['real', 'float', 'double']);
    }

    /**
     * Determine whether the given cast is a string cast.
     */
    protected function isStringCastable(string $key): bool
    {
        return in_array($key, [
            'string',
            'encrypted',
            AsStringable::class,
        ]);
    }

    /**
     * Determine whether the given cast is a timestamp cast.
     */
    protected function isTimestampCastable(string $key): bool
    {
        return $key === 'timestamp';
    }

    /**
     * Generate a JSON value.
     */
    protected function jsonValue(): string
    {
        return json_encode($this->arrayValue());
    }

    /**
     * Create a new instance of the factory builder with the given mutated properties.
     *
     * @return static
     */
    protected function newInstance(array $arguments = [])
    {
        return parent::newInstance($arguments)
            ->forModel($this->model)
            ->configure();
    }

    /**
     * Generate a real number value.
     */
    protected function realValue(): float
    {
        return fake()->randomFloat(2, 0, 100);
    }

    /**
     * Generate a string value.
     */
    protected function stringValue(?int $length): string
    {
        return fake()->text($length ?? 10);
    }

    /**
     * Generate a timestamp value.
     */
    protected function timestampValue(): int
    {
        return fake()->unixTime();
    }

    /**
     * Generate a value for the given column.
     */
    protected function value(array $column): mixed
    {
        if ($value = $this->guessValue($column['name'])) {
            return $value;
        }

        $type = $this->parseColumnType($column);

        return ($value = $this->valueFromCast($column, $type)) ?
            $value :
            $this->valueFromColumn($column, $type);
    }

    /**
     * Generate a value using the defined cast for the column.
     */
    protected function valueFromCast(array $column, array $type): mixed
    {
        if (in_array($column['name'], $this->modelInstance->getDates())) {
            return $this->dateValue();
        }

        if (! $key = $this->modelInstance->getCasts()[$column['name']] ?? null) {
            return null;
        }

        if ($this->isArrayCastable($key)) {
            return $this->arrayValue();
        }

        if ($this->isIntegerCastable($key)) {
            return $this->integerValue();
        }

        if ($this->isRealCastable($key)) {
            return $this->realValue();
        }

        if ($precision = $this->isDecimalCastable($key)) {
            return $this->decimalValue($precision);
        }

        if ($this->isBooleanCastable($key)) {
            return $this->booleanValue();
        }

        if ($this->isDateCastable($key)) {
            return $this->dateValue();
        }

        if ($this->isTimestampCastable($key)) {
            return $this->timestampValue();
        }

        if ($this->isEnumCollectionCastable($key)) {
            return $this->enumCollectionValue($key);
        }

        if ($this->isEnumCastable($key)) {
            return $this->enumValue($key);
        }

        if ($this->isStringCastable($key)) {
            return $this->stringValue($type['length'] ?? null);
        }

        return null;
    }

    /**
     * Generate a value for the given column.
     */
    protected function valueFromColumn(array $column, array $type): mixed
    {
        if ($column['nullable'] && $column['default'] === null) {
            return null;
        }

        if ($value = $this->parseDefaultExpression($column['default'])) {
            return $value;
        }

        return match ($type['name']) {
            'integer' => $this->integerValue(),
            'date' => $this->dateValue(),
            'numeric' => $this->decimalValue($type['precision'] ?? 10, $type['scale'] ?? 2),
            'time' => fake()->time(),
            'datetime', 'dateTimeTz' => fake()->dateTime(),
            'timestamp', 'timestampTz' => $this->timestampValue(),
            'text' => fake()->text(),
            'boolean' => $this->booleanValue(),
            'json' => $this->jsonValue(),
            'enum' => fake()->randomElement($type['values'] ?? []),
            'set' => fake()->randomElements($type['values'] ?? []),
            default => $this->stringValue($type['length'] ?? null),
        };
    }

    /**
     * Map a database type name into equivalent column type.
     */
    public function parseColumnType(array $column): array
    {
        $type = match ($column['type']) {
            'tinyint(1)', 'bit' => 'boolean',
            'varchar(max)', 'nvarchar(max)' => 'text',
            default => null,
        };

        $type ??= match ($column['type_name']) {
            'integer', 'int', 'int4', 'smallint', 'int2', 'tinyint', 'mediumint', 'bigint', 'int8' => 'integer',
            'date' => 'date',
            'numeric', 'decimal', 'float', 'real', 'float4', 'double', 'float8' => 'numeric',
            'time', 'timetz' => 'time',
            'datetime', 'datetime2', 'smalldatetime','datetimeoffset' => 'datetime',
            'timestamp', 'timestamptz' => 'timestamp',
            'text', 'ntext', 'tinytext', 'mediumtext', 'longtext' => 'text',
            'boolean', 'bool' => 'boolean',
            'json', 'jsonb' => 'json',
            'enum' => 'enum',
            'set' => 'set',

            'char', 'bpchar', 'nchar' => 'char',
            'varchar', 'nvarchar' => 'string',
            'binary', 'varbinary', 'bytea', 'image', 'blob', 'tinyblob', 'mediumblob', 'longblob' => 'binary',
            'year' => 'year',
            'uuid', 'uniqueidentifier' => 'uuid',
            'macaddr', 'macaddr8' => 'mac_address',
            'inet', 'inet4', 'inet6', 'cidr' => 'ip_address',
            'geometry', 'geometrycollection', 'linestring', 'multilinestring', 'point', 'multipoint', 'polygon', 'multipolygon' => 'geometry',
            'geography' => 'geography',

            // 'money', 'smallmoney' => 'money',
            // 'bit', 'varbit' => 'bit',
            // 'xml' => 'xml',
            // 'interval' => 'interval',
            // 'box', 'circle', 'line', 'lseg', 'path' => 'geometry',
            // 'tsvector', 'tsquery' => 'text',
            default => null,
        };

        $values = str_contains($column['type'], '(')
            ? str_getcsv(Str::between($column['type'], '(', ')'), ",", "'")
            : null;

        $values = is_null($values) ? [] : match ($type) {
            'string', 'char', 'binary', 'bit' => ['length' => (int) $values[0]],
            'datetime', 'time', 'timestamp' => ['precision' => (int) $values[0]],
            'numeric' => ['precision' => (int) $values[0], 'scale' => isset($values[1]) ? (int) $values[1] : null],
            'enum', 'set' => ['values' => $values],
            'geometry', 'geography' => ['subtype' => $values[0] ?? $column['type_name'] ?? null, 'srid' => isset($values[1]) ? (int) $values[1] : null],
            default => [],
        };

        return array_merge(['name' => $type], array_filter($values));
    }

    protected function parseDefaultExpression(?string $default): mixed
    {
        if (blank($default)) {
            return null;
        }

        $driver = $this->modelInstance->getConnection()->getDriverName();

        if ($driver === 'mysql') {
            if ($default === 'NULL'
                || preg_match("/^\(.*\)$/", $default) === 1
                || str_ends_with($default, '()')
                || str_starts_with(strtolower($default), 'current_timestamp')) {
                return null;
            }

            if (preg_match("/^'(.*)'$/", $default, $matches) === 1) {
                return str_replace("''", "'", $matches[1]);
            }
        }

        if ($driver === 'pgsql') {
            if (str_starts_with($default, 'NULL::')) {
                $default = null;
            }

            if (preg_match("/^['(](.*)[')]::/", $default, $matches) === 1) {
                return str_replace("''", "'", $matches[1]);
            }
        }

        if ($driver === 'sqlsrv') {
            while (preg_match('/^\((.*)\)$/', $default, $matches)) {
                $default = $matches[1];
            }

            if ($default === 'NULL'
                || str_ends_with($default, '()')) {
                return null;
            }

            if (preg_match('/^\'(.*)\'$/', $default, $matches) === 1) {
                return str_replace("''", "'", $matches[1]);
            }
        }

        if ($driver === 'sqlite') {
            if ($default === 'NULL'
                || str_starts_with(strtolower($default), 'current_timestamp')) {
                return null;
            }

            if (preg_match('/^\'(.*)\'$/s', $default, $matches) === 1) {
                return str_replace("''", "'", $matches[1]);
            }
        }

        return $default;
    }

    private function isAutoIncrement(array $column): bool
    {
        return $column['auto_increment'];
    }
}
