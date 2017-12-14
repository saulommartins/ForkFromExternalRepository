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
    * Classe de regra de negocio para MONETARIO.MOEDA
    * Data de Criacao: 16/12/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho

    * @package URBEM
    * @subpackage Regra

    * $Id: RMONMoeda.class.php 60940 2014-11-25 18:03:14Z michel $

* Casos de uso: uc-05.05.06
*/

/*
$Log$
Revision 1.9  2006/09/15 14:46:22  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_MON_MAPEAMENTO."TMONMoeda.class.php" );
include_once ( CAM_GT_MON_MAPEAMENTO."TMONRegraConversaoMoeda.class.php" );

class RMONMoeda
{
/**
    * @access Private
    * @var Integer
*/
var $inCodMoeda;
/**
    * @access Private
    * @var Integer
*/
var $inCodModulo;
/**
    * @access Private
    * @var Integer
*/
var $inCodBiblioteca;
/**
    * @access Private
    * @var Integer
*/
var $inCodFuncao;
/**
    * @access Private
    * @var String
*/
var $stDescSingular;
/**
    * @access Private
    * @var String
*/
var $stDescPlural;
/**
    * @access Private
    * @var String
*/
var $stFracaoSingular;
/**
    * @access Private
    * @var String
*/
var $stFracaoPlural;
/**
    * @access Private
    * @var String
*/
var $stSimbolo;
/**
    * @access Private
    * @var Date
*/
var $dtVigencia;
/**
    * @access Private
    * @var Object
*/
var $obTMONMoeda;
/**
    * @access Private
    * @var Object
*/
var $obTMONRegraConversaoMoeda;
/**
    * @access Private
    * @var String
*/
var $strFormula;
/**
    * @access Private
    * @var String
*/
var $strFormulaAntiga;

//SETTERS
function setCodMoeda($valor) { $this->inCodMoeda = $valor; }
function setDescSingular($valor) { $this->stDescSingular = $valor; }
function setDescPlural($valor) { $this->stDescPlural = $valor; }
function setFracaoSingular($valor) { $this->stFracaoSingular = $valor; }
function setFracaoPlural($valor) { $this->stFracaoPlural = $valor; }
function setSimbolo($valor) { $this->stSimbolo = $valor; }
function setDtVigencia($valor) { $this->dtVigencia = $valor; }

function setCodModulo($valor) { $this->inCodModulo = $valor; }
function setCodBiblioteca($valor) { $this->inCodBiblioteca = $valor; }
function setCodFuncao($valor) { $this->inCodFuncao = $valor; }
function setStrFormula($valor) { $this->strFormula = $valor; }
function setStrFormulaAntiga($valor) { $this->strFormulaAntiga = $valor; }

//GETTERS
function getCodMoeda() { return $this->inCodMoeda; }
function getDescSingular() { return $this->stDescSingular; }
function getDescPlural() { return $this->stDescPlural; }
function getFracaoSingular() { return $this->stFracaoSingular; }
function getFracaoPlural() { return $this->stFracaoPlural; }
function getSimbolo() { return $this->stSimbolo; }
function getDtVigencia() { return $this->dtVigencia; }

function getCodModulo() { return $this->inCodModulo; }
function getCodBiblioteca() { return $this->inCodBiblioteca; }
function getCodFuncao() { return $this->inCodFuncao; }
function getStrFormula() { return $this->strFormula; }
function getStrFormulaAntiga() { return $this->strFormulaAntiga; }

//METODO CONSTRUTOR
/**
     * Metodo construtor
     * @access Private
*/
function RMONMoeda()
{
    $this->obTMONMoeda  = new TMONMoeda;
    $this->obTMONRegraConversaoMoeda  = new TMONRegraConversaoMoeda;
    $this->obTransacao  = new Transacao;
}

/**
* Inclui os dados setados na tabela MONETARIO.Moeda
* @access Public
* @param  Object $obTransacao Parametro Transacao
* @return Object Objeto Erro
*/
function IncluirMoeda($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->verificaMoeda();
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->obTMONMoeda->proximoCod( $this->inCodMoeda, $boTransacao );
            if ( !$obErro->ocorreu() ) {

            $this->obTMONMoeda->setDado( "cod_moeda", $this->getCodMoeda() );
            $this->obTMONMoeda->setDado( "descricao_singular", $this->getDescSingular() );
            $this->obTMONMoeda->setDado( "descricao_plural", $this->getDescPlural() );
            $this->obTMONMoeda->setDado( "fracao_singular", $this->getFracaoSingular() );
            $this->obTMONMoeda->setDado( "fracao_plural", $this->getFracaoPlural() );
            $this->obTMONMoeda->setDado( "simbolo", $this->getSimbolo() );
            $this->obTMONMoeda->setDado( "inicio_vigencia", $this->getDtVigencia() );

            $obErro = $this->obTMONMoeda->inclusao( $boTransacao );

            //Comentado Regra de Conversão, pois a mesma não vai mais existir na ação, conforme ticket #22008
            /*
            if ( !$obErro->ocorreu() ) {
            $this->obTMONRegraConversaoMoeda->setDado( "cod_moeda", $this->getCodMoeda() );
            $this->obTMONRegraConversaoMoeda->setDado( "cod_modulo", $this->getCodModulo() );
            $this->obTMONRegraConversaoMoeda->setDado( "cod_biblioteca", $this->getCodBiblioteca() );
            $this->obTMONRegraConversaoMoeda->setDado( "cod_funcao", $this->getCodFuncao() );
            $obErro = $this->obTMONRegraConversaoMoeda->inclusao( $boTransacao );
            }
            */
        }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTMONMoeda );

    return $obErro;
}

