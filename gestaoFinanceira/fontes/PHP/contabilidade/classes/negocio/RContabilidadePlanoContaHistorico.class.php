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
    * Classe de Regra de Plano Conta Historico
    * Data de Criação   : 08/10/2012

    * @author Analista: Tonismar
    * @author Desenvolvedor: Eduardo

    * @package URBEM
    * @subpackage Regra
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_FW_BANCO_DADOS.'Transacao.class.php';
include_once CAM_GA_ADM_NEGOCIO.'RAdministracaoUF.class.php';
include_once CAM_GF_CONT_MAPEAMENTO.'TContabilidadePlanoContaHistorico.class.php';

/**
    * Classe de Regra de Plano Conta Historico
*/
class RContabilidadePlanoContaHistorico
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
    * @var Timestamp
*/
var $tpTimestamp;
/**
    * @access Private
    * @var String
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
    * @access public
    * @param Timestamp $valor
*/
function setTimestamp($valor) { $this->tpTimestamp = $valor; }
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
    * @return Timestamp
*/
function getTimestamp() { return $this->tpTimestamp; }
/**
    * @access Public
    * @return String
*/
function getExercicio() { return $this->stExercicio; }

/**
     * Método construtor
     * @access Public
*/
function RContabilidadePlanoContaHistorico()
{
    $this->obRAdministracaoUF = new RUF;
    $this->obTransacao        = new Transacao;
}

/**
    * Executa um verificaPlanoExistente na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function verificaPlanoExistente(&$rsRecordSet, $stOrder = '', $boTransacao = '')
{
    $obTContabilidadePlanoContaHistorico = new TContabilidadePlanoContaHistorico;
    $stFiltro = '';

    if($this->getExercicio())
        $stFiltro .= " plano_conta_historico.exercicio = '" . $this->getExercicio() . "'  AND ";

    $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
    $obErro = $obTContabilidadePlanoContaHistorico->recuperaTodos($rsRecordSet, $stFiltro, $stOrder, $boTransacao);

    return $obErro;
}

/**
    * Executa um verificaUltimoPlanoEscolhido na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function verificaUltimoPlanoEscolhido(&$rsRecordSet, $stOrder = '', $boTransacao = '')
{
    $obTContabilidadePlanoContaHistorico = new TContabilidadePlanoContaHistorico;
    $stFiltro = '';

    if($this->getExercicio())
        $stFiltro .= " AND plano_conta_historico.exercicio = '" . $this->getExercicio() . "' ";

    $obErro = $obTContabilidadePlanoContaHistorico->verificaUltimoPlanoEscolhido($rsRecordSet, $stFiltro, $stOrder, $boTransacao);

    return $obErro;
}

/**
    * Incluir Plano Conta Historico
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function salvar($boTransacao = "")
{
    $obTContabilidadePlanoContaHistorico = new TContabilidadePlanoContaHistorico;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if (!$obErro->ocorreu()) {
        $obTContabilidadePlanoContaHistorico->setDado('cod_uf'   , $this->obRAdministracaoUF->getCodigoUF());
        $obTContabilidadePlanoContaHistorico->setDado('cod_plano', $this->inCodPlano);
        $obTContabilidadePlanoContaHistorico->setDado('exercicio', $this->stExercicio);
        $obTContabilidadePlanoContaHistorico->setDado('timestamp', $this->tpTimestamp);

        $obErro = $obTContabilidadePlanoContaHistorico->inclusao($boTransacao);
    }

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );

    return $obErro;
}

}
