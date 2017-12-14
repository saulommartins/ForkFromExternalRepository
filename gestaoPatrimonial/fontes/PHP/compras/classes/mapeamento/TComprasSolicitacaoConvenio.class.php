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
    * Classe de mapeamento da tabela compras.solicitacao_item_dotacao_anulacao
    * Data de Criação: 09/12/2008

    * @author Analista: Gelson Wolowski Goncalves
    * @author Desenvolvedor: Grasiele Torres

    * @package URBEM
    * @subpackage Mapeamento

    $Id: $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela   compras.solicitacao_convenio
  * Data de Criação: 07/12/2008

  * Data de Criação: 09/12/2008

    * @author Analista: Gelson Wolowski Goncalves
    * @author Desenvolvedor: Grasiele Torres

    * @package URBEM
    * @subpackage Mapeamento
*/

class TComprasSolicitacaoConvenio extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TComprasSolicitacaoConvenio()
{
    parent::Persistente();
    $this->setTabela("compras.solicitacao_convenio");

    $this->setCampoCod('');
    $this->setComplementoChave('exercicio, cod_entidade, cod_solicitacao');

    $this->AddCampo('exercicio'          ,'char'   ,true,'4'   ,true ,true  ,'TComprasSolicitacao');
    $this->AddCampo('cod_entidade'       ,'integer',true,''    ,true ,true  ,'TComprasSolicitacao');
    $this->AddCampo('cod_solicitacao'    ,'integer',true,''    ,true ,true  ,'TComprasSolicitacao');
    $this->AddCampo('num_convenio'       ,'integer',true,''    ,false,true  ,'TLicitacaoConvenio' );
    $this->AddCampo('exercicio_convenio' ,'char'   ,true,'4'   ,false,true  ,'TLicitacaoConvenio' );
}

}
