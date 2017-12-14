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
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: $
*
* Caso de uso: uc-05.03.13
*/


CREATE OR REPLACE FUNCTION arrecadacao.fn_rl_arrecadacao_periodico_por_cgm (

        VARCHAR, VARCHAR, VARCHAR, VARCHAR,
        VARCHAR, VARCHAR, VARCHAR, VARCHAR,

        VARCHAR, VARCHAR,
        VARCHAR, VARCHAR,

        VARCHAR, VARCHAR,
        VARCHAR, VARCHAR,
        VARCHAR, VARCHAR,

        DATE, DATE

    )

RETURNS SETOF RECORD AS '
DECLARE

    inCodCreditoInicial     ALIAS FOR $1;
    inCodEspecieInicial     ALIAS FOR $2;
    inCodGeneroInicial      ALIAS FOR $3;
    inCodNaturezaInicial    ALIAS FOR $4;

    inCodCreditoFinal       ALIAS FOR $5;
    inCodEspecieFinal       ALIAS FOR $6;
    inCodGeneroFinal        ALIAS FOR $7;
    inCodNaturezaFinal      ALIAS FOR $8;

    inCodGrupoInicial       ALIAS FOR $9;
    inExercicioGrupoInicial ALIAS FOR $10;

    inCodGrupoFinal         ALIAS FOR $11;
    inExercicioGrupoFinal   ALIAS FOR $12;

    inCodIMInicial          ALIAS FOR $13;
    inCodIMFinal            ALIAS FOR $14;

    inCodIEInicial          ALIAS FOR $15;
    inCodIEFinal            ALIAS FOR $16;

    inCodCGMInicial         ALIAS FOR $17;
    inCodCGMFinal           ALIAS FOR $18;

    dtInicio                ALIAS FOR $19;
    dtFinal                 ALIAS FOR $20;




    inCodLogradouroTMP      VARCHAR := '''';
    stAuxiliarEDI           VARCHAR := '''';
    nuResultado             NUMERIC := 0.00;
    flSomatorio             NUMERIC := 0.00;

    stFiltro                VARCHAR := '''';


    stAuxiliarCredito       VARCHAR := '''';
    stFiltroCredito         VARCHAR := '''';

    stAuxiliarGrupo         VARCHAR := '''';
    stFiltroGrupo           VARCHAR := '''';
    stOrderGrupo            VARCHAR := '''';

    stAuxiliarIM            VARCHAR := '''';
    stFiltroIM              VARCHAR := '''';
    stOrderIM               VARCHAR := '''';

    stAuxiliarIE            VARCHAR := '''';
    stFiltroIE              VARCHAR := '''';

    stAuxiliarCGM           VARCHAR := '''';
    stFiltroCGM             VARCHAR := '''';


    stOrdemAuxiliar         VARCHAR := '''';
    stOrdemAuxiliar2        VARCHAR := '''';


    stFiltroValor           VARCHAR := '''';

    stFiltroSituacao        VARCHAR := '''';
    stFiltroSituacaoExterno VARCHAR := '''';


    stSelectAuxiliar        VARCHAR := '''';
    stSqlPrincipal          VARCHAR := '''';
    stSqlAuxiliar           VARCHAR := '''';

    boCredito               BOOLEAN := false;


    numeracaoPagto          VARCHAR := '''';
    dataPagto               VARCHAR := '''';
    flValorLancado          NUMERIC ;
    flValorPago             NUMERIC ;
    flValorAbertoVencido    NUMERIC ;
    flValorAbertoAVencer    NUMERIC ;


    reRecord         RECORD;

BEGIN


    -- ############# CREDITO ############# ----------------------------------------------------
    IF ( inCodCreditoInicial != '''' OR inCodCreditoFinal != '''' ) THEN

        boCredito := true;

        IF ( inCodCreditoInicial != '''' AND inCodCreditoFinal != '''' ) THEN

            stFiltroCredito := '' ( ac.cod_credito BETWEEN ''||inCodCreditoInicial;
            stFiltroCredito := stFiltroCredito ||'' AND ''||inCodCreditoFinal||'' ) '';

            stFiltroCredito := stFiltroCredito ||'' AND ( ac.cod_especie between ''||inCodEspecieInicial;
            stFiltroCredito := stFiltroCredito ||'' AND ''||inCodEspecieFinal||'' ) '';

            stFiltroCredito := stFiltroCredito ||'' AND ( ac.cod_genero between ''||inCodGeneroInicial;
            stFiltroCredito := stFiltroCredito ||'' AND ''||inCodGeneroFinal||'' ) '';

            stFiltroCredito := stFiltroCredito ||'' AND ( ac.cod_natureza between ''||inCodNaturezaInicial;
            stFiltroCredito := stFiltroCredito ||'' AND ''||inCodNaturezaFinal||'' ) '';

        ELSE

            stFiltroCredito := '' ac.cod_credito = ''|| inCodCreditoInicial || inCodCreditoFinal;
            stFiltroCredito := stFiltroCredito||'' AND ac.cod_especie = '' ||inCodEspecieInicial||inCodEspecieFinal;
            stFiltroCredito := stFiltroCredito||'' AND ac.cod_genero  = '' ||inCodGeneroInicial||inCodGeneroFinal;
            stFiltroCredito := stFiltroCredito||'' AND ac.cod_natureza ='' ||inCodNaturezaInicial||inCodNaturezaFinal;

        END IF;

            stFiltroCredito := stFiltroCredito ||'' AND acgc.cod_calculo is NULL AND'';

    END IF;



    --############# GRUPO DE CREDITO ############# ----------------------------------------------------
    IF ( inCodGrupoInicial != '''' OR inCodGrupoFinal != '''' ) THEN

        boCredito := true;


        IF ( inCodGrupoInicial != '''' AND inCodGrupoFinal != '''' ) THEN
            stFiltroGrupo := '' ( acgc.cod_grupo BETWEEN ''||inCodGrupoInicial||'' AND ''||inCodGrupoFinal||'' )'';

            IF ( inExercicioGrupoInicial != '''' OR inExercicioGrupoFinal != '''' ) THEN
                IF ( inExercicioGrupoInicial != '''' AND inExercicioGrupoFinal != '''' ) THEN
                    stFiltroGrupo := stFiltroGrupo || '' AND ( acgc.ano_exercicio between ''||inExercicioGrupoInicial||'' AND ''||inExercicioGrupoFinal||'' ) '';
                ELSE
                    stFiltroGrupo := stFiltroGrupo || '' AND acgc.ano_exercicio = ''|| inExercicioGrupoInicial || inExercicioGrupoFinal ;
                END IF;

            END IF;

        ELSE
            stFiltroGrupo := '' acgc.cod_grupo = ''|| inCodGrupoInicial || inCodGrupoFinal;
            stFiltroGrupo := stFiltroGrupo ||'' AND acgc.ano_exercicio = ''|| inExercicioGrupoInicial || inExercicioGrupoFinal ;
        END IF;


        stFiltroGrupo := stFiltroGrupo ||'' AND acgc.cod_calculo IS NOT NULL AND'';

    END IF;



    -- ############# IMOVEIS ############# ----------------------------------------------------
    IF ( inCodIMInicial != '''' OR inCodIMFinal != '''' ) THEN

        stAuxiliarIM:= '' INNER JOIN arrecadacao.imovel_calculo as aic ON aic.cod_calculo = ac.cod_calculo '';

        IF ( inCodIMInicial != '''' AND inCodIMFinal != '''' ) THEN
            stFiltroIM  := '' ( aic.inscricao_municipal BETWEEN ''||inCodIMInicial||'' AND ''||inCodIMFinal||'' ) '';
        ELSE
            stFiltroIM  := '' aic.inscricao_municipal = ''|| inCodIMInicial || inCodIMFinal;
        END IF;

            stFiltroIM  := stFiltroIM  ||'' AND'';

    END IF;


    -- ############# EMPRESAS ############# ----------------------------------------------------
    IF ( inCodIEInicial != '''' OR inCodIEFinal != '''' ) THEN

        stAuxiliarIE := '' INNER JOIN arrecadacao.cadastro_economico_calculo as cec '';
        stAuxiliarIE := stAuxiliarIE ||'' ON cec.cod_calculo = ac.cod_calculo '';

        IF ( inCodIEInicial != '''' AND inCodIEFinal != '''' ) THEN
            stFiltroIE  := '' ( cec.inscricao_economica BETWEEN ''||inCodIEInicial||'' AND ''||inCodIEFinal||'' ) '';
        ELSE
            stFiltroIE  := '' cec.inscricao_economica = ''|| inCodIEInicial || inCodIEFinal;
        END IF;

            stFiltroIE  := stFiltroIE ||'' AND'';

    END IF;


    -- ############# CONTRIBUINTE ############# ----------------------------------------------------
    IF ( inCodCGMInicial != '''' OR inCodCGMFinal != '''' ) THEN


        stAuxiliarCGM := '' LEFT JOIN arrecadacao.imovel_calculo as aic
                            ON aic.cod_calculo = ac.cod_calculo

                            LEFT JOIN arrecadacao.cadastro_economico_calculo as cec
                            ON cec.cod_calculo = ac.cod_calculo
        '';

        IF ( inCodCGMInicial != '''' AND inCodCGMFinal != '''' ) THEN
            stFiltroCGM := '' ( accgm.numcgm BETWEEN ''||inCodCGMInicial||'' AND ''||inCodCGMFinal||'' )'';
        ELSE
            stFiltroCGM := '' accgm.numcgm = ''|| inCodCGMInicial || inCodCGMFinal;
        END IF;

        stFiltroCGM := stFiltroCGM ||'' AND'';
    ELSE
        stFiltroCGM := '''';

    END IF;



    IF ( boCredito ) THEN

        stAuxiliarGrupo := ''   LEFT JOIN arrecadacao.calculo_grupo_credito as acgc
                                ON acgc.cod_calculo = ac.cod_calculo
                                AND acgc.ano_exercicio = ac.exercicio '';
    END IF;


