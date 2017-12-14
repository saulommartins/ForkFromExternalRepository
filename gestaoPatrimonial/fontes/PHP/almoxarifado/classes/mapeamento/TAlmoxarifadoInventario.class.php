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
    * Classe de mapeamento da tabela ALMOXARIFADO.INVENTARIO
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
  * Efetua conexão com a tabela ALMOXARIFADO.INVENTARIO
  * Data de Criação: 24/10/2007

    * @author Analista: Anelise Schwengber
    * @author Desenvolvedor: Andre Almeida

    * @package URBEM
    * @subpackage Mapeamento
*/

class TAlmoxarifadoInventario extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TAlmoxarifadoInventario()
{
    parent::Persistente();
    $this->setTabela('almoxarifado.inventario');

    $this->setCampoCod('cod_inventario');
    $this->setComplementoChave('exercicio, cod_almoxarifado');

    $this->AddCampo( 'exercicio'       , 'char'    ,  true,   '4',  true, false );
    $this->AddCampo( 'cod_almoxarifado', 'integer' ,  true,    '',  true, 'TAlmoxarifadoAlmoxarifado' );
    $this->AddCampo( 'cod_inventario'  , 'sequence',  true,    '',  true, false);
    $this->AddCampo( 'dt_inventario'   , 'date'    ,  false,   '', false, false);
    $this->AddCampo( 'observacao'      , 'varchar' ,  true, '160', false, false);
    $this->AddCampo( 'processado'      , 'boolean' ,  true,    '', false, false);

}

