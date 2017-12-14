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
    * Classe de Regra de Negócio Configuracao Divida Ativa
    * Data de Criação   : 04/05/2006

    * @author Analista : Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato

    * $Id: RDATConfiguracao.class.php 59612 2014-09-02 12:00:51Z gelson $

    Caso de uso: uc-05.04.01
*/

/*
$Log$
Revision 1.3  2006/09/15 14:36:06  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_ADM_NEGOCIO . "RConfiguracaoConfiguracao.class.php" );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoAcao.class.php" );
include_once ( CLA_TRANSACAO );

/**
    * Classe de Regra de Negócio Configuracao Arrecadação
    * Data de Criação   : 04/05/2006
    * @author Analista : Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
*/
class RDATConfiguracao extends RConfiguracaoConfiguracao
{
/**
    * @var Object
    * @access Private
*/
var $obTConfiguracao;

/**
    * @var Integer
    * @access Private
*/
var $inAnoExercicio;

/**
    * @var Integer
    * @access Private
*/
var $stLivroFolha;

/**
    * @var String
    * @access Private
*/
var $stUtilizarValorReferencia;

/**
    * @var String
    * @access Private
*/
var $stTipoValorReferencia;

/**
    * @var Integer
    * @access Private
*/
var $inMoedaValorReferencia;

/**
    * @var Integer
    * @access Private
*/
var $inIndicadorValorReferencia;

/**
    * @var Integer
    * @access Private
*/
var $inValorReferencia;

/**
    * @var String
    * @access Private
*/
var $stLimiteValorReferencia;

/**
    * @var String
    * @access Private
*/
var $stUtilizarCreditoDivida;

/**
    * @var Integer
    * @access Private
*/
var $inCreditoDivida;

/**
    * @var String
    * @access Private
*/
var $stNumeracaoInscricao;

var $inLancamentoAtivo;
var $inInscricaoAutomatica;
var $inValidacao;
var $inLimites;
var $inCodModalidade;
var $inCodTipoDocumentoRemissao;

var $stSecretaria;
var $stCoordenador;
var $stChefeDepartamento;
var $stSetorArrecadacao;
var $stMetodologiaCalculo;
var $stNroLeiInscricaoDA;
var $stMensagemDoc;
var $boUtilizarResp2;
var $boUtilizarMsg;
var $boUtilizarLeiDA;
var $boUtilizarIncidValDA;
var $boUtilizarMetCalc;

// funcoes para setar valores
function setDocumentoUtilizarMetCalc($valor) { $this->boUtilizarMetCalc = $valor; }
function setDocumentoUtilizarIncidValDA($valor) { $this->boUtilizarIncidValDA = $valor; }
function setDocumentoUtilizarLeiDA($valor) { $this->boUtilizarLeiDA = $valor; }
function setDocumentoUtilizarMsg($valor) { $this->boUtilizarMsg = $valor; }
function setDocumentoUtilizarResp2($valor) { $this->boUtilizarResp2 = $valor; }
function setDocumentoNroLeiInscricaoDA($valor) { $this->stNroLeiInscricaoDA = $valor; }
function setDocumentoMetodologiaCalculo($valor) { $this->stMetodologiaCalculo = $valor; }
function setDocumentoSetorArrecadacao($valor) { $this->stSetorArrecadacao = $valor; }
function setDocumentoSecretaria($valor) { $this->stSecretaria = $valor; }
function setDocumentoCoordenador($valor) { $this->stCoordenador = $valor; }
function setDocumentoChefeDepartamento($valor) { $this->stChefeDepartamento = $valor; }
function setDocumentoMensagem($valor) { $this->stMensagemDoc = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setAnoExercicio($valor) { $this->inAnoExercicio   = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setLivroFolha($valor) { $this->stLivroFolha   = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setUtilizarValorReferencia($valor) { $this->stUtilizarValorReferencia   = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setTipoValorReferencia($valor) { $this->stTipoValorReferencia   = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setMoedaValorReferencia($valor) { $this->inMoedaValorReferencia   = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setIndicadorValorReferencia($valor) { $this->inIndicadorValorReferencia   = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setValorReferencia($valor) { $this->inValorReferencia   = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setLimiteValorReferencia($valor) { $this->stLimiteValorReferencia   = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setUtilizarCreditoDivida($valor) { $this->stUtilizarCreditoDivida   = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setCreditoDivida($valor) { $this->inCreditoDivida   = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setNumeracaoInscricao($valor) { $this->stNumeracaoInscricao = $valor; }
function setLancamentoAtivo($valor) { $this->inLancamentoAtivo = $valor; }
function setInscricaoAutomatica($valor) { $this->inInscricaoAutomatica = $valor; }
function setValidacao($valor) { $this->inValidacao = $valor; }
function setLimites($valor) { $this->inLimites = $valor; }
function setCodModalidade($valor) { $this->inCodModalidade = $valor; }

function getLancamentoAtivo() { return $this->inLancamentoAtivo; }
function getInscricaoAutomatica() { return $this->inInscricaoAutomatica; }
function getValidacao() { return $this->inValidacao; }
function getLimites() { return $this->inLimites; }
function getCodModalidade() { return $this->inCodModalidade; }
function getDocumentoSecretaria() { return $this->stSecretaria; }
function getDocumentoCoordenador() { return $this->stCoordenador; }
function getDocumentoChefeDepartamento() { return $this->stChefeDepartamento; }
function getDocumentoMetodologiaCalculo() { return $this->stMetodologiaCalculo; }
function getDocumentoSetorArrecadacao() { return $this->stSetorArrecadacao; }
function getDocumentoNroLeiInscricaoDA() { return $this->stNroLeiInscricaoDA; }
function getDocumentoMensagem() { return $this->stMensagemDoc; }
function getDocumentoUtilizarMetCalc() { return $this->boUtilizarMetCalc; }
function getDocumentoUtilizarIncidValDA() { return $this->boUtilizarIncidValDA; }
function getDocumentoUtilizarLeiDA() { return $this->boUtilizarLeiDA; }
function getDocumentoUtilizarMsg() { return $this->boUtilizarMsg; }
function getDocumentoUtilizarResp2() { return $this->boUtilizarResp2; }

// funcoes para retornar valores
/**
    * @access Public
    * @return Integer
*/
function getAnoExercicio() { return $this->inAnoExercicio; }

function getLivroFolha() { return $this->stLivroFolha; }

/**
    * @access Public
    * @return String
*/
function getUtilizarValorReferencia() { return $this->stUtilizarValorReferencia; }

/**
    * @access Public
    * @return String
*/
function getTipoValorReferencia() { return $this->stTipoValorReferencia; }

/**
    * @access Public
    * @return Integer
*/
function getMoedaValorReferencia() { return $this->inMoedaValorReferencia; }

/**
    * @access Public
    * @return Integer
*/
function getIndicadorValorReferencia() { return $this->inIndicadorValorReferencia; }

/**
    * @access Public
    * @return Integer
*/
function getValorReferencia() { return $this->inValorReferencia; }

/**
    * @access Public
    * @return String
*/
function getLimiteValorReferencia() { return $this->stLimiteValorReferencia; }

/**
    * @access Public
    * @return String
*/
function getUtilizarCreditoDivida() { return $this->stUtilizarCreditoDivida; }

/**
    * @access Public
    * @return Integer
*/
function getCreditoDivida() { return $this->inCreditoDivida; }

/**
    * @access Public
    * @return String
*/
function getNumeracaoInscricao() { return $this->stNumeracaoInscricao; }

/**
    * @access Public
    * @return Integer
*/
function getCodTipoDocumentoRemissao() { return $this->inCodTipoDocumentoRemissao; }

/**
    * Método Construtor
    * @access Private
*/
function RDATConfiguracao()
{
    parent::RConfiguracaoConfiguracao();

    $this->obTConfiguracao  = new TAdministracaoConfiguracao;
    $this->obTransacao      = new Transacao;
    $this->setCodModulo     ( 33 );
    $this->inCodTipoDocumentoRemissao=7;
}

function salvarLivro($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( $obErro->ocorreu() ) {
        return $obErro;
    }

    // numero inicial livro
    $this->setParametro ("livro_folha");
    $this->setValor     ( $this->getLivroFolha() );
    $this->verificaParametro( $boExiste, $boTransacao );
    if ($boExiste) {
        $obErro = parent::alterar( $boTransacao );
    } else {
        $obErro = parent::incluir( $boTransacao );
    }

    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTConfiguracao );

        return $obErro;
    }

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTConfiguracao );

    return $obErro;
}

function salvarInscricao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( $obErro->ocorreu() ) {
        return $obErro;
    }

    // utilizar valor referencia
    $this->setParametro ("utilizar_valor_referencia");
    $this->setValor     ( $this->getUtilizarValorReferencia() );
    $this->verificaParametro( $boExiste, $boTransacao );
    if ($boExiste) {
        $obErro = parent::alterar( $boTransacao );
    } else {
        $obErro = parent::incluir( $boTransacao );
    }

    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTConfiguracao );

        return $obErro;
    }

    // tipo valor referencia
    $this->setParametro ("tipo_valor_referencia");
    $this->setValor     ( $this->getTipoValorReferencia() );
    $this->verificaParametro( $boExiste, $boTransacao );
    if ($boExiste) {
        $obErro = parent::alterar( $boTransacao );
    } else {
        $obErro = parent::incluir( $boTransacao );
    }

    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTConfiguracao );

        return $obErro;
    }

    // moeda valor referencia
    $this->setParametro ("moeda_valor_referencia");
    $this->setValor     ( $this->getMoedaValorReferencia() );
    $this->verificaParametro( $boExiste, $boTransacao );
    if ($boExiste) {
        $obErro = parent::alterar( $boTransacao );
    } else {
        $obErro = parent::incluir( $boTransacao );
    }

    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTConfiguracao );

        return $obErro;
    }

    // indicador valor referencia
    $this->setParametro ("indicador_valor_referencia");
    $this->setValor     ( $this->getIndicadorValorReferencia() );
    $this->verificaParametro( $boExiste, $boTransacao );
    if ($boExiste) {
        $obErro = parent::alterar( $boTransacao );
    } else {
        $obErro = parent::incluir( $boTransacao );
    }

    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTConfiguracao );

        return $obErro;
    }

