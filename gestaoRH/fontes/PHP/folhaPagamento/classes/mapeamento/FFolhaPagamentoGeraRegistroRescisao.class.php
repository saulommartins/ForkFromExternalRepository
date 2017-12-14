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
    * Classe de mapeamento da tabela FFolhaPagamentoGeraRegistroRescisao
    * Data de Criação: 20/11/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 31795 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-07 13:03:23 -0300 (Qui, 07 Jun 2007) $

    * Casos de uso: uc-04.04.44
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Data de Criação: 02/08/2006

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class FFolhaPagamentoGeraRegistroRescisao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FFolhaPagamentoGeraRegistroRescisao()
{
    parent::Persistente();
    $this->setTabela('geraRegistroRescisao');
}

function geraRegistroRescisao(&$rsRecordSet,$boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $stSql  = $this->montaGeraRegistroRescisao().$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaGeraRegistroRescisao()
{
    $stSql .= "select removerTodosBuffers(); SELECT ".$this->getTabela()."(".$this->getDado('cod_contrato').",".$this->getDado('cod_periodo_movimentacao').",'".$this->getDado('exercicio_atual')."','".Sessao::getEntidade()."') as retorno  \n";

    return $stSql;
}

}
?>
