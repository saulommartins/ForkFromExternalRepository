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
    * Classe de mapeamento da tabela EMPENHO.ITEM_EMPENHO_DESPESAS_FIXAS
    * Data de Criação: 30/11/2004

    * @author Analista: Cleisson Barboza e Diego Victoria
    * @author Desenvolvedor: Tonismar R. Bernardo

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: tonismar $
    $Date: 2006-09-26 14:58:23 -0300 (Ter, 26 Set 2006) $

    * Casos de uso: uc-02.03.29, uc-02.03.30
*/

/**

$Log$
Revision 1.2  2006/09/26 17:58:23  tonismar
Manter Empenho Despesas Mensais Fixas

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TEmpenhoItemEmpenhoDespesasFixas extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TEmpenhoItemEmpenhoDespesasFixas()
{
    parent::Persistente();
    $this->setTabela('empenho.item_empenho_despesas_fixas');

    $this->setCampoCod('num_item');
    $this->setComplementoChave('cod_pre_empenho, exercicio, cod_despesa' );

    $this->AddCampo('num_item','integer',true,'',true,true);
    $this->AddCampo('cod_entidade','integer',true,'',true,true);
    $this->AddCampo('exercicio','char',true,'4',true,true);
    $this->AddCampo('cod_pre_empenho','integer',true,'',true,true);
    $this->AddCampo('cod_despesa_fixa','integer',true,'',false,true);
    $this->AddCampo('cod_despesa','integer',true,'',false,true);
    $this->AddCampo('consumo','integer',true,'',false,false);
    $this->AddCampo('dt_documento','date',true,'',false,false);
}
}
