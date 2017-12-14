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
    * Classe de Regra para geração do código de barra e linha digitável padrão Ficha de Compensação para o Banco Caixa
    * Data de Criação   : 04/10/2009

    * @author Davi Ritter Aroldi

    * @package URBEM
    * @subpackage Regra

   * Casos de uso: uc-05.03.11
*/

class RCodigoBarraFichaCompensacaoCaixa
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
        $valor = str_replace(".","",$info['valor_documento']);
        $valor = str_pad($valor,10,0,STR_PAD_LEFT);

        $tipoMoeda = $info['tipo_moeda'];
        $stConvenio = str_pad( $info['convenio'], 6, 0, STR_PAD_LEFT );
        $nNumero = $info['nosso_numero'];
        $stFator = $info['fator_vencimento'];
        $stAgCodCedente = $info['ag_cod_cedente'];

        //$stDV = digito verificador das posicoes 1 ate 4 + 6 ate 44
        $stDVModOnze = $this->_modulo11( '104'.$tipoMoeda.$stFator.$valor.$nNumero.$stAgCodCedente );

        //$stFator  vou fazer vir do banco, eh a data de vencimento - 07/10/1997 (para pagamentos que nao podem ser feitos apos vencimento tacar mais 15 dias)
        $barraFebraban = '104'.$tipoMoeda.$stDVModOnze.$stFator.$valor.$nNumero.$stAgCodCedente;

        $campo1 = '104'.$tipoMoeda.$barraFebraban[20-1].".".$barraFebraban[21-1].$barraFebraban[22-1].$barraFebraban[23-1].$barraFebraban[24-1];
        $stDV = $this->_modulo10( '104'.$tipoMoeda.$barraFebraban[20-1].$barraFebraban[21-1].$barraFebraban[22-1].$barraFebraban[23-1].$barraFebraban[24-1] );
        $campo1 .= $stDV;

        $campo2 = $barraFebraban[25-1].$barraFebraban[26-1].$barraFebraban[27-1].$barraFebraban[28-1].$barraFebraban[29-1].".".$barraFebraban[30-1].$barraFebraban[31-1].$barraFebraban[32-1].$barraFebraban[33-1].$barraFebraban[34-1];
        $stDV = $this->_modulo10( $barraFebraban[25-1].$barraFebraban[26-1].$barraFebraban[27-1].$barraFebraban[28-1].$barraFebraban[29-1].$barraFebraban[30-1].$barraFebraban[31-1].$barraFebraban[32-1].$barraFebraban[33-1].$barraFebraban[34-1] );
        $campo2 .= $stDV;

        $campo3 = $barraFebraban[35-1].$barraFebraban[36-1].$barraFebraban[37-1].$barraFebraban[38-1].$barraFebraban[39-1].".".$barraFebraban[40-1].$barraFebraban[41-1].$barraFebraban[42-1].$barraFebraban[43-1].$barraFebraban[44-1];
        $stDV = $this->_modulo10( $barraFebraban[35-1].$barraFebraban[36-1].$barraFebraban[37-1].$barraFebraban[38-1].$barraFebraban[39-1].$barraFebraban[40-1].$barraFebraban[41-1].$barraFebraban[42-1].$barraFebraban[43-1].$barraFebraban[44-1] );
        $campo3 .= $stDV;

        $campo4 = $stDVModOnze;

        $campo5 = $stFator.$valor;
        $linhaFebraban = $campo1." ".$campo2." ".$campo3." ".$campo4." ".$campo5;

        return array( 'linha_digitavel' => $linhaFebraban, 'codigo_barras' => $barraFebraban );
    }

    public function _modulo11($codigo)
    {
        $len = strlen($codigo);
        $inSoma = 0;
        $inPosicao = 2;
        for ($i = $len-1; $i >= 0; $i--) {
            $inSoma += $codigo[$i] * $inPosicao;
            $inPosicao++;
            if ($inPosicao > 9)
                $inPosicao = 2;
        }

        $resto = $inSoma % 11;

        $resto = 11 - $resto;
        if ( ( $resto == 0 ) || ( $resto == 10 ) || ( $resto == 11 ) )
            return 1;
        else
            return $resto;
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

        $len = strlen($codigo)-1;
        $boDois = true;
        for ($i = $len; $i >= 0; $i--) {
            if ($boDois) {
                $acc .= $codigo[$i] * 2;
            } else {
                $acc .= $codigo[$i] * 1;
            }

            $boDois = !$boDois;
        }

        for ($i = 0; $i < strlen($acc); $i++ ) {
            $soma += $acc[$i];
        }

        if ( $soma < 10 )
            $dac = 10 - $soma;
        else {
            $resto = substr( $soma, 0, strlen($soma)-1 );
            $resto++;
            for ( $inx=0; $inx<strlen($soma)-(strlen($soma)-1); $inx++ ) {
                $resto .= "0";
            }

            $dac = $resto - $soma;
            if ( $dac == 10 )
                $dac = 0;
        }

        return $dac;
    }
}
