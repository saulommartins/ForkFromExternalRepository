<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
 * Classe Utilitária para tratamento de strings
 * Data de Criação: 12/03/2009
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author Marcio Medeiros <marcio.medeiros>
 * @package GF
 * @subpackage LDO
 */
class LDOString
{

    /**
     * Converte float em string formato moeda.
     *
     * @param  float  $flValor
     * @return string
     */
    public static function retornarValorMonetario($flValor)
    {
        # Obtém parte inteira e decimal do número.
        list($stInt, $stDec) = explode('.', $flValor);

        $stInt = $stInt ? $stInt : '0';
        $stDec = $stDec ? $stDec : '0';

        $arNum = array();
        $stNum = '';
        $j = 0;

        for ($i = strlen($stInt) - 1; $i >= 0; $i--) {
            $stNum = $stInt[$i] . $stNum;

            if ((++$j % 3 == 0) && $i) {
                $stNum = '.' . $stNum;
            }
        }

        return $stNum . ',' . substr($stDec . '00', 0, 2);
    }

    /**
     * Formata um valor para tipo float
     *
     * @param  string $stValor
     * @param  int    $inTipoOrigem (Padrão 1 - Monetário)
     * @return float
     * @example $flValor = LDOString::retornarValorFloat($stValor);
     * @ignore Para novos tipos de origem apenas acrescente em switch ($inTipoOrigem)
     */
    public static function retornarValorFloat($stValor, $inTipoOrigem = 1)
    {
        switch ($inTipoOrigem) {
            case 1:
                // Tipo monetário
                $valorNovo = str_replace ( '.', '',  $stValor);
                $valorNovo = str_replace ( ',', '.', $valorNovo);
                break;
        }
        $valorNovo = floatval($valorNovo);

        return $valorNovo;
    }

    /**
     * Retira acentuação de uma string.
     *
     * @param string
     * @return string
     * @ignore Este método pode ser reescrito para utilizar regex
     */
    public static function retirarAcento($string)
    {
        $char = array("ç" => "c", "Ç" => "C", "à" => "a", "À" => "A",
                      "á" => "a", "Á" => "A", "ã" => "a", "Ã" => "A",
                      "â" => "a", "Â" => "A", "è" => "e", "È" => "E",
                      "é" => "e", "É" => "E", "ê" => "e", "Ê" => "E",
                       "ì" => "i", "Ì" => "I", "í" => "i", "Í" => "I",
                      "î" => "i", "Î" => "I", "ò" => "o", "Ò" => "O",
                      "ó" => "o", "Ó" => "O", "õ" => "o", "Õ" => "O",
                      "ô" => "o", "Ô" => "O", "ù" => "u", "Ù" => "U",
                      "ú" => "u", "Ú" => "U", "û" => "u", "Û" => "U");

        $strTratada = strtr($string, $char);

        return $strTratada;
    }

    public static function formatString($str)
    {
        /**
        * Retira tudo que nao for letras e acentos
        * Retira tambem a acentuacao das letras
        **/
        $a = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿ??';
        $b = 'aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr';
        $str = preg_replace( "/[^a-zA-Z0-9 ]/", "", strtr($str, $a, $b));

        // Converte a string para maiuscula e Retira os espacos em branco do inicio e do fim
        $str = trim(strtoupper($str));

        $formated_string = '';

        /**
        * Retira as letras repetidas quando forem uma apos a outra,
        * CASO A STRING SEJA ALFANUMÉRICA, SE FOR SOMENTE NÚMEROS NÃO ENTRARÁ
        * Tesstte vira Teste
        **/

        if (!preg_match('/^-?\d+?$/', $str)) {
            for ($i = 0; $i <= strlen($str); $i++) {
                if (substr($str,$i,1) != substr($formated_string,-1)) {
                    $formated_string .= substr($str,$i,1);
                }
            }
        } else {
            $formated_string = $str;
        }

        // Substitui Z por S e retira os H's
        $str = str_replace('H','', str_replace('Z','S',$formated_string));

        // Separa a string por palavras
        $arStr = explode(' ', $str);

        // Se a quantidade de palavras for 1, retorna ela, caso contrario, entra no if
        if (count($arStr) > 1) {
            $first_word = array_shift($arStr);
            $last_word = array_pop($arStr);
            // Atribui vazio para a primeira posicao do array, para ter espaco entre a primeira e a ultima palavra
            $middle_word[] = '';
            // Percorre cada palavra restante, concatenando a primeira letra com um espaco
            foreach ($arStr as $name) {
                // Se concatena se a palavra tiver mais de 2 letras
                if (strlen($name) > 2) {
                    $middle_word[] = substr($name,0,1);
                }
            }
            //Forma a string para retorna-la
            $str = $first_word.implode(' ',$middle_word).' '.$last_word;
        } else {
            $str = $arStr[0];
        }

        return $str;
    }

    public static function validateSimilarity($stValue1, $stValue2, $inPercent = 90)
    {
        similar_text(self::formatString($stValue1), self::formatString($stValue2), $inPerc);

        $boReturn = false;
        if ($inPerc >= $inPercent) {
            $boReturn = true;
        }

        return $boReturn;
    }
}
