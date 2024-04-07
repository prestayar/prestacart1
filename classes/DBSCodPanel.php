<?php
/**
 * PrestaCart      Cart Module For Prestashop
 *
 * @DBSCore        Compatible with DBSCore V11Cart
 * @website        PrestaYar.com
 * @copyright	   (c) 2017 - PrestaYar Team
 * @author         Hashem Afkhami <hashem_afkhami@yahoo.com>
 * @since          02 Jan 2017
 */

class PsCodPanel extends PsCartParent
{
    /**
     * get Url Content With CURL
     *
     */
    public static function getUrlContent($url = NULL)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $response = curl_exec($curl);
        if ($response === FALSE) {
            $info = curl_getinfo($curl);
            curl_close($curl);
            return FALSE;
        }
        curl_close($curl);
        return $response;
    }

    /**
     * send Request CURL
     *
     */
    public static function sendRequest($url = NULL, $data = array() )
    {
        $curl = curl_init($url);
        curl_setopt($curl,CURLOPT_POSTFIELDS,http_build_query($data));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $response = curl_exec($curl);
        if ($response === FALSE) {
            $info = curl_getinfo($curl);
            curl_close($curl);
            return FALSE;
        }
        curl_close($curl);
        return $response;
    }
}