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
    * Classe de regra de negócio para Atividade
    * Data de Criação: 19/11/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Tonismar Régis Bernardo
    * @package URBEM

    * @subpackage Regra

    * $Id: RCEMAtividadeProfissao.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.07
*/

/*
$Log$
Revision 1.3  2006/09/15 12:13:58  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMAtividadeCadastroEconomico.class.php");

/**
    * Classe de regra de negócio para Localizacao
    * Data de Criação: 17/09/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Tonismar Régis Bernardo
    * @package URBEM
    * @subpackage Regra
*/

class RCEMAtividadeProfissao extends RCEMNivelAtividade
{
/**
    * @access Private
    * @var Integer
*/
var $inCodigoProfissao;
/**
    * @access Private
    * @var Integer
*/
var $inCodigoAtividade;
/**
    * @access Private
    * @var String
*/
var $inCodigosAtividades;
/**
    * @access Private
    * @var Object
*/
var $obErro;
/**
    * @access Private
    * @var Object
*/
var $obTCEMAtividadeProfissao;

/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoAtividade($valor) { $this->inCodigoAtividade = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoProfissao($valor) { $this->inCodigoProfissao = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setCodigosAtividades($valor) { $this->inCodigosAtividades = $valor;   }

/**
    * @access Public
    * @return Integer
*/
function getCodigoAtividade() { return $this->inCodigoAtividade;  }
/**
    * @access Public
    * @param Integer $valor
*/
function getCodigoProfissao() { return $this->inCodigoProfissao; }
/**
    * @access Public
    * @return String
*/
function getCodigosAtividades() { return $this->inCodigosAtividades;    }

/**
     * Método construtor
     * @access Private
*/
function RCEMAtividadeProfissao()
{
    parent::RCEMNivelAtividade();
    $this->obTCEMAtividadeProfissao  = new TCEMAtividadeProfissao;
    $this->obErro                    = new Erro;
}

/**
    * Lista as Atividades segundo o filtro setado
    * @access Public
    * @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarAtividadesProfissoes(&$rsRecordSet , $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodigosAtividades) {

        $arAtividades = explode (',', $this->inCodigosAtividades );
        $cont = 0;
        while ($cont < count ($arAtividades)) {
            if ($arAtividades[$cont] != '') {
                $stFiltro .= " AP.cod_atividade = ". $arAtividades[$cont] ." OR ";
            }
            $cont++;
        }
        $stFiltro = " AND ".substr( $stFiltro, 0, strlen( $stFiltro ) - 3 );

    }

    if ($this->inCodigoProfissao) {
        $stFiltro .= " AND AP.cod_profissao = ". $this->inCodigoProfissao ."  ";
    }
    if ($this->inCodigoAtividade) {
        $stFiltro .= " AND AP.cod_atividade = ". $this->inCodigoAtividade ." ";
    }

    $stFiltro = " WHERE ".substr( $stFiltro, 4, strlen( $stFiltro ) );

    $stOrdem = " ORDER BY nom_profissao";
    $obErro = $this->obTCEMAtividadeProfissao->RecuperaAtividadesProfissoes( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );
//    $this->obTCEMAtividadeProfissao->debug();
    return $obErro;
}

/**
    * Lista as Atividades que estão definidas na inscrição
    * @access Public
    * @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarAtividadeInscricao(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";

    if ($this->inCodigoAtividade) {
        $stFiltro .= " COD_ATIVIDADE = ".$this->inCodigoAtividade." AND \r\n";
    }
    if ($stFiltro) {
        $stFiltro = "\r\n\t WHERE ".substr($stFiltro,0,-6);
    }

    $stOrdem = " ORDER BY cod_atividade";
    $obErro = $this->obTCEMAtividadeCadastroEconomico->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

}

?>
