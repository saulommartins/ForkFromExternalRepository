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
    * Classe de regra de negócio para Empenho - Ordem de Pagamento
    * Data de Criação: 15/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues

    * @package URBEM
    * @subpackage Regra

    $Id: REmpenhoOrdemPagamento.class.php 64368 2016-01-28 12:04:02Z franver $

    * Casos de uso: uc-02.03.03,uc-02.03.05,uc-02.03.20,uc-02.03.23,uc-02.04.05,uc-02.03.28
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GF_EMP_NEGOCIO."REmpenhoNotaLiquidacao.class.php"             );
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"                 );
include_once ( CAM_GF_EMP_NEGOCIO."REmpenhoEmpenho.class.php"                    );
include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoOrdemPagamentoLiquidacaoAnulada.class.php" );

/**
* Classe de regra de negócio para Empenho - Ordem de Pagamento
* Data de Criação: 15/12/2004

* @author Analista: Jorge B. Ribarr
* @author Desenvolvedor: Fábio Bertoldi Rodrigues

* @package URBEM
* @subpackage Regra
*/

class REmpenhoOrdemPagamento
{
/**
* @access Private
* @var Integer
*/
var $inCodigoEmpenho;
/**
* @access Private
* @var Integer
*/
var $inCodigoOrdem;
/**
* @access Private
* @var Integer
*/
var $inCodigoOrdemInicial;
/**
* @access Private
* @var Integer
*/
var $inCodigoOrdemFinal;
/**
* @access Private
* @var String
*/
var $stExercicio;
/**
* @access Private
* @var String
*/
var $stObservacao;
/**
* @access Private
* @var Date
*/
var $dtDataEmissao;
/**
* @access Private
* @var Date
*/
var $dtDataEmissaoInicial;
/**
* @access Private
* @var Date
*/
var $dtDataEmissaoFinal;
/**
* @access Private
* @var Date
*/
var $dtDataVencimento;
/**
* @access Private
* @var Date
*/
var $dtDataPagamento;
/**
* @access Private
* @var Date
*/
var $dtDataEstorno;
/**
* @access Private
* @var Date
*/
var $dataAnulacao;
/**
* @access Private
* @var String
*/
var $stTimestampAnulacao;
/**
* @access Private
* @var String
*/
var $stMotivo;
/**
* @access Private
* @var String
*/
var $stSituacao;
/**
* @access Private
* @var Float
*/
var $flValorAnulado;
/**
* @access Private
* @var Float
*/
var $flValorPagamento;
/**
* @access Private
* @var Float
*/
var $flValorNota;
var $flValorNotaOriginal;
var $flValorNotaAnulacoes;
var $stPagamentoEstornado;
var $arRetencao;
var $nuTotalRetencoes;
/**
* @access Private
* @var Object
*/
var $obREmpenhoNotaLiquidacao;
/**
* @access Private
* @var Object
*/
var $obFEmpenhoSaldoPagamento;
/**
* @access Private
* @var Array
*/
var $arNotaLiquidacao;
/**
* @access Private
* @var Integer
*/
var $inCodFornecedor;
/**
    * @access Private
    * @var Boolean
*/
var $boListarAnulada;
/**
    * @access Private
    * @var Boolean
*/
var $boListarNaoAnulada;
/**
    * @access Private
    * @var Boolean
*/
var $boListarPaga;
/**
    * @access Private
    * @var Boolean
*/
var $boListarNaoPaga;
/**
    * @access Private
    * @var Numeric
*/
var $inSaldoPagamento;
/**
    * @access Private
    * @var Numeric
*/
var $nuValorAPagar;
/**
    * @access Private
    * @var Array
*/
var $arOrdemPagamentoLiquidacaoAnulada;
/**
    * @access Private
    * @var String
*/
var $stTipo;
/**
    * @access Private
    * @var Boolean
*/
var $boEstorno;
/**
    * @access Private
    * @var Numeric
*/
var $vlPago;
var $boRetencao;
var $arRetencoes;

//SETTERS
/**
* @access Public
* @param Integer $valor
*/
function setSaldoPagamento($valor) { $this->inSaldoPagamento = $valor;        }
/**
* @access Public
* @param Integer $valor
*/
function setValorAPagar($valor) { $this->nuValorAPagar = $valor;        }
/**
* @access Public
* @param Integer $valor
*/
function setCodigoEmpenho($valor) { $this->inCodigoEmpenho = $valor;        }
/**
* @access Public
* @param Integer $valor
*/
function setCodigoOrdem($valor) { $this->inCodigoOrdem = $valor;        }
/**
* @access Public
* @param Integer $valor
*/
function setCodigoOrdemInicial($valor) { $this->inCodigoOrdemInicial = $valor; }
/**
* @access Public
* @param Integer $valor
*/
function setCodigoOrdemFinal($valor) { $this->inCodigoOrdemFinal = $valor;   }
/**
* @access Public
* @param String $valor
*/
function setExercicio($valor) { $this->stExercicio = $valor;          }
/**
* @access Public
* @param String $valor
*/
function setObservacao($valor) { $this->stObservacao = $valor;         }
/**
* @access Public
* @param Date $valor
*/
function setDataEmissao($valor) { $this->dtDataEmissao = $valor;        }
/**
* @access Public
* @param Date $valor
*/
function setDataEmissaoInicial($valor) { $this->dtDataEmissaoInicial = $valor; }
/**
* @access Public
* @param Date $valor
*/
function setDataEmissaoFinal($valor) { $this->dtDataEmissaoFinal = $valor;   }
/**
* @access Public
* @param Date $valor
*/
function setDataVencimento($valor) { $this->dtDataVencimento = $valor;     }
/**
* @access Public
* @param Date $valor
*/
function setDataPagamento($valor) { $this->dtDataPagamento   = $valor;     }
/**
* @access Public
* @param Date $valor
*/
function setDataEstorno($valor) { $this->dtDataEstorno     = $valor;     }
/**
* @access Public
* @param Date $valor
*/
function setDataAnulacao($valor) { $this->dtDataAnulacao    = $valor;     }
/**
* @access Public
* @param String $valor
*/
function setTimestampAnulacao($valor) { $this->stTimestampAnulacao    = $valor;     }
/**
* @access Public
* @param String $valor
*/
function setMotivo($valor) { $this->stMotivo = $valor;             }
/**
* @access Public
* @param String $valor
*/
function setSituacao($valor) { $this->stSituacao = $valor;            }
/**
* @access Public
* @param Float $valor
*/
function setValorAnulado($valor) { $this->flValorAnulado = $valor;       }
/**
* @access Public
* @param Float $valor
*/
function setValorPagamento($valor) { $this->flValorPagamento = $valor;     }
/**
* @access Public
* @param Float $valor
*/
function setValorNota($valor) { $this->flValorNota      = $valor;     }
/**
* @access Public
* @param Float $valor
*/
function setNotaLiquidacao($valor) { $this->arNotaLiquidacao = $valor;     }
/**
* @access Public
* @param Integer $valor
*/
function setFornecedor($valor) { $this->inCodFornecedor = $valor;     }
/**
    * @access Public
    * @param Boolean $valor
*/
function setListarAnulada($valor) { $this->boListarAnulada = $valor;     }
/**
    * @access Public
    * @param Boolean $valor
*/
function setListarNaoAnulada($valor) { $this->boListarNaoAnulada = $valor;  }
/**
    * @access Public
    * @param Boolean $valor
*/
function setListarPaga($valor) { $this->boListarPaga = $valor;        }
/**
    * @access Public
    * @param Boolean $valor
*/
function setListarNaoPaga($valor) { $this->boListarNaoPaga = $valor;     }
/**
    * @access Public
    * @param array $valor
*/
function setOrdemPagamentoLiquidacaoAnulada($valor) { $this->arOrdemPagamentoLiquidacaoAnulada = $valor;     }
/**
    * @access Public
    * @param String $stTipo
*/
function setTipo($valor) { $this->stTipo          = $valor; }
/**
    * @access Public
    * @param Boolean $boEstorno
*/
function setEstorno($valor) { $this->boEstorno       = $valor; }
/**
    * @access Public
    * @param Numeric $vlPago
*/
function setValorPago($valor) { $this->vlPago =  $valor; }
function setPagamentoEstornado($valor) { $this->stPagamentoEstornado = $valor; }
function setRetencao($valor) { $this->boRetencao = $valor; }
function setRetencoes($valor) { $this->arRetencoes = $valor; }
/**
    * @access Public
    * @param Boolean $boEstorno
*/
function setAdiantamento($valor) { $this->boAdiantamento = $valor; }
//GETTERS
/**
* @access Public
* @return Integer
*/
function getSaldoPagamento() { return $this->inSaldoPagamento;    }
/**
* @access Public
* @return Integer
*/
function getValorAPagar() { return $this->nuValorAPagar;    }
/**
* @access Public
* @return Integer
*/
function getCodigoEmpenho() { return $this->inCodigoEmpenho;    }
/**
* @access Public
* @return Integer
*/
function getCodigoOrdem() { return $this->inCodigoOrdem;        }
/**
* @access Public
* @return Integer
*/
function getCodigoOrdemInicial() { return $this->inCodigoOrdemInicial; }
/**
* @access Public
* @return Integer
*/
function getCodigoOrdemFinal() { return $this->inCodigoOrdemFinal;    }
/**
* @access Public
* @return String
*/
function getExercicio() { return $this->stExercicio;           }
/**
* @access Public
* @return String
*/
function getObservacao() { return $this->stObservacao;          }
/**
* @access Public
* @return Date
*/
function getDataEmissao() { return $this->dtDataEmissao;         }
/**
* @access Public
* @return Date
*/
function getDataEmissaoInicial() { return $this->dtDataEmissaoInicial;  }
/**
* @access Public
* @return Date
*/
function getDataEmissaoFinal() { return $this->dtDataEmissaoFinal;    }
/**
* @access Public
* @return Date
*/
function getDataVencimento() { return $this->dtDataVencimento;      }
/**
* @access Public
* @return Date
*/
function getDataPagamento() { return $this->dtDataPagamento;       }
/**
* @access Public
* @return Date
*/
function getDataEstorno() { return $this->dtDataEstorno;         }
/**
* @access Public
* @return Date
*/
function getDataAnulacao() { return $this->dtDataAnulacao;        }
/**
* @access Public
* @return String
*/
function getTimestampAnulacao() { return $this->stTimestampAnulacao;        }
/**
* @access Public
* @return String
*/
function getMotivo() { return $this->stMotivo;              }
/**
* @access Public
* @return String
*/
function getSituacao() { return $this->stSituacao;            }
/**
* @access Public
* @return String
*/
function getValorAnulado() { return $this->flValorAnulado;        }
/**
* @access Public
* @return String
*/
function getValorPagamento() { return $this->flValorPagamento;      }
/**
* @access Public
* @return String
*/
function getValorNota() { return $this->flValorNota;           }
function getValorNotaOriginal() { return $this->flValorNotaOriginal;           }
function getValorNotaAnulacoes() { return $this->flValorNotaAnulacoes;           }
/**
* @access Public
* @return Date
*/
function getNotaLiquidacao() { return $this->arNotaLiquidacao;      }
/**
* @access Public
* @return Integer
*/
function getFornecedor() { return $this->inCodFornecedor;          }
/**
    * @access Public
    * @return Boolean
*/
function getListarAnulada() { return $this->boListarAnulada;      }
/**
    * @access Public
    * @return Boolean
*/
function getListarNaoAnulada() { return $this->boListarNaoAnulada;   }
/**
    * @access Public
    * @return Boolean
*/
function getListarPaga() { return $this->boListarPaga;         }
/**
    * @access Public
    * @return Boolean
*/
function getListarNaoPaga() { return $this->boListarNaoPaga;      }
/**
    * @access Public
    * @return Array
*/
function getOrdemPagamentoLiquidacaoAnulada() { return $this->arOrdemPagamentoLiquidacaoAnulada;      }
/**
    * @access Public
    * @return String
*/
function getTipo() { return $this->stTipo;                }
/**
    * @access Public
    * @return Boolean
*/
function getEstorno() { return $this->boEstorno;             }
/**
    * @access Public
    * @return Numeric
*/
function getValorPago() { return $this->vlPago;               }
function getPagamentoEstornado() { return $this->stPagamentoEstornado; }
function getRetencao() { return $this->boRetencao;           }
function getRetencoes() { return $this->arRetencoes;          }
/**
    * @access Public
    * @return Boolean
*/
function getAdiantamento() { return $this->boAdiantamento;       }
//METODO CONSTRUTOR
/**
* Método construtor
* @access Private
*/
function REmpenhoOrdemPagamento()
{
    $this->obREmpenhoNotaLiquidacao        = new REmpenhoNotaLiquidacao(new REmpenhoEmpenho);
    $this->obREmpenhoEmpenho               = new REmpenhoEmpenho;
    $this->obROrcamentoEntidade            = new ROrcamentoEntidade;
    $this->obTransacao                     = new Transacao;
    $this->arNotaLiquidacao                = array();
    $this->setEstorno( false );
    $this->setRetencao( false );
    $this->setAdiantamento( false );
}

/**
    * Método para adicionar notas de liquidação
    * @acces Public
*/
function addNotaLiquidacao()
{
    $this->arNotaLiquidacao[] = new REmpenhoNotaLiquidacao(new REmpenhoEmpenho);
    $this->roUltimaNotaLiquidacao = &$this->arNotaLiquidacao[ count( $this->arNotaLiquidacao )-1 ];
}

// METODOS FUNCIONAIS (inclusao,alteracao,exclusao...)
/**
* Inclui os dados setados na tabela de Ordem de pagamento
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function incluir($boTransacao = "", $boFlagTransacao = true)
{
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoOrdemPagamento.class.php"        );
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoPagamentoLiquidacao.class.php"   );
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoOrdemPagamentoRetencao.class.php"   );
    $obTEmpenhoPagamentoLiquidacao   = new TEmpenhoPagamentoLiquidacao;
    $obTEmpenhoOrdemPagamento        = new TEmpenhoOrdemPagamento;
    $obTEmpenhoOrdemPagamentoRetencao = new TEmpenhoOrdemPagamentoRetencao;
    $obErro = new Erro;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if ( count( $this->arNotaLiquidacao ) < 1  ) {
            $obErro->setDescricao( "Deve ser informado ao menos uma Nota de Liquidação!" );
        }
        if (!$obErro->ocorreu()) {
            $obErro = $this->listarMaiorData( $rsMaiorData, "",$boTransacao);
            if (!$obErro->ocorreu()) {
                $stMaiorData = $this->retornaMaiorDataLiquidacao( $this->arNotaLiquidacao );
                if (SistemaLegado::comparaDatas($stMaiorData,$this->dtDataEmissao)) {
                    $obErro->setDescricao( "Data da OP deve ser maior ou igual a '".$stMaiorData."'!" );
                }
                if ( !$obErro->ocorreu() ) {
                    if ( !$obErro->ocorreu() ) {
                        if ( (!$obErro->ocorreu() ) && ( SistemaLegado::comparaDatas($this->dtDataEmissao,$this->getDataVencimento()) ) ) {
                            $obErro->setDescricao("A data de vencimento deve ser maior ou igual a data da OP!");
                        }
                        if ( !$obErro->ocorreu() ) {
                            // Verifica configuração do empenho
                            $obREmpenhoNotaLiquidacao = new REmpenhoNotaLiquidacao( new REmpenhoEmpenho() );
                            $obTEmpenhoOrdemPagamento->setDado( "exercicio", $this->stExercicio );
                            $obErro = $obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->obREmpenhoConfiguracao->consultar( $boTransacao );
                            if ( !$obErro->ocorreu() ) {
                                if ( $obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->obREmpenhoConfiguracao->getNumeracao() == 'P' ) {
                                    $obTEmpenhoOrdemPagamento->setComplementoChave( "cod_entidade,exercicio" );
                                    $obTEmpenhoOrdemPagamento->setDado( "cod_entidade", $this->obROrcamentoEntidade->getCodigoEntidade() );
                                } else {
                                    $obTEmpenhoOrdemPagamento->setComplementoChave( "exercicio" );
                                    $obTEmpenhoOrdemPagamento->setDado( "cod_entidade", null );
                                }
                                $obErro = $obTEmpenhoOrdemPagamento->proximoCod( $this->inCodigoOrdem, $boTransacao );
                            }
                            if ( !$obErro->ocorreu() ) {
                                $obTEmpenhoOrdemPagamento->setDado( "cod_entidade", $this->obROrcamentoEntidade->getCodigoEntidade() );
                                $obTEmpenhoOrdemPagamento->setDado( "cod_ordem"     , $this->inCodigoOrdem    );
                                $obTEmpenhoOrdemPagamento->setDado( "observacao"    , $this->stObservacao     );
                                $obTEmpenhoOrdemPagamento->setDado( "dt_emissao"    , $this->dtDataEmissao    );
                                $obTEmpenhoOrdemPagamento->setDado( "dt_vencimento" , $this->dtDataVencimento );

                                $obErro = $obTEmpenhoOrdemPagamento->inclusao( $boTransacao );
                                if ( !$obErro->ocorreu() ) {
                                    if ($this->getRetencao()) {
                                            $obTEmpenhoOrdemPagamentoRetencao->setDado( "exercicio",    $this->stExercicio      );
                                            $obTEmpenhoOrdemPagamentoRetencao->setDado( "cod_entidade", $this->obROrcamentoEntidade->getCodigoEntidade() );
                                            $obTEmpenhoOrdemPagamentoRetencao->setDado( "cod_ordem",    $this->inCodigoOrdem    );
                                            if ( Sessao::getExercicio() > '2012' ) {
                                                $obTEmpenhoOrdemPagamentoRetencao->setDado( "estorno", 'f' );
                                            }
                                        foreach ($this->arRetencoes as $arRetencao => $item) {

                                            if ($item['stTipo'] == 'O') {
                                                $obTEmpenhoOrdemPagamentoRetencao->setDado("cod_receita", $item['cod_reduzido']);
                                                $obErro = $obTEmpenhoOrdemPagamentoRetencao->recuperaCodPlanoReceita( $rsRecordSet, $boTransacao );
                                                if(!$obErro->ocorreu() && $rsRecordSet->getNumLinhas() == 1)
                                                    $stCodPlano = $rsRecordSet->getCampo('cod_plano');
                                            } else {
                                                $stCodPlano = $item['cod_reduzido'];
                                            }
                                            
                                            $obTEmpenhoOrdemPagamentoRetencao->setDado( "cod_plano", $stCodPlano );
                                            $obTEmpenhoOrdemPagamentoRetencao->setDado( "vl_retencao", str_replace(',','.',str_replace('.','',$item['nuValor'])) );
                                            $obTEmpenhoOrdemPagamentoRetencao->proximoSequencial($inSequencial, $boTransacao);
                                            $obTEmpenhoOrdemPagamentoRetencao->setDado("sequencial", $inSequencial);

                                            $obErro = $obTEmpenhoOrdemPagamentoRetencao->inclusao ( $boTransacao );
                                            $obTEmpenhoOrdemPagamentoRetencao->setDado( "cod_plano", '' );
                                            $obTEmpenhoOrdemPagamentoRetencao->setDado("cod_receita", '');
                                        }

                                    }
                                    if (!$obErro->ocorreu()) {
                                        foreach ($this->arNotaLiquidacao as $novaNota => $arNotaLiquidacao) {
                                            if (SistemaLegado::comparaDatas($arNotaLiquidacao["dt_nota"],$this->dtDataEmissao)) {
                                                $obErro->setDescricao( "A data da O.P. deve ser posterior ou igual à data da liquidação." );
                                            }
                                            if ( !$obErro->ocorreu() ) {
                                                $nuVlAPagar = str_replace('.','',$arNotaLiquidacao["valor_pagar"] );
                                                $nuVlAPagar = str_replace(',','.',$nuVlAPagar );
                                                $nuVlMaxAPagar = str_replace('.','',$arNotaLiquidacao["max_valor_pagar"] );
                                                $nuVlMaxAPagar = str_replace(',','.',$nuVlMaxAPagar );
                                                if ($nuVlAPagar > $nuVlMaxAPagar) {
                                                    $obErro->setDescricao( "Valor do nota ".$arNotaLiquidacao["cod_nota"]." não pode ser superior a R$".$nuVlMaxAPagar );
                                                } else {
                                                    $obTEmpenhoPagamentoLiquidacao->setDado( "cod_ordem"            , $this->inCodigoOrdem                             );
                                                    $obTEmpenhoPagamentoLiquidacao->setDado( "exercicio"            , $this->stExercicio                               );
                                                    $obTEmpenhoPagamentoLiquidacao->setDado( "cod_entidade"         , $this->obROrcamentoEntidade->getCodigoEntidade() );
                                                    $obTEmpenhoPagamentoLiquidacao->setDado( "exercicio_liquidacao" , $arNotaLiquidacao["ex_nota"]                     );
                                                    $obTEmpenhoPagamentoLiquidacao->setDado( "cod_nota"             , $arNotaLiquidacao["cod_nota"]                    );
                                                    $obTEmpenhoPagamentoLiquidacao->setDado( "vl_pagamento"         , $nuVlAPagar                                      );

                                                    $obErro = $obTEmpenhoPagamentoLiquidacao->inclusao( $boTransacao );
                                                }
                                                if ( $obErro->ocorreu() ) {
                                                    break;
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
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTEmpenhoOrdemPagamento );

    return $obErro;
}

/**
* Anula a Ordem de pagamento setada
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function anular($boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoOrdemPagamentoAnulada.class.php" );

    $obTEmpenhoOrdemPagamentoAnulada = new TEmpenhoOrdemPagamentoAnulada;
    $obTEmpenhoOrdemPagamentoLiquidacaoAnulada = new TEmpenhoOrdemPagamentoLiquidacaoAnulada;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $rsLista = new RecordSet;
        $obErro = $this->listar( $rsLista, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $valorAnulado = 0;
            while ( !$rsLista->eof() ) {
                if ( $rsLista->getCampo("cod_ordem")  == $this->inCodigoOrdem AND $rsLista->getCampo("exercicio") == $this->stExercicio AND $rsLista->getCampo("cod_entidade") == $this->obROrcamentoEntidade->getCodigoEntidade() ) {
                    $valorAnulado += $rsLista->getCampo("valor_anulada");
                    $somaTemp = str_replace(".","",$this->flValorAnulado);
                    $valorParaAnular = str_replace(",",".",$somaTemp);
                    $valorAval = $rsLista->getCampo("vl_nota");
                }
                $rsLista->proximo();
            }

            $obErro = $this->consultar($boTransacao);
            if ( !$obErro->ocorreu() ) {
                if (SistemaLegado::comparaDatas($this->getDataEmissao(),date("d/m/Y"))) {
                    $obErro->setDescricao( "A data de anulação deve ser posterior ou igual à data da emissão." );
                }
                if ( !$obErro->ocorreu() ) {
                    if ($valorParaAnular <= $valorAval) {
                        if ( !$obErro->ocorreu() ) {
                            if ( is_array($this->arOrdemPagamentoLiquidacaoAnulada) ) {
                                foreach ($this->arOrdemPagamentoLiquidacaoAnulada as $obOPLA) {
                                    /*
                                     * Ticket #23502, é necessário fazer a busca do timestamp apartir do PHP, pois como o banco está numa mesma transação. Ele não altera cria um novo timestamp durante esse processo.
                                     * Foi também necessário adicionar um sleep, porque quando o PHP criar um timestamp, ele não pega o milisegundo EX.: 2016-01-01 09:10:35.000000.
                                     * Sendo assim o sleep logo abaixo é para fazer o PHP esperar um segundo para continuar o processo.
                                     **/

