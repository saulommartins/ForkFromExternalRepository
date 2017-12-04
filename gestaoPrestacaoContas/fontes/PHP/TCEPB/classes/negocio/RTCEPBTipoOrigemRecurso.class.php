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
/*
 * Arquivo de negócio da tabela tcepb.tipo_origem_recurso
 * Data de Criação   : 05/02/2009

 * @author Analista      Tonismar Regis Bernardo
 * @author Desenvolvedor Eduardo Paculski Schitz

 * @package URBEM
 * @subpackage

 $Id:$
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_FW_BANCO_DADOS."Transacao.class.php";

class RTCEPBTipoOrigemRecurso
{
/**
    * @var Object
    * @access Private
*/
var $obTransacao;
/**
    * @var String
    * @access Private
*/
var $stExercicio;
/**
     * @access Public
     * @param Object $valor
*/
function setTransacao($valor) { $this->obTransacao                   = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setExercicio($valor) { $this->stExercicio                   = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function getTransacao() { return $this->obTransacao;                            }
/**
     * @access Public
     * @param String $valor
*/
function getExercicio() { return $this->stExercicio;                            }
/**
    * Método Construtor
    * @access Private
*/
function RTCEPBTipoOrigemRecurso()
{
    $this->setExercicio              	( Sessao::getExercicio()           );
    $this->setTransacao              	( new Transacao                );
}

/**
    * Lista todos as origens de recurso de acordo com o filtro
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function recuperaOrigemRecurso(&$rsLista, $stOrder = "cod_tipo", $obTransacao = "")
{
    include_once CAM_GPC_TPB_MAPEAMENTO."TTPBTipoOrigemRecursos.class.php";
    $obTTPBTipoOrigemRecurso = new TTPBTipoOrigemRecurso;

    if (Sessao::getExercicio() < '2009') {
        $obTTPBTipoOrigemRecurso->setDado('exercicio', '2008');
    } else {
        $stCampo = 'exercicio';
        $stTabela = 'tcepb.tipo_origem_recurso';
        $stFiltro  = "WHERE exercicio = '".Sessao::getExercicio()."'";
        $stFiltro .= ' LIMIT 1 ';
        $stExercicio = SistemaLegado::pegaDado($stCampo, $stTabela, $stFiltro);
        if ($stExercicio) {
            $obTTPBTipoOrigemRecurso->setDado('exercicio', $stExercicio."::VARCHAR");
        } else {
            $stFiltroExercicio = '(SELECT MAX(exercicio) FROM tcepb.tipo_origem_recurso)';
            $obTTPBTipoOrigemRecurso->setDado('exercicio', $stFiltroExercicio."::VARCHAR");
        }
    }

    $obErro = $obTTPBTipoOrigemRecurso->recuperaTodos($rsLista, '', $stOrder, $obTransacao);

    return $obErro;
}

}
