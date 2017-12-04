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
    * Classe de mapeamento da tabela FFOLHAPAGAMENTOCALCULAEVENTOPORBENEFICIARIO
    * Data de Criação: 27/03/2007

    * @author Analista: Dagiane
    * @author Desenvolvedor: André Machado

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-07 13:03:23 -0300 (Qui, 07 Jun 2007) $

    * Casos de uso: uc-04.05.57
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Data de Criação: 27/03/2007

  * @author Analista: Dagiane
  * @author Desenvolvedor: André Machado

  * @package URBEM
  * @subpackage Mapeamento
*/
class FFolhaPagamentoCalculaEventoPorBeneficiario extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FFolhaPagamentoCalculaEventoPorBeneficiario()
{
    parent::Persistente();
    $this->setTabela('CalculaEventoPorBeneficiario');
}

function calculaEventoPorBeneficiario(&$rsRecordSet,$boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $stSql  = $this->montaCalculaEventoPorBeneficiario().$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaCalculaEventoPorBeneficiario()
{
    $stSql .= "SELECT ".$this->getTabela()."(".$this->getDado('inCodContrato').",".$this->getDado('inCodBeneficiario').",'".Sessao::getEntidade()."') as retorno  \n";

    return $stSql;
}

}
?>
