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
    * Classe de Regra de Negócio para Boletim
    * Data de Criação   : 20/10/2005

    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Regra

    $Id: RTesourariaBoletim.class.php 64692 2016-03-22 13:36:45Z michel $

    * Casos de uso: uc-02.04.04,uc-02.04.05,uc-02.04.17,uc-02.04.06,uc-02.04.20, uc-02.04.02,uc-02.04.25,uc-02.04.33,uc-02.04.34
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GF_TES_NEGOCIO."RTesourariaTransferencia.class.php";
include_once CAM_GF_TES_NEGOCIO."RTesourariaPagamento.class.php";
include_once CAM_GF_TES_NEGOCIO."RTesourariaBordero.class.php";
include_once CAM_GF_TES_NEGOCIO."RTesourariaTerminal.class.php";
include_once CAM_GF_TES_NEGOCIO."RTesourariaUsuarioTerminal.class.php";
include_once CAM_GF_TES_NEGOCIO."RTesourariaArrecadacao.class.php";
include_once CAM_GF_TES_NEGOCIO."RTesourariaConfiguracao.class.php";
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php";
include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeConfiguracaoLancamentoReceita.class.php";

class RTesourariaBoletim
{
/*
    * @var Object
    * @access Private
*/
var $obRTesourariaUsuarioTerminal;
/*
    * @var Object
    * @access Private
*/
var $obRTesourariaConfiguracao;
/*
    * @var Object
    * @access Private
*/
var $obROrcamentoEntidade;
/*
    * @var Integer
    * @access Private
*/
var $inCodBoletim;
var $inCodBoletimInicial;
var $inCodBoletimFinal;
/*
    * @var String
    * @access Private
*/
var $stExercicio;
/*
    * @var String
    * @access Private
*/
var $stDataBoletim;
var $stDataBoletimInicial;
var $stDataBoletimFinal;

/*
    * @var String
    * @access Private
*/
var $stDataFechamento;
/*
    * @var Array
    * @access Private
*/
var $arArrecadacao;
/*
    * @var Object
    * @access Private
*/
var $roUltimaArrecadacao;
/*
    * @var Array
    * @access Private
*/
var $arTransferencia;
/*
    * @var Object
    * @access Private
*/
var $roUltimaTransferencia;
/*
    * @var Array
    * @access Private
*/
var $arPagamento;
/*
    * @var Object
    * @access Private
*/
var $roUltimoPagamento;
/*
    * @var Array
    * @access Private
*/
var $arBordero;
/*
    * @var Object
    * @access Private
*/
var $roUltimoBordero;
/*
    * @var Integer
    * @access Private
*/
var $inLoteTributario;

/*
    * @access Public
    * @param Object $valor
*/
function setRTesourariaPagamento($valor) { $this->obRTesourariaPagamento            = $valor; }
/*
    * @access Public
    * @param Object $valor
*/
function setRTesourariaUsuarioTerminal($valor) { $this->obRTesourariaUsuarioTerminal      = $valor; }
/*
    * @access Public
    * @param Object $valor
*/
function setRTesourariaConfiguracao($valor) { $this->obRTesourariaConfiguracao         = $valor; }
/*
    * @access Public
    * @param Integer $valor
*/
function setCodBoletim($valor) { $this->inCodBoletim                      = $valor; }
function setCodBoletimInicial($valor) { $this->inCodBoletimInicial               = $valor; }
function setCodBoletimFinal($valor) { $this->inCodBoletimFinal                 = $valor; }
/*
    * @access Public
    * @param String $valor
*/
function setExercicio($valor) { $this->stExercicio                       = $valor; }
/*
    * @access Public
    * @param String $valor
*/
function setDataBoletim($valor) { $this->stDataBoletim                     = $valor; }
function setDataBoletimInicial($valor) { $this->stDataBoletimInicial              = $valor; }
function setDataBoletimFinal($valor) { $this->stDataBoletimFinal                = $valor; }
/*
    * @access Public
    * @param String $valor
*/
function setDataFechamento($valor) { $this->stDataFechamento                  = $valor; }
/*
    * @access Public
    * @param Array $valor
*/
function setArrecadacao($valor) { $this->arArrecadacao                     = $valor; }
/*
    * @access Public
    * @param Array $valor
*/
function setTransferencia($valor) { $this->arTransferencia                   = $valor; }
/*
    * @access Public
    * @param Array $valor
*/
function setPagamento($valor) { $this->arPagamento                       = $valor; }
/*
    * @access Public
    * @param Array $valor
*/
function setBordero($valor) { $this->arBordero                       = $valor; }
/*
    * @access Public
    * @return Object
*/
function getRTesourariaUsuarioTerminal() { return $this->obRTesourariaUsuarioTerminal;      }
/*
    * @access Public
    * @return Object
*/
function getRTesourariaConfiguracao() { return $this->obRTesourariaConfiguracao;         }
/*
    * @access Public
    * @return Integer
*/
function getCodBoletim() { return $this->inCodBoletim;                      }
function getCodBoletimInicial() { return $this->inCodBoletimInicial;               }
function getCodBoletimFinal() { return $this->inCodBoletimFinal;                 }
/*
    * @access Public
    * @return String
*/
function getExercicio() { return $this->stExercicio;                       }
/*
    * @access Public
    * @return String
*/
function getDataBoletim() { return $this->stDataBoletim;                     }
function getDataBoletimInicial() { return $this->stDataBoletimInicial;              }
function getDataBoletimFinal() { return $this->stDataBoletimFinal;                }
/*
    * @access Public
    * @return String
*/
function getDataFechamento() { return $this->stDataFechamento;                  }
/*
    * @access Public
    * @return Array
*/
function getArrecadacao() { return $this->arArrecadacao;                     }
/*
    * @access Public
    * @return Array
*/
function getTransferencia() { return $this->arTransferencia;                   }
/*
    * @access Public
    * @return Array
*/
function getPagamento() { return $this->arPgamento;                        }

/*
    * @access Public
    * @return Array
*/
function getBordero() { return $this->arBordero;                        }

/**
    * Método Construtor
    * @access Private
*/
function RTesourariaBoletim()
{
    $this->obRTesourariaUsuarioTerminal = new RTesourariaUsuarioTerminal( new RTesourariaTerminal() );
    $this->obRTesourariaConfiguracao    = new RTesourariaConfiguracao();
    $this->obROrcamentoEntidade         = new ROrcamentoEntidade();
}

/**
    * Método para adicionar uma arrecadacao ao boletim
    * @access Private
*/
function addArrecadacao()
{
    $this->arArrecadacao[] = new RTesourariaArrecadacao( $this );
    $this->roUltimaArrecadacao = &$this->arArrecadacao[ count( $this->arArrecadacao )-1 ];
}

/**
    * Método para adicionar uma transferencia ao boletim
    * @access Private
*/
function addTransferencia()
{
    $this->arTransferencia[] = new RTesourariaTransferencia( $this );
    $this->roUltimaTransferencia = &$this->arTransferencia[ count( $this->arTransferencia)-1 ];
}

/**
    * Método para adicionar uma pagamento ao boletim
    * @access Private
*/
function addPagamento()
{
    $this->arPagamento[] = new RTesourariaPagamento( $this );
    $this->roUltimoPagamento = &$this->arPagamento[ count( $this->arPagamento )-1 ];
}
/**
    * Método para adicionar um bordero ao boletim
    * @access Private
*/
function addBordero()
{
    $this->arBordero[] = new RTesourariaBordero( $this );
    $this->roUltimoBordero = &$this->arBordero[ count( $this->arBordero )-1 ];
}
/**
    * Executa um proximoCodigo na Persistente
    * @access Public
    * @param Object $boTransacao
    * @return Object Objeto Erro
*/
function buscaProximoCodigo($boTransacao = "")
{
    include_once ( CAM_FW_BANCO_DADOS   ."Transacao.class.php"           );
    include_once ( CAM_GF_TES_MAPEAMENTO."TTesourariaBoletim.class.php" );
     $obTransacao               =  new Transacao;
     $obTTesourariaBoletim      =  new TTesourariaBoletim;
     $obTTesourariaBoletim->setDado( "exercicio", $this->stExercicio );
     $obTTesourariaBoletim->setDado( "cod_entidade", $this->obROrcamentoEntidade->getCodigoEntidade() );
     $obErro = $obTTesourariaBoletim->proximoCod( $inCodBoletim, $obTransacao );
     $this->inCodBoletim = $inCodBoletim;

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
    include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php"              );
    include_once ( CAM_GF_TES_MAPEAMENTO ."TTesourariaBoletim.class.php" );
    $obTransacao                  = new Transacao();
    $obTTesourariaBoletim         = new TTesourariaBoletim();
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {

        $obErro = $this->listarBoletimAberto( $rsBoletimAberto, '', $boTransacao );
        if ( !$obErro->ocorreu() ) {
            if ( !$rsBoletimAberto->eof() ) {
                if( $rsBoletimAberto->getCampo( 'cod_boletim') != $this->inCodBoletim )
                    $obErro->setDescricao( 'Boletim incorreto' );

            } else {
                $obErro = $this->listar( $rsRecordSet, $stFiltro, '', $boTransacao );
                if ( !$obErro->ocorreu() and !$rsRecordSet->eof() ) {
                    $obErro->setDescricao( 'Não há boletins abertos!' );
                } elseif ( !$obErro->ocorreu() ) {
                    $obTTesourariaBoletim->setDado( "cod_boletim"       , $this->inCodBoletim                                                                );
                    $obTTesourariaBoletim->setDado( "exercicio"         , $this->stExercicio                                                                 );
                    $obTTesourariaBoletim->setDado( "cod_entidade"      , $this->obROrcamentoEntidade->getCodigoEntidade()                                   );
                    $obTTesourariaBoletim->setDado( "cod_terminal"      , $this->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->getCodTerminal()       );
                    $obTTesourariaBoletim->setDado( "timestamp_terminal", $this->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->getTimestampTerminal() );
                    $obTTesourariaBoletim->setDado( "cgm_usuario"       , $this->obRTesourariaUsuarioTerminal->obRCGM->getNumCgm()                           );
                    $obTTesourariaBoletim->setDado( "timestamp_usuario" , $this->obRTesourariaUsuarioTerminal->getTimestampUsuario()                         );
                    $obTTesourariaBoletim->setDado( "dt_boletim"        , $this->stDataBoletim                                                               );
                    $obErro = $obTTesourariaBoletim->inclusao( $boTransacao );
                }
            }
        }
        // Abre terminal
        if ( !$obErro->ocorreu() ) {
            $obErro = $this->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->listarSituacaoPorBoletim( $rsTerminais , $this, '', '', $boTransacao );
            if ( !$obErro->ocorreu() ) {
                if ( !$rsTerminais->eof() ) {
                    if( $rsTerminais->getCampo( "situacao" ) == 'fechado' or $rsTerminais->getCampo( "situacao" ) == 'liberado' )
                        $obErro->setDescricao( "Este terminal está fechado!" );
                } else {
                    $obErro = $this->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->abrirTerminal( $this, $boTransacao );
                }
            }
        }
    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTTesourariaBoletim );

    return $obErro;
}

/**
    * Realiza lançamentos de arrecadação
    * @access private
    * @param Array $arCodLote
    * @param RecordSet $rsRecordSet
    * @param Object $boTransacao
    * @return Object $obErro
*/
function lancarArrecadacao(&$arCodLote, $rsArrecadacao, $boRetencao = "", $boTransacao = "")
{
    include_once CAM_GF_ORC_NEGOCIO."ROrcamentoReceita.class.php";
    include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoReceita.class.php";
    include_once CAM_GF_CONT_NEGOCIO."RContabilidadeDesdobramentoReceita.class.php";
    include_once CAM_GF_TES_MAPEAMENTO."FTesourariaRealizacaoReceitaVariavel.class.php";
    include_once CAM_GF_TES_MAPEAMENTO."FTesourariaRealizacaoReceitaFixa.class.php";
    include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeLancamentoReceita.class.php";
    include_once CAM_GF_CONT_MAPEAMENTO.'TContabilidadeValorLancamento.class.php';
    include_once CAM_GF_CONT_NEGOCIO.'RContabilidadePlanoBanco.class.php';
    include_once CAM_GF_CONT_NEGOCIO.'RContabilidadePlanoConta.class.php';
    include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoConfiguracao.class.php";
    include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoRecursoDestinacao.class.php";
    include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoReceitaCreditoTributario.class.php';
    $obRContabilidadePlanoBanco = new RContabilidadePlanoBanco;
    $obRContabilidadeDesdobramentoReceita   = new RContabilidadeDesdobramentoReceita( new ROrcamentoReceita() );

    $obFTesourariaRealizacaoReceitaVariavel = new FTesourariaRealizacaoReceitaVariavel($boTransacao);
    $obFTesourariaRealizacaoReceitaFixa     = new FTesourariaRealizacaoReceitaFixa($boTransacao);
    $nuVlTotalDespesa         = 0;
    $nuVlTotalDisponibilidade = 0;
    $nuVlTotalReceita         = 0;
    $nuSomatorioReceita       = 0;
    $rsArrecadacao->setPrimeiroElemento();
    $obErro = new Erro;
    while ( !$rsArrecadacao->eof() ) {
        if ( $rsArrecadacao->getCampo( "tipo" ) == "A" ) {
            if (Sessao::getExercicio() > '2008') {
                $obTOrcamentoReceitaArrec = new TOrcamentoReceita;
                $stFiltroReceita  = ' WHERE exercicio = '.Sessao::getExercicio().'::varchar ';
                $stFiltroReceita .= ' AND cod_receita = '.$rsArrecadacao->getCampo('cod_receita').' ';
                $obTOrcamentoReceitaArrec->recuperaTodos($rsReceita, $stFiltroReceita, '', $boTransacao);
                $inCodRecurso = $rsReceita->getCampo('cod_recurso');
                $boCreditoTributario = $rsReceita->getCampo('credito_tributario');

                $boDestinacao = false;
                $obTOrcamentoConfiguracao = new TOrcamentoConfiguracao;
                $obTOrcamentoConfiguracao->setDado("exercicio", Sessao::getExercicio() );
                $obTOrcamentoConfiguracao->setDado("parametro","recurso_destinacao");
                $obTOrcamentoConfiguracao->consultar($boTransacao);
                if($obTOrcamentoConfiguracao->getDado("valor") == 'true')
                    $boDestinacao = true;

                if ($boDestinacao && $inCodRecurso != '') {
                    $obTOrcamentoRecursoDestinacao = new TOrcamentoRecursoDestinacao;
                    $obTOrcamentoRecursoDestinacao->setDado("exercicio", Sessao::getExercicio());

                    $stFiltro  = ' WHERE recurso_destinacao.cod_recurso = '.$inCodRecurso;
                    $stFiltro .= '   AND recurso_destinacao.exercicio = '.Sessao::getExercicio().'::varchar';
                    $obErro = $obTOrcamentoRecursoDestinacao->recuperaTodos($rsDestinacao, $stFiltro, '', $boTransacao);
                    $inCodEspecificacao = $rsDestinacao->getCampo('cod_especificacao');

                    // Verifica qual o cod_recurso que possui conta contabil vinculada
                    $obTOrcamentoRecursoDestinacao->setDado('exercicio', Sessao::getExercicio());
                    $obTOrcamentoRecursoDestinacao->setDado('cod_especificacao', $inCodEspecificacao);
                    if ( Sessao::getExercicio() > '2012' ) {
                        $obTOrcamentoRecursoDestinacao->setDado('cod_estrutural', "'8.2.1.1.1.%'");
                    } else {
                        $obTOrcamentoRecursoDestinacao->setDado('cod_estrutural', "'2.9.3.2.0.00.00.%'");
                    }
                    $obErro = $obTOrcamentoRecursoDestinacao->recuperaRecursoVinculoConta($rsContaRecurso, '', '', $boTransacao);

                    $inCodRecurso = $rsContaRecurso->getCampo('cod_recurso');
                }

                if ($inCodRecurso != '') {
                    include_once CAM_GF_CONT_MAPEAMENTO.'TContabilidadePlanoBanco.class.php';
                    $obTContabilidadePlanoBanco = new TContabilidadePlanoBanco;
                    $obTContabilidadePlanoBanco->setDado('cod_recurso',$inCodRecurso);
                    $obTContabilidadePlanoBanco->setDado('exercicio',Sessao::getExercicio());
                    if ( Sessao::getExercicio() > '2012' ) {
                        $obTContabilidadePlanoBanco->setDado('cod_estrutural', "7.2.1.1.1.%");
                    } else {
                        $obTContabilidadePlanoBanco->setDado('cod_estrutural', "1.9.3.2.0.00.00.%");
                    }
                    $rsContaRecurso = new RecordSet();
                    $obErro = $obTContabilidadePlanoBanco->verificaContasRecurso($rsContaRecurso, $boTransacao);
                    if ( $rsContaRecurso->getNumLinhas() < 0 ) {
                        $obErro->setDescricao("O recurso da receita não possuir conta criada para o grupo (7.2.1.1.1)!");
                        break;
                    }
                    if ( $rsContaRecurso->getNumLinhas() > 1 ) {
                        $obErro->setDescricao("Recurso (".$inCodRecurso.") não pode estar vinculado a mais de uma conta contábil!");
                        break;
                    }
                    
                    if ( !$obErro->ocorreu() ) {
                        if ( Sessao::getExercicio() > '2012' ) {
                            $obTContabilidadePlanoBanco->setDado('cod_estrutural', "8.2.1.1.1.%");
                        } else {
                            $obTContabilidadePlanoBanco->setDado('cod_estrutural', "2.9.3.2.0.00.00.%");
                        }
                    
                        $obErro = $obTContabilidadePlanoBanco->verificaContasRecurso($rsContaRecurso, $boTransacao);
                        if ( $rsContaRecurso->getNumLinhas() < 0 ) {
                            $obErro->setDescricao("O recurso da receita não possuir conta criada para o grupo (8.2.1.1.1)!");
                            break;
                        }
                        if ( $rsContaRecurso->getNumLinhas() <> 1 ) {
                            $obErro->setDescricao("Recurso (".$inCodRecurso.") não pode estar vinculado a mais de uma conta contábil!");
                            break;
                        }
                        
                        if( !$obErro->ocorreu() ) {
                            $obRContabilidadePlanoBanco = new RContabilidadePlanoBanco;
                            $obRContabilidadePlanoBanco->obROrcamentoRecurso->setCodRecurso($inCodRecurso);
                            $obErro = $obRContabilidadePlanoBanco->getContasRecurso($rsContasRecurso, $boTransacao);
                            
                            $inCodPlanoUm = $rsContasRecurso->getCampo('cod_plano_um');
                            $inCodPlanoDois = $rsContasRecurso->getCampo('cod_plano_dois');
                        }
                    }
                } else {
                    $inCodPlanoUm = '';
                    $inCodPlanoDois = '';
                }
            } else {
                $inCodPlanoUm = '';
                $inCodPlanoDois = '';
            }
            
            if( $obErro->ocorreu() )
                break;
            
            $obRContabilidadeDesdobramentoReceita->roROrcamentoReceitaPrincipal->setCodReceita( $rsArrecadacao->getCampo('cod_receita') );
            $obRContabilidadeDesdobramentoReceita->roROrcamentoReceitaPrincipal->setExercicio ( $rsArrecadacao->getCampo('exercicio') );
            $obRContabilidadeDesdobramentoReceita->roROrcamentoReceitaPrincipal->obROrcamentoEntidade->setCodigoEntidade ( $rsArrecadacao->getCampo('cod_entidade') );
            $obErro = $obRContabilidadeDesdobramentoReceita->listar( $rsReceitaSecundaria, $boTransacao );

            if(!$boRetencao)
                 $obFTesourariaRealizacaoReceitaVariavel->setDado( "cod_lote", $arCodLote[$rsArrecadacao->getCampo( "cod_entidade" )][0] );
            else {
                $obFTesourariaRealizacaoReceitaVariavel->setDado( "cod_lote", '');
                $inCodLote = "";
            }

            $stEstruturalDebito  = str_replace( '.', '', $rsArrecadacao->getCampo( "cod_estrutural_debito"  ) );
            $stCodPlanoEstruturalDebito = $rsArrecadacao->getCampo( "conta_debito"  );

            // Faz lancamentos das receitas secundárias
            $nuVlReceitaPrincipal = $rsArrecadacao->getCampo('valor');
            while ( !$rsReceitaSecundaria->eof() and !$obErro->ocorreu() ) {
                $nuValorParcela = number_format( $rsArrecadacao->getCampo('valor') * ( $rsReceitaSecundaria->getCampo( "percentual" ) / 100) , 2, ".", "");
                $nuSomatorioReceita   = bcadd( $nuSomatorioReceita  , $nuValorParcela, 4 );
                $nuVlReceitaPrincipal = bcsub( $nuVlReceitaPrincipal, $nuValorParcela, 4 );

                if ( Sessao::getExercicio() > '2012' ) {
                    if ($boCreditoTributario == "t") {
                        $stCodPlanoEstruturalDebito  = $rsArrecadacao->getCampo('conta_debito'); //código da conta de banco/caixa da arrecadação
                        
                        $obTOrcamentoReceitaCreditoTributario = new TOrcamentoReceitaCreditoTributario;
                        $obTOrcamentoReceitaCreditoTributario->setDado('cod_receita' , $rsReceitaSecundaria->getCampo( "cod_receita_secundaria" ));
                        $obTOrcamentoReceitaCreditoTributario->setDado('exercicio'   , $rsReceitaSecundaria->getCampo( "exercicio" ));
                        $obErro = $obTOrcamentoReceitaCreditoTributario->recuperaContaCreditoTributario($rsContaCreditoTributario, '', '', $boTransacao);
                        
                        if ($rsContaCreditoTributario->getNumLinhas() < 1) {
                            $obErro->setDescricao("A receita secundária ".$rsArrecadacao->getCampo('cod_receita')." não possui conta de crédito tributário cadastrada!");
                            return $obErro;
                        }
                        
                        $stCodPlanoClasReceita = $rsContaCreditoTributario->getCampo('cod_plano');
                        $stClasReceita = str_replace( ".", "", $stCodPlanoClasReceita); 
                    } else {
                        $stFiltroConfiguracao = " WHERE configuracao_lancamento_receita.estorno = 'false'
                                                AND configuracao_lancamento_receita.exercicio = '".$rsReceitaSecundaria->getCampo('exercicio')."'
                                                AND receita.cod_receita = ".$rsReceitaSecundaria->getCampo('cod_receita_secundaria');
                        $obTContabilidadeConfiguracaoLancamentoReceita = new TContabilidadeConfiguracaoLancamentoReceita;
                        $obErro = $obTContabilidadeConfiguracaoLancamentoReceita->recuperaContasReceita( $rsLista, $stFiltroConfiguracao, "", $boTransacao );
                        
                        if ($rsLista->getNumLinhas() < 1 && !$obErro->ocorreu()) {
                            $obErro->setDescricao("Não há configuração de lançamento de conta para o desdobramento da receita ".$rsReceitaSecundaria->getCampo('cod_receita_principal')."!");
                            return $obErro;
                        }
                    }
                } else {
                    $obROrcamentoReceita = new ROrcamentoReceita();
                    $obROrcamentoReceita->setExercicio ( $rsReceitaSecundaria->getCampo('exercicio') );
                    $obROrcamentoReceita->setCodReceita( $rsReceitaSecundaria->getCampo('cod_receita_secundaria') );
                    $obErro = $obROrcamentoReceita->listar( $rsLista , '', $boTransacao );
                }
                
                if ( !$obErro->ocorreu() ) {
                    if ( Sessao::getExercicio() > '2012' ) {
                        if ($boCreditoTributario != "t") {
                            $stClasReceita = str_replace( ".", "", $rsLista->getCampo( "cod_estrutural" ));
                            $stCodPlanoClasReceita = $rsLista->getCampo("cod_plano"); 
                        }
                    } else {
                        $stClasReceita = '4'.str_replace( ".", "", $rsLista->getCampo( "mascara_classificacao" ));
                    }
                    
                    if($boRetencao)
                         $stComplemento = $rsArrecadacao->getCampo('cod_ordem')."/".$rsArrecadacao->getCampo('exercicio');
                    else $stComplemento = ( $rsArrecadacao->getCampo("numeracao") ) ? $rsArrecadacao->getCampo("numeracao") : $rsReceitaSecundaria->getCampo("cod_receita_secundaria");
                    
                    if($boRetencao)
                         $stNomLote     = "Arrecadação por Retenção Orçamentária - OP ".$rsArrecadacao->getCampo('cod_ordem')."/".$rsArrecadacao->getCampo('exercicio');
                    else $stNomLote     = "Arrecadação de Receita Boletim N. ".$rsArrecadacao->getCampo( "cod_boletim" )."/".$rsArrecadacao->getCampo("exercicio");
                    
                    $obFTesourariaRealizacaoReceitaVariavel->setDado( "conta_recebimento", $stEstruturalDebito                        );
                    $obFTesourariaRealizacaoReceitaVariavel->setDado( "clas_receita"     , $stClasReceita                             );
                    $obFTesourariaRealizacaoReceitaVariavel->setDado( "exercicio"        , $rsArrecadacao->getCampo( "exercicio" )    );
                    $obFTesourariaRealizacaoReceitaVariavel->setDado( "valor"            , $nuValorParcela                            );
                    $obFTesourariaRealizacaoReceitaVariavel->setDado( "complemento"      , $stComplemento                             );
                    $obFTesourariaRealizacaoReceitaVariavel->setDado( "nom_lote"         , $stNomLote                                 );
                    $obFTesourariaRealizacaoReceitaVariavel->setDado( "tipo_lote"        , "A"                                        );
                    $obFTesourariaRealizacaoReceitaVariavel->setDado( "dt_lote"          , $this->stDataBoletim                       );
                    $obFTesourariaRealizacaoReceitaVariavel->setDado( "cod_entidade"     , $rsArrecadacao->getCampo( "cod_entidade" ) );
                    $obFTesourariaRealizacaoReceitaVariavel->setDado( "cod_reduzido"     , $rsArrecadacao->getCampo( "conta_debito" ) );
                    if ($boRetencao) {
                        $obFTesourariaRealizacaoReceitaVariavel->setDado( "cod_historico" , 950  );
                        $inCodHistoricoDesdobramento = 950;
                    } else {
                        $inCodHistoricoDesdobramento = $rsArrecadacao->getCampo( "cod_historico" );

                        if(empty($inCodHistoricoDesdobramento)){
                            $obFTesourariaRealizacaoReceitaVariavel->setDado( "cod_historico" , null );
                            $inCodHistoricoDesdobramento = 907;
                        }else{
                            $obFTesourariaRealizacaoReceitaVariavel->setDado( "cod_historico" , $inCodHistoricoDesdobramento );
                        }
                    }
                    if (Sessao::getExercicio() > '2012') {
                        $obFTesourariaRealizacaoReceitaVariavel->setDado( "cod_plano_conta_recebimento", $stCodPlanoEstruturalDebito                        );
                        $obFTesourariaRealizacaoReceitaVariavel->setDado( "cod_plano_clas_receita"     , $stCodPlanoClasReceita                             );
                    }
                    $obErro = $obFTesourariaRealizacaoReceitaVariavel->executaFuncao( $rsRetornoFuncao, $boTransacao );
                    if ( !$obErro->ocorreu() ) {
                        $inCodLote = $obFTesourariaRealizacaoReceitaVariavel->getDado( "cod_lote" );

                        if (Sessao::getExercicio() > '2008') {
                            $obTOrcamentoReceitaLista = new TOrcamentoReceita;
                            $stFiltroReceita  = ' WHERE exercicio = '.Sessao::getExercicio().'::varchar';
                            if ($boCreditoTributario != "t") {
                                $stFiltroReceita .= ' AND cod_receita = '.$rsLista->getCampo('cod_receita').' ';
                            } else {
                                $stFiltroReceita .= ' AND cod_receita = '.$rsArrecadacao->getCampo('cod_receita').' ';
                            }
                            $obTOrcamentoReceitaLista->recuperaTodos($rsReceita, $stFiltroReceita, '', $boTransacao);
                            $inCodRecursoDesdobramento = $rsReceita->getCampo('cod_recurso');

                            $boDestinacao = false;
                            $obTOrcamentoConfiguracao = new TOrcamentoConfiguracao;
                            $obTOrcamentoConfiguracao->setDado("exercicio", Sessao::getExercicio() );
                            $obTOrcamentoConfiguracao->setDado("parametro","recurso_destinacao");
                            $obTOrcamentoConfiguracao->consultar($boTransacao);
                            if($obTOrcamentoConfiguracao->getDado("valor") == 'true')
                                $boDestinacao = true;

                            if ($boDestinacao && $inCodRecursoDesdobramento != '') {
                                $obTOrcamentoRecursoDestinacao = new TOrcamentoRecursoDestinacao;
                                $obTOrcamentoRecursoDestinacao->setDado("exercicio", Sessao::getExercicio());

                                $stFiltro  = ' WHERE recurso_destinacao.cod_recurso = '.$inCodRecursoDesdobramento;
                                $stFiltro .= '   AND recurso_destinacao.exercicio = '.Sessao::getExercicio();
                                $obErro = $obTOrcamentoRecursoDestinacao->recuperaTodos($rsDestinacao, $stFiltro, '', $boTransacao);
                                $inCodEspecificacao = $rsDestinacao->getCampo('cod_especificacao');

                                // Verifica qual o cod_recurso que possui conta contabil vinculada
                                $obTOrcamentoRecursoDestinacao->setDado('exercicio', Sessao::getExercicio());
                                $obTOrcamentoRecursoDestinacao->setDado('cod_especificacao', $inCodEspecificacao);
                                if ( Sessao::getExercicio() > '2012' ) {
                                    $obTOrcamentoRecursoDestinacao->setDado('cod_estrutural', "'2.9.3.2.0.00.00.%'");
                                } else {
                                    $obTOrcamentoRecursoDestinacao->setDado('cod_estrutural', "'8.2.1.1.1.%'");
                                }
                                $obTOrcamentoRecursoDestinacao->recuperaRecursoVinculoConta($rsContaRecurso, '', '', $boTransacao);

                                $inCodRecursoDesdobramento = $rsContaRecurso->getCampo('cod_recurso');
                            }

                            if ($inCodRecursoDesdobramento != '' && !$obErro->ocorreu()) {
                                $obRContabilidadePlanoBanco->obROrcamentoRecurso->setCodRecurso($inCodRecursoDesdobramento);
                                $obErro = $obRContabilidadePlanoBanco->getContasRecurso($rsContasRecursoDesdobramento, $boTransacao);
                                $inCodPlanoUmDesdobramento = $rsContasRecursoDesdobramento->getCampo('cod_plano_um');
                                $inCodPlanoDoisDesdobramento = $rsContasRecursoDesdobramento->getCampo('cod_plano_dois');

                                if ($inCodPlanoUmDesdobramento != '' && $inCodPlanoDoisDesdobramento != '' && !$obErro->ocorreu()) {
                                    $obTContabilidadeValorLancamento = new TContabilidadeValorLancamento;
                                    $obTContabilidadeValorLancamento->setDado("cod_lote", $inCodLote);
                                    $obTContabilidadeValorLancamento->setDado("tipo", 'A');
                                    $obTContabilidadeValorLancamento->setDado("exercicio", $rsArrecadacao->getCampo("exercicio"));
                                    $obTContabilidadeValorLancamento->setDado("cod_entidade", $rsArrecadacao->getCampo('cod_entidade'));
                                    $obTContabilidadeValorLancamento->setDado("cod_plano_deb", $inCodPlanoUmDesdobramento);
                                    $obTContabilidadeValorLancamento->setDado("cod_plano_cred", $inCodPlanoDoisDesdobramento);
                                    $obTContabilidadeValorLancamento->setDado("cod_historico", $inCodHistoricoDesdobramento);
                                    $obTContabilidadeValorLancamento->setDado("complemento", $stComplemento);
                                    $obTContabilidadeValorLancamento->setDado("vl_lancamento", $nuValorParcela);

                                    $obErro = $obTContabilidadeValorLancamento->inclusaoPorPl($rsRecordSet, $boTransacao);
                                }
                            }
                        }

                        if (!$obErro->ocorreu()) {
                            $obTContabilidadeLancamentoReceita = new TContabilidadeLancamentoReceita();
                            $obTContabilidadeLancamentoReceita->setDado( "cod_lote"    , $inCodLote                                       );
                            $obTContabilidadeLancamentoReceita->setDado( "tipo"        , "A"                                              );
                            $obTContabilidadeLancamentoReceita->setDado( "sequencia"   , $rsRetornoFuncao->getCampo( "sequencia" )        );
                            $obTContabilidadeLancamentoReceita->setDado( "exercicio"   , $this->stExercicio                               );
                            $obTContabilidadeLancamentoReceita->setDado( "cod_entidade", $this->obROrcamentoEntidade->getCodigoEntidade() );
                            $obTContabilidadeLancamentoReceita->setDado( "cod_receita" , $rsReceitaSecundaria->getCampo( "cod_receita_secundaria" ) );
                            $obTContabilidadeLancamentoReceita->setDado( "estorno"     , "false"                                          );
                            $obErro = $obTContabilidadeLancamentoReceita->inclusao( $boTransacao );
                        }

                        /* RECEITA REALIZADA | RECEITA A REALIZAR | DÍVIDA ATIVA | RETENÇOES */
                        if (!$obErro->ocorreu() && $boRetencao ) {
                            $nuVlDespesaRet = 0.00;
                            $nuVlDisponibilidadeRet = 0.00;
                            if ( substr( $stClasReceita, 1, 4 ) == "1931" ) {
                                $nuVlDespesaRet = $nuValorParcela;
                            }
                            if ( substr( $stClasReceita, 1, 4 ) == "1932" ) {
                                $nuVlDisponibilidadeRet = $nuValorParcela;
                            }
                            $obFTesourariaRealizacaoReceitaFixa->setDado( "exercicio"              , $rsArrecadacao->getCampo( "exercicio"     ) );
                            $obFTesourariaRealizacaoReceitaFixa->setDado( "valor"                  , $nuValorParcela                             );
                            $obFTesourariaRealizacaoReceitaFixa->setDado( "complemento"            , $stComplemento                              );
                            $obFTesourariaRealizacaoReceitaFixa->setDado( "cod_lote"               , $inCodLote                                  );
                            $obFTesourariaRealizacaoReceitaFixa->setDado( "nom_lote"               , $stNomLote                                  );
                            $obFTesourariaRealizacaoReceitaFixa->setDado( "tipo_lote"              , "A"                                         );
                            $obFTesourariaRealizacaoReceitaFixa->setDado( "dt_lote"                , $this->stDataBoletim                        );
                            $obFTesourariaRealizacaoReceitaFixa->setDado( "cod_entidade"           , $rsArrecadacao->getCampo( "cod_entidade"  ) );
                            if ( Sessao::getExercicio() > '2012' ) {
                                $obFTesourariaRealizacaoReceitaFixa->setDado( "valor_despesa"          , ''                                          );
                                $obFTesourariaRealizacaoReceitaFixa->setDado( "valor_disponibilidades" , ''                                          );
                            } else {
                                $obFTesourariaRealizacaoReceitaFixa->setDado( "valor_despesa"          , $nuVlDespesaRet                             );
                                $obFTesourariaRealizacaoReceitaFixa->setDado( "valor_disponibilidades" , $nuVlDisponibilidadeRet                     );
                            }
                            if ($boRetencao) {
                                $obFTesourariaRealizacaoReceitaFixa->setDado( "cod_historico"          , 950  );
                            } else{
                                $inCodHistoricoReceitaFixa = $rsArrecadacao->getCampo( "cod_historico" );

                                if(empty($inCodHistoricoReceitaFixa)){
                                    $obFTesourariaRealizacaoReceitaFixa->setDado( "cod_historico"      , null                       );
                                }else{
                                    $obFTesourariaRealizacaoReceitaFixa->setDado( "cod_historico"      , $inCodHistoricoReceitaFixa );
                                }
                            }
                            
                            $obErro = $obFTesourariaRealizacaoReceitaFixa->executaFuncao( $rsRetornoFuncao, $boTransacao );
                        }
                    }
                }

                $rsReceitaSecundaria->proximo();
            }

            $stEstruturalCredito = str_replace( '.', '', $rsArrecadacao->getCampo( "cod_estrutural_credito" ) );

            if ( Sessao::getExercicio() > '2012' ) {
                if ($boCreditoTributario == "t") {
                    $stCodPlanoEstruturalDebito  = $rsArrecadacao->getCampo('conta_debito'); //código da conta de banco/caixa da arrecadação

                    $obTOrcamentoReceitaCreditoTributario = new TOrcamentoReceitaCreditoTributario;
                    $obTOrcamentoReceitaCreditoTributario->setDado('cod_receita' , $rsArrecadacao->getCampo( "cod_receita" ));
                    $obTOrcamentoReceitaCreditoTributario->setDado('exercicio'   , $rsArrecadacao->getCampo( "exercicio" ));
                    $obErro = $obTOrcamentoReceitaCreditoTributario->recuperaContaCreditoTributario($rsContaCreditoTributario, '', '', $boTransacao);

                    if ($rsContaCreditoTributario->getNumLinhas() < 1) {
                        $obErro->setDescricao("A receita ".$rsArrecadacao->getCampo('cod_receita')." não possui conta de crédito tributário cadastrada!");

                        return $obErro;
                    }

                    $stCodPlanoEstruturalCredito = $rsContaCreditoTributario->getCampo('cod_plano');
                } else {
                    $stFiltroConfiguracao = " WHERE configuracao_lancamento_receita.estorno = 'false'
                                                AND configuracao_lancamento_receita.exercicio = '".$rsArrecadacao->getCampo( "exercicio" )."'
                                                AND receita.cod_receita = ".$rsArrecadacao->getCampo( "cod_receita" );
                    $obTContabilidadeConfiguracaoLancamentoReceita = new TContabilidadeConfiguracaoLancamentoReceita;
                    $obErro = $obTContabilidadeConfiguracaoLancamentoReceita->recuperaContasReceita( $rsListaConfiguracao, $stFiltroConfiguracao, "", $boTransacao );

                    if ($rsListaConfiguracao->getNumLinhas() < 1) {
                        $obErro->setDescricao("Não há configuração de lançamento de conta para a receita ".$rsArrecadacao->getCampo('cod_receita')."!");

                        return $obErro;
                    }

                    if (!$obErro->ocorreu()) {
                        $stEstruturalCredito = $rsListaConfiguracao->getCampo('cod_estrutural');
                        $stCodPlanoEstruturalCredito = $rsListaConfiguracao->getCampo('cod_plano');
                    }
                }
            }

            if($boRetencao)
                 $stComplemento = $rsArrecadacao->getCampo('cod_ordem')."/".$rsArrecadacao->getCampo('exercicio');
            else $stComplemento = ( $rsArrecadacao->getCampo("numeracao") ) ? $rsArrecadacao->getCampo("numeracao") : $rsArrecadacao->getCampo("cod_receita");

            if($boRetencao)
                 $stNomLote     = "Arrecadação por Retenção Orçamentária - OP ".$rsArrecadacao->getCampo('cod_ordem')."/".$rsArrecadacao->getCampo('exercicio');
            else $stNomLote     = "Arrecadação de Receita Boletim N. ".$rsArrecadacao->getCampo( "cod_boletim" )."/".$rsArrecadacao->getCampo("exercicio");

            $obFTesourariaRealizacaoReceitaVariavel->setDado( "conta_recebimento", $stEstruturalDebito                        );
            $obFTesourariaRealizacaoReceitaVariavel->setDado( "clas_receita"     , $stEstruturalCredito                       );
            $obFTesourariaRealizacaoReceitaVariavel->setDado( "exercicio"        , $rsArrecadacao->getCampo( "exercicio"    ) );
            $obFTesourariaRealizacaoReceitaVariavel->setDado( "valor"            , $nuVlReceitaPrincipal                      );
            $obFTesourariaRealizacaoReceitaVariavel->setDado( "complemento"      , $stComplemento                             );
            $obFTesourariaRealizacaoReceitaVariavel->setDado( "nom_lote"         , $stNomLote                                 );
            $obFTesourariaRealizacaoReceitaVariavel->setDado( "tipo_lote"        , "A"                                        );
            $obFTesourariaRealizacaoReceitaVariavel->setDado( "dt_lote"          , $this->stDataBoletim                       );
            $obFTesourariaRealizacaoReceitaVariavel->setDado( "cod_entidade"     , $rsArrecadacao->getCampo( "cod_entidade" ) );
            $obFTesourariaRealizacaoReceitaVariavel->setDado( "cod_reduzido"     , $rsArrecadacao->getCampo( "conta_debito" ) );
            if ($boRetencao) {
                $obFTesourariaRealizacaoReceitaVariavel->setDado( "cod_historico" , 950  );
                $inCodHistorico = 950;
            } else {
                $inCodHistorico = $rsArrecadacao->getCampo( "cod_historico" );

                if(empty($inCodHistorico)){
                    $obFTesourariaRealizacaoReceitaVariavel->setDado( "cod_historico" , null );
                    $inCodHistorico = 907;
                }else{
                    $obFTesourariaRealizacaoReceitaVariavel->setDado( "cod_historico" , $inCodHistorico );
                }
            }
            if (Sessao::getExercicio() > '2012') {
                $obFTesourariaRealizacaoReceitaVariavel->setDado( "cod_plano_conta_recebimento", $stCodPlanoEstruturalDebito                        );
                $obFTesourariaRealizacaoReceitaVariavel->setDado( "cod_plano_clas_receita"     , $stCodPlanoEstruturalCredito                       );
            }
            $obErro = $obFTesourariaRealizacaoReceitaVariavel->executaFuncao( $rsRetornoFuncao, $boTransacao );
            $inCodLote = $obFTesourariaRealizacaoReceitaVariavel->getDado( "cod_lote" );
            if (Sessao::getExercicio() > '2008') {
                if ($inCodPlanoUm != '' && $inCodPlanoDois != '' && !$obErro->ocorreu()) {
                    $obTContabilidadeValorLancamento = new TContabilidadeValorLancamento;
                    $obTContabilidadeValorLancamento->setDado("cod_lote", $inCodLote);
                    $obTContabilidadeValorLancamento->setDado("tipo", 'A');
                    $obTContabilidadeValorLancamento->setDado("exercicio", $rsArrecadacao->getCampo("exercicio"));
                    $obTContabilidadeValorLancamento->setDado("cod_entidade", $rsArrecadacao->getCampo('cod_entidade'));
                    $obTContabilidadeValorLancamento->setDado("cod_plano_deb", $inCodPlanoUm);
                    $obTContabilidadeValorLancamento->setDado("cod_plano_cred", $inCodPlanoDois);
                    $obTContabilidadeValorLancamento->setDado("cod_historico", $inCodHistorico);
                    $obTContabilidadeValorLancamento->setDado("complemento", $stComplemento);
                    $obTContabilidadeValorLancamento->setDado("vl_lancamento", $nuVlReceitaPrincipal);

                    $obErro = $obTContabilidadeValorLancamento->inclusaoPorPl($rsRecordSet, $boTransacao);
                }
            }

            if($boRetencao)
                 $arCodLote['retencao'][] = $obFTesourariaRealizacaoReceitaVariavel->getDado( "cod_lote" );
            else $arCodLote[$rsArrecadacao->getCampo("cod_entidade")][0] = $obFTesourariaRealizacaoReceitaVariavel->getDado( "cod_lote" );

            if ( !$obErro->ocorreu() ) {
                $inCodLote = $obFTesourariaRealizacaoReceitaVariavel->getDado( "cod_lote" );
                $obTContabilidadeLancamentoReceita = new TContabilidadeLancamentoReceita();
                $obTContabilidadeLancamentoReceita->setDado( "cod_lote"    , $inCodLote                                       );
                $obTContabilidadeLancamentoReceita->setDado( "tipo"        , "A"                                              );
                $obTContabilidadeLancamentoReceita->setDado( "sequencia"   , $rsRetornoFuncao->getCampo( "sequencia" )        );
                $obTContabilidadeLancamentoReceita->setDado( "exercicio"   , $this->stExercicio                               );
                $obTContabilidadeLancamentoReceita->setDado( "cod_entidade", $this->obROrcamentoEntidade->getCodigoEntidade() );
                $obTContabilidadeLancamentoReceita->setDado( "cod_receita" , $rsArrecadacao->getCampo( "cod_receita" )        );
                $obTContabilidadeLancamentoReceita->setDado( "estorno"     , "false"                                          );
                $obErro = $obTContabilidadeLancamentoReceita->inclusao( $boTransacao );
                if ($boRetencao && !$obErro->ocorreu()) {
                    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeLancamentoRetencao.class.php" );
                    $obTContabilidadeLancamentoRetencao = new TContabilidadeLancamentoRetencao;
                    $obTContabilidadeLancamentoRetencao->setDado('tipo'     , 'A'        );
                    $obTContabilidadeLancamentoRetencao->setDado('cod_lote' , $inCodLote );
                    $obTContabilidadeLancamentoRetencao->setDado('cod_entidade' , $this->obROrcamentoEntidade->getCodigoEntidade() );
                    $obTContabilidadeLancamentoRetencao->setDado('exercicio', $this->stExercicio );
                    $obTContabilidadeLancamentoRetencao->setDado('sequencia', $rsRetornoFuncao->getCampo( "sequencia" ) );
                    $obTContabilidadeLancamentoRetencao->setDado('cod_ordem', $rsArrecadacao->getCampo('cod_ordem') );
                    $obTContabilidadeLancamentoRetencao->setDado('cod_plano', $rsArrecadacao->getCampo('cod_plano') );
                    $obTContabilidadeLancamentoRetencao->setDado('exercicio_retencao', $this->stExercicio );
                    $obErro = $obTContabilidadeLancamentoRetencao->inclusao( $boTransacao );

                    /* RECEITA REALIZADA E RECEITA A REALIZAR || RETENCOES */
                    if (!$obErro->ocorreu()) {
                        $nuVlDespesaRet = 0.00;
                        $nuVlDisponibilidadeRet = 0.00;
                        if ( substr( $stEstruturalCredito, 1, 4 ) == "1931" ) {
                            $nuVlDespesaRet = $nuVlReceitaPrincipal;
                        }
                        if ( substr( $stEstruturalCredito, 1, 4 ) == "1932" ) {
                            $nuVlDisponibilidadeRet = $nuVlReceitaPrincipal;
                        }
                        $obFTesourariaRealizacaoReceitaFixa->setDado( "exercicio"              , $rsArrecadacao->getCampo( "exercicio"     ) );
                        $obFTesourariaRealizacaoReceitaFixa->setDado( "valor"                  , $nuVlReceitaPrincipal                       );
                        $obFTesourariaRealizacaoReceitaFixa->setDado( "complemento"            , $stComplemento                              );
                        $obFTesourariaRealizacaoReceitaFixa->setDado( "cod_lote"               , $inCodLote                                  );
                        $obFTesourariaRealizacaoReceitaFixa->setDado( "nom_lote"               , $stNomLote                                  );
                        $obFTesourariaRealizacaoReceitaFixa->setDado( "tipo_lote"              , "A"                                         );
                        $obFTesourariaRealizacaoReceitaFixa->setDado( "dt_lote"                , $this->stDataBoletim                        );
                        $obFTesourariaRealizacaoReceitaFixa->setDado( "cod_entidade"           , $rsArrecadacao->getCampo( "cod_entidade"  ) );
                        if ( Sessao::getExercicio() > '2012' ) {
                            $obFTesourariaRealizacaoReceitaFixa->setDado( "valor_despesa"          ,  ''                                         );
                            $obFTesourariaRealizacaoReceitaFixa->setDado( "valor_disponibilidades" ,  ''                                         );
                        } else {
                            $obFTesourariaRealizacaoReceitaFixa->setDado( "valor_despesa"          , $nuVlDespesaRet                             );
                            $obFTesourariaRealizacaoReceitaFixa->setDado( "valor_disponibilidades" , $nuVlDisponibilidadeRet                     );
                        }
                        $obFTesourariaRealizacaoReceitaFixa->setDado( "cod_historico"          , 950 );
                        $obErro = $obFTesourariaRealizacaoReceitaFixa->executaFuncao( $rsRetornoFuncao, $boTransacao );
                    }
                }
            }

            if ( !$obErro->ocorreu() ) {
                $nuVlTotalReceita = bcadd( $nuVlTotalReceita, $rsArrecadacao->getCampo( "valor" ), 4 );
                if ( substr( $stEstruturalCredito, 1, 4 ) == "1931" ) {
                    $nuVlTotalDespesa = bcadd( $nuVlTotalDespesa, $rsArrecadacao->getCampo( "valor" ), 4 );
                }
                if ( substr( $stEstruturalCredito, 1, 4 ) == "1932" ) {
                    $nuVlTotalDisponibilidade = bcadd( $nuVlTotalDisponibilidade, $rsArrecadacao->getCampo( "valor" ), 4 );
                }
                $inCodEntidadeOld = $rsArrecadacao->getCampo( "cod_entidade" );
                $stTipoOld        = $rsArrecadacao->getCampo( "tipo" );
                $rsArrecadacao->proximo();

                // Se mudar a entidade, faz lançamento totalizado
                if (!$boRetencao) {
                    if ( $inCodEntidadeOld != $rsArrecadacao->getCampo( "cod_entidade" ) or $stTipoOld != $rsArrecadacao->getCampo( "tipo" ) ) {
                        $rsArrecadacao->anterior();
                        $inCodLote = $obFTesourariaRealizacaoReceitaVariavel->getDado( "cod_lote" );
                        $stNomLote = "Arrecadação de Receita Boletim N. ".$rsArrecadacao->getCampo( "cod_boletim" )."/".$rsArrecadacao->getCampo("exercicio");

                        $obFTesourariaRealizacaoReceitaFixa->setDado( "exercicio"              , $rsArrecadacao->getCampo( "exercicio"     ) );
                        $obFTesourariaRealizacaoReceitaFixa->setDado( "valor"                  , $nuVlTotalReceita                           );
                        $obFTesourariaRealizacaoReceitaFixa->setDado( "complemento"            , '' );
                        $obFTesourariaRealizacaoReceitaFixa->setDado( "cod_lote"               , $inCodLote                                  );
                        $obFTesourariaRealizacaoReceitaFixa->setDado( "nom_lote"               , $stNomLote                                  );
                        $obFTesourariaRealizacaoReceitaFixa->setDado( "tipo_lote"              , "A"                                         );
                        $obFTesourariaRealizacaoReceitaFixa->setDado( "dt_lote"                , $this->stDataBoletim                        );
                        $obFTesourariaRealizacaoReceitaFixa->setDado( "cod_entidade"           , $rsArrecadacao->getCampo( "cod_entidade"  ) );
                        if ( Sessao::getExercicio() > '2012' ) {
                            $obFTesourariaRealizacaoReceitaFixa->setDado( "valor_despesa"          , ''                                          );
                            $obFTesourariaRealizacaoReceitaFixa->setDado( "valor_disponibilidades" , ''                                          );
                        } else {
                            $obFTesourariaRealizacaoReceitaFixa->setDado( "valor_despesa"          , $nuVlTotalDespesa                           );
                            $obFTesourariaRealizacaoReceitaFixa->setDado( "valor_disponibilidades" , $nuVlTotalDisponibilidade                   );
                        }

                        $inCodHistoricoReceitaFixa = $rsArrecadacao->getCampo( "cod_historico" );

                        if(empty($inCodHistoricoReceitaFixa)){
                            $obFTesourariaRealizacaoReceitaFixa->setDado( "cod_historico"      , null                       );
                        }else{
                            $obFTesourariaRealizacaoReceitaFixa->setDado( "cod_historico"      , $inCodHistoricoReceitaFixa );
                        }

                        $obErro = $obFTesourariaRealizacaoReceitaFixa->executaFuncao( $rsRetornoFuncao, $boTransacao );

                        $rsArrecadacao->proximo();

                        if ( !$obErro->ocorreu() ) {
                            $obFTesourariaRealizacaoReceitaVariavel->setDado( "cod_lote", "" );
                            $obFTesourariaRealizacaoReceitaFixa->setDado( "cod_lote", "" );
                            $nuVlTotalReceita = 0;
                            $nuVlTotalDespesa = 0;
                            $nuVlTotalDisponibilidade = 0;
                        }
                    }
                }
            }
            if( $obErro->ocorreu() )
                break;
        } else {
            $rsArrecadacao->proximo();
        }
    }

    return $obErro;
}

/**
    * Realiza lançamentos de estorno de arrecadação
    * @access private
    * @param Array $arCodLote
    * @param RecordSet $rsRecordSet
    * @param Object $boTransacao
    * @return Object $obErro
*/
function lancarEstornoArrecadacao(&$arCodLote, $rsArrecadacao, $boRetencao = "", $boTransacao = "")
{
    include_once CAM_GF_ORC_NEGOCIO.'ROrcamentoReceita.class.php';
    include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoReceita.class.php';
    include_once CAM_GF_CONT_NEGOCIO.'RContabilidadeDesdobramentoReceita.class.php';
    include_once CAM_GF_TES_MAPEAMENTO.'FTesourariaEstornoRealizacaoReceitaVariavel.class.php';
    include_once CAM_GF_TES_MAPEAMENTO.'FTesourariaEstornoRealizacaoReceitaFixa.class.php';
    include_once CAM_GF_CONT_MAPEAMENTO.'TContabilidadeLancamentoReceita.class.php';
    include_once CAM_GF_CONT_MAPEAMENTO.'TContabilidadeValorLancamento.class.php';
    include_once CAM_GF_CONT_NEGOCIO.'RContabilidadePlanoBanco.class.php';
    include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoConfiguracao.class.php";
    include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoRecursoDestinacao.class.php";
    include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoReceitaCreditoTributario.class.php';
    $obRContabilidadePlanoBanco = new RContabilidadePlanoBanco;
    $obRContabilidadeDesdobramentoReceita   = new RContabilidadeDesdobramentoReceita( new ROrcamentoReceita() );
    $obFTesourariaEstornoRealizacaoReceitaVariavel = new FTesourariaEstornoRealizacaoReceitaVariavel();
    $obFTesourariaEstornoRealizacaoReceitaFixa     = new FTesourariaEstornoRealizacaoReceitaFixa();

    $nuVlTotalDespesa         = 0;
    $nuVlTotalDisponibilidade = 0;
    $nuVlTotalReceita         = 0;
    $nuSomatorioReceita       = 0;
    $rsArrecadacao->setPrimeiroElemento();

    $obErro = new Erro();
    while ( !$rsArrecadacao->eof() ) {
        if ( $rsArrecadacao->getCampo( "tipo" ) == "E" ) {
            if (Sessao::getExercicio() > '2008') {
                $obTOrcamentoReceitaArrec = new TOrcamentoReceita;
                $stFiltroReceita  = ' WHERE exercicio = '.Sessao::getExercicio().'::varchar';
                $stFiltroReceita .= ' AND cod_receita = '.$rsArrecadacao->getCampo('cod_receita').' ';
                $obTOrcamentoReceitaArrec->recuperaTodos($rsReceita, $stFiltroReceita, '', $boTransacao);
                $inCodRecurso = $rsReceita->getCampo('cod_recurso');
                $boCreditoTributario = $rsReceita->getCampo('credito_tributario');

                $boDestinacao = false;
                $obTOrcamentoConfiguracao = new TOrcamentoConfiguracao;
                $obTOrcamentoConfiguracao->setDado("exercicio", Sessao::getExercicio() );
                $obTOrcamentoConfiguracao->setDado("parametro","recurso_destinacao");
                $obTOrcamentoConfiguracao->consultar($boTransacao);
                if($obTOrcamentoConfiguracao->getDado("valor") == 'true')
                    $boDestinacao = true;

                if ($boDestinacao && $inCodRecurso != '') {
                    $obTOrcamentoRecursoDestinacao = new TOrcamentoRecursoDestinacao;
                    $obTOrcamentoRecursoDestinacao->setDado("exercicio", Sessao::getExercicio());

                    $stFiltro  = ' WHERE recurso_destinacao.cod_recurso = '.$inCodRecurso;
                    $stFiltro .= '   AND recurso_destinacao.exercicio = '.Sessao::getExercicio().'::varchar';
                    $obErro = $obTOrcamentoRecursoDestinacao->recuperaTodos($rsDestinacao, $stFiltro, '', $boTransacao);
                    $inCodEspecificacao = $rsDestinacao->getCampo('cod_especificacao');

                    // Verifica qual o cod_recurso que possui conta contabil vinculada
                    $obTOrcamentoRecursoDestinacao->setDado('exercicio', Sessao::getExercicio());
                    $obTOrcamentoRecursoDestinacao->setDado('cod_especificacao', $inCodEspecificacao);
                    if ( Sessao::getExercicio() > '2012' ) {
                        $obTOrcamentoRecursoDestinacao->setDado('cod_estrutural', "'2.9.3.2.0.00.00.%'");
                    } else {
                        $obTOrcamentoRecursoDestinacao->setDado('cod_estrutural', "'8.2.1.1.1.%'");
                    }
                    $obTOrcamentoRecursoDestinacao->recuperaRecursoVinculoConta($rsContaRecurso, '', '', $boTransacao);

                    $inCodRecurso = $rsContaRecurso->getCampo('cod_recurso');
                }

                if ($inCodRecurso != '' && !$obErro->ocorreu()) {
                    $obRContabilidadePlanoBanco = new RContabilidadePlanoBanco;
                    $obRContabilidadePlanoBanco->obROrcamentoRecurso->setCodRecurso($inCodRecurso);
                    $obErro = $obRContabilidadePlanoBanco->getContasRecurso($rsContasRecurso, $boTransacao);
                    $inCodPlanoUm = $rsContasRecurso->getCampo('cod_plano_um');
                    $inCodPlanoDois = $rsContasRecurso->getCampo('cod_plano_dois');
                } else {
                    $inCodPlanoUm = '';
                    $inCodPlanoDois = '';
                }
            } else {
                $inCodPlanoUm = '';
                $inCodPlanoDois = '';
            }

            $obRContabilidadeDesdobramentoReceita->roROrcamentoReceitaPrincipal->setCodReceita( $rsArrecadacao->getCampo('cod_receita') );
            $obRContabilidadeDesdobramentoReceita->roROrcamentoReceitaPrincipal->setExercicio ( $rsArrecadacao->getCampo('exercicio') );
            $obRContabilidadeDesdobramentoReceita->roROrcamentoReceitaPrincipal->obROrcamentoEntidade->setCodigoEntidade ( $rsArrecadacao->getCampo('cod_entidade'));
            $obErro = $obRContabilidadeDesdobramentoReceita->listar( $rsReceitaSecundaria, $boTransacao );

            // Separando lotes de estorno do de arrecadação
            if(!$boRetencao)
                $obFTesourariaEstornoRealizacaoReceitaVariavel->setDado( "cod_lote", $arCodLote[$rsArrecadacao->getCampo( "cod_entidade" )][1] );
            else {
                $obFTesourariaEstornoRealizacaoReceitaVariavel->setDado( "cod_lote", '');
                $inCodLote = "";
            }

            $stEstruturalCredito = str_replace( '.', '', $rsArrecadacao->getCampo( "cod_estrutural_credito" ) );
            $stCodPlanoEstruturalCredito = str_replace( '.', '', $rsArrecadacao->getCampo( "conta_credito" ) );
            
            // Faz lancamentos das receitas secundárias
            $nuVlReceitaPrincipal = $rsArrecadacao->getCampo('valor');
            while ( !$rsReceitaSecundaria->eof() and !$obErro->ocorreu() ) {
                $nuValorParcela = number_format( $rsArrecadacao->getCampo('valor') * ( $rsReceitaSecundaria->getCampo( "percentual" ) / 100) , 2, ".", "");
                $nuSomatorioReceita   = bcadd( $nuSomatorioReceita  , $nuValorParcela, 4 );
                $nuVlReceitaPrincipal = bcsub( $nuVlReceitaPrincipal, $nuValorParcela, 4 );

                if ( Sessao::getExercicio() > '2012' ) {
                    if ($boCreditoTributario == "t") {
                        $stCodPlanoClasReceita  = $rsArrecadacao->getCampo('conta_credito'); //código da conta de banco/caixa da arrecadação
                        
                        $obTOrcamentoReceitaCreditoTributario = new TOrcamentoReceitaCreditoTributario;
                        $obTOrcamentoReceitaCreditoTributario->setDado('cod_receita' , $rsReceitaSecundaria->getCampo( "cod_receita_secundaria" ));
                        $obTOrcamentoReceitaCreditoTributario->setDado('exercicio'   , $rsReceitaSecundaria->getCampo( "exercicio" ));
                        $obErro = $obTOrcamentoReceitaCreditoTributario->recuperaContaCreditoTributario($rsContaCreditoTributario, '', '', $boTransacao);

                        if ($rsContaCreditoTributario->getNumLinhas() < 1) {
                            $obErro->setDescricao("A receita secundária ".$rsReceitaSecundaria->getCampo('cod_receita_secundaria')." não possui conta de crédito tributário cadastrada!");

                            return $obErro;
                        }

                        $stCodPlanoEstruturalCredito = $rsContaCreditoTributario->getCampo('cod_plano');
                        $stClasReceita = str_replace( ".", "", $stCodPlanoEstruturalCredito);
                        
                    } else {
                        $stFiltroConfiguracao = " WHERE configuracao_lancamento_receita.estorno = 'false'
                                                AND configuracao_lancamento_receita.exercicio = '".$rsReceitaSecundaria->getCampo('exercicio')."'::varchar
                                                AND receita.cod_receita = ".$rsReceitaSecundaria->getCampo('cod_receita_secundaria');
                        $obTContabilidadeConfiguracaoLancamentoReceita = new TContabilidadeConfiguracaoLancamentoReceita;
                        $obErro = $obTContabilidadeConfiguracaoLancamentoReceita->recuperaContasReceita( $rsLista, $stFiltroConfiguracao, "", $boTransacao );

                        if ($rsLista->getNumLinhas() < 1) {
                            $obErro->setDescricao("Não há configuração de lançamento de conta para o desdobramento da receita ".$rsReceitaSecundaria->getCampo('cod_receita_principal')."!");

                            return $obErro;
                        }
                    }
                } else {
                    $obROrcamentoReceita = new ROrcamentoReceita();
                    $obROrcamentoReceita->setExercicio( $rsReceitaSecundaria->getCampo('exercicio') );
                    $obROrcamentoReceita->setCodReceita( $rsReceitaSecundaria->getCampo('cod_receita_secundaria') );
                    $obErro = $obROrcamentoReceita->listar( $rsLista , '', $boTransacao );
                }

                if ( !$obErro->ocorreu() ) {
                    if ( Sessao::getExercicio() > '2012' ) {
                        if ($boCreditoTributario != "t") {
                            $stClasReceita = str_replace( ".", "", $rsLista->getCampo( "cod_estrutural" ));
                            $stCodPlanoClasReceita = $rsArrecadacao->getCampo('conta_credito');
                            $stCodPlanoEstruturalCredito = str_replace( ".", "", $rsLista->getCampo( "cod_plano" ));
                            
                        }

                    } else {
                        if (( substr($rsLista->getCampo("mascara_classificacao"),0,1) == 9 ) && ( Sessao::getExercicio() >= 2008 ) ) {
                            $stClasReceita = str_replace( ".", "", $rsLista->getCampo( "mascara_classificacao" ));
                        } else {
                            $stClasReceita = '4'.str_replace(".", "", $rsLista->getCampo( "mascara_classificacao" ));
                        }
                    }

                    if($boRetencao)
                         $stComplemento = $rsArrecadacao->getCampo('cod_ordem')."/".$rsArrecadacao->getCampo('exercicio');
                    else $stComplemento = ( $rsArrecadacao->getCampo("numeracao") ) ? $rsArrecadacao->getCampo("numeracao") : $rsReceitaSecundaria->getCampo("cod_receita_secundaria");

                    if($boRetencao)
                         $stNomLote = "Estorno de Arrecadação por Retenção Orçamentária - OP ".$rsArrecadacao->getCampo('cod_ordem')."/".$rsArrecadacao->getCampo('exercicio');
                    else $stNomLote = "Estorno de Arrecadação de Receita Boletim N. ".$rsArrecadacao->getCampo( "cod_boletim" )."/".$rsArrecadacao->getCampo("exercicio");

                    $obFTesourariaEstornoRealizacaoReceitaVariavel->setDado( "conta_recebimento", $stEstruturalCredito                       );
                    $obFTesourariaEstornoRealizacaoReceitaVariavel->setDado( "clas_receita"     , $stClasReceita                             );
                    $obFTesourariaEstornoRealizacaoReceitaVariavel->setDado( "exercicio"        , $rsArrecadacao->getCampo( "exercicio" )    );
                    $obFTesourariaEstornoRealizacaoReceitaVariavel->setDado( "valor"            , $nuValorParcela                            );
                    $obFTesourariaEstornoRealizacaoReceitaVariavel->setDado( "complemento"      , $stComplemento                             );
                    $obFTesourariaEstornoRealizacaoReceitaVariavel->setDado( "nom_lote"         , $stNomLote                                 );
                    $obFTesourariaEstornoRealizacaoReceitaVariavel->setDado( "tipo_lote"        , "A"                                        );
                    $obFTesourariaEstornoRealizacaoReceitaVariavel->setDado( "dt_lote"          , $this->stDataBoletim                       );
                    $obFTesourariaEstornoRealizacaoReceitaVariavel->setDado( "cod_entidade"     , $rsArrecadacao->getCampo( "cod_entidade" ) );
                    $obFTesourariaEstornoRealizacaoReceitaVariavel->setDado( "cod_reduzido"     , $rsArrecadacao->getCampo( "conta_credito" ));
                    if ($boRetencao) {
                        $obFTesourariaEstornoRealizacaoReceitaVariavel->setDado( "cod_historico"    , 951  );
                        $inCodHistoricoDesdobramento = 951;
                    } else {
                        $inCodHistoricoDesdobramento = $rsArrecadacao->getCampo( "cod_historico" );

                        if(empty($inCodHistoricoDesdobramento)){
                            $obFTesourariaEstornoRealizacaoReceitaVariavel->setDado( "cod_historico"    , null );
                            $inCodHistoricoDesdobramento = 914;
                        }else{
                            $obFTesourariaEstornoRealizacaoReceitaVariavel->setDado( "cod_historico"    , $inCodHistoricoDesdobramento );
                        }
                    }
                    if (Sessao::getExercicio() > '2012') {
                        $obFTesourariaEstornoRealizacaoReceitaVariavel->setDado( "cod_plano_conta_recebimento", $stCodPlanoEstruturalCredito                       );
                        $obFTesourariaEstornoRealizacaoReceitaVariavel->setDado( "cod_plano_clas_receita"     , $stCodPlanoClasReceita                        );
                    }
                    $obErro = $obFTesourariaEstornoRealizacaoReceitaVariavel->executaFuncao( $rsRetornoFuncao, $boTransacao );
                    
                    if ( !$obErro->ocorreu() ) {
                        $inCodLote = $obFTesourariaEstornoRealizacaoReceitaVariavel->getDado( "cod_lote" );

                        if (Sessao::getExercicio() > '2008') {
                            $obTOrcamentoReceitaLista = new TOrcamentoReceita;
                            $stFiltroDestinacao  = ' WHERE exercicio = '.Sessao::getExercicio().'::varchar ';
                            if ($boCreditoTributario != "t") {
                                $stFiltroDestinacao .= ' AND cod_receita = '.$rsLista->getCampo('cod_receita').' ';
                            } else {
                                $stFiltroDestinacao .= ' AND cod_receita = '.$rsArrecadacao->getCampo('cod_receita').' ';
                            }

                            $obTOrcamentoReceitaLista->recuperaTodos($rsDestinacao, $stFiltroDestinacao, '', $boTransacao);
                            $inCodRecursoDesdobramento = $rsDestinacao->getCampo('cod_recurso');

                            $boDestinacao = false;
                            $obTOrcamentoConfiguracao = new TOrcamentoConfiguracao;
                            $obTOrcamentoConfiguracao->setDado("exercicio", Sessao::getExercicio() );
                            $obTOrcamentoConfiguracao->setDado("parametro","recurso_destinacao");
                            $obTOrcamentoConfiguracao->consultar($boTransacao);
                            if($obTOrcamentoConfiguracao->getDado("valor") == 'true')
                                $boDestinacao = true;

                            if ($boDestinacao && $inCodRecursoDesdobramento!= '') {
                                $obTOrcamentoRecursoDestinacao = new TOrcamentoRecursoDestinacao;
                                $obTOrcamentoRecursoDestinacao->setDado("exercicio", Sessao::getExercicio());

                                $stFiltro  = ' WHERE recurso_destinacao.cod_recurso = '.$inCodRecursoDesdobramento;
                                $stFiltro .= '   AND recurso_destinacao.exercicio = '.Sessao::getExercicio().'::varchar';
                                $obErro = $obTOrcamentoRecursoDestinacao->recuperaTodos($rsDestinacao, $stFiltro, '', $boTransacao);
                                $inCodEspecificacao = $rsDestinacao->getCampo('cod_especificacao');

                                // Verifica qual o cod_recurso que possui conta contabil vinculada
                                $obTOrcamentoRecursoDestinacao->setDado('exercicio', Sessao::getExercicio());
                                $obTOrcamentoRecursoDestinacao->setDado('cod_especificacao', $inCodEspecificacao);
                                if ( Sessao::getExercicio() > '2012' ) {
                                    $obTOrcamentoRecursoDestinacao->setDado('cod_estrutural', "'2.9.3.2.0.00.00.%'");
                                } else {
                                    $obTOrcamentoRecursoDestinacao->setDado('cod_estrutural', "'8.2.1.1.1.%'");
                                }
                                $obTOrcamentoRecursoDestinacao->recuperaRecursoVinculoConta($rsContaRecurso, '', '', $boTransacao);

                                $inCodRecursoDesdobramento = $rsContaRecurso->getCampo('cod_recurso');
                            }

                            if ($inCodRecursoDesdobramento != '') {
                                $obRContabilidadePlanoBanco->obROrcamentoRecurso->setCodRecurso($inCodRecursoDesdobramento);
                                $obErro = $obRContabilidadePlanoBanco->getContasRecurso($rsContasRecursoDesdobramento, $boTransacao);
                                $inCodPlanoUmDesdobramento = $rsContasRecursoDesdobramento->getCampo('cod_plano_um');
                                $inCodPlanoDoisDesdobramento = $rsContasRecursoDesdobramento->getCampo('cod_plano_dois');

                                if ($inCodPlanoUmDesdobramento != '' && $inCodPlanoDoisDesdobramento != '' && !$obErro->ocorreu()) {
                                    $obTContabilidadeValorLancamento = new TContabilidadeValorLancamento;
                                    $obTContabilidadeValorLancamento->setDado("cod_lote", $inCodLote);
                                    $obTContabilidadeValorLancamento->setDado("tipo", 'A');
                                    $obTContabilidadeValorLancamento->setDado("exercicio", $rsArrecadacao->getCampo("exercicio"));
                                    $obTContabilidadeValorLancamento->setDado("cod_entidade", $rsArrecadacao->getCampo('cod_entidade'));
                                    $obTContabilidadeValorLancamento->setDado("cod_plano_deb", $inCodPlanoDoisDesdobramento);
                                    $obTContabilidadeValorLancamento->setDado("cod_plano_cred", $inCodPlanoUmDesdobramento);
                                    $obTContabilidadeValorLancamento->setDado("cod_historico", $inCodHistoricoDesdobramento);
                                    $obTContabilidadeValorLancamento->setDado("complemento", $stComplemento);
                                    $obTContabilidadeValorLancamento->setDado("vl_lancamento", $nuValorParcela);

                                    $obErro = $obTContabilidadeValorLancamento->inclusaoPorPl($rsRecordSet, $boTransacao);
                                }
                            }
                        }

                        $obTContabilidadeLancamentoReceita = new TContabilidadeLancamentoReceita();
                        $obTContabilidadeLancamentoReceita->setDado( "cod_lote"    , $inCodLote                                       );
                        $obTContabilidadeLancamentoReceita->setDado( "tipo"        , "A"                                              );
                        $obTContabilidadeLancamentoReceita->setDado( "sequencia"   , $rsRetornoFuncao->getCampo( "sequencia" )        );
                        $obTContabilidadeLancamentoReceita->setDado( "exercicio"   , $this->stExercicio                               );
                        $obTContabilidadeLancamentoReceita->setDado( "cod_entidade", $this->obROrcamentoEntidade->getCodigoEntidade() );
                        $obTContabilidadeLancamentoReceita->setDado( "cod_receita" , $rsReceitaSecundaria->getCampo( "cod_receita_secundaria" ) );
                        $obTContabilidadeLancamentoReceita->setDado( "estorno"     , "true"                                           );
                        $obErro = $obTContabilidadeLancamentoReceita->inclusao( $boTransacao );

                        /* ESTORNO | Receita a realizar | Receita Realizada | Dívida Ativa | Retenções */
                        if (!$obErro->ocorreu() && $boRetencao ) {
                            $nuVlDespesaRet = 0.00;
                            $nuVlDisponibilidadeRet = 0.00;
                            if ( substr( $stClasReceita, 1, 4 ) == "1931" ) {
                                $nuVlDespesaRet = $rsArrecadacao->getCampo( "valor" );
                            }
                            if ( substr( $stClasReceita, 1, 4 ) == "1932" ) {
                                $nuVlDisponibilidadeRet = $rsArrecadacao->getCampo( "valor" );
                            }
                            $obFTesourariaEstornoRealizacaoReceitaFixa->setDado( "exercicio"              , $rsArrecadacao->getCampo( "exercicio"     ) );
                            $obFTesourariaEstornoRealizacaoReceitaFixa->setDado( "valor"                  , $nuValorParcela                             );
                            $obFTesourariaEstornoRealizacaoReceitaFixa->setDado( "complemento"            , $stComplemento                              );
                            $obFTesourariaEstornoRealizacaoReceitaFixa->setDado( "cod_lote"               , $inCodLote                                  );
                            $obFTesourariaEstornoRealizacaoReceitaFixa->setDado( "nom_lote"               , $stNomLote                                  );
                            $obFTesourariaEstornoRealizacaoReceitaFixa->setDado( "tipo_lote"              , "A"                                         );
                            $obFTesourariaEstornoRealizacaoReceitaFixa->setDado( "dt_lote"                , $this->stDataBoletim                        );
                            $obFTesourariaEstornoRealizacaoReceitaFixa->setDado( "cod_entidade"           , $rsArrecadacao->getCampo( "cod_entidade"  ) );
                            if ( Sessao::getExercicio() > '2012' ) {
                                $obFTesourariaEstornoRealizacaoReceitaFixa->setDado( "valor_despesa"          , ''                                          );
                                $obFTesourariaEstornoRealizacaoReceitaFixa->setDado( "valor_disponibilidades" , ''                                          );
                            } else {
                                $obFTesourariaEstornoRealizacaoReceitaFixa->setDado( "valor_despesa"          , $nuVlDespesaRet                             );
                                $obFTesourariaEstornoRealizacaoReceitaFixa->setDado( "valor_disponibilidades" , $nuVlDisponibilidadeRet                     );
                            }
                            if ($boRetencao) {
                                $obFTesourariaEstornoRealizacaoReceitaFixa->setDado( "cod_historico"          , 951                 );
                            } else{
                                $inCodHistoricoReceitaFixa = $rsArrecadacao->getCampo( "cod_historico" );

                                if(empty($inCodHistoricoReceitaFixa)){
                                    $obFTesourariaEstornoRealizacaoReceitaFixa->setDado( "cod_historico"      , null                       );
                                }else{
                                    $obFTesourariaEstornoRealizacaoReceitaFixa->setDado( "cod_historico"      , $inCodHistoricoReceitaFixa );
                                }
                            }
                            $obErro = $obFTesourariaEstornoRealizacaoReceitaFixa->executaFuncao( $rsRetornoFuncao, $boTransacao );
                        }

                    }
                }
                $rsReceitaSecundaria->proximo();
            }

            if (!$obErro->ocorreu()) {
                $stCodPlanoEstruturalCredito = str_replace( '.', '', $rsArrecadacao->getCampo( "conta_credito" ) );
                $stEstruturalDebito  = str_replace( '.', '', $rsArrecadacao->getCampo( "cod_estrutural_debito"  ) );

                if ( Sessao::getExercicio() > '2012' ) {
                    if ($boCreditoTributario == "t") {
                        $stCodPlanoEstruturalCredito  = $rsArrecadacao->getCampo('conta_credito'); //código da conta de banco/caixa da arrecadação
                        
                        $obTOrcamentoReceitaCreditoTributario = new TOrcamentoReceitaCreditoTributario;
                        $obTOrcamentoReceitaCreditoTributario->setDado('cod_receita' , $rsArrecadacao->getCampo( "cod_receita" ));
                        $obTOrcamentoReceitaCreditoTributario->setDado('exercicio'   , $rsArrecadacao->getCampo( "exercicio" ));
                        $obErro = $obTOrcamentoReceitaCreditoTributario->recuperaContaCreditoTributario($rsContaCreditoTributario, '', '', $boTransacao);
                        
                        if ($rsContaCreditoTributario->getNumLinhas() < 1) {
                            $obErro->setDescricao("A receita ".$rsArrecadacao->getCampo('cod_receita')." não possui conta de crédito tributário cadastrada!");
                            return $obErro;
                        }
                        
                        $stCodPlanoEstruturalDebito = $rsContaCreditoTributario->getCampo('cod_plano');
                    } else {
                        if (empty($stEstruturalDebito)) {
                            $stFiltroConfiguracao = " WHERE configuracao_lancamento_receita.estorno = 'false'
                                                        AND configuracao_lancamento_receita.exercicio = '".$rsArrecadacao->getCampo('exercicio')."'::varchar
                                                        AND receita.cod_receita = ".$rsArrecadacao->getCampo('cod_receita');
                            $obTContabilidadeConfiguracaoLancamentoReceita = new TContabilidadeConfiguracaoLancamentoReceita;
                            $obErro = $obTContabilidadeConfiguracaoLancamentoReceita->recuperaContasReceita( $rsListaConfiguracao, $stFiltroConfiguracao, "", $boTransacao );
                            
                            if ($rsListaConfiguracao->getNumLinhas() < 1) {
                                $obErro->setDescricao("Não há configuração de lançamento de conta para o desdobramento da receita ".$rsArrecadacao->getCampo('cod_receita')."!");
                                return $obErro;
                            }
                            
                            $stEstruturalDebito  = str_replace( '.', '', $rsListaConfiguracao->getCampo( "cod_estrutural"  ) );
                            $stCodPlanoEstruturalDebito  = str_replace( '.', '', $rsListaConfiguracao->getCampo( "cod_plano"  ) );
                        }
                    }
                }
                
                if (!$obErro->ocorreu()) {
                    if($boRetencao)
                         $stComplemento = $rsArrecadacao->getCampo('cod_ordem')."/".$rsArrecadacao->getCampo('exercicio');
                    else $stComplemento = ( $rsArrecadacao->getCampo("numeracao") ) ? $rsArrecadacao->getCampo("numeracao") : $rsArrecadacao->getCampo("cod_receita");
                    
                    if($boRetencao)
                         $stNomLote = "Estorno de Arrecadação por Retenção Orçamentária - OP ".$rsArrecadacao->getCampo('cod_ordem')."/".$rsArrecadacao->getCampo('exercicio');
                    else $stNomLote = "Estorno de Arrecadação de Receita Boletim N. ".$rsArrecadacao->getCampo( "cod_boletim" )."/".$rsArrecadacao->getCampo("exercicio");
                    
                    $obFTesourariaEstornoRealizacaoReceitaVariavel->setDado( "conta_recebimento", $stEstruturalCredito                       );
                    $obFTesourariaEstornoRealizacaoReceitaVariavel->setDado( "clas_receita"     , $stEstruturalDebito                        );
                    $obFTesourariaEstornoRealizacaoReceitaVariavel->setDado( "exercicio"        , $rsArrecadacao->getCampo( "exercicio"    ) );
                    $obFTesourariaEstornoRealizacaoReceitaVariavel->setDado( "valor"            , $nuVlReceitaPrincipal                      );
                    $obFTesourariaEstornoRealizacaoReceitaVariavel->setDado( "complemento"      , $stComplemento                             );
                    $obFTesourariaEstornoRealizacaoReceitaVariavel->setDado( "nom_lote"         , $stNomLote                                 );
                    $obFTesourariaEstornoRealizacaoReceitaVariavel->setDado( "cod_lote"         , $inCodLote                                 );
                    $obFTesourariaEstornoRealizacaoReceitaVariavel->setDado( "tipo_lote"        , "A"                                        );
                    $obFTesourariaEstornoRealizacaoReceitaVariavel->setDado( "dt_lote"          , $this->stDataBoletim                       );
                    $obFTesourariaEstornoRealizacaoReceitaVariavel->setDado( "cod_entidade"     , $rsArrecadacao->getCampo( "cod_entidade" ) );
                    $obFTesourariaEstornoRealizacaoReceitaVariavel->setDado( "cod_reduzido"     , $rsArrecadacao->getCampo( "conta_debito" ) );
                    
                    if ($boRetencao) {
                        $obFTesourariaEstornoRealizacaoReceitaVariavel->setDado( "cod_historico"    , 951  );
                        $inCodHistorico = 951;
                    } else {
                        $inCodHistorico = $rsArrecadacao->getCampo( "cod_historico" );

                        if(empty($inCodHistorico)){
                            $obFTesourariaEstornoRealizacaoReceitaVariavel->setDado( "cod_historico"    , null );
                            $inCodHistorico = 914;
                        }else{
                            $obFTesourariaEstornoRealizacaoReceitaVariavel->setDado( "cod_historico"    , $inCodHistorico );
                        }
                    }
                    
                    if (Sessao::getExercicio() > '2012') {
                        $obFTesourariaEstornoRealizacaoReceitaVariavel->setDado( "cod_plano_conta_recebimento"     , $stCodPlanoEstruturalDebito );
                        $obFTesourariaEstornoRealizacaoReceitaVariavel->setDado( "cod_plano_clas_receita"     , $stCodPlanoEstruturalCredito     );
                    }
                    
                    $obErro = $obFTesourariaEstornoRealizacaoReceitaVariavel->executaFuncao( $rsRetornoFuncao, $boTransacao );
                    
                    $inCodLote = $obFTesourariaEstornoRealizacaoReceitaVariavel->getDado( "cod_lote" );
                    
                    if (Sessao::getExercicio() > '2008' && !$obErro->ocorreu()) {
                        if ($inCodPlanoUm != '' && $inCodPlanoDois != '') {
                            $obTContabilidadeValorLancamento = new TContabilidadeValorLancamento;
                            $obTContabilidadeValorLancamento->setDado("cod_lote", $inCodLote);
                            $obTContabilidadeValorLancamento->setDado("tipo", 'A');
                            $obTContabilidadeValorLancamento->setDado("exercicio", $rsArrecadacao->getCampo("exercicio"));
                            $obTContabilidadeValorLancamento->setDado("cod_entidade", $rsArrecadacao->getCampo('cod_entidade'));
                            $obTContabilidadeValorLancamento->setDado("cod_plano_deb", $inCodPlanoDois);
                            $obTContabilidadeValorLancamento->setDado("cod_plano_cred", $inCodPlanoUm);
                            $obTContabilidadeValorLancamento->setDado("cod_historico", $inCodHistorico);
                            $obTContabilidadeValorLancamento->setDado("complemento", $stComplemento);
                            $obTContabilidadeValorLancamento->setDado("vl_lancamento", $nuVlReceitaPrincipal);

                            $obErro = $obTContabilidadeValorLancamento->inclusaoPorPl($rsRecordSet, $boTransacao);
                        }
                    }

                    if($boRetencao)
                         $arCodLote['retencao'][] = $obFTesourariaEstornoRealizacaoReceitaVariavel->getDado( "cod_lote" );
                    else $arCodLote[$rsArrecadacao->getCampo("cod_entidade")][1] = $obFTesourariaEstornoRealizacaoReceitaVariavel->getDado( "cod_lote" );
                }
            }

            if ( !$obErro->ocorreu() ) {
                $obTContabilidadeLancamentoReceita = new TContabilidadeLancamentoReceita();
                $obTContabilidadeLancamentoReceita->setDado( "cod_lote"    , $inCodLote                                       );
                $obTContabilidadeLancamentoReceita->setDado( "tipo"        , "A"                                              );
                $obTContabilidadeLancamentoReceita->setDado( "sequencia"   , $rsRetornoFuncao->getCampo( "sequencia" )        );
                $obTContabilidadeLancamentoReceita->setDado( "exercicio"   , $this->stExercicio                               );
                $obTContabilidadeLancamentoReceita->setDado( "cod_entidade", $this->obROrcamentoEntidade->getCodigoEntidade() );
                $obTContabilidadeLancamentoReceita->setDado( "cod_receita" , $rsArrecadacao->getCampo( "cod_receita" )        );
                $obTContabilidadeLancamentoReceita->setDado( "estorno"     , "true"                                           );
                $obErro = $obTContabilidadeLancamentoReceita->inclusao( $boTransacao );
                if ($boRetencao && !$obErro->ocorreu()) {
                    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeLancamentoRetencao.class.php" );
                    $obTContabilidadeLancamentoRetencao = new TContabilidadeLancamentoRetencao;
                    $obTContabilidadeLancamentoRetencao->setDado('tipo'     , 'A'        );
                    $obTContabilidadeLancamentoRetencao->setDado('cod_lote' , $inCodLote );
                    $obTContabilidadeLancamentoRetencao->setDado('cod_entidade' , $this->obROrcamentoEntidade->getCodigoEntidade() );
                    $obTContabilidadeLancamentoRetencao->setDado('exercicio', $this->stExercicio );
                    $obTContabilidadeLancamentoRetencao->setDado('sequencia', $rsRetornoFuncao->getCampo( "sequencia" ) );
                    $obTContabilidadeLancamentoRetencao->setDado('cod_ordem', $rsArrecadacao->getCampo('cod_ordem') );
                    $obTContabilidadeLancamentoRetencao->setDado('cod_plano', $rsArrecadacao->getCampo('cod_plano') );
                    $obTContabilidadeLancamentoRetencao->setDado('exercicio_retencao', $this->stExercicio );
                    $obTContabilidadeLancamentoRetencao->setDado('estorno', true );
                    $obErro = $obTContabilidadeLancamentoRetencao->inclusao( $boTransacao );

                    /* ESTORNO | Receita a realizar | Receita Realizada | Divida Ativa | Retenções */
                    if (!$obErro->ocorreu()) {
                        $nuVlDespesaRet = 0.00;
                        $nuVlDisponibilidadeRet = 0.00;

                        if ( substr( $stEstruturalDebito, 1, 4 ) == "1931" ) {
                            $nuVlDespesaRet = $nuVlReceitaPrincipal;
                        }
                        if ( substr( $stEstruturalDebito, 1, 4 ) == "1932" ) {
                            $nuVlDisponibilidadeRet = $nuVlReceitaPrincipal;
                        }
                        $obFTesourariaEstornoRealizacaoReceitaFixa->setDado( "exercicio"              , $rsArrecadacao->getCampo( "exercicio"     ) );
                        $obFTesourariaEstornoRealizacaoReceitaFixa->setDado( "valor"                  , $nuVlReceitaPrincipal                       );
                        $obFTesourariaEstornoRealizacaoReceitaFixa->setDado( "complemento"            , $stComplemento                              );
                        $obFTesourariaEstornoRealizacaoReceitaFixa->setDado( "cod_lote"               , $inCodLote                                  );
                        $obFTesourariaEstornoRealizacaoReceitaFixa->setDado( "nom_lote"               , $stNomLote                                  );
                        $obFTesourariaEstornoRealizacaoReceitaFixa->setDado( "tipo_lote"              , "A"                                         );
                        $obFTesourariaEstornoRealizacaoReceitaFixa->setDado( "dt_lote"                , $this->stDataBoletim                        );
                        $obFTesourariaEstornoRealizacaoReceitaFixa->setDado( "cod_entidade"           , $rsArrecadacao->getCampo( "cod_entidade"  ) );
                        if ( Sessao::getExercicio() > '2012' ) {
                            $obFTesourariaEstornoRealizacaoReceitaFixa->setDado( "valor_despesa"          , ''                                          );
                            $obFTesourariaEstornoRealizacaoReceitaFixa->setDado( "valor_disponibilidades" , ''                                          );
                        } else {
                            $obFTesourariaEstornoRealizacaoReceitaFixa->setDado( "valor_despesa"          , $nuVlDespesaRet                             );
                            $obFTesourariaEstornoRealizacaoReceitaFixa->setDado( "valor_disponibilidades" , $nuVlDisponibilidadeRet                     );
                        }
                        $obFTesourariaEstornoRealizacaoReceitaFixa->setDado( "cod_historico"          , 951                 );
                        $obErro = $obFTesourariaEstornoRealizacaoReceitaFixa->executaFuncao( $rsRetornoFuncao, $boTransacao );
                    }
                }
            }

            if ( !$obErro->ocorreu() ) {
                $nuVlTotalReceita = bcadd( $nuVlTotalReceita, $rsArrecadacao->getCampo( "valor" ), 4 );
                if ( substr( $stEstruturalDebito, 1, 4 ) == "1931" ) {
                    $nuVlTotalDespesa = bcadd( $nuVlTotalDespesa, $rsArrecadacao->getCampo( "valor" ), 4 );
                }
                if ( substr( $stEstruturalDebito, 1, 4 ) == "1932" ) {
                    $nuVlTotalDisponibilidade = bcadd( $nuVlTotalDisponibilidade, $rsArrecadacao->getCampo( "valor" ), 4 );
                }
                $inCodEntidadeOld = $rsArrecadacao->getCampo( "cod_entidade" );
                $stTipoOld        = $rsArrecadacao->getCampo( "tipo" );
                $rsArrecadacao->proximo();

                if (!$boRetencao) {
                    // Se mudar a entidade, faz lançamento totalizado
                    if ( $inCodEntidadeOld != $rsArrecadacao->getCampo( "cod_entidade" ) or $stTipoOld != $rsArrecadacao->getCampo( "tipo" ) ) {
                        $rsArrecadacao->anterior();
                        $inCodLote = $obFTesourariaEstornoRealizacaoReceitaVariavel->getDado( "cod_lote" );
                        $stNomLote = "Estorno de Arrecadação de Receita Boletim N. ".$rsArrecadacao->getCampo( "cod_boletim" )."/".$rsArrecadacao->getCampo("exercicio");

                        $obFTesourariaEstornoRealizacaoReceitaFixa->setDado( "exercicio"              , $rsArrecadacao->getCampo( "exercicio"     ) );
                        $obFTesourariaEstornoRealizacaoReceitaFixa->setDado( "valor"                  , $nuVlTotalReceita                           );
                        $obFTesourariaEstornoRealizacaoReceitaFixa->setDado( "complemento"            , ''                                          );
                        $obFTesourariaEstornoRealizacaoReceitaFixa->setDado( "cod_lote"               , $inCodLote                                  );
                        $obFTesourariaEstornoRealizacaoReceitaFixa->setDado( "nom_lote"               , $stNomLote                                  );
                        $obFTesourariaEstornoRealizacaoReceitaFixa->setDado( "tipo_lote"              , "A"                                         );
                        $obFTesourariaEstornoRealizacaoReceitaFixa->setDado( "dt_lote"                , $this->stDataBoletim                        );
                        $obFTesourariaEstornoRealizacaoReceitaFixa->setDado( "cod_entidade"           , $rsArrecadacao->getCampo( "cod_entidade"  ) );
                        if ( Sessao::getExercicio() > '2012' ) {
                            $obFTesourariaEstornoRealizacaoReceitaFixa->setDado( "valor_despesa"          , ''                                          );
                            $obFTesourariaEstornoRealizacaoReceitaFixa->setDado( "valor_disponibilidades" , ''                                          );
                        } else {
                            $obFTesourariaEstornoRealizacaoReceitaFixa->setDado( "valor_despesa"          , $nuVlTotalDespesa                           );
                            $obFTesourariaEstornoRealizacaoReceitaFixa->setDado( "valor_disponibilidades" , $nuVlTotalDisponibilidade                   );
                        }

                        $inCodHistoricoReceitaFixa = $rsArrecadacao->getCampo( "cod_historico" );

                        if(empty($inCodHistoricoReceitaFixa)){
                            $obFTesourariaEstornoRealizacaoReceitaFixa->setDado( "cod_historico"      , null                       );
                        }else{
                            $obFTesourariaEstornoRealizacaoReceitaFixa->setDado( "cod_historico"      , $inCodHistoricoReceitaFixa );
                        }

                        $obErro = $obFTesourariaEstornoRealizacaoReceitaFixa->executaFuncao( $rsRetornoFuncao, $boTransacao );

                        $rsArrecadacao->proximo();

                        if ( !$obErro->ocorreu() ) {
                            $obFTesourariaEstornoRealizacaoReceitaVariavel->setDado( "cod_lote", "" );
                            $obFTesourariaEstornoRealizacaoReceitaFixa->setDado( "cod_lote", "" );
                            $nuVlTotalReceita = 0;
                            $nuVlTotalDespesa = 0;
                            $nuVlTotalDisponibilidade = 0;

                        }
                    }
                }
            }
             if($obErro->ocorreu())
                break;
        } else {
            $rsArrecadacao->proximo();
        }
    }

    return $obErro;
}

/**
    * Libera Boletim para Contabilidade
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function liberar($boTransacao = "", $boArrecadar=true)
{
    include_once ( CAM_FW_BANCO_DADOS   ."Transacao.class.php"                            );
    include_once ( CAM_GF_TES_MAPEAMENTO."TTesourariaBoletimLiberado.class.php"           );
    include_once ( CAM_GF_TES_MAPEAMENTO."TTesourariaBoletimLiberadoLote.class.php"       );
    $obTransacao                            = new Transacao();
    $obTTesourariaBoletimLiberado           = new TTesourariaBoletimLiberado();
    $obTTesourariaBoletimLiberadoLote       = new TTesourariaBoletimLiberadoLote();

    $boFlagTransacao = false;
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->listar( $rsBoletim , '', '', $boTransacao );
        if ( $rsBoletim->getCampo( "situacao" ) != "fechado" ) {
            $obErro->setDescricao( "Para ser liberado, o boletim ".$rsBoletim->getCampo('cod_boletim')." precisa estar fechado ( Situação: ".$rsBoletim->getCampo( "situacao" )." )" );
        }
        if ( !$obErro->ocorreu() ) {
            $this->stDataBoletim = $rsBoletim->getCampo( "data_boletim" );
        }
    }
    $stTimstampLiberado = date( "Y-m-d H:i:s.ms" );

    if ( !$obErro->ocorreu() ) {
        if ($boArrecadar) {
            $this->addArrecadacao();
        }

        $obErro = $this->roUltimaArrecadacao->listarArrecadacaoValorConta( $rsArrecadacao, '', $boTransacao );
        if (!$obErro->ocorreu()) {
            $obErro = $this->roUltimaArrecadacao->listarArrecadacaoValorConta( $rsArrecadacaoRetencao, '', $boTransacao, $boRetencao = true );
        }

        // Faz lancamentos se existir arrecadação para o boletim
        if (!$obErro->ocorreu() && (($rsArrecadacao->getNumLinhas() > -1) OR ($rsArrecadacaoRetencao->getNumLinhas() > -1))) {

            if ( !$obErro->ocorreu() ) {
                $obErro = $this->lancarArrecadacao( $arCodLote, $rsArrecadacao, '', $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    $obErro = $this->lancarEstornoArrecadacao( $arCodLote, $rsArrecadacao, '', $boTransacao );
                    if ( !$obErro->ocorreu() ) {
                        $obErro = $this->lancarArrecadacao( $arCodLoteRet, $rsArrecadacaoRetencao, $boRetencao = true, $boTransacao );
                        if ( !$obErro->ocorreu() ) {
                            $obErro = $this->lancarEstornoArrecadacao( $arCodLoteRet, $rsArrecadacaoRetencao, $boRetencao = true, $boTransacao );
                        }
                    }
                }
            }
            if ( !$obErro->ocorreu() ) {
                $obTTesourariaBoletimLiberado->setDado( "cod_boletim"         , $this->inCodBoletim                            );
                $obTTesourariaBoletimLiberado->setDado( "exercicio"           , $this->stExercicio                             );
                $obTTesourariaBoletimLiberado->setDado( "cod_entidade"        , $this->obROrcamentoEntidade->getCodigoEntidade() );
                $obTTesourariaBoletimLiberado->setDado( "timestamp_liberado"  , $stTimstampLiberado                            );
                $obTTesourariaBoletimLiberado->setDado( "timestamp_fechamento", $rsBoletim->getCampo( "timestamp_fechamento" ) );
                $obTTesourariaBoletimLiberado->setDado( "cod_terminal"        , $this->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->getCodTerminal() );
                $obTTesourariaBoletimLiberado->setDado( "timestamp_terminal"  , $this->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->getTimestampTerminal() );
                $obTTesourariaBoletimLiberado->setDado( "cgm_usuario"         , $this->obRTesourariaUsuarioTerminal->obRCGM->getNumCGM()   );
                $obTTesourariaBoletimLiberado->setDado( "timestamp_usuario"   , $this->obRTesourariaUsuarioTerminal->getTimestampUsuario() );
                $obErro = $obTTesourariaBoletimLiberado->inclusao( $boTransacao );

                if (is_array($arCodLote[$this->obROrcamentoEntidade->getCodigoEntidade()]) && !$obErro->ocorreu() ) {
                    foreach ( $arCodLote[$this->obROrcamentoEntidade->getCodigoEntidade()] as $key => $inCodLote ) {
                        $obTTesourariaBoletimLiberadoLote->setDado('cod_entidade'        , $this->obROrcamentoEntidade->getCodigoEntidade() );
                        $obTTesourariaBoletimLiberadoLote->setDado('cod_boletim'         , $this->inCodBoletim                           );
                        $obTTesourariaBoletimLiberadoLote->setDado('timestamp_fechamento', $rsBoletim->getCampo( "timestamp_fechamento" ));
                        $obTTesourariaBoletimLiberadoLote->setDado('timestamp_liberado'  , $stTimstampLiberado                           );
                        $obTTesourariaBoletimLiberadoLote->setDado('exercicio'           , $this->stExercicio                            );
                        $obTTesourariaBoletimLiberadoLote->setDado('tipo'                , 'A'                                           );
                        $obTTesourariaBoletimLiberadoLote->setDado('cod_lote'            , $inCodLote                                    );
                        $obErro = $obTTesourariaBoletimLiberadoLote->inclusao( $boTransacao );

                        if ( $obErro->ocorreu() ) {
                           break;
                        }
                    }
                }

                if (is_array($arCodLoteRet['retencao']) && !$obErro->ocorreu() ) {
                    foreach ($arCodLoteRet['retencao'] as $item => $inCodLote) {
                        $obTTesourariaBoletimLiberadoLote->setDado('cod_entidade'        , $this->obROrcamentoEntidade->getCodigoEntidade() );
                        $obTTesourariaBoletimLiberadoLote->setDado('cod_boletim'         , $this->inCodBoletim                              );
                        $obTTesourariaBoletimLiberadoLote->setDado('timestamp_fechamento', $rsBoletim->getCampo( "timestamp_fechamento" )   );
                        $obTTesourariaBoletimLiberadoLote->setDado('timestamp_liberado'  , $stTimstampLiberado                              );
                        $obTTesourariaBoletimLiberadoLote->setDado('exercicio'           , $this->stExercicio                               );
                        $obTTesourariaBoletimLiberadoLote->setDado('tipo'                , 'A'                                              );
                        $obTTesourariaBoletimLiberadoLote->setDado('cod_lote'            , $inCodLote                                       );
                        $obErro = $obTTesourariaBoletimLiberadoLote->inclusao( $boTransacao );
                        if( $obErro->ocorreu() )
                           break;
                    }
                }
            }
        } else {
            if ( !$obErro->ocorreu() ) {
                $obTTesourariaBoletimLiberado->setDado( "cod_boletim"         , $this->inCodBoletim                            );
                $obTTesourariaBoletimLiberado->setDado( "exercicio"           , $this->stExercicio                             );
                $obTTesourariaBoletimLiberado->setDado( "cod_entidade"        , $this->obROrcamentoEntidade->getCodigoEntidade() );
                $obTTesourariaBoletimLiberado->setDado( "timestamp_liberado"  , $stTimstampLiberado                            );
                $obTTesourariaBoletimLiberado->setDado( "timestamp_fechamento", $rsBoletim->getCampo( "timestamp_fechamento" ) );
                $obTTesourariaBoletimLiberado->setDado( "cod_terminal"        , $this->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->getCodTerminal() );
                $obTTesourariaBoletimLiberado->setDado( "timestamp_terminal"  , $this->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->getTimestampTerminal() );
                $obTTesourariaBoletimLiberado->setDado( "cgm_usuario"         , $this->obRTesourariaUsuarioTerminal->obRCGM->getNumCGM()   );
                $obTTesourariaBoletimLiberado->setDado( "timestamp_usuario"   , $this->obRTesourariaUsuarioTerminal->getTimestampUsuario() );
                $obErro = $obTTesourariaBoletimLiberado->inclusao( $boTransacao );
            }
        }
    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro , $obTTesourariaBoletimLiberado );

    return $obErro;
}

function cancelarLiberacao($boTransacao = "")
{
    include_once( CAM_FW_BANCO_DADOS   ."Transacao.class.php"                           );
    include_once( CAM_GF_TES_MAPEAMENTO."TTesourariaBoletimLiberado.class.php"          );
    include_once( CAM_GF_TES_MAPEAMENTO."TTesourariaBoletimLiberadoLote.class.php"          );
    include_once( CAM_GF_TES_MAPEAMENTO."TTesourariaBoletimLiberadoCancelado.class.php" );
    include_once( CAM_GF_CONT_MAPEAMENTO."TContabilidadeLancamentoReceita.class.php"    );
    include_once( CAM_GF_CONT_NEGOCIO  ."RContabilidadeLancamentoValor.class.php"       );
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeLancamentoRetencao.class.php"     );

    $stFiltro="";
    $stOrder="";

    $obTransacao                           = new Transacao();
    $obTTesourariaBoletimLiberado          = new TTesourariaBoletimLiberado();
    $obTTesourariaBoletimLiberadoLote      = new TTesourariaBoletimLiberadoLote();
    $obTTesourariaBoletimLiberadoCancelado = new TTesourariaBoletimLiberadoCancelado();
    $obRContabilidadeLancamentoValor       = new RContabilidadeLancamentoValor();

    $stTimestampCancelado = date( "Y-m-d H:i:s.ms" );

    $boFlagTransacao = false;
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if( $this->stExercicio )
            $stFiltro .= " exercicio = '".$this->stExercicio."'::varchar AND ";
        if( $this->inCodBoletim )
            $stFiltro .= " cod_boletim = ".$this->inCodBoletim." AND ";
        if( $this->obROrcamentoEntidade->getCodigoEntidade() )
            $stFiltro .= " cod_entidade IN( ".$this->obROrcamentoEntidade->getCodigoEntidade()." ) AND ";

        $stFiltro = ( $stFiltro!="" ) ? " WHERE ".substr( $stFiltro, 0, strlen($stFiltro)-4) : '';
        $obErro = $obTTesourariaBoletimLiberado->recuperaTodos( $rsBoletimLiberado, $stFiltro, $stOrder, $boTransacao );

        if ( !$obErro->ocorreu() ) {

        if( $this->stExercicio )
            $stFiltro = " exercicio = '".$this->stExercicio."'::varchar AND ";
        if( $this->inCodBoletim )
            $stFiltro .= " cod_boletim = ".$this->inCodBoletim." AND ";
        if( $this->obROrcamentoEntidade->getCodigoEntidade() )
            $stFiltro .= " cod_entidade IN( ".$this->obROrcamentoEntidade->getCodigoEntidade()." ) AND ";
            $stFiltro .= " timestamp_liberado = '".$rsBoletimLiberado->getCampo( 'timestamp_liberado' )."' AND ";
            $stFiltro .= " timestamp_fechamento = '".$rsBoletimLiberado->getCampo( 'timestamp_fechamento' )."' AND ";

        $stFiltro = ( $stFiltro!="" ) ? " WHERE ".substr( $stFiltro, 0, strlen($stFiltro)-4) : '';
        $obErro = $obTTesourariaBoletimLiberadoLote->recuperaTodos( $rsBoletimLiberadoLote, $stFiltro, $stOrder, $boTransacao );
          }

        if ( !$obErro->ocorreu() ) {
            $obTTesourariaBoletimLiberadoLote->setDado( "exercicio"  , $this->stExercicio  );
            $obTTesourariaBoletimLiberadoLote->setDado( "cod_boletim", $this->inCodBoletim );
            $obTTesourariaBoletimLiberadoLote->setDado( "timestamp_liberado"  , $rsBoletimLiberado->getCampo( "timestamp_liberado"   ) );
            $obTTesourariaBoletimLiberadoLote->setDado( "timestamp_fechamento", $rsBoletimLiberado->getCampo( "timestamp_fechamento" ) );
            $obTTesourariaBoletimLiberadoLote->setDado( "cod_entidade"        , $rsBoletimLiberado->getCampo( "cod_entidade"         ) );
            $obErro = $obTTesourariaBoletimLiberadoLote->exclusao( $boTransacao );
        }

        if ( !$obErro->ocorreu() ) {
            while ( !$rsBoletimLiberado->eof() ) {
                $obTTesourariaBoletimLiberado->setDado( "exercicio"  , $this->stExercicio  );
                $obTTesourariaBoletimLiberado->setDado( "cod_boletim", $this->inCodBoletim );
                $obTTesourariaBoletimLiberado->setDado( "timestamp_liberado"  , $rsBoletimLiberado->getCampo( "timestamp_liberado"   ) );
                $obTTesourariaBoletimLiberado->setDado( "timestamp_fechamento", $rsBoletimLiberado->getCampo( "timestamp_fechamento" ) );
                $obTTesourariaBoletimLiberado->setDado( "cod_entidade"        , $rsBoletimLiberado->getCampo( "cod_entidade"         ) );
                $obErro = $obTTesourariaBoletimLiberado->exclusao( $boTransacao );

                if ( !$obErro->ocorreu() ) {

                    while ( !$rsBoletimLiberadoLote->eof() ) {

                        $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setExercicio( $this->stExercicio );
                        $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setCodLote( $rsBoletimLiberadoLote->getCampo("cod_lote") );
                        $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setTipo( "A" );
                        $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $rsBoletimLiberadoLote->getCampo("cod_entidade"));
                        $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->setSequencia( "" );
                        $obErro = $obRContabilidadeLancamentoValor->listar( $rsLancamento, "", $boTransacao );
                        if ( !$obErro->ocorreu() ) {
                            while ( !$rsLancamento->eof() ) {

                                $obTContabilidadeLancamentoRetencao = new TContabilidadeLancamentoRetencao();
                                $obTContabilidadeLancamentoRetencao->setDado( "cod_lote"    , $rsBoletimLiberadoLote->getCampo( "cod_lote" )     );
                                $obTContabilidadeLancamentoRetencao->setDado( "tipo"        , "A"                 );
                                $obTContabilidadeLancamentoRetencao->setDado( "sequencia"   , $rsLancamento->getCampo( "sequencia" )         );
                                $obTContabilidadeLancamentoRetencao->setDado( "exercicio"   , $this->stExercicio                 );
                                $obTContabilidadeLancamentoRetencao->setDado( "cod_entidade", $rsBoletimLiberadoLote->getCampo( "cod_entidade" ) );
                                $obErro = $obTContabilidadeLancamentoRetencao->exclusao( $boTransacao );

                                if ( !$obErro->ocorreu() ) {

                                    $obTContabilidadeLancamentoReceita = new TContabilidadeLancamentoReceita();
                                    $obTContabilidadeLancamentoReceita->setDado( "cod_lote"    , $rsBoletimLiberadoLote->getCampo( "cod_lote" )     );
                                    $obTContabilidadeLancamentoReceita->setDado( "tipo"        , "A"                                            );
                                    $obTContabilidadeLancamentoReceita->setDado( "sequencia"   , $rsLancamento->getCampo( "sequencia" )         );
                                    $obTContabilidadeLancamentoReceita->setDado( "exercicio"   , $this->stExercicio                             );
                                    $obTContabilidadeLancamentoReceita->setDado( "cod_entidade", $rsBoletimLiberadoLote->getCampo( "cod_entidade" ) );
                                    $obErro = $obTContabilidadeLancamentoReceita->exclusao( $boTransacao );
                                    if ( !$obErro->ocorreu() ) {
                                        $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->setSequencia( $rsLancamento->getCampo( "sequencia" ) );
                                        $obErro = $obRContabilidadeLancamentoValor->excluir( $boTransacao );
                                    }
                                }
                                $rsLancamento->proximo();

                                if( $obErro->ocorreu() )
                                    break;
                            }
                        }
                    $rsBoletimLiberadoLote->proximo();
                    } //Fim While dos Lotes a serem excluidos.
                }

                if( $obErro->ocorreu() )
                    break;

                $rsBoletimLiberado->proximo();
            }
        }
        $rsBoletimLiberado->setPrimeiroElemento();
        if ( !$obErro->ocorreu() ) {
            $this->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->addUsuarioTerminal();
            $obTTesourariaBoletimLiberadoCancelado->setDado( "cod_boletim"         , $this->inCodBoletim );
            $obTTesourariaBoletimLiberadoCancelado->setDado( "exercicio"           , $this->stExercicio  );
            $obTTesourariaBoletimLiberadoCancelado->setDado( "cod_entidade"        , $this->obROrcamentoEntidade->getCodigoEntidade() );
            $obTTesourariaBoletimLiberadoCancelado->setDado( "timestamp_liberado"  , $rsBoletimLiberado->getCampo("timestamp_liberado"));
            $obTTesourariaBoletimLiberadoCancelado->setDado( "timestamp_fechamento", $rsBoletimLiberado->getCampo("timestamp_fechamento"));
            $obTTesourariaBoletimLiberadoCancelado->setDado( "timestamp_cancelado" , $stTimestampCancelado );
            $obTTesourariaBoletimLiberadoCancelado->setDado( "cod_terminal"        , $this->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->getCodTerminal() );
            $obTTesourariaBoletimLiberadoCancelado->setDado( "timestamp_terminal"  , $this->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->getTimestampTerminal() );
            $obTTesourariaBoletimLiberadoCancelado->setDado( "cgm_usuario"         , $this->obRTesourariaUsuarioTerminal->obRCGM->getNumCGM()   );
            $obTTesourariaBoletimLiberadoCancelado->setDado( "timestamp_usuario"   , $this->obRTesourariaUsuarioTerminal->getTimestampUsuario() );
            $obErro = $obTTesourariaBoletimLiberadoCancelado->inclusao( $boTransacao );
        }
    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro , $obTTesourariaBoletimLiberado );

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
function listar(&$rsRecordSet, $stFiltro = "", $stOrder = "", $boTransacao = "")
{
    include_once ( CAM_GF_TES_MAPEAMENTO ."TTesourariaBoletim.class.php"         );
    $obTTesourariaBoletim = new TTesourariaBoletim();
    if( $this->stExercicio )
        $stFiltro = " TB.exercicio = '".$this->stExercicio."'::varchar AND ";
    if( $this->stDataBoletim )
        $stFiltro .= " TB.dt_boletim = TO_DATE( '".$this->stDataBoletim."', 'dd/mm/yyyy' ) AND ";
    if( $this->getCodBoletim() )
        $stFiltro .= " TB.cod_boletim = ".$this->getCodBoletim()." AND ";
    if( $this->obROrcamentoEntidade->getCodigoEntidade() )
        $stFiltro .= " TB.cod_entidade IN ( ".$this->obROrcamentoEntidade->getCodigoEntidade()." ) AND ";

    $stFiltro = ( $stFiltro ) ? " AND ".substr( $stFiltro, 0, strlen( $stFiltro )-4 ) : '';
    $stOrder  = ( $stOrder  ) ? " ORDER BY ".$stOrder : '';
    $obErro = $obTTesourariaBoletim->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Executa um recuperaTodos na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @return Object Objeto Erro
*/
function listarBoletimAberto(&$rsRecordSet, $stOrder = "",  $boTransacao = "")
{
    include_once ( CAM_GF_TES_MAPEAMENTO ."TTesourariaBoletim.class.php" );
    $obTTesourariaBoletim = new TTesourariaBoletim();
    $stFiltro = "";
    if( $this->getCodBoletim() )
        $stFiltro .= " TB.cod_boletim = ".$this->getCodBoletim()." AND ";
    if( $this->stExercicio )
        $stFiltro .= " TB.exercicio = '".$this->stExercicio."'::varchar AND ";
    if( $this->stDataBoletim )
        $stFiltro .= " TB.dt_boletim = TO_DATE( '".$this->stDataBoletim."', 'dd/mm/yyyy' ) AND ";
    if( $this->obROrcamentoEntidade->getCodigoEntidade() )
        $stFiltro .= " TB.cod_entidade IN ( ".$this->obROrcamentoEntidade->getCodigoEntidade()." ) AND ";

    $stFiltro .= " TBL.timestamp_liberado IS NULL AND
                    CASE WHEN tbf.timestamp_fechamento IS NULL
                     THEN
                         TRUE
                     ELSE
                        CASE WHEN TBR.timestamp_reabertura IS NOT NULL
                            THEN
                                TBF.timestamp_fechamento < TBR.timestamp_reabertura
                            ELSE
                                FALSE
                        END
                     END AND ";

    $stFiltro = ( $stFiltro ) ? " AND ".substr( $stFiltro, 0, strlen( $stFiltro )-4 ) : '';
    $obErro = $obTTesourariaBoletim->recuperaBoletimAberto( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Executa um recuperaTodos na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @return Object Objeto Erro
*/
function listarBoletimFechado(&$rsRecordSet, $stOrder = "",  $boTransacao = "")
{
    $stFiltro="";
    include_once ( CAM_GF_TES_MAPEAMENTO ."TTesourariaBoletim.class.php" );
    $obTTesourariaBoletim = new TTesourariaBoletim();

    if ( $this->getCodBoletimInicial() && $this->getCodBoletimFinal() ) {
        if ($this->getCodBoletimInicial() == $this->getCodBoletimFinal()) {
            $stFiltro .= " TB.cod_boletim = ".$this->getCodBoletimInicial()." AND ";
        } else {
            $stFiltro .= " TB.cod_boletim BETWEEN ".$this->getCodBoletimInicial()." AND ".$this->getCodBoletimFinal()." AND ";
        }
    }
    if ( $this->getCodBoletimInicial() && !$this->getCodBoletimFinal() ) {
        $stFiltro .= " TB.cod_boletim >= ".$this->getCodBoletimInicial()." AND ";
    }
    if ( $this->getCodBoletimFinal() && !$this->getCodBoletimInicial() ) {
        $stFiltro .= " TB.cod_boletim <= ".$this->getCodBoletimFinal()." AND ";
    }
    if( $this->getCodBoletim() )
        $stFiltro .= " TB.cod_boletim = ".$this->getCodBoletim()." AND ";
    if( $this->getExercicio() )
        $stFiltro .= " TB.exercicio = '".$this->getExercicio()."'::varchar AND ";
    if( $this->getDataBoletim() )
        $stFiltro .= " TB.dt_boletim = TO_DATE( '".$this->getDataBoletim()."', 'dd/mm/yyyy' ) AND ";

    if ( $this->getDataBoletimInicial() && $this->getDataBoletimFinal() ) {
        if ($this->getDataBoletimInicial() == $this->getDataBoletimFinal()) {
            $stFiltro .= " TB.dt_boletim = TO_DATE( '".$this->getDataBoletimInicial()."', 'dd/mm/yyyy' ) AND ";
        } else {
            $stFiltro .= " TB.dt_boletim BETWEEN TO_DATE('".$this->getDataBoletimInicial()."','dd/mm/yyyy') AND TO_DATE('".$this->getDataBoletimFinal()."','dd/mm/yyyy') AND ";
        }
    }
    if ( $this->getDataBoletimInicial() && !$this->getDataBoletimFinal() ) {
        $stFiltro .= " TB.dt_boletim >= TO_DATE('".$this->getDataBoletimInicial()."','dd/mm/yyyy') AND ";
    }
    if ( $this->getDataBoletimFinal() && !$this->getDataBoletimInicial() ) {
        $stFiltro .= " TB.dt_boletim <= TO_DATE('".$this->getDataBoletimFinal()."','dd/mm/yyyy') AND ";
    }

    if( $this->obROrcamentoEntidade->getCodigoEntidade() )
        $stFiltro .= " TB.cod_entidade IN( ".$this->obROrcamentoEntidade->getCodigoEntidade()." ) AND ";

    $stFiltro .= " TBL.timestamp_liberado IS NULL AND TBF.timestamp_fechamento IS NOT NULL AND
                    CASE WHEN TBR.timestamp_reabertura IS NOT NULL
                        THEN
                            TBF.timestamp_fechamento > TBR.timestamp_reabertura
                        ELSE
                            TRUE
                    END AND ";

    $stFiltro = ( $stFiltro ) ? " AND ".substr( $stFiltro, 0, strlen( $stFiltro )-4 ) : '';
    $obErro = $obTTesourariaBoletim->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Executa um recuperaTodos na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @return Object Objeto Erro
*/
function listarBoletimLiberado(&$rsRecordSet, $stOrder = "",  $boTransacao = "")
{
    $stFiltro="";
    include_once ( CAM_GF_TES_MAPEAMENTO ."TTesourariaBoletim.class.php" );
    $obTTesourariaBoletim = new TTesourariaBoletim();

    if ( $this->getCodBoletimInicial() && $this->getCodBoletimFinal() ) {
        if ($this->getCodBoletimInicial() == $this->getCodBoletimFinal()) {
            $stFiltro .= " TB.cod_boletim = ".$this->getCodBoletimInicial()." AND ";
        } else {
            $stFiltro .= " TB.cod_boletim BETWEEN ".$this->getCodBoletimInicial()." AND ".$this->getCodBoletimFinal()." AND ";
        }
    }
    if ( $this->getCodBoletimInicial() && !$this->getCodBoletimFinal() ) {
        $stFiltro .= " TB.cod_boletim >= ".$this->getCodBoletimInicial()." AND ";
    }
    if ( $this->getCodBoletimFinal() && !$this->getCodBoletimInicial() ) {
        $stFiltro .= " TB.cod_boletim <= ".$this->getCodBoletimFinal()." AND ";
    }

    if( $this->getCodBoletim() )
        $stFiltro .= " TB.cod_boletim in (".$this->getCodBoletim().") AND ";
    if( $this->getExercicio() )
        $stFiltro .= " TB.exercicio = '".$this->getExercicio()."'::varchar AND ";
    if( $this->getDataBoletim() )
        $stFiltro .= " TB.dt_boletim = TO_DATE( '".$this->getDataBoletim()."', 'dd/mm/yyyy' ) AND ";

    if ( $this->getDataBoletimInicial() && $this->getDataBoletimFinal() ) {
        if ($this->getDataBoletimInicial() == $this->getDataBoletimFinal()) {
            $stFiltro .= " TB.dt_boletim = TO_DATE( '".$this->getDataBoletimInicial()."', 'dd/mm/yyyy' ) AND ";
        } else {
            $stFiltro .= " TB.dt_boletim BETWEEN TO_DATE('".$this->getDataBoletimInicial()."','dd/mm/yyyy') AND TO_DATE('".$this->getDataBoletimFinal()."','dd/mm/yyyy') AND ";
        }
    }
    if ( $this->getDataBoletimInicial() && !$this->getDataBoletimFinal() ) {
        $stFiltro .= " TB.dt_boletim >= TO_DATE('".$this->getDataBoletimInicial()."','dd/mm/yyyy') AND ";
    }
    if ( $this->getDataBoletimFinal() && !$this->getDataBoletimInicial() ) {
        $stFiltro .= " TB.dt_boletim <= TO_DATE('".$this->getDataBoletimFinal()."','dd/mm/yyyy') AND ";
    }

    if( $this->obROrcamentoEntidade->getCodigoEntidade() )
        $stFiltro .= " TB.cod_entidade IN( ".$this->obROrcamentoEntidade->getCodigoEntidade()." ) AND ";

    $stFiltro .= " TBL.timestamp_liberado IS NOT NULL AND ";
    $stFiltro = ( $stFiltro ) ? " AND ".substr( $stFiltro, 0, strlen( $stFiltro )-4 ) : '';
    $obErro = $obTTesourariaBoletim->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Método para retornar o código do boletim aberto do dia atual
    * @access Public
    * @param integer $inCodBoletim
    * @param Object  $obTransacao
    * @return Object $obErro
*/
function buscarCodigoBoletim(&$inCodBoletim, &$stDtBoletim,  $boTransacao = "")
{
    include_once ( CAM_GF_TES_MAPEAMENTO ."TTesourariaBoletim.class.php" );
    $obTTesourariaBoletim = new TTesourariaBoletim();
    $stDataBoletim = $this->stDataBoletim;
    $this->stDataBoletim = null;
    $obErro = $this->listarBoletimAberto( $rsBoletim, '', $boTransacao );
    $this->stDataBoletim = $stDataBoletim;
    if ( !$obErro->ocorreu() ) {
        if ( !$rsBoletim->eof() ) {
            $inCodBoletim = $rsBoletim->getCampo( 'cod_boletim' );
            $stDtBoletim  = $rsBoletim->getCampo( 'dt_boletim'  );
        } else {
            $obErro = $this->listar( $rsRecordSet, $stFiltro, '', $boTransacao );
            if( !$obErro->ocorreu() and !$rsRecordSet->eof() )
                $obErro->setDescricao( 'O boletim para a data atual já está fechado!' );
        }

        if ( !$obErro->ocorreu() and !$inCodBoletim ) {
            $obErro->setDescricao( 'Não existe boletim para esta entidade! Você deverá abrir ou reabrir um boletim!' );
        }
    }

    return $obErro;
}

/**
    * Salva os dados no banco de dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function fecharBoletim($boTransacao = "")
{
    include_once ( CAM_FW_BANCO_DADOS    ."Transacao.class.php"                  );
    include_once ( CAM_GF_TES_MAPEAMENTO ."TTesourariaBoletimFechado.class.php"  );
    $obTransacao = new Transacao();
    $obTTesourariaBoletimFechado         = new TTesourariaBoletimFechado();
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

        if ( !$obErro->ocorreu() ) {

            $this->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->addUsuarioTerminal();
            $obTTesourariaBoletimFechado->setDado( "cod_boletim"         , $this->getCodBoletim());
            $obTTesourariaBoletimFechado->setDado( "exercicio"           , $this->getExercicio());
            $obTTesourariaBoletimFechado->setDado( "cod_entidade"        , $this->obROrcamentoEntidade->getCodigoEntidade() );
            $obTTesourariaBoletimFechado->setDado( "cod_terminal"         , $this->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->getCodTerminal());
            $obTTesourariaBoletimFechado->setDado( "timestamp_terminal"   , $this->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->getTimestampTerminal());
            $obTTesourariaBoletimFechado->setDado( "cgm_usuario"          , $this->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->roUltimoUsuario->obRCGM->getNumCGM());
            $obTTesourariaBoletimFechado->setDado( "timestamp_usuario"    , $this->obRTesourariaUsuarioTerminal->getTimestampUsuario());
            $obErro = $obTTesourariaBoletimFechado->inclusao( $boTransacao );
        }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTTesourariaBoletimFechado );

    return $obErro;
}

/**
    * Salva os dados no banco de dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function reabrirBoletim($boTransacao = "")
{
    include_once ( CAM_FW_BANCO_DADOS    ."Transacao.class.php"                  );
    include_once ( CAM_GF_TES_MAPEAMENTO ."TTesourariaBoletimReaberto.class.php" );
    $obTransacao = new Transacao();
    $obTTesourariaBoletimReaberto         = new TTesourariaBoletimReaberto();
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

        if ( !$obErro->ocorreu() ) {
            $boMultiploBoletim = $this->multiploBoletim();

            $this->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->addUsuarioTerminal();
            $obTTesourariaBoletimReaberto->setDado( "cod_boletim"          , $this->getCodBoletim());
            $obTTesourariaBoletimReaberto->setDado( "exercicio"            , $this->getExercicio());
            $obTTesourariaBoletimReaberto->setDado( "cod_entidade"         , $this->obROrcamentoEntidade->getCodigoEntidade() );
            $obTTesourariaBoletimReaberto->setDado( "timestamp_fechamento" , $this->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->getTimestampFechamento());
            $obTTesourariaBoletimReaberto->setDado( "cod_terminal"         , $this->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->getCodTerminal());
            $obTTesourariaBoletimReaberto->setDado( "timestamp_terminal"   , $this->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->getTimestampTerminal());
            $obTTesourariaBoletimReaberto->setDado( "cgm_usuario"          , $this->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->roUltimoUsuario->obRCGM->getNumCGM());
            $obTTesourariaBoletimReaberto->setDado( "timestamp_usuario"    , $this->obRTesourariaUsuarioTerminal->getTimestampUsuario());

            $obErro = $obTTesourariaBoletimReaberto->inclusao( $boTransacao );
            // Reabre terminal
            if ( !$obErro->ocorreu() ) {
                if ($boMultiploBoletim) {
                    $obErro = $this->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->listarSituacaoPorBoletim( $rsTerminais , $this, '', '', $boTransacao );
                    if ( !$obErro->ocorreu() ) {
                        if ( !$rsTerminais->eof() ) {
                            if( $rsTerminais->getCampo( "situacao" ) == 'fechado' )
                                $obErro = $this->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->abrirTerminal( $this, $boTransacao);
                        } else {
                            $obErro = $this->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->abrirTerminal( $this, $boTransacao);
                        }
                    }
                } else {
                    $obErro = $this->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->listarSituacaoPorBoletim( $rsTerminais , $this, '', '', $boTransacao );
                    if ( !$obErro->ocorreu() ) {
                        if ( !$rsTerminais->eof() ) {
                            if( $rsTerminais->getCampo( "situacao" ) == 'fechado' )
                                $obErro = $this->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->abrirTerminal( $this, $boTransacao);
                            else
                                $obErro->setDescricao( "Este terminal está aberto!" );
                        } else {
                            $obErro = $this->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->abrirTerminal( $this, $boTransacao);
                        }
                    }
                }

            }
        }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTTesourariaBoletimReaberto );

    return $obErro;
}

/**
    * Método para incluir arrecadações no banco de dados
    * @access Public
    * @param Object $boTransacao Parâmetro Transação
    * @return Object Objeto de Erro
*/
function arrecadar($boTransacao = "")
{
    include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php" );
    $obTransacao = new Transacao();
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if ( count( $this->arArrecadacao ) > 0 ) {
            foreach ($this->arArrecadacao as $obRTesourariaArrecadacao) {
                $obErro = $obRTesourariaArrecadacao->incluir( $boTransacao );
                if ($obRTesourariaArrecadacao->obRTesourariaAutenticacao->getDescricao()) {
                    $this->roUltimaArrecadacao->obRTesourariaAutenticacao->setDescricao($obRTesourariaArrecadacao->obRTesourariaAutenticacao->getDescricao());
                }
                $this->roUltimaArrecadacao->setCodArrecadacao( $obRTesourariaArrecadacao->getCodArrecadacao() );

                if( $obErro->ocorreu() )
                    break;
            }
        } else {
            $obErro->setDescricao( "É necessário a inclusão de pelo menos um carnê/receita!" );
        }
    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );

    return $obErro;
}

/**
    * Método para incluir arrecadações no banco de dados
    * @access Public
    * @param Object $boTransacao Parâmetro Transação
    * @return Object Objeto de Erro
*/
function arrecadarViaBanco($boTransacao = "")
{
    include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php" );
    $obTransacao = new Transacao();
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if ( count( $this->arArrecadacao ) > 0 ) {
            foreach ($this->arArrecadacao as $obRTesourariaArrecadacao) {
                $obErro = $obRTesourariaArrecadacao->incluir( $boTransacao );

                // vincular boletim com lote
                if ( !$obErro->ocorreu() ) {
                    include_once ( CAM_GF_TES_MAPEAMENTO."TTesourariaBoletimLote.class.php"     );
                    $obTTesourariaBoletimLote = new TTesourariaBoletimLote();
                    $obTTesourariaBoletimLote->setDado ( 'exercicio' , $this->getExercicio() );
                    $obTTesourariaBoletimLote->setDado ( 'cod_entidade' , $this->obROrcamentoEntidade->getCodigoEntidade());
                    $obTTesourariaBoletimLote->setDado ( 'cod_boletim' , $this->getCodBoletim() );
                    $obTTesourariaBoletimLote->setDado ( 'cod_lote' , $this->inLoteTributario );
                    $obTTesourariaBoletimLote->setDado ( 'timestamp_arrecadacao' , $obRTesourariaArrecadacao->stTimestampArrecadacao );
                    $obTTesourariaBoletimLote->setDado ( 'cod_arrecadacao' , $obRTesourariaArrecadacao->inCodArrecadacao );
                    $obErro = $obTTesourariaBoletimLote->inclusao( $boTransacao );
                }

                if($obRTesourariaArrecadacao->obRTesourariaAutenticacao->getDescricao())
                    $this->roUltimaArrecadacao->obRTesourariaAutenticacao->setDescricao($obRTesourariaArrecadacao->obRTesourariaAutenticacao->getDescricao());

                if( $obErro->ocorreu() )
                    break;
            }
        } else {
            $obErro->setDescricao( "É necessário a inclusão de pelo menos um carnê/receita!" );
        }
    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );

    return $obErro;
}

/* verifica configuração sobre multiplo boletim, permitido ou não */
function multiploBoletim()
{
    $this->obRTesourariaConfiguracao->consultarTesouraria();
    $boMultiplosBoletins = (boolean) $this->obRTesourariaConfiguracao->getMultiplosBoletins();

    return $boMultiplosBoletins;
}

}