    // valor referencia
    $this->setParametro ("valor_referencia");
    $this->setValor     ( $this->getValorReferencia() );
    $this->verificaParametro( $boExiste, $boTransacao );
    if ($boExiste) {
        $obErro = parent::alterar( $boTransacao );
    } else {
        $obErro = parent::incluir( $boTransacao );
    }

    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTConfiguracao );

        return $obErro;
    }

    // limite valor referencia
    $this->setParametro ("limite_valor_referencia");
    $this->setValor     ( $this->getLimiteValorReferencia() );
    $this->verificaParametro( $boExiste, $boTransacao );
    if ($boExiste) {
        $obErro = parent::alterar( $boTransacao );
    } else {
        $obErro = parent::incluir( $boTransacao );
    }

    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTConfiguracao );

        return $obErro;
    }

    // utilizar credito divida
    $this->setParametro ("utilizar_credito_divida");
    $this->setValor     ( $this->getUtilizarCreditoDivida() );
    $this->verificaParametro( $boExiste, $boTransacao );
    if ($boExiste) {
        $obErro = parent::alterar( $boTransacao );
    } else {
        $obErro = parent::incluir( $boTransacao );
    }

    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTConfiguracao );

        return $obErro;
    }

    // credito divida
    $this->setParametro ("credito_divida");
    $this->setValor     ( $this->getCreditoDivida() );
    $this->verificaParametro( $boExiste, $boTransacao );
    if ($boExiste) {
        $obErro = parent::alterar( $boTransacao );
    } else {
        $obErro = parent::incluir( $boTransacao );
    }

    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTConfiguracao );

        return $obErro;
    }

    // numeracao inscricao
    $this->setParametro ("numeracao_inscricao");
    $this->setValor     ( $this->getNumeracaoInscricao() );
    $this->verificaParametro( $boExiste, $boTransacao );
    if ($boExiste) {
        $obErro = parent::alterar( $boTransacao );
    } else {
        $obErro = parent::incluir( $boTransacao );
    }

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTConfiguracao );

    return $obErro;
}

