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
* $Revision: 27052 $
* $Name$
* $Author: cako $
* $Date: 2007-12-05 15:12:56 -0200 (Qua, 05 Dez 2007) $
*
* Casos de uso: uc-02.01.32
*/

/*
$Log$
Revision 1.6  2007/03/01 20:08:58  luciano
#8509#

Revision 1.5  2006/07/18 20:10:00  andre.almeida
Bug #6556#

Revision 1.4  2006/07/14 17:58:41  andre.almeida
Bug #6556#

Alterado scripts de NOT IN para NOT EXISTS.

Revision 1.3  2006/07/05 20:38:05  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION orcamento.fn_razao_despesa(  VARCHAR 
                                                        ,INT 
                                                        ,CHARACTER
                                                        ,CHARACTER
                                                        ,CHARACTER
                                                        ,CHARACTER
                                                        ,CHARACTER
                                                        ,CHARACTER
                                                        ,CHARACTER
                                                        ,INT
                                                        ,VARCHAR
                                                        ,VARCHAR
                                                    ) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio             ALIAS FOR $1;
    inCodEntidade           ALIAS FOR $2;
    stDataInicio            ALIAS FOR $3;
    stDataFim               ALIAS FOR $4;
    inCodDotacao            ALIAS FOR $5;
    boEmpenho               ALIAS FOR $6;
    boLiquidacao            ALIAS FOR $7;
    boPagamento             ALIAS FOR $8;
    boSuplementacao         ALIAS FOR $9;
    inCodConta              ALIAS FOR $10;
    stDestinacaoRecurso     ALIAS FOR $11;
    inCodDetalhamento       ALIAS FOR $12;

    stSubSql                VARCHAR := '';
    stSubSql2               VARCHAR := '';
    stSql                   VARCHAR := '';
    reRegistro              RECORD;
    
BEGIN

stSql := '
create temporary table tmp_lancamento AS

--SELECT PARA TABELA DE CREDITO
select
     clt.dt_lote
    ,chc.complemento as boo
    ,cl.complemento
    ,cle.estorno
    ,ccc.cod_plano
    ,cl.sequencia
    ,cpc.cod_estrutural
    ,ccc.tipo_valor
    ,chc.nom_historico
    ,abs(cvl.vl_lancamento) as vl_lancamento

    --CHAVE PARA O INDICE
    ,cle.cod_lote
    ,cle.tipo
    ,cle.exercicio
    ,cle.cod_entidade
from
     contabilidade.lancamento_empenho as cle
    ,contabilidade.lancamento as cl
    ,contabilidade.historico_contabil as chc
    ,contabilidade.lote as clt
    ,contabilidade.valor_lancamento as cvl
    ,contabilidade.plano_analitica as cpa

    ,contabilidade.plano_conta as cpc
    ,contabilidade.conta_credito as ccc

where
    cle.tipo in (''E'',''L'',''P'')
    and cle.exercicio = '''||stExercicio||'''
    and cle.cod_entidade = '||inCodEntidade||'
    ';
    if stDataFim is not null and stDataFim <> '' then
        stSql := stSql || ' and clt.dt_lote <= to_date('''||stDataFim||''',''dd-mm-yyyy'') ';
    end if;
    if stDataInicio is not null and stDataInicio <> '''' then
        stSql := stSql || ' and clt.dt_lote >= to_date('''||stDataInicio||''',''dd-mm-yyyy'') ';
    end if;
    if boEmpenho = 'N' then
        stSql := stSql || ' and cl.tipo <> ''E'' ';
    end if;
    if boLiquidacao = 'N' then
        stSql := stSql || ' and cl.tipo <> ''L'' ';
    end if;
    if boPagamento = 'N' then
        stSql := stSql || ' and cl.tipo <> ''P'' ';
    end if;
    if boSuplementacao = 'N' then
        stSql := stSql || ' and cl.tipo <> ''S'' ';
    end if;

stSql := stSql || '

    and cle.cod_lote = cl.cod_lote
    and cle.tipo = cl.tipo
    and cle.sequencia = cl.sequencia
    and cle.exercicio = cl.exercicio
    and cle.cod_entidade = cl.cod_entidade

    and chc.cod_historico = cl.cod_historico
    and chc.exercicio = cl.exercicio

    and clt.cod_lote = cl.cod_lote
    and clt.exercicio = cl.exercicio
    and clt.tipo = cl.tipo
    and clt.cod_entidade = cl.cod_entidade

    and cvl.cod_lote = cl.cod_lote
    and cvl.tipo = cl.tipo
    and cvl.sequencia = cl.sequencia
    and cvl.exercicio = cl.exercicio
    and cvl.cod_entidade = cl.cod_entidade

    and cvl.tipo_valor = ccc.tipo_valor
    and cvl.cod_lote = ccc.cod_lote
    and cvl.tipo = ccc.tipo
    and cvl.sequencia = ccc.sequencia
    and cvl.exercicio = ccc.exercicio
    and cvl.cod_entidade = ccc.cod_entidade

    and ccc.cod_plano = cpa.cod_plano
    and ccc.exercicio = cpa.exercicio

    and cpa.cod_conta = cpc.cod_conta
    and cpa.exercicio = cpc.exercicio

UNION

--SELECT PARA TABELA DE DEBITO
select
     clt.dt_lote
    ,chc.complemento as boo
    ,cl.complemento
    ,cle.estorno
    ,ccd.cod_plano
    ,cl.sequencia
    ,cpc.cod_estrutural
    ,ccd.tipo_valor
    ,chc.nom_historico
    ,abs(cvl.vl_lancamento) as vl_lancamento

    --CHAVE PARA O INDICE
    ,cle.cod_lote
    ,cle.tipo
    ,cle.exercicio
    ,cle.cod_entidade
from
     contabilidade.lancamento_empenho as cle
    ,contabilidade.lancamento as cl
    ,contabilidade.historico_contabil as chc
    ,contabilidade.lote as clt
    ,contabilidade.valor_lancamento as cvl
    ,contabilidade.plano_analitica as cpa
    ,contabilidade.plano_banco as cpb

    ,contabilidade.plano_conta as cpc
    ,contabilidade.conta_debito as ccd
where
    cle.tipo = ''P''
    and cle.exercicio = '''||stExercicio||'''
    and cle.cod_entidade = '||inCodEntidade||' ';

    if stDataFim is not null and stDataFim <> '' then
        stSql := stSql || ' and clt.dt_lote <= to_date('''||stDataFim||''',''dd-mm-yyyy'') ';
    end if;
    if stDataInicio is not null and stDataInicio <> '' then
        stSql := stSql || ' and clt.dt_lote >= to_date('''||stDataInicio||''',''dd-mm-yyyy'') ';
    end if;
    if boEmpenho = 'N' then
        stSql := stSql || ' and cl.tipo <> ''E'' ';
    end if;
    if boLiquidacao = 'N' then
        stSql := stSql || ' and cl.tipo <> ''L'' ';
    end if;
    if boPagamento = 'N' then
        stSql := stSql || ' and cl.tipo <> ''P'' ';
    end if;
    if boSuplementacao = 'N' then
        stSql := stSql || ' and cl.tipo <> ''S'' ';
    end if;

stSql := stSql || '

    and cle.cod_lote = cl.cod_lote
    and cle.tipo = cl.tipo
    and cle.sequencia = cl.sequencia
    and cle.exercicio = cl.exercicio
    and cle.cod_entidade = cl.cod_entidade

    and chc.cod_historico = cl.cod_historico
    and chc.exercicio = cl.exercicio

    and clt.cod_lote = cl.cod_lote
    and clt.exercicio = cl.exercicio
    and clt.tipo = cl.tipo
    and clt.cod_entidade = cl.cod_entidade

    and cvl.cod_lote = cl.cod_lote
    and cvl.tipo = cl.tipo
    and cvl.sequencia = cl.sequencia
    and cvl.exercicio = cl.exercicio
    and cvl.cod_entidade = cl.cod_entidade

    and cvl.tipo_valor = ccd.tipo_valor
    and cvl.cod_lote = ccd.cod_lote
    and cvl.tipo = ccd.tipo
    and cvl.sequencia = ccd.sequencia
    and cvl.exercicio = ccd.exercicio
    and cvl.cod_entidade = ccd.cod_entidade

    and ccd.cod_plano = cpa.cod_plano
    and ccd.exercicio = cpa.exercicio

    and cpa.cod_conta = cpc.cod_conta
    and cpa.exercicio = cpc.exercicio

    and cpa.cod_plano = cpb.cod_plano
    and cpa.exercicio = cpb.exercicio

UNION
---------------SELECT SUPLEMENTACAO----------------------

select 
     clt.dt_lote
    ,chc.complemento as boo
    ,cl.complemento
    ,chc.complemento
    ,cl.cod_lote
    ,cl.sequencia
    ,clt.nom_lote
    ,cvl.tipo_valor
    ,chc.nom_historico
    ,abs(cvl.vl_lancamento) as vl_lancamento
    ,cl.cod_lote
    ,cl.tipo
    ,cl.exercicio
    ,cl.cod_entidade
from
     contabilidade.lancamento cl
    ,contabilidade.valor_lancamento cvl
    ,contabilidade.historico_contabil chc
    ,contabilidade.lote clt
    ,contabilidade.lancamento_transferencia ct
    ,contabilidade.transferencia_despesa ctd
    ,orcamento.suplementacao os
    ,orcamento.suplementacao_reducao osr
where
    cl.tipo in (''S'',''T'')
    and os.cod_tipo <> 16
    and cl.exercicio = '''||stExercicio||'''
    and cl.cod_entidade = '||inCodEntidade||'
    and osr.cod_despesa = '||inCodDotacao||' ';

    if stDataFim is not null and stDataFim <> '' then
        stSql := stSql || ' and clt.dt_lote <= to_date('''||stDataFim||''',''dd-mm-yyyy'')';
    end if;
    if stDataInicio is not null and stDataInicio <> '' then
        stSql := stSql || ' and clt.dt_lote >= to_date('''||stDataInicio||''',''dd-mm-yyyy'') ';
    end if;
    if boEmpenho = 'N' then
        stSql := stSql || ' and cl.tipo <> ''E'' ';
    end if;
    if boLiquidacao = 'N' then
        stSql := stSql || ' and cl.tipo <> ''L'' ';
    end if;
    if boPagamento = 'N' then
        stSql := stSql || ' and cl.tipo <> ''P'' ';
    end if;
    if boSuplementacao = 'N' then
        stSql := stSql || ' and cl.tipo <> ''S'' ';
    end if;

stSql := stSql || '
    and cl.cod_lote = cvl.cod_lote
    and cl.tipo = cvl.tipo
    and cl.sequencia = cvl.sequencia
    and cl.exercicio = cvl.exercicio
    and cl.cod_entidade = cvl.cod_entidade

    and chc.cod_historico = cl.cod_historico
    and chc.exercicio = cl.exercicio

    and clt.cod_lote = cl.cod_lote
    and clt.tipo = cl.tipo
    and clt.exercicio = cl.exercicio
    and clt.cod_entidade = cl.cod_entidade

    and cl.cod_lote = ct.cod_lote
    and cl.tipo = clt.tipo
    and cl.sequencia = ct.sequencia
    and cl.exercicio = ct.exercicio
    and cl.cod_entidade = ct.cod_entidade

    and ct.cod_entidade = ctd.cod_entidade
    and ct.cod_tipo = ctd.cod_tipo
    and ct.exercicio = ctd.exercicio
    and ct.sequencia = ctd.sequencia
    and ct.tipo = ctd.tipo
    and ct.cod_lote = ctd.cod_lote

    and ctd.exercicio = os.exercicio
    and ctd.cod_suplementacao = os.cod_suplementacao
    and ctd.cod_tipo = os.cod_tipo

    and os.cod_suplementacao = osr.cod_suplementacao
    and os.exercicio = osr.exercicio

    AND NOT EXISTS ( SELECT 1
                       FROM orcamento.suplementacao_anulada osa
                      WHERE cod_suplementacao = osr.cod_suplementacao
                        AND osa.exercicio = '''||stExercicio||'''
    )
    AND NOT EXISTS ( SELECT 1
                       FROM orcamento.suplementacao_anulada osa
                      WHERE osa.cod_suplementacao_anulacao = osr.cod_suplementacao
                        AND osa.exercicio = '''||stExercicio||'''
    )
union

select 
     clt.dt_lote
    ,chc.complemento as boo
    ,cl.complemento
    ,chc.complemento
    ,cl.cod_lote
    ,cl.sequencia
    ,clt.nom_lote
    ,cvl.tipo_valor
    ,chc.nom_historico
    ,abs(cvl.vl_lancamento) as vl_lancamento
    ,cl.cod_lote
    ,cl.tipo
    ,cl.exercicio
    ,cl.cod_entidade
from
     contabilidade.lancamento cl
    ,contabilidade.valor_lancamento cvl
    ,contabilidade.historico_contabil chc
    ,contabilidade.lote clt
    ,contabilidade.lancamento_transferencia ct
    ,contabilidade.transferencia_despesa ctd
    ,orcamento.suplementacao os
    ,orcamento.suplementacao_reducao oss
where
    cl.tipo in (''S'',''T'')
    and cl.exercicio = '''||stExercicio||'''
    and cl.cod_entidade = '||inCodEntidade||'
    and oss.cod_despesa = '||inCodDotacao||' 
    ';

    if stDataFim is not null and stDataFim <> '' then
        stSql := stSql || ' and clt.dt_lote <= to_date('''||stDataFim||''',''dd-mm-yyyy'') ';
    end if;
    if stDataInicio is not null and stDataInicio <> '' then
        stSql := stSql || ' and clt.dt_lote >= to_date('''||stDataInicio||''',''dd-mm-yyyy'')';
    end if;
    if boEmpenho = 'N' then
        stSql := stSql || ' and cl.tipo <> ''E'' ';
    end if;
    if boLiquidacao = 'N' then
        stSql := stSql || ' and cl.tipo <> ''L'' ';
    end if;
    if boPagamento = 'N' then
        stSql := stSql || ' and cl.tipo <> ''P'' ';
    end if;
    if boSuplementacao = 'N' then
        stSql := stSql || ' and cl.tipo <> ''S'' ';
    end if;

stSql := stSql || '
    and cl.cod_lote = cvl.cod_lote
    and cl.tipo = cvl.tipo
    and cl.sequencia = cvl.sequencia
    and cl.exercicio = cvl.exercicio
    and cl.cod_entidade = cvl.cod_entidade

    and chc.cod_historico = cl.cod_historico
    and chc.exercicio = cl.exercicio

    and clt.cod_lote = cl.cod_lote
    and clt.tipo = cl.tipo
    and clt.exercicio = cl.exercicio
    and clt.cod_entidade = cl.cod_entidade

    and cl.cod_lote = ct.cod_lote
    and cl.tipo = clt.tipo
    and cl.sequencia = ct.sequencia
    and cl.exercicio = ct.exercicio
    and cl.cod_entidade = ct.cod_entidade

    and ct.cod_entidade = ctd.cod_entidade
    and ct.cod_tipo = ctd.cod_tipo
    and ct.exercicio = ctd.exercicio
    and ct.sequencia = ctd.sequencia
    and ct.tipo = ctd.tipo
    and ct.cod_lote = ctd.cod_lote

    and ctd.exercicio = os.exercicio
    and ctd.cod_suplementacao = os.cod_suplementacao
    and ctd.cod_tipo = os.cod_tipo

    and os.cod_suplementacao = oss.cod_suplementacao
    and os.exercicio = oss.exercicio

    AND NOT EXISTS ( SELECT 1
                       FROM orcamento.suplementacao_anulada osa
                      WHERE osa.cod_suplementacao = oss.cod_suplementacao
                        AND osa.exercicio = '''||stExercicio||'''
                   )
    AND NOT EXISTS ( SELECT 1
                       FROM orcamento.suplementacao_anulada osa
                      WHERE osa.cod_suplementacao_anulacao = oss.cod_suplementacao
                        AND osa.exercicio = '''||stExercicio||'''
                   )
';
EXECUTE stSql;

create index idx_tmp_lancamento on tmp_lancamento(cod_lote,tipo,sequencia,exercicio,cod_entidade);

        stSubSql := '
        create temporary table tmp_lancamento_sub as
        select
             tmp.dt_lote
            ,tmp.boo
            ,tmp.complemento
            ,tmp.estorno
            ,tmp.cod_plano
            ,tmp.sequencia
            ,tmp.cod_estrutural
            ,tmp.tipo_valor
            ,tmp.nom_historico
            ,tmp.vl_lancamento
--            
            ,ce.exercicio
            ,ce.tipo
            ,ce.cod_lote
            ,ce.cod_entidade
            ,cgm.nom_cgm
            ,cgm.numcgm
            ,eped.cod_conta
            ,eped.cod_despesa
        from
             contabilidade.empenhamento as ce
            ,empenho.empenho as ee
            ,empenho.pre_empenho as epe
            ,empenho.pre_empenho_despesa as eped
            ,sw_cgm as cgm
            ,tmp_lancamento as tmp
        where
            tmp.exercicio = '''||stExercicio||'''
            and tmp.cod_entidade = '||inCodEntidade||'
            ';

            if inCodDotacao is not null and inCodDotacao <> '' then
                stSubSql := stSubSql || ' and eped.cod_despesa = '||inCodDotacao||' ';
            end if;
            stSubSql := stSubSql || ' and eped.cod_conta = '||inCodConta||' ';

        stSubSql := stSubSql || '
            and ce.cod_empenho = ee.cod_empenho
            and ce.exercicio = ee.exercicio
            and ce.cod_entidade = ee.cod_entidade

            and ee.cod_pre_empenho = epe.cod_pre_empenho
            and ee.exercicio = epe.exercicio

            and epe.cod_pre_empenho = eped.cod_pre_empenho
            and epe.exercicio = eped.exercicio

            and epe.cgm_beneficiario = cgm.numcgm

            and tmp.exercicio = ce.exercicio
            and tmp.sequencia = ce.sequencia
            and tmp.tipo = ce.tipo
            and tmp.cod_lote = ce.cod_lote
            and tmp.cod_entidade = ce.cod_entidade
        UNION
        select 
             tmp.dt_lote
            ,tmp.boo
            ,tmp.complemento
            ,tmp.estorno
            ,tmp.cod_plano
            ,tmp.sequencia
            ,tmp.cod_estrutural
            ,tmp.tipo_valor
            ,tmp.nom_historico
            ,tmp.vl_lancamento
--
            ,clq.exercicio
            ,clq.tipo
            ,clq.cod_lote
            ,clq.cod_entidade
            ,cgm.nom_cgm
            ,cgm.numcgm
            ,eped.cod_conta
            ,eped.cod_despesa
        from
             contabilidade.liquidacao as clq
            ,empenho.nota_liquidacao as enl
            ,empenho.empenho as ee
            ,empenho.pre_empenho as epe
            ,empenho.pre_empenho_despesa as eped
            ,sw_cgm as cgm
            ,tmp_lancamento as tmp
        where
            tmp.exercicio = '''||stExercicio||'''
            and tmp.cod_entidade = '||inCodEntidade||' 
            ';

            if inCodDotacao is not null and inCodDotacao <> '' then
                stSubSql := stSubSql || ' and eped.cod_despesa = '||inCodDotacao||' ';
            end if;
            stSubSql := stSubSql || ' and eped.cod_conta = '||inCodConta||' ';
    
        stSubSql := stSubSql || '
            and ee.cod_pre_empenho = epe.cod_pre_empenho
            and ee.exercicio = epe.exercicio

            and epe.cod_pre_empenho = eped.cod_pre_empenho
            and epe.exercicio = eped.exercicio

            and epe.cgm_beneficiario = cgm.numcgm

            and clq.exercicio = enl.exercicio
            and clq.cod_entidade = enl.cod_entidade
            and clq.cod_nota = enl.cod_nota

            and enl.exercicio_empenho = ee.exercicio
            and enl.cod_empenho = ee.cod_empenho
            and enl.cod_entidade = ee.cod_entidade

            and tmp.exercicio = clq.exercicio
            and tmp.tipo = clq.tipo
            and tmp.cod_lote = clq.cod_lote
            and tmp.cod_entidade = clq.cod_entidade 
        UNION 
        select
             tmp.dt_lote
            ,tmp.boo
            ,tmp.complemento
            ,tmp.estorno
            ,tmp.cod_plano
            ,tmp.sequencia
            ,tmp.cod_estrutural
            ,tmp.tipo_valor
            ,tmp.nom_historico
            ,tmp.vl_lancamento
            ,cp.exercicio
            ,cp.tipo
            ,cp.cod_lote
            ,cp.cod_entidade
            ,cgm.nom_cgm
            ,cgm.numcgm
            ,eped.cod_conta
            ,eped.cod_despesa
        from
             contabilidade.pagamento as cp
            ,empenho.nota_liquidacao as enl
            ,empenho.nota_liquidacao_paga as enlp
            ,empenho.empenho as ee
            ,empenho.pre_empenho as epe
            ,empenho.pre_empenho_despesa as eped
            ,sw_cgm as cgm
            ,tmp_lancamento as tmp
        where
            tmp.cod_entidade = '||inCodEntidade||'
            and tmp.exercicio = '''||stExercicio||'''
            ';

            if inCodDotacao is not null and inCodDotacao <> '' then
                stSubSql := stSubSql || ' and eped.cod_despesa = '||inCodDotacao||' ';
            end if;
            stSubSql := stSubSql || ' and eped.cod_conta = '||inCodConta||' ';
        
        stSubSql := stSubSql || '
            and ee.cod_pre_empenho = epe.cod_pre_empenho
            and ee.exercicio = epe.exercicio

            and epe.cod_pre_empenho = eped.cod_pre_empenho
            and epe.exercicio = eped.exercicio

            and epe.cgm_beneficiario = cgm.numcgm

            and cp.exercicio = enlp.exercicio
            and cp.cod_entidade = enlp.cod_entidade
            and cp.cod_nota = enlp.cod_nota
            and cp.timestamp = enlp.timestamp

            and enlp.cod_nota = enl.cod_nota
            and enlp.exercicio = enl.exercicio
            and enlp.cod_entidade = enl.cod_entidade

            and enl.exercicio_empenho = ee.exercicio
            and enl.cod_empenho = ee.cod_empenho
            and enl.cod_entidade = ee.cod_entidade

            and tmp.exercicio = cp.exercicio
            and tmp.sequencia = cp.sequencia
            and tmp.tipo = cp.tipo
            and tmp.cod_lote = cp.cod_lote 
            and tmp.cod_entidade = cp.cod_entidade 
        ';

EXECUTE stSubSql;
-----------------------------------------------------SUPLEMENTACOES---REDUCAO-----------------------------------------------------------------------
--        UNION

        stSubSql2 := '
        select distinct
            os.exercicio 
            ,ctd.tipo
            ,clt.cod_lote
            ,clt.cod_entidade
        
            ,ctd.exercicio
            ,ctd.cod_tipo  
        
            ,os.cod_suplementacao
            ,os.dt_suplementacao
            ,osr.cod_despesa
            ,osr.valor

            ,chc.nom_historico
            ,cl.complemento
            ,os.cod_suplementacao
        from
             orcamento.suplementacao os
            ,orcamento.suplementacao_reducao osr
            ,contabilidade.lancamento_transferencia clt
            ,contabilidade.lancamento cl
            ,contabilidade.historico_contabil chc
            ,contabilidade.transferencia_despesa ctd
            ,orcamento.despesa od
             JOIN orcamento.recurso('''|| stExercicio ||''') as rec
                ON (    rec.cod_recurso = od.cod_recurso
                    AND rec.exercicio   = od.exercicio   )

        where
                clt.exercicio = '''||stExercicio||'''
            and clt.cod_entidade = '||inCodEntidade||'
            and clt.tipo in (''S'')
            and os.cod_tipo <> 16
            and clt.cod_entidade = ctd.cod_entidade
            and clt.cod_tipo = ctd.cod_tipo
            and clt.exercicio = ctd.exercicio
            and clt.sequencia = ctd.sequencia
            and clt.tipo = ctd.tipo
            and clt.cod_lote = ctd.cod_lote

            and clt.sequencia = cl.sequencia
            and clt.cod_lote = cl.cod_lote
            and clt.tipo = cl.tipo
            and clt.exercicio = cl.exercicio
            and clt.cod_entidade = cl.cod_entidade

            and cl.cod_historico = chc.cod_historico
            and cl.exercicio = chc.exercicio
        
            and ctd.cod_suplementacao = os.cod_suplementacao
            and ctd.exercicio = os.exercicio
        
            and os.cod_suplementacao = osr.cod_suplementacao
            and os.exercicio = osr.exercicio

            and osr.cod_despesa = od.cod_despesa
            and osr.exercicio = od.exercicio
        
            AND NOT EXISTS ( SELECT 1
                               FROM orcamento.suplementacao_anulada osa
                              WHERE cod_suplementacao = osr.cod_suplementacao
                                AND osa.exercicio = '''||stExercicio||'''
            )
        ';
              if inCodDotacao is not null and inCodDotacao <> '' then
                  stSubSql2 := stSubSql2 || ' and osr.cod_despesa = '||inCodDotacao||' ';
              end if;

              if (stDestinacaoRecurso is not null and stDestinacaoRecurso <> '') then
                  stSql := stSql || ' AND rec.masc_recurso_red like '''||stDestinacaoRecurso||'%'' ';
              end if;

              if (inCodDetalhamento is not null and inCodDetalhamento <> '') then
                  stSql := stSql || ' AND rec.cod_detalhamento = '|| inCodDetalhamento ||' ';
              end if;

         stSubSql2 := stSubSql2 || ' and od.cod_conta = '||inCodConta||' ';
-------------------------------------------------------SUPLEMENTACOES---SUPLEMENTADA-----------------------------------------------------------------
        stSubSql2 := stSubSql2 || '
        UNION
        select distinct
            os.exercicio 
            ,ctd.tipo
            ,clt.cod_lote
            ,clt.cod_entidade
        
            ,ctd.exercicio
            ,ctd.cod_tipo  
        
            ,os.cod_suplementacao
            ,os.dt_suplementacao
            ,oss.cod_despesa
            ,oss.valor

            ,chc.nom_historico
            ,cl.complemento    
            ,os.cod_suplementacao
        from
             orcamento.suplementacao os
            ,orcamento.suplementacao_suplementada oss
            ,contabilidade.lancamento_transferencia clt
            ,contabilidade.lancamento cl
            ,contabilidade.historico_contabil chc
            ,contabilidade.transferencia_despesa ctd
            ,orcamento.despesa od
             JOIN orcamento.recurso('''|| stExercicio ||''') as rec
                ON (    rec.cod_recurso = od.cod_recurso
                    AND rec.exercicio   = od.exercicio   )
        where
                clt.exercicio = '''||stExercicio||'''
            and clt.cod_entidade = '||inCodEntidade||'
            and clt.tipo in (''S'')
            and clt.cod_entidade = ctd.cod_entidade
            and clt.cod_tipo = ctd.cod_tipo
            and clt.exercicio = ctd.exercicio
            and clt.sequencia = ctd.sequencia
            and clt.tipo = ctd.tipo
            and clt.cod_lote = ctd.cod_lote

            and clt.sequencia = cl.sequencia
            and clt.cod_lote = cl.cod_lote
            and clt.tipo = cl.tipo
            and clt.exercicio = cl.exercicio
            and clt.cod_entidade = cl.cod_entidade

            and cl.cod_historico = chc.cod_historico
            and cl.exercicio = chc.exercicio

            and ctd.cod_suplementacao = os.cod_suplementacao
            and ctd.exercicio = os.exercicio

            and os.cod_suplementacao = oss.cod_suplementacao
            and os.exercicio = oss.exercicio

            and oss.cod_despesa = od.cod_despesa
            and oss.exercicio = od.exercicio
        
            AND NOT EXISTS ( SELECT 1
                               FROM orcamento.suplementacao_anulada osa
                              WHERE cod_suplementacao = oss.cod_suplementacao
                                AND osa.exercicio = '''||stExercicio||'''
            )
    ';

            if inCodDotacao is not null and inCodDotacao <> '' then
                stSubSql2 := stSubSql2 || ' and oss.cod_despesa = '||inCodDotacao||' ';
            end if;

            if (stDestinacaoRecurso is not null and stDestinacaoRecurso <> '') then
                stSql := stSql || ' AND rec.masc_recurso_red like '''||stDestinacaoRecurso||'%'' ';
            end if;

            if (inCodDetalhamento is not null and inCodDetalhamento <> '') then
                stSql := stSql || ' AND rec.cod_detalhamento = '|| inCodDetalhamento ||' ';
            end if;

            stSubSql2 := stSubSql2 || ' and od.cod_conta = '||inCodConta||' '; 
-----------------------------------------------------------FIM SUPLEMENTACOES---------------------------------------------------------------------
--        '';
    

    FOR reRegistro IN EXECUTE stSubSql2
    LOOP 
--        RETURN NEXT reRegistro;
          insert into tmp_lancamento_sub values (reRegistro.dt_suplementacao,null,reRegistro.complemento,null,null,null,null,null,reRegistro.nom_historico,reRegistro.valor,reRegistro.exercicio,reRegistro.tipo,reRegistro.cod_lote,reRegistro.cod_entidade,reRegistro.exercicio,reRegistro.cod_tipo,reRegistro.cod_suplementacao,reRegistro.cod_despesa);

    END LOOP;

    stSubSql := ' select * from tmp_lancamento_sub ';

    FOR reRegistro IN EXECUTE stSubSql
    LOOP
        RETURN NEXT reRegistro;
    END LOOP;   
 
    DROP INDEX idx_tmp_lancamento;
    DROP TABLE tmp_lancamento;
    DROP TABLe tmp_lancamento_sub;

    RETURN;

END;
$$ language 'plpgsql';
