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
--/**
--
-- script de funcao PLSQL
--
-- URBEM Solugues de Gestco Pzblica Ltda
-- www.urbem.cnm.org.br
--
-- $Revision: 23177 $
-- $Name$
-- $Autor: Marcia $
-- Date: 2006/07/01 10:50:00 $
--
-- Caso de uso: uc-04.05.53
--
-- Objetivo: efetua a gravacao do registro de ferias no processo de 
-- avalicao de medias
--*/

CREATE OR REPLACE FUNCTION gravaRegistroEventoFerias(integer,integer,integer,numeric,numeric,varchar,integer) RETURNS BOOLEAN as $$

DECLARE
    inCodContrato                ALIAS FOR $1;
    inCodPeriodoMovimentacao     ALIAS FOR $2;
    inCodEvento                  ALIAS FOR $3;
    nuValor                      ALIAS FOR $4;
    nuQuantidade                 ALIAS FOR $5;
    stDesdobramento              ALIAS FOR $6;   
    inParcelas                   ALIAS FOR $7;   
    inContador                   INTEGER := 0;
    inCodRegistro                INTEGER := 0;
    stTimestamp                  TIMESTAMP := now();
    boRetorno                    BOOLEAN := TRUE;
    stSql                        VARCHAR := '';
    reRegistro                   RECORD;
    stEntidade                   VARCHAR;
