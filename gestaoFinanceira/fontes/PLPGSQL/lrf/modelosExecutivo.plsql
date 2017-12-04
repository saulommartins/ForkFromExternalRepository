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
* $Revision: 13282 $
* $Name$
* $Author: jose.eduardo $
* $Date: 2006-07-27 17:22:31 -0300 (Qui, 27 Jul 2006) $
*
* Casos de uso  uc-02.05.03,uc-02.05.04,uc-02.05.05,uc-02.05.06,uc-02.05.07,uc-02.05.08,uc-02.05.10,uc-02.05.12 
*/

/*
$Log$
Revision 1.8  2006/07/27 20:22:19  jose.eduardo
Bug #6642#

Revision 1.7  2006/07/05 20:37:50  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION tcers.fn_rel_modelos_executivo(varchar,varchar,varchar,varchar,varchar,varchar,varchar) RETURNS SETOF RECORD AS '
DECLARE
    stExercicio                 ALIAS FOR $1;
    stDtInicial                 ALIAS FOR $2;
    stDtFinal                   ALIAS FOR $3;
    stCodEntidades              ALIAS FOR $4;
    stCodModelo                 ALIAS FOR $5;
    stTipoValorDespesa          ALIAS FOR $6;
    stFiltro                    ALIAS FOR $7;

    stSql               VARCHAR   := '''';
    reRegistro          RECORD;
    arOrdem             INTEGER[] := array[0];
    arValores           NUMERIC[] := array[0];
    inCount             INTEGER   := 0;
    inCountAnt          INTEGER   := 0;


BEGIN

IF (substr(stDtFinal,7,4) > 2004) THEN
/*    stSql := ''
        CREATE TEMPORARY TABLE tmp_despesa AS (
            SELECT
                d.cod_despesa,
                e.exercicio,
                e.cod_entidade,
                e.cod_empenho
            FROM
                orcamento.despesa           as d,
                empenho.pre_empenho_despesa as ped,
                empenho.pre_empenho         as pe,
                empenho.empenho             as e
            WHERE
                d.num_orgao <> 1    AND
                d.cod_entidade      IN ('' || stCodEntidades || '') AND
                d.exercicio         = '' || stExercicio || '' AND

                d.exercicio         = ped.exercicio     AND
                d.cod_despesa       = ped.cod_despesa   AND

                ped.exercicio       = pe.exercicio      AND
                ped.cod_pre_empenho = pe.cod_pre_empenho AND

                pe.exercicio       = e.exercicio      AND
                pe.cod_pre_empenho = e.cod_pre_empenho
    )'';
    EXECUTE stSql;*/