function salvarDocumento($inDocumento, $boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( $obErro->ocorreu() ) {
        return $obErro;
    }

    $this->setParametro ( "utilmsg_doc_".$inDocumento );
    $this->setValor     ( $this->getDocumentoUtilizarMsg() );
    $this->verificaParametro( $boExiste, $boTransacao );
    if ($boExiste) {
        $obErro = parent::alterar( $boTransacao );
    } else {
        $obErro = parent::incluir( $boTransacao );
    }

    $this->setParametro ( "utilresp2_doc_".$inDocumento );
    $this->setValor     ( $this->getDocumentoUtilizarResp2() );
    $this->verificaParametro( $boExiste, $boTransacao );
    if ($boExiste) {
        $obErro = parent::alterar( $boTransacao );
    } else {
        $obErro = parent::incluir( $boTransacao );
    }

    $this->setParametro ( "utilleida_doc_".$inDocumento );
    $this->setValor     ( $this->getDocumentoUtilizarLeiDA() );
    $this->verificaParametro( $boExiste, $boTransacao );
    if ($boExiste) {
        $obErro = parent::alterar( $boTransacao );
    } else {
        $obErro = parent::incluir( $boTransacao );
    }

    $this->setParametro ( "utilincval_doc_".$inDocumento );
    $this->setValor     ( $this->getDocumentoUtilizarIncidValDA() );
    $this->verificaParametro( $boExiste, $boTransacao );
    if ($boExiste) {
        $obErro = parent::alterar( $boTransacao );
    } else {
        $obErro = parent::incluir( $boTransacao );
    }

    $this->setParametro ( "utilmetcalc_doc_".$inDocumento );
    $this->setValor     ( $this->getDocumentoUtilizarMetCalc() );
    $this->verificaParametro( $boExiste, $boTransacao );
    if ($boExiste) {
        $obErro = parent::alterar( $boTransacao );
    } else {
        $obErro = parent::incluir( $boTransacao );
    }

    $this->setParametro ( "msg_doc_".$inDocumento );
    $this->setValor     ( $this->getDocumentoMensagem() );
    $this->verificaParametro( $boExiste, $boTransacao );
    if ($boExiste) {
        $obErro = parent::alterar( $boTransacao );
    } else {
        $obErro = parent::incluir( $boTransacao );
    }

    $this->setParametro ( "secretaria_".$inDocumento );
    $this->setValor     ( $this->getDocumentoSecretaria() );
    $this->verificaParametro( $boExiste, $boTransacao );
    if ($boExiste) {
        $obErro = parent::alterar( $boTransacao );
    } else {
        $obErro = parent::incluir( $boTransacao );
    }

    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTConfiguracao );

        return $obErro;
    }

    $this->setParametro ( "coordenador_".$inDocumento );
    $this->setValor     ( $this->getDocumentoCoordenador() );
    $this->verificaParametro( $boExiste, $boTransacao );
    if ($boExiste) {
        $obErro = parent::alterar( $boTransacao );
    } else {
        $obErro = parent::incluir( $boTransacao );
    }

    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTConfiguracao );

        return $obErro;
    }

    $this->setParametro ( "chefe_departamento_".$inDocumento );
    $this->setValor     ( $this->getDocumentoChefeDepartamento() );
    $this->verificaParametro( $boExiste, $boTransacao );
    if ($boExiste) {
        $obErro = parent::alterar( $boTransacao );
    } else {
        $obErro = parent::incluir( $boTransacao );
    }

    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTConfiguracao );

        return $obErro;
    }

    $this->setParametro ( "metodologia_calculo_".$inDocumento );
    $this->setValor     ( $this->getDocumentoMetodologiaCalculo() );
    $this->verificaParametro( $boExiste, $boTransacao );
    if ($boExiste) {
        $obErro = parent::alterar( $boTransacao );
    } else {
        $obErro = parent::incluir( $boTransacao );
    }

    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTConfiguracao );

        return $obErro;
    }

    $this->setParametro ( "setor_arrecadacao_".$inDocumento );
    $this->setValor     ( $this->getDocumentoSetorArrecadacao() );
    $this->verificaParametro( $boExiste, $boTransacao );
    if ($boExiste) {
        $obErro = parent::alterar( $boTransacao );
    } else {
        $obErro = parent::incluir( $boTransacao );
    }

    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTConfiguracao );

        return $obErro;
    }

    $this->setParametro ( "nro_lei_inscricao_da_".$inDocumento );
    $this->setValor     ( $this->getDocumentoNroLeiInscricaoDA() );
    $this->verificaParametro( $boExiste, $boTransacao );
    if ($boExiste) {
        $obErro = parent::alterar( $boTransacao );
    } else {
        $obErro = parent::incluir( $boTransacao );
    }

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTConfiguracao );

    return $obErro;
}

