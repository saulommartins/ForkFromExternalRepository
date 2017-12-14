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
* Script de função PLPGSQL - Relatório STN - RREO - Anexo 8 a partir de 2015
* para trazer os Saldos Financeiros das Contas que tem vínculo com o Fundeb
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Revision: $
* $Name: $
* $Author: $
* $Date: $
*
* $Id: relatorioRREOAnexo8_saldos.plsql 61214 2014-12-16 19:49:31Z evandro $
*
* Casos de uso: 
*/


CREATE OR REPLACE FUNCTION stn.fn_rreo_anexo8_saldos( stExercicio VARCHAR, stDtFim VARCHAR, stEntidades VARCHAR ) RETURNS SETOF RECORD AS $$
DECLARE

    --arDatas         VARCHAR[] ;
    stDtIni         VARCHAR := '';
    --stDtFim         VARCHAR := '';
    arRetorno       NUMERIC[];

    reRegistro      RECORD;
    stSql           VARCHAR := '';

BEGIN

    stDtIni := '01/01/' || stExercicio;
    --arDatas := publico.bimestre ( stExercicio, inBimestre );
    --stDtFim := arDatas [ 1 ];

    stSQL := '
    CREATE TEMPORARY TABLE tmp_debito AS (
    SELECT
        *
    FROM
        (SELECT
            pc.cod_estrutural,
            pa.cod_plano,
            vl.tipo_valor,
            vl.vl_lancamento,
            vl.cod_entidade,
            lo.cod_lote,
            lo.dt_lote,
            lo.exercicio,
            lo.tipo,
            vl.sequencia,
            vl.oid as oid_temp,
            sc.cod_sistema 
        FROM
            contabilidade.plano_conta      pc
            INNER JOIN
            contabilidade.plano_analitica  pa ON
                pc.cod_conta    = pa.cod_conta AND
                pc.exercicio    = pa.exercicio
            INNER JOIN
            stn.vinculo_fundeb svf ON
                svf.cod_plano = pa.cod_plano AND
                svf.exercicio = pa.exercicio
            INNER JOIN 
            contabilidade.conta_debito     cd ON
                pa.cod_plano    = cd.cod_plano AND
                pa.exercicio    = cd.exercicio
            INNER JOIN 
            contabilidade.valor_lancamento vl ON
                cd.cod_lote     = vl.cod_lote AND 
                cd.tipo         = vl.tipo AND
                cd.sequencia    = vl.sequencia AND
                cd.exercicio    = vl.exercicio AND
                cd.tipo_valor   = vl.tipo_valor AND
                cd.cod_entidade = vl.cod_entidade
            INNER JOIN 
            contabilidade.lancamento la ON
                vl.cod_lote     = la.cod_lote AND
                vl.tipo         = la.tipo AND
                vl.sequencia    = la.sequencia AND
                vl.exercicio    = la.exercicio AND
                vl.cod_entidade = la.cod_entidade
            INNER JOIN 
            contabilidade.lote lo ON
                la.cod_lote     = lo.cod_lote AND
                la.exercicio    = lo.exercicio AND
                la.tipo         = lo.tipo AND
                la.cod_entidade = lo.cod_entidade
            INNER JOIN 
            contabilidade.sistema_contabil sc ON
                sc.cod_sistema  = pc.cod_sistema AND
                sc.exercicio    = pc.exercicio 
        WHERE 
            vl.tipo_valor   = ''D'' AND 
            pa.exercicio = ''' || stExercicio || ''' 
        ORDER BY 
            pc.cod_estrutural
        ) as tabela
    WHERE true 
    );
    
    CREATE UNIQUE INDEX unq_debito ON tmp_debito (cod_estrutural varchar_pattern_ops, oid_temp);
    ';
    
    EXECUTE stSQL; 


    stSQL := '
    CREATE TEMPORARY TABLE tmp_credito AS (
    SELECT
        *
    FROM
        (SELECT
            pc.cod_estrutural,
            pa.cod_plano,
            vl.tipo_valor,
            vl.vl_lancamento,
            vl.cod_entidade,
            lo.cod_lote,
            lo.dt_lote,
            lo.exercicio,
            lo.tipo,
            vl.sequencia,
            vl.oid as oid_temp,
            sc.cod_sistema 
        FROM 
            contabilidade.plano_conta pc
            INNER JOIN
            contabilidade.plano_analitica pa ON
                pc.cod_conta    = pa.cod_conta AND 
                pc.exercicio    = pa.exercicio
            INNER JOIN
            stn.vinculo_fundeb svf ON
                svf.cod_plano = pa.cod_plano AND
                svf.exercicio = pa.exercicio
            INNER JOIN 
            contabilidade.conta_credito cd ON
                pa.cod_plano    = cd.cod_plano AND
                pa.exercicio    = cd.exercicio
            INNER JOIN 
            contabilidade.valor_lancamento vl ON
                cd.cod_lote     = vl.cod_lote AND 
                cd.tipo         = vl.tipo AND
                cd.sequencia    = vl.sequencia AND
                cd.exercicio    = vl.exercicio AND
                cd.tipo_valor   = vl.tipo_valor AND
                cd.cod_entidade = vl.cod_entidade
            INNER JOIN 
            contabilidade.lancamento la ON
                vl.cod_lote     = la.cod_lote AND
                vl.tipo         = la.tipo AND
                vl.sequencia    = la.sequencia AND
                vl.exercicio    = la.exercicio AND
                vl.cod_entidade = la.cod_entidade
            INNER JOIN 
            contabilidade.lote lo ON
                la.cod_lote     = lo.cod_lote AND
                la.exercicio    = lo.exercicio AND
                la.tipo         = lo.tipo AND
                la.cod_entidade = lo.cod_entidade
            INNER JOIN 
            contabilidade.sistema_contabil sc ON
                sc.cod_sistema  = pc.cod_sistema AND
                sc.exercicio    = pc.exercicio 
        WHERE
            vl.tipo_valor   = ''C'' AND
            pa.exercicio    = ''' || stExercicio || '''             
        ORDER BY
            pc.cod_estrutural
        ) as tabela
    WHERE
        true 
    );
    
    CREATE UNIQUE INDEX unq_credito ON tmp_credito (cod_estrutural varchar_pattern_ops, oid_temp);
    ';
    
    EXECUTE stSQL;
    
    
    stSQL := '
    CREATE TEMPORARY TABLE tmp_totaliza_debito AS (
    SELECT
        * 
    FROM
        tmp_debito
    WHERE
        dt_lote BETWEEN to_date( ''' || stDtIni || ''' , ''dd/mm/yyyy'' ) AND 
                        to_date( ''' || stDtFim || ''' , ''dd/mm/yyyy'' ) AND 
        tipo <> ''I''
    ) ';
    
    EXECUTE stSQL;
    
    
    stSQL := '
    CREATE TEMPORARY TABLE tmp_totaliza_credito AS (
    SELECT
        * 
    FROM
        tmp_credito
    WHERE
        dt_lote BETWEEN to_date( ''' || stDtIni || ''' , ''dd/mm/yyyy'' ) AND
                        to_date( ''' || stDtFim || ''' , ''dd/mm/yyyy'' ) AND
        tipo <> ''I''
    ) ';
    
    EXECUTE stSQL;
    
    stSQL := '
    CREATE TEMPORARY TABLE tmp_totaliza AS (
    SELECT
        *
    FROM
        tmp_debito 
    WHERE 
        dt_lote = to_date( ''' || stDtIni || ''', ''dd/mm/yyyy'') AND
        tipo = ''I'' 
    UNION
    SELECT
        *
    FROM
        tmp_credito 
    WHERE
        dt_lote = to_date( ''' || stDtIni || ''', ''dd/mm/yyyy'') AND
        tipo = ''I'' 
    ) ';

    EXECUTE stSQL;
    
    -- select de retorno
    
    stSQL := '
    SELECT
        re.exercicio, 
        re.cod_recurso,
        trim(pc.cod_estrutural) AS cod_estrutural,
        0.00 as vl_saldo_anterior,
        0.00 as vl_saldo_debitos,
        0.00 as vl_saldo_creditos,
        0.00 as vl_saldo_atual 
    FROM
        orcamento.recurso re
