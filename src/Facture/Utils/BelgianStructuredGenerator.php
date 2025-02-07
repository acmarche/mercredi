<?php

namespace AcMarche\Mercredi\Facture\Utils;

class BelgianStructuredGenerator
{
    /**
     * Set divisor used to calculate the modulus
     */
    const MODULO = 97;

    /**
     * Set the plus sign as a circumfix
     */
    const CIRCUMFIX_PLUS = "+";

    /**
     * The modulus resulting from the modulo operation
     */
    private static int $modulus;

    /**
     * A structured message with a valid formatting
     */
    private static ?string $structuredMessage = null;

    /**
     * The number used to generate a structured message
     */
    private static ?int $number = null;

    /**
     * Generate a valid structured message based on the number
     *
     * @param int|null $number
     * @param string $circumfix The circumfix. Defaults to the plus sign
     *
     * @return string|null A valid structured message
     * @throws \Exception
     */
    public static function generate(?int $number = null, string $circumfix = self::CIRCUMFIX_PLUS): ?string
    {
        self::setNumber($number);
        self::$modulus = self::mod(self::$number);

        $structuredMessage = str_pad(self::$number, 10, 0, STR_PAD_LEFT).str_pad(self::$modulus, 2, 0, STR_PAD_LEFT);

        $pattern = ['/^([0-9]{3})([0-9]{4})([0-9]{5})$/'];
        $replace = [str_pad('$1/$2/$3', 14, $circumfix, STR_PAD_BOTH)];
        self::$structuredMessage = preg_replace($pattern, $replace, $structuredMessage);

        return self::$structuredMessage;
    }

    /**
     * Set the number
     *
     * If no number is passed to this method, a random number will be generated
     *
     * @param int|null $number The number used to generate a structured message
     *
     * @throws \Exception If the number is out of bounds
     */
    public static function setNumber(int $number = null): void
    {
        try {
            if (is_null($number)) {
                self::$number = random_int(1, 9999999999);
            } else {
                if (($number < 1) || ($number > 9999999999)) {
                    throw new \InvalidArgumentException(
                        'The number should be an integer larger then 0 and smaller then 9999999999.',
                    );
                }

                self::$number = $number;
            }
        } catch (\Exception $e) {
            throw new \Exception('Failed to set number', null, $e);
        }
    }

    /**
     * The mod97 calculation
     *
     * If the modulus is 0, the result is substituted to 97
     *
     * @param int $dividend The dividend
     *
     * @return int           The modulus
     */
    private static function mod(int $dividend): int
    {
        $modulus = $dividend % self::MODULO;

        return ($modulus > 0) ? $modulus : self::MODULO;
    }

    /**
     * Validates a structured message
     *
     * The validation is the mod97 calculation of the number and comparison of
     * the result to the provided modulus.
     *
     * @return bool TRUE if valid, FALSE if invalid
     */
    public static function validate()
    {
        $pattern = ['/^[+*]{3}([0-9]{3})[\/]?([0-9]{4})[\/]?([0-9]{5})[+*]{3}$/'];
        $replace = ['${1}${2}${3}'];
        $rawStructuredMessage = preg_replace($pattern, $replace, self::$structuredMessage);

        $number = substr($rawStructuredMessage, 0, 10);
        $modulus = substr($rawStructuredMessage, 10, 2);

        return $modulus == self::mod($number);
    }
}