                                    $this->setTimestampAnulacao( date('Y-m-d H:i:s.u') );
                                    sleep(1);
                                    $obTEmpenhoOrdemPagamentoAnulada->setDado( "cod_ordem"    , $this->inCodigoOrdem  );
                                    $obTEmpenhoOrdemPagamentoAnulada->setDado( "exercicio"    , $this->stExercicio    );
                                    $obTEmpenhoOrdemPagamentoAnulada->setDado( "cod_entidade" , $this->obROrcamentoEntidade->getCodigoEntidade() );
                                    $obTEmpenhoOrdemPagamentoAnulada->setDado( "motivo"       , $this->stMotivo       );
                                    $obTEmpenhoOrdemPagamentoAnulada->setDado( "timestamp"    , $this->getTimestampAnulacao() );
                                    $obErro = $obTEmpenhoOrdemPagamentoAnulada->inclusao( $boTransacao );

                                    // Grava Anulações
                                    $obOPLA->setDado ( 'timestamp', $this->getTimestampAnulacao() );
                                    $obErro = $obOPLA->inclusao( $boTransacao );

                                    if ( $obErro->ocorreu() ) { break; }
                                }
                            }
                        }
                    } else {
                        $obErro->setDescricao( "Valor a anular excede o valor disponível!" );
                    }
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTEmpenhoOrdemPagamentoAnulada );

    return $obErro;
}

