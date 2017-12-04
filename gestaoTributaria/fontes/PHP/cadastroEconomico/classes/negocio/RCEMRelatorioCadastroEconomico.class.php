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
    * Classe de Regra para Relatório do Cadastro Economico
    * Data de Criação   : 29/04/2005

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Diego Bueno Coelho

    * @package URBEM
    * @subpackage Regra

    * $Id: RCEMRelatorioCadastroEconomico.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.17
*/

/*
$Log$
Revision 1.5  2007/01/30 11:37:04  dibueno
Bug #8042#

Revision 1.4  2007/01/11 10:21:16  dibueno
Bug #8042#

Revision 1.3  2006/09/15 12:13:58  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_BANCO_DADOS."PersistenteRelatorio.class.php" );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMRelatorioCadastroEconomico.class.php"       );

/**
    * Classe de Regra para Relatório do Cadastro Economico
    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Diego Bueno
*/
class RCEMRelatorioCadastroEconomico extends PersistenteRelatorio
{
var $inCodLicencaInicial;
var $inCodLicencaFinal;
/**
    * @access Private
    * @var Integer
*/
var $inInscricaoAtual;
/**
    * @access Private
    * @var Integer
*/
var $inInscricaoInicial;
/**
    * @access Private
    * @var Integer
*/
var $inInscricaoFinal;
/**
    * @access Private
    * @var Integer
*/
var $inCodInicio;
/**
    * @access Private
    * @var Integer
*/
var $inCodTermino;
/**
    * @access Private
    * @var Date
*/
var $dtInicio;
/**
    * @access Private
    * @var Integer
*/
var $inCodSocio;
/**
    * @access Private
    * @var String
*/
var $stTipoInscricao;
/**
    * @access Private
    * @var String
*/
var $stSituacao;
/**
    * @access Private
    * @var String
*/
var $stTipoRelatorio;
/**
    * @access Private
    * @var String
*/
var $stOrder;

/**
    * @var Object
    * @access Private
*/
var $obTCEMRelatorioCE;
/**
    * @var Object
    * @access Private
*/
var $obRCadastroDinamico;
var $inCodLogradouroInicial;
var $inCodLogradouroFinal;

/**
    * @access Public
    * @param Integer $valor
*/
function setCodLogradouroInicial($valor) { $this->inCodLogradouroInicial  = $valor;   }
function setCodLogradouroFinal($valor) { $this->inCodLogradouroFinal    = $valor;   }
function setCodLicencaInicial($valor) { $this->inCodLicencaInicial     = $valor;   }
function setCodLicencaFinal($valor) { $this->inCodLicencaFinal       = $valor;   }
function setInscricaoAtual($valor) { $this->inInscricaoAtual		= $valor;   }
function setInscricaoInicial($valor) { $this->inInscricaoInicial  	= $valor;   }
function setInscricaoFinal($valor) { $this->inInscricaoFinal 	    = $valor;   }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodInicio($valor) { $this->inCodInicio           = $valor;  }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodTermino($valor) { $this->inCodTermino          = $valor;  }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodSocio($valor) { $this->inCodSocio          = $valor;  }
/**
    * @access Public
    * @param String $valor
*/
function setDtInicio($valor) { $this->dtInicio                = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setTipoInscricao($valor) { $this->stTipoInscricao   = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setSituacao($valor) { $this->stSituacao   = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setTipoRelatorio($valor) { $this->stTipoRelatorio   = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setOrder($valor) { $this->stOrder                = $valor; }

/**
    * @access Public
    * @return Integer
*/
function getCodLicencaInicial() { return $this->inCodLicencaInicial;   }
function getCodLicencaFinal() { return $this->inCodLicencaFinal;   }
function getInscricaoAtual() { return $this->inInscricaoAtual;      }
function getInscricaoInicial() { return $this->inInscricaoInicial;      }
function getInscricaoFinal() { return $this->inInscricaoFinal;		}
/**
    * @access Public
    * @return Integer
*/
function getCodInicio() { return $this->inCodInicio;          }
/**
    * @access Public
    * @return Integer
*/
function getCodTermino() { return $this->inCodTermino;         }
/**
    * @access Public
    * @return Integer
*/
function getCodSocio() { return $this->inCodSocio;         }
/**
    * @access Public
    * @return Date
*/
function getDtInicio() { return $this->dtInicio;          }
/**
    * @access Public
    * @return String
*/
function getTipoInscricao() { return $this->stTipoInscricao;   }
/**
    * @access Public
    * @return String
*/
function getSituacao() { return $this->stSituacao;   }
/**
    * @access Public
    * @return String
*/
function getTipoRelatorio() { return $this->stTipoRelatorio;   }
/**
    * @access Public
    * @return String
*/
function getOrder() { return $this->stOrder;              }

/**
    * Método Construtor
    * @access Private
*/
function RCEMRelatorioCadastroEconomico()
{
    $this->obTCEMRelatorioCE = new TCEMRelatorioCadastroEconomico;
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$rsRecordSet, $stOrder = "")
{
        $stFiltro =  null;

        if ($this->inCodLogradouroInicial || $this->inCodLogradouroFinal) {
            $boLogradouro = true;
            if ($this->inCodLogradouroInicial && $this->inCodLogradouroFinal) {
                $stFiltro .= " AND logradouro.cod_logradouro BETWEEN ".$this->inCodLogradouroInicial." AND ".$this->inCodLogradouroFinal." \n";
            } elseif ($this->inCodLogradouroInicial) {
                $stFiltro .= " AND logradouro.cod_logradouro = ".$this->inCodLogradouroInicial." \n";
            } else {
                $stFiltro .= " AND logradouro.cod_logradouro = ".$this->inCodLogradouroFinal." \n";
            }
        }

        if ( $this->getInscricaoInicial() || $this->getInscricaoFinal() ) {

            if ( $this->getInscricaoInicial() && $this->getInscricaoFinal() ) {
                $stFiltro .= " AND ce.inscricao_economica BETWEEN ". $this->getInscricaoInicial() ." AND ". $this->getInscricaoFinal()." \n";
            } elseif ( $this->getInscricaoInicial() && !$this->getInscricaoFinal() ) {
                $stFiltro .= " AND ce.inscricao_economica = ". $this->getInscricaoInicial() ." \n";
            } elseif ( !$this->getInscricaoInicial() && $this->getInscricaoFinal() ) {
                $stFiltro .= " AND ce.inscricao_economica = ". $this->getInscricaoFinal() ." \n";
            }

        }
        if ( $this->getDtInicio() ) {
            $stFiltro .= " AND ce.dt_abertura = '". $this->getDtInicio() ."' \n";
        }
        if ( $this->getTipoInscricao() ) {
            if ( $this->getTipoInscricao() == 'fato' ) {
                    $stFiltro .= " AND ef.numcgm is not null \n";
                $boEmpresaFato = true;
            } elseif ( $this->getTipoInscricao() == 'direito' ) {
                    $stFiltro .= " AND ed.numcgm is not null \n";
                $boEmpresaDireito = true;
            } elseif ( $this->getTipoInscricao() == 'autonomo' ) {
                    $stFiltro .= " AND au.numcgm is not null \n";
                $boEmpresaAutonoma = true;
            }
        }
        if ( $this->getCodSocio() ) {
            $stFiltro .= " AND cesocio.numcgm  = ". $this->getCodSocio() ." \n";
            $boSocio = true;
        }
        if ( $this->getCodInicio() || $this->getCodTermino() ) {
            if ( $this->getCodInicio() && $this->getCodTermino() ) {
                $stFiltro .= " AND atividade.cod_estrutural BETWEEN '". $this->getCodInicio() ."' and '". $this->getCodTermino() ."'  \n";
            } elseif ( $this->getCodInicio() && !$this->getCodTermino() ) {
                $stFiltro .= " AND atividade.cod_estrutural = '". $this->getCodInicio() ."' \n";
            } elseif ( !$this->getCodInicio() && $this->getCodTermino() ) {
                $stFiltro .= " AND atividade.cod_estrutural = '". $this->getCodTermino() ."' \n";
            }

            $boAtividade = true;
        }

        if ( $this->getCodLicencaInicial() || $this->getCodLicencaFinal() ) {
            if ( $this->getCodLicencaInicial() && $this->getCodLicencaFinal() ) {
                $arTMPDadosInicial = explode( "/", $this->getCodLicencaInicial() );
                $arTMPDadosFinal = explode( "/", $this->getCodLicencaFinal() );
                $stFiltro .= " AND elic.cod_licenca BETWEEN ".$arTMPDadosInicial[0]." AND ".$arTMPDadosFinal[0]." \n";
                $stFiltro .= " AND elic.exercicio BETWEEN ".$arTMPDadosInicial[1]." AND ".$arTMPDadosFinal[1]." \n";
            }else
                if ( $this->getCodLicencaInicial() && !$this->getCodLicencaFinal() ) {
                    $arTMPDadosInicial = explode( "/", $this->getCodLicencaInicial() );
                    $stFiltro .= " AND elic.cod_licenca = ".$arTMPDadosInicial[0]." \n";
                    $stFiltro .= " AND elic.exercicio = ".$arTMPDadosInicial[1]." \n";
                }else
                    if ( !$this->getCodLicencaInicial() && $this->getCodLicencaFinal() ) {
                        $arTMPDadosFinal = explode( "/", $this->getCodLicencaFinal() );
                        $stFiltro .= " AND elic.cod_licenca = ".$arTMPDadosFinal[0]." \n";
                        $stFiltro .= " AND elic.exercicio = ".$arTMPDadosFinal[1]." \n";
                    }

            $boLicenca = true;
        }

        $stOrder = " ORDER BY ce.inscricao_economica, ce.dt_abertura \n";
        $this->obTCEMRelatorioCE->relatorioCadastroEconomico ( $rsRecordSet, $stFiltro, $stOrder, $boTransacao, $boEmpresaFato, $boEmpresaDireito, $boEmpresaAutonoma, $boSocio, $boAtividade, $boLicenca, $boLogradouro );

    return $obErro;
}

function geraAtividades(&$rsRecordSet)
{
    $stFiltro = null;
    $stFiltro .= " ace.inscricao_economica = ". $this->getInscricaoAtual() ." AND\n";
    if ( $this->getCodInicio() && $this->getCodTermino() ) {
        $stFiltro .= " A.cod_estrutural BETWEEN '". $this->getCodInicio() ."' and '". $this->getCodTermino() ."' AND\n";
    } elseif ( $this->getCodInicio() && !$this->getCodTermino() ) {
        $stFiltro .= " A.cod_estrutural = '". $this->getCodInicio() ."' AND\n";
    } elseif ( !$this->getCodInicio() && $this->getCodTermino() ) {
        $stFiltro .= " A.cod_estrutural = '". $this->getCodTermino() ."' AND\n";
    }

    if ( $this->getCodLicencaInicial() || $this->getCodLicencaFinal() ) {
        if ( $this->getCodLicencaInicial() && $this->getCodLicencaFinal() ) {
            $arTMPDadosInicial = explode( "/", $this->getCodLicencaInicial() );
            $arTMPDadosFinal = explode( "/", $this->getCodLicencaFinal() );
            $stFiltro .= " elic.cod_licenca BETWEEN ".$arTMPDadosInicial[0]." AND ".$arTMPDadosFinal[0]." AND\n";
            $stFiltro .= " elic.exercicio BETWEEN ".$arTMPDadosInicial[1]." AND ".$arTMPDadosFinal[1]." AND\n";
        }else
            if ( $this->getCodLicencaInicial() && !$this->getCodLicencaFinal() ) {
                $arTMPDadosInicial = explode( "/", $this->getCodLicencaInicial() );
                $stFiltro .= " elic.cod_licenca = ".$arTMPDadosInicial[0]." AND\n";
                $stFiltro .= " elic.exercicio = ".$arTMPDadosInicial[1]." AND\n";
            }else
                if ( !$this->getCodLicencaInicial() && $this->getCodLicencaFinal() ) {
                    $arTMPDadosFinal = explode( "/", $this->getCodLicencaFinal() );
                    $stFiltro .= " elic.cod_licenca = ".$arTMPDadosFinal[0]." AND\n";
                    $stFiltro .= " elic.exercicio = ".$arTMPDadosFinal[1]." AND\n";
                }
    }

    $stFiltro = " WHERE ". substr ( $stFiltro, 0, (strlen ($stFiltro) - 4) );

    $stOrdem = " ORDER BY A.cod_atividade \n";
    $this->obTCEMRelatorioCE->recuperaAtividadeRelatorio( $rsRecordSet, $stFiltro , $stOrdem , $boTransacao );

    return $obErro;
}

function geraSocios(&$rsRecordSet , $arSocios)
{
    $stFiltro = null;
    if ( $this->getInscricaoAtual()  ) {
        $stFiltro .= " cesocio.inscricao_economica = ". $this->getInscricaoAtual() ." AND \n";
    }
    if ($arSocios) {
        $stSocios = str_replace ( '}', '', str_replace ( '{', '', $arSocios ) );
        $stFiltro .= " cesocio.numcgm in (". $stSocios .") AND \n";
    }

    $stFiltro = " WHERE " . substr ( $stFiltro, 0, (strlen ($stFiltro) - 5) );

    $stOrdem = " GROUP BY 1,2,3
                 ORDER BY  cgm.nom_cgm
                 ) as consulta

                 JOIN economico.sociedade ON
                      sociedade.numcgm = consulta.numcgm AND
                      sociedade.timestamp = consulta.timestamp   \n";

    $this->obTCEMRelatorioCE->recuperaSociedadeRelatorio( $rsRecordSet, $stFiltro , $stOrdem , $boTransacao );

    return $obErro;

}

function geraAtributosDinamicos(&$rsRecordSet)
{
    $stFiltro = null;
    $stFiltro .= " ad.ativo = true and                                          \n";
    $stFiltro .= " efv.inscricao_economica = ". $this->getInscricaoAtual() ." OR\n";
    $stFiltro .= " eav.inscricao_economica = ". $this->getInscricaoAtual() ." OR\n";
    $stFiltro .= " edv.inscricao_economica = ". $this->getInscricaoAtual() ." AND\n";
    $stFiltro = " WHERE ". substr ( $stFiltro, 0, (strlen ($stFiltro) - 4) );

    $stOrdem  = "  ORDER BY                                                         \n";
    $stOrdem .= "     CASE WHEN efv.cod_atributo is not null THEN                   \n";
    $stOrdem .= "               efv.cod_atributo                                    \n";
    $stOrdem .= "          WHEN eav.cod_atributo is not null THEN                   \n";
    $stOrdem .= "               eav.cod_atributo                                    \n";
    $stOrdem .= "          ELSE                                                     \n";
    $stOrdem .= "              edv.cod_atributo                                     \n";
    $stOrdem .= "          END                                                      \n";
    $this->obTCEMRelatorioCE->recuperaAtributosDinamicos( $rsRecordSet, $stFiltro , $stOrdem , $boTransacao );
    //$this->obTCEMRelatorioCE->debug();
    return $obErro;

}

}
