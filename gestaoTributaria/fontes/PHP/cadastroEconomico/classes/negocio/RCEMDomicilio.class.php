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
    * Classe de regra de negócio para Domicilio Informado
    * Data de Criação: 11/04/2005

    * @author Desenvolvedor: Diego Bueno Coelho

    * @package URBEM
    * @subpackage Regra

    * $Id: RCEMDomicilio.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.10
*/

/*
$Log$
Revision 1.3  2006/09/15 12:13:58  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMDomicilioFiscal.class.php"  );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMDomicilioInformado.class.php"  );
//include_once ( CAM_GT_CEM_NEGOCIO."RCEMInscricaoEconomica.class.php"  );

class RCEMDomicilio
{
/**
    * @access Private
    * @var Integer
*/
var $inCodLogradouro;
/**
    * @access Private
    * @var String
*/
var $stNomLogradouro;
/**
    * @access Private
    * @var String
*/
var $stNomMunicipio;
/**
    * @access Private
    * @var String
*/
var $stNomUF;
/**
    * @access Private
    * @var Integer
*/
var $inCodBairro;
/**
    * @access Private
    * @var Integer
*/
var $inCEP;
/**
    * @access Private
    * @var Integer
*/
var $inCodMunicipio;
/**
    * @access Private
    * @var Integer
*/
var $inCodUF;
/**
    * @access Private
    * @var Integer
*/
var $stNumero;
/**
    * @access Private
    * @var String
*/
var $stComplemento;
/**
    * @access Private
    * @var String
*/
var $stCaixaPostal;
/**
    * @access Private
    * @var String
*/
var $stNomCategoria;
/**
    * @access Private
    * @var String
*/
var $stDomicilioFiscal;
/**
    * @access Private
    * @var String
*/
var $stDomicilioExibir;

/**
    * @access Private
    * @var Integer
*/
var $inscricaoEconomica;

