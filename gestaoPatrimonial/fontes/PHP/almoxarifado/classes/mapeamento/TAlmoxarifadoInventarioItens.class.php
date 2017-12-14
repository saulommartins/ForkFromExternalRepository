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
    * Classe de mapeamento da tabela ALMOXARIFADO.INVENTARIO_ITENS
    * Data de Criação: 24/10/2007

    * @author Analista: Anelise Schwengber
    * @author Desenvolvedor: Andre Almeida

    * @ignore

    $Revision: 1.9 $
    $Name:  $
    $Author: bruce $
    $Date: 2007/07/24 20:00:05 $

    * Casos de uso: uc-03.03.15
*/

/*
    $Log:
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela ALMOXARIFADO.INVENTARIO_ITENS
  * Data de Criação: 24/10/2007

    * @author Analista: Anelise Schwengber
    * @author Desenvolvedor: Andre Almeida

    * @package URBEM
    * @subpackage Mapeamento
*/

class TAlmoxarifadoInventarioItens extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TAlmoxarifadoInventarioItens()
{
    parent::Persistente();
    $this->setTabela('almoxarifado.inventario_itens');

    $this->setCampoCod('');
    $this->setComplementoChave('exercicio, cod_almoxarifado, cod_inventario, cod_item, cod_marca, cod_centro');

    $this->AddCampo( 'exercicio'       , 'char'     ,  true,    '4',  true, 'TAlmoxarifadoInventario' );
    $this->AddCampo( 'cod_almoxarifado', 'integer'  ,  true,     '',  true, 'TAlmoxarifadoInventario' );
    $this->AddCampo( 'cod_inventario'  , 'integer'  ,  true,     '',  true, 'TAlmoxarifadoInventario' );
    $this->AddCampo( 'cod_item'        , 'integer'  ,  true,     '',  true, false );
    $this->AddCampo( 'cod_marca'       , 'integer'  ,  true,     '',  true, false );
    $this->AddCampo( 'cod_centro'      , 'integer'  ,  true,     '',  true, false );
    $this->AddCampo( 'quantidade'      , 'numeric'  ,  true, '14,4', false, false );
    $this->AddCampo( 'justificativa'   , 'varchar'  ,  true,  '160', false, false );
    $this->AddCampo( 'timestamp'       , 'timestamp',  false,    '', false, false );

}

