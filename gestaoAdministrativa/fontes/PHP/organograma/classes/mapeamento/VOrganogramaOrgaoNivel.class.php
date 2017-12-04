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
* Classe de Mapeamento para visão organograma_orgao_nivel
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 11637 $
$Name$
$Author: bruce $
$Date: 2006-06-23 13:36:30 -0300 (Sex, 23 Jun 2006) $

Casos de uso: uc-01.05.01, uc-01.05.02, uc-01.05.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );
/**
  * Efetua conexão com a view organograma.vw_orgao_nivel
  * Data de Criação: 14/09/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Diego Barbosa Victoria

*/

class VOrganogramaOrgaoNivel extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function VOrganogramaOrgaoNivel()
{
    parent::Persistente();
    $this->setTabela('organograma.vw_orgao_nivel');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_orgao, cod_organograma');

    $this->AddCampo('cod_orgao'         , 'integer' );
    $this->AddCampo('cod_organograma'   , 'integer' );
    $this->AddCampo('orgao'             , 'varchar' );
    $this->AddCampo('orgao_reduzido'    , 'varchar' );
    $this->AddCampo('num_cgm_pf'        , 'integer' );
    $this->AddCampo('cod_calendar'      , 'integer' );
    $this->AddCampo('cod_norma'         , 'integer' );
    //$this->AddCampo('descricao'         , 'varchar' );
    $this->AddCampo('criacao'           , 'date' );
    $this->AddCampo('inativacao'        , 'date' );
    $this->AddCampo('nivel'             , 'integer' );
}

function recuperaOrganograma(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY o.descricao ";
    $stSql  = $this->montaRecuperaOrganograma().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaOrganograma()
{
  $stSql .= "SELECT o.cod_orgao,                                                                                                         \n ";
  $stSql .= "     o.num_cgm_pf,                                                                                                          \n ";
  $stSql .= "     o.cod_calendar,                                                                                                        \n ";
  $stSql .= "      o.cod_norma,                                                                                                          \n ";
  $stSql .= "      o.descricao,                                                                                                          \n ";
  $stSql .= "      o.criacao,                                                                                                            \n ";
  $stSql .= "      o.inativacao,                                                                                                         \n ";
  $stSql .= "      o.sigla_orgao,                                                                                                        \n ";
  $stSql .= "      orn.cod_organograma,                                                                                                  \n ";
  $stSql .= "      organograma.fn_consulta_orgao(orn.cod_organograma, o.cod_orgao) AS orgao,                                             \n ";
  $stSql .= "      publico.fn_mascarareduzida(organograma.fn_consulta_orgao(orn.cod_organograma, o.cod_orgao)) AS orgao_reduzido,        \n ";
  $stSql .= "      publico.fn_nivel(organograma.fn_consulta_orgao(orn.cod_organograma, o.cod_orgao)) AS nivel                            \n ";
  $stSql .= " FROM organograma.orgao o, organograma.orgao_nivel orn                                                                      \n ";
  $stSql .= "WHERE o.cod_orgao = orn.cod_orgao                                                                                           \n ";
  if ($this->getDado('cod_organograma')) {
      $stSql .= "      and orn.cod_organograma = ".$this->getDado('cod_organograma') ."                                                                                       \n ";
  }
  $stSql .= "group by                                                                                                                    \n ";
  $stSql .= "      o.cod_orgao,                                                                                                          \n ";
  $stSql .= "      o.num_cgm_pf,                                                                                                         \n ";
  $stSql .= "      o.cod_calendar,                                                                                                       \n ";
  $stSql .= "      o.cod_norma,                                                                                                          \n ";
  $stSql .= "      o.descricao,                                                                                                          \n ";
  $stSql .= "      o.criacao,                                                                                                            \n ";
  $stSql .= "      o.inativacao,                                                                                                         \n ";
  $stSql .= "      o.sigla_orgao,                                                                                                        \n ";
  $stSql .= "      orn.cod_organograma,                                                                                                  \n ";
  $stSql .= "      orgao,                                                                                                                \n ";
  $stSql .= "      orgao_reduzido                                                                                                        \n ";

  return $stSql  ;
}

}