function consultarDocumento($inDocumento, $boTransacao = "")
{
    $this->setParametro ( "utilmsg_doc_".$inDocumento );
    $obErro = parent::consultar ( $boTransacao );
    if ( !$obErro->ocorreu() ) {
         $this->setDocumentoUtilizarMsg( $this->getValor() );
    }

    if ($obErro->ocorreu())
        return $obErro;

    $this->setParametro ( "utilresp2_doc_".$inDocumento );
    $obErro = parent::consultar ( $boTransacao );
    if ( !$obErro->ocorreu() ) {
         $this->setDocumentoUtilizarResp2( $this->getValor() );
    }

    if ($obErro->ocorreu())
        return $obErro;

    $this->setParametro ( "utilleida_doc_".$inDocumento );
    $obErro = parent::consultar ( $boTransacao );
    if ( !$obErro->ocorreu() ) {
         $this->setDocumentoUtilizarLeiDA( $this->getValor() );
    }

    if ($obErro->ocorreu())
        return $obErro;

    $this->setParametro ( "utilincval_doc_".$inDocumento );
    $obErro = parent::consultar ( $boTransacao );
    if ( !$obErro->ocorreu() ) {
         $this->setDocumentoUtilizarIncidValDA( $this->getValor() );
    }

    if ($obErro->ocorreu())
        return $obErro;

    $this->setParametro ( "utilmetcalc_doc_".$inDocumento );
    $obErro = parent::consultar ( $boTransacao );
    if ( !$obErro->ocorreu() ) {
         $this->setDocumentoUtilizarMetCalc( $this->getValor() );
    }

    if ($obErro->ocorreu())
        return $obErro;

    $this->setParametro ( "msg_doc_".$inDocumento );
    $obErro = parent::consultar ( $boTransacao );
    if ( !$obErro->ocorreu() ) {
         $this->setDocumentoMensagem( $this->getValor() );
    }

    if ($obErro->ocorreu())
        return $obErro;

    $this->setParametro ( "nro_lei_inscricao_da_".$inDocumento );
    $obErro = parent::consultar ( $boTransacao );
    if ( !$obErro->ocorreu() ) {
         $this->setDocumentoNroLeiInscricaoDA( $this->getValor() );
    }

    if ($obErro->ocorreu())
        return $obErro;

    $this->setParametro ( "secretaria_".$inDocumento );
    $obErro = parent::consultar ( $boTransacao );
    if ( !$obErro->ocorreu() ) {
         $this->setDocumentoSecretaria( $this->getValor() );
    }

    if ($obErro->ocorreu())
        return $obErro;

    $this->setParametro ( "coordenador_".$inDocumento );
    $obErro = parent::consultar ( $boTransacao );
    if ( !$obErro->ocorreu() ) {
         $this->setDocumentoCoordenador( $this->getValor() );
    }

    if ($obErro->ocorreu())
        return $obErro;

    $this->setParametro ( "chefe_departamento_".$inDocumento );
    $obErro = parent::consultar ( $boTransacao );
    if ( !$obErro->ocorreu() ) {
         $this->setDocumentoChefeDepartamento( $this->getValor() );
    }

    if ($obErro->ocorreu())
        return $obErro;

    $this->setParametro ( "metodologia_calculo_".$inDocumento );
    $obErro = parent::consultar ( $boTransacao );
    if ( !$obErro->ocorreu() ) {
         $this->setDocumentoMetodologiaCalculo( $this->getValor() );
    }

    if ($obErro->ocorreu())
        return $obErro;

    $this->setParametro ( "setor_arrecadacao_".$inDocumento );
    $obErro = parent::consultar ( $boTransacao );
    if ( !$obErro->ocorreu() ) {
         $this->setDocumentoSetorArrecadacao( $this->getValor() );
    }

    return $obErro;
}

