<?php

namespace CraigPaul\Moneris;

/**
 * CraigPaul\Moneris\Gateway
 *
 * @property bool $avs
 * @property-read array $avsCodes
 * @property bool $cvd
 * @property-read array $cvdCodes
 * @property-read string $environment
 * @property-read string $id
 * @property-read string $token
 */
class Gateway
{
    use Gettable, Settable;

    /**
     * Determine if we will use the Address Verification Service.
     *
     * @var bool
     */
    protected $avs = false;

    /**
     * @var array
     */
    protected $avsCodes = ['A', 'B', 'D', 'M', 'P', 'W', 'X', 'Y', 'Z'];

    /**
     * Determine if we will use the Card Validation Digits.
     *
     * @var bool
     */
    protected $cvd = false;

    /**
     * @var array
     */
    protected $cvdCodes = ['M', 'Y', 'P', 'S', 'U'];

    /**
     * The environment used for connecting to the Moneris API.
     *
     * @var string
     */
    protected $environment;

    /**
     * The Moneris Store ID.
     *
     * @var string
     */
    protected $id;

    /**
     * The Moneris API Token.
     *
     * @var string
     */
    protected $token;

    /**
     * The current transaction.
     *
     * @var \CraigPaul\Moneris\Transaction
     */
    protected $transaction;

    /**
     * Create a new Moneris instance.
     *
     * @param string $id
     * @param string $token
     * @param string $environment
     *
     * @return void
     */
    public function __construct(string $id, string $token, string $environment)
    {
        $this->id = $id;
        $this->token = $token;
        $this->environment = $environment;
    }

    /**
     * Pre-authorize a purchase.
     *
     * @param array $params
     *
     * @return \CraigPaul\Moneris\Response
     */
    public function preauth(array $params = [])
    {
        $params = array_merge($params, [
            'type' => 'preauth',
            'crypt_type' => Crypt::SSL_ENABLED_MERCHANT,
        ]);

        $transaction = $this->transaction($params);

        return $this->process($transaction);
    }

    /**
     * Make a purchase.
     *
     * @param array $params
     *
     * @return \CraigPaul\Moneris\Response
     */
    public function purchase(array $params = [])
    {
        $params = array_merge($params, [
            'type' => 'purchase',
            'crypt_type' => Crypt::SSL_ENABLED_MERCHANT,
        ]);

        $transaction = $this->transaction($params);

        return $this->process($transaction);
    }

    /**
     * Process a transaction through the Moneris API.
     *
     * @param \CraigPaul\Moneris\Transaction $transaction
     *
     * @return \CraigPaul\Moneris\Response
     */
    protected function process(Transaction $transaction)
    {
        return Processor::process($transaction);
    }

    /**
     * Get or create a new Transaction instance.
     *
     * @param array|null $params
     *
     * @return \CraigPaul\Moneris\Transaction
     */
    protected function transaction(array $params = null)
    {
        if (is_null($this->transaction) || !is_null($params)) {
            return $this->transaction = new Transaction($this, $params);
        }

        return $this->transaction;
    }
}