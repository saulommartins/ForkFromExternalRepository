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
    * Classe de mapeamento da tabela ALMOXARIFADO.PEDIDO_TRANSFERENCIA
    * Data de Criação: 25/04/2006

    * @author Analista      : Diego Victoria
    * @author Desenvolvedor : Rodrigo

    * @package URBEM
    * Casos de uso: uc-03.03.08
*/

/*
$Log: TAlmoxarifadoPedidoTransferencia.class.php,v $
Revision 1.11  2007/08/06 19:00:06  leandro.zis
Corrigido nota de transferencia

Revision 1.10  2007/07/19 21:43:26  leandro.zis
Bug #9612#, Bug #9604#, Bug #9601#, Bug #9482#, Bug #9614#

Revision 1.9  2006/07/06 14:04:43  diego
Retirada tag de log com erro.

Revision 1.8  2006/07/06 12:09:27  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TAlmoxarifadoPedidoTransferencia extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
 function TAlmoxarifadoPedidoTransferencia()
 {
    parent::Persistente();
    $this->setTabela('almoxarifado.pedido_transferencia');

    $this->setCampoCod('cod_transferencia');
    $this->setComplementoChave('exercicio');

    $this->AddCampo('exercicio','char',true,'4',true,false          );
    $this->AddCampo('cod_transferencia','integer',true,'',true,false);
    $this->AddCampo('cgm_almoxarife','integer',true,'',false,'TAlmoxarifadoAlmoxarife' );
    $this->AddCampo('cod_almoxarifado_origem','integer',true,'',false,'TAlmoxarifadoAlmoxarifado' );
    $this->AddCampo('cod_almoxarifado_destino','integer',true,'',false,'TAlmoxarifadoAlmoxarifado' );
    $this->AddCampo('observacao','varchar',true,'160,',false,false  );
    $this->AddCampo('timestamp','timestamp',false,'',false,false    );

 }

 function montaRecuperaTransferencias()
 {
  $stSql = "
     select pedido_transferencia.exercicio
         ,pedido_transferencia.cod_transferencia
         ,pedido_transferencia.cod_almoxarifado_origem
         ,sw_cgm_origem.nom_cgm as nom_almoxarifado_origem
         ,pedido_transferencia.cod_almoxarifado_destino
         ,sw_cgm_destino.nom_cgm as nom_almoxarifado_destino
         ,pedido_transferencia.observacao
     from almoxarifado.pedido_transferencia
     join almoxarifado.almoxarifado as almoxarifado_origem
       on almoxarifado_origem.cod_almoxarifado = pedido_transferencia.cod_almoxarifado_origem
     join sw_cgm as sw_cgm_origem
       on sw_cgm_origem.numcgm = almoxarifado_origem.cgm_almoxarifado
     join almoxarifado.almoxarifado as almoxarifado_destino
       on almoxarifado_destino.cod_almoxarifado = pedido_transferencia.cod_almoxarifado_destino
     join sw_cgm as sw_cgm_destino
       on sw_cgm_destino.numcgm = almoxarifado_destino.cgm_almoxarifado
     join almoxarifado.pedido_transferencia_item
       on pedido_transferencia_item.cod_transferencia = pedido_transferencia.cod_transferencia
      and pedido_transferencia_item.exercicio = pedido_transferencia.exercicio
left join almoxarifado.pedido_transferencia_anulacao
       on pedido_transferencia_anulacao.cod_transferencia = pedido_transferencia.cod_transferencia
      and pedido_transferencia_anulacao.exercicio = pedido_transferencia.exercicio
left  join almoxarifado.transferencia_almoxarifado_item
       on transferencia_almoxarifado_item.exercicio = pedido_transferencia.exercicio
      and transferencia_almoxarifado_item.cod_transferencia = pedido_transferencia.cod_transferencia
      and transferencia_almoxarifado_item.cod_item = pedido_transferencia_item.cod_item
      and transferencia_almoxarifado_item.cod_marca = pedido_transferencia_item.cod_marca
      and transferencia_almoxarifado_item.cod_centro = pedido_transferencia_item.cod_centro
      and (transferencia_almoxarifado_item.cod_almoxarifado  = pedido_transferencia.cod_almoxarifado_destino or
          transferencia_almoxarifado_item.cod_almoxarifado  = pedido_transferencia.cod_almoxarifado_origem)
    where pedido_transferencia_anulacao.cod_transferencia is null
      and transferencia_almoxarifado_item.cod_transferencia is null

";

    if ($this->getDado('exercicio')) {
      $stSql .= " and pedido_transferencia.exercicio = '".$this->getDado('exercicio')."' ";
    }

    if ($this->getDado('cod_transferencia')) {
      $stSql .= " and pedido_transferencia.cod_transferencia = ".$this->getDado('cod_transferencia');
    }

    $arCodAlmoxarifado_Origem = $this->getDado('cod_almoxarifado_origem');
    if ($arCodAlmoxarifadoOrigem) {
      $stSql .= " and pedido_transferencia.cod_almoxarifado_origem in (".implode(',',$this->getDado('cod_almoxarifado_origem')).")";
    }

    $arCodAlmoxarifadoDestino = $this->getDado('cod_almoxarifado_destino');
    if ($arCodAlmoxarifadoDestino) {
      $stSql .= " and pedido_transferencia.cod_almoxarifado_destino in (".implode(',',$this->getDado('cod_almoxarifado_destino')).")";
    }

    if ($this->getDado('observacao')) {
      $stSql .= " and pedido_transferencia.observacao like '".$this->getDado('observacao')."' ";
    }

    if ($this->getDado('cod_item')) {
      $stSql .= " and pedido_transferencia_item.cod_item = ".$this->getDado('cod_item');
    }

    if ($this->getDado('cod_marca')) {
      $stSql .= " and pedido_transferencia_item.cod_marca = ".$this->getDado('cod_marca');
    }

    if ($this->getDado('cod_centro')) {
      $stSql .= " and pedido_transferencia_item.cod_centro  = ".$this->getDado('cod_centro');
    }

    if ($this->getDado('cod_transferencia')) {
      $stSql .= " and pedido_transferencia.cod_transferencia = ".$this->getDado('cod_transferencia');
    }

    $stSql .= " group by  pedido_transferencia.exercicio
                         ,pedido_transferencia.cod_transferencia
                         ,pedido_transferencia.cod_almoxarifado_origem
                         ,sw_cgm_origem.nom_cgm
                         ,pedido_transferencia.cod_almoxarifado_destino
                         ,sw_cgm_destino.nom_cgm
                         ,pedido_transferencia.observacao ";

    return $stSql;
 }

  public function recuperaTransferencias(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
  {
     return $this->executaRecupera("montaRecuperaTransferencias",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
  }

  public function montaRecuperaTransferencia()
  {
     $stSql = " select pedido_transferencia.*
                      ,cgm_almoxarifado_origem.nom_cgm as nom_almoxarifado_origem
                      ,cgm_almoxarifado_destino.nom_cgm as nom_almoxarifado_destino
                  from almoxarifado.pedido_transferencia
                  join almoxarifado.almoxarifado as almoxarifado_origem
                    on pedido_transferencia.cod_almoxarifado_origem = almoxarifado_origem.cod_almoxarifado
                  join sw_cgm as cgm_almoxarifado_origem
                    on cgm_almoxarifado_origem.numcgm = almoxarifado_origem.cgm_almoxarifado
                  join almoxarifado.almoxarifado as almoxarifado_destino
                    on pedido_transferencia.cod_almoxarifado_destino = almoxarifado_destino.cod_almoxarifado
                  join sw_cgm as cgm_almoxarifado_destino
                    on cgm_almoxarifado_destino.numcgm = almoxarifado_destino.cgm_almoxarifado
                 where pedido_transferencia.cod_transferencia = ".$this->getDado('cod_transferencia')."
     ";

     return $stSql;
  }

function recuperaTransferencia(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
     return $this->executaRecupera("montaRecuperaTransferencia",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaTransferenciasEntrada()
{
    $stSql  = "     SELECT pedido_transferencia.exercicio                                                                    \n";
    $stSql .= "          , pedido_transferencia.cod_transferencia                                                            \n";
    $stSql .= "          , pedido_transferencia.cod_almoxarifado_origem                                                      \n";
    $stSql .= "          , sw_cgm_origem.nom_cgm as nom_almoxarifado_origem                                                  \n";
    $stSql .= "          , pedido_transferencia.cod_almoxarifado_destino                                                     \n";
    $stSql .= "          , sw_cgm_destino.nom_cgm as nom_almoxarifado_destino                                                \n";
    $stSql .= "          , pedido_transferencia.observacao                                                                   \n";
    $stSql .= "       FROM almoxarifado.pedido_transferencia                                                                 \n";
    $stSql .= "       JOIN almoxarifado.almoxarifado as almoxarifado_origem                                                  \n";
    $stSql .= "         ON almoxarifado_origem.cod_almoxarifado = pedido_transferencia.cod_almoxarifado_origem               \n";
    $stSql .= "       JOIN sw_cgm as sw_cgm_origem                                                                           \n";
    $stSql .= "         ON sw_cgm_origem.numcgm = almoxarifado_origem.cgm_almoxarifado                                       \n";
    $stSql .= "       JOIN almoxarifado.almoxarifado as almoxarifado_destino                                                 \n";
    $stSql .= "         ON almoxarifado_destino.cod_almoxarifado = pedido_transferencia.cod_almoxarifado_destino             \n";
    $stSql .= "       JOIN sw_cgm as sw_cgm_destino                                                                          \n";
    $stSql .= "         ON sw_cgm_destino.numcgm = almoxarifado_destino.cgm_almoxarifado                                     \n";
    $stSql .= "       JOIN almoxarifado.pedido_transferencia_item                                                            \n";
    $stSql .= "         ON pedido_transferencia_item.cod_transferencia = pedido_transferencia.cod_transferencia              \n";
    $stSql .= "        AND pedido_transferencia_item.exercicio = pedido_transferencia.exercicio                              \n";
    $stSql .= "  LEFT JOIN almoxarifado.pedido_transferencia_anulacao                                                        \n";
    $stSql .= "         ON pedido_transferencia_anulacao.cod_transferencia = pedido_transferencia.cod_transferencia          \n";
    $stSql .= "        AND pedido_transferencia_anulacao.exercicio = pedido_transferencia.exercicio                          \n";
    $stSql .= "  LEFT JOIN almoxarifado.transferencia_almoxarifado_item                                                      \n";
    $stSql .= "         ON transferencia_almoxarifado_item.exercicio = pedido_transferencia.exercicio                        \n";
    $stSql .= "        AND transferencia_almoxarifado_item.cod_transferencia = pedido_transferencia.cod_transferencia        \n";
    $stSql .= "        AND transferencia_almoxarifado_item.cod_item = pedido_transferencia_item.cod_item                     \n";
    $stSql .= "        AND transferencia_almoxarifado_item.cod_marca = pedido_transferencia_item.cod_marca                   \n";
    $stSql .= "        AND transferencia_almoxarifado_item.cod_centro = pedido_transferencia_item.cod_centro                 \n";
    $stSql .= "        AND transferencia_almoxarifado_item.cod_almoxarifado  = pedido_transferencia.cod_almoxarifado_destino \n";
    $stSql .= "  LEFT JOIN almoxarifado.transferencia_almoxarifado_item as transferencia_item_origem                         \n";
    $stSql .= "         ON transferencia_item_origem.exercicio = pedido_transferencia.exercicio                              \n";
    $stSql .= "        AND transferencia_item_origem.cod_transferencia = pedido_transferencia.cod_transferencia              \n";
    $stSql .= "        AND transferencia_item_origem.cod_item = pedido_transferencia_item.cod_item                           \n";
    $stSql .= "        AND transferencia_item_origem.cod_marca = pedido_transferencia_item.cod_marca                         \n";
    $stSql .= "        AND transferencia_item_origem.cod_centro = pedido_transferencia_item.cod_centro                       \n";
    $stSql .= "        AND transferencia_item_origem.cod_almoxarifado  = pedido_transferencia.cod_almoxarifado_origem        \n";
    $stSql .= "      WHERE pedido_transferencia_anulacao.cod_transferencia is null                                           \n";
    $stSql .= "        AND transferencia_item_origem.cod_transferencia is not null                                           \n";
    $stSql .= "        AND transferencia_item_origem.cod_transferencia NOT IN (                                              \n";
    $stSql .= "            SELECT distinct tai.cod_transferencia                                                             \n";
    $stSql .= "              FROM almoxarifado.transferencia_almoxarifado_item tai                                           \n";
    $stSql .= "              JOIN almoxarifado.lancamento_material lm                                                        \n";
    $stSql .= "                ON lm.cod_lancamento       = tai.cod_lancamento                                               \n";
    $stSql .= "               AND lm.cod_item             = tai.cod_item                                                     \n";
    $stSql .= "               AND lm.cod_centro           = tai.cod_centro                                                   \n";
    $stSql .= "               AND lm.cod_marca            = tai.cod_marca                                                    \n";
    $stSql .= "               AND lm.cod_almoxarifado     = tai.cod_almoxarifado                                             \n";
    $stSql .= "              join almoxarifado.natureza_lancamento nl                                                        \n";
    $stSql .= "                ON nl.exercicio_lancamento = lm.exercicio_lancamento                                          \n";
    $stSql .= "               AND nl.num_lancamento       = lm.num_lancamento                                                \n";
    $stSql .= "               AND nl.cod_natureza         = lm.cod_natureza                                                  \n";
    $stSql .= "               AND nl.tipo_natureza        = lm.tipo_natureza                                                 \n";
    $stSql .= "               where nl.tipo_natureza      = 'E'                                                              \n";
    $stSql .= "        )                                                                                                     \n";

    if ($this->getDado('exercicio')) {
        $stSql .= " and pedido_transferencia.exercicio = '".$this->getDado('exercicio')."' ";
    }

    if ($this->getDado('cod_transferencia')) {
        $stSql .= " and pedido_transferencia.cod_transferencia = ".$this->getDado('cod_transferencia');
    }

    if ($this->getDado('cod_almoxarifado_origem')) {
        $stSql .= " and pedido_transferencia.cod_almoxarifado_origem in (".implode(',',$this->getDado('cod_almoxarifado_origem')).")";
    }

    if ($this->getDado('cod_almoxarifado_destino')) {
        $stSql .= " and pedido_transferencia.cod_almoxarifado_destino in (".implode(',',$this->getDado('cod_almoxarifado_destino')).")";
    }

    if ($this->getDado('observacao')) {
        $stSql .= " and pedido_transferencia.observacao ilike '".$this->getDado('observacao')."' ";
    }

    if ($this->getDado('cod_item')) {
        $stSql .= " and pedido_transferencia_item.cod_item = ".$this->getDado('cod_item');
    }

    if ($this->getDado('cod_marca')) {
        $stSql .= " and pedido_transferencia_item.cod_marca = ".$this->getDado('cod_marca');
    }

    if ($this->getDado('cod_centro')) {
        $stSql .= " and pedido_transferencia_item.cod_centro  = ".$this->getDado('cod_centro');
    }

    if ($this->getDado('cod_transferencia')) {
        $stSql .= " and pedido_transferencia.cod_transferencia = ".$this->getDado('cod_transferencia');
    }

    return $stSql;

}

function recuperaTransferenciasEntrada(&$rsRecordSet,$stFiltro="",$stOrdem="",$boTransacao="")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaTransferenciasEntrada().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
}

function montaRecuperaTransferenciasSaida()
{
    $stSql = "    SELECT pedido_transferencia.exercicio                                                                   \n";
    $stSql.= "         , pedido_transferencia.cod_transferencia                                                           \n";
    $stSql.= "         , pedido_transferencia.cod_almoxarifado_origem                                                     \n";
    $stSql.= "         , sw_cgm_origem.nom_cgm as nom_almoxarifado_origem                                                 \n";
    $stSql.= "         , pedido_transferencia.cod_almoxarifado_destino                                                    \n";
    $stSql.= "         , sw_cgm_destino.nom_cgm as nom_almoxarifado_destino                                               \n";
    $stSql.= "         , pedido_transferencia.observacao                                                                  \n";
    $stSql.= "      FROM almoxarifado.pedido_transferencia                                                                \n";
    $stSql.= "      JOIN almoxarifado.almoxarifado as almoxarifado_origem                                                 \n";
    $stSql.= "        ON almoxarifado_origem.cod_almoxarifado = pedido_transferencia.cod_almoxarifado_origem              \n";
    $stSql.= "      JOIN sw_cgm as sw_cgm_origem                                                                          \n";
    $stSql.= "        ON sw_cgm_origem.numcgm = almoxarifado_origem.cgm_almoxarifado                                      \n";
    $stSql.= "      JOIN almoxarifado.almoxarifado as almoxarifado_destino                                                \n";
    $stSql.= "        ON almoxarifado_destino.cod_almoxarifado = pedido_transferencia.cod_almoxarifado_destino            \n";
    $stSql.= "      JOIN sw_cgm as sw_cgm_destino                                                                         \n";
    $stSql.= "        ON sw_cgm_destino.numcgm = almoxarifado_destino.cgm_almoxarifado                                    \n";
    $stSql.= "      JOIN almoxarifado.pedido_transferencia_item                                                           \n";
    $stSql.= "        ON pedido_transferencia_item.cod_transferencia = pedido_transferencia.cod_transferencia             \n";
    $stSql.= "       AND pedido_transferencia_item.exercicio = pedido_transferencia.exercicio                             \n";
    $stSql.= " LEFT JOIN almoxarifado.pedido_transferencia_anulacao                                                       \n";
    $stSql.= "        ON pedido_transferencia_anulacao.cod_transferencia = pedido_transferencia.cod_transferencia         \n";
    $stSql.= "       AND pedido_transferencia_anulacao.exercicio = pedido_transferencia.exercicio                         \n";
    $stSql.= " LEFT JOIN almoxarifado.transferencia_almoxarifado_item                                                     \n";
    $stSql.= "        ON transferencia_almoxarifado_item.exercicio = pedido_transferencia.exercicio                       \n";
    $stSql.= "       AND transferencia_almoxarifado_item.cod_transferencia = pedido_transferencia.cod_transferencia       \n";
    $stSql.= "       AND transferencia_almoxarifado_item.cod_item = pedido_transferencia_item.cod_item                    \n";
    $stSql.= "       AND transferencia_almoxarifado_item.cod_marca = pedido_transferencia_item.cod_marca                  \n";
    $stSql.= "       AND transferencia_almoxarifado_item.cod_centro = pedido_transferencia_item.cod_centro                \n";
    $stSql.= "       AND transferencia_almoxarifado_item.cod_almoxarifado  = pedido_transferencia.cod_almoxarifado_origem \n";
    $stSql.= "     WHERE pedido_transferencia_anulacao.cod_transferencia is null                                          \n";
    $stSql.= "       AND transferencia_almoxarifado_item.cod_transferencia is null                                        \n";

    if ($this->getDado('exercicio')) {
        $stSql .= " AND pedido_transferencia.exercicio = '".$this->getDado('exercicio')."' ";
    }

    if ($this->getDado('cod_transferencia')) {
        $stSql .= " AND pedido_transferencia.cod_transferencia = ".$this->getDado('cod_transferencia');
    }

    if ($this->getDado('cod_almoxarifado_origem')) {
        $stSql .= " AND pedido_transferencia.cod_almoxarifado_origem in (".implode(',',$this->getDado('cod_almoxarifado_origem')).")";
    }

    if ($this->getDado('cod_almoxarifado_destino')) {
        $stSql .= " AND pedido_transferencia.cod_almoxarifado_destino in (".implode(',',$this->getDado('cod_almoxarifado_destino')).")";
    }

    if ($this->getDado('observacao')) {
        $stSql .= " AND pedido_transferencia.observacao like '".$this->getDado('observacao')."' ";
    }

    if ($this->getDado('cod_item')) {
        $stSql .= " AND pedido_transferencia_item.cod_item = ".$this->getDado('cod_item');
    }

    if ($this->getDado('cod_marca')) {
        $stSql .= " AND pedido_transferencia_item.cod_marca = ".$this->getDado('cod_marca');
    }

    if ($this->getDado('cod_centro')) {
        $stSql .= " AND pedido_transferencia_item.cod_centro  = ".$this->getDado('cod_centro');
    }

    if ($this->getDado('cod_transferencia')) {
        $stSql .= " AND pedido_transferencia.cod_transferencia = ".$this->getDado('cod_transferencia');
    }

    return $stSql;

}

function recuperaTransferenciasSaida(&$rsRecordSet,$stFiltro="",$stOrdem="",$boTransacao="")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaTransferenciasSaida().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
}

}
