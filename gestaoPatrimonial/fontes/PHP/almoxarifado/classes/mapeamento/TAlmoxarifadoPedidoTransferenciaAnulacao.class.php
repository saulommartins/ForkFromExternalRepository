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
    * Classe de mapeamento da tabela ALMOXARIFADO.PEDIDO_TRANSFERENCIA_ANULACAO
    * Data de Criação: 23/07/2007

    * @author Analista:
    * @author Desenvolvedor: Leandro André Zis

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso: uc-03.03.08
*/

/*
$Log$
Revision 1.1  2007/09/13 14:54:10  leandro.zis
Ticket#10090#

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  ALMOXARIFADO.PEDIDO_TRANSFERENCIA_ANULACAO
  * Data de Criação: 23/07/2007

  * @author Analista:
  * @author Desenvolvedor: Leandro André Zis

  * @package URBEM
  * @subpackage Mapeamento
*/
class TAlmoxarifadoPedidoTransferenciaAnulacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TAlmoxarifadoPedidoTransferenciaAnulacao()
{
    parent::Persistente();
    $this->setTabela('almoxarifado.pedido_transferencia_anulacao');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_transferencia,exercicio');

    $this->AddCampo('cod_transferencia','integer',true,'',true,true);
    $this->AddCampo('exercicio','char',true,'4',true,true);
    $this->AddCampo('timestamp','timestamp',false,'',false,false);

}
}
