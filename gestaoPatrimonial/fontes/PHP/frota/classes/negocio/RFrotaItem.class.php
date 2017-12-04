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
    * Página de
    * Data de criação : 15/03/2006

    * @author Analista:
    * @author Programador: Fernando Zank Correa Evangelista

    Caso de uso: uc-03.02.10

    $Id: RFrotaItem.class.php 59612 2014-09-02 12:00:51Z gelson $
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GP_ALM_NEGOCIO."RAlmoxarifadoCatalogoItem.class.php"                              );
include_once ( CAM_GP_FRO_MAPEAMENTO."TFrotaItem.class.php"                                          );
include_once ( CAM_GP_FRO_NEGOCIO."RFrotaTipoItem.class.php"                                         );
include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php"                                              );

class RFrotaItem extends RAlmoxarifadoCatalogoItem
{
/**
    * @Acces Private
    * @var Object
*/

var $obRFrotaTipoItem;

//método Construtor
function RFrotaItem()
{
    parent::RAlmoxarifadoCatalogoItem();
    $this->obRFrotaTipoItem         = new RFrotaTipoItem;

}
function listar(&$rsLista, $stOrder = "order by cod_item", $boTransacao = "")
{
    $obErro = new Erro;
    $obTFrotaItem = new TFrotaItem();
    if ($this->getCodigo()) {
        $stFiltro .= "and catalogo_item.cod_item = ".$this->getCodigo()."";
    }
    if ($this->getDescricao()) {
        $stFiltro .= "and catalogo_item.descricao ilike '".$this->getDescricao()."'";
    }
    if ($this->obRFrotaTipoItem->getCodTipo()) {
        $stFiltro .= "and tipo_item.cod_tipo = ".$this->obRFrotaTipoItem->getCodTipo()."";
    }
    $obErro = $obTFrotaItem->recuperaItem( $rsLista, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

function listarCombustivel(&$rsLista, $stOrder = "ci.descricao", $boTransacao = "")
{
    $obErro = new Erro;
    $obTFrotaItem = new TFrotaItem();
    if ($this->getCodigo()) {
        $obTFrotaItem->setDado("inCodigo", $this->getCodigo());
    }
    $obErro = $obTFrotaItem->recuperaCombustivelCatalogo( $rsLista, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

}
