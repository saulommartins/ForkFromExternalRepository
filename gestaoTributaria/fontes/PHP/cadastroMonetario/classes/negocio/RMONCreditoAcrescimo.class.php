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
    * Classe de regra de negócio para MONETARIO.CREDITO_ACRESCIMO
    * Data de Criação: 20/05/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno COelho

    * @package URBEM
    * @subpackage Regra

    * $Id: RMONCreditoAcrescimo.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.05.10
                uc-02.04.03
*/

/*
$Log$
Revision 1.9  2006/11/22 15:41:36  cercato
Bug #7578#

Revision 1.8  2006/10/11 16:19:48  cercato
#7159#

Revision 1.7  2006/09/15 14:46:22  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
//include_once ( CAM_GT_MON_MAPEAMENTO."TMONEspecieCredito.class.php");
include_once ( CAM_GT_MON_MAPEAMENTO."TMONCreditoAcrescimo.class.php");
include_once ( CAM_GT_MON_MAPEAMENTO."TMONCredito.class.php");
//include_once ( CAM_GT_MON_MAPEAMENTO."TMONNaturezaCredito.class.php");
//include_once ( CAM_GT_MON_MAPEAMENTO."TMONGeneroCredito.class.php");
//include_once ( CAM_GA_NORMAS_NEGOCIO."RNorma.class.php");
//include_once ( CAM_GT_ARR_NEGOCIO."RARRGrupo.class.php");

class RMONCreditoAcrescimo
{
/**
    * @access Private
    * @var Integer
*/
var $inCodCredito;
/**
    * @access Private
    * @var Integer
*/
var $inCodAcrescimo;
/**
    * @access Private
    * @var Integer
*/
var $inCodNatureza;
/**
    * @access Private
    * @var Integer
*/
var $inCodEspecie;
/**
    * @access Private
    * @var Integer
*/
var $inCodGenero;
/**
    * @access Private
    * @var Integer
*/
var $inCodTipo;
/**
    * @access Private
    * @var OBJECT
*/
var $obTMONCreditoAcrescimo;

//SETTERS
function setCodCredito($valor) { $this->inCodCredito = $valor; }
function setCodAcrescimo($valor) { $this->inCodAcrescimo = $valor; }
function setCodEspecie($valor) { $this->inCodEspecie = $valor; }
function setCodGenero($valor) { $this->inCodGenero = $valor; }
function setCodNatureza($valor) { $this->inCodNatureza = $valor; }
function setCodTipo($valor) { $this->inCodTipo = $valor; }

//GETTERS
function getCodCredito() { return $this->inCodCredito; }
function getCodAcrescimo() { return $this->inCodAcrescimo; }
function getCodEspecie() { return $this->inCodEspecie; }
function getCodGenero() { return $this->inCodGenero; }
function getCodNatureza() { return $this->inCodNatureza; }
function getCodTipo() { return $this->inCodTipo; }

//METODO CONSTRUTOR
/**
* Método construtor
* @access Private
*/
function RMONCreditoAcrescimo()
{
    $this->obTransacao      = new Transacao;
    // instancia mapeamentos
    $this->obTMONCreditoAcrescimo    = new TMONCreditoAcrescimo;
}

