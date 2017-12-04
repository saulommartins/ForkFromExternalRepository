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
    * Classe de Regra de Negócio Lançamento
    * Data de Criação   : 20/11/2004

    * @author Analista : Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Regra

    $Id: RContabilidadeLancamentoReceita.class.php 66028 2016-07-08 19:08:45Z michel $

    * Casos de uso: uc-02.02.05
                    uc-02.02.16
                    uc-02.01.06
                    uc-02.03.28
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GF_CONT_NEGOCIO."RContabilidadeLancamentoValor.class.php";
include_once CAM_GF_CONT_NEGOCIO."RContabilidadeDesdobramentoReceita.class.php";
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoReceita.class.php";

class RContabilidadeLancamentoReceita extends RContabilidadeLancamentoValor
{
/**
    * @var Object
    * @access Private
*/
var $obRContabilidadeLancamentoValor;
/**
    * @var Object
    * @access Private
*/
var $obROrcamentoReceita;
/**
    * @var Object
    * @access Private
*/
var $obRDesdobramentoReceita;
/**
    * @var Boolean
    * @access Private
*/
var $boEstorno;
/**
    * @access Public;
    * @var Integer
*/
var $inCountReceitaExercicio;

/**
     * @access Public
     * @param Integer $valor
*/
function setROrcamentoReceita($valor) { $this->obROrcamentoReceita = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setEstorno($valor) { $this->boEstorno   = $valor; }
/**
    * @access Public
    * @param Integer $Valor
*/
function setCountReceitaExercicio($valor) { $this->inCountReceitaExercicio = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function getROrcamentoReceita() { return $this->obROrcamentoReceita; }
/**
     * @access Public
     * @param Integer $valor
*/
function getEstorno() { return $this->boEstorno;   }
/**
    * @access Public
    * @return Integer
*/
function getCountReceitaExercicio() { return $this->inCountReceitaExercicio; }

/**
    * Método Construtor
    * @access Private
*/
function RContabilidadeLancamentoReceita()
{
    parent::RContabilidadeLancamentoValor();
    $this->obROrcamentoReceita               = new ROrcamentoReceita;
    $this->obRDesdobramentoReceita           = new RContabilidadeDesdobramentoReceita( $this->obROrcamentoReceita );
    $this->obRContabilidadeLancamento->obRContabilidadeLote->setTipo( "A" );
}

/**
    * Método para validar se ja foi realizada a implantação de saldos de balanço em um exercício informado
    * @access Private
    * @param Integer $inCodPlano
    * @param Object $boTransacao
    * @return Object $obErro
*/
function verificaImplantacaoDeSaldos($stExercicio, $boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoConta.class.php" );
    $obTContabilidadePlanoConta = new TContabilidadePlanoConta;

    $obTContabilidadePlanoConta->setDado("exercicio", $stExercicio );
    $obErro = $obTContabilidadePlanoConta->recuperaVerificaImplantacaoSaldos($rsRetorno, '','',$boTransacao);
    if ( !$obErro->ocorreu() ) {
        $retorno = $rsRetorno->getCampo('retorno');
        if ($retorno == 'true') {
            $obErro->setDescricao( "Já foram implantados os Saldos de Balanço para o exercício ".$stExercicio.".");
        }
    }

    return $obErro;
}

/**
    * Inclui Lancamento Receita no Banco de Dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluir($boTransacao = "", $boFlagTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeLancamentoReceita.class.php" );
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeLote.class.php" );
    include_once ( CAM_GF_CONT_MAPEAMENTO."FContabilidadeRealizacaoReceita.class.php" );
    $obFContabilidadeRealizacaoReceita  = new FContabilidadeRealizacaoReceita;
    $obTContabilidadeLancamentoReceita  = new TContabilidadeLancamentoReceita;
    $obTContabilidadeLote               = new TContabilidadeLote;

    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->validaAnoLote();
        if ( !$obErro->ocorreu() ) {
            $obErro = $this->verificaImplantacaoDeSaldos($this->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio()+1, $boTransacao);
            if ( !$obErro->ocorreu() ) {
                $this->obROrcamentoReceita->obROrcamentoEntidade->setCodigoEntidade( $this->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() );
                $obErro = $this->obROrcamentoReceita->verificaRelacionamentoReceitaEntidade( $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    $inCodigoReceitaPrincipal =  $this->obROrcamentoReceita->getCodReceita();
                    $this->obRDesdobramentoReceita->roROrcamentoReceitaPrincipal->setCodReceita(  $this->obROrcamentoReceita->getCodReceita() );
                    $this->obRDesdobramentoReceita->roROrcamentoReceitaPrincipal->setExercicio( $this->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio() );
                    $obErro = $this->obRDesdobramentoReceita->verificaReceitaSecundaria( $rsContaSecundaria, $boTransacao );
                    if ( !$obErro->ocorreu() and !$rsContaSecundaria->eof() ) {
                         $obErro->setDescricao( " Receita secundária não pode receber lançamento!" );
                    } else {
                        $obErro = $this->obRDesdobramentoReceita->listar( $rsReceitaSecundaria, $boTransacao );
                        $nuSomatorioReceita = 0;
                        $nuSaldo = $this->getValor();
                        if ( !$obErro->ocorreu() ) {
                            while ( !$rsReceitaSecundaria->eof()  and !$obErro->ocorreu() ) {
                                $nuValorParcela = number_format( $this->getValor() * ( $rsReceitaSecundaria->getCampo( "percentual" ) / 100) , 2, ".", "");
                                $nuSomatorioReceita += number_format( $this->getValor() * ( $rsReceitaSecundaria->getCampo( "percentual" ) / 100) , 2 , ".", "");
                                $nuSaldo -= number_format( $this->getValor() * ( $rsReceitaSecundaria->getCampo( "percentual" ) / 100 ), 2 , ".", "");
                                $inCodigoReceitaSecundaria = $rsReceitaSecundaria->getCampo( "cod_receita_secundaria" );
                                $this->obRContabilidadePlanoContaAnalitica->setCodPlano ( $this->getContaDebito() );
                                $this->obRContabilidadePlanoContaAnalitica->setExercicio( $this->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio());
                                $obErro = $this->obRContabilidadePlanoContaAnalitica->consultar( $boTransacao );
                                if ( !$obErro->ocorreu() ) {
                                    $stCodRecebimento = $this->obRContabilidadePlanoContaAnalitica->getCodEstrutural();
                                    $stCodRecebimento = str_replace( ".", "", $stCodRecebimento );
                                    $this->obROrcamentoReceita->setCodReceita( $inCodigoReceitaSecundaria );
                                    $this->obROrcamentoReceita->setExercicio( $this->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio() );
                                    $obErro = $this->obROrcamentoReceita->listar( $rsLista , '', $boTransacao );
                                    if ( !$obErro->ocorreu() ) {
                                        $stClasReceita    = $rsLista->getCampo( "mascara_classificacao" );
                                        $stClasReceita    = str_replace( ".", "", $stClasReceita );
                                        $obFContabilidadeRealizacaoReceita->setDado( "conta_recebimento" , $stCodRecebimento );
                                        $obFContabilidadeRealizacaoReceita->setDado( "clas_receita"      , $stClasReceita    );
                                        $obFContabilidadeRealizacaoReceita->setDado( "exercicio"         , $this->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio()     );
                                        $obFContabilidadeRealizacaoReceita->setDado( "valor"             , $nuValorParcela );
                                        $obFContabilidadeRealizacaoReceita->setDado( "complemento"       , $this->obRContabilidadeLancamento->getComplemento() );
                                        $obFContabilidadeRealizacaoReceita->setDado( "cod_lote"          , $this->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote() );
                                        $obFContabilidadeRealizacaoReceita->setDado( "tipo_lote"         , $this->obRContabilidadeLancamento->obRContabilidadeLote->getTipo());
                                        $obFContabilidadeRealizacaoReceita->setDado( "cod_entidade"      , $this->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() );
                                        $obFContabilidadeRealizacaoReceita->setDado( "dt_lote", $this->obRContabilidadeLancamento->obRContabilidadeLote->getDtLote() );
                                        $obFContabilidadeRealizacaoReceita->setDado( "nom_lote", $this->obRContabilidadeLancamento->obRContabilidadeLote->getNomLote() );
                                        $obFContabilidadeRealizacaoReceita->setDado( "cod_reduzido"      , $this->obROrcamentoReceita->getCodReceita() );
                                        if( $this->obRContabilidadeLancamento->obRContabilidadeHistoricoPadrao->getCodHistorico())
                                             $obFContabilidadeRealizacaoReceita->setDado( "cod_historico"     , $this->obRContabilidadeLancamento->obRContabilidadeHistoricoPadrao->getCodHistorico() );
                                        else $obFContabilidadeRealizacaoReceita->setDado( "cod_historico"     , null );
                                        $obTContabilidadeLote->setDado( "cod_lote" , $this->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote() );
                                        $obTContabilidadeLote->setDado( "tipo", $this->obRContabilidadeLancamento->obRContabilidadeLote->getTipo() );
                                        $obTContabilidadeLote->setDado( "exercicio", $this->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio() );
                                        $obTContabilidadeLote->setDado( "cod_entidade", $this->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() );
                                        $obErro = $obTContabilidadeLote->recuperaPorChave( $rsLote, $boTransacao );
                                        if ( !$obErro->ocorreu() ) {
                                            if ( $rsLote->eof() ) {
                                                $obFContabilidadeRealizacaoReceita->setDado( "cod_lote","" );
                                                $this->obRContabilidadeLancamento->obRContabilidadeLote->setCodLote( "" );
                                            } else {
                                                if ( $this->obRContabilidadeLancamento->obRContabilidadeLote->getDtLote() != $rsLote->getCampo("dt_lote") ) {
                                                    $obErro->setDescricao("O lote ".$rsLote->getCampo('cod_lote')." foi utilizado em outra data!");
                                                }
                                            }
                                            if ( !$obErro->ocorreu() ) {
                                                $obErro = $obFContabilidadeRealizacaoReceita->recuperaTodos( $rsRecordSet, "", "", $boTransacao );
                                                if ( !$obErro->ocorreu() ) {
                                                    if ( $this->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote() == ""  ) {
                                                        $obErro = $this->obRContabilidadeLancamento->obRContabilidadeLote->listarLotes( $rsCodLote, " cod_lote desc", $boTransacao );                                                                                                                                                           if ( !$obErro->ocorreu() ) {
                                                            $this->obRContabilidadeLancamento->obRContabilidadeLote->setCodLote( $rsCodLote->getCampo( "cod_lote" ) );
                                                        }
                                                    }
                                                    if ( !$obErro->ocorreu() ) {
                                                        $inSequencia = $rsRecordSet->getCampo( 'sequencia' ) ? $rsRecordSet->getCampo( 'sequencia' ) : 1;
                                                        $this->obRContabilidadeLancamento->setSequencia( $inSequencia );
                                                        $obTContabilidadeLancamentoReceita->setDado( "cod_lote"     , $this->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote() );
                                                        $obTContabilidadeLancamentoReceita->setDado( "tipo"         , $this->obRContabilidadeLancamento->obRContabilidadeLote->getTipo() );
                                                        $obTContabilidadeLancamentoReceita->setDado( "sequencia"    , $this->obRContabilidadeLancamento->getSequencia() );
                                                        $obTContabilidadeLancamentoReceita->setDado( "exercicio"    , $this->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio() );
                                                        $obTContabilidadeLancamentoReceita->setDado( "cod_entidade" , $this->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() );
                                                        $obTContabilidadeLancamentoReceita->setDado( "estorno"      , $this->boEstorno );
                                                        $obTContabilidadeLancamentoReceita->setDado( "cod_receita"  , $inCodigoReceitaSecundaria );
                                                        $obErro = $obTContabilidadeLancamentoReceita->inclusao( $boTransacao );
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                                if ( $obErro->ocorreu() ) {
                                    break;
                                }
                                $rsReceitaSecundaria->proximo();
                            }
                            if ( !$obErro->ocorreu() ) {
                                $this->obRContabilidadePlanoContaAnalitica->setCodPlano ( $this->getContaDebito() );
                                $this->obRContabilidadePlanoContaAnalitica->setExercicio( $this->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio());
                                $obErro = $this->obRContabilidadePlanoContaAnalitica->consultar( $boTransacao );
                                if ( !$obErro->ocorreu() ) {
                                    $stCodRecebimento = $this->obRContabilidadePlanoContaAnalitica->getCodEstrutural();
                                    $stCodRecebimento = str_replace( ".", "", $stCodRecebimento );
                                    $this->obROrcamentoReceita->setCodReceita( $inCodigoReceitaPrincipal );
                                    $this->obROrcamentoReceita->setExercicio( $this->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio() );
                                    $obErro = $this->obROrcamentoReceita->listar( $rsLista , '', $boTransacao );
                                    if ( !$obErro->ocorreu() ) {
                                        $stClasReceita    = $rsLista->getCampo( "mascara_classificacao" );
                                        $stClasReceita    = str_replace( ".", "", $stClasReceita );
                                        $obFContabilidadeRealizacaoReceita->setDado( "conta_recebimento" , $stCodRecebimento );
                                        $obFContabilidadeRealizacaoReceita->setDado( "clas_receita"      , $stClasReceita    );
                                        $obFContabilidadeRealizacaoReceita->setDado( "exercicio"         , $this->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio()     );
                                        $obFContabilidadeRealizacaoReceita->setDado( "valor"             , $nuSaldo );
                                        $obFContabilidadeRealizacaoReceita->setDado( "complemento"       , $this->obRContabilidadeLancamento->getComplemento() );
                                        $obFContabilidadeRealizacaoReceita->setDado( "cod_lote"          , $this->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote() );
                                        $obFContabilidadeRealizacaoReceita->setDado( "tipo_lote"         , $this->obRContabilidadeLancamento->obRContabilidadeLote->getTipo());
                                        $obFContabilidadeRealizacaoReceita->setDado( "cod_entidade"      , $this->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() );
                                        $obFContabilidadeRealizacaoReceita->setDado( "dt_lote", $this->obRContabilidadeLancamento->obRContabilidadeLote->getDtLote() );
                                        $obFContabilidadeRealizacaoReceita->setDado( "nom_lote", $this->obRContabilidadeLancamento->obRContabilidadeLote->getNomLote() );
                                        $obFContabilidadeRealizacaoReceita->setDado( "cod_reduzido"      , $this->obROrcamentoReceita->getCodReceita() );
                                        if($this->obRContabilidadeLancamento->obRContabilidadeHistoricoPadrao->getCodHistorico())
                                             $obFContabilidadeRealizacaoReceita->setDado ("cod_historico" ,  $this->obRContabilidadeLancamento->obRContabilidadeHistoricoPadrao->getCodHistorico() );
                                        else $obFContabilidadeRealizacaoReceita->setDado ("cod_historico" ,  null );
                                        $obTContabilidadeLote->setDado( "cod_lote" , $this->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote() );
                                        $obTContabilidadeLote->setDado( "tipo", $this->obRContabilidadeLancamento->obRContabilidadeLote->getTipo() );
                                        $obTContabilidadeLote->setDado( "exercicio", $this->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio() );
                                        $obTContabilidadeLote->setDado( "cod_entidade", $this->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() );
                                        $obErro = $obTContabilidadeLote->recuperaPorChave( $rsLote, $boTransacao );

                                        if ( !$obErro->ocorreu() ) {
                                            if ( $rsLote->eof() ) {
                                                $obFContabilidadeRealizacaoReceita->setDado( "cod_lote","" );
                                                $this->obRContabilidadeLancamento->obRContabilidadeLote->setCodLote( "" );
                                            } else {
                                                if ( $this->obRContabilidadeLancamento->obRContabilidadeLote->getDtLote() != $rsLote->getCampo("dt_lote") ) {
                                                    $obErro->setDescricao("O lote ".$rsLote->getCampo('cod_lote')." foi utilizado em outra data!");
                                                }
                                            }
                                            if ( !$obErro->ocorreu() ) {
                                                $obErro = $obFContabilidadeRealizacaoReceita->recuperaTodos( $rsRecordSet, "", "", $boTransacao );
                                                if ( !$obErro->ocorreu() ) {
                                                    if ( $this->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote() == "" ) {
                                                        $obErro = $this->obRContabilidadeLancamento->obRContabilidadeLote->listarLotes( $rsCodLote, " cod_lote desc", $boTransacao );
                                                        if ( !$obErro->ocorreu() ) {
                                                            $this->obRContabilidadeLancamento->obRContabilidadeLote->setCodLote( $rsCodLote->getCampo( "cod_lote" ) );
                                                        }
                                                    }
                                                    if ( !$obErro->ocorreu() ) {
                                                        $inSequencia = $rsRecordSet->getCampo( 'sequencia' ) ? $rsRecordSet->getCampo( 'sequencia' ) : 1;
                                                        $this->obRContabilidadeLancamento->setSequencia( $inSequencia );
                                                        $obTContabilidadeLancamentoReceita->setDado( "cod_lote"     , $this->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote() );
                                                        $obTContabilidadeLancamentoReceita->setDado( "tipo"         , $this->obRContabilidadeLancamento->obRContabilidadeLote->getTipo() );
                                                        $obTContabilidadeLancamentoReceita->setDado( "sequencia"    , $this->obRContabilidadeLancamento->getSequencia() );
                                                        $obTContabilidadeLancamentoReceita->setDado( "exercicio"    , $this->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio() );
                                                        $obTContabilidadeLancamentoReceita->setDado( "cod_entidade" , $this->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() );
                                                        $obTContabilidadeLancamentoReceita->setDado( "estorno"      , $this->boEstorno );
                                                        $obTContabilidadeLancamentoReceita->setDado( "cod_receita"  , $this->obROrcamentoReceita->getCodReceita() );
                                                        $obErro = $obTContabilidadeLancamentoReceita->inclusao( $boTransacao );
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTContabilidadeLancamentoReceita );

    return $obErro;
}

/**
    * Anular Receita ( inverte contas )
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterar($boTransacao = "", $boFlagTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeLancamentoReceita.class.php" );
    include_once ( CAM_GF_CONT_MAPEAMENTO."FContabilidadeEstornoRealizacaoReceita.class.php" );
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeLote.class.php" );
    $obTContabilidadeLote               = new TContabilidadeLote;
    $obFContabilidadeEstornoRealizacaoReceita = new FContabilidadeEstornoRealizacaoReceita;
    $obTContabilidadeLancamentoReceita  = new TContabilidadeLancamentoReceita;

//  $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->validaAnoLote();
        if ( !$obErro->ocorreu() ) {
            $obErro = $this->verificaImplantacaoDeSaldos($this->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio()+1, $boTransacao);
            if ( !$obErro->ocorreu() ) {
                $this->obROrcamentoReceita->obROrcamentoEntidade->setCodigoEntidade( $this->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() );
                $obErro = $this->obROrcamentoReceita->verificaRelacionamentoReceitaEntidade( $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    $inCodigoReceitaPrincipal =  $this->obROrcamentoReceita->getCodReceita();
                    $this->obRDesdobramentoReceita->roROrcamentoReceitaPrincipal->setCodReceita(  $this->obROrcamentoReceita->getCodReceita() );
                    $this->obRDesdobramentoReceita->roROrcamentoReceitaPrincipal->setExercicio( $this->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio() );
                    $obErro = $this->obRDesdobramentoReceita->verificaReceitaSecundaria( $rsContaSecundaria, $boTransacao );
                    if ( !$obErro->ocorreu() and !$rsContaSecundaria->eof() ) {
                         $obErro->setDescricao( " Receita secundária não pode receber lançamento!" );
                    } else {
                        $obErro = $this->obRDesdobramentoReceita->listar( $rsReceitaSecundaria, $boTransacao );
                        $nuSomatorioReceita = 0;
                        $nuSaldo = $this->getValor();
                        if ( !$obErro->ocorreu() ) {
                            while ( !$rsReceitaSecundaria->eof()  and !$obErro->ocorreu() ) {
                                $nuValorParcela = number_format( $this->getValor() * ( $rsReceitaSecundaria->getCampo( "percentual" ) / 100) , 2, ".", "");
                                $nuSomatorioReceita += number_format( $this->getValor() * ( $rsReceitaSecundaria->getCampo( "percentual" ) / 100) , 2 , ".", "");
                                $nuSaldo -= number_format( $this->getValor() * ( $rsReceitaSecundaria->getCampo( "percentual" ) / 100 ), 2 , ".", "");
                                $inCodigoReceitaSecundaria = $rsReceitaSecundaria->getCampo( "cod_receita_secundaria" );
                                $this->obRContabilidadePlanoContaAnalitica->setCodPlano ( $this->getContaDebito() );
                                $this->obRContabilidadePlanoContaAnalitica->setExercicio( $this->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio());
                                $obErro = $this->obRContabilidadePlanoContaAnalitica->consultar( $boTransacao );
                                if ( !$obErro->ocorreu() ) {
                                    $stCodRecebimento = $this->obRContabilidadePlanoContaAnalitica->getCodEstrutural();
                                    $stCodRecebimento = str_replace( ".", "", $stCodRecebimento );
                                    $this->obROrcamentoReceita->setCodReceita( $inCodigoReceitaSecundaria );
                                    $this->obROrcamentoReceita->setExercicio( $this->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio() );
                                    $obErro = $this->obROrcamentoReceita->listar( $rsLista , '', $boTransacao );
                                    if ( !$obErro->ocorreu() ) {
                                        $stClasReceita    = $rsLista->getCampo( "mascara_classificacao" );
                                        $stClasReceita    = str_replace( ".", "", $stClasReceita );
                                        $obFContabilidadeEstornoRealizacaoReceita->setDado( "conta_recebimento" , $stCodRecebimento );
                                        $obFContabilidadeEstornoRealizacaoReceita->setDado( "clas_receita"      , $stClasReceita    );
                                        $obFContabilidadeEstornoRealizacaoReceita->setDado( "exercicio"         , $this->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio()     );
                                        $obFContabilidadeEstornoRealizacaoReceita->setDado( "valor"             , $nuValorParcela );
                                        $obFContabilidadeEstornoRealizacaoReceita->setDado( "complemento"       , $this->obRContabilidadeLancamento->getComplemento() );
                                        $obFContabilidadeEstornoRealizacaoReceita->setDado( "cod_lote"          , $this->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote() );
                                        $obFContabilidadeEstornoRealizacaoReceita->setDado( "tipo_lote"         , $this->obRContabilidadeLancamento->obRContabilidadeLote->getTipo());
                                        $obFContabilidadeEstornoRealizacaoReceita->setDado( "cod_entidade"      , $this->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() );
                                        $obFContabilidadeEstornoRealizacaoReceita->setDado( "dt_lote", $this->obRContabilidadeLancamento->obRContabilidadeLote->getDtLote() );
                                        $obFContabilidadeEstornoRealizacaoReceita->setDado( "nom_lote", $this->obRContabilidadeLancamento->obRContabilidadeLote->getNomLote() );
                                        $obFContabilidadeEstornoRealizacaoReceita->setDado( "cod_reduzido"      , $this->obROrcamentoReceita->getCodReceita() );
                                        if($this->obRContabilidadeLancamento->obRContabilidadeHistoricoPadrao->getCodHistorico())
                                             $obFContabilidadeEstornoRealizacaoReceita->setDado ("cod_historico" ,  $this->obRContabilidadeLancamento->obRContabilidadeHistoricoPadrao->getCodHistorico() );
                                        else $obFContabilidadeEstornoRealizacaoReceita->setDado ("cod_historico" ,  null );
                                        $obTContabilidadeLote->setDado( "cod_lote" , $this->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote() );
                                        $obTContabilidadeLote->setDado( "tipo", $this->obRContabilidadeLancamento->obRContabilidadeLote->getTipo() );
                                        $obTContabilidadeLote->setDado( "exercicio", $this->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio() );
                                        $obTContabilidadeLote->setDado( "cod_entidade", $this->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() );
                                        $obErro = $obTContabilidadeLote->recuperaPorChave( $rsLote, $boTransacao );
                                        if ( !$obErro->ocorreu() ) {
                                            if ( $rsLote->eof() ) {
                                                $obFContabilidadeEstornoRealizacaoReceita->setDado( "cod_lote","" );
                                                $this->obRContabilidadeLancamento->obRContabilidadeLote->setCodLote( "" );
                                            }
                                            $obErro = $obFContabilidadeEstornoRealizacaoReceita->recuperaTodos( $rsRecordSet, "", "", $boTransacao );
                                            if ( $this->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote() == "" ) {                                                              $obErro = $this->obRContabilidadeLancamento->obRContabilidadeLote->listarLotes( $rsCodLote," cod_lote desc", $boTransacao );
                                                if ( !$obErro->ocorreu() ) {
                                                        $this->obRContabilidadeLancamento->obRContabilidadeLote->setCodLote( $rsCodLote->getCampo( "cod_lote" ) );
                                                }
                                            }
                                            if ( !$obErro->ocorreu() ) {

                                                $inSequencia = $rsRecordSet->getCampo( 'sequencia' ) ? $rsRecordSet->getCampo( 'sequencia' ) : 1;
                                                $this->obRContabilidadeLancamento->setSequencia( $inSequencia );
                                                $obTContabilidadeLancamentoReceita->setDado( "cod_lote"     , $this->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote() );
                                                $obTContabilidadeLancamentoReceita->setDado( "tipo"         , $this->obRContabilidadeLancamento->obRContabilidadeLote->getTipo() );
                                                $obTContabilidadeLancamentoReceita->setDado( "sequencia"    , $this->obRContabilidadeLancamento->getSequencia() );
                                                $obTContabilidadeLancamentoReceita->setDado( "exercicio"    , $this->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio() );
                                                $obTContabilidadeLancamentoReceita->setDado( "cod_entidade" , $this->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() );
                                                $obTContabilidadeLancamentoReceita->setDado( "estorno"      , $this->boEstorno );
                                                $obTContabilidadeLancamentoReceita->setDado( "cod_receita"  , $inCodigoReceitaSecundaria );
                                                $obErro = $obTContabilidadeLancamentoReceita->inclusao( $boTransacao );
                                            }
                                        }
                                    }
                                }
                                if ( $obErro->ocorreu() ) {
                                    break;
                                }
                                $rsReceitaSecundaria->proximo();
                            }
                            if ( !$obErro->ocorreu() ) {
                                $this->obRContabilidadePlanoContaAnalitica->setCodPlano ( $this->getContaDebito() );
                                $this->obRContabilidadePlanoContaAnalitica->setExercicio( $this->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio());
                                $obErro = $this->obRContabilidadePlanoContaAnalitica->consultar( $boTransacao );
                                if ( !$obErro->ocorreu() ) {
                                    $stCodRecebimento = $this->obRContabilidadePlanoContaAnalitica->getCodEstrutural();
                                    $stCodRecebimento = str_replace( ".", "", $stCodRecebimento );
                                    $this->obROrcamentoReceita->setCodReceita( $inCodigoReceitaPrincipal );
                                    $this->obROrcamentoReceita->setExercicio( $this->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio() );
                                    $obErro = $this->obROrcamentoReceita->listar( $rsLista , '', $boTransacao );
                                    if ( !$obErro->ocorreu() ) {
                                        $stClasReceita    = $rsLista->getCampo( "mascara_classificacao" );
                                        $stClasReceita    = str_replace( ".", "", $stClasReceita );
                                        $obFContabilidadeEstornoRealizacaoReceita->setDado( "conta_recebimento" , $stCodRecebimento );
                                        $obFContabilidadeEstornoRealizacaoReceita->setDado( "clas_receita"      , $stClasReceita    );
                                        $obFContabilidadeEstornoRealizacaoReceita->setDado( "exercicio"         , $this->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio()     );
                                        $obFContabilidadeEstornoRealizacaoReceita->setDado( "valor"             , $nuSaldo );
                                        $obFContabilidadeEstornoRealizacaoReceita->setDado( "complemento"       , $this->obRContabilidadeLancamento->getComplemento() );
                                        $obFContabilidadeEstornoRealizacaoReceita->setDado( "cod_lote"          , $this->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote() );
                                        $obFContabilidadeEstornoRealizacaoReceita->setDado( "tipo_lote"         , $this->obRContabilidadeLancamento->obRContabilidadeLote->getTipo());
                                        $obFContabilidadeEstornoRealizacaoReceita->setDado( "cod_entidade"      , $this->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() );
                                        $obFContabilidadeEstornoRealizacaoReceita->setDado( "dt_lote", $this->obRContabilidadeLancamento->obRContabilidadeLote->getDtLote() );
                                        $obFContabilidadeEstornoRealizacaoReceita->setDado( "nom_lote", $this->obRContabilidadeLancamento->obRContabilidadeLote->getNomLote() );
                                        $obFContabilidadeEstornoRealizacaoReceita->setDado( "cod_reduzido"      , $this->obROrcamentoReceita->getCodReceita() );
                                        if($this->obRContabilidadeLancamento->obRContabilidadeHistoricoPadrao->getCodHistorico())
                                             $obFContabilidadeEstornoRealizacaoReceita->setDado ("cod_historico" ,  $this->obRContabilidadeLancamento->obRContabilidadeHistoricoPadrao->getCodHistorico() );
                                        else $obFContabilidadeEstornoRealizacaoReceita->setDado ("cod_historico" ,  null );
                                        $obTContabilidadeLote->setDado( "cod_lote" , $this->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote() );
                                        $obTContabilidadeLote->setDado( "tipo", $this->obRContabilidadeLancamento->obRContabilidadeLote->getTipo() );
                                        $obTContabilidadeLote->setDado( "exercicio", $this->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio() );
                                        $obTContabilidadeLote->setDado( "cod_entidade", $this->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() );
                                        $obErro = $obTContabilidadeLote->recuperaPorChave( $rsLote, $boTransacao );
                                        if ( !$obErro->ocorreu() ) {
                                            if ( $rsLote->eof() ) {
                                                $obFContabilidadeEstornoRealizacaoReceita->setDado( "cod_lote","" );
                                                $this->obRContabilidadeLancamento->obRContabilidadeLote->setCodLote( "" );
                                            }
                                            $obErro = $obFContabilidadeEstornoRealizacaoReceita->recuperaTodos( $rsRecordSet, "", "", $boTransacao );
                                            if ( !$obErro->ocorreu() ) {
                                                if ( $this->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote() == "" ) {
                                                        $obErro = $this->obRContabilidadeLancamento->obRContabilidadeLote->listarLotes( $rsCodLote," cod_lote desc", $boTransacao );                                                                                                                                                           if ( !$obErro->ocorreu() ) {
                                                        $this->obRContabilidadeLancamento->obRContabilidadeLote->setCodLote( $rsCodLote->getCampo( "cod_lote" ) );
                                                    }                                                                                                                                       }
                                                if ( !$obErro->ocorreu() ) {
                                                    $inSequencia = $rsRecordSet->getCampo( 'sequencia' ) ? $rsRecordSet->getCampo( 'sequencia' ) : 1;
                                                    $this->obRContabilidadeLancamento->setSequencia( $inSequencia );
                                                    $obTContabilidadeLancamentoReceita->setDado( "cod_lote"     , $this->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote() );
                                                    $obTContabilidadeLancamentoReceita->setDado( "tipo"         , $this->obRContabilidadeLancamento->obRContabilidadeLote->getTipo() );
                                                    $obTContabilidadeLancamentoReceita->setDado( "sequencia"    , $this->obRContabilidadeLancamento->getSequencia() );
                                                    $obTContabilidadeLancamentoReceita->setDado( "exercicio"    , $this->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio() );
                                                    $obTContabilidadeLancamentoReceita->setDado( "cod_entidade" , $this->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() );
                                                    $obTContabilidadeLancamentoReceita->setDado( "estorno"      , $this->boEstorno );
                                                    $obTContabilidadeLancamentoReceita->setDado( "cod_receita"  , $this->obROrcamentoReceita->getCodReceita() );
                                                    $obErro = $obTContabilidadeLancamentoReceita->inclusao( $boTransacao );
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTContabilidadeLancamentoReceita );

    return $obErro;
}

function validaAnoLote()
{
    ;
    $obErro = new Erro;
    $stDataLote = $this->obRContabilidadeLancamento->obRContabilidadeLote->getDtLote();
    $arDataLote = explode( "/", $stDataLote );
    if ( $arDataLote[2] != Sessao::getExercicio() ) {
        $obErro->setDescricao( " O ano deve ser o mesmo do exercício corrente! " );
    }

    return $obErro;
}

/**
    * Exclui dados do LancamentoValor do banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
*/
function excluir($boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeLancamentoReceita.class.php" );
    $obTContabilidadeLancamentoReceita = new TContabilidadeLancamentoReceita;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $obTContabilidadeLancamentoReceita->setDado( "sequencia"    , $this->obRContabilidadeLancamento->inSequencia );
        $obTContabilidadeLancamentoReceita->setDado( "exercicio"    , $this->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio()   );
        $obTContabilidadeLancamentoReceita->setDado( "cod_lote"     , $this->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote()     );
        $obTContabilidadeLancamentoReceita->setDado( "tipo"         , $this->obRContabilidadeLancamento->obRContabilidadeLote->getTipo()        );
        $obTContabilidadeLancamentoReceita->setDado( "cod_entidade" , $this->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() );
        $obTContabilidadeLancamentoReceita->setDado( "cod_receita"  , $this->obROrcamentoReceita->getCodReceita() );
        $obErro = $obTContabilidadeLancamentoReceita->exclusao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTContabilidadeLancamentoReceitaobTContabilidadeLote);

    return $obErro;
}

/**
    * Executa um recuperaPorChave na classe Persistente
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultar($boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeLancamentoReceita.class.php" );
    $obTContabilidadeLancamentoReceita = new TContabilidadeLancamentoReceita;

    $obErro = parent::consultar( $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obTContabilidadeLancamentoReceita->setDado( "sequencia"    , $this->obRContabilidadeLancamento->inSequencia );
        $obTContabilidadeLancamentoReceita->setDado( "exercicio"    , $this->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio()   );
        $obTContabilidadeLancamentoReceita->setDado( "cod_lote"     , $this->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote()     );
        $obTContabilidadeLancamentoReceita->setDado( "tipo"         , $this->obRContabilidadeLancamento->obRContabilidadeLote->getTipo()        );
        $obTContabilidadeLancamentoReceita->setDado( "cod_entidade" , $this->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() );
        $obTContabilidadeLancamentoReceita->setDado( "cod_receita"  , $this->obROrcamentoReceita->getCodReceita() );
        $obErro = $obTContabilidadeLancamentoReceita->recuperaPorChave( $rsRecordSet, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->boEstorno = $rsRecordSet->getCampo( "estorno" );
            $this->obROrcamentoReceita->setExercicio( $this->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio() );
            $this->obROrcamentoReceita->listar( $rsRecordSet, $boTransacao );
            $this->obROrcamentoReceita->obROrcamentoClassificacaoReceita->setDescricao( $rsRecordSet->getCampo( "descricao" ) );
       }
    }

    return $obErro;
}

/**
    * Lista todos os Lancamentos de acordo com o filtro
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listar(&$rsLista, $stOrder = "", $obTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeLancamentoReceita.class.php" );
    $obTContabilidadeLancamentoReceita = new TContabilidadeLancamentoReceita;

    $stFiltro = "";

    if( $this->obRContabilidadeLancamento->getSequencia() )
        $stFiltro .= " l.sequencia = ".$this->obRContabilidadeLancamento->getSequencia()." AND ";
    if( $this->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio() )
        $stFiltro .= " l.exercicio = '".$this->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio()."' AND ";
    if( $this->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote() )
        $stFiltro .= " l.cod_lote = ". $this->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote()." AND  ";
    if( $this->obRContabilidadeLancamento->obRContabilidadeLote->getTipo() )
        $stFiltro .= " l.tipo = '".$this->obRContabilidadeLancamento->obRContabilidadeLote->getTipo()."' AND ";
    if( $this->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() )
        $stFiltro .= " l.cod_entidade = ".$this->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade()." AND ";
    if( $this->obRContabilidadeLancamento->obRContabilidadeHistoricoPadrao->getCodHistorico() )
        $stFiltro .= " l.tipo_valor = ".$this->stTipoValor. " AND ";
    if( $this->nuValor )
        $stFiltro .= " la.vl_lancamento = '%".$this->nuValor."%' AND ";
    if( $this->boEstorno )
        $stFiltro .= " lr.estorno = true AND ";
    if( $this->obROrcamentoReceita->getCodReceita() )
        $stFilyto .= " lr.cod_receita = ".$this->obROrcamentoReceita->getCodReceita()." AND ";
    if ( $this->obRContabilidadeLancamento->obRContabilidadeLote->getDtLoteInicial() and $this->obRContabilidadeLancamento->obRContabilidadeLote->getDtLoteTermino() ) {
        $stFiltro .= " dt_lote between to_date('".$this->obRContabilidadeLancamento->obRContabilidadeLote->getDtLoteInicial()."', 'dd/mm/yyyy') ";
        $stFiltro .= "and to_date('".$this->obRContabilidadeLancamento->obRContabilidadeLote->getDtLoteTermino(). "', 'dd/mm/yyyy') AND ";
    }
    $stFiltro = ($stFiltro)? " AND ".substr($stFiltro, 0, strlen($stFiltro)-4) : "";
    $obErro = $obTContabilidadeLancamentoReceita->recuperaRelacionamento( $rsLista, $stFiltro, $stOrder, $obTransacao );

    return $obErro;
}

/**
    * Executa um listar para contar quantas receitas existem na contabilidade_lancamento_receita no exercicio atual
    * @access public
    * @return Object $obErro
*/
function consultarExistenciaReceita($boTransacao = "")
{
    include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeLancamentoReceita.class.php";
    $obTContabilidadeLancamentoReceita = new TContabilidadeLancamentoReceita;

    $obTContabilidadeLancamentoReceita->setDado( "exercicio", $this->obRContabilidadeLancamento->obRContabilidadeLote->getExercicio() );
    if($this->obROrcamentoReceita->getCodReceita())
        $obTContabilidadeLancamentoReceita->setDado( "cod_receita", $this->obROrcamentoReceita->getCodReceita() );

    $obErro = $obTContabilidadeLancamentoReceita->recuperaExistenciaReceita( $rsRecordSet, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $this->inCountReceitaExercicio = $rsRecordSet->getCampo( "total" );
    }

    return $obErro;
}

}