--        INNER JOIN 
--        stn.vinculo_recurso vn ON
--            vn.exercicio = re.exercicio AND
--            vn.cod_recurso = re.cod_recurso
        INNER JOIN
        contabilidade.plano_recurso pr ON
            pr.exercicio = re.exercicio AND
            pr.cod_recurso = re.cod_recurso 
        INNER JOIN 
        contabilidade.plano_analitica pa ON
            pa.exercicio = pr.exercicio AND
            pa.cod_plano = pr.cod_plano
        INNER JOIN
        stn.vinculo_fundeb svf ON
            svf.cod_plano = pa.cod_plano AND
            svf.exercicio = pa.exercicio
        INNER JOIN
        contabilidade.plano_conta pc ON
            pc.cod_conta = pa.cod_conta AND
            pc.exercicio = pa.exercicio 
        INNER JOIN
        stn.vinculo_fundeb vb ON
            vb.cod_plano = pa.cod_plano AND
            vb.exercicio = pa.exercicio
    WHERE 
        re.exercicio = ''' || stExercicio || ''' --AND 
        -- Fundeb (cod_vinculo = 1)
        --vn.cod_vinculo = 1 
    GROUP BY
        re.exercicio, 
        re.cod_recurso,
        pc.cod_estrutural 
    ';
    
    FOR reRegistro IN EXECUTE stSql
    LOOP
    
        arRetorno := contabilidade.fn_totaliza_balancete_verificacao( publico.fn_mascarareduzida(reRegistro.cod_estrutural) , stDtIni, stDtFim );
        
        reRegistro.vl_saldo_anterior := arRetorno[1];
        reRegistro.vl_saldo_debitos  := arRetorno[2];
        reRegistro.vl_saldo_creditos := arRetorno[3];
        reRegistro.vl_saldo_atual    := arRetorno[4];
        IF ( reRegistro.vl_saldo_anterior <> 0.00 ) OR
           ( reRegistro.vl_saldo_debitos  <> 0.00 ) OR
           ( reRegistro.vl_saldo_creditos <> 0.00 ) OR
           ( reRegistro.vl_saldo_atual    <> 0.00 )
        THEN
            RETURN NEXT reRegistro;
        END IF;
    
    END LOOP;
    
    DROP TABLE tmp_debito;
    DROP TABLE tmp_credito;
    DROP TABLE tmp_totaliza_debito;
    DROP TABLE tmp_totaliza_credito;
    DROP TABLE tmp_totaliza;
    
    RETURN;
 
END;

$$ language 'plpgsql';
