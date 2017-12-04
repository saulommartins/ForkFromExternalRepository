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
    * Classe de Regra de Negócio Configuração do Orçamento
    * Data de Criação: 13/07/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Roberto Pawelski Rodrigues

    $Id: ROrcamentoConfiguracao.class.php 64548 2016-03-11 18:28:10Z evandro $

    * Casos de uso: uc-02.01.01
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
    * Classe de Regra de Negócio Configuração do Orçamento
    * Data de Criação: 13/07/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Roberto Pawelski Rodrigues

    * @package URBEM
    * @subpackage Regra
*/
class ROrcamentoConfiguracao
{
/**
    * @var Integer
    * @access Private
*/
var $inExercicio;
/**
    * @var Integer
    * @access Private
*/
var $inCodEntidadePrefeitura;
/**
    * @var Integer
    * @access Private
*/
var $inCodEntidadeCamara;
/**
    * @var Integer
    * @access Private
*/
var $inCodEntidadeRPPS;
/**
    * @var Integer
    * @access Private
*/
var $inCodEntidadeConsorcio;
/**
    * @var Integer
    * @access Private
*/
var $inNumPAOPosicaoDigitoID;
/**
    * @var Integer
    * @access Private
*/
var $inNumPAODigitosIDProjeto;
/**
    * @var Integer
    * @access Private
*/
var $inNumPAODigitosIDAtividade;
/**
    * @var Integer
    * @access Private
*/
var $inNumPAODigitosIDOperEspeciais;
/**
    * @var Integer
    * @access Private
*/
var $inNumPAODigitosIDNaoOrcamentarios;
/**
    * @var Integer
    * @access Private
*/
var $inMascPosicaoReceita;
/**
    * @var Integer
    * @access Private
*/
var $inMascDespesa;
/**
    * @var Integer
    * @access Private
*/
var $inMascReceitaDedutora;
/**
    * @var String
    * @access Private
*/
var $stMascRecurso;
/**
    * @var String
    * @access Private
*/
var $stMascDestinacaoRecurso;
/**
    * @var Boolean
    * @access Private
*/
var $boDestinacaoRecurso;
/**
    * @var Integer
    * @access Private
*/
var $inMascClassificacaoReceita;
/**
    * @var Integer
    * @access Private
*/
var $inUnidadeMedidaMetas;
/**
    * @var Integer
    * @access Private
*/
var $inUnidadeMedidaMetasReceita;
/**
    * @var Integer
    * @access Private
*/
var $inUnidadeMedidaMetasDespesa;
/**
    * @var Integer
    * @access Private
*/
var $inPosicao;
/**
    * @var Integer
    * @access Private
*/
var $inPosicaoRubrica;
/**
    * @var Integer
    * @access Private
*/
var $inFormaExecucaoOrcamento;
/**
    * @var Integer
    * @access Private
*/
var $inPeriodoApuracaoMetas;
/**
    * @var Integer
    * @access Private
*/
var $inCodContador;
/**
* @var Integer
* @access Private
*/
var $inCodTecContabil;
/**
    * @var String
    * @access Private
*/
var $stMascClassDespesa;
/**
    * @var String
    * @access Private
*/
var $stMascClassReceita;
/**
    * @var String
    * @access Private
*/
var $stMascClassReceitaDedutora;
/**
    * @var Object
    * @access Private
*/
var $obTAcao;
/**
    * @var Boolean
    * @access Private
*/
var $boDedutora;
/**
    * @var Integer
    * @access Private
*/
var $inCodTipoReceita;
/**
    * @var Integer
    * @access Private
*/
var $inCodOrganograma;
/**
    * @var Integer
    * @access Private
*/
var $inCodNivel;
/**
    * @var String
    * @access Private
*/
var $stTipoNivel;
/**
    * @access Public
    * @param Object $valor
*/
var $inPorcentagemLimiteSuplementacaoDecreto;

var $stSuplementacaoRigidaRecurso;

function setLimiteSuplementacaoDecreto($valor) { $this->inPorcentagemLimiteSuplementacaoDecreto = $valor; }

function setSuplementacaoRigidaRecurso($valor) { $this->stSuplementacaoRigidaRecurso = $valor; }

function setCodModulo($valor) { $this->inCodModulo                       = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setExercicio($valor) { $this->inExercicio                       = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setCodEntidadePrefeitura($valor) { $this->inCodEntidadePrefeitura           = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setCodEntidadeCamara($valor) { $this->inCodEntidadeCamara               = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setCodEntidadeRPPS($valor) { $this->inCodEntidadeRPPS               = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setCodEntidadeConsorcio($valor) { $this->inCodEntidadeConsorcio               = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setPosicao($valor) { $this->inPosicao                         = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setPosicaoRubrica($valor) { $this->inPosicaoRubrica                  = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setFormaExecucaoOrcamento($valor) { $this->inFormaExecucaoOrcamento          = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setPeriodoApuracaoMetas($valor) { $this->inPeriodoApuracaoMetas            = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setNumPAOPosicaoDigitoID($valor) { $this->inNumPAOPosicaoDigitoID           = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setNumPAODigitosIDProjeto($valor) { $this->inNumPAODigitosIDProjeto          = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setNumPAODigitosIDAtividade($valor) { $this->inNumPAODigitosIDAtividade        = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setNumPAODigitosIDOperEspeciais($valor) { $this->inNumPAODigitosIDOperEspeciais    = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setNumPAODigitosIDNaoOrcamentarios($valor) { $this->inNumPAODigitosIDNaoOrcamentarios = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setMascPosicaoReceita($valor) { $this->inMascPosicaoReceita              = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setMascClassificacaoReceita($valor) { $this->inMascDespesa                     = $valor; }
function setMascClassificacaoReceitaDedutora($valor) { $this->inMascReceitaDedutora         = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setMascDespesa($valor) { $this->stMascDespesa                     = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setMascRecurso($valor) { $this->stMascRecurso                     = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setMascDestinacaoRecurso($valor) { $this->stMascDestinacaoRecurso           = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setDestinacaoRecurso($valor) { $this->boDestinacaoRecurso               = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setUnidadeMedidaMetas($valor) { $this->inUnidadeMedidaMetas              = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setUnidadeMedidaMetasReceita($valor) { $this->inUnidadeMedidaMetasReceita       = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setUnidadeMedidaMetasDespesa($valor) { $this->inUnidadeMedidaMetasDespesa       = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setCodContador($valor) { $this->inCodContador                     = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setCodTecContabil($valor) { $this->inCodTecContabil                  = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setMascClassDespesa($valor) { $this->stMascClassDespesa               = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setMascClassReceita($valor) { $this->stMascClassReceita                = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setMascClassReceitaDedutora($valor) { $this->stMascClassReceitaDedutora        = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setDedutora($valor) { $this->boDedutora                        = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setTipoReceita($valor) { $this->inCodTipoReceita                  = $valor; }
/**
    * @access Public
    * @return Integer
*/
function getCodModulo() { return $this->inCodModulo;                     }
/**
    * @access Public
    * @return Integer
*/
function getExercicio() { return $this->inExercicio;                     }
/**
    * @access Public
    * @return Integer
*/
function getCodEntidadePrefeitura() { return $this->inCodEntidadePrefeitura;         }
/**
    * @access Public
    * @return Integer
*/
function getCodEntidadeCamara() { return $this->inCodEntidadeCamara;                 }
/**
    * @access Public
    * @return Integer
*/
function getCodEntidadeRPPS() { return $this->inCodEntidadeRPPS;                 }
/**
    * @access Public
    * @return Integer
*/
function getCodEntidadeConsorcio() { return $this->inCodEntidadeConsorcio;         }
/**
    * @access Public
    * @return Integer
*/
function getPosicao() { return $this->inPosicao;                       }
/**
    * @access Public
    * @return Integer
*/
function getPosicaoRubrica() { return $this->inPosicaoRubrica;                }
/**
    * @access Public
    * @return Integer
*/
function getFormaExecucaoOrcamento() { return $this->inFormaExecucaoOrcamento;        }
/**
    * @access Public
    * @return Integer
*/
function getPeriodoApuracaoMetas() { return $this->inPeriodoApuracaoMetas;          }
/**
    * @access Public
    * @return Integer
*/
function getNumPAOPosicaoDigitoID() { return $this->inNumPAOPosicaoDigitoID;         }
/**
    * @access Public
    * @return Integer
*/
function getNumPAODigitosIDProjeto() { return $this->inNumPAODigitosIDProjeto;        }
/**
    * @access Public
    * @return Integer
*/
function getNumPAODigitosIDAtividade() { return $this->inNumPAODigitosIDAtividade;      }
/**
    * @access Public
    * @return Integer
*/
function getNumPAODigitosIDOperEspeciais() { return $this->inNumPAODigitosIDOperEspeciais;  }
/**
    * @access Public
    * @return Integer
*/
function getNumPAODigitosIDNaoOrcamentarios() { return $this->inNumPAODigitosIDNaoOrcamentarios;  }
/**
    * @access Public
    * @return Integer
*/
function getMascPosicaoReceita() { return $this->inMascPosicaoReceita;            }
/**
    * @access Public
    * @return Integer
*/
function getMascClassificacaoReceita() { return $this->inMascDespesa;                   }
/**
    * @access Public
    * @return Integer
*/
function getMascClassificacaoReceitaDedutora() { return $this->inMascReceitaDedutora;        }
/**
    * @access Public
    * @return String
*/
function getMascDespesa() { return $this->stMascDespesa;                   }
/**
    * @access Public
    * @return String
*/
function getMascRecurso() { return $this->stMascRecurso;                   }
/**
    * @access Public
    * @return String
*/
function getMascDestinacaoRecurso() { return $this->stMascDestinacaoRecurso;         }
/**
    * @access Public
    * @return Booleano
*/
function getDestinacaoRecurso() { return $this->boDestinacaoRecurso;             }
/**
    * @access Public
    * @return Integer
*/
function getUnidadeMedidaMetas() { return $this->inUnidadeMedidaMetas;            }
/**
    * @access Public
    * @return Integer
*/
function getUnidadeMedidaMetasReceita() { return $this->inUnidadeMedidaMetasReceita;            }
/**
    * @access Public
    * @return Integer
*/
function getUnidadeMedidaMetasDespesa() { return $this->inUnidadeMedidaMetasDespesa;            }
/**
    * @access Public
    * @return Integer
*/
function getCodContador() { return $this->inCodContador;                   }
/**
    * @access Public
    * @return Integer
*/
function getCodTecContabil() { return $this->inCodTecContabil;                }
/**
    * @access Public
    * @return Integer
*/
function getMascClassDespesa() { return $this->stMascClassDespesa;              }
/**
    * @access Public
    * @return Integer
*/
function getMascClassReceita() { return $this->stMascClassReceita;              }
/**
    * @access Public
    * @return String
*/
function getMascClassReceitaDedutora() { return $this->stMascClassReceitaDedutora;      }
/**
    * @access Public
    * @return Boolean
*/
function getDedutora() { return $this->boDedutora;                      }
/**
    * @access Public
    * @return Integer
*/
function getTipoReceita() { return $this->inCodTipoReceita;                }
/**
    * Método Construtor
    * @access Private
*/
function getLimiteSuplementacaoDecreto() { return $this->inPorcentagemLimiteSuplementacaoDecreto; }

function getSuplementacaoRigidaRecurso() { return $this->stSuplementacaoRigidaRecurso; }

function ROrcamentoConfiguracao()
{
    $this->setExercicio   ( Sessao::getExercicio() );
    $this->setCodModulo   ( 8 );
    $this->setTipoReceita ( 0 );
}
/**
    * Executa Inclusão/Alteração das configurações do orçamento
    * @access Public
    * @param  Object $boTransacao
    * @return Object Objeto Erro
*/
function salvaConfiguracao($boTransacao = "")
{
    include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php"              );
    include CAM_GF_ORC_MAPEAMENTO . 'TOrcamentoOrganogramaNivel.class.php';
    $obTAdministracaoConfiguracao = new TAdministracaoConfiguracao();
    $obTOrcamentoOrganogramaNivel = new TOrcamentoOrganogramaNivel();

    $obTAdministracaoConfiguracao->setDado( "cod_modulo" , $this->getCodModulo() );
    $obTAdministracaoConfiguracao->setDado( "exercicio"  , $this->getExercicio() );
    $boFlagTransacao = false;
    $obErro    = new Erro;
    $recordSet = new RecordSet;

    if ( empty( $boTransacao ) ) {
        $obTransacao = new Transacao;
        $obTransacao->begin();
        $boTransacao = $obTransacao->getTransacao();
        $boFlagTransacao = true;
    }

    //entidade prefeitura
    $obTAdministracaoConfiguracao->setDado("parametro", "cod_entidade_prefeitura" );
    $obTAdministracaoConfiguracao->recuperaPorChave($recordSet);
    $obTAdministracaoConfiguracao->setDado( "valor", (!$this->getCodEntidadePrefeitura()?'0':$this->getCodEntidadePrefeitura() ));
    if ($recordSet->getNumLinhas() > 0) {
        $obErro = $obTAdministracaoConfiguracao->alteracao( $boTransacao );
    } else {
        $obErro = $obTAdministracaoConfiguracao->inclusao( $boTransacao );
    }

    //entidade camara
    $obTAdministracaoConfiguracao->setDado("parametro", "cod_entidade_camara" );
    $obTAdministracaoConfiguracao->recuperaPorChave($recordSet);
    $obTAdministracaoConfiguracao->setDado( "valor", (!$this->getCodEntidadeCamara()?'0':$this->getCodEntidadeCamara() ) );
    if ($recordSet->getNumLinhas() > 0) {
        $obErro = $obTAdministracaoConfiguracao->alteracao( $boTransacao );
    } else {
        $obErro = $obTAdministracaoConfiguracao->inclusao( $boTransacao );
    }

    //entidade rpps
    $obTAdministracaoConfiguracao->setDado("parametro", "cod_entidade_rpps" );
    $obTAdministracaoConfiguracao->recuperaPorChave($recordSet);
    $obTAdministracaoConfiguracao->setDado( "valor", (!$this->getCodEntidadeRPPS()?'0':$this->getCodEntidadeRPPS() ) );
    if ($recordSet->getNumLinhas() > 0) {
        $obErro = $obTAdministracaoConfiguracao->alteracao( $boTransacao );
    } else {
        $obErro = $obTAdministracaoConfiguracao->inclusao( $boTransacao );
    }

    //entidade consorcio
    $obTAdministracaoConfiguracao->setDado("parametro", "cod_entidade_consorcio" );
    $obTAdministracaoConfiguracao->recuperaPorChave($recordSet);
    $obTAdministracaoConfiguracao->setDado( "valor", (!$this->getCodEntidadeConsorcio()?'0':$this->getCodEntidadeConsorcio() ) );

    if ($recordSet->getNumLinhas() > 0) {
        $obErro = $obTAdministracaoConfiguracao->alteracao( $boTransacao );
    } else {
        $obErro = $obTAdministracaoConfiguracao->inclusao( $boTransacao );
    }

    $obTAdministracaoConfiguracao->setDado("parametro", "forma_execucao_orcamento" );
    $obTAdministracaoConfiguracao->recuperaPorChave($recordSet);
    $obTAdministracaoConfiguracao->setDado( "valor", $this->getFormaExecucaoOrcamento() );
    if ($recordSet->getNumLinhas() > 0) {
        $obErro = $obTAdministracaoConfiguracao->alteracao( $boTransacao );
    } else {
        $obErro = $obTAdministracaoConfiguracao->inclusao( $boTransacao );
    }

    if ( $this->getNumPAOPosicaoDigitoID() ) {
        $obTAdministracaoConfiguracao->setDado("parametro", "pao_posicao_digito_id" );
        $obTAdministracaoConfiguracao->recuperaPorChave($recordSet);
        $obTAdministracaoConfiguracao->setDado( "valor", $this->getNumPAOPosicaoDigitoID() );
        if ($recordSet->getNumLinhas() > 0) {
            $obErro = $obTAdministracaoConfiguracao->alteracao( $boTransacao );
        } else {
            $obErro = $obTAdministracaoConfiguracao->inclusao( $boTransacao );
        }
    }

    if ( $this->getNumPAODigitosIDAtividade() and !$obErro->ocorreu() ) {
        $obTAdministracaoConfiguracao->setDado("parametro", "pao_digitos_id_atividade" );
        $obTAdministracaoConfiguracao->recuperaPorChave($recordSet);
        $obTAdministracaoConfiguracao->setDado( "valor", $this->getNumPAODigitosIDAtividade() );
        if ($recordSet->getNumLinhas() > 0) {
            $obErro = $obTAdministracaoConfiguracao->alteracao( $boTransacao );
        } else {
            $obErro = $obTAdministracaoConfiguracao->inclusao( $boTransacao );
        }
    }

    $obTAdministracaoConfiguracao->setDado("parametro", "pao_digitos_id_oper_especiais" );
    $obTAdministracaoConfiguracao->recuperaPorChave($recordSet);
    $obTAdministracaoConfiguracao->setDado( "valor", $this->getNumPAODigitosIDOperEspeciais() );
    if ($recordSet->getNumLinhas() > 0) {
        $obErro = $obTAdministracaoConfiguracao->alteracao( $boTransacao );
    } else {
        $obErro = $obTAdministracaoConfiguracao->inclusao( $boTransacao );
    }

    if ( $this->getNumPAODigitosIDNaoOrcamentarios() and !$obErro->ocorreu() ) {
        $obTAdministracaoConfiguracao->setDado("parametro", "pao_digitos_id_nao_orcamentarios" );
        $obTAdministracaoConfiguracao->recuperaPorChave($recordSet);
        $obTAdministracaoConfiguracao->setDado( "valor", $this->getNumPAODigitosIDNaoOrcamentarios() );
        if ($recordSet->getNumLinhas() > 0) {
            $obErro = $obTAdministracaoConfiguracao->alteracao( $boTransacao );
        } else {
            $obErro = $obTAdministracaoConfiguracao->inclusao( $boTransacao );
        }
    }

    if ( $this->getMascPosicaoReceita() and !$obErro->ocorreu() ) {
        $obTAdministracaoConfiguracao->setDado("parametro", "masc_rubrica_despesa" );
        $obTAdministracaoConfiguracao->recuperaPorChave($recordSet);
        $obTAdministracaoConfiguracao->setDado( "valor", $this->getMascPosicaoReceita() );
        if ($recordSet->getNumLinhas() > 0) {
            $obErro = $obTAdministracaoConfiguracao->alteracao( $boTransacao );
        } else {
            $obErro = $obTAdministracaoConfiguracao->inclusao( $boTransacao );
        }
    }

    if ( $this->getMascDespesa() and !$obErro->ocorreu() ) {
        $obTAdministracaoConfiguracao->setDado("parametro", "masc_despesa" );
        $obTAdministracaoConfiguracao->recuperaPorChave($recordSet);
        $obTAdministracaoConfiguracao->setDado( "valor", $this->getMascDespesa() );
        if ($recordSet->getNumLinhas() > 0) {
            $obErro = $obTAdministracaoConfiguracao->alteracao( $boTransacao );
        } else {
            $obErro = $obTAdministracaoConfiguracao->inclusao( $boTransacao );
        }
    }

    if ( $this->getUnidadeMedidaMetas() and !$obErro->ocorreu() ) {
        $obTAdministracaoConfiguracao->setDado("parametro", "unidade_medida_metas" );
        $obTAdministracaoConfiguracao->recuperaPorChave($recordSet);
        $obTAdministracaoConfiguracao->setDado( "valor", $this->getUnidadeMedidaMetas() );
        if ($recordSet->getNumLinhas() > 0) {
            $obErro = $obTAdministracaoConfiguracao->alteracao( $boTransacao );
        } else {
            $obErro = $obTAdministracaoConfiguracao->inclusao( $boTransacao );
        }
    }

    if ( $this->getUnidadeMedidaMetasReceita() and !$obErro->ocorreu() ) {
        $obTAdministracaoConfiguracao->setDado("parametro", "unidade_medida_metas_receita" );
        $obTAdministracaoConfiguracao->recuperaPorChave($recordSet);
        $obTAdministracaoConfiguracao->setDado( "valor", $this->getUnidadeMedidaMetasReceita() );
        if ($recordSet->getNumLinhas() > 0) {
            $obErro = $obTAdministracaoConfiguracao->alteracao( $boTransacao );
        } else {
            $obErro = $obTAdministracaoConfiguracao->inclusao( $boTransacao );
        }
    }

    if ( $this->getUnidadeMedidaMetasDespesa() and !$obErro->ocorreu() ) {
        $obTAdministracaoConfiguracao->setDado("parametro", "unidade_medida_metas_despesa" );
        $obTAdministracaoConfiguracao->recuperaPorChave($recordSet);
        $obTAdministracaoConfiguracao->setDado( "valor", $this->getUnidadeMedidaMetasDespesa() );
        if ($recordSet->getNumLinhas() > 0) {
            $obErro = $obTAdministracaoConfiguracao->alteracao( $boTransacao );
        } else {
            $obErro = $obTAdministracaoConfiguracao->inclusao( $boTransacao );
        }
    }

    if ( $this->getNumPAODigitosIDProjeto() and !$obErro->ocorreu() ) {
        $obTAdministracaoConfiguracao->setDado("parametro", "pao_digitos_id_projeto" );
        $obTAdministracaoConfiguracao->recuperaPorChave($recordSet);
        $obTAdministracaoConfiguracao->setDado( "valor", $this->getNumPAODigitosIDProjeto() );
        if ($recordSet->getNumLinhas() > 0) {
            $obErro = $obTAdministracaoConfiguracao->alteracao( $boTransacao );
        } else {
            $obErro = $obTAdministracaoConfiguracao->inclusao( $boTransacao );
        }
    }

    if ( $this->getMascRecurso() and !$obErro->ocorreu() ) {
        $obTAdministracaoConfiguracao->setDado("parametro", "masc_recurso" );
        $obTAdministracaoConfiguracao->recuperaPorChave($recordSet);
        $obTAdministracaoConfiguracao->setDado( "valor", $this->getMascRecurso() );
        if ($recordSet->getNumLinhas() > 0) {
            $obErro = $obTAdministracaoConfiguracao->alteracao( $boTransacao );
        } else {
            $obErro = $obTAdministracaoConfiguracao->inclusao( $boTransacao );
        }
    }

    if ( $this->getMascDestinacaoRecurso() and !$obErro->ocorreu() ) {
        $obTAdministracaoConfiguracao->setDado("parametro", "masc_destinacao_recurso" );
        $obTAdministracaoConfiguracao->recuperaPorChave($recordSet);
        $obTAdministracaoConfiguracao->setDado( "valor", $this->getMascDestinacaoRecurso() );
        if ($recordSet->getNumLinhas() > 0) {
            $obErro = $obTAdministracaoConfiguracao->alteracao( $boTransacao );
        } else {
            $obErro = $obTAdministracaoConfiguracao->inclusao( $boTransacao );
        }
    }

    if ( $this->getDestinacaoRecurso() and !$obErro->ocorreu() ) {
        $obTAdministracaoConfiguracao->setDado("parametro", "recurso_destinacao" );
        $obTAdministracaoConfiguracao->recuperaPorChave($recordSet);
        $obTAdministracaoConfiguracao->setDado( "valor", ($this->getDestinacaoRecurso() == 'S' ? 'true' : 'false') );
        if ($recordSet->getNumLinhas() > 0) {
            $obErro = $obTAdministracaoConfiguracao->alteracao( $boTransacao );
        } else {
            $obErro = $obTAdministracaoConfiguracao->inclusao( $boTransacao );
        }
    }

    if ( $this->getCodContador() and !$obErro->ocorreu() ) {
        $obTAdministracaoConfiguracao->setDado("parametro", "cod_contador" );
        $obTAdministracaoConfiguracao->recuperaPorChave($recordSet);
        $obTAdministracaoConfiguracao->setDado( "valor", $this->getCodContador() );
        if ($recordSet->getNumLinhas() > 0) {
            $obErro = $obTAdministracaoConfiguracao->alteracao( $boTransacao );
        } else {
            $obErro = $obTAdministracaoConfiguracao->inclusao( $boTransacao );
        }
    }

    if ( $this->getCodTecContabil() and !$obErro->ocorreu() ) {
        $obTAdministracaoConfiguracao->setDado("parametro", "cod_tec_contabil" );
        $obTAdministracaoConfiguracao->recuperaPorChave($recordSet);
        $obTAdministracaoConfiguracao->setDado( "valor", $this->getCodTecContabil() );
        if ($recordSet->getNumLinhas() > 0) {
            $obErro = $obTAdministracaoConfiguracao->alteracao( $boTransacao );
        } else {
            $obErro = $obTAdministracaoConfiguracao->inclusao( $boTransacao );
        }
    }

    if ( $this->getMascClassDespesa() and !$obErro->ocorreu() ) {
        $obTAdministracaoConfiguracao->setDado("parametro", "masc_class_despesa" );
        $obTAdministracaoConfiguracao->recuperaPorChave($recordSet);
        $obTAdministracaoConfiguracao->setDado( "valor", $this->getMascClassDespesa() );
        if ($recordSet->getNumLinhas() > 0) {
            $obErro = $obTAdministracaoConfiguracao->alteracao( $boTransacao );
        } else {
            $obErro = $obTAdministracaoConfiguracao->inclusao( $boTransacao );
        }
    }

    if ( $this->getMascClassReceita() and !$obErro->ocorreu() ) {
        $obTAdministracaoConfiguracao->setDado("parametro", "masc_class_receita" );
        $obTAdministracaoConfiguracao->recuperaPorChave($recordSet);
        $obTAdministracaoConfiguracao->setDado( "valor", $this->getMascClassReceita() );
        if ($recordSet->getNumLinhas() > 0) {
            $obErro = $obTAdministracaoConfiguracao->alteracao( $boTransacao );
        } else {
            $obErro = $obTAdministracaoConfiguracao->inclusao( $boTransacao );
        }
    }

    if ( $this->getMascClassReceitaDedutora() and !$obErro->ocorreu() ) {
        $obTAdministracaoConfiguracao->setDado("parametro", "masc_class_receita_dedutora" );
        $obTAdministracaoConfiguracao->recuperaPorChave($recordSet);
        $obTAdministracaoConfiguracao->setDado( "valor", $this->getMascClassReceitaDedutora() );
        if ($recordSet->getNumLinhas() > 0) {
            $obErro = $obTAdministracaoConfiguracao->alteracao( $boTransacao );
        } else {
            $obErro = $obTAdministracaoConfiguracao->inclusao( $boTransacao );
        }
    }

    if ( $this->getLimiteSuplementacaoDecreto() and !$obErro->ocorreu() ) {
        $obTAdministracaoConfiguracao->setDado("parametro", "limite_suplementacao_decreto" );
        $obTAdministracaoConfiguracao->recuperaPorChave($recordSet);
        $obTAdministracaoConfiguracao->setDado( "valor", $this->getLimiteSuplementacaoDecreto() );
        if ($recordSet->getNumLinhas() > 0) {
            $obErro = $obTAdministracaoConfiguracao->alteracao( $boTransacao );
        } else {
            $obErro = $obTAdministracaoConfiguracao->inclusao( $boTransacao );
        }
    }

    if ( Sessao::getExercicio() > 2015 && SistemaLegado::isTCEMG($boTransacao) ) {
        if ( $this->getLimiteSuplementacaoDecreto() and !$obErro->ocorreu() ) {
            $obTAdministracaoConfiguracao->setDado("parametro", "suplementacao_rigida_recurso" );
            $obTAdministracaoConfiguracao->recuperaPorChave($recordSet);
            $obTAdministracaoConfiguracao->setDado( "valor", $this->getSuplementacaoRigidaRecurso() );
            if ($recordSet->getNumLinhas() > 0) {
                $obErro = $obTAdministracaoConfiguracao->alteracao( $boTransacao );
            } else {
                $obErro = $obTAdministracaoConfiguracao->inclusao( $boTransacao );
            }
        }
    }

    if ($boFlagTransacao) {
        if ( !$obErro->ocorreu() ) {
            $obErro = $obTransacao->commitAndClose();
        } else {
            $obTransacao->rollbackAndClose();
        }
    }

    return $obErro;
}
/**
    * Efetua consulta nas configurações.
    * @access Public
    * @param  Object $boTransacao
    * @return Object Objeto erro
*/
function consultarConfiguracao($boTransacao = "")
{
    include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php"              );
    $obTAdministracaoConfiguracao = new TAdministracaoConfiguracao;

    $obErro = new Erro;
    if (!$this->inCodModulo) {
        $obErro = $this->buscaModulo( $boTransacao );
    }

    if(!$this->getExercicio())
        $this->setExercicio(Sessao::getExercicio());

    if ( !$obErro->ocorreu() ) {
        if (Sessao::getExercicio() < '2014') {
            $arParametro = array( "cod_entidade_consorcio"
                                , "cod_entidade_prefeitura"
                                , "cod_entidade_camara"
                                , "cod_entidade_rpps"
                                , "forma_execucao_orcamento"
                                , "pao_posicao_digito_id"
                                , "pao_digitos_id_atividade"
                                , "pao_digitos_id_oper_especiais"
                                , "masc_rubrica_despesa"
                                , "masc_despesa"
                                , "masc_classificacao_receita"
                                , "unidade_medida_metas"
                                , "pao_digitos_id_projeto"
                                , "pao_digitos_id_nao_orcamentarios"
                                , "masc_recurso"
                                , "masc_recurso_destinacao"
                                , "recurso_destinacao"
                                , "cod_contador"
                                , "cod_tec_contabil"
                                , "masc_class_despesa" );
        } else {
            $arParametro = array( "cod_entidade_consorcio"
                                , "cod_entidade_prefeitura"
                                , "cod_entidade_camara"
                                , "cod_entidade_rpps"
                                , "forma_execucao_orcamento"
                                , "pao_posicao_digito_id"
                                , "pao_digitos_id_atividade"
                                , "pao_digitos_id_oper_especiais"
                                , "masc_rubrica_despesa"
                                , "masc_despesa"
                                , "masc_classificacao_receita"
                                , "unidade_medida_metas"
                                , "unidade_medida_metas_receita"
                                , "unidade_medida_metas_despesa"
                                , "pao_digitos_id_projeto"
                                , "pao_digitos_id_nao_orcamentarios"
                                , "masc_recurso"
                                , "masc_recurso_destinacao"
                                , "recurso_destinacao"
                                , "cod_contador"
                                , "cod_tec_contabil"
                                , "masc_class_despesa"
                                , 'limite_suplementacao_decreto'
                                , 'suplementacao_rigida_recurso' );
        }
        foreach ($arParametro as $stParametro) {
            $stFiltro = " WHERE COD_MODULO = ".$this->getCodModulo()." AND parametro = '".$stParametro."' AND exercicio = '".$this->getExercicio()."'";
            $stOrder = " ORDER BY parametro ";
            $obErro = $obTAdministracaoConfiguracao->recuperaTodos( $rsConfiguracao, $stFiltro, $stOrder, $boTransacao );
            if ( $obErro->ocorreu() ) {
                break;
            }
            $arParametroConfiguracao[$stParametro] = $rsConfiguracao->getCampo("valor");
        }
    }
    if ( !$obErro->ocorreu() ) {

        if($arParametroConfiguracao["cod_entidade_prefeitura"]=="0") $arParametroConfiguracao["cod_entidade_prefeitura"] = '';
        if($arParametroConfiguracao["cod_entidade_camara"]=="0") $arParametroConfiguracao["cod_entidade_camara"] = '';
        if($arParametroConfiguracao["cod_entidade_rpps"]=="0") $arParametroConfiguracao["cod_entidade_rpps"] = '';
        if($arParametroConfiguracao["cod_entidade_consorcio"]=="0") $arParametroConfiguracao["cod_entidade_consorcio"] = '';

        // Recupera mascaras
        $arParametroConfiguracao["masc_classificacao_receita"] = $this->recuperaMascaraReceita( $boTransacao );
        $this->setDedutora( true );
        $arParametroConfiguracao["masc_classificacao_receita_dedutora"] = $this->recuperaMascaraReceita( $boTransacao );
        $this->setDedutora( false );
        $arParametroConfiguracao["masc_rubrica_despesa"]       = $this->recuperaMascaraDespesa( $boTransacao );
        $this->setCodEntidadePrefeitura             ( $arParametroConfiguracao["cod_entidade_prefeitura"]       );
        $this->setCodEntidadeCamara                 ( $arParametroConfiguracao["cod_entidade_camara"]           );
        $this->setCodEntidadeRPPS                   ( $arParametroConfiguracao["cod_entidade_rpps"]             );
        $this->setCodEntidadeConsorcio              ( $arParametroConfiguracao["cod_entidade_consorcio"]        );
        $this->setFormaExecucaoOrcamento            ( $arParametroConfiguracao["forma_execucao_orcamento"]      );
        $this->setNumPAOPosicaoDigitoID             ( $arParametroConfiguracao["pao_posicao_digito_id"]         );
        $this->setNumPAODigitosIDAtividade          ( $arParametroConfiguracao["pao_digitos_id_atividade"]      );
        $this->setNumPAODigitosIDOperEspeciais      ( $arParametroConfiguracao["pao_digitos_id_oper_especiais"] );
        $this->setNumPAODigitosIDNaoOrcamentarios   ( $arParametroConfiguracao["pao_digitos_id_nao_orcamentarios"] );
        $this->setMascPosicaoReceita                ( $arParametroConfiguracao["masc_rubrica_despesa"]          );
        $this->setMascDespesa                       ( $arParametroConfiguracao["masc_despesa"]                  );
        $this->setMascClassDespesa                  ( $arParametroConfiguracao["masc_class_despesa"]            );
        $this->setMascClassificacaoReceita          ( $arParametroConfiguracao["masc_classificacao_receita"]    );
        $this->setMascClassificacaoReceitaDedutora  ( $arParametroConfiguracao["masc_classificacao_receita_dedutora"]    );
        if (Sessao::getExercicio() < '2014') {
            $this->setUnidadeMedidaMetas                ( $arParametroConfiguracao["unidade_medida_metas"]          );
        } else {
            $this->setUnidadeMedidaMetasReceita         ( $arParametroConfiguracao["unidade_medida_metas_receita"]  );
            $this->setUnidadeMedidaMetasDespesa         ( $arParametroConfiguracao["unidade_medida_metas_despesa"]  );
        }
        $this->setNumPAODigitosIDProjeto            ( $arParametroConfiguracao["pao_digitos_id_projeto"]        );
        $this->setMascRecurso                       ( $arParametroConfiguracao["masc_recurso"]                  );
        $this->setMascDestinacaoRecurso             ( $arParametroConfiguracao["masc_recurso_destinacao"]       );
        $this->setDestinacaoRecurso                 ( $arParametroConfiguracao["recurso_destinacao"]            );
        $this->setCodContador                       ( $arParametroConfiguracao["cod_contador"]                  );
        $this->setCodTecContabil                    ( $arParametroConfiguracao["cod_tec_contabil"]              );
        $this->setLimiteSuplementacaoDecreto        ( $arParametroConfiguracao["limite_suplementacao_decreto"]  );
        $this->setSuplementacaoRigidaRecurso        ( $arParametroConfiguracao["suplementacao_rigida_recurso"]  );
        
    }

    return $obErro;
}
/**
    * Efetua consulta em uma configuração.
    * @access Public
    * @param  Object $boTransacao
    * @return Object Objeto erro
*/
function consultarConfiguracaoEspecifica($stParametro, $boTransacao = "")
{
    include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php"              );
    $obTAdministracaoConfiguracao = new TAdministracaoConfiguracao;

    $obErro = new Erro;
    if (!$this->inCodModulo) {
        $obErro = $this->buscaModulo( $boTransacao );
    }

    if(!$this->getExercicio())
        $this->setExercicio(Sessao::getExercicio());

    if ( !$obErro->ocorreu() ) {
        $stFiltro = " WHERE cod_modulo = ".$this->getCodModulo()." AND parametro = '".$stParametro."' AND exercicio = '".$this->getExercicio()."'";
        $stOrder = " ORDER BY parametro ";
        $obErro = $obTAdministracaoConfiguracao->recuperaTodos( $rsConfiguracao, $stFiltro, $stOrder, $boTransacao );
        if ( $obErro->ocorreu() ) {
            break;
        }

        return $rsConfiguracao->getCampo("valor");
    }

    return $obErro;
}
/**
    * Efetua consulta para recuperar código módulo.
    * @access Public
    * @param  Object $boTransacao
    * @return Object Objeto erro
*/
function buscaModulo($boTransacao = "")
{
    include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoAcao.class.php"                      );
    $obTAcao                      = new TAdministracaoAcao;

    ;
    $stFiltro  = " AND A.cod_acao = ".Sessao::read('acao')." ";
    $obErro = $obTAcao->recuperaRelacionamento( $rsRelacionamento, $stFiltro, "", $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->setCodModulo( $rsRelacionamento->getCampo("cod_modulo") );
    }

    return $obErro;
}
/**
    * Efetua inclusão dados na Máscara de Classificação de Receita.
    * @access Public
    * @param  Object $boTransacao
    * @return Object Objeto erro
*/
function salvarReceita($boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoPosicaoReceita.class.php"   );
    $obTOrcamentoPosicaoReceita   = new TOrcamentoPosicaoReceita;

    $obTOrcamentoPosicaoReceita->setDado( "exercicio"   , $this->getExercicio()                );
    $obTOrcamentoPosicaoReceita->setDado( "cod_posicao" , $this->getPosicao()                  );
    $obTOrcamentoPosicaoReceita->setDado( "mascara"     , $this->getMascPosicaoReceita()       );
    $obTOrcamentoPosicaoReceita->setDado( "cod_tipo"     ,$this->getTipoReceita()              );
    $obErro = $obTOrcamentoPosicaoReceita->inclusao( $boTransacao );

    return $obErro;
}
/**
    * Efetua inclusão na Máscara de Classificação de Receita.
    * @access Public
    * @param  Object $boTransacao
    * @return Object Objeto erro
*/
function alterarReceita($boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoPosicaoReceita.class.php"   );
    $obTOrcamentoPosicaoReceita   = new TOrcamentoPosicaoReceita;

    $obTOrcamentoPosicaoReceita->setDado( "exercicio"   , $this->getExercicio()                );
    $obTOrcamentoPosicaoReceita->setDado( "cod_posicao" , $this->getPosicao()                  );
    $obTOrcamentoPosicaoReceita->setDado( "mascara"     , $this->getMascPosicaoReceita()       );
    $obTOrcamentoPosicaoReceita->setDado( "cod_tipo"     ,$this->getTipoReceita()              );
    $obErro = $obTOrcamentoPosicaoReceita->alteracao( $boTransacao );

    return $obErro;
}
/**
    * Efetua alterações na Máscara de Rubrica de Despesa.
    * @access Public
    * @param  Object $boTransacao
    * @return Object Objeto erro
*/
function salvarRubrica($boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoPosicaoDespesa.class.php"   );
    $obTOrcamentoPosicaoDespesa   = new TOrcamentoPosicaoDespesa;

    $obTOrcamentoPosicaoDespesa->setDado( "exercicio"   , $this->getExercicio()               );
    $obTOrcamentoPosicaoDespesa->setDado( "cod_posicao" , $this->getPosicaoRubrica()          );
    $obTOrcamentoPosicaoDespesa->setDado( "mascara"     , $this->getMascClassificacaoReceita());
    $obErro = $obTOrcamentoPosicaoDespesa->inclusao( $boTransacao );

    return $obErro;
}
/**
    * Efetua Alterações na Máscara de Rubrica de Despesa.
    * @access Public
    * @param  Object $boTransacao
    * @return Object Objeto erro
*/
function alterarRubrica($boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoPosicaoDespesa.class.php"   );
    $obTOrcamentoPosicaoDespesa   = new TOrcamentoPosicaoDespesa;

    $obTOrcamentoPosicaoDespesa->setDado( "exercicio"   , $this->getExercicio()               );
    $obTOrcamentoPosicaoDespesa->setDado( "cod_posicao" , $this->getPosicaoRubrica()          );
    $obTOrcamentoPosicaoDespesa->setDado( "mascara"     , $this->getMascClassificacaoReceita());
    $obErro = $obTOrcamentoPosicaoDespesa->alteracao( $boTransacao );

    return $obErro;
}
/**
    * Recupera Máscara de Classificação de Receita.
    * @access Public
    * @param  Object $boTransacao
    * @return Object Objeto erro
*/
function recuperaMascaraReceita($boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoPosicaoReceita.class.php"   );
    $obTOrcamentoPosicaoReceita   = new TOrcamentoPosicaoReceita;
    if ($this->getDedutora()) {
           $obTOrcamentoPosicaoReceita->setDado('dedutora', true );
    }

    $obErro = $obTOrcamentoPosicaoReceita->recuperaMascara( $this->getExercicio(), $boTransacao );

    return $obErro;
}
/**
    * Recupera Máscara de Rubrica de Despesa.
    * @access Public
    * @param  Object $boTransacao
    * @return Object Objeto erro
*/
function recuperaMascaraDespesa($boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoPosicaoDespesa.class.php"   );
    $obTOrcamentoPosicaoDespesa   = new TOrcamentoPosicaoDespesa;

    $obErro = $obTOrcamentoPosicaoDespesa->recuperaMascara( $this->getExercicio(), $boTransacao );

    return $obErro;
}
/**
    * Efetua exclusão da Máscara de Receita.
    * @access Public
    * @param  Object $boTransacao
    * @return Object Objeto erro
*/
function deletarMascaraReceita($boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoPosicaoReceita.class.php"   );
    $obTOrcamentoPosicaoReceita   = new TOrcamentoPosicaoReceita;
    $obTOrcamentoPosicaoReceita->setDado( "exercicio" , $this->getExercicio() );
    $obTOrcamentoPosicaoReceita->setDado( "cod_posicao" , $this->getPosicao() );
    if ($this->getDedutora()) {
           $obTOrcamentoPosicaoReceita->setDado( "dedutora" , true );
           $obTOrcamentoPosicaoReceita->setDado( "cod_tipo" , 1 );
    } else $obTOrcamentoPosicaoReceita->setDado( "cod_tipo" ,'0' );

    $obErro = $obTOrcamentoPosicaoReceita->recuperaDeletaPosicao( $rsDeleta,'','', $boTransacao  );

    return $obErro;
}

/**
    * Efetua exclusão da Máscara de Despesa.
    * @access Public
    * @param  Object $boTransacao
    * @return Object Objeto erro
*/
function deletarMascaraDespesa($boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoPosicaoDespesa.class.php"   );
    $obTOrcamentoPosicaoDespesa   = new TOrcamentoPosicaoDespesa;

    $obTOrcamentoPosicaoDespesa->setDado( "exercicio" , $this->getExercicio() );
    $obTOrcamentoPosicaoDespesa->setDado( "cod_posicao" , $this->getPosicao() );
    $obErro = $obTOrcamentoPosicaoDespesa->recuperaDeletaPosicao( $rsDeleta );

    return $obErro;
}
/**
    * Efetua exclusão da Máscara de Receita.
    * @access Public
    * @param  Object $rsMaxPosicaoReceita
    * @param  Object $boTransacao
    * @return Object Objeto erro
*/
function recuperaMaxPosicaoReceita(&$rsMaxPosicaoReceita, $boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoPosicaoReceita.class.php"   );
    $obTOrcamentoPosicaoReceita   = new TOrcamentoPosicaoReceita;
    if($this->getDedutora())
        $obTOrcamentoPosicaoReceita->setDado('dedutora', true );
    $obTOrcamentoPosicaoReceita->setDado('exercicio', $this->getExercicio() );

    $obErro = $obTOrcamentoPosicaoReceita->recuperaMaxPosicao( $rsMaxPosicaoReceita, '', '', $boTransacao );

    return $obErro;
}

/**
    * Efetua exclusão da Máscara de Despesa.
    * @access Public
    * @param  Object $rsMaxPosicaoDespesa
    * @param  Object $boTransacao
    * @return Object Objeto erro
*/
function recuperaMaxPosicaoDespesa(&$rsMaxPosicaoDespesa, $boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoPosicaoDespesa.class.php"   );
    $obTOrcamentoPosicaoDespesa   = new TOrcamentoPosicaoDespesa;

    $obErro = $obTOrcamentoPosicaoDespesa->recuperaMaxPosicao( $rsMaxPosicaoDespesa );

    return $obErro;
}

/**
    * Recupera o nivel
    * @access Public
    * @param  Object $rsOrgao
    * @param  Object $boTransacao
    * @return Object Objeto erro
*/
function recuperaMaxNivel(&$rsOrgao, $boTransacao = "")
{
    include_once CAM_GF_ORC_MAPEAMENTO . 'TOrcamentoOrganogramaNivel.class.php';

    $obTOrcamentoOrganogramaNivel = new TOrcamentoOrganogramaNivel();
    $stFiltro = "
        WHERE organograma_nivel.cod_organograma = " . $this->inCodOrganograma . "
          AND organograma_nivel.tipo = '" . $this->stTipoNivel . "'
    ";
    $obErro = $obTOrcamentoOrganogramaNivel->getMaxOrganogramaNivel($rsOrgao, $stFiltro);

    return $obErro;
}
}
