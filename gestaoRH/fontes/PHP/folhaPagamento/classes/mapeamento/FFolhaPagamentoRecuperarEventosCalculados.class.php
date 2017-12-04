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
    * Classe de mapeamento da funcao recuperarEventosCalculados
    * Data de Criação: 13/10/2009

    * @author Analista: Dagiane
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

    * @package URBEM
    * @subpackage Mapeamento

    $Id: $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
    * Data de Criação: 13/10/2009

    * @author Analista: Dagiane
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

  * @package URBEM
  * @subpackage Mapeamento
*/
class FFolhaPagamentoRecuperarEventosCalculados extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FFolhaPagamentoRecuperarEventosCalculados()
{
    parent::Persistente();
    $this->setTabela('recuperarEventosCalculados');
}

function recuperarEventosCalculados(&$rsRecordSet,$stFiltro='',$stOrdem='',$boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $stSql  = $this->montaRecuperarEventosCalculados().$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperarEventosCalculados()
{
    $stSql .= "SELECT *
                 FROM ".$this->getTabela()."( ".$this->getDado('cod_configuracao')."
                                            , ".$this->getDado('cod_periodo_movimentacao')."
                                            , ".($this->getDado('cod_contrato')?$this->getDado('cod_contrato'):0)."
                                            , ".($this->getDado('cod_complementar')?$this->getDado('cod_complementar'):0)."
                                            , '".Sessao::getEntidade()."'
                                            , '".$this->getDado('ordem')."')\n";

    return $stSql;
}

}
?>
