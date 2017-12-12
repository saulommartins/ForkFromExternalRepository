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
    * Classe de Regra de Negócio Empenho
    * Data de Criação   : 02/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Eduardo Martins
    * @author Desenvolvedor: Anderson R. M. Buzo
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues

    * @package URBEM
    * @subpackage Regra

    $Id: REmpenhoEmpenho.class.php 66001 2016-07-06 16:51:18Z michel $

    * Casos de uso: uc-02.01.23
                    uc-02.01.08
                    uc-02.03.02
                    uc-02.03.03
                    uc-02.03.04
                    uc-02.03.30
                    uc-02.03.31
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GF_EMP_NEGOCIO."REmpenhoAutorizacaoEmpenho.class.php";
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoReserva.class.php";
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoReservaSaldos.class.php";
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php";
include_once CAM_GF_EMP_NEGOCIO."REmpenhoPreEmpenho.class.php";
include_once CAM_GF_EMP_NEGOCIO."REmpenhoConfiguracao.class.php";
include_once CAM_GRH_ENT_MAPEAMENTO."TEntidade.class.php";

class REmpenhoEmpenho extends REmpenhoPreEmpenho
{
/*
    * @var Object
    * @access Private
*/
var $obTransacao;
/*
    * @var Object
    * @access Private
*/
var $obREmpenhoAutorizacaoEmpenho;
/*
    * @var Object
    * @access Private
*/
var $obROrcamentoReserva;
/*
    * @var Object
    * @access Private
*/
var $obROrcamentoReservaSaldos;
/*
    * @var Object
    * @access Private
*/
var $obROrcamentoEntidade;
/*
    * @var Object
    * @access Private
*/
var $obREmpenhoConfiguracao;
/*
    * @var Integer
    * @access Private
*/
var $inCodEmpenho;
/*
    * @var Integer
    * @access Private
*/
var $inCodEmpenhoInicial;
/*
    * @var Integer
    * @access Private
*/
var $inCodEmpenhoFinal;
/*
    * @var Integer
    * @access Private
*/
var $inCodDespesa;
/*
    * @var String
    * @access Private
*/
var $stDtEmpenho;
/*
    * @var String
    * @access Private
*/
var $stDtEmpenhoInicial;
/*
    * @var String
    * @access Private
*/
var $stDtEmpenhoFinal;
/*
    * @var String
    * @access Private
*/
var $stDtAnulacao;
/*
    * @var String
    * @access Private
*/
var $stDtLiquidacaoInicial;
/*
    * @var String
    * @access Private
*/
var $stDtLiquidacaoFinal;
/*
    * @var String
    * @access Private
*/
var $stDtVencimento;
/*
    * @var Numeric
    * @access Private
*/
var $nuVlSaldoAnterior;
/*
    * @var String
    * @access Private
*/
var $stMotivo;
/*
    * @var String
    * @access Private
*/
var $stExercicioEmissao;
/*
    * @var Boolean
    * @access Private
*/
var $boSomar;
/*
    * @var Boolean
    * @access Private
*/
var $boMaior;
/*
    * @var Boolean
    * @access Private
*/
var $boSomarLiquidacao;
/*
    * @var String
    * @access Private
*/
var $stTimestamp;
/*
    * @var Integer
    * @access Private
*/
var $inSituacao;
/*
    * @var Integer
    * @access Private
*/
var $inCodLiquidacaoInicial;
/*
    * @var Integer
    * @access Private
*/
var $inCodLiquidacaoFinal;
/*
    * @var Integer
    * @access Private
*/
var $inSaldoAnterior;
/*
    * @var Timestamp
    * @access Private
*/
var $stHora;

/*
    * @var Integer
    * @access Private
*/
var $inCodFornecedor;

/*
    * @var Integer
    * @access Private
*/
var $inCodCategoria;

/*
    * @var String
    * @access Private
*/
var $stNomCategoria;
/*
    * @var Integer
    * @access Private
*/
var $inCodContrapartida;
/*
    * @var String
    * @access Private
*/
var $stNomContrapartida;

/*
    * @var Integer
    * @access Private
*/
var $inCodLiquidacao;
/*
    * @var Integer
    * @access Private
*/
var $inNumItemEmpenho;
/*
    * @var Array
    * @access Private
*/
var $arAtributosDinamicos;

var $boComplementar;

var $inCodEmpenhoOriginal;

var $stExercicioEmpenhoOriginal;

/*
    * @var Integer
    * @access Private
*/
var $inCodTipoDocumento; //tceam

/**
    * @var Boolean
    * @access Private
*/
var $boEmpenhoCompraLicitacao;

/**
    * @var Integer
    * @access Private
*/
var $inCodModalidadeCompra;

/**
    * @var Integer
    * @access Private
*/
var $inCompraInicial;

/**
    * @var Integer
    * @access Private
*/
var $inCompraFinal;

/**
    * @var Integer
    * @access Private
*/
var $inCodModalidadeLicitacao;

/**
    * @var Integer
    * @access Private
*/
var $inLicitacaoInicial;

/**
    * @var Integer
    * @access Private
*/
var $inLicitacaoFinal;

/*
    * @access Public
    * @param Object $valor
*/
function setTransacao($valor) { $this->obTransacao = $valor; }
/*
    * @access Public
    * @param Object $valor
*/
function setROrcamentoEntidade($valor) { $this->obROrcamentoEntidade = $valor; }
/*
    * @access Public
    * @param Integer $valor
*/
function setCodEmpenho($valor) { $this->inCodEmpenho = $valor; }
/*
    * @access Public
    * @param Integer $valor
*/
function setCodEmpenhoInicial($valor) { $this->inCodEmpenhoInicial = $valor; }
/*
    * @access Public
    * @param Integer $valor
*/
function setCodEmpenhoFinal($valor) { $this->inCodEmpenhoFinal = $valor; }
/*
    * @access Public
    * @param Integer $valor
*/
function setCodDespesa($valor) { $this->inCodDespesa = $valor; }
/*
    * @access Public
    * @param String $valor
*/
function setDtEmpenho($valor) { $this->stDtEmpenho = $valor; }
/*
    * @access Public
    * @param String $valor
*/
function setDtEmpenhoInicial($valor) { $this->stDtEmpenhoInicial = $valor; }
/*
    * @access Public
    * @param String $valor
*/
function setDtEmpenhoFinal($valor) { $this->stDtEmpenhoFinal = $valor; }
/*
    * @access Public
    * @param String $valor
*/
function setDtAnulacao($valor) { $this->stDtAnulacao = $valor; }
/*
    * @access Public
    * @param String $valor
*/
function setDtVencimento($valor) { $this->stDtVencimento = $valor; }
/*
    * @access Public
    * @param Numeric $valor
*/
function setVlSaldoAnterior($valor) { $this->nuVlSaldoAnterior = $valor; }
/*
    * @access Public
    * @param String $valor
*/
function setMotivo($valor) { $this->stMotivo = $valor; }
/*
    * @access Public
    * @param String $valor
*/
function setExercicioEmissao($valor) { $this->stExercicioEmissao = $valor; }
/*
    * @access Public
    * @param Boolean $valor
*/
function setSomar($valor) { $this->boSomar = $valor; }
/*
    * @access Public
    * @param Boolean $valor
*/
function setMaior($valor) { $this->boMaior = $valor; }
/*
    * @access Public
    * @param Boolean $valor
*/
function setSomarLiquidacao($valor) { $this->boSomarLiquidacao = $valor; }
/*
    * @access Public
    * @param String $valor
*/
function setTimestamp($valor) { $this->stTimestamp = $valor; }
/*
    * @access Public
    * @param Integer $valor
*/
function setSituacao($valor) { $this->inSituacao = $valor; }
/*
    * @access Public
    * @param String $valor
*/
function setNumItemEmpenho($valor) { $this->inNumItemEmpenho = $valor; }
/*
    * @access Public
    * @param Integer $valor
*/
function setCodLiquidacao($valor) { $this->inCodLiquidacao = $valor; }
/*
    * @access Public
    * @param Integer $valor
*/
function setCodLiquidacaoInicial($valor) { $this->inCodLiquidacaoInicial = $valor; }
/*
    * @access Public
    * @param Integer $valor
*/
function setCodLiquidacaoFinal($valor) { $this->inCodLiquidacaoFinal = $valor; }
/*
    * @access Public
    * @param Integer $valor
*/
function setSaldoAnterior($valor) { $this->inSaldoAnterior = $valor; }
/*
    * @access Public
    * @param Timestamp $valor
*/
function setHora($valor) { $this->stHora = $valor; }

/*
    * @access Public
    * @param Integer $valor
*/
function setCodFornecedor($valor) { $this->inCodFornecedor = $valor; }
/*
    * @access Public
    * @param Timestamp $valor
*/
function setCodCategoria($valor) { $this->inCodCategoria = $valor; }
/*
    * @access Public
    * @param String $valor
*/
function setNomCategoria($valor) { $this->stNomCategoria = $valor; }
/*
    * @access Public
    * @param Integer $valor
*/
function setCodContrapartida($valor) { $this->inCodContrapartida = $valor; }
/*
    * @access Public
    * @param String $valor
*/
function setNomContrapartida($valor) { $this->stNomContrapartida = $valor; }
/*
    * @access Public
    * @param Array $valor
*/
function setAtributosDinamicos($valor) { $this->arAtributosDinamicos = $valor; }

/*
    * @access Public
    * @param Integer $valor
*/
function setCodTipoDocumento($valor) { $this->inCodTipoDocumento = $valor; }

/**
    * @access Public
    * @param Boolean $valor
*/
function setBoEmpenhoCompraLicitacao($valor) { $this->boEmpenhoCompraLicitacao = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setCodModalidadeCompra($valor) { $this->inCodModalidadeCompra = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setCompraInicial($valor) { $this->inCompraInicial = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setCompraFinal($valor) { $this->inCompraFinal = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setCodModalidadeLicitacao($valor) { $this->inCodModalidadeLicitacao = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setLicitacaoInicial($valor) { $this->inLicitacaoInicial = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setLicitacaoFinal($valor) { $this->inLicitacaoFinal = $valor; }

/*
    * @access Public
    * @return Object
*/
function getTransacao() { return $this->obTransacao  ; }
/*
    * @access Public
    * @return Object
*/
function getROrcamentoEntidade() { return $this->obROrcamentoEntidade  ; }
/*
    * @access Public
    * @return Integer
*/
function getCodEmpenho() { return $this->inCodEmpenho; }
/*
    * @access Public
    * @return Integer
*/
function getCodEmpenhoInicial() { return $this->inCodEmpenhoInicial; }
/*
    * @access Public
    * @return Integer
*/
function getCodEmpenhoFinal() { return $this->inCodEmpenhoFinal; }
/*
    * @access Public
    * @return Integer
*/
function getCodDespesa() { return $this->inCodDespesa; }
/*
    * @access Public
    * @return String
*/
function getDtEmpenho() {return  $this->stDtEmpenho; }
/*
    * @access Public
    * @return String
*/
function getDtEmpenhoInicial() {return  $this->stDtEmpenhoInicial; }
/*
    * @access Public
    * @return String
*/
function getDtEmpenhoFinal() {return  $this->stDtEmpenhoFinal; }
/*
    * @access Public
    * @return String
*/
function getDtAnulacao() {return  $this->stDtAnulacao; }
/*
    * @access Public
    * @return String
*/
function getDtVencimento() {return  $this->stDtVencimento; }
/*
    * @access Public
    * @return Numeric
*/
function getVlSaldoAnterior() {return  $this->nuVlSaldoAnterior; }
/*
    * @access Public
    * @return String
*/
function getMotivo() {return  $this->stMotivo; }
/*
    * @access Public
    * @return String
*/
function getexErcicioEmissao() {return  $this->stExercicioEmissao; }
/*
    * @access Public
    * @return Boolean
*/
function getSomar() {return  $this->boSomar; }
/*
    * @access Public
    * @return Boolean
*/
function getMaior() {return  $this->boMaior; }
/*
    * @access Public
    * @return Boolean
*/
function getSomarLiquidacao() {return  $this->boSomarLiquidacao; }
/*
    * @access Public
    * @return String
*/
function getTimestamp() {return  $this->stTimestamp; }
/*
    * @access Public
    * @return Integer
*/
function getSituacao() { return $this->inSituacao; }
/*
    * @access Public
    * @return Integer
*/
function getNumItemEmpenho() { return $this->inNumItemEmpenho; }
/*
    * @access Public
    * @return Integer
*/
function getCodLiquidacao() { return $this->inCodLiquidacao; }
/*
    * @access Public
    * @return Integer
*/
function getCodLiquidacaoInicial() { return $this->inCodLiquidacaoInicial; }
/*
    * @access Public
    * @return Integer
*/
function getCodLiquidacaoFinal() { return $this->inCodLiquidacaoFinal; }
/*
    * @access Public
    * @return Integer
*/
function getSaldoAnterior() { return $this->inSaldoAnterior; }
/*
    * @access Public
    * @return Timestamp
*/
function getHora() { return $this->stHora;          }
/*
    * @access Public
    * @return Integer
*/
function getCodFornecedor() { return $this->inCodFornecedor;  }
/*
    * @access Public
    * @return Integer
*/
function getCodCategoria() { return $this->inCodCategoria;  }
/*
    * @access Public
    * @return String
*/
function getNomCategoria() { return $this->stNomCategoria;  }
/*
    * @access Public
    * @return Integer
*/
function getCodContrapartida() { return $this->inCodContrapartida;  }
/*
    * @access Public
    * @return String
*/
function getNomContrapartida() { return $this->stNomContrapartida;  }
/*
    * @access Public
    * @return String
*/
function getAtributosDinamicos() { return $this->arAtributosDinamicos;  }

/*
    * @access Public
    * @return Integer
*/
function getCodTipoDocumento() { return $this->inCodTipoDocumento;  }

/**
    * @access Public
    * @return Boolean
*/
function getBoEmpenhoCompraLicitacao() { return $this->boEmpenhoCompraLicitacao; }

/**
    * @access Public
    * @return Integer
*/
function getCodModalidadeCompra() { return $this->inCodModalidadeCompra; }

/**
    * @access Public
    * @return Integer
*/
function getCompraInicial() { return $this->inCompraInicial; }

/**
    * @access Public
    * @return Integer
*/
function getCompraFinal() { return $this->inCompraFinal; }

/**
    * @access Public
    * @return Integer
*/
function getCodModalidadeLicitacao() { return $this->inCodModalidadeLicitacao; }

/**
    * @access Public
    * @return Integer
*/
function getLicitacaoInicial() { return $this->inLicitacaoInicial; }

/**
    * @access Public
    * @return Integer
*/
function getLicitacaoFinal() { return $this->inLicitacaoFinal; }


/**
    * Método Construtor
    * @access Private
*/
function REmpenhoEmpenho()
{
    parent::REmpenhoPreEmpenho();
    $this->obREmpenhoAutorizacaoEmpenho         =  new REmpenhoAutorizacaoEmpenho;
    $this->obROrcamentoEntidade                 =  new ROrcamentoEntidade;
    $this->obROrcamentoReserva                  =  new ROrcamentoReserva;
    $this->obREmpenhoConfiguracao               =  new REmpenhoConfiguracao;
    $this->obROrcamentoReservaSaldos            =  new ROrcamentoReservaSaldos;
    $this->obTransacao                          =  new Transacao;
}

/**
    * Método para verificar se o pre_empenho já está sendo utilizado no empenho
    * @access Private
    * @param Object $boErro
    * @return Object $obErro
*/
function verificaPreEmpenho($boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO     ."TEmpenhoEmpenho.class.php"                    );
    $obTEmpenhoEmpenho                    =  new TEmpenhoEmpenho;

    $obTEmpenhoEmpenho->setDado( 'cod_pre_empenho', $this->inCodPreEmpenho );
    $obTEmpenhoEmpenho->setDado( 'exercicio'      , $this->stExercicio     );
    $obErro = $obTEmpenhoEmpenho->recuperaPreEmpenho( $rsRecordSet, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if( $rsRecordSet->getCampo('cod_pre_empenho') != 0 )
            $obErro->setDescricao( 'Estes itens já foram empenhados. Pre-empenho: '.$rsRecordSet->getCampo('cod_pre_empenho') );
    }

    return $obErro;
}

/**
    * Salva os dados no banco de dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluir($boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO     ."TEmpenhoEmpenho.class.php"                    );
    include_once ( CAM_GF_EMP_MAPEAMENTO     ."FEmpenhoEmpenhoEmissao.class.php"             );
    include_once ( CAM_GF_EMP_MAPEAMENTO     ."FEmpenhoEmpenhoEmissaoTCEMS.class.php"        );
    include_once ( CAM_GF_CONT_MAPEAMENTO    ."TContabilidadeEmpenhamento.class.php"         );
    include_once ( CAM_GF_CONT_MAPEAMENTO    ."TContabilidadeLancamentoEmpenho.class.php"    );
    $obTContabilidadeLancamentoEmpenho    =  new TContabilidadeLancamentoEmpenho;
    $obTContabilidadeEmpenhamento         =  new TContabilidadeEmpenhamento;
    $obTEmpenhoEmpenho                    =  new TEmpenhoEmpenho;

    if (Sessao::getExercicio() > '2012') {
        $obFEmpenhoEmpenhoEmissao             =  new FEmpenhoEmpenhoEmissaoTCEMS;
    } else {
        $obFEmpenhoEmpenhoEmissao             =  new FEmpenhoEmpenhoEmissao;
    }

    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if ( $this->obREmpenhoTipoEmpenho->getCodTipo() == 0 ) {
            $obErro->setDescricao( "Campo Tipo de Empenho inválido!" );
        }
        if ( !$obErro->ocorreu() ) {
            list( $dia,$mes,$ano )    = explode( '/', $this->stDtVencimento );
            list( $diaE,$mesE,$anoE ) = explode( '/', $this->stDtEmpenho );
            $intEmpenhoExercicio      = Sessao::getExercicio()."1231";
            if ("$anoE$mesE$diaE" <= "$ano$mes$dia") {
              if ("$ano$mes$dia" > $intEmpenhoExercicio) {
                  $obErro->setDescricao( "A data de vencimento deve ser inferior ou igual à 31/12/".Sessao::getExercicio());
              } else {
                $obErro = $this->verificaPreEmpenho( $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    $obErro = $this->buscaProximoCod( $boTransacao );
                    $obTEmpenhoEmpenho->setDado( "cod_entidade"       , $this->obROrcamentoEntidade->getCodigoEntidade() );
                    $obTEmpenhoEmpenho->setDado( "exercicio"          , $this->stExercicio       );
                    if ( !$obErro->ocorreu() ) {
                        if($this->getTipoEmissao() == 'E'){
                            $this->obROrcamentoDespesa->obTPeriodo->setTDataFinal( $this->stDtEmpenho );
                            $obErro = $this->obROrcamentoDespesa->consultarValorReservaDotacaoPeriodo( $nuVlReserva, $boTransacao );
                        }
                        else
                            $obErro = $this->obROrcamentoDespesa->consultarValorReservaDotacao( $nuVlReserva, $boTransacao );

                        if ( !$obErro->ocorreu() ) {
                           $obTEmpenhoEmpenho->setDado( "cod_empenho"        , $this->inCodEmpenho      );
                           if ($this->inCodCategoria) {
                           $obTEmpenhoEmpenho->setDado( "cod_categoria"      , $this->inCodCategoria    );
                           }
                           $obTEmpenhoEmpenho->setDado( "cod_pre_empenho"    , $this->inCodPreEmpenho   );
                           $obTEmpenhoEmpenho->setDado( "dt_empenho"         , $this->stDtEmpenho       );
                           $obTEmpenhoEmpenho->setDado( "dt_vencimento"      , $this->stDtVencimento    );
                           $obTEmpenhoEmpenho->setDado( "vl_saldo_anterior"  , ($this->nuVlSaldoAnterior + $nuVlReserva) );
                           $obErro = $obTEmpenhoEmpenho->inclusao( $boTransacao );
                        }
                    }
                }
             }
            } else {
                $obErro->setDescricao( "A data de vencimento deve ser posterior ou igual à data do empenho." );
            }
        }

        if ( !$obErro->ocorreu() ) {
            $nuVlReserva = str_replace('.','',$this->obROrcamentoReservaSaldos->getVlReserva() );
            $nuVlReserva = str_replace(',','.',$nuVlReserva );
            $this->obROrcamentoReservaSaldos->setVlReserva( $nuVlReserva );
            $obFEmpenhoEmpenhoEmissao->setDado( "exercicio"       , $this->stExercicio                                       );
            $obFEmpenhoEmpenhoEmissao->setDado( "valor"           , $this->obROrcamentoReservaSaldos->getVlReserva()         );
            $obFEmpenhoEmpenhoEmissao->setDado( "complemento"     , $this->inCodEmpenho."/".$this->stExercicio               );
            $obFEmpenhoEmpenhoEmissao->setDado( "nom_lote"        , "Emissão de Empenho n° ".$this->inCodEmpenho."/".$this->stExercicio );
            $obFEmpenhoEmpenhoEmissao->setDado( "dt_lote"         , $this->stDtEmpenho                                       );
            $obFEmpenhoEmpenhoEmissao->setDado( "tipo_lote"       , 'E'                                                      );
            $obFEmpenhoEmpenhoEmissao->setDado( "cod_entidade"    , $this->obROrcamentoEntidade->getCodigoEntidade()         );
            $obFEmpenhoEmpenhoEmissao->setDado( "cod_pre_empenho" , $this->inCodPreEmpenho                                   );
            //como fazer para passar o cod_despesa somente para o tcems com exercicio acima de 2012
            if (Sessao::getExercicio() > '2012') {
                $obFEmpenhoEmpenhoEmissao->setDado( "cod_despesa", $this->obROrcamentoDespesa->getCodDespesa() );
                $obFEmpenhoEmpenhoEmissao->setDado( "cod_class_despesa", $this->obROrcamentoClassificacaoDespesa->getMascClassificacao() );
            }
            $obErro = $obFEmpenhoEmpenhoEmissao->executaFuncao( $rsRecordSet , $boTransacao );
        }
        if ( !$obErro->ocorreu() ) {
            $obTContabilidadeLancamentoEmpenho->setDado( 'cod_lote'    , $obFEmpenhoEmpenhoEmissao->getDado( 'cod_lote' )  );
            $obTContabilidadeLancamentoEmpenho->setDado( 'tipo'        , 'E'                                                     );
            $obTContabilidadeLancamentoEmpenho->setDado( 'sequencia'   , $obFEmpenhoEmpenhoEmissao->getDado( 'sequencia' ) );
            $obTContabilidadeLancamentoEmpenho->setDado( 'exercicio'   , $this->stExercicio                                      );
            $obTContabilidadeLancamentoEmpenho->setDado( 'cod_entidade', $this->obROrcamentoEntidade->getCodigoEntidade()        );
            $obTContabilidadeLancamentoEmpenho->setDado( 'estorno'     , false                                                   );
            $obErro = $obTContabilidadeLancamentoEmpenho->inclusao( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $obTContabilidadeEmpenhamento->setDado( 'exercicio'         , $this->stExercicio                                      );
                $obTContabilidadeEmpenhamento->setDado( 'exercicio_empenho' , $this->stExercicio                                      );
                $obTContabilidadeEmpenhamento->setDado( 'sequencia'         , $obFEmpenhoEmpenhoEmissao->getDado( 'sequencia' ) );
                $obTContabilidadeEmpenhamento->setDado( 'tipo'              , 'E'                                                     );
                $obTContabilidadeEmpenhamento->setDado( 'cod_lote'          , $obFEmpenhoEmpenhoEmissao->getDado( 'cod_lote' )  );
                $obTContabilidadeEmpenhamento->setDado( 'cod_entidade'      , $this->obROrcamentoEntidade->getCodigoEntidade()        );
                $obTContabilidadeEmpenhamento->setDado( 'cod_empenho'       , $this->inCodEmpenho                                     );
                $obErro = $obTContabilidadeEmpenhamento->inclusao( $boTransacao );
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTEmpenhoEmpenho );

    return $obErro;
}

/**
    * @access Public
    * @param Object $obTransacao
    * @return Object Objeto Erro
*/
function emitirEmpenhoDespesaFixa($boTransacao = "")
{
    include_once( CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenho.class.php" );
    include_once( CAM_GF_EMP_MAPEAMENTO."TEmpenhoDespesasFixas.class.php" );

    $obTEmpenhoEmpenho = new TEmpenhoEmpenho;
    $obTEmpenhoEmpenhoDespesasFixas = new TEmpenhoDespesasFixas;

    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $mes = substr($this->getDtEmpenho(),3,2);
        $obTEmpenhoEmpenhoDespesasFixas->setDado('stExercicio',$this->getExercicio());
        $obTEmpenhoEmpenhoDespesasFixas->setDado('inCodEntidade',$this->obROrcamentoEntidade->getCodigoEntidade());
        $obTEmpenhoEmpenhoDespesasFixas->setDado('inCodDespesa',$this->obROrcamentoDespesa->getCodDespesa());
        $obTEmpenhoEmpenhoDespesasFixas->setDado('inCodDespesaFixa',$this->getCodDespesaFixa());
        $obTEmpenhoEmpenhoDespesasFixas->setDado('stMes',$mes);
        $obErro =$obTEmpenhoEmpenhoDespesasFixas->verificaExistenciaDespesaFixaMes($rsRetorno, '', $boTransacao);
        if (!$obErro->ocorreu()) {
            if ($rsRetorno->getCampo('empenho_mes_atual') =='true') {
                $obErro->setDescricao(" Já foi emitido Empenho para esta Despesa Fixa no mês atual.");
            }
            if (!$obErro->ocorreu()) {
                $obErro = $this->listarMaiorData( $rsMaiorData, '', $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    $stMaiorData = $rsMaiorData->getCampo('dataempenho');

                    if (SistemaLegado::comparaDatas($rsMaiorData->getCampo('dataempenho'),$this->getDtEmpenho())) {
                        $obErro->setDescricao(" Data de Empenho deve ser maior que '".$rsMaiorData->getCampo('dataempenho')."'!'");
                    }

                    if (SistemaLegado::comparaDatas($this->getDtEmpenho(),date('d/m/Y'))) {
                        $obErro->setDescricao(" Data de Empenho deve ser menor ou igual a data atual!" );
                    }

                    if ( !$obErro->ocorreu() ) {
                        $obErro = $this->consultaSaldoAnterior( $nuSaldoAnterior, '', $boTransacao );
                        $nuVlReserva = str_replace( '.','',$this->obROrcamentoReservaSaldos->getVlReserva());
                        $nuVlReserva = str_replace( ',','.',$nuVlReserva);
                        $this->setVlSaldoAnterior( $nuSaldoAnterior );
                        if ( $this->getVlSaldoAnterior() < $nuVlReserva ) {
                            $obErro->setDescricao( "Valor da reserva é superior ao Saldo Anterior." );
                        }
                        if ( !$obErro->ocorreu() ) {
                            $obErro = $this->checarFormaExecucaoOrcamento( $boDetalhadoExecucao, $boTransacao );
                        }
                        if ( !$obErro->ocorreu() ) {
                            if ( $boDetalhadoExecucao and !$obErro->ocorreu() ) {
                                if( $this->obROrcamentoDespesa->getCodDespesa() and !$this->obROrcamentoClassificacaoDespesa->getMascClassificacao() )
                                    $obErro->setDescricao( "Campo Desdobramento inválido!()" );
                            }
                        }
                        if ( !$obErro->ocorreu() ) {
                            $stTimestampAtributo = substr($this->getDtEmpenho(),6,4). "-" . substr($this->getDtEmpenho(),3,2). "-" . substr($this->getDtEmpenho(),0,2) . date(" H:i:s.") . str_pad(1, 3, "0", STR_PAD_LEFT);
                            $this->setTimestampAtributo( $stTimestampAtributo );

                            $obErro = parent::incluirItemEmpenhoDespesaFixa( $boTransacao );
                            if ( !$obErro->ocorreu() ) {
                                $obErro = $this->incluir( $boTransacao );
                            }
                        }
                    }
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTEmpenhoEmpenho );

    return $obErro;
}

/**
    * @access Public
    * @param Object $boTransacao
    * @return Object Objeto Erro
*/
function emitirEmpenhoDiverso($boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO     ."TEmpenhoEmpenho.class.php"                    );
    $obTEmpenhoEmpenho                    =  new TEmpenhoEmpenho;

    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if ( !$obErro->ocorreu() ) {

        $obErro = $this->listarMaiorData( $rsMaiorData,'',$boTransacao );
        if (!$obErro->ocorreu()) {
            $stMaiorData = $rsMaiorData->getCampo( "dataempenho" );

            if (SistemaLegado::comparaDatas($rsMaiorData->getCampo( "dataempenho" ),$this->getDtEmpenho())) {
                $obErro->setDescricao( "Data de Empenho deve ser maior que '".$rsMaiorData->getCampo( "dataempenho" )."'!" );
            }

            if (SistemaLegado::comparaDatas($this->getDtEmpenho(),date('d/m/Y'))) {
                 $obErro->setDescricao( "Data de Empenho deve ser menor ou igual a data atual!" );
            }
            if (!$obErro->ocorreu() && ($this->getCodCategoria() == 2 || $this->getCodCategoria() == 3)) {

                include_once( TEMP."TEmpenhoResponsavelAdiantamento.class.php" );

                $obTEmpenhoResponsavel = new TEmpenhoResponsavelAdiantamento;

                $stFiltro  = " AND responsavel_adiantamento.numcgm ='".$this->obRCGM->getNumCGM()."'";
                $stFiltro .= " AND responsavel_adiantamento.exercicio ='".$this->getExercicio()."'";
                $obErro = $obTEmpenhoResponsavel->recuperaResponsavelAdiantamento($rsResponsavel,$stFiltro, $boTransacao);

                if ( !$obErro->ocorreu() ) {

                    if ($rsResponsavel->getNumLinhas() < 0) {
                        $obErro->setDescricao( "Responsável por adiantamento não cadastrado!" );
                    }
                }
            }

            if ( !$obErro->ocorreu() ) {
                
                $this->setdataEmpenho($this->getDtEmpenho());
                $this->setCodEntidade($this->obROrcamentoEntidade->getCodigoEntidade());
                
                $obErro = $this->consultaSaldoAnteriorDataEmpenho( $nuSaldoAnterior, '', $boTransacao );
                
				$nuVlReserva = str_replace( '.','',$this->obROrcamentoReservaSaldos->getVlReserva());
                $nuVlReserva = str_replace( ',','.',$nuVlReserva );
                $this->setVlSaldoAnterior( $nuSaldoAnterior );
                
                if ( $this->getVlSaldoAnterior() < $nuVlReserva ) {
                    $obErro->setDescricao( "Valor da reserva é superior ao Saldo Anterior" );
                }
                if ( !$obErro->ocorreu() ) {
                    $obErro = $this->checarFormaExecucaoOrcamento( $boDetalhadoExecucao , $boTransacao );
                }
                if ( !$obErro->ocorreu() ) {
                    if ( $boDetalhadoExecucao and !$obErro->ocorreu() ) {
                        if( $this->obROrcamentoDespesa->getCodDespesa() and !$this->obROrcamentoClassificacaoDespesa->getMascClassificacao() )
                            $obErro->setDescricao( "Campo Desdobramento inválido!()" );
                    }
                }

                if ( !$obErro->ocorreu() ) {
                    $stTimestampAtributo = substr($this->getDtEmpenho(),6,4). "-" . substr($this->getDtEmpenho(),3,2). "-" . substr($this->getDtEmpenho(),0,2)  . date (" H:i:s.") . str_pad(1, 3, "0", STR_PAD_LEFT);
                    $this->setTimestampAtributo( $stTimestampAtributo );

                    $obErro = parent::incluir( $boTransacao );
                    if ( !$obErro->ocorreu() ) {
                        $obErro = $this->incluir( $boTransacao );
                    }
                }
            }
        }
    }

    $obTEmpenhoEmpenho->setDado( "cod_entidade"       , $this->obROrcamentoEntidade->getCodigoEntidade() );
    $obTEmpenhoEmpenho->setDado( "exercicio"          , $this->stExercicio       );
    $obTEmpenhoEmpenho->setDado( "cod_empenho"        , $this->inCodEmpenho      );

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTEmpenhoEmpenho );
    return $obErro;
}

/**
    * Anula Empenho
    * @access Public
    * @param Object $boTransacao
    * @return Object Objeto Erro
*/
function anular($boTransacao = "")
{
    include_once CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenho.class.php";
    include_once CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenhoAnulado.class.php";
    include_once CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenhoAnuladoItem.class.php";
    include_once CAM_GF_EMP_MAPEAMENTO."FEmpenhoEmpenhoEmissaoAnulacao.class.php";
    include_once CAM_GF_EMP_MAPEAMENTO."FEmpenhoEmpenhoEmissaoAnulacaoTCEMS.class.php";
    include_once CAM_GF_EMP_MAPEAMENTO."FEmpenhoEmpenhoEstornoRestosAPagar.class.php";
    include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeEmpenhamento.class.php";
    include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeLancamentoEmpenho.class.php";
    include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeLancamentoEmpenhoAnulado.class.php";

    $obTContabilidadeLancamentoEmpenhoAnulado = new TContabilidadeLancamentoEmpenhoAnulado;
    $obTContabilidadeLancamentoEmpenho        =  new TContabilidadeLancamentoEmpenho;
    $obTContabilidadeEmpenhamento             =  new TContabilidadeEmpenhamento;
    $obFEmpenhoEmpenhoEstornoRestosAPagar     =  new FEmpenhoEmpenhoEstornoRestosAPagar;
    $obTEmpenhoEmpenhoAnuladoItem             =  new TEmpenhoEmpenhoAnuladoItem;
    $obTEmpenhoEmpenhoAnulado                 =  new TEmpenhoEmpenhoAnulado;
    $obTEmpenhoEmpenho                        =  new TEmpenhoEmpenho;

    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if (Sessao::getExercicio() > '2012')
        $obFEmpenhoEmpenhoEmissaoAnulacao =  new FEmpenhoEmpenhoEmissaoAnulacaoTCEMS;
    else
        $obFEmpenhoEmpenhoEmissaoAnulacao =  new FEmpenhoEmpenhoEmissaoAnulacao;

    if ( !$obErro->ocorreu() ) {
        if ( !$obErro->ocorreu() ) {
            if (SistemaLegado::comparaDatas($this->getDtEmpenho(),$this->getDtAnulacao())) {
                $obErro->setDescricao( "A data da anulação deve ser posterior ou igual à data do empenho." );
            }
            if (SistemaLegado::comparaDatas($this->getDtEmpenho(),date('d/m/Y'))) {
                 $obErro->setDescricao( "Data de Empenho deve ser menor ou igual a data atual!" );
            }
            if ( !$obErro->ocorreu() ) {
                if ( count($this->arItemPreEmpenho ) ) {
                    $milisegundos = 1;
                    $stDataAnulacao = substr($this->stDtAnulacao,6,4) . "-" . substr($this->stDtAnulacao,3,2). "-" . substr($this->stDtAnulacao,0,2)  . date (" H:i:s.") . str_pad($milisegundos, 3, "0", STR_PAD_LEFT);
                    $boLiberado = false;
                    while (!$boLiberado) {
                        $this->recuperaTimestampAnulado($rsTimestampAnulado, " timestamp = '$stDataAnulacao' ", $boTransacao );
                        if ($rsTimestampAnulado->getCampo('timestampAnulado')) {
                            $milisegundos++;
                            if($milisegundos > 999)
                                $milisegundos = 1;
                            $stDataAnulacao = substr($this->stDtAnulacao,6,4) . "-" . substr($this->stDtAnulacao,3,2). "-" . substr($this->stDtAnulacao,0,2)  . date (" H:i:s.") . str_pad($milisegundos, 3, "0", STR_PAD_LEFT);
                        } else {
                            $boLiberado = true;
                        }
                    }

                    $obTEmpenhoEmpenhoAnulado->setDado( "exercicio"   , $this->stExercicio                               );
                    $obTEmpenhoEmpenhoAnulado->setDado( "cod_entidade", $this->obROrcamentoEntidade->getCodigoEntidade() );
                    $obTEmpenhoEmpenhoAnulado->setDado( "cod_empenho" , $this->inCodEmpenho                              );
                    $obTEmpenhoEmpenhoAnulado->setDado( "timestamp"   , $stDataAnulacao                     );
                    $obTEmpenhoEmpenhoAnulado->setDado( "motivo"      , $this->stMotivo                                  );
                    $obErro = $obTEmpenhoEmpenhoAnulado->inclusao( $boTransacao );

                    if ( !$obErro->ocorreu() ) {
                        $vlTotalAnulado = 0;
                        foreach ($this->arItemPreEmpenho as $obItemPreEmpenho) {
                            $obErro = $this->validarValorItem( $obItemPreEmpenho->getNumItem(), $boTransacao );

                            if ( !$obErro->ocorreu() ) {
                                $obTEmpenhoEmpenhoAnuladoItem->setDado( "num_item"        ,$obItemPreEmpenho->getNumItem() );
                                $obTEmpenhoEmpenhoAnuladoItem->setDado( "exercicio"       ,$this->stExercicio              );
                                $obTEmpenhoEmpenhoAnuladoItem->setDado( "cod_pre_empenho" ,$this->inCodPreEmpenho          );
                                $obTEmpenhoEmpenhoAnuladoItem->setDado( "cod_empenho"     ,$this->inCodEmpenho             );
                                $obTEmpenhoEmpenhoAnuladoItem->setDado( "cod_entidade"    ,$this->obROrcamentoEntidade->getCodigoEntidade() );
                                $obTEmpenhoEmpenhoAnuladoItem->setDado( "timestamp"       ,$stDataAnulacao                     );
                                $obTEmpenhoEmpenhoAnuladoItem->setDado( "vl_anulado"      ,$obItemPreEmpenho->getValorEmpenhadoAnulado()    );
                                $vlTotalAnulado += $obItemPreEmpenho->getValorEmpenhadoAnulado();
                                $obErro = $obTEmpenhoEmpenhoAnuladoItem->inclusao( $boTransacao );
                                if ( $obErro->ocorreu() ) {
                                    break;
                                }
                            } else {
                                break;
                            }
                        }
                    }
                    if ( !$obErro->ocorreu() ) {
                        if ($this->stExercicio == $this->stExercicioEmissao) {
                            $obFEmpenhoEmpenhoEmissaoAnulacao->setDado( "exercicio"       , $this->stExercicioEmissao                                );
                            $obFEmpenhoEmpenhoEmissaoAnulacao->setDado( "valor"           , $vlTotalAnulado                                          );
                            $obFEmpenhoEmpenhoEmissaoAnulacao->setDado( "complemento"     , $this->inCodEmpenho."/".$this->stExercicio               );
                            $obFEmpenhoEmpenhoEmissaoAnulacao->setDado( "nom_lote"        , "Anulação de Empenho n° ".$this->inCodEmpenho."/".$this->stExercicio );
                            $obFEmpenhoEmpenhoEmissaoAnulacao->setDado( "dt_lote"         , $this->getDtAnulacao()                                   );
                            $obFEmpenhoEmpenhoEmissaoAnulacao->setDado( "tipo_lote"       , 'E'                                                      );
                            $obFEmpenhoEmpenhoEmissaoAnulacao->setDado( "cod_entidade"    , $this->obROrcamentoEntidade->getCodigoEntidade()         );
                            $obFEmpenhoEmpenhoEmissaoAnulacao->setDado( "cod_pre_empenho" , $this->inCodPreEmpenho                                   );
                            if (Sessao::getExercicio() > '2012') {
                                $obFEmpenhoEmpenhoEmissaoAnulacao->setDado( "cod_despesa" , $this->obROrcamentoDespesa->getCodDespesa() );
                                $obFEmpenhoEmpenhoEmissaoAnulacao->setDado( "cod_class_despesa", $this->obROrcamentoClassificacaoDespesa->getMascClassificacao() );
                            }
                            $obErro = $obFEmpenhoEmpenhoEmissaoAnulacao->executaFuncao( $rsRecordSet , $boTransacao );
                            $inCodLote   = $obFEmpenhoEmpenhoEmissaoAnulacao->getDado( 'cod_lote' );
                            $inSequencia = $obFEmpenhoEmpenhoEmissaoAnulacao->getDado( 'sequencia' );
                        } else {
                            $arChaveAtributo =  array( "cod_pre_empenho" =>$this->inCodPreEmpenho, "exercicio" =>$this->stExercicio );
                            $this->obREmpenhoAutorizacaoEmpenho->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributo );
                            $obErro = $this->obREmpenhoAutorizacaoEmpenho->obRCadastroDinamico->salvarValores( $boTransacao );

                            if ( !$obErro->ocorreu() ) {
                                $obFEmpenhoEmpenhoEstornoRestosAPagar->setDado( 'exercicio'       , $this->stExercicioEmissao                         );
                                $obFEmpenhoEmpenhoEstornoRestosAPagar->setDado( 'valor'           , $vlTotalAnulado                                   );
                                $obFEmpenhoEmpenhoEstornoRestosAPagar->setDado( 'complemento'     , $this->inCodEmpenho."/".$this->stExercicio        );
                                $obFEmpenhoEmpenhoEstornoRestosAPagar->setDado( 'nom_lote'        , "Anulação de Empenho RP n° ".$this->inCodEmpenho."/".$this->stExercicio );
                                $obFEmpenhoEmpenhoEstornoRestosAPagar->setDado( "dt_lote"         , $this->getDtAnulacao()                            );
                                $obFEmpenhoEmpenhoEstornoRestosAPagar->setDado( 'tipo_lote'       , 'E'                                               );
                                $obFEmpenhoEmpenhoEstornoRestosAPagar->setDado( 'cod_entidade'    , $this->obROrcamentoEntidade->getCodigoEntidade()  );
                                $obFEmpenhoEmpenhoEstornoRestosAPagar->setDado( 'cod_pre_empenho' , $this->inCodPreEmpenho                            );
                                $obFEmpenhoEmpenhoEstornoRestosAPagar->setDado( 'exerc_rp'        , $this->stExercicio                                );
                                $obErro = $obFEmpenhoEmpenhoEstornoRestosAPagar->executaFuncao( $rsRecordSet, $boTransacao );
                                $inCodLote   = $obFEmpenhoEmpenhoEstornoRestosAPagar->getDado( 'cod_lote' );
                                $inSequencia = $obFEmpenhoEmpenhoEstornoRestosAPagar->getDado( 'sequencia' );
                            }
                        }
                    }

                    if ( !$obErro->ocorreu() ) {
                        $obTContabilidadeLancamentoEmpenho->setDado( 'cod_lote'    , $inCodLote  );
                        $obTContabilidadeLancamentoEmpenho->setDado( 'tipo'        , 'E'                                                     );
                        $obTContabilidadeLancamentoEmpenho->setDado( 'sequencia'   , $inSequencia );
                        $obTContabilidadeLancamentoEmpenho->setDado( 'exercicio'   , $this->stExercicioEmissao                               );
                        $obTContabilidadeLancamentoEmpenho->setDado( 'cod_entidade', $this->obROrcamentoEntidade->getCodigoEntidade()        );
                        $obTContabilidadeLancamentoEmpenho->setDado( 'estorno'     , true                                                    );
                        $obErro = $obTContabilidadeLancamentoEmpenho->inclusao( $boTransacao );
                        if ( !$obErro->ocorreu() ) {
                            $obTContabilidadeEmpenhamento->setDado( 'exercicio'   , $this->stExercicioEmissao                               );
                            $obTContabilidadeEmpenhamento->setDado( 'exercicio_empenho' , $this->stExercicio                                      );
                            $obTContabilidadeEmpenhamento->setDado( 'sequencia'   , $inSequencia  );
                            $obTContabilidadeEmpenhamento->setDado( 'tipo'        , 'E'                                                     );
                            $obTContabilidadeEmpenhamento->setDado( 'cod_lote'    , $inCodLote  );
                            $obTContabilidadeEmpenhamento->setDado( 'cod_entidade', $this->obROrcamentoEntidade->getCodigoEntidade()        );
                            $obTContabilidadeEmpenhamento->setDado( 'cod_empenho' , $this->inCodEmpenho                                     );
                            $obErro = $obTContabilidadeEmpenhamento->inclusao( $boTransacao );
                        }
                    }

                    if ( !$obErro->ocorreu() ) {
                        $obTContabilidadeLancamentoEmpenhoAnulado->setDado( 'exercicio'            , $this->stExercicioEmissao );
                        $obTContabilidadeLancamentoEmpenhoAnulado->setDado( 'cod_lote'             , $inCodLote );
                        $obTContabilidadeLancamentoEmpenhoAnulado->setDado( 'tipo'                 , 'E' );
                        $obTContabilidadeLancamentoEmpenhoAnulado->setDado( 'sequencia'            , $inSequencia );
                        $obTContabilidadeLancamentoEmpenhoAnulado->setDado( 'cod_entidade'         , $this->obROrcamentoEntidade->getCodigoEntidade() );
                        $obTContabilidadeLancamentoEmpenhoAnulado->setDado( 'exercicio_anulacao'   , $this->stExercicio );
                        $obTContabilidadeLancamentoEmpenhoAnulado->setDado( 'cod_empenho_anulacao' , $this->inCodEmpenho );
                        $obTContabilidadeLancamentoEmpenhoAnulado->setDado( 'timestamp_anulacao'   , $stDataAnulacao );
                        $obErro = $obTContabilidadeLancamentoEmpenhoAnulado->inclusao( $boTransacao );
                    }

                    if ( !$obErro->ocorreu() ) {
                        $this->obREmpenhoAutorizacaoEmpenho->setCodPreEmpenho($this->inCodPreEmpenho );
                        $this->obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->setCodigoEntidade($this->obROrcamentoEntidade->getCodigoEntidade() );
                        $this->obREmpenhoAutorizacaoEmpenho->setExercicio($this->stExercicio) ;
                        $obErro = $this->obREmpenhoAutorizacaoEmpenho->listarPorPreEmpenho($rsAutorizacao, "", $boTransacao);

                        if ( !$obErro->ocorreu() ) {
                            if ($rsAutorizacao->getCampo( "cod_autorizacao" )>0) {
                                $this->obREmpenhoConfiguracao->consultar($boTransacao);

                                $this->obREmpenhoAutorizacaoEmpenho->setExercicio($rsAutorizacao->getCampo( "exercicio" )) ;
                                $this->obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->setCodigoEntidade($rsAutorizacao->getCampo( "cod_entidade" ));
                                $this->obREmpenhoAutorizacaoEmpenho->setCodPreEmpenho($rsAutorizacao->getCampo( "cod_pre_empenho" ));
                                $this->obREmpenhoAutorizacaoEmpenho->setCodAutorizacao($rsAutorizacao->getCampo( "cod_autorizacao" ));
                                $this->obREmpenhoAutorizacaoEmpenho->consultar($boTransacao);

                                if (!$this->obREmpenhoAutorizacaoEmpenho->getDtAnulacao()) {
                                    $this->obREmpenhoAutorizacaoEmpenho->setDtAnulacao($this->getDtEmpenho());
                                    $this->obREmpenhoAutorizacaoEmpenho->setMotivoAnulacao("Anulada a partir do empenho ".$this->inCodEmpenho."/".$this->stExercicio);
                                    echo '<br>transacao? ' . $boTransacao;
                                    echo '<br>flag '. $boFlagTransacao;

                                    $obErro = $this->obREmpenhoAutorizacaoEmpenho->anular($boTransacao);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    $this->setDtAnulacao( $stDataAnulacao );
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTEmpenhoEmpenhoAnulado );

    return $obErro;
}

/**
    * Método para gerar proximo codigo apartir da configuração
    * @access Private
    * @param Object $boTransacao
    * @return Object $obErro
*/
function buscaProximoCod($boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO     ."TEmpenhoEmpenho.class.php"                    );
    $obTEmpenhoEmpenho                    =  new TEmpenhoEmpenho;

    $obREmpenhoConfiguracao = new REmpenhoConfiguracao;
    $obErro = $obREmpenhoConfiguracao->consultar( $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if ( $obREmpenhoConfiguracao->getNumeracao() == 'P' ) {
            $obTEmpenhoEmpenho->setComplementoChave( "cod_entidade,exercicio" );
            $obTEmpenhoEmpenho->setDado( "cod_entidade" , $this->obROrcamentoEntidade->getCodigoEntidade() );
        } else {
            $obTEmpenhoEmpenho->setComplementoChave( "exercicio" );
        }
        $obTEmpenhoEmpenho->setDado( "exercicio" , $this->stExercicio );
        $obErro = $obTEmpenhoEmpenho->proximoCod( $this->inCodEmpenho, $boTransacao );
    }

    return $obErro;
}

/**
    * Executa um recuperaTodos na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function recuperaTimestampAnulado(&$rsRecordSet, $stFiltro, $boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO     ."TEmpenhoEmpenho.class.php"                    );
    $obTEmpenhoEmpenho                    =  new TEmpenhoEmpenho;

    $stFiltro = " WHERE " . $stFiltro;
    $obErro = $obTEmpenhoEmpenho->recuperaTimestampAnulado( $rsRecordSet, $stFiltro, $boTransacao );

    return $obErro;
}

function recuperaUltimoEmpenho(&$rsRecordSet, $boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenho.class.php" );
    $obTEmpenhoEmpenho = new TEmpenhoEmpenho;

    if($this->obROrcamentoEntidade->getCodigoEntidade())
        $stFiltro = "  AND empenho.cod_entidade = ".$this->obROrcamentoEntidade->getCodigoEntidade()." \n";
    $stFiltro.= " AND empenho.exercicio = '".Sessao::getExercicio()."'                              \n";

    $stOrdem  = " ORDER BY empenho.dt_empenho DESC LIMIT 1                                             \n";

    $obErro = $obTEmpenhoEmpenho->recuperaUltimaDataEmpenho( $rsRecordSet,$stFiltro,$stOrdem );

    return $obErro;
}

/**
    * Executa um recuperaTodos na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarAdiantamentoSubvencao(&$rsRecordSet, $boTransacao = "")
{
   include_once( CAM_GF_EMP_MAPEAMENTO     ."TEmpenhoEmpenho.class.php" );
   $obTEmpenhoEmpenho =  new TEmpenhoEmpenho();

   $obTEmpenhoEmpenho->setDado( "numcgm"   , $this->obRUsuario->obRCGM->getNumCGM() );
   $obTEmpenhoEmpenho->setDado( "exercicio", $this->stExercicio                     );
   if (empty($stFiltro)) {
      $stFiltro = null;
   }
   if($this->stExercicio )
      $stFiltro.= " AND tabela.exercicio = '".$this->stExercicio."'                                      \n";
   if($this->obROrcamentoEntidade->getCodigoEntidade())
      $stFiltro.= " AND tabela.cod_entidade IN (".$this->obROrcamentoEntidade->getCodigoEntidade()." )   \n";
   if($this->inCodEmpenhoInicial )
      $stFiltro.= " AND tabela.cod_empenho >= ".$this->inCodEmpenhoInicial."                             \n";
   if($this->inCodEmpenhoFinal )
      $stFiltro.= " AND tabela.cod_empenho <= ".$this->inCodEmpenhoFinal."                               \n";
   if($this->obRCGM->getNumCGM() )
      $stFiltro.= " AND tabela.credor = ".$this->obRCGM->getNumCGM()."                                   \n";
   if ($this->stDtEmpenhoInicial or $this->stDtEmpenhoFinal) {
      $this->stDtEmpenhoInicial = ( $this->stDtEmpenhoInicial ) ? $this->stDtEmpenhoInicial : '01/01/'.$this->stExercicio;
      $this->stDtEmpenhoFinal   = ( $this->stDtEmpenhoFinal   ) ? $this->stDtEmpenhoFinal   : '31/12/'.$this->stExercicio;
      $stFiltro.= " AND TO_DATE(dt_empenho, 'dd/mm/yyyy') BETWEEN ";
      $stFiltro.= "TO_DATE('".$this->stDtEmpenhoInicial."','dd/mm/yyyy') AND TO_DATE('".$this->stDtEmpenhoFinal."','dd/mm/yyyy')";
  }

   // Consultar Todos Pagos ñ prestados
   if ($this->inSituacao == 1) {
      $stFiltro.= " AND  tabela.vl_prestado > 0.00            \n";
   }

   // Consultar Pagos ñ prestados
   if ($this->inSituacao == 2) {
       $stFiltro.= " AND  tabela.vl_pago-tabela.vl_pago_anulado > 0.00               \n";
       $stFiltro.= " AND  tabela.vl_pago-tabela.vl_pago_anulado > tabela.vl_prestado \n";
   }

   // Consultar Empenhos prestado contas
   if ($this->inSituacao == 3) {
       $stFiltro.= " AND  tabela.vl_pago-tabela.vl_pago_anulado > 0.00               \n";
       $stFiltro.= " AND  tabela.vl_prestado > 0.00\n";
   }

   $stOrder  = "tabela.cod_entidade, tabela.cod_empenho, tabela.nom_fornecedor";
   $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 4, strlen($stFiltro)) : "";
   $stOrder  = ($stOrder) ? $stOrder : "tabela.cod_empenho";
   $obErro = $obTEmpenhoEmpenho->recuperaConsultaAdiantamentoSubvencao( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

   return $obErro;
}

/**
    * Executa um recuperaTodos na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarConsultaEmpenho(&$rsRecordSet, $stOrder = "tabela.cod_entidade, tabela.cod_empenho, tabela.nom_fornecedor", $boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO     ."TEmpenhoEmpenho.class.php"                    );
    $obTEmpenhoEmpenho                    =  new TEmpenhoEmpenho;

    $obTEmpenhoEmpenho->setDado( "numcgm"   , $this->obRUsuario->obRCGM->getNumCGM() );
    $obTEmpenhoEmpenho->setDado( "exercicio", $this->stExercicio );

    if( $this->stExercicio )
        $stFiltro .= " AND tabela.exercicio = '".$this->stExercicio."' ";
    if( $this->obROrcamentoEntidade->getCodigoEntidade() )
        $stFiltro  .= " AND tabela.cod_entidade IN (".$this->obROrcamentoEntidade->getCodigoEntidade()." ) ";
    if( $this->obROrcamentoEntidade->obRCGMPessoaFisica->getCPF())
         $stFiltro  .= " AND tabela.credor in ( SELECT numcgm from sw_cgm_pessoa_fisica where cpf = '".$this->obROrcamentoEntidade->obRCGMPessoaFisica->getCPF()."') ";
    if( $this->obROrcamentoEntidade->obRCGMPessoaJuridica->getCNPJ())
         $stFiltro  .= " AND tabela.credor in ( SELECT numcgm from sw_cgm_pessoa_juridica where cnpj = '".$this->obROrcamentoEntidade->obRCGMPessoaJuridica->getCNPJ()."') ";
    if( $this->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->getNumeroOrgao())
        $stFiltro .= " AND tabela.num_orgao = ".$this->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->getNumeroOrgao()." ";
    if( $this->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->getNumeroUnidade())
        $stFiltro .= " AND tabela.num_unidade = ".$this->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->getNumeroUnidade()." ";
    if( $this->inCodDespesa )
        $stFiltro  .= " AND tabela.cod_despesa = ".$this->inCodDespesa." ";
    if( $this->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->getCodEstrutural() )
        $stFiltro  .= " AND tabela.mascara_classificacao like publico.fn_mascarareduzida('".$this->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->getCodEstrutural()."')||'.%' ";
    if( $this->inCodEmpenhoInicial )
        $stFiltro  .= " AND tabela.cod_empenho >= ".$this->inCodEmpenhoInicial." ";
    if( $this->inCodEmpenhoFinal )
        $stFiltro  .= " AND tabela.cod_empenho <= ".$this->inCodEmpenhoFinal." ";
    if( $this->inCodLiquidacaoInicial )
        $stFiltro  .= " AND tabela.cod_nota >= ".$this->inCodLiquidacaoInicial." ";
    if( $this->inCodLiquidacaoFinal )
        $stFiltro  .= " AND tabela.cod_nota <= ".$this->inCodLiquidacaoFinal." ";

    if( $this->obREmpenhoAutorizacaoEmpenho->getCodAutorizacao() )
        $stFiltro  .= " AND tabela.cod_autorizacao = ".$this->obREmpenhoAutorizacaoEmpenho->getCodAutorizacao()." ";
    if( $this->obRCGM->getNumCGM() )
        $stFiltro  .= " AND tabela.credor = ".$this->obRCGM->getNumCGM()." ";
    if( $this->obROrcamentoDespesa->obROrcamentoRecurso->getCodRecurso() )
        $stFiltro  .= " AND tabela.cod_recurso = ".$this->obROrcamentoDespesa->obROrcamentoRecurso->getCodRecurso()." ";
    if( $this->obROrcamentoDespesa->obROrcamentoRecurso->getDestinacaoRecurso() )
        $stFiltro  .= " AND tabela.masc_recurso_red like '".$this->obROrcamentoDespesa->obROrcamentoRecurso->getDestinacaoRecurso()."%' ";
    if( $this->obROrcamentoDespesa->obROrcamentoRecurso->getCodDetalhamento() )
        $stFiltro  .= " AND tabela.cod_detalhamento = ".$this->obROrcamentoDespesa->obROrcamentoRecurso->getCodDetalhamento()." ";

    if( $this->obREmpenhoHistorico->getCodHistorico() )
        $stFiltro  .= " AND tabela.cod_historico = ".$this->obREmpenhoHistorico->getCodHistorico()." ";
    if ($this->stDtEmpenhoInicial or $this->stDtEmpenhoFinal) {
        $this->stDtEmpenhoInicial = ( $this->stDtEmpenhoInicial ) ? $this->stDtEmpenhoInicial : '01/01/'.$this->stExercicio;
        $this->stDtEmpenhoFinal = ( $this->stDtEmpenhoFinal ) ? $this->stDtEmpenhoFinal : '31/12/'.$this->stExercicio;
        $stFiltro .= " AND  TO_DATE(dt_empenho,'dd/mm/yyyy' ) between ";
        $stFiltro .= "TO_DATE('".$this->stDtEmpenhoInicial."','dd/mm/yyyy') AND TO_DATE('".$this->stDtEmpenhoFinal."','dd/mm/yyyy') ";
  }
    if( $this->inSituacao == 1)
        $stFiltro  .= " AND (tabela.vl_pago - tabela.vl_pago_anulado) <= (tabela.vl_empenhado - tabela.vl_empenhado_anulado) AND (tabela.vl_empenhado - tabela.vl_empenhado_anulado) > 0 AND (tabela.vl_pago - tabela.vl_pago_anulado) <> 0";
    if( $this->inSituacao == 2)
        $stFiltro  .= " AND (( tabela.vl_empenhado - tabela.vl_empenhado_anulado ) - (tabela.vl_pago - tabela.vl_pago_anulado)) > 0 ";
    if( $this->inSituacao == 3)
        $stFiltro  .= " AND ((tabela.vl_empenhado - tabela.vl_empenhado_anulado) - (tabela.vl_liquidado - tabela.vl_liquidado_anulado)) > 0 AND (tabela.vl_liquidado - tabela.vl_liquidado_anulado) <> 0";
    if( $this->inSituacao == 4)
        $stFiltro  .= " AND (tabela.vl_empenhado - tabela.vl_empenhado_anulado) >= 0 AND (tabela.vl_empenhado_anulado) > 0";

    if( $this->inCodPreEmpenho )
        $stFiltro  .= " AND tabela.cod_pre_empenho = ".$this->inCodPreEmpenho." ";
    if( $this->inCodEmpenho )
        $stFiltro  .= " AND tabela.cod_empenho = ".$this->inCodEmpenho." ";
    if ($this->boSomar) {
        $stFiltro .= " AND ( tabela.vl_empenhado -  tabela.vl_empenhado_anulado ) > ( tabela.vl_liquidado - tabela.vl_liquidado_anulado ) ";
    }
    
    if( $this->inCodModalidadeCompra )
        $stFiltro .= " AND tabela.compra_cod_modalidade = ".$this->inCodModalidadeCompra." \n";

    if( $this->inCompraInicial )
        $stFiltro .= " AND tabela.cod_compra_direta >= ".$this->inCompraInicial." \n";
        
    if( $this->inCompraFinal )
        $stFiltro .= " AND tabela.cod_compra_direta <= ".$this->inCompraFinal." \n";
    
    if( $this->inCodModalidadeLicitacao )
        $stFiltro .= " AND tabela.licitacao_cod_modalidade = ".$this->inCodModalidadeLicitacao." \n";

    if( $this->inLicitacaoInicial )
        $stFiltro .= " AND tabela.cod_licitacao >= ".$this->inLicitacaoInicial." \n";
        
    if( $this->inLicitacaoFinal )
        $stFiltro .= " AND tabela.cod_licitacao <= ".$this->inLicitacaoFinal." \n";
    
    //adicionado o filtro por atributos dinamicos
    if ( is_array($this->arAtributosDinamicos) ) {
        foreach ($this->arAtributosDinamicos as $arTemp) {
        $stFiltro .= "
         AND EXISTS ( 	SELECT 1
                            FROM empenho.atributo_empenho_valor
                         WHERE atributo_empenho_valor.exercicio = tabela.exercicio
                           AND atributo_empenho_valor.cod_pre_empenho = tabela.cod_pre_empenho
                           AND atributo_empenho_valor.cod_modulo = ".$arTemp['cod_modulo']."
                           AND atributo_empenho_valor.cod_cadastro = ".$arTemp['cod_cadastro']."
                           AND atributo_empenho_valor.cod_atributo = ".$arTemp['cod_atributo']."
        ";
        if ($arTemp['tipo'] == 'multiplo') {
            $stFiltro .= " AND atributo_empenho_valor.valor IN(".$arTemp['valor'].") ";
        } else {
            $stFiltro .= " AND atributo_empenho_valor.valor = '".$arTemp['valor']."' ";
        }

        $stFiltro .= "
            )
        ";
        }
    }

    $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 4, strlen($stFiltro)) : "";
    $stOrder = ($stOrder) ? $stOrder : "tabela.cod_empenho";
    
    if($this->boEmpenhoCompraLicitacao)
        $obErro = $obTEmpenhoEmpenho->recuperaConsultaEmpenhoCompraLicitacao( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    else
        $obErro = $obTEmpenhoEmpenho->recuperaConsultaEmpenho( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Executa um recuperaTodos na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarMaiorData(&$rsRecordSet, $stOrder = "", $boTransacao = "" , $stDataAutorizacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO     ."TEmpenhoEmpenho.class.php"                    );
    $obTEmpenhoEmpenho                    =  new TEmpenhoEmpenho;
    $stFiltro = "";
    if( $this->obROrcamentoEntidade->getCodigoEntidade() )
        $stFiltro  .= " AND e.cod_entidade IN (".$this->obROrcamentoEntidade->getCodigoEntidade()." ) ";
    if( $this->stExercicio )
        $stFiltro .= " AND e.exercicio = '".$this->stExercicio."' ";

    $obTEmpenhoEmpenho->setDado('stExercicio',$this->stExercicio);
    $obTEmpenhoEmpenho->setDado('stDataAutorizacao',$stDataAutorizacao);

    $obErro = $obTEmpenhoEmpenho->recuperaMaiorDataEmpenho( $rsRecordSet, $stFiltro, $stOrder, $boTransacao , $stDataAutorizacao);

    return $obErro;
}

/**
    * Executa um recuperaTodos na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarMaiorDataAnulacao(&$rsRecordSet, $stOrder = "", $boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO     ."TEmpenhoEmpenho.class.php"                    );
    $obTEmpenhoEmpenho                    =  new TEmpenhoEmpenho;
    $stFiltro = "";
    if( $this->obROrcamentoEntidade->getCodigoEntidade() )
        $stFiltro  .= " AND cod_entidade IN (".$this->obROrcamentoEntidade->getCodigoEntidade()." ) ";
    if( $this->stExercicio )
        $stFiltro .= " AND exercicio = '".$this->stExercicio."' ";

    $obTEmpenhoEmpenho->setDado('stExercicio',$this->stExercicio);
    $obTEmpenhoEmpenho->setDado('stDataEmpenho',$this->stDtEmpenho);

    $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 4, strlen($stFiltro)) : "";
    $obErro = $obTEmpenhoEmpenho->recuperaMaiorDataAnulada( $rsRecordSet, $stFiltro, $stOrder, $boTransacao);

    return $obErro;
}

/**
    * Executa um recuperaTodos na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listar(&$rsRecordSet, $stOrder = "tabela.cod_entidade, tabela.cod_empenho, tabela.nom_fornecedor", $boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO     ."TEmpenhoEmpenho.class.php"                    );
    $obTEmpenhoEmpenho                    =  new TEmpenhoEmpenho;

    $obTEmpenhoEmpenho->setDado( "numcgm"   , $this->obRUsuario->obRCGM->getNumCGM() );
    $obTEmpenhoEmpenho->setDado( "exercicio", $this->stExercicio );

    $stFiltro = '';

    if( $this->stExercicio )
        $stFiltro .= " AND tabela.exercicio = '".$this->stExercicio."' ";
    if( $this->obROrcamentoEntidade->getCodigoEntidade() )
        $stFiltro  .= " AND tabela.cod_entidade IN (".$this->obROrcamentoEntidade->getCodigoEntidade()." ) ";
    if( $this->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->getNumeroOrgao())
        $stFiltro .= " AND tabela.num_orgao = ".$this->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->getNumeroOrgao()." ";
    if( $this->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->getNumeroUnidade())
        $stFiltro .= " AND tabela.num_unidade = ".$this->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->getNumeroUnidade()." ";
    if( $this->inCodDespesa )
        $stFiltro  .= " AND tabela.cod_despesa = ".$this->inCodDespesa." ";
    if( $this->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->getCodEstrutural() )
        $stFiltro  .= " AND tabela.cod_estrutural = '".$this->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->getCodEstrutural()."' ";

    if ($this->stDtVencimento) {
        $stFiltro .= " AND TO_DATE(tabela.dt_vencimento,'dd/mm/yyyy') = TO_DATE('".$this->stDtVencimento."','dd/mm/yyyy') ";
    }

    if ($this->inCodEmpenhoInicial or $this->inCodEmpenhoFinal) {
        if ($this->inCodEmpenhoInicial == $this->inCodEmpenhoFinal) {
                $stFiltro  .= " AND tabela.cod_empenho = ".$this->inCodEmpenhoInicial." ";
        } else {
            if( $this->inCodEmpenhoInicial )
                $stFiltro  .= " AND tabela.cod_empenho >= ".$this->inCodEmpenhoInicial." ";
            if( $this->inCodEmpenhoFinal )
                $stFiltro  .= " AND tabela.cod_empenho <= ".$this->inCodEmpenhoFinal." ";
        }
    }
    if( $this->inCodLiquidacaoInicial )
        $stFiltro  .= " AND tabela.cod_nota >= ".$this->inCodLiquidacaoInicial." ";
    if( $this->inCodLiquidacaoFinal )
        $stFiltro  .= " AND tabela.cod_nota <= ".$this->inCodLiquidacaoFinal." ";

    if( $this->obREmpenhoAutorizacaoEmpenho->getCodAutorizacao() )
        $stFiltro  .= " AND tabela.cod_autorizacao = ".$this->obREmpenhoAutorizacaoEmpenho->getCodAutorizacao()." ";
    if( $this->obRCGM->getNumCGM() )
        $stFiltro  .= " AND tabela.credor = ".$this->obRCGM->getNumCGM()." ";
    if( $this->obROrcamentoDespesa->obROrcamentoRecurso->getCodRecurso() )
        $stFiltro  .= " AND tabela.cod_recurso = ".$this->obROrcamentoDespesa->obROrcamentoRecurso->getCodRecurso()." ";
    if( $this->obREmpenhoHistorico->getCodHistorico() )
        $stFiltro  .= " AND tabela.cod_historico = ".$this->obREmpenhoHistorico->getCodHistorico()." ";
    if ($this->stDtEmpenhoInicial or $this->stDtEmpenhoFinal) {
        if ($this->stDtEmpenhoInicial == $this->stDtEmpenhoFinal) {
            $stFiltro .= " AND  TO_DATE(dt_empenho,'dd/mm/yyyy' ) = TO_DATE('".$this->stDtEmpenhoInicial."','dd/mm/yyyy') ";
        } else {
            $this->stDtEmpenhoInicial = ( $this->stDtEmpenhoInicial ) ? $this->stDtEmpenhoInicial : '01/01/'.$this->stExercicio;
            $this->stDtEmpenhoFinal = ( $this->stDtEmpenhoFinal ) ? $this->stDtEmpenhoFinal : '31/12/'.$this->stExercicio;
            $stFiltro .= " AND  TO_DATE(dt_empenho,'dd/mm/yyyy' ) between ";
            $stFiltro .= "TO_DATE('".$this->stDtEmpenhoInicial."','dd/mm/yyyy') AND TO_DATE('".$this->stDtEmpenhoFinal."','dd/mm/yyyy') ";
        }
    }
    if ( $this->obREmpenhoAutorizacaoEmpenho->getCodAutorizacaoInicial() or $this->obREmpenhoAutorizacaoEmpenho->getCodAutorizacaoFinal()) {
        if ( $this->obREmpenhoAutorizacaoEmpenho->getCodAutorizacaoInicial() == $this->obREmpenhoAutorizacaoEmpenho->getCodAutorizacaoFinal() ) {
            $stFiltro .= " AND  tabela.cod_autorizacao = ".$this->obREmpenhoAutorizacaoEmpenho->getCodAutorizacaoInicial();
        } else {
            if( $this->obREmpenhoAutorizacaoEmpenho->getCodAutorizacaoInicial() )
                $stFiltro .= " AND  tabela.cod_autorizacao >= ".$this->obREmpenhoAutorizacaoEmpenho->getCodAutorizacaoInicial();
            if( $this->obREmpenhoAutorizacaoEmpenho->getCodAutorizacaoFinal() )
                $stFiltro .= " AND  tabela.cod_autorizacao <= ".$this->obREmpenhoAutorizacaoEmpenho->getCodAutorizacaoFinal();
        }
    }

    if( $this->inSituacao == 1)
        $stFiltro  .= " AND tabela.vl_pago > 0 ";
    if( $this->inSituacao == 2)
        $stFiltro  .= " AND (tabela.vl_empenhado - tabela.vl_pago) > 0 ";
    if( $this->inSituacao == 3)
        $stFiltro  .= " AND tabela.vl_liquidado > 0 ";
    if( $this->inSituacao == 4)
        $stFiltro  .= " AND (tabela.vl_empenhado - tabela.vl_empenhado_anulado) = 0 ";
    if( $this->inSituacao == 5)   // Utilizado na emissão do empenho complementar
        $stFiltro  .= " AND (tabela.vl_empenhado - tabela.vl_empenhado_anulado) > 0 ";
    if( $this->inSituacao == 6) // anuladas -- anuladas parcialmente ou totalmente
        $stFiltro  .= " AND (tabela.vl_liquidado - tabela.vl_liquidado_anulado) >= 0 AND (tabela.vl_liquidado_anulado) > 0";
    if( $this->inSituacao == 7) // pagos -- pagamentos parciais ou totais
        $stFiltro  .= " AND ( (tabela.vl_pago - tabela.vl_pago_anulado) <= (tabela.vl_empenhado - tabela.vl_empenhado_anulado) AND (tabela.vl_empenhado - tabela.vl_empenhado_anulado) > 0 AND (tabela.vl_pago - tabela.vl_pago_anulado) <> 0 )";
    if( $this->inSituacao == 8) // válidas -- não anuladas totalmente
        $stFiltro  .= " AND (( tabela.vl_liquidado - tabela.vl_liquidado_anulado ) > 0 OR ( vl_liquidado ) = 0)";

    if( $this->inCodPreEmpenho )
        $stFiltro  .= " AND tabela.cod_pre_empenho = ".$this->inCodPreEmpenho." ";
    if( $this->inCodEmpenho )
        $stFiltro  .= " AND tabela.cod_empenho = ".$this->inCodEmpenho." ";
    if ($this->boSomar) {
        $stFiltro .= " AND ( tabela.vl_empenhado -  tabela.vl_empenhado_anulado ) > ( tabela.vl_liquidado - tabela.vl_liquidado_anulado ) ";
    }
    
    if( $this->inCodModalidadeCompra )
        $stFiltro .= " AND tabela.compra_cod_modalidade = ".$this->inCodModalidadeCompra." \n";

    if( $this->inCompraInicial )
        $stFiltro .= " AND tabela.cod_compra_direta >= ".$this->inCompraInicial." \n";
        
    if( $this->inCompraFinal )
        $stFiltro .= " AND tabela.cod_compra_direta <= ".$this->inCompraFinal." \n";
    
    if( $this->inCodModalidadeLicitacao )
        $stFiltro .= " AND tabela.licitacao_cod_modalidade = ".$this->inCodModalidadeLicitacao." \n";

    if( $this->inLicitacaoInicial )
        $stFiltro .= " AND tabela.cod_licitacao >= ".$this->inLicitacaoInicial." \n";
        
    if( $this->inLicitacaoFinal )
        $stFiltro .= " AND tabela.cod_licitacao <= ".$this->inLicitacaoFinal." \n";
    
    $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 4, strlen($stFiltro)) : "";
    $stOrder  = ($stOrder) ? $stOrder : "tabela.cod_empenho";
    
    if($this->boEmpenhoCompraLicitacao)
        $obErro   = $obTEmpenhoEmpenho->recuperaEmpenhoCompraLicitacaoAnulado( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    else
        $obErro   = $obTEmpenhoEmpenho->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
       
    return $obErro;
}

function listarEmpenhosPopUp(&$rsRecordSet, $stOrder = "tabela.cod_entidade, tabela.cod_empenho, tabela.nom_fornecedor", $boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenho.class.php" );
    $obTEmpenhoEmpenho = new TEmpenhoEmpenho;

    $obTEmpenhoEmpenho->setDado( "numcgm"   , $this->obRUsuario->obRCGM->getNumCGM() );

    $stFiltro = '';

    if( $this->stExercicio )
        $stFiltro .= " AND tabela.exercicio = '".$this->stExercicio."' ";
    if( $this->obROrcamentoEntidade->getCodigoEntidade() )
        $stFiltro  .= " AND tabela.cod_entidade IN (".$this->obROrcamentoEntidade->getCodigoEntidade()." ) ";
    if ($this->inCodEmpenhoInicial or $this->inCodEmpenhoFinal) {
        if ($this->inCodEmpenhoInicial == $this->inCodEmpenhoFinal) {
                $stFiltro  .= " AND tabela.cod_empenho = ".$this->inCodEmpenhoInicial." ";
        } else {
            if( $this->inCodEmpenhoInicial )
                $stFiltro  .= " AND tabela.cod_empenho >= ".$this->inCodEmpenhoInicial." ";
            if( $this->inCodEmpenhoFinal )
                $stFiltro  .= " AND tabela.cod_empenho <= ".$this->inCodEmpenhoFinal." ";
        }
    }
    if ($this->stDtEmpenhoInicial or $this->stDtEmpenhoFinal) {
        if ($this->stDtEmpenhoInicial == $this->stDtEmpenhoFinal) {
            $stFiltro .= " AND  TO_DATE(dt_empenho,'dd/mm/yyyy' ) = TO_DATE('".$this->stDtEmpenhoInicial."','dd/mm/yyyy') ";
        } else {
            $this->stDtEmpenhoInicial = ( $this->stDtEmpenhoInicial ) ? $this->stDtEmpenhoInicial : '01/01/'.$this->stExercicio;
            $this->stDtEmpenhoFinal = ( $this->stDtEmpenhoFinal ) ? $this->stDtEmpenhoFinal : '31/12/'.$this->stExercicio;
            $stFiltro .= " AND  TO_DATE(dt_empenho,'dd/mm/yyyy' ) between ";
            $stFiltro .= "TO_DATE('".$this->stDtEmpenhoInicial."','dd/mm/yyyy') AND TO_DATE('".$this->stDtEmpenhoFinal."','dd/mm/yyyy') ";
        }
    }
    
    $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 4, strlen($stFiltro)) : "";
    $stOrder  = ($stOrder) ? $stOrder : "tabela.cod_empenho";
    
    $obErro = $obTEmpenhoEmpenho->recuperaEmpenhosPopUp( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
       
    return $obErro;
}


/**
    * Executa um recuperaTodos na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarAnulados(&$rsRecordSet, $stOrder = "eai.num_item", $boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO     ."TEmpenhoEmpenho.class.php"                    );
    $obTEmpenhoEmpenho                    =  new TEmpenhoEmpenho;

    if( $this->stExercicio )
        $stFiltro .= " AND e.exercicio = '".$this->stExercicio."' ";
    if( $this->obROrcamentoEntidade->getCodigoEntidade() )
        $stFiltro  .= " AND e.cod_entidade = ".$this->obROrcamentoEntidade->getCodigoEntidade()."  ";
    if( $this->inCodEmpenho )
        $stFiltro  .= " AND e.cod_empenho = ".$this->inCodEmpenho." ";
    $stFiltro = ($stFiltro) ? " AND " . substr($stFiltro, 4, strlen($stFiltro)) : "";
    $stOrder = ($stOrder) ? $stOrder : "eai.num_item";
    $obErro = $obTEmpenhoEmpenho->recuperaRelacionamentoAnulados( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Executa um recuperaTodos na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarReemitirAnulados(&$rsRecordSet, $stOrder="", $boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO     ."TEmpenhoEmpenho.class.php"                    );
    $obTEmpenhoEmpenho                    =  new TEmpenhoEmpenho;

    if( $this->stExercicio )
        $stFiltro .= " AND e.exercicio = '".$this->stExercicio."' ";
    if( $this->obROrcamentoEntidade->getCodigoEntidade() )
        $stFiltro  .= " AND e.cod_entidade IN (".$this->obROrcamentoEntidade->getCodigoEntidade().")  ";
    if( $this->inCodDespesa )
        $stFiltro  .= " AND ped.cod_despesa = ".$this->inCodDespesa." ";
    if( $this->inCodEmpenhoInicial )
        $stFiltro  .= " AND e.cod_empenho >= ".$this->inCodEmpenhoInicial." ";
    if( $this->inCodEmpenhoFinal )
        $stFiltro  .= " AND e.cod_empenho <= ".$this->inCodEmpenhoFinal." ";
    if( $this->obREmpenhoAutorizacaoEmpenho->getCodAutorizacao() )
        $stFiltro  .= " AND ae.cod_autorizacao = ".$this->obREmpenhoAutorizacaoEmpenho->getCodAutorizacao()." ";
    if( $this->obRCGM->getNumCGM() )
        $stFiltro  .= " AND c.numcgm = ".$this->obRCGM->getNumCGM()." ";
    if( $this->inCodModalidadeCompra )
        $stFiltro .= " AND compra_direta.cod_modalidade = ".$this->inCodModalidadeCompra." \n";
    if( $this->inCompraInicial )
        $stFiltro .= " AND compra_direta.cod_compra_direta >= ".$this->inCompraInicial." \n";
    if( $this->inCompraFinal )
        $stFiltro .= " AND compra_direta.cod_compra_direta <= ".$this->inCompraFinal." \n";
    if ($this->stDtEmpenhoInicial or $this->stDtEmpenhoFinal) {
        $this->stDtEmpenhoInicial = ( $this->stDtEmpenhoInicial ) ? $this->stDtEmpenhoInicial : '01/01/'.$this->stExercicio;
        $this->stDtEmpenhoFinal = ( $this->stDtEmpenhoFinal ) ? $this->stDtEmpenhoFinal : '31/12/'.$this->stExercicio;
        $stFiltro .= " AND  to_date(eai.timestamp,'yyyy-mm-dd') between ";
        $stFiltro .= "TO_DATE('".$this->stDtEmpenhoInicial."','dd/mm/yyyy') AND TO_DATE('".$this->stDtEmpenhoFinal."','dd/mm/yyyy') ";
    }
    if ($this->getCodModalidadeLicitacao()) {
        $stFiltro .= " AND adjudicacao.cod_modalidade = ".$this->getCodModalidadeLicitacao();
    }
    if ($this->getLicitacaoInicial()) {
        $stFiltro .= " AND adjudicacao.cod_licitacao >= ".$this->getLicitacaoInicial();
    }
    if ($this->getLicitacaoFinal()) {
        $stFiltro .= " AND adjudicacao.cod_licitacao <= ".$this->getLicitacaoFinal();
    }

    $stFiltro = ($stFiltro) ? " AND " . substr($stFiltro, 4, strlen($stFiltro)) : "";
    $stOrder = ($stOrder) ? $stOrder : "dt_anulado,e.cod_entidade,e.cod_empenho,e.exercicio,nom_fornecedor";
    $obErro = $obTEmpenhoEmpenho->recuperaRelacionamentoReemitirAnulados( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Executa um recuperaTodos na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarReemitirLiquidacao(&$rsRecordSet, $stOrder="", $boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO     ."TEmpenhoEmpenho.class.php"                    );
    $obTEmpenhoEmpenho                    =  new TEmpenhoEmpenho;

    if( $this->stExercicio )
        $stFiltro .= " AND nl.exercicio_empenho = '".$this->stExercicio."' ";

    if( $this->obROrcamentoEntidade->getCodigoEntidade() )
        $stFiltro  .= " AND nl.cod_entidade IN (".$this->obROrcamentoEntidade->getCodigoEntidade().")  ";

    if( $this->inCodDespesa )
        $stFiltro  .= " AND ped.cod_despesa = ".$this->inCodDespesa." ";

    if( $this->inCodEmpenhoInicial )
        $stFiltro  .= " AND e.cod_empenho >= ".$this->inCodEmpenhoInicial." ";
    if( $this->inCodEmpenhoFinal )
        $stFiltro  .= " AND e.cod_empenho <= ".$this->inCodEmpenhoFinal." ";

    if( $this->inCodLiquidacaoInicial )
        $stFiltro  .= " AND nl.cod_nota >= ".$this->inCodLiquidacaoInicial." ";
    if( $this->inCodLiquidacaoFinal )
        $stFiltro  .= " AND nl.cod_nota <= ".$this->inCodLiquidacaoFinal." ";

    if( $this->obRCGM->getNumCGM() )
        $stFiltro  .= " AND c.numcgm = ".$this->obRCGM->getNumCGM()." ";

    if ($this->stDtVencimento) {
        $stFiltro .= " AND empenho.nota_liquidacao.dt_vencimento = TO_DATE('".$this->stDtVencimento."','dd/mm/yyyy') ";
    }

    if ($this->stDtEmpenhoInicial or $this->stDtEmpenhoFinal) {
        $this->stDtEmpenhoInicial = ( $this->stDtEmpenhoInicial ) ? $this->stDtEmpenhoInicial : '01/01/'.$this->stExercicio;
        $this->stDtEmpenhoFinal = ( $this->stDtEmpenhoFinal ) ? $this->stDtEmpenhoFinal : '31/12/'.$this->stExercicio;
        $stFiltro .= " AND  dt_liquidacao between ";
        $stFiltro .= "TO_DATE('".$this->stDtEmpenhoInicial."','dd/mm/yyyy') AND TO_DATE('".$this->stDtEmpenhoFinal."','dd/mm/yyyy') ";
    }

    if ( $this->getCodTipoDocumento() ) {
        $stFiltro .= " AND documento.cod_tipo = ". $this->getCodTipoDocumento()." ";
    }

    $stFiltro = ($stFiltro) ? " AND " . substr($stFiltro, 4, strlen($stFiltro)) : "";
    $stOrder = ($stOrder) ? $stOrder : "nl.cod_entidade,nl.cod_empenho,nl.exercicio,dt_anulacao,nom_fornecedor";
    $obErro = $obTEmpenhoEmpenho->recuperaRelacionamentoReemitirLiquidacao( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Método para verificar se empenho é implantado
    * @access Public
    * @param  Boolean $boImplantado
    * @param  Object  $boTransacao
    * @return Object  $obErro
*/
function checarImplantado(&$boImplantado, $boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO     ."TEmpenhoEmpenho.class.php"                    );
    $obTEmpenhoEmpenho                    =  new TEmpenhoEmpenho;
    $stFiltro = "";
    if( $this->inCodEmpenho )
        $stFiltro .= " EE.cod_empenho = ".$this->inCodEmpenho." AND ";
    if( $this->stExercicio )
        $stFiltro .= " EE.exercicio = '".$this->stExercicio."' AND ";
    if( $this->obROrcamentoEntidade->getCodigoEntidade())
        $stFiltro .= " EE.cod_entidade = ".$this->obROrcamentoEntidade->getCodigoEntidade()." AND ";
    $stFiltro = ( $stFiltro ) ? " AND ".substr($stFiltro,0,strlen($stFiltro)-4) : '';
    $obErro = $obTEmpenhoEmpenho->executaChecaImplantado( $rsRecordSet, $stFiltro, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $boImplantado = ( $rsRecordSet->getCampo( "implantado" ) == 't' ) ? true : false;
    }

    return $obErro;
}

/**
    * Método para verificar a categoria do empenho
    * @access Public
    * @param  Boolean $boImplantado
    * @param  Object  $boTransacao
    * @return Object  $obErro
*/
function retornaCategoria(&$inCodCategoria, $boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO     ."TEmpenhoEmpenho.class.php"                    );
    $obTEmpenhoEmpenho                    =  new TEmpenhoEmpenho;

    $stFiltro = empty($stFiltro) ? "" : $stFiltro;
    if( $this->inCodEmpenho )
        $stFiltro .= " EE.cod_empenho = ".$this->inCodEmpenho." AND ";
    if( $this->stExercicio )
        $stFiltro .= " EE.exercicio = '".$this->stExercicio."' AND ";
    if( $this->obROrcamentoEntidade->getCodigoEntidade())
        $stFiltro .= " EE.cod_entidade = ".$this->obROrcamentoEntidade->getCodigoEntidade()." AND ";
    $stFiltro = ( $stFiltro ) ? " AND ".substr($stFiltro,0,strlen($stFiltro)-4) : '';
    $obErro = $obTEmpenhoEmpenho->executaRetornaCategoria( $rsRecordSet, $stFiltro, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->setCodCategoria($rsRecordSet->getCampo( "cod_categoria" ));
        $inCodCategoria = $rsRecordSet->getCampo( "cod_categoria" );
    }

    return $obErro;
}

/**
    * Executa um recuperaTodos na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarRestosAPagar(&$rsRecordSet, $stOrder = "tabela.cod_entidade, tabela.cod_empenho, tabela.nom_fornecedor", $boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO     ."TEmpenhoEmpenho.class.php"                    );
    $obTEmpenhoEmpenho                    =  new TEmpenhoEmpenho;

    $obTEmpenhoEmpenho->setDado( "numcgm"   , $this->obRUsuario->obRCGM->getNumCGM() );
    $obTEmpenhoEmpenho->setDado( "exercicio", $this->stExercicio );

    if( $this->stExercicio )
        $stFiltro .= " AND tabela.exercicio = '".$this->stExercicio."' ";
    if( $this->obROrcamentoEntidade->getCodigoEntidade() )
        $stFiltro  .= " AND tabela.cod_entidade IN (".$this->obROrcamentoEntidade->getCodigoEntidade()." ) ";
    if( $this->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->getNumeroOrgao())
        $stFiltro .= " AND tabela.num_orgao = ".$this->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->getNumeroOrgao()." ";
    if( $this->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->getNumeroUnidade())
        $stFiltro .= " AND tabela.num_unidade = ".$this->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->getNumeroUnidade()." ";
    if( $this->inCodDespesa )
        $stFiltro  .= " AND tabela.cod_despesa = ".$this->inCodDespesa." ";
    if( $this->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->getCodEstrutural() )
        $stFiltro  .= " AND tabela.cod_estrutural = '".$this->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->getCodEstrutural()."' ";
    if( $this->inCodEmpenhoInicial )
        $stFiltro  .= " AND tabela.cod_empenho >= ".$this->inCodEmpenhoInicial." ";
    if( $this->inCodEmpenhoFinal )
        $stFiltro  .= " AND tabela.cod_empenho <= ".$this->inCodEmpenhoFinal." ";
    if( $this->inCodLiquidacaoInicial )
        $stFiltro  .= " AND tabela.cod_nota >= ".$this->inCodLiquidacaoInicial." ";
    if( $this->inCodLiquidacaoFinal )
        $stFiltro  .= " AND tabela.cod_nota <= ".$this->inCodLiquidacaoFinal." ";
    if( $this->obREmpenhoAutorizacaoEmpenho->getCodAutorizacao() )
        $stFiltro  .= " AND tabela.cod_autorizacao = ".$this->obREmpenhoAutorizacaoEmpenho->getCodAutorizacao()." ";
    if( $this->obRCGM->getNumCGM() )
        $stFiltro  .= " AND tabela.credor = ".$this->obRCGM->getNumCGM()." ";
    if( $this->obROrcamentoDespesa->obROrcamentoRecurso->getCodRecurso() )
        $stFiltro  .= " AND tabela.cod_recurso = ".$this->obROrcamentoDespesa->obROrcamentoRecurso->getCodRecurso()." ";
    if( $this->obREmpenhoHistorico->getCodHistorico() )
        $stFiltro  .= " AND tabela.cod_historico = ".$this->obREmpenhoHistorico->getCodHistorico()." ";
    if ($this->stDtEmpenhoInicial or $this->stDtEmpenhoFinal) {
        $this->stDtEmpenhoInicial = ( $this->stDtEmpenhoInicial ) ? $this->stDtEmpenhoInicial : '01/01/'.$this->stExercicio;
        $this->stDtEmpenhoFinal = ( $this->stDtEmpenhoFinal ) ? $this->stDtEmpenhoFinal : '31/12/'.$this->stExercicio;
        $stFiltro .= " AND  TO_DATE(dt_empenho,'dd/mm/yyyy' ) between ";
        $stFiltro .= "TO_DATE('".$this->stDtEmpenhoInicial."','dd/mm/yyyy') AND TO_DATE('".$this->stDtEmpenhoFinal."','dd/mm/yyyy') ";
    }

    if ($this->stDtVencimento) {
        $stFiltro .= " AND  TO_DATE(tabela.dt_vencimento,'dd/mm/yyyy' ) = TO_DATE('".$this->stDtVencimento."','dd/mm/yyyy') ";
    }

    if ( $this->obREmpenhoAutorizacaoEmpenho->getCodAutorizacaoInicial() or $this->obREmpenhoAutorizacaoEmpenho->getCodAutorizacaoFinal()) {
        if ( $this->obREmpenhoAutorizacaoEmpenho->getCodAutorizacaoInicial() == $this->obREmpenhoAutorizacaoEmpenho->getCodAutorizacaoFinal() ) {
            $stFiltro .= " AND  tabela.cod_autorizacao = ".$this->obREmpenhoAutorizacaoEmpenho->getCodAutorizacaoInicial();
        } else {
            $stFiltro .= " AND  tabela.cod_autorizacao >= ".$this->obREmpenhoAutorizacaoEmpenho->getCodAutorizacaoInicial();
            $stFiltro .= " AND  tabela.cod_autorizacao <= ".$this->obREmpenhoAutorizacaoEmpenho->getCodAutorizacaoFinal();
        }
    }

    if( $this->inSituacao == 1)
        $stFiltro  .= " AND tabela.vl_pago > 0 ";
    if( $this->inSituacao == 2)
        $stFiltro  .= " AND (tabela.vl_empenhado - tabela.vl_pago) > 0 ";
    if ($this->inSituacao == 3) {
        $stFiltro  .= " AND tabela.vl_liquidado > 0 ";
        // caso este método necessite mostrar os registros por nota, deve-se setar a situacao para 3.
        $obTEmpenhoEmpenho->setDado( "inSituacao", 3 );
    }
    if( $this->inSituacao == 4)
        $stFiltro  .= " AND (tabela.vl_empenhado - tabela.vl_empenhado_anulado) = 0 ";

    if( $this->inCodPreEmpenho )
        $stFiltro  .= " AND tabela.cod_pre_empenho = ".$this->inCodPreEmpenho." ";
    if( $this->inCodEmpenho )
        $stFiltro  .= " AND tabela.cod_empenho = ".$this->inCodEmpenho." ";
    if ($this->boSomar) {
        $stFiltro .= " AND ( tabela.vl_empenhado -  tabela.vl_empenhado_anulado ) > ( tabela.vl_liquidado - tabela.vl_liquidado_anulado ) ";
    }
    $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 4, strlen($stFiltro)) : "";
    $stOrder  = ($stOrder) ? $stOrder : "tabela.cod_empenho";
    $obErro   = $obTEmpenhoEmpenho->recuperaRestosAPagar( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Executa um recuperaTodos na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarRestosAPagarAjustes(&$rsRecordSet, $stOrder = "tabela.cod_entidade, tabela.cod_empenho, tabela.nom_fornecedor", $boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO     ."TEmpenhoEmpenho.class.php"                    );
    $obTEmpenhoEmpenho                    =  new TEmpenhoEmpenho;

    $obTEmpenhoEmpenho->setDado( "numcgm"   , $this->obRUsuario->obRCGM->getNumCGM() );
    $obTEmpenhoEmpenho->setDado( "exercicio", $this->stExercicio );

    if( $this->stExercicio )
        $stFiltro .= " AND tabela.exercicio = '".$this->stExercicio."' ";
    if( $this->obROrcamentoEntidade->getCodigoEntidade() )
        $stFiltro  .= " AND tabela.cod_entidade IN (".$this->obROrcamentoEntidade->getCodigoEntidade()." ) ";
    if( $this->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->getNumeroOrgao())
        $stFiltro .= " AND tabela.num_orgao = ".$this->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->getNumeroOrgao()." ";
    if( $this->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->getNumeroUnidade())
        $stFiltro .= " AND tabela.num_unidade = ".$this->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->getNumeroUnidade()." ";
    if( $this->inCodDespesa )
        $stFiltro  .= " AND tabela.cod_despesa = ".$this->inCodDespesa." ";
    if( $this->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->getCodEstrutural() )
        $stFiltro  .= " AND tabela.cod_estrutural = '".$this->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->getCodEstrutural()."' ";
    if( $this->inCodEmpenhoInicial )
        $stFiltro  .= " AND tabela.cod_empenho >= ".$this->inCodEmpenhoInicial." ";
    if( $this->inCodEmpenhoFinal )
        $stFiltro  .= " AND tabela.cod_empenho <= ".$this->inCodEmpenhoFinal." ";
    if( $this->inCodLiquidacaoInicial )
        $stFiltro  .= " AND tabela.cod_nota >= ".$this->inCodLiquidacaoInicial." ";
    if( $this->inCodLiquidacaoFinal )
        $stFiltro  .= " AND tabela.cod_nota <= ".$this->inCodLiquidacaoFinal." ";
    if( $this->obREmpenhoAutorizacaoEmpenho->getCodAutorizacao() )
        $stFiltro  .= " AND tabela.cod_autorizacao = ".$this->obREmpenhoAutorizacaoEmpenho->getCodAutorizacao()." ";
    if( $this->obRCGM->getNumCGM() )
        $stFiltro  .= " AND tabela.credor = ".$this->obRCGM->getNumCGM()." ";
    if( $this->obROrcamentoDespesa->obROrcamentoRecurso->getCodRecurso() )
        $stFiltro  .= " AND tabela.cod_recurso = ".$this->obROrcamentoDespesa->obROrcamentoRecurso->getCodRecurso()." ";
    if( $this->obREmpenhoHistorico->getCodHistorico() )
        $stFiltro  .= " AND tabela.cod_historico = ".$this->obREmpenhoHistorico->getCodHistorico()." ";
    if ($this->stDtEmpenhoInicial or $this->stDtEmpenhoFinal) {
        $this->stDtEmpenhoInicial = ( $this->stDtEmpenhoInicial ) ? $this->stDtEmpenhoInicial : '01/01/'.$this->stExercicio;
        $this->stDtEmpenhoFinal = ( $this->stDtEmpenhoFinal ) ? $this->stDtEmpenhoFinal : '31/12/'.$this->stExercicio;
        $stFiltro .= " AND  TO_DATE(dt_empenho,'dd/mm/yyyy' ) between ";
        $stFiltro .= "TO_DATE('".$this->stDtEmpenhoInicial."','dd/mm/yyyy') AND TO_DATE('".$this->stDtEmpenhoFinal."','dd/mm/yyyy') ";
    }
    if ( $this->obREmpenhoAutorizacaoEmpenho->getCodAutorizacaoInicial() or $this->obREmpenhoAutorizacaoEmpenho->getCodAutorizacaoFinal()) {
        if ( $this->obREmpenhoAutorizacaoEmpenho->getCodAutorizacaoInicial() == $this->obREmpenhoAutorizacaoEmpenho->getCodAutorizacaoFinal() ) {
            $stFiltro .= " AND  tabela.cod_autorizacao = ".$this->obREmpenhoAutorizacaoEmpenho->getCodAutorizacaoInicial();
        } else {
            $stFiltro .= " AND  tabela.cod_autorizacao >= ".$this->obREmpenhoAutorizacaoEmpenho->getCodAutorizacaoInicial();
            $stFiltro .= " AND  tabela.cod_autorizacao <= ".$this->obREmpenhoAutorizacaoEmpenho->getCodAutorizacaoFinal();
        }
    }

    if( $this->inSituacao == 1)
        $stFiltro  .= " AND tabela.vl_pago > 0 ";
    if( $this->inSituacao == 2)
        $stFiltro  .= " AND (tabela.vl_empenhado - tabela.vl_pago) > 0 ";
    if( $this->inSituacao == 3)
        $stFiltro  .= " AND tabela.vl_liquidado > 0 ";
    if( $this->inSituacao == 4)
        $stFiltro  .= " AND (tabela.vl_empenhado - tabela.vl_empenhado_anulado) = 0 ";

    if( $this->inCodPreEmpenho )
        $stFiltro  .= " AND tabela.cod_pre_empenho = ".$this->inCodPreEmpenho." ";
    if( $this->inCodEmpenho )
        $stFiltro  .= " AND tabela.cod_empenho = ".$this->inCodEmpenho." ";
    if ($this->boSomar) {
        $stFiltro .= " AND ( tabela.vl_empenhado -  tabela.vl_empenhado_anulado ) > ( tabela.vl_liquidado - tabela.vl_liquidado_anulado ) ";
    }
    
     if( $this->inCodModalidadeCompra )
        $stFiltro .= " AND tabela.compra_cod_modalidade = ".$this->inCodModalidadeCompra." \n";

    if( $this->inCompraInicial )
        $stFiltro .= " AND tabela.cod_compra_direta >= ".$this->inCompraInicial." \n";
        
    if( $this->inCompraFinal )
        $stFiltro .= " AND tabela.cod_compra_direta <= ".$this->inCompraFinal." \n";
    
    if( $this->inCodModalidadeLicitacao )
        $stFiltro .= " AND tabela.licitacao_cod_modalidade = ".$this->inCodModalidadeLicitacao." \n";

    if( $this->inLicitacaoInicial )
        $stFiltro .= " AND tabela.cod_licitacao >= ".$this->inLicitacaoInicial." \n";
        
    if( $this->inLicitacaoFinal )
        $stFiltro .= " AND tabela.cod_licitacao <= ".$this->inLicitacaoFinal." \n";
    
    $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 4, strlen($stFiltro)) : "";
    $stOrder  = ($stOrder) ? $stOrder : "tabela.cod_empenho";
    
    if( $this->boEmpenhoCompraLicitacao )
        $obErro   = $obTEmpenhoEmpenho->recuperaRestosAPagarAjustesCompraLicitacao( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    else
        $obErro   = $obTEmpenhoEmpenho->recuperaRestosAPagarAjustes( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    
    return $obErro;
}

/**
    * Executa um recuperaTodos na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarRestosConsultaEmpenho(&$rsRecordSet, $stOrder = "tabela.cod_entidade, tabela.cod_empenho, tabela.nom_fornecedor", $boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO     ."TEmpenhoEmpenho.class.php"                    );
    $obTEmpenhoEmpenho                    =  new TEmpenhoEmpenho;

    $obTEmpenhoEmpenho->setDado( "numcgm"   , $this->obRUsuario->obRCGM->getNumCGM() );
    $obTEmpenhoEmpenho->setDado( "exercicio", $this->stExercicio );

    if ($this->stExercicio) {
        $stFiltro .= " AND tabela.exercicio = '".$this->stExercicio."' ";
        $obTEmpenhoEmpenho->setDado('exercicio', $this->stExercicio );
    }
    if( $this->obROrcamentoEntidade->getCodigoEntidade() )
        $stFiltro  .= " AND tabela.cod_entidade IN (".$this->obROrcamentoEntidade->getCodigoEntidade()." ) ";
    if( $this->obROrcamentoEntidade->obRCGMPessoaFisica->getCPF())
        $stFiltro  .= " AND tabela.credor in ( SELECT numcgm from sw_cgm_pessoa_fisica where cpf = '".$this->obROrcamentoEntidade->obRCGMPessoaFisica->getCPF()."') ";
    if( $this->obROrcamentoEntidade->obRCGMPessoaJuridica->getCNPJ())
        $stFiltro  .= " AND tabela.credor in ( SELECT numcgm from sw_cgm_pessoa_juridica where cnpj = '".$this->obROrcamentoEntidade->obRCGMPessoaJuridica->getCNPJ()."') ";
    if( $this->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->getNumeroOrgao())
        $stFiltro .= " AND tabela.num_orgao = ".$this->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->getNumeroOrgao()." ";
    if( $this->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->getNumeroUnidade())
        $stFiltro .= " AND tabela.num_unidade = ".$this->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->getNumeroUnidade()." ";
    if( $this->inCodDespesa )
        $stFiltro  .= " AND tabela.cod_despesa = ".$this->inCodDespesa." ";
    if( $this->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->getCodEstrutural() )
        $stFiltro  .= " AND tabela.cod_estrutural like publico.fn_mascarareduzida('".$this->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->getCodEstrutural()."')||'.%' ";
    if( $this->inCodEmpenhoInicial )
        $stFiltro  .= " AND tabela.cod_empenho >= ".$this->inCodEmpenhoInicial." ";
    if( $this->inCodEmpenhoFinal )
        $stFiltro  .= " AND tabela.cod_empenho <= ".$this->inCodEmpenhoFinal." ";
    if( $this->inCodLiquidacaoInicial )
        $stFiltro  .= " AND tabela.cod_nota >= ".$this->inCodLiquidacaoInicial." ";
    if( $this->inCodLiquidacaoFinal )
        $stFiltro  .= " AND tabela.cod_nota <= ".$this->inCodLiquidacaoFinal." ";
    if( $this->obREmpenhoAutorizacaoEmpenho->getCodAutorizacao() )
        $stFiltro  .= " AND tabela.cod_autorizacao = ".$this->obREmpenhoAutorizacaoEmpenho->getCodAutorizacao()." ";
    if( $this->obRCGM->getNumCGM() )
        $stFiltro  .= " AND tabela.credor = ".$this->obRCGM->getNumCGM()." ";
    if( $this->obROrcamentoDespesa->obROrcamentoRecurso->getCodRecurso() )
        $stFiltro  .= " AND tabela.cod_recurso = ".$this->obROrcamentoDespesa->obROrcamentoRecurso->getCodRecurso()." ";
    if( $this->obROrcamentoDespesa->obROrcamentoRecurso->getDestinacaoRecurso() )
        $stFiltro  .= " AND tabela.cod_recurso like '".$this->obROrcamentoDespesa->obROrcamentoRecurso->getDestinacaoRecurso()."%' ";

    if( $this->obROrcamentoDespesa->obROrcamentoRecurso->getCodDetalhamento() )
        $stFiltro  .= " AND tabela.cod_detalhamento = ".$this->obROrcamentoDespesa->obROrcamentoRecurso->getCodDetalhamento()." ";

    if( $this->obREmpenhoHistorico->getCodHistorico() )
        $stFiltro  .= " AND tabela.cod_historico = ".$this->obREmpenhoHistorico->getCodHistorico()." ";
    if ($this->stDtEmpenhoInicial or $this->stDtEmpenhoFinal) {
        $this->stDtEmpenhoInicial = ( $this->stDtEmpenhoInicial ) ? $this->stDtEmpenhoInicial : '01/01/'.$this->stExercicio;
        $this->stDtEmpenhoFinal = ( $this->stDtEmpenhoFinal ) ? $this->stDtEmpenhoFinal : '31/12/'.$this->stExercicio;
        $stFiltro .= " AND  TO_DATE(dt_empenho,'dd/mm/yyyy' ) between ";
        $stFiltro .= "TO_DATE('".$this->stDtEmpenhoInicial."','dd/mm/yyyy') AND TO_DATE('".$this->stDtEmpenhoFinal."','dd/mm/yyyy') ";
    }

    if( $this->inSituacao == 1)
        $stFiltro  .= " AND (tabela.vl_pago - tabela.vl_pago_anulado) >= (tabela.vl_empenhado - tabela.vl_empenhado_anulado) ";
    if( $this->inSituacao == 2)
        $stFiltro  .= " AND (( tabela.vl_empenhado - tabela.vl_empenhado_anulado ) - tabela.vl_pago) > 0 ";
    if( $this->inSituacao == 3)
        $stFiltro  .= " AND (tabela.vl_empenhado - tabela.vl_empenhado_anulado) <= (tabela.vl_liquidado - tabela.vl_liquidado_anulado) ";
    if( $this->inSituacao == 4)
        $stFiltro  .= " AND (tabela.vl_empenhado - tabela.vl_empenhado_anulado) = 0 ";

    if( $this->inCodPreEmpenho )
        $stFiltro  .= " AND tabela.cod_pre_empenho = ".$this->inCodPreEmpenho." ";
    if( $this->inCodEmpenho )
        $stFiltro  .= " AND tabela.cod_empenho = ".$this->inCodEmpenho." ";
    if ($this->boSomar) {
        $stFiltro .= " AND ( tabela.vl_empenhado -  tabela.vl_empenhado_anulado ) > ( tabela.vl_liquidado - tabela.vl_liquidado_anulado ) ";
    }
    
    if( $this->inCodModalidadeCompra )
        $stFiltro .= " AND tabela.compra_cod_modalidade = ".$this->inCodModalidadeCompra." \n";

    if( $this->inCompraInicial )
        $stFiltro .= " AND tabela.cod_compra_direta >= ".$this->inCompraInicial." \n";
        
    if( $this->inCompraFinal )
        $stFiltro .= " AND tabela.cod_compra_direta <= ".$this->inCompraFinal." \n";
    
    if( $this->inCodModalidadeLicitacao )
        $stFiltro .= " AND tabela.licitacao_cod_modalidade = ".$this->inCodModalidadeLicitacao." \n";

    if( $this->inLicitacaoInicial )
        $stFiltro .= " AND tabela.cod_licitacao >= ".$this->inLicitacaoInicial." \n";
        
    if( $this->inLicitacaoFinal )
        $stFiltro .= " AND tabela.cod_licitacao <= ".$this->inLicitacaoFinal." \n";

    //adicionado o filtro por atributos dinamicos
    if ( is_array($this->arAtributosDinamicos) ) {
        foreach ($this->arAtributosDinamicos as $arTemp) {
        $stFiltro .= "
         AND EXISTS ( 	SELECT 1
                            FROM empenho.atributo_empenho_valor
                         WHERE atributo_empenho_valor.exercicio = tabela.exercicio
                           AND atributo_empenho_valor.cod_pre_empenho = tabela.cod_pre_empenho
                           AND atributo_empenho_valor.cod_modulo = ".$arTemp['cod_modulo']."
                           AND atributo_empenho_valor.cod_cadastro = ".$arTemp['cod_cadastro']."
                           AND atributo_empenho_valor.cod_atributo = ".$arTemp['cod_atributo']."
        ";
        if ($arTemp['tipo'] == 'multiplo') {
            $stFiltro .= " AND atributo_empenho_valor.valor IN(".$arTemp['valor'].") ";
        } else {
            $stFiltro .= " AND atributo_empenho_valor.valor = '".$arTemp['valor']."' ";
        }

        $stFiltro .= "
            )
        ";
        }
    }

    $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 4, strlen($stFiltro)) : "";
    $stOrder  = ($stOrder) ? $stOrder : "tabela.cod_empenho";
    
    if($this->boEmpenhoCompraLicitacao)
        $obErro   = $obTEmpenhoEmpenho->recuperaRestosConsultaEmpenhoCompraLicitacao( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    else    
        $obErro   = $obTEmpenhoEmpenho->recuperaRestosConsultaEmpenho( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    
    return $obErro;
}

/**
    * Executa um recuperaTodos na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarPorNota(&$rsRecordSet, $stOrder = "", $boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO     ."TEmpenhoEmpenho.class.php"                    );

    $obTEmpenhoEmpenho                    =  new TEmpenhoEmpenho;

    $obTEmpenhoEmpenho->setDado( "numcgm"   , $this->obRUsuario->obRCGM->getNumCGM() );
    $obTEmpenhoEmpenho->setDado( "exercicio", $this->stExercicio );

    if( $this->inCodDespesa )
        $stFiltro  .= " AND tabela.cod_despesa = ".$this->inCodDespesa." ";

    if( $this->inCodFornecedor )
        $stFiltro  .= " AND tabela.num_fornecedor = ".$this->inCodFornecedor." ";

    if( $this->inCodPreEmpenho )
        $stFiltro  .= " AND tabela.cod_pre_empenho = ".$this->inCodPreEmpenho." ";

    if( $this->stDtVencimento )
        $stFiltro  .= " AND TO_DATE(tabela.dt_vencimento_liquidacao,'dd/mm/yyyy') = TO_DATE('".$this->stDtVencimento."','dd/mm/yyyy') ";

    if ($this->inCodEmpenhoInicial and $this->inCodEmpenhoFinal) {
        $stFiltro .= " AND tabela.cod_empenho between '".$this->inCodEmpenhoInicial."' AND '".$this->inCodEmpenhoFinal."' ";
    } elseif ($this->inCodEmpenhoInicial and !$this->inCodEmpenhoFinal) {
        $stFiltro .= " AND tabela.cod_empenho >= '".$this->inCodEmpenhoInicial."' ";
    } elseif (!$this->inCodEmpenhoInicial and $this->inCodEmpenhoFinal) {
        $stFiltro .= " AND tabela.cod_empenho <= '".$this->inCodEmpenhoFinal."' ";
    }
    if( $this->inCodLiquidacaoInicial )
        $stFiltro  .= " AND tabela.cod_nota >= ".$this->inCodLiquidacaoInicial." ";
    if( $this->inCodLiquidacaoFinal )
        $stFiltro  .= " AND tabela.cod_nota <= ".$this->inCodLiquidacaoFinal." ";

    if( $this->obROrcamentoEntidade->getCodigoEntidade() )
        $stFiltro  .= " AND tabela.cod_entidade IN (".$this->obROrcamentoEntidade->getCodigoEntidade()." ) ";
    if( $this->stExercicio )
        $stFiltro .= " AND tabela.exercicio = '".$this->stExercicio."' ";
    if ($this->stDtEmpenhoInicial or $this->stDtEmpenhoFinal) {
        $this->stDtEmpenhoInicial = ( $this->stDtEmpenhoInicial ) ? $this->stDtEmpenhoInicial : '01/01/'.$this->stExercicio;
        $this->stDtEmpenhoFinal   = ( $this->stDtEmpenhoFinal )   ? $this->stDtEmpenhoFinal   : '31/12/'.$this->stExercicio;
        $stFiltro .= " AND  TO_DATE(dt_empenho,'dd/mm/yyyy' ) between ";
        $stFiltro .= "TO_DATE('".$this->stDtEmpenhoInicial."','dd/mm/yyyy') AND TO_DATE('".$this->stDtEmpenhoFinal."','dd/mm/yyyy') ";
    }
    if ($this->stDtLiquidacaoInicial or $this->stDtLiquidacaoFinal) {
        $this->stDtLiquidacaoInicial = ( $this->stDtLiquidacaoInicial ) ? $this->stDtLiquidacaoInicial : '01/01/'.$this->stExercicio;
        $this->stDtLiquidacaoFinal   = ( $this->stDtLiquidacaoFinal )   ? $this->stDtLiquidacaoFinal   : '31/12/'.$this->stExercicio;
        $stFiltro .= " AND  TO_DATE(dt_liquidacao,'dd/mm/yyyy' ) between ";
        $stFiltro .= "TO_DATE('".$this->stDtLiquidacaoInicial."','dd/mm/yyyy') AND TO_DATE('".$this->stDtLiquidacaoFinal."','dd/mm/yyyy') ";
    }

    if ($this->boSomar) {
        $stFiltro .= " AND tabela.vl_empenhado > (tabela.vl_empenhado_anulado + (tabela.vl_liquidado - tabela.vl_liquidado_anulado)) ";
    }
    if ($this->boSomarLiquidacao) {
        $stFiltro .= " AND tabela.vl_liquidado > tabela.vl_liquidado_anulado ";
        $obTEmpenhoEmpenho->setDado("stAcao",'anular');
    }

    if ( $this->getCodTipoDocumento() ) {
        $stFiltro .= " AND tabela.cod_tipo = ".$this->getCodTipoDocumento()." ";
    }

    $stFiltro = ($stFiltro) ? " AND " . substr($stFiltro, 4, strlen($stFiltro)) : "";
    $stOrder = ($stOrder) ? $stOrder : "tabela.cod_entidade,tabela.cod_empenho,tabela.cod_nota";
    $obErro = $obTEmpenhoEmpenho->recuperaRelacionamentoPorNota( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Executa um recuperaTodos na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarLiquidacaoAnuladaPorItem(&$rsRecordSet)
{
    include_once ( CAM_GF_EMP_MAPEAMENTO     ."TEmpenhoEmpenho.class.php"                    );
    $obTEmpenhoEmpenho                    =  new TEmpenhoEmpenho;

    $obTEmpenhoEmpenho->setDado( "exercicio", $this->stExercicioEmissao );
    $obTEmpenhoEmpenho->setDado( "cod_empenho", $this->inCodEmpenho );
    $obTEmpenhoEmpenho->setDado( "cod_entidade", $this->obROrcamentoEntidade );
    $obTEmpenhoEmpenho->setDado( "cod_liquidacao", $this->inCodLiquidacao );
    $obTEmpenhoEmpenho->setDado( "cod_item_liquidacao", $this->inNumItemEmpenho );
    $obErro = $obTEmpenhoEmpenho->recuperaLiquidacaoAnuladaPorItem( $rsRecordSet, $boTransacao );

    return $obErro;
}

/**
    * Executa um recuperaTodos na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarRestosPorNota(&$rsRecordSet, $stOrder = "", $boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO     ."TEmpenhoEmpenho.class.php"                    );
    $obTEmpenhoEmpenho                    =  new TEmpenhoEmpenho;

    $obTEmpenhoEmpenho->setDado( "numcgm"   , $this->obRUsuario->obRCGM->getNumCGM() );
    $obTEmpenhoEmpenho->setDado( "exercicio", $this->stExercicio );
    if( $this->inCodDespesa )
        $stFiltro  .= " AND tabela.cod_despesa = ".$this->inCodDespesa." ";
    if( $this->inCodPreEmpenho )
        $stFiltro  .= " AND tabela.cod_pre_empenho = ".$this->inCodPreEmpenho." ";

    if( $this->inCodFornecedor )
        $stFiltro  .= " AND tabela.num_fornecedor = ".$this->inCodFornecedor." ";

    if( $this->stDtVencimento )
        $stFiltro  .= " AND TO_DATE(tabela.dt_vencimento_liquidacao,'dd/mm/yyyy') = TO_DATE('".$this->stDtVencimento."','dd/mm/yyyy') ";

    if ($this->inCodEmpenhoInicial and $this->inCodEmpenhoFinal) {
        $stFiltro .= " AND tabela.cod_empenho between '".$this->inCodEmpenhoInicial."' AND '".$this->inCodEmpenhoFinal."' ";
    } elseif ($this->inCodEmpenhoInicial and !$this->inCodEmpenhoFinal) {
        $stFiltro .= " AND tabela.cod_empenho >= '".$this->inCodEmpenhoInicial."' ";
    } elseif (!$this->inCodEmpenhoInicial and $this->inCodEmpenhoFinal) {
        $stFiltro .= " AND tabela.cod_empenho <= '".$this->inCodEmpenhoFinal."' ";
    }

    if( $this->inCodLiquidacaoInicial )
        $stFiltro  .= " AND tabela.cod_nota >= ".$this->inCodLiquidacaoInicial." ";
    if( $this->inCodLiquidacaoFinal )
        $stFiltro  .= " AND tabela.cod_nota <= ".$this->inCodLiquidacaoFinal." ";

    if( $this->obROrcamentoEntidade->getCodigoEntidade() )
        $stFiltro  .= " AND tabela.cod_entidade IN (".$this->obROrcamentoEntidade->getCodigoEntidade()." ) ";
    if( $this->stExercicio )
        $stFiltro .= " AND tabela.exercicio = '".$this->stExercicio."' ";
    if ($this->stDtEmpenhoInicial or $this->stDtEmpenhoFinal) {
        $this->stDtEmpenhoInicial = ( $this->stDtEmpenhoInicial ) ? $this->stDtEmpenhoInicial : '01/01/'.$this->stExercicio;
        $this->stDtEmpenhoFinal   = ( $this->stDtEmpenhoFinal )   ? $this->stDtEmpenhoFinal   : '31/12/'.$this->stExercicio;
        $stFiltro .= " AND  TO_DATE(dt_empenho,'dd/mm/yyyy' ) between ";
        $stFiltro .= "TO_DATE('".$this->stDtEmpenhoInicial."','dd/mm/yyyy') AND TO_DATE('".$this->stDtEmpenhoFinal."','dd/mm/yyyy') ";
    }
    if ($this->boSomar) {
        $stFiltro .= " AND tabela.vl_empenhado > (tabela.vl_empenhado_anulado + (tabela.vl_liquidado - tabela.vl_liquidado_anulado)) ";
    }
    if ($this->boSomarLiquidacao) {
        $stFiltro .= " AND tabela.vl_liquidado > tabela.vl_liquidado_anulado ";
        $obTEmpenhoEmpenho->setDado("stAcao",'anular');
    }
    $stFiltro = ($stFiltro) ? " AND " . substr($stFiltro, 4, strlen($stFiltro)) : "";
    $stOrder = ($stOrder) ? $stOrder : "tabela.cod_entidade,tabela.cod_empenho,tabela.cod_nota";
    $obErro = $obTEmpenhoEmpenho->recuperaRestosPorNota( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}
/**
    * Verifica se o valor a ser anulado é menor que o valor do item
    * @access Public
    * @param Integer $inNumItem
    * @param Object $boTransacao
    * @return Object $obErro
*/
function validarValorItem($inNumItem, $boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO     ."TEmpenhoEmpenho.class.php"                    );
    $obTEmpenhoEmpenho                    =  new TEmpenhoEmpenho;

    $obErro = new Erro;
    if ( !sizeof($this->arItemPreEmpenho) ) {
        $obErro = $this->consultar( $boTransacao );
    }
    if ( !$obErro->ocorreu() ) {
        if( $this->inCodEmpenho )
            $stFiltro  .= " AND EE.cod_empenho = ".$this->inCodEmpenho." ";
        if( $this->obROrcamentoEntidade->getCodigoEntidade() )
            $stFiltro  .= " AND EE.cod_entidade = ".$this->obROrcamentoEntidade->getCodigoEntidade()." ";
        if( $this->stExercicio )
            $stFiltro  .= " AND EE.exercicio = '".$this->stExercicio."' ";
        if( $inNumItem )
            $stFiltro  .= " AND IE.num_item = '".$inNumItem."' ";
        $stOrder = ($stOrder) ? $stOrder : "EE.cod_empenho,PE.num_item";
        $obErro = $obTEmpenhoEmpenho->recuperaValorItem( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $nuValorEmpenhado        = $rsRecordSet->getCampo( "vl_total"                   );
            $nuValorEmpenhadoAnulado = $rsRecordSet->getCampo( "vl_item_anulado"            );
            $nuValorLiquidado        = $rsRecordSet->getCampo( "vl_item_liquidado"          );
            $nuValorLiquidadoAnulado = $rsRecordSet->getCampo( "vl_item_liquidado_anulado"  );
            $nuValorTotal = bcsub( $nuValorEmpenhado, ( bcadd( $nuValorEmpenhadoAnulado ,( bcsub($nuValorLiquidado, $nuValorLiquidadoAnulado, 4) ), 4 ) ),4 );
            foreach ($this->arItemPreEmpenho as $obItemPreEmpenho) {
                if ( $obItemPreEmpenho->getNumItem() == $inNumItem ) {
                    if ( $obItemPreEmpenho->getValorEmpenhadoAnulado() > $nuValorTotal ) {
                        $obErro->setDescricao( "Valor a ser anulado é maior que o saldo do Item" );
                        break;
                    }
                }
            }
        }
    }

    return $obErro;
}

/**
    * Executa um recuperaValorItem na classe Persistente
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultarValorItem($boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO     ."TEmpenhoEmpenho.class.php"                    );
    $obTEmpenhoEmpenho                    =  new TEmpenhoEmpenho;

    if(empty($stFiltro))
        $stFiltro = null;

    $obErro = new Erro;
    if ( !sizeof($this->arItemPreEmpenho) ) {
        $obErro = $this->consultar( $boTransacao );
    }
    if ( !$obErro->ocorreu() ) {
        if( $this->inCodEmpenho )
            $stFiltro  .= " AND EE.cod_empenho = ".$this->inCodEmpenho." ";
        if( $this->obROrcamentoEntidade->getCodigoEntidade() )
            $stFiltro  .= " AND EE.cod_entidade = ".$this->obROrcamentoEntidade->getCodigoEntidade()." ";
        if( $this->stExercicio )
            $stFiltro  .= " AND EE.exercicio = '".$this->stExercicio."' ";
        $stOrder = (isset($stOrder)) ? $stOrder : "EE.cod_empenho,PE.num_item";
        $obErro = $obTEmpenhoEmpenho->recuperaValorItem( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            while ( !$rsRecordSet->eof() ) {
                for ( $inCount = 0; $inCount < count($this->arItemPreEmpenho); $inCount++ ) {
                    if ( $this->arItemPreEmpenho[$inCount]->getNumItem() == $rsRecordSet->getCampo( "num_item" ) ) {
                        $this->arItemPreEmpenho[$inCount]->setValorEmpenhadoAnulado( $rsRecordSet->getCampo( "vl_item_anulado"           ) );
                        $this->arItemPreEmpenho[$inCount]->setValorLiquidado       ( $rsRecordSet->getCampo( "vl_item_liquidado"         ) );
                        $this->arItemPreEmpenho[$inCount]->setValorLiquidadoAnulado( $rsRecordSet->getCampo( "vl_item_liquidado_anulado" ) );
                    }
                }
                $rsRecordSet->proximo();
            }
        }
    }

    return $obErro;
}

/**
    * Executa um recuperaValorItem na classe Persistente
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultarValorNotaItem($boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO     ."TEmpenhoEmpenho.class.php"                    );
    $obTEmpenhoEmpenho                    =  new TEmpenhoEmpenho;

    $obErro = new Erro;
    if ( !sizeof($this->arItemPreEmpenho) ) {
        $obErro = $this->consultar( $boTransacao );
    }
    if ( !$obErro->ocorreu() ) {
        if( $this->inCodEmpenho )
            $stFiltro  .= " AND EE.cod_empenho = ".$this->inCodEmpenho." ";
        if( $this->obROrcamentoEntidade->getCodigoEntidade() )
            $stFiltro  .= " AND EE.cod_entidade = ".$this->obROrcamentoEntidade->getCodigoEntidade()." ";
        if( $this->stExercicio )
            $stFiltro  .= " AND EE.exercicio = '".$this->stExercicio."' ";
        if( $this->inCodLiquidacaoInicial )
            $stFiltro  .= " AND NL.cod_nota = '".$this->inCodLiquidacaoInicial."' ";
        $stOrder = ($stOrder) ? $stOrder : "EE.cod_empenho,PE.num_item";
        $obErro = $obTEmpenhoEmpenho->recuperaValorNotaItem( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            while ( !$rsRecordSet->eof() ) {
                for ( $inCount = 0; $inCount < count($this->arItemPreEmpenho); $inCount++ ) {
                    if ( $this->arItemPreEmpenho[$inCount]->getNumItem() == $rsRecordSet->getCampo( "num_item" ) ) {
                        $this->arItemPreEmpenho[$inCount]->setValorEmpenhadoAnulado( $rsRecordSet->getCampo( "vl_item_anulado"           ) );
                        $this->arItemPreEmpenho[$inCount]->setValorLiquidado       ( $rsRecordSet->getCampo( "vl_item_liquidado"         ) );
                        $this->arItemPreEmpenho[$inCount]->setValorLiquidadoAnulado( $rsRecordSet->getCampo( "vl_item_liquidado_anulado" ) );
                    }
                }
                $rsRecordSet->proximo();
            }
        }
    }

    return $obErro;
}

/**
    * Executa um recuperaTodos na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarEmpenhoAnulado(&$rsRecordSet, $stOrder = "cod_empenho,timestamp,num_item", $boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO     ."TEmpenhoEmpenhoAnulado.class.php"             );
    $obTEmpenhoEmpenhoAnulado             =  new TEmpenhoEmpenhoAnulado;

    if( $this->stExercicio )
        $stFiltro .= " ea.exercicio = '".$this->stExercicio."' AND ";
    if( $this->obROrcamentoEntidade->getCodigoEntidade() )
        $stFiltro .= " ea.cod_entidade = ".$this->obROrcamentoEntidade->getCodigoEntidade()." AND ";
    if( $this->inCodEmpenho )
        $stFiltro .= " ea.cod_empenho = ".$this->inCodEmpenho." AND ";
    if( $this->stTimestamp )
        $stFiltro .= " ea.timestamp = '".$this->stTimestamp."' AND ";
    else
        $stOrder = " cod_empenho,timestamp DESC,num_item ASC ";

    $stFiltro = ($stFiltro) ? " AND " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
    $stOrder = ($stOrder) ? $stOrder : "cod_empenho,timestamp,num_item";
    $obErro = $obTEmpenhoEmpenhoAnulado->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Executa um recuperaPorChave na classe Persistente
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultar($boTransacao = "")
{
    include_once CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenho.class.php";
    $obTEmpenhoEmpenho = new TEmpenhoEmpenho;
    $obTEmpenhoEmpenho->setDado( "exercicio"       , $this->stExercicio                                 );
    $obTEmpenhoEmpenho->setDado( "cod_empenho"     , $this->inCodEmpenho                                );
    $obTEmpenhoEmpenho->setDado( "cod_entidade"    , $this->obROrcamentoEntidade->getCodigoEntidade()   );
    $obErro = $obTEmpenhoEmpenho->recuperaPorChave( $rsEmpenho, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $this->inCodPreEmpenho   = $rsEmpenho->getCampo( "cod_pre_empenho"   );
        $this->inCodCategoria    = $rsEmpenho->getCampo( "cod_categoria"     );
        $this->stDtEmpenho       = $rsEmpenho->getCampo( "dt_empenho"        );
        $this->stDtVencimento    = $rsEmpenho->getCampo( "dt_vencimento"     );
        $this->nuVlSaldoAnterior = $rsEmpenho->getCampo( "vl_saldo_anterior" );
        $this->stHora            = $rsEmpenho->getCampo( "hora"              );

        if ($this->getCodCategoria()) {
            include_once CAM_GF_EMP_MAPEAMENTO."TEmpenhoCategoriaEmpenho.class.php";
            $obTEmpenhoCategoriaEmpenho = new TEmpenhoCategoriaEmpenho;
            $stFiltro = " WHERE cod_categoria = ".$this->getCodCategoria()."";
            $obErro = $obTEmpenhoCategoriaEmpenho->recuperaTodos($rsCategoria,$stFiltro,'',$boTransacao);
            if(!$obErro->ocorreu())
                $this->stNomCategoria = $rsCategoria->getCampo('descricao');
        }

        // Se o empenho for de adiantamentos então busca a contrapartida
        if ( ($this->getCodCategoria() == 2 || $this->getCodCategoria() == 3) && !$obErro->ocorreu()) {
            include_once CAM_GF_EMP_MAPEAMENTO."TEmpenhoContrapartidaEmpenho.class.php";
            $obTEmpenhoContrapartidaEmpenho = new TEmpenhoContrapartidaEmpenho;
            $obTEmpenhoContrapartidaEmpenho->setDado( 'exercicio'   , $this->stExercicio                                );
            $obTEmpenhoContrapartidaEmpenho->setDado( 'cod_empenho' , $this->inCodEmpenho                               );
            $obTEmpenhoContrapartidaEmpenho->setDado( 'cod_entidade', $this->obROrcamentoEntidade->getCodigoEntidade()  );
            $obErro = $obTEmpenhoContrapartidaEmpenho->recuperaContrapartidaLancamento( $rsContrapartida,'', $boTransacao );
            if (!$obErro->ocorreu()) {
                $this->inCodContrapartida = $rsContrapartida->getCampo( 'conta_contrapartida'   );
                $this->stNomContrapartida = $rsContrapartida->getCampo( 'nom_conta'             );
            }
        }

        if (!$obErro->ocorreu()) {
            // Se o empenho for complementar adiciona a linha EMPENHO COMPLEMENTAR ao relatorio
            include_once CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenhoComplementar.class.php";
            $obTEmpenhoEmpenhoComplementar = new TEmpenhoEmpenhoComplementar();
            $obTEmpenhoEmpenhoComplementar->setDado( "exercicio"       , $this->stExercicio                                 );
            $obTEmpenhoEmpenhoComplementar->setDado( "cod_empenho"     , $this->inCodEmpenho                                );
            $obTEmpenhoEmpenhoComplementar->setDado( "cod_entidade"    , $this->obROrcamentoEntidade->getCodigoEntidade()   );
            $obErro = $obTEmpenhoEmpenhoComplementar->recuperaPorChave( $rsEmpenhoComplementar, $boTransacao );
        }

        if ($rsEmpenhoComplementar->getNumLinhas() == 1 && !$obErro->ocorreu()) {
            $this->boComplementar               = true;
            $this->inCodEmpenhoOriginal         = $rsEmpenhoComplementar->getCampo( 'cod_empenho_original'  );
            $this->stExercicioEmpenhoOriginal   = $rsEmpenhoComplementar->getCampo( 'exercicio'             );
        }

        if ( !$obErro->ocorreu() ) {
            $this->obROrcamentoEntidade->setExercicio( $this->stExercicio );
            $obErro = $this->obROrcamentoEntidade->consultar( $rsEntidade , $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $obErro = parent::consultar( $boTransacao );
            }
        }
    }

    return $obErro;
}

/**
    * Executa um recuperaPorChave na classe Persistente
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultarRestosAPagar($boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO     ."TEmpenhoRestosPreEmpenho.class.php"           );
    $obTEmpenhoRestosPreEmpenho = new TEmpenhoRestosPreEmpenho;
    $obErro = $this->consultar( $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obTEmpenhoRestosPreEmpenho->setDado( "cod_pre_empenho", $this->inCodPreEmpenho );
        $obTEmpenhoRestosPreEmpenho->setDado( "exercicio"      , $this->stExercicio     );
        $obErro = $obTEmpenhoRestosPreEmpenho->recuperaDespesa( $rsRecordSet, '', '', $boTransacao );
        if ( !$obErro->ocorreu() ) {
           $stDotacao = $rsRecordSet->getCampo('dotacao_formatada');
           $this->obROrcamentoDespesa->setCodDespesa( $stDotacao );
           $this->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->setNumeroOrgao( $rsRecordSet->getCampo('num_orgao'));
           $this->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->setNumeroUnidade($rsRecordSet->getCampo('num_unidade'));
        }
    }
}

/**
    * Método para Consultar o Saldo da Dotação
    * @access public
    * @param Object $rsRecordSet
    * @param String $stOrder
    * @param Object $boTransacao
    * @return Object $obErro
*/
function consultarSaldoAnterior($boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO     ."FEmpenhoSaldoAnterior.class.php"              );
    $obFEmpenhoSaldoAnterior              =  new FEmpenhoSaldoAnterior;
    if(empty($stFiltro))
        $stFiltro = null;
    if(empty($stOrder))
        $stOrder = null;

    $obFEmpenhoSaldoAnterior->setDado( "cod_despesa" , $this->obROrcamentoDespesa->getCodDespesa() );
    $obFEmpenhoSaldoAnterior->setDado( "exercicio"   , $this->obROrcamentoDespesa->getExercicio() );
    $obFEmpenhoSaldoAnterior->setDado( "cod_empenho" , $this->inCodEmpenho );
    $obErro = $obFEmpenhoSaldoAnterior->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    $this->setSaldoAnterior ( $rsRecordSet->getCampo("saldo_anterior") );

    return $obErro;
}

/**
    * Método para Recuperar os Exercícios
    * @access public
    * @param Object $rsRecordSet
    * @param String $stOrder
    * @param Object $boTransacao
    * @return Object $obErro
*/
function recuperaExercicios(&$rsRecordSet, $boTransacao = "" , $stExercicio)
{
    include_once ( CAM_GF_EMP_MAPEAMENTO     ."TEmpenhoEmpenho.class.php"                    );
    $obTEmpenhoEmpenho                    =  new TEmpenhoEmpenho;

    $obTEmpenhoEmpenho->recuperaExercicios( $rsRecordSet, $stFiltro, $boTransacao, $stExercicio);

    return $obErro;
}

/**
    * Método para Recuperar os Exercícios de RP
    * @access public
    * @param Object $rsRecordSet
    * @param String $stOrder
    * @param Object $boTransacao
    * @return Object $obErro
*/
function recuperaExerciciosRP(&$rsRecordSet, $boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO     ."TEmpenhoEmpenho.class.php"                    );
    $obTEmpenhoEmpenho                    =  new TEmpenhoEmpenho;

    $obTEmpenhoEmpenho->recuperaExerciciosRP( $rsRecordSet );

    return $obErro;
}

}
