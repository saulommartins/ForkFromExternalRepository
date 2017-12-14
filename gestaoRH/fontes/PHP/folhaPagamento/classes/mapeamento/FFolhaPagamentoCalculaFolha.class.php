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
    * Classe de mapeamento da tabela FFOLHAPAGAMENTOCALCULAFOLHA
    * Data de Criação: 06/12/2005

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2008-01-09 16:03:59 -0200 (Qua, 09 Jan 2008) $

    * Casos de uso: uc-04.05.09
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Data de Criação: 06/12/2005

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class FFolhaPagamentoCalculaFolha extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FFolhaPagamentoCalculaFolha()
{
    parent::Persistente();
    $this->setTabela('CalculaFolha');
}

function calculaFolha(&$rsRecordSet,$boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $stSql  = $this->montaCalculaFolha();
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaCalculaFolha()
{
    $stSql = "SELECT ".$this->getTabela()."(".$this->getDado('cod_contrato').",1,'".$this->getDado('boErro')."','".Sessao::getEntidade()."','".Sessao::getExercicio()."') as retorno  \n";

    return $stSql;
}

}
?>
