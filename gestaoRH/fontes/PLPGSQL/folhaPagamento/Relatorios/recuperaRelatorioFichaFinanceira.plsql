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
/* recuperaContratosRelatorioFichaFinanceira
 *
 * Data de Criação : 01/06/2009


 * @author Analista : Dagiane Vieira
 * @author Desenvolvedor : Alex Cardoso

 * @package URBEM
 * @subpackage

 */


CREATE OR REPLACE FUNCTION recuperaContratosRelatorioFichaFinanceira(VARCHAR,INTEGER,INTEGER,INTEGER,INTEGER,VARCHAR,VARCHAR,VARCHAR) RETURNS SETOF colunasContratosRelatorioFichaFinanceira AS $$
DECLARE
    stEntidade                      ALIAS FOR $1;
    inCodConfiguracao               ALIAS FOR $2;
    inCodComplementar               ALIAS FOR $3;
    inCodPeriodoMovimentacaoInicial ALIAS FOR $4;
    inCodPeriodoMovimentacaoFinal   ALIAS FOR $5;
    stTipoFiltro                    ALIAS FOR $6;
    stValoresFiltro                 ALIAS FOR $7;
    stExercicio                     ALIAS FOR $8;
    rwContratosFichaFinanceira      colunasContratosRelatorioFichaFinanceira%ROWTYPE;
    stSql                           VARCHAR;
    stSqlFolhas                     VARCHAR;
    stSqlPeriodoMovimentacao        VARCHAR;
    stSqlEvento                     VARCHAR;
    stSqlFiltros                    VARCHAR;
    reRegistro                      RECORD;
