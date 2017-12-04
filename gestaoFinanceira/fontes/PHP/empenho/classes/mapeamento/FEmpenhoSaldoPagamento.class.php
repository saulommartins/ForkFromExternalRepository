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
    * Mapeamento da função EMPENHO_FN_SALDO_PAGAMENTO
    * Data de Criação   : 13/06/2005

    * @author Diego Barbosa Victoria
    * @author Cleisson Barboza

    * @package URBEM
    * @subpackage Mapaeamento

    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.03.05
*/

/*
$Log$
Revision 1.6  2006/07/05 20:46:56  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FEmpenhoSaldoPagamento extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FEmpenhoSaldoPagamento()
{
    parent::Persistente();

    $this->setTabela('empenho.fn_saldo_pagamento');
    $this->AddCampo('exercicio'     ,'varchar',false,''    ,false,false);
    $this->AddCampo('cod_entidade'   ,'integer',false,''    ,false,false);
    $this->AddCampo('cod_ordem'   ,'integer',false,''    ,false,false);
}

function montaRecuperaTodos()
{
    $stSql  = "SELECT                                                               \n";
    $stSql .= "  ".$this->getTabela()." (                                           \n";
    $stSql .= "                               '".$this->getDado( "exercicio" )."'   \n";
    $stSql .= "                               ,".$this->getDado( "cod_entidade" )." \n";
    $stSql .= "                               ,".$this->getDado( "cod_ordem" )."    \n";
    $stSql .= "                               ) AS saldo_pagamento                  \n";

    return $stSql;
}

}
