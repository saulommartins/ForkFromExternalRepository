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
    * Classe de regra de negócio PessoalRegime
    * Data de Criação: 22/04/2005

    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Vandré Miguel Ramos

    * @package URBEM
    * @subpackage Regra

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

    Caso de uso: uc-04.04.05

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalRegime.class.php"   );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalSubDivisao.class.php"   );

class RPessoalRegime
{
/**
    * @access Private
    * @var Integer
*/
var $inCodRegime;
/**
    * @access Private
    * @var String
*/
var $stDescricao;
/**
    * @access Private
    * @var Array de Objetos
*/
var $arRPessoalSubDivisao;
/**
    * @access Private
    * @var Object
*/
var $obTRegime;
/**
    * @access Private
    * @var Object
*/
var $obTransacao;
/**
    * @access Private
    * @var Object
*/
var $roUltimoPessoalSubDivisao;
/**
    * @access Public
    * @param Integer $Valor
*/

function setCodRegime($valor) { $this->inCodRegime      = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setDescricao($valor) { $this->stDescricao  = $valor ; }
/**
    * @access Public
    * @param Object $Valor
*/
function setTRegime($valor) { $this->obTRegime      = $valor  ; }
/**
    * @access Public
    * @param Object $Valor
*/
function setRPessoalSubDivisao($valor) { $this->obRPessoalSubDivisao       = $valor  ; }

/**
    * @access Public
    * @return Integer
*/
function getCodRegime() { return $this->inCodRegime                ; }
/**
    * @access Public
    * @return String
*/
function getDescricao() { return $this->stDescricao                     ; }
/**
    * @access Public
    * @return Object
*/
function getTRegime() { return $this->obTRegime      ; }
/**
    * @access Public
    * @return Object
*/
function getRPessoalSubDivisao() { return $this->obRPessoalSubDivisao            ; }

/**
     * Método construtor
     * @access Private
*/
function RPessoalRegime()
{
    $this->setTRegime             ( new TPessoalRegime        );
    $this->obTransacao         = new Transacao;
    $this->arRPessoalSubDivisao   = array();
}

/**
* Adiciona um array de referencia-objeto
* @access Public
*/
function addPessoalSubDivisao()
{
   $this->arRPessoalSubDivisao[]      =  new RPessoalSubDivisao($this);
   $this->roUltimoPessoalSubDivisao   = &$this->arRPessoalSubDivisao[ count($this->arRPessoalSubDivisao) - 1 ];
}
/**
    * Inclui dados do Regime no banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluirRegime($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    //if ( !$obErro->ocorreu() ) {
        //$stFiltro = " where  descricao = '". $this->getDescricao() . "'";
        //$this->obTRegime->recuperaTodos( $rsRecordSet, $stFiltro, '', $boTransacao );
        //if ( $rsRecordSet->getNumLinhas() > 0 ) {
        //   $obErro->setDescricao( 'Já existe um regime cadastrado com esta descrição' );
        //}

        //if ( !$obErro->ocorreu() ) {
        //    $obErro = new erro;
        //    $obErro =  $this->obTRegime->proximoCod( $inCodRegime , $boTransacao );
        //    $this->setCodRegime($inCodRegime);
        //}
        //if ( !$obErro->ocorreu() ) {
        //     $this->obTRegime->setDado("cod_regime"             , $this->getCodRegime() );
        //     $this->obTRegime->setDado("descricao"              , $this->getDescricao() );
        //     $obErro = $this->obTRegime->inclusao( $boTransacao );
        //}
    //}
    if (!$obErro->ocorreu()) {
        foreach ($this->arRPessoalSubDivisao  as $obRPessoalSubDivisao) {
            $obErro =  $obRPessoalSubDivisao->incluirSubDivisao($boTransacao);
            if ($obErro->ocorreu()) {
                break;
            }
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTRegime );
    }

    return $obErro;
 }

/**
    * Altera dados do Regime no banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function AlterarRegime($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $inCodSubDivisaoTemp = $this->roUltimoPessoalSubDivisao->getCodSubDivisao();
        $this->roUltimoPessoalSubDivisao->setCodSubDivisao(null);
        $this->roUltimoPessoalSubDivisao->listarSubDivisao($rsSubDivisao,$stFiltro,$boTransacao);
        $this->roUltimoPessoalSubDivisao->setCodSubDivisao($inCodSubDivisaoTemp);
        while (!$rsSubDivisao->eof()) {
            $arSubDivisao[] = $rsSubDivisao->getCampo('cod_sub_divisao');
            $rsSubDivisao->proximo();
        }
        foreach ($this->arRPessoalSubDivisao  as $obRPessoal) {
            $arSubDivisao2[]= $obRPessoal->getCodSubDivisao();
        }
        if (($arSubDivisao) && ($arSubDivisao2)) {
            $arSubDivisao  = array_diff($arSubDivisao,$arSubDivisao2);
            $aux = $this->roUltimoPessoalSubDivisao->getCodSubDivisao();
            foreach ($arSubDivisao as $arAux) {
                $this->roUltimoPessoalSubDivisao->setCodSubDivisao($arAux);
                $obErro = $this->roUltimoPessoalSubDivisao->excluirSubDivisao($boTransacao);
                if ($obErro->ocorreu()) {
                    break;
                }
            }
            $this->roUltimoPessoalSubDivisao->setCodSubDivisao($aux);
        }
        if (!$obErro->ocorreu()) {
            foreach ($this->arRPessoalSubDivisao  as $obRPessoalSubDivisao) {
                if ($obRPessoalSubDivisao->getCodSubDivisao() == '' && $obRPessoalSubDivisao->getDescricao()!='') {
                    $obErro =  $obRPessoalSubDivisao->incluirSubDivisao($boTransacao);
                } else {
                    $obErro =  $obRPessoalSubDivisao->alterarSubDivisao($boTransacao);
                }
                if ($obErro->ocorreu()) {
                    break;
                }
            }
        }
     }
     $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTRegime );

     return $obErro;

 }
/**
    * Exclui dados do Regime no banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirRegime($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if (!$obErro->ocorreu()) {
        $this->obTRegime->setDado("cod_regime", $this->getCodRegime() );
        $obErro = $this->obTRegime->validaExclusao("", $boTransacao);
        if (!$obErro->ocorreu()) {
            foreach ($this->arRPessoalSubDivisao  as $obRPessoalSubDivisao) {
                $obErro =  $obRPessoalSubDivisao->excluirSubDivisao($boTransacao);
                if ($obErro->ocorreu()) {
                    break;
                }
            }
            $obErro = $this->obTRegime->exclusao( $boTransacao );
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTRegime );

        return $obErro;
    }
 }

/**
    * Executa um recuperaLista na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarRegime(&$rsRecordSet, $stOrder = "", $boTransacao = "")
{
    $stFiltro = "";
    if ($this->getCodRegime()) {
        $stFiltro = " WHERE cod_regime = ".$this->getCodRegime();
    }
    $obErro = $this->obTRegime->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}
function listarRegimeSemInternos(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = " WHERE cod_regime NOT IN (1, 2) ";
    if ($this->getCodRegime()) {
        $stFiltro .= " AND cod_regime = ".$this->getCodRegime();
    }
    $stOrder = " descricao ";
    $obErro = $this->obTRegime->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}
}
