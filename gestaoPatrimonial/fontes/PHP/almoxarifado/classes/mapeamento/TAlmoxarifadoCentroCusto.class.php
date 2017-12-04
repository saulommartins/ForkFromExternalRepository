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
    * Classe de mapeamento da tabela ALMOXARIFADO.CENTRO_CUSTO
    * Data de Criação: 26/10/2005

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Fernando Zank Correa Evangelista

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 13070 $
    $Name$
    $Author: tonismar $
    $Date: 2006-07-20 18:01:17 -0300 (Qui, 20 Jul 2006) $

    * Casos de uso: uc-03.03.07
*/

/*
$Log$
Revision 1.13  2006/07/20 21:00:38  tonismar
comitei pro Zank passar o script do help

Revision 1.12  2006/07/06 14:04:43  diego
Retirada tag de log com erro.

Revision 1.11  2006/07/06 12:09:27  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  ALMOXARIFADO.CENTRO_CUSTO
  * Data de Criação: 26/10/2005

  * @author Analista: Diego Victoria
  * @author Desenvolvedor: Fernando Zank Correa Evangelista

  * @package URBEM
  * @subpackage Mapeamento
*/
class TAlmoxarifadoCentroCusto extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TAlmoxarifadoCentroCusto()
{
    parent::Persistente();
    $this->setTabela('almoxarifado.centro_custo');

    $this->setCampoCod('cod_centro');
    $this->setComplementoChave('');

    $this->AddCampo('cod_centro','integer',true,'',true,false);
    $this->AddCampo('descricao','varchar',true,'160',false,false);
    $this->AddCampo('dt_vigencia','date',false,'',false,false);

}

  public function montaRecuperaRelacionamento()
  {
        $stSql="SELECT DISTINCT 
                        centro_custo.cod_centro                                        
                        ,centro_custo.descricao                                         
                        ,TO_CHAR(centro_custo.dt_vigencia, 'dd/mm/yyyy') as dt_vigencia 
                        ,centro_custo_permissao.numcgm     
                        ,centro_custo_entidade.cod_entidade
                        ,cgm_entidade.nom_cgm AS desc_entidade
                        ,sw_cgm.nom_cgm        
                FROM almoxarifado.centro_custo
                LEFT JOIN almoxarifado.centro_custo_permissao 
                    ON centro_custo_permissao.cod_centro = centro_custo.cod_centro 
                LEFT JOIN almoxarifado.centro_custo_entidade 
                    ON centro_custo_entidade.cod_centro  = centro_custo.cod_centro
                ,orcamento.entidade                                                                                        
                ,sw_cgm                                                                                                    
                ,sw_cgm as cgm_entidade
                WHERE centro_custo_entidade.cod_entidade = entidade.cod_entidade
                AND centro_custo_entidade.exercicio = entidade.exercicio
                AND cgm_entidade.numcgm = entidade.numcgm
                AND centro_custo_permissao.numcgm = sw_cgm.numcgm
        ";
    return $stSql;
 }

