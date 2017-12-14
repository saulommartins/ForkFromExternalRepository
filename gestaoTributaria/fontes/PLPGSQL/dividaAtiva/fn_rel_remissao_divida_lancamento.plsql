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
CREATE OR REPLACE FUNCTION divida.fn_rel_remissao_divida(
      stCreditos        VARCHAR
    , boGrupo           BOOLEAN
) RETURNS SETOF RECORD AS $$
DECLARE
    stQuery                 VARCHAR;
    stFiltro                VARCHAR;
    stFiltroDivida          VARCHAR;
    stCodigos               VARCHAR[];
    stCodigosDesmembrados   VARCHAR[];
    stInsert                VARCHAR;
    reRegistro              RECORD;
    inInscricao             INTEGER;
    inIndex                 INTEGER;
    inNumCGM                INTEGER;
    stNomCGM                VARCHAR;
    inCodCalculo            INTEGER;
    inCodGrupo              INTEGER;
    stExercicio             CHAR(4);
    stDescricao             VARCHAR(80);
    inCodInscricao          INTEGER;
    stExercicioDA           CHAR(4);
    stDtInscricaoDA         VARCHAR;
    nuValor                 NUMERIC(14,2);   
    inCodLancamento         INTEGER;
    flUltimoRegistro        BOOLEAN;
    stTabela                VARCHAR;