--    stFiltro := ;
--    stFiltro :=
    stFiltro := stFiltro || stFiltroIM || stFiltroCredito || stFiltroGrupo || stFiltroIE || stFiltroCGM;
    IF ( stFiltro != '''' ) THEN
        stFiltro := '' AND ''||substring( stFiltro from 1 for ( char_length(stFiltro) - 4 ));
    ELSE
        stFiltro := '''';
    END IF;




    /****========================================================================*/
    /****===============          QUERY PRINCIPAL        ========================*/
    /****========================================================================*/

    stSqlPrincipal = ''

        SELECT
            maior.descricao,
            maior.numcgm,
            (
                SELECT
                    nom_cgm
                FROM
                    sw_cgm
                WHERE
                    sw_cgm.numcgm = maior.numcgm
            )
            , sum(maior.lancado)
            , sum(maior.pago)
            , sum(maior.aberto_vencido)
            , sum(maior.aberto_a_vencer)
        FROM
        (

    SELECT
        al.descricao,
        al.numcgm,
        buscaParcelasEmAberto( al.cod_lancamento, ''''''||dtInicio||'''''', ''''''||dtFinal||'''''', true ) + buscaParcelasEmAberto( al.cod_lancamento, ''''''||dtInicio||'''''', ''''''||dtFinal||'''''', false ) + buscaParcelasPagasOrigem( al.cod_lancamento, ''''''||dtInicio||'''''', ''''''||dtFinal||'''''' ) AS lancado,
        buscaParcelasPagas( al.cod_lancamento, ''''''||dtInicio||'''''', ''''''||dtFinal||'''''' ) AS pago,
        buscaParcelasEmAberto( al.cod_lancamento, ''''''||dtInicio||'''''', ''''''||dtFinal||'''''', true ) AS aberto_vencido,
        buscaParcelasEmAberto( al.cod_lancamento, ''''''||dtInicio||'''''', ''''''||dtFinal||'''''', false ) AS aberto_a_vencer
    FROM
        (
            SELECT
                cod_lancamento
                , numcgm
                , descricao
            FROM
                (
                    SELECT DISTINCT
                        ap.cod_lancamento
                        , accgm.numcgm
                        , arrecadacao.buscaVinculoLancamento( alc.cod_lancamento, ac.exercicio::int ) as descricao
                        , btrim ( arrecadacao.fn_busca_lancamento_situacao( ap.cod_lancamento ) ) as situacao
                    FROM
                        arrecadacao.parcela ap
                        INNER JOIN (
                            SELECT
                                cod_lancamento
                                , max(cod_calculo) as cod_calculo
                            FROM
                                arrecadacao.lancamento_calculo as al
                            GROUP BY
                                cod_lancamento
                        ) as alc
                        ON alc.cod_lancamento = ap.cod_lancamento

                        INNER JOIN arrecadacao.calculo as ac
                        ON ac.cod_calculo = alc.cod_calculo

                        INNER JOIN arrecadacao.calculo_cgm as accgm
                        ON accgm.cod_calculo = ac.cod_calculo

                        ''|| stAuxiliarGrupo || stAuxiliarCGM || stAuxiliarIE || stAuxiliarIM || ''

                    WHERE
                        -- alc.cod_lancamento = 236705 AND
                        ap.vencimento between ''''''|| dtInicio ||'''''' AND ''''''|| dtFinal ||'''''' 
                        ''|| stFiltro ||''
--TESTA se não está paga a parcela única nr_parcela = 0
			 AND ap.cod_lancamento NOT IN (
				SELECT cod_lancamento 
	                          FROM arrecadacao.parcela
                                 INNER JOIN arrecadacao.carne
				   USING (cod_parcela)
                                 INNER JOIN arrecadacao.pagamento
				   USING (numeracao)
				 INNER JOIN arrecadacao.tipo_pagamento
 				   USING (cod_tipo)
			         WHERE cod_lancamento = ap.cod_lancamento AND ( nr_parcela = 0 OR tipo_pagamento.pagamento = ''''f'''')
)

                    ORDER BY
                        ap.cod_lancamento
                ) as busca_lancamentos
            WHERE
                (   btrim(situacao)  != ''''Recalculo'''' )
                AND  (   btrim(situacao)  != ''''Anulação''''  )
                AND  (   btrim(situacao)  != ''''Reemitida''''  )

            GROUP BY    cod_lancamento, numcgm, descricao
        ) as al

    ) as maior
        GROUP BY maior.numcgm, descricao
        --, cod_lancamento
    '';


        FOR reRecord IN EXECUTE stSqlPrincipal LOOP

            return next reRecord;

        END LOOP;

    RETURN ;

END;
' LANGUAGE 'plpgsql';
