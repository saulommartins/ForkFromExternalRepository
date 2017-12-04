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
    * Classe de mapeamento da função fn_listar_arrecadacao
    * Data de Criação: 15/12//2005

    * @author Analista: Lucas Leusin Oiagem
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2008-04-14 15:37:22 -0300 (Seg, 14 Abr 2008) $

    * Casos de uso: uc-02.04.08
*/

/*
$Log$
Revision 1.5  2007/07/13 19:10:48  cako
Bug#9383#, Bug#9384#

Revision 1.4  2006/07/05 20:38:37  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FTesourariaListarArrecadacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FTesourariaListarArrecadacao()
{
    parent::Persistente();
}

function montaRecuperaTodos()
{
    $stSql = "                                                                           \n";
    if (!$this->getDado('retencao')) {
        $stSql  = "SELECT *                                                                  \n";
        $stSql .= "FROM tesouraria.fn_listar_arrecadacao('".$this->getDado("stFiltro")."', '".$this->getDado("stFiltro")."' );  \n";
    }
    $stSql .= "                                                                          \n";
    $stSql .= "SELECT tbl.exercicio                                                      \n";
    $stSql .= "      ,tbl.cod_entidade                                                   \n";
    $stSql .= "      ,tbl.cod_boletim                                                   \n";
    $stSql .= "      ,tbl.conta_debito                                                   \n";
    $stSql .= "      ,tbl.conta_credito                                                  \n";
    $stSql .= "      ,tbl.numeracao                                                      \n";
    $stSql .= "      ,tbl.cod_receita                                                    \n";
    $stSql .= "      ,tbl.tipo                                                           \n";
    if ($this->getDado('retencao')) {
       $stSql .= "   ,tbl.cod_ordem                                                     \n";
       $stSql .= "   ,tbl.cod_plano                                                     \n";
    }
    $stSql .= "      ,( tbl.valor - tbl.vl_desconto + tbl.vl_juros + tbl.vl_multa ) AS valor \n";
    $stSql .= "      ,CPCD.cod_estrutural AS cod_estrutural_debito                       \n";
    $stSql .= "      ,CPCC.cod_estrutural AS cod_estrutural_credito                      \n";
    $stSql .= "FROM(                                                                     \n";
    $stSql .= "      SELECT tmp_arrecadacao.cod_entidade                                \n";
    $stSql .= "            ,tmp_arrecadacao.exercicio                                   \n";
    $stSql .= "            ,tmp_arrecadacao.cod_boletim                                  \n";
    $stSql .= "            ,tmp_arrecadacao.conta_debito                                \n";
    $stSql .= "            ,tmp_arrecadacao.conta_credito                               \n";
    $stSql .= "            ,tmp_arrecadacao.numeracao                                   \n";
    $stSql .= "            ,tmp_arrecadacao.cod_receita                                 \n";
    $stSql .= "            ,'A' as tipo                                                  \n";
    if ($this->getDado('retencao')) {
        $stSql .= "        ,aopr.cod_ordem                                              \n";
        $stSql .= "        ,aopr.cod_plano                                              \n";
        $stSql .= "        ,valor                                                       \n";
        $stSql .= "        ,vl_desconto                                                 \n";
        $stSql .= "        ,vl_juros                                                    \n";
        $stSql .= "        ,vl_multa                                                    \n";
    } else {
        $stSql .= "        ,SUM( valor ) AS valor                                        \n";
        $stSql .= "        ,SUM( vl_desconto ) AS vl_desconto                            \n";
        $stSql .= "        ,SUM( vl_juros ) AS vl_juros                                  \n";
        $stSql .= "        ,SUM( vl_multa ) as vl_multa                                  \n";
    }
    $stSql .= "      FROM tmp_arrecadacao                                                \n";
    if ($this->getDado('retencao')) {
        $stSql .= "       JOIN tesouraria.arrecadacao_ordem_pagamento_retencao as aopr                     \n";
        $stSql .= "       ON (    aopr.cod_arrecadacao       = tmp_arrecadacao.cod_arrecadacao             \n";
        $stSql .= "           AND aopr.timestamp_arrecadacao = tmp_arrecadacao.timestamp_arrecadacao       \n";
        $stSql .= "           AND aopr.exercicio             = tmp_arrecadacao.exercicio                   \n";
        $stSql .= "           AND aopr.cod_entidade          = tmp_arrecadacao.cod_entidade                \n";
        $stSql .= "           AND aopr.cod_plano             = tmp_arrecadacao.conta_credito               \n";
        $stSql .= "       )                                                             \n";
    }
    $stSql .= "      WHERE                                                               \n";
    if($this->getDado('retencao'))
         $stSql .= " EXISTS ";
    else $stSql .= " NOT EXISTS ";
    $stSql .= "             ( SELECT aopr.cod_arrecadacao                                                         \n";
    $stSql .= "                 FROM tesouraria.arrecadacao_ordem_pagamento_retencao as aopr                      \n";
    $stSql .= "                WHERE     aopr.cod_arrecadacao       = tmp_arrecadacao.cod_arrecadacao             \n";
    $stSql .= "                      AND aopr.timestamp_arrecadacao = tmp_arrecadacao.timestamp_arrecadacao       \n";
    $stSql .= "                      AND aopr.exercicio             = tmp_arrecadacao.exercicio                   \n";
    $stSql .= "                      AND aopr.cod_entidade          = tmp_arrecadacao.cod_entidade                \n";
    $stSql .= "                      AND aopr.cod_plano             = tmp_arrecadacao.conta_credito               \n";
    $stSql .= "             )                                                                                     \n";
    if (!$this->getDado('retencao')) {
        $stSql .= "      GROUP BY exercicio                                                  \n";
        $stSql .= "              ,cod_entidade                                               \n";
        $stSql .= "              ,cod_boletim                                               \n";
        $stSql .= "              ,conta_debito                                               \n";
        $stSql .= "              ,conta_credito                                              \n";
        $stSql .= "              ,numeracao                                                  \n";
        $stSql .= "              ,cod_receita                                                \n";
    }
    $stSql .= "                                                                          \n";
    $stSql .= "      UNION ALL                                                           \n";
    $stSql .= "                                                                          \n";
    $stSql .= "      SELECT tmp_arrecadacao_estornada.cod_entidade                      \n";
    $stSql .= "            ,tmp_arrecadacao_estornada.exercicio                         \n";
    $stSql .= "            ,tmp_arrecadacao_estornada.cod_boletim                       \n";
    $stSql .= "            ,tmp_arrecadacao_estornada.conta_debito                      \n";
    $stSql .= "            ,tmp_arrecadacao_estornada.conta_credito                     \n";
    $stSql .= "            ,tmp_arrecadacao_estornada.numeracao                         \n";
    $stSql .= "            ,tmp_arrecadacao_estornada.cod_receita                       \n";
    $stSql .= "            ,'E' as tipo                                                  \n";
    if ($this->getDado('retencao')) {
        $stSql .= "        ,aeopr.cod_ordem                                              \n";
        $stSql .= "        ,aeopr.cod_plano                                              \n";
        $stSql .= "        ,valor                                                        \n";
        $stSql .= "        ,vl_desconto                                                  \n";
        $stSql .= "        ,vl_juros                                                     \n";
        $stSql .= "        ,vl_multa                                                     \n";
    } else {
        $stSql .= "        ,SUM( valor ) as valor                                        \n";
        $stSql .= "        ,SUM( vl_desconto ) as vl_desconto                            \n";
        $stSql .= "        ,SUM( vl_juros ) AS vl_juros                                  \n";
        $stSql .= "        ,SUM( vl_multa ) as vl_multa                                  \n";
    }
    $stSql .= "      FROM tmp_arrecadacao_estornada                                      \n";
    if ($this->getDado('retencao')) {
        $stSql .= "       JOIN tesouraria.arrecadacao_estornada_ordem_pagamento_retencao as aeopr                     \n";
        $stSql .= "       ON (    aeopr.cod_arrecadacao       = tmp_arrecadacao_estornada.cod_arrecadacao             \n";
        $stSql .= "           AND aeopr.timestamp_arrecadacao = tmp_arrecadacao_estornada.timestamp_arrecadacao       \n";
        $stSql .= "           AND aeopr.exercicio             = tmp_arrecadacao_estornada.exercicio                   \n";
        $stSql .= "           AND aeopr.cod_entidade          = tmp_arrecadacao_estornada.cod_entidade                \n";
        $stSql .= "           AND aeopr.cod_plano             = tmp_arrecadacao_estornada.conta_debito                \n";
        $stSql .= "       )                                                             \n";
    }

    $stSql .= "     WHERE                                                                \n";
    if($this->getDado('retencao'))
         $stSql .= " EXISTS ";
    else $stSql .= " NOT EXISTS ";
    $stSql .= "          ( SELECT aeopr.cod_arrecadacao                                                                \n";
    $stSql .= "              FROM tesouraria.arrecadacao_estornada_ordem_pagamento_retencao as aeopr                   \n";
    $stSql .= "             WHERE     aeopr.cod_arrecadacao       = tmp_arrecadacao_estornada.cod_arrecadacao          \n";
    $stSql .= "                   AND aeopr.timestamp_arrecadacao = tmp_arrecadacao_estornada.timestamp_arrecadacao    \n";
    $stSql .= "                   AND aeopr.exercicio             = tmp_arrecadacao_estornada.exercicio                \n";
    $stSql .= "                   AND aeopr.cod_entidade          = tmp_arrecadacao_estornada.cod_entidade             \n";
    $stSql .= "                   AND aeopr.cod_plano             = tmp_arrecadacao_estornada.conta_debito             \n";
    $stSql .= "          )                                                                                             \n";
    if (!$this->getDado('retencao')) {
        $stSql .= "      GROUP BY exercicio                                                  \n";
        $stSql .= "              ,cod_entidade                                               \n";
        $stSql .= "              ,cod_boletim                                               \n";
        $stSql .= "              ,conta_debito                                               \n";
        $stSql .= "              ,conta_credito                                              \n";
        $stSql .= "              ,numeracao                                                  \n";
        $stSql .= "              ,cod_receita                                                \n";
    }
    $stSql .= "                                                                          \n";
    $stSql .= "      ORDER BY exercicio                                                  \n";
    $stSql .= "              ,cod_entidade                                               \n";
    $stSql .= "              ,cod_boletim                                               \n";
    $stSql .= "              ,conta_debito                                               \n";
    $stSql .= "              ,conta_credito                                              \n";
    $stSql .= "              ,numeracao                                                  \n";
    $stSql .= ") AS tbl                                                                  \n";
    $stSql .= " ,contabilidade.plano_analitica AS CPAD                                   \n";
    $stSql .= " ,contabilidade.plano_conta     AS CPCD                                   \n";
    $stSql .= " ,contabilidade.plano_analitica AS CPAC                                   \n";
    $stSql .= " ,contabilidade.plano_conta     AS CPCC                                   \n";
    $stSql .= " WHERE tbl.exercicio     = CPAD.exercicio                                 \n";
    $stSql .= "   AND tbl.conta_debito  = CPAD.cod_plano                                 \n";
    $stSql .= "   AND CPAD.exercicio    = CPCD.exercicio                                 \n";
    $stSql .= "   AND CPAD.cod_conta    = CPCD.cod_conta                                 \n";
    $stSql .= "   AND tbl.exercicio     = CPAC.exercicio                                 \n";
    $stSql .= "   AND tbl.conta_credito = CPAC.cod_plano                                 \n";
    $stSql .= "   AND CPAC.exercicio    = CPCC.exercicio                                 \n";
    $stSql .= "   AND CPAC.cod_conta    = CPCC.cod_conta                                 \n";
    $stSql .= " ORDER BY tipo                                                            \n";
    $stSql .= "         ,exercicio                                                       \n";
    $stSql .= "         ,cod_entidade                                                    \n";
    if($this->getDado('retencao'))
        $stSql .= "     ,cod_ordem                                                       \n";
    $stSql .= "         ,conta_debito                                                    \n";
    $stSql .= "         ,conta_credito                                                   \n";

    return $stSql;
}

}