function recuperaInventario(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
     return $this->executaRecupera("montaRecuperaInventario",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaInventario()
{
    $stSql1 = "  select inventario.exercicio
                     , inventario.cod_almoxarifado
                     , sw_cgm.nom_cgm as desc_almoxarifado
                     , inventario.cod_inventario
                     , to_char(inventario.dt_inventario, 'dd/mm/yyyy') as dt_inventario
                  from almoxarifado.inventario
                  join almoxarifado.almoxarifado
                    on almoxarifado.cod_almoxarifado = inventario.cod_almoxarifado
                  join sw_cgm
                    on sw_cgm.numcgm = almoxarifado.cgm_almoxarifado
                  join (
                        select inventario_itens.exercicio
                             , inventario_itens.cod_almoxarifado
                             , inventario_itens.cod_inventario
                         from almoxarifado.inventario_itens
                         join almoxarifado.catalogo_item
                           on catalogo_item.cod_item = inventario_itens.cod_item
                         join almoxarifado.catalogo_classificacao
                           on catalogo_classificacao.cod_classificacao = catalogo_item.cod_classificacao
                          and catalogo_classificacao.cod_catalogo      = catalogo_item.cod_catalogo
    ";
    $stSqlFiltro1 = "";
    if( $this->getDado('cod_estrutural') )
        $stSqlFiltro1 .= " and catalogo_classificacao.cod_estrutural like '".$this->getDado('cod_estrutural')."%' ";
    if( $this->getDado('cod_item') )
        $stSqlFiltro1 .= " and inventario_itens.cod_item = ".$this->getDado('cod_item');
    if( $this->getDado('cod_marca') )
        $stSqlFiltro1 .= " and inventario_itens.cod_marca = ".$this->getDado('cod_marca');
    if( $this->getDado('cod_centro') )
        $stSqlFiltro1 .= " and inventario_itens.cod_centro = ".$this->getDado('cod_centro');
    if( $this->getDado('cod_catalogo') )
        $stSqlFiltro1 .= " and catalogo_item.cod_catalogo = ".$this->getDado('cod_catalogo');
    $stSql2 = " group by inventario_itens.exercicio
                       , inventario_itens.cod_almoxarifado
                       , inventario_itens.cod_inventario
                       ) as inventario_itens
                    on inventario_itens.exercicio        = inventario.exercicio
                   and inventario_itens.cod_almoxarifado = inventario.cod_almoxarifado
                   and inventario_itens.cod_inventario   = inventario.cod_inventario
           left join almoxarifado.inventario_anulacao
                 on inventario_anulacao.exercicio        = inventario.exercicio
                and inventario_anulacao.cod_almoxarifado = inventario.cod_almoxarifado
                and inventario_anulacao.cod_inventario   = inventario.cod_inventario
          left join almoxarifado.lancamento_inventario_itens
                 on lancamento_inventario_itens.exercicio        = inventario.exercicio
                and lancamento_inventario_itens.cod_almoxarifado = inventario.cod_almoxarifado
                and lancamento_inventario_itens.cod_inventario   = inventario.cod_inventario
               where inventario_anulacao.exercicio is null
                 and lancamento_inventario_itens.exercicio is null
                 and inventario.processado = false
               ";
    $stSqlFiltro2 = "";
    if( $this->getDado('exercicio') )
        $stSqlFiltro2 .= " and inventario.exercicio = '".$this->getDado('exercicio')."'";
    if( $this->getDado('cod_almoxarifado') )
        $stSqlFiltro2 .= " and inventario.cod_almoxarifado IN (".$this->getDado('cod_almoxarifado').")";
    if( $this->getDado('cod_inventario') )
        $stSqlFiltro2 .= " and inventario.cod_inventario = ".$this->getDado('cod_inventario');
    if( $this->getDado('dt_inventario') )
        $stSqlFiltro2 .= " and inventario.dt_inventario = to_date('".$this->getDado('dt_inventario')."', 'dd/mm/yyyy') ";
    if( $this->getDado('observacao') )
        $stSqlFiltro2 .= " and inventario.observacao like '".$this->getDado('observacao')."'";

    if($stSqlFiltro1)
        $stSqlFiltro1 = " where ".substr($stSqlFiltro1,4,strlen($stSqlFiltro1)-4);

   return $stSql1.$stSqlFiltro1.$stSql2.$stSqlFiltro2;
}

function listarInventario(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
     return $this->executaRecupera("montaRecuperaListaInventario",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaListaInventario()
{
    $stSql1 = "  select inventario.exercicio
                     , inventario.cod_almoxarifado
                     , sw_cgm.nom_cgm as desc_almoxarifado
                     , inventario.cod_inventario
                     , to_char(inventario.dt_inventario, 'dd/mm/yyyy') as dt_inventario
                  from almoxarifado.inventario
                  join almoxarifado.almoxarifado
                    on almoxarifado.cod_almoxarifado = inventario.cod_almoxarifado
                  join sw_cgm
                    on sw_cgm.numcgm = almoxarifado.cgm_almoxarifado
                  join (
                        select inventario_itens.exercicio
                             , inventario_itens.cod_almoxarifado
                             , inventario_itens.cod_inventario
                         from almoxarifado.inventario_itens
                         join almoxarifado.catalogo_item
                           on catalogo_item.cod_item = inventario_itens.cod_item
                         join almoxarifado.catalogo_classificacao
                           on catalogo_classificacao.cod_classificacao = catalogo_item.cod_classificacao
                          and catalogo_classificacao.cod_catalogo      = catalogo_item.cod_catalogo
    ";
    if( $this->getDado('cod_catalogo') )
        $stSqlFiltro1 .= " and catalogo_item.cod_catalogo = ".$this->getDado('cod_catalogo');
    $stSql2 = " group by inventario_itens.exercicio
                       , inventario_itens.cod_almoxarifado
                       , inventario_itens.cod_inventario
                       ) as inventario_itens
                    on inventario_itens.exercicio        = inventario.exercicio
                   and inventario_itens.cod_almoxarifado = inventario.cod_almoxarifado
                   and inventario_itens.cod_inventario   = inventario.cod_inventario
           left join almoxarifado.inventario_anulacao
                 on inventario_anulacao.exercicio        = inventario.exercicio
                and inventario_anulacao.cod_almoxarifado = inventario.cod_almoxarifado
                and inventario_anulacao.cod_inventario   = inventario.cod_inventario
               ";

    if($stSqlFiltro1)
        $stSqlFiltro1 = " where ".substr($stSqlFiltro1,4,strlen($stSqlFiltro1)-4);

   return $stSql1.$stSqlFiltro1.$stSql2.$stSqlFiltro2;
}

}

?>
