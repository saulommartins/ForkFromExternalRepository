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
    * Classe de mapeamento da tabela empenho.item_prestacao_contas
    * Data de Criação: 26/10/2006

    * @author Analista: Gelson
    * @author Desenvolvedor: Rodrigo

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: luciano $
    $Date: 2007-05-15 11:50:12 -0300 (Ter, 15 Mai 2007) $

    * Casos de uso: uc-02.03.31
*/
/*
$Log$
Revision 1.1  2007/05/15 14:50:12  luciano
Adicionado ao repositorio

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TEmpenhoItemPrestacaoContasAnulado extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
  public function TEmpenhoItemPrestacaoContasAnulado()
  {
    parent::Persistente();
    $this->setTabela("empenho.item_prestacao_contas_anulado");

    $this->setCampoCod('');
    $this->setComplementoChave('exercicio,cod_entidade,cod_empenho,num_item');

    $this->AddCampo('num_item'           ,'sequence',true  ,''      ,true,false);
    $this->AddCampo('exercicio'          ,'char'    ,true  ,'4'     ,true,'TEmpenhoPrestacaoContas');
    $this->AddCampo('cod_entidade'       ,'integer' ,true  ,''      ,true,'TEmpenhoPrestacaoContas');
    $this->AddCampo('cod_empenho'        ,'integer' ,true  ,''      ,true,'TEmpenhoPrestacaoContas');
  }
}
?>
