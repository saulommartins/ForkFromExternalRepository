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
    * Classe de mapeamento da tabela ALMOXARIFADO.REQUISICAO_ITEM
    * Data de Criação: 25/04/2006

    * @author Analista      : Diego Victoria
    * @author Desenvolvedor : Rodrigo

    * @package URBEM
    * Casos de uso: uc-03.03.08
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TAlmoxarifadoPedidoTransferenciaItem extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
  public function TAlmoxarifadoPedidoTransferenciaItem()
  {
    parent::Persistente();
    $this->setTabela('almoxarifado.pedido_transferencia_item');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_transferencia,exercicio,cod_centro,cod_marca,cod_item,cod_centro');

    $this->AddCampo('cod_transferencia','integer',true,'',true,true);
    $this->AddCampo('exercicio','char',true,'4',true,true          );
    $this->AddCampo('cod_centro','integer',true,'',true,true       );
    $this->AddCampo('cod_marca','integer',true,'',true,true        );
    $this->AddCampo('cod_item','integer',true,'',true,true         );
    $this->AddCampo('quantidade','numeric',true,'14.4',false,false );
    //$this->AddCampo('cod_centro_destino','integer',true,'',true,true);
  }

  public function recuperaPedidoItens(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
  {
       return $this->executaRecupera("montaRecuperaPedidoItens",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }
    public function montaRecuperaPedidoItens()
    {
        $stSQL  = " SELECT pedido_transferencia_item.*
                        , pedido_transferencia_item_destino.cod_centro_destino
                        , marca.descricao as desc_marca
                        , centro_custo.descricao as desc_centro
                        , catalogo_item.descricao as desc_item
                        , unidade_medida.nom_unidade as desc_unidade
                        , (catalogo_item.cod_tipo = 2) as perecivel
                        , (select descricao
                             from almoxarifado.centro_custo as centro
                            where centro.cod_centro = pedido_transferencia_item_destino.cod_centro_destino) as desc_centro_destino
                    FROM almoxarifado.pedido_transferencia_item
                    JOIN almoxarifado.pedido_transferencia_item_destino USING (exercicio, cod_transferencia, cod_item, cod_marca, cod_centro)
                    JOIN almoxarifado.marca USING (cod_marca)
                    JOIN almoxarifado.centro_custo USING (cod_centro)
                    JOIN almoxarifado.catalogo_item USING (cod_item)
                    JOIN administracao.unidade_medida using (cod_grandeza, cod_unidade)
                 ";

         if ($this->getDado('exercicio')) {
           $stSQL .= " where exercicio = '".$this->getDado('exercicio')."' ";
         }
         if ($this->getDado('cod_transferencia')) {
           $stSQL .= "   and cod_transferencia = ".$this->getDado('cod_transferencia');
         }

        return $stSQL;
    }

}
