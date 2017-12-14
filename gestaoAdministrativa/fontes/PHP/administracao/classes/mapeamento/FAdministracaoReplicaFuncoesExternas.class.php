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
    * Classe de mapeamento da tabela FAdministracaoReplicaFuncoesExternas
    * Data de Criação: 19/09/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso: uc-01.03.95

    $Id: FAdministracaoReplicaFuncoesExternas.class.php 59612 2014-09-02 12:00:51Z gelson $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Data de Criação: 09/09/2006

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class FAdministracaoReplicaFuncoesExternas extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FAdministracaoReplicaFuncoesExternas()
{
    parent::Persistente();
    $this->setTabela('replicaFuncoesExternas');
}

function replicaFuncoesExternas(&$rsRecordSet,$boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $stSql  = $this->montaReplicaFuncoesExternas().$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaReplicaFuncoesExternas()
{
    $stSql .= "SELECT ".$this->getTabela()."(".$this->getDado('cod_modulo_origem').",".$this->getDado('cod_biblioteca_origem').",".$this->getDado('cod_funcao_origem').",".$this->getDado('cod_modulo_destino').",".$this->getDado('cod_biblioteca_destino').",'".$this->getDado('nom_funcao')."') as retorno  \n";

    return $stSql;
}

}
?>