/**
    * Inclui os dados referentes a Credito ACRESCIMO
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluirCreditoAcrescimo($boTransacao = "")
{
    $obTMONCreditoAcrescimo    = new TMONCreditoAcrescimo;
    $boFlagTransacao = false;

    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obTMONCreditoAcrescimo->setDado( "cod_credito", $this->getCodCredito() );
        $obTMONCreditoAcrescimo->setDado( "cod_acrescimo",$this->getCodAcrescimo());
        $obTMONCreditoAcrescimo->setDado( "cod_natureza", $this->getCodNatureza() );
        $obTMONCreditoAcrescimo->setDado( "cod_genero", $this->getCodGenero() );
        $obTMONCreditoAcrescimo->setDado( "cod_especie", $this->getCodEspecie() );
        $obTMONCreditoAcrescimo->setDado( "cod_tipo", $this->getCodTipo() );

        $obErro = $obTMONCreditoAcrescimo->inclusao( $boTransacao );
        //$obTMONCreditoAcrescimo->debug();
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTMONCreditoAcrescimo );

    return $obErro;
}

/**
    * Exclui os dados referentes a Credito ACRESCIMO
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirCreditoAcrescimo($boTransacao = "")
{
    $obTMONCreditoAcrescimo    = new TMONCreditoAcrescimo;
    $boFlagTransacao = false;

    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
    $obTMONCreditoAcrescimo->setDado( "cod_credito", $this->getCodCredito() );
    $obTMONCreditoAcrescimo->setDado( "cod_genero", $this->getCodGenero() );
    $obTMONCreditoAcrescimo->setDado( "cod_natureza", $this->getCodNatureza() );
    $obTMONCreditoAcrescimo->setDado( "cod_especie", $this->getCodEspecie() );
    $obTMONCreditoAcrescimo->setDado( "cod_acrescimo", $this->getCodAcrescimo());

    $obErro = $obTMONCreditoAcrescimo->exclusao( $boTransacao );
        //$obTMONCreditoAcrescimo->debug(); exit;
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTMONCreditoAcrescimo );

    return $obErro;
}

/**
* Lista os Créditos
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function listarCreditos(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ($this->roRARRGrupo) {
        if ( $this->roRARRGrupo->getCodGrupo() ) {
            $stFiltro .= " \n ag.cod_grupo = '".$this->roRARRGrupo->getCodGrupo()."' AND ";
        }
        if ( $this->roRARRGrupo->getExercicio() ) {
            $stFiltro .= " \n ag.ano_exercicio = '".$this->roRARRGrupo->getExercicio()."' AND ";
        }
    }
    if ( $this->getCodCredito() ) {
        $stFiltro .= " \n mc.cod_credito = '".$this->getCodCredito()."' AND ";
    }
    if ( $this->getCodEspecie() ) {
        $stFiltro .= " \n me.cod_especie = '".$this->getCodEspecie()."' AND ";
    }
    if ( $this->getCodGenero() ) {
        $stFiltro .= " \n mg.cod_genero = '".$this->getCodGenero()."' AND ";
    }
    if ( $this->getCodNatureza() ) {
        $stFiltro .= " \n mn.cod_natureza = '".$this->getCodNatureza()."' AND ";
    }
    if ( $this->getDescricao() ) {
        $stFiltro .= " \n mc.descricao_credito like '%".$this->getDescricao()."%' AND ";
    }
    if ($stFiltro) {
        $stFiltro = "     WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }
    $stOrder = "\n ORDER BY mc.cod_credito ";
    $obErro = $this->obTMONCredito->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
* Lista os Acréscimos do Crédito
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function ListarAcrescimosDoCredito(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = $stOrder = "";
    if ( $this->getCodCredito() ) {
        $stFiltro .= " \n ca.cod_credito = '".$this->getCodCredito()."' AND ";
    }

    if ( $this->getCodEspecie() ) {
        $stFiltro .= " \n ca.cod_especie = '".$this->getCodEspecie()."' AND ";
    }
    if ( $this->getCodGenero() ) {
        $stFiltro .= " \n ca.cod_genero = '".$this->getCodGenero()."' AND ";
    }
    if ( $this->getCodNatureza() ) {
        $stFiltro .= " \n ca.cod_natureza = '".$this->getCodNatureza()."' AND ";
    }

    if ( $this->getCodAcrescimo() ) {
        $stFiltro .= " \n ac.cod_acrescimo = '".$this->getCodAcrescimo()."' AND ";
    }

    if ( $this->getCodTipo() ) {
        $stFiltro .= " \n ca.cod_tipo = '".$this->getCodTipo()."' AND ";
    }

    if ($stFiltro) {
        $stFiltro = " WHERE " . substr ( $stFiltro, 0 , strlen ($stFiltro)-4) ;
    }

    $obErro = $this->obTMONCreditoAcrescimo->recuperaAcrescimosDoCredito( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

}//fim da classe
