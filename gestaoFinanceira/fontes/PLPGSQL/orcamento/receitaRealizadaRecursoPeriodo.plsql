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
/*
* Script de função PLPGSQL
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Revision: 12203 $
* $Name$
* $Author: cleisson $
* $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $
*
* Casos de uso: uc-02.01.29
*/

/*
$Log$
Revision 1.6  2006/07/05 20:38:05  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION orcamento.fn_receita_realizada_recurso_periodo(varchar,varchar,integer,varchar,varchar) RETURNS NUMERIC(14,2) AS $$
DECLARE
    inExercicio         ALIAS FOR $1        ;
    stCodEntidade       ALIAS FOR $2        ;
    inCodRecurso        ALIAS FOR $3        ;
    dtInicial           ALIAS FOR $4        ;
    dtFinal             ALIAS FOR $5        ;
    nuSoma1             NUMERIC := 0.00     ;
    nuSoma2             NUMERIC := 0.00     ;
    nuRetorno           NUMERIC := 0.00     ;
    stSql1              VARCHAR :=''        ;
    stSql2              VARCHAR :=''        ;
    crCursor1           refCursor           ;
    crCursor2           refCursor           ;
BEGIN
-- Faz a soma  dos valores com estorno igual a false
stSql1 := '
    SELECT
            coalesce(SUM(vl.vl_lancamento),0.00)
        FROM
            contabilidade.valor_lancamento      as vl   ,
            orcamento.conta_receita             as ocr  ,
            orcamento.receita                   as ore  ,
            contabilidade.lancamento_receita    as lr   ,
            contabilidade.lancamento            as lan  ,
            contabilidade.lote                  as lote ,
            contabilidade.conta_debito          as ccd  ,
            contabilidade.plano_analitica       as cpa  ,
            contabilidade.plano_conta           as cpc
            --contabilidade.conta_receita         as ccr
        WHERE ore.cod_recurso   = '||inCodRecurso||'
        AND ore.cod_entidade    IN ( '||stCodEntidade||' )
        AND ore.exercicio       = '|| quote_literal(inExercicio) ||'
        AND ocr.cod_conta       = ore.cod_conta
        AND ocr.exercicio       = ore.exercicio
        -- join lancamento receita
        AND lr.cod_receita      = ore.cod_receita
        AND lr.exercicio        = ore.exercicio
        AND lr.cod_entidade     = ore.cod_entidade
        AND lr.estorno          = true
        AND lr.tipo             = ''A''
        -- join nas tabelas lancamento_receita e lancamento
        AND lan.cod_lote        = lr.cod_lote
        AND lan.sequencia       = lr.sequencia
        AND lan.exercicio       = lr.exercicio
        AND lan.cod_entidade    = lr.cod_entidade
        AND lan.tipo            = lr.tipo
        -- join nas tabelas lancamento e valor_lancamento
        AND vl.exercicio        = lan.exercicio
        AND vl.sequencia        = lan.sequencia
        AND vl.cod_entidade     = lan.cod_entidade
        AND vl.cod_lote         = lan.cod_lote
        AND vl.tipo             = lan.tipo
        -- ligar conta debito
        AND ccd.cod_lote        = vl.cod_lote
        AND ccd.tipo            = vl.tipo
        AND ccd.sequencia       = vl.sequencia
        AND ccd.exercicio       = vl.exercicio
        AND ccd.tipo_valor      = vl.tipo_valor
        AND ccd.cod_entidade    = vl.cod_entidade
        -- ligar plano analitica
        AND cpa.cod_plano       = ccd.cod_plano
        AND cpa.exercicio       = ccd.exercicio
        -- ligar plano conta
        AND cpc.cod_conta       = cpa.cod_conta
        AND cpc.exercicio       = cpa.exercicio
        -- sistema contabil TEM que ser 1
        AND cpc.cod_sistema    IN( 1,3,4 )
        -- tipo de lancamento receita deve ser = A , de arrecadação
        AND  lr.tipo            = ''A''
        -- na tabela valor lancamento  tipo_valor deve ser credito
        AND vl.tipo_valor       = ''D''
        -- Data Inicial e Data Final, antes iguala codigo do lote
        AND lote.cod_lote       = lan.cod_lote
        AND lote.cod_entidade   = lan.cod_entidade
        AND lote.exercicio      = lan.exercicio
        AND lote.tipo           = lan.tipo
        AND lote.dt_lote BETWEEN to_date('|| quote_literal(dtInicial) ||',''dd/mm/yyyy'') AND to_date('|| quote_literal(dtFinal) ||',''dd/mm/yyyy'');
    ';



-- Faz a soma  dos valores com estorno igual a false
stSql2 := '
    SELECT
            coalesce(sum(vl.vl_lancamento),0.00)
        FROM
            contabilidade.valor_lancamento      as vl   ,
            orcamento.conta_receita             as ocr  ,
            orcamento.receita                   as ore  ,
            contabilidade.lancamento_receita    as lr   ,
            contabilidade.lancamento            as lan  ,
            contabilidade.lote                  as lote ,
            contabilidade.conta_credito         as ccr  ,
            contabilidade.plano_analitica       as cpa  ,
            contabilidade.plano_conta           as cpc
        WHERE ore.cod_recurso   = '||inCodRecurso||'
        AND ore.cod_entidade    IN('||stCodEntidade||' )
        AND ore.exercicio       = '|| quote_literal(inExercicio) ||'
        AND ocr.cod_conta       = ore.cod_conta
        AND ocr.exercicio       = ore.exercicio
        -- join lancamento receita
        AND lr.cod_receita      = ore.cod_receita
        AND lr.exercicio        = ore.exercicio
        AND lr.cod_entidade     = ore.cod_entidade
        AND lr.estorno          = false
        AND lr.tipo             = ''A''
        -- join nas tabelas lancamento_receita e lancamento
        AND lan.cod_lote        = lr.cod_lote
        AND lan.sequencia       = lr.sequencia
        AND lan.exercicio       = lr.exercicio
        AND lan.cod_entidade    = lr.cod_entidade
        AND lan.tipo            = lr.tipo
        -- join nas tabelas lancamento e valor_lancamento
        AND vl.exercicio        = lan.exercicio
        AND vl.sequencia        = lan.sequencia
        AND vl.cod_entidade     = lan.cod_entidade
        AND vl.cod_lote         = lan.cod_lote
        AND vl.tipo             = lan.tipo
        -- ligar conta debito
        AND ccr.cod_lote        = vl.cod_lote
        AND ccr.tipo            = vl.tipo
        AND ccr.sequencia       = vl.sequencia
        AND ccr.exercicio       = vl.exercicio
        AND ccr.tipo_valor      = vl.tipo_valor
        AND ccr.cod_entidade    = vl.cod_entidade
        -- ligar plano analitica
        AND cpa.cod_plano       = ccr.cod_plano
        AND cpa.exercicio       = ccr.exercicio
        -- ligar plano conta
        AND cpc.cod_conta       = cpa.cod_conta
        AND cpc.exercicio       = cpa.exercicio
        -- sistema contabil TEM que ser 1
        AND cpc.cod_sistema     IN( 1,3,4 )
        -- tipo de lancamento receita deve ser = A , de arrecadação
        AND lr.tipo             = ''A''
        -- na tabela valor lancamento  tipo_valor deve ser credito
        AND vl.tipo_valor       = ''C''
        -- Data Inicial e Data Final, antes iguala codigo do lote
        AND lote.cod_lote       = lan.cod_lote
        AND lote.cod_entidade   = lan.cod_entidade
        AND lote.exercicio      = lan.exercicio
        AND lote.tipo           = lan.tipo
        AND lote.dt_lote BETWEEN to_date('|| quote_literal(dtInicial) ||',''dd/mm/yyyy'') AND to_date('|| quote_literal(dtFinal) ||',''dd/mm/yyyy'');
    ';

-- Verifica a diferença(soma)
    OPEN crCursor1 FOR EXECUTE stSql1;
    FETCH crCursor1 INTO nuSoma1;
    OPEN crCursor2 FOR EXECUTE stSql2;
    FETCH crCursor2 INTO nuSoma2;
    nuRetorno   := ( nuSoma1 + nuSoma2 ) * -1;
    CLOSE crCursor1;
    CLOSE crCursor2;
    RETURN nuRetorno;
END;
$$ language 'plpgsql';
