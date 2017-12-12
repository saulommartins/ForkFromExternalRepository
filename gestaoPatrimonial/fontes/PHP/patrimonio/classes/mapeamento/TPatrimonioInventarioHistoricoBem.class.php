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
 * Data de Criação: 08/01/2009

 * @author Analista:      Gelson Wolowski
 * @author Desenvolvedor: Diogo Zarpelon <diogo.zarpelon@cnm.org.br>

 * @package URBEM
 * @subpackage

  $Id:$

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TPatrimonioInventarioHistoricoBem extends Persistente
{
    /**
     * Método Construtor
     * @access Private
     */
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela('patrimonio.inventario_historico_bem');

        $this->setCampoCod('id_inventario');
        $this->setComplementoChave( 'exercicio,cod_bem' );

        $this->AddCampo('exercicio'           , 'char'      , true  , '4'   , true  , true);
        $this->AddCampo('id_inventario'       , 'integer'   , true  , ''    , true  , true);
        $this->AddCampo('cod_bem'             , 'integer'   , true  , ''    , true  , true);
        $this->AddCampo('timestamp_historico' , 'timestamp' , true  , ''    , false , true);
        $this->AddCampo('timestamp'           , 'timestamp' , true  , ''    , false , false);
        $this->AddCampo('cod_situacao'        , 'integer'   , true  , ''    , false , true);
        $this->AddCampo('cod_orgao'           , 'integer'   , true  , ''    , false , true);
        $this->AddCampo('cod_local'           , 'integer'   , true  , ''    , false , true);
        $this->AddCampo('descricao'           , 'varchar'   , true  , '100' , false , false);
    }

    public function recuperaBemHistoricoInventario(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaBemHistoricoInventario().$stFiltro.$stOrdem;
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);
        $this->setDebug( $stSql );

        return $obErro;
    }

    public function montaRecuperaBemHistoricoInventario()
    {
        $stSql  = "       SELECT DISTINCT																			 \n";
        $stSql .= "               historico_bem.cod_bem																 \n";
        $stSql .= "            ,  TRIM(bem.descricao) as nom_bem														 \n";
        $stSql .= "            ,  recuperaDescricaoOrgao(historico_bem.cod_orgao, 'NOW()') as nom_orgao					 \n";
        $stSql .= "            ,  (																						 \n";
        $stSql .= "					SELECT  local.descricao																 \n";
        $stSql .= "					  FROM  organograma.local															 \n";
        $stSql .= "					 WHERE  local.cod_local = historico_bem.cod_local									 \n";
        $stSql .= "				  ) as nom_local																		 \n";
        $stSql .= "            ,  (																						 \n";
        $stSql .= "					SELECT  situacao_bem.nom_situacao													 \n";
        $stSql .= "					  FROM  patrimonio.situacao_bem														 \n";
        $stSql .= "					 WHERE situacao_bem.cod_situacao = historico_bem.cod_situacao						 \n";
        $stSql .= "				  ) as nom_situacao																		 \n";
        $stSql .= "            ,  recuperaDescricaoOrgao(inventario_historico_bem.cod_orgao, 'NOW()') as nom_orgao_novo  \n";
        $stSql .= "            ,  (																						 \n";
        $stSql .= "					SELECT  local.descricao																 \n";
        $stSql .= "					  FROM  organograma.local															 \n";
        $stSql .= "					 WHERE  local.cod_local = inventario_historico_bem.cod_local						 \n";
        $stSql .= "				  ) as nom_local_novo																	 \n";
        $stSql .= "            ,  (																						 \n";
        $stSql .= "					SELECT  situacao_bem.nom_situacao													 \n";
        $stSql .= "					  FROM  patrimonio.situacao_bem														 \n";
        $stSql .= "					 WHERE situacao_bem.cod_situacao = inventario_historico_bem.cod_situacao			 \n";
        $stSql .= "				  ) as nom_situacao_novo																 \n";
        $stSql .= "            ,  CASE WHEN (inventario_historico_bem.cod_orgao <> historico_bem.cod_orgao) OR			 \n";
        $stSql .= "                         (inventario_historico_bem.cod_local <> historico_bem.cod_local) OR			 \n";
        $stSql .= "                         (inventario_historico_bem.descricao  <> '') 					OR			 \n";
        $stSql .= "                         (inventario_historico_bem.cod_situacao <> historico_bem.cod_situacao)		 \n";
        $stSql .= "               THEN 'Sim'																			 \n";
        $stSql .= "               ELSE 'Não'																			 \n";
        $stSql .= "               END as modificado																		 \n";
        $stSql .= "																										 \n";
        $stSql .= "         FROM  patrimonio.historico_bem																 \n";
        $stSql .= "																										 \n";
        $stSql .= "   INNER JOIN  patrimonio.bem																		 \n";
        $stSql .= "           ON  bem.cod_bem = historico_bem.cod_bem													 \n";
        $stSql .= "																										 \n";
        $stSql .= "   INNER JOIN  (																						 \n";
        $stSql .= "                   SELECT  cod_bem																	 \n";
        $stSql .= "                        ,  MAX(timestamp) AS timestamp												 \n";
        $stSql .= "                     FROM  patrimonio.historico_bem													 \n";
        $stSql .= "                 GROUP BY  cod_bem																	 \n";
        $stSql .= "               ) as resumo																			 \n";
        $stSql .= "       ON  resumo.cod_bem   = historico_bem.cod_bem													 \n";        
        $stSql .= "																										 \n";
        $stSql .= "   INNER JOIN  patrimonio.inventario_historico_bem													 \n";
        $stSql .= "           ON  inventario_historico_bem.cod_bem = resumo.cod_bem										 \n";
        $stSql .= "																										 \n";
        $stSql .= "        WHERE  1=1																					 \n";
        $stSql .= "																										 \n";
        $stSql .= "          AND  NOT EXISTS																			 \n";
        $stSql .= "               (																						 \n";
        $stSql .= "                    SELECT  1																		 \n";
        $stSql .= "                      FROM  patrimonio.bem_baixado													 \n";
        $stSql .= "                     WHERE  bem_baixado.cod_bem = bem.cod_bem										 \n";
        $stSql .= "               )																						 \n";

        if ($this->getDado('id_inventario')) {
            $stSql .= " AND  inventario_historico_bem.id_inventario = ".$this->getDado('id_inventario');
        }

        if ($this->getDado('exercicio')) {
            $stSql .= " AND  inventario_historico_bem.exercicio = '".$this->getDado('exercicio')."'";
        }

        if ($this->getDado('cod_orgao')) {
            $stSql .= " AND  historico_bem.cod_orgao = ".$this->getDado('cod_orgao');
        }

        if ($this->getDado('cod_local')) {
            $stSql .= " AND  historico_bem.cod_local = ".$this->getDado('cod_local');
        }

        return $stSql;
    }

}

?>
