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
    * Extensão da Classe de mapeamento
    * Data de Criação: 30/01/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Cleisson Barboza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 59708 $
    $Name$
    $Author: michel $
    $Date: 2014-09-05 16:10:36 -0300 (Fri, 05 Sep 2014) $
    
    $Id: TTPBPagamentos.class.php 59708 2014-09-05 19:10:36Z michel $

    * Casos de uso: uc-06.03.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTPBPagamentos extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTPBPagamentos()
{
    parent::Persistente();
    $this->setDado('exercicio', Sessao::getExercicio() );
}

//Mapeamento do case pode ser encontrado no documento de tabelas auxiliares do tribunal
function montaRecuperaTodos()
{
    $stSql .= " SELECT                                                                                      \n";
    $stSql .= "     emp.exercicio as ano_empenho                                                            \n";
    $stSql .= "     ,lpad(des.num_orgao::varchar, 2, '0')||lpad(des.num_unidade::varchar, 2, '0') as unidade_orcamentaria     \n";
    $stSql .= "     ,emp.cod_empenho as num_empenho                                                         \n";
    $stSql .= "     ,tc.numero_pagamento_empenho( pag.exercicio, pag.cod_entidade, pag.cod_nota, pag.timestamp) AS num_parcela \n";
    $stSql .= "     ,TO_CHAR(pag.timestamp,'ddmmyyyy') as data_pagamento                                    \n";
    $stSql .= "     ,coalesce(sum(vl_pago),0.00) as valor_pagamento                                         \n";
    $stSql .= "     ,'0' as conta_bancaria_debito                                                           \n";
    $stSql .= "     ,'0' as num_doc_deb_automatico                                                          \n";

    $stSql .= "     ,lpad(CASE WHEN substr(trim(UPPER(pag.observacao)),1,2) = 'CH'
                        THEN substring(substr(pag.observacao, length(pag.observacao)-5, 6), 'Y*([0-9]{1,6})')
                        ELSE '0' END,6,'0') as num_cheque                                                         \n";

    $stSql .= "     ,CASE WHEN pcre.cod_banco=999 THEN '000' ELSE replace(pcre.conta_corrente,'-','') END as conta_bancaria_credito \n";
    $stSql .= "
         , ( SELECT cod_origem_recursos
               FROM tcepb.pagamento_origem_recursos_interna
              WHERE pagamento_origem_recursos_interna.cod_entidade = pag.cod_entidade
                AND pagamento_origem_recursos_interna.exercicio = pag.exercicio
                AND pagamento_origem_recursos_interna.cod_nota = pag.cod_nota
                AND pagamento_origem_recursos_interna.timestamp = pag.timestamp
--           GROUP BY pagamento_origem_recursos_interna.cod_entidade
--                  , pagamento_origem_recursos_interna.exercicio
--                  , pagamento_origem_recursos_interna.cod_nota
--                  , pagamento_origem_recursos_interna.cod_origem_recursos


           ) AS origem_recursos \n";
    $stSql .= "     , 1 AS tipo_lancamento                                                                  \n";
    $stSql .= " FROM                                                                                        \n";
    $stSql .= "     orcamento.despesa               as des,                                                 \n";
    $stSql .= "     empenho.pre_empenho_despesa     as ped,                                                 \n";
    $stSql .= "     empenho.empenho                 as emp,                                                 \n";
    $stSql .= "     empenho.nota_liquidacao         as liq,                                                 \n";
    $stSql .= "     empenho.nota_liquidacao_paga    as pag,                                                 \n";
    $stSql .= "     empenho.pagamento_liquidacao_nota_liquidacao_paga as pnlp,                              \n";

    $stSql .= "     contabilidade.pagamento AS cpa,                                                         \n";

//   $stSql .= "   inner join tcepb.pagamento_origem_recursos_interna AS pori
//                       on ( pori.cod_entidade = cpa.cod_entidade        AND
//                            pori.exercicio    = cpa.exercicio           AND
//                            pori.timestamp    = cpa.timestamp           AND
//                            pori.cod_nota     = cpa.cod_nota ),             ";

    $stSql .= "     contabilidade.lancamento        as cla,                                                 \n";
    $stSql .= "     contabilidade.conta_credito     as cre,                                                 \n";

    $stSql .= "     contabilidade.plano_banco       as pcre                                                 \n";
