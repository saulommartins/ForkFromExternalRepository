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
CREATE OR REPLACE FUNCTION recuperarTotaisFolha(VARCHAR,VARCHAR,VARCHAR,VARCHAR,INTEGER,BOOLEAN,INTEGER,INTEGER,VARCHAR,BOOLEAN,BOOLEAN,INTEGER,VARCHAR) RETURNS SETOF colunasTotaisFolha AS $$
DECLARE
    stTipoFiltro                ALIAS FOR $1;
    stValoresFiltro             ALIAS FOR $2;
    stExercicio                 ALIAS FOR $3;
    stEntidade                  ALIAS FOR $4;
    inCodPeriodoMovimentacao    ALIAS FOR $5;
    boElementoDespesa           ALIAS FOR $6;
    inCodConfiguracao           ALIAS FOR $7;
    inCodConfiguracaoTotais     ALIAS FOR $8;
    stSituacao                  VARCHAR := $9;
    boAgrupar                   ALIAS FOR $10;
    boAgruparBanco              ALIAS FOR $11;
    inCodComplementar           ALIAS FOR $12;
    stCodBancos                 ALIAS FOR $13;
    rwTotaisFolha               colunasTotaisFolha%ROWTYPE;
    stSql                       VARCHAR;
    stSqlFiltros                VARCHAR;
    stSituacaoFormatado         VARCHAR;
    arSituacao                  VARCHAR[];
    stConfiguracao              VARCHAR:='cs';
    reRegistro                  RECORD;
    reEvento                    RECORD;
    reDespesa                   RECORD;
    crCursor                    REFCURSOR;
    inIndex                     INTEGER:=1;