function consultar($boTransacao = "")
{
    $this->setParametro ("livro_folha");
    $obErro = parent::consultar ( $boTransacao );
    if ( !$obErro->ocorreu() ) {
         $this->setLivroFolha( $this->getValor() );
    }

    if ($obErro->ocorreu())
        return $obErro;

    $this->setParametro ("utilizar_valor_referencia");
    $obErro = parent::consultar ( $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->setUtilizarValorReferencia( $this->getValor() );
    }

    if ($obErro->ocorreu())
        return $obErro;

    $this->setParametro ("tipo_valor_referencia");
    $obErro = parent::consultar ( $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->setTipoValorReferencia( $this->getValor() );
    }

    if ($obErro->ocorreu())
        return $obErro;

    $this->setParametro ("moeda_valor_referencia");
    $obErro = parent::consultar ( $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->setMoedaValorReferencia( $this->getValor() );
    }

    if ($obErro->ocorreu())
        return $obErro;

    $this->setParametro ("indicador_valor_referencia");
    $obErro = parent::consultar ( $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->setIndicadorValorReferencia( $this->getValor() );
    }

    if ($obErro->ocorreu())
        return $obErro;

    $this->setParametro ("valor_referencia");
    $obErro = parent::consultar ( $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->setValorReferencia( $this->getValor() );
    }

    if ($obErro->ocorreu())
        return $obErro;

    $this->setParametro ("limite_valor_referencia");
    $obErro = parent::consultar ( $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->setLimiteValorReferencia( $this->getValor() );
    }

    if ($obErro->ocorreu())
        return $obErro;

    $this->setParametro ("utilizar_credito_divida");
    $obErro = parent::consultar ( $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->setUtilizarCreditoDivida( $this->getValor() );
    }

    if ($obErro->ocorreu())
        return $obErro;

    $this->setParametro ("credito_divida");
    $obErro = parent::consultar ( $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->setCreditoDivida( $this->getValor() );
    }

    if ($obErro->ocorreu())
        return $obErro;

    $this->setParametro ("numeracao_inscricao");
    $obErro = parent::consultar ( $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->setNumeracaoInscricao( $this->getValor() );
    }

    if ($obErro->ocorreu())
        return $obErro;

    $this->setParametro ("lancamento_ativo");
    $obErro = parent::consultar ( $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->setLancamentoAtivo( $this->getValor() );
    }

    if ($obErro->ocorreu())
        return $obErro;

    $this->setParametro ("inscricao_automatica");
    $obErro = parent::consultar ( $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->setInscricaoAutomatica( $this->getValor() );
    }

    if ($obErro->ocorreu())
        return $obErro;

    $this->setParametro ("validacao");
    $obErro = parent::consultar ( $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->setValidacao( $this->getValor() );
    }

    if ($obErro->ocorreu())
        return $obErro;

    $this->setParametro ("modalidade_inscricao_automatica");
    $obErro = parent::consultar ( $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->setCodModalidade( $this->getValor() );
    }

    if ($obErro->ocorreu())
        return $obErro;

    $this->setParametro ("limites");
    $obErro = parent::consultar ( $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->setLimites( $this->getValor() );
    }

    return $obErro;
}

