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
    * Classe de mapeamento da tabela compras.cotacao_fornecedor_item_desclassificacao
    * Data de Criação: 14/09/2006

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Nome do Programador

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 18852 $
    $Name$
    $Author: bruce $
    $Date: 2006-12-19 10:46:18 -0200 (Ter, 19 Dez 2006) $

    * Casos de uso: uc-03.05.25
                    uc-03.05.26
*/
/*
$Log$
Revision 1.4  2006/12/19 12:46:18  bruce
colocação do UC de  julgamento de proposta

Revision 1.3  2006/12/06 11:53:56  bruce
desenvolvimento

Revision 1.2  2006/11/07 16:41:27  larocca
Inclusão dos Casos de Uso

Revision 1.1  2006/09/14 16:32:08  cleisson
inclusão

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  compras.cotacao_fornecedor_item_desclassificacao
  * Data de Criação: 14/09/2006

  * @author Analista: Gelson W. Gonçalves
  * @author Desenvolvedor: Nome do Programador

  * @package URBEM
  * @subpackage Mapeamento
*/
class TComprasCotacaoFornecedorItemDesclassificacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TComprasCotacaoFornecedorItemDesclassificacao()
{
    parent::Persistente();
    $this->setTabela("compras.cotacao_fornecedor_item_desclassificacao");

    $this->setCampoCod('');
    $this->setComplementoChave('cgm_fornecedor,cod_item,cod_cotacao,exercicio');

    $this->AddCampo('cgm_fornecedor','integer'  ,true, false ,''   ,true,'TComprasCotacaoFornecedorItem');
    $this->AddCampo('cod_item'      ,'integer'  ,true, false ,''   ,true,'TComprasCotacaoFornecedorItem');
    $this->AddCampo('cod_cotacao'   ,'integer'  ,true, false ,''   ,true,'TComprasCotacaoFornecedorItem');
    $this->AddCampo('exercicio'     ,'char'     ,true, false ,'4'  ,true,'TComprasCotacaoFornecedorItem');
    $this->AddCampo('lote'          ,'integer'  ,true, true  ,''   ,true,'TComprasCotacaoFornecedorItem');
    $this->AddCampo('justificativa' ,'text'     ,false, true,''   ,false,false);
    $this->AddCampo('timestamp'     ,'timestamp',false, false ,''   ,false,false);

}
}
