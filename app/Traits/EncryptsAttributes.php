<?php

namespace App\Traits;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;

trait EncryptsAttributes
{
    /**
     * Intercept encrypted attributes before persisting.
     */
    public function setAttribute($key, $value)
    {
        if ($this->shouldEncryptAttribute($key, $value)) {
            $value = Crypt::encryptString($value);
        }

        return parent::setAttribute($key, $value);
    }

    /**
     * Intercept encrypted attributes when reading from model.
     */
    public function getAttributeValue($key): mixed
    {
        $value = parent::getAttributeValue($key);

        if (! $this->isEncryptedAttribute($key) || $value === null || $value === '') {
            return $value;
        }

        try {
            return Crypt::decryptString($value);
        } catch (DecryptException $exception) {
            // Return raw value if data is already plain text or not decryptable.
            return $value;
        }
    }

    protected function isEncryptedAttribute(string $key): bool
    {
        return property_exists($this, 'encrypted') && in_array($key, $this->encrypted, true);
    }

    protected function shouldEncryptAttribute(string $key, mixed $value): bool
    {
        return $this->isEncryptedAttribute($key)
            && $value !== null
            && $value !== ''
            && $this->isEncryptableValue($value);
    }

    protected function isEncryptableValue(mixed $value): bool
    {
        if (! is_string($value)) {
            return false;
        }

        try {
            Crypt::decryptString($value);

            return false;
        } catch (DecryptException $exception) {
            return true;
        }
    }
}
