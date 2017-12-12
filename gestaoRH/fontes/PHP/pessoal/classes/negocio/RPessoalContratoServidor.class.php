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
    * Classe de Regra de Negócio Pessoal Contrato Servidor
    * Data de Criação   : 20/12/2004

    * @author Analista: Leandro Oliveira.
    * @author Desenvolvedor: Rafael Almeida

    * @package URBEM
    * @subpackage Regra

      $Revision: 30772 $
      $Name$
      $Author: alex $
      $Date: 2008-03-27 10:09:14 -0300 (Qui, 27 Mar 2008) $

      Caso de uso: uc-04.04.07

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_NORMAS_NEGOCIO."RNorma.class.php"                                       );
include_once ( CAM_GA_ORGAN_NEGOCIO."ROrganogramaOrgao.class.php"                                );
include_once ( CAM_GA_ORGAN_NEGOCIO."ROrganogramaLocal.class.php"                                );
include_once ( CAM_GT_MON_NEGOCIO."RMONBanco.class.php"                                  );
include_once ( CAM_GT_MON_NEGOCIO."RMONAgencia.class.php"                                );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalTipoPagamento.class.php"                            );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalFormaPagamento.class.php"                           );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalTipoSalario.class.php"                              );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalTipoAdmissao.class.php"                             );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalVinculoEmpregaticio.class.php"                      );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalContrato.class.php"                                 );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoSindicato.class.php"                         );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalCargo.class.php"                                    );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalRegime.class.php"                                   );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalCategoria.class.php"                                );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalOcorrencia.class.php"                               );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPrevidencia.class.php"                       );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalGradeHorario.class.php"                             );
//Atributos Dinâmicos
include_once ( CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php"                          );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalAtributoContratoServidorValor.class.php" );

/**
    * Classe de Regra de Negócio Pessoal Contrato Servidor
    * Data de Criação   : 20/12/2004

    * @author Analista: Leandro Oliveira.
    * @author Desenvolvedor: Rafael Almeida

    * @package URBEM
    * @subpackage Regra
*/

