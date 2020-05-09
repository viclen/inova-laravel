<?php

namespace App;

class Formatter
{
    public static function soNumeros($entrada)
    {
        $permitido = "1234567890";
        $saida = "";

        for ($i = 0; $i < strlen($entrada); $i++) {
            $c = substr($entrada, $i, 1);
            if (strpos($permitido, $c) !== false) {
                $saida .= $c;
            }
        }

        return $saida;
    }

    public static function valor($entrada)
    {
        if (!$entrada) {
            return "R$ 0,00";
        }

        $entrada = str_replace(".", ",", $entrada . "");

        $permitido = "1234567890";
        $decimal = 0;
        $saida = "";

        for ($i = 0; $i < strlen($entrada); $i++) {
            $c = substr($entrada, $i, 1);
            if (strpos($permitido, $c) !== false && $decimal < 3) {
                $saida .= $c;
                $decimal = $decimal > 0 ? $decimal + 1 : 0;
            }
            if ($c == "," && !$decimal) {
                $saida .= $c;
                $decimal = 1;
            }
        }

        if ($decimal == 0) {
            $saida .= ",00";
        } else if ($decimal == 1) {
            $saida .= "00";
        } else if ($decimal == 2) {
            $saida .= "0";
        }

        $saidacomponto = "";
        $count = 0;
        for ($i = strpos($saida, ",") - 1; $i >= 0; $i--) {
            if ($count > 2) {
                $saidacomponto = "." . $saidacomponto;
                $count = 0;
            }
            $count++;
            $saidacomponto = substr($saida, $i, 1) . $saidacomponto;
        }
        $saida = $saidacomponto . "," . explode(",", $saida)[1];

        return "R$ " . $saida;
    }

    public static function boolean($entrada)
    {
        if ($entrada) {
            return 'Sim';
        } else {
            return 'Não';
        }
    }

    public static function mil($entrada)
    {
        $entrada = "$entrada";

        $saidacomponto = "";
        $count = 0;
        for ($i = strlen($entrada) - 1; $i >= 0; $i--) {
            if ($count > 2) {
                $saidacomponto = "." . $saidacomponto;
                $count = 0;
            }
            $count++;
            $saidacomponto = substr($entrada, $i, 1) . $saidacomponto;
        }
        return $saidacomponto;
    }
}
