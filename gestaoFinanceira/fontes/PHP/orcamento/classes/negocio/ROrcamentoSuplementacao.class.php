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
    * Classe de Regra de Negócio de Suplementacao
    * Data de Criação: 10/02/2005

    * @author Analista: Dieine
    * @author Desenvolvedor: Marcelo B. Paulino

    * @package URBEM
    * @subpackage Regra

    $Revision: 30668 $
    $Name$
    $Author: cako $
    $Date: 2007-12-05 15:12:56 -0200 (Qua, 05 Dez 2007) $

    * Casos de uso: uc-02.01.24
                    uc-02.01.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GF_ORC_NEGOCIO   ."ROrcamentoDespesa.class.php"                                     );
include_once ( CAM_GA_NORMAS_NEGOCIO."RNorma.class.php"                                                );
include_once ( CAM_GF_CONT_NEGOCIO  ."RContabilidadeTransferenciaDespesa.class.php"                    );
include_once ( CAM_GF_CONT_NEGOCIO  ."RContabilidadeHistoricoPadrao.class.php"                         );

class ROrcamentoSuplementacao
{
/**
    * @var Object
    * @access Private
*/
var $obRNorma;
/**
    * @var Object
    * @access Private
*/
var $obROrcamentoDespesa;
/**
    * @var Object
    * @access Private
*/
var $obRContabilidadeTransferenciaDespesa;
/**
    * @var Object
    * @access Private
*/
var $obRContabilidadeHistoricoPadrao;
/**
    * @var Object
    * @access Private
*/
var $obTransacao;
/**
    * @var String
    * @access Private
*/
var $stExercicio;
/**
    * @var String
    * @access Private
*/
var $stDecreto;
/**
    * @var String
    * @access Private
*/
var $stCredSuplementar;
/**
    * @var String
    * @access Private
*/
var $stMotivo;
/**
    * @var String
    * @access Private
*/
var $stDtLancamento;
/**
    * @var String
    * @access Private
*/
var $stDtAnulacao;
/**
    * @var String
    * @access Private
*/
var $stDtSuplementacaoInicial;
/**
    * @var String
    * @access Private
*/
var $stDtSuplementacaoFinal;
/**
    * @var Numeric
    * @access Private
*/
var $nuVlTotal;
/**
    * @var Numeric
    * @access Private
*/
var $nuVlSuplementacao;
/**
    * @var Numeric
    * @access Private
*/
var $nuVlReducao;
/**
    * @var Integer
    * @access Private
*/
var $inCodSuplementacao;
/**
    * @var Integer
    * @access Private
*/
var $inCodSuplementacaoAnulada;
/**
    * @var Integer
    * @access Private
*/
var $inCodTipo;
/**
    * @var String
    * @access Private
*/
var $stNomTipo;
/**
    * @var Integer
    * @access Private
*/
var $inCodTipoAnulacao;
/**
    * @var Array
    * @access Private
*/
var $arDespesaReducao;
/**
    * @var Object
    * @access Private
*/
var $roUltimoDespesaReducao;
/**
    * @var Array
    * @access Private
*/
var $arDespesaSuplementada;
/**
    * @var Object
    * @access Private
*/
var $roUltimoDespesaSuplementada;
/**
    * @var Object
    * @access Private
*/
var $boEstorno;
/**
    * @var Integer
    * @access Private
*/
var $inSituacao;

/**
    * @access Public
    * @param Object $valor
*/
function setRContabilidadeHistoricoPadrao($valor) { $this->obRContabilidadeHistoricoPadrao = $valor;       }
/**
     * @access Public
     * @param String $valor
*/
function setExercicio($valor) { $this->stExercicio  = $valor;                          }
/**
     * @access Public
     * @param String $valor
*/
function setCredSuplementar($valor) { $this->stCredSuplementar = $valor;                     }
/**
     * @access Public
     * @param String $valor
*/
function setMotivo($valor) { $this->stMotivo = $valor;                              }
/**
     * @access Public
     * @param String $valor
*/
function setDtLancamento($valor) { $this->stDtLancamento = $valor;                        }
/**
     * @access Public
     * @param String $valor
*/
function setDtAnulacao($valor) { $this->stDtAnulacao = $valor;                          }
/**
     * @access Public
     * @param String $valor
*/
function setDtSuplementacaoInicial($valor) { $this->stDtSuplementacaoInicial = $valor;              }
/**
     * @access Public
     * @param String $valor
*/
function setDtSuplementacaoFinal($valor) { $this->stDtSuplementacaoFinal = $valor;                }
/**
     * @access Public
     * @param integer $valor
*/
function setCodSuplementacao($valor) { $this->inCodSuplementacao = $valor;                    }
/**
     * @access Public
     * @param integer $valor
*/
function setCodSuplementacaoAnulada($valor) { $this->inCodSuplementacaoAnulada = $valor;             }
/**
     * @access Public
     * @param Integer $valor
*/
function setVlTotal($valor) { $this->nuVlTotal = $valor;                             }
/**
     * @access Public
     * @param Integer $valor
*/
function setVlSuplementacao($valor) { $this->nuVlSuplementacao = $valor;                     }
/**
     * @access Public
     * @param Integer $valor
*/
function setVlReducao($valor) { $this->nuVlReducao = $valor;                           }
/**
     * @access Public
     * @param Integer $valor
*/
function setCodTipo($valor) { $this->inCodTipo = $valor;                             }
/**
     * @access Public
     * @param String $valor
*/
function setNomTipo($valor) { $this->stNomTipo = $valor;                             }
/**
     * @access Public
     * @param Integer $valor
*/
function setCodTipoAnulacao($valor) { $this->inCodTipoAnulacao = $valor;                     }
/**
     * @access Public
     * @param String $valor
*/
function setDecreto($valor) { $this->stDecreto = $valor;                             }
/**
    * @access Public
    * @param Object $Valor
*/
function setUltimoDespesaReducao($valor) { $this->roUltimoDespesaReducao = $valor;                }
/**
    * @access Public
    * @param Object $Valor
*/
function setDespesaReducao($valor) { $this->arDespesaReducao = $valor;                      }
/**
    * @access Public
    * @param Object $Valor
*/
function setUltimoDespesaSuplementada($valor) { $this->roUltimoDespesaSuplementada = $valor;           }
/**
    * @access Public
    * @param Object $Valor
*/
function setDespesaSuplementada($valor) { $this->arDespesaSuplementada = $valor;                 }
/**
    * @access Public
    * @param Integer $Valor
*/
function setSituacao($valor) { $this->inSituacao = $valor;                            }

/**
    * @access Public
    * @return Object $valor
*/
function getRContabilidadeHistoricoPadrao() { return $this->obRContabilidadeHistoricoPadrao;       }
/**
     * @access Public
     * @param String $valor
*/
function getExercicio() { return $this->stExercicio;                           }
/**
     * @access Public
     * @param String $valor
*/
function getCredSuplementar() { return $this->stCredSuplementar;                     }
/**
     * @access Public
     * @param String $valor
*/
function getMotivo() { return $this->stMotivo;                              }
/**
     * @access Public
     * @param String $valor
*/
function getDtLancamento() { return $this->stDtLancamento;                        }
/**
     * @access Public
     * @param String $valor
*/
function getDtAnulacao() { return $this->stDtAnulacao;                          }
/**
     * @access Public
     * @param String $valor
*/
function getDtSuplementacaoInicial() { return $this->stDtSuplementacaoInicial;              }
/**
     * @access Public
     * @param String $valor
*/
function getDtSuplementacaoFinal() { return $this->stDtSuplementacaoFinal;                }
/**
     * @access Public
     * @param Integer $valor
*/
function getCodSuplementacao() { return $this->inCodSuplementacao;                    }
/**
     * @access Public
     * @param Integer $valor
*/
function getCodSuplementacaoAnulada() { return $this->inCodSuplementacaoAnulada;             }
/**
     * @access Public
     * @param Integer $valor
*/
function getVlTotal() { return $this->nuVlTotal;                             }
/**
     * @access Public
     * @param Integer $valor
*/
function getVlSuplementacao() { return $this->nuVlSuplementacao;                     }
/**
     * @access Public
     * @param Integer $valor
*/
function getVlReducao() { return $this->nuVlReducao;                           }
/**
     * @access Public
     * @param Integer $valor
*/
function getCodTipo() { return $this->inCodTipo;                             }
/**
     * @access Public
     * @param String $valor
*/
function getNomTipo() { return $this->stNomTipo;                             }
/**
     * @access Public
     * @param Integer $valor
*/
function getCodTipoAnulacao() { return $this->inCodTipoAnulacao;                     }
/**
     * @access Public
     * @param String $valor
*/
function getDecreto() { return $this->stDecreto;                             }
/**
    * @access Public
    * @param Object $Valor
*/
function getUltimoDespesaReducao() { return $this->roUltimoDespesaReducao;                }
/**
    * @access Public
    * @param Object $Valor
*/
function getDespesaReducao() { return $this->arDespesaReducao;                      }
/**
    * @access Public
    * @param Object $Valor
*/
function getUltimoDespesaSuplementada() { return $this->roUltimoDespesaSuplementada;           }
/**
    * @access Public
    * @param Object $Valor
*/
function getDespesaSuplementada() { return $this->arDespesaSuplementada;                 }
/**
    * @access Public
    * @param Integer $Valor
*/
function getSituacao() { return $this->inSituacao;                            }

/**
    * Instancia um novo objeto do tipo Despesa
    * @access Public
*/
function addDespesaReducao()
{
   $this->arDespesaReducao[] = new ROrcamentoDespesa();
   $this->roUltimoDespesaReducao = &$this->arDespesaReducao[ count( $this->arDespesaReducao ) -1 ];
}

/**
    * Instancia um novo objeto do tipo Despesa
    * @access Public
*/
function addDespesaSuplementada()
{
   $this->arDespesaSuplementada[] = new ROrcamentoDespesa();
   $this->roUltimoDespesaSuplementada = &$this->arDespesaSuplementada[ count( $this->arDespesaSuplementada ) -1 ];
}
/**
    * Método Construtor
    * @access Private
*/
function ROrcamentoSuplementacao()
{
    $this->obRNorma                              = new RNorma;
    $this->obTransacao                           = new Transacao;
    $this->obRContabilidadeTransferenciaDespesa  = new RContabilidadeTransferenciaDespesa( $this );
    $this->obRContabilidadeHistoricoPadrao       = new RContabilidadeHistoricoPadrao;
    $this->obRContabilidadeTransferenciaDespesa->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->obRContabilidadeLote->setTipo('S');
    $this->obROrcamentoDespesa                   = new ROrcamentoDespesa;
}

/**
    * Método para validar se uma despesa não tem saldo e nem suplementacoes
    * @access Private
    * @param Boolean $boVlInicial
    * @param Object $obROrcamentoDespesa
    * @param Object $boTransacao
    * @return Object $obErro
**/
function checarSaldoDotacaoInicial(&$boVlInicial, $obROrcamentoDespesa,  $boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoSuplementacao.class.php"                               );
    $obTOrcamentoSuplementacao             = new TOrcamentoSuplementacao;

    $obErro = new Erro;
    if ( in_array( $this->inCodTipo, array( 6,7,8,9,10 ) ) ) {
        $nuVlInicial = 0;
        $obErro = $obROrcamentoDespesa->consultar( $rsDespesa, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $nuVlInicial = $rsDespesa->getCampo('vl_original');
//            $obTOrcamentoSuplementacao->setDado( "cod_despesa", $obROrcamentoDespesa->getCodDespesa() );
//            if( $obROrcamentoDespesa->getExercicio() )
//                $stFiltro = " S.exercicio = '".$obROrcamentoDespesa->getExercicio()."' AND ";
//
//            $stFiltro = ( $stFiltro ) ? ' WHERE '.substr( $stFiltro, 0, strlen($stFiltro)-4 ) : '';
//            $obErro = $obTOrcamentoSuplementacao->recuperaVlSuplementacao( $rsRecordSet, $stFiltro, $boTransacao );
//            if ( !$obErro->ocorreu() ) {
//                $nuVlInicial += $rsRecordSet->getCampo( 'vl_suplementado' );
//                $nuVlInicial += $rsRecordSet->getCampo( 'vl_reduzido' );
//            }
            $boVlInicial = ( $nuVlInicial > 0 ) ?  true : false;
        }
    }

    return $obErro;
}

/**
    * Método para validar orgão, modalidade e categoria das transferencias por transferencia
    * @access Private
    * @return $obErro
*/
function validarTransferencia()
{
    $obErro = new Erro;
    // Pega apenas os primeiros 4 digitos do cod_estrutural ( categoria, grupo, modalidade )
    foreach ($this->arDespesaSuplementada as $obROrcamentoDespesaSuplementada) {
        $arDotacaoSuplementada[] = ( substr( $obROrcamentoDespesaSuplementada->obROrcamentoClassificacaoDespesa->getMascClassificacao(), 0, 4 ) );
    }
    foreach ($this->arDespesaReducao as $obROrcamentoDespesaReducao) {
        $arDotacaoReducao[] = ( substr( $obROrcamentoDespesaReducao->obROrcamentoClassificacaoDespesa->getMascClassificacao(), 0, 4 ) );
    }
    // verifica se as dotacoes tem correspondentes com a mesma categoria, grupo e modalidade
    $arDiffReducaoSuplementada = array_diff( $arDotacaoReducao, $arDotacaoSuplementada );
    $arDiffSuplementadaReducao = array_diff( $arDotacaoSuplementada, $arDotacaoReducao );
    if ( count( $arDiffReducaoSuplementada ) > 0 ) {
        $obErro->setDescricao( 'Elementos de despesa da redução não encontrados na suplementação' );
    } elseif ( count( $arDiffSuplementadaReducao ) > 0 ) {
        $obErro->setDescricao( 'Elementos de despesa da suplementação não encontrados na redução' );
    }

    return $obErro;
}

/**
    * Método que executa os lancamentos automaticos suplementares
    * @access Private
    * @param Object $boTransacao
    * @return Object $obErro
**/
function fazerLancamentoSuplementar($boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."FOrcamentoSuplementacoesCreditoSuplementar.class.php"            );
    $obFOrcamentoSuplementacoesCreditoSuplementar = new FOrcamentoSuplementacoesCreditoSuplementar;
    $obFOrcamentoSuplementacoesCreditoSuplementar->setDado( 'exercicio'       , $this->stExercicio       );
    $obFOrcamentoSuplementacoesCreditoSuplementar->setDado( 'valor'           , $this->nuVlTotal         );
    $obFOrcamentoSuplementacoesCreditoSuplementar->setDado( 'complemento'     , $this->stDecreto         );
    $obFOrcamentoSuplementacoesCreditoSuplementar->setDado( 'dt_lote'         , $this->stDtLancamento    );
    $obFOrcamentoSuplementacoesCreditoSuplementar->setDado( 'tipo_lote'       , $this->obRContabilidadeTransferenciaDespesa->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->obRContabilidadeLote->getTipo() );
    $obFOrcamentoSuplementacoesCreditoSuplementar->setDado( 'cod_entidade'    , $this->obRContabilidadeTransferenciaDespesa->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() );
    $obFOrcamentoSuplementacoesCreditoSuplementar->setDado( 'cred_suplementar', $this->stCredSuplementar );
    $obErro = $obFOrcamentoSuplementacoesCreditoSuplementar->executaFuncao( $rsRecordSet, $boTransacao   );
    if ( !$obErro->ocorreu() ) {
        $inCodLote   = $obFOrcamentoSuplementacoesCreditoSuplementar->getDado( 'cod_lote' );
        $inSequencia = $rsRecordSet->getCampo( 'sequencia' );
        $this->obRContabilidadeTransferenciaDespesa->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->obRContabilidadeLote->setCodLote( $inCodLote );
        $this->obRContabilidadeTransferenciaDespesa->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->setSequencia( $inSequencia );
    }

