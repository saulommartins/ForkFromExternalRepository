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
    * Classe de Regra para geração do código de barra e linha digitável padrão febraban (PARA CARNES DA PREFEITURA DE PETROPOLIS)
    * Data de Criação   : 04/01/2006

    * @author Analista: Fábio Bertoldi
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @package URBEM
    * @subpackage Regra

    * $Id: RCodigoBarraFebraban.class.php 59612 2014-09-02 12:00:51Z gelson $

   * Casos de uso: uc-05.03.11
*/

/*
$Log$
Revision 1.5  2006/09/15 11:37:31  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

Revision 1.4  2006/09/15 10:26:13  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

class RCodigoBarraFebraban
{
    /*
        @param Array $info contendo:
            $arInfo = array(
                            'vencimento' => 'dd/mm/yyyy',
                            'valor_documento' => 99.99,
                            'nosso_numero' => '999999999999999999',
                            'tipo_moeda' => 9,
                            'cod_febraban' => 9999,
                           );
        @return Array contendo o codigo de barra e a linha digitavel formatada

    */
    public function geraFebraban($info)
    {
        /*
            formata o valor do documento para o codigo de barras
            casas decimais do valor deve ser separado por ponto
        */

        if (strstr($info['valor_documento'], '§')) {
            $info['valor_documento'] = explode('§', $info['valor_documento']);
            $info['valor_documento'] = $info['valor_documento'][0];
        }

        $valor = str_replace(".","",$info['valor_documento']);
        $valor = str_pad($valor,11,0,STR_PAD_LEFT);

        /*
            formata o vencimento do documento para o codigo de barras
            data no formato brasileiro dd/mm/YY
        */
        $arVencimento  = explode("/", $info['vencimento']);
        $dtVencimento = $arVencimento[2].$arVencimento[1].$arVencimento[0];

        $tipoMoeda = $info['tipo_moeda'];

        $codFebraban = str_pad($info['cod_febraban'],4,0,STR_PAD_LEFT);

        $nNumero = substr($info['nosso_numero'],0,strlen($info['nosso_numero']));
        $nNumero = str_pad($nNumero,17,0,STR_PAD_LEFT);

        $dGeral = $this->_modulo10('81'.$tipoMoeda.$valor.$codFebraban.$dtVencimento.$nNumero);

        $linha = '81'.$tipoMoeda.$dGeral.$valor.$codFebraban.$dtVencimento.$nNumero;

        $linhaFebraban = substr($linha,0,11).' '.
                         $this->_modulo10(substr($linha,0,11)).' '.
                         substr($linha,11,11).' '.
                         $this->_modulo10(substr($linha,11,11)).' '.
                         substr($linha,22,11).' '.
                         $this->_modulo10(substr($linha,22,11)).' '.
                         substr($linha,33,11).' '.
                         $this->_modulo10(substr($linha,33,11));

        $barraFebraban = $linha;

        return array(
                       'linha_digitavel' => $linhaFebraban,
                       'codigo_barras' => $barraFebraban
                    );
    }

    /*
        * Retorna o digito verificador padrao febraban
        @param String $codigo
        @return Integer $dac
    */
    public function _modulo10($codigo)
    {
        $soma  = 0;
        $acc   = 0;
        $resto = 0;
        $dac   = 0;

        $len = strlen($codigo);

        for ($i = 0; $i < $len; $i++) {
            if ($i % 2 == 0) {
                $acc .= $codigo[$i] * 2;
            } else {
                $acc .= $codigo[$i] * 1;
            }
        }

        for ($i = 0; $i < strlen($acc); $i++ ) {
            $soma += $acc[$i];
        }

        $resto = $soma % 10;

        if ($resto == 0) {
            $dac = 0;
        } else {
            $dac = 10 - $resto;
        }

        return $dac;
    }
}