//    $stSql .= "     tcepb.pagamento_origem_recursos_interna AS pori                                    \n";

    $stSql .= " WHERE                                                                                       \n";
    $stSql .= "     des.cod_despesa     = ped.cod_despesa       AND                                         \n";
    $stSql .= "     des.exercicio       = ped.exercicio         AND                                         \n";
    $stSql .= "                                                                                             \n";
    $stSql .= "     ped.exercicio       = emp.exercicio         AND                                         \n";
    $stSql .= "     ped.cod_pre_empenho = emp.cod_pre_empenho   AND                                         \n";
    $stSql .= "                                                                                             \n";
    $stSql .= "     emp.exercicio       = liq.exercicio_empenho AND                                         \n";
    $stSql .= "     emp.cod_empenho     = liq.cod_empenho       AND                                         \n";
    $stSql .= "     emp.cod_entidade    = liq.cod_entidade      AND                                         \n";
    $stSql .= "                                                                                             \n";
    $stSql .= "     liq.exercicio       = pag.exercicio         AND                                         \n";
    $stSql .= "     liq.cod_nota        = pag.cod_nota          AND                                         \n";
    $stSql .= "     liq.cod_entidade    = pag.cod_entidade      AND                                         \n";
    $stSql .= "                                                                                             \n";
    $stSql .= "     pag.exercicio       = cpa.exercicio_liquidacao  AND                                     \n";
    $stSql .= "     pag.cod_nota        = cpa.cod_nota          AND                                         \n";
    $stSql .= "     pag.cod_entidade    = cpa.cod_entidade      AND                                         \n";
    $stSql .= "     pag.timestamp       = cpa.timestamp         AND                                         \n";
    $stSql .= "                                                                                             \n";
    $stSql .= "     pag.exercicio       = pnlp.exercicio_liquidacao AND                                     \n";
    $stSql .= "     pag.cod_entidade    = pnlp.cod_entidade     AND                                         \n";
    $stSql .= "     pag.cod_nota        = pnlp.cod_nota         AND                                         \n";
    $stSql .= "     pag.timestamp     = pnlp.timestamp          AND                                         \n";
    $stSql .= "                                                                                             \n";
    $stSql .= "     cpa.exercicio       = cre.exercicio         AND                                         \n";
    $stSql .= "     cpa.cod_lote        = cre.cod_lote          AND                                         \n";
    $stSql .= "     cpa.cod_entidade    = cre.cod_entidade      AND                                         \n";
    $stSql .= "     cpa.tipo            = cre.tipo              AND                                         \n";
    $stSql .= "     cre.sequencia       = 2                     AND                                         \n";
    $stSql .= "                                                                                             \n";
    $stSql .= "     cla.exercicio       = cpa.exercicio         AND                                         \n";
    $stSql .= "     cla.cod_lote        = cpa.cod_lote          AND                                         \n";
    $stSql .= "     cla.cod_entidade    = cpa.cod_entidade      AND                                         \n";
    $stSql .= "     cla.tipo            = cpa.tipo              AND                                         \n";
    $stSql .= "     cla.sequencia       = cpa.sequencia         AND                                         \n";
    $stSql .= "                                                                                             \n";
    $stSql .= "     cre.exercicio       = pcre.exercicio        AND                                         \n";
    $stSql .= "     cre.cod_plano       = pcre.cod_plano                                                    \n";
    $stSql .= "                                                                                             \n";
//    $stSql .= "     AND pori.cod_entidade = cpa.cod_entidade                                              \n";
//    $stSql .= "     AND pori.exercicio    = cpa.exercicio                                                 \n";
//    $stSql .= "     AND pori.timestamp    = cpa.timestamp                                                 \n";
//    $stSql .= "     AND pori.cod_nota     = cpa.cod_nota                                                  \n";
    if ( $this->getDado('exercicio') ) {
        $stSql .= " AND emp.exercicio = '".$this->getDado('exercicio')."'                                   \n";
    }
    if ( $this->getDado('stEntidades') ) {
        $stSql .= " AND   emp.cod_entidade in (".$this->getDado('stEntidades').")                           \n";
    }
    if ( $this->getDado('inMes') ) {
        $stSql .= " AND     to_char(pag.timestamp,'mm') = '".$this->getDado('inMes')."'                     \n";
    }
    $stSql .= "                                                                                             \n";
    $stSql .= " GROUP BY                                                                                    \n";
    $stSql .= "      emp.exercicio                                                                          \n";
    $stSql .= "     ,emp.cod_empenho                                                                        \n";
    $stSql .= "     ,TO_CHAR(pag.timestamp,'ddmmyyyy')                                                    \n";
    $stSql .= "     ,des.num_orgao                                                                          \n";
    $stSql .= "     ,des.num_unidade                                                                        \n";
    $stSql .= "     ,pcre.cod_banco                                                                         \n";
    $stSql .= "     ,pcre.cod_agencia                                                                       \n";
    $stSql .= "     ,pcre.conta_corrente                                                                    \n";
    $stSql .= "     ,pag.observacao                                                                   \n";
    $stSql .= "     ,pnlp.cod_ordem
                    ,pag.exercicio
                    ,pag.cod_entidade
                    ,pag.cod_nota

                    ,pag.timestamp \n";

    $stSql .= "                                                                                             \n";
    $stSql .= " ORDER BY                                                                                    \n";
    $stSql .= "     emp.exercicio                                                                           \n";
    $stSql .= "     ,emp.cod_empenho                                                                        \n";
    $stSql .= "     ,TO_CHAR(pag.timestamp,'ddmmyyyy')                                                    \n";
    //$stSql .= "     ,pag.timestamp                                                                          \n";
    return $stSql;
}
}
