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
    * Data de Criação   : 08/09/2006

    * @author Eduardo Martins

    * @package URBEM
    * @subpackage Mapaeamento

    $Revision: 30668 $
    $Name$
    $Author: eduardo $
    $Date: 2006-09-28 06:56:56 -0300 (Qui, 28 Set 2006) $

    * Casos de uso: uc-02.03.05
*/

/*
$Log$
Revision 1.1  2006/09/28 09:52:53  eduardo
Bug #7060#

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FEmpenhoConsultarValoresOP extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FEmpenhoConsultarValoresOP()
{
    parent::Persistente();

    $this->setTabela('empenho.fn_consultar_valores_op');
    $this->AddCampo('exercicio'      ,'varchar',false,''    ,false,false);
    $this->AddCampo('cod_entidade'   ,'integer',false,''    ,false,false);
    $this->AddCampo('cod_ordem'      ,'integer',false,''    ,false,false);
}

function montaRecuperaTodos()
{
    $stSql  = "select * \n";
    $stSql .= "from ". $this->getTabela() ."( '".$this->getDado('exercicio')   ."', \n";
    $stSql .= "                                ".$this->getDado('cod_entidade').",  \n";
    $stSql .= "                                ".$this->getDado('cod_ordem')   .")  \n";
    $stSql .= "     as retorno ( cod_nota                integer,  \n";
    $stSql .= "                  exercicio_liquidacao    varchar,  \n";
    $stSql .= "                  vl_pagamento            numeric, \n";
    $stSql .= "                  vl_pagamento_anulado    numeric, \n";
    $stSql .= "                  vl_pago                 numeric, \n";
    $stSql .= "                  vl_pago_anulado         numeric, \n";
    $stSql .= "                  vl_a_anular             numeric \n";
    $stSql .= "                ); \n";

    return $stSql;
}

/**
 * Recupera Valor a Pagar da OP
 *
 * @access Public
 *
 * @param $rsRecordSet RecordSet
 * @param $stFiltro String
 * @param $stOrder String
 * @param $boTransacao Boolean
 *
 * @return Erro
 */
function recuperaValorAPagarLiquidacao(&$rsRecordSet, $stFiltro='', $stOrder='', $boTransacao ='')
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stFiltro = ' where 1=1 ';

    if ( $this->getDado('cod_nota') ) {
        $stFiltro .= ' AND cod_nota = ' . $this->getDado('cod_nota');
    }
    if ( $this->getDado('exercicio_liquidacao') ) {
        $stFiltro .= " AND exercicio_liquidacao = '" . $this->getDado('exercicio_liquidacao') . "' ";
    }

    $stSql = $this->montaRecuperaValorAPagarLiquidacao().$stFiltro.$stOrder;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaValorAPagarLiquidacao()
{
    $stSql .= "select  cod_nota, exercicio_liquidacao, \n";
    $stSql .= "         ( coalesce(vl_pagamento,0.00) - coalesce(vl_pagamento_anulado,0.00) ) \n";
    $stSql .= "       - ( coalesce(vl_pago     ,0.00) - coalesce(vl_pago_anulado     ,0.00) ) as vl_a_pagar \n";
    $stSql .= "from ". $this->getTabela() ."( '".$this->getDado('exercicio')   ."', \n";
    $stSql .= "                                ".$this->getDado('cod_entidade').",  \n";
    $stSql .= "                                ".$this->getDado('cod_ordem')   .")  \n";
    $stSql .= "     as retorno ( cod_nota                integer,  \n";
    $stSql .= "                  exercicio_liquidacao    varchar,  \n";
    $stSql .= "                  vl_pagamento            numeric, \n";
    $stSql .= "                  vl_pagamento_anulado    numeric, \n";
    $stSql .= "                  vl_pago                 numeric, \n";
    $stSql .= "                  vl_pago_anulado         numeric, \n";
    $stSql .= "                  vl_a_anular             numeric  \n";
    $stSql .= "                ) \n";
    $stSql .= " \n";

    return $stSql;
}

/**
 * Recupera Valor a Pagar da OP
 *
 * @access Public
 *
 * @param $rsRecordSet RecordSet
 * @param $stFiltro String
 * @param $stOrder String
 * @param $boTransacao Boolean
 *
 * @return Erro
 */
function recuperaValorAPagar(&$rsRecordSet, $stFiltro='', $stOrder='', $boTransacao ='')
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaValorAPagar().$stOrder;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaValorAPagar()
{
    $stSql .= "select   ( coalesce(sum(vl_pagamento),0.00) - coalesce(sum(vl_pagamento_anulado),0.00) ) \n";
    $stSql .= "       - ( coalesce(sum(vl_pago)     ,0.00) - coalesce(sum(vl_pago_anulado)     ,0.00) ) as vl_a_pagar \n";
    $stSql .= "from ( \n";
    $stSql .= "select * \n";
    $stSql .= "from ". $this->getTabela() ."( '".$this->getDado('exercicio')   ."', \n";
    $stSql .= "                                ".$this->getDado('cod_entidade').",  \n";
    $stSql .= "                                ".$this->getDado('cod_ordem')   .")  \n";
    $stSql .= "     as retorno ( cod_nota                integer,  \n";
    $stSql .= "                  exercicio_liquidacao    varchar,  \n";
    $stSql .= "                  vl_pagamento            numeric, \n";
    $stSql .= "                  vl_pagamento_anulado    numeric, \n";
    $stSql .= "                  vl_pago                 numeric, \n";
    $stSql .= "                  vl_pago_anulado         numeric, \n";
    $stSql .= "                  vl_a_anular             numeric  \n";
    $stSql .= "                ) \n";
    $stSql .= ") as tbl \n";

    return $stSql;
}

}
