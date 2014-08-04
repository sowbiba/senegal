<?php
/**

 */

namespace Api\Sdk\Bridge;
/**
 * Class SdkBridge
 * @package Api\Sdk\Bridge
 * @author Florent Coquel
 * @since 07/06/13
 */
interface SdkBridge
{
    public function handle();

    public function permissiveTransaction($callback, $args);
}