/**
* Altera os dados setados na tabela de Ordem de pagamento
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function alterar($boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoOrdemPagamento.class.php"        );
    $obTEmpenhoOrdemPagamento        = new TEmpenhoOrdemPagamento;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obTEmpenhoOrdemPagamento->setDado( "cod_ordem"     , $this->inCodigoOrdem    );
        $obTEmpenhoOrdemPagamento->setDado( "exercicio"     , $this->stExercicio      );
        $obTEmpenhoOrdemPagamento->setDado( "cod_entidade"  , $this->obROrcamentoEntidade->getCodigoEntidade() );
        $obTEmpenhoOrdemPagamento->setDado( "observacao"    , $this->stObservacao     );
        $obTEmpenhoOrdemPagamento->setDado( "dt_emissao"    , $this->dtDataEmissao    );
        $obTEmpenhoOrdemPagamento->setDado( "dt_vencimento" , $this->dtDataVencimento );
        $obErro = $obTEmpenhoOrdemPagamento->alteracao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTEmpenhoOrdemPagamento );

    return $obErro;
}

/**
* Exclui os dados setados na tabela de Ordem de pagamento
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function excluir($boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoOrdemPagamento.class.php"        );
    $obTEmpenhoOrdemPagamento        = new TEmpenhoOrdemPagamento;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
            $obTEmpenhoOrdemPagamento->setDado( "cod_ordem"     , $this->inCodigoOrdem    );
            $obTEmpenhoOrdemPagamento->setDado( "exercicio"     , $this->stExercicio      );
            $obTEmpenhoOrdemPagamento->setDado( "cod_entidade"  , $this->obROrcamentoEntidade->getCodigoEntidade() );
            $obTEmpenhoOrdemPagamento->setDado( "observacao"    , $this->stObservacao     );
            $obTEmpenhoOrdemPagamento->setDado( "dt_emissao"    , $this->dtDataEmissao    );
            $obTEmpenhoOrdemPagamento->setDado( "dt_vencimento" , $this->dtDataVencimento );
            $obErro = $obTEmpenhoOrdemPagamento->exclusao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTEmpenhoOrdemPagamento );

    return $obErro;
}

/**
* Lista as Ordens de Pagamento que podem ser anuladas
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function listarAnularOp(&$rsRecordSet, $boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoOrdemPagamento.class.php"        );
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoOrdemPagamentoLiquidacaoAnulada.class.php" );

    $obTEmpenhoOrdemPagamento        = new TEmpenhoOrdemPagamento;

    $stFiltro = "";

    if ($this->inCodigoOrdem) {
        $stFiltro .= " op.cod_ordem = ".$this->inCodigoOrdem." AND ";
    }
    if ($this->inCodigoOrdemInicial and $this->inCodigoOrdemFinal) {
        $stFiltro .= " op.cod_ordem between ".$this->inCodigoOrdemInicial." AND ".$this->inCodigoOrdemFinal." AND ";
    } elseif ($this->inCodigoOrdemInicial and !$this->inCodigoOrdemFinal) {
        $stFiltro .= " op.cod_ordem >= ".$this->inCodigoOrdemInicial." AND ";
    } elseif (!$this->inCodigoOrdemInicial and $this->inCodigoOrdemFinal) {
        $stFiltro .= " op.cod_ordem <= ".$this->inCodigoOrdemFinal." AND ";
    }

    if ( $this->obREmpenhoNotaLiquidacao->getCodNotaInicial() and $this->obREmpenhoNotaLiquidacao->getCodNotaFinal() ) {
        $obTEmpenhoOrdemPagamento->setDado( 'itensOP.cod_nota', $this->obREmpenhoNotaLiquidacao->getCodNotaInicial()." AND ".$this->obREmpenhoNotaLiquidacao->getCodNotaFinal() );
    } elseif ( $this->obREmpenhoNotaLiquidacao->getCodNotaInicial() and !$this->obREmpenhoNotaLiquidacao->getCodNotaFinal() ) {
        $obTEmpenhoOrdemPagamento->setDado( 'itensOP.cod_nota_inicial', $this->obREmpenhoNotaLiquidacao->getCodNotaInicial() );
    } elseif ( !$this->obREmpenhoNotaLiquidacao->getCodNotaInicial() and $this->obREmpenhoNotaLiquidacao->getCodNotaFinal() ) {
        $obTEmpenhoOrdemPagamento->setDado( 'itensOP.cod_nota_final', $this->obREmpenhoNotaLiquidacao->getCodNotaFinal() );
    }

    if ( $this->obREmpenhoEmpenho->getCodEmpenhoInicial() and $this->obREmpenhoEmpenho->getCodEmpenhoFinal() ) {
        $stFiltro .= " itensOP.cod_empenho >= " . $this->obREmpenhoEmpenho->getCodEmpenhoInicial() ." AND itensOP.cod_empenho <= " . $this->obREmpenhoEmpenho->getCodEmpenhoFinal() . " AND ";
    } elseif ( $this->obREmpenhoEmpenho->getCodEmpenhoInicial() ) {
         $stFiltro .= " itensOP.cod_empenho >= " . $this->obREmpenhoEmpenho->getCodEmpenhoInicial() . " AND ";
    } elseif ( $this->obREmpenhoEmpenho->getCodEmpenhoFinal() ) {
         $stFiltro .= " itensOP.cod_empenho_final <= " .  $this->obREmpenhoEmpenho->getCodEmpenhoFinal() . " AND ";
    }

    if ($this->stExercicio) {
        $stFiltro .= " op.exercicio = '".$this->stExercicio."' AND ";
    }

    if ( $this->getFornecedor() ) {
        $stFiltro .= " itensOP.cgm_beneficiario = '".$this->getFornecedor()."' AND ";
    }

    if ( $this->obREmpenhoEmpenho->getExercicio() ) {
        $obTEmpenhoOrdemPagamento->setDado( 'exercicio_empenho', $this->obREmpenhoEmpenho->getExercicio() );
        $stFiltro .= " itensOP.exercicio_empenho = '". $this->obREmpenhoEmpenho->getExercicio() . "' AND ";
    }

    if ( $this->obROrcamentoEntidade->getCodigoEntidade() ) {
        $obTEmpenhoOrdemPagamento->setDado( 'cod_entidade' , $this->obROrcamentoEntidade->getCodigoEntidade() );
    }

    if( $this->dtDataVencimento  )
        $stFiltro .= " op.dt_vencimento = TO_DATE('".$this->dtDataVencimento."', 'dd/mm/yyyy' ) AND ";

    if ($this->dtDataEmissaoInicial and $this->dtDataEmissaoFinal) {
        $stFiltro .= " op.dt_emissao between TO_DATE('".$this->dtDataEmissaoInicial."', 'dd/mm/yyyy' ) AND TO_DATE('".$this->dtDataEmissaoFinal."','dd/mm/yyyy') AND ";
    } elseif ($this->dtDataEmissaoInicial and !$this->dtDataEmissaoFinal) {
        $stFiltro .= " op.dt_emissao >= TO_DATE('".$this->dtDataEmissaoInicial."','dd/mm/yyyy') AND ";
    } elseif (!$this->dtDataEmissaoInicial and $this->dtDataEmissaoFinal) {
        $stFiltro .= " op.dt_emissao <= TO_DATE('".$this->dtDataEmissaoFinal.", 'dd/mm/yyyy') AND ";
    }

    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 )."\n";
    }

    $obErro = $obTEmpenhoOrdemPagamento->recuperaListaAnulacao( $rsRecordSet, $stFiltro, '', $boTransacao );

    return $obErro;
}

/**
* Lista as Ordens de Pagamento conforme o filtro setado
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function listar(&$rsRecordSet, $boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoOrdemPagamento.class.php"        );
    $obTEmpenhoOrdemPagamento        = new TEmpenhoOrdemPagamento;

    $stFiltro = "";

    if ($this->inCodigoOrdem) {
        $stFiltro .= " cod_ordem = ".$this->inCodigoOrdem." AND ";
    }
    if ($this->inCodigoOrdemInicial and $this->inCodigoOrdemFinal) {
        $stFiltro .= " cod_ordem between ".$this->inCodigoOrdemInicial." AND ".$this->inCodigoOrdemFinal." AND ";
    } elseif ($this->inCodigoOrdemInicial and !$this->inCodigoOrdemFinal) {
        $stFiltro .= " cod_ordem >= ".$this->inCodigoOrdemInicial." AND ";
    } elseif (!$this->inCodigoOrdemInicial and $this->inCodigoOrdemFinal) {
        $stFiltro .= " cod_ordem <= ".$this->inCodigoOrdemFinal." AND ";
    }

    if ( $this->obREmpenhoNotaLiquidacao->getCodNotaInicial() and $this->obREmpenhoNotaLiquidacao->getCodNotaFinal() ) {
        $obTEmpenhoOrdemPagamento->setDado( 'cod_nota', $this->obREmpenhoNotaLiquidacao->getCodNotaInicial()." AND ".$this->obREmpenhoNotaLiquidacao->getCodNotaFinal() );
    } elseif ( $this->obREmpenhoNotaLiquidacao->getCodNotaInicial() and !$this->obREmpenhoNotaLiquidacao->getCodNotaFinal() ) {
        $obTEmpenhoOrdemPagamento->setDado( 'cod_nota_inicial', $this->obREmpenhoNotaLiquidacao->getCodNotaInicial() );
    } elseif ( !$this->obREmpenhoNotaLiquidacao->getCodNotaInicial() and $this->obREmpenhoNotaLiquidacao->getCodNotaFinal() ) {
        $obTEmpenhoOrdemPagamento->setDado( 'cod_nota_final', $this->obREmpenhoNotaLiquidacao->getCodNotaFinal() );
    }

    if ( $this->obREmpenhoEmpenho->getCodEmpenhoInicial() and $this->obREmpenhoEmpenho->getCodEmpenhoFinal() ) {
        $obTEmpenhoOrdemPagamento->setDado( 'cod_empenho', $this->obREmpenhoEmpenho->getCodEmpenhoInicial()." AND ".$this->obREmpenhoEmpenho->getCodEmpenhoFinal() );
    } elseif ( $this->obREmpenhoEmpenho->getCodEmpenhoInicial() ) {
         $obTEmpenhoOrdemPagamento->setDado('cod_empenho_inicial', $this->obREmpenhoEmpenho->getCodEmpenhoInicial());
    } elseif ( $this->obREmpenhoEmpenho->getCodEmpenhoFinal() ) {
        $obTEmpenhoOrdemPagamento->setDado('cod_empenho_final', $this->obREmpenhoEmpenho->getCodEmpenhoFinal());
    }

    if ($this->stExercicio) {
        $obTEmpenhoOrdemPagamento->setDado('exercicio_op', $this->stExercicio );
        $stFiltro .= " exercicio = '".$this->stExercicio."' AND ";
    }

    if ( $this->getFornecedor() ) {
        $stFiltro .= " cgm_beneficiario = ".$this->getFornecedor()." AND ";
    }

    if ( $this->obREmpenhoEmpenho->getExercicio() ) {
        $obTEmpenhoOrdemPagamento->setDado('exercicio_empenho', $this->obREmpenhoEmpenho->getExercicio());
    }

    if ( $this->obROrcamentoEntidade->getCodigoEntidade() ) {
        $stFiltro .= " cod_entidade in (".$this->obROrcamentoEntidade->getCodigoEntidade().") AND ";
        $obTEmpenhoOrdemPagamento->setDado('cod_entidade', $this->obROrcamentoEntidade->getCodigoEntidade() );
    }

    if ( $this->obREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->getCodRecurso() ) {
        $stFiltro .= " cod_recurso = ".$this->obREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->getCodRecurso()." AND ";
    }

    if ( $this->obREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->getDestinacaoRecurso() ) {
        $stFiltro .= " masc_recurso_red like '".$this->obREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->getDestinacaoRecurso()."%' AND ";
    }

    if ( $this->obREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->getCodDetalhamento() ) {
        $stFiltro .= " cod_detalhamento = ".$this->obREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->getCodDetalhamento()." AND ";
    }

    if( $this->dtDataVencimento  )
        $stFiltro .= " TO_DATE(dt_vencimento,'dd/mm/yyyy') = TO_DATE('".$this->dtDataVencimento."', 'dd/mm/yyyy' ) AND ";

    if ($this->dtDataEmissaoInicial and $this->dtDataEmissaoFinal) {
        $stFiltro .= " TO_DATE(dt_emissao,'dd/mm/yyyy') between TO_DATE('".$this->dtDataEmissaoInicial."', 'dd/mm/yyyy' ) AND TO_DATE('".$this->dtDataEmissaoFinal."','dd/mm/yyyy') AND ";
    } elseif ($this->dtDataEmissaoInicial and !$this->dtDataEmissaoFinal) {
        $stFiltro .= " TO_DATE(dt_emissao,'dd/mm/yyyy') >= TO_DATE('".$this->dtDataEmissaoInicial."','dd/mm/yyyy') AND ";
    } elseif (!$this->dtDataEmissaoInicial and $this->dtDataEmissaoFinal) {
        $stFiltro .= " TO_DATE(dt_emissao,'dd/mm/yyyy') <= TO_DATE('".$this->dtDataEmissaoFinal.", 'dd/mm/yyyy') AND ";
    }

    if ($this->boListarNaoAnulada) {
        $stFiltro .= " situacao <> 'Anulada' AND ";
    }
    if ($this->boListarAnulada) {
        $stFiltro .= " situacao = 'Anulada' AND ";
    }
    if ($this->boListarNaoPaga) {
        $stFiltro .= " situacao = 'A Pagar' AND ";
    }
    if ($this->boListarPaga) {
        $stFiltro .= " situacao = 'Paga' AND ";
     }

    if ($stFiltro) {
        $stFiltro = " AND ".substr( $stFiltro, 0, strlen($stFiltro) - 4 )."\n";
    }

    $stOrder = " ORDER BY cod_entidade,cod_ordem ";
    $obErro = $obTEmpenhoOrdemPagamento->recuperaRelacionamento( $rsRecordSet, $stFiltro.$stGroupBy, $stOrder, $boTransacao );

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
function listarMaiorData(&$rsRecordSet, $stOrder = "", $boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO     ."TEmpenhoOrdemPagamento.class.php"                    );
    $obTEmpenhoOrdemPagamento                    =  new TEmpenhoOrdemPagamento;

    if( $this->obROrcamentoEntidade->getCodigoEntidade() )
        $stFiltro  .= " AND cod_entidade IN (".$this->obROrcamentoEntidade->getCodigoEntidade()." ) ";

    if( $this->stExercicio )
        $stFiltro .= " AND exercicio = '".$this->stExercicio."' ";

    $obTEmpenhoOrdemPagamento->setDado('stExercicio',$this->stExercicio);
    $obTEmpenhoOrdemPagamento->setDado('stDataLiquidacao',$this->obREmpenhoNotaLiquidacao->getDtLiquidacao() );

    $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 4, strlen($stFiltro)) : "";
    $obErro = $obTEmpenhoOrdemPagamento->recuperaMaiorDataOrdem( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Verifica a data da liquidação mais nova
    * @access Private
    * @param  Array de Liquidações da OP
    * @return String
*/
function retornaMaiorDataLiquidacao($arLiquidacao)
{
    $dtLiquidacaoMaisNova = false;

    foreach ($arLiquidacao as $novaNota => $arNotaLiquidacao) {

        if (!$dtLiquidacaoMaisNova) {
            $arNotaLiquidacao["dt_nota"];
        }

        if ( SistemaLegado::comparaDatas( $arNotaLiquidacao["dt_nota"], $dtLiquidacaoMaisNova) ) {
            $dtLiquidacaoMaisNova = $arNotaLiquidacao["dt_nota"];
        }
    }

    return $dtLiquidacaoMaisNova;
}

