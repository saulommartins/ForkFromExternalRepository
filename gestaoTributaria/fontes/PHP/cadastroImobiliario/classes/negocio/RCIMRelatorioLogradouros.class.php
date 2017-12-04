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
 * Classe de regra de Relatório de Logradouro
 * Data de Criação: 23/03/2005

 * @author Analista: Fábio Bertoldi Rodrigues
 * @author Desenvolvedor: Marcelo B. Paulino

 * @package URBEM
 * @subpackage Regra

 * $Id: RCIMRelatorioLogradouros.class.php 63656 2015-09-24 19:44:19Z evandro $

 * Casos de uso: uc-05.01.20
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_FW_BANCO_DADOS."PersistenteRelatorio.class.php";
include_once CAM_GT_CIM_NEGOCIO."RCIMLogradouro.class.php";
include_once CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php";

/**
    * Classe de Regra para relatório de Logradouros
    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Marcelo B. Paulino
*/
class RCIMRelatorioLogradouros extends PersistenteRelatorio
{
/**
    * @var Object
    * @access Private
*/
var $obTLogradouro;
/**
    * @var Object
    * @access Private
*/
var $obRCIMLogradouro;

/**
    * @var Boolean
    * @access Private
*/
var $boMostrarHistorico;

var $boMostrarNorma;

/**
    * @access Public
    * @param String $valor
*/
function setCodInicio($valor) { $this->inCodInicio         = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setCodInicioBairro($valor) { $this->inCodInicioBairro   = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setCodInicioCEP($valor) { $this->inCodInicioCEP      = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setCodTermino($valor) { $this->inCodTermino        = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setCodTerminoBairro($valor) { $this->inCodTerminoBairro = $valor;  }
/**
    * @access Public
    * @param String $valor
*/
function setCodTerminoCEP($valor) { $this->inCodTerminoCEP    = $valor;  }
/**
    * @access Public
    * @param String $valor
*/
function setOrder($valor) { $this->stOrder            = $valor;  }
/**
    * @access Public
    * @param Boolean $valor
*/
function setMostrarHistorico($valor) { $this->boMostrarHistorico   = $valor;  }

function setMostrarNorma($valor) { $this->boMostrarNorma   = $valor;  }

/**
    * @access Public
    * @return Integer
*/
function getCodInicio() { return $this->inCodInicio;       }
/**
    * @access Public
    * @return Integer
*/
function getCodInicioBairro() { return $this->inCodInicioBairro; }
/**
    * @access Public
    * @return Integer
*/
function getCodInicioCEP() { return $this->inCodInicioCEP;     }
/**
    * @access Public
    * @return Integer
*/
function getCodTermino() { return $this->inCodTermino;       }
/**
    * @access Public
    * @return Integer
*/
function getCodTerminoBairro() { return $this->inCodTerminoBairro; }
/**
    * @access Public
    * @return Integer
*/
function getCodTerminoCEP() { return $this->inCodTerminoCEP;    }
/**
    * @access Public
    * @return Integer
*/
function getOrder() { return $this->stOrder;            }

/**
    * @access Public
    * @return Boolean
*/
function getMostrarHistorico() { return $this->boMostrarHistorico; }

function getMostrarNorma() { return $this->boMostrarNorma; }

/**
    * Método Construtor
    * @access Private
*/
function RCIMRelatorioLogradouros()
{
    $this->obTLogradouro    = new TLogradouro;
    $this->obRCIMLogradouro = new RCIMLogradouro;
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$rsRecordSet , $stOrder = "")
{
    $stFiltro = "";
    if ( $this->obRCIMLogradouro->getCodigoMunicipio() ) {
        $stFiltro .= " sw_logradouro.cod_municipio  = ".$this->obRCIMLogradouro->getCodigoMunicipio()." \r\n AND";
    }// filtro codigo da uf
    if ( $this->obRCIMLogradouro->getCodigoUF() ) {
        $stFiltro .= " sw_uf.cod_uf = ".$this->obRCIMLogradouro->getCodigoUF()." \r\n AND";
    }// filtra tipo logradouro
    if ( $this->obRCIMLogradouro->getCodigoTipo() ) {
        $stFiltro .= " sw_tipo_logradouro.cod_tipo  = ".$this->obRCIMLogradouro->getCodigoTipo()." \r\n AND";
    }//filtra nome do bairro
    if ( $this->obRCIMLogradouro->obRCIMBairro->getNomeBairro() ) {
        $stFiltro .= " UPPER( sw_bairro.nom_bairro ) like UPPER( '".$this->obRCIMLogradouro->obRCIMBairro->getNomeBairro()."%' )\r\n AND";
    }//filtra nome logradouro
    if ( $this->obRCIMLogradouro->getNomeLogradouro() ) {
        $stFiltro .= " UPPER( sw_nome_logradouro.nom_logradouro ) like UPPER( '".$this->obRCIMLogradouro->getNomeLogradouro()."%' )\r\n AND";
    }
// filtros com between
    // INICIO E TERMINO PARA CODIGO DO LOGRADOURO
    if ( $this->getCodInicio() && $this->getCodTermino() ) {
        $stFiltro .= " sw_logradouro.cod_logradouro BETWEEN  ".$this->getCodInicio()."";
        $stFiltro .= " AND ".$this->getCodTermino()." \r\n AND";
    }
    if ( $this->getCodInicio() && !$this->getCodTermino() ) {
        $stFiltro .= " sw_logradouro.cod_logradouro BETWEEN  ".$this->getCodInicioBairro()."";
        $stFiltro .= " AND (select max(cod_logradouro) from ".$this->obRCIMLogradouro->obTLogradouro->getTabela().")  \r\n AND";
    }
    if ( !$this->getCodInicio() && $this->getCodTermino() ) {
        $stFiltro .= " sw_logradouro.cod_logradouro BETWEEN  0";
        $stFiltro .= " AND ".$this->getCodTermino()." \r\n AND";
   }
// cod bairro
    if ( $this->getCodInicioBairro() && $this->getCodTerminoBairro() ) {
        $stFiltro .= " sw_bairro.cod_bairro BETWEEN  ".$this->getCodInicioBairro()."";
        $stFiltro .= " AND ".$this->getCodTerminoBairro()." \r\n AND";
    }
    if ( $this->getCodInicioBairro() && !$this->getCodTerminoBairro() ) {
        $stFiltro .= " sw_bairro.cod_bairro BETWEEN  ".$this->getCodInicioBairro()."";
        $stFiltro .= " AND (select max(cod_bairro) from ".$this->obRCIMLogradouro->obRCIMBairro->obTBairro->getTabela().")  \r\n AND";
    }
    if ( !$this->getCodInicioBairro() && $this->getCodTerminoBairro() ) {
        $stFiltro .= " sw_bairro.cod_bairro BETWEEN  0";
        $stFiltro .= " AND ".$this->getCodTerminoBairro()." \r\n AND";
   }
// cod cep
    if ( $this->getCodInicioCEP() && $this->getCodTerminoCEP() ) {
        $stFiltro .= " imobiliario.fn_consulta_cep(sw_logradouro.cod_logradouro) BETWEEN  '".$this->getCodInicioCEP()."'";
        $stFiltro .= " AND '".$this->getCodTerminoCEP()."' \r\nAND";
    }
    if ( $this->getCodInicioCEP() && !$this->getCodTerminoCEP() ) {
        $stFiltro .= " imobiliario.fn_consulta_cep(sw_logradouro.cod_logradouro) BETWEEN  '".$this->getCodInicioCEP()."'";
        $stFiltro .= " AND '99999999' AND\r\n";
    }
    if ( !$this->getCodInicioCEP() && $this->getCodTerminoCEP() ) {
        $stFiltro .= " imobiliario.fn_consulta_cep(sw_logradouro.cod_logradouro) BETWEEN  '0'";
        $stFiltro .= " AND '".$this->getCodTerminoCEP()."' \r\nAND";
   }

    if ($stFiltro) {
        $stFiltro = "\r\n WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    }

    if ($this->getMostrarHistorico() == 'S') {
        Sessao::write('mostra_historico','true');
        if ($this->getOrder() == "codlogradouro") {
            $stOrder = " \n ORDER BY sw_logradouro.cod_logradouro , sw_nome_logradouro.dt_inicio DESC";
            $obErro = $this->obTLogradouro->recuperaHistoricoLogradouro( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );            
        } else { //Se for ordenado por NOME DE LOGRADOURO
            //Buscar todos os dados com o maxtimestamp
            $stOrder = " ORDER BY sw_nome_logradouro.nom_logradouro ASC";
            $obErro = $this->obTLogradouro->recuperaRelacionamentoRelatorio( $rsMaxLogradouro, $stFiltro, $stOrder, $boTransacao );
            //Retirar o max do registro de historico
            if (empty($stFiltro) ) {
                $stFiltro .= " WHERE sw_nome_logradouro.timestamp <> (SELECT max(timestamp) 
                                                            FROM sw_nome_logradouro as max 
                                                            WHERE max.cod_logradouro = sw_nome_logradouro.cod_logradouro)  ";
            }else{
                $stFiltro .= " AND sw_nome_logradouro.timestamp <> (SELECT max(timestamp) 
                                                            FROM sw_nome_logradouro as max 
                                                            WHERE max.cod_logradouro = sw_nome_logradouro.cod_logradouro)  ";
            }
            $stOrder = "ORDER BY cod_logradouro, sw_nome_logradouro.timestamp DESC";
            $obErro = $this->obTLogradouro->recuperaHistoricoLogradouro( $rsHistorico, $stFiltro, $stOrder, $boTransacao );    

            $rsRecordSet = new RecordSet();
            $inChave = 0;
            foreach ($rsMaxLogradouro->getElementos() as $valorMax) {
                    $arLogradouros[$inChave]['grupo']                     = $valorMax['grupo'];
                    $arLogradouros[$inChave]['cod_tipo']                  = $valorMax['cod_tipo'];
                    $arLogradouros[$inChave]['tipo_nome']                 = $valorMax['tipo_nome'];
                    $arLogradouros[$inChave]['nom_tipo']                  = $valorMax['nom_tipo'];
                    $arLogradouros[$inChave]['nom_logradouro']            = $valorMax['nom_logradouro'];
                    $arLogradouros[$inChave]['cod_logradouro']            = $valorMax['cod_logradouro'];
                    $arLogradouros[$inChave]['cod_uf']                    = $valorMax['cod_uf'];
                    $arLogradouros[$inChave]['cod_municipio']             = $valorMax['cod_municipio'];
                    $arLogradouros[$inChave]['cod_bairro']                = $valorMax['cod_bairro'];
                    $arLogradouros[$inChave]['nom_bairro']                = $valorMax['nom_bairro'];
                    $arLogradouros[$inChave]['nom_municipio']             = $valorMax['nom_municipio'];
                    $arLogradouros[$inChave]['nom_uf']                    = $valorMax['nom_uf'];
                    $arLogradouros[$inChave]['sigla_uf']                  = $valorMax['sigla_uf'];
                    $arLogradouros[$inChave]['cep']                       = $valorMax['cep'];
                    $arLogradouros[$inChave]['dt_inicio']                 = $valorMax['dt_inicio'];
                    $arLogradouros[$inChave]['dt_fim']                    = $valorMax['dt_fim'];
                    $arLogradouros[$inChave]['descricao_norma_relatorio'] = $valorMax['descricao_norma_relatorio'];
                foreach ($rsHistorico->getElementos() as $valorHistorico) {
                    if ($valorMax['cod_logradouro'] == $valorHistorico['cod_logradouro']) {                        
                        $inChave++;
                        $arLogradouros[$inChave]['grupo']                     = $valorHistorico['grupo'];
                        $arLogradouros[$inChave]['cod_tipo']                  = $valorHistorico['cod_tipo'];
                        $arLogradouros[$inChave]['tipo_nome']                 = $valorHistorico['tipo_nome'];
                        $arLogradouros[$inChave]['nom_tipo']                  = $valorHistorico['nom_tipo'];
                        $arLogradouros[$inChave]['nom_logradouro']            = $valorHistorico['nom_logradouro'];
                        $arLogradouros[$inChave]['cod_logradouro']            = $valorHistorico['cod_logradouro'];
                        $arLogradouros[$inChave]['cod_uf']                    = $valorHistorico['cod_uf'];
                        $arLogradouros[$inChave]['cod_municipio']             = $valorHistorico['cod_municipio'];
                        $arLogradouros[$inChave]['cod_bairro']                = $valorHistorico['cod_bairro'];
                        $arLogradouros[$inChave]['nom_bairro']                = $valorHistorico['nom_bairro'];
                        $arLogradouros[$inChave]['nom_municipio']             = $valorHistorico['nom_municipio'];
                        $arLogradouros[$inChave]['nom_uf']                    = $valorHistorico['nom_uf'];
                        $arLogradouros[$inChave]['sigla_uf']                  = $valorHistorico['sigla_uf'];
                        $arLogradouros[$inChave]['cep']                       = $valorHistorico['cep'];
                        $arLogradouros[$inChave]['dt_inicio']                 = $valorHistorico['dt_inicio'];
                        $arLogradouros[$inChave]['dt_fim']                    = $valorHistorico['dt_fim'];
                        $arLogradouros[$inChave]['descricao_norma_relatorio'] = $valorHistorico['descricao_norma_relatorio'];
                    }
                }
                $inChave++;
            }
            $rsRecordSet->preenche($arLogradouros);
        }
        
    }else{//Se for sem o historico dos logradouros
        Sessao::write('mostra_historico','false');
        if ($this->stOrder == "codlogradouro") {
            $stOrder = " ORDER BY sw_logradouro.cod_logradouro , sw_nome_logradouro.dt_inicio DESC";
        } else {
            $stOrder = " ORDER BY sw_nome_logradouro.nom_logradouro, sw_logradouro.cod_logradouro, sw_nome_logradouro.dt_inicio";
        }        
        $obErro = $this->obTLogradouro->recuperaRelacionamentoRelatorio( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    }

    return $obErro;

}



}
