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
    * Classe de mapeamento da tabela ALMOXARIFADO.REQUISICAO_ITENS_ANULACAO
    * Data de Criação: 26/10/2005

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Fernando Zank Correa Evangelista

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 13070 $
    $Name$
    $Author: tonismar $
    $Date: 2006-07-20 18:01:17 -0300 (Qui, 20 Jul 2006) $

    * Casos de uso: uc-03.03.10
*/

/*
$Log$
Revision 1.9  2006/07/20 21:00:38  tonismar
comitei pro Zank passar o script do help

Revision 1.8  2006/07/06 14:04:44  diego
Retirada tag de log com erro.

Revision 1.7  2006/07/06 12:09:27  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  ALMOXARIFADO.REQUISICAO_ITENS_ANULACAO
  * Data de Criação: 26/10/2005

  * @author Analista: Diego Victoria
  * @author Desenvolvedor: Fernando Zank Correa Evangelista

  * @package URBEM
  * @subpackage Mapeamento
*/
class TAlmoxarifadoRequisicaoItensAnulacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TAlmoxarifadoRequisicaoItensAnulacao()
{
    parent::Persistente();
    $this->setTabela('almoxarifado.requisicao_itens_anulacao');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_item,cod_marca,cod_centro,exercicio,cod_requisicao,cod_almoxarifado,timestamp');

    $this->AddCampo('cod_item','integer',true,'',true,'TAlmoxarifadoRequisicaoItens');
    $this->AddCampo('cod_marca','integer',true,'',true,'TAlmoxarifadoRequisicaoItens');
    $this->AddCampo('cod_centro','integer',true,'',true,'TAlmoxarifadoRequisicaoItens');
    $this->AddCampo('exercicio','char',true,'4',true,'TAlmoxarifadoRequisicaoAnulacao');
    $this->AddCampo('cod_requisicao','integer',true,'',true,'TAlmoxarifadoRequisicaoAnulacao');
    $this->AddCampo('cod_almoxarifado','integer',true,'',true,'TAlmoxarifadoRequisicaoAnulacao');
    $this->AddCampo('timestamp','timestamp',false,'',true,'TAlmoxarifadoRequisicaoAnulacao');
    $this->AddCampo('quantidade','numeric',true,'14.4',false,false);

}
}