    return $obErro;
}

/**
    * Método que executa os lancamentos automaticos especiais
    * @access Private
    * @param Object $boTransacao
    * @return Object $obErro
**/
function fazerLancamentoEspecial($boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."FOrcamentoSuplementacoesCreditoEspecial.class.php"               );
    $obFOrcamentoSuplementacoesCreditoEspecial = new FOrcamentoSuplementacoesCreditoEspecial;
    $obFOrcamentoSuplementacoesCreditoEspecial->setDado( 'exercicio'       , $this->stExercicio       );
    $obFOrcamentoSuplementacoesCreditoEspecial->setDado( 'valor'           , $this->nuVlTotal         );
    $obFOrcamentoSuplementacoesCreditoEspecial->setDado( 'complemento'     , $this->stDecreto         );
    $obFOrcamentoSuplementacoesCreditoEspecial->setDado( 'dt_lote'         , $this->stDtLancamento    );
    $obFOrcamentoSuplementacoesCreditoEspecial->setDado( 'tipo_lote'       , $this->obRContabilidadeTransferenciaDespesa->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->obRContabilidadeLote->getTipo() );
    $obFOrcamentoSuplementacoesCreditoEspecial->setDado( 'cod_entidade'    , $this->obRContabilidadeTransferenciaDespesa->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() );
    $obFOrcamentoSuplementacoesCreditoEspecial->setDado( 'cred_suplementar', $this->stCredSuplementar );
    $obErro = $obFOrcamentoSuplementacoesCreditoEspecial->executaFuncao( $rsRecordSet, $boTransacao   );
    if ( !$obErro->ocorreu() ) {
        $inCodLote   = $obFOrcamentoSuplementacoesCreditoEspecial->getDado( 'cod_lote' );
        $inSequencia = $rsRecordSet->getCampo( 'sequencia' );
        $this->obRContabilidadeTransferenciaDespesa->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->obRContabilidadeLote->setCodLote( $inCodLote );
        $this->obRContabilidadeTransferenciaDespesa->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->setSequencia( $inSequencia );
    }

    return $obErro;
}

