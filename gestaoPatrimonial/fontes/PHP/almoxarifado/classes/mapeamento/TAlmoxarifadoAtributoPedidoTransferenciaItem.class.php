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
    * Classe de mapeamento da tabela ALMOXARIFADO.ATRIBUTO_PEDIDO_TRANSFERENCIA_ITEM
    * Data de Criação: 25/04/2006

    * @author Analista      : Diego Victoria
    * @author Desenvolvedor : Leandro Zis

    * @package URBEM
    * Casos de uso: uc-03.03.08
*/

/*
$Log$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TAlmoxarifadoAtributoPedidoTransferenciaItem extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
  public function TAlmoxarifadoAtributoPedidoTransferenciaItem()
  {
    parent::Persistente();
    $this->setTabela('almoxarifado.atributo_pedido_transferencia_item');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_sequence,cod_transferencia,exercicio,cod_marca,cod_item,cod_centro');

    $this->AddCampo('cod_sequencial','sequence',true,'',true,false);
    $this->AddCampo('cod_transferencia','integer',true,'',true,'TAlmoxarifadoPedidoTransferenciaItem');
    $this->AddCampo('exercicio','char',true,'4',true,'TAlmoxarifadoPedidoTransferenciaItem'          );
    $this->AddCampo('cod_centro','integer',true,'',true,'TAlmoxarifadoPedidoTransferenciaItem'       );
    $this->AddCampo('cod_marca','integer',true,'',true,'TAlmoxarifadoPedidoTransferenciaItem'       );
    $this->AddCampo('cod_item','integer',true,'',true,'TAlmoxarifadoPedidoTransferenciaItem'         );
    $this->AddCampo('quantidade','numeric',true,'14.4',false,false );

  }

   public function recuperaAtributos(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
   {
      return $this->executaRecupera("montaRecuperaAtributos",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
   }

   public function montaRecuperaAtributos()
   {
      $stSQL = " select publico.concatenar_hifen(nom_atributo) as atributos
                   from administracao.atributo_dinamico
                   join almoxarifado.atributo_pedido_transferencia_item_valor
                  using (cod_modulo, cod_cadastro, cod_atributo) ";
      if ($this->getDado('exercicio')) {
        $stSQL .= " where exercicio = '".$this->getDado('exercicio')."' ";
      }
      if ($this->getDado('cod_transferencia')) {
        $stSQL .= "   and cod_transferencia = ".$this->getDado('cod_transferencia');
      }
      if ($this->getDado('cod_item')) {
        $stSQL .= "   and cod_item = ".$this->getDado('cod_item');
      }
      if ($this->getDado('cod_centro')) {
        $stSQL .= "   and cod_centro = ".$this->getDado('cod_centro');
      }
      if ($this->getDado('cod_marca')) {
        $stSQL .= "   and cod_marca = ".$this->getDado('cod_marca');
      }

      return $stSQL;
   }

   public function recuperaAtributosValores(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
   {
       return $this->executaRecupera("montaRecuperaAtributosValores",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaAtributosValores()
    {
        $stSQL  = " select cod_sequencial
                         , publico.concatenar_hifen(cod_atributo::varchar) as cod_atributos
                         , publico.concatenar_hifen(valor) as valor_atributos
                         , quantidade
                      from almoxarifado.atributo_pedido_transferencia_item
                      join almoxarifado.atributo_pedido_transferencia_item_valor
                     using (exercicio, cod_transferencia, cod_item, cod_marca, cod_centro, cod_sequencial)
                      join administracao.atributo_dinamico
                     using (cod_modulo, cod_cadastro, cod_atributo)
                 ";

         if ($this->getDado('exercicio')) {
           $stSQL .= " where exercicio = '".$this->getDado('exercicio')."' ";
         }
         if ($this->getDado('cod_transferencia')) {
           $stSQL .= "   and cod_transferencia = ".$this->getDado('cod_transferencia');
         }
         if ($this->getDado('cod_item')) {
           $stSQL .= "   and cod_item = ".$this->getDado('cod_item');
         }
         if ($this->getDado('cod_centro')) {
           $stSQL .= "   and cod_centro = ".$this->getDado('cod_centro');
         }
         if ($this->getDado('cod_marca')) {
           $stSQL .= "   and cod_marca = ".$this->getDado('cod_marca');
         }

         $stSQL .= " group by cod_sequencial, quantidade
                     order by cod_sequencial; ";

        return $stSQL;
    }

}