/**
* Exclui os dados setados na tabela MONETARIO.MOEDA
* @access Public
* @param  Object $obTransacao Parametro Transacao
* @return Object Objeto Erro
*/
function ExcluirMoeda($boTransacao = "")
{
    include_once ( CAM_GT_MON_NEGOCIO."RMONCredito.class.php");

    $obRMONCredito = new RMONCredito;
    $obRMONCredito->setCodMoeda( $this->getCodMoeda() );
    $obRMONCredito->buscaMoedaCredito( $rsMoeda );

    if ( !$rsMoeda->Eof() ) {
        $obErro = new Erro;
        $obErro->setDescricao("Moeda '".$this->getDescSingular()."' está sendo utilizada pelo Crédito '".$rsMoeda->getCampo("descricao_credito")."'!");

        return $obErro;
    }

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {

        $this->obTMONRegraConversaoMoeda = new TMONRegraConversaoMoeda;

        $this->obTMONRegraConversaoMoeda->setDado ("cod_moeda", $this->getCodMoeda());
        $obErro = $this->obTMONRegraConversaoMoeda->exclusao( $boTransacao );

        if ( !$obErro->ocorreu() ) {

            $this->obTMONMoeda->setDado( "cod_moeda", $this->getCodMoeda() );
            $this->obTMONMoeda->setDado( "descricao_singular", $this->getDescSingular() );
            $this->obTMONMoeda->setDado( "descricao_plural", $this->getDescPlural() );
            $this->obTMONMoeda->setDado( "fracao_singular", $this->getFracaoSingular() );
            $this->obTMONMoeda->setDado( "fracao_plural", $this->getFracaoPlural() );
            $this->obTMONMoeda->setDado( "simbolo", $this->getSimbolo() );
            $this->obTMONMoeda->setDado( "inicio_vigencia", $this->getDtVigencia() );

            $obErro = $this->obTMONMoeda->exclusao( $boTransacao );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTMONMoeda );

    return $obErro;
}

/**
* Altera os dados setados na tabela MOEDA
* @access Public
* @param  Object $obTransacao Parametro Transacao
* @return Object Objeto Erro
*/
function AlterarMoeda($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {

        $obErro = $this->verificaMoeda();
        if ( !$obErro->ocorreu() ) {
            $this->obTMONMoeda->setDado( "cod_moeda", $this->getCodMoeda() );
            $this->obTMONMoeda->setDado( "descricao_singular", $this->getDescSingular() );
            $this->obTMONMoeda->setDado( "descricao_plural", $this->getDescPlural() );
            $this->obTMONMoeda->setDado( "fracao_singular", $this->getFracaoSingular() );
            $this->obTMONMoeda->setDado( "fracao_plural", $this->getFracaoPlural() );
            $this->obTMONMoeda->setDado( "simbolo", $this->getSimbolo() );
            $this->obTMONMoeda->setDado( "inicio_vigencia", $this->getDtVigencia() );

            $obErro = $this->obTMONMoeda->alteracao( $boTransacao );
            
            //Comentado Regra de Conversão, pois a mesma não vai mais existir na ação, conforme ticket #22008
            /*
            if (!$obErro->Ocorreu()) {
                if ( $this->getStrFormulaAntiga() ) {
                    $x = explode ('.', $this->getStrFormulaAntiga() );
                    $this->obTMONRegraConversaoMoeda->setDado("cod_moeda",$this->getCodMoeda());
                    $this->obTMONRegraConversaoMoeda->setDado ("cod_modulo", $x[0] );
                    $this->obTMONRegraConversaoMoeda->setDado ("cod_biblioteca", $x[1] );
                    $this->obTMONRegraConversaoMoeda->setDado ("cod_funcao", $x[2] );
                    $obErro = $this->obTMONRegraConversaoMoeda->alteracao ( $boTransacao );
                }
            }
            */
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTMONMoeda );

    return $obErro;
}

/**
* Lista as MOEDAS conforme o filtro setado
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parametro Transacao
* @return Object Objeto Erro
*/
function ListarMoeda(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->getCodMoeda() ) {
        $stFiltro .= " cod_moeda = '".$this->getCodMoeda()."' AND ";
    }
    if ( $this->getDescSingular() ) {
        $stFiltro .= " descricao_singular like '%".$this->getDescSingular()."%' AND ";
    }
    if ( $this->getDescPlural() ) {
        $stFiltro .= " descricao_plural like '%".$this->getDescPlural()."%' AND ";
    }
    if ( $this->getFracaoSingular() ) {
        $stFiltro .= " fracao_singular like '%".$this->getFracaoSingular()."%' AND ";
    }
    if ( $this->getFracaoPlural() ) {
        $stFiltro .= " fracao_plural like '%".$this->getFracaoPlural()."%' AND ";
    }
    if ( $this->getSimbolo() ) {
        $stFiltro .= " simbolo like '%".$this->getSimbolo()."%' AND ";
    }

    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }

    $stOrder = " ORDER BY cod_moeda ";
    $obErro = $this->obTMONMoeda->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
* Recupera do BD os dados da MOEDA selecionada
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parametro Transacao
* @return Object Objeto Erro
*/
function ConsultarMoeda($boTransacao = "")
{
    $this->obTransacao = new Transacao;
    $stFiltro = "";

    if ($this->inCodMoeda) {
        $stFiltro .= " cod_moeda = ". $this->inCodMoeda ." AND ";
    }
    if ($this->stDescSingular) {
        $stFiltro .= " descricao_singular = ".$this->stDescricaoSingular." AND ";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }
    $stOrder = " ORDER BY cod_moeda ";
    $obErro = $this->obTMONMoeda->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    if ( !$obErro->ocorreu() and !$rsRecordSet->eof() ) {
        $this->inCodMoeda  = $rsRecordSet->getCampo( "cod_moeda" );
        $this->stDescPlural = $rsRecordSet->getCampo( "descricao_plural");
    }

    return $obErro;
}//------------------------------------------------------ FIM DA CONSULTA

/**
    * Verifica se a MOEDA a ser incluida ja existe
    * @access Public
    * @param  Object $rsMoeda Objeto RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parametro Transacao
    * @return Object Objeto Erro
*/
function verificaMoeda($boTransacao = "")
{
    $stFiltro  = " WHERE upper(descricao_singular) = '". strtoupper ( $this->getDescSingular() ) ."' \n";
    if ($this->getCodMoeda())
        $stFiltro .= " AND cod_moeda <> ". $this->getCodMoeda() ." ";

    $obErro = $this->obTMONMoeda->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    if ( $rsRecordSet->getNumLinhas() > 0 ) {
        $obErro->setDescricao("Moeda já cadastrado no Sistema! [". $this->getDescSingular() . "]");
    }

    return $obErro;
}

function BuscaRegraDaMoeda(&$rsRecordSet , $inCodMoeda, $boTransacao='')
{
    $obErro = $this->obTMONRegraConversaoMoeda->RecuperaRelacionamentoDadosDaMoeda( $rsRecordSet, $stFiltro, $stOrder, $boTransacao, $inCodMoeda );

    if ( !$obErro->ocorreu () && $rsRecordSet->getNumLinhas()>0 ) {

        $this->setCodMoeda        ($rsRecordSet->getCampo("cod_moeda"));
        $this->setCodModulo       ($rsRecordSet->getCampo("cod_modulo"));
        $this->setCodBiblioteca   ($rsRecordSet->getCampo("cod_biblioteca"));
        $this->setCodFuncao       ($rsRecordSet->getCampo("cod_funcao"));

        $this->DevolveDescFormula ( $rsRecordSet , $boTransacao='', $rsRecordSet->getCampo("cod_modulo"), $rsRecordSet->getCampo("cod_biblioteca"), $rsRecordSet->getCampo("cod_funcao") );

    }

   return $obErro;
}

/**
    * Busca a descricao da formula. chamada na funcao DevolveFormula
    * @access Public
    * @param  Object $obTransacao Parametro Transacao
    * @return Object Objeto Erro
*/
function DevolveDescFormula(&$rsRecordSet, $boTransacao='')
{
    $codMod  = $rsRecordSet->getCampo("cod_modulo");
    $codBib  = $rsRecordSet->getCampo("cod_biblioteca");
    $codFunc = $rsRecordSet->getCampo("cod_funcao");

    $obErro = $this->obTMONRegraConversaoMoeda->RecuperaRelacionamentoDescricaoFormula( $rsRecordSet, $stFiltro, $stOrder, $boTransacao, $codMod, $codBib, $codFunc );
    if ( !$obErro->ocorreu () ) {
        $this->setStrFormula ( $rsRecordSet->getCampo("nom_funcao") );
    }

    return $obErro;
}

}
