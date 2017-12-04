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
    * Classe de mapeamento da funcao recuperaDespesaPorPAORubricaDespesa
    * Data de Criação: 10/06/2009

    * @author Analista: Dagiane
    * @author Desenvolvedor: Alex Cardoso

    * @package URBEM
    * @subpackage Mapeamento

    $Id: $

    * Casos de uso: uc-04.05.29
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
    * Data de Criação: 10/06/2009

    * @author Analista: Dagiane
    * @author Desenvolvedor: Alex Cardoso

  * @package URBEM
  * @subpackage Mapeamento
*/
class FFolhaPagamentoRecuperaDespesaPorPAORubricaDespesa extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FFolhaPagamentoRecuperaDespesaPorPAORubricaDespesa()
{
    parent::Persistente();
    $this->setTabela('recuperaDespesaPorPAORubricaDespesa');
}

function recuperaDespesaPorPAORubricaDespesa(&$rsRecordSet,$boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $stSql  = $this->montaRecuperaDespesaPorPAORubricaDespesa($boTransacao);
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDespesaPorPAORubricaDespesa(&$boTransacao)
{
    $stSql  = "SELECT *
                 FROM ".$this->getTabela()."( ".Sessao::getCodEntidade($boTransacao)."
                                             , ".$this->getDado('exercicio')."
                                             , ".$this->getDado('num_pao')."
                                             , '".$this->getDado('cod_estrutural')."')\n";

    return $stSql;
}

}
?>