BEGIN

    stSql := '  SELECT *
                  FROM (     SELECT contrato.*
                                  , servidor_contrato_servidor.cod_servidor
                                  , servidor.numcgm
                                  , sw_cgm.nom_cgm
                                  , to_char(contrato_servidor_nomeacao_posse.dt_posse,''dd/mm/yyyy'') as dt_posse
                                  , to_char(contrato_servidor_nomeacao_posse.dt_nomeacao,''dd/mm/yyyy'') as dt_nomeacao
                                  , to_char(contrato_servidor_nomeacao_posse.dt_admissao,''dd/mm/yyyy'') as dt_admissao
                                  , contrato_servidor_orgao.cod_orgao
                                  , recuperadescricaoorgao(contrato_servidor_orgao.cod_orgao,'|| quote_literal(stExercicio ||'-01-01') ||'::date) as desc_orgao
                                  , contrato_servidor_local.cod_local
                                  , local.descricao as desc_local
                                  , cargo.descricao as desc_funcao
                               FROM pessoal'|| stEntidade ||'.servidor
                         INNER JOIN sw_cgm
                                 ON sw_cgm.numcgm = servidor.numcgm
                         INNER JOIN pessoal'|| stEntidade ||'.servidor_contrato_servidor
                                 ON servidor.cod_servidor = servidor_contrato_servidor.cod_servidor
                         INNER JOIN pessoal'|| stEntidade ||'.contrato_servidor
                                 ON servidor_contrato_servidor.cod_contrato = contrato_servidor.cod_contrato
                         INNER JOIN pessoal'|| stEntidade ||'.contrato
                                 ON contrato_servidor.cod_contrato = contrato.cod_contrato
                         INNER JOIN ultimo_contrato_servidor_nomeacao_posse('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacaoFinal ||')
                                 AS contrato_servidor_nomeacao_posse
                                 ON contrato_servidor_nomeacao_posse.cod_contrato = contrato_servidor.cod_contrato
                         INNER JOIN ultimo_contrato_servidor_orgao('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacaoFinal ||')
                                 AS contrato_servidor_orgao
                                 ON contrato_servidor_orgao.cod_contrato = contrato_servidor.cod_contrato
                         INNER JOIN ultimo_contrato_servidor_funcao('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacaoFinal ||')
                                 AS contrato_servidor_funcao
                                 ON contrato_servidor_funcao.cod_contrato = contrato_servidor.cod_contrato
                         INNER JOIN pessoal'|| stEntidade ||'.cargo
                                 ON cargo.cod_cargo = contrato_servidor_funcao.cod_cargo
                          LEFT JOIN ultimo_contrato_servidor_local('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacaoFinal ||')
                                 AS contrato_servidor_local
                                 ON contrato_servidor_local.cod_contrato = contrato_servidor.cod_contrato
                          LEFT JOIN organograma.local
                                 ON local.cod_local = contrato_servidor_local.cod_local

                              UNION

                             SELECT contrato.*
                                  , 0 as cod_servidor
                                  , pensionista.numcgm
                                  , sw_cgm.nom_cgm
                                  , '''' as dt_posse
                                  , '''' as dt_nomeacao
                                  , '''' as dt_admissao
                                  , contrato_pensionista_orgao.cod_orgao
                                  , recuperadescricaoorgao(contrato_pensionista_orgao.cod_orgao,'|| quote_literal(stExercicio ||'-01-01') ||'::date) as desc_orgao
                                  , contrato_servidor_local.cod_local
                                  , local.descricao as desc_local
                                  , cargo.descricao as desc_funcao
                               FROM pessoal'|| stEntidade ||'.pensionista
                         INNER JOIN sw_cgm
                                 ON pensionista.numcgm = sw_cgm.numcgm
                         INNER JOIN pessoal'|| stEntidade ||'.contrato_pensionista
                                 ON contrato_pensionista.cod_pensionista      = pensionista.cod_pensionista
                                AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente
                         INNER JOIN pessoal'|| stEntidade ||'.contrato
                                 ON contrato_pensionista.cod_contrato = contrato.cod_contrato
                         INNER JOIN ultimo_contrato_pensionista_orgao('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacaoFinal ||')
                                 AS contrato_pensionista_orgao
                                 ON contrato_pensionista_orgao.cod_contrato = contrato_pensionista.cod_contrato
                         INNER JOIN ultimo_contrato_servidor_funcao('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacaoFinal ||')
                                 AS contrato_servidor_funcao
                                 ON contrato_servidor_funcao.cod_contrato = contrato_pensionista.cod_contrato_cedente
                         INNER JOIN pessoal'|| stEntidade ||'.cargo
                                 ON cargo.cod_cargo = contrato_servidor_funcao.cod_cargo
                          LEFT JOIN ultimo_contrato_servidor_local('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacaoFinal ||')
                                 AS contrato_servidor_local
                                 ON contrato_servidor_local.cod_contrato = contrato_pensionista.cod_contrato_cedente
                          LEFT JOIN organograma.local
                                 ON local.cod_local = contrato_servidor_local.cod_local
                       ) AS contratos';


    IF inCodPeriodoMovimentacaoInicial <> inCodPeriodoMovimentacaoFinal THEN
        stSqlPeriodoMovimentacao := ' AND cod_periodo_movimentacao BETWEEN '|| inCodPeriodoMovimentacaoInicial ||' AND '|| inCodPeriodoMovimentacaoFinal;
    ELSE
        stSqlPeriodoMovimentacao := ' AND cod_periodo_movimentacao = '|| inCodPeriodoMovimentacaoFinal;
    END IF;

    stSqlEvento  := '';
    stSqlFiltros := '';

    IF (stTipoFiltro = 'contrato_todos' OR
        stTipoFiltro = 'cgm_contrato_todos') THEN
        stSqlFiltros := ' AND cod_contrato IN ('|| stValoresFiltro ||')';
    ELSIF stTipoFiltro = 'lotacao' THEN
        stSqlFiltros := ' AND cod_orgao IN ('|| stValoresFiltro ||')';
    ELSIF stTipoFiltro = 'local' THEN
        stSqlFiltros := ' AND cod_local IN ('|| stValoresFiltro ||')';
    ELSIF stTipoFiltro = 'evento' THEN
        stSqlEvento := ' AND evento.cod_evento IN ('|| stValoresFiltro ||')';
    END IF;

    stSqlFolhas := '';

    IF inCodConfiguracao IS NULL OR
       inCodConfiguracao = 0 THEN

        stSqlFolhas := stSqlFolhas ||'  SELECT cod_contrato
                                         FROM folhapagamento'|| stEntidade ||'.registro_evento_complementar
                                            , folhapagamento'|| stEntidade ||'.evento_complementar_calculado
                                            , folhapagamento'|| stEntidade ||'.evento
                                        WHERE registro_evento_complementar.cod_registro = evento_complementar_calculado.cod_registro';

        IF inCodConfiguracao = 0 THEN
            stSqlFolhas := stSqlFolhas ||'
                                          AND registro_evento_complementar.cod_complementar = '|| inCodComplementar;
        END IF;

        stSqlFolhas := stSqlFolhas ||'     AND evento_complementar_calculado.cod_evento  = evento.cod_evento
                                          '|| stSqlPeriodoMovimentacao ||'
                                          '|| stSqlEvento ||'
                                     GROUP BY cod_contrato';

        IF inCodConfiguracao IS NULL THEN
            stSqlFolhas := stSqlFolhas ||' UNION ';
        END IF;
    END IF;

    IF inCodConfiguracao IS NULL OR
       inCodConfiguracao = 1 THEN

        stSqlFolhas := stSqlFolhas ||'  SELECT cod_contrato
                                         FROM folhapagamento'|| stEntidade ||'.registro_evento_periodo
                                            , folhapagamento'|| stEntidade ||'.evento_calculado
                                            , folhapagamento'|| stEntidade ||'.evento
                                        WHERE registro_evento_periodo.cod_registro = evento_calculado.cod_registro
                                          AND evento_calculado.cod_evento          = evento.cod_evento
                                          '|| stSqlPeriodoMovimentacao ||'
                                          '|| stSqlEvento ||'
                                     GROUP BY cod_contrato';

        IF inCodConfiguracao IS NULL THEN
            stSqlFolhas := stSqlFolhas ||' UNION ';
        END IF;
    END IF;

    IF inCodConfiguracao IS NULL OR
       inCodConfiguracao = 2 THEN

        stSqlFolhas := stSqlFolhas ||'  SELECT cod_contrato
                                         FROM folhapagamento'|| stEntidade ||'.registro_evento_ferias
                                            , folhapagamento'|| stEntidade ||'.evento_ferias_calculado
                                            , folhapagamento'|| stEntidade ||'.evento
                                        WHERE registro_evento_ferias.cod_registro = evento_ferias_calculado.cod_registro
                                          AND evento_ferias_calculado.cod_evento  = evento.cod_evento
                                          '|| stSqlPeriodoMovimentacao ||'
                                          '|| stSqlEvento ||'
                                     GROUP BY cod_contrato';

        IF inCodConfiguracao IS NULL THEN
            stSqlFolhas := stSqlFolhas ||' UNION ';
        END IF;
    END IF;

    IF inCodConfiguracao IS NULL OR
       inCodConfiguracao = 3 THEN

        stSqlFolhas := stSqlFolhas ||'  SELECT cod_contrato
                                         FROM folhapagamento'|| stEntidade ||'.registro_evento_decimo
                                            , folhapagamento'|| stEntidade ||'.evento_decimo_calculado
                                            , folhapagamento'|| stEntidade ||'.evento
                                        WHERE registro_evento_decimo.cod_registro = evento_decimo_calculado.cod_registro
                                          AND evento_decimo_calculado.cod_evento  = evento.cod_evento
                                          '|| stSqlPeriodoMovimentacao ||'
                                          '|| stSqlEvento ||'
                                     GROUP BY cod_contrato';

        IF inCodConfiguracao IS NULL THEN
            stSqlFolhas := stSqlFolhas ||' UNION ';
        END IF;
    END IF;

    IF inCodConfiguracao IS NULL OR
       inCodConfiguracao = 4 THEN

        stSqlFolhas := stSqlFolhas ||'  SELECT cod_contrato
                                         FROM folhapagamento'|| stEntidade ||'.registro_evento_rescisao
                                            , folhapagamento'|| stEntidade ||'.evento_rescisao_calculado
                                            , folhapagamento'|| stEntidade ||'.evento
                                        WHERE registro_evento_rescisao.cod_registro = evento_rescisao_calculado.cod_registro
                                          AND evento_rescisao_calculado.cod_evento  = evento.cod_evento
                                          '|| stSqlPeriodoMovimentacao ||'
                                          '|| stSqlEvento ||'
                                     GROUP BY cod_contrato';
    END IF;

    stSql := stSql || ' WHERE contratos.cod_contrato IN (
                                                          '|| stSqlFolhas ||'
                                                        )';
    stSql := stSql||stSqlFiltros;
    RAISE NOTICE 'stSql: %', stSql;
    FOR reRegistro IN EXECUTE stSql LOOP
        rwContratosFichaFinanceira.cod_contrato := reRegistro.cod_contrato;
        rwContratosFichaFinanceira.registro     := reRegistro.registro;
        rwContratosFichaFinanceira.cod_servidor := reRegistro.cod_servidor;
        rwContratosFichaFinanceira.numcgm       := reRegistro.numcgm;
        rwContratosFichaFinanceira.nom_cgm      := reRegistro.nom_cgm;
        rwContratosFichaFinanceira.dt_posse     := reRegistro.dt_posse;
        rwContratosFichaFinanceira.dt_nomeacao  := reRegistro.dt_nomeacao;
        rwContratosFichaFinanceira.dt_admissao  := reRegistro.dt_admissao;
        rwContratosFichaFinanceira.cod_orgao    := reRegistro.cod_orgao;
        rwContratosFichaFinanceira.desc_orgao   := reRegistro.desc_orgao;
        rwContratosFichaFinanceira.cod_local    := reRegistro.cod_local;
        rwContratosFichaFinanceira.desc_local   := reRegistro.desc_local;
        rwContratosFichaFinanceira.desc_funcao  := reRegistro.desc_funcao;

        RETURN NEXT rwContratosFichaFinanceira;
    END LOOP;

END;
$$ LANGUAGE 'plpgsql';



CREATE OR REPLACE FUNCTION recuperaOcorrenciasCalculoRelatorioFichaFinanceira(VARCHAR,INTEGER,INTEGER,INTEGER,INTEGER,INTEGER) RETURNS SETOF colunasOcorrenciasCalculoRelatorioFichaFinanceira AS $$
DECLARE
    stEntidade                              ALIAS FOR $1;
    inCodConfiguracao                       ALIAS FOR $2;
    inCodComplementar                       ALIAS FOR $3;
    inCodPeriodoMovimentacaoInicial         ALIAS FOR $4;
    inCodPeriodoMovimentacaofinal           ALIAS FOR $5;
    inCodContrato                           ALIAS FOR $6;
    stSql                                   VARCHAR;
    reRegistro                              RECORD;
    rwOcorrenciasCalculoFichaFinanceira     colunasOcorrenciasCalculoRelatorioFichaFinanceira%ROWTYPE;

BEGIN

    stSql := '';

    IF inCodConfiguracao IS NULL OR
       inCodConfiguracao = 0 THEN

        stSql := stSql ||'   SELECT cod_periodo_movimentacao
                                 , 0 as cod_configuracao
                                 , cod_complementar
                              FROM folhapagamento'|| stEntidade ||'.registro_evento_complementar
                             WHERE cod_periodo_movimentacao BETWEEN '|| inCodPeriodoMovimentacaoInicial ||' AND '|| inCodPeriodoMovimentacaoFinal ||'
                               AND cod_contrato = '|| inCodContrato;

        IF inCodConfiguracao = 0 THEN
            stSql := stSql ||'  AND cod_complementar = '|| inCodComplementar;
        END IF;

        stSql := stSql ||'      AND EXISTS (SELECT 1
                                             FROM folhapagamento'|| stEntidade ||'.evento_complementar_calculado
                                            WHERE evento_complementar_calculado.cod_registro       = registro_evento_complementar.cod_registro
                                              AND evento_complementar_calculado.cod_evento         = registro_evento_complementar.cod_evento
                                              AND evento_complementar_calculado.cod_configuracao   = registro_evento_complementar.cod_configuracao
                                              AND evento_complementar_calculado.timestamp_registro = registro_evento_complementar.timestamp )
                          GROUP BY cod_periodo_movimentacao
                                 , cod_complementar';

        IF inCodConfiguracao IS NULL THEN
            stSql := stSql ||' UNION ';
        END IF;
    END IF;

    IF inCodConfiguracao IS NULL OR
       inCodConfiguracao = 1 THEN

        stSql := stSql ||'   SELECT registro_evento_periodo.cod_periodo_movimentacao
                                 , 1 as cod_configuracao
                                 , null::integer as cod_complementar
                              FROM folhapagamento'|| stEntidade ||'.registro_evento_periodo
                             WHERE cod_periodo_movimentacao BETWEEN '|| inCodPeriodoMovimentacaoInicial ||' AND '|| inCodPeriodoMovimentacaoFinal ||'
                               AND cod_contrato = '|| inCodContrato ||'
                               AND EXISTS (SELECT 1
                                             FROM folhapagamento'|| stEntidade ||'.evento_calculado
                                            WHERE evento_calculado.cod_registro = registro_evento_periodo.cod_registro )
                          GROUP BY cod_periodo_movimentacao';

        IF inCodConfiguracao IS NULL THEN
            stSql := stSql ||' UNION ';
        END IF;
    END IF;


    IF inCodConfiguracao IS NULL OR
       inCodConfiguracao = 2 THEN

        stSql := stSql ||'   SELECT cod_periodo_movimentacao
                                 , 2 as cod_configuracao
                                 , null::integer as cod_complementar
                              FROM folhapagamento'|| stEntidade ||'.registro_evento_ferias
                             WHERE cod_periodo_movimentacao BETWEEN '|| inCodPeriodoMovimentacaoInicial ||' AND '|| inCodPeriodoMovimentacaoFinal ||'
                               AND cod_contrato = '|| inCodContrato ||'
                               AND EXISTS ( SELECT 1
                                              FROM folhapagamento'|| stEntidade ||'.evento_ferias_calculado
                                             WHERE evento_ferias_calculado.cod_registro       = registro_evento_ferias.cod_registro
                                               AND evento_ferias_calculado.cod_evento         = registro_evento_ferias.cod_evento
                                               AND evento_ferias_calculado.desdobramento      = registro_evento_ferias.desdobramento
                                               AND evento_ferias_calculado.timestamp_registro = registro_evento_ferias.timestamp )
                          GROUP BY cod_periodo_movimentacao';

        IF inCodConfiguracao IS NULL THEN
            stSql := stSql ||' UNION ';
        END IF;
    END IF;


    IF inCodConfiguracao IS NULL OR
       inCodConfiguracao = 3 THEN

        stSql := stSql ||'   SELECT cod_periodo_movimentacao
                                 , 3 as cod_configuracao
                                 , null::integer as cod_complementar
                              FROM folhapagamento'|| stEntidade ||'.registro_evento_decimo
                             WHERE cod_periodo_movimentacao BETWEEN '|| inCodPeriodoMovimentacaoInicial ||' AND '|| inCodPeriodoMovimentacaoFinal ||'
                               AND cod_contrato = '|| inCodContrato ||'
                               AND EXISTS ( SELECT 1
                                              FROM folhapagamento'|| stEntidade ||'.evento_decimo_calculado
                                             WHERE evento_decimo_calculado.cod_registro        = registro_evento_decimo.cod_registro
                                               AND evento_decimo_calculado.cod_evento          = registro_evento_decimo.cod_evento
                                               AND evento_decimo_calculado.desdobramento       = registro_evento_decimo.desdobramento
                                               AND evento_decimo_calculado.timestamp_registro  = registro_evento_decimo.timestamp )
                          GROUP BY cod_periodo_movimentacao';

        IF inCodConfiguracao IS NULL THEN
            stSql := stSql ||' UNION ';
        END IF;
    END IF;

    IF inCodConfiguracao IS NULL OR
       inCodConfiguracao = 4 THEN

        stSql := stSql ||'   SELECT cod_periodo_movimentacao
                                 , 4 as cod_configuracao
                                 , null::integer as cod_complementar
                              FROM folhapagamento'|| stEntidade ||'.registro_evento_rescisao
                             WHERE cod_periodo_movimentacao BETWEEN '|| inCodPeriodoMovimentacaoInicial ||' AND '|| inCodPeriodoMovimentacaoFinal ||'
                               AND cod_contrato = '|| inCodContrato ||'
                               AND EXISTS ( SELECT 1
                                              FROM folhapagamento'|| stEntidade ||'.evento_rescisao_calculado
                                             WHERE evento_rescisao_calculado.cod_registro        = registro_evento_rescisao.cod_registro
                                               AND evento_rescisao_calculado.cod_evento          = registro_evento_rescisao.cod_evento
                                               AND evento_rescisao_calculado.desdobramento       = registro_evento_rescisao.desdobramento
                                               AND evento_rescisao_calculado.timestamp_registro  = registro_evento_rescisao.timestamp )
                          GROUP BY cod_periodo_movimentacao';
    END IF;


    stSql := 'SELECT *
                   , publico.fn_mes_extenso(periodo_movimentacao.dt_final)||''/''|| to_char(periodo_movimentacao.dt_final, ''yyyy'') AS descricao_periodo
                   , CASE WHEN ocorrencias_calculo_periodo.cod_configuracao <> 0 THEN
                          (SELECT ''Folha ''|| descricao FROM folhapagamento'|| stEntidade ||'.configuracao_evento WHERE cod_configuracao = ocorrencias_calculo_periodo.cod_configuracao)
                     ELSE
                          ''Folha Complementar - ''|| ocorrencias_calculo_periodo.cod_configuracao
                     END AS descricao_configuracao
                FROM (
                       '|| stSql ||'
                     ) as ocorrencias_calculo_periodo
          INNER JOIN folhapagamento'|| stEntidade ||'.periodo_movimentacao
                  ON periodo_movimentacao.cod_periodo_movimentacao = ocorrencias_calculo_periodo.cod_periodo_movimentacao
            ORDER BY ocorrencias_calculo_periodo.cod_periodo_movimentacao
                   , ocorrencias_calculo_periodo.cod_configuracao
                   , ocorrencias_calculo_periodo.cod_complementar';

    FOR reRegistro IN EXECUTE stSql LOOP
        rwOcorrenciasCalculoFichaFinanceira.cod_periodo_movimentacao    :=  reRegistro.cod_periodo_movimentacao;
        rwOcorrenciasCalculoFichaFinanceira.cod_configuracao            :=  reRegistro.cod_configuracao;
        rwOcorrenciasCalculoFichaFinanceira.cod_complementar            :=  reRegistro.cod_complementar;
        rwOcorrenciasCalculoFichaFinanceira.descricao_periodo           :=  reRegistro.descricao_periodo;
        rwOcorrenciasCalculoFichaFinanceira.descricao_configuracao      :=  reRegistro.descricao_configuracao;

        RETURN NEXT rwOcorrenciasCalculoFichaFinanceira;
    END LOOP;

END;
$$ LANGUAGE 'plpgsql';



CREATE OR REPLACE FUNCTION recuperaTotaisValoresRelatorioFichaFinanceira(VARCHAR,INTEGER,INTEGER,INTEGER,INTEGER,INTEGER,VARCHAR) RETURNS SETOF colunasTotaisValoresRelatorioFichaFinanceira AS $$
DECLARE
    stEntidade                      ALIAS FOR $1;
    inCodConfiguracao               ALIAS FOR $2;
    inCodComplementar               ALIAS FOR $3;
    inCodPeriodoMovimentacaoInicial ALIAS FOR $4;
    inCodPeriodoMovimentacaoFinal   ALIAS FOR $5;
    inCodContrato                   ALIAS FOR $6;
    stOrdenacao                     VARCHAR := $7;
    stSql                           VARCHAR;
    stSqlOcorrenciasComplementares  VARCHAR;
    reRegistro                      RECORD;
    rwTotaisValoresFichaFinanceira  colunasTotaisValoresRelatorioFichaFinanceira%ROWTYPE;

BEGIN


    stSql := '';
    stSqlOcorrenciasComplementares := '';

    IF stOrdenacao IS NULL OR
       trim(stOrdenacao) = '' THEN
         stOrdenacao := 'codigo';
    END IF;

    IF inCodConfiguracao IS NULL OR
       inCodConfiguracao = 0 THEN

        stSqlOcorrenciasComplementares := '  SELECT cod_complementar
                                               FROM folhapagamento'|| stEntidade ||'.complementar
                                              WHERE cod_periodo_movimentacao BETWEEN '|| inCodPeriodoMovimentacaoInicial ||' AND '|| inCodPeriodoMovimentacaoFinal;

        IF inCodConfiguracao = 0 THEN
            stSqlOcorrenciasComplementares := stSqlOcorrenciasComplementares ||'  AND cod_complementar = '|| inCodComplementar;
        END IF;

        stSqlOcorrenciasComplementares := stSqlOcorrenciasComplementares ||' GROUP BY cod_complementar';

        FOR reRegistro IN EXECUTE stSqlOcorrenciasComplementares LOOP

            stSql := stSql ||'SELECT cod_evento
                                  , codigo
                                  , descricao
                                  , natureza
                                  , quantidade
                                  , desdobramento_texto as desdobramento
                                  , sequencia
                                  , CASE WHEN natureza = ''P'' THEN
                                        valor
                                    ELSE
                                        0
                                    END as proventos
                                  , CASE WHEN natureza = ''D'' THEN
                                        valor
                                    ELSE
                                        0
                                    END as descontos
                                  , CASE WHEN natureza IN (''B'',''I'') THEN
                                        valor
                                    ELSE
                                        0
                                    END as valor
                                  , CASE WHEN natureza IN (''P'',''D'') THEN
                                        1
                                    ELSE
                                        2
                                    END as ordem_por_natureza
                               FROM recuperarEventosCalculadosIntervalo(0, '|| inCodPeriodoMovimentacaoInicial ||', '|| inCodPeriodoMovimentacaoFinal ||', '|| inCodContrato ||', '|| reRegistro.cod_complementar ||', '|| quote_literal(stEntidade) ||', '''')';

            IF inCodConfiguracao IS NULL THEN
                stSql := stSql ||' UNION ALL ';
            END IF;

        END LOOP;

    END IF;

    IF inCodConfiguracao IS NULL OR
       inCodConfiguracao = 1 THEN

        stSql := stSql ||'SELECT cod_evento
                              , codigo
                              , descricao
                              , natureza
                              , quantidade
                              , desdobramento_texto as desdobramento
                              , sequencia
                              , CASE WHEN natureza = ''P'' THEN
                                    valor
                                ELSE
                                    0
                                END as proventos
                              , CASE WHEN natureza = ''D'' THEN
                                    valor
                                ELSE
                                    0
                                END as descontos
                              , CASE WHEN natureza IN (''B'',''I'') THEN
                                    valor
                                ELSE
                                    0
                                END as valor
                              , CASE WHEN natureza IN (''P'',''D'') THEN
                                    1
                                ELSE
                                    2
                                END as ordem_por_natureza
                           FROM recuperarEventosCalculadosIntervalo(1, '|| inCodPeriodoMovimentacaoInicial ||', '|| inCodPeriodoMovimentacaoFinal ||', '|| inCodContrato ||', '|| inCodComplementar ||', '|| quote_literal(stEntidade) ||', '''')';

        IF inCodConfiguracao IS NULL THEN
            stSql := stSql ||' UNION ALL ';
        END IF;
    END IF;

    IF inCodConfiguracao IS NULL OR
       inCodConfiguracao = 2 THEN

        stSql := stSql ||'SELECT cod_evento
                              , codigo
                              , descricao
                              , natureza
                              , quantidade
                              , desdobramento_texto as desdobramento
                              , sequencia
                              , CASE WHEN natureza = ''P'' THEN
                                    valor
                                ELSE
                                    0
                                END as proventos
                              , CASE WHEN natureza = ''D'' THEN
                                    valor
                                ELSE
                                    0
                                END as descontos
                              , CASE WHEN natureza IN (''B'',''I'') THEN
                                    valor
                                ELSE
                                    0
                                END as valor
                              , CASE WHEN natureza IN (''P'',''D'') THEN
                                    1
                                ELSE
                                    2
                                END as ordem_por_natureza
                           FROM recuperarEventosCalculadosIntervalo(2, '|| inCodPeriodoMovimentacaoInicial ||', '|| inCodPeriodoMovimentacaoFinal ||', '|| inCodContrato ||', '|| inCodComplementar ||', '|| quote_literal(stEntidade) ||', '''')';

        IF inCodConfiguracao IS NULL THEN
            stSql := stSql ||' UNION ALL ';
        END IF;
    END IF;



    IF inCodConfiguracao IS NULL OR
       inCodConfiguracao = 3 THEN

        stSql := stSql ||'SELECT cod_evento
                              , codigo
                              , descricao
                              , natureza
                              , quantidade
                              , desdobramento_texto as desdobramento
                              , sequencia
                              , CASE WHEN natureza = ''P'' THEN
                                    valor
                                ELSE
                                    0
                                END as proventos
                              , CASE WHEN natureza = ''D'' THEN
                                    valor
                                ELSE
                                    0
                                END as descontos
                              , CASE WHEN natureza IN (''B'',''I'') THEN
                                    valor
                                ELSE
                                    0
                                END as valor
                              , CASE WHEN natureza IN (''P'',''D'') THEN
                                    1
                                ELSE
                                    2
                                END as ordem_por_natureza
                           FROM recuperarEventosCalculadosIntervalo(3, '|| inCodPeriodoMovimentacaoInicial ||', '|| inCodPeriodoMovimentacaoFinal ||', '|| inCodContrato ||', '|| inCodComplementar ||', '|| quote_literal(stEntidade) ||', '''')';

        IF inCodConfiguracao IS NULL THEN
            stSql := stSql ||' UNION ALL ';
        END IF;
    END IF;

    IF inCodConfiguracao IS NULL OR
       inCodConfiguracao = 4 THEN

        stSql := stSql ||'SELECT cod_evento
                              , codigo
                              , descricao
                              , natureza
                              , quantidade
                              , desdobramento_texto as desdobramento
                              , sequencia
                              , CASE WHEN natureza = ''P'' THEN
                                    valor
                                ELSE
                                    0
                                END as proventos
                              , CASE WHEN natureza = ''D'' THEN
                                    valor
                                ELSE
                                    0
                                END as descontos
                              , CASE WHEN natureza IN (''B'',''I'') THEN
                                    valor
                                ELSE
                                    0
                                END as valor
                              , CASE WHEN natureza IN (''P'',''D'') THEN
                                    1
                                ELSE
                                    2
                                END as ordem_por_natureza
                           FROM recuperarEventosCalculadosIntervalo(4, '|| inCodPeriodoMovimentacaoInicial ||', '|| inCodPeriodoMovimentacaoFinal ||', '|| inCodContrato ||', '|| inCodComplementar ||', '|| quote_literal(stEntidade) ||', '''')';
    END IF;

    stSql := 'SELECT cod_evento
                   , codigo as codigo_evento
                   , descricao as descricao_evento
                   , natureza as natureza_evento
                   , sum(quantidade) as quantidade
                   , desdobramento
                   , sequencia
                   , sum(proventos) as proventos
                   , sum(descontos) as descontos
                   , sum(valor) as valor
                   , ordem_por_natureza
                FROM ('|| stSql ||'
                     ) as eventos_calculados_todas_folhas
               GROUP BY cod_evento
                      , codigo_evento
                      , descricao_evento
                      , natureza_evento
                      , desdobramento
                      , sequencia
                      , ordem_por_natureza
               ORDER BY ordem_por_natureza, '|| stOrdenacao;

    FOR reRegistro IN EXECUTE stSql LOOP

        rwTotaisValoresFichaFinanceira.codigo_evento     := reRegistro.codigo_evento;
        rwTotaisValoresFichaFinanceira.descricao_evento  := reRegistro.descricao_evento;
        rwTotaisValoresFichaFinanceira.natureza_evento   := reRegistro.natureza_evento;
        rwTotaisValoresFichaFinanceira.quantidade        := reRegistro.quantidade;
        rwTotaisValoresFichaFinanceira.desdobramento     := reRegistro.desdobramento;
        rwTotaisValoresFichaFinanceira.proventos         := reRegistro.proventos;
        rwTotaisValoresFichaFinanceira.descontos         := reRegistro.descontos;
        rwTotaisValoresFichaFinanceira.valor             := reRegistro.valor;

        RETURN NEXT rwTotaisValoresFichaFinanceira;
    END LOOP;

END;
$$ LANGUAGE 'plpgsql';


CREATE OR REPLACE FUNCTION recuperaNovoRelatorioFichaFinanceira(VARCHAR,INTEGER,INTEGER,INTEGER,INTEGER,INTEGER,VARCHAR) RETURNS SETOF novoRelatorioFichaFinanceira AS $$
DECLARE
    stEntidade                              ALIAS FOR $1;
    inCodConfiguracao                       ALIAS FOR $2;
    inCodComplementar                       ALIAS FOR $3;
    inCodPeriodoMovimentacaoInicial         ALIAS FOR $4;
    inCodPeriodoMovimentacaofinal           ALIAS FOR $5;
    inCodContrato                           ALIAS FOR $6;
    stOrdemEventos                          ALIAS FOR $7;
    stSql                                   VARCHAR;
    stSqlAux                                VARCHAR;
    reRegistro                              RECORD;
    reValoresEventos                        RECORD;
    reRegistroAux                           novoRelatorioFichaFinanceira%ROWTYPE;

BEGIN

    stSql := '';

    IF inCodConfiguracao IS NULL OR
       inCodConfiguracao = 0 THEN

        stSql := stSql ||'   SELECT cod_periodo_movimentacao
                                 , 0 as cod_configuracao
                                 , cod_complementar
                              FROM folhapagamento'|| stEntidade ||'.registro_evento_complementar
                             WHERE cod_periodo_movimentacao BETWEEN '|| inCodPeriodoMovimentacaoInicial ||' AND '|| inCodPeriodoMovimentacaoFinal ||'
                               AND cod_contrato = '|| inCodContrato;

        IF inCodConfiguracao = 0 THEN
            stSql := stSql ||'  AND cod_complementar = '|| inCodComplementar;
        END IF;

        stSql := stSql ||'      AND EXISTS (SELECT 1
                                             FROM folhapagamento'|| stEntidade ||'.evento_complementar_calculado
                                            WHERE evento_complementar_calculado.cod_registro       = registro_evento_complementar.cod_registro
                                              AND evento_complementar_calculado.cod_evento         = registro_evento_complementar.cod_evento
                                              AND evento_complementar_calculado.cod_configuracao   = registro_evento_complementar.cod_configuracao
                                              AND evento_complementar_calculado.timestamp_registro = registro_evento_complementar.timestamp )
                          GROUP BY cod_periodo_movimentacao
                                 , cod_complementar';

        IF inCodConfiguracao IS NULL THEN
            stSql := stSql ||' UNION ';
        END IF;
    END IF;

    IF inCodConfiguracao IS NULL OR
       inCodConfiguracao = 1 THEN

        stSql := stSql ||'   SELECT registro_evento_periodo.cod_periodo_movimentacao
                                 , 1 as cod_configuracao
                                 , null::integer as cod_complementar
                              FROM folhapagamento'|| stEntidade ||'.registro_evento_periodo
                             WHERE cod_periodo_movimentacao BETWEEN '|| inCodPeriodoMovimentacaoInicial ||' AND '|| inCodPeriodoMovimentacaoFinal ||'
                               AND cod_contrato = '|| inCodContrato ||'
                               AND EXISTS (SELECT 1
                                             FROM folhapagamento'|| stEntidade ||'.evento_calculado
                                            WHERE evento_calculado.cod_registro = registro_evento_periodo.cod_registro )
                          GROUP BY cod_periodo_movimentacao';

        IF inCodConfiguracao IS NULL THEN
            stSql := stSql ||' UNION ';
        END IF;
    END IF;


    IF inCodConfiguracao IS NULL OR
       inCodConfiguracao = 2 THEN

        stSql := stSql ||'   SELECT cod_periodo_movimentacao
                                 , 2 as cod_configuracao
                                 , null::integer as cod_complementar
                              FROM folhapagamento'|| stEntidade ||'.registro_evento_ferias
                             WHERE cod_periodo_movimentacao BETWEEN '|| inCodPeriodoMovimentacaoInicial ||' AND '|| inCodPeriodoMovimentacaoFinal ||'
                               AND cod_contrato = '|| inCodContrato ||'
                               AND EXISTS ( SELECT 1
                                              FROM folhapagamento'|| stEntidade ||'.evento_ferias_calculado
                                             WHERE evento_ferias_calculado.cod_registro       = registro_evento_ferias.cod_registro
                                               AND evento_ferias_calculado.cod_evento         = registro_evento_ferias.cod_evento
                                               AND evento_ferias_calculado.desdobramento      = registro_evento_ferias.desdobramento
                                               AND evento_ferias_calculado.timestamp_registro = registro_evento_ferias.timestamp )
                          GROUP BY cod_periodo_movimentacao';

        IF inCodConfiguracao IS NULL THEN
            stSql := stSql ||' UNION ';
        END IF;
    END IF;


    IF inCodConfiguracao IS NULL OR
       inCodConfiguracao = 3 THEN

        stSql := stSql ||'   SELECT cod_periodo_movimentacao
                                 , 3 as cod_configuracao
                                 , null::integer as cod_complementar
                              FROM folhapagamento'|| stEntidade ||'.registro_evento_decimo
                             WHERE cod_periodo_movimentacao BETWEEN '|| inCodPeriodoMovimentacaoInicial ||' AND '|| inCodPeriodoMovimentacaoFinal ||'
                               AND cod_contrato = '|| inCodContrato ||'
                               AND EXISTS ( SELECT 1
                                              FROM folhapagamento'|| stEntidade ||'.evento_decimo_calculado
                                             WHERE evento_decimo_calculado.cod_registro        = registro_evento_decimo.cod_registro
                                               AND evento_decimo_calculado.cod_evento          = registro_evento_decimo.cod_evento
                                               AND evento_decimo_calculado.desdobramento       = registro_evento_decimo.desdobramento
                                               AND evento_decimo_calculado.timestamp_registro  = registro_evento_decimo.timestamp )
                          GROUP BY cod_periodo_movimentacao';

        IF inCodConfiguracao IS NULL THEN
            stSql := stSql ||' UNION ';
        END IF;
    END IF;

    IF inCodConfiguracao IS NULL OR
       inCodConfiguracao = 4 THEN

        stSql := stSql ||'   SELECT cod_periodo_movimentacao
                                 , 4 as cod_configuracao
                                 , null::integer as cod_complementar
                              FROM folhapagamento'|| stEntidade ||'.registro_evento_rescisao
                             WHERE cod_periodo_movimentacao BETWEEN '|| inCodPeriodoMovimentacaoInicial ||' AND '|| inCodPeriodoMovimentacaoFinal ||'
                               AND cod_contrato = '|| inCodContrato ||'
                               AND EXISTS ( SELECT 1
                                              FROM folhapagamento'|| stEntidade ||'.evento_rescisao_calculado
                                             WHERE evento_rescisao_calculado.cod_registro        = registro_evento_rescisao.cod_registro
                                               AND evento_rescisao_calculado.cod_evento          = registro_evento_rescisao.cod_evento
                                               AND evento_rescisao_calculado.desdobramento       = registro_evento_rescisao.desdobramento
                                               AND evento_rescisao_calculado.timestamp_registro  = registro_evento_rescisao.timestamp )
                          GROUP BY cod_periodo_movimentacao';
    END IF;

    stSql := 'SELECT *
                   , publico.fn_mes_extenso(periodo_movimentacao.dt_final)||''/''|| to_char(periodo_movimentacao.dt_final, ''yyyy'') AS descricao_periodo
                   , CASE WHEN ocorrencias_calculo_periodo.cod_configuracao <> 0 THEN
                          (SELECT ''Folha ''|| descricao FROM folhapagamento'|| stEntidade ||'.configuracao_evento WHERE cod_configuracao = ocorrencias_calculo_periodo.cod_configuracao)
                     ELSE
                          ''Folha Complementar - ''|| ocorrencias_calculo_periodo.cod_complementar
                     END AS descricao_configuracao
                FROM (
                       '|| stSql ||'
                     ) as ocorrencias_calculo_periodo
          INNER JOIN folhapagamento'|| stEntidade ||'.periodo_movimentacao
                  ON periodo_movimentacao.cod_periodo_movimentacao = ocorrencias_calculo_periodo.cod_periodo_movimentacao
            ORDER BY ocorrencias_calculo_periodo.cod_periodo_movimentacao
                   , ocorrencias_calculo_periodo.cod_configuracao
                   , ocorrencias_calculo_periodo.cod_complementar';

    FOR reRegistro IN EXECUTE stSql LOOP
        IF (reRegistro.cod_complementar IS NULL) THEN reRegistro.cod_complementar := 0; END IF;
        stSqlAux := 'SELECT codigo as codigo_evento
                          , descricao as descricao_evento
                          , natureza::varchar as natureza_evento
                          , desdobramento_texto as desdobramento
                          , quantidade
                          , CASE WHEN natureza = ''P'' THEN valor ELSE 0 END AS proventos
                          , CASE WHEN natureza = ''D'' THEN valor ELSE 0 END AS descontos
                          , CASE WHEN natureza IN (''B'',''I'') THEN valor ELSE 0 END AS valor
                          , CASE WHEN natureza IN (''P'',''D'') THEN 1 ELSE 2 END AS ordem_por_natureza
                     FROM recuperarEventosCalculados('||reRegistro.cod_configuracao||',
                                                     '||reRegistro.cod_periodo_movimentacao||',
                                                     '||inCodContrato||',
                                                     '||reRegistro.cod_complementar||',
                                                     '''||stEntidade||''',
                                                     ''''
                                                    )
                     ORDER BY desdobramento DESC, ordem_por_natureza, '||stOrdemEventos||'';

        FOR reValoresEventos IN EXECUTE stSqlAux LOOP
            reRegistroAux.cod_periodo_movimentacao  :=  reRegistro.cod_periodo_movimentacao;
            reRegistroAux.cod_configuracao          :=  reRegistro.cod_configuracao;
            reRegistroAux.cod_complementar          :=  reRegistro.cod_complementar;
            reRegistroAux.descricao_periodo         :=  reRegistro.descricao_periodo;
            reRegistroAux.descricao_configuracao    :=  reRegistro.descricao_configuracao;
            reRegistroAux.codigo_evento             :=  reValoresEventos.codigo_evento;
            reRegistroAux.descricao_evento          :=  reValoresEventos.descricao_evento;
            reRegistroAux.natureza_evento           :=  reValoresEventos.natureza_evento;
            reRegistroAux.desdobramento             :=  reValoresEventos.desdobramento;
            reRegistroAux.quantidade                :=  reValoresEventos.quantidade;
            reRegistroAux.proventos                 :=  reValoresEventos.proventos;
            reRegistroAux.descontos                 :=  reValoresEventos.descontos;
            reRegistroAux.valor                     :=  reValoresEventos.valor;
            reRegistroAux.ordem_por_natureza        :=  reValoresEventos.ordem_por_natureza;

            RETURN NEXT reRegistroAux;
        END LOOP;
    END LOOP;
END;
$$ LANGUAGE 'plpgsql';