/**
    * Método que executa os lancamentos automaticos extraordinarios
    * @access Private
    * @param Object $boTransacao
    * @return Object $obErro
**/
function fazerLancamentoExtraordinario($boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."FOrcamentoSuplementacoesCreditoExtraordinario.class.php"         );
    $obFOrcamentoSuplementacoesCreditoExtraordinario = new FOrcamentoSuplementacoesCreditoExtraordinario;
    $obFOrcamentoSuplementacoesCreditoExtraordinario->setDado( 'exercicio'       , $this->stExercicio       );
    $obFOrcamentoSuplementacoesCreditoExtraordinario->setDado( 'valor'           , $this->nuVlTotal         );
    $obFOrcamentoSuplementacoesCreditoExtraordinario->setDado( 'complemento'     , $this->stDecreto         );
    $obFOrcamentoSuplementacoesCreditoExtraordinario->setDado( 'dt_lote'         , $this->stDtLancamento    );
    $obFOrcamentoSuplementacoesCreditoExtraordinario->setDado( 'tipo_lote'       , $this->obRContabilidadeTransferenciaDespesa->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->obRContabilidadeLote->getTipo() );
    $obFOrcamentoSuplementacoesCreditoExtraordinario->setDado( 'cod_entidade'    , $this->obRContabilidadeTransferenciaDespesa->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() );
    $obFOrcamentoSuplementacoesCreditoExtraordinario->setDado( 'cred_suplementar', $this->stCredSuplementar );
    $obErro = $obFOrcamentoSuplementacoesCreditoExtraordinario->executaFuncao( $rsRecordSet, $boTransacao   );
    if ( !$obErro->ocorreu() ) {
        $inCodLote   = $obFOrcamentoSuplementacoesCreditoExtraordinario->getDado( 'cod_lote' );
        $inSequencia = $rsRecordSet->getCampo( 'sequencia' );
        $this->obRContabilidadeTransferenciaDespesa->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->obRContabilidadeLote->setCodLote( $inCodLote );
        $this->obRContabilidadeTransferenciaDespesa->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->setSequencia( $inSequencia );
    }

    return $obErro;
}

/**
    * Método que executa os lancamentos automaticos de transferencia
    * @access Private
    * @param Object $boTransacao
    * @return Object $obErro
**/
function fazerLancamentoTransferencia($boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."FOrcamentoSuplementacoesTransferencia.class.php"                 );
    $obFOrcamentoSuplementacoesTransferencia = new FOrcamentoSuplementacoesTransferencia;
    $obFOrcamentoSuplementacoesTransferencia->setDado( 'exercicio'       , $this->stExercicio       );
    $obFOrcamentoSuplementacoesTransferencia->setDado( 'valor'           , $this->nuVlTotal         );
    $obFOrcamentoSuplementacoesTransferencia->setDado( 'complemento'     , $this->stDecreto         );
    $obFOrcamentoSuplementacoesTransferencia->setDado( 'dt_lote'         , $this->stDtLancamento    );
    $obFOrcamentoSuplementacoesTransferencia->setDado( 'tipo_lote'       , $this->obRContabilidadeTransferenciaDespesa->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->obRContabilidadeLote->getTipo() );
    $obFOrcamentoSuplementacoesTransferencia->setDado( 'cod_entidade'    , $this->obRContabilidadeTransferenciaDespesa->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() );
    $obFOrcamentoSuplementacoesTransferencia->setDado( 'cod_historico'   , $this->obRContabilidadeHistoricoPadrao->getCodHistorico() );
    $obErro = $obFOrcamentoSuplementacoesTransferencia->executaFuncao( $rsRecordSet, $boTransacao   );
    if ( !$obErro->ocorreu() ) {
        $inCodLote   = $obFOrcamentoSuplementacoesTransferencia->getDado( 'cod_lote' );
        $inSequencia = $rsRecordSet->getCampo( 'sequencia' );
        $this->obRContabilidadeTransferenciaDespesa->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->obRContabilidadeLote->setCodLote( $inCodLote );
        $this->obRContabilidadeTransferenciaDespesa->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->setSequencia( $inSequencia );
    }

    return $obErro;
}

/**
    * Método que executa os lancamentos automaticos suplementares de anulação
    * @access Private
    * @param Object $boTransacao
    * @return Object $obErro
**/
function fazerLancamentoSuplementarAnulacao($boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."FOrcamentoAnulacaoSuplementacoesCreditoSuplementar.class.php"    );
    $obFOrcamentoAnulacaoSuplementacoesCreditoSuplementar = new FOrcamentoAnulacaoSuplementacoesCreditoSuplementar;
    $obFOrcamentoAnulacaoSuplementacoesCreditoSuplementar->setDado( 'exercicio'    , $this->stExercicio       );
    $obFOrcamentoAnulacaoSuplementacoesCreditoSuplementar->setDado( 'valor'        , $this->nuVlTotal         );
    $obFOrcamentoAnulacaoSuplementacoesCreditoSuplementar->setDado( 'complemento'  , $this->stDecreto         );
    $obFOrcamentoAnulacaoSuplementacoesCreditoSuplementar->setDado( 'dt_lote'      , $this->stDtLancamento    );
    $obFOrcamentoAnulacaoSuplementacoesCreditoSuplementar->setDado( 'tipo_lote'    , $this->obRContabilidadeTransferenciaDespesa->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->obRContabilidadeLote->getTipo() );
    $obFOrcamentoAnulacaoSuplementacoesCreditoSuplementar->setDado( 'cod_entidade' , $this->obRContabilidadeTransferenciaDespesa->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() );
    $obFOrcamentoAnulacaoSuplementacoesCreditoSuplementar->setDado( 'tipo_anulacao', $this->stCredSuplementar );
    $obErro = $obFOrcamentoAnulacaoSuplementacoesCreditoSuplementar->executaFuncao( $rsRecordSet, $boTransacao   );
    if ( !$obErro->ocorreu() ) {
        $inCodLote   = $obFOrcamentoAnulacaoSuplementacoesCreditoSuplementar->getDado( 'cod_lote' );
        $inSequencia = $rsRecordSet->getCampo( 'sequencia' );
        $this->obRContabilidadeTransferenciaDespesa->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->obRContabilidadeLote->setCodLote( $inCodLote );
        $this->obRContabilidadeTransferenciaDespesa->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->setSequencia( $inSequencia );
    }

    return $obErro;
}

