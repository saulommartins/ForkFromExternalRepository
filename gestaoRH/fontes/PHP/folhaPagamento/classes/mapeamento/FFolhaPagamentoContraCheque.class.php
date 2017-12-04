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
    * Classe de mapeamento da tabela FFOLHAPAGAMENTOCONTRACHEQUE
    * Data de Criação: 24/01/2007

    * @author Desenvolvedor: Diego Lemos de Souza

    * Casos de uso: uc-04.05.30

    * $Id: FFolhaPagamentoContraCheque.class.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Data de Criação: 24/01/2007

  * @author Analista: Dagiane
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class FFolhaPagamentoContraCheque extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FFolhaPagamentoContraCheque()
{
    parent::Persistente();
    $this->setTabela('contraCheque');
}

function contraCheque(&$rsRecordSet,$boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $stSql  = $this->montaContraCheque().$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaContraCheque()
{
    $stSql .= "SELECT * FROM ".$this->getTabela()."(".$this->getDado('cod_periodo_movimentacao').",".$this->getDado('quant_evento').",'".$this->getDado('ordem')."',".$this->getDado('folha').",".$this->getDado('cod_complementar').",'".$this->getDado('desdobramento')."','".$this->getDado('filtro')."',".$this->getDado('registro_reemissao').",".$this->getDado("duplicar").",'".Sessao::getEntidade()."','".$this->getDado("situacao")."') as retorno  \n";

    return $stSql;
}

}
?>