BEGIN

    IF TRIM(stSituacao) = '' THEN
        stSituacao := 'R,A,P,E';
    END IF;

    stSituacaoFormatado := '';
    arSituacao := string_to_array(stSituacao,',');
    WHILE arSituacao[inIndex] IS NOT NULL LOOP
        stSituacaoFormatado := stSituacaoFormatado || quote_literal(arSituacao[inIndex]) ||',';
        inIndex := inIndex + 1;
    END LOOP;
    stSituacaoFormatado := substr(stSituacaoFormatado,0,char_length(stSituacaoFormatado));

    IF boAgrupar IS TRUE THEN
        IF stTipoFiltro = 'lotacao_grupo' THEN
            stConfiguracao := stConfiguracao || ',o';
        END IF;
        IF stTipoFiltro = 'local_grupo' THEN
            stConfiguracao := stConfiguracao || ',l';
        END IF;
        IF stTipoFiltro = 'reg_sub_fun_esp_grupo' THEN
            stConfiguracao := stConfiguracao || ',f';
        END IF;
    END IF;


    stSql := '  SELECT eventos_calculados.cod_evento
                     , eventos_calculados.codigo
                     , eventos_calculados.descricao
                     , CASE WHEN eventos_calculados.natureza = ''P''
                             THEN sum(eventos_calculados.valor)
                             ELSE 0
                       END as proventos
                     , CASE WHEN eventos_calculados.natureza = ''D''
                             THEN sum(eventos_calculados.valor)
                             ELSE 0
                       END as descontos
                     , servidor_pensionista.agrupamento_banco
                     , servidor_pensionista.agrupamento
                  FROM (  ';

    -------------
    -- SERVIDOR
    -------------
    IF NOT(TRIM(stSituacao) = 'E') OR TRIM(stSituacao) = '' THEN
        stSql := stSql || 'SELECT cod_contrato
                                , cod_banco_salario as cod_banco';
        IF boAgruparBanco IS TRUE THEN
            stSql := stSql || '
                       , nom_banco_salario as agrupamento_banco';
        ELSE
            stSql := stSql || ' , ''''::varchar as agrupamento_banco';
        END IF;

        IF boAgrupar IS TRUE THEN
            IF stTipoFiltro = 'lotacao_grupo' THEN
                stSql := stSql || '
                       , desc_orgao as agrupamento';
            END IF;
            IF stTipoFiltro = 'local_grupo' THEN
                stSql := stSql || '
                       , desc_local as agrupamento';
            END IF;
            IF stTipoFiltro = 'atributo_servidor_grupo' THEN
                stSql := stSql || '
                       , valor_atributo as agrupamento';
            END IF;
            IF stTipoFiltro = 'reg_sub_fun_esp_grupo' THEN
                stSql := stSql || '
                       , desc_funcao as agrupamento';
            END IF;
        ELSE
            stSql := stSql || ' , ''''::varchar as agrupamento  ';
        END IF;
        stSql := stSql || ' FROM recuperarContratoServidor('|| quote_literal(stConfiguracao) ||','|| quote_literal(stEntidade) ||','|| inCodPeriodoMovimentacao ||','|| quote_literal(stTipoFiltro) ||','|| quote_literal(stValoresFiltro) ||','|| quote_literal(stExercicio) ||') AS servidor';
    END IF;

    -------------------------------
    -- UNION SERVIDOR PENSIONISTA
    -------------------------------
    IF STRPOS(TRIM(stSituacao),'E') > 0 AND char_length(TRIM(stSituacao)) > 1 THEN
        stSql := stSql || ' UNION ';
    END IF;

    -----------------
    -- PENSIONISTA
    -----------------
    IF STRPOS(TRIM(stSituacao),'E') > 0 THEN
        stSql := stSql || 'SELECT cod_contrato
                                , cod_banco_salario as cod_banco';
        IF boAgruparBanco IS TRUE THEN
            stSql := stSql || '
                       , nom_banco_salario as agrupamento_banco';
        ELSE
            stSql := stSql || ' , ''''::varchar as agrupamento_banco';
        END IF;
        IF boAgrupar IS TRUE THEN
            IF stTipoFiltro = 'lotacao_grupo' THEN
                stSql := stSql || '
                       , desc_orgao as agrupamento';
            END IF;
            IF stTipoFiltro = 'local_grupo' THEN
                stSql := stSql || '
                       , desc_local as agrupamento';
            END IF;
            IF stTipoFiltro = 'atributo_pensionista_grupo' THEN
                stSql := stSql || '
                       , valor_atributo as agrupamento';
            END IF;
            IF stTipoFiltro = 'reg_sub_fun_esp_grupo' THEN
                stSql := stSql || '
                       , desc_funcao as agrupamento';
            END IF;
        ELSE
            stSql := stSql || ' , ''''::varchar as agrupamento';
        END IF;
        stSql := stSql || ' FROM recuperarContratoPensionista('|| quote_literal(stConfiguracao) ||','|| quote_literal(stEntidade) ||','|| inCodPeriodoMovimentacao ||','|| quote_literal(stTipoFiltro) ||','|| quote_literal(stValoresFiltro) ||','|| quote_literal(stExercicio) ||') AS pensionista';
    END IF;

    stSql := stSql || ') AS servidor_pensionista';

    stSql := stSql || ' INNER JOIN (      SELECT cod_contrato
                                               , cod_evento
                                               , codigo
                                               , descricao
                                               , natureza
                                               , COALESCE(sum(valor),0) as valor
                                            FROM recuperarEventosCalculados('|| inCodConfiguracao ||','|| inCodPeriodoMovimentacao ||',0,'|| inCodComplementar ||','''', ''evento.descricao'') as eventos_proventos
                                           WHERE (eventos_proventos.natureza = ''P'' OR eventos_proventos.natureza = ''D'')';

    IF COALESCE(inCodConfiguracaoTotais,0) != 0 THEN
        stSql := stSql || '
                                                 AND EXISTS (SELECT 1
                                                               FROM folhapagamento'|| stEntidade ||'.totais_folha_eventos
                                                              WHERE totais_folha_eventos.cod_evento = eventos_proventos.cod_evento
                                                                AND totais_folha_eventos.cod_configuracao = '|| inCodConfiguracaoTotais ||')';
    END IF;

    stSql := stSql || '
                                         GROUP BY eventos_proventos.cod_contrato
                                               , eventos_proventos.cod_evento
                                               , eventos_proventos.codigo
                                               , eventos_proventos.natureza
                                               , eventos_proventos.descricao
                                 ) as eventos_calculados
                              ON servidor_pensionista.cod_contrato = eventos_calculados.cod_contrato';

    stSqlFiltros := '';

    IF TRIM(stSituacao) != '' THEN
        stSqlFiltros := stSqlFiltros || ' AND recuperarSituacaoDoContrato(servidor_pensionista.cod_contrato,'|| inCodPeriodoMovimentacao ||','|| quote_literal(stEntidade) ||') IN ('|| stSituacaoFormatado ||')';
    END IF;

    IF stCodBancos != '' THEN
        stSqlFiltros := stSqlFiltros || ' AND servidor_pensionista.cod_banco IN ('|| stCodBancos ||')';
    END IF;

    IF stSqlFiltros != '' THEN
        stSqlFiltros := ' WHERE '|| substring(stSqlFiltros FROM 6);
    END IF;

    stSql := stSql || stSqlFiltros;
    stSql := stSql || ' GROUP BY eventos_calculados.cod_evento
                               , eventos_calculados.codigo
                               , eventos_calculados.descricao
                               , eventos_calculados.natureza
                               , agrupamento_banco
                               , agrupamento';


    FOR reRegistro IN EXECUTE stSql LOOP

        IF boElementoDespesa IS TRUE THEN
            stSql := '    SELECT conta_despesa.*
                            FROM folhapagamento'|| stEntidade ||'.configuracao_evento_despesa
                      INNER JOIN (  SELECT cod_evento
                                         , cod_configuracao
                                         , max(timestamp) as timestamp
                                      FROM folhapagamento'|| stEntidade ||'.configuracao_evento_despesa
                                   GROUP BY cod_evento
                                          , cod_configuracao) as max_configuracao_evento_despesa
                              ON max_configuracao_evento_despesa.cod_evento = configuracao_evento_despesa.cod_evento
                             AND max_configuracao_evento_despesa.cod_configuracao = configuracao_evento_despesa.cod_configuracao
                             AND max_configuracao_evento_despesa.timestamp = configuracao_evento_despesa.timestamp
                       LEFT JOIN orcamento.conta_despesa
                              ON conta_despesa.cod_conta = configuracao_evento_despesa.cod_conta
                             AND conta_despesa.exercicio = configuracao_evento_despesa.exercicio
                           WHERE configuracao_evento_despesa.cod_evento = '|| reRegistro.cod_evento ||'
                             AND configuracao_evento_despesa.cod_configuracao = '|| inCodConfiguracao;

                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reDespesa;
                CLOSE crCursor;
                IF reDespesa.cod_estrutural != '' THEN
                    rwTotaisFolha.codigo            := reDespesa.cod_estrutural;
                ELSE
                    rwTotaisFolha.codigo            := reRegistro.codigo;
                END IF;
                IF reDespesa.descricao != '' THEN
                    rwTotaisFolha.descricao         := reDespesa.descricao;
                ELSE
                    rwTotaisFolha.descricao         := reRegistro.descricao;
                END IF;
        ELSE
            rwTotaisFolha.codigo            := reRegistro.codigo;
            rwTotaisFolha.descricao         := reRegistro.descricao;
        END IF;

        rwTotaisFolha.provento          := reRegistro.proventos;
        rwTotaisFolha.desconto          := reRegistro.descontos;
        rwTotaisFolha.agrupamento_banco := reRegistro.agrupamento_banco;
        rwTotaisFolha.agrupamento       := reRegistro.agrupamento;

        RETURN NEXT rwTotaisFolha;
    END LOOP;
END;
$$ LANGUAGE 'plpgsql';
