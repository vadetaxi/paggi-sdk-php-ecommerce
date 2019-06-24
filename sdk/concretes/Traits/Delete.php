<?php
/**
 * This file holds the top level logic for any delete methods
 *
 * PHP version 5.6, 7.0, 7.1, 7.2
 *
 * @category Delete_File
 * @package  Paggi
 * @author   Paggi Integracoes <ti-integracoes@paggi.com>
 * @license  GNU GPLv3 https://www.gnu.org/licenses/gpl-3.0.en.html
 * @link     http://developers.paggi.com
 */
namespace Paggi\SDK\Traits;

/**
 * Trait Delete - Delete some resource
 *
 * @category Delete_Trait
 * @package  Paggi
 * @author   Paggi Integracoes <ti-integracoes@paggi.com>
 * @license  GNU GPLv3 https://www.gnu.org/licenses/gpl-3.0.en.html
 * @link     http://developers.paggi.com
 * */
trait Delete
{
    /**
     * DELETE METHOD
     *
     * @param $id      The id of the object who will be deleted
     * @param $timeout false or integer as seconds to timeout the request
     *
     * @return boolean Result of the deletion
     */
    public static function delete($idObject, $timeout = false)
    {
        $class = new \ReflectionClass(self::class);
        $response = self::makeRequest($class, "Delete", [], [], $idObject, "", $timeout);
        return self::manageResponse($response);
    }
}
