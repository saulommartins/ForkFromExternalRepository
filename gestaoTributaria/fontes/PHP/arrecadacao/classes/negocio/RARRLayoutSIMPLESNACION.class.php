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
    * Classe de Regra de Negócio para Arquivo de Baixa com Layout da SIMPLESNACION
    * Data de Criação   : 17/09/2007

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @package URBEM
    * @subpackage Regra

    * $Id: RARRLayoutSIMPLESNACION.class.php 59612 2014-09-02 12:00:51Z gelson $

   * Casos de uso: uc-05.03.10
*/

/*
$Log$
Revision 1.1  2007/09/17 15:33:14  cercato
Ticket#10190#

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMCadastroEconomico.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCadastroEconomicoFaturamento.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCalculo.class.php" );

set_time_limit(0);

class RARRLayoutSIMPLESNACION extends RARRPagamento
{
/**
    * @var Object
    * @access Private
*/
var $obTransacao;
/**
    * @access Private
    * @var Array
*/
var $arDadosArquivo;

// SETTERS
/**
    * @access Public
    * @param Array $valor
*/
function setDadosArquivo($valor) { $this->arDadosArquivo = $valor; }

// GETTERES
/**
    * @access Public
    * @param Array $valor
*/
function getDadosArquivo() { return $this->arDadosArquivo;  }

/**
     * Método construtor
     * @access Private
*/
function RARRLayoutSIMPLESNACION()
{
    parent::RARRPagamento();
    $this->obTransacao = new Transacao;
}

/**
    * Verifica o layout do arquivo de baixa
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function efetuarBaixa($arDadosArquivo, $boTransacao = "")
{
    ;
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( $obErro->ocorreu() ) {
        return $obErro;
    }

    foreach ($arDadosArquivo AS $key => $stValorLinha) {
        array_shift( $arDadosArquivo );
        $stPrimeiroElemento = substr( $stValorLinha, 0, 1 );

        //HEADER
        if ($stPrimeiroElemento == 1) { //header
            $stCodBanco = substr( $stValorLinha, 75, 3 );
            $stDataGeracaoDia = substr( $stValorLinha, 35, 2 );
            $stDataGeracaoMes = substr( $stValorLinha, 33, 2 );
            $stDataGeracaoAno = substr( $stValorLinha, 29, 4 );
        }else
            if ($stPrimeiroElemento == 2) { //dados
                $stCNPJ = substr( $stValorLinha, 74, 14 );
                $obTCEMCadastroEconomico = new TCEMCadastroEconomico;
                $obTCEMCadastroEconomico->recuperaInscricaoEconomica( $rsInscricaoEconomica, $stCNPJ, $boTransacao );
                if ( !$rsInscricaoEconomica->Eof() ) {
                    $stCompetenciaMes = substr( $stValorLinha, 104, 2 );
                    $stCompetenciaAno = substr( $stValorLinha, 100, 4 );
                    $obTARRCadastroEconomicoFaturamento = new TARRCadastroEconomicoFaturamento;
                    $obTARRCadastroEconomicoFaturamento->setDado( "inscricao_economica", $rsInscricaoEconomica->getCampo("inscricao_economica") );
                    $obTARRCadastroEconomicoFaturamento->setDado( "competencia", "'".$stCompetenciaMes."/".$stCompetenciaAno."'" );
                    $obTARRCadastroEconomicoFaturamento->inclusao( $boTransacao );

                    $obTARRCalculo = new TARRCalculo;
                }
            }

    }

    //echo "terminou<br>";exit;
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRPagamento );

    return $obErro;
}

} // fecha classe

?>
