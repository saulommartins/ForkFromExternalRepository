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
* Classe de negócio Processo
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 28826 $
$Name$
$Author: rodrigosoares $
$Date: 2008-03-27 16:33:30 -0300 (Qui, 27 Mar 2008) $

Casos de uso: uc-01.06.98
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php" );
include_once ( CAM_GA_PROT_MAPEAMENTO."TProcesso.class.php"    );
include_once ( CAM_GA_CGM_NEGOCIO."RCGM.class.php"              );

/**
* Classe de regra de negócio para Processo
* Data de Criação: 24/11/2004

* @author Analista: Ricardo Lopes de Alencar
* @author Desenvolvedor: Fábio Bertoldi Rodrigues

* @package URBEM
* @subpackage Regra
*/

class RProcesso
{
/**
* @access Private
* @var Integer
*/
var $inCodigoProcesso;
/*
* @access Private
* @var String
*/
var $stExercicio;
/*
* @access Private
* @var Object
*/
var $obTProcesso;
/**
* @access Private
* @var Integer
*/
var $inCodigoAssunto;
/**
* @access Private
* @var Integer
*/
var $inCodigoClassificacao;
/**
* @access Private
* @var Integer
*/
var $inCodigoSituacao;

//SETTERS
/**
* @access Public
* @param Integer $valor
*/
function setCodigoProcesso($valor) { $this->inCodigoProcesso = $valor; }
/**
* @access Public
* @param String $valor
*/
function setExercicio($valor) { $this->stExercicio = $valor;      }
/**
* @access Public
* @param Integer $valor
*/
function setCodigoAssunto($valor) { $this->inCodigoAssunto = $valor;  }
/**
* @access Public
* @param Integer $valor
*/
function setCodigoClassificacao($valor) { $this->inCodigoClassificacao = $valor; }
/**
* @access Public
* @param Integer $valor
*/
function setCodigoSituacao($valor) { $this->inCodigoSituacao = $valor; }

//GETTERS
/**
* @access Public
* @return Integer
*/
function getCodigoProcesso() { return $this->inCodigoProcesso; }
/**
* @access Public
* @return String
*/
function getExercicio() { return $this->stExercicio;      }
/**
* @access Public
* @return Integer
*/
function getCodigoAssunto() { return $this->inCodigoAssunto; }
/**
* @access Public
* @return Integer
*/
function getCodigoClassificacao() { return $this->inCodigoClassificacao; }
/**
* @access Public
* @return Integer
*/
function getCodigoSituacao() { return $this->inCodigoSituacao; }

//METODO CONSTRUTOR
/**
* Método construtor
* @access Private
*/
function RProcesso()
{
    $this->obTProcesso = new TProcesso;
    $this->obTransacao = new Transacao;
    $this->obRCGM      = new RCGM;
}

/**
    * Recupera do banco de dados os dados do Processo selecionado
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultarProcesso($boTransacao = "")
{
    $this->obTProcesso->setDado( "cod_processo"   , $this->inCodigoProcesso    );
    $obErro = $this->obTProcesso->recuperaPorChave( $rsProcesso, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->stExercicio = $rsProcesso->getCampo( "ano_exercicio" );
    }

    return $obErro;
}

/**
    * Recupera do banco de dados os dados do Processo selecionado
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarProcesso(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";

    if ( $this->obRCGM->getNumCGM() ) {
        $stFiltro .= " AND PI.numcgm = ". $this->obRCGM->getNumCGM()." ";
    }

    if ( $this->obRCGM->getNomCGM() ) {
        $stFiltro .= " AND G.nom_cgm = '".$this->obRCGM->getNomCGM()."' ";
    }

    if ($this->inCodigoClassificacao) {
        $stFiltro .=" AND P.cod_classificacao = ". $this->inCodigoClassificacao." ";
    }

    if ($this->inCodigoAssunto) {
        $stFiltro .=" AND P.cod_assunto = ". $this->inCodigoAssunto." ";
    }

    if ($this->stExercicio) {
        $stFiltro .=" AND P.ano_exercicio = '".$this->stExercicio."' ";
    }

    if ($this->inCodigoSituacao) {
        $stFiltro .=" AND P.cod_situacao = ".$this->inCodigoSituacao." ";
    }

    $obErro = $this->obTProcesso->recuperaProcesso( $rsRecordSet, $stFiltro, '', $boTransacao );

    return $obErro;
}

/**
    * Verifica se o processo consultado é valido
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function validarProcesso($boTransacao = "")
{
    $this->obTProcesso->setDado( "cod_processo"   , $this->inCodigoProcesso    );
    $stFiltro  = " WHERE cod_processo = ".$this->inCodigoProcesso. " AND ";
    $stFiltro .= " ano_exercicio = '".$this->stExercicio."' ";
    $obErro = $this->obTProcesso->recuperaTodos( $rsProcessos, $stFiltro, "", $boTransacao );
    if ( !$obErro->ocorreu() and $rsProcessos->eof() ) {
       $obErro->setDescricao( "Processo inexistente!" );
    }

    return $obErro;
}

/**
    * Busca um assunto de acordo com a classificação setada
    * Este método é temporário nesta classe, foi criado aqui por não haver nenhuma
    * das classes necessárias para sua criação adequada
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @param  Object $rsRecordSet
    * @return Object Objeto Erro
*/
function listarAssunto(&$rsRecordSet, $boTransacao = "")
{
    include_once( CAM_GA_PROT_MAPEAMENTO."TAssunto.class.php" );

    $obTAssunto = new TAssunto;
    $stFiltro = "";
    $stOrdem  = "";

    if ($this->inCodigoAssunto) {
        $stFiltro = " cod_assunto = " .$this->inCodigoAssunto. " and ";
    }

    if ($this->inCodigoClassificacao) {
        $stFiltro = " cod_classificacao = " .$this->inCodigoClassificacao. " and ";
    }

    $stFiltro = ($stFiltro)?' WHERE '.substr($stFiltro,0,strlen($stFiltro)-4):'';

    $stOrdem = " ORDER BY cod_assunto ";

    $obErro = $obTAssunto->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}
}