function recuperaItensInventarioPorClassificacao(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
     return $this->executaRecupera("montaRecuperaItensInventarioPorClassificacao",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaItensInventarioPorClassificacao()
{
    $stSql = "
        select inventario_itens.cod_inventario
             , catalogo_classificacao.cod_estrutural
          from almoxarifado.inventario_itens
          join almoxarifado.inventario
            on inventario.exercicio        = inventario_itens.exercicio
           and inventario.cod_inventario   = inventario_itens.cod_inventario
           and inventario.cod_almoxarifado = inventario_itens.cod_almoxarifado
     left join almoxarifado.inventario_anulacao
            on inventario_anulacao.exercicio        = inventario.exercicio
           and inventario_anulacao.cod_almoxarifado = inventario.cod_almoxarifado
           and inventario_anulacao.cod_inventario   = inventario.cod_inventario
          join almoxarifado.catalogo_item
            on catalogo_item.cod_item = inventario_itens.cod_item
          join almoxarifado.catalogo_classificacao
            on catalogo_classificacao.cod_classificacao = catalogo_item.cod_classificacao
           and catalogo_classificacao.cod_catalogo      = catalogo_item.cod_catalogo
         where not inventario.processado
           and inventario_anulacao.exercicio is null
    ";

    if( $this->getDado('cod_estrutural') )
        $stSqlFiltro = " and catalogo_classificacao.cod_estrutural like '".$this->getDado('cod_estrutural')."%' ";

    return $stSql.$stSqlFiltro;
}

function recuperaItensInventario(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
     return $this->executaRecupera("montaRecuperaItensInventario",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaItensInventario()
{
    $stSql = "
select inventario.exercicio
     , inventario.cod_inventario
     , inventario.cod_almoxarifado
     , sw_cgm.nom_cgm as desc_almoxarifado
     , catalogo_item.cod_catalogo
     , catalogo.descricao as desc_catalogo
     , inventario.observacao
     , catalogo_item.cod_classificacao
     , catalogo_classificacao.descricao as desc_classificacao
     , catalogo_classificacao.cod_estrutural
     , inventario_itens.cod_centro
     , centro_custo.descricao as desc_centro_custo
     , catalogo_item.cod_item
     , catalogo_item.descricao
     , catalogo_item.descricao_resumida
     , unidade_medida.nom_unidade
     , estoque_material.cod_marca
     , marca.descricao as desc_marca
     , lancamento.saldo
     , inventario_itens.quantidade
     , inventario_itens.justificativa
 from almoxarifado.inventario
 join almoxarifado.almoxarifado
   on almoxarifado.cod_almoxarifado = inventario.cod_almoxarifado
 join sw_cgm
   on sw_cgm.numcgm = almoxarifado.cgm_almoxarifado
 join almoxarifado.inventario_itens
   on inventario_itens.exercicio        = inventario.exercicio
  and inventario_itens.cod_almoxarifado = inventario.cod_almoxarifado
  and inventario_itens.cod_inventario   = inventario.cod_inventario
 join almoxarifado.catalogo_item
   on catalogo_item.cod_item = inventario_itens.cod_item
 join almoxarifado.catalogo
   on catalogo.cod_catalogo = catalogo_item.cod_catalogo
 join almoxarifado.catalogo_classificacao
   on catalogo_classificacao.cod_classificacao = catalogo_item.cod_classificacao
  and catalogo_classificacao.cod_catalogo      = catalogo_item.cod_catalogo
 join administracao.unidade_medida
   on unidade_medida.cod_grandeza = catalogo_item.cod_grandeza
  and unidade_medida.cod_unidade  = catalogo_item.cod_unidade
 join almoxarifado.estoque_material
   on estoque_material.cod_item         = inventario_itens.cod_item
  and estoque_material.cod_marca        = inventario_itens.cod_marca
  and estoque_material.cod_almoxarifado = inventario_itens.cod_almoxarifado
  and estoque_material.cod_centro       = inventario_itens.cod_centro
 join almoxarifado.marca
   on marca.cod_marca = estoque_material.cod_marca
 join almoxarifado.centro_custo
   on centro_custo.cod_centro = inventario_itens.cod_centro
left join (
        SELECT lancamento_material.cod_almoxarifado
             , lancamento_material.cod_item
             , lancamento_material.cod_marca
             , lancamento_material.cod_centro
             , sum(lancamento_material.quantidade) as saldo
          from almoxarifado.lancamento_material
      group by lancamento_material.cod_almoxarifado
             , lancamento_material.cod_item
             , lancamento_material.cod_marca
             , lancamento_material.cod_centro
      ) as lancamento
   on lancamento.cod_almoxarifado = inventario.cod_almoxarifado
  and lancamento.cod_item         = inventario_itens.cod_item
  and lancamento.cod_marca        = inventario_itens.cod_marca
  and lancamento.cod_centro       = inventario_itens.cod_centro
    ";

    if( $this->getDado('exercicio') )
        $stSqlFiltro .= " and inventario.exercicio = '".$this->getDado('exercicio')."' ";

    if( $this->getDado('cod_almoxarifado') )
        $stSqlFiltro .= " and inventario.cod_almoxarifado = ".$this->getDado('cod_almoxarifado');

    if( $this->getDado('cod_inventario') )
        $stSqlFiltro .= " and inventario.cod_inventario = ".$this->getDado('cod_inventario');

    if( $stSqlFiltro )
        $stSqlFiltro .= " where ".substr($stSqlFiltro,4,strlen($stSqlFiltro)-4);

    $stSqlOrder = " order by inventario.cod_inventario, inventario.cod_almoxarifado, catalogo_item.cod_classificacao, catalogo_item.cod_item, estoque_material.cod_marca, inventario_itens.cod_centro ";

    return $stSql.$stSqlFiltro.$stSqlOrder;

}

function verificaItensInventarioNaoProcessado(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaVerificaItensInventarioNaoProcessado",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaVerificaItensInventarioNaoProcessado()
{
    $stSql = " SELECT catalogo_item.cod_item, catalogo_item.descricao                              \n";
    $stSql.= "   FROM almoxarifado.inventario_itens                                                \n";

    $stSql.= "  INNER JOIN almoxarifado.inventario                                                 \n";
    $stSql.= "          ON (     inventario_itens.cod_inventario = inventario.cod_inventario       \n";
    $stSql.= "               AND inventario_itens.exercicio = inventario.exercicio                 \n";
    $stSql.= "               AND inventario_itens.cod_almoxarifado = inventario.cod_almoxarifado   \n";
    $stSql.= "              )                                                                      \n";

    $stSql.= "  INNER JOIN almoxarifado.catalogo_item                                              \n";
    $stSql.= "          ON (     inventario_itens.cod_item = catalogo_item.cod_item   )            \n";

    $stSql.= "  WHERE inventario.processado = 'f'                                                  \n";

    if ($this->getDado('cod_item')) {
        $stSql.= "   AND inventario_itens.cod_item =".$this->getDado('cod_item')."                 \n";
    }

    if ($this->getDado('cod_marca')) {
        $stSql.= "    AND inventario_itens.cod_marca =".$this->getDado('cod_marca')."              \n";
    }

    if ($this->getDado('cod_almoxarifado')) {
        $stSql.= "    AND inventario_itens.cod_almoxarifado =".$this->getDado('cod_almoxarifado')."\n";
    }

    if ($this->getDado('cod_centro')) {
        $stSql.= "    AND inventario_itens.cod_centro =".$this->getDado('cod_centro')."            \n";
    }

    if ($this->getDado('exercicio')) {
        $stSql.= "    AND inventario_itens.exercicio ='".$this->getDado('exercicio')."'              \n";
    }

    return $stSql;
}

}

?>