class RPessoalContratoServidor extends RPessoalContrato
{
/**
   * @access Private
   * @var Object
*/
var $obTransacao;
/**
   * @access Private
   * @var Object
*/
var $obRNorma;
/**
   * @access Private
   * @var Object
*/
var $obROrganogramaOrgao;
/**
   * @access Private
   * @var Object
*/
var $obROrganogramaLocal;
/**
   * @access Private
   * @var Object
*/
var $obRPessoalTipoPagamento;
/**
   * @access Private
   * @var Object
*/
var $obRMonetarioBancoFGTS;
/**
   * @access Private
   * @var Object
*/
var $obRMonetarioAgenciaFGTS;
/**
   * @access Private
   * @var Object
*/
var $obRMonetarioBancoSalario;
/**
   * @access Private
   * @var Object
*/
var $obRMonetarioAgenciaSalario;
/**
   * @access Private
   * @var Object
*/
var $obRPessoalFormaPagamento;
/**
   * @access Private
   * @var Object
*/
var $obRPessoalTipoSalario;
/**
   * @access Private
   * @var Object
*/
var $obRPessoalTipoAdmissao;
/**
   * @access Private
   * @var Object
*/
var $obRPessoalVinculoEmpregaticio;
/**
   * @access Private
   * @var Object
*/
var $obRFolhaPagamentoSindicato;
/**
   * @access Private
   * @var Object
*/
var $obRPessoalCargo;
/**
   * @access Private
   * @var Object
*/
var $obRPessoalCargoFuncao;
/**
   * @access Private
   * @var Object
*/
var $obRPessoalRegime;
/**
   * @access Private
   * @var Object
*/
var $obRPessoalRegimeFuncao;
/**
   * @access Private
   * @var Object
*/
var $obRPessoalCategoria;
/**
   * @access Private
   * @var Object
*/
var $obRFolhaPagamentoPrevidencia;
/**
   * @access Private
   * @var Object
*/
var $obRPessoalOcorrencia;
/**
   * @access Private
   * @var Object
*/
var $obRPessoalGradeHorario;
/**
   * @access Private
   * @var Integer
*/
var $inCodContratoServidor;
/**
   * @access Private
   * @var String
*/
var $stContaCorrenteFgts;
/**
   * @access Private
   * @var String
*/
var $stNumClassificacao;
/**
   * @access Private
   * @var String
*/
var $stContaCorrenteSalario;
/**
   * @access Private
   * @var Boolean
*/
var $boFuncionarioCedido;
/**
   * @access Private
   * @var Numeric
*/
var $nuCargaHoraria;
/**
   * @access Private
   * @var Integer
*/
var $inNroCartaoPonto;
/**
   * @access Private
   * @var Integer
*/
var $inCodConselho;
/**
   * @access Private
   * @var Integer
*/
var $inNroConselho;
/**
   * @access Private
   * @var Boolean
*/
var $boAtivo;
/**
   * @access Private
   * @var Date
*/
var $dtNomeacao;
/**
   * @access Private
   * @var Date
*/
var $dtAlteracaoFuncao;
/**
   * @access Private
   * @var Date
*/
var $dtAtiInativacao;
/**
   * @access Private
   * @var Date
*/
var $dtAdmissao;
/**
   * @access Private
   * @var Date
*/
var $dtDataBase;
/**
   * @access Private
   * @var Date
*/
var $dtPosse;
/**
   * @access Private
   * @var Date
*/
var $dtDemissao;
/**
   * @access Private
   * @var String
*/
var $stCausaDemissao;
/**
   * @access Private
   * @var Date
*/
var $dtOpcaoFgts;
/**
   * @access Private
   * @var Numeric
*/
var $nuPercentualInsalubridade;
/**
   * @access Private
   * @var Numeric
*/
var $nuSalario;
/**
   * @access Private
   * @var Date
*/
var $dtValidadeExameMedico;
/**
   * @access Private
   * @var Numeric
*/
var $nuPercentualPericulosidade;
/**
   * @access Private
   * @var Boolean
*/
var $boAdiantamento;
/**
   * @access Private
   * @var Date
*/
var $dtInicioProgressao;
/**
   * @access Private
   * @var Date
*/
var $dtValidadeConselho;
/**
   * @access Private
   * @var Numeric
*/
var $nuHrMensal;
/**
   * @access Private
   * @var Numeric
*/
var $nuHrSemanal;
/**
   * @access Private
   * @var Object
*/
var $roPessoalServidor;
/**
   * @access Private
   * @var Array
*/
var $arRPessoalAssentamento;
/**
   * @access Private
   * @var Object
*/
var $roUltimoAssentamento;
/**
   * @access Private
   * @var Array
*/
var $arRPessoalServidor;
/**
   * @access Private
   * @var Object
*/
var $roUltimoServidor;
/**
   * @access Private
   * @var Array
*/
var $arRPessoalRescisao;
/**
   * @access Private
   * @var Object
*/
var $roUltimoPessoalRescisao;
/**
   * @access Private
   * @var Date
*/
var $dtVigenciaSalario;
/**
   * @access Private
   * @var String
*/
var $stSituacao;

/**
    * @access Public
    * @param Object $valor
*/
function setSituacao($valor) { $this->stSituacao = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setTransacao($valor) { $this->obTransacao                                    = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRNorma($valor) { $this->obRNorma                                       = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setROrganogramaOrgao($valor) { $this->obROrganogramaOrgao                            = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setROrganogramaLocal($valor) { $this->obROrganogramaLocal                            = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRPessoalTipoPagamento($valor) { $this->obRPessoalTipoPagamento                        = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRMonetarioBancoFGTS($valor) { $this->obRMonetarioBancoFGTS                          = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRMonetarioAgenciaFGTS($valor) { $this->obRMonetarioAgenciaFGTS                        = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRMonetarioBancoSalario($valor) { $this->obRMonetarioBancoSalario                       = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRMonetarioAgenciaSalario($valor) { $this->obRMonetarioAgenciaSalario                     = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRPessoalFormaPagamento($valor) { $this->obRPessoalFormaPagamento                       = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRPessoalTipoSalario($valor) { $this->obRPessoalTipoSalario                          = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRPessoalTipoAdmissao($valor) { $this->obRPessoalTipoAdmissao                         = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRPessoalVinculoEmpregaticio($valor) { $this->obRPessoalVinculoEmpregaticio                  = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRFolhaPagamentoSindicato($valor) { $this->obRFolhaPagamentoSindicato                     = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRPessoalCargo($valor) { $this->obRPessoalCargo                                = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRPessoalCargoFuncao($valor) { $this->obRPessoalCargoFuncao                          = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRPessoalRegime($valor) { $this->obRPessoalRegime                               = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRPessoalRegimeFuncao($valor) { $this->obRPessoalRegimeFuncao                         = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRPessoalCategoria($valor) { $this->obRPessoalCategoria                            = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRFolhaPagamentoPrevidencia($valor) { $this->obRFolhaPagamentoPrevidencia                   = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRPessoalOcorrencia($valor) { $this->obRPessoalOcorrencia                           = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRPessoalGradeHorario($valor) { $this->obRPessoalGradeHorario                         = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodContratoServidor($valor) { $this->inCodContratoServidor                          = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodConselho($valor) { $this->inCodConselho                                  = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setNroConselho($valor) { $this->inNroConselho                                  = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setContaCorrenteFgts($valor) { $this->stContaCorrenteFgts                            = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setNumClassificacao($valor) { $this->stNumClassificacao                             = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setContaCorrenteSalario($valor) { $this->stContaCorrenteSalario                         = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setFuncionarioCedido($valor) { $this->boFuncionarioCedido                            = $valor; }
/**
    * @access Public
    * @param Numeric $valor
*/
function setCargaHoraria($valor) { $this->nuCargaHoraria                                 = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setNroCartaoPonto($valor) { $this->inNroCartaoPonto                               = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setAtivo($valor) { $this->boAtivo                                        = $valor; }
/**
    * @access Public
    * @param Date $valor
*/
function setNomeacao($valor) { $this->dtNomeacao                                     = $valor; }
/**
    * @access Public
    * @param Date $valor
*/
function setAlteracaoFuncao($valor) { $this->dtAlteracaoFuncao                              = $valor; }
/**
    * @access Public
    * @param Date $valor
*/
function setValidadeConselho($valor) { $this->dtValidadeConselho                             = $valor; }
/**
    * @access Public
    * @param Date $valor
*/
function setAtiInativacao($valor) { $this->dtAtiInativacao                                = $valor; }
/**
    * @access Public
    * @param Date $valor
*/
function setAdmissao($valor) { $this->dtAdmissao                                     = $valor; }
/**
    * @access Public
    * @param Date $valor
*/
function setDataBase($valor) { $this->dtDataBase                                     = $valor; }
/**
    * @access Public
    * @param Date $valor
*/
function setPosse($valor) { $this->dtPosse                                        = $valor; }
/**
    * @access Public
    * @param Date $valor
*/
function setDemissao($valor) { $this->dtDemissao                                     = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setCausaDemissao($valor) { $this->stCausaDemissao                                = $valor; }
/**
    * @access Public
    * @param Date $valor
*/
function setOpcaoFgts($valor) { $this->dtOpcaoFgts                                    = $valor; }
/**
    * @access Public
    * @param Numeric $valor
*/
function setPercentualInsalubridade($valor) { $this->nuPercentualInsalubridade                      = $valor; }
/**
    * @access Public
    * @param Numeric $valor
*/
function setSalario($valor) { $this->nuSalario                                      = $valor; }
/**
    * @access Public
    * @param Date $valor
*/
function setValidadeExameMedico($valor) { $this->dtValidadeExameMedico                          = $valor; }
/**
    * @access Public
    * @param Numeric $valor
*/
function setPercentualPericulosidade($valor) { $this->nuPercentualPericulosidade                     = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setAdiantamento($valor) { $this->boAdiantamento                                 = $valor; }
/**
    * @access Public
    * @param Date $valor
*/
function setInicioProgressao($valor) { $this->dtInicioProgressao                             = $valor; }
/**
    * @access Public
    * @param Numeric $valor
*/
function setHrMensal($valor) { $this->nuHrMensal                                     = $valor; }
/**
    * @access Public
    * @param Numeric $valor
*/
function setHrSemanal($valor) { $this->nuHrSemanal                                    = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setPessoalServidor(&$valor) { $this->roPessoalServidor                              = &$valor; }
/**
    * @access Public
    * @param Array $valor
*/
function setRPessoalAssentamento($valor) { $this->arRPessoalAssentamento                         = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setUltimoAssentamento($valor) { $this->roUltimoAssentamento                           = $valor; }
/**
    * @access Public
    * @param Array $valor
*/
function setRPessoalServidor($valor) { $this->arRPessoalServidor                             = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setUltimoServidor($valor) { $this->roUltimoServidor                               = $valor; }
/**
    * @access Public
    * @param Array $valor
*/
function setRPessoalRescisao($valor) { $this->arRPessoalRescisao                             = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setUltimoPessoalRescisao($valor) { $this->roUltimoPessoalRescisao                        = $valor; }
/**
    * @access Public
    * @param Date $valor
*/
function setVigenciaSalario($valor) { $this->dtVigenciaSalario                                  = $valor; }

/**
    * @access Public
    * @return Object
*/
function getSituacao() { return $this->stSituacao; }
/**
    * @access Public
    * @return Object
*/
function getTransacao() { return $this->obTransacao;                                    }
/**
    * @access Public
    * @return Object
*/
function getRNorma() { return $this->obRNorma;                                       }
/**
    * @access Public
    * @return Object
*/
function getROrganogramaOrgao() { return $this->obROrganogramaOrgao;                            }
/**
    * @access Public
    * @return Object
*/
function getROrganogramaLocal() { return $this->obROrganogramaLocal;                            }
/**
    * @access Public
    * @return Object
*/
function getRPessoalTipoPagamento() { return $this->obRPessoalTipoPagamento;                        }
/**
    * @access Public
    * @return Object
*/
function getRMonetarioBancoFGTS() { return $this->obRMonetarioBancoFGTS;                          }
/**
    * @access Public
    * @return Object
*/
function getRMonetarioAgenciaFGTS() { return $this->obRMonetarioAgenciaFGTS;                        }
/**
    * @access Public
    * @return Object
*/
function getRMonetarioBancoSalario() { return $this->obRMonetarioBancoSalario;                       }
/**
    * @access Public
    * @return Object
*/
function getRMonetarioAgenciaSalario() { return $this->obRMonetarioAgenciaSalario;                     }
/**
    * @access Public
    * @return Object
*/
function getRPessoalFormaPagamento() { return $this->obRPessoalFormaPagamento;                       }
/**
    * @access Public
    * @return Object
*/
function getRPessoalTipoSalario() { return $this->obRPessoalTipoSalario;                          }
/**
    * @access Public
    * @return Object
*/
function getRPessoalTipoAdmissao() { return $this->obRPessoalTipoAdmissao;                         }
/**
    * @access Public
    * @return Object
*/
function getRPessoalVinculoEmpregaticio() { return $this->obRPessoalVinculoEmpregaticio;                  }
/**
    * @access Public
    * @return Object
*/
function getRFolhaPagamentoSindicato() { return $this->obRFolhaPagamentoSindicato;                     }
/**
    * @access Public
    * @return Object
*/
function getRPessoalCargo() { return $this->obRPessoalCargo;                                }
/**
    * @access Public
    * @return Object
*/
function getRPessoalCargoFuncao() { return $this->obRPessoalCargoFuncao;                          }
/**
    * @access Public
    * @return Object
*/
function getRPessoalRegime() { return $this->obRPessoalRegime;                               }
/**
    * @access Public
    * @return Object
*/
function getRPessoalRegimeFuncao() { return $this->obRPessoalRegimeFuncao;                         }
/**
    * @access Public
    * @return Object
*/
function getRPessoalCategoria() { return $this->obRPessoalCategoria;                            }
/**
    * @access Public
    * @return Object
*/
function getRFolhaPagamentoPrevidencia() { return $this->obRFolhaPagamentoPrevidencia;                   }
/**
    * @access Public
    * @return Object
*/
function getRPessoalOcorrencia() { return $this->obRPessoalOcorrencia;                           }
/**
    * @access Public
    * @return Object
*/
function getRPessoalGradeHorario() { return $this->obRPessoalGradeHorario;                         }
/**
    * @access Public
    * @return Integer
*/
function getCodContratoServidor() { return $this->inCodContratoServidor;                          }
/**
    * @access Public
    * @return String
*/
function getContaCorrenteFgts() { return $this->stContaCorrenteFgts;                            }
/**
    * @access Public
    * @return String
*/
function getNumClassificacao() { return $this->stNumClassificacao;                             }
/**
    * @access Public
    * @return String
*/
function getContaCorrenteSalario() { return $this->stContaCorrenteSalario;                         }
/**
    * @access Public
    * @return Boolean
*/
function getFuncionarioCedido() { return $this->boFuncionarioCedido;                            }
/**
    * @access Public
    * @return Numeric
*/
function getCargaHoraria() { return $this->nuCargaHoraria;                                 }
/**
    * @access Public
    * @return Integer
*/
function getNroCartaoPonto() { return $this->inNroCartaoPonto;                               }
/**
    * @access Public
    * @return Integer
*/
function getCodConselho() { return $this->inCodConselho;                                  }
/**
    * @access Public
    * @return Integer
*/
function getNroConselho() { return $this->inNroConselho;                                  }
/**
    * @access Public
    * @return Boolean
*/
function getAtivo() { return $this->boAtivo;                                        }
/**
    * @access Public
    * @return Date
*/
function getNomeacao() { return $this->dtNomeacao;                                     }
/**
    * @access Public
    * @return Date
*/
function getAlteracaoFuncao() { return $this->dtAlteracaoFuncao;                              }
/**
    * @access Public
    * @return Date
*/
function getValidadeConselho() { return $this->dtValidadeConselho;                             }
/**
    * @access Public
    * @return Date
*/
function getAtiInativacao() { return $this->dtAtiInativacao;                                }
/**
    * @access Public
    * @return Date
*/
function getAdmissao() { return $this->dtAdmissao;                                     }
/**
    * @access Public
    * @return Date
*/
function getDataBase() { return $this->dtDataBase;                                     }
/**
    * @access Public
    * @return Date
*/
function getPosse() { return $this->dtPosse;                                        }
/**
    * @access Public
    * @return Date
*/
function getDemissao() { return $this->dtDemissao;                                     }
/**
    * @access Public
    * @return String
*/
function getCausaDemissao() { return $this->stCausaDemissao;                                }
/**
    * @access Public
    * @return Date
*/
function getOpcaoFgts() { return $this->dtOpcaoFgts;                                    }
/**
    * @access Public
    * @return Numeric
*/
function getPercentualInsalubridade() { return $this->nuPercentualInsalubridade;                      }
/**
    * @access Public
    * @return Numeric
*/
function getSalario() { return $this->nuSalario;                                      }
/**
    * @access Public
    * @return Date
*/
function getValidadeExameMedico() { return $this->dtValidadeExameMedico;                          }
/**
    * @access Public
    * @return Numeric
*/
function getPercentualPericulosidade() { return $this->nuPercentualPericulosidade;                     }
/**
    * @access Public
    * @return Boolean
*/
function getAdiantamento() { return $this->boAdiantamento;                                 }
/**
    * @access Public
    * @return Date
*/
function getInicioProgressao() { return $this->dtInicioProgressao;                             }
/**
    * @access Public
    * @return Numeric
*/
function getHrMensal() { return $this->nuHrMensal;                                     }
/**
    * @access Public
    * @return Numeric
*/
function getHrSemanal() { return $this->nuHrSemanal;                                    }
/**
    * @access Public
    * @return Object
*/
function getPessoalServidor() { return $this->roPessoalServidor;                              }
/**
    * @access Public
    * @return Array
*/
function getRPessoalAssentamento() { return $this->arRPessoalAssentamento;                         }
/**
    * @access Public
    * @return Object
*/
function getUltimoAssentamento() { return $this->roUltimoAssentamento;                           }
/**
    * @access Public
    * @return Array
*/
function getRPessoalServidor() { return $this->arRPessoalServidor;                             }
/**
    * @access Public
    * @return Object
*/
function getUltimoServidor() { return $this->roUltimoServidor;                               }
/**
    * @access Public
    * @return Array
*/
function getRPessoalRescisao() { return $this->arRPessoalRescisao;                             }
/**
    * @access Public
    * @return Object
*/
function getUltimoPessoalRescisao() { return $this->roUltimoPessoalRescisao;                        }
/**
    * @access Public
    * @return Date
*/
function getVigenciaSalario() { return $this->dtVigenciaSalario;                        }

// Método construtor
function RPessoalContratoServidor(&$roPessoalServidor)
{
    parent::RPessoalContrato();
    $this->setRPessoalCargo                             ( new RPessoalCargo                              );
    $this->setRPessoalCategoria                         ( new RPessoalCategoria                          );
    $this->setRPessoalCargoFuncao                       ( new RPessoalCargo                              );
    $this->setRMonetarioBancoFGTS                       ( new RMONBanco                            );
    $this->setRMonetarioAgenciaFGTS                     ( new RMONAgencia                          );
    $this->setRMonetarioBancoSalario                    ( new RMONBanco                            );
    $this->setRMonetarioAgenciaSalario                  ( new RMONAgencia                          );
    $this->setRPessoalVinculoEmpregaticio               ( new RPessoalVinculoEmpregaticio                );
    $this->setRFolhaPagamentoSindicato                  ( new RFolhaPagamentoSindicato                   );
    $this->setRPessoalTipoAdmissao                      ( new RPessoalTipoAdmissao);
    $this->setRPessoalTipoSalario                       ( new RPessoalTipoSalario);
    $this->setRPessoalFormaPagamento                    ( new RPessoalFormaPagamento);
    $this->setRPessoalTipoPagamento                     ( new RPessoalTipoPagamento);
    $this->setROrganogramaOrgao                         ( new ROrganogramaOrgao);
    $this->setROrganogramaLocal                         ( new ROrganogramaLocal);
    $this->setRNorma                                    ( new RNorma);
    $this->setRPessoalRegime                            ( new RPessoalRegime);
    $this->setRPessoalRegimeFuncao                      ( new RPessoalRegime);
    $this->setRFolhaPagamentoPrevidencia                ( new RFolhaPagamentoPrevidencia);
    $this->setRPessoalOcorrencia                        ( new RPessoalOcorrencia);
    $this->setRPessoalGradeHorario                      ( new RPessoalGradeHorario);
    $this->setTransacao                                 ( new Transacao);
    $this->setPessoalServidor                           ( $roPessoalServidor);
    $this->obRCadastroDinamico                          = new RCadastroDinamico ;
    $this->obRCadastroDinamico->setPersistenteValores   ( new TPessoalAtributoContratoServidorValor );
    $this->obRCadastroDinamico->setCodCadastro          ( 5 );
    $this->obRCadastroDinamico->obRModulo->setCodModulo ( 22 );
}

function incluirContrato($boTransacao = "")
{
    global $rsFuncaoVagas;
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidor.class.php"                       );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorExameMedico.class.php"            );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorSalario.class.php"                );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorInicioProgressao.class.php"       );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorNomeacaoPosse.class.php"          );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorOcorrencia.class.php"             );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorOrgao.class.php"                  );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorLocal.class.php"                  );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorPrevidencia.class.php"            );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorNivelPadrao.class.php"            );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorPadrao.class.php"                 );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorEspecialidadeCargo.class.php"     );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorEspecialidadeFuncao.class.php"    );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorFuncao.class.php"                 );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorSubDivisaoFuncao.class.php"       );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorRegimeFuncao.class.php"           );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorContaSalario.class.php"           );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorFormaPagamento.class.php"         );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorContaFgts.class.php"              );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorConselho.class.php"               );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalCargoSubDivisao.class.php"                        );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalEspecialidadeSubDivisao.class.php"                );
    $obTPessoalContratoServidor                     = new TPessoalContratoServidor;
    $obTPessoalContratoServidorExameMedico          = new TPessoalContratoServidorExameMedico;
    $obTPessoalContratoServidorSalario              = new TPessoalContratoServidorSalario;
    $obTPessoalContratoServidorInicioProgressao     = new TPessoalContratoServidorInicioProgressao;
    $obTPessoalContratoServidorNomeacaoPosse        = new TPessoalContratoServidorNomeacaoPosse;
    $obTPessoalContratoServidorOcorrencia           = new TPessoalContratoServidorOcorrencia;
    $obTPessoalContratoServidorOrgao                = new TPessoalContratoServidorOrgao;
    $obTPessoalContratoServidorLocal                = new TPessoalContratoServidorLocal;
    $obTPessoalContratoServidorPrevidencia          = new TPessoalContratoServidorPrevidencia;
    $obTPessoalContratoServidorNivelPadrao          = new TPessoalContratoServidorNivelPadrao;
    $obTPessoalContratoServidorPadrao               = new TPessoalContratoServidorPadrao;
    $obTPessoalContratoServidorEspecialidadeCargo   = new TPessoalContratoServidorEspecialidadeCargo;
    $obTPessoalContratoServidorEspecialidadeFuncao  = new TPessoalContratoServidorEspecialidadeFuncao;
    $obTPessoalContratoServidorFuncao               = new TPessoalContratoServidorFuncao;
    $obTPessoalContratoServidorSubDivisaoFuncao     = new TPessoalContratoServidorSubDivisaoFuncao;
    $obTPessoalContratoServidorRegimeFuncao         = new TPessoalContratoServidorRegimeFuncao;
    $obTPessoalContratoServidorContaSalario         = new TPessoalContratoServidorContaSalario;
    $obTPessoalContratoServidorFormaPagamento       = new TPessoalContratoServidorFormaPagamento;
    $obTPessoalContratoServidorContaFgts            = new TPessoalContratoServidorContaFgts;
    $obTPessoalContratoServidorConselho             = new TPessoalContratoServidorConselho;
    $obTPessoalCargoSubDivisao                      = new TPessoalCargoSubDivisao;
    $obTPessoalEspecialidadeSubDivisao              =  new TPessoalEspecialidadeSubDivisao;
    if ( !$obErro->ocorreu() ) {
        if ( $this->getSalario() <= 0 ) {
            $obErro->setDescricao("Campo Salário da guia Contrato deve ser maior que zero!()");
        }
        if ( $this->getHrMensal() <= 0 ) {
            $obErro->setDescricao("Campo Horas Mensais da guia Contrato deve ser maior que zero!()");
        }
        if ( $this->getHrSemanal() <= 0 ) {
            $obErro->setDescricao("Campo Horas Semanais da guia Contrato deve ser maior que zero!()");
        }
    }
    if ( !$obErro->ocorreu() ) {
        //validacao do tipo de cargo e funcao selecionados
        //CARGO
        $inCodRegime              = $this->obRPessoalRegime->getCodRegime();
        $inCodSubDivisao          = $this->obRPessoalCargo->roUltimoCargoSubDivisao->obRPessoalSubDivisao->getCodSubDivisao();
        $inCodCargo               = $this->obRPessoalCargo->getCodCargo();
        $inCodEspecialidade       = $this->obRPessoalCargo->roUltimoEspecialidade->getCodEspecialidade();

        //FUNÇÃO
        $inCodRegimeFuncao        = $this->obRPessoalRegimeFuncao->getCodRegime();
        $inCodSubDivisaoFuncao    = $this->obRPessoalCargoFuncao->roUltimoEspecialidade->roPessoalCargo->roUltimoCargoSubDivisao->obRPessoalSubDivisao->getCodSubDivisao();
        $inCodFuncao              = $this->obRPessoalCargoFuncao->roUltimoEspecialidade->roPessoalCargo->getCodCargo();
        $inCodEspecialidadeFuncao = $this->obRPessoalCargoFuncao->roUltimoEspecialidade->getCodEspecialidade();

        if ( !$obErro->ocorreu() and $inCodEspecialidade == "" ) {
            $obTPessoalCargoSubDivisao->setDado("cod_regime",$inCodRegime);
            $obTPessoalCargoSubDivisao->setDado("cod_sub_divisao",$inCodSubDivisao);
            $obTPessoalCargoSubDivisao->setDado("cod_cargo",$inCodCargo);

            $obErro = $obTPessoalCargoSubDivisao->getVagasDisponiveisCargo($rsCargoVagas,"","",$boTransacao);

            if ( !$obErro->ocorreu() and $rsCargoVagas->getCampo("vagas") == 0) {
                $obErro->setDescricao("Cargo selecionado não possui vagas disponíveis!");
            }
        }

        if ( !$obErro->ocorreu() and $inCodEspecialidade != "" ) {
            $obTPessoalEspecialidadeSubDivisao->setDado("cod_regime",$inCodRegime);
            $obTPessoalEspecialidadeSubDivisao->setDado("cod_sub_divisao",$inCodSubDivisao);
            $obTPessoalEspecialidadeSubDivisao->setDado("cod_especialidade",$inCodEspecialidade);

            $obErro = $obTPessoalEspecialidadeSubDivisao->getVagasDisponiveisEspecialidade($rsEspecialidadeVagas,"","",$boTransacao);

            if (!$obErro->ocorreu()) {
                if ($rsEspecialidadeVagas->getCampo("vagas") == 0) {
                    $obErro->setDescricao("Especialidade do cargo selecionada não possui vagas disponíveis!");
                }
            }
        }

        if (!$obErro->ocorreu() and trim($inCodEspecialidadeFuncao) == "") {
            if ($inCodCargo != $inCodFuncao) {
                $obTPessoalCargoSubDivisao->setDado("cod_regime",$inCodRegime);
                $obTPessoalCargoSubDivisao->setDado("cod_sub_divisao",$inCodSubDivisao);
                $obTPessoalCargoSubDivisao->setDado("cod_cargo",$inCodFuncao);

                $obErro = $obTPessoalCargoSubDivisao->getVagasDisponiveisCargo($rsFuncaoVagas,"","",$boTransacao);

                if ( !$obErro->ocorreu() and $rsFuncaoVagas->getCampo("vagas") == 0) {
                    $obErro->setDescricao("Função selecionada não possui vagas disponíveis!");
                }
            }
        }

        if (!$obErro->ocorreu() and $inCodEspecialidade != $inCodEspecialidadeFuncao and $inCodEspecialidadeFuncao != "") {
            $obTPessoalEspecialidadeSubDivisao->setDado("cod_regime",$inCodRegime);
            $obTPessoalEspecialidadeSubDivisao->setDado("cod_sub_divisao",$inCodSubDivisao);
            $obTPessoalEspecialidadeSubDivisao->setDado("cod_especialidade",$inCodEspecialidadeFuncao);

            $obErro = $obTPessoalEspecialidadeSubDivisao->getVagasDisponiveisEspecialidade($rsEspecialidadeFuncaoVagas,"","",$boTransacao);

            if (!$obErro->ocorreu()) {
                if ($rsEspecialidadeFuncaoVagas->getCampo("vagas") == 0) {
                    $obErro->setDescricao("Especialidade do função selecionada não possui vagas disponíveis!");
                }
            }
        }

    }

    if ( !$obErro->ocorreu() ) {//9
        $obErro = parent::incluirContrato( $boTransacao );
        if ( !$obErro->ocorreu() ) {//8
            $obTPessoalContratoServidor->setDado("cod_contrato",          $this->getCodContrato()                             );
            $obTPessoalContratoServidor->setDado("cod_norma",             $this->obRNorma->getCodNorma()                      );
            $obTPessoalContratoServidor->setDado("cod_cargo",             $this->obRPessoalCargo->getCodCargo()               );
            $obTPessoalContratoServidor->setDado("cod_regime",            $this->obRPessoalRegime->getCodRegime()             );
            $obTPessoalContratoServidor->setDado("cod_portaria_nomeacao", $this->obRNorma->getCodNorma()                      );
            $obTPessoalContratoServidor->setDado("cod_tipo_admissao",     $this->obRPessoalTipoAdmissao->getCodTipoAdmissao() );
            $obTPessoalContratoServidor->setDado("cod_vinculo",           $this->obRPessoalVinculoEmpregaticio->getCodVinculoEmpregaticio()  );
            $obTPessoalContratoServidor->setDado("cod_tipo_pagamento",    $this->obRPessoalTipoPagamento->getCodTipoPagamento()   );
            $obTPessoalContratoServidor->setDado("cod_tipo_salario",      $this->obRPessoalTipoSalario->getCodTipoSalario()       );
            $obTPessoalContratoServidor->setDado("nr_cartao_ponto",       $this->getNroCartaoPonto()                              );
            $obTPessoalContratoServidor->setDado("funcionario_cedido",    $this->getFuncionarioCedido()                           );
            $obTPessoalContratoServidor->setDado("ativo",                 $this->getAtivo()                                       );
            $obTPessoalContratoServidor->setDado("dt_demissao",           $this->getNomeacao()                                    );
            $obTPessoalContratoServidor->setDado("dt_validade_exame",     $this->getValidadeExameMedico()                         );
            $obTPessoalContratoServidor->setDado("dt_opcao_fgts",         $this->getOpcaoFgts()                                   );
            $obTPessoalContratoServidor->setDado("cod_padrao",            $this->obRPessoalCargo->obRFolhaPagamentoPadrao->getCodPadrao());

            $this->obRPessoalCargo->obRFolhaPagamentoPadrao->addNivelPadrao();

            $inCodProgressao = $this->obRPessoalCargo->obRFolhaPagamentoPadrao->roUltimoNivelPadrao->getCodNivelPadrao();

            $obTPessoalContratoServidor->setDado("cod_progressao",        $inCodProgressao );
            $obTPessoalContratoServidor->setDado("adiantamento",          $this->getAdiantamento()                                );
            $obTPessoalContratoServidor->setDado("cod_categoria",         $this->obRPessoalCategoria->getCodCategoria()           );
            $obTPessoalContratoServidor->setDado("cod_sub_divisao",       $this->obRPessoalCargo->roUltimoCargoSubDivisao->obRPessoalSubDivisao->getCodSubDivisao());
            $obTPessoalContratoServidor->setDado("cod_grade",             $this->obRPessoalGradeHorario->getCodGrade() );

            $obErro = $obTPessoalContratoServidor->inclusao( $boTransacao );

            if ( !$obErro->ocorreu() and $this->getValidadeExameMedico() ) {
                $obTPessoalContratoServidorExameMedico->setDado("cod_contrato"     ,$this->getCodContrato()            );
                $obTPessoalContratoServidorExameMedico->setDado("dt_validade_exame",$this->getValidadeExameMedico()    );

                $obErro = $obTPessoalContratoServidorExameMedico->inclusao($boTransacao);
            }

            if ( !$obErro->ocorreu() and $this->obRFolhaPagamentoSindicato->obRCGM->getNumCGM() != "") {
                include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorSindicato.class.php");
                $obTPessoalContratoServidorSindicato = new TPessoalContratoServidorSindicato();
                $obTPessoalContratoServidorSindicato->setDado("numcgm_sindicato",      $this->obRFolhaPagamentoSindicato->obRCGM->getNumCGM() );
                $obTPessoalContratoServidorSindicato->setDado("cod_contrato",          $this->getCodContrato() );

                $obErro = $obTPessoalContratoServidorSindicato->inclusao($boTransacao);
            }

            if ( !$obErro->ocorreu() ) {
                include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
                $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();

                $obErro = $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao,"","",$boTransacao);
            }

            if ( !$obErro->ocorreu() ) {
                $obTPessoalContratoServidorSalario->setDado("cod_contrato"    ,$this->getCodContrato()    );
                $obTPessoalContratoServidorSalario->setDado("salario"         ,$this->getSalario()        );
                $obTPessoalContratoServidorSalario->setDado("horas_mensais"   ,$this->getHrMensal()       );
                $obTPessoalContratoServidorSalario->setDado("horas_semanais"  ,$this->getHrSemanal()      );
                $obTPessoalContratoServidorSalario->setDado("vigencia"        ,$this->getVigenciaSalario());
                $obTPessoalContratoServidorSalario->setDado("cod_periodo_movimentacao"        ,$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));

                $obErro = $obTPessoalContratoServidorSalario->inclusao($boTransacao);
            }

            if ( !$obErro->ocorreu() and $this->getInicioProgressao() ) {
                $obTPessoalContratoServidorInicioProgressao->setDado("cod_contrato"           ,$this->getCodContrato()        );
                $obTPessoalContratoServidorInicioProgressao->setDado("dt_inicio_progressao"   ,$this->getInicioProgressao()   );

                $obErro = $obTPessoalContratoServidorInicioProgressao->inclusao($boTransacao);
            }

            if ( !$obErro->ocorreu() ) {
                $obTPessoalContratoServidorNomeacaoPosse->setDado("cod_contrato"  ,$this->getCodContrato()    );
                $obTPessoalContratoServidorNomeacaoPosse->setDado("dt_nomeacao"   ,$this->getNomeacao()       );
                $obTPessoalContratoServidorNomeacaoPosse->setDado("dt_posse"      ,$this->getPosse()          );
                $obTPessoalContratoServidorNomeacaoPosse->setDado("dt_admissao"      ,$this->getAdmissao()          );

                $obErro = $obTPessoalContratoServidorNomeacaoPosse->inclusao($boTransacao);
            }

            if ( !$obErro->ocorreu() ) {
                $inNumOcorrencia = $this->obRPessoalOcorrencia->getCodOcorrencia();
                $this->obRPessoalOcorrencia->setCodOcorrencia("");
                $this->obRPessoalOcorrencia->setNumOcorrencia($inNumOcorrencia);

                $obErro = $this->obRPessoalOcorrencia->listarOcorrencia($rsOcorrencia,$boTransacao);

                if ( !$obErro->ocorreu() ) {
                    $this->obRPessoalOcorrencia->setCodOcorrencia( $rsOcorrencia->getCampo('cod_ocorrencia') );
                    $obTPessoalContratoServidorOcorrencia->setDado('cod_ocorrencia',$this->obRPessoalOcorrencia->getCodOcorrencia());
                    $obTPessoalContratoServidorOcorrencia->setDado('cod_contrato',$this->getCodContrato());

                    $obErro = $obTPessoalContratoServidorOcorrencia->inclusao( $boTransacao );
                }
            }

            if ( !$obErro->ocorreu() ) {//7
                $obTPessoalContratoServidorOrgao->setDado("cod_contrato",   $this->getCodContrato()                           );
                $obTPessoalContratoServidorOrgao->setDado("cod_orgao",      $this->obROrganogramaOrgao->getCodOrgao("cod_orgao") );

                $obErro = $obTPessoalContratoServidorOrgao->inclusao( $boTransacao );
            }

            if ( !$obErro->ocorreu() ) {//6
                if ( $this->obROrganogramaLocal->getCodLocal() ) {
                    $obTPessoalContratoServidorLocal->setDado("cod_contrato", $this->getCodContrato() );
                    $obTPessoalContratoServidorLocal->setDado("cod_local"   , $this->obROrganogramaLocal->getCodLocal() );

                    $obErro = $obTPessoalContratoServidorLocal->inclusao( $boTransacao );
                }
            }

            if ( !$obErro->ocorreu() ) {//5
                $arPrevidencia = Sessao::read("PREVIDENCIA");
                if ( is_array( $arPrevidencia ) ) {
                    while ( list( $arId, $inCodPrevidencia ) = each( $arPrevidencia ) ) {
                        if ( !$obErro->ocorreu() ) {
                            $obTPessoalContratoServidorPrevidencia->setDado("cod_contrato" , $this->getCodContrato() );
                            $obTPessoalContratoServidorPrevidencia->setDado("cod_previdencia" , $inCodPrevidencia );
                            $obTPessoalContratoServidorPrevidencia->setDado("timestamp_previdencia" , $inCodPrevidencia );

                            $obErro = $obTPessoalContratoServidorPrevidencia->inclusao( $boTransacao );
                        }
                    }
                }
            }

            if ( !$obErro->ocorreu() ) {//4
                foreach ($this->obRPessoalCargo->obRFolhaPagamentoPadrao->arRFolhaPagamentoNivelPadrao as $obRNivelPadrao) {
                    if ( $obRNivelPadrao->getCodNivelPadrao() ) {
                        $obRNivelPadrao->listarNivelPadrao( $rsNivelPadrao,$boTransacao );

                        $inCodNivelPadrao = $obRNivelPadrao->getCodNivelPadrao();
                        $stTimestamp      = $rsNivelPadrao->getCampo('timestamp_padrao');
                        $inCodPadrao      = $rsNivelPadrao->getCampo('cod_padrao');
                        break;
                    }
                }

                if ($inCodNivelPadrao) {
                    $obTPessoalContratoServidorNivelPadrao->setDado("cod_contrato"            , $this->getCodContrato() );
                    $obTPessoalContratoServidorNivelPadrao->setDado("cod_nivel_padrao"        , $inCodNivelPadrao);
                    $obTPessoalContratoServidorNivelPadrao->setDado("timestamp_nivel_padrao"  , $stTimestamp);
                    $obTPessoalContratoServidorNivelPadrao->setDado("cod_padrao"              , $inCodPadrao);
                    $obTPessoalContratoServidorNivelPadrao->setDado("cod_periodo_movimentacao"        ,$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));

                    $obErro = $obTPessoalContratoServidorNivelPadrao->inclusao( $boTransacao );
                }
            }

            if ( !$obErro->ocorreu() ) {//3
                if ( $this->obRPessoalCargo->obRFolhaPagamentoPadrao->getCodPadrao() ) {
                    $obTPessoalContratoServidorPadrao->setDado("cod_contrato"     , $this->getCodContrato() );
                    $obTPessoalContratoServidorPadrao->setDado("cod_padrao"       , $this->obRPessoalCargo->obRFolhaPagamentoPadrao->getCodPadrao());
                    $obTPessoalContratoServidorPadrao->setDado("timestamp_padrao" , $stTimestamp);

                    $obErro = $obTPessoalContratoServidorPadrao->inclusao( $boTransacao );
                }
            }

            if ( !$obErro->ocorreu() ) {//1
                if ( !$obErro->ocorreu() and $inCodEspecialidade != "" ) {
                    $obTPessoalContratoServidorEspecialidadeCargo->setDado("cod_contrato", $this->getCodContrato());
                    $obTPessoalContratoServidorEspecialidadeCargo->setDado("cod_especialidade", $inCodEspecialidade);

                    $obErro = $obTPessoalContratoServidorEspecialidadeCargo->inclusao( $boTransacao );
                }

                if (!$obErro->ocorreu() and $inCodEspecialidade != $inCodEspecialidadeFuncao and $inCodEspecialidadeFuncao != "") {
                    $obTPessoalContratoServidorEspecialidadeFuncao->setDado("cod_contrato", $this->getCodContrato());
                    $obTPessoalContratoServidorEspecialidadeFuncao->setDado("cod_especialidade", $inCodEspecialidadeFuncao);

                    $obErro = $obTPessoalContratoServidorEspecialidadeFuncao->inclusao( $boTransacao );
                }

                if ( !$obErro->ocorreu() ) {
                    include_once(CAM_GRH_PES_NEGOCIO."RConfiguracaoPessoal.class.php");
                    $obRConfiguracaoPessoal = new RConfiguracaoPessoal();

                    $obErro = $obRConfiguracaoPessoal->consultar($boTransacao);
                }

                if ( !$obErro->ocorreu() ) {
                    $obTPessoalContratoServidorFuncao->setDado( "cod_contrato", $this->getCodContrato() );
                    $obTPessoalContratoServidorFuncao->setDado( "cod_cargo", $this->obRPessoalCargoFuncao->getCodCargo()  );

                    $dtPosse    = $this->getPosse();
                    $dtNomeacao = $this->getNomeacao();
                    $dtAdmissao = $this->getAdmissao();

                    $dtPosse    = implode('',array_reverse(explode('/',$dtPosse)));
                    $dtNomeacao = implode('',array_reverse(explode('/',$dtNomeacao)));
                    $dtAdmissao = implode('',array_reverse(explode('/',$dtAdmissao)));

                    $dtData = $this->getPosse();

                    if ($dtNomeacao > $dtPosse) {
                            $dtData = $this->getNomeacao();
                    }
                    if ($dtAdmissao > $dtPosse) {
                            $dtData = $this->getAdmissao();
                    }
                    if ($dtNomeacao > $dtAdmissao) {
                            $dtData = $this->getNomeacao();
                    }

                    $obTPessoalContratoServidorFuncao->setDado( "vigencia", $dtData  );

                    $obErro = $obTPessoalContratoServidorFuncao->inclusao( $boTransacao );
                }

                if ( !$obErro->ocorreu() ) {
                    $obTPessoalContratoServidorSubDivisaoFuncao->setDado("cod_contrato",$this->getCodContrato());
                    $obTPessoalContratoServidorSubDivisaoFuncao->setDado("cod_sub_divisao",$this->obRPessoalCargoFuncao->roUltimoCargoSubDivisao->obRPessoalSubDivisao->getCodSubDivisao());

                    $obErro = $obTPessoalContratoServidorSubDivisaoFuncao->inclusao($boTransacao);
                }

                if ( !$obErro->ocorreu() ) {
                    $obTPessoalContratoServidorRegimeFuncao->setDado("cod_contrato",$this->getCodContrato());
                    $obTPessoalContratoServidorRegimeFuncao->setDado("cod_regime",$this->obRPessoalRegimeFuncao->getCodRegime());

                    $obErro = $obTPessoalContratoServidorRegimeFuncao->inclusao($boTransacao);
                }

                if ( !$obErro->ocorreu() ) {
                    $obTPessoalContratoServidorFormaPagamento->setDado("cod_contrato", $this->getCodContrato());
                    $obTPessoalContratoServidorFormaPagamento->setDado("cod_forma_pagamento", $this->obRPessoalFormaPagamento->getCodFormaPagamento());

                    $obErro = $obTPessoalContratoServidorFormaPagamento->inclusao($boTransacao);
                }

                if ( !$obErro->ocorreu() and $this->obRMonetarioBancoSalario->getNumBanco() ) {
                    //Dados bancarios Salario
                    if ( $this->obRMonetarioAgenciaSalario->getCodAgencia() == "" ) {
                        $obErro->setDescricao("Você deve selecionar uma agencia bancária para o Informações Salariais na aba contrato!");
                    }
                    if ( $this->getContaCorrenteSalario() == "" ) {
                        $obErro->setDescricao("Você deve informar uma conta para crédito para o Informações Salariais na aba contrato!");
                    }

                    if ( !$obErro->ocorreu() ) {
                        $this->obRMonetarioBancoSalario->setCodBanco("");

                        $obErro = $this->obRMonetarioBancoSalario->listarBanco($rsBanco,$boTransacao);

                        $this->obRMonetarioBancoSalario->setCodBanco($rsBanco->getCampo('cod_banco'));
                    }

                    if ( !$obErro->ocorreu() ) {
                        $this->obRMonetarioAgenciaSalario->setNumAgencia($this->obRMonetarioAgenciaSalario->getCodAgencia());
                        $this->obRMonetarioAgenciaSalario->setCodAgencia('');
                        $this->obRMonetarioAgenciaSalario->obRMONBanco = $this->obRMonetarioBancoSalario;

                        $obErro = $this->obRMonetarioAgenciaSalario->listarAgencia($rsAgenciaBancaria, $boTransacao);

                        $this->obRMonetarioAgenciaSalario->setCodAgencia( $rsAgenciaBancaria->getCampo('cod_agencia') );
                    }

                    if ( !$obErro->ocorreu() ) {
                        $obTPessoalContratoServidorContaSalario->setDado("cod_contrato",      $this->getCodContrato()         );
                        $obTPessoalContratoServidorContaSalario->setDado("cod_banco",         $this->obRMonetarioBancoSalario->getCodBanco()      );
                        $obTPessoalContratoServidorContaSalario->setDado("cod_agencia",       $this->obRMonetarioAgenciaSalario->getCodAgencia()  );
                        $obTPessoalContratoServidorContaSalario->setDado("nr_conta",          $this->getContaCorrenteSalario() );

                        $obErro = $obTPessoalContratoServidorContaSalario->inclusao( $boTransacao );
                    }
                }
                //Dados Bancarios FGTS
                if ( !$obErro->ocorreu() and $this->obRMonetarioBancoFGTS->getNumBanco() ) {
                    if ( $this->obRMonetarioAgenciaFGTS->getCodAgencia() == "" ) {
                        $obErro->setDescricao("Você deve selecionar uma agencia bancária para o FGTS na aba contrato!");
                    }
                    if ( $this->getContaCorrenteFgts() == "" ) {
                        $obErro->setDescricao("Você deve informar uma conta para crédito para o FGTS na aba contrato!");
                    }
                    if ( !$obErro->ocorreu() ) {
                        $this->obRMonetarioAgenciaFGTS->setNumAgencia( $this->obRMonetarioAgenciaFGTS->getCodAgencia() );
                        $this->obRMonetarioAgenciaFGTS->setCodAgencia("");

                        $obErro = $this->obRMonetarioAgenciaFGTS->listarAgencia($rsAgenciaBancaria, $boTransacao);

                        $this->obRMonetarioAgenciaFGTS->setCodAgencia( $rsAgenciaBancaria->getCampo('cod_agencia') );
                    }
                    if ( !$obErro->ocorreu() ) {
                        $this->obRMonetarioBancoFGTS->setCodBanco("");

                        $obErro = $this->obRMonetarioBancoFGTS->listarBanco($rsBanco,$boTransacao);

                        $this->obRMonetarioBancoFGTS->setCodBanco($rsBanco->getCampo('cod_banco'));
                    }
                    if ( !$obErro->ocorreu() ) {
                        $obTPessoalContratoServidorContaFgts->setDado("cod_contrato",   $this->getCodContrato()         );
                        $obTPessoalContratoServidorContaFgts->setDado("cod_banco",      $this->obRMonetarioBancoFGTS->getCodBanco()         );
                        $obTPessoalContratoServidorContaFgts->setDado("cod_agencia",    $this->obRMonetarioAgenciaFGTS->getCodAgencia()     );
                        $obTPessoalContratoServidorContaFgts->setDado("nr_conta",       $this->getContaCorrenteFgts()  );

                        $obErro = $obTPessoalContratoServidorContaFgts->inclusao( $boTransacao );
                    }
                }// fim do if Dados FGTS

                if ( !$obErro->ocorreu() ) {
                    //O Restante dos valores vem setado da pagina de processamento
                    $arChaveAtributoCandidato =  array( "cod_contrato" => $this->getCodContrato() );
                    $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoCandidato );

                    $obErro = $this->obRCadastroDinamico->salvarValores( $boTransacao );
                }

                if ( $this->getCodConselho() or $this->getNroConselho() or $this->getValidadeConselho() ) {
                    if ( !$this->getCodConselho() ) {
                        $obErro->setDescricao("Você deve selecionar o conselho profissional na aba contrato!");
                    }
                    if ( !$this->getNroConselho() ) {
                        $obErro->setDescricao("Você deve informar o número do conselho profissional na aba contrato!");
                    }
                    if ( !$this->getValidadeConselho() ) {
                        $obErro->setDescricao("Você deve informar a data de validade do conselho profissional na aba contrato!");
                    }
                    if ( !$obErro->ocorreu() ) {
                        $obTPessoalContratoServidorConselho->setDado("cod_contrato",$this->getCodContrato());
                        $obTPessoalContratoServidorConselho->setDado("cod_conselho",$this->getCodConselho());
                        $obTPessoalContratoServidorConselho->setDado("nr_conselho",$this->getNroConselho());
                        $obTPessoalContratoServidorConselho->setDado("dt_validade",$this->getValidadeConselho());

                        $obErro = $obTPessoalContratoServidorConselho->inclusao($boTransacao);
                    }
                }
            }//1
        }//8
    }//9

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTPessoalContratoServidor );

    return $obErro;
}

function gerarAssentamento($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    $inCodSubDivisao      = $this->obRPessoalCargo->roUltimoCargoSubDivisao->obRPessoalSubDivisao->getCodSubDivisao();
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoAssentamento.class.php");
    $obTPessoalAssentamentoAssentamento = new TPessoalAssentamentoAssentamento();
    $stFiltro  = " AND (assentamento_assentamento.cod_motivo = 11 ";
    $stFiltro .= "   OR assentamento_assentamento.cod_motivo = 12 ";
    $stFiltro .= "   OR assentamento_assentamento.cod_motivo = 13)";
    $stFiltro .= " AND assentamento_sub_divisao.cod_sub_divisao = ".$inCodSubDivisao;
    $obErro = $obTPessoalAssentamentoAssentamento->recuperaAssentamentoSubDivisao($rsAssentamento,$stFiltro,"",$boTransacao);
    if ( !$obErro->ocorreu() ) {
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalRegime.class.php");
        $obTPessoalRegime = new TPessoalRegime();
        $obTPessoalRegime->setDado("cod_regime",$this->obRPessoalRegime->getCodRegime());

        $obErro = $obTPessoalRegime->recuperaPorChave($rsRegime,$boTransacao);
    }
    if ( !$obErro->ocorreu() ) {
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalSubDivisao.class.php");
        $obTPessoalSubDivisao = new TPessoalSubDivisao();
        $obTPessoalSubDivisao->setDado("cod_sub_divisao",$inCodSubDivisao);
        $obTPessoalSubDivisao->setDado("cod_regime",$this->obRPessoalRegimeFuncao->getCodRegime());

        $obErro = $obTPessoalSubDivisao->recuperaPorChave($rsSubDivisao,$boTransacao);
    }
    if ( !$obErro->ocorreu() ) {
        $inCodCargo                = $this->obRPessoalCargo->getCodCargo();
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalCargo.class.php");
        $obTPessoalCargo = new TPessoalCargo();
        $obTPessoalCargo->setDado("cod_cargo",$inCodCargo);

        $obErro = $obTPessoalCargo->recuperaPorChave($rsCargo,$boTransacao);
    }
    $rsEspecialidade = new RecordSet;
    $inCodEspecialidade   = $this->obRPessoalCargo->roUltimoEspecialidade->getCodEspecialidade();
    if ( !$obErro->ocorreu() and $inCodEspecialidade != "") {
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalEspecialidade.class.php");
        $obTPessoalEspecialidade = new TPessoalEspecialidade();
        $obTPessoalEspecialidade->setDado("cod_especialidade",$inCodEspecialidade);
        $obTPessoalEspecialidade->setDado("cod_cargo",$inCodCargo);

        $obErro = $obTPessoalEspecialidade->recuperaPorChave($rsEspecialidade,$boTransacao);
    }
    if ( !$obErro->ocorreu() ) {
        include_once(CAM_GRH_PES_NEGOCIO."RPessoalAssentamentoGeradoContratoServidor.class.php");
        $obRPessoalAssentametoGeradoContratoServidor = new RPessoalAssentamentoGeradoContratoServidor();
        $obRPessoalAssentametoGeradoContratoServidor->addRPessoalGeracaoAssentamento();
        while (!$rsAssentamento->eof()) {
            if ($rsAssentamento->getCampo("assentamento_automatico") == "t") {
                switch ($rsAssentamento->getCampo("cod_motivo")) {
                    case 11:
                        $stDataInicioFim = $this->getNomeacao();
                        $stObservacao = "Nomeado sob o regime ".$rsRegime->getCampo("descricao")."/".$rsSubDivisao->getCampo("descricao")." e cargo ".$rsCargo->getCampo("descricao");
                        if ($rsEspecialidade->getNumLinhas() == 1) {
                            $stObservacao .= "/".$rsEspecialidade->getCampo("descricao");
                        }
                        break;
                    case 12:
                        $stDataInicioFim = $this->getPosse();
                        $stObservacao = "Posse sob o regime ".$rsRegime->getCampo("descricao")."/".$rsSubDivisao->getCampo("descricao")." e cargo ".$rsCargo->getCampo("descricao");
                        if ($rsEspecialidade->getNumLinhas() == 1) {
                            $stObservacao .= "/".$rsEspecialidade->getCampo("descricao");
                        }
                        break;
                    case 13:
                        $stDataInicioFim = $this->getAdmissao();
                        $stObservacao = "Admitido sob o regime ".$rsRegime->getCampo("descricao")."/".$rsSubDivisao->getCampo("descricao")." e cargo ".$rsCargo->getCampo("descricao");
                        if ($rsEspecialidade->getNumLinhas() == 1) {
                            $stObservacao .= "/".$rsEspecialidade->getCampo("descricao");
                        }
                        break;
                }
                $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->obRPessoalContratoServidor->setCodContrato($this->getCodContrato());
                $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->obRPessoalAssentamento->setCodAssentamento($rsAssentamento->getCampo("cod_assentamento"));
                $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->setDescricaoObservacao($stObservacao);
                $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->setPeriodoInicial($stDataInicioFim);
                $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->setPeriodoFinal($stDataInicioFim);
                $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->setCodNorma($this->obRNorma->getCodNorma());
                $obErro = $obRPessoalAssentametoGeradoContratoServidor->incluirAssentamentoGeradoContratoServidor($boTransacao);
                if ($obErro->ocorreu()) {
                    break;
                }
            }
            $rsAssentamento->proximo();
        }
    }

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTPessoalContratoServidor );

    return $obErro;
}

function alterarContrato($boTransacao = "")
{
    global $rsFuncaoVagas;
    global $rsCargoAlterar;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidor.class.php"                       );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorExameMedico.class.php"            );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorSalario.class.php"                );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorInicioProgressao.class.php"       );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorNomeacaoPosse.class.php"          );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorOcorrencia.class.php"             );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorOrgao.class.php"                  );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorLocal.class.php"                  );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorLocalHistorico.class.php"         );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorPrevidencia.class.php"            );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorNivelPadrao.class.php"            );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorPadrao.class.php"                 );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorEspecialidadeCargo.class.php"     );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorEspecialidadeFuncao.class.php"    );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorFuncao.class.php"                 );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorSubDivisaoFuncao.class.php"       );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorRegimeFuncao.class.php"           );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorContaSalario.class.php"           );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorFormaPagamento.class.php"         );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorContaFgts.class.php"              );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorConselho.class.php"               );
    $obTPessoalContratoServidor                     = new TPessoalContratoServidor;
    $obTPessoalContratoServidorExameMedico          = new TPessoalContratoServidorExameMedico;
    $obTPessoalContratoServidorSalario              = new TPessoalContratoServidorSalario;
    $obTPessoalContratoServidorInicioProgressao     = new TPessoalContratoServidorInicioProgressao;
    $obTPessoalContratoServidorNomeacaoPosse        = new TPessoalContratoServidorNomeacaoPosse;
    $obTPessoalContratoServidorOcorrencia           = new TPessoalContratoServidorOcorrencia;
    $obTPessoalContratoServidorOrgao                = new TPessoalContratoServidorOrgao;
    $obTPessoalContratoServidorLocal                = new TPessoalContratoServidorLocal;
    $obTPessoalContratoServidorLocalHistorico       = new TPessoalContratoServidorLocalHistorico;
    $obTPessoalContratoServidorPrevidencia          = new TPessoalContratoServidorPrevidencia;
    $obTPessoalContratoServidorNivelPadrao          = new TPessoalContratoServidorNivelPadrao;
    $obTPessoalContratoServidorPadrao               = new TPessoalContratoServidorPadrao;
    $obTPessoalContratoServidorEspecialidadeCargo   = new TPessoalContratoServidorEspecialidadeCargo;
    $obTPessoalContratoServidorEspecialidadeFuncao  = new TPessoalContratoServidorEspecialidadeFuncao;
    $obTPessoalContratoServidorFuncao               = new TPessoalContratoServidorFuncao;
    $obTPessoalContratoServidorSubDivisaoFuncao     = new TPessoalContratoServidorSubDivisaoFuncao;
    $obTPessoalContratoServidorRegimeFuncao         = new TPessoalContratoServidorRegimeFuncao;
    $obTPessoalContratoServidorContaSalario         = new TPessoalContratoServidorContaSalario;
    $obTPessoalContratoServidorFormaPagamento       = new TPessoalContratoServidorFormaPagamento;
    $obTPessoalContratoServidorContaFgts            = new TPessoalContratoServidorContaFgts;
    $obTPessoalContratoServidorConselho             = new TPessoalContratoServidorConselho;
    if ( !$obErro->ocorreu() ) {
        if ( $this->getSalario() <= 0 ) {
            $obErro->setDescricao("Campo Salário da guia Contrato deve ser maior que zero!()");
        }
        if ( $this->getHrMensal() <= 0 ) {
            $obErro->setDescricao("Campo Horas Mensais da guia Contrato deve ser maior que zero!()");
        }
        if ( $this->getHrSemanal() <= 0 ) {
            $obErro->setDescricao("Campo Horas Semanais da guia Contrato deve ser maior que zero!()");
        }
    }
    if ( !$obErro->ocorreu() ) {//9
        $obTPessoalContratoServidor->setDado("cod_contrato",          $this->getCodContrato()                             );
        $obTPessoalContratoServidor->setDado("cod_norma",             $this->obRNorma->getCodNorma()                      );
        $obTPessoalContratoServidor->setDado("cod_cargo",             $this->obRPessoalCargo->getCodCargo()               );
        $obTPessoalContratoServidor->setDado("cod_regime",            $this->obRPessoalRegime->getCodRegime()             );
        $obTPessoalContratoServidor->setDado("cod_tipo_admissao",     $this->obRPessoalTipoAdmissao->getCodTipoAdmissao() );
        $obTPessoalContratoServidor->setDado("cod_vinculo",           $this->obRPessoalVinculoEmpregaticio->getCodVinculoEmpregaticio()  );
        $obTPessoalContratoServidor->setDado("cod_tipo_pagamento",    $this->obRPessoalTipoPagamento->getCodTipoPagamento()   );
        $obTPessoalContratoServidor->setDado("cod_tipo_salario",      $this->obRPessoalTipoSalario->getCodTipoSalario()       );
        $obTPessoalContratoServidor->setDado("nr_cartao_ponto",       $this->getNroCartaoPonto()                              );
        $obTPessoalContratoServidor->setDado("ativo",                 $this->getAtivo()                                       );
        $obTPessoalContratoServidor->setDado("dt_opcao_fgts",         $this->getOpcaoFgts()                                   );
        $this->obRPessoalCargo->obRFolhaPagamentoPadrao->addNivelPadrao();
        $inCodProgressao = $this->obRPessoalCargo->obRFolhaPagamentoPadrao->roUltimoNivelPadrao->getCodNivelPadrao();
        $obTPessoalContratoServidor->setDado("adiantamento",          $this->getAdiantamento()                                );
        $obTPessoalContratoServidor->setDado("cod_categoria",         $this->obRPessoalCategoria->getCodCategoria()           );
        $obTPessoalContratoServidor->setDado("cod_sub_divisao",       $this->obRPessoalCargo->roUltimoCargoSubDivisao->obRPessoalSubDivisao->getCodSubDivisao());
        $obTPessoalContratoServidor->setDado("cod_grade",             $this->obRPessoalGradeHorario->getCodGrade()           );
        $obErro = $obTPessoalContratoServidor->alteracao( $boTransacao );        
    }
    if ( !$obErro->ocorreu() ) {
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorSindicato.class.php");
        $obTPessoalContratoServidorSindicato = new TPessoalContratoServidorSindicato();
        $obTPessoalContratoServidorSindicato->setDado("cod_contrato",          $this->getCodContrato() );
        if ($this->obRFolhaPagamentoSindicato->obRCGM->getNumCGM() != "") {
            $obTPessoalContratoServidorSindicato->setDado("numcgm_sindicato",      $this->obRFolhaPagamentoSindicato->obRCGM->getNumCGM() );
            $obErro = $obTPessoalContratoServidorSindicato->recuperaPorChave($rsSindicato,$boTransacao);
            if (!$obErro->ocorreu()) {
                if ($rsSindicato->getNumLinhas() < 0) {
                    $obErro = $obTPessoalContratoServidorSindicato->inclusao($boTransacao);
                } else {
                    $obErro = $obTPessoalContratoServidorSindicato->alteracao($boTransacao);
                }
            }
        } else {
            $obErro = $obTPessoalContratoServidorSindicato->exclusao($boTransacao);
        }
    }
    if ( !$obErro->ocorreu() and $this->getValidadeExameMedico() ) {
        $obTPessoalContratoServidorExameMedico->setDado("cod_contrato"     ,$this->getCodContrato()            );
        $obTPessoalContratoServidorExameMedico->setDado("dt_validade_exame",$this->getValidadeExameMedico()    );
        $obErro = $obTPessoalContratoServidorExameMedico->inclusao($boTransacao);
    }
    if ( !$obErro->ocorreu() ) {
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
        $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
        $obErro = $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao,"","",$boTransacao);
    }
    if ( !$obErro->ocorreu() ) {
        $obTPessoalContratoServidorSalario->setDado("cod_contrato"    ,$this->getCodContrato()    );
        $obTPessoalContratoServidorSalario->setDado("salario"         ,$this->getSalario()        );
        $obTPessoalContratoServidorSalario->setDado("horas_mensais"   ,$this->getHrMensal()       );
        $obTPessoalContratoServidorSalario->setDado("horas_semanais"  ,$this->getHrSemanal()      );
        $obTPessoalContratoServidorSalario->setDado("vigencia"        ,$this->getVigenciaSalario());
        $obTPessoalContratoServidorSalario->setDado("cod_periodo_movimentacao"        ,$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
        $obErro = $obTPessoalContratoServidorSalario->inclusao($boTransacao);
    }
    if ( !$obErro->ocorreu() and $this->getInicioProgressao() ) {
        $obTPessoalContratoServidorInicioProgressao->setDado("cod_contrato"           ,$this->getCodContrato()        );
        $obTPessoalContratoServidorInicioProgressao->setDado("dt_inicio_progressao"   ,$this->getInicioProgressao()   );
        $obErro = $obTPessoalContratoServidorInicioProgressao->inclusao($boTransacao);
    }
    if ( !$obErro->ocorreu() ) {
        $stFiltro = " AND contrato_servidor_nomeacao_posse.cod_contrato = ".$this->getCodContrato();
        $obErro = $obTPessoalContratoServidorNomeacaoPosse->recuperaNomeacaoPosseDeContratos($rsAdmissaoNomeacaoPosse,$stFiltro,"",$boTransacao);
    }
    if ( !$obErro->ocorreu() ) {
        $obTPessoalContratoServidorNomeacaoPosse->setDado("cod_contrato"  ,$this->getCodContrato()    );
        $obTPessoalContratoServidorNomeacaoPosse->setDado("dt_nomeacao"   ,$this->getNomeacao()       );
        $obTPessoalContratoServidorNomeacaoPosse->setDado("dt_posse"      ,$this->getPosse()          );
        $obTPessoalContratoServidorNomeacaoPosse->setDado("dt_admissao"      ,$this->getAdmissao()          );
        $obErro = $obTPessoalContratoServidorNomeacaoPosse->inclusao($boTransacao);
    }
    if ( !$obErro->ocorreu() ) {
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoGeradoContratoServidor.class.php");
        $obTPessoalAssentamentoGeradoContratoServidor = new TPessoalAssentamentoGeradoContratoServidor();
        $stFiltro  = " AND assentamento_gerado_contrato_servidor.cod_contrato = ".$this->getCodContrato();
        $stFiltro .= " AND (assentamento_assentamento.cod_motivo = 11 ";
        $stFiltro .= "   OR assentamento_assentamento.cod_motivo = 12 ";
        $stFiltro .= "   OR assentamento_assentamento.cod_motivo = 13)";
        $obErro = $obTPessoalAssentamentoGeradoContratoServidor->recuperaRelacionamento($rsAssentamentoGerado,$stFiltro,"",$boTransacao);
    }
    //Caso uma das data (nomeacao,posse, admissão) for diferente do registro, ou seja, esteja sendo alterada
    //ou não tenha sido gerado um assentamento para o contrato em questão
    //será gerado um assentamento ou será feito uma alteração do assentamento já existente
    if( !$obErro->ocorreu() and
        ($rsAdmissaoNomeacaoPosse->getCampo("dt_nomeacao") != $this->getNomeacao()
      or $rsAdmissaoNomeacaoPosse->getCampo("dt_posse") != $this->getPosse()
      or $rsAdmissaoNomeacaoPosse->getCampo("dt_admissao") != $this->getAdmissao()
      or $rsAssentamentoGerado->getNumLinhas() < 0)){
        $inCodSubDivisao      = $this->obRPessoalCargo->roUltimoCargoSubDivisao->obRPessoalSubDivisao->getCodSubDivisao();
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoAssentamento.class.php");
        $obTPessoalAssentamentoAssentamento = new TPessoalAssentamentoAssentamento();
        $stFiltro  = " AND (assentamento_assentamento.cod_motivo = 11 ";
        $stFiltro .= "   OR assentamento_assentamento.cod_motivo = 12 ";
        $stFiltro .= "   OR assentamento_assentamento.cod_motivo = 13)";
        $stFiltro .= " AND assentamento_sub_divisao.cod_sub_divisao = ".$inCodSubDivisao;
        $obErro = $obTPessoalAssentamentoAssentamento->recuperaAssentamentoSubDivisao($rsAssentamento,$stFiltro,"",$boTransacao);
        if ( !$obErro->ocorreu() ) {
            include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalRegime.class.php");
            $obTPessoalRegime = new TPessoalRegime();
            $obTPessoalRegime->setDado("cod_regime",$this->obRPessoalRegime->getCodRegime());
            $obErro = $obTPessoalRegime->recuperaPorChave($rsRegime,$boTransacao);
        }
        if ( !$obErro->ocorreu() ) {
            include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalSubDivisao.class.php");
            $obTPessoalSubDivisao = new TPessoalSubDivisao();
            $obTPessoalSubDivisao->setDado("cod_sub_divisao",$inCodSubDivisao);
            $obTPessoalSubDivisao->setDado("cod_regime",$this->obRPessoalRegimeFuncao->getCodRegime());
            $obErro = $obTPessoalSubDivisao->recuperaPorChave($rsSubDivisao,$boTransacao);
        }
        if ( !$obErro->ocorreu() ) {
            $inCodCargo                = $this->obRPessoalCargo->getCodCargo();
            include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalCargo.class.php");
            $obTPessoalCargo = new TPessoalCargo();
            $obTPessoalCargo->setDado("cod_cargo",$inCodCargo);
            $obErro = $obTPessoalCargo->recuperaPorChave($rsCargo,$boTransacao);
        }
        $rsEspecialidade = new RecordSet;
        $inCodEspecialidade   = $this->obRPessoalCargo->roUltimoEspecialidade->getCodEspecialidade();
        if ( !$obErro->ocorreu() and $inCodEspecialidade != "") {
            include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalEspecialidade.class.php");
            $obTPessoalEspecialidade = new TPessoalEspecialidade();
            $obTPessoalEspecialidade->setDado("cod_especialidade",$inCodEspecialidade);
            $obTPessoalEspecialidade->setDado("cod_cargo",$inCodCargo);
            $obErro = $obTPessoalEspecialidade->recuperaPorChave($rsEspecialidade,$boTransacao);
        }
        if ( !$obErro->ocorreu() ) {
            include_once ( CAM_GRH_PES_MAPEAMENTO."FPessoalRegistrarEventoPorAssentamento.class.php" );
            $obFPessoalRegistrarEventoPorAssentamento = new FPessoalRegistrarEventoPorAssentamento();
            include_once(CAM_GRH_PES_NEGOCIO."RPessoalAssentamentoGeradoContratoServidor.class.php");
            $obRPessoalAssentametoGeradoContratoServidor = new RPessoalAssentamentoGeradoContratoServidor();
            $obRPessoalAssentametoGeradoContratoServidor->addRPessoalGeracaoAssentamento();
            while (!$rsAssentamento->eof()) {
                if ($rsAssentamento->getCampo("assentamento_automatico") == "t") {
                    switch ($rsAssentamento->getCampo("cod_motivo")) {
                        case 11:
                            $stDataInicioFim = $this->getNomeacao();
                            $stObservacao = "Nomeado sob o regime ".$rsRegime->getCampo("descricao")."/".$rsSubDivisao->getCampo("descricao")." e cargo ".$rsCargo->getCampo("descricao");
                            if ($rsEspecialidade->getNumLinhas() == 1) {
                                $stObservacao .= "/".$rsEspecialidade->getCampo("descricao");
                            }
                            break;
                        case 12:
                            $stDataInicioFim = $this->getPosse();
                            $stObservacao = "Posse sob o regime ".$rsRegime->getCampo("descricao")."/".$rsSubDivisao->getCampo("descricao")." e cargo ".$rsCargo->getCampo("descricao");
                            if ($rsEspecialidade->getNumLinhas() == 1) {
                                $stObservacao .= "/".$rsEspecialidade->getCampo("descricao");
                            }
                            break;
                        case 13:
                            $stDataInicioFim = $this->getAdmissao();
                            $stObservacao = "Admitido sob o regime ".$rsRegime->getCampo("descricao")."/".$rsSubDivisao->getCampo("descricao")." e cargo ".$rsCargo->getCampo("descricao");
                            if ($rsEspecialidade->getNumLinhas() == 1) {
                                $stObservacao .= "/".$rsEspecialidade->getCampo("descricao");
                            }
                            break;
                    }
                    $inCodAssentamentoGerado = 0;
                    while (!$rsAssentamentoGerado->eof()) {
                        if ($rsAssentamentoGerado->getCampo("cod_motivo") == $rsAssentamento->getCampo("cod_motivo")) {
                            $inCodAssentamentoGerado = $rsAssentamentoGerado->getCampo("cod_assentamento_gerado");
                            break;
                        }
                        $rsAssentamentoGerado->proximo();
                    }
                    $rsAssentamentoGerado->setPrimeiroElemento();

                    $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->obRPessoalContratoServidor->setCodContrato($this->getCodContrato());
                    $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->obRPessoalAssentamento->setCodAssentamento($rsAssentamento->getCampo("cod_assentamento"));
                    $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->setDescricaoObservacao($stObservacao);
                    $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->setPeriodoInicial($stDataInicioFim);
                    $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->setPeriodoFinal($stDataInicioFim);
                    $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->setCodNorma($this->obRNorma->getCodNorma());
                    if ($inCodAssentamentoGerado === 0) {
                        $obErro = $obRPessoalAssentametoGeradoContratoServidor->incluirAssentamentoGeradoContratoServidor($boTransacao);
                    } else {
                        $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->setCodAssentamentoGerado( $inCodAssentamentoGerado );
                        $obErro = $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->alterarGeracaoAssentamento($boTransacao);
                    }
                    if ($obErro->ocorreu()) {
                        break;
                    }
                }
                $rsAssentamento->proximo();
            }
        }
    }
    if ( !$obErro->ocorreu() ) {
        $inNumOcorrencia = $this->obRPessoalOcorrencia->getCodOcorrencia();
        $this->obRPessoalOcorrencia->setCodOcorrencia("");
        $this->obRPessoalOcorrencia->setNumOcorrencia($inNumOcorrencia);
        $obErro = $this->obRPessoalOcorrencia->listarOcorrencia($rsOcorrencia,$boTransacao);
        if ( !$obErro->ocorreu() ) {
            $this->obRPessoalOcorrencia->setCodOcorrencia( $rsOcorrencia->getCampo('cod_ocorrencia') );
            $obTPessoalContratoServidorOcorrencia->setDado('cod_ocorrencia',$this->obRPessoalOcorrencia->getCodOcorrencia());
            $obTPessoalContratoServidorOcorrencia->setDado('cod_contrato',$this->getCodContrato());
            $obErro = $obTPessoalContratoServidorOcorrencia->inclusao( $boTransacao );
        }
    }
    if ( !$obErro->ocorreu() ) {//8
        $obTPessoalContratoServidorOrgao->setDado("cod_contrato",   $this->getCodContrato() );
        $obTPessoalContratoServidorOrgao->setDado("cod_orgao", $this->obROrganogramaOrgao->getCodOrgao());
        $obErro = $this->listarContratoServidorLotacao($rsOrgao,$boTransacao);
        if ( $rsOrgao->getNumLinhas() < 0 ) {
            $obErro = $obTPessoalContratoServidorOrgao->inclusao( $boTransacao );
        }
    }
    if ( !$obErro->ocorreu() ) {//7
        if ( $this->obROrganogramaLocal->getCodLocal() ) {
            $obTPessoalContratoServidorLocal->setDado("cod_contrato", $this->getCodContrato() );
            $obTPessoalContratoServidorLocal->setDado("cod_local"   , $this->obROrganogramaLocal->getCodLocal() );
            $obErro = $obTPessoalContratoServidorLocal->inclusao( $boTransacao );
        } else {
            $rsLocal = new RecordSet();
            $obErro = $obTPessoalContratoServidorLocal->recuperaTodos($rsLocal, " WHERE cod_contrato = ".$this->getCodContrato(), "", $boTransacao);
            if ($obErro->ocorreu()) {
                break;
            }
            while (!$rsLocal->eof()) {
                $obTPessoalContratoServidorLocalHistorico->setDado('cod_contrato', $this->getCodContrato());
                $obTPessoalContratoServidorLocalHistorico->setDado('cod_local',    $rsLocal->getCampo('cod_local'));
                $obTPessoalContratoServidorLocalHistorico->setDado('timestamp',    $rsLocal->getCampo('timestamp'));
                $obErro = $obTPessoalContratoServidorLocalHistorico->inclusao( $boTransacao );
                if ($obErro->ocorreu()) {
                    break;
                }
                $rsLocal->proximo();
            }

            if (!$obErro->ocorreu()) {
                $obTPessoalContratoServidorLocal = new TPessoalContratoServidorLocal;
                $tempComp = $obTPessoalContratoServidorLocal->getComplementoChave();
                $tempCod  = $obTPessoalContratoServidorLocal->getCampoCod();
                $obTPessoalContratoServidorLocal->setComplementoChave('');
                $obTPessoalContratoServidorLocal->setCampoCod('cod_contrato');
                $obTPessoalContratoServidorLocal->setDado("cod_contrato",$this->getCodContrato() );
                $obErro = $obTPessoalContratoServidorLocal->exclusao( $boTransacao );
                $obTPessoalContratoServidorLocal->setComplementoChave($tempComp);
                $obTPessoalContratoServidorLocal->setCampoCod($tempCod);
            }
        }
    }

    $arPrevidencia = Sessao::read("PREVIDENCIA");
    if ( !$obErro->ocorreu() and is_array($arPrevidencia) ) {//6
        $obErro = $obTPessoalContratoServidorPrevidencia->recuperaNow3($now,$boTransacao);
        if ( !$obErro->ocorreu() ) {
            $stCodPrevidencia = "";
            while ( list( $arId, $inCodPrevidencia ) = each( $arPrevidencia ) ) {
                $stCodPrevidencia .= $inCodPrevidencia.",";
            }
            $stCodPrevidencia = substr($stCodPrevidencia,0,strlen($stCodPrevidencia)-1);
            $stFiltro  = " AND contrato_servidor_previdencia.cod_contrato = ".$this->getCodContrato();
            $stFiltro .= " AND contrato_servidor_previdencia.bo_excluido = false";
            $stFiltro .= ( count($arPrevidencia) ) ? " AND contrato_servidor_previdencia.cod_previdencia not in (".$stCodPrevidencia.")" : "";
            $obErro = $obTPessoalContratoServidorPrevidencia->recuperaRelacionamento($rsContratoPrevidencia,$stFiltro,$stOrdem,$boTransacao);
        }
        if ( !$obErro->ocorreu() ) {
            while (!$rsContratoPrevidencia->eof()) {
                $obTPessoalContratoServidorPrevidencia->setDado("cod_contrato"      , $rsContratoPrevidencia->getCampo('cod_contrato'));
                $obTPessoalContratoServidorPrevidencia->setDado("cod_previdencia"   , $rsContratoPrevidencia->getCampo('cod_previdencia'));
                $obTPessoalContratoServidorPrevidencia->setDado("timestamp"         , $now);
                $obTPessoalContratoServidorPrevidencia->setDado("bo_excluido"       , true);
                $obErro = $obTPessoalContratoServidorPrevidencia->inclusao( $boTransacao );
                if ( $obErro->ocorreu() ) {
                    break;
                }
                $rsContratoPrevidencia->proximo();
            }
        }
        if ( !$obErro->ocorreu() ) {
            reset($arPrevidencia);
            while ( list( $arId, $inCodPrevidencia ) = each( $arPrevidencia ) ) {
                $obTPessoalContratoServidorPrevidencia->setDado("cod_contrato"      , $this->getCodContrato() );
                $obTPessoalContratoServidorPrevidencia->setDado("cod_previdencia"   , $inCodPrevidencia );
                $obTPessoalContratoServidorPrevidencia->setDado("timestamp"         , $now);
                $obTPessoalContratoServidorPrevidencia->setDado("bo_excluido"       , false);
                $obErro = $obTPessoalContratoServidorPrevidencia->inclusao( $boTransacao );
                if ( $obErro->ocorreu() ) {
                    break;
                }
            }
        }
    }
    if ( !$obErro->ocorreu() ) {//5
        foreach ($this->obRPessoalCargo->obRFolhaPagamentoPadrao->arRFolhaPagamentoNivelPadrao as $obRNivelPadrao) {
            if ( $obRNivelPadrao->getCodNivelPadrao() ) {
                $obRNivelPadrao->listarNivelPadrao( $rsNivelPadrao,$boTransacao );
                $inCodNivelPadrao = $obRNivelPadrao->getCodNivelPadrao();
                $stTimestamp      = $rsNivelPadrao->getCampo('timestamp_padrao');
                $inCodPadrao      = $rsNivelPadrao->getCampo('cod_padrao');
                break;
            }
        }
        if ($inCodNivelPadrao) {
            $obTPessoalContratoServidorNivelPadrao->setDado("cod_contrato" , $this->getCodContrato() );
            $obTPessoalContratoServidorNivelPadrao->setDado("cod_nivel_padrao", $inCodNivelPadrao);
            $obTPessoalContratoServidorNivelPadrao->setDado("timestamp_nivel_padrao"  , $stTimestamp);
            $obTPessoalContratoServidorNivelPadrao->setDado("cod_padrao"              , $inCodPadrao);
            $obTPessoalContratoServidorNivelPadrao->setDado("cod_periodo_movimentacao"        ,$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
            $obTPessoalContratoServidorNivelPadrao->recuperaPorChave( $rsNivelPadrao, $boTransacao );
            if ( $rsNivelPadrao->getNumLinhas() < 0 ) {
                $obErro = $obTPessoalContratoServidorNivelPadrao->inclusao( $boTransacao );
            }
        }
    }
    if ( !$obErro->ocorreu() ) {//4
        if ( $this->obRPessoalCargo->obRFolhaPagamentoPadrao->getCodPadrao() ) {
            $obTPessoalContratoServidorPadrao->setDado("cod_contrato" , $this->getCodContrato() );
            $obTPessoalContratoServidorPadrao->setDado("cod_padrao", $this->obRPessoalCargo->obRFolhaPagamentoPadrao->getCodPadrao());
            $obErro = $obTPessoalContratoServidorPadrao->inclusao( $boTransacao );
        } else {
            $obTPessoalContratoServidorPadrao->setDado("cod_contrato" , $this->getCodContrato() );
            $obTPessoalContratoServidorPadrao->setDado("cod_padrao", "0");
            $obErro = $obTPessoalContratoServidorPadrao->inclusao( $boTransacao );
        }
    }

    if ( !$obErro->ocorreu() ) {
        $stFiltro  = " WHERE contrato_servidor_forma_pagamento.cod_forma_pagamento = ".$this->obRPessoalFormaPagamento->getCodFormaPagamento();
        $stFiltro .= "   AND contrato_servidor_forma_pagamento.cod_contrato = ".$this->getCodContrato();
        $obErro = $obTPessoalContratoServidorFormaPagamento->recuperaUltimaFormaPagamento($rsContratoServidorFormaPagamento, $stFiltro, "", $boTransacao);

        if ($rsContratoServidorFormaPagamento->getCampo("cod_forma_pagamento") != $this->obRPessoalFormaPagamento->getCodFormaPagamento()) {
            $obTPessoalContratoServidorFormaPagamento->setDado("cod_contrato", $this->getCodContrato());
            $obTPessoalContratoServidorFormaPagamento->setDado("cod_forma_pagamento", $this->obRPessoalFormaPagamento->getCodFormaPagamento());
            $obErro = $obTPessoalContratoServidorFormaPagamento->inclusao($boTransacao);
        }
    }

    if ( !$obErro->ocorreu() ) {//2
        $stFiltro = " AND cs.cod_contrato = ". $this->getCodContrato() ."";
        $obErro = $obTPessoalContratoServidor->recuperaRelacionamentoCargo( $rsCargoAlterar,$stFiltro, "", $boTransacao );
        if( $rsCargoAlterar->getCampo('cod_funcao')               != $_POST['inCodFuncao'] or
            $rsCargoAlterar->getCampo('cod_especialidade_funcao') != $_POST['inCodEspecialidadeFuncao'] or
            $rsCargoAlterar->getCampo('cod_sub_divisao_funcao')   != $_POST['inCodSubDivisaoFuncao'] ) {
            $obErro = $this->alterarVaga( $boTransacao );
        }
        //Dados bancarios Salario
        if ( !$obErro->ocorreu() and $this->obRMonetarioBancoSalario->getNumBanco() ) {
            if ( $this->obRMonetarioAgenciaSalario->getCodAgencia() == "" ) {
                $obErro->setDescricao("Você deve selecionar uma agencia bancária para o Informações Salariais na aba contrato!");
            }
            if ( $this->getContaCorrenteSalario() == "" ) {
                $obErro->setDescricao("Você deve informar uma conta para crédito para o Informações Salariais na aba contrato!");
            }
            if ( !$obErro->ocorreu() ) {
                $this->obRMonetarioBancoSalario->setCodBanco("");
                $obErro = $this->obRMonetarioBancoSalario->listarBanco($rsBanco,$boTransacao);
                $this->obRMonetarioBancoSalario->setCodBanco($rsBanco->getCampo('cod_banco'));
            }
            if ( !$obErro->ocorreu() ) {
                $this->obRMonetarioAgenciaSalario->setNumAgencia($this->obRMonetarioAgenciaSalario->getCodAgencia());
                $this->obRMonetarioAgenciaSalario->setCodAgencia('');
                $this->obRMonetarioAgenciaSalario->obRMONBanco = $this->obRMonetarioBancoSalario;
                $obErro = $this->obRMonetarioAgenciaSalario->listarAgencia($rsAgenciaBancaria, $boTransacao);
                $this->obRMonetarioAgenciaSalario->setCodAgencia( $rsAgenciaBancaria->getCampo('cod_agencia') );
            }
            if ( !$obErro->ocorreu() ) {
                $obTPessoalContratoServidorContaSalario->setDado("cod_contrato",        $this->getCodContrato()         );
                $obErro = $obTPessoalContratoServidorContaSalario->exclusao( $boTransacao );
            }
            if ( !$obErro->ocorreu() ) {
                $obTPessoalContratoServidorContaSalario->setDado("cod_banco",   $this->obRMonetarioBancoSalario->getCodBanco()      );
                $obTPessoalContratoServidorContaSalario->setDado("cod_agencia", $this->obRMonetarioAgenciaSalario->getCodAgencia()  );
                $obTPessoalContratoServidorContaSalario->setDado("nr_conta",     $this->getContaCorrenteSalario() );
                $obErro = $obTPessoalContratoServidorContaSalario->inclusao( $boTransacao );
            }
        }//fim do if dados SALARIO
        //Dados Bancarios FGTS
        if ( !$obErro->ocorreu() ) {
            $obTPessoalContratoServidorContaFgts->setDado("cod_contrato",        $this->getCodContrato()         );
            $obErro = $obTPessoalContratoServidorContaFgts->exclusao( $boTransacao );
        }
        if ( !$obErro->ocorreu() and $this->obRMonetarioBancoFGTS->getNumBanco() ) {
            if ( $this->obRMonetarioAgenciaFGTS->getCodAgencia() == "" ) {
                $obErro->setDescricao("Você deve selecionar uma agencia bancária para o FGTS na aba contrato!");
            }
            if ( $this->getContaCorrenteFgts() == "" ) {
                $obErro->setDescricao("Você deve informar uma conta para crédito para o FGTS na aba contrato!");
            }
            if ( !$obErro->ocorreu() ) {
                $this->obRMonetarioAgenciaFGTS->setNumAgencia( $this->obRMonetarioAgenciaFGTS->getCodAgencia() );
                $this->obRMonetarioAgenciaFGTS->setCodAgencia("");
                $obErro = $this->obRMonetarioAgenciaFGTS->listarAgencia($rsAgenciaBancaria, $boTransacao);
                $this->obRMonetarioAgenciaFGTS->setCodAgencia( $rsAgenciaBancaria->getCampo('cod_agencia') );
            }
            if ( !$obErro->ocorreu() ) {
                $this->obRMonetarioBancoFGTS->setCodBanco("");
                $obErro = $this->obRMonetarioBancoFGTS->listarBanco($rsBanco,$boTransacao);
                $this->obRMonetarioBancoFGTS->setCodBanco($rsBanco->getCampo('cod_banco'));
            }
            if ( !$obErro->ocorreu() ) {
                $obTPessoalContratoServidorContaFgts->setDado("cod_banco",      $this->obRMonetarioBancoFGTS->getCodBanco()         );
                $obTPessoalContratoServidorContaFgts->setDado("cod_agencia",    $this->obRMonetarioAgenciaFGTS->getCodAgencia()     );
                $obTPessoalContratoServidorContaFgts->setDado("nr_conta",       $this->getContaCorrenteFgts()  );
                $obErro = $obTPessoalContratoServidorContaFgts->inclusao( $boTransacao );
            }
        }//fim do if dados FGTS
        if ( !$obErro->ocorreu() ) {
            //O Restante dos valores vem setado da pagina de processamento
            //$arChaveAtributoCandidato =  array( "cod_contrato" => $this->getCodContrato() );
            //$this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoCandidato );
            include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalAtributoContratoServidorValor.class.php");
            $obTPessoalAtributoContratoServidorValor = new TPessoalAtributoContratoServidorValor();
            for ($inIndex=0;$inIndex<count($this->obRCadastroDinamico->arAtributosDinamicos);$inIndex++) {
                $obRAtributoDinamico = $this->obRCadastroDinamico->arAtributosDinamicos[$inIndex];
                $obTPessoalAtributoContratoServidorValor->setDado("cod_contrato",$this->getCodContrato());
                $obTPessoalAtributoContratoServidorValor->setDado("cod_atributo",$obRAtributoDinamico->getCodAtributo());
                $obTPessoalAtributoContratoServidorValor->setDado("cod_cadastro",5);
                $obTPessoalAtributoContratoServidorValor->setDado("valor",$obRAtributoDinamico->getValor());
                $obTPessoalAtributoContratoServidorValor->setDado("cod_modulo",22);
                $obErro = $obTPessoalAtributoContratoServidorValor->inclusao($boTransacao);
                if ($obErro->ocorreu()) {
                    break;
                }
            }

//            $obErro = $this->obRCadastroDinamico->salvarValores( $boTransacao );
        }
    }//2
    if ( !$obErro->ocorreu() ) {

        $obTPessoalContratoServidorSubDivisaoFuncao->setDado("cod_contrato",$this->getCodContrato());
        $obTPessoalContratoServidorSubDivisaoFuncao->setDado("cod_sub_divisao",$this->obRPessoalCargoFuncao->roUltimoCargoSubDivisao->obRPessoalSubDivisao->getCodSubDivisao());
        $obErro = $obTPessoalContratoServidorSubDivisaoFuncao->inclusao($boTransacao);
    }
    if ( !$obErro->ocorreu() ) {
        $obTPessoalContratoServidorRegimeFuncao->setDado("cod_contrato",$this->getCodContrato());
        $obTPessoalContratoServidorRegimeFuncao->setDado("cod_regime",$this->obRPessoalRegimeFuncao->getCodRegime());
        $obErro = $obTPessoalContratoServidorRegimeFuncao->inclusao($boTransacao);
    }
    if ( $this->getCodConselho() or $this->getNroConselho() or $this->getValidadeConselho() ) {
        if ( !$this->getCodConselho() ) {
            $obErro->setDescricao("Você deve selecionar o conselho profissional na aba contrato!");
        }
        if ( !$this->getNroConselho() ) {
            $obErro->setDescricao("Você deve informar o número do conselho profissional na aba contrato!");
        }
        if ( !$this->getValidadeConselho() ) {
            $obErro->setDescricao("Você deve informar a data de validade do conselho profissional na aba contrato!");
        }
        if ( !$obErro->ocorreu() ) {
            $this->consultarContratoServidorConselho( $rsContratoServidorConselho, "", "", $boTransacao );
            $obTPessoalContratoServidorConselho->setDado("cod_contrato",$this->getCodContrato());
            $obTPessoalContratoServidorConselho->setDado("cod_conselho",$this->getCodConselho());
            $obTPessoalContratoServidorConselho->setDado("nr_conselho",$this->getNroConselho());
            $obTPessoalContratoServidorConselho->setDado("dt_validade",$this->getValidadeConselho());
            if ($rsContratoServidorConselho->getNumLinhas() > 0)
                $obErro = $obTPessoalContratoServidorConselho->alteracao($boTransacao);
            else
                $obErro = $obTPessoalContratoServidorConselho->inclusao($boTransacao);
        }
    } else {
        $this->consultarContratoServidorConselho( $rsContratoServidorConselho, "", "", $boTransacao );
        if ($rsContratoServidorConselho->getNumLinhas() > 0) {
            $obTPessoalContratoServidorConselho->setDado("cod_contrato",$this->getCodContrato());
            $obErro = $obTPessoalContratoServidorConselho->exclusao($boTransacao);
        }
    }

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTPessoalContratoServidor );

    return $obErro;
}

function listarContratoServidorContaFGTS(&$rsRecordSet,$boTransacao="")
{
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorContaFgts.class.php");
    $obTPessoalContratoServidorContaFGTS =  new TPessoalContratoServidorContaFGTS;
    $stFiltro = "";
    $stOrder  = "";
    if ( $this->getCodContrato() ) {
        $stFiltro .= " AND cod_contrato = ".$this->getCodContrato();
    }
    $stFiltro = ($stFiltro) ?" WHERE ".substr($stFiltro,4,strlen($stFiltro)) : "";
    $obErro = $obTPessoalContratoServidorContaFGTS->recuperaTodos($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

function listarContratoServidorContaSalario(&$rsRecordSet,$boTransacao="")
{
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorContaSalario.class.php");
    $obTPessoalContratoServidorContaSalario =  new TPessoalContratoServidorContaSalario;
    $stFiltro = "";
    $stOrder  = "";
    if ( $this->getCodContrato() ) {
        $stFiltro .= " AND cod_contrato = ".$this->getCodContrato();
    }
    $stFiltro = ($stFiltro) ?" WHERE ".substr($stFiltro,4,strlen($stFiltro)) : "";
    $obErro = $obTPessoalContratoServidorContaSalario->recuperaTodos($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

function listarContratoServidorNivelPadrao(&$rsRecordSet,$boTransacao="")
{
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorNivelPadrao.class.php");
    $obTPessoalContratoServidorNivelPadrao =  new TPessoalContratoServidorNivelPadrao;
    $stFiltro = "";
    $stOrder  = "";
    if ( $this->getCodContrato() ) {
        $stFiltro .= " AND cod_contrato = ".$this->getCodContrato();
    }
    $stFiltro = ($stFiltro) ?" WHERE ".substr($stFiltro,4,strlen($stFiltro)) : "";
    $obErro = $obTPessoalContratoServidorNivelPadrao->recuperaTodos($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

function listarContratoServidorExameMedico(&$rsRecordSet,$boTransacao="")
{
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorExameMedico.class.php");
    $obTPessoalContratoServidorExameMedico =  new TPessoalContratoServidorExameMedico;
    $stFiltro = "";
    $stOrder  = "";
    if ( $this->getCodContrato() ) {
        $stFiltro .= " AND cod_contrato = ".$this->getCodContrato();
    }
    $stFiltro = ($stFiltro) ?" WHERE ".substr($stFiltro,4,strlen($stFiltro)) : "";
    $obErro = $obTPessoalContratoServidorExameMedico->recuperaTodos($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

function listarContratoServidorLocal(&$rsRecordSet,$boTransacao="")
{
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorLocal.class.php");
    $obTPessoalContratoServidorLocal =  new TPessoalContratoServidorLocal;
    $stFiltro = "";
    $stOrder  = "";
    if ( $this->getCodContrato() ) {
        $stFiltro .= " AND cont_local.cod_contrato = ".$this->getCodContrato();
    }
    $obErro = $obTPessoalContratoServidorLocal->recuperaRelacionamento($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

function listarContratoServidorLotacao(&$rsRecordSet,$boTransacao="")
{
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorOrgao.class.php");
    $obTPessoalContratoServidorOrgao =  new TPessoalContratoServidorOrgao;
    $stFiltro = "";
    $stOrder  = "";
    if ( $this->getCodContrato() ) {
        $stFiltro .= " AND contrato.cod_contrato = ".$this->getCodContrato();
    }
    if ( $this->getRegistro() ) {
        $stFiltro .= " AND contrato.registro = ".$this->getRegistro();
    }
    if ( $this->obROrganogramaOrgao->getCodOrgao() ) {
        $stFiltro .= " AND contrato_servidor_orgao.cod_orgao = ".$this->obROrganogramaOrgao->getCodOrgao();
    }
    $obErro = $obTPessoalContratoServidorOrgao->recuperaRelacionamento($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

function listarContratoServidorLotacaoComSubDivisaoAssentamento(&$rsRecordSet, $inCodAssentamento,$boTransacao="")
{
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorOrgao.class.php");
    $obTPessoalContratoServidorOrgao =  new TPessoalContratoServidorOrgao;
    $stFiltro = "";
    $stOrder  = "";
    if ( $this->getCodContrato() ) {
        $stFiltro .= " AND contrato.cod_contrato = ".$this->getCodContrato();
    }
    if ( $this->getRegistro() ) {
        $stFiltro .= " AND contrato.registro = ".$this->getRegistro();
    }
    if ( $this->obROrganogramaOrgao->getCodOrgao() ) {
        $stFiltro .= " AND contrato_servidor_orgao.cod_orgao = ".$this->obROrganogramaOrgao->getCodOrgao();
    }
    if ($inCodAssentamento) {
        $stFiltro .= " AND assentamento_sub_divisao.cod_assentamento = ".$inCodAssentamento;
    }
    $obErro = $obTPessoalContratoServidorOrgao->recuperaContratoServidorLotacaoComSubDivisaoAssentamento($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

function listarContratoServidorSalario(&$rsRecordSet,$boTransacao="")
{
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorSalario.class.php");
    $obTPessoalContratoServidorSalario =  new TPessoalContratoServidorSalario;
    $stFiltro = "";
    $stOrder  = "";
    if ( $this->getCodContrato() ) {
        $stFiltro .= " AND salario.cod_contrato = ".$this->getCodContrato();
    }
    $obErro = $obTPessoalContratoServidorSalario->recuperaRelacionamento($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

function listarContratoServidorInicioProgressao(&$rsRecordSet,$boTransacao="")
{
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorInicioProgressao.class.php");
    $obTPessoalContratoServidorInicioProgressao =  new TPessoalContratoServidorInicioProgressao;
    $stFiltro = "";
    $stOrder  = "";
    if ( $this->getCodContrato() ) {
        $stFiltro .= " AND inicio_progressao.cod_contrato = ".$this->getCodContrato();
    }
    $obErro = $obTPessoalContratoServidorInicioProgressao->recuperaRelacionamento($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

function consultarContratoServidorConselho(&$rsRecordSet, $stFiltro = "", $stOrder = "", $boTransacao = "")
{
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorConselho.class.php");
    $obTPessoalContratoServidorConselho =  new TPessoalContratoServidorConselho;
    $stFiltro = "";
    if ( $this->getCodContrato() ) {
        $stFiltro .= " Where cod_contrato = ".$this->getCodContrato()."";
    }
    $obErro = $obTPessoalContratoServidorConselho->recuperaTodos($rsRecordSet, $stFiltro, $stOrder, $boTransacao);

    return $obErro;
}

function consultarContratoServidorSubDivisaoFuncao(&$rsRecordSet, $stFiltro = "", $stOrder = "", $boTransacao = "")
{
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorSubDivisaoFuncao.class.php");
    $obTPessoalContratoServidorSubDivisaoFuncao =  new TPessoalContratoServidorSubDivisaoFuncao;
    if ( $this->getCodContrato() ) {
        $stFiltro .= " AND pf.cod_contrato = ".$this->getCodContrato()."";
    }
    $obErro = $obTPessoalContratoServidorSubDivisaoFuncao->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao);

    return $obErro;
}

function consultarContratoServidorEspecialidadeFuncao(&$rsRecordSet, $stFiltro = "", $stOrder = "", $boTransacao = "")
{
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorEspecialidadeFuncao.class.php");
    $obTPessoalContratoServidorEspecialidadeFuncao =  new TPessoalContratoServidorEspecialidadeFuncao;
    if ( $this->getCodContrato() ) {
        $stFiltro .= " AND contrato_servidor_especialidade_funcao.cod_contrato = ".$this->getCodContrato()."";
    }
    $obErro = $obTPessoalContratoServidorEspecialidadeFuncao->recuperaUltimaEspecialidade( $rsRecordSet, $stFiltro, $stOrder, $boTransacao);

    return $obErro;
}

function consultarContratoServidorRegimeFuncao(&$rsRecordSet, $stFiltro = "", $stOrder = "", $boTransacao = "")
{
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorRegimeFuncao.class.php");
    $obTPessoalContratoServidorRegimeFuncao =  new TPessoalContratoServidorRegimeFuncao;
    if ( $this->getCodContrato() ) {
        $stFiltro .= " AND pf.cod_contrato = ".$this->getCodContrato()."";
    }
    if ( $this->obRPessoalRegimeFuncao->getCodRegime() ) {
        $stFiltro .= " AND cod_regime = ".$this->obRPessoalRegimeFuncao->getCodRegime()."";
    }
    $obErro = $obTPessoalContratoServidorRegimeFuncao->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao);

    return $obErro;
}

function listarContratoServidor(&$rsRecordSet, $stFiltro, $stOrdem= "", $boTransacao = "")
{
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidor.class.php");
    $obTPessoalContratoServidor =  new TPessoalContratoServidor;
    $stFiltro .= " AND numcgm =".$this->roPessoalServidor->obRCGMPessoaFisica->getNumCGM();
    $obErro = $obTPessoalContratoServidor->recuperaRelacionamento( $rsRecordSet, $stFiltro, "", $boTransacao);

    return $obErro;
}

function listarDadosAbaContratoServidor(&$rsRecordSet,$boTransacao = "")
{
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidor.class.php");
    $obTPessoalContratoServidor =  new TPessoalContratoServidor;
    $obTPessoalContratoServidor->setDado( 'cod_contrato', $this->getCodContrato() );

    if ( $this->getRegistro() ) {
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");
        $obTPessoalContrato =  new TPessoalContrato;
        $stFiltro = " WHERE registro = ".$this->getRegistro();
        $obTPessoalContrato->recuperaTodos( $rsContrato, $stFiltro );

        if ( $rsContrato->getNumLinhas() !== -1 ) {
            $this->setCodContrato( $rsContrato->getCampo("cod_contrato") );
        }
    }

    $stFiltro = "";
    $stOrder  = "";
    if ( $this->getCodContrato() ) {
        $stFiltro = " AND cs.cod_contrato = ".$this->getCodContrato();
    }

    $obErro = $obTPessoalContratoServidor->recuperaDadosAbaContratoServidor( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

function listarContratosServidorResumido(&$rsRecordSet,$boTransacao = "")
{
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidor.class.php");
    $obTPessoalContratoServidor =  new TPessoalContratoServidor;
    if ( $this->roPessoalServidor->obRCGMPessoaFisica->getNumCGM() ) {
        $stFiltro .= " AND cgm.numcgm =".$this->roPessoalServidor->obRCGMPessoaFisica->getNumCGM();
    }
    if ( $this->getCodContrato() ) {
        $stFiltro .= " AND pcs.cod_contrato = ".$this->getCodContrato()."";
    }
    if ( $this->getRegistro() != "" ) {
        if ( strpos($this->getRegistro(),'between') ) {
            $stFiltro .= $this->getRegistro();
        } else {
            $stFiltro .= " AND pc.registro = ".$this->getRegistro();
        }
    }
    if ( $this->obRPessoalCargo->getCodCargo() ) {
        $stFiltro .= " AND pcs.cod_cargo = ".$this->obRPessoalCargo->getCodCargo();
    }
    if ( is_object($this->obRPessoalCargo->roUltimoEspecialidade) ) {
        if ( $this->obRPessoalCargo->roUltimoEspecialidade->getCodEspecialidade() ) {
            $stFiltro .= " AND esp_cargo.cod_especialidade = ".$this->obRPessoalCargo->roUltimoEspecialidade->getCodEspecialidade();
        }
    }
    if ( is_object($this->obRPessoalCargoFuncao->roUltimoEspecialidade) ) {
        if ( $this->obRPessoalCargoFuncao->roUltimoEspecialidade->getCodEspecialidade() ) {
            $stFiltro .= " AND esp_funcao.cod_especialidade = ".$this->obRPessoalCargoFuncao->roUltimoEspecialidade->getCodEspecialidade();
        }
    }
    if ( $this->obRPessoalCargo->obRFolhaPagamentoPadrao->getCodPadrao() ) {
        $stFiltro .= " AND pcsp.cod_padrao = ".$this->obRPessoalCargo->obRFolhaPagamentoPadrao->getCodPadrao();
    }
    if ( $this->obROrganogramaOrgao->getCodOrgao() ) {
        $stFiltro .= " AND pcso.cod_orgao = ".$this->obROrganogramaOrgao->getCodOrgao();
    }
    if ( $this->obROrganogramaLocal->getCodLocal() ) {
        $stFiltro .= " AND pcsl.cod_local = ".$this->obROrganogramaLocal->getCodLocal();
    }
    $stOrder = " servidor";
    $obErro = $obTPessoalContratoServidor->recuperaContratosServidorResumido( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

function listarContratosServidorRelatorio(&$rsRecordSet,$boTransacao = "")
{
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidor.class.php");
    $obTPessoalContratoServidor =  new TPessoalContratoServidor;
    if ( $this->roPessoalServidor->obRCGMPessoaFisica->getNumCGM() ) {
        $stFiltro .= " AND cgm.numcgm =".$this->roPessoalServidor->obRCGMPessoaFisica->getNumCGM();
    }
    if ( $this->getCodContrato() ) {
        $stFiltro .= " AND pcs.cod_contrato = ".$this->getCodContrato()."";
    }
    if ( $this->getRegistro() != "" ) {
        if ( strpos($this->getRegistro(),'between') ) {
            $stFiltro .= $this->getRegistro();
        } else {
            $stFiltro .= " AND pc.registro = ".$this->getRegistro();
        }
    }
    if ( $this->obRPessoalCargo->getCodCargo() ) {
        $stFiltro .= " AND pcs.cod_cargo = ".$this->obRPessoalCargo->getCodCargo();
    }
    if ( is_object($this->obRPessoalCargo->roUltimoEspecialidade) ) {
        if ( $this->obRPessoalCargo->roUltimoEspecialidade->getCodEspecialidade() ) {
            $stFiltro .= " AND esp_cargo.cod_especialidade = ".$this->obRPessoalCargo->roUltimoEspecialidade->getCodEspecialidade();
        }
    }
    if ( is_object($this->obRPessoalCargoFuncao->roUltimoEspecialidade) ) {
        if ( $this->obRPessoalCargoFuncao->roUltimoEspecialidade->getCodEspecialidade() ) {
            $stFiltro .= " AND esp_funcao.cod_especialidade = ".$this->obRPessoalCargoFuncao->roUltimoEspecialidade->getCodEspecialidade();
        }
    }
    if ( $this->obRPessoalCargo->obRFolhaPagamentoPadrao->getCodPadrao() ) {
        $stFiltro .= " AND pcsp.cod_padrao = ".$this->obRPessoalCargo->obRFolhaPagamentoPadrao->getCodPadrao();
    }
    if ( $this->obROrganogramaOrgao->getCodOrgao() ) {
        $stFiltro .= " AND pcso.cod_orgao = ".$this->obROrganogramaOrgao->getCodOrgao();
    }
    if ( $this->obROrganogramaLocal->getCodLocal() ) {
        $stFiltro .= " AND pcsl.cod_local = ".$this->obROrganogramaLocal->getCodLocal();
    }
    $stOrder = " servidor";
    $obErro = $obTPessoalContratoServidor->recuperaContratosServidorRelatorio( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}



function alterarVaga($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidor.class.php");
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalCargoSubDivisao.class.php");
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalEspecialidadeSubDivisao.class.php");
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorFuncao.class.php");
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorEspecialidadeFuncao.class.php");
    $obTPessoalContratoServidor                     =  new TPessoalContratoServidor;
    $obTPessoalCargoSubDivisao                      =  new TPessoalCargoSubDivisao;
    $obTPessoalContratoServidorFuncao               =  new TPessoalContratoServidorFuncao;
    $obTPessoalEspecialidadeSubDivisao              =  new TPessoalEspecialidadeSubDivisao;
    $obTPessoalContratoServidorEspecialidadeFuncao  =  new TPessoalContratoServidorEspecialidadeFuncao;

    //CARGO
    $inCodRegime              = $this->obRPessoalRegime->getCodRegime();
    $inCodSubDivisao          = $this->obRPessoalCargo->roUltimoCargoSubDivisao->obRPessoalSubDivisao->getCodSubDivisao();
    $inCodCargo               = $this->obRPessoalCargo->getCodCargo();
    $inCodEspecialidade       = $this->obRPessoalCargo->roUltimoEspecialidade->getCodEspecialidade();

    //FUNÇÃO
    $inCodRegimeFuncao        = $this->obRPessoalRegimeFuncao->getCodRegime();
    $inCodSubDivisaoFuncao    = $this->obRPessoalCargoFuncao->roUltimoEspecialidade->roPessoalCargo->roUltimoCargoSubDivisao->obRPessoalSubDivisao->getCodSubDivisao();
    $inCodFuncao              = $this->obRPessoalCargoFuncao->roUltimoEspecialidade->roPessoalCargo->getCodCargo();
    $inCodEspecialidadeFuncao = $this->obRPessoalCargoFuncao->roUltimoEspecialidade->getCodEspecialidade();

    if ($inCodEspecialidadeFuncao != "") {
        if ($inCodRegimeFuncao         != $inCodRegime or
           $inCodSubDivisao           != $inCodSubDivisaoFuncao or
           $inCodEspecialidadeFuncao  != $inCodEspecialidade) {

            $obTPessoalEspecialidadeSubDivisao->setDado("cod_regime",$inCodRegimeFuncao);
            $obTPessoalEspecialidadeSubDivisao->setDado("cod_sub_divisao",$inCodSubDivisaoFuncao);
            $obTPessoalEspecialidadeSubDivisao->setDado("cod_especialidade",$inCodEspecialidadeFuncao);
            $obTPessoalEspecialidadeSubDivisao->setDado("entidade",Sessao::getEntidade());
            $obErro = $obTPessoalEspecialidadeSubDivisao->getVagasDisponiveisEspecialidade($rsVargas,"","",$boTransacao);
            if (!$obErro->ocorreu() AND $rsVargas->getCampo("vagas") == 0) {
                $obErro->setDescricao ("Especialidade selecionada não possui vagas disponíveis!");
            }
        }

        if (!$obErro->ocorreu()) {
            $obTPessoalContratoServidorFuncao->setDado( "cod_contrato"    , $this->getCodContrato()       );
            $obTPessoalContratoServidorFuncao->setDado( "cod_cargo"       , $inCodFuncao                  );
            $obTPessoalContratoServidorFuncao->setDado( "vigencia"        , $this->getAlteracaoFuncao()   );
            $obErro = $obTPessoalContratoServidorFuncao->inclusao( $boTransacao );

            if (!$obErro->ocorreu()) {
                $obTPessoalContratoServidorEspecialidadeFuncao->setDado("cod_contrato"        , $this->getCodContrato());
                $obTPessoalContratoServidorEspecialidadeFuncao->setDado("cod_especialidade"   , $inCodEspecialidadeFuncao);
                $obErro = $obTPessoalContratoServidorEspecialidadeFuncao->inclusao( $boTransacao );
            }
        }
    } else {
        if ($inCodRegimeFuncao != $inCodRegime or
           $inCodSubDivisao   != $inCodSubDivisaoFuncao or
           $inCodFuncao       != $inCodCargo) {
            $obTPessoalCargoSubDivisao->setDado("cod_regime",$inCodRegimeFuncao);
            $obTPessoalCargoSubDivisao->setDado("cod_sub_divisao",$inCodSubDivisaoFuncao);
            $obTPessoalCargoSubDivisao->setDado("cod_cargo",$inCodFuncao);
            $obTPessoalCargoSubDivisao->setDado("entidade",Sessao::getEntidade());
            $obErro = $obTPessoalCargoSubDivisao->getVagasDisponiveisCargo($rsVargas,"","",$boTransacao);
            if (!$obErro->ocorreu() AND $rsVargas->getCampo("vagas") == 0) {
                $obErro->setDescricao ("Função selecionada não possui vagas disponíveis!");
            }
        }
        if (!$obErro->ocorreu()) {
            $obTPessoalContratoServidorFuncao->setDado( "cod_contrato"    , $this->getCodContrato()       );
            $obTPessoalContratoServidorFuncao->setDado( "cod_cargo"       , $inCodFuncao                  );
            $obTPessoalContratoServidorFuncao->setDado( "vigencia"        , $this->getAlteracaoFuncao()   );
            $obErro = $obTPessoalContratoServidorFuncao->inclusao( $boTransacao );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPessoalCargoSubDivisao );

    return $obErro;
}

function excluirContratoServidor($boTransacao = "")
{
    $boFlagTransacao = false;
    $requestAux = new Request($_REQUEST);
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    include_once ( CAM_GRH_BEN_NEGOCIO."RBeneficioContratoServidorConcessaoValeTransporte.class.php" );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidor.class.php"                       );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorSituacao.class.php"               );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorExameMedico.class.php"            );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorSalario.class.php"                );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorInicioProgressao.class.php"       );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorNomeacaoPosse.class.php"          );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorOcorrencia.class.php"             );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorOrgao.class.php"                  );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorLocal.class.php"                  );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorLocalHistorico.class.php"         );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorPrevidencia.class.php"            );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorNivelPadrao.class.php"            );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorPadrao.class.php"                 );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorEspecialidadeCargo.class.php"     );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorEspecialidadeFuncao.class.php"    );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorFuncao.class.php"                 );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorSubDivisaoFuncao.class.php"       );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorRegimeFuncao.class.php"           );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorContaSalario.class.php"           );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorFormaPagamento.class.php"         );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorContaFgts.class.php"              );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorConselho.class.php"               );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalServidorContratoServidor.class.php"               );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalFerias.class.php"                                 );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalLoteFeriasContrato.class.php"                     );
    include_once ( CAM_GRH_BEN_MAPEAMENTO."TBeneficioContratoServidorGrupoConcessaoValeTransporte.class.php");
    include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoContratoServidorPeriodo.class.php"              );  
    include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEventoPeriodo.class.php"              );  
          
    $obRBeneficioContratoServidorConcessaoValeTransporte    = new RBeneficioContratoServidorConcessaoValeTransporte;
    $obTPessoalContratoServidor                     = new TPessoalContratoServidor;
    $obTPessoalServidorContratoServidor             = new TPessoalServidorContratoServidor;
    $obTPessoalContratoServidorExameMedico          = new TPessoalContratoServidorExameMedico;
    $obTPessoalContratoServidorSalario              = new TPessoalContratoServidorSalario;
    $obTPessoalContratoServidorInicioProgressao     = new TPessoalContratoServidorInicioProgressao;
    $obTPessoalContratoServidorNomeacaoPosse        = new TPessoalContratoServidorNomeacaoPosse;
    $obTPessoalContratoServidorOcorrencia           = new TPessoalContratoServidorOcorrencia;
    $obTPessoalContratoServidorOrgao                = new TPessoalContratoServidorOrgao;
    $obTPessoalContratoServidorLocal                = new TPessoalContratoServidorLocal;
    $obTPessoalContratoServidorLocalHistorico       = new TPessoalContratoServidorLocalHistorico;
    $obTPessoalContratoServidorPrevidencia          = new TPessoalContratoServidorPrevidencia;
    $obTPessoalContratoServidorNivelPadrao          = new TPessoalContratoServidorNivelPadrao;
    $obTPessoalContratoServidorPadrao               = new TPessoalContratoServidorPadrao;
    $obTPessoalContratoServidorSubDivisaoFuncao     = new TPessoalContratoServidorSubDivisaoFuncao;
    $obTPessoalContratoServidorRegimeFuncao         = new TPessoalContratoServidorRegimeFuncao;
    $obTPessoalContratoServidorContaSalario         = new TPessoalContratoServidorContaSalario;
    $obTPessoalContratoServidorFormaPagamento       = new TPessoalContratoServidorFormaPagamento;
    $obTPessoalContratoServidorContaFgts            = new TPessoalContratoServidorContaFgts;
    $obTPessoalContratoServidorConselho             = new TPessoalContratoServidorConselho;
    $obTPessoalFerias                               = new TPessoalFerias;
    $obTPessoalLoteFeriasContrato                   = new TPessoalLoteFeriasContrato;
    $obTPessoalContratoServidorSituacao             = new TPessoalContratoServidorSituacao();
    $obTFolhaPagamentoContratoServidorPeriodo       = new TFolhaPagamentoContratoServidorPeriodo();
    $obTBeneficioContratoServidorGrupoConcessaoValeTransporte = new TBeneficioContratoServidorGrupoConcessaoValeTransporte;
    $obRBeneficioContratoServidorConcessaoValeTransporte->obRPessoalContratoServidor->setCodContrato( $this->getCodContrato() );
    $obRBeneficioContratoServidorConcessaoValeTransporte->listarContratoServidorConcessaoValeTransporte($rsConcessoes,$boTransacao);
    $stFiltro = " WHERE cod_contrato = ".$this->getCodContrato();
    $obTBeneficioContratoServidorGrupoConcessaoValeTransporte->recuperaTodos($rsGrupoConcessoes,$stFiltro,$stOrdem,$boTransacao);
    if ( $rsConcessoes->getNumLinhas() > 0 or $rsGrupoConcessoes->getNumLinhas() > 0 ) {
        $obErro->setDescricao('O servidor não pode ser excluído porque possui uma concessão de vale-transporte.');
    }
    $obTPessoalFerias->recuperaTodos($rsFerias,$stFiltro,$stOrdem,$boTransacao);
    if ( $rsFerias->getNumLinhas() > 0 ) {
        $obErro->setDescricao('O servidor não pode ser excluído porque possui férias cadastradas.');
    }

    if ( !$obErro->ocorreu() ) {
        $obTPessoalLoteFeriasContrato->setDado("cod_contrato" ,$this->getCodContrato() );
        $obErro = $obTPessoalLoteFeriasContrato->exclusao($boTransacao);
    }

    if ( !$obErro->ocorreu() ) {
        $obTPessoalContratoServidorExameMedico->setDado("cod_contrato"     ,$this->getCodContrato()            );
        $obErro = $obTPessoalContratoServidorExameMedico->exclusao($boTransacao);
    }
    if ( !$obErro->ocorreu() ) {
        $obTPessoalContratoServidorSalario->setDado("cod_contrato"    ,$this->getCodContrato()    );
        $obErro = $obTPessoalContratoServidorSalario->exclusao($boTransacao);
    }
    if ( !$obErro->ocorreu() ) {
        $obTPessoalContratoServidorInicioProgressao->setDado("cod_contrato"           ,$this->getCodContrato()        );
        $obErro = $obTPessoalContratoServidorInicioProgressao->exclusao($boTransacao);
    }
    if ( !$obErro->ocorreu() ) {
        $obTPessoalContratoServidorNomeacaoPosse->setDado("cod_contrato"  ,$this->getCodContrato()    );
        $obErro = $obTPessoalContratoServidorNomeacaoPosse->exclusao($boTransacao);
    }
    if ( !$obErro->ocorreu() ) {
        $obTPessoalContratoServidorOcorrencia->setDado('cod_contrato',$this->getCodContrato());
        $obErro = $obTPessoalContratoServidorOcorrencia->exclusao( $boTransacao );
    }
    if ( !$obErro->ocorreu() ) {//9
        $obTPessoalContratoServidorContaFgts->setDado("cod_contrato",$this->getCodContrato() );
        $obErro = $obTPessoalContratoServidorContaFgts->exclusao( $boTransacao );
    }

    $stFiltro = " WHERE cod_contrato = ".$this->getCodContrato();
    $obTPessoalContratoServidorFormaPagamento->recuperaTodos($rsContratoServidorFormaPagamento, $stFiltro, "", $boTransacao);
    $obTPessoalContratoServidorFormaPagamento->setDado("cod_contrato",$this->getCodContrato());

    while (!$rsContratoServidorFormaPagamento->eof()) {
        if (!$obErro->ocorreu()) {
            $obTPessoalContratoServidorFormaPagamento->setDado("timestamp",$rsContratoServidorFormaPagamento->getCampo("timestamp"));
            $obTPessoalContratoServidorFormaPagamento->setDado("cod_forma_pagamento",$rsContratoServidorFormaPagamento->getCampo("cod_forma_pagamento"));
            $obErro = $obTPessoalContratoServidorFormaPagamento->exclusao( $boTransacao );
        }
        $rsContratoServidorFormaPagamento->proximo();
    }

    if ( !$obErro->ocorreu() ) {
        $obTFolhaPagamentoContratoServidorPeriodo->setDado("cod_contrato", $this->getCodContrato() );
        $obErro = $obTFolhaPagamentoContratoServidorPeriodo->listar($rsContratoPeriodoServidor,$boTransacao);        
        if ( $rsContratoPeriodoServidor->getNumLinhas() > 1) {
            $obErro->setDescricao('O servidor não pode ser excluído porque possui dados em outros periodos de movimentação.');
        }else{            
            $obTFolhaPagamentoRegistroEventoPeriodo = new TFolhaPagamentoRegistroEventoPeriodo();
            $obTFolhaPagamentoRegistroEventoPeriodo->setDado("cod_contrato",$this->getCodContrato());
            $stFiltro  = " WHERE cod_contrato = ".$this->getCodContrato();
            $stFiltro .= " AND cod_periodo_movimentacao = ".$rsContratoPeriodoServidor->getCampo('cod_periodo_movimentacao');
            $obErro = $obTFolhaPagamentoRegistroEventoPeriodo->recuperaTodos($rsRegistroEventoPeriodo,$stFiltro,"", $boTransacao);            
            if ( !$obErro->ocorreu() ) {
                foreach ($rsRegistroEventoPeriodo->getElementos() as $registros) {
                    $obTFolhaPagamentoRegistroEventoPeriodo->setDado("cod_contrato",$registros['cod_contrato']);
                    $obTFolhaPagamentoRegistroEventoPeriodo->setDado("cod_registro",$registros['cod_registro']);
                    $obTFolhaPagamentoRegistroEventoPeriodo->setDado("cod_periodo_movimentacao",$registros['cod_periodo_movimentacao']);
                    $obErro = $obTFolhaPagamentoRegistroEventoPeriodo->deletarRegistroEventoPeriodo($boTransacao);
                    if ( $obErro->ocorreu() ) {
                        return $obErro;
                    }
                }                
            }
            
            if ( !$obErro->ocorreu() ) {
                $obErro = $obTFolhaPagamentoContratoServidorPeriodo->exclusao($boTransacao);                
            }
        }
    }

    if ( !$obErro->ocorreu() ) {
        $obTPessoalContratoServidorContaSalario->setDado("cod_contrato",$this->getCodContrato()         );
        $obErro = $obTPessoalContratoServidorContaSalario->exclusao( $boTransacao );
    }
    if ( !$obErro->ocorreu() ) {
        $obTPessoalContratoServidorLocalHistorico->setDado("cod_contrato", $this->getCodContrato() );
        $obErro = $obTPessoalContratoServidorLocalHistorico->exclusao( $boTransacao );
    }
    if ( !$obErro->ocorreu() ) {
        $obTPessoalContratoServidorLocal->setDado("cod_contrato", $this->getCodContrato() );
        $obErro = $obTPessoalContratoServidorLocal->exclusao( $boTransacao );
    }
    if ( !$obErro->ocorreu() ) {
        $obTPessoalContratoServidorOrgao->setDado("cod_contrato",   $this->getCodContrato() );
        $obErro = $obTPessoalContratoServidorOrgao->exclusao( $boTransacao );
    }
    if ( !$obErro->ocorreu() ) {
        $obTPessoalContratoServidorPrevidencia->setDado("cod_contrato" , $this->getCodContrato() );
        $obErro = $obTPessoalContratoServidorPrevidencia->exclusao( $boTransacao );
    }
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->excluirVaga( $boTransacao ); //alteracao das vagas do contrato
    }
    if ( !$obErro->ocorreu() ) {
        $obTPessoalServidorContratoServidor->setDado("cod_contrato",$this->getCodContrato() );
        $obTPessoalServidorContratoServidor->setDado("cod_servidor",$requestAux->get("inCodServidor") );        
        $obErro = $obTPessoalServidorContratoServidor->exclusao( $boTransacao );                
    }
    if ( !$obErro->ocorreu() ) {
        $tempComp = $obTPessoalContratoServidorLocalHistorico->getComplementoChave();
        $tempCod  = $obTPessoalContratoServidorLocalHistorico->getCampoCod();
        $obTPessoalContratoServidorLocalHistorico->setComplementoChave('');
        $obTPessoalContratoServidorLocalHistorico->setCampoCod('cod_contrato');
        $obTPessoalContratoServidorLocalHistorico->setDado("cod_contrato",$this->getCodContrato() );
        $obErro = $obTPessoalContratoServidorLocalHistorico->exclusao( $boTransacao );
        $obTPessoalContratoServidorLocalHistorico->setComplementoChave($tempComp);
        $obTPessoalContratoServidorLocalHistorico->setCampoCod($tempCod);
    }
    if ( !$obErro->ocorreu() ) {
        $tempComp = $obTPessoalContratoServidorLocal->getComplementoChave();
        $tempCod  = $obTPessoalContratoServidorLocal->getCampoCod();
        $obTPessoalContratoServidorLocal->setComplementoChave('');
        $obTPessoalContratoServidorLocal->setCampoCod('cod_contrato');
        $obTPessoalContratoServidorLocal->setDado("cod_contrato",$this->getCodContrato() );
        $obErro = $obTPessoalContratoServidorLocal->exclusao( $boTransacao );
        $obTPessoalContratoServidorLocal->setComplementoChave($tempComp);
        $obTPessoalContratoServidorLocal->setCampoCod($tempCod);
    }
    if ( !$obErro->ocorreu() ) {
        $obTPessoalContratoServidorNivelPadrao->setDado("cod_contrato",$this->getCodContrato() );
        $obErro = $obTPessoalContratoServidorNivelPadrao->exclusao( $boTransacao );
    }
    if ( !$obErro->ocorreu() ) {
        $obTPessoalContratoServidorPadrao->setDado("cod_contrato",$this->getCodContrato() );
        $obErro = $obTPessoalContratoServidorPadrao->exclusao( $boTransacao );
    }
    if ( !$obErro->ocorreu() ) {
        $obTPessoalContratoServidorOrgao->setDado("cod_contrato",$this->getCodContrato() );
        $obErro = $obTPessoalContratoServidorOrgao->exclusao( $boTransacao );
    }
    if ( !$obErro->ocorreu() ) {
        //O Restante dos valores vem setado da pagina de processamento
        $arChaveAtributoCandidato =  array( "cod_contrato" => $this->getCodContrato() );
        $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoCandidato );
        $obErro = $this->obRCadastroDinamico->excluirValores( $boTransacao );
    }
    if ( !$obErro->ocorreu() ) {
        $obTPessoalContratoServidorRegimeFuncao->setDado("cod_contrato",$this->getCodContrato());
        $obErro = $obTPessoalContratoServidorRegimeFuncao->exclusao($boTransacao);
    }
    if ( !$obErro->ocorreu() ) {
        $obTPessoalContratoServidorSubDivisaoFuncao->setDado("cod_contrato",$this->getCodContrato());
        $obErro = $obTPessoalContratoServidorSubDivisaoFuncao->exclusao($boTransacao);
    }
    if ( !$obErro->ocorreu() ) {
        $obTPessoalContratoServidorConselho->setDado("cod_contrato",$this->getCodContrato() );
        $obErro = $obTPessoalContratoServidorConselho->exclusao( $boTransacao );
    }
    if ( !$obErro->ocorreu() ) {
        $obTPessoalContratoServidorSituacao->setDado("cod_contrato",$this->getCodContrato() );
        $obErro = $obTPessoalContratoServidorSituacao->exclusao( $boTransacao );
    }
    if ( !$obErro->ocorreu() ) {
        $obTPessoalContratoServidor->setDado("cod_contrato",$this->getCodContrato() );
        $obErro = $obTPessoalContratoServidor->exclusao( $boTransacao );                                 
    }    
    if ( !$obErro->ocorreu() ) {        
        $obErro = $this->excluirContrato( $boTransacao );        
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTPessoalContratoServidor );

    return $obErro;
}

function excluirVaga($boTransacao = "")
{
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidor.class.php"                       );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorEspecialidadeCargo.class.php"     );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorEspecialidadeFuncao.class.php"    );
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorFuncao.class.php"                 );
    $obTPessoalContratoServidor                     = new TPessoalContratoServidor;
    $obTPessoalContratoServidorEspecialidadeCargo   = new TPessoalContratoServidorEspecialidadeCargo;
    $obTPessoalContratoServidorEspecialidadeFuncao  = new TPessoalContratoServidorEspecialidadeFuncao;
    $obTPessoalContratoServidorFuncao               = new TPessoalContratoServidorFuncao;
    if ( !$obErro->ocorreu() ) {
        $obTPessoalContratoServidorEspecialidadeCargo->setDado("cod_contrato", $this->getCodContrato());
        $obErro = $obTPessoalContratoServidorEspecialidadeCargo->exclusao($boTransacao);
    }
    if ( !$obErro->ocorreu() ) {
        $obTPessoalContratoServidorEspecialidadeFuncao->setDado("cod_contrato", $this->getCodContrato());
        $obErro = $obTPessoalContratoServidorEspecialidadeFuncao->exclusao($boTransacao);
    }
    if ( !$obErro->ocorreu() ) {
        $obTPessoalContratoServidorFuncao->setDado("cod_contrato", $this->getCodContrato());
        $obErro = $obTPessoalContratoServidorFuncao->exclusao($boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );

    return $obErro;
}

function listarContratoRescisao(&$rsRecordSet,$stFiltro,$stOrder,$boTransacao)
{
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidor.class.php");
    $obTPessoalContratoServidor =  new TPessoalContratoServidor;
    $obErro= $obTPessoalContratoServidor->recuperaRelacionamentoContratoRescisao($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

function listarContratosCargoExercido(&$rsRecordSet , $boTransacao="")
{
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidor.class.php");

    $stFiltro = "";

    if ( $this->obRPessoalCargo->getCodCargo() ) {
        $stFiltro .= " AND pcs.cod_cargo = ".$this->obRPessoalCargo->getCodCargo()." \n";
    }
    if ($this->obRPessoalCargo->roUltimoEspecialidade) {
        if ( $this->obRPessoalCargo->roUltimoEspecialidade->getCodEspecialidade() ) {
            $stFiltro .= " AND pcsec.cod_especialidade = ".$this->obRPessoalCargo->roUltimoEspecialidade->getCodEspecialidade()." \n";
        }
    }

    $stFiltro = ($stFiltro) ? " WHERE ".substr($stFiltro,4,strlen($stFiltro)) : "";
    $stOrder = "pcs.cod_contrato";

    $obTPessoalContratoServidor =  new TPessoalContratoServidor;
    $obErro = $obTPessoalContratoServidor->recuperaContratosCargoExercido( $rsRecordSet , $stFiltro , $stOrder , $boTransacao );

    return $obErro;
}

function listarContratosCargoExercidoComSubDivisaoAssentamento(&$rsRecordSet, $inCodAssentamento="", $boTransacao="")
{
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidor.class.php");

    $stFiltro = "";

    if ( $this->obRPessoalCargo->getCodCargo() ) {
        $stFiltro .= " AND pcs.cod_cargo = ".$this->obRPessoalCargo->getCodCargo()." \n";
    }
    if ($this->obRPessoalCargo->roUltimoEspecialidade) {
        if ( $this->obRPessoalCargo->roUltimoEspecialidade->getCodEspecialidade() ) {
            $stFiltro .= " AND pcsec.cod_especialidade = ".$this->obRPessoalCargo->roUltimoEspecialidade->getCodEspecialidade()." \n";
        }
    }
    if ($inCodAssentamento) {
      $stFiltro .= " AND assentamento_sub_divisao.cod_assentamento = ".$inCodAssentamento." \n";
    }

    $stFiltro .= " AND contrato_servidor_situacao.timestamp = (
                                                                SELECT timestamp
                                                                  FROM pessoal.contrato_servidor_situacao
                                                                 WHERE cod_contrato = pcs.cod_contrato                                                               
                                                              ORDER BY timestamp desc
                                                                 LIMIT 1
                                                              )
                   AND contrato_servidor_situacao.situacao = 'A' \n";

    $stFiltro = ($stFiltro) ? " WHERE ".substr($stFiltro,4,strlen($stFiltro)) : "";
    $stOrder = "pcs.cod_contrato";

    $obTPessoalContratoServidor = new TPessoalContratoServidor;
    $obErro = $obTPessoalContratoServidor->recuperaContratosCargoExercidoComSubDivisaoAssentamento( $rsRecordSet , $stFiltro , $stOrder , $boTransacao );

    return $obErro;
}

function listarContratosFuncaoExercida(&$rsRecordSet , $boTransacao="")
{
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidor.class.php");

    $stFiltro = "";

    if ( $this->obRPessoalCargo->getCodCargo() ) {
        $stFiltro .= " AND pcsf.cod_cargo = ".$this->obRPessoalCargo->getCodCargo()." \n";
    }
    if ($this->obRPessoalCargo->roUltimoEspecialidade) {
        if ( $this->obRPessoalCargo->roUltimoEspecialidade->getCodEspecialidade() ) {
            $stFiltro .= " AND pcsef.cod_especialidade = ".$this->obRPessoalCargo->roUltimoEspecialidade->getCodEspecialidade()." \n";
        }
    }

    $stFiltro = ($stFiltro) ? " WHERE ".substr($stFiltro,4,strlen($stFiltro)) : "";
    $stOrder = "pcs.cod_contrato";

    $obTPessoalContratoServidor =  new TPessoalContratoServidor;
    $obErro = $obTPessoalContratoServidor->recuperaContratosFuncaoExercida( $rsRecordSet , $stFiltro , $stOrder , $boTransacao );

    return $obErro;
}

function listarContratosFuncaoExercidaComSubDivisaoAssentamento(&$rsRecordSet, $inCodAssentamento="", $boTransacao="")
{
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidor.class.php");

    $stFiltro = "";

    if ( $this->obRPessoalCargo->getCodCargo() ) {
        $stFiltro .= " AND pcsf.cod_cargo = ".$this->obRPessoalCargo->getCodCargo()." \n";
    }
    if ($this->obRPessoalCargo->roUltimoEspecialidade) {
        if ( $this->obRPessoalCargo->roUltimoEspecialidade->getCodEspecialidade() ) {
            $stFiltro .= " AND pcsef.cod_especialidade = ".$this->obRPessoalCargo->roUltimoEspecialidade->getCodEspecialidade()." \n";
        }
    }

    if ($inCodAssentamento) {
        $stFiltro .= " AND assentamento_sub_divisao.cod_assentamento = ".$inCodAssentamento." \n";
    }

    $stFiltro .= " AND assentamento_sub_divisao.cod_sub_divisao = contrato_servidor_sub_divisao_funcao.cod_sub_divisao
                   AND contrato_servidor_situacao.timestamp = (
                                                                SELECT timestamp
                                                                  FROM pessoal.contrato_servidor_situacao
                                                                 WHERE cod_contrato = pcs.cod_contrato                                                               
                                                              ORDER BY timestamp desc
                                                                 LIMIT 1
                                                              )
                   AND contrato_servidor_situacao.situacao = 'A' \n";

    $stFiltro = ($stFiltro) ? " WHERE ".substr($stFiltro,4,strlen($stFiltro)) : "";
    $stOrder = "pcs.cod_contrato";

    $obTPessoalContratoServidor =  new TPessoalContratoServidor;
    $obErro = $obTPessoalContratoServidor->recuperaContratosFuncaoExercidaComSubDivisaoAssentamento( $rsRecordSet , $stFiltro , $stOrder , $boTransacao );
    return $obErro;
}

function listarContratosLotacao(&$rsRecordSet , $boTransacao="")
{
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidor.class.php");

    $stFiltro = "";

    if ($this->obROrganogramaOrgao) {
        if ( $this->obROrganogramaOrgao->getCodOrgaoEstruturado() ) {
            $stFiltro .= " AND oon.orgao = '". $this->obROrganogramaOrgao->getCodOrgaoEstruturado() ."' \n";
        }
    }

    $stFiltro = ($stFiltro) ? " WHERE ".substr($stFiltro,4,strlen($stFiltro)) : "";
    $stOrder = "pcs.cod_contrato";

    $obTPessoalContratoServidor =  new TPessoalContratoServidor;
    $obErro= $obTPessoalContratoServidor->recuperaContratosLotacao( $rsRecordSet , $stFiltro , $stOrder , $boTransacao );

    return $obErro;
}

function listarContratos(&$rsRecordSet , $boTransacao="")
{
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidor.class.php");

    $obTPessoalContratoServidor =  new TPessoalContratoServidor;
    if ( $this->getRegistro() ) {
          $obTPessoalContratoServidor->setDado("registro", $this->getRegistro());
    }
    if ( $this->roPessoalServidor->obRCGMPessoaFisica->getNumCGM() ) {
        $obTPessoalContratoServidor->setDado("numcgm", $this->roPessoalServidor->obRCGMPessoaFisica->getNumCGM());
    }
    
    if ($this->getSituacao()) {
        $stFiltro .= " WHERE recuperarSituacaoDoContratoLiteral(contrato.cod_contrato,0,'".Sessao::getEntidade()."') SIMILAR TO '".$this->getSituacao()."' ";
    }

    $stOrder = " nom_cgm,registro";
    $obErro= $obTPessoalContratoServidor->recuperaRelacionamentoListarContratos( $rsRecordSet , $stFiltro , $stOrder , $boTransacao );

    return $obErro;
}

function listarFuncaoDoRegistro(&$rsRecordSet,$inRegistro,$boTransacao="")
{
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorFuncao.class.php");
    $obTPessoalContratoServidorFuncao =  new TPessoalContratoServidorFuncao;
    $stFiltro .= " AND registro = $inRegistro";
    $obErro = $obTPessoalContratoServidorFuncao->recuperaRelacionamento($rsRecordSet,$stFiltro,$stOrdem,$boTransacao);

    return $obErro;
}

//1 - Ativo
//2 - Inativo(Aposentado)
//3 - Pensionista
function consultarVinculoDoServidor($inCodContrato, $boTransacao = '')
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoVinculo.class.php");

    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalAposentadoria.class.php");
    $obTPessoalAposentadoria =  new TPessoalAposentadoria();
    $stFiltro = " AND aposentadoria.cod_contrato = ".$inCodContrato;
    $obTPessoalAposentadoria->recuperaRelacionamento($rsAposentado,$stFiltro);

    $obTPessoalContrato = new TPessoalContratoServidor;
    if ( $rsAposentado->getNumLinhas() != -1 ) {
            $stVinculo = 'Inativo';
    } else {
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoPensionista.class.php");
        $obTPessoalContratoPensionista = new TPessoalContratoPensionista();
        $stFiltro = " AND contrato_pensionista.cod_contrato = ".$inCodContrato;
        $obTPessoalContratoPensionista->recuperaRelacionamento($rsPensionista,$stFiltro);

    if ( $rsPensionista->getNumLinhas() != -1 ) {
            $stVinculo = 'Pensionista';
    } else {
            $stVinculo = 'Ativo';
    }
    }

    $stFiltro = '';
    $obTFolhaPagamentoVinculo = new TFolhaPagamentoVinculo;
    $stFiltro = "		WHERE vinculo.descricao ILIKE '".$stVinculo."%'";
    $obTFolhaPagamentoVinculo->recuperaTodos($rsVinculo,$stFiltro,"",$boTransacao);

    return  $rsVinculo->getCampo("cod_vinculo");
}

}
