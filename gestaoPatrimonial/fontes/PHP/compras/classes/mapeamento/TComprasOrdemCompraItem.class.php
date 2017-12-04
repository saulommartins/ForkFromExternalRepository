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
    * Classe de mapeamento da tabela compras.ordem_item
    * Data de Criação: 30/06/2006

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Leandro André Zis

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TComprasOrdemCompraItem.class.php 62696 2015-06-09 14:19:37Z michel $

    * Casos de uso: uc-03.04424
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TComprasOrdemCompraItem extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TComprasOrdemCompraItem()
{
    parent::Persistente();
    $this->setTabela("compras.ordem_item");

    $this->setCampoCod('');
    $this->setComplementoChave('exercicio,cod_entidade,cod_ordem,cod_pre_empenho,num_item,tipo,exercicio_pre_empenho');

    $this->AddCampo('exercicio'             ,'char'     ,true ,'4'      ,true   ,true );
    $this->AddCampo('cod_entidade'          ,'integer'  ,true ,''       ,true   ,true );
    $this->AddCampo('cod_ordem'             ,'integer'  ,true ,''       ,true   ,true );
    $this->AddCampo('cod_pre_empenho'       ,'integer'  ,true ,''       ,true   ,true );
    $this->AddCampo('num_item'              ,'integer'  ,true ,''       ,true   ,true );
    $this->AddCampo('quantidade'            ,'numeric'  ,true ,'14,4'   ,false  ,false);
    $this->AddCampo('vl_total'              ,'numeric'  ,true ,'14,2'   ,false  ,false);
    $this->AddCampo('tipo'                  ,'char'     ,true ,'4'      ,true   ,true );
    $this->AddCampo('exercicio_pre_empenho' ,'char'     ,true ,'4'      ,true   ,true );
    $this->AddCampo('cod_marca'             ,'integer'  ,false, ''      ,false  ,true );
    $this->AddCampo('cod_item'              ,'integer'  ,false, ''      ,false  ,true );
    $this->AddCampo('cod_centro'            ,'integer'  ,false, ''      ,false  ,true );
}
}