/**
    * Método que executa os lancamentos automaticos especiais de anulação
    * @access Private
    * @param Object $boTransacao
    * @return Object $obErro
**/
function fazerLancamentoEspecialAnulacao($boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."FOrcamentoAnulacaoSuplementacoesCreditoEspecial.class.php"       );
    $obFOrcamentoAnulacaoSuplementacoesCreditoEspecial = new FOrcamentoAnulacaoSuplementacoesCreditoEspecial;
    $obFOrcamentoAnulacaoSuplementacoesCreditoEspecial->setDado( 'exercicio'    , $this->stExercicio       );
    $obFOrcamentoAnulacaoSuplementacoesCreditoEspecial->setDado( 'valor'        , $this->nuVlTotal         );
    $obFOrcamentoAnulacaoSuplementacoesCreditoEspecial->setDado( 'complemento'  , $this->stDecreto         );
    $obFOrcamentoAnulacaoSuplementacoesCreditoEspecial->setDado( 'dt_lote'      , $this->stDtLancamento    );
    $obFOrcamentoAnulacaoSuplementacoesCreditoEspecial->setDado( 'tipo_lote'    , $this->obRContabilidadeTransferenciaDespesa->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->obRContabilidadeLote->getTipo() );
    $obFOrcamentoAnulacaoSuplementacoesCreditoEspecial->setDado( 'cod_entidade' , $this->obRContabilidadeTransferenciaDespesa->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() );
    $obFOrcamentoAnulacaoSuplementacoesCreditoEspecial->setDado( 'tipo_anulacao', $this->stCredSuplementar );
    $obErro = $obFOrcamentoAnulacaoSuplementacoesCreditoEspecial->executaFuncao( $rsRecordSet, $boTransacao   );
    if ( !$obErro->ocorreu() ) {
        $inCodLote   = $obFOrcamentoAnulacaoSuplementacoesCreditoEspecial->getDado( 'cod_lote' );
        $inSequencia = $rsRecordSet->getCampo( 'sequencia' );
        $this->obRContabilidadeTransferenciaDespesa->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->obRContabilidadeLote->setCodLote( $inCodLote );
        $this->obRContabilidadeTransferenciaDespesa->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->setSequencia( $inSequencia );
    }

    return $obErro;
}

/**
    * Método que executa os lancamentos automaticos extraordinarios de anulação
    * @access Private
    * @param Object $boTransacao
    * @return Object $obErro
**/
function fazerLancamentoExtraordinarioAnulacao($boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."FOrcamentoAnulacaoSuplementacoesCreditoExtraordinario.class.php" );
    $obFOrcamentoAnulacaoSuplementacoesCreditoExtraordinario = new FOrcamentoAnulacaoSuplementacoesCreditoExtraordinario;
    $obFOrcamentoAnulacaoSuplementacoesCreditoExtraordinario->setDado( 'exercicio'    , $this->stExercicio       );
    $obFOrcamentoAnulacaoSuplementacoesCreditoExtraordinario->setDado( 'valor'        , $this->nuVlTotal         );
    $obFOrcamentoAnulacaoSuplementacoesCreditoExtraordinario->setDado( 'complemento'  , $this->stDecreto         );
    $obFOrcamentoAnulacaoSuplementacoesCreditoExtraordinario->setDado( 'dt_lote'      , $this->stDtLancamento    );
    $obFOrcamentoAnulacaoSuplementacoesCreditoExtraordinario->setDado( 'tipo_lote'    , $this->obRContabilidadeTransferenciaDespesa->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->obRContabilidadeLote->getTipo() );
    $obFOrcamentoAnulacaoSuplementacoesCreditoExtraordinario->setDado( 'cod_entidade' , $this->obRContabilidadeTransferenciaDespesa->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() );
    $obFOrcamentoAnulacaoSuplementacoesCreditoExtraordinario->setDado( 'tipo_anulacao', $this->stCredSuplementar );
    $obErro = $obFOrcamentoAnulacaoSuplementacoesCreditoExtraordinario->executaFuncao( $rsRecordSet, $boTransacao   );
    if ( !$obErro->ocorreu() ) {
        $inCodLote   = $obFOrcamentoAnulacaoSuplementacoesCreditoExtraordinario->getDado( 'cod_lote' );
        $inSequencia = $rsRecordSet->getCampo( 'sequencia' );
        $this->obRContabilidadeTransferenciaDespesa->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->obRContabilidadeLote->setCodLote( $inCodLote );
        $this->obRContabilidadeTransferenciaDespesa->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->setSequencia( $inSequencia );
    }

    return $obErro;
}

/**
    * Método que executa os lancamentos automaticos de anulação de transferencia
    * @access Private
    * @param Object $boTransacao
    * @return Object $obErro
**/
function fazerLancamentoTransferenciaAnulacao($boTransacao = "")
{
    $obErro = $this->consultarHistoricoLancamento( $boTransacao );
    if ( !$obErro->ocorreu() ) {
        include_once ( CAM_GF_ORC_MAPEAMENTO."FOrcamentoAnulacaoSuplementacoesTransferencia.class.php"         );
        $obFOrcamentoAnulacaoSuplementacoesTransferencia = new FOrcamentoAnulacaoSuplementacoesTransferencia;
        $obFOrcamentoAnulacaoSuplementacoesTransferencia->setDado( 'exercicio'       , $this->stExercicio       );
        $obFOrcamentoAnulacaoSuplementacoesTransferencia->setDado( 'valor'           , $this->nuVlTotal         );
        $obFOrcamentoAnulacaoSuplementacoesTransferencia->setDado( 'complemento'     , $this->stDecreto         );
        $obFOrcamentoAnulacaoSuplementacoesTransferencia->setDado( 'dt_lote'         , $this->stDtLancamento    );
        $obFOrcamentoAnulacaoSuplementacoesTransferencia->setDado( 'tipo_lote'       , $this->obRContabilidadeTransferenciaDespesa->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->obRContabilidadeLote->getTipo() );
        $obFOrcamentoAnulacaoSuplementacoesTransferencia->setDado( 'cod_entidade'    , $this->obRContabilidadeTransferenciaDespesa->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() );
        $obFOrcamentoAnulacaoSuplementacoesTransferencia->setDado( 'cod_historico'   , $this->obRContabilidadeHistoricoPadrao->getCodHistorico() );
        $obErro = $obFOrcamentoAnulacaoSuplementacoesTransferencia->executaFuncao( $rsRecordSet, $boTransacao   );
        if ( !$obErro->ocorreu() ) {
            $inCodLote   = $obFOrcamentoAnulacaoSuplementacoesTransferencia->getDado( 'cod_lote' );
            $inSequencia = $rsRecordSet->getCampo( 'sequencia' );
            $this->obRContabilidadeTransferenciaDespesa->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->obRContabilidadeLote->setCodLote( $inCodLote );
            $this->obRContabilidadeTransferenciaDespesa->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->setSequencia( $inSequencia );
        }
    }

    return $obErro;
}

/**
    * Método para validar mes/ano do lançamento
    * @access Private
    * @return obErro
*/
function validarDataLancamento()
{
    $obErro = new Erro;
    if ( substr($this->stDtLancamento,6,4) == $this->stExercicio ) {
/**
    * O trecho de codigo abaixo foi comentado para possibilitar ajuster na suplementação por parte dos clientes
    * assim que forem feitos, este trecho abaixo sera descomentado - NAO APAGUE
**/
//        if ( substr($this->stDtLancamento,3,2) == date('m') ) {
//           if ( substr($this->stDtLancamento,0,2) > date('d') ) {
//               $obErro->setDescricao( 'O dia do lançamento não pode ser superior ao atual.' );
//           }
//        } else {
//            $obErro->setDescricao( 'O mês do lançamento não pode ser diferente do atual.' );
//        }
    } else {
        $obErro->setDescricao( 'A data informada deve pertencer ao exercício de trabalho.' );
    }

    return $obErro;
}

