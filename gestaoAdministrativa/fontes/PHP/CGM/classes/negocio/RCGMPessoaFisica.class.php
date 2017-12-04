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
* Classe de negócio para tratamento de CGM Pessoa Física
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 27796 $
$Name$
$Author: rodrigosoares $
$Date: 2008-01-28 16:04:26 -0200 (Seg, 28 Jan 2008) $

Casos de uso: uc-01.02.92, uc-01.02.93, uc-04.04.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
//include_once    ( "../../../includes/Constante.inc.php" );
include_once ( CAM_GA_CGM_MAPEAMENTO."TCGMPessoaFisica.class.php"   );
include_once ( CAM_GA_CGM_NEGOCIO."RCGM.class.php"       				);
include_once ( CAM_GA_CGM_MAPEAMENTO."TEscolaridade.class.php" );

class RCGMPessoaFisica extends RCGM
{
var $obTCGMPessoaFisica;
var $dtNascimento;
var $stPisPasep;

//SETTERS
function setTCGMPessoaFisica($valor) { $this->obTCGMPessoaFisica       = $valor;  }
function setCodCategoriaCNH($valor) { $this->inCodCategoriaCNH        = $valor;  }
function setRG($valor) { $this->stRG				       = $valor;  }
function setEmissaoRG($valor) { $this->dtEmissaoRG		       = $valor;  }
function setOrgaoEmissor($valor) { $this->stOrgaoEmissor	       = $valor;  }
function setCPF($valor) { $this->stCPF				       = $valor;  }
function setNumCNH($valor) { $this->stNumCNH			       = $valor;  }
function setNomCNH($valor) { $this->stNomCNH                 = $valor;  }
function setValidadeCNH($valor) { $this->dtValidadeCNH		       = $valor;  }
function setCodNacionalidade($valor) { $this->inCodNacionalidade       = $valor;  }
function setCodEscolaridade($valor) { $this->inCodEscolaridade        = $valor;  }
function setDataNascimento($valor) { $this->dtNascimento             = $valor;  }
function setPISPASEP($valor) { $this->stPisPasep               = $valor;  }

//GETTERS
function getTCGMPessoaFisica() { return $this->obTCGMPessoaFisica;       	}
function getCodCategoriaCNH() { return $this->inCodCategoriaCNH;       	}
function getRG() { return $this->stRG;       				}
function getEmissaoRG() { return $this->dtEmissaoRG;       		}
function getOrgaoEmissor() { return $this->stOrgaoEmissor;       		}
function getCPF() { return $this->stCPF;       				}
function getNumCNH() { return $this->stNumCNH;       			}
function getNomCNH() { return $this->stNomCNH;                  }
function getValidadeCNH() { return $this->dtValidadeCNH;       		}
function getCodNacionalidade() { return $this->inCodNacionalidade;       	}
function getCodEscolaridade() { return $this->inCodEscolaridade;       	}
function getDataNascimento() { return $this->dtNascimento;              }
function getPISPASEP() { return $this->stPisPasep;                }

//METODO CONSTRUTOR
/**
     * Método construtor
     * @access Private
*/
function RCGMPessoaFisica()
{
    parent::RCGM();
    $this->setTCGMPessoaFisica( new TCGMPessoaFisica );
}

function listarCGM(&$rsRecordSet, $stOrder = "", $boTransacao = "")
{
    if ( $this->getRG() ) {
        $stFiltro .= " rg =  AND \n";
    }
    $obErro = $this->obTCGMPessoaFisica->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

function consultarCGM(&$rsRecordSet, $boTransacao = "")
{
    $stOrder = "";
    if ( $this->getNumCGM() ) {
        $stFiltro = " AND CGM.numcgm = ".$this->getNumCGM();
    }
    $obErro = $this->obTCGMPessoaFisica->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $this->setNumCGM 			( $rsRecordSet->getCampo('numcgm')              );
        $this->setNomCGM			( $rsRecordSet->getCampo('nom_cgm') 			);
        $this->setCodCategoriaCNH	( $rsRecordSet->getCampo('cod_categoria_cnh') 	);
        $this->setRG				( $rsRecordSet->getCampo('rg') 					);
        $this->setEmissaoRG			( $rsRecordSet->getCampo('dt_emissao_rg') 		);
        $this->setOrgaoEmissor		( $rsRecordSet->getCampo('orgao_emissao') 		);
        $this->setCPF				( $rsRecordSet->getCampo('cpf') 				);
        $this->setNumCNH			( $rsRecordSet->getCampo('num_cnh') 			);
        $this->setNomCNH            ( $rsRecordSet->getCampo('nom_cnh')             );
        $this->setValidadeCNH		( $rsRecordSet->getCampo('dt_validade_cnh') 	);
        $this->setCodNacionalidade	( $rsRecordSet->getCampo('cod_nacionalidade') 	);
        $this->setCodEscolaridade	( $rsRecordSet->getCampo('cod_escolaridade') 	);
        $this->setPISPASEP          ( $rsRecordSet->getCampo('servidor_pis_pasep')  );
    }

    return $obErro;
}

function consultarEscolaridade(&$stDescEscolaridade, $boTransacao = "")
{
    $obErro = new Erro();
    if ( $this->getCodEscolaridade() ) {
        $this->obTEscolaridade = new TEscolaridade;
        $this->obTEscolaridade->setDado('cod_escolaridade', $this->getCodEscolaridade() );
        $this->obTEscolaridade->recuperaPorChave($rsRecordSet, $boTransacao);
        $stDescEscolaridade = $rsRecordSet->getCampo('descricao');
    } else {
        $stDescEscolaridade = '';
    }

    return $obErro;
}

function consultarNacionalidade(&$stDescNascionalidade, $boTransacao = "")
{
    $obErro = new Erro();
    if ( $this->getCodNacionalidade() ) {
        include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoPais.class.php" );
        $obTAdministracaoPais = new TPais;
        $obTAdministracaoPais->setDado('cod_pais', $this->getCodNacionalidade() );
        $obTAdministracaoPais->recuperaPorChave($rsRecordSet, $boTransacao);
        $stDescNascionalidade = $rsRecordSet->getCampo('nacionalidade');
    } else {
        $stDescNascionalidade = '';
    }

    return $obErro;
}

}

?>