/**
    * Lista os documentos do tipo Certidão de Remissão
    * @access Public
    * @param RecorSet $rsModeloDocumentoRemissao
    * @param Boolean $boTransacao
    * @return Object obErro
 *
*/
function listarModelosDocumentoRemissao(&$rsModeloDocumentoRemissao,$boTransacao='')
{
    include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoModeloDocumento.class.php" );
    $obTAdministracaoModeloDocumento = new TAdministracaoModeloDocumento;
    $obErro = $obTAdministracaoModeloDocumento->recuperaTodos($rsModeloDocumentoRemissao, ' WHERE cod_tipo_documento='.$this->inCodTipoDocumentoRemissao,'',$boTransacao);

    return $obErro;
}

function salvarRemissao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( $obErro->ocorreu() ) {
        return $obErro;
    }

    $this->setParametro ("lancamento_ativo");
    $this->setValor     ( $this->getLancamentoAtivo() );
    $this->verificaParametro( $boExiste, $boTransacao );
    if ($boExiste) {
        $obErro = parent::alterar( $boTransacao );
    } else {
        $obErro = parent::incluir( $boTransacao );
    }

    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTConfiguracao );

        return $obErro;
    }

    $this->setParametro ("inscricao_automatica");
    $this->setValor     ( $this->getInscricaoAutomatica() );
    $this->verificaParametro( $boExiste, $boTransacao );
    if ($boExiste) {
        $obErro = parent::alterar( $boTransacao );
    } else {
        $obErro = parent::incluir( $boTransacao );
    }

    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTConfiguracao );

        return $obErro;
    }

    $this->setParametro ("validacao");
    $this->setValor     ( $this->getValidacao() );
    $this->verificaParametro( $boExiste, $boTransacao );
    if ($boExiste) {
        $obErro = parent::alterar( $boTransacao );
    } else {
        $obErro = parent::incluir( $boTransacao );
    }

    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTConfiguracao );

        return $obErro;
    }

    $this->setParametro ("modalidade_inscricao_automatica");
    $this->setValor     ( $this->getCodModalidade() );
    $this->verificaParametro( $boExiste, $boTransacao );
    if ($boExiste) {
        $obErro = parent::alterar( $boTransacao );
    } else {
        $obErro = parent::incluir( $boTransacao );
    }

    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTConfiguracao );

        return $obErro;
    }

    $this->setParametro ("limites");
    $this->setValor     ( $this->getLimites() );
    $this->verificaParametro( $boExiste, $boTransacao );
    if ($boExiste) {
        $obErro = parent::alterar( $boTransacao );
    } else {
        $obErro = parent::incluir( $boTransacao );
    }

    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTConfiguracao );

        return $obErro;
    }

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTConfiguracao );

    return $obErro;
}

} // fecha classe

?>