/**
    * Método que chama a função de lancamento correta de acordo com o Codigo do Tipo
    * @access Private
    * @param  Object $boTransacao
    * @return Object $obErro
**/
function fazerLancamento($boTransacao = "")
{
    $obErro = new Erro;
    $obErro = $this->validarDataLancamento();
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->obRNorma->consultar( $rsRecordSet, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->stDecreto  = $this->obRNorma->obRTipoNorma->getNomeTipoNorma().' '.$this->obRNorma->getNumNorma().'/'.$this->obRNorma->getExercicio();
            $this->stDecreto .= ' - '.$this->obRNorma->getNomeNorma();
            //Desabilitado, para a funcionalidade Alteração Orçamentária pois ainda não temos os lançamentos oficiais de suplementação para a nova contabilidade. Somente para cliente do tcems
            if (Sessao::getExercicio() > '2012') {
                if ( in_array( $this->inCodTipo, array( 1,2,3,4,5 ) ) ) {
                    $obErro = $this->fazerLancamentoSuplementar( $boTransacao );
                } elseif ( in_array( $this->inCodTipo, array( 6,7,8,9,10 ) ) ) {
                    $obErro = $this->fazerLancamentoEspecial( $boTransacao );
                } elseif ( in_array( $this->inCodTipo, array( 11 ) ) ) {
                    $obErro = $this->fazerLancamentoExtraordinario( $boTransacao );
                } elseif ( in_array( $this->inCodTipo, array( 12,13,14 ) ) ) {
                    $obErro = $this->fazerLancamentoTransferencia( $boTransacao );
                } elseif ( in_array( $this->inCodTipo, array( 15 ) ) ) {
                    $obErro = $this->fazerLancamentoAnulacaoExterna( $boTransacao );
                } elseif ( in_array( $this->inCodTipo, array( 16 ) ) ) {
                    if ( in_array( $this->inCodTipoAnulacao, array( 1,2,3,4,5 ) ) ) {
                        $obErro = $this->fazerLancamentoSuplementarAnulacao( $boTransacao );
                    } elseif ( in_array( $this->inCodTipoAnulacao, array( 6,7,8,9,10 ) ) ) {
                        $obErro = $this->fazerLancamentoEspecialAnulacao( $boTransacao );
                    } elseif ( in_array( $this->inCodTipoAnulacao, array( 11 ) ) ) {
                        $obErro = $this->fazerLancamentoExtraordinarioAnulacao( $boTransacao );
                    } elseif ( in_array( $this->inCodTipoAnulacao, array( 12, 13, 14 ) ) ) {
                        $obErro = $this->fazerLancamentoTransferenciaAnulacao( $boTransacao );
                    } elseif ( in_array( $this->inCodTipoAnulacao, array( 15 ) ) ) {
                        $obErro = $obErro = $this->fazerLancamentoAnulacaoExternaAnulacao( $boTransacao );
                    }
                }
            }
        }
    }

    return $obErro;
}

/**
    * Método para fazer lancamento contabil e inclusao nas tabelas de transferencia para anulaão externa
    * @access Private
    * @param Object $boTransacao
    * @return Objebct $obErro
*/
function fazerLancamentoAnulacaoExterna($boTransacao = "")
{
    $this->obRContabilidadeTransferenciaDespesa->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $this->roUltimoDespesaSuplementada->obROrcamentoEntidade->getCodigoEntidade() );
    $this->stCredSuplementar = 'AnulacaoExternaSuplementada';
    $obErro = $this->fazerLancamentoSuplementar( $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obRContabilidadeTransferenciaDespesa->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->obRContabilidadeLote->setExercicio( $this->stExercicio );
        $this->obRContabilidadeTransferenciaDespesa->obRContabilidadeLancamentoTransferencia->setCodTipo( $this->inCodTipo );
        $this->obRContabilidadeTransferenciaDespesa->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $this->roUltimoDespesaSuplementada->obROrcamentoEntidade->getCodigoEntidade() );
        $obErro = $this->obRContabilidadeTransferenciaDespesa->incluir( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obRContabilidadeTransferenciaDespesa->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $this->roUltimoDespesaReducao->obROrcamentoEntidade->getCodigoEntidade() );
            $this->stCredSuplementar = 'AnulacaoExternaReduzida';
            $obErro = $this->fazerLancamentoSuplementar( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->obRContabilidadeTransferenciaDespesa->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->obRContabilidadeLote->setExercicio( $this->stExercicio );
                $this->obRContabilidadeTransferenciaDespesa->obRContabilidadeLancamentoTransferencia->setCodTipo( $this->inCodTipo );
                $this->obRContabilidadeTransferenciaDespesa->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $this->roUltimoDespesaReducao->obROrcamentoEntidade->getCodigoEntidade() );
                $obErro = $this->obRContabilidadeTransferenciaDespesa->incluir( $boTransacao );
            }
        }
    }

    return $obErro;
}

/**
    * Método para fazer lancamento contabil e inclusao nas tabelas de transferencia para anulaão externa
    * @access Private
    * @param Object $boTransacao
    * @return Objebct $obErro
*/
function fazerLancamentoAnulacaoExternaAnulacao($boTransacao = "")
{
    $this->obRContabilidadeTransferenciaDespesa->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $this->roUltimoDespesaSuplementada->obROrcamentoEntidade->getCodigoEntidade() );
    $this->stCredSuplementar = 'AnulacaoExternaReduzida';
    $obErro = $this->fazerLancamentoSuplementarAnulacao( $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obRContabilidadeTransferenciaDespesa->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->obRContabilidadeLote->setExercicio( $this->stExercicio );
        $this->obRContabilidadeTransferenciaDespesa->obRContabilidadeLancamentoTransferencia->setCodTipo( $this->inCodTipo );
        $this->obRContabilidadeTransferenciaDespesa->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $this->roUltimoDespesaSuplementada->obROrcamentoEntidade->getCodigoEntidade() );
        $obErro = $this->obRContabilidadeTransferenciaDespesa->incluir( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obRContabilidadeTransferenciaDespesa->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $this->roUltimoDespesaReducao->obROrcamentoEntidade->getCodigoEntidade() );
            $this->stCredSuplementar = 'AnulacaoExternaSuplementada';
            $obErro = $this->fazerLancamentoSuplementarAnulacao( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->obRContabilidadeTransferenciaDespesa->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->obRContabilidadeLote->setExercicio( $this->stExercicio );
                $this->obRContabilidadeTransferenciaDespesa->obRContabilidadeLancamentoTransferencia->setCodTipo( $this->inCodTipo );
                $this->obRContabilidadeTransferenciaDespesa->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $this->roUltimoDespesaReducao->obROrcamentoEntidade->getCodigoEntidade() );
            }
        }
    }

    return $obErro;
}