//-------------------------------------------- SETTERS
/**
    * @access Public
    * @param Integer $valor
*/
function setCodBairro($valor) { $this->inCodBairro = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCEP($valor) { $this->inCEP = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodMunicipio($valor) { $this->inCodMunicipio = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodUF($valor) { $this->inCodUF = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setComplemento($valor) { $this->stComplemento = $valor;   }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodLogradouro($valor) { $this->inCodLogradouro = $valor;   }
/**
    * @access Public
    * @param Integer $valor
*/
function setNomLogradouro($valor) { $this->stNomLogradouro = $valor;   }
/**
    * @access Public
    * @param Integer $valor
*/
function setNomMunicipio($valor) { $this->stNomMunicipio = $valor;   }
/**
    * @access Public
    * @param Integer $valor
*/
function setNomUF($valor) { $this->stNomUF = $valor;   }
/**
    * @access Public
    * @param Integer $valor
*/
function setNumero($valor) { $this->stNumero = $valor;   }
/**
    * @access Public
    * @param String $valor
*/
function setCaixaPostal($valor) { $this->stCaixaPostal = $valor;  }
/**
    * @access Public
    * @param String
*/
function setDomicilioFiscal($valor) { $this->stDomicilioFiscal = $valor;  }
/**
    * @access Public
    * @param String
*/
function setDomicilioExibir($valor) { $this->stDomicilioExibir = $valor;  }
/**
    * @access Public
    * @param Integer
*/
function setInscricaoEconomica($valor) { $this->inscricaoEconomica = $valor;  }

//-------------------------------------------- GETTERS
/**
    * @access Public
    * @return Integer
*/
function getCodBairro() { return $this->inCodBairro; }
/**
    * @access Public
    * @return Integer
*/
function getCEP() { return $this->inCEP;   }
/**
    * @access Public
    * @return Integer
*/
function getCodMunicipio() { return $this->inCodMunicipio;   }
/**
    * @access Public
    * @return Integer
*/
function getCodUF() { return $this->inCodUF;   }
/**
    * @access Public
    * @return Integer
*/
function getNumero() { return $this->stNumero; }
/**
    * @access Public
    * @return Integer
*/
function getCodLogradouro() { return $this->inCodLogradouro;   }
/**
    * @access Public
    * @return Integer
*/
function getNomLogradouro() { return $this->stNomLogradouro;   }
/**
    * @access Public
    * @return Integer
*/
function getNomMunicipio() { return $this->stNomMunicipio;   }
/**
    * @access Public
    * @return Integer
*/
function getNomUF() { return $this->stNomUF;   }

/**
    * @access Public
    * @return String
*/
function getComplemento() { return $this->stComplemento; }
/**
    * @access Public
    * @return String
*/
function getCaixaPostal() { return $this->stCaixaPostal; }
/**
    * @access Public
    * @return String
*/
function getDomicilioFiscal() { return $this->stDomicilioFiscal;  }
/**
    * @access Public
    * @return String
*/
function getDomicilioExibir() { return $this->stDomicilioExibir;  }
/**
    * @access Public
    * @return Integer
*/
function getInscricaoEconomica() { return $this->inscricaoEconomica;  }

/**
    * Método construtor
    * @access Private
*/
function RCEMDomicilio()
{
    //mapeamentos
    $this->obTCEMDomicilioFiscal    = new TCEMDomicilioFiscal;
    $this->obTCEMDomicilioInformado = new TCEMDomicilioInformado;
    //negocios
    //$this->obRCEMInscricaoEconomica = new TCEMDomicilioInformado;

    $this->obTransacao     = new Transacao;
}

/**
    * Inclui uma novo registro no Domicilio Informado
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluirDomicilioInformado($boTransacao = "")
{
$boFlaTransacao = false;
$obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if ( !$obErro->ocorreu() ) {

        $this->obTCEMDomicilioInformado->setDado ( "inscricao_economica",$this->obInscricaoEconomica->getInscricaoEconomica() );
        $this->obTCEMDomicilioInformado->setDado ( "cod_logradouro",$this->inCodLogradouro );
        $this->obTCEMDomicilioInformado->setDado ( "numero" ,       $this->inNumero );
        $this->obTCEMDomicilioInformado->setDado ( "complemento" ,  $this->stComplemento );
        $this->obTCEMDomicilioInformado->setDado ( "cod_bairro",    $this->inCodBairro );
        $this->obTCEMDomicilioInformado->setDado ( "cod_cep" ,      $this->inCEP );
        $this->obTCEMDomicilioInformado->setDado ( "caixa_postal" , $this->stCaixaPostal );
        $this->obTCEMDomicilioInformado->setDado ( "cod_municipio" ,$this->inCodMunicipio );
        $this->obTCEMDomicilioInformado->setDado ( "cod_uf" ,       $this->inCodUF );
        $obErro = $this->obTCEMDomicilioInformado->inclusao( $boTransacao );

    }

    $this->obTransacao->fechaTransacao($boFlagTransacao,$boTransacao, $obErro, $this->obTCEMDomicilioInformado);

    return $obErro;

}

/**
    * Lista as Categorias
    * @access Public
    * @param  Object $rsRecordSet Objeto RecrdSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*//*
function listarCategoria(&$rsRecordSet , $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->getCodigoCategoria() ) {
      $stFiltro .= " cod_categoria = ".$this->getCodigoCategoria()." AND ";
    }
    if ( $this->getNomeCategoria() ) {
            $stFiltro .= " UPPER( nom_categoria ) like UPPER( '%".$this->getNomeCategoria()."%' ) AND ";
        }

    if ($stFiltro) {
         $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    }
    $stOrdem = " ORDER BY cod_categoria";
    $obErro = $this->obTCEMDomicilioInformado->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}*/

/**
    * Consulta as Categorias
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultarDomicilioAtual($boTransacao = "")
{
    $obErro = new Erro;
        if ($this->inCodigoCategoria) {
            $obErro = $this->listarCategoria( $rsCategoria, $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->stNomeCategoria   = $rsAtividade->getCampo( "nom_atividade" );
            }
        }

    return $obErro;

}

/**
    * Verifica Domicilio Atual - utilizada no FM para exibir os dados de domicilio da Inscricao Economica
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function verificaDomicilioAtual($boTransacao = "")
{
    $obErro = new Erro;

    $stFiltro .= " ce.inscricao_economica = ". $this->getInscricaoEconomica(). " AND ";
    $stFiltro = "\r\n\t AND  ".substr($stFiltro,0,-4);
    $stOrdem = " ORDER BY di.timestamp DESC limit 1 ";
    $obErro =     $this->obTCEMDomicilioInformado->recuperaDomicilioInformado($rsDI,$stFiltro,$stOrdem,$boTransacao );
   // $this->obTCEMDomicilioInformado->debug();
    if ( !$obErro->ocorreu() ) {

        $stFiltro = " inscricao_economica = ". $this->getInscricaoEconomica(). " AND ";
        $stFiltro = "\r\n\t WHERE ".substr($stFiltro,0,-4);
        $stOrdem = " ORDER BY timestamp DESC limit 1 ";

        $obErro = $this->obTCEMDomicilioFiscal->recuperaTodos( $rsDF, $stFiltro, $stOrdem, $boTransacao);
        //$this->obTCEMDomicilioFiscal->debug();
        if ( !$obErro->ocorreu () ) {

            if ( ($rsDI->getCampo ('timestamp') > $rsDF->getCampo ('timestamp')) || !$rsDF->getCampo ('timestamp') ) {

                $this->setDomicilioExibir ( 'EI' );

                $this->setCodLogradouro ( $rsDI->getCampo ('cod_logradouro') );
                $this->setNomLogradouro ( $rsDI->getCampo ('nom_logradouro') );
                $this->setNumero        ( $rsDI->getCampo ('numero') );
                $temp = $rsDI->getCampo('complemento');
                $temp = str_replace("'", "", $temp);

                $this->setComplemento   ( $temp );//$rsDI->getCampo ('complemento') );
                $this->setCodBairro     ( $rsDI->getCampo ('cod_bairro') );
                $this->setCEP           ( $rsDI->getCampo ('cep') );
                $this->setCaixaPostal   ( $rsDI->getCampo ('caixa_postal') );
                $this->setCodMunicipio  ( $rsDI->getCampo ('cod_municipio') );
                $this->setCodUF         ( $rsDI->getCampo ('cod_uf') );
                $this->setNomMunicipio  ( $rsDI->getCampo ('nom_municipio') );
                $this->setNomUF         ( $rsDI->getCampo ('nom_uf') );

            } else {

                $this->setDomicilioExibir ( 'IC' );
                $this->setDomicilioFiscal ( $rsDF->getCampo ('inscricao_municipal') );

            }
        }
    }

    return $obErro;
}

}
