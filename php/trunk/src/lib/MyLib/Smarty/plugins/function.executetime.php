<?php
function smarty_function_executetime($params, &$smarty)
{
    return round(microtime(true) - $_SERVER['REQUEST_TIME'], 6);
}