/*    stSql := ''
        CREATE TEMPORARY TABLE tmp_conta_despesa AS (
            SELECT
                le.sequencia,
                le.cod_lote,
                le.tipo,
                le.exercicio,
                le.cod_entidade
            FROM
                contabilidade.lancamento_empenho as le
                    LEFT OUTER JOIN (
                        SELECT
                            e.exercicio,
                            e.cod_lote,
                            e.tipo,
                            e.sequencia,
                            e.cod_entidade
                        FROM
                            contabilidade.empenhamento as e,
                            tmp_despesa as d
                        WHERE
                            e.cod_empenho   = d.cod_empenho     AND
                            e.cod_entidade  = d.cod_entidade    AND
                            e.exercicio     = d.exercicio
                    ) as ce on (
                        le.exercicio     = ce.exercicio      AND
                        le.cod_lote      = ce.cod_lote       AND
                        le.tipo          = ce.tipo           AND
                        le.sequencia     = ce.sequencia      AND
                        le.cod_entidade  = ce.cod_entidade
                    )
                   LEFT OUTER JOIN (
                        SELECT
                            l.exercicio,
                            l.cod_lote,
                            l.tipo,
                            l.sequencia,
                            l.cod_entidade
                        FROM
                            contabilidade.liquidacao    as l,
                            tmp_despesa as d
                        WHERE
                            l.cod_empenho   = d.cod_empenho     AND
                            l.cod_entidade  = d.cod_entidade    AND
                            l.exercicio     = d.exercicio
                    ) as cl on (
                        le.exercicio     = cl.exercicio      AND
                        le.cod_lote      = cl.cod_lote       AND
                        le.tipo          = cl.tipo           AND
                        le.sequencia     = cl.sequencia      AND
                        le.cod_entidade  = cl.cod_entidade
                    )
                    LEFT OUTER JOIN (
                        SELECT
                            p.exercicio,
                            p.cod_lote,
                            p.tipo,
                            p.sequencia,
                            p.cod_entidade
                        FROM
                            contabilidade.pagamento         as p,
                            empenho.nota_liquidacao_paga    as nlp,
                            empenho.nota_liquidacao         as nl,
                            tmp_despesa                     as d
                        WHERE
                            p.exercicio_liquidacao  = nlp.exercicio     AND
                            p.cod_entidade          = nlp.cod_entidade  AND
                            p.cod_nota              = nlp.cod_nota      AND
                            p.timestamp             = nlp.timestamp     AND

                            nlp.exercicio           = nl.exercicio      AND
                            nlp.cod_entidade        = nl.cod_entidade   AND
                            nlp.cod_nota            = nl.cod_nota       AND

                            nl.cod_empenho          = d.cod_empenho     AND
                            nl.cod_entidade         = d.cod_entidade    AND
                            nl.exercicio_empenho    = d.exercicio
                    ) as cp on (
                        le.exercicio     = cp.exercicio      AND
                        le.cod_lote      = cp.cod_lote       AND
                        le.tipo          = cp.tipo           AND
                        le.sequencia     = cp.sequencia      AND
                        le.cod_entidade  = cp.cod_entidade
                    )
            WHERE
                le.cod_entidade      IN ('' || stCodEntidades || '') AND
                le.exercicio         = '' || stExercicio || '' '';

                if (stTipoValorDespesa is not null and stTipoValorDespesa<>'''') then
                    stSql := stSql || ''AND le.tipo             = '''''' || stTipoValorDespesa || '''''' '';
                end if;

        stSql := stSql || ''
        )'';
    EXECUTE stSql;*/

    stSql := ''
        CREATE TEMPORARY TABLE tmp_empenhado AS (
        SELECT
            e.cod_empenho,
            e.exercicio,
            e.cod_entidade,
            e.dt_empenho,
            pe.cod_pre_empenho,
            cd.cod_estrutural
        FROM
            orcamento.conta_despesa     as cd,
            empenho.pre_empenho_despesa as ped,
            empenho.pre_empenho         as pe,
            empenho.empenho             as e
        WHERE
            cd.cod_conta        = ped.cod_conta         AND
            cd.exercicio        = ped.exercicio         AND

            ped.cod_pre_empenho = pe.cod_pre_empenho    AND
            ped.exercicio       = pe.exercicio          AND

            pe.cod_pre_empenho  = e.cod_pre_empenho     AND
            pe.exercicio        = e.exercicio           AND

            e.cod_entidade      IN ('' || stCodEntidades || '') '';

    IF ( (stCodModelo <> 1) AND (stCodModelo <> 2) AND (stCodModelo <> 9) ) THEN
        stSql := stSql || '' AND e.exercicio         = '' || quote_literal(stExercicio);
    END IF;
    
        stSql := stSql || ''
        GROUP BY
            e.cod_empenho,
            e.exercicio,
            e.cod_entidade,
            e.dt_empenho,
            pe.cod_pre_empenho,
            cd.cod_estrutural
        )'';

    EXECUTE stSql;

    stSql := ''
        CREATE TEMPORARY TABLE tmp_credito AS
            SELECT
                pc.cod_estrutural   as cod_estrutural,
                l.tipo              as tipo,
                l.exercicio         as exercicio,
                l.sequencia         as sequencia,
                l.cod_entidade      as cod_entidade,
                l.cod_lote          as cod_lote,
                vl.vl_lancamento     as valor
            FROM
                contabilidade.lancamento l,
                contabilidade.lote lo,
                contabilidade.valor_lancamento vl,
                contabilidade.conta_credito cc,
                contabilidade.plano_analitica pa,
                contabilidade.plano_conta pc
            WHERE
                    l.cod_entidade      IN ('' || stCodEntidades || '') '';

    IF ( (stCodModelo <> 1) AND (stCodModelo <> 2) AND (stCodModelo <> 9) ) THEN
        stSql := stSql || '' AND l.exercicio         = '' || quote_literal(stExercicio);
    END IF;

        stSql := stSql || ''

                AND lo.dt_lote BETWEEN to_date(''''''|| stDtInicial ||'''''',''''dd/mm/yyyy'''') AND to_date(''''''|| stDtFinal ||'''''',''''dd/mm/yyyy'''')

                AND l.exercicio    = vl.exercicio
                AND l.cod_lote     = vl.cod_lote
                AND l.tipo         = vl.tipo
                AND l.sequencia    = vl.sequencia
                AND l.cod_entidade = vl.cod_entidade

                AND l.exercicio    = lo.exercicio
                AND l.cod_lote     = lo.cod_lote
                AND l.tipo         = lo.tipo
                AND l.cod_entidade = lo.cod_entidade

                AND vl.exercicio    = cc.exercicio
                AND vl.cod_lote     = cc.cod_lote
                AND vl.tipo         = cc.tipo
                AND vl.tipo_valor   = cc.tipo_valor
                AND vl.sequencia    = cc.sequencia
                AND vl.cod_entidade = cc.cod_entidade

                AND cc.cod_plano = pa.cod_plano
                AND cc.exercicio = pa.exercicio

                AND pa.cod_conta = pc.cod_conta
                AND pa.exercicio = pc.exercicio '';
/*
                if (stTipoValorDespesa is not null and stTipoValorDespesa<>'''') then
                    stSql := stSql || ''AND l.tipo             = '''''' || stTipoValorDespesa || '''''' '';
                end if;*/
    EXECUTE stSql;


    stSql := ''
        CREATE TEMPORARY TABLE tmp_debito AS
            SELECT
                pc.cod_estrutural   as cod_estrutural,
                l.tipo              as tipo,
                l.exercicio         as exercicio,
                l.sequencia         as sequencia,
                l.cod_entidade      as cod_entidade,
                l.cod_lote          as cod_lote,
                vl.vl_lancamento     as valor
            FROM
                contabilidade.lancamento l,
                contabilidade.lote lo,
                contabilidade.valor_lancamento vl,
                contabilidade.conta_debito cd,
                contabilidade.plano_analitica pa,
                contabilidade.plano_conta pc
            WHERE
                    l.cod_entidade      IN ('' || stCodEntidades || '') '';

    IF ( (stCodModelo <> 1) AND (stCodModelo <> 2) AND (stCodModelo <> 9) ) THEN
        stSql := stSql || '' AND l.exercicio         = '' || quote_literal(stExercicio);
    END IF;

        stSql := stSql || ''

                AND lo.dt_lote BETWEEN to_date(''''''|| stDtInicial ||'''''',''''dd/mm/yyyy'''') AND to_date(''''''|| stDtFinal ||'''''',''''dd/mm/yyyy'''')

                AND l.exercicio    = vl.exercicio
                AND l.cod_lote     = vl.cod_lote
                AND l.tipo         = vl.tipo
                AND l.sequencia    = vl.sequencia
                AND l.cod_entidade = vl.cod_entidade

                AND l.exercicio    = lo.exercicio
                AND l.cod_lote     = lo.cod_lote
                AND l.tipo         = lo.tipo
                AND l.cod_entidade = lo.cod_entidade

                AND vl.exercicio    = cd.exercicio
                AND vl.cod_lote     = cd.cod_lote
                AND vl.tipo         = cd.tipo
                AND vl.tipo_valor   = cd.tipo_valor
                AND vl.sequencia    = cd.sequencia
                AND vl.cod_entidade = cd.cod_entidade

                AND cd.cod_plano = pa.cod_plano
                AND cd.exercicio = pa.exercicio

                AND pa.cod_conta = pc.cod_conta
                AND pa.exercicio = pc.exercicio '';
/*
                if (stTipoValorDespesa is not null and stTipoValorDespesa<>'''') then
                    stSql := stSql || ''AND l.tipo             = '''''' || stTipoValorDespesa || '''''' '';
                end if;*/
    EXECUTE stSql;
END IF;

stSql := ''
    CREATE TEMPORARY TABLE tmp AS
    SELECT
        m.cod_modelo,
        m.nom_modelo, '';

    IF ( (stCodModelo <> 1) AND (stCodModelo <> 2) AND (stCodModelo <> 9) ) THEN
        stSql := stSql || '' m.exercicio, '';
    END IF;

        stSql := stSql || ''

        qm.cod_quadro,
        qm.nom_quadro,
        pcm.redutora,
        pcm.ordem,
        pc.nom_conta,
        pc.cod_estrutural as cod_estrutural_completo,
        CASE WHEN substr(pc.cod_estrutural,1,1) = 4 THEN
            substr(pc.cod_estrutural,3)
        ELSE
            CASE WHEN substr(pc.cod_estrutural,1,1) = 3 THEN
                substr(pc.cod_estrutural,3)
            ELSE
                pc.cod_estrutural
            END
        END as cod_estrutural, '';

        IF ((substr(stDtFinal,7,4) > 2004) AND (substr(stDtInicial,7,4) < 2005)) THEN
            stSql := stSql || ''
                CASE WHEN substr(pc.cod_estrutural,1,1) = 4 THEN
                    coalesce(tcers.fn_somatorio_contabil(publico.fn_mascarareduzida(pc.cod_estrutural),''''2'''','''''''','''''' || stDtInicial || '''''', '''''' || stDtFinal || ''''''),0.00)  - coalesce(tcers.fn_somatorio_contabil_siam(substr(publico.fn_mascarareduzida(pc.cod_estrutural),3),''''1'''',''''1'''','''''' || stTipoValorDespesa || '''''',cast(substr('''''' || stDtInicial || '''''',4,2) as integer)),0.00)
                ELSE
                    CASE WHEN substr(pc.cod_estrutural,1,1) = 3 THEN
                        coalesce(tcers.fn_somatorio_contabil(publico.fn_mascarareduzida(pc.cod_estrutural),''''3'''','''''' || stTipoValorDespesa || '''''','''''' || stDtInicial || '''''', '''''' || stDtFinal || ''''''),0.00) + coalesce(tcers.fn_somatorio_contabil_siam(substr(publico.fn_mascarareduzida(pc.cod_estrutural),3),''''2'''',''''1'''','''''' || stTipoValorDespesa || '''''',cast(substr('''''' || stDtInicial || '''''',4,2) as integer)),0.00)
                    ELSE
                        coalesce(tcers.fn_somatorio_contabil(publico.fn_mascarareduzida(pc.cod_estrutural),''''1'''','''''''','''''' || stDtInicial || '''''', '''''' || stDtFinal || ''''''),0.00)
                    END
                END as valor_contabil,'';
        ELSE
            IF (substr(stDtFinal,7,4) > 2004) THEN
                stSql := stSql || ''
                    CASE WHEN substr(pc.cod_estrutural,1,1) = 4 THEN
                        coalesce(tcers.fn_somatorio_contabil(publico.fn_mascarareduzida(pc.cod_estrutural),''''2'''','''''''','''''' || stDtInicial || '''''', '''''' || stDtFinal || ''''''),0.00)
                    ELSE
                        CASE WHEN substr(pc.cod_estrutural,1,1) = 3 THEN
                            coalesce(tcers.fn_somatorio_contabil(publico.fn_mascarareduzida(pc.cod_estrutural),''''3'''','''''' || stTipoValorDespesa || '''''','''''' || stDtInicial || '''''', '''''' || stDtFinal || ''''''),0.00)
                        ELSE
                            coalesce(tcers.fn_somatorio_contabil(publico.fn_mascarareduzida(pc.cod_estrutural),''''1'''','''''''','''''' || stDtInicial || '''''', '''''' || stDtFinal || ''''''),0.00)
                        END
                    END as valor_contabil,'';
            ELSE
                stSql := stSql || ''
                    CASE WHEN substr(pc.cod_estrutural,1,1) = 4 THEN
                        coalesce(tcers.fn_somatorio_contabil_siam(substr(publico.fn_mascarareduzida(pc.cod_estrutural),3),''''1'''',''''1'''','''''' || stTipoValorDespesa || '''''',cast(substr('''''' || stDtInicial || '''''',4,2) as integer)),0.00)
                    ELSE
                        CASE WHEN substr(pc.cod_estrutural,1,1) = 3 THEN
                            coalesce(tcers.fn_somatorio_contabil_siam(substr(publico.fn_mascarareduzida(pc.cod_estrutural),3),''''2'''',''''1'''','''''' || stTipoValorDespesa || '''''',cast(substr('''''' || stDtInicial || '''''',4,2) as integer)),0.00)
                        END
                    END as valor_contabil,'';
            END IF;
        END IF;
        stSql := stSql || ''
        sum(apcm.vl_ajuste) as valor_ajuste,
        0.00 as valor_ajustado
    FROM
        tcers.modelo_lrf                    as m,
        tcers.quadro_modelo_lrf             as qm,
        tcers.plano_conta_modelo_lrf        as pcm
            LEFT OUTER JOIN tcers.ajuste_plano_conta_modelo_lrf as apcm ON (
                pcm.exercicio    = apcm.exercicio     AND
                pcm.cod_modelo   = apcm.cod_modelo    AND
                pcm.cod_quadro   = apcm.cod_quadro    AND
                pcm.cod_conta    = apcm.cod_conta     AND
                ((apcm.mes <= ltrim(substr('''''' || stDtFinal || '''''',4,2),0) AND apcm.exercicio = substr('''''' || stDtFinal || '''''',7,4)) OR (apcm.mes >= ltrim(substr('''''' || stDtInicial || '''''',4,2),0) AND apcm.exercicio = substr('''''' || stDtInicial || '''''',7,2)))
            ),
        contabilidade.plano_conta           as pc
    WHERE
        m.exercicio         = '' || stExercicio || '' AND
        m.cod_modelo        = '' || stCodModelo || '' AND

        m.exercicio     = qm.exercicio      AND
        m.cod_modelo    = qm.cod_modelo     AND

        qm.exercicio    = pcm.exercicio     AND
        qm.cod_modelo   = pcm.cod_modelo    AND
        qm.cod_quadro   = pcm.cod_quadro    AND

        pcm.exercicio   = pc.exercicio      AND
        pcm.cod_conta   = pc.cod_conta
    GROUP BY
        m.cod_modelo,
        m.nom_modelo, '';

    IF ( (stCodModelo <> 1) AND (stCodModelo <> 2) AND (stCodModelo <> 9) ) THEN
        stSql := stSql || ''m.exercicio, '';
    END IF;

    stSql := stSql || ''
        qm.cod_quadro,
        qm.nom_quadro,
        pcm.redutora,
        pc.nom_conta,
        pc.cod_estrutural,
        cod_estrutural,
        valor_contabil,
        pcm.ordem
    ORDER BY
        pcm.ordem'';


    EXECUTE stSql;

    /* Contas não existentes, cadastradas automaticamente */
    INSERT INTO tmp (ordem)
        SELECT 31 WHERE ''3.3.1.4.0.13.40.01.00.00'' NOT IN ( SELECT cod_estrutural_completo FROM tmp ) AND stCodModelo = 2
    ;
    INSERT INTO tmp (ordem)
        SELECT 20 WHERE ''4.1.9.9.0.99.05.00.00.00'' NOT IN ( SELECT cod_estrutural_completo FROM tmp ) AND stCodModelo = 1
    ;
    INSERT INTO tmp (ordem)
        SELECT 36 WHERE ''5.1.2.1.7.01.31.01.00.00'' NOT IN ( SELECT cod_estrutural_completo FROM tmp ) AND stCodModelo = 2
    ;

    stSql := ''SELECT * FROM tmp ORDER BY ordem'';

/*    FOR reRegistro IN EXECUTE stSql
    LOOP
        inCount := inCount + 1;
        IF(reRegistro.redutora = true) THEN
            IF(inCountAnt <= 0) THEN
                inCountAnt := inCount - 1;
            END IF;
                arValores[inCountAnt] := coalesce(arValores[inCountAnt],0.00) + coalesce(reRegistro.valor_contabil,0.00);
                arValores[inCount] := 0;
        ELSE
            arValores[inCount] := 0;
            inCountAnt := 0;
        END IF;

    END LOOP;

    inCount = 0;

    stSql := ''SELECT * FROM tmp ORDER BY ordem'';*/
    FOR reRegistro IN EXECUTE stSql
    LOOP
/*        inCount := inCount + 1;
        IF( arValores[inCount] <> 0 )THEN
            reRegistro.valor_contabil := ABS(coalesce(reRegistro.valor_contabil,0.00) - arValores[inCount]);
        ELSE*/
            reRegistro.valor_contabil := ABS(coalesce(reRegistro.valor_contabil,0.00));
--        END IF;

        reRegistro.valor_ajuste   := coalesce(reRegistro.valor_ajuste,0.00);
        reRegistro.valor_ajustado := reRegistro.valor_contabil + reRegistro.valor_ajuste;

        IF reRegistro.cod_estrutural_completo IS NULL THEN
            reRegistro.valor_contabil := null;
            reRegistro.valor_ajuste   := null;
            reRegistro.valor_ajustado := null;
        END IF;

        RETURN next reRegistro;
    END LOOP;


    IF (stCodModelo = 2) THEN
        reRegistro.redutora                := false;
        reRegistro.nom_conta               := ''EMPENHOS DO EXERCÍCIO'';
        reRegistro.cod_estrutural_completo := '''';
        reRegistro.cod_estrutural          := '''';

        IF (stTipoValorDespesa = ''L'' OR stTipoValorDespesa = ''P'') THEN
            reRegistro.valor_contabil      := '' '' || coalesce(tcers.fn_somatorio_empenho_modelos_lrf(''1'',''1'',''3.1'','''' || stCodEntidades || '''', '''' || stExercicio || '''','''' || stDtInicial || '''','''' || stDtFinal || ''''),0.00) || '' '';
        ELSE
            reRegistro.valor_contabil      := ''0.00'';
        END IF;

        reRegistro.valor_ajuste            := null;
        reRegistro.valor_ajustado          := null;

        RETURN next reRegistro;

        reRegistro.redutora                := false;
        reRegistro.nom_conta               := ''RESTOS A PAGAR NÃO PROCESSADOS - EXECUTIVO / INDIRETAS'';
        reRegistro.cod_estrutural_completo := '''';
        reRegistro.cod_estrutural          := '''';
        reRegistro.valor_contabil          := '' '' || coalesce(tcers.fn_somatorio_empenho_modelos_lrf(''2'',''1'',''3.1'','''' || stCodEntidades || '''', '''' || stExercicio || '''','''' || stDtInicial || '''','''' || stDtFinal || ''''),0.00) || '' '';
        reRegistro.valor_ajuste            := null;
        reRegistro.valor_ajustado          := null;

        RETURN next reRegistro;
    END IF;

    IF (substr(stDtFinal,7,4) > 2004) THEN
--        DROP TABLE tmp_despesa;
--        DROP TABLE tmp_conta_despesa;
      DROP TABLE tmp_debito;
      DROP TABLE tmp_credito;
      DROP TABLE tmp_empenhado;
    END IF;
      DROP TABLE tmp;

    RETURN;

END;
'language 'plpgsql';
