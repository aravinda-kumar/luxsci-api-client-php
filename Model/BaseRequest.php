<?php
namespace LuxSciApiClient_Model;

/**
 * Base class for all models of requests
 * Class BaseRequest
 * @package LuxSciApiClient_Model
 */
abstract class BaseRequest
{
    /**
     * @return array
     */
    function toArray()
    {
        return get_object_vars($this);
    }
}