/**
    * Inclui dados no banco
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluirSuplementada($boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoSuplementacaoSuplementada.class.php"                   );
    $obTOrcamentoSuplementacaoSuplementada = new TOrcamentoSuplementacaoSuplementada;

    $this->nuVlSuplementacao = 0;
    $obErro = new Erro;
    if ( sizeof( $this->arDespesaSuplementada ) > 0 ) {
        foreach ($this->arDespesaSuplementada as $obDespesaSuplementada) {
            $obErro = $this->checarSaldoDotacaoInicial( $boVlInicial, $obDespesaSuplementada, $boTransacao );
            if ( !$obErro->ocorreu() ) {
                if (!$boVlInicial) {
                    $obTOrcamentoSuplementacaoSuplementada->setDado( "exercicio"         , $this->stExercicio         );
                    $obTOrcamentoSuplementacaoSuplementada->setDado( "cod_suplementacao" , $this->inCodSuplementacao  );
                    $obTOrcamentoSuplementacaoSuplementada->setDado( "cod_despesa"       , $obDespesaSuplementada->getCodDespesa()    );
                    $obTOrcamentoSuplementacaoSuplementada->setDado( "valor"             , $obDespesaSuplementada->getValorOriginal() );
                    $this->nuVlSuplementacao = bcadd( $this->nuVlSuplementacao, $obDespesaSuplementada->getValorOriginal(), 2 );

                    $obErro = $obTOrcamentoSuplementacaoSuplementada->inclusao( $boTransacao );
                } else {
                    $obErro->setDescricao( 'Esta Suplementação não é um Crédito Especial' );
                }
            }
            if ( $obErro->ocorreu() ) {
                break;
            }
        }
    } elseif ($this->inCodTipo != 16) {
        $obErro->setDescricao( "É necessário cadastrar pelo menos uma Suplementação" );
    }

    if ($this->inCodTipo != 16) {
        if ( !$obErro->ocorreu() and $this->nuVlSuplementacao != $this->nuVlTotal ) {
            $obErro->setDescricao("Valor a ser suplementado é diferente do valor da norma informado!");
        }
    }

    return $obErro;
}

/**
    * Inclui dados no banco
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluirReducao($boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoSuplementacaoReducao.class.php"                        );
    $obTOrcamentoSuplementacaoReducao      = new TOrcamentoSuplementacaoReducao;

    $obErro = new Erro;
    $this->nuVlReducao = 0;
    if ( sizeof( $this->arDespesaReducao ) ) {
        foreach ($this->arDespesaReducao as $obDespesaReducao) {

            $obDespesaReducao->setExercicio ( $this->stExercicio );
            $obDespesaReducao->consultarSaldoDotacao( $boTransacao );
            if ( $obDespesaReducao->getValorOriginal() <= $obDespesaReducao->getSaldoDotacao() or $this->inCodTipo == 16 ) {

               $obTOrcamentoSuplementacaoReducao->setDado( 'cod_suplementacao', $this->inCodSuplementacao             );
               $obTOrcamentoSuplementacaoReducao->setDado( 'exercicio'        , $this->stExercicio                    );
               $obTOrcamentoSuplementacaoReducao->setDado( 'cod_despesa'      , $obDespesaReducao->getCodDespesa()    );
               $obTOrcamentoSuplementacaoReducao->setDado( 'valor'            , $obDespesaReducao->getValorOriginal() );
               $this->nuVlReducao = bcadd( $this->nuVlReducao, $obDespesaReducao->getValorOriginal(), 2 );

               $obErro = $obTOrcamentoSuplementacaoReducao->inclusao( $boTransacao );
            } else {
               $obErro->setDescricao("Valor a reduzir é superior ao saldo da dotação" );
            }
            if ( $obErro->ocorreu() ) {
                break;
            }
        }
    }

    if ( !$obErro->ocorreu() and sizeof( $this->arDespesaReducao ) and $this->inCodTipo != 16 ) {
        if( $this->nuVlReducao != $this->nuVlTotal )
            $obErro->setDescricao( 'Valor a ser reduzido é diferente do valor da norma informado!' );
    }

    return $obErro;
}

/**
    * Inclui dados no banco
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluir($boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoSuplementacao.class.php"                               );
    $obTOrcamentoSuplementacao             = new TOrcamentoSuplementacao;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    //consulta Tipo de Norma
//    $obErro = $this->obRNorma->consultar( $rsRecordSet, $boTransacao );
//    if ( strpos(strtoupper(' '.$this->obRNorma->obRTipoNorma->getNomeTipoNorma()), strtoupper("decreto")) ) {
//        $obErro = $this->listar( $rsRecordSet ,'cod_suplementacao', $boTransacao );
//        if ( !$rsRecordSet->eof() )
//            $obErro->setDescricao("Este decreto já foi utilizado nesse tipo de suplementação - ");
//    }
    if ( !$obErro->ocorreu() and $this->inCodTipo == 14 ) {
        $obErro = $this->validarTransferencia();
    }
    $stDtAtual = date("d/m/Y");
    if ( !$obErro->ocorreu() ) {
        list( $dia1,$mes1,$ano1 ) = explode( '/', $stDtAtual );
        list( $dia2,$mes2,$ano2 ) = explode( '/', $this->getDtLancamento() );
        if ("$ano1$mes1$dia1" < "$ano2$mes2$dia2") {
            $obErro->setDescricao("A data informada não pode ser maior que a atual - ");
        }
    }
    if ( !$obErro->ocorreu() ) {
        $obTOrcamentoSuplementacao->setDado( "exercicio"         , $this->stExercicio             );
        $obTOrcamentoSuplementacao->setDado( "cod_suplementacao" , $this->inCodSuplementacao      );
        $obTOrcamentoSuplementacao->setDado( "cod_norma"         , $this->obRNorma->getCodNorma() );
        $obTOrcamentoSuplementacao->setDado( "cod_tipo"          , $this->inCodTipo               );
        $obTOrcamentoSuplementacao->setDado( "dt_suplementacao"  , $this->stDtLancamento          );
        $obTOrcamentoSuplementacao->setDado( "motivo"            , $this->stMotivo                );

        $obTOrcamentoSuplementacao->proximoCod( $this->inCodSuplementacao , $boTransacao );

        $obTOrcamentoSuplementacao->setDado( "exercicio"         , $this->stExercicio             );
        $obTOrcamentoSuplementacao->setDado( "cod_suplementacao" , $this->inCodSuplementacao      );
        $obTOrcamentoSuplementacao->setDado( "cod_norma"         , $this->obRNorma->getCodNorma() );
        $obTOrcamentoSuplementacao->setDado( "cod_tipo"          , $this->inCodTipo               );
        $obTOrcamentoSuplementacao->setDado( "dt_suplementacao"  , $this->stDtLancamento          );
        $obTOrcamentoSuplementacao->setDado( "motivo"            , $this->stMotivo                );

        $obErro = $obTOrcamentoSuplementacao->inclusao( $boTransacao );

        if ( !$obErro->ocorreu() ) {
            $obErro = $this->incluirSuplementada( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $obErro = $this->incluirReducao( $boTransacao );
            }
        }

        if ( !$obErro->ocorreu() ) {
            $obErro = $this->fazerLancamento( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->obRContabilidadeTransferenciaDespesa->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->obRContabilidadeLote->setExercicio( $this->stExercicio );
                $this->obRContabilidadeTransferenciaDespesa->obRContabilidadeLancamentoTransferencia->setCodTipo( $this->inCodTipo );
                $obErro = $this->obRContabilidadeTransferenciaDespesa->incluir( $boTransacao );
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTOrcamentoReserva );

    return $obErro;
}

/**
    * Inclui dados no banco
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function anular($boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoSuplementacao.class.php"                               );
    $obTOrcamentoSuplementacao             = new TOrcamentoSuplementacao;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $obTOrcamentoSuplementacao->setDado( "exercicio"         , $this->stExercicio             );
        $obTOrcamentoSuplementacao->setDado( "cod_suplementacao" , $this->inCodSuplementacao      );
        $obTOrcamentoSuplementacao->setDado( "cod_norma"         , $this->obRNorma->getCodNorma() );
        $obTOrcamentoSuplementacao->setDado( "cod_tipo"          , $this->inCodTipo               );
        $obTOrcamentoSuplementacao->setDado( "dt_suplementacao"  , $this->stDtLancamento          );
        $obTOrcamentoSuplementacao->setDado( "motivo"            , $this->stMotivo                );

        $obTOrcamentoSuplementacao->proximoCod( $this->inCodSuplementacao , $boTransacao );

        $obTOrcamentoSuplementacao->setDado( "exercicio"         , $this->stExercicio             );
        $obTOrcamentoSuplementacao->setDado( "cod_suplementacao" , $this->inCodSuplementacao      );
        $obTOrcamentoSuplementacao->setDado( "cod_norma"         , $this->obRNorma->getCodNorma() );
        $obTOrcamentoSuplementacao->setDado( "cod_tipo"          , $this->inCodTipo               );
        $obTOrcamentoSuplementacao->setDado( "dt_suplementacao"  , $this->stDtLancamento          );
        $obTOrcamentoSuplementacao->setDado( "motivo"            , $this->stMotivo                );

        $obErro = $obTOrcamentoSuplementacao->inclusao( $boTransacao );

        if ( !$obErro->ocorreu() ) {
            $obErro = $this->incluirSuplementada( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $obErro = $this->incluirReducao( $boTransacao );
            }
        }

        if ( !$obErro->ocorreu() ) {
            $obErro = $this->fazerLancamento( $boTransacao );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTOrcamentoReserva );

    return $obErro;
}

/**
    * Método para anular suplementações
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object $obErro Objeto de Erro
*/
function anularSuplementacao($boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoSuplementacaoAnulada.class.php"                        );
    $obTOrcamentoSuplementacaoAnulada = new TOrcamentoSuplementacaoAnulada;
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $arDtAnulacao   = explode('/',$this->stDtAnulacao   );
        $arDtLancamento = explode('/',$this->stDtLancamento );
        $stDtAnulacao   = $arDtAnulacao[2].$arDtAnulacao[1].$arDtAnulacao[0];
        $stDtLancamento = $arDtLancamento[2].$arDtLancamento[1].$arDtLancamento[0];

        if( $stDtAnulacao < $stDtLancamento )
            $obErro->setDescricao( 'Data de anulação da suplementação deve ser maior que a data da suplementação' );

        if ( !$obErro->ocorreu() ) {
            // Inverte as despesas para realizar a anulação corretamente
            $arDespesaSuplementada = $this->arDespesaSuplementada;
            $arDespesaReducao      = $this->arDespesaReducao;
            $this->arDespesaSuplementada = $arDespesaReducao;
            $this->arDespesaReducao      = $arDespesaSuplementada;
            $this->obRContabilidadeTransferenciaDespesa->obRContabilidadeLancamentoTransferencia->setEstorno( true );
            //A data de lancamento recebe a data de anulção antes de inserir a suplementacao de anulação, porque o metodo inserir que é utilizado tanto no cadastro quando na anulação utiliza a data de lançamento, que deve corresponder ao dia de anulação quando a suplementação inserida foi para anular outra.
            $this->stDtLancamento = $this->stDtAnulacao;
            $obErro = $this->incluir( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $obTOrcamentoSuplementacaoAnulada->setDado( 'cod_suplementacao'         , $this->inCodSuplementacaoAnulada );
                $obTOrcamentoSuplementacaoAnulada->setDado( 'exercicio'                 , $this->stExercicio               );
                $obTOrcamentoSuplementacaoAnulada->setDado( 'cod_suplementacao_anulacao', $this->inCodSuplementacao        );
                $obErro = $obTOrcamentoSuplementacaoAnulada->inclusao( $boTransacao );
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTOrcamentoReserva );

    return $obErro;
}

/**
    * Lista suplementações de acordo com o filtro
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listar(&$rsLista, $stOrder = "cod_suplementacao", $boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoSuplementacao.class.php"                               );
    $obTOrcamentoSuplementacao             = new TOrcamentoSuplementacao;

    $stFiltro = "";
    if( $this->inCodSuplementacao )
        $stFiltro .= " cod_suplementacao = ".$this->inCodSuplementacao." AND ";
    if( $this->stExercicio )
        $stFiltro .= " exercicio = '".$this->stExercicio."' AND ";
    if( $this->obRNorma->getCodNorma() )
        $stFiltro .= " cod_norma = ".$this->obRNorma->getCodNorma() ." AND ";
    if( $this->getCodTipo() )
        $stFiltro .= " cod_tipo = ".$this->getCodTipo()." AND ";

    $stFiltro = ($stFiltro) ? ' WHERE '.substr($stFiltro,0,(strlen($stFiltro)-4)):'';
    $stOrder = ($stOrder) ? $stOrder : 'cod_suplementacao';
    $obErro = $obTOrcamentoSuplementacao->recuperaTodos( $rsLista, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Lista suplementações de acordo com o filtro
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarSuplementacaoDespesa(&$rsLista, $stOrder = "", $boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoSuplementacao.class.php"                               );
    $obTOrcamentoSuplementacao             = new TOrcamentoSuplementacao;

    $stFiltro = "";
    if( $this->inCodSuplementacao )
        $stFiltro .= " OS.cod_suplementacao = ".$this->inCodSuplementacao." AND ";
    if( $this->stExercicio )
        $stFiltro .= " OS.exercicio = '".$this->stExercicio."' AND ";
    if( $this->obRNorma->getCodNorma() )
        $stFiltro .= " OS.cod_norma = ".$this->obRNorma->getCodNorma() ." AND ";
    if( $this->getCodTipo() and $this->getCodTipo() != 16 )
        $stFiltro .= " OS.cod_tipo = ".$this->getCodTipo()." AND ";
    else
        $stFiltro .= " OS.cod_tipo != 16 AND ";

    $stFiltro = ($stFiltro) ? ' WHERE '.substr($stFiltro,0,(strlen($stFiltro)-4)):'';
    $stOrder = ($stOrder) ? $stOrder : 'exercicio,cod_suplementacao,cod_despesa';
    $obErro = $obTOrcamentoSuplementacao->recuperaSuplementacaoDespesa( $rsLista, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Executa recupera relacionamento de acordo com o filtro
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarSuplementacao(&$rsLista, $stOrder = "", $boTransacao = "")
{
    include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoSuplementacao.class.php";
    $obTOrcamentoSuplementacao = new TOrcamentoSuplementacao;

    $stFiltro = "";
    if( $this->inCodSuplementacao )
        $stFiltro .= " OS.cod_suplementacao = ".$this->inCodSuplementacao." AND ";

    if( $this->stExercicio )
        $stFiltro .= " OS.exercicio = '".$this->stExercicio."' AND ";

    if( $this->obRNorma->getCodNorma() )
        $stFiltro .= " OS.cod_norma = ".$this->obRNorma->getCodNorma() ." AND ";

    if( $this->getCodTipo() )
        $stFiltro .= " OS.cod_tipo = ".$this->getCodTipo()." AND ";

    if ( $this->stDtSuplementacaoInicial AND $this->stDtSuplementacaoFinal )
        $stFiltro .= " OS.dt_suplementacao BETWEEN TO_DATE( '".$this->stDtSuplementacaoInicial."', 'dd/mm/yyyy' ) AND TO_DATE( '".$this->stDtSuplementacaoFinal."', 'dd/mm/yyyy' ) AND";

    if( $this->stDtLancamento )
        $stFiltro .= " OS.dt_suplementacao = TO_DATE( '".$this->stDtLancamento."', 'dd/mm/yyyy' ) AND ";

    if( $this->obROrcamentoDespesa->getCodDespesa() )
        $obTOrcamentoSuplementacao->setDado( 'inCodDespesa', $this->obROrcamentoDespesa->getCodDespesa() );

    if( $this->obROrcamentoDespesa->obROrcamentoRecurso->getCodRecurso() )
        $stFiltro .= " OSS.cod_recurso = ".$this->obROrcamentoDespesa->obROrcamentoRecurso->getCodRecurso(). " AND";

    if ( $this->obRContabilidadeTransferenciaDespesa->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() ) {
        $inCodEntidade = $this->obRContabilidadeTransferenciaDespesa->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade();
        if (Sessao::getExercicio() > '2012') {
            $stFiltro .= " ( OSS.cod_entidade IN ( ".$inCodEntidade. " ) OR OSR.cod_entidade IN ( ".$inCodEntidade. " ) ) AND ";
        } else {
            $stFiltro .= " CTD.cod_entidade IN ( ".$inCodEntidade. " ) AND ";
        }
    }

    if ($this->inSituacao == '2') {
        $stFiltro .= " OSA.cod_suplementacao IS NULL \n";
        $stFiltro .= " AND";
    } elseif ($this->inSituacao == '3') {
        $stFiltro .= " OSA.cod_suplementacao IS NOT NULL \n";
        $stFiltro .= " AND";
    }

    $stFiltro = ($stFiltro) ? ' AND '.substr($stFiltro,0,(strlen($stFiltro)-4)):'';
    $obTOrcamentoSuplementacao->setDado( 'stFiltro' , $stFiltro );
    $obTOrcamentoSuplementacao->setDado( 'stExercicio', $this->stExercicio );
    if( $this->obROrcamentoDespesa->obROrcamentoRecurso->getDestinacaoRecurso() )
        $obTOrcamentoSuplementacao->setDado('stDestinacaoRecurso', $this->obROrcamentoDespesa->obROrcamentoRecurso->getDestinacaoRecurso() );
    if( $this->obROrcamentoDespesa->obROrcamentoRecurso->getCodDetalhamento() )
        $obTOrcamentoSuplementacao->setDado('inCodDetalhamento',$this->obROrcamentoDespesa->obROrcamentoRecurso->getCodDetalhamento() );

    $stOrder = ($stOrder) ? $stOrder : 'OS.cod_suplementacao,OS.cod_tipo,OS.dt_suplementacao';

    $obErro = $obTOrcamentoSuplementacao->recuperaRelacionamentoRecurso( $rsLista, '', $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Lista os tipos de suplementação de acordo com o filtro
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarTipo(&$rsLista, $stOrder = "cod_tipo", $boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeTipoTransferencia.class.php"                      );
    $obTContabilidadeTipoTransferencia     = new TContabilidadeTipoTransferencia;

    $stFiltro = "";
    if( $this->inCodTipo )
        $stFiltro .= " cod_tipo = ".$this->inCodTipo." AND ";
    if( $this->stExercicio )
        $stFiltro .= " exercicio = '".$this->stExercicio."' AND ";

    $stFiltro = ($stFiltro) ? ' WHERE '.substr($stFiltro,0,(strlen($stFiltro)-4)):'';
    $stOrder = ($stOrder) ? $stOrder : 'cod_tipo';
    $obErro = $obTContabilidadeTipoTransferencia->recuperaTodos( $rsLista, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Método para listar as suplementações suplementadas
    * @access Public
    * @param Object $boTransacao
    * @return Object $obErro
*/
function listarSuplementacaoSuplementada(&$arDespesaSuplementada, $boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoSuplementacaoSuplementada.class.php"                   );
    $obTOrcamentoSuplementacaoSuplementada = new TOrcamentoSuplementacaoSuplementada;

    $obTOrcamentoSuplementacaoSuplementada->setDado( 'cod_suplementacao', $this->inCodSuplementacao );
    $obTOrcamentoSuplementacaoSuplementada->setDado( 'exercicio'        , $this->stExercicio        );
    $obErro = $obTOrcamentoSuplementacaoSuplementada->recuperaPorChave( $rsRecordSet, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $inCount = 0;
        while ( !$rsRecordSet->eof() ) {
            $arDespesaSuplementada[$inCount] = new ROrcamentoDespesa();
            $arDespesaSuplementada[$inCount]->setExercicio( $this->stExercicio );
            $arDespesaSuplementada[$inCount]->setCodDespesa( $rsRecordSet->getCampo( 'cod_despesa' ) );
            $arDespesaSuplementada[$inCount]->setValorOriginal( $rsRecordSet->getCampo( 'valor' ) );
            $obErro = $arDespesaSuplementada[$inCount]->consultarContaDespesa( $rsRecordSet2, $boTransacao );
            if ( $obErro->ocorreu() ) {
                break;
            } else {
                $arDespesaSuplementada[$inCount]->setDescricao( $rsRecordSet2->getCampo('descricao') );
                $arDespesaSuplementada[$inCount]->obROrcamentoEntidade->setCodigoEntidade( $rsRecordSet2->getCampo('cod_entidade') );
                $arDespesaSuplementada[$inCount]->consultarSaldoDotacao();
                $inCount++;
                $rsRecordSet->proximo();
            }
        }
    }

    return $obErro;
}

/**
    * Método para listar as suplementações reduzidas
    * @access Public
    * @param Object $boTransacao
    * @return Object $obErro
*/
function listarSuplementacaoReducao(&$arDespesaReducao, $boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoSuplementacaoReducao.class.php"                        );
    $obTOrcamentoSuplementacaoReducao      = new TOrcamentoSuplementacaoReducao;

    $obTOrcamentoSuplementacaoReducao->setDado( 'cod_suplementacao', $this->inCodSuplementacao );
    $obTOrcamentoSuplementacaoReducao->setDado( 'exercicio'        , $this->stExercicio        );
    $obErro = $obTOrcamentoSuplementacaoReducao->recuperaPorChave( $rsRecordSet, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $inCount = 0;
        while ( !$rsRecordSet->eof() ) {
            $arDespesaReducao[$inCount] = new ROrcamentoDespesa();
            $arDespesaReducao[$inCount]->setExercicio( $this->stExercicio );
            $arDespesaReducao[$inCount]->setCodDespesa( $rsRecordSet->getCampo( 'cod_despesa' ) );
            $arDespesaReducao[$inCount]->setValorOriginal( $rsRecordSet->getCampo( 'valor' ) );
            $obErro = $arDespesaReducao[$inCount]->consultarContaDespesa( $rsRecordSet2, $boTransacao );
            if ( $obErro->ocorreu() ) {
                break;
            } else {
                $arDespesaReducao[$inCount]->setDescricao( $rsRecordSet2->getCampo('descricao') );
                $arDespesaReducao[$inCount]->obROrcamentoEntidade->setCodigoEntidade( $rsRecordSet2->getCampo('cod_entidade') );
                $arDespesaReducao[$inCount]->consultarSaldoDotacao();
                $inCount++;
                $rsRecordSet->proximo();
            }
        }
    }

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
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoSuplementacao.class.php"                               );
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeTipoTransferencia.class.php"                      );
    $obTContabilidadeTipoTransferencia     = new TContabilidadeTipoTransferencia;
    $obTOrcamentoSuplementacao             = new TOrcamentoSuplementacao;

    $obTOrcamentoSuplementacao->setDado( 'cod_suplementacao', $this->inCodSuplementacao );
    $obTOrcamentoSuplementacao->setDado( 'exercicio'        , $this->stExercicio        );
    if ($this->inCodSuplementacao) {
        $stFiltro .= " AND S.cod_suplementacao = ".$this->inCodSuplementacao;
    }
    if ($this->stExercicio) {
        $stFiltro .= " AND S.exercicio = '".$this->stExercicio."'";
    }
    $obErro = $obTOrcamentoSuplementacao->recuperaConsultaSuplementacao( $rsRecordSet, $stFiltro, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->inCodTipo      = $rsRecordSet->getCampo("cod_tipo"          );
        $this->stDtLancamento = $rsRecordSet->getCampo("dt_suplementacao"  );
        $this->stMotivo       = $rsRecordSet->getCampo("motivo"            );
        $this->nuVlTotal      = $rsRecordSet->getCampo("vl_suplementacao"  );
        $this->stDtAnulacao   = $rsRecordSet->getCampo("dt_anulacao"       );
        $this->obRNorma->setCodNorma( $rsRecordSet->getCampo("cod_norma" ) );
        $obErro = $this->obRNorma->consultar( $rsRecordSet, $boTransacao );
           if ( !$obErro->ocorreu() ) {
            $obTContabilidadeTipoTransferencia->setDado( 'cod_tipo', $this->inCodTipo );
            $obErro = $obTContabilidadeTipoTransferencia->recuperaPorChave( $rsRecordSet, $boTransacao );
            $this->stNomTipo = $rsRecordSet->getCampo( 'nom_tipo' );
            if ( !$obErro->ocorreu() ) {
                $obErro = $this->obRContabilidadeTransferenciaDespesa->consultar( $boTransacao );
            }
        }
        if ( !$obErro->ocorreu() ) {
            $obErro = $this->listarSuplementacaoSuplementada( $arDespesaSuplementada, $boTransacao );
            if( !$obErro->ocorreu() )
                $obErro = $this->listarSuplementacaoReducao( $arDespesaReducao, $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->arDespesaSuplementada = $arDespesaSuplementada;
                $this->arDespesaReducao = $arDespesaReducao;
            }
        }
    }

    return $obErro;
}

/**
    * Método para consultar o recurso do lancamento de uma suplementação
    * @access Public
    * @param Object $boTransacao
    * @return Object $obErro
*/
function consultarHistoricoLancamento($boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoSuplementacao.class.php"                               );
    $obTOrcamentoSuplementacao             = new TOrcamentoSuplementacao;

    if( $this->inCodSuplementacao )
        $stFiltro = " AND OS.cod_suplementacao = ".$this->inCodSuplementacaoAnulada;
    if( $this->stExercicio )
        $stFiltro .= " AND OS.exercicio = '".$this->stExercicio."' ";
    if( $this->inCodTipo )
        $stFiltro .= " AND OS.cod_tipo = ".$this->inCodTipoAnulacao;
    if( $this->obRContabilidadeTransferenciaDespesa->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() )
        $stFiltro .= " AND CTD.cod_entidade = ".$this->obRContabilidadeTransferenciaDespesa->obRContabilidadeLancamentoTransferencia->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade();

    $obErro = $obTOrcamentoSuplementacao->recuperaHistorico( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obRContabilidadeHistoricoPadrao->setCodHistorico( $rsRecordSet->getCampo( 'cod_historico' ) );
    }

    return $obErro;
}

}

?>