BEGIN
    stTabela:= TO_CHAR(NOW(),'ddmmyyyyhhmiss');
    stFiltroDivida :='LEFT';
    
    stQuery :='CREATE TEMP TABLE tmp_divida_remido'|| stTabela ||' AS
        SELECT divida_ativa.cod_inscricao
             , divida_ativa.exercicio
             , divida_ativa.dt_inscricao
             , divida_remissao.cod_norma
             , divida_remissao.numcgm
             , divida_remissao.dt_remissao
             , divida_remissao.observacao
             , (  SELECT MIN(num_parcelamento) AS num_parcelamento
                    FROM divida.divida_parcelamento
                   WHERE divida_ativa.cod_inscricao = divida_parcelamento.cod_inscricao
                     AND divida_ativa.exercicio = divida_parcelamento.exercicio
                GROUP BY divida_ativa.cod_inscricao
                       , divida_ativa.exercicio
               ) AS num_parcelamento
          FROM divida.divida_ativa
    INNER JOIN divida.divida_remissao
            ON divida_ativa.cod_inscricao = divida_remissao.cod_inscricao
           AND divida_ativa.exercicio = divida_remissao.exercicio
    WHERE ( 1 = 1 )';
    
    EXECUTE stQuery;

    stQuery :='CREATE TEMP TABLE tmp_divida_remido_valor_parcelamento'|| stTabela ||' AS
        SELECT tmp_divida_remido.cod_inscricao
             , tmp_divida_remido.exercicio 
             , tmp_divida_remido.dt_inscricao 
             , parcela_origem.cod_parcela
             , tmp_divida_remido.num_parcelamento
             , sum(valor) AS valor
          FROM tmp_divida_remido'|| stTabela ||' AS tmp_divida_remido
    INNER JOIN divida.divida_parcelamento
            ON tmp_divida_remido.num_parcelamento = divida_parcelamento.num_parcelamento
    INNER JOIN divida.parcela_origem
            ON divida_parcelamento.num_parcelamento = parcela_origem.num_parcelamento
      GROUP BY tmp_divida_remido.cod_inscricao
             , tmp_divida_remido.exercicio
             , tmp_divida_remido.dt_inscricao
             , parcela_origem.cod_parcela
             , tmp_divida_remido.num_parcelamento';

    EXECUTE stQuery;

    stQuery:='
    CREATE TEMP TABLE TMP_LANCAMENTO'|| stTabela ||' AS
        SELECT lancamento_calculo.valor
             , lancamento_calculo.cod_lancamento
             , lancamento_calculo.cod_inscricao 
             , COALESCE(lancamento_calculo.exercicio ,'''' ) AS exercicio_da
             , lancamento_calculo.dt_inscricao AS dt_inscricao_da
             , COALESCE( cadastro_economico_calculo.inscricao_economica,
                         imovel_calculo.inscricao_municipal )::TEXT AS inscricao
             , divida_cgm.numcgm
             , sw_cgm.nom_cgm AS nomcgm ';

        IF (boGrupo = true) THEN
            stQuery := stQuery || '
             , grupo_credito.cod_grupo AS cod_grupo
             , grupo_credito.ano_exercicio as ano_exercicio
             , grupo_credito.descricao AS descricao ';
        ELSE
            stQuery := stQuery || '
             , credito.cod_credito AS cod_grupo
             , calculo.exercicio as ano_exercicio
             , credito.descricao_credito AS descricao ';
        END IF;

        stQuery := stQuery || '
             , lancamento_calculo.cod_calculo
        FROM (
                  SELECT tmp_divida_remido_valor_parcelamento.cod_inscricao
                       , tmp_divida_remido_valor_parcelamento.exercicio
                       , sum( COALESCE( tmp_divida_remido_valor_parcelamento.valor, parcela.valor)) AS valor
                       , tmp_divida_remido_valor_parcelamento.dt_inscricao
                       , lancamento.situacao
                       , (SELECT MIN(cod_calculo)
                            FROM arrecadacao.lancamento_calculo
                           WHERE lancamento_calculo.cod_lancamento = lancamento.cod_lancamento
                          ) AS cod_calculo
                       , lancamento.cod_lancamento
                    FROM arrecadacao.lancamento
              INNER JOIN arrecadacao.parcela
                      ON lancamento.cod_lancamento = parcela.cod_lancamento
               '|| stFiltroDivida ||' JOIN tmp_divida_remido_valor_parcelamento'|| stTabela ||' AS tmp_divida_remido_valor_parcelamento
                      ON parcela.cod_parcela = tmp_divida_remido_valor_parcelamento.cod_parcela
                    JOIN divida.relatorio_remissao_credito
                      ON relatorio_remissao_credito.cod_lancamento = lancamento.cod_lancamento
                   WHERE lancamento.situacao = ''R''
                GROUP BY tmp_divida_remido_valor_parcelamento.cod_inscricao
                       , tmp_divida_remido_valor_parcelamento.exercicio
                       , tmp_divida_remido_valor_parcelamento.dt_inscricao
                       , lancamento.situacao
                       , cod_calculo
                       , lancamento.cod_lancamento
             ) AS lancamento_calculo
    INNER JOIN divida.divida_cgm
            ON lancamento_calculo.cod_inscricao = divida_cgm.cod_inscricao
           AND lancamento_calculo.exercicio = divida_cgm.exercicio
     LEFT JOIN arrecadacao.calculo_cgm
         USING (cod_calculo)
    INNER JOIN sw_cgm
            ON divida_cgm.numcgm = sw_cgm.numcgm
    INNER JOIN arrecadacao.calculo
            ON lancamento_calculo.cod_calculo = calculo.cod_calculo
     LEFT JOIN monetario.credito
            ON calculo.cod_credito  = credito.cod_credito
           AND calculo.cod_especie  = credito.cod_especie
           AND calculo.cod_genero   = credito.cod_genero
           AND calculo.cod_natureza = credito.cod_natureza ';

        IF (boGrupo = true) THEN
            stQuery := stQuery || '
     LEFT JOIN arrecadacao.calculo_grupo_credito
            ON calculo_grupo_credito.cod_calculo = calculo.cod_calculo
     LEFT JOIN arrecadacao.grupo_credito
            ON calculo_grupo_credito.cod_grupo = grupo_credito.cod_grupo
           AND calculo_grupo_credito.ano_exercicio = grupo_credito.ano_exercicio ';
        END IF;

        stQuery := stQuery || '
     LEFT JOIN arrecadacao.imovel_calculo
            ON lancamento_calculo.cod_calculo = imovel_calculo.cod_calculo
     LEFT JOIN arrecadacao.cadastro_economico_calculo
            ON lancamento_calculo.cod_calculo = cadastro_economico_calculo.cod_calculo
         WHERE ( 1 = 1 )
    ';
    
    IF stCreditos IS NOT NULL THEN
        IF (boGrupo = true) THEN
            stQuery:= stQuery ||' AND GRUPO_CREDITO.ANO_EXERCICIO||GRUPO_CREDITO.COD_GRUPO in ('|| stCreditos ||')';
        ELSE
            stCodigos := STRING_TO_ARRAY(stCreditos, '-');
            stFiltro := '';
        
            FOR inIndex IN 1..array_upper(stCodigos, 1) LOOP
                stCodigosDesmembrados := STRING_TO_ARRAY(stCodigos[inIndex], '.');
        
                stFiltro := stFiltro || '  (   calculo.cod_credito  = '|| SUBSTR(stCodigosDesmembrados[1], 5, LENGTH(stCodigosDesmembrados[1]));
                stFiltro := stFiltro || '  AND calculo.cod_especie  = '|| stCodigosDesmembrados[2];
                stFiltro := stFiltro || '  AND calculo.cod_genero   = '|| stCodigosDesmembrados[3];
                stFiltro := stFiltro || '  AND calculo.cod_natureza = '|| stCodigosDesmembrados[4];
                stFiltro := stFiltro || '  AND calculo.exercicio    = '|| SUBSTR(stCodigosDesmembrados[1], 1, 4);
                stFiltro := stFiltro || '  ) OR ';
            END LOOP;
        
            stQuery := stQuery || ' AND ('|| SUBSTR(stFiltro, 1, LENGTH(stFiltro)-3) ||') ';
        END IF;
    END IF;
    
    EXECUTE stQuery;
    
    stQuery:='
    CREATE TEMP TABLE TMP_REL_REMISSAO'|| stTabela ||' (
          inscricao           INT
        , cgm                 VARCHAR
        , cod_grupo           INTEGER
        , ano_exercicio       CHAR(4)
        , descricao           VARCHAR(80)
        , cod_inscricao       VARCHAR
        , exercicio_da        CHAR(4)
        , dt_inscricao_da     VARCHAR
        , valor               NUMERIC(14,2)
    )';
    
    EXECUTE stQuery;
    stQuery := 'SELECT * FROM TMP_LANCAMENTO'|| stTabela ||' ORDER BY cod_calculo,cod_lancamento,inscricao,cod_grupo,ano_exercicio,cod_inscricao , exercicio_da';
    
    inCodCalculo := null;
    FOR reRegistro IN EXECUTE stQuery LOOP
        IF inCodCalculo IS NULL THEN
            inCodCalculo    := reRegistro.cod_calculo;
            inCodLancamento := reRegistro.cod_lancamento;
            inInscricao     := reRegistro.inscricao; 
            inCodGrupo      := reRegistro.cod_grupo;
            stExercicio     := reRegistro.ano_exercicio;
            stDescricao     := reRegistro.descricao;
            inCodInscricao  := reRegistro.cod_inscricao;
            stExercicioDA   := reRegistro.exercicio_da;
        END IF;
        IF inCodCalculo    = reRegistro.cod_calculo    AND
           inCodLancamento = reRegistro.cod_lancamento AND
           inInscricao     = reRegistro.inscricao      AND
           inCodGrupo      = reRegistro.cod_grupo      AND
           stExercicio     = reRegistro.ano_exercicio  AND
           inCodInscricao  = reRegistro.cod_inscricao  AND
           stExercicioDA   = reRegistro.exercicio_da
        THEN
            IF LENGTH(COALESCE(stNomCGM,'')) > 0 THEN 
                stNomCGM := stNomCGM ||'\n'|| reRegistro.numcgm ||'-'|| REPLACE(reRegistro.nomcgm,'''','''''''' );
            ELSE
                stNomCGM := reRegistro.numcgm ||' - '|| REPLACE(reRegistro.nomcgm,''',''''''');
            END IF;
            inCodLancamento:= reRegistro.cod_lancamento;
            inInscricao     := reRegistro.inscricao; 
            inCodGrupo      := reRegistro.cod_grupo;
            stExercicio     := reRegistro.ano_exercicio;
            stDescricao     := reRegistro.descricao;
            inCodInscricao  := reRegistro.cod_inscricao;
            stExercicioDA   := reRegistro.exercicio_da;
            IF reRegistro.dt_inscricao_da is not null then
               stDtInscricaoDA := TO_CHAR(reRegistro.dt_inscricao_da,'DD/MM/YYYY');
            ELSE
               stDtInscricaoDA := '';
            END IF;
            nuValor         := reRegistro.valor;
        ELSE
            stInsert:= 'INSERT INTO
                       TMP_REL_REMISSAO'|| stTabela ||'
                 VALUES
                       ('|| inInscricao     ||'
                       ,'|| quote_literal(stNomCGM)      ||'
                       ,'|| inCodGrupo      ||'
                       ,'|| quote_literal(stExercicio)     ||'
                       ,'|| quote_literal(stDescricao)     ||'
                       ,'|| quote_literal(coalesce( inCodInscricao::text,'null'))  ||'
                       ,'|| quote_literal(stExercicioDA)   ||'
                       ,'|| quote_literal(stDtInscricaoDA) ||'
                       ,'|| nuValor         ||'
                       )';

            EXECUTE stInsert;

            inCodCalculo := reRegistro.cod_calculo;
            stNomCGM := reRegistro.numcgm ||' - '|| REPLACE(reRegistro.nomcgm,'''','''''''');
            inInscricao     := reRegistro.inscricao;
            inCodGrupo      := reRegistro.cod_grupo;
            stExercicio     := reRegistro.ano_exercicio;
            stDescricao     := reRegistro.descricao;
            inCodInscricao  := reRegistro.cod_inscricao;
            stExercicioDA   := reRegistro.exercicio_da;
            IF reRegistro.dt_inscricao_da is not null then
               stDtInscricaoDA := TO_CHAR(reRegistro.dt_inscricao_da,'DD/MM/YYYY');
            ELSE
               stDtInscricaoDA := '';
            END IF;
            nuValor         := reRegistro.valor;
        END IF;
    END LOOP;

    --FAZ A ULTIMA INCLUSAO CASO O LOOP TERMINE SEM FAZER O ULTIMO INSEERT
    IF inInscricao IS NOT NULL THEN
        stInsert:= 'INSERT INTO
                   TMP_REL_REMISSAO'|| stTabela ||'
             VALUES
                   ('|| inInscricao     ||'
                   ,'|| quote_literal(stNomCGM)        ||'
                   ,'|| inCodGrupo      ||'
                   ,'|| quote_literal(stExercicio)     ||'
                   ,'|| quote_literal(stDescricao)     ||'
                   ,'|| quote_literal(coalesce( inCodInscricao::text,'null'))  ||'
                   ,'|| quote_literal(stExercicioDA)   ||'
                   ,'|| quote_literal(stDtInscricaoDA) ||'
                   ,'|| nuValor         ||'
                   )';
        EXECUTE stInsert;
    
    END IF;
    
    stQuery := 'SELECT * FROM TMP_REL_REMISSAO'|| stTabela;
    
    FOR reRegistro IN EXECUTE stQuery LOOP
        return next reRegistro;
    END LOOP;
    
    EXECUTE 'DROP TABLE tmp_divida_remido'|| stTabela;
    EXECUTE 'DROP TABLE tmp_divida_remido_valor_parcelamento'|| stTabela;
    EXECUTE 'DROP TABLE TMP_LANCAMENTO'|| stTabela;
    EXECUTE 'DROP TABLE TMP_REL_REMISSAO'|| stTabela;

    RETURN;
END;
$$ LANGUAGE 'plpgsql';
