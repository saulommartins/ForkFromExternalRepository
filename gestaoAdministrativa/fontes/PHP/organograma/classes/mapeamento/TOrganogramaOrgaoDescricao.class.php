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
   * Classe de Mapeamento
   * Data de Criação   : 15/01/2009

   * @author Analista      Gelson Golçalves
   * @author Desenvolvedor Alexandre Melo

   * @package URBEM
   * @subpackage

   $Id:$

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TOrganogramaOrgaoDescricao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TOrganogramaOrgaoDescricao()
{
    parent::Persistente();
    $this->setTabela('organograma.orgao_descricao');

    $this->setCampoCod('cod_orgao');
    $this->setComplementoChave('');

    $this->AddCampo('cod_orgao', 'integer',   true,  '',    true,  true);
    $this->AddCampo('timestamp', 'timestamp', true, '',     true,  false);
    $this->AddCampo('descricao', 'varchar',   true,  '100', false, false);

}

function recuperaUltimoOrgaoDescricao(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaUltimoOrgaoDescricao();
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    $this->setDebug( $stSql );

    return $obErro;
}

function montaRecuperaUltimoOrgaoDescricao()
{
    $stSql .= "   SELECT cod_orgao                                                                      \n";
    $stSql .= "        , recuperaDescricaoOrgao(orgao_descricao.cod_orgao, now()::date) as descricao    \n";
    $stSql .= "        , max(timestamp) as timestamp                                                    \n";
    $stSql .= "     FROM                                                                                \n";
    $stSql .= "          organograma.orgao_descricao                                                    \n";
    $stSql .= "    WHERE                                                                                \n";
    $stSql .= "          cod_orgao = ".$this->getDado('cod_orgao')."                                    \n";
    $stSql .= " GROUP BY                                                                                \n";
    $stSql .= "          cod_orgao                                                                      \n";
    $stSql .= "        , descricao                                                                      \n";

    return $stSql;
}

}

?>
