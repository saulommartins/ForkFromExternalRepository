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
    * Classe de Regra de Plano Conta Geral
    * Data de Criação   : 08/10/2012

    * @author Analista: Tonismar
    * @author Desenvolvedor: Eduardo

    * @package URBEM
    * @subpackage Regra
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_FW_BANCO_DADOS.'Transacao.class.php';
include_once CAM_GA_ADM_NEGOCIO.'RAdministracaoUF.class.php';
include_once CAM_GF_CONT_MAPEAMENTO.'TContabilidadePlanoContaEstrutura.class.php';
include_once CAM_GF_CONT_MAPEAMENTO.'FContabilidadeDeletarEscolhaPlanoContas.class.php';
include_once CAM_GF_CONT_MAPEAMENTO.'FContabilidadeIncluirEscolhaPlanoContas.class.php';

/**
    * Classe de Regra de Plano Conta Estrutura
*/
class RContabilidadePlanoContaEstrutura
{
/**
    * @access Private
    * @var Object
*/
var $obRAdministracaoUF;
/**
    * @access Private
    * @var Object
*/
var $obTransacao;
/**
    * @access Private
    * @var Integer
*/
var $inCodPlano;
/**
    * @access Private
    * @var String
*/
var $stCodigoEstrutural;
/**
    * @access Private
    * @var String
*/
var $stTitulo;
/**
    * @access Private
    * @var Text
*/
var $stFuncao;
/**
    * @access Private
    * @var Char
*/
var $stNaturezaSaldo;
/**
    *@access Private
    *@var Char
*/
var $stEscrituracao;
/**
    *@access Private
    *@var Char
*/
var $stNaturezaInformacao;
/**
    *@access Private
    *@var Char
*/
var $stIndicadorSuperavit;
/**
    *@access Private
    *@var Char
*/
var $stExercicio;

/**
    * @access Public
    * @param Object $Valor
*/
function setRAdministracaoUF($valor) { $this->obRAdministracaoUF = $valor; }
/**
    * @access Public
    * @param Object $Valor
*/
function setTransacao($valor) { $this->obTransacao = $valor; }
/**
    * @access Public
    * @param Integer $Valor
*/
function setCodPlano($valor) { $this->inCodPlano = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setCodigoEstrutural($valor) { $this->stCodigoEstrutural = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setTitulo($valor) { $this->stTitulo = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setFuncao($valor) { $this->stFuncao = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setNaturezaSaldo($valor) { $this->stNaturezaSaldo = $valor; }
/**
    * @access public
    * @param String $valor
*/
function setEscrituracao($valor) { $this->stEscrituracao = $valor; }
/**
    * @access public
    * @param String $valor
*/
function setNaturezaInformacao($valor) { $this->stNaturezaInformacao = $valor; }
/**
    * @access public
    * @param String $valor
*/
function setIndicadorSuperavit($valor) { $this->stIndicadorSuperavit = $valor; }
/**
    * @access public
    * @param String $valor
*/
function setExercicio($valor) { $this->stExercicio = $valor; }

/**
    * @access Public
    * @return Object
*/
function getRAdministracaoUF() { return $this->obRAdministracaoUF; }
/**
    * @access Public
    * @return Object
*/
function getTransacao() { return $this->obTransacao; }
/**
    * @access Public
    * @return Integer
*/
function getCodPlano() { return $this->inCodPlano; }
/**
    * @access Public
    * @return String
*/
function getCodigoEstrutural() { return $this->stCodigoEstrutural; }
/**
    * @access Public
    * @return String
*/
function getTitulo() { return $this->stTitulo; }
/**
    * @access Public
    * @return String
*/
function getFuncao() { return $this->stFuncao; }
/**
    * @access public
    * @return String
*/
function getNaturezaSaldo() { return $this->stNaturezaSaldo; }
/**
    * @access public
    * @return String
*/
function getEscrituracao() { return $this->stEscrituracao; }
/**
    * @access public
    * @return String
*/
function getNaturezaInformacao() { return $this->stNaturezaInformacao; }
/**
    * @access public
    * @return String
*/
function getIndicadorSuperavit() { return $this->stIndicadorSuperavit; }
/**
    * @access public
    * @return String
*/
function getExercicio() { return $this->stExercicio; }

/**
     * Método construtor
     * @access Public
*/
function RContabilidadePlanoContaEstrutura()
{
    $this->obRAdministracaoUF = new RUF;
    $this->obTransacao        = new Transacao;
}

/**
    * Executa um recuperaTodos na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listar(&$rsRecordSet, $stOrder = "" , $boTransacao = "")
{
    $obTContabilidadePlanoContaEstrutura = new TContabilidadePlanoContaEstrutura;

    if ($this->obRAdministracaoUF->getCodigoUF() != "")
        $stFiltro  = " cod_uf = " . $this->obRAdministracaoUF->getCodigoUF() . "  AND ";
    if($this->inCodPlano != "")
        $stFiltro .= " cod_plano = " . $this->inCodPlano . " AND ";

    $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";

    $stOrder = ($stOrder) ? $stOrder : "codigo_estrutural";
    $obErro = $obTContabilidadePlanoContaEstrutura->recuperaTodos($rsRecordSet, $stFiltro, $stOrder, $boTransacao);

    return $obErro;
}

/**
    * Executa um listarContasParaIncluir na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarContasParaIncluir(&$rsRecordSet, $stOrder = "" , $boTransacao = "")
{
    $obTContabilidadePlanoContaEstrutura = new TContabilidadePlanoContaEstrutura;

    if ($this->obRAdministracaoUF->getCodigoUF() != "")
        $obTContabilidadePlanoContaEstrutura->setDado('cod_uf', $this->obRAdministracaoUF->getCodigoUF());

    if($this->inCodPlano != "")
        $obTContabilidadePlanoContaEstrutura->setDado('cod_plano', $this->inCodPlano);

    if($this->stExercicio)
        $obTContabilidadePlanoContaEstrutura->setDado('exercicio', $this->stExercicio);

    $obErro = $obTContabilidadePlanoContaEstrutura->listarContasParaIncluir($rsRecordSet, '', '', $boTransacao);

    return $obErro;
}

/**
    * Deleta as contas sem movimentação
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function deletarContasSemMovimentacao(&$rsRecordSet, $stOrder = "" , $boTransacao = "")
{
    $obFContabilidadeDeletarEscolhaPlanoContas = new FContabilidadeDeletarEscolhaPlanoContas;
    $obFContabilidadeDeletarEscolhaPlanoContas->setDado('exercicio', $this->stExercicio);

    $obErro = $obFContabilidadeDeletarEscolhaPlanoContas->recuperaTodos($rsRecordSet, '', '', $boTransacao);

    return $obErro;
}

/**
    * Inclui as contas do plano de contas selecionado
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluirEscolhaPlanoContas(&$rsRecordSet, $stOrder = "" , $boTransacao = "")
{
    $obFContabilidadeIncluirEscolhaPlanoContas = new FContabilidadeIncluirEscolhaPlanoContas;
    $obFContabilidadeIncluirEscolhaPlanoContas->setDado('exercicio', $this->stExercicio);
    $obFContabilidadeIncluirEscolhaPlanoContas->setDado('cod_uf'   , $this->obRAdministracaoUF->getCodigoUF());
    $obFContabilidadeIncluirEscolhaPlanoContas->setDado('cod_plano', $this->inCodPlano);

    $obErro = $obFContabilidadeIncluirEscolhaPlanoContas->recuperaTodos($rsRecordSet, '', '', $boTransacao);

    return $obErro;
}
}
