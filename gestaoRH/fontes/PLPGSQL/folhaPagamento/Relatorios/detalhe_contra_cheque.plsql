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

--create type registro_detalhe_contracheque as (
--    codigo          varchar,
--    descricao       varchar,
--    desdobramento   varchar,
--    referencia      numeric,
--    proventos       numeric,
--    descontos       numeric
--);

create or replace function detalhe_contra_cheque(integer, integer, integer, integer, varchar, varchar,boolean)
returns setof RECORD as $$
DECLARE
    inCodContrato            alias for $1;
    inCodPeriodoMovimentacao alias for $2;
    inCodConfiguracao        alias for $3;
    inCodComplementar        alias for $4;
    stEntidade               alias for $5;
    stDesdobramento          alias for $6;
    boIRRF                   alias for $7;
    stChaveSQL               varchar;
    stSql                    varchar;
    regAuxiliar              record;
    reRecord                 record;
    stAuxiliar               varchar;
BEGIN

--       SELECT into regAuxiliar count(*) as qtd
--	from pg_tables
--	where schemaname ~ 'pg_temp'
--	and tablename = 'sqls_preparados';
--
--	if (regAuxiliar.qtd = 0) then
--		create temp table sqls_preparados (
--				nome_plano varchar not null
--			, definicao text not null
--			, primary key(nome_plano)
--		);
--	end if;

        IF inCodConfiguracao = 0 THEN

            --stChaveSQL := 'eventos_complementar_'||stEntidade||'_'||inCodComplementar;

            stSql := ' SELECT evento_complementar_calculado.valor
                          --, evento_complementar_calculado.quantidade
                          , ( CASE WHEN evento.apresenta_parcela=''t''
                                      THEN ((round(evento_complementar_calculado.quantidade,0))||''/''||
                                      (select registro_evento_complementar_parcela.parcela
					FROM folhapagamento'||stEntidade||'.registro_evento_complementar_parcela
					WHERE registro_evento_complementar_parcela.cod_registro = ultimo_registro_evento_complementar.cod_registro
                                        LIMIT 1))::VARCHAR
                                ELSE (replace(evento_complementar_calculado.quantidade::varchar ,''.'','',''))::VARCHAR
			    END ) as quantidade
                          , evento.cod_evento
                          , ( CASE WHEN evento_complementar_calculado.desdobramento IS NOT NULL THEN
                                            evento.descricao||'' ''||getDesdobramentoFolha(evento_complementar_calculado.cod_configuracao,evento_complementar_calculado.desdobramento,'''||stEntidade||''')
                                       WHEN evento_complementar_calculado.desdobramento IS NOT NULL AND  evento.natureza = ''I'' THEN
                                            evento.descricao||'' ''||getDesdobramentoFolha(evento_complementar_calculado.cod_configuracao,evento_complementar_calculado.desdobramento,'''||stEntidade||''') || '' (INFORMATIVO)''
                                       WHEN evento.natureza = ''I'' THEN
                                            evento.descricao || '' (INFORMATIVO)''
                                   ELSE evento.descricao
                              END ) AS descricao
                          , evento.codigo
                          , evento.natureza
                          , getDesdobramentoFolha(evento_complementar_calculado.cod_configuracao,evento_complementar_calculado.desdobramento,''' || stEntidade ||''') as desdobramento_texto
                          , evento.descricao as nom_evento
                       FROM folhapagamento'||stEntidade||'.registro_evento_complementar
                          , folhapagamento'||stEntidade||'.ultimo_registro_evento_complementar
                          , folhapagamento'||stEntidade||'.evento_complementar_calculado
                          , folhapagamento'||stEntidade||'.evento
                      WHERE registro_evento_complementar.cod_registro = ultimo_registro_evento_complementar.cod_registro
                        AND registro_evento_complementar.cod_evento   = ultimo_registro_evento_complementar.cod_evento
                        AND registro_evento_complementar.timestamp    = ultimo_registro_evento_complementar.timestamp
                        AND registro_evento_complementar.cod_configuracao = ultimo_registro_evento_complementar.cod_configuracao
                        AND ultimo_registro_evento_complementar.cod_registro = evento_complementar_calculado.cod_registro
                        AND ultimo_registro_evento_complementar.cod_evento   = evento_complementar_calculado.cod_evento
                        AND ultimo_registro_evento_complementar.timestamp    = evento_complementar_calculado.timestamp_registro
                        AND ultimo_registro_evento_complementar.cod_configuracao = evento_complementar_calculado.cod_configuracao
                        AND evento_complementar_calculado.cod_evento = evento.cod_evento
                        AND cod_complementar = '||inCodComplementar||'
                    ';

        ELSEIF inCodConfiguracao = 1 THEN

           -- stChaveSQL := 'eventos_salario_'||stEntidade;

            stSql := 'SELECT evento_calculado.valor
                          --, evento_calculado.quantidade
                          , ( CASE WHEN evento.apresenta_parcela=''t''
                                      THEN (round(evento_calculado.quantidade,0))||''/''||(select registro_evento_parcela.parcela
					FROM folhapagamento'||stEntidade||'.registro_evento_parcela
					WHERE registro_evento_parcela.cod_registro = ultimo_registro_evento.cod_registro
                                        LIMIT 1)::VARCHAR
				ELSE (replace(evento_calculado.quantidade::varchar ,''.'','',''))::VARCHAR
			    END ) as quantidade
                          --, evento_calculado.cod_registro
                          , evento.cod_evento
                          --, evento_calculado.desdobramento
                          , ( CASE WHEN evento_calculado.desdobramento IS NOT NULL THEN
                                            evento.descricao ||'' ''|| getDesdobramentoSalario(evento_calculado.desdobramento,'||quote_literal(stEntidade)||')
                                      WHEN evento_calculado.desdobramento IS NOT NULL AND  evento.natureza = ''I'' THEN
                                            evento.descricao ||'' ''|| getDesdobramentoSalario(evento_calculado.desdobramento,'||quote_literal(stEntidade)||')   || '' (INFORMATIVO)''
                                      WHEN evento.natureza = ''I'' THEN
                                            evento.descricao || '' (INFORMATIVO)''
                                      ELSE evento.descricao
                            END ) as descricao
                          , evento.codigo
                          , evento.natureza
                          , getDesdobramentoSalario(evento_calculado.desdobramento,'||quote_literal(stEntidade)||') as desdobramento_texto
                          , evento.descricao as nom_evento
                       FROM folhapagamento'||stEntidade||'.registro_evento_periodo
                          , folhapagamento'||stEntidade||'.registro_evento
                          , folhapagamento'||stEntidade||'.ultimo_registro_evento
                          , folhapagamento'||stEntidade||'.evento_calculado
                          , folhapagamento'||stEntidade||'.evento
                      WHERE registro_evento_periodo.cod_registro = registro_evento.cod_registro
                        AND registro_evento.cod_registro = ultimo_registro_evento.cod_registro
                        AND registro_evento.cod_evento   = ultimo_registro_evento.cod_evento
                        AND registro_evento.timestamp    = ultimo_registro_evento.timestamp
                        AND ultimo_registro_evento.cod_registro = evento_calculado.cod_registro
                        AND ultimo_registro_evento.cod_evento   = evento_calculado.cod_evento
                        AND ultimo_registro_evento.timestamp    = evento_calculado.timestamp_registro
                        AND evento_calculado.cod_evento = evento.cod_evento ';


        ELSEIF inCodConfiguracao = 2 THEN

            --stChaveSQL := 'eventos_ferias_'||stEntidade||'_'||stDesdobramento;

            stSql := 'SELECT evento_ferias_calculado.valor
                          --, evento_ferias_calculado.quantidade
                          , ( CASE WHEN evento.apresenta_parcela=''t''
                                      THEN ((round(evento_ferias_calculado.quantidade,0))||''/''||
                                      (select registro_evento_ferias_parcela.parcela
					FROM folhapagamento'||stEntidade||'.registro_evento_ferias_parcela
					WHERE registro_evento_ferias_parcela.cod_registro = ultimo_registro_evento_ferias.cod_registro
                                        LIMIT 1))::VARCHAR
                                ELSE (replace(evento_ferias_calculado.quantidade::varchar ,''.'','',''))::VARCHAR
			    END ) as quantidade
                          , evento.cod_evento
                          , ( CASE WHEN evento.natureza = ''I'' THEN
                                            evento.descricao || '' (INFORMATIVO)''
                                      ELSE evento.descricao
                            END ) as descricao
                          , evento.codigo
                          , evento.natureza
                          , getDesdobramentoFerias(evento_ferias_calculado.desdobramento,'||quote_literal(stEntidade)||') as desdobramento_texto
                          , evento.descricao as nom_evento
                        FROM folhapagamento'||stEntidade||'.registro_evento_ferias
                            , folhapagamento'||stEntidade||'.ultimo_registro_evento_ferias
                            , folhapagamento'||stEntidade||'.evento_ferias_calculado
                            , folhapagamento'||stEntidade||'.evento
                        WHERE registro_evento_ferias.cod_registro = ultimo_registro_evento_ferias.cod_registro
                          AND registro_evento_ferias.cod_evento   = ultimo_registro_evento_ferias.cod_evento
                          AND registro_evento_ferias.timestamp    = ultimo_registro_evento_ferias.timestamp
                          AND registro_evento_ferias.desdobramento = ultimo_registro_evento_ferias.desdobramento
                          AND ultimo_registro_evento_ferias.cod_registro = evento_ferias_calculado.cod_registro
                          AND ultimo_registro_evento_ferias.cod_evento   = evento_ferias_calculado.cod_evento
                          AND ultimo_registro_evento_ferias.timestamp    = evento_ferias_calculado.timestamp_registro
                          AND ultimo_registro_evento_ferias.desdobramento= evento_ferias_calculado.desdobramento
                          AND evento_ferias_calculado.cod_evento = evento.cod_evento
                    ';
            IF (stDesdobramento = 'A' OR stDesdobramento = 'F' OR stDesdobramento = 'D') THEN
                stSql := stSql ||' AND evento_ferias_calculado.desdobramento = '||quote_literal(stDesdobramento)||' ';
            END IF;

        ELSEIF inCodConfiguracao = 3 THEN

            --stChaveSQL := 'eventos_decimo_'||stEntidade||'_'||stDesdobramento;

            stSql := 'SELECT evento_decimo_calculado.valor
                         --, evento_decimo_calculado.quantidade
                         , ( CASE WHEN evento.apresenta_parcela=''t''
                                      THEN ((round(evento_decimo_calculado.quantidade,0))||''/''||
                                      (select registro_evento_decimo_parcela.parcela
					FROM folhapagamento'||stEntidade||'.registro_evento_decimo_parcela
					WHERE registro_evento_decimo_parcela.cod_registro = registro_evento_decimo.cod_registro
                                        LIMIT 1))::VARCHAR
                                ELSE (replace(evento_decimo_calculado.quantidade::varchar ,''.'','',''))::VARCHAR
			    END ) as quantidade
                         , evento.cod_evento
                         , ( CASE WHEN evento.natureza = ''I'' THEN
                                            evento.descricao || '' (INFORMATIVO)''
                                      ELSE evento.descricao
                            END ) as descricao
                         , evento.codigo
                         , evento.natureza
                         , getDesdobramentoDecimo(evento_decimo_calculado.desdobramento,'||quote_literal(stEntidade)||') as desdobramento_texto
                         , evento.descricao as nom_evento
                      FROM folhapagamento'||stEntidade||'.evento_decimo_calculado
                         , folhapagamento'||stEntidade||'.registro_evento_decimo
                         , folhapagamento'||stEntidade||'.evento
                     WHERE evento_decimo_calculado.cod_registro = registro_evento_decimo.cod_registro
                       AND evento_decimo_calculado.cod_evento = registro_evento_decimo.cod_evento
                       AND evento_decimo_calculado.timestamp_registro = registro_evento_decimo.timestamp
                       AND evento_decimo_calculado.desdobramento = registro_evento_decimo.desdobramento
                       AND evento_decimo_calculado.cod_evento = evento.cod_evento
                    ';
            IF (stDesdobramento != '') THEN
                stSql := stSql ||' AND evento_decimo_calculado.desdobramento = '||quote_literal(stDesdobramento)||' ';
            END IF;

        ELSEIF inCodConfiguracao = 4 THEN

            --stChaveSQL := 'eventos_recisao_'||stEntidade||'_'||stDesdobramento;

            stSql := 'SELECT evento_rescisao_calculado.valor
                         --, evento_rescisao_calculado.quantidade
                         , ( CASE WHEN evento.apresenta_parcela=''t'' AND
				(select registro_evento_rescisao_parcela.parcela
				FROM folhapagamento'||stEntidade||'.registro_evento_rescisao_parcela
				WHERE registro_evento_rescisao_parcela.cod_registro = registro_evento_rescisao.cod_registro
				) IS NOT NULL
                                      THEN ((round(evento_rescisao_calculado.quantidade,0))||''/''||
                                      (select registro_evento_rescisao_parcela.parcela
					FROM folhapagamento'||stEntidade||'.registro_evento_rescisao_parcela
					WHERE registro_evento_rescisao_parcela.cod_registro = registro_evento_rescisao.cod_registro
                                        LIMIT 1))::VARCHAR
                                ELSE (replace(evento_rescisao_calculado.quantidade::varchar ,''.'','',''))::VARCHAR
			    END ) as quantidade
                         , evento.cod_evento
                         , ( CASE WHEN evento.natureza = ''I'' THEN
                                            evento.descricao || '' (INFORMATIVO)''
                                      ELSE evento.descricao
                            END ) as descricao
                         , evento.codigo
                         , evento.natureza
                         , getDesdobramentoRescisao(evento_rescisao_calculado.desdobramento,'||quote_literal(stEntidade)||') as desdobramento_texto
                         , evento.descricao as nom_evento
                      FROM folhapagamento'||stEntidade||'.evento_rescisao_calculado
                         , folhapagamento'||stEntidade||'.registro_evento_rescisao
                         , folhapagamento'||stEntidade||'.ultimo_registro_evento_rescisao
                         , folhapagamento'||stEntidade||'.evento
                     WHERE registro_evento_rescisao.cod_registro     = ultimo_registro_evento_rescisao.cod_registro
                       AND registro_evento_rescisao.cod_evento       = ultimo_registro_evento_rescisao.cod_evento
                       AND registro_evento_rescisao.desdobramento = ultimo_registro_evento_rescisao.desdobramento
                       AND registro_evento_rescisao.timestamp        = ultimo_registro_evento_rescisao.timestamp
                       AND registro_evento_rescisao.cod_registro     = evento_rescisao_calculado.cod_registro
                       AND registro_evento_rescisao.cod_evento       = evento_rescisao_calculado.cod_evento
                       AND registro_evento_rescisao.desdobramento = evento_rescisao_calculado.desdobramento
                       AND registro_evento_rescisao.timestamp        = evento_rescisao_calculado.timestamp_registro
                       AND evento_rescisao_calculado.cod_evento = evento.cod_evento
                ';
            IF (stDesdobramento != '') THEN
                stSql := stSql ||' AND evento_rescisao_calculado.desdobramento = '||quote_literal(stDesdobramento)||' ';
            END IF;

        END IF;


        stSql := stSql || ' AND cod_contrato = '|| inCodContrato || '
                            AND cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                            AND (natureza IN (''P'',''D'') OR ( evento.natureza = ''I'' AND evento.apresentar_contracheque = ''t'') )
                            ORDER BY codigo ';

        FOR reRecord IN EXECUTE stSql
        LOOP
            RETURN NEXT reRecord;
        END LOOP;
END;
$$
LANGUAGE 'plpgsql';


