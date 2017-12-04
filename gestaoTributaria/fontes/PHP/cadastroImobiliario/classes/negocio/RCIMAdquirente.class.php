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
     * Classe de regra de negócio para adquirente
     * Data de Criação: 03/12/2004

     * @author Analista: Ricardo Lopes de Alencar
     * @author Desenvolvedor: Vitor Davi Valentini

     * @package URBEM
     * @subpackage Regra

    * $Id: RCIMAdquirente.class.php 59612 2014-09-02 12:00:51Z gelson $

     * Casos de uso: uc-05.01.17
*/

/*
$Log$
Revision 1.5  2007/08/07 18:05:53  cercato
Bug#9836#

Revision 1.4  2006/09/18 09:12:40  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMTransferenciaAdquirente.class.php" );
include_once ( CAM_GA_CGM_NEGOCIO. "RCGM.class.php"                           );

class RCIMAdquirente
{
/**
    * @access Private
    * @var Integer
*/
var $inCodigoTransferencia;
/**
    * @access Private
    * @var Integer
*/
var $inNumeroCGM;
/**
    * @access Private
    * @var Integer
*/
var $inOrdem;
/**
    * @access Private
    * @var Float
*/
var $flCota;
/**
    * @access Private
    * @var Array
*/
var $arAdquirentes;
/**
    * @access Private
    * @var Object
*/
var $obTCIMTransferenciaAdquirente;
/**
    * @access Private
    * @var Object
*/
var $obRCGM;
/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoTransferencia($valor) { $this->inCodigoTransferencia = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setNumeroCGM($valor) { $this->inNumeroCGM           = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setOrdem($valor) { $this->inOrdem               = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setCota($valor) { $this->flCota                = $valor; }
/**
    * @access Public
    * @param Array $valor
*/
function setAdquirentes($valor) { $this->arAdquirentes         = $valor; }
/**
    * @access Public
    * @return Integer
*/
function getCodigoTransferencia() { return $this->inCodigoTransferencia; }
/**
    * @access Public
    * @return Integer
*/
function getNumeroCGM() { return $this->inNumeroCGM;           }
/**
    * @access Public
    * @return String
*/
function getOrdem() { return $this->inOrdem;               }
/**
    * @access Public
    * @return Boolean
*/
function getCota() { return $this->inCota;                }
/**
    * @access Public
    * @return Array
*/
function getAdquirentes() { return $this->arAdquirentes;         }
/**
     * Método construtor
     * @access Private
*/
function RCIMAdquirente()
{
    $this->obTCIMTransferenciaAdquirente = new TCIMTransferenciaAdquirente;
    $this->obRCGM                        = new RCGM;
    $this->obTransacao                   = new Transacao;
}
/**
    * Inclui os dados setados na tabela de Transferencia Adquirente
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluirAdquirente($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->salvarAdquirentes( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMTransferenciaAdquirente );

    return $obErro;
}
/**
    * Altera os dados dos Adquirentes da Transferência selecionado no banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterarAdquirente($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->salvarAdquirentes( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMTransferenciaAdquirente );

    return $obErro;
}
/**
    * Exclui os Adquirentes da Transferência selecionado do banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirAdquirente($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTCIMTransferenciaAdquirente->setDado( "cod_transferencia", $this->inCodigoTransferencia );
        $obErro = $this->obTCIMTransferenciaAdquirente->exclusao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMTransferenciaAdquirente );

    return $obErro;
}
/**
    * Recupera do banco de dados os dados de Adquirentes da Transferência selecionado
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultarAdquirentes($boTransacao = "")
{
    $this->obTCIMTransferenciaAdquirente->setDado( "cod_transferencia", $this->inCodigoTransferencia );
    $obErro = $this->obTCIMTransferenciaAdquirente->recuperaPorChave( $rsAdquirentes, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $arConteudoAdquirentes = array();
        $inId                  = 0;
        $rsCGM                 = new Recordset;
        $this->arAdquirentes = array();
        while ( !$rsAdquirentes->eof() ) {
            $this->obRCGM->setNumCGM( $rsAdquirentes->getCampo( "numcgm" ) );
            $this->obRCGM->consultar( $rsCGM, $boTransacao );

            $arConteudoAdquirentes[ "inId"   ] = ++$inId;
            $arConteudoAdquirentes[ "codigo" ] = $rsAdquirentes->getCampo( "numcgm" );
            $arConteudoAdquirentes[ "nome"   ] = $this->obRCGM->getNomCGM();
            $arConteudoAdquirentes[ "quota"  ] = $rsAdquirentes->getCampo( "cota" );
            $this->arAdquirentes[]             = $arConteudoAdquirentes;
            $rsAdquirentes->proximo();
        }
    }

    return $obErro;
}
/**
    * Altera os dados do Adquirente da Transferência selecionado no banco de dados
    * @access Public
    * @param  Object $rsNaturezaTransferencia Objeto RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function salvarAdquirentes($boTransacao = "")
{
    $this->obTCIMTransferenciaAdquirente->setDado( "cod_transferencia", $this->inCodigoTransferencia );
    $obErro = $this->obTCIMTransferenciaAdquirente->exclusao( $boTransacao );
    $inTotalArray = count( $this->arAdquirentes  ) - 1;
    if ($inTotalArray >= 0) {
        $inTotalQuota = 0;
        foreach ($this->arAdquirentes as $inChave => $arElementos) {
            $inTotalQuota += $arElementos['quota'];
        }
        if ( (number_format($inTotalQuota,2,".","") - 100) == 0 ) {
            for ($inCount = 0; $inCount <= $inTotalArray; $inCount ++) {
                 $this->obTCIMTransferenciaAdquirente->setDado( "cod_transferencia", $this->inCodigoTransferencia             );
                 $this->obTCIMTransferenciaAdquirente->setDado( "numcgm"           , $this->arAdquirentes[$inCount]['codigo'] );
                 $this->obTCIMTransferenciaAdquirente->setDado( "ordem"            , $inCount + 1                             );
                 $this->obTCIMTransferenciaAdquirente->setDado( "cota"             , $this->arAdquirentes[$inCount]['quota']  );
                 $obErro = $this->obTCIMTransferenciaAdquirente->inclusao( $boTransacao );
            }
        } else {
            $obErro->setDescricao( "A soma das quotas dos adquirentes deve ser igual a 100%!" );
        }
    } else {
        $obErro->setDescricao( "Deverá ser informado no mínimo um Adquirente!" );
    }

    return $obErro;
}
}
?>