function recuperaPermissaoUsuario(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaPermissaoUsuario().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

  public function montaRecuperaPermissaoUsuario()
  {
      $stSql  =" SELECT centro_custo.cod_centro                                        ,        \n";
      $stSql .="        centro_custo.descricao                                         ,        \n";
      $stSql .="        TO_CHAR(centro_custo.dt_vigencia, 'dd/mm/yyyy') as dt_vigencia ,        \n";
      $stSql .="        responsavel.numcgm                                  ,        \n";
      $stSql .="        centro_custo_entidade.cod_entidade                             ,        \n";
      $stSql .="        cgm_entidade.nom_cgm AS desc_entidade                                ,              \n";
      $stSql .="        sw_cgm.nom_cgm        \n";
      $stSql .="   FROM almoxarifado.centro_custo        \n";
      $stSql .="   JOIN almoxarifado.centro_custo_permissao as responsavel ON (responsavel.cod_centro = centro_custo.cod_centro )   \n";
      $stSql .="   JOIN almoxarifado.centro_custo_permissao  ON (centro_custo_permissao.cod_centro = centro_custo.cod_centro )   \n";
      $stSql .="   LEFT JOIN almoxarifado.centro_custo_entidade  ON (centro_custo_entidade.cod_centro  = centro_custo.cod_centro) , \n";
      $stSql .="        orcamento.entidade      ,                                       \n";
      $stSql .="        sw_cgm      ,                                                   \n";
      $stSql .="        sw_cgm as cgm_entidade                                          \n";
      $stSql .="  WHERE centro_custo_entidade.cod_entidade = entidade.cod_entidade      \n";
      $stSql .="    AND centro_custo_entidade.exercicio    = entidade.exercicio         \n";
      $stSql .="    AND cgm_entidade.numcgm    = entidade.numcgm                        \n";
      $stSql .="    AND responsavel.numcgm      = sw_cgm.numcgm                         \n";
      $stSql .="    AND responsavel.responsavel = true                                  \n";

    return $stSql;
 }

function montaRecuperaRelacionamentoOld()
{
    $stSql =  "select                                               \n";
    $stSql .= "       acc.cod_centro,                               \n";
    $stSql .= "       acc.descricao,                                \n";
    $stSql .= "       TO_CHAR(acc.dt_vigencia, 'dd/mm/yyyy') as dt_vigencia, \n";
    $stSql .= "       acce.cod_entidade,                            \n";
    $stSql .= "       cgm_ent.nom_cgm as desc_entidade,             \n";
    $stSql .= "       accr.cgm_responsavel,                         \n";
    $stSql .= "       cgm_resp.nom_cgm                              \n";
    $stSql .= "from                                                 \n";
    $stSql .= "       almoxarifado.centro_custo acc,                \n";
    $stSql .= "       almoxarifado.centro_custo_responsavel accr,   \n";
    $stSql .= "       almoxarifado.centro_custo_entidade acce,      \n";
    $stSql .= "       orcamento.entidade oe,                        \n";
    $stSql .= "       sw_cgm as cgm_ent,                            \n";
    $stSql .= "       sw_cgm as cgm_resp                            \n";
    $stSql .= "where                                                \n";
    $stSql .= "       acc.cod_centro = accr.cod_centro and          \n";
    $stSql .= "       acc.cod_centro = acce.cod_centro and          \n";
    $stSql .= "       acce.cod_entidade = oe.cod_entidade and       \n";
    $stSql .= "       oe.numcgm = cgm_ent.numcgm and                \n";
    $stSql .= "       acce.exercicio = oe.exercicio and             \n";
    $stSql .= "       accr.cgm_responsavel = cgm_resp.numcgm        \n";

    return $stSql;
}

function verificaPermissao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaVerificaPermissao().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaVerificaPermissao()
{
$stSql .= " select                                      \n";
$stSql .= "     cod_centro                              \n";
$stSql .= " from                                        \n";
$stSql .= "     almoxarifado.centro_custo_permissao     \n";
$stSql .= " where                                       \n";
$stSql .= "     numcgm = ".$this->getDado( 'numcgm' )." \n";

return $stSql;
}

function recuperaPermissaoUsuarioExcluir(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaPermissaoUsuarioExcluir().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

  public function montaRecuperaPermissaoUsuarioExcluir()
  {
      $stSql  =" SELECT centro_custo.cod_centro                                                                                                 \n";
      $stSql .="      , centro_custo.descricao                                                                                                  \n";
      $stSql .="      , TO_CHAR(centro_custo.dt_vigencia, 'dd/mm/yyyy') as dt_vigencia                                                          \n";
      $stSql .="      , responsavel.numcgm                                                                                                      \n";
      $stSql .="      , centro_custo_entidade.cod_entidade                                                                                      \n";
      $stSql .="      , cgm_entidade.nom_cgm AS desc_entidade                                                                                   \n";
      $stSql .="      , sw_cgm.nom_cgm                                                                                                          \n";
      $stSql .="   FROM almoxarifado.centro_custo                                                                                               \n";
      $stSql .="   JOIN almoxarifado.centro_custo_permissao as responsavel ON (responsavel.cod_centro = centro_custo.cod_centro )               \n";
      $stSql .="   JOIN almoxarifado.centro_custo_permissao  ON (centro_custo_permissao.cod_centro = centro_custo.cod_centro )                  \n";
      $stSql .="   LEFT JOIN almoxarifado.centro_custo_entidade  ON (centro_custo_entidade.cod_centro  = centro_custo.cod_centro)               \n";
      $stSql .="      , orcamento.entidade                                                                                                      \n";
      $stSql .="      , sw_cgm                                                                                                                  \n";
      $stSql .="      , sw_cgm as cgm_entidade                                                                                                  \n";
      $stSql .="  WHERE centro_custo_entidade.cod_entidade = entidade.cod_entidade                                                              \n";
      $stSql .="    AND centro_custo_entidade.exercicio    = entidade.exercicio                                                                 \n";
      $stSql .="    AND cgm_entidade.numcgm    = entidade.numcgm                                                                                \n";
      $stSql .="    AND responsavel.numcgm      = sw_cgm.numcgm                                                                                 \n";
      $stSql .="    AND responsavel.responsavel = true                                                                                          \n";
      $stSql .="    AND NOT EXISTS ( SELECT * from almoxarifado.estoque_material WHERE cod_centro = centro_custo.cod_centro )                   \n";
      $stSql .="    AND NOT EXISTS ( SELECT * from compras.solicitacao_item WHERE cod_centro = centro_custo.cod_centro )                        \n";
      $stSql .="    AND NOT EXISTS ( SELECT * from almoxarifado.pedido_transferencia_item WHERE cod_centro = centro_custo.cod_centro )          \n";
      $stSql .="    AND NOT EXISTS ( SELECT * from almoxarifado.pedido_transferencia_item_destino WHERE cod_centro = centro_custo.cod_centro )  \n";

    return $stSql;
 }

}
