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
    * Classe de Regra para emissão do Plano de Contas
    * Data de Criação   : 25/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson Buzo
    * @author Desenvolvedor: Gelson W. Gonçalves

    * @package URBEM
    * @subpackage Regra

    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2007-05-28 12:43:13 -0300 (Seg, 28 Mai 2007) $

    * Casos de uso: uc-02.02.18
*/

/*
$Log$
Revision 1.8  2007/05/28 15:42:44  hboaventura
Bug #9301#

Revision 1.7  2006/07/05 20:50:26  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE_RELATORIO          );
include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadePlanoBanco.class.php"          );

/**
    * Classe de Regra para emissão do Plano de Contas com Banco/Recurso
    * @author Desenvolvedor: Gelson W. Gonçalves
*/
class RContabilidadeRelatorioBancoRecurso extends PersistenteRelatorio
{
/**
    * @var Object
    * @access Private
*/
var $obRContabilidadePlanoBanco;
/**
    * @var String
    * @access Private
*/
var $stFiltro;
/**
    * @var String
    * @access Private
*/
var $stExercicio;

/**
    * @var String
    * @access Private
*/
var $stEntidades;

/**
    * @var Array
    * @access Private
*/
var $arCodEstrutural;

/**
    * @var Array
    * @access Private
*/
var $arCodPlano;

/**
    * @var Integer
    * @access Private
*/
var $inCodBanco;

/**
    * @var Integer
    * @access Private
*/
var $inCodAgencia;

/**
    * @var Integer
    * @access Private
*/
var $inContaCorrente;

/**
    * @var Integer
    * @access Private
*/
var $inCodRecurso;

/**
    * @var String
    * @access Private
*/
var $stDescricao;

/**
    * @var Integer
    * @access Private
*/
var $inOrdenacao;

/**
     * @access Public
     * @param Object $valor
*/
function setRContabilidadePlanoBanco($valor) { $this->obRContabilidadePlanoBanco = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setFiltro($valor) { $this->stFiltro = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setExercicio($valor) { $this->stExercicio = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setEntidades($valor) { $this->stEntidades = $valor; }
/**
     * @access Public
     * @return Array
*/

function setCodPlano($valor) { return $this->arCodPlano = $valor; }
/**
     * @access Public
     * @return Array
*/

function setCodEstrutural($valor) { return $this->arCodEstrutural = $valor; }
/**
     * @access Public
     * @return Integer
*/
function setCodRecurso($valor) { return $this->inCodRecurso = $valor; }
/**
     * @access Public
     * @return Integer
*/

function setCodBanco($valor) { return $this->inCodBanco = $valor; }
/**
     * @access Public
     * @return Integer
*/

function setCodAgencia($valor) { return $this->inCodAgencia = $valor; }
/**
     * @access Public
     * @return Integer
*/
function setContaCorrente($valor) { return $this->inContaCorrente = $valor; }
/**
     * @access Public
     * @return Integer
*/

function setDescricao($valor) { return $this->stDescricao = $valor; }
/**
     * @access Public
     * @return Integer
*/

function setOrdenacao($valor) { return $this->inOrdenacao = $valor; }
/**
     * @access Public
     * @return String
*/

function getRContabilidadePlanoBanco() { return $this->obRContabilidadePlanoBanco; }
/**
     * @access Public
     * @return String
*/
function getFiltro() { return $this->stFiltro; }
/**
     * @access Public
     * @return Array
*/
function getCodPlano() { return $this->arCodPlano; }
/**
     * @access Public
     * @return Array
*/
function getCodEstrutural() { return $this->arCodEstrutural; }
/**
     * @access Public
     * @param String $valor
*/
function getEntidades() { return $this->stEntidades; }
/**
     * @access Public
     * @return Integer
*/
function getCodRecurso() { return $this->inCodRecurso; }
/**
     * @access Public
     * @return Integer
*/

function getCodBanco() { return $this->inCodBanco; }
/**
     * @access Public
     * @return Integer
*/

function getCodAgencia() { return $this->inCodAgencia; }
/**
     * @access Public
     * @return Integer
*/
function getContaCorrente() { return $this->inContaCorrente; }
/**
     * @access Public
     * @return Integer
*/

function getDescricao() { return $this->stDescricao; }
/**
     * @access Public
     * @return Integer
*/

function getOrdenacao() { return $this->inOrdenacao; }
/**
     * @access Public
     * @return String
*/
function getExercicio() { return $this->stExercicio; }
/**
    * Método Construtor
    * @access Private
*/
function RContabilidadeRelatorioBancoRecurso()
{
    $this->obRContabilidadePlanoBanco = new RContabilidadePlanoBanco;
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$rsRecordSet , $stOrder = "cod_estrutural")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoBanco.class.php"       );
    $obTContabilidadePlanoBanco   = new TContabilidadePlanoBanco;
    
    $arCodEstrutural = $this->getCodEstrutural();
    $arCodPlano = $this->getCodPlano();
    
    $obTContabilidadePlanoBanco->setDado('exercicio',$this->getExercicio());
    $obTContabilidadePlanoBanco->setDado('entidades',$this->getEntidades());
    $obTContabilidadePlanoBanco->setDado('estruturalInicial',$arCodEstrutural[0]);
    $obTContabilidadePlanoBanco->setDado('estruturalFinal',$arCodEstrutural[1]);
    $obTContabilidadePlanoBanco->setDado('codPlanoInicial',$arCodPlano[0]);
    $obTContabilidadePlanoBanco->setDado('codPlanoFinal',$arCodPlano[1]);
    $obTContabilidadePlanoBanco->setDado('recurso',$this->getCodRecurso());
    $obTContabilidadePlanoBanco->setDado('descricao',$this->getDescricao());
    $obTContabilidadePlanoBanco->setDado('ordenacao',$this->getOrdenacao());
    $obTContabilidadePlanoBanco->setDado('banco',$this->getCodBanco());
    $obTContabilidadePlanoBanco->setDado('agencia',$this->getCodAgencia());
    $obTContabilidadePlanoBanco->setDado('conta_corrente',$this->getContaCorrente());
    
    $obErro = $obTContabilidadePlanoBanco->recuperaRelatorioContaBanco( $rsRecordSet );
    return $obErro;
}

}
