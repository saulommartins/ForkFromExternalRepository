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
    * Classe de Regra de Negócio para Arrecadacao
    * Data de Criação   : 20/10/2005

    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Regra

    $Id: RTesourariaArrecadacao.class.php 64153 2015-12-09 19:16:02Z evandro $

    $Revision: 31732 $
    $Name$
    $Autor:$
    $Date: 2007-08-14 18:43:59 -0300 (Ter, 14 Ago 2007) $

    * Casos de uso: uc-02.04.04 , uc-02.04.25
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GF_TES_NEGOCIO    ."RTesourariaAutenticacao.class.php"           );
include_once ( CAM_GF_ORC_NEGOCIO    ."ROrcamentoEntidade.class.php"              );
include_once ( CAM_GF_ORC_NEGOCIO    ."ROrcamentoReceita.class.php"               );
include_once ( CAM_GF_CONT_NEGOCIO   ."RContabilidadePlanoBanco.class.php"        );
include_once ( CAM_GT_ARR_NEGOCIO    ."RARRCarne.class.php"                       );

/**
    * Classe de Regra de Assinatura
    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Anderson R. M. Buzo
*/
class RTesourariaArrecadacao
{
/*
    * @var Object
    * @access Private
*/
var $obRTesourariaAutenticacao;
/*
    * @var Object
    * @access Private
*/
var $roRTesourariaBoletim;
/*
    * @var Object
    * @access Private
*/
var $obROrcamentoEntidade;
/*
    * @var Object
    * @access Private
*/
var $obRContabilidadePlanoBanco;
/*
    * @var Object
    * @access Private
*/
var $obRARRCarne;
/*
    * @var Object
    * @access Private
*/
var $obROrcamentoReceita;
/*
    * @var Object
    * @access Private
*/
var $obROrcamentoReceitaDedutora;
/*
    * @var Integer
    * @access Private
*/
var $inCodArrecadacao;
/*
    * @var String
    * @access Private
*/
var $stTimestampArrecadacao;
/*
    * @var String
    * @access Private
*/
var $stTimestampEstornada;
/*
    * @var String
    * @access Private
*/
var $stObservacao;
/*
    * @var String
    * @access Private
*/
var $stObservacaoEstornada;
/*
    * @var String
    * @access Private
*/
var $nuVlArrecadacao;
/*
    * @var String
    * @access Private
*/
var $nuVlEstornado;
/*
    * @var String
    * @access Private
*/
var $nuVlDeducaoEstornado;
var $stDtInicial;
var $stDtFinal;
var $boDevolucao;

/*
    * @access Public
    * @param Object $valor
*/
function setRTesourariaAutenticacao($valor) { $this->obRTesourariaAutenticacao                = $valor; }
/*
    * @access Public
    * @param Object $valor
*/
function setROrcamentoEntidade($valor) { $this->obROrcamentoEntidade              = $valor; }
/*
    * @access Public
    * @param Object $valor
*/
function setRContabilidadePlanoBanco($valor) { $this->obRContabilidadePlanoBanco        = $valor; }
/*
    * @access Public
    * @param Object $valor
*/
function setRARRCarne($valor) { $this->obRARRCarne                       = $valor; }
/*
    * @access Public
    * @param Integer $valor
*/
function setCodArrecadacao($valor) { $this->inCodArrecadacao                  = $valor; }
/*
    * @access Public
    * @param String $valor
*/
function setTimestampArrecadacao($valor) { $this->stTimestampArrecadacao            = $valor; }
/*
    * @access Public
    * @param String $valor
*/
function setTimestampEstornada($valor) { $this->stTimestampEstornada              = $valor; }
/*
    * @access Public
    * @param String $valor
*/
function setObservacao($valor) { $this->stObservacao                      = $valor; }
/*
    * @access Public
    * @param String $valor
*/
function setObservacaoEstornada($valor) { $this->stObservacaoEstornada             = $valor; }
/*
    * @access Public
    * @param String $valor
*/
function setVlArrecadacao($valor) { $this->nuVlArrecadacao                   = $valor; }
/*
    * @access Public
    * @param String $valor
*/
function setVlEstornado($valor) { $this->nuVlEstornado                     = $valor; }
/*
    * @access Public
    * @param String $valor
*/
function setVlDeducao($valor) { $this->nuVlDeducao                       = $valor; }
/*
    * @access Public
    * @param String $valor
*/
function setVlDeducaoEstornado($valor) { $this->nuVlDeducaoEstornado              = $valor; }
function setDtInicial($valor) { $this->stDtInicial                       = $valor; }
function setDtFinal($valor) { $this->stDtFinal                         = $valor; }
function setDevolucao($valor) { $this->boDevolucao                       = $valor; }

/*
    * @access Public
    * @return Object
*/
function getRTesourariaAutenticacao() { return $this->obRTesourariaAutenticacao;                }
/*
    * @access Public
    * @return Object
*/
function getROrcamentoEntidade() { return $this->obROrcamentoEntidade;              }
/*
    * @access Public
    * @return Object
*/
function getRContabilidadePlanoBanco() { return $this->obRContabilidadePlanoBanco;        }
/*
    * @access Public
    * @return Object
*/
function getRARRCarne() { return $this->obRARRCarne;                       }
/*
    * @access Public
    * @return Integer $valor
*/
function getCodArrecadacao() { return $this->inCodArrecadacao;                  }
/*
    * @access Public
    * @return String
*/
function getTimestampArrecadacao() { return $this->stTimestampArrecadacao;            }
/*
    * @access Public
    * @return String
*/
function getTimestampEstornada() { return $this->stTimestampEstornada;              }
/*
    * @access Public
    * @return String
*/
function getObservacao() { return $this->stObservacao;                      }
/*
    * @access Public
    * @return String
*/
function getObservacaoEstornada() { return $this->stObservacaoEstornada;             }
/*
    * @access Public
    * @return String
*/
function getVlArrecadacao() { return $this->nuVlArrecadacao;                   }
/*
    * @access Public
    * @return String
*/
function getVlEstornado() { return $this->nuVlEstornado;                   }
/*
    * @access Public
    * @return String
*/
function getVlDeducaoEstornado() { return $this->nuVlDeducaoEstornado;            }
function getDevolucao() { return $this->boDevolucao;                     }

/**
    * Método Construtor
    * @access Private
*/
function RTesourariaArrecadacao(&$roRTesourariaBoletim)
{
    $this->obRTesourariaAutenticacao         =  new RTesourariaAutenticacao();
    $this->obROrcamentoEntidade              =  new ROrcamentoEntidade();
    $this->obROrcamentoReceita               =  new ROrcamentoReceita();
    $this->obROrcamentoReceitaDedutora       =  new ROrcamentoReceita();
    $this->obRContabilidadePlanoBanco        =  new RContabilidadePlanoBanco();
    $this->obRARRCarne                       =  new RARRCarne();
    $this->roRTesourariaBoletim              =  &$roRTesourariaBoletim;
}

/**
    * Salva dados no banco de dados
    * @access Private
    * @param Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluirArrecadacaoReceita($boTransacao = "")
{
    include_once( CAM_GF_TES_MAPEAMENTO."TTesourariaArrecadacaoReceita.class.php" );
    include_once( CAM_GF_TES_MAPEAMENTO."TTesourariaArrecadacaoReceitaDedutora.class.php" );
    $obTTesourariaArrecadacaoReceita         = new TTesourariaArrecadacaoReceita();
    $obTTesourariaArrecadacaoReceitaDedutora = new TTesourariaArrecadacaoReceitaDedutora();
    $obTTesourariaArrecadacaoReceita->setDado( "cod_arrecadacao"       , $this->inCodArrecadacao                            );
    $obTTesourariaArrecadacaoReceita->setDado( "exercicio"             , $this->roRTesourariaBoletim->getExercicio()        );
    $obTTesourariaArrecadacaoReceita->setDado( "cod_receita"           , $this->obROrcamentoReceita->getCodReceita()        );
    $obTTesourariaArrecadacaoReceita->setDado( "timestamp_arrecadacao" , $this->stTimestampArrecadacao                      );
    $obTTesourariaArrecadacaoReceita->setDado( "vl_arrecadacao"        , $this->nuVlArrecadacao                             );
    $obErro = $obTTesourariaArrecadacaoReceita->inclusao( $boTransacao );
    if ( !$obErro->ocorreu() AND $this->obROrcamentoReceitaDedutora->getCodReceita()) {
        $obTTesourariaArrecadacaoReceitaDedutora->setDado( "cod_arrecadacao"       , $this->inCodArrecadacao                            );
        $obTTesourariaArrecadacaoReceitaDedutora->setDado( "cod_receita"           , $this->obROrcamentoReceita->getCodReceita()        );
        $obTTesourariaArrecadacaoReceitaDedutora->setDado( "cod_receita_dedutora"  , $this->obROrcamentoReceitaDedutora->getCodReceita());
        $obTTesourariaArrecadacaoReceitaDedutora->setDado( "exercicio"             , $this->roRTesourariaBoletim->getExercicio()        );
        $obTTesourariaArrecadacaoReceitaDedutora->setDado( "timestamp_arrecadacao" , $this->stTimestampArrecadacao                      );
        $obTTesourariaArrecadacaoReceitaDedutora->setDado( "vl_deducao"            , $this->nuVlDeducao                                 );
        $obErro = $obTTesourariaArrecadacaoReceitaDedutora->inclusao( $boTransacao );
    }

    return $obErro;
}

/**
    * Salva dados no banco de dados
    * @access Private
    * @param Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluirArrecadacaoCarne($boTransacao = "")
{
    include_once( CAM_GF_TES_MAPEAMENTO."TTesourariaArrecadacaoCarne.class.php" );
    $obTTesourariaArrecadacaoCarne = new TTesourariaArrecadacaoCarne();
    $obTTesourariaArrecadacaoCarne->setDado( "cod_arrecadacao"       , $this->inCodArrecadacao                                );
    $obTTesourariaArrecadacaoCarne->setDado( "exercicio"             , $this->roRTesourariaBoletim->getExercicio()            );
    $obTTesourariaArrecadacaoCarne->setDado( "numeracao"             , $this->obRARRCarne->getNumeracao()                     );
    $obTTesourariaArrecadacaoCarne->setDado( "cod_convenio"          , $this->obRARRCarne->obRMONConvenio->getCodigoConvenio());
    $obTTesourariaArrecadacaoCarne->setDado( "timestamp_arrecadacao" , $this->stTimestampArrecadacao                          );
    $obErro = $obTTesourariaArrecadacaoCarne->inclusao( $boTransacao );
    if (!$obErro->ocorreu()) {
        $obErro = $this->incluirArrecadacaoReceita( $boTransacao );
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
    include_once ( CAM_FW_BANCO_DADOS    ."Transacao.class.php"              );
    include_once ( CAM_GF_TES_MAPEAMENTO ."TTesourariaArrecadacao.class.php" );
    include_once( CAM_GF_CONT_NEGOCIO."RContabilidadeDesdobramentoReceita.class.php" );

    $obTransacao =  new Transacao();
    $obTTesourariaArrecadacao = new TTesourariaArrecadacao();
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {

        $obErro = $this->roRTesourariaBoletim->incluir( $boTransacao );

        if ( !$obErro->ocorreu() ) {
            $obRDesdobramentoReceita = new RContabilidadeDesdobramentoReceita( $this->obROrcamentoReceita );
            $obErro = $obRDesdobramentoReceita->verificaReceitaSecundaria( $rsContaSecundaria, $boTransacao  );
            if ( !$obErro->ocorreu() and !$rsContaSecundaria->eof() ) {
                $obErro->setDescricao( 'Receita secundária não pode receber lançamento' );
            }
        }

        if( !$obErro->ocorreu() )
            $obErro = $obTTesourariaArrecadacao->proximoCod( $this->inCodArrecadacao, $boTransacao );

        if ( !$obErro->ocorreu() ) {
            if ($this->getDevolucao()) {
                $this->obRTesourariaAutenticacao->setTipo("EA");
            } else {
                $this->obRTesourariaAutenticacao->setTipo("A");
            }
            $this->obRTesourariaAutenticacao->obRTesourariaConfiguracao->setExercicio($this->roRTesourariaBoletim->getExercicio());
            $this->obRTesourariaAutenticacao->setDataAutenticacao( $this->roRTesourariaBoletim->getDataBoletim() );
            $obErro = $this->obRTesourariaAutenticacao->autenticar($boTransacao);
            if ( !$obErro->ocorreu() ) {
                $obTTesourariaArrecadacao->setDado( "cod_arrecadacao"       , $this->inCodArrecadacao                          );
                $obTTesourariaArrecadacao->setDado( "exercicio"             , $this->roRTesourariaBoletim->getExercicio()      );
                $obTTesourariaArrecadacao->setDado( "timestamp_arrecadacao" , $this->stTimestampArrecadacao                    );
                $obTTesourariaArrecadacao->setDado( 'cod_autenticacao'      , $this->obRTesourariaAutenticacao->getCodAutenticacao()  );
                $obTTesourariaArrecadacao->setDado( 'dt_autenticacao'       , $this->roRTesourariaBoletim->getDataBoletim()    );
                $obTTesourariaArrecadacao->setDado( "cod_boletim"           , $this->roRTesourariaBoletim->getCodBoletim()     );
                $obTTesourariaArrecadacao->setDado( "cod_terminal"          , $this->roRTesourariaBoletim->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->getCodTerminal()       );
                $obTTesourariaArrecadacao->setDado( "timestamp_terminal"    , $this->roRTesourariaBoletim->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->getTimestampTerminal() );
                $obTTesourariaArrecadacao->setDado( "cgm_usuario"           , $this->roRTesourariaBoletim->obRTesourariaUsuarioTerminal->obRCGM->getNumCGM() );
                $obTTesourariaArrecadacao->setDado( "timestamp_usuario"     , $this->roRTesourariaBoletim->obRTesourariaUsuarioTerminal->getTimestampUsuario() );
                $obTTesourariaArrecadacao->setDado( "cod_plano"             , $this->obRContabilidadePlanoBanco->getCodPlano() );
                $obTTesourariaArrecadacao->setDado( "cod_entidade"          , $this->obROrcamentoEntidade->getCodigoEntidade() );
                $obTTesourariaArrecadacao->setDado( "observacao"            , $this->stObservacao                              );
                if($this->getDevolucao())
                    $obTTesourariaArrecadacao->setDado( "devolucao"         , true );
                    $obErro = $obTTesourariaArrecadacao->inclusao( $boTransacao );
            }
        }
    }

    if ( !$obErro->ocorreu() ) {
        if ( $this->obRARRCarne->getNumeracao() ) {
            $obErro = $this->incluirArrecadacaoCarne( $boTransacao );
        } elseif ( $this->obROrcamentoReceita->getCodReceita() ) {
            $obErro = $this->incluirArrecadacaoReceita( $boTransacao );
        }
        if ( !$obErro->ocorreu() ) {
            $this->montaDescricaoAutenticacao( $boTransacao );
        }
    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTTesourariaArrecadacao );

    return $obErro;
}

/**
    * Estorna dados da Assinatura do banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function estornar($boTransacao = "")
{
    include_once ( CAM_FW_BANCO_DADOS    ."Transacao.class.php"                       );
    include_once ( CAM_GF_TES_MAPEAMENTO ."TTesourariaArrecadacaoEstornada.class.php" );
    include_once ( CAM_GF_TES_MAPEAMENTO ."TTesourariaArrecadacaoEstornadaReceita.class.php" );
    include_once ( CAM_GF_TES_MAPEAMENTO ."TTesourariaArrecadacaoReceitaDedutoraEstornada.class.php" );
    $obTransacao     =  new Transacao();
    $boFlagTransacao = false;
    $obTTesourariaArrecadacaoEstornada = new TTesourariaArrecadacaoEstornada();
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $obErro = $this->roRTesourariaBoletim->buscarCodigoBoletim( $inCodBoletim, $stDtBoletim, $boTransacao );
        if ( $obErro->ocorreu() ) {
            if ( $obErro->getDescricao() != 'O boletim para a data atual já está fechado!' ) {
                $obErro = $this->roRTesourariaBoletim->listar( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
                if ( !$obErro->ocorreu() and !$rsRecordSet->eof() ) {
                    if ( $rsRecordSet->getCampo( "situacao" ) != "liberado" ) {
                        $obErro = new Erro;
                    } else {
                        $obErro->setDescricao( "O boletim correspondente a esta arrecadação já foi liberado para a contabilidade!" );
                    }
                }
            }
        }

        $this->roRTesourariaBoletim->setCodBoletim ( $inCodBoletim );
        $this->roRTesourariaBoletim->setDataBoletim( $stDtBoletim  );

        list( $stDia, $stMes, $stAno ) = explode( '/', $stDtBoletim );
        $stTimestampEstorno = ($stDtBoletim == date('d/m/Y')) ? date( 'Y-m-d H:i:s.ms' ) : $stAno.'-'.$stMes.'-'.$stDia.' '.date('H:i:s.ms');
        list( $stAnoA, $stMesA, $stDiaA) = explode('-',substr($this->getTimestampArrecadacao(),0,10));
        $stDtArrecadacao = $stDiaA.'/'.$stMesA.'/'.$stAnoA;

        if (sistemaLegado::comparaDatas ($stDtArrecadacao, $stDtBoletim)) {
            $obErro->setDescricao("Impossível estornar em boletim de data anterior à arrecadação.");
        }

        if(!$this->setTimestampEstornada)
            $this->setTimestampEstornada( $stTimestampEstorno );

        if ( !$obErro->ocorreu() ) {
            $this->obRTesourariaAutenticacao->setTipo("EA");
            $this->obRTesourariaAutenticacao->setDataAutenticacao( $this->roRTesourariaBoletim->getDataBoletim() );
            $this->obRTesourariaAutenticacao->obRTesourariaConfiguracao->setExercicio($this->roRTesourariaBoletim->getExercicio());
            $obErro = $this->obRTesourariaAutenticacao->autenticar($boTransacao);

            if ( !$obErro->ocorreu() ) {
                $obTTesourariaArrecadacaoEstornada->setDado( "cod_arrecadacao"      , $this->inCodArrecadacao                     );
                $obTTesourariaArrecadacaoEstornada->setDado( "exercicio"            , $this->roRTesourariaBoletim->getExercicio() );
                $obTTesourariaArrecadacaoEstornada->setDado( "timestamp_arrecadacao", $this->stTimestampArrecadacao               );
                $obTTesourariaArrecadacaoEstornada->setDado( "timestamp_estornada"  , $this->stTimestampEstornada                 );
                $obTTesourariaArrecadacaoEstornada->setDado( 'cod_autenticacao'     , $this->obRTesourariaAutenticacao->getCodAutenticacao()  );
                $obTTesourariaArrecadacaoEstornada->setDado( 'dt_autenticacao'      , $this->obRTesourariaAutenticacao->getDataAutenticacao()  );
                $obTTesourariaArrecadacaoEstornada->setDado( "cod_terminal"         , $this->roRTesourariaBoletim->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->getCodTerminal()       );
                $obTTesourariaArrecadacaoEstornada->setDado( "timestamp_terminal"   , $this->roRTesourariaBoletim->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->getTimestampTerminal() );
                $obTTesourariaArrecadacaoEstornada->setDado( "cgm_usuario"          , $this->roRTesourariaBoletim->obRTesourariaUsuarioTerminal->obRCGM->getNumCGM()     );
                $obTTesourariaArrecadacaoEstornada->setDado( "timestamp_usuario"    , $this->roRTesourariaBoletim->obRTesourariaUsuarioTerminal->getTimestampUsuario() );
                $obTTesourariaArrecadacaoEstornada->setDado( "observacao"           , $this->stObservacaoEstornada                );
                $obTTesourariaArrecadacaoEstornada->setDado( "cod_entidade"         , $this->roRTesourariaBoletim->obROrcamentoEntidade->getCodigoEntidade() );
                $obTTesourariaArrecadacaoEstornada->setDado( "cod_boletim"          , $this->roRTesourariaBoletim->getCodBoletim() );
                $obErro = $obTTesourariaArrecadacaoEstornada->inclusao( $boTransacao );

                if ( !$obErro->ocorreu() ) {
                    $obTTesourariaArrecadacaoEstornadaReceita   = new TTesourariaArrecadacaoEstornadaReceita();
                    $obTTesourariaArrecadacaoEstornadaReceita->setDado('cod_arrecadacao'       , $this->inCodArrecadacao );
                    $obTTesourariaArrecadacaoEstornadaReceita->setDado('exercicio'             , $this->roRTesourariaBoletim->getExercicio() );
                    $obTTesourariaArrecadacaoEstornadaReceita->setDado('timestamp_arrecadacao' , $this->stTimestampArrecadacao );
                    $obTTesourariaArrecadacaoEstornadaReceita->setDado('timestamp_estornada'   , $this->stTimestampEstornada );
                    $obTTesourariaArrecadacaoEstornadaReceita->setDado('cod_receita'           , $this->obROrcamentoReceita->getCodReceita() );
                    $obTTesourariaArrecadacaoEstornadaReceita->setDado('vl_estornado'          , $this->getVlEstornado() );
                    $obErro = $obTTesourariaArrecadacaoEstornadaReceita->inclusao( $boTransacao );
                    if ( !$obErro->ocorreu() AND $this->obROrcamentoReceitaDedutora->getCodReceita() AND $this->getVlDeducaoEstornado() > 0.00 ) {
                        $obTTesourariaArrecadacaoReceitaDedutoraEstornada   = new TTesourariaArrecadacaoReceitaDedutoraEstornada();
                        $obTTesourariaArrecadacaoReceitaDedutoraEstornada->setDado('cod_arrecadacao'              , $this->inCodArrecadacao );
                        $obTTesourariaArrecadacaoReceitaDedutoraEstornada->setDado('cod_receita'                  , $this->obROrcamentoReceita->getCodReceita() );
                        $obTTesourariaArrecadacaoReceitaDedutoraEstornada->setDado('cod_receita_dedutora'         , $this->obROrcamentoReceitaDedutora->getCodReceita() );
                        $obTTesourariaArrecadacaoReceitaDedutoraEstornada->setDado('exercicio'                    , $this->roRTesourariaBoletim->getExercicio() );
                        $obTTesourariaArrecadacaoReceitaDedutoraEstornada->setDado('timestamp_arrecadacao'        , $this->stTimestampArrecadacao );
                        $obTTesourariaArrecadacaoReceitaDedutoraEstornada->setDado('timestamp_estornada'          , $this->stTimestampEstornada );
                        $obTTesourariaArrecadacaoReceitaDedutoraEstornada->setDado('timestamp_dedutora_estornada' , $stTimestampEstorno            );
                        $obTTesourariaArrecadacaoReceitaDedutoraEstornada->setDado('vl_estornado'                 , $this->getVlDeducaoEstornado() );
                        $obErro = $obTTesourariaArrecadacaoReceitaDedutoraEstornada->inclusao( $boTransacao );
                    }
                    if ( !$obErro->ocorreu() ) {
                        $this->montaDescricaoAutenticacao( $boTransacao );
                    }
                }
            }
        }
    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro , $obTTesourariaArrecadacaoEstornada );

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
function listarArrecadacaoNaoEstornadaReceita(&$rsRecordSet, $stOrder = "", $boTransacao = "")
{
    include_once ( CAM_GF_TES_MAPEAMENTO ."TTesourariaArrecadacao.class.php" );
    $obTTesourariaArrecadacao = new TTesourariaArrecadacao();
    if( $this->obROrcamentoReceita->getCodReceita() )
        $stFiltro .= " TBL.cod_receita = ".$this->obROrcamentoReceita->getCodReceita()." AND ";
    if( $this->obROrcamentoReceitaDedutora->getCodReceita() )
        $stFiltro .= " TBL.cod_receita_dedutora = ".$this->obROrcamentoReceitaDedutora->getCodReceita()." AND ";
    if( $this->roRTesourariaBoletim->getExercicio() )
        $stFiltro .= " TBL.exercicio = '".$this->roRTesourariaBoletim->getExercicio()."' AND ";
    if( $this->roRTesourariaBoletim->getCodBoletim() )
        $stFiltro .= " TBL.cod_boletim = ".$this->roRTesourariaBoletim->getCodBoletim()." AND ";
    if( $this->obROrcamentoEntidade->getCodigoEntidade() )
        $stFiltro .= " TBL.cod_entidade IN( ".$this->obROrcamentoEntidade->getCodigoEntidade()." ) AND ";
    if( $this->obRContabilidadePlanoBanco->getCodPlano() )
        $stFiltro .= " TBL.cod_plano = ".$this->obRContabilidadePlanoBanco->getCodPlano()." AND ";
    if( $this->roRTesourariaBoletim->getDataBoletim() )
        $stFiltro .= " TBL.dt_boletim = TO_DATE( '".$this->roRTesourariaBoletim->getDataBoletim()."', 'dd/mm/yyyy' ) AND ";
    if( $this->stTimestampArrecadacao )
        $stFiltro .= " TO_DATE( TBL.timestamp_arrecadacao, 'yyyy-mm-dd' ) = TO_DATE( '".$this->stTimestampArrecadacao."', 'dd/mm/yyyy' ) AND ";
    if ($this->stDtInicial && $this->stDtFinal) {
        $stFiltro .= " TO_DATE(TO_CHAR(tbl.timestamp_arrecadacao,'dd/mm/yyyy'),'dd/mm/yyyy') BETWEEN TO_DATE('".$this->stDtInicial."','dd/mm/yyyy') \n";
        $stFiltro .= "                                                                           AND TO_DATE('".$this->stDtFinal."'  ,'dd/mm/yyyy') AND ";
    }

    $stFiltro .= "  \n NOT EXISTS ( SELECT  bl.exercicio
                                           ,bl.cod_entidade
                                           ,bl.timestamp_arrecadacao
                                           ,bl.cod_arrecadacao
                                      FROM tesouraria.boletim_lote_arrecadacao as bl
                                     WHERE bl.cod_entidade          = tbl.cod_entidade
                                       AND bl.exercicio             = tbl.exercicio
                                       AND bl.cod_arrecadacao       = tbl.cod_arrecadacao
                                       AND bl.timestamp_arrecadacao = tbl.timestamp_arrecadacao
                               ) AND ";

    $stFiltro .= "  \n NOT ExISTS ( SELECT aopr.cod_arrecadacao
                                      FROM tesouraria.arrecadacao_ordem_pagamento_retencao as aopr
                                     WHERE aopr.cod_entidade          = tbl.cod_entidade
                                       AND aopr.exercicio             = tbl.exercicio
                                       AND aopr.cod_arrecadacao       = tbl.cod_arrecadacao
                                       AND aopr.timestamp_arrecadacao = tbl.timestamp_arrecadacao
                               ) AND ";

    $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";

    $obErro = $obTTesourariaArrecadacao->recuperaArrecadacaoNaoEstornadaReceita( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

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
function listarArrecadacaoNaoEstornadaCarne(&$rsRecordSet, $stOrder = "", $boTransacao = "")
{
    $stFiltro = " AND TAC.cod_arrecadacao IS NOT NULL ";
    $obErro = $this->listarArrecadacaoNaoEstornada( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

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
function listarArrecadacaoNaoEstornada(&$rsRecordSet, $stFiltro = "", $stOrder = "", $boTransacao = "")
{
    include_once ( CAM_GF_TES_MAPEAMENTO ."TTesourariaArrecadacao.class.php" );
    $obTTesourariaArrecadacao = new TTesourariaArrecadacao();
    if( $this->obRARRCarne->getNumeracao() )
        $stFiltro .= " AND TAC.numeracao = '".$this->obRARRCarne->getNumeracao()."' ";
    if( $this->obROrcamentoReceita->getCodReceita() )
        $stFiltro .= " AND TAR.cod_receita = ".$this->obROrcamentoReceita->getCodReceita()." ";
    if( $this->obROrcamentoReceitaDedutora->getCodReceita() )
        $stFiltro .= " AND TARD.cod_receita_dedutora = ".$this->obROrcamentoReceitaDedutora->getCodReceita()." ";
    if( $this->roRTesourariaBoletim->getExercicio() )
        $stFiltro .= " AND TA.exercicio = '".$this->roRTesourariaBoletim->getExercicio()."' ";
    if( $this->roRTesourariaBoletim->getCodBoletim() )
        $stFiltro .= " AND TA.cod_boletim = ".$this->roRTesourariaBoletim->getCodBoletim()." ";
    if( $this->obROrcamentoEntidade->getCodigoEntidade() )
        $stFiltro .= " AND TA.cod_entidade IN( ".$this->obROrcamentoEntidade->getCodigoEntidade()." ) ";
    if( $this->obRContabilidadePlanoBanco->getCodPlano() )
        $stFiltro .= " AND TA.cod_plano = ".$this->obRContabilidadePlanoBanco->getCodPlano()." ";
    if( $this->roRTesourariaBoletim->getDataBoletim() )
        $stFiltro .= " AND TB.dt_boletim = TO_DATE( '".$this->roRTesourariaBoletim->getDataBoletim()."', 'dd/mm/yyyy' ) ";
    if( $this->stTimestampArrecadacao )
        $stFiltro .= " AND TO_DATE( TA.timestamp_arrecadacao, 'yyyy-mm-dd' ) = TO_DATE( '".$this->stTimestampArrecadacao."', 'dd/mm/yyyy' ) ";

    $stFiltro .= "
    GROUP BY
        TA.cod_arrecadacao,
        TA.exercicio,
        TA.timestamp_arrecadacao,
        TA.cod_autenticacao,
        TA.dt_autenticacao,
        TA.cod_boletim,
        TA.cod_terminal,
        TA.timestamp_terminal,
        TA.cgm_usuario,
        TA.timestamp_usuario,
        TA.cod_plano,
        TA.cod_entidade,
        TA.observacao,
        TAC.numeracao,
        TAR.cod_receita,
        TAR.cod_receita_dedutora,
        TO_CHAR(TA.timestamp_arrecadacao,'dd/mm/yyyy'),
        TAR.vl_arrecadacao,
        TAR.vl_deducao,
        TAR.descricao

    ";

    $obErro = $obTTesourariaArrecadacao->recuperaArrecadacaoNaoEstornada( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Executa um recupera todos na classe Persistente
    * @acess Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarArrecadacaoValorConta(&$rsRecordSet, $stOrder = "", $boTransacao = "", $boRetencao = "")
{
    $stFiltro="";
    if (Sessao::getExercicio() > '2012') {
        include_once( CAM_GF_TES_MAPEAMENTO ."FTesourariaListarArrecadacaoTCEMS.class.php" );
        $obFTesourariaListarArrecadacao = new FTesourariaListarArrecadacaoTCEMS();
    } else {
        include_once( CAM_GF_TES_MAPEAMENTO ."FTesourariaListarArrecadacao.class.php" );
        $obFTesourariaListarArrecadacao = new FTesourariaListarArrecadacao();
    }
    if( $this->roRTesourariaBoletim->getExercicio() )
        $stFiltro .= " AND TB.exercicio = ''".$this->roRTesourariaBoletim->getExercicio()."''::varchar ";
    if( $this->roRTesourariaBoletim->getCodBoletim() )
        $stFiltro .= " AND TB.cod_boletim = ".$this->roRTesourariaBoletim->getCodBoletim()." ";
    if( $this->roRTesourariaBoletim->getDataBoletim() )
        $stFiltro .= " AND TB.dt_boletim = TO_DATE( ''".$this->roRTesourariaBoletim->getDataBoletim()."'', ''dd/mm/yyyy'' ) ";
    if( $this->roRTesourariaBoletim->obROrcamentoEntidade->getCodigoEntidade() )
        $stFiltro .= " AND TB.cod_entidade IN( ".$this->roRTesourariaBoletim->obROrcamentoEntidade->getCodigoEntidade()." ) ";
    $obFTesourariaListarArrecadacao->setDado( "stFiltro", $stFiltro );
    if($boRetencao) $obFTesourariaListarArrecadacao->setDado('retencao', true );
    $obErro = $obFTesourariaListarArrecadacao->recuperaTodos( $rsRecordSet, '', $stOrder, $boTransacao );

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
function listarTerminalArrecadacao(&$rsRecordSet, $stFiltro = "", $stOrder = "", $boTransacao = "")
{
    include_once ( CAM_GF_TES_MAPEAMENTO ."TTesourariaArrecadacao.class.php" );
    $obTTesourariaArrecadacao = new TTesourariaArrecadacao();
    if( $this->obRARRCarne->getNumeracao() )
        $stFiltro .= " AND A.numeracao = '".$this->obRARRCarne->getNumeracao()."' ";

    if( $this->roRTesourariaBoletim->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->getCodTerminal() )
        $stFiltro .= " AND TT.cod_terminal = ".$this->roRTesourariaBoletim->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->getCodTerminal();
    if( $this->roRTesourariaBoletim->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->getExercicioTerminal() )
        $stFiltro .= " AND TT.exercicio = '".$this->roRTesourariaBoletim->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->getExercicioTerminal()."' ";
    if( $this->roRTesourariaBoletim->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->getTimestampTerminal() )
        $stFiltro .= " AND TT.timestamp_terminal = '".$this->roRTesourariaBoletim->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->getTimestampTerminal()."' ";

    if( $this->roRTesourariaBoletim->obRTesourariaUsuarioTerminal->obRCGM->getNumCGM() )
        $stFiltro .= " AND UT.cgm_usuario = ".$this->roRTesourariaBoletim->obRTesourariaUsuarioTerminal->obRCGM->getNumCGM();
    if( $this->roRTesourariaBoletim->obRTesourariaUsuarioTerminal->getTimestampUsuario() )
        $stFiltro .= " AND UT.timestamp_usuario = '".$this->roRTesourariaBoletim->obRTesourariaUsuarioTerminal->getTimestampUsuario()."' ";

    if( $this->getTimestampArrecadacao() )
        $stFiltro .= " AND A.timestamp_arrecadacao = '".$this->getTimestampArrecadacao()."' ";
    if( $this->getObservacao() )
        $stFiltro .= " AND A.observacao ilike '%".$this->getObservacao()."%' ";

    $stFiltro .= " AND timestamp_fechamento is null ";

    $stFiltro = ($stFiltro) ? $stFiltro : "";
    $stOrder = ($stOrder) ? $stOrder : " ORDER BY TT.cod_terminal";

    $stGroupBy .= "     GROUP BY                                                        \n";
    $stGroupBy .= "         TT.timestamp_terminal,                                      \n";
    $stGroupBy .= "         TT.exercicio,                                               \n";
    $stGroupBy .= "         TT.cod_terminal,                                            \n";
    $stGroupBy .= "         TT.ip,                                                      \n";
    $stGroupBy .= "         UT.cgm_usuario,                                             \n";
    $stGroupBy .= "         UT.timestamp_usuario,                                       \n";
    $stGroupBy .= "         CGM.nom_cgm,                                                \n";
    $stGroupBy .= "         timestamp_fechamento,                                       \n";
    $stGroupBy .= "         to_char(A.dt_boletim,'dd/mm/yyyy')                          \n";

    $obErro = $obTTesourariaArrecadacao->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
}

function montaDescricaoAutenticacao($boTransacao = "")
{
    if ($this->obRTesourariaAutenticacao->obRTesourariaConfiguracao->getFormaComprovacao()==2) {
        $stDescricao = chr(15).$this->obRTesourariaAutenticacao->obRTesourariaConfiguracao->getDigitos();
        $inCodAutenticacao = $this->obRTesourariaAutenticacao->getCodAutenticacao();
        $stDescricao .= str_pad($inCodAutenticacao, 6, "0", STR_PAD_LEFT) . " ";
        $stDescricao .= substr($this->obRTesourariaAutenticacao->getDataAutenticacao(),0,6) . substr($this->obRTesourariaAutenticacao->getDataAutenticacao(),8,2)." ";

        if ($this->nuVlArrecadacao AND !$this->getDevolucao()) {
            if (strstr($this->nuVlArrecadacao,',') !== false) {
                $nuValor = $this->nuVlArrecadacao;
            } else {
                $nuValor = number_format($this->nuVlArrecadacao,2,',','.');
            }
            if ( $this->obRARRCarne->getNumeracao() ) {
                $stDescricao .= "ARR ";
                $stDescricao .= $this->obRARRCarne->getNumeracao()."/". substr($this->roRTesourariaBoletim->getExercicio(), 2, 2) . " ";
                $stDescricao .= str_pad($nuValor, 14, "*", STR_PAD_LEFT) . " \\n \\r";
            } elseif ( $this->obROrcamentoReceita->getCodReceita() ) {
                $stDescricao .= "ARR RECEITA ";
                $stDescricao .= $this->obROrcamentoReceita->getCodReceita()."/". substr($this->roRTesourariaBoletim->getExercicio(), 2, 2) . " ";
                $stDescricao = str_pad($stDescricao, (10 - strlen($this->obROrcamentoReceita->getCodReceita())) , " ") . " ";
                $stDescricao .= str_pad($nuValor, 14, "*", STR_PAD_LEFT) . " \\n \\r";
            }
        } else {
            $nuValor = ($this->getDevolucao()) ? $this->nuVlArrecadacao : $this->nuVlEstornado;
            if (strstr($nuValor,',') !== false) {
                $nuValor = $nuValor;
            } else {
                $nuValor = number_format($nuValor,2,',','.');
            }
            if ( $this->obRARRCarne->getNumeracao() ) {
                $stDescricao .= "EARR ";
                $stDescricao .= $this->obRARRCarne->getNumeracao()."/". substr($this->roRTesourariaBoletim->getExercicio(), 2, 2) . " ";
                $stDescricao .= str_pad($nuValor, 14, "*", STR_PAD_LEFT) . " \\n \\r";
            } elseif ( $this->obROrcamentoReceita->getCodReceita() ) {
                $stDescricao .= "EARR RECEITA ";
                $stDescricao .= $this->obROrcamentoReceita->getCodReceita()."/". substr($this->roRTesourariaBoletim->getExercicio(), 2, 2) . " ";
                $stDescricao = str_pad($stDescricao, (10 - strlen($this->obROrcamentoReceita->getCodReceita())) , " ") . " ";
                $stDescricao .= str_pad($nuValor, 14, "*", STR_PAD_LEFT) . " \\n \\r";
            }
        }
        if ($this->obRContabilidadePlanoBanco->getCodPlano()) {
            $this->obRContabilidadePlanoBanco->listarContaAnalitica($rsPlanoBanco,'',$boTransacao);
            $stDescricao .= $this->obRContabilidadePlanoBanco->getCodPlano() . ' - ' . tiraAcentos($rsPlanoBanco->getCampo('nom_conta')) . " \\n \\r";
        }

        $this->obRTesourariaAutenticacao->setDescricao(array($stDescricao));
    } else {

        $this->obRTesourariaAutenticacao->montaComprovante($cabecalho, $rodape, $boTransacao );

        if ($this->obRTesourariaAutenticacao->getTipo()=="A") {
            $nuValorTotal = number_format(str_replace(",",".",str_replace(".","",$this->nuVlArrecadacao)) - str_replace(",",".",str_replace(".","",$this->nuVlDeducao)),2,'.','');
            $corpo = wordwrap("Recebemos pela conta ".$this->obRContabilidadePlanoBanco->getNomConta()." (".$this->obRContabilidadePlanoBanco->getCodPlano()."/". substr($this->roRTesourariaBoletim->getExercicio(),2,2).") o valor de ".number_format($nuValorTotal,2,',','.')." (".extenso($nuValorTotal)." ), relativos à(s) receita(s) abaixo discriminada(s):", 60, "\\n")."\\n\\n";
        } else {
            $nuValorTotal = number_format($this->nuVlEstornado - $this->nuVlDeducaoEstornado,2,'.','');
            $corpo = wordwrap("Estornado o recebimento do valor de ".number_format($nuValorTotal,2,',','.')." (".extenso($nuValorTotal)." ), relativos à(s) receita(s) abaixo discriminada(s):", 60, "\\n")."\\n\\n";
        }

        $corpo .= str_pad("Conta", 10, " ", STR_PAD_BOTH);
        $corpo .= str_pad("Descrição", 35, " ", STR_PAD_BOTH);
        $corpo .= str_pad("Valor", 15, " ", STR_PAD_BOTH)."\\n";

        $corpo .= str_pad($this->obROrcamentoReceita->getCodReceita()."/". substr($this->roRTesourariaBoletim->getExercicio(), 2, 2), 10, " ", STR_PAD_BOTH);;
        if ($this->obRTesourariaAutenticacao->getTipo()=="A") {
            $contaReceita = wordwrap($this->obROrcamentoReceita->obROrcamentoClassificacaoReceita->getDescricao(), 35, "\n");
        } else {
            $this->obROrcamentoReceita->listar($rsRecordSetReceita,"",$boTransacao);
            $contaReceita = wordwrap($rsRecordSetReceita->getCampo("descricao"), 35, "\n");
        }

        $arContaReceita = explode("\n",$contaReceita);
        $corpo .= str_pad($arContaReceita[0], 35, " ");
        if ($this->obRTesourariaAutenticacao->getTipo()=="A") {
            $corpo .= str_pad(number_format($this->nuVlArrecadacao,2,',','.')." D", 15, " ", STR_PAD_LEFT)."\\n";
        } else {
            $corpo .= str_pad(number_format($this->nuVlEstornado,2,',','.')." C", 15, " ", STR_PAD_LEFT)."\\n";
        }

        for ($i = 1; $i<count($arContaReceita); $i++) {
            $corpo .= str_pad("", 10, " ", STR_PAD_BOTH);;
            $corpo .= str_pad($arContaReceita[$i], 35, " ");
            $corpo .= str_pad("", 15, " ", STR_PAD_LEFT)."\\n";
        }

        if ($this->obROrcamentoReceitaDedutora->getCodReceita()) {
            $corpo .= str_pad($this->obROrcamentoReceitaDedutora->getCodReceita()."/". substr($this->roRTesourariaBoletim->getExercicio(), 2, 2), 10, " ", STR_PAD_BOTH);;

            if ($this->obRTesourariaAutenticacao->getTipo()=="A") {
                $contaReceitaDedutora = wordwrap($this->obROrcamentoReceitaDedutora->obROrcamentoClassificacaoReceita->getDescricao(), 35, "\n");
            } else {
                $this->obROrcamentoReceitaDedutora->listar($rsRecordSetReceitaDedutora,"",$boTransacao);
                $contaReceitaDedutora = wordwrap($rsRecordSetReceitaDedutora->getCampo("descricao"), 35, "\n");
            }

            $arContaReceitaDedutora = explode("\n",$contaReceitaDedutora);
            $corpo .= str_pad($arContaReceitaDedutora[0], 35, " ");

            if ($this->obRTesourariaAutenticacao->getTipo()=="A") {
                $corpo .= str_pad(number_format($this->nuVlDeducao,2,',','.')." C", 15, " ", STR_PAD_LEFT)."\\n";
            } else {
                $corpo .= str_pad(number_format($this->nuVlDeducaoEstornado,2,',','.')." D", 15, " ", STR_PAD_LEFT)."\\n";
            }

            for ($i = 1; $i<count($arContaReceitaDedutora); $i++) {
                $corpo .= str_pad("", 10, " ", STR_PAD_BOTH);;
                $corpo .= str_pad($arContaReceitaDedutora[$i], 35, " ");
                $corpo .= str_pad("", 15, " ", STR_PAD_LEFT)."\\n";
            }
        }

        $stDescricao = chr(15).$cabecalho . $corpo . "\\n \\r" . $rodape;

        $this->obRTesourariaAutenticacao->setDescricao(array(tiraAcentos($stDescricao)."\\n\\n\\n\\n\\n\\n\\n\\n\\n"));

    }
}

function getBoletimArrecadacao(&$rsBoletins , $stOrder = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql  = "      select boletim.cod_boletim \r\n";
    $stSql .= "           , to_char( dt_boletim ,'dd/mm/YYYY') \r\n";
    $stSql .= "        from tesouraria.arrecadacao \r\n";
    $stSql .= "  inner join tesouraria.boletim \r\n";
    $stSql .= "          on boletim.cod_boletim = arrecadacao.cod_boletim  \r\n";
    $stSql .= "         and boletim.cod_entidade = arrecadacao.cod_entidade  \r\n";
    $stSql .= "         and boletim.exercicio    = arrecadacao.exercicio     \r\n";
    //filtro
    $stSql .= "       where arrecadacao.cod_arrecadacao = " . $this->getCodArrecadacao() . " \r\n";
    $stSql .= "         and arrecadacao.cod_entidade    = " . $this->obROrcamentoEntidade->getCodigoEntidade() . " \r\n";
    $stSql .= "         and arrecadacao.timestamp_arrecadacao  = " . $this->getTimestampArrecadacao() . " \r\n";

    $obErro = $obConexao->executaSQL( $rsBoletins , $stSql , $boTransacao );

    return $obErro;
}

}