/**
* Lista as Ordens de Pagamento conforme o filtro setado
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function listarReemitir(&$rsRecordSet, $boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoOrdemPagamento.class.php"        );
    $obTEmpenhoOrdemPagamento        = new TEmpenhoOrdemPagamento;

    $stFiltro = "";
    if ( $this->getFornecedor() ) {
        $stFiltro .= " AND pe.cgm_beneficiario = ".$this->getFornecedor()." ";
    }
    if ($this->inCodigoOrdemInicial and $this->inCodigoOrdemFinal) {
        $stFiltro .= " AND op.cod_ordem between '".$this->inCodigoOrdemInicial."' AND '".$this->inCodigoOrdemFinal."' ";
    } elseif ($this->inCodigoOrdemInicial and !$this->inCodigoOrdemFinal) {
        $stFiltro .= " AND op.cod_ordem >= '".$this->inCodigoOrdemInicial."' ";
    } elseif (!$this->inCodigoOrdemInicial and $this->inCodigoOrdemFinal) {
        $stFiltro .= " AND op.cod_ordem <= '".$this->inCodigoOrdemFinal."' ";
    }
    if ( $this->obREmpenhoEmpenho->getCodEmpenhoInicial() ) {
        $stFiltro .= " AND nl.cod_empenho >= '".$this->obREmpenhoEmpenho->getCodEmpenhoInicial()."' ";
    }
    if ( $this->obREmpenhoEmpenho->getCodEmpenhoFinal() ) {
        $stFiltro .= " AND nl.cod_empenho <= '".$this->obREmpenhoEmpenho->getCodEmpenhoFinal()."' ";
    }
    if ( $this->obREmpenhoEmpenho->getExercicio() ) {
        $stFiltro .= " AND ee.exercicio = '".$this->obREmpenhoEmpenho->getExercicio()."' ";
    }
    if ( $this->obROrcamentoEntidade->getCodigoEntidade() ) {
        $stFiltro .= " AND op.cod_entidade in (".$this->obROrcamentoEntidade->getCodigoEntidade().") 
                       AND TO_CHAR(oa.timestamp,'yyyy') = '".Sessao::getExercicio()."'
                    ";
    }
    if ($this->dtDataVencimento) {
        $stFiltro .= " AND empenho.ordem_pagamento.dt_vencimento = TO_DATE('".$this->dtDataVencimento."','dd/mm/yyyy') ";
    }
    if ($this->dtDataEmissaoInicial and $this->dtDataEmissaoFinal) {
        $stFiltro .= " AND oa.timestamp between TO_DATE('".$this->dtDataEmissaoInicial."', 'dd/mm/yyyy' ) AND TO_DATE('".$this->dtDataEmissaoFinal."','dd/mm/yyyy') ";
    } elseif ($this->dtDataEmissaoInicial and !$this->dtDataEmissaoFinal) {
        $stFiltro .= " AND oa.timestamp >= TO_DATE('".$this->dtDataEmissaoInicial."','dd/mm/yyyy') ";
    } elseif (!$this->dtDataEmissaoInicial and $this->dtDataEmissaoFinal) {
        $stFiltro .= " AND oa.timestamp <= TO_DATE('".$this->dtDataEmissaoFinal.", 'dd/mm/yyyy') ";
    }

    $stOrder = " ORDER BY op.cod_entidade,op.cod_ordem,op.exercicio,dt_anulado,beneficiario,valor ";

    $obErro = $obTEmpenhoOrdemPagamento->recuperaRelacionamentoReemitir( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
* Lista as Ordens de Pagamento Disponiveis para Estorno
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function listarDadosEstornoPagamento(&$rsRecordSet, $boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoOrdemPagamento.class.php"        );
    $obTEmpenhoOrdemPagamento        = new TEmpenhoOrdemPagamento;

    $stFiltro = " \n where coalesce(pag.vl_pago,0.00)-coalesce(itens.vl_prestado,0.00) > 0 \n";

    if ( $this->getCodigoOrdem() ) {
        $stFiltro .= " and op.cod_ordem = '".$this->getCodigoOrdem()."' \n";
    }
    if ( $this->getCodigoEmpenho() ) {
        $stFiltro .= " and em.cod_empenho = '".$this->getCodigoEmpenho()."' \n";
    }
    if ($this->stExercicio) {
        $stFiltro .= " and op.exercicio = '".$this->stExercicio."' \n";
    }
    if ($this->dtDataVencimento) {
        $stFiltro .= " and op.dt_vencimento = TO_DATE('".$this->dtDataVencimento."','dd/mm/yyyy') \n";
    }
    if ( $this->obREmpenhoEmpenho->getExercicio() ) {
        $stFiltro .= " and em.exercicio = '".$this->obREmpenhoEmpenho->getExercicio()."' \n";
    }
    if ( $this->obROrcamentoEntidade->getCodigoEntidade() ) {
        $stFiltro .= " and op.cod_entidade = ".$this->obROrcamentoEntidade->getCodigoEntidade()." \n";
    }
    if ( $this->getNotaLiquidacao() ) {
        $stFiltro .= " and nl.cod_nota = ".$this->getNotaLiquidacao()." \n";
    }
    if ( $this->getFornecedor() ) {
        $stFiltro .= " and pe.cgm_beneficiario = ".$this->getFornecedor()." \n";
    }

    $obTEmpenhoOrdemPagamento->setDado("stFiltro" , " substring(cast(plnlp.timestamp as varchar),1,4) = '".Sessao::getExercicio()."' ");

    $obErro = $obTEmpenhoOrdemPagamento->recuperaListaEstorno( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}
/**
* Lista as Ordens de Pagamento Utilizadas no Pagamento
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function listarDadosPagamento(&$rsRecordSet, $boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoOrdemPagamento.class.php"        );
    $obTEmpenhoOrdemPagamento        = new TEmpenhoOrdemPagamento;

    $stFiltro = "";
    if ( $this->getCodigoOrdem() ) {
        $stFiltro .= " EOP.cod_ordem = '".$this->getCodigoOrdem()."' AND ";
    }
    if ( $this->getCodigoEmpenho() ) {
        $stFiltro .= " EMP.cod_empenho = '".$this->getCodigoEmpenho()."' AND ";
    }
    if ($this->stExercicio) {
        $stFiltro .= " eop.exercicio = '".$this->stExercicio."' AND ";
    }
    if ($this->dtDataVencimento) {
        $stFiltro .= " eop.dt_vencimento = TO_DATE('".$this->dtDataVencimento."','dd/mm/yyyy') AND ";
    }
    if ( $this->obREmpenhoEmpenho->getExercicio() ) {
        $stFiltro .= " EMP.exercicio_empenho = '".$this->obREmpenhoEmpenho->getExercicio()."' AND ";
    }
    if ( $this->obROrcamentoEntidade->getCodigoEntidade() ) {
        $stFiltro .= " eop.cod_entidade in (".$this->obROrcamentoEntidade->getCodigoEntidade().") AND ";
    }
    if ( $this->getNotaLiquidacao() ) {
        $stFiltro .= " EMP.cod_nota = ".$this->getNotaLiquidacao()." AND ";
    }
    if ( $this->getFornecedor() ) {
        $stFiltro .= " EMP.cgm_beneficiario = ".$this->getFornecedor()." AND ";
    }
    if ($this->boListarPaga) {
        $stFiltro .= " eopa.cod_ordem is null AND ";
        $stFiltro .= " plnlp.cod_ordem is not null AND ";
        $obTEmpenhoOrdemPagamento->setDado("stFiltro" , " and substring(plnlp.timestamp,1,4)='".Sessao::getExercicio()."' ");
    }
    if ($this->boListarNaoPaga) {
       $stFiltro .= " eopa.cod_ordem is null AND ";
       $stFiltro .= " plnlp.cod_ordem is null AND ";
    }

    $stFiltro .= " eop.exercicio = '".Sessao::getExercicio()."' AND ";

    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }

    $stOrder  = " GROUP BY eop.exercicio,eop.dt_vencimento,eop.dt_emissao,emp.exercicio_empenho,eop.COD_ORDEM,eop.COD_ENTIDADE,EMP.CGM_BENEFICIARIO,CGME.NOM_CGM,EMP.NOM_CGM,VALOR_PAGAMENTO,EMP.implantado   \n";
    $stOrder .= " ORDER BY eop.cod_ordem   ";

    $obErro = $obTEmpenhoOrdemPagamento->recuperaDadosPagamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

function listarDadosPagamentoBordero(&$rsRecordSet, $boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoOrdemPagamento.class.php"        );
    $obTEmpenhoOrdemPagamento        = new TEmpenhoOrdemPagamento;

    $stFiltro = "";
    if ( $this->getCodigoOrdem() ) {
        $obTEmpenhoOrdemPagamento->setDado('cod_ordem', $this->getCodigoOrdem());
    }
    if ($this->stExercicio) {
        $obTEmpenhoOrdemPagamento->setDado('exercicio', $this->stExercicio );
    }
    if ( $this->obROrcamentoEntidade->getCodigoEntidade() ) {
        $obTEmpenhoOrdemPagamento->setDado('cod_entidade', $this->obROrcamentoEntidade->getCodigoEntidade() );
    }

    $obErro = $obTEmpenhoOrdemPagamento->recuperaDadosPagamentoBordero( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

function listarDadosPagamentoBorderoContaRecurso(&$rsRecordSet, $boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoOrdemPagamento.class.php"        );
    $obTEmpenhoOrdemPagamento        = new TEmpenhoOrdemPagamento;

    $stFiltro = "";
    if ( $this->getCodigoOrdem() ) {
        $obTEmpenhoOrdemPagamento->setDado('cod_ordem', $this->getCodigoOrdem());
    }
    if ($this->stExercicio) {
        $obTEmpenhoOrdemPagamento->setDado('exercicio', $this->stExercicio );
    }
    if ( $this->obROrcamentoEntidade->getCodigoEntidade() ) {
        $obTEmpenhoOrdemPagamento->setDado('cod_entidade', $this->obROrcamentoEntidade->getCodigoEntidade() );
    }
    if ( $this->obREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->getCodRecurso() ){
        $obTEmpenhoOrdemPagamento->setDado('cod_recurso', $this->obREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->getCodRecurso() );   
    }
    
    $obErro = $obTEmpenhoOrdemPagamento->recuperaDadosPagamentoBorderoContaRecurso( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}


/**
* Lista as Ordens de Pagamento A Pagar
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function listarOrdensPagamentoAPagar(&$rsRecordSet, $boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoOrdemPagamento.class.php"        );
    $obTEmpenhoOrdemPagamento        = new TEmpenhoOrdemPagamento;

    // LISTAR SOMENTE OPs A PAGAR !
    $stFiltro = " WHERE  coalesce(top.total_op,0.00) > coalesce(plnlp.vl_pago,0.00) \n";

    if ( $this->getCodigoOrdem() ) {
        $stFiltro .= " AND op.cod_ordem = '".$this->getCodigoOrdem()."' ";
    }
    if ( $this->getCodigoEmpenho() ) {
        $obTEmpenhoOrdemPagamento->setDado("cod_empenho",$this->getCodigoEmpenho());
    }
    if ($this->stExercicio) {
        $stFiltro .= " AND op.exercicio = '".$this->stExercicio."' ";
    }
    if ($this->dtDataVencimento) {
        $stFiltro .= " AND op.dt_vencimento = TO_DATE('".$this->dtDataVencimento."','dd/mm/yyyy') ";
    }
    if ( $this->obREmpenhoEmpenho->getExercicio() ) {
        $obTEmpenhoOrdemPagamento->setDado("exercicio_empenho",$this->obREmpenhoEmpenho->getExercicio());
    }
    if ( $this->obROrcamentoEntidade->getCodigoEntidade() ) {
        $stFiltro .= " AND op.cod_entidade in (".$this->obROrcamentoEntidade->getCodigoEntidade().") ";
    }
    if ( $this->getNotaLiquidacao() ) {
        $obTEmpenhoOrdemPagamento->setDado("cod_nota",$this->getNotaLiquidacao());
    }
    if ( $this->getFornecedor() ) {
        $obTEmpenhoOrdemPagamento->setDado("cgm_beneficiario",$this->getFornecedor());
    }

    $stOrder .= " ORDER BY op.cod_ordem   ";

    $obErro = $obTEmpenhoOrdemPagamento->recuperaListaPagamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}
/**
* Lista itens de uma Ordem de Pagamento
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function listarItensPagamento(&$rsRecordSet, $boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoOrdemPagamento.class.php"        );
    $obTEmpenhoOrdemPagamento        = new TEmpenhoOrdemPagamento;

    $stFiltro = "";
    if ($this->inCodigoEmpenho) {
        $stFiltro .= " AND eem.cod_empenho = ".$this->inCodigoEmpenho;
    }
    if ($this->inCodigoOrdem) {
        $stFiltro .= " AND epl.cod_ordem = ".$this->inCodigoOrdem;
    }
    if ($this->stExercicio) {
        $stFiltro .= " AND epl.exercicio = '".$this->stExercicio."'";
    }
    if ( $this->obROrcamentoEntidade->getCodigoEntidade() ) {
        $stFiltro .= " AND epl.cod_entidade = (".$this->obROrcamentoEntidade->getCodigoEntidade().")";
    }

    $stOrder  = "\ngroup by \n";
    $stOrder .= "    eem.cod_empenho, \n";
    $stOrder .= "    eem.exercicio, \n";
    $stOrder .= "    to_char(eem.dt_empenho,'dd/mm/yyyy'), \n";
    $stOrder .= "    enl.cod_nota, \n";
    $stOrder .= "    enl.exercicio, \n";
    $stOrder .= "    to_char(enl.dt_liquidacao,'dd/mm/yyyy'), \n";
    $stOrder .= "    enl.cod_entidade, \n";
    $stOrder .= "    epl.vl_pagamento, \n";
    $stOrder .= "    opla.vl_anulado, \n";
    $stOrder .= "    ode.cod_recurso, \n";

    $stOrder .= "    rpe.recurso, \n";
    $stOrder .= "    ece.conta_contrapartida \n";
    $stOrder .= " \n";
    $stOrder .= " ORDER BY enl.cod_nota \n";

    $obTEmpenhoOrdemPagamento->setDado( 'filtro' , $stFiltro);
    $obTEmpenhoOrdemPagamento->setDado( 'groupby', $stOrder );

    $obErro = $obTEmpenhoOrdemPagamento->recuperaItensPagamento( $rsRecordSet, '', '', $boTransacao );
//$obTEmpenhoOrdemPagamento->debug();
    return $obErro;
}
/**
* Lista itens de uma Ordem de Pagamento a serem estornados
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function listarItensEstorno(&$rsRecordSet, $boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoOrdemPagamento.class.php"        );
    $obTEmpenhoOrdemPagamento        = new TEmpenhoOrdemPagamento;

    $stFiltro = "";
    if ($this->inCodigoOrdem) {
        $stFiltro .= " AND epl.cod_ordem = ".$this->inCodigoOrdem;
    }
    if ($this->stExercicio) {
        $stFiltro .= " AND epl.exercicio = '".$this->stExercicio."'";
    }
    if ( $this->obROrcamentoEntidade->getCodigoEntidade() ) {
        $stFiltro .= " AND epl.cod_entidade = (".$this->obROrcamentoEntidade->getCodigoEntidade().")";
    }

    $stOrder  = "group by \n";
    $stOrder .= "    eem.cod_empenho, \n";
    $stOrder .= "    eem.exercicio, \n";
    $stOrder .= "    to_char(eem.dt_empenho,'dd/mm/yyyy'), \n";
    $stOrder .= "    enl.cod_nota, \n";
    $stOrder .= "    enl.exercicio, \n";
    $stOrder .= "    to_char(enl.dt_liquidacao,'dd/mm/yyyy'), \n";
    $stOrder .= "    enl.cod_entidade, \n";
    $stOrder .= "    epl.vl_pagamento, \n";
    $stOrder .= "    itens.vl_prestado, \n";
    $stOrder .= "    opla.vl_anulado, \n";
    $stOrder .= "    pag.timestamp, \n";
    $stOrder .= "    ode.cod_recurso, \n";
    $stOrder .= "    rpe.recurso, \n";
    $stOrder .= "    pag.exercicio_plano, \n";
    $stOrder .= "    pag.cod_plano      \n";
    $stOrder .= " \n";
    $stOrder .= " ORDER BY enl.cod_nota \n";

    $obTEmpenhoOrdemPagamento->setDado( 'filtro' , $stFiltro);
    $obTEmpenhoOrdemPagamento->setDado( 'groupby', $stOrder );

    $obErro = $obTEmpenhoOrdemPagamento->recuperaItensEstorno( $rsRecordSet, '', '', $boTransacao );

    return $obErro;
}

/**
* Lista as Notas de uma Ordens de Pagamento
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function listarItem(&$rsRecordSet, $boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoPagamentoLiquidacao.class.php"   );
    $obTEmpenhoPagamentoLiquidacao   = new TEmpenhoPagamentoLiquidacao;

    $stFiltro = "";
    if ($this->inCodigoOrdem) {
        $stFiltro .= " eop.cod_ordem = ".$this->inCodigoOrdem." AND ";
    }
    if ($this->stExercicio) {
        $stFiltro .= " eop.exercicio = '".$this->stExercicio."' AND ";
    }
    if ( $this->obROrcamentoEntidade->getCodigoEntidade() ) {
        $stFiltro .= " eop.cod_entidade = (".$this->obROrcamentoEntidade->getCodigoEntidade().") AND ";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }
    $stOrder = " ORDER BY cod_ordem ";
    $obErro = $obTEmpenhoPagamentoLiquidacao->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Executa SUM em pagamento_liquidacao
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarValorPago(&$rsRecordSet, $boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoPagamentoLiquidacao.class.php"   );
    $obTEmpenhoPagamentoLiquidacao   = new TEmpenhoPagamentoLiquidacao;

    if(empty($stFiltro))
        $stFiltro = null;

    if ($this->inCodigoOrdem) {
        $stFiltro .= "  AND pl.cod_ordem = '" . $this->inCodigoOrdem . "'";
    }
    if ($this->stExercicio) {
        $stFiltro .= " AND e.exercicio = '" . $this->stExercicio . "'";
    }
    if ( $this->obROrcamentoEntidade->getCodigoEntidade() ) {
        $stFiltro .= " AND e.cod_entidade = (".$this->obROrcamentoEntidade->getCodigoEntidade().")";
    }
    if ( $this->obREmpenhoEmpenho->getCodEmpenho() ) {
        $stFiltro .= " AND e.cod_empenho = ".$this->obREmpenhoEmpenho->getCodEmpenho();
    }
    $obErro = $obTEmpenhoPagamentoLiquidacao->recuperaValorPago( $rsRecordSet, $stFiltro, $boTransacao );

    return $obErro;
}

/**
* Recupera do banco de dados os dados da Ordem de Pagamento selecionada
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function consultar($boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoOrdemPagamento.class.php"        );
    $obTEmpenhoOrdemPagamento        = new TEmpenhoOrdemPagamento;
    if(empty($stFiltro))
        $stFiltro = "";
    if(empty($stGroupBy))
        $stGroupBy = "";

    if ($this->inCodigoOrdem) {
        $stFiltro .= " cod_ordem = ".$this->inCodigoOrdem." AND ";
        $obTEmpenhoOrdemPagamento->setDado( 'cod_ordem', $this->inCodigoOrdem );
    }
    if ($this->stExercicio) {
        $stFiltro .= " exercicio = '".$this->stExercicio."' AND ";
        $obTEmpenhoOrdemPagamento->setDado( 'exercicio', $this->stExercicio );
    }
    if ( $this->obROrcamentoEntidade->getCodigoEntidade() ) {
        $stFiltro .= " cod_entidade = ".$this->obROrcamentoEntidade->getCodigoEntidade()." AND ";
        $obTEmpenhoOrdemPagamento->setDado( 'cod_entidade', $this->obROrcamentoEntidade->getCodigoEntidade() );
    }
    if ( $this->obREmpenhoEmpenho->getExercicio() ) {
        $obTEmpenhoOrdemPagamento->setDado( 'exercicio_empenho', $this->obREmpenhoEmpenho->getExercicio() );
    }
    if ($stFiltro) {
        $stFiltro = " AND ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }

    $obErro = $obTEmpenhoOrdemPagamento->recuperaRelacionamento( $rsRecordSet, $stFiltro.$stGroupBy, '', $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $obTEmpenhoOrdemPagamento->recuperaUltimoEstorno( $rsUltimoEstorno, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            if ( !$rsUltimoEstorno->eof() ) {
                $this->dtDataEstorno = $rsUltimoEstorno->getCampo( 'dt_estorno' );
            }
            $obErro = $obTEmpenhoOrdemPagamento->recuperaUltimoPagamento( $rsUltimoPagamento, $boTransacao );
            if ( !$obErro->ocorreu() ) {
                if ( !$rsUltimoPagamento->eof() ) {
                    $this->dtDataPagamento = $rsUltimoPagamento->getCampo( 'dt_pagamento' );
                }
                $obErro = $obTEmpenhoOrdemPagamento->recuperaRetencoes( $rsRetencoes, $boTransacao );
                if (!$obErro->ocorreu() && !$rsRetencoes->eof() ) {
                    $this->setRetencao  ( true );
                    $this->setRetencoes ( $rsRetencoes->getElementos() );
                    $this->nuTotalRetencoes = 0.00;
                    while (!$rsRetencoes->EOF()) {
                        $this->nuTotalRetencoes = bcadd($this->nuTotalRetencoes,$rsRetencoes->getCampo('vl_retencao'),2);
                        $rsRetencoes->proximo();
                    }
                }
            }
        }

        if (!$obErro->ocorreu()) {
            // Verifica se a OP é de Adiantamentos/Subvenções
            $obErro = $obTEmpenhoOrdemPagamento->verificaAdiantamento( $rsAdiantamento, $boTransacao );
            if (!$obErro->ocorreu() && !$rsAdiantamento->eof() ) {
                if($rsAdiantamento->getCampo('adiantamento') == 't')
                    $this->setAdiantamento( true );
            }
        }
    }

    $rsRecordSet->addFormatacao( 'vl_nota', 'NUMERIC_BR' );

    if ( !$obErro->ocorreu() and !$rsRecordSet->eof() ) {
        $this->inCodigoOrdem    = $rsRecordSet->getCampo( "cod_ordem"       );
        $this->stExercicio      = $rsRecordSet->getCampo( "exercicio"       );
        $this->stObservacao     = $rsRecordSet->getCampo( "observacao"      );
        $this->dtDataEmissao    = $rsRecordSet->getCampo( "dt_emissao"      );
        $this->dtDataVencimento = $rsRecordSet->getCampo( "dt_vencimento"   );
        $this->dtDataAnulacao   = $rsRecordSet->getCampo( "dt_anulacao"     );
        $this->stSituacao       = $rsRecordSet->getCampo( "situacao"        );
        $this->stPagamentoEstornado = $rsRecordSet->getCampo( "pagamento_estornado" );
        $this->flValorNota      = $rsRecordSet->getCampo( "vl_nota"         );
        $this->flValorNotaOriginal  = $rsRecordSet->getCampo( "vl_nota_original" );
        $this->flValorNotaAnulacoes = $rsRecordSet->getCampo( "vl_nota_anulacoes");
        $this->flValorPagamento = $rsRecordSet->getCampo( "valor_pagamento" );

        if (!$this->getEstorno()) {
            $obErro = $this->listarItensPagamento ( $rsListaPagamento, $boTransacao );
            $stCampo = "vl_pagamento";
        } else {
            $obErro = $this->listarItensEstorno   ( $rsListaPagamento, $boTransacao );
            $stCampo = "vl_pago";
        }

        if ( !$obErro->ocorreu() ) {

            while ( !$rsListaPagamento->eof() ) {
                $this->addNotaLiquidacao();
                $this->roUltimaNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade( $rsListaPagamento->getCampo( "cod_entidade" ) );
                $this->roUltimaNotaLiquidacao->setCodNota   ( $rsListaPagamento->getCampo( "cod_nota" ) );
                $this->roUltimaNotaLiquidacao->setExercicio ( $rsListaPagamento->getCampo( "ex_nota" ) );
                $this->roUltimaNotaLiquidacao->setCodOrdem  ( $this->inCodigoOrdem );
                $this->roUltimaNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->setCodRecurso( $rsListaPagamento->getCampo( "cod_recurso" ) );
                $this->roUltimaNotaLiquidacao->setValorTotal( $rsListaPagamento->getCampo( $stCampo ) );
                $this->roUltimaNotaLiquidacao->setTimestamp ( $rsListaPagamento->getCampo( "timestamp"    ) );

                $obErro = $this->roUltimaNotaLiquidacao->consultar( $boTransacao );
                if ( $obErro->ocorreu() ) {
                    break;
                } else {
                    $this->roUltimaNotaLiquidacao->buscaValorAPagar( $boTransacao );
                }

                $rsListaPagamento->proximo();
            }
        }
    }

    return $obErro;
}

/**
    * Método para Consultar o Saldo do Pagamento
    * @access public
    * @param Object $rsRecordSet
    * @param String $stOrder
    * @param Object $boTransacao
    * @return Object $obErro
*/
function consultarSaldoPagamento($boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO."FEmpenhoSaldoPagamento.class.php" );
    $obFEmpenhoSaldoPagamento        = new FEmpenhoSaldoPagamento;

    $obFEmpenhoSaldoPagamento->setDado( "exercicio"   , $this->stExercicio );
    $obFEmpenhoSaldoPagamento->setDado( "cod_entidade", $this->obROrcamentoEntidade->getCodigoEntidade() );
    $obFEmpenhoSaldoPagamento->setDado( "cod_ordem"   , $this->inCodigoOrdem );
    $obErro = $obFEmpenhoSaldoPagamento->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    $this->setSaldoPagamento ( $rsRecordSet->getCampo("saldo_pagamento") );

    return $obErro;
}

/**
    * Método para Consultar o Valor a pagar
    * @access public
    * @param Object $rsRecordSet
    * @param String $stOrder
    * @param Object $boTransacao
    * @return Object $obErro
*/
function consultarValorAPagar($boTransacao = "")
{
    if (!$this->getEstorno()) {
        $obErro = $this->listarItensPagamento( $rsRecordSet, $boTransacao );
        $stCampo = "vl_pagamento";
    } else {
        $obErro = $this->listarItensEstorno  ( $rsRecordSet, $boTransacao );
        $stCampo = "vl_pago";
    }
    
    $nuVlAPagar = 0;
    $nuVlPago   = 0;
    while ( !$rsRecordSet->eof() ) {
        $nuVlAPagar += $rsRecordSet->getCampo("vl_pagamento");
        $nuVlPago   += $rsRecordSet->getCampo("vl_pago");
        $rsRecordSet->proximo();
    }

    $this->setValorAPagar ( $nuVlAPagar );
    $this->setValorPago   ( $nuVlPago   );

    return $obErro;
}

}