BEGIN
    stEntidade := recuperarBufferTexto('stEntidade');

    -- ???????????????
    inContador := selectIntoInteger('SELECT COUNT(ultimo_registro_evento_ferias.*) AS contador
                 FROM folhapagamento'||stEntidade||'.ultimo_registro_evento_ferias
                    , folhapagamento'||stEntidade||'.registro_evento_ferias
                WHERE ultimo_registro_evento_ferias.cod_registro  = registro_evento_ferias.cod_registro
                  AND ultimo_registro_evento_ferias.cod_evento    = registro_evento_ferias.cod_evento
                  AND ultimo_registro_evento_ferias.timestamp     = registro_evento_ferias.timestamp
                  AND ultimo_registro_evento_ferias.desdobramento = registro_evento_ferias.desdobramento
                  AND registro_evento_ferias.cod_contrato = '||inCodContrato||'
                  AND registro_evento_ferias.cod_periodo_movimentacao  = '||inCodPeriodoMovimentacao||'
                  AND registro_evento_ferias.cod_evento = '||inCodEvento||'
                  AND registro_evento_ferias.desdobramento = '||quote_literal(stDesdobramento)||' ' );

    IF inContador = 0 THEN
        stSql := ' SELECT registro_evento_ferias.*
                     FROM folhapagamento'||stEntidade||'.registro_evento_ferias
                    WHERE cod_contrato = '||inCodContrato||'
                      AND cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||' 
                      AND cod_evento = '||inCodEvento||'
                      AND desdobramento = '||quote_literal(stDesdobramento)||' ';
        FOR reRegistro IN EXECUTE stSql
        LOOP
            stSql := 'DELETE FROM folhapagamento'||stEntidade||'.registro_evento_ferias_parcela   
                            WHERE cod_registro = '||reRegistro.cod_registro||'
                              AND cod_evento = '||reRegistro.cod_evento||'
                              AND desdobramento = '''||reRegistro.desdobramento||'''
                              AND timestamp = '''||reRegistro.timestamp||'''';
            EXECUTE stSql; 
            stSql := 'DELETE FROM folhapagamento'||stEntidade||'.evento_ferias_calculado_dependente
                            WHERE cod_registro = '||reRegistro.cod_registro||'
                              AND cod_evento = '||reRegistro.cod_evento||'
                              AND desdobramento = '''||reRegistro.desdobramento||'''
                              AND timestamp_registro = '''||reRegistro.timestamp||'''';
            EXECUTE stSql;
            stSql := 'DELETE FROM folhapagamento'||stEntidade||'.evento_ferias_calculado         
                            WHERE cod_registro = '||reRegistro.cod_registro||'
                              AND cod_evento = '||reRegistro.cod_evento||'
                              AND desdobramento = '''||reRegistro.desdobramento||'''
                              AND timestamp_registro = '''||reRegistro.timestamp||'''';
            EXECUTE stSql;
            stSql := 'DELETE FROM folhapagamento'||stEntidade||'.log_erro_calculo_ferias          
                            WHERE cod_registro = '||reRegistro.cod_registro||'
                              AND cod_evento = '||reRegistro.cod_evento||'
                              AND desdobramento = '''||reRegistro.desdobramento||'''
                              AND timestamp = '''||reRegistro.timestamp||'''';
            EXECUTE stSql;
            stSql := 'DELETE FROM folhapagamento'||stEntidade||'.ultimo_registro_evento_ferias    
                            WHERE cod_registro = '||reRegistro.cod_registro||'
                              AND cod_evento = '||reRegistro.cod_evento||'
                              AND desdobramento = '''||reRegistro.desdobramento||'''
                              AND timestamp = '''||reRegistro.timestamp||'''';
            EXECUTE stSql;
            stSql := 'DELETE FROM folhapagamento'||stEntidade||'.registro_evento_ferias           
                            WHERE cod_registro = '||reRegistro.cod_registro||'
                              AND cod_evento = '||reRegistro.cod_evento||'
                              AND desdobramento = '''||reRegistro.desdobramento||'''
                              AND timestamp = '''||reRegistro.timestamp||'''';
            EXECUTE stSql;
        END LOOP;
        inCodRegistro := selectIntoInteger(' SELECT COALESCE(max(cod_registro)+1,1) as cod_registro
                                               FROM folhapagamento'||stEntidade||'.registro_evento_ferias');

        inContador := selectIntoInteger('SELECT count(*)
                                 FROM folhapagamento'||stEntidade||'.contrato_servidor_periodo
                                WHERE cod_contrato = '||inCodContrato||'
                                  AND cod_periodo_movimentacao = '||inCodPeriodoMovimentacao);
        IF inContador = 0 THEN
            stSql := 'INSERT INTO folhapagamento'||stEntidade||'.contrato_servidor_periodo (cod_contrato,cod_periodo_movimentacao) VALUES ('||inCodContrato||','||inCodPeriodoMovimentacao||')';
            EXECUTE stSql;
        END IF;

        stSql := ' INSERT into folhapagamento'||stEntidade||'.registro_evento_ferias
                 ( cod_registro
                  ,timestamp
                  ,cod_evento
                  ,desdobramento
                  ,cod_contrato
                  ,cod_periodo_movimentacao
                  ,valor
                  ,quantidade
                  ,automatico
                 )
               VALUES 
                 (  '||inCodRegistro||'
                   , TO_TIMESTAMP('''||stTimestamp||''',''yyyy-mm-dd hh24:mi:ss.us'')
                   , '||inCodEvento||'
                   , '''||stDesdobramento||'''
                   , '||inCodContrato||'
                   , '||inCodPeriodoMovimentacao||'
                   , '||nuValor||'
                   , '||nuQuantidade||'
                   , TRUE
                 )';   
        EXECUTE stSql;

        stSql := ' INSERT into folhapagamento'||stEntidade||'.ultimo_registro_evento_ferias
                 ( cod_registro
                  ,timestamp
                  ,cod_evento
                  ,desdobramento
                 )
               VALUES
                 (  '||inCodRegistro||'
                   , TO_TIMESTAMP('''||stTimestamp||''',''yyyy-mm-dd hh24:mi:ss.us'')
                   , '||inCodEvento||'
                   , '''||stDesdobramento||'''
                 )';
        EXECUTE stSql;
        IF inParcelas > 0 THEN
            stSql := 'INSERT into folhapagamento'||stEntidade||'.registro_evento_ferias_parcela
                      (cod_registro,timestamp,cod_evento,desdobramento,parcela) VALUES
                      ('||inCodRegistro||',TO_TIMESTAMP('''||stTimestamp||''',''yyyy-mm-dd hh24:mi:ss.us'')
                      ,'||inCodEvento||', '''||stDesdobramento||''','||inParcelas||')';
            EXECUTE stSql;
        END IF;
   END IF;
   RETURN TRUE;
END;
$$ LANGUAGE 'plpgsql';


