<?php

namespace Laravel\Braintree\Concerns;

trait Prorates
{
    /**
     * Indicates if the plan change should be prorated.
     *
    * @var bool
     */
    protected $prorationBehavior = true;

    /**
     * Indicate that the plan change should not be prorated.
     *
     * @return $this
     */
    public function noProrate()
    {
        $this->prorationBehavior = false;

        return $this;
    }

    /**
     * Indicate that the plan change should be prorated.
     *
     * @return $this
     */
    public function prorate()
    {
        $this->prorationBehavior = true;

        return $this;
    }

    /**
     * Set the prorating behavior.
     *
     * @param  bool  $prorationBehavior
     * @return $this
     */
    public function setProrationBehavior($prorationBehavior)
    {
        $this->prorationBehavior = $prorationBehavior;

        return $this;
    }

    /**
     * Determine the prorating behavior when updating the subscription.
     *
     * @return string
     */
    public function prorateBehavior()
    {
        return $this->prorationBehavior;
    }
}