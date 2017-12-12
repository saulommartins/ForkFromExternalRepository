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

    * Classe de Regra de Negócio para arrecadacao carne
    * Data de Criação   : 10/10/2005

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @package URBEM
    * @subpackage Regra

    $Id: RARRCarne.class.php 63415 2015-08-25 21:17:03Z arthur $

   * Casos de uso: uc-05.03.11, uc-02.04.04

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GT_ARR_MAPEAMENTO."TARRCarne.class.php"            ;
include_once CAM_GT_ARR_MAPEAMENTO."TARRCarneConsolidacao.class.php";
include_once CAM_GT_ARR_MAPEAMENTO."TARRParcela.class.php"          ;
include_once CAM_GT_ARR_MAPEAMENTO."TARRMotivoDevolucao.class.php"  ;
include_once CAM_GT_ARR_MAPEAMENTO."TARRCarneDevolucao.class.php"   ;
include_once CAM_GT_MON_NEGOCIO."RMONCredito.class.php"             ;
include_once CAM_GT_ARR_NEGOCIO."RARRCarneConsolidacao.class.php"   ;

class RARRCarne
{
/*
    * @var Object
    * @access Private
*/
var $obTransacao;
/**
    * @access Private
    * @var Object
*/
var $obTARRCarne;
/**
    * @access Private
    * @var Object
*/
var $obTARRCarneConsolidacao;
/**
    * @access Private
    * @var Object
*/
var $obTARRMotivo;
/**
    * @access Private
    * @var String
*/
var $stNumeracao;
/**
    * @access Private
    * @var String
*/
var $stNumCobranca;
/**
    * @access Private
    * @var String
*/
var $stExercicioCobranca;
/**
    * @access Private
    * @var String
*/
var $stNumeracaoMigrada;
/**
    * @access Private
    * @var Integer
*/
var $inCodMotivo;
/**
    * @access Private
    * @var String
*/
var $stMotivo;
/**
    * @access Private
    * @var String
*/
var $stExercicio;
/**
    * @access Private
    * @var Object
*/
var $obRARRParcela;
/**
    * @access Private
    * @var Object
*/
var $obRMONConvenio;
/**
    * @access Private
    * @var Object
*/
var $obRMONCarteira;
/**
    * @access Private
    * @var String
*/
var $stCodContribuinteConjunto;
/**
    * @access Private
    * @var Integer
*/
var $inCodContribuinteInicial;
/**
    * @access Private
    * @var Integer
*/
var $inCodContribuinteFinal;

/**
    * @access Private
    * @var Integer
*/
var $inInscricaoDivida;

/**
    * @access Private
    * @var Integer
*/
var $inInscricaoImobiliariaInicial;
/**
    * @access Private
    * @var Integer
*/
var $inInscricaoImobiliariaFinal;
/**
    * @access Private
    * @var Integer
*/
var $stValorCompostoInicial;
/**
    * @access Private
    * @var Integer
*/
var $stValorCompostoFinal;
/**
    * @access Private
    * @var Integer
*/
var $inInscricaoEconomicaInicial;
/**
    * @access Private
    * @var Integer
*/
var $inInscricaoEconomicaFinal;
/**
    * @access Private
    * @var Integer
*/
var $inCodAtividadeInicial;
/**
    * @access Private
    * @var Integer
*/
var $inCodAtividadeFinal;
/**
    * @access Private
    * @var Integer
*/
var $inCodCredito;
var $inCodEspecie;
var $inCodGenero ;
var $inCodNatureza;
/**
    * @access Private
    * @var Integer
*/
var $inCodGrupo;
/**
    * @access Private
    * @var Integer
*/
var $inOcorrenciaPagamento;
var $stTipo;
/**
    * @access Private
    * @var Date
*/
var $dtDataPagamento;

/**
    * @access Public
    * @var Objeto
*/
var $obRMONCredito;
var $obTARRParcela;
var $obTARRCarneDevolucao;
var $obRARRCarneConsolidacao;

// SETTERS
/*
    * @access Public
    * @param Object $valor
*/
function setTARRCarne($valor) { $this->obTARRCarne = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setNumeracao($valor) { $this->stNumeracao = $valor    ; }
/**
    * @access Public
    * @param String $valor
*/
function setNumCobranca($valor) { $this->stNumCobranca = $valor    ; }
/**
    * @access Public
    * @param String $valor
*/
function setExercicioCobranca($valor) { $this->stExercicioCobranca = $valor    ; }
/**
    * @access Public
    * @param String $valor
*/
function setNumeracaoMigrada($valor) { $this->stNumeracaoMigrada  = $valor    ; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodMotivo($valor) { $this->inCodMotivo  = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setMotivo($valor) { $this->stMotivo    = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setOcorrenciaPagamento($valor) { $this->inOcorrenciaPagamento    = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setExercicio($valor) { $this->stExercicio = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodContribuinteConjunto($valor) { $this->stCodContribuinteConjunto = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodContribuinteInicial($valor) { $this->inCodContribuinteInicial = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodContribuinteFinal($valor) { $this->inCodContribuinteFinal = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setInscricaoImobiliariaInicial($valor) { $this->inInscricaoImobiliariaInicial = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setInscricaoDivida($valor) { $this->inInscricaoDivida = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setInscricaoImobiliariaFinal($valor) { $this->inInscricaoImobiliariaFinal = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setValorCompostoInicial($valor) { $this->stValorCompostoInicial = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setValorCompostoFinal($valor) { $this->stValorCompostoFinal = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setInscricaoEconomicaInicial($valor) { $this->inInscricaoEconomicaInicial = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setInscricaoEconomicaFinal($valor) { $this->inInscricaoEconomicaFinal = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setAtividadeInicial($valor) { $this->inCodAtividadeInicial = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setAtividadeFinal($valor) { $this->inCodAtividadeFinal = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCredito($valor) { $this->inCodCredito = $valor;        }
/**
    * @access Public
    * @param Integer $valor
*/
function setGrupo($valor) { $this->inCodGrupo = $valor;          }
/**
    * @access Public
    * @param Date $valor
*/
function setDataPagamento($valor) { $this->dtDataPagamento = $valor;          }

// GETTERES
/*
    * @access Public
    * @return Object
*/
function getTARRCarne() { return $this->obTARRCarne;          }
/**
    * @access Public
    * @return String
*/
function getNumeracao() { return $this->stNumeracao;          }
/**
    * @access Public
    * @return String
*/
function getNumCobranca() { return $this->stNumCobranca;          }
/**
    * @access Public
    * @return String
*/
function getExercicioCobranca() { return $this->stExercicioCobranca;          }
/**
    * @access Public
    * @return String
*/
function getNumeracaoMigrada() { return $this->stNumeracaoMigrada;  }
/**
    * @access Public
    * @return Integer
*/
function getCodMotivo() { return $this->inCodMotivo;          }
/**
    * @access Public
    * @return Integer
*/
function getOcorrenciaPagamento() { return $this->inOcorrenciaPagamento;          }
/**
    * @access Public
    * @return String
*/
function getMotivo() { return $this->stMotivo;             }
/**
    * @access Public
    * @return String
*/
function getExercicio() { return $this->stExercicio;          }

/**
    * @access Public
    * @return Integer
*/
function getInscricaoDivida() { return $this->inInscricaoDivida; }
/**
    * @access Public
    * @param Integer $valor
*/
function getCodContribuinteConjunto() { return $this->stCodContribuinteConjunto; }
/**
    * @access Public
    * @param Integer $valor
*/
function getCodContribuinteInicial() { return $this->inCodContribuinteInicial; }
/**
    * @access Public
    * @param Integer $valor
*/
function getCodContribuinteFinal() { return $this->inCodContribuinteFinal; }
/**
    * @access Public
    * @param Integer $valor
*/
function getInscricaoImobiliariaInicial() { return $this->inInscricaoImobiliariaInicial; }
/**
    * @access Public
    * @param Integer $valor
*/
function getInscricaoImobiliariaFinal() { return $this->inInscricaoImobiliariaFinal; }
/**
    * @access Public
    * @param Integer $valor
*/
function getValorCompostoIniciall() { return $this->stValorCompostoInicial; }
/**
    * @access Public
    * @param Integer $valor
*/
function getValorCompostoFinal() { return $this->stValorCompostoFinal; }
/**
    * @access Public
    * @param Integer $valor
*/
function getInscricaoEconomicaInicial() { return $this->inInscricaoEconomicaInicial; }
/**
    * @access Public
    * @param Integer $valor
*/
function getInscricaoEconomicaFinal() { return $this->inInscricaoEconomicaFinal; }
/**
    * @access Public
    * @param Integer $valor
*/
function getAtividadeInicial() { return $this->inCodAtividadeInicial; }
/**
    * @access Public
    * @param Integer $valor
*/
function getAtividadeFinal() { return $this->inCodAtividadeFinal; }
/**
    * @access Public
    * @param Integer $valor
*/
function getCredito() { return $this->inCodCredito; }
/**
    * @access Public
    * @param Integer $valor
*/
function getGrupo() { return $this->inCodGrupo;   }
/**
    * @access Public
    * @param Date $valor
*/
function getDataPagamento() { return $this->dtDataPagamento;   }

/**
     * Método construtor
     * @access Private
*/
function RARRCarne($obParcela = "vazio")
{
    include_once ( CAM_GT_MON_NEGOCIO."RMONConvenio.class.php" );
    include_once ( CAM_GT_MON_NEGOCIO."RMONCarteira.class.php" );
    include_once ( CAM_GT_ARR_NEGOCIO."RARRParcela.class.php"  );

    if ( is_object ( $obParcela ) ) {
        $this->obRARRParcela = $obParcela;
    } else {
        $this->obRARRParcela = new RARRParcela ( new RARRLancamento ( new RARRCalculo ));
    }

    $this->obRMONConvenio       = new RMONConvenio;
    $this->obRMONCarteira       = new RMONCarteira;
    $this->obTARRCarne          = new TARRCarne;
    $this->obTARRParcela        = new TARRParcela;
    $this->obTARRCarneDevolucao = new TARRCarneDevolucao;
    $this->obTransacao      = new Transacao;
    $this->obRARRCarneConsolidacao = new RARRCarneConsolidacao;

}

/**
    * Incluir Carne
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluirCarne($boTransacao = '')
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTARRCarne = new TARRCarne;
        $this->obTARRCarne->setDado("numeracao"   , $this->stNumeracao );
        $this->obTARRCarne->setDado("cod_convenio", $this->obRMONConvenio->getCodigoConvenio() );
        if( $this->obRMONCarteira->getCodigoCarteira() > 0 )
            $this->obTARRCarne->setDado("cod_carteira", $this->obRMONCarteira->getCodigoCarteira() );
        $this->obTARRCarne->setDado("cod_parcela" , $this->obRARRParcela->getCodParcela() );
        $this->obTARRCarne->setDado("exercicio"   , $this->stExercicio );
        $this->obTARRCarne->setDado ( "impresso"      , false                                         );
        $obErro = $this->obTARRCarne->inclusao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRCarne );

    return $obErro;
}

function efetuaReemitirCarne($arReemissao,  $boTransacao = '')
{
    include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCarneDevolucao.class.php" );

    $obTARRCarne = new TARRCarne;
    $obTARRCarneDevolucao = new TARRCarneDevolucao;

    $boFlagTransacao = true;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        // reemitir parcela
        $obErro = $this->obRARRParcela->reemitirParcela($arReemissao,$boTransacao);

        if ( !$obErro->ocorreu() ) {
            $obTARRCarneDevolucao->recuperaListaCarnesParaCancelarNaParcela( $rsListaCarnesCancelar, $this->obRARRParcela->getCodParcela(), $boTransacao );
            // devolver carne antigo
            while ( !$rsListaCarnesCancelar->eof() ) {
                $obTARRCarneDevolucao->setDado( "numeracao", $rsListaCarnesCancelar->getCampo("numeracao") );
                $obTARRCarneDevolucao->setDado( "cod_convenio", $rsListaCarnesCancelar->getCampo("cod_convenio") );
                $obTARRCarneDevolucao->setDado( "cod_motivo", 10 ); // 10 - Parcela Reemitida
                $obTARRCarneDevolucao->setDado( "dt_devolucao", date("d/m/Y") ); // data devolução é hoje.
                $obErro = $obTARRCarneDevolucao->inclusao($boTransacao);
                $rsListaCarnesCancelar->proximo();
            }

            if ( !$obErro->ocorreu() ) {
                // insere novo carne
                $obTARRCarne->setDado ( "numeracao"     , $this->getNumeracao()                         );
                $obTARRCarne->setDado ( "cod_convenio"  , $this->obRMONConvenio->getCodigoConvenio()    );
                if ( $this->obRMONConvenio->getCodigoConvenio() == '-1' ) {
                    $obTARRCarne->setDado ( "cod_carteira"  , null);
                } else {
                    $obTARRCarne->setDado ( "cod_carteira"  , $this->obRMONCarteira->getCodigoCarteira()    );
                }
                $obTARRCarne->setDado ( "cod_parcela"   , $this->obRARRParcela->getCodParcela()         );
                $obTARRCarne->setDado ( "exercicio"     , $this->getExercicio()                         );
                $obTARRCarne->setDado ( "impresso"      , true                                          );
                $obErro = $obTARRCarne->inclusao($boTransacao);
            }
        }
    }

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTARRCarne );

    return $obErro;
}

function devolverCarne($boTransacao = '')
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    $arCarnesSessao = Sessao::read( "Carnes" );
    for ($inX=0; $inX<count($arCarnesSessao); $inX++) {
        $this->obTARRCarneDevolucao->setDado( "numeracao"    , $arCarnesSessao[$inX]['stNumeracao'] );
        $this->obTARRCarneDevolucao->setDado( "cod_convenio" , $arCarnesSessao[$inX]['cod_convenio'] );
        $this->obTARRCarneDevolucao->setDado( "cod_motivo"   , $arCarnesSessao[$inX]['inCodMotivo'] );
        $this->obTARRCarneDevolucao->setDado( "dt_devolucao" , date("d/m/Y") );
        $obErro = $this->obTARRCarneDevolucao->inclusao( $boTransacao );
        if ($obErro->ocorreu())
            break;
    }

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRCarneDevolucao );

    return $obErro;
}

function alterarCarne($boTransacao = '')
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obTARRCarne = new TARRCarne;
        $obTARRCarne->setDado ( "numeracao"     , $this->getNumeracao()                         );
        $obTARRCarne->setDado ( "cod_convenio"  , $this->obRMONConvenio->getCodigoConvenio()    );
        $obTARRCarne->setDado ( "cod_parcela"   , $this->obRARRParcela->getCodParcela()         );
        $obTARRCarne->setDado ( "exercicio"     , $this->getExercicio()                         );
        $obTARRCarne->setDado ( "impresso"      , TRUE                                         );
        $obErro = $obTARRCarne->alteracao($boTransacao);
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRCarne );

    return $obErro;
}
function listarCarne(&$rsRecordSet, $boTransacao = '')
{
    $stFiltro = '';
    if ($this->stNumeracao) {
        $stFiltro .= " numeracao = '".$this->stNumeracao."' and";
    }
    if ( $this->obRARRParcela->getCodParcela() ) {
        $stFiltro .= " cod_parcela = ".$this->obRARRParcela->getCodParcela()." and";
    }
    if ($stFiltro) {
        $stFiltro = " where ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }
    $obErro = $this->obTARRCarne->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    Lista carnes para devolução
    @access Public
    @param RecordSet $rsRecordSet
    @param Object obTransacao
    @return Object obErro
*/
function listarNomeDevolucao(&$rsRecordSet, $obTransacao)
{
    $stFiltro = '';

    if ($this->stNumeracao) {
        $stFiltro .= " ac.numeracao = '".$this->stNumeracao."' and ";
    }

    if ($this->stExercicio) {
        $stFiltro .= " ac.exercicio = '".$this->stExercicio."' and ";
    }

    if ($stFiltro) {
        $stFiltro = " where ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }

    $obErro = $this->obTARRCarne->recuperaNomeDevolucao( $rsRecordSet, $stFiltro, $obTransacao );

    return $obErro;
}

function listarMotivo(&$rsRecordSet, $boTransacao = '')
{
    $this->obTARRMotivo = new TARRMotivoDevolucao;

    $stFiltro = '';

    if ($this->inCodMotivo) {
        $stFiltro .= " cod_motivo = ".$this->inCodMotivo. " and ";
    }

    if ($this->stMotivo) {
        $stFiltro .= " to_upper(descricao) like to_upper(".$this->stMotivo.") and ";
    }

    if ($stFiltro) {
        $stFiltro = " where ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }

    $stOrdem = " cod_motivo ";

    $obErro = $this->obTARRMotivo->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    * Lista carnes para emissão de acordo com o filtro
    * @access Public
    * @param Object $rsRecordSet retorna RecordSet preenchido
    * @param Object $boTransacao contêm a transação do banco
    * @return Object Objeto Erro
*/
function listarEmitirCarne(&$rsRecordSet, $boTransacao = '')
{
    $stFiltro = '';

    if ( $this->obRARRParcela->getCodParcela() ) {
        $stFiltro .= ' and ap.cod_parcela in ('. $this->obRARRParcela->getCodParcela().')';
    }

    /* FILTRO POR CREDITO */
    if ($this->inCodCredito) {
        $stFiltro .= " and mc.cod_credito = ".$this->inCodCredito;
    }

    /* FILTRO POR GRUPO */
    if ($this->inCodGrupo) {
        $stFiltro .= " and macg.cod_grupo = ".$this->inCodGrupo;
    }

    /* FILTRO POR INSCRICAO IMOBILIARIA */
    if (( $this->inInscricaoImobiliariaInicial ) && ( $this->inInscricaoImobiliariaFinal )) {
        $stFiltro .= " and aii.inscricao_municipal between ".$this->inInscricaoImobiliariaInicial." and ".$this->inInscricaoImobiliariaFinal;
    } elseif (( $this->inInscricaoImobiliariaInicial ) && ( !$this->inInscricaoImobiliariaFinal )) {
        $stFiltro .= " and aii.inscricao_municipal = ".$this->inInscricaoImobiliariaInicial;
    } elseif (( !$this->inInscricaoImobiliariaInicial ) && ( $this->inInscricaoImobiliariaFinal )) {
        $stFiltro .= " and aii.inscricao_municipal = ".$this->inInscricaoImobiliariaFinal;
    }

    /* FILTRO POR INSCRICAO ECONOMICA */
    if (( $this->inInscricaoEconomicaInicial ) && ( $this->inInscricaoImobiliariaFinal )) {
        $stFiltro .= " and aece.inscricao_economica between ".$this->inInscricaoEconomicaInicial." and ".$this->inInscricaoEconomicaFinal;
    } elseif (( $this->inInscricaoEconomicaInicial ) && ( !$this->inInscricaoEconomicaFinal )) {
        $stFiltro .= " and aece.inscricao_economica = ".$this->inInscricaoEconomicaInicial;
    } elseif (( !$this->inInscricaoEconomicaInicial ) && ( $this->inInscricaoEconomicaFinal )) {
        $stFiltro .= " and aece.inscricao_economica = ".$this->inInscricaoEconomicaFinal;
    }

    /* FILTRO POR CONTRIBUINTE */
    if (( $this->inCodContribuinteInicial ) && ( $this->inCodContribuinteFinal )) {
        $stFiltro .= " and cgm.numcgm between ".$this->inCodContribuinteInicial." and ".$this->inCodContribuinteFinal;
    } elseif (( $this->inCodContribuinteInicial ) && ( !$this->inCodContribuinteFinal )) {
        $stFiltro .= " and cgm.numcgm = ".$this->inCodContribuinteInicial;
    } elseif (( !$this->inCodContribuinteInicial ) && ( $this->inCodContribuinteFinal )) {
        $stFiltro .= " and cgm.numcgm = ".$this->inCodContribuinteFinal;
    }

    /* FILTRO POR LOCALIZACAO */
    if (( $this->stValorCompostoInicial ) && ( $this->stValorCompostoFinal )) {
        $stFiltro .= " and aii.codigo_composto between '".$this->stValorCompostoInicial."' and '".$this->stValorCompostoFinal."'";
    } elseif (( $this->stValorCompostoInicial ) && ( !$this->stValorCompostoFinal )) {
        $stFiltro .= " and aii.codigo_composto like '".$this->stValorCompostoInicial."'%";
    } elseif (( !$this->stValorCompostoInicial ) && ( $this->stValorCompostoFinal )) {
        $stFiltro .= " and aii.codigo_composto like '".$this->stValorCompostoFinal."'%";
    }

    /* FILTRO POR ATIVIDADE */
    if (( $this->inCodAtividadeInicial ) && ( $this->inCodAtividadeFinal )) {
        $stFiltro .= " and aece.cod_estrutural between '".$this->inCodAtividadeInicial."' and '".$this->inCodAtividadeFinal."'";
    } elseif (( $this->inCodAtividadeInicial ) && ( !$this->inCodAtividadeFinal )) {
        $stFiltro .= " and aece.cod_estrutural = '".$this->inCodAtividadeInicial."'";
    } elseif (( !$this->inCodAtividadeInicial ) && ( $this->inCodAtividadeFinal )) {
        $stFiltro .= " and aece.cod_estrutural = '".$this->inCodAtividadeFinal."'";
    }

    $stFiltro .= " and ac.exercicio = '".$this->stExercicio."' ";
    $stOrdem = ' order by ap.cod_parcela';

    $obErro = $this->obTARRCarne->selecionaCarne( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    * Metodo para listar o código dos cálculos de determinda parcela
    * @access Public
    * @param Object $rsRecordSet retorno do RecordSet preenchido
    * @param Object $boTransacao contêm a transação atual do banco
    * @return Object $obErro objeto de erro
*/
function listarCalculoParcela(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = '';

    if ( $this->RARRParcela->getCodParcela() ) {
        $stFiltro = " ap.cod_parcela = ".$this->RARRParcela->getCodParcela()." AND ";
    }

    if ($stFiltro) {
        $stFiltro = "\r\n\t WHERE ".substr( $stFiltro ,0 ,-4 );
    }

    $obErro = $this->obTARRCarne->recuperaCalculoParcela( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    * Metodo para emissão do carnê
    * @access Public
    * @param Object $rsRecordSet retorno do RecordSet preenchido
    * @param Object $boTransacao contêm a transação atual do banco
    * @return Object $obErro objeto de erro
*/
function reemitirCarne(&$rsRecordSet, $boTransacao = '')
{
    $stFiltro = '';

    /* FILTRO POR CREDITO */
    if ($this->inCodCredito) {
        $stFiltro .= " and mc.cod_credito = ".$this->inCodCredito;
    }

    /* FILTRO POR GRUPO */
    if ($this->inCodGrupo) {
        $stFiltro .= " and acgc.cod_grupo = ".$this->inCodGrupo;
    }

    /* FILTRO POR INSCRICAO IMOBILIARIA */
    if (( $this->inInscricaoImobiliariaInicial ) && ( $this->inInscricaoImobiliariaFinal )) {
        $stFiltro .= " and ii.inscricao_municipal between ".$this->inInscricaoImobiliariaInicial." and ".$this->inInscricaoImobiliariaFinal;
    } elseif (( $this->inInscricaoImobiliariaInicial ) && ( !$this->inInscricaoImobiliariaFinal )) {
        $stFiltro .= " and ii.inscricao_municipal = ".$this->inInscricaoImobiliariaInicial;
    } elseif (( !$this->inInscricaoImobiliariaInicial ) && ( $this->inInscricaoImobiliariaFinal )) {
        $stFiltro .= " and ii.inscricao_municipal = ".$this->inInscricaoImobiliariaFinal;
    }

    /* FILTRO POR INSCRICAO ECONOMICA */
    if (( $this->inInscricaoEconomicaInicial ) && ( $this->inInscricaoImobiliariaFinal )) {
        $stFiltro .= " and aece.inscricao_economica between ".$this->inInscricaoEconomicaInicial." and ".$this->inInscricaoEconomicaFinal;
    } elseif (( $this->inInscricaoEconomicaInicial ) && ( !$this->inInscricaoEconomicaFinal )) {
        $stFiltro .= " and aece.inscricao_economica = ".$this->inInscricaoEconomicaInicial;
    } elseif (( !$this->inInscricaoEconomicaInicial ) && ( $this->inInscricaoEconomicaFinal )) {
        $stFiltro .= " and aece.inscricao_economica = ".$this->inInscricaoEconomicaFinal;
    }

    /* FILTRO POR CONTRIBUINTE */
    if (( $this->inCodContribuinteInicial ) && ( $this->inCodContribuinteFinal )) {
        $stFiltro .= " and cgm.numcgm between ".$this->inCodContribuinteInicial." and ".$this->inCodContribuinteFinal;
    } elseif (( $this->inCodContribuinteInicial ) && ( !$this->inCodContribuinteFinal )) {
        $stFiltro .= " and cgm.numcgm = ".$this->inCodContribuinteInicial;
    } elseif (( !$this->inCodContribuinteInicial ) && ( $this->inCodContribuinteFinal )) {
        $stFiltro .= " and cgm.numcgm = ".$this->inCodContribuinteFinal;
    }

    /* FILTRO POR LOCALIZACAO */
    if (( $this->stValorCompostoInicial ) && ( $this->stValorCompostoFinal )) {
        $stFiltro .= " and aii.codigo_composto between '".$this->stValorCompostoInicial."' and '".$this->stValorCompostoFinal."'";
    } elseif (( $this->stValorCompostoInicial ) && ( !$this->stValorCompostoFinal )) {
        $stFiltro .= " and aii.codigo_composto like '".$this->stValorCompostoInicial."'%";
    } elseif (( !$this->stValorCompostoInicial ) && ( $this->stValorCompostoFinal )) {
        $stFiltro .= " and aii.codigo_composto like '".$this->stValorCompostoFinal."'%";
    }

    /* FILTRO POR ATIVIDADE */
    if (( $this->inCodAtividadeInicial ) && ( $this->inCodAtividadeFinal )) {
        $stFiltro .= " and aece.cod_estrutural between '".$this->inCodAtividadeInicial."' and '".$this->inCodAtividadeFinal."'";
    } elseif (( $this->inCodAtividadeInicial ) && ( !$this->inCodAtividadeFinal )) {
        $stFiltro .= " and aece.cod_estrutural = '".$this->inCodAtividadeInicial."'";
    } elseif (( !$this->inCodAtividadeInicial ) && ( $this->inCodAtividadeFinal )) {
        $stFiltro .= " and aece.cod_estrutural = '".$this->inCodAtividadeFinal."'";
    }

    $stFiltro .= " and ac.exercicio = '".$this->stExercicio."' \n";

    if ( $this->obRARRParcela->roRARRLancamento->roRARRCalculo->getCodCalculo() ) {
        $stFiltro .= " and ac.cod_calculo in (".$this->obRARRParcela->roRARRLancamento->roRARRCalculo->getCodCalculo().") \n ";
    }

    if ( $this->obRARRParcela->roRARRLancamento->getCodLancamento() ) {
        $stFiltro .= " and al.cod_lancamento = ".$this->obRARRParcela->roRARRLancamento->getCodLancamento()." \n ";
    }

    $obErro = $this->obTARRCarne->geraReemitirCarne( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    * Metodo para emissão do carnê
    * @access Public
    * @param Object $rsRecordSet retorno do RecordSet preenchido
    * @param Object $boTransacao contêm a transação atual do banco
    * @return Object $obErro objeto de erro
*/
function reemitirCarneManaquiri(&$rsRecordSet, $boTransacao = '')
{
    $stFiltro = '';

    /* FILTRO POR CREDITO */
    if ($this->inCodCredito) {
        $stFiltro .= " and mc.cod_credito = ".$this->inCodCredito;
    }

    /* FILTRO POR GRUPO */
    if ($this->inCodGrupo) {
        $stFiltro .= " and acgc.cod_grupo = ".$this->inCodGrupo;
    }

    /* FILTRO POR INSCRICAO IMOBILIARIA */
    if (( $this->inInscricaoImobiliariaInicial ) && ( $this->inInscricaoImobiliariaFinal )) {
        $stFiltro .= " and ii.inscricao_municipal between ".$this->inInscricaoImobiliariaInicial." and ".$this->inInscricaoImobiliariaFinal;
    } elseif (( $this->inInscricaoImobiliariaInicial ) && ( !$this->inInscricaoImobiliariaFinal )) {
        $stFiltro .= " and ii.inscricao_municipal = ".$this->inInscricaoImobiliariaInicial;
    } elseif (( !$this->inInscricaoImobiliariaInicial ) && ( $this->inInscricaoImobiliariaFinal )) {
        $stFiltro .= " and ii.inscricao_municipal = ".$this->inInscricaoImobiliariaFinal;
    }

    /* FILTRO POR INSCRICAO ECONOMICA */
    if (( $this->inInscricaoEconomicaInicial ) && ( $this->inInscricaoImobiliariaFinal )) {
        $stFiltro .= " and aece.inscricao_economica between ".$this->inInscricaoEconomicaInicial." and ".$this->inInscricaoEconomicaFinal;
    } elseif (( $this->inInscricaoEconomicaInicial ) && ( !$this->inInscricaoEconomicaFinal )) {
        $stFiltro .= " and aece.inscricao_economica = ".$this->inInscricaoEconomicaInicial;
    } elseif (( !$this->inInscricaoEconomicaInicial ) && ( $this->inInscricaoEconomicaFinal )) {
        $stFiltro .= " and aece.inscricao_economica = ".$this->inInscricaoEconomicaFinal;
    }

    /* FILTRO POR CONTRIBUINTE */
    if (( $this->inCodContribuinteInicial ) && ( $this->inCodContribuinteFinal )) {
        $stFiltro .= " and cgm.numcgm between ".$this->inCodContribuinteInicial." and ".$this->inCodContribuinteFinal;
    } elseif (( $this->inCodContribuinteInicial ) && ( !$this->inCodContribuinteFinal )) {
        $stFiltro .= " and cgm.numcgm = ".$this->inCodContribuinteInicial;
    } elseif (( !$this->inCodContribuinteInicial ) && ( $this->inCodContribuinteFinal )) {
        $stFiltro .= " and cgm.numcgm = ".$this->inCodContribuinteFinal;
    }

    /* FILTRO POR LOCALIZACAO */
    if (( $this->stValorCompostoInicial ) && ( $this->stValorCompostoFinal )) {
        $stFiltro .= " and aii.codigo_composto between '".$this->stValorCompostoInicial."' and '".$this->stValorCompostoFinal."'";
    } elseif (( $this->stValorCompostoInicial ) && ( !$this->stValorCompostoFinal )) {
        $stFiltro .= " and aii.codigo_composto like '".$this->stValorCompostoInicial."'%";
    } elseif (( !$this->stValorCompostoInicial ) && ( $this->stValorCompostoFinal )) {
        $stFiltro .= " and aii.codigo_composto like '".$this->stValorCompostoFinal."'%";
    }

    /* FILTRO POR ATIVIDADE */
    if (( $this->inCodAtividadeInicial ) && ( $this->inCodAtividadeFinal )) {
        $stFiltro .= " and aece.cod_estrutural between '".$this->inCodAtividadeInicial."' and '".$this->inCodAtividadeFinal."'";
    } elseif (( $this->inCodAtividadeInicial ) && ( !$this->inCodAtividadeFinal )) {
        $stFiltro .= " and aece.cod_estrutural = '".$this->inCodAtividadeInicial."'";
    } elseif (( !$this->inCodAtividadeInicial ) && ( $this->inCodAtividadeFinal )) {
        $stFiltro .= " and aece.cod_estrutural = '".$this->inCodAtividadeFinal."'";
    }

    $stFiltro .= " and ac.exercicio = '".$this->stExercicio."' \n";

    if ( $this->obRARRParcela->roRARRLancamento->roRARRCalculo->getCodCalculo() ) {
        $stFiltro .= " and ac.cod_calculo in (".$this->obRARRParcela->roRARRLancamento->roRARRCalculo->getCodCalculo().") \n ";
    }

    if ( $this->obRARRParcela->roRARRLancamento->getCodLancamento() ) {
        $stFiltro .= " and al.cod_lancamento = ".$this->obRARRParcela->roRARRLancamento->getCodLancamento()." \n ";
    }

    $obErro = $this->obTARRCarne->geraReemitirCarneManaquiri( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
* Diversos
*/

function reemitirCarneDiverso(&$rsRecordSet, $boTransacao = '')
{
    $stFiltro = '';
    $stOrdem = " ) AS registros ";

    /* FILTRO POR CREDITO */
    if ($this->inCodCredito) {
        $stFiltro .= " and mc.cod_credito = ".$this->inCodCredito;
    }

    /* FILTRO POR GRUPO */
    if ($this->inCodGrupo) {
        $stFiltro .= " and acgc.cod_grupo = ".$this->inCodGrupo;
    }

    /* FILTRO POR INSCRICAO IMOBILIARIA */
    if (( $this->inInscricaoImobiliariaInicial ) && ( $this->inInscricaoImobiliariaFinal )) {
        $stFiltro .= " and aii.inscricao_municipal between ".$this->inInscricaoImobiliariaInicial." and ".$this->inInscricaoImobiliariaFinal;
    } elseif (( $this->inInscricaoImobiliariaInicial ) && ( !$this->inInscricaoImobiliariaFinal )) {
        $stFiltro .= " and aii.inscricao_municipal = ".$this->inInscricaoImobiliariaInicial;
    } elseif (( !$this->inInscricaoImobiliariaInicial ) && ( $this->inInscricaoImobiliariaFinal )) {
        $stFiltro .= " and aii.inscricao_municipal = ".$this->inInscricaoImobiliariaFinal;
    }

    /* FILTRO POR INSCRICAO ECONOMICA */
    if (( $this->inInscricaoEconomicaInicial ) && ( $this->inInscricaoImobiliariaFinal )) {
        $stFiltro .= " and aece.inscricao_economica between ".$this->inInscricaoEconomicaInicial." and ".$this->inInscricaoEconomicaFinal;
    } elseif (( $this->inInscricaoEconomicaInicial ) && ( !$this->inInscricaoEconomicaFinal )) {
        $stFiltro .= " and aece.inscricao_economica = ".$this->inInscricaoEconomicaInicial;
    } elseif (( !$this->inInscricaoEconomicaInicial ) && ( $this->inInscricaoEconomicaFinal )) {
        $stFiltro .= " and aece.inscricao_economica = ".$this->inInscricaoEconomicaFinal;
    }

    /* FILTRO POR CONTRIBUINTE */
    if ($this->stCodContribuinteConjunto) {
        $this->obTARRCarne->setDado('numcgm', $this->stCodContribuinteConjunto);
        $stOrdem .= "GROUP BY inscricao_municipal, natureza, quadra, lote, zoneamento, endereco_entrega, numero_entrega, bairro_entrega, cep_entrega, municipio_entrega, nom_logradouro, numero";
        $stOrdem .= ", bairro, cep, municipio, cod_calculo, exercicio, data_processamento, descricao, ano_exercicio, cod_grupo, descricao_credito, cod_credito, valor, observacao";
        $stFiltro .= " and cgm.numcgm IN (".$this->stCodContribuinteConjunto.")";
    } elseif (( $this->inCodContribuinteInicial ) && ( $this->inCodContribuinteFinal )) {
        $stFiltro .= " and cgm.numcgm between ".$this->inCodContribuinteInicial." and ".$this->inCodContribuinteFinal;
    } elseif (( $this->inCodContribuinteInicial ) && ( !$this->inCodContribuinteFinal )) {
        $stFiltro .= " and cgm.numcgm = ".$this->inCodContribuinteInicial;
    } elseif (( !$this->inCodContribuinteInicial ) && ( $this->inCodContribuinteFinal )) {
        $stFiltro .= " and cgm.numcgm = ".$this->inCodContribuinteFinal;
    }

    /* FILTRO POR LOCALIZACAO */
    if (( $this->stValorCompostoInicial ) && ( $this->stValorCompostoFinal )) {
        $stFiltro .= " and aii.codigo_composto between '".$this->stValorCompostoInicial."' and '".$this->stValorCompostoFinal."'";
    } elseif (( $this->stValorCompostoInicial ) && ( !$this->stValorCompostoFinal )) {
        $stFiltro .= " and aii.codigo_composto like '".$this->stValorCompostoInicial."'%";
    } elseif (( !$this->stValorCompostoInicial ) && ( $this->stValorCompostoFinal )) {
        $stFiltro .= " and aii.codigo_composto like '".$this->stValorCompostoFinal."'%";
    }

    /* FILTRO POR ATIVIDADE */
    if (( $this->inCodAtividadeInicial ) && ( $this->inCodAtividadeFinal )) {
        $stFiltro .= " and aece.cod_estrutural between '".$this->inCodAtividadeInicial."' and '".$this->inCodAtividadeFinal."'";
    } elseif (( $this->inCodAtividadeInicial ) && ( !$this->inCodAtividadeFinal )) {
        $stFiltro .= " and aece.cod_estrutural = '".$this->inCodAtividadeInicial."'";
    } elseif (( !$this->inCodAtividadeInicial ) && ( $this->inCodAtividadeFinal )) {
        $stFiltro .= " and aece.cod_estrutural = '".$this->inCodAtividadeFinal."'";
    }

    if ( $this->stExercicio )
        $stFiltro .= " and ac.exercicio = '".$this->stExercicio."' \n";

    if ( $this->obRARRParcela->roRARRLancamento->roRARRCalculo->getCodCalculo() ) {
        $stFiltro .= " and ac.cod_calculo in (".$this->obRARRParcela->roRARRLancamento->roRARRCalculo->getCodCalculo().") \n ";
    }

    if ( $this->obRARRParcela->roRARRLancamento->getCodLancamento() ) {
        $stFiltro .= " and al.cod_lancamento = ".$this->obRARRParcela->roRARRLancamento->getCodLancamento()." \n ";
    }

    $obErro = $this->obTARRCarne->CarneDiverso( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

function emitirCarneDivida(&$rsRecordSet, $boTransacao = '')
{
    $stFiltro = '';
    /* FILTRO POR CONTRIBUINTE */
    if (( $this->inCodContribuinteInicial ) && ( $this->inCodContribuinteFinal )) {
        $stFiltro .= " and cgm.numcgm between ".$this->inCodContribuinteInicial." and ".$this->inCodContribuinteFinal;
    } elseif (( $this->inCodContribuinteInicial ) && ( !$this->inCodContribuinteFinal )) {
        $stFiltro .= " and cgm.numcgm = ".$this->inCodContribuinteInicial;
    } elseif (( !$this->inCodContribuinteInicial ) && ( $this->inCodContribuinteFinal )) {
        $stFiltro .= " and cgm.numcgm = ".$this->inCodContribuinteFinal;
    }

    if ( $this->stExercicio )
        $stFiltro .= " and ac.exercicio = '".$this->stExercicio."' \n";

    if ( $this->obRARRParcela->roRARRLancamento->roRARRCalculo->getCodCalculo() ) {
        $stFiltro .= " and ac.cod_calculo in (".$this->obRARRParcela->roRARRLancamento->roRARRCalculo->getCodCalculo().") \n ";
    }

    if ( $this->obRARRParcela->roRARRLancamento->getCodLancamento() ) {
        $stFiltro .= " and al.cod_lancamento = ".$this->obRARRParcela->roRARRLancamento->getCodLancamento()." \n ";
    }

    $obErro = $this->obTARRCarne->CarneDivida( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );
    
    return $obErro;
}

/**
    * Lista carnes para baixa de acordo com o filtro
    * @access Public
    * @param Object $rsRecordSet retorna RecordSet preenchido
    * @param Object $boTransacao contêm a transação do banco
    * @return Object Objeto Erro
*/
function listarCarnesBaixa(&$rsRecordSet, $boTransacao = '')
{
    $stFiltro = $stFiltroPos = '';

    /* FILTRO POR EXERCICIO */
    if ($this->stExercicio) {
        if ($this->stTipo == "da") {
            $stFiltro .= " calculo.exercicio= '".$this->stExercicio."' and";
        } else {
            $stFiltro .= " ac.exercicio= '".$this->stExercicio."' and";
        }
    }

    /* FILTRO POR NUMERACAO */
    if ($this->stNumeracao) {
        $stFiltro .= " (carne.numeracao = '".trim($this->stNumeracao)."' or carne_consolidacao.numeracao_consolidacao = '".trim($this->stNumeracao)."') and";

    }

    /* FILTRO POR NUMERACAO MIGRADA */
    if ($this->stNumeracaoMigrada) {
        $stFiltroPos .= " trim( TABELA.numeracao_migrada )='".trim($this->stNumeracaoMigrada)."' and";
    }

    /* FILTRO POR CONTRIBUINTE */
    if (( $this->inCodContribuinteInicial ) && ( $this->inCodContribuinteFinal )) {
        $stFiltro .= " sw_cgm.numcgm between ".$this->inCodContribuinteInicial." and ".$this->inCodContribuinteFinal." and";
        //$stFiltro .= " TABELA.numcgm between ".$this->inCodContribuinteInicial." and ".$this->inCodContribuinteFinal." and";
    } elseif (( $this->inCodContribuinteInicial ) && ( !$this->inCodContribuinteFinal )) {
        $stFiltro .= " sw_cgm.numcgm = ".$this->inCodContribuinteInicial." and";
       // $stFiltro .= " TABELA.numcgm = ".$this->inCodContribuinteInicial." and";
    } elseif (( !$this->inCodContribuinteInicial ) && ( $this->inCodContribuinteFinal )) {
        $stFiltro .= " sw_cgm.numcgm = ".$this->inCodContribuinteFinal." and";
        //$stFiltro .= " TABELA.numcgm = ".$this->inCodContribuinteFinal." and";
    }

    /* FILTRO POR LOCALIZACAO */
    if (( $this->stValorCompostoInicial ) && ( $this->stValorCompostoFinal )) {
        $stFiltroPos .= " TABELA.codigo_composto between '".$this->stValorCompostoInicial."' and '".$this->stValorCompostoFinal."' and";
    } elseif (( $this->stValorCompostoInicial ) && ( !$this->stValorCompostoFinal )) {
        $stFiltroPos .= " TABELA.codigo_composto like '".$this->stValorCompostoInicial."'% and";
    } elseif (( !$this->stValorCompostoInicial ) && ( $this->stValorCompostoFinal )) {
        $stFiltroPos .= " TABELA.codigo_composto like '".$this->stValorCompostoFinal."'% and";
    }

    /* FILTRO POR ATIVIDADE */
    if (( $this->inCodAtividadeInicial ) && ( $this->inCodAtividadeFinal )) {
        $stFiltroPos .= " TABELA.cod_estrutural between '".$this->inCodAtividadeInicial."' and '".$this->inCodAtividadeFinal."' and";
    } elseif (( $this->inCodAtividadeInicial ) && ( !$this->inCodAtividadeFinal )) {
        $stFiltroPos .= " TABELA.cod_estrutural = '".$this->inCodAtividadeInicial."' and";
    } elseif (( !$this->inCodAtividadeInicial ) && ( $this->inCodAtividadeFinal )) {
        $stFiltroPos .= " TABELA.cod_estrutural = '".$this->inCodAtividadeFinal."' and";
    }

    /* FILTRO POR INSCRICAO IMOBILIARIA */
    if (( $this->inInscricaoImobiliariaInicial ) && ( $this->inInscricaoImobiliariaFinal )) {
        $stFiltro .= " aic.inscricao_municipal between ".$this->inInscricaoImobiliariaInicial." and ".$this->inInscricaoImobiliariaFinal." and";
    } elseif (( $this->inInscricaoImobiliariaInicial ) && ( !$this->inInscricaoImobiliariaFinal )) {
        $stFiltro .= " aic.inscricao_municipal = ".$this->inInscricaoImobiliariaInicial." and";
    } elseif (( !$this->inInscricaoImobiliariaInicial ) && ( $this->inInscricaoImobiliariaFinal )) {
        $stFiltro .= " aic.inscricao_municipal = ".$this->inInscricaoImobiliariaFinal." and";
    }
    /* FILTRO POR INSCRICAO ECONOMICA */
    if ( $this->getInscricaoEconomicaInicial() && $this->getInscricaoEconomicaFinal() ) {
        $stFiltro .= " cec.inscricao_economica between ".$this->getInscricaoEconomicaInicial()." and ".$this->getInscricaoEconomicaFinal()." and";
    } elseif (( $this->getInscricaoEconomicaInicial() ) && ( !$this->getInscricaoEconomicaFinal() )) {
        $stFiltro .= " cec.inscricao_economica = ".$this->getInscricaoEconomicaInicial()." and";
    } elseif (( !$this->getInscricaoEconomicaInicial() ) && ( $this->getInscricaoEconomicaFinal() )) {
        $stFiltro .= " cec.inscricao_economica = ".$this->getInscricaoEconomicaFinal()." and";
    }

    if ($this->inInscricaoDivida) {
        $stFiltroPos .= " TABELA.cod_inscricao = ".$this->inInscricaoDivida." and";
    }

    if ($this->stTipo == "da") {
         $stFiltro .= " divida_cancelada.cod_inscricao IS NULL AND parcelamento.numero_parcelamento = '".$this->getNumCobranca()."' AND parcelamento.exercicio = '".$this->getExercicioCobranca()."' AND ";
        if ( $this->getCredito() ) {
            $arTMP = explode ( '.', $this->inCodCredito );
            $stFiltro .= "\n calculo.cod_credito   = ".$arTMP[0]." and ";
            $stFiltro .= "\n calculo.cod_especie   = ".$arTMP[1]." and ";
            $stFiltro .= "\n calculo.cod_genero    = ".$arTMP[2]." and ";
            $stFiltro .= "\n calculo.cod_natureza  = ".$arTMP[3]." and ";
        }

        $stOrdem = "\n order by TABELA.numero_parcelamento , TABELA.numcgm, TABELA.cod_lancamento, TABELA.nr_parcela";

    } else {
        /* FILTRO POR GRUPO DE CREDITO */
        if ( $this->getGrupo() ) {
            $stFiltro .= " acgc.cod_grupo = ".$this->inCodGrupo." and";
        }
        /* FILTRO POR CREDITO */
        if ( $this->getCredito() ) {
            $arTMP = explode ( '.', $this->inCodCredito );
            // array [0]> cod_credito [1]> cod_especie [2]> cod_genero [3]> cod_natureza
            $stFiltro .= "\n ac.cod_credito   = ".$arTMP[0]." and ";
            $stFiltro .= "\n ac.cod_especie   = ".$arTMP[1]." and ";
            $stFiltro .= "\n ac.cod_genero    = ".$arTMP[2]." and ";
            $stFiltro .= "\n ac.cod_natureza  = ".$arTMP[3]." and ";
        }

    }

    if ($this->stTipo != "da") {
        $stFiltro .= " carne.cod_convenio != -1 and ";
        $stOrdem = "\n order by TABELA.inscricao, TABELA.numcgm, TABELA.cod_lancamento, TABELA.nr_parcela";
    }

    if ($this->stTipo == "cgm") {

    $stOrdem = "\n order by todos.inscricao, todos.numcgm, todos.cod_lancamento, todos.nr_parcela";

    $stFiltroPos .= " ( TABELA.situacao = 'A Vencer' or TABELA.situacao = 'Vencida' ) \n";
    $stFiltroPos .= " group by cod_lancamento
        , nr_parcela
        , vencimento
        , numeracao
        , exercicio
        , cod_carteira
        , cod_convenio
        , numcgm
        , nom_cgm
        , cod_credito
        , cod_natureza
        , cod_genero
        , cod_especie
        , descricao_credito
        , convenio_atual
        , inscricao_economica
        , inscricao_municipal
        , inscricao
        , carteira_atual
        , info_parcela
        , numeracao_migrada
        , cod_grupo
        , origem
        , situacao
        , valida ";
    $stFiltroPos .= " ) as todos                                                         \n";
    $stFiltroPos .= " LEFT JOIN arrecadacao.pagamento                                       \n";
    $stFiltroPos .= " ON todos.numeracao = pagamento.numeracao                              \n";
    $stFiltroPos .= " AND todos.cod_convenio = pagamento.cod_convenio                       \n";
    $stFiltroPos .= "  WHERE pagamento.numeracao is null                                 \n";
    } else {
    $stFiltroPos .= " \n ( TABELA.situacao = 'A Vencer' or TABELA.situacao = 'Vencida' ) AND\n";
    }

    if ($stFiltro) {
        $stFiltro = "\r\n\t WHERE ". substr ( $stFiltro, 0, (strlen ( $stFiltro ) - 4 ) ) ;
    }
    if ($stFiltroPos) {
        $stFiltroPos  = "\n\n WHERE ". substr ( $stFiltroPos, 0, (strlen ( $stFiltroPos ) - 4 ) ) ;
    }

    if ($this->stTipo == "imobiliaria") {
       
        $obErro = $this->obTARRCarne->recuperaListaReEmissaoImobiliario( $rsRecordSet, $stFiltro,  $stFiltroPos, $stOrdem, $boTransacao );
    } elseif ($this->stTipo == "economica") {
        
        $obErro = $this->obTARRCarne->recuperaListaReEmissaoEconomico( $rsRecordSet, $stFiltro,  $stFiltroPos, $stOrdem, $boTransacao );
    } elseif ($this->stTipo == "cgm") {
       
        if ($this->stExercicio) {
            $stFiltroExercicioPagamentos = " exercicio = '".$this->stExercicio."' ";
        }
        $obErro = $this->obTARRCarne->recuperaListaReEmissaoCgm( $rsRecordSet, $stFiltro, $stFiltroExercicioPagamentos ,$stFiltroPos, $stOrdem, $boTransacao );
    } elseif ($this->stTipo == "da") {

        $obErro = $this->obTARRCarne->recuperaListaReEmissaoDividaAtiva( $rsRecordSet, $stFiltro, $stFiltroPos, $stOrdem, $boTransacao );
    }



    return $obErro;
}

/**
    * Executa um recuperaValoresCarne na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarValoresCarne(&$rsRecordSet, $boTransacao = "")
{
    $obTARRCarne = new TARRCarne();
    $obTARRCarne->setDado( 'exercicio', $this->stExercicio );
    $obTARRCarne->setDado( 'numeracao', $this->stNumeracao );
    $obErro = $obTARRCarne->recuperaValoresCarne( $rsRecordSet,$boTransacao );

    return $obErro;
}

/**
    * Recupera valores relativo ao Lançamento, Calculo e Parcelas
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultarLancamento(&$rsRecordSet, $boTransacao = '')
{
    $stFiltro = '';
    if ($this->stNumeracao) {
        $stFiltro = " c.numeracao = '".$this->stNumeracao."' AND";
    }

    if ($stFiltro) {
        $stFiltro = " WHERE ". substr ( $stFiltro, 0, strlen ( $stFiltro ) - 4 );
    }
    $this->obTARRCarne->setDado( 'numero_carne', $this->stNumeracao );
    $obErro = $this->obTARRCarne->recuperaLancamento( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

function consultarCompensacao(&$rsRecordSet, $boTransacao = '')
{
    $stFiltro = '';

    if ($this->inCodGrupo) {
        $stFiltro .= " credito_grupo.cod_grupo = ".$this->inCodGrupo." AND";
    }

    if ($this->inExercicio) {
        $stFiltro .= " credito_grupo.ano_exercicio = ".$this->inExercicio." AND";
    }

    if ($this->inCodCredito) {
        $stFiltro .= " credito.cod_credito = ".$this->inCodCredito." AND";
    }

    if ($this->inCodEspecie) {
        $stFiltro .= " credito.cod_especie = ".$this->inCodEspecie." AND";
    }

    if ($this->inCodGenero) {
        $stFiltro .= " credito.cod_genero = ".$this->inCodGenero." AND";
    }

    if ($this->inCodNatureza) {
        $stFiltro .= " credito.cod_natureza = ".$this->inCodNatureza." AND";
    }

    if ($stFiltro) {
        $stFiltro = " WHERE ". substr ( $stFiltro, 0, strlen ( $stFiltro ) - 4 );
    }

    $obErro = $this->obTARRCarne->retornaDadosCompensacao( $rsRecordSet, $stFiltro, $boTransacao );
    return $obErro;
}

/**
    * Verifica se a parcela faz parte de um calculo do economico
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function verificaCarneEconomico(&$rsRecordSet, $boTransacao = '')
{
    $stFiltro = '';
    if ($this->stNumeracao) {
        $stFiltro = " AND c.numeracao = '".$this->stNumeracao."'";
    }
    $stOrdem = " ORDER BY p.cod_parcela ASC";
    $obErro = $this->obTARRCarne->verificaCarneEconomico( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    * Verifica se a parcela faz parte de um calculo do imobiliario
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function verificaCarneImobiliario(&$rsRecordSet , $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->obRARRParcela->roRARRLancamento->roRARRCalculo->getCodCalculo() ) {
        $stFiltro .= " ic.cod_calculo =".$this->obRARRParcela->roRARRLancamento->roRARRCalculo->getCodCalculo()." \n ";
    }
    $obErro = $this->obTARRCarne->verificaCarneImobiliario( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    * Verifica se uma parcela ja foi paga
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function verificaPagamento(&$rsRecordSet, $boTransacao = '')
{
    $stFiltro = '';
    if ($this->stNumeracao) {
        $stFiltro = " AND numeracao = '".$this->stNumeracao."'";
    }
    $stOrdem = " order by ocorrencia_pagamento desc limit 1 ";
    $obErro = $this->obTARRCarne->verificaPagamento( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );
    return $obErro;
}

/**
    * Verifica se o carne é consolidado
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function verificaConsolidacao(&$rsRecordSet, $boTransacao = '')
{
    $this->obTARRCarneConsolidacao = new TARRCarneConsolidacao;
    $stFiltro = '';
    if ($this->stNumeracao) {
        $stFiltro = " WHERE trim(leading '0' from numeracao_consolidacao) = '".$this->stNumeracao."'";
    }
    $obErro = $this->obTARRCarneConsolidacao->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    * Lista as parcelas do lancamento de acordo com o filtro setado
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listaParcelasLancamento(&$rsRecordSet, $boTransacao = '')
{
    $stFiltro = '';
    if ( $this->obRARRParcela->roRARRLancamento->getCodLancamento() ) {
        $stFiltro .= " p.cod_lancamento = ".$this->obRARRParcela->roRARRLancamento->getCodLancamento()." AND ";
    }
    if ( $this->obRARRParcela->getNrParcela() > 0 ) {
        $stFiltro .= " p.nr_parcela = 0 AND ";
    } else {
        $stFiltro .= " p.cod_parcela <> ".$this->obRARRParcela->getCodParcela()." AND ";
    }

    if ($stFiltro) {
        $stFiltro = " WHERE ". substr ( $stFiltro, 0, strlen ( $stFiltro ) - 4 );
    }

    $stOrdem = " order by p.nr_parcela";
    $obErro = $this->obTARRCarne->listaParcelasLancamento( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

function listaCarnesParaCancelar(&$rsRecordSet, $boTransacao = '')
{
    $obErro = $this->obTARRCarne->listaCarnesParaCancelar( $rsRecordSet, $this->obRARRParcela->getCodParcela(), $boTransacao );

    return $obErro;
}

function listarConsulta(&$rsRecordSet , $boTransacao = "", $dtDataBase = "", $dtVencimentoPR ="")
{
    $stFiltro = "";
    // se a data estiver vazia
    if ( !$dtDataBase) $dtDataBase = date("Y-m-d");
    if ( $this->obRARRParcela->roRARRLancamento->getCodLancamento() ) {
        $stFiltro .= " al.cod_lancamento= ".$this->obRARRParcela->roRARRLancamento->getCodLancamento() . " AND ";
    }
    if ( $this->getNumeracao() ) {
        $stNumeracao = $this->getNumeracao();
        $stFiltro .= " carne.numeracao= '".$this->getNumeracao()."' AND ";
    }
    if ( $this->getOcorrenciaPagamento() ) {
        $stFiltro .= " apag.ocorrencia_pagamento = ".$this->getOcorrenciaPagamento()." AND ";
    }
    if ( $this->obRARRParcela->getCodParcela() ) {
        $stFiltro .= " ap.cod_parcela= ".$this->obRARRParcela->getCodParcela(). " AND ";
    }

    if ($stFiltro) {
        $stFiltro = " ". substr ( $stFiltro, 0, strlen ( $stFiltro ) - 4 );
    }

   $obErro = $this->obTARRCarne->recuperaConsulta ( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao , $dtDataBase, $dtVencimentoPR, $stNumeracao );

   return $obErro;
}

function listarDetalheCreditosConsulta(&$rsRecordSet , $boTransacao = "", $dtDataBase = "", $dtVencimentoPR ="")
{
    $stFiltro = "";
    // se a data estiver vazia
    if ( !$dtDataBase ) $dtDataBase = date("Y-m-d");
    if ( $this->obRARRParcela->roRARRLancamento->getCodLancamento() ) {
        $stFiltro .= " al.cod_lancamento= ".$this->obRARRParcela->roRARRLancamento->getCodLancamento()." and ";
    }
    if ( $this->getNumeracao() ) {
        $stNumeracao = $this->getNumeracao();
        $stFiltro .= " carne.numeracao= '".$this->getNumeracao()."' and ";
    }
    if ( $this->getOcorrenciaPagamento() ) {
        $stFiltro .= " apag.ocorrencia_pagamento = ".$this->getOcorrenciaPagamento()." AND ";
    }
    if ( $this->obRARRParcela->getCodParcela() ) {
        $stFiltro .= " ap.cod_parcela= ".$this->obRARRParcela->getCodParcela()." and ";
    }
    if ($stFiltro) {
        $stFiltro = substr ( $stFiltro, 0, strlen ( $stFiltro ) - 4 );
    }
    $obErro = $this->obTARRCarne->recuperaDetalheCreditosConsulta($rsRecordSet, $stFiltro, $stOrdem, $boTransacao, $dtDataBase, $dtVencimentoPR, $stNumeracao );

    return $obErro;
}

function listarDetalheCreditosBaixa(&$rsRecordSet , $boTransacao = "", $dtDataBase = "")
{
    $stFiltro = "";
    // se a data estiver vazia
    if ( !$dtDataBase ) $dtDataBase = date("Y-m-d");
    if ( $this->obRARRParcela->roRARRLancamento->getCodLancamento() ) {
        $stFiltro .= " cod_lancamento= ".$this->obRARRParcela->roRARRLancamento->getCodLancamento()." and ";
    }
    if ( $this->getNumeracao() ) {
        $stFiltro .= " numeracao= '".$this->getNumeracao()."' and ";
        // se o filtro é por numeração, podemos buscar cod_lancamento e cod_parcela para agilizar a busca
        $rsParcelaLancamento = new Recordset();
        $this->obTARRCarne->executaRecupera("montaRecuperaParcelaLancamentoPorNumeracao",
                                            $rsParcelaLancamento,
                                            sprintf(' where numeracao = \'%s\' ',$this->getNumeracao()), // filtro
                                            '', // order by
                                            $boTransacao);
        $this->obTARRCarne->setDado('cod_lancamento' , $rsParcelaLancamento->getCampo('cod_lancamento'));
        $this->obTARRCarne->setDado('cod_parcela' , $rsParcelaLancamento->getCampo('cod_parcela'));
    }
    if ( $this->obRARRParcela->getCodParcela() ) {
        $stFiltro .= " cod_parcela= ".$this->obRARRParcela->getCodParcela()." and ";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ". substr ( $stFiltro, 0, strlen ( $stFiltro ) - 4 );
    }
    $stOrdem = " ORDER BY cod_parcela, nr_parcela";
    $obErro = $this->obTARRCarne->recuperaDetalheCreditosBaixa($rsRecordSet, $stFiltro, $stOrdem, $boTransacao , $dtDataBase, $valorPorcentagem );

    return $obErro;
}

function listarDetalheCreditosBaixaDivida(&$rsRecordSet , $boTransacao = "", $dtDataBase = "")
{
    $stFiltro = "";
    // se a data estiver vazia
    if ( !$dtDataBase ) $dtDataBase = date("Y-m-d");
    if ( $this->getNumeracao() ) {
        $stFiltro .= " carne.numeracao= '".$this->getNumeracao()."'";
    }
    $this->obTARRCarne->setDado('numero_carne' , $this->getNumeracao());
    $obErro = $this->obTARRCarne->recuperaDetalheCreditosBaixaDivida($rsRecordSet, $stFiltro, $boTransacao , $dtDataBase, $valorPorcentagem );

    return $obErro;
}

function recuperaConvenio(&$rsRecordSet , $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->getNumeracao() ) {
        $stFiltro .= " numeracao= '".$this->getNumeracao()."' AND";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }
    $stOrdem = " timestamp limit 1";
    $obErro = $this->obTARRCarne->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );
    
    return $obErro;
}

function recuperaNumeracao(&$rsRecordSet , $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->getNumeracaoMigrada() ) {
        $stFiltro .= " AND trim(numeracao_migracao)= '".trim($this->getNumeracaoMigrada())."'";
    }
    if ($this->stExercicio) {
        $stFiltro .= " AND prefixo='".$this->stExercicio."'";
    }
    $obErro = $this->obTARRCarne->recuperaNumeracao( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );
    
    return $obErro;
}
function listarPagamentosConsulta(&$rsRecordSet , $boTransacao = "", $dtDataBase = "")
{
    $stFiltro = "";
    // se a data estiver vazia
    if ( !$dtDataBase ) $dtDataBase = date("Y-m-d");

    if ( $this->obRARRParcela->roRARRLancamento->getCodLancamento() ) {
        $stFiltro .= " p.cod_lancamento= ".$this->obRARRParcela->roRARRLancamento->getCodLancamento()." and ";
    }

    if ( $this->getNumeracao() ) {
        $stFiltro .= " c.numeracao= '".$this->getNumeracao()."' and ";
    }

    if ( $this->obRARRParcela->getCodParcela() ) {
        $stFiltro .= " p.cod_parcela= ".$this->obRARRParcela->getCodParcela()." and ";
    }

    if ($stFiltro) {
        $stFiltro = "\r\n\t WHERE \r\n".substr($stFiltro,0, -4)."";
    }

    $stOrdem = " ORDER BY pag.ocorrencia_pagamento ";
    $obErro = $this->obTARRCarne->recuperaListaPagamentosConsulta($rsRecordSet, $stFiltro, $stOrdem, $boTransacao , $dtDataBase, $valorPorcentagem );

    return $obErro;
}

/**
    * Recupera valores de juro, multa e original da parcela informada
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function recuperaValorParcelaJuroMulta(&$rsRecordSet, $dtDataVencimento, $dtDataPagamento, $nuValorPagamento, $boTransacao = '')
{
    $stFiltro = '';
    if ($dtDataVencimento) {
        $this->obTARRCarne->setDado('data_vencimento', $dtDataVencimento);
    }
    if ($dtDataPagamento) {
        $this->obTARRCarne->setDado('data_pagamento', $dtDataPagamento);
    }
    if ($nuValorPagamento) {
        $this->obTARRCarne->setDado('valor', $nuValorPagamento);
    }
    $obErro = $this->obTARRCarne->recuperaValorParcelaJuroMulta( $rsRecordSet, $boTransacao );

    return $obErro;
}

function listarEmissaoCarne(&$rsRecordSet, $boTransacao = '')
{
    $stFiltro = '';

    if ($this->inCodGrupo) {
        $stFiltro .= " and calculo_grupo_credito.cod_grupo = ".$this->inCodGrupo;
    }
    if ( $this->obRARRParcela->roRARRLancamento->getCodLancamento() ) {
        $stFiltro .= " and lancamento.cod_lancamento = ". $this->obRARRParcela->roRARRLancamento->getCodLancamento();
    }

    $stFiltro .= " and calculo.exercicio = '".$this->getExercicio()."' ";

    $stOrdem = ' order by parcela.cod_parcela';

    $obErro = $this->obTARRParcela->recuperaParcelasParaEmissao( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );
    
    return $obErro;
}

function listarCarneConsulta(&$rsRecordSet , $boTransacao = "")
{
    $stFiltro = "";
    $stOrdem = "";
    $stFiltroExercicio = "";
    $stFiltroExercicio2 = "";
    $boNumeracao = false;

    if ( $this->obRARRParcela->roRARRLancamento->obRCIMImovel->getNumeroInscricao() ) {
        $stTipo = "ii";
        $stFiltro .= "ic.inscricao_municipal = ".$this->obRARRParcela->roRARRLancamento->obRCIMImovel->getNumeroInscricao()." AND ";
    }
    if ( $this->obRARRParcela->roRARRLancamento->obRCEMInscricaoEconomica->getInscricaoEconomica() ) {
        $stTipo = "ie";
        $stFiltro .= "cec.inscricao_economica = ".$this->obRARRParcela->roRARRLancamento->obRCEMInscricaoEconomica->getInscricaoEconomica()." AND ";
    }
    if ( $this->obRARRParcela->roRARRLancamento->obRCgm->getNumCgm() ) {
        $stFiltro .= "cgm.numcgm = ".$this->obRARRParcela->roRARRLancamento->obRCgm->getNumCgm()." AND ";
    }
    if ( $this->obRARRParcela->roRARRLancamento->roRARRCalculo->getExercicio() ) {

        $stFiltro .= " calc.exercicio = '".$this->obRARRParcela->roRARRLancamento->roRARRCalculo->getExercicio()."' AND ";

    }

    if ( $this->getNumeracao() ) {
        $boNumeracao = true;
        $stFiltro .= "nac.numeracao in ( '".$this->getNumeracao()."' ) AND ";
    }

    if ($stFiltro) {
        $stFiltro = "\r\n\t WHERE \r\n".substr($stFiltro,0, -4)."";
    }
   
    $obErro = $this->obTARRCarne->recuperaListaConsultaCarne( $rsRecordSet, $stFiltro, $stOrdem, $stTipo, $boNumeracao, $stFiltroExercicio, $stFiltroExercicio2, $boTransacao );

    return $obErro;
}

/**
    * Lista carnes para reemissão de acordo com o filtro
    * @access Public
    * @param Object $rsRecordSet retorna RecordSet preenchido
    * @param Object $boTransacao contêm a transação do banco
    * @return Object Objeto Erro
*/
function novaListaEmissao(&$rsRecordSet, $boTransacao = '')
{
    require_once(CAM_GT_ARR_MAPEAMENTO.'FARRListaEmissao.class.php');
    $obListaEmissao = new FARRListaEmissao;

    $stFiltro = '';
    /* FILTRO POR CREDITO */
    if ($this->inCodCredito) {
        $obListaEmissao->inCodCredito = $this->inCodCredito;
        $obListaEmissao->inCodEspecie = $this->inCodEspecie;
        $obListaEmissao->inCodGenero  = $this->inCodGenero ;
        $obListaEmissao->inCodNatureza= $this->inCodNatureza;
    }
    if ($this->stNumeracao) {
        $obListaEmissao->stNumAnterior= $this->stNumeracao;
    }
    /* FILTRO POR GRUPO */
    if ($this->inCodGrupo) {
        $obListaEmissao->inCodGrupo   = $this->inCodGrupo;
    }
    /* FILTRO POR EXERCICIO */
    if ($this->stExercicio) {
        $obListaEmissao->inExercicio   = $this->stExercicio;
    }

    /* FILTRO POR NUMERACAO */
    if ($this->stNumeracao) {
        $obListaEmissao->stNumAnterior = $this->stNumeracao;
    }

    if ($this->stValorCompostoInicial) {
        $obListaEmissao->stLocInicial = $this->stValorCompostoInicial;
        if ($this->stValorCompostoFinal) {
            $obListaEmissao->stLocFinal = $this->stValorCompostoFinal;
        }
    }else
        if ($this->stValorCompostoFinal) {
            $obListaEmissao->stLocInicial = $this->stValorCompostoFinal;
        }

    /* FILTRO POR CONTRIBUINTE */
    if (( $this->inCodContribuinteInicial ) && ( $this->inCodContribuinteFinal )) {
        $obListaEmissao->inCgmInicial  = $this->inCodContribuinteInicial;
        $obListaEmissao->inCgmFinal    = $this->inCodContribuinteFinal;
    } elseif (( $this->inCodContribuinteInicial ) && ( !$this->inCodContribuinteFinal )) {
        $obListaEmissao->inCgmInicial  = $this->inCodContribuinteInicial;
    } elseif (( !$this->inCodContribuinteInicial ) && ( $this->inCodContribuinteFinal )) {
        $obListaEmissao->inCgmInicial  = $this->inCodContribuinteFinal;
    }

    /* FILTRO POR INSCRICAO IMOBILIARIA */
    if (( $this->inInscricaoImobiliariaInicial ) && ( $this->inInscricaoImobiliariaFinal )) {
        $obListaEmissao->inCodIIInicial = $this->inInscricaoImobiliariaInicial;
        $obListaEmissao->inCodIIFinal   = $this->inInscricaoImobiliariaFinal;
    } elseif (( $this->inInscricaoImobiliariaInicial ) && ( !$this->inInscricaoImobiliariaFinal )) {
        $obListaEmissao->inCodIIInicial = $this->inInscricaoImobiliariaInicial;
    } elseif (( !$this->inInscricaoImobiliariaInicial ) && ( $this->inInscricaoImobiliariaFinal )) {
        $obListaEmissao->inCodIIInicial = $this->inInscricaoImobiliariaFinal;
    }
    /* FILTRO POR INSCRICAO ECONOMICA */
    if ( $this->getInscricaoEconomicaInicial() && $this->getInscricaoEconomicaFinal() ) {
        $obListaEmissao->inCodIEInicial = $this->getInscricaoEconomicaInicial();
        $obListaEmissao->inCodIEFinal   = $this->getInscricaoEconomicaFinal();
    } elseif (( $this->getInscricaoEconomicaInicial() ) && ( !$this->getInscricaoEconomicaFinal() )) {
        $obListaEmissao->inCodIEInicial = $this->getInscricaoEconomicaInicial();
    } elseif (( !$this->getInscricaoEconomicaInicial() ) && ( $this->getInscricaoEconomicaFinal() )) {
        $obListaEmissao->inCodIEInicial = $this->getInscricaoEconomicaFinal();
    }

    $obErro = $obListaEmissao->executaFuncao($rsRecordSet,'',$boTransacao);

    return $obErro;
}

function novaListaEmissaoIPTUDesoneradoMata(&$rsRecordSet, $boTransacao = '')
{
    require_once(CAM_GT_ARR_MAPEAMENTO.'FARRListaEmissaoIPTUDesoneradoMata.class.php');
    $obListaEmissao = new FARRListaEmissaoIPTUDesoneradoMata;

    $stFiltro = '';

    /* FILTRO POR GRUPO */
    if ($this->inCodGrupo) {
        $obListaEmissao->inCodGrupo   = $this->inCodGrupo;
    }
    /* FILTRO POR EXERCICIO */
    if ($this->stExercicio) {
        $obListaEmissao->inExercicio   = $this->stExercicio;
    }

    /* FILTRO POR INSCRICAO IMOBILIARIA */
    if (( $this->inInscricaoImobiliariaInicial ) && ( $this->inInscricaoImobiliariaFinal )) {
        $obListaEmissao->inCodIIInicial = $this->inInscricaoImobiliariaInicial;
        $obListaEmissao->inCodIIFinal   = $this->inInscricaoImobiliariaFinal;
    } elseif (( $this->inInscricaoImobiliariaInicial ) && ( !$this->inInscricaoImobiliariaFinal )) {
        $obListaEmissao->inCodIIInicial = $this->inInscricaoImobiliariaInicial;
    } elseif (( !$this->inInscricaoImobiliariaInicial ) && ( $this->inInscricaoImobiliariaFinal )) {
        $obListaEmissao->inCodIIInicial = $this->inInscricaoImobiliariaFinal;
    }

    $obErro = $obListaEmissao->executaFuncao($rsRecordSet,'',$boTransacao);
    
    return $obErro;
}

function listarModeloDeCarne(&$rsRecordSet, $inCodAcao, $boTransacao = "")
{
    $stFiltro = " WHERE aamc.cod_acao = ".$inCodAcao;
    $obErro = $this->obTARRCarne->recuperaModeloCarne( $rsRecordSet, $stFiltro, $boTransacao );

    return $obErro;
}

function listarCarneDevolucao(&$rsRecordSet , $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->getNumeracao() )
        $stFiltro = $this->getNumeracao();

    $obErro = $this->obTARRCarne->listaCarneDevolucao( $rsRecordSet, $stFiltro, $boTransacao );

    return $obErro;
}

function listarPagamentosCancelados(&$rsRecordSet , $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->getNumeracao() )
        $stFiltro = $this->getNumeracao();

    $obErro = $this->obTARRCarne->listaPagamentosCancelados( $rsRecordSet, $stFiltro, $boTransacao );

    return $obErro;
}

}

?>