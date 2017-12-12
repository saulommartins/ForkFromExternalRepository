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
* $Revision: 27033 $
* $Name$
* $Author: cako $
* $Date: 2007-12-04 12:08:26 -0200 (Ter, 04 Dez 2007) $
*
* Casos de uso: uc-02.03.26
*/

/*
$Log$
Revision 1.7  2006/07/05 20:37:38  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION empenho.fn_relatorio_programacao_pagamentos_dispon_financ(VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    stDtInicial         ALIAS FOR $2;
    stDtFinal           ALIAS FOR $3;
    inCodRecurso        ALIAS FOR $4;
    stEntidade          ALIAS FOR $5;
    stSql               VARCHAR   := '';
    stSqlComplemento    VARCHAR   := '';
    stMascRecurso       VARCHAR   := '';
    reRegistro          RECORD;
    arRetorno           NUMERIC[];

BEGIN

    stSql := 'CREATE TEMPORARY TABLE tmp_debito AS
                SELECT *
                FROM (
                    SELECT
                         pc.cod_estrutural
                        ,pa.cod_plano
                        ,vl.tipo_valor
                        ,vl.vl_lancamento
                        ,vl.cod_entidade
                        ,lo.cod_lote
                        ,lo.dt_lote
                        ,lo.exercicio
                        ,lo.tipo
                        ,vl.sequencia
                        ,vl.oid as oid_temp
                    FROM
                         contabilidade.plano_banco       as pb
                        ,contabilidade.plano_conta       as pc
                        ,contabilidade.plano_analitica   as pa
                        ,contabilidade.conta_debito      as cd
                        ,contabilidade.valor_lancamento  as vl
                        ,contabilidade.lancamento        as la
                        ,contabilidade.lote              as lo
                        ,contabilidade.plano_recurso     as re
                    WHERE   pb.cod_plano    = pa.cod_plano
                    AND     pb.exercicio    = pa.exercicio
                    AND     pc.cod_conta    = pa.cod_conta
                    AND     pc.exercicio    = pa.exercicio
                    AND    (pc.cod_estrutural like ''1.1.1.1.1%'' OR pc.cod_estrutural like ''1.1.1.1.2%'')
                    AND     pa.cod_plano    = cd.cod_plano
                    AND     pa.exercicio    = cd.exercicio
                    AND     pa.cod_plano    = re.cod_plano
                    AND     pa.exercicio    = re.exercicio
                    AND     cd.cod_lote     = vl.cod_lote
                    AND     cd.tipo         = vl.tipo
                    AND     cd.sequencia    = vl.sequencia
                    AND     cd.exercicio    = vl.exercicio
                    AND     cd.tipo_valor   = vl.tipo_valor
                    AND     cd.cod_entidade = vl.cod_entidade
                    AND     vl.cod_lote     = la.cod_lote
                    AND     vl.tipo         = la.tipo
                    AND     vl.sequencia    = la.sequencia
                    AND     vl.exercicio    = la.exercicio
                    AND     vl.cod_entidade = la.cod_entidade
                    AND     vl.tipo_valor   = ''D''
                    AND     la.cod_lote     = lo.cod_lote
                    AND     la.exercicio    = lo.exercicio
                    AND     la.tipo         = lo.tipo
                    AND     la.cod_entidade = lo.cod_entidade
                    AND     pb.cod_entidade IN ('|| stEntidade ||')
                    AND     pa.exercicio    = ''|| quote_literal(stExercicio) ||'' ';
                    --IF (inCodRecurso is not null and inCodRecurso<>'') THEN
                    --    stSql := stSql ||' AND re.cod_recurso = '|| inCodRecurso ||' ';
                    --END IF;

                    stSql := stSql || ' ORDER BY pc.cod_estrutural
                  ) as tabela';
    EXECUTE stSql;

    stSql := 'CREATE TEMPORARY TABLE tmp_credito AS
                SELECT *
                FROM (
                    SELECT
                         pc.cod_estrutural
                        ,pa.cod_plano
                        ,vl.tipo_valor
                        ,vl.vl_lancamento
                        ,vl.cod_entidade
                        ,lo.cod_lote
                        ,lo.dt_lote
                        ,lo.exercicio
                        ,lo.tipo
                        ,vl.sequencia
                        ,vl.oid as oid_temp
                    FROM
                         contabilidade.plano_banco       as pb
                        ,contabilidade.plano_conta       as pc
                        ,contabilidade.plano_analitica   as pa
                        ,contabilidade.conta_credito     as cc
                        ,contabilidade.valor_lancamento  as vl
                        ,contabilidade.lancamento        as la
                        ,contabilidade.lote              as lo
                        ,contabilidade.plano_recurso     as re
                    WHERE   pb.cod_plano    = pa.cod_plano
                    AND     pb.exercicio    = pa.exercicio
                    AND     pc.cod_conta    = pa.cod_conta
                    AND     pc.exercicio    = pa.exercicio
                    AND    (pc.cod_estrutural like ''1.1.1.1.1%'' OR pc.cod_estrutural like ''1.1.1.1.2%'')
                    AND     pa.cod_plano    = cc.cod_plano
                    AND     pa.exercicio    = cc.exercicio
                    AND     pa.cod_plano    = re.cod_plano
                    AND     pa.exercicio    = re.exercicio
                    AND     cc.cod_lote     = vl.cod_lote
                    AND     cc.tipo         = vl.tipo
                    AND     cc.sequencia    = vl.sequencia
                    AND     cc.exercicio    = vl.exercicio
                    AND     cc.tipo_valor   = vl.tipo_valor
                    AND     cc.cod_entidade = vl.cod_entidade
                    AND     vl.cod_lote     = la.cod_lote
                    AND     vl.tipo         = la.tipo
                    AND     vl.sequencia    = la.sequencia
                    AND     vl.exercicio    = la.exercicio
                    AND     vl.cod_entidade = la.cod_entidade
                    AND     vl.tipo_valor   = ''C''
                    AND     la.cod_lote     = lo.cod_lote
                    AND     la.exercicio    = lo.exercicio
                    AND     la.tipo         = lo.tipo
                    AND     la.cod_entidade = lo.cod_entidade
                    AND     pb.cod_entidade IN ('|| stEntidade ||')
                    AND     pa.exercicio    = ''|| quote_literal(stExercicio) ||'' ';
                    --IF (inCodRecurso is not null and inCodRecurso<>'') THEN
                    --    stSql := stSql ||' AND re.cod_recurso = '|| inCodRecurso ||' ';
                    --END IF;
                    stSql := stSql || ' ORDER BY pc.cod_estrutural
                  ) as tabela';
    EXECUTE stSql;

    CREATE UNIQUE INDEX unq_debito              ON tmp_debito           (cod_estrutural varchar_pattern_ops, oid_temp);
    CREATE UNIQUE INDEX unq_credito             ON tmp_credito          (cod_estrutural varchar_pattern_ops, oid_temp);


    CREATE TEMPORARY TABLE tmp_totaliza_debito AS
        SELECT *
        FROM  tmp_debito
        WHERE dt_lote BETWEEN to_date( stDtInicial , 'dd/mm/yyyy' ) AND   to_date( stDtFinal , 'dd/mm/yyyy' )
        AND   tipo <> 'I';


    CREATE TEMPORARY TABLE tmp_totaliza_credito AS
        SELECT *
        FROM  tmp_credito
        WHERE dt_lote BETWEEN to_date( stDtInicial , 'dd/mm/yyyy' ) AND   to_date( stDtFinal , 'dd/mm/yyyy' )
        AND   tipo <> 'I';

    stSqlComplemento := ' dt_lote = to_date( '|| quote_literal(stDtInicial) ||' , '|| quote_literal('dd/mm/yyyy') ||') ';
    stSqlComplemento := stSqlComplemento || ' AND tipo = '|| quote_literal('I') ||' ';

    stSql := 'CREATE TEMPORARY TABLE tmp_totaliza AS
        SELECT * FROM tmp_debito
        WHERE
             '|| stSqlComplemento ||'
       UNION
        SELECT * FROM tmp_credito
        WHERE
             '|| stSqlComplemento ||'
    ';
    EXECUTE stSql;

    CREATE UNIQUE INDEX unq_totaliza            ON tmp_totaliza         (cod_estrutural varchar_pattern_ops, oid_temp);
    CREATE UNIQUE INDEX unq_totaliza_credito    ON tmp_totaliza_credito (cod_estrutural varchar_pattern_ops, oid_temp);
    CREATE UNIQUE INDEX unq_totaliza_debito     ON tmp_totaliza_debito  (cod_estrutural varchar_pattern_ops, oid_temp);

    SELECT INTO
                   stMascRecurso
                   administracao.configuracao.valor
        FROM   administracao.configuracao
        WHERE   administracao.configuracao.cod_modulo = 8
          AND   administracao.configuracao.parametro = 'masc_recurso'
          AND   administracao.configuracao.exercicio = stExercicio;

        stSql := '
            SELECT
                pc.cod_estrutural,
                --sw_fn_mascara_dinamica(''|| quote_literal(stMascRecurso) ||'', cast(re.cod_recurso as varchar)) AS cod_recurso,
                ore.masc_recurso AS cod_recurso,
                pa.cod_plano,
                ore.nom_recurso,
                pc.nom_conta,
                0.00 as vl_saldo_atual
            FROM
                contabilidade.plano_banco       as pb,
                contabilidade.plano_conta       as pc,
                contabilidade.plano_analitica   as pa,
                contabilidade.plano_recurso     as re,
                orcamento.recurso(''|| quote_literal(stExercicio) ||'') as ore
            WHERE
                pb.exercicio = pa.exercicio
            AND pb.cod_plano = pa.cod_plano

            AND pc.exercicio = pa.exercicio
            AND pc.cod_conta = pa.cod_conta

            AND pa.exercicio = re.exercicio
            AND pa.cod_plano = re.cod_plano

            AND re.exercicio    = ore.exercicio
            AND re.cod_recurso  = ore.cod_recurso

            AND (pc.cod_estrutural like ''1.1.1.1.1%'' OR pc.cod_estrutural like ''1.1.1.1.2%'')
            AND  pb.cod_entidade IN ('|| stEntidade ||')
            AND  pc.exercicio    = ''|| quote_literal(stExercicio) ||'' ';
            --IF (inCodRecurso is not null and inCodRecurso<>'') THEN
            --    stSql := stSql || ' AND re.cod_recurso = '|| inCodRecurso ||' ';
            --END IF;
            stSql := stSql || ' ORDER BY cod_estrutural ';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        arRetorno := empenho.fn_totaliza_disponibilidades_financeiras( publico.fn_mascarareduzida(reRegistro.cod_estrutural) , stDtInicial, stDtFinal);
        reRegistro.vl_saldo_atual    := arRetorno[1];
        IF ( reRegistro.vl_saldo_atual <> 0.00 )
        THEN
            RETURN NEXT reRegistro;
        END IF;
    END LOOP;

    DROP INDEX unq_totaliza;
    DROP INDEX unq_totaliza_debito;
    DROP INDEX unq_totaliza_credito;
    DROP INDEX unq_debito;
    DROP INDEX unq_credito;

    DROP TABLE tmp_totaliza;
    DROP TABLE tmp_debito;
    DROP TABLE tmp_credito;
    DROP TABLE tmp_totaliza_debito;
    DROP TABLE tmp_totaliza_credito;

    RETURN;
END;
$$ LANGUAGE 'plpgsql';
