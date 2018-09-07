<?php

/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

class IPUtils {

    /**
     * Check if a given ipv4 address is in a given cidr
     * @param  string $ip    IP to check in IPV4 format eg. 127.0.0.1
     * @param  string $range IP/CIDR netmask eg. 127.0.0.0/24, also 127.0.0.1 is accepted and /32 assumed
     * @return boolean true if the ip is in this range / false if not.
     * @author Thorsten Ott <https://gist.github.com/tott/7684443>
     */
    public static function ip4_in_cidr($ip, $cidr) {
        if (strpos($cidr, '/') == false) {
            $cidr .= '/32';
        }
        // $range is in IP/CIDR format eg 127.0.0.1/24
        list( $cidr, $netmask ) = explode('/', $cidr, 2);
        $range_decimal = ip2long($cidr);
        $ip_decimal = ip2long($ip);
        $wildcard_decimal = pow(2, ( 32 - $netmask)) - 1;
        $netmask_decimal = ~ $wildcard_decimal;
        return ( ( $ip_decimal & $netmask_decimal ) == ( $range_decimal & $netmask_decimal ) );
    }

    /**
     * Check if a given ipv6 address is in a given cidr
     * @param string $ip IP to check in IPV6 format
     * @param string $cidr CIDR netmask
     * @return boolean true if the IP is in this range, false otherwise.
     * @author MW. <https://stackoverflow.com/a/7952169>
     */
    public static function ip6_in_cidr($ip, $cidr) {
        $address = inet_pton($ip);
        $subnetAddress = inet_pton(explode("/", $cidr)[0]);
        $subnetMask = explode("/", $cidr)[1];

        $addr = str_repeat("f", $subnetMask / 4);
        switch ($subnetMask % 4) {
            case 0:
                break;
            case 1:
                $addr .= "8";
                break;
            case 2:
                $addr .= "c";
                break;
            case 3:
                $addr .= "e";
                break;
        }
        $addr = str_pad($addr, 32, '0');
        $addr = pack("H*", $addr);

        $binMask = $addr;
        return ($address & $binMask) == $subnetAddress;
    }

    /**
     * Check if the REMOTE_ADDR is on Cloudflare's network.
     * @return boolean true if it is, otherwise false
     */
    public static function validateCloudflare() {
        if (filter_var($_SERVER["REMOTE_ADDR"], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            // Using IPv6
            $cloudflare_ips_v6 = [
                "2400:cb00::/32",
                "2405:8100::/32",
                "2405:b500::/32",
                "2606:4700::/32",
                "2803:f800::/32",
                "2c0f:f248::/32",
                "2a06:98c0::/29"
            ];
            $valid = false;
            foreach ($cloudflare_ips_v6 as $cidr) {
                if (ip6_in_cidr($_SERVER["REMOTE_ADDR"], $cidr)) {
                    $valid = true;
                    break;
                }
            }
        } else {
            // Using IPv4
            $cloudflare_ips_v4 = [
                "103.21.244.0/22",
                "103.22.200.0/22",
                "103.31.4.0/22",
                "104.16.0.0/12",
                "108.162.192.0/18",
                "131.0.72.0/22",
                "141.101.64.0/18",
                "162.158.0.0/15",
                "172.64.0.0/13",
                "173.245.48.0/20",
                "188.114.96.0/20",
                "190.93.240.0/20",
                "197.234.240.0/22",
                "198.41.128.0/17"
            ];
            $valid = false;
            foreach ($cloudflare_ips_v4 as $cidr) {
                if (ip4_in_cidr($_SERVER["REMOTE_ADDR"], $cidr)) {
                    $valid = true;
                    break;
                }
            }
        }
        return $valid;
    }

    /**
     * Makes a good guess at the client's real IP address.
     *
     * @return string Client IP or `0.0.0.0` if we can't find anything
     */
    public static function getClientIP() {
        // If CloudFlare is in the mix, we should use it.
        // Check if the request is actually from CloudFlare before trusting it.
        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
            if (validateCloudflare()) {
                return $_SERVER["HTTP_CF_CONNECTING_IP"];
            }
        }

        if (isset($_SERVER["REMOTE_ADDR"])) {
            return $_SERVER["REMOTE_ADDR"];
        }

        return "0.0.0.0"; // This will not happen unless we aren't a web server
    }

}
