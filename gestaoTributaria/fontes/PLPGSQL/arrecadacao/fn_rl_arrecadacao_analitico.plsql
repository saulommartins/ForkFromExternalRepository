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
CREATE OR REPLACE FUNCTION arrecadacao.fn_rl_arrecadacao_analitico (
        VARCHAR, VARCHAR, VARCHAR, VARCHAR,
        VARCHAR, VARCHAR, VARCHAR, VARCHAR,

        VARCHAR, VARCHAR,   
        VARCHAR, VARCHAR,   

        VARCHAR, VARCHAR,   
        VARCHAR, VARCHAR,
        VARCHAR, VARCHAR,

        VARCHAR, VARCHAR,
        VARCHAR, VARCHAR,

        VARCHAR, VARCHAR,

        VARCHAR,
        VARCHAR,
        VARCHAR,
        VARCHAR
    )
RETURNS SETOF RECORD AS $$
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

    inCodLogradouroInicial  ALIAS FOR $19;
    inCodLogradouroFinal    ALIAS FOR $20;

    inCodCondominioInicial  ALIAS FOR $21;
    inCodCondominioFinal    ALIAS FOR $22;

    nuValorInicial          ALIAS FOR $23;
    nuValorFinal            ALIAS FOR $24;

    stSituacao              ALIAS FOR $25;
    inExercicio             ALIAS FOR $26;

    stCodAtividadeInicial   ALIAS FOR $27;
    stCodAtividadeFinal     ALIAS FOR $28;


    inCodLogradouroTMP      VARCHAR := '';
    stAuxiliarEDI           VARCHAR := '';
    stAuxiliarInscricoes    VARCHAR := '';
    nuResultado             NUMERIC := 0.00;
    flSomatorio             NUMERIC := 0.00;

    stFiltro                VARCHAR := '';
    stBuscaCalculos         VARCHAR := '';

    stAuxiliarCredito       VARCHAR := '';
    stFiltroCredito         VARCHAR := '';
    stFiltroCreditoWHERE    VARCHAR := '';

    stAuxiliarGrupo         VARCHAR := '';
    stFiltroGrupo           VARCHAR := '';
    stFiltroGrupoAND        VARCHAR := '';
    stFiltroGrupoWHERE      VARCHAR := '';
    stOrderGrupo            VARCHAR := '';

    stAuxiliarIM            VARCHAR := '';
    stFiltroIM              VARCHAR := '';
    stOrderIM               VARCHAR := '';

    stAuxiliarIE            VARCHAR := '';
    stFiltroIE              VARCHAR := '';

    stAuxiliarCGM           VARCHAR := '';
    stFiltroCGM             VARCHAR := '';
    stFiltroCGMAND          VARCHAR := '';


    stSelectAuxiliarCondominio  VARCHAR := '';
    stAuxiliarCond          VARCHAR := '';
    stFiltroCond            VARCHAR := '';
    stOrdemAuxiliar         VARCHAR := '';
    stOrdemAuxiliar2        VARCHAR := '';

    stAuxiliarLogradouro    VARCHAR := '';
    stFiltroLogradouroLO    VARCHAR := '';
    stFiltroLogradouroEDI   VARCHAR := '';
    stFiltroLogradouroICT   VARCHAR := '';

    stFiltroValor           VARCHAR := '';

    stFiltroSituacao        VARCHAR := '';
    stFiltroSituacaoExterno VARCHAR := '';


    stAuxiliarTipo          VARCHAR := '';
    stExercicio             VARCHAR := '';
    stExercicioWhere        VARCHAR := '';
    stExercicioAnd          VARCHAR := '';

    stSelectAuxiliar        VARCHAR := '';
    stJoinAuxiliar          VARCHAR := '';
    stSqlPrincipal          VARCHAR := '';
    stAtividade             VARCHAR := '';

    boCondominio            BOOLEAN := false;
    boLogradouro            BOOLEAN := false;
    boCredito               BOOLEAN := false;
    boContribuinte          BOOLEAN := false;


    numeracaoPagto          VARCHAR := '';
    dataPagto               VARCHAR := '';
    nuParcelaValor          NUMERIC ;
    nuDescontoValor         NUMERIC ;
    nuJurosPagar            NUMERIC ;
    nuMultaPagar            NUMERIC ;
    nuCorrecaoPagar         NUMERIC ;
    nuValorCorrigido        NUMERIC ;
    nuValorPago             NUMERIC ;
    nuJurosPago             NUMERIC ;
    nuMultaPaga             NUMERIC ;
    nuCorrecaoPago          NUMERIC ;
    nuDiferencaValor        NUMERIC ;


    reRecord         RECORD;

BEGIN


    IF inExercicio != '' THEN

        stExercicio      := ' ac.exercicio = '''|| inExercicio ||''' AND ';
        stExercicioWhere := ' WHERE ac.exercicio = '''|| inExercicio ||''' ';
        stExercicioAnd   := ' AND ac.exercicio = '''|| inExercicio ||''' ';

    END IF;


    -- ############# CREDITO ############# ----------------------------------------------------
    IF ( inCodCreditoInicial != '' OR inCodCreditoFinal != '' ) THEN

        IF ( inCodCreditoInicial != '' AND inCodCreditoFinal != '' ) THEN

            stFiltroCredito := ' ( ac.cod_credito BETWEEN '||inCodCreditoInicial;
            stFiltroCredito := stFiltroCredito ||' AND '||inCodCreditoFinal||' ) ';

            stFiltroCredito := stFiltroCredito ||' AND ( ac.cod_especie between '||inCodEspecieInicial;
            stFiltroCredito := stFiltroCredito ||' AND '||inCodEspecieFinal||' ) ';

            stFiltroCredito := stFiltroCredito ||' AND ( ac.cod_genero between '||inCodGeneroInicial;
            stFiltroCredito := stFiltroCredito ||' AND '||inCodGeneroFinal||' ) ';

            stFiltroCredito := stFiltroCredito ||' AND ( ac.cod_natureza between '||inCodNaturezaInicial;
            stFiltroCredito := stFiltroCredito ||' AND '||inCodNaturezaFinal||' ) ';

        ELSE

            stFiltroCredito := ' ac.cod_credito = '|| inCodCreditoInicial || inCodCreditoFinal;
            stFiltroCredito := stFiltroCredito||' AND ac.cod_especie = ' ||inCodEspecieInicial||inCodEspecieFinal;
            stFiltroCredito := stFiltroCredito||' AND ac.cod_genero  = ' ||inCodGeneroInicial||inCodGeneroFinal;
            stFiltroCredito := stFiltroCredito||' AND ac.cod_natureza =' ||inCodNaturezaInicial||inCodNaturezaFinal;

        END IF;


        IF inExercicio != '' THEN
            stFiltroCreditoWHERE :=
                ' AND '||stFiltroCredito||' AND acgc.cod_calculo is NULL '||stExercicioAnd;
        ELSE
            stFiltroCreditoWHERE :=
                ' WHERE '||stFiltroCredito||' AND acgc.cod_calculo is NULL '||stExercicioAnd;
        END IF;

            stFiltroCredito := stFiltroCredito ||' AND acgc.cod_calculo is NULL AND ';

    END IF;



    --############# GRUPO DE CREDITO ############# ----------------------------------------------------
    IF ( inCodGrupoInicial != '' OR inCodGrupoFinal != '' ) THEN

        IF ( inCodGrupoInicial != '' AND inCodGrupoFinal != '' ) THEN
            stFiltroGrupo := ' ( acgc.cod_grupo BETWEEN '||inCodGrupoInicial||' AND '||inCodGrupoFinal||' )';

            IF ( inExercicioGrupoInicial != '' OR inExercicioGrupoFinal != '' ) THEN
                IF ( inExercicioGrupoInicial != '' AND inExercicioGrupoFinal != '' ) THEN
                    stFiltroGrupo := stFiltroGrupo || ' AND ( acgc.ano_exercicio between '||inExercicioGrupoInicial||' AND '||inExercicioGrupoFinal||' ) ';
                ELSE
                    stFiltroGrupo := stFiltroGrupo || ' AND acgc.ano_exercicio = '|| inExercicioGrupoInicial || inExercicioGrupoFinal ;
                END IF;
                                
            END IF;

        ELSE
            stFiltroGrupo := ' agc.cod_grupo = '|| inCodGrupoInicial || inCodGrupoFinal;
            stFiltroGrupo := stFiltroGrupo ||' AND acgc.ano_exercicio = '|| inExercicioGrupoInicial || inExercicioGrupoFinal ;
        END IF;


        stFiltroGrupoAND    := stFiltroGrupo ||' AND acgc.cod_calculo IS NOT NULL AND ';
        stFiltroGrupoWHERE  := 'WHERE'|| stFiltroGrupo ||' AND acgc.cod_calculo IS NOT NULL';

    END IF;


    -- ################## CONDOMINIO ################## ----------------------------------------------

    IF ( inCodCondominioInicial != '' OR inCodCondominioFinal != '' ) THEN

        boCondominio := true;

        IF ( inCodCondominioInicial != '' AND inCodCondominioFinal != '' ) THEN
            stFiltroCond := ' ic.cod_condominio between '||inCodCondominioInicial;
            stFiltroCond := stFiltroCond ||' AND '|| inCodCondominioFinal;
        ELSE
            stFiltroCond := ' ic.cod_condominio = '||inCodCondominioInicial||inCodCondominioFinal;
        END IF;


        stAuxiliarCond := '
                SELECT
                    iic.inscricao_municipal as inscricao
                    , ac.exercicio::integer
                    , ac.cod_calculo
                    , ''IM''::varchar as tipo_inscricao
                    , iic.cod_condominio::varchar as cod_condominio
                    , ic.nom_condominio::varchar as nom_condominio
                FROM
                    arrecadacao.calculo as ac

                    INNER JOIN arrecadacao.imovel_calculo as aic
                    ON aic.cod_calculo = ac.cod_calculo

                    INNER JOIN imobiliario.imovel_condominio as iic
                    ON iic.inscricao_municipal = aic.inscricao_municipal

                    INNER JOIN imobiliario.condominio as ic
                    ON ic.cod_condominio = iic.cod_condominio

                WHERE
                    '|| stFiltroCond ||'
        ';

    END IF;

    IF ( boCondominio = true ) THEN

        stSelectAuxiliarCondominio := ' iic.cod_condominio::varchar as cod_condominio ';
        stSelectAuxiliarCondominio := stSelectAuxiliarCondominio||' , ic.nom_condominio::varchar as nom_condominio';

        stOrdemAuxiliar  := ' , iic.cod_condominio, ic.nom_condominio ';

        stOrdemAuxiliar2 := ' , cod_condominio::integer , ';

    ELSE
        stSelectAuxiliarCondominio := '  ''''::varchar as cod_condominio ';
        stSelectAuxiliarCondominio := stSelectAuxiliarCondominio || ' , ''''::varchar as nom_condominio ';
    END IF;


    -- ############# CONTRIBUINTE ############# ----------------------------------------------------
    IF ( inCodCGMInicial != '' OR inCodCGMFinal != '' ) THEN

        boContribuinte = true;

        IF ( inCodCGMInicial != '' AND inCodCGMFinal != '' ) THEN
            stFiltroCGM := ' ( ccgm.numcgm BETWEEN '||inCodCGMInicial||' AND '||inCodCGMFinal||' )';
        ELSE
            stFiltroCGM := ' ccgm.numcgm = '|| inCodCGMInicial || inCodCGMFinal;
        END IF;

        stFiltroCGMAND := ' AND '||stFiltroCGM;

        stAuxiliarCGM := '
            SELECT
                coalesce ( aic.inscricao_municipal, cec.inscricao_economica ) as inscricao
                , ac.exercicio::integer
                , ac.cod_calculo
                , ( CASE WHEN aic.inscricao_municipal IS NOT NULL THEN
                        ''IM''::varchar
                    ELSE
                        ''IE''::varchar
                    END
                )as tipo_inscricao
                , '||stSelectAuxiliarCondominio||'
            FROM
                arrecadacao.calculo as ac
                INNER JOIN arrecadacao.calculo_cgm as ccgm
                ON ccgm.cod_calculo = ac.cod_calculo
                LEFT JOIN arrecadacao.imovel_calculo as aic
                ON aic.cod_calculo = ac.cod_calculo
                LEFT JOIN arrecadacao.cadastro_economico_calculo as cec
                ON cec.cod_calculo = ac.cod_calculo
            WHERE
                '|| stFiltroCGM ||'
                '||stExercicioAnd||'
        ';


    END IF;

    -- ############# IMOVEIS ############# ----------------------------------------------------
    IF ( inCodIMInicial != '' OR inCodIMFinal != '' ) THEN


        IF ( inCodIMInicial != '' AND inCodIMFinal != '' ) THEN
            stFiltroIM  := ' ( aic.inscricao_municipal BETWEEN '||inCodIMInicial||' AND '||inCodIMFinal||' ) ';
        ELSE
            stFiltroIM  := ' aic.inscricao_municipal = '|| inCodIMInicial || inCodIMFinal;
        END IF;

        stAuxiliarIM := '
            SELECT
                aic.inscricao_municipal as inscricao
                , ac.exercicio::integer
                , ac.cod_calculo
                , ''IM''::varchar as tipo_inscricao
                , '||stSelectAuxiliarCondominio||'
            FROM
                arrecadacao.imovel_calculo as aic
                INNER JOIN arrecadacao.calculo as ac
                ON ac.cod_calculo = aic.cod_calculo
            WHERE
                '||stFiltroIM||'
                '||stExercicioAnd||'
        ';


        --stFiltroIM  := stFiltroIM  ||' AND'';

    END IF;


    -- ############# EMPRESAS ############# ----------------------------------------------------
    IF ( stCodAtividadeInicial != '' OR stCodAtividadeFinal != '' ) THEN
        stAtividade := ' , busca_calculos.atividade::varchar AS atividade ';
    ELSE
        stAtividade := ' , '' ''::varchar AS atividade ';
    END IF;

    IF ( inCodIEInicial != '' OR inCodIEFinal != '' OR stCodAtividadeInicial != '' OR stCodAtividadeFinal != '' ) THEN
        IF ( stCodAtividadeInicial != '' AND stCodAtividadeFinal != '' ) THEN
            stFiltroIE  := ' ( atividade.cod_estrutural BETWEEN '||stCodAtividadeInicial||' AND '||stCodAtividadeFinal||' ) AND ';
        ELSE
            IF ( stCodAtividadeInicial != '' OR stCodAtividadeFinal != '' ) THEN
                stFiltroIE  := ' atividade.cod_estrutural = '|| stCodAtividadeInicial || stCodAtividadeFinal||' AND ';
            END IF;
        END IF;

        IF ( inCodIEInicial != '' AND inCodIEFinal != '' ) THEN
            stFiltroIE  := stFiltroIE||' ( cec.inscricao_economica BETWEEN '||inCodIEInicial||' AND '||inCodIEFinal||' ) ';
        ELSE
            stFiltroIE  := stFiltroIE||' cec.inscricao_economica = '|| inCodIEInicial || inCodIEFinal;
        END IF;

        IF ( stCodAtividadeInicial != '' OR stCodAtividadeFinal != '' ) THEN
            stAuxiliarIE := '
                SELECT
                    cec.inscricao_economica as inscricao
                    , ac.exercicio::integer
                    , ac.cod_calculo
                    , ''IE''::varchar as tipo_inscricao
                    , '||stSelectAuxiliarCondominio||'
                    , atividade.cod_estrutural ||'' - ''|| atividade.nom_atividade AS atividade
                FROM
                    arrecadacao.cadastro_economico_calculo as cec
    
                INNER JOIN
                    economico.atividade_cadastro_economico
                ON
                    atividade_cadastro_economico.inscricao_economica = cec.inscricao_economica
    
                INNER JOIN
                    economico.atividade
                ON
                    atividade.cod_atividade = atividade_cadastro_economico.cod_atividade
    
                INNER JOIN 
                    arrecadacao.calculo as ac
                ON 
                    ac.cod_calculo = cec.cod_calculo
    
                WHERE
                    '||stFiltroIE||'
                    '||stExercicioAnd||'
                ORDER BY
                    atividade.cod_estrutural
            ';
        ELSE
            stAuxiliarIE := '
                SELECT
                    cec.inscricao_economica as inscricao
                    , ac.exercicio::integer
                    , ac.cod_calculo
                    , ''IE''::varchar as tipo_inscricao
                    , '||stSelectAuxiliarCondominio||'
                FROM
                    arrecadacao.cadastro_economico_calculo as cec

                INNER JOIN 
                    arrecadacao.calculo as ac
                ON 
                    ac.cod_calculo = cec.cod_calculo

                WHERE
                    '||stFiltroIE||'
                    '||stExercicioAnd||'
            ';
        END IF;
    END IF;

    -- ################## LOGRADOURO ################## -------------------------------------------------
    IF inCodLogradouroInicial != '' OR inCodLogradouroFinal != '' THEN

        IF ( inCodLogradouroInicial != '' AND inCodLogradouroFinal != '' ) THEN
            -- eh intervalo
            stFiltroLogradouroLO  := ' ( LO.cod_logradouro between '|| inCodLogradouroInicial ||' AND '|| inCodLogradouroFinal ||' ) ';
            stFiltroLogradouroEDI := ' ( EDI.cod_logradouro between '|| inCodLogradouroInicial ||' AND '|| inCodLogradouroFinal ||' ) ';
            stFiltroLogradouroICT := ' ( ict.cod_logradouro between '|| inCodLogradouroInicial ||' AND '|| inCodLogradouroFinal ||' ) ';
        ELSE
            stFiltroLogradouroLO  := ' LO.cod_logradouro  ='|| inCodLogradouroInicial || inCodLogradouroFinal;
            stFiltroLogradouroEDI := ' EDI.cod_logradouro ='|| inCodLogradouroInicial || inCodLogradouroFinal;
            stFiltroLogradouroICT := ' ict.cod_logradouro ='|| inCodLogradouroInicial || inCodLogradouroFinal;
        END IF;

        stAuxiliarLogradouro := '
                SELECT
                    iic.inscricao_municipal as inscricao
                    , ac.exercicio::integer
                    , ac.cod_calculo
                    , ''IM'' as tipo_inscricao
                    , '||stSelectAuxiliarCondominio||'
                FROM
                    imobiliario.confrontacao_trecho as ict

                    INNER JOIN imobiliario.imovel_confrontacao as iic
                    ON iic.cod_lote = ict.cod_lote

                    LEFT JOIN arrecadacao.imovel_calculo as aic
                    ON aic.inscricao_municipal = iic.inscricao_municipal

                    INNER JOIN arrecadacao.calculo as ac
                    ON ac.cod_calculo = aic.cod_calculo

                WHERE
                    '|| stFiltroLogradouroICT ||'
                    '|| stExercicioAnd ||'

            UNION

                SELECT
                    cec.inscricao_economica as inscricao
                    , ac.exercicio::integer
                    , cec.cod_calculo
                    , ''IE'' as tipo_inscricao
                    , '||stSelectAuxiliarCondominio||'
                FROM
                    (
                        SELECT
                            res.inscricao_economica
                            , res.timestamp
                        FROM
                            (
                                SELECT
                                    EDF.inscricao_economica
                                    , EDF.timestamp
                                FROM
                                    sw_logradouro as LO

                                    INNER JOIN imobiliario.confrontacao_trecho as CT
                                    ON CT.cod_logradouro = LO.cod_logradouro
                                    INNER JOIN imobiliario.imovel_confrontacao as IC
                                    ON IC.cod_lote = CT.cod_lote

                                    INNER JOIN (
                                        SELECT
                                            inscricao_economica
                                            , inscricao_municipal
                                            , max(timestamp) as timestamp
                                        FROM
                                            economico.domicilio_fiscal
                                        GROUP BY
                                            inscricao_economica
                                            , inscricao_municipal
                                    ) as EDF
                                    ON EDF.inscricao_municipal = IC.inscricao_municipal

                                    LEFT JOIN imobiliario.baixa_imovel as ibi
                                    ON ibi.inscricao_municipal = IC.inscricao_municipal
                                    AND ibi.dt_termino is null

                                WHERE
                                    ibi.inscricao_municipal is null
                                    AND
                                        '|| stFiltroLogradouroLO ||'

                            UNION
                                SELECT
                                    EDI.inscricao_economica
                                    , EDI.timestamp
                                FROM
                                    economico.domicilio_informado as EDI
                                    INNER JOIN (
                                        SELECT
                                            inscricao_economica,
                                            max(timestamp) as timestamp
                                        FROM
                                            economico.domicilio_informado
                                        GROUP BY
                                            inscricao_economica
                                    ) as EDI2
                                    ON EDI2.inscricao_economica = EDI.inscricao_economica

                                WHERE
                                    '|| stFiltroLogradouroEDI ||'
                            ) as res

                            LEFT JOIN economico.baixa_cadastro_economico as ebce
                            ON ebce.inscricao_economica = res.inscricao_economica
                            and ebce.dt_termino is null

                        WHERE
                            ebce.inscricao_economica is null

                    ) as temp

                    INNER JOIN arrecadacao.cadastro_economico_calculo as cec
                    ON cec.inscricao_economica = temp.inscricao_economica

                    INNER JOIN arrecadacao.calculo as ac
                    ON ac.cod_calculo = cec.cod_calculo

            '|| stExercicioWhere ||'

        ';

    END IF;

    -- ############# VALOR ############# ----------------------------------------------------
    IF ( nuValorInicial != '' OR nuValorFinal != '' ) THEN

        IF ( nuValorInicial != '' AND nuValorFinal != '' ) THEN
            stFiltroValor := ' ( ap.valor BETWEEN '||nuValorInicial||' AND '||nuValorFinal||' )';
        ELSE
            stFiltroValor := ' ap.valor = '||nuValorInicial||nuValorFinal;
        END IF;

        stFiltroValor := ' WHERE '|| stFiltroValor ;
    ELSE
        stFiltroValor := ' WHERE ap.valor > 0 ';
    END IF;


    -- ############# SITUACAO CARNE ############# ----------------------------------------------------
    IF ( stSituacao != '' AND stSituacao != 'Todos' ) THEN

        IF ( stSituacao = 'Pago' ) THEN
            stFiltroSituacao  := ' apag.numeracao IS NOT NULL ';
        ELSIF ( stSituacao = 'Aberto' ) THEN
            stFiltroSituacao  := ' apag.numeracao IS NULL ';
        END IF;

        stFiltroSituacao    := ' WHERE '||stFiltroSituacao;

    END IF;


    stAuxiliarInscricoes := '';
    IF ( stAuxiliarLogradouro != '' ) THEN
        stBuscaCalculos := stAuxiliarLogradouro;
    ELSIF ( stAuxiliarCond != '' ) THEN
        stBuscaCalculos := stAuxiliarCond;
    ELSIF ( stAuxiliarIM != '' ) THEN
        stBuscaCalculos := stAuxiliarIM;
    ELSIF ( stAuxiliarIE != '' ) THEN
        stBuscaCalculos := stAuxiliarIE;
    ELSIF ( stAuxiliarCGM != '' ) THEN
        stBuscaCalculos := stAuxiliarCGM;
    ELSE
        stBuscaCalculos := '
            SELECT
                COALESCE( aic.inscricao_municipal, cec.inscricao_economica ) as inscricao
                , ac.exercicio::integer
                , ac.cod_calculo
                , CASE WHEN aic.inscricao_municipal IS NOT NULL THEN 
                        ''IM''::varchar
                  ELSE
                        ''IE''::varchar
                  END as tipo_inscricao
                , '||stSelectAuxiliarCondominio||'
            FROM
                arrecadacao.calculo as ac

            LEFT JOIN 
                arrecadacao.imovel_calculo as aic
            ON 
                aic.cod_calculo = ac.cod_calculo

            LEFT JOIN 
                arrecadacao.cadastro_economico_calculo as cec
            ON 
                cec.cod_calculo = ac.cod_calculo
        ';

    END IF;


    IF ( boContribuinte = true ) THEN
        IF ( stFiltroCreditoWHERE != '' ) THEN
            stFiltroCreditoWHERE := stFiltroCreditoWHERE || stFiltroCGMAND;
        ELSIF ( stFiltroGrupoWHERE != '' ) THEN
            stFiltroGrupoWHERE := stFiltroGrupoWHERE || stFiltroCGMAND;
        ELSE
            stFiltroGrupoWHERE := stFiltroCGMAND;
        END IF;
    END IF;


    /****========================================================================*/
    /****===============          QUERY PRINCIPAL        ========================*/
    /****========================================================================*/

    stSqlPrincipal = '
SELECT
    *
    , ( CASE WHEN pagamento_data IS NULL THEN
            0.00
        ELSE
            CASE WHEN ( soma_aberto <> soma_pago ) THEN
                soma_pago - soma_aberto
            ELSE
                diferenca_pago
            END
        END
    )::numeric(14,2) as diferenca_pago
FROM
(
SELECT
    inscricao::int
  
    , tipo_inscricao::varchar
    , cod_lancamento::integer
    , exercicio::varchar

    , contribuinte_numcgm::integer
    , contribuinte_nomcgm::varchar

    , cod_condominio::varchar
    , nom_condominio::varchar

    , origem::varchar
    , cod_grupo::varchar
    , cod_credito::varchar
    , cod_especie::varchar
    , cod_genero::varchar
    , cod_natureza::varchar

    , ( CASE WHEN cod_motivo is not null THEN
            CASE WHEN ( cod_motivo = 109 ) THEN
                descricao_devolucao
            WHEN ( cod_motivo = 666  ) THEN
                ''Ativo''
            WHEN ( pagamento = false ) THEN
                ''Anulado''
            END
        ELSE
            ''Ativo''
        END
    )::varchar as situacao_lancamento

    , parcela_situacao::varchar

    , ( CASE WHEN cod_motivo IS NOT NULL
            OR ( ( pagamento_data IS NOT NULL ) AND ( pagamento = FALSE ) )
        THEN
            CASE WHEN cod_motivo = 666 THEN
                 TRUE
            ELSE FALSE
            END
        ELSE
            CASE WHEN (( parcela_vencimento < now()::date ) AND ( pagamento_data is null ))
            THEN
                CASE WHEN nr_parcela = 0  THEN
                      FALSE
                ELSE TRUE
                END
            ELSE
                TRUE
            END
        END
    )::boolean as parcela_valida

    , numeracao::varchar
    , cod_parcela::integer
    , nr_parcela::integer

    , info_parcela::varchar
    , parcela_vencimento::date
    , to_char ( parcela_vencimento, ''dd/mm/YYYY'' )::varchar as parcela_vencimento_br

    , parcela_valor_normal::numeric
    , parcela_valor_desconto::numeric

    , correcao_aberto::numeric
    , juros_aberto::numeric
    , multa_aberto::numeric


    , coalesce (
        busca_soma.parcela_valor_normal - busca_soma.parcela_valor_desconto
        + busca_soma.juros_aberto + busca_soma.multa_aberto + busca_soma.correcao_aberto
        , 0.00
    )::numeric(14,2) as soma_aberto

    , ( case when cod_motivo is not null THEN
            descricao_devolucao
        WHEN pagamento_data is null AND ( parcela_vencimento <= now()::date AND nr_parcela = 0 )
                and ( baixa_manual_unica = ''nao'' )    THEN
            ''Cancelada''
        ELSE
            CASE WHEN pagamento = false THEN
                pagamento_tipo
            ELSE
                to_char( pagamento_data ,''dd/mm/YYYY'')
            END
        END
    )::varchar as pagamento_data

    , ( CASE WHEN pagamento_data IS NOT NULL THEN
            valor_pago_calculo --( parcela_valor_normal - parcela_valor_desconto )
        ELSE 0.00
        END
    )::numeric as pagamento_valor
    , correcao_pago::numeric
    , juros_pago::numeric
    , multa_pago::numeric
    , diferenca_pago::numeric

    , ( CASE WHEN pagamento_data IS NOT NULL THEN
            coalesce (
                (valor_pago_calculo) + busca_soma.correcao_pago + busca_soma.juros_pago + busca_soma.multa_pago + busca_soma.diferenca_pago
            , 0.00
            )
        ELSE 0.00
        END
    )::numeric(14,2) as soma_pago,

    atividade::varchar

FROM
(    
    SELECT
        busca_pagamento.*
        , (
            SELECT
                nom_resumido
            FROM
                arrecadacao.tipo_pagamento as atp2
                INNER JOIN arrecadacao.pagamento as apag2
                ON apag2.cod_tipo = atp2.cod_tipo
            WHERE
                apag2.numeracao = busca_pagamento.numeracao
                AND apag2.cod_convenio = busca_pagamento.cod_convenio
                AND apag2.ocorrencia_pagamento = busca_pagamento.ocorrencia_pagamento
        ) as nom_resumido
        , (
            SELECT
                atp2.pagamento::boolean
            FROM
                arrecadacao.tipo_pagamento as atp2
                INNER JOIN arrecadacao.pagamento as apag2
                ON apag2.cod_tipo = atp2.cod_tipo
            WHERE
                apag2.numeracao = busca_pagamento.numeracao
                AND apag2.cod_convenio = busca_pagamento.cod_convenio
                AND apag2.ocorrencia_pagamento = busca_pagamento.ocorrencia_pagamento
        ) as pagamento
        , (
            SELECT
                atp2.nom_resumido
            FROM
                arrecadacao.tipo_pagamento as atp2
                INNER JOIN arrecadacao.pagamento as apag2
                ON apag2.cod_tipo = atp2.cod_tipo
            WHERE
                apag2.numeracao = busca_pagamento.numeracao
                AND apag2.cod_convenio = busca_pagamento.cod_convenio
                AND apag2.ocorrencia_pagamento = busca_pagamento.ocorrencia_pagamento
        ) as pagamento_tipo

        , coalesce ( CASE WHEN nr_parcela = 0 THEN
                0.00
            ELSE
                CASE WHEN ( busca_pagamento.pagamento_data IS NULL ) THEN
                    aplica_juro ( busca_pagamento.numeracao, busca_pagamento.exercicio, busca_pagamento.cod_parcela, now()::date )
                WHEN (  busca_pagamento.pagamento_data IS NOT NULL )
                        AND
                     (  busca_pagamento.pagamento_data > busca_pagamento.parcela_vencimento)
                THEN
                    aplica_juro ( busca_pagamento.numeracao, busca_pagamento.exercicio, busca_pagamento.cod_parcela, busca_pagamento.pagamento_data )
                ELSE
                    0.00
                END
            END
        , 0.00 )::numeric(14,2) as juros_aberto

        , coalesce ( CASE WHEN nr_parcela = 0 THEN
                0.00
            ELSE
                CASE WHEN ( busca_pagamento.pagamento_data IS NULL ) THEN
                    aplica_multa ( busca_pagamento.numeracao, busca_pagamento.exercicio, busca_pagamento.cod_parcela, now()::date )
                WHEN ( busca_pagamento.pagamento_data IS NOT NULL )
                        AND
                     (  busca_pagamento.pagamento_data > busca_pagamento.parcela_vencimento)
                THEN
                    aplica_multa ( busca_pagamento.numeracao, busca_pagamento.exercicio, busca_pagamento.cod_parcela, busca_pagamento.pagamento_data )
                ELSE
                    0.00
                END
            END
        , 0.00 )::numeric(14,2) as multa_aberto

        , coalesce ( CASE WHEN nr_parcela = 0 THEN
                0.00
            ELSE
                CASE WHEN ( busca_pagamento.pagamento_data IS NULL ) THEN
                    aplica_correcao ( busca_pagamento.numeracao, busca_pagamento.exercicio, busca_pagamento.cod_parcela, now()::date )
                WHEN ( busca_pagamento.pagamento_data IS NOT NULL )
                        AND
                     (  busca_pagamento.pagamento_data > busca_pagamento.parcela_vencimento)
                THEN
                    aplica_correcao ( busca_pagamento.numeracao, busca_pagamento.exercicio, busca_pagamento.cod_parcela, busca_pagamento.pagamento_data )
                ELSE
                    0.00
                END
            END
        , 0.00 )::numeric(14,2) as correcao_aberto

        , coalesce ( CASE WHEN nr_parcela = 0 THEN
                0.00
            ELSE
                CASE WHEN
                        ( busca_pagamento.pagamento_data IS NOT NULL )
                        AND
                        ( busca_pagamento.parcela_situacao = ''Vencida'' )
                THEN
                    (
                        select coalesce ( sum(valor) , 0.00 )
                        FROM arrecadacao.pagamento_acrescimo
                        WHERE numeracao = busca_pagamento.numeracao
                        AND cod_convenio = busca_pagamento.cod_convenio
                        AND ocorrencia_pagamento = busca_pagamento.ocorrencia_pagamento
                        AND cod_tipo = 1
                    )
                ELSE
                    0.00
                END
            END
        , 0.00 ) as correcao_pago
        ,(
            SELECT
                sum(pagamento_calculo.valor)
            FROM
                arrecadacao.pagamento_calculo
            WHERE 
                pagamento_calculo.numeracao = busca_pagamento.numeracao
                AND pagamento_calculo.cod_convenio = busca_pagamento.cod_convenio
                AND pagamento_calculo.ocorrencia_pagamento = busca_pagamento.ocorrencia_pagamento
        ) AS valor_pago_calculo
        , coalesce ( CASE WHEN nr_parcela = 0 THEN
                0.00
            ELSE
                CASE WHEN
                        ( busca_pagamento.pagamento_data IS NOT NULL )
                        AND
                        ( busca_pagamento.parcela_situacao = ''Vencida'' )
                THEN
                    (
                        select coalesce ( sum(valor) , 0.00 )
                        FROM arrecadacao.pagamento_acrescimo
                        WHERE numeracao = busca_pagamento.numeracao
                        AND cod_convenio = busca_pagamento.cod_convenio
                        AND ocorrencia_pagamento = busca_pagamento.ocorrencia_pagamento
                        AND cod_tipo = 2
                    )
                ELSE
                    0.00
                END
            END
        , 0.00 ) as juros_pago
        , coalesce ( CASE WHEN nr_parcela = 0 THEN
                0.00
            ELSE
                CASE WHEN
                        ( busca_pagamento.pagamento_data IS NOT NULL )
                        AND
                        ( busca_pagamento.parcela_situacao = ''Vencida'' )
                THEN
                    (
                        select coalesce ( sum(valor) , 0.00 )
                        FROM arrecadacao.pagamento_acrescimo
                        WHERE numeracao = busca_pagamento.numeracao
                        AND cod_convenio = busca_pagamento.cod_convenio
                        AND ocorrencia_pagamento = busca_pagamento.ocorrencia_pagamento
                        AND cod_tipo = 3
                    )
                ELSE
                    0.00
                END
            END
        , 0.00 ) as multa_pago
        , coalesce ( CASE WHEN nr_parcela = 0 THEN
                0.00
            ELSE
                CASE WHEN
                        ( busca_pagamento.pagamento_data IS NOT NULL )
                        AND
                        ( busca_pagamento.parcela_situacao = ''Vencida'' )
                THEN
                    (
                        select coalesce ( sum(valor) , 0.00 )
                        FROM arrecadacao.pagamento_diferenca
                        WHERE numeracao = busca_pagamento.numeracao
                        AND cod_convenio = busca_pagamento.cod_convenio
                        AND ocorrencia_pagamento = busca_pagamento.ocorrencia_pagamento
                    )
                ELSE
                    0.00
                END
            END
        , 0.00 ) as diferenca_pago
    FROM
    (
        SELECT
            busca_carne.*
            , ( CASE WHEN ( valor_desconto > 0.00 ) THEN
                    CASE WHEN apag.data_pagamento IS NULL THEN
                        CASE WHEN ( now()::date <= parcela_vencimento ) THEN
                            parcela_valor_normal - valor_desconto
                        ELSE
                            0.00
                        END
                    ELSE
                        CASE WHEN ( apag.data_pagamento <= parcela_vencimento ) THEN
                            parcela_valor_normal - valor_desconto
                        ELSE
                            0.00
                        END
                    END
                ELSE
                    0.00
                END
            ) as parcela_valor_desconto
            , apag.data_pagamento as pagamento_data
            , coalesce (apag.valor, 0.00) as pagamento_valor
            --, coalesce (apag.valor, 0.00) as pagamento_valor
            , max(apag.ocorrencia_pagamento) as ocorrencia_pagamento

        FROM
        (
            SELECT
                busca_parcelas.*
                , carne.numeracao
                , carne.cod_convenio

                , ( CASE WHEN ((pagamento.numeracao is not null) and (acd.numeracao IS not NULL)) THEN
                                 666 
                        ELSE
                                  acd.cod_motivo
                        END ) as cod_motivo

                , ( CASE WHEN ((pagamento.numeracao is not null) and (acd.numeracao IS not NULL)) THEN
                                  to_char ( pagamento.data_pagamento,''dd/mm/YYYY'' ) 
                        ELSE
                                   amd.descricao_resumida
                        END ) as descricao_devolucao

            FROM
                (
                    SELECT DISTINCT
                        busca_situacao_lancamentos.*
                        , ap.cod_parcela
                        , ap.nr_parcela as nr_parcela
                        , ( CASE WHEN ap.nr_parcela = 0 THEN
                                ''Única''
                            ELSE
                                ap.nr_parcela ||''/''||busca_situacao_lancamentos.total_parcelas
                            END
                        )::varchar as info_parcela
                        , ap.parcela_valor_normal
                        , ap.valor_desconto
                        , ap.parcela_vencimento
                        , ap.parcela_situacao
                    FROM
                        (
                            SELECT DISTINCT
                                busca_lancamentos.*
                                , arrecadacao.fn_total_parcelas (cod_lancamento) as total_parcelas
                            FROM
                            (
                                SELECT DISTINCT
                                    inscricao::integer
                                    , tipo_inscricao::varchar
                                    , al.cod_lancamento
                                    , cgm.numcgm as contribuinte_numcgm
                                    , cgm.nom_cgm as contribuinte_nomcgm
                                    , cod_condominio::varchar
                                    , nom_condominio::varchar
                                    , agc.cod_grupo
                                    , ( CASE WHEN agc.cod_grupo IS NULL THEN
                                        ac.cod_credito ELSE NULL END
                                    ) as cod_credito
                                    , ( CASE WHEN agc.cod_grupo IS NULL THEN
                                        ac.cod_especie ELSE NULL END
                                    ) as cod_especie
                                    , ( CASE WHEN agc.cod_grupo IS NULL THEN
                                        ac.cod_genero ELSE NULL END
                                    ) as cod_genero
                                    , ( CASE WHEN agc.cod_grupo IS NULL THEN
                                        ac.cod_natureza ELSE NULL END
                                    ) as cod_natureza

                                    , ( CASE WHEN agc.cod_grupo IS NOT NULL THEN
                                            agc.descricao || '' / '' || ac.exercicio
                                        ELSE
                                            split_part (
                                                monetario.fn_busca_mascara_credito (
                                                    ac.cod_credito
                                                    , ac.cod_especie
                                                    , ac.cod_genero
                                                    , ac.cod_natureza
                                                ), ''§'', 1
                                            )||'' - ''||
                                            split_part (
                                                monetario.fn_busca_mascara_credito (
                                                    ac.cod_credito
                                                    , ac.cod_especie
                                                    , ac.cod_genero
                                                    , ac.cod_natureza
                                                ), ''§'', 6
                                            )
                                        END
                                    ) as origem
                                    , ac.exercicio::int
                                    , baixa_manual_unica.valor as baixa_manual_unica
                                    '|| stAtividade ||'

                                FROM
                                    (
                                        '|| stBuscaCalculos ||'

                                    ) as busca_calculos

                                    INNER JOIN arrecadacao.calculo as ac
                                    ON ac.cod_calculo = busca_calculos.cod_calculo
                                    AND ac.exercicio = busca_calculos.exercicio::VARCHAR

                                    INNER JOIN arrecadacao.calculo_cgm as ccgm
                                    ON ccgm.cod_calculo = ac.cod_calculo
                                    INNER JOIN sw_cgm as cgm
                                    ON cgm.numcgm = ccgm.numcgm

                                    '|| stAuxiliarInscricoes ||'

                                    LEFT JOIN arrecadacao.calculo_grupo_credito as acgc
                                    ON acgc.cod_calculo = ac.cod_calculo
                                    AND acgc.ano_exercicio = ac.exercicio

                                    LEFT JOIN arrecadacao.grupo_credito as agc
                                    ON agc.cod_grupo = acgc.cod_grupo
                                    AND agc.ano_exercicio = acgc.ano_exercicio

                                    LEFT JOIN (
                                        select
                                            exercicio
                                            , valor
                                        from
                                            administracao.configuracao
                                        where parametro = ''baixa_manual_unica''
                                    ) as baixa_manual_unica
                                    ON baixa_manual_unica.exercicio = ac.exercicio

                                    INNER JOIN (
                                        SELECT
                                            cod_lancamento
                                            , max(cod_calculo) as cod_calculo
                                        FROM
                                            arrecadacao.lancamento_calculo alc
                                        GROUP BY
                                            cod_lancamento
                                    ) as alc
                                    ON alc.cod_calculo = ac.cod_calculo

                                    INNER JOIN arrecadacao.lancamento as al
                                    ON al.cod_lancamento = alc.cod_lancamento

                                '|| stExercicioWhere ||'

                                '|| stFiltroGrupoWHERE     ||'
                                '|| stFiltroCreditoWHERE   ||'

                            ) as busca_lancamentos

                        ) as busca_situacao_lancamentos

                        INNER JOIN (
                            SELECT
                                ap.cod_lancamento
                                , ( CASE WHEN apr.cod_parcela IS NULL THEN
                                    ap.cod_parcela
                                ELSE
                                    apr.cod_parcela
                                END
                                ) as cod_parcela
                                , ( CASE WHEN apr.cod_parcela IS NULL THEN
                                    ap.valor
                                ELSE
                                    apr.valor
                                END
                                ) as parcela_valor_normal
                                , ap.nr_parcela
                                , apd.valor as valor_desconto
                                , ap.vencimento as parcela_vencimento
                                , (
                                    CASE WHEN ( ap.nr_parcela = 0 ) THEN
                                        CASE WHEN
                                            ( ap.vencimento <= now()::date )
                                        THEN
                                            ''Vencida''
                                        ELSE
                                            ''OK''
                                        END
                                    ELSE
                                        CASE WHEN
                                            ( ap.vencimento <= now()::date )
                                        THEN
                                            ''Vencida''
                                        ELSE
                                            ''OK''
                                        END
                                    END
                                ) as parcela_situacao
                            FROM
                                arrecadacao.parcela as ap
                                LEFT JOIN arrecadacao.parcela_reemissao apr
                                ON apr.cod_parcela = ap.cod_parcela
                                LEFT JOIN arrecadacao.parcela_desconto as apd
                                ON apd.cod_parcela = ap.cod_parcela

                            '|| stFiltroValor ||'

                            ) as ap
                            ON ap.cod_lancamento = busca_situacao_lancamentos.cod_lancamento

                        ) as busca_parcelas

                        INNER JOIN arrecadacao.carne
                        ON carne.cod_parcela = busca_parcelas.cod_parcela

                        LEFT JOIN arrecadacao.pagamento
                        ON pagamento.numeracao = carne.numeracao

                        LEFT JOIN arrecadacao.carne_devolucao as acd
                        ON acd.numeracao = carne.numeracao
                        AND acd.cod_convenio = carne.cod_convenio
                        LEFT JOIN arrecadacao.motivo_devolucao as amd
                        ON amd.cod_motivo = acd.cod_motivo

                    WHERE

                        CASE WHEN ((pagamento.numeracao is not null) and (acd.numeracao IS not NULL)) THEN
                                   pagamento.numeracao is not null
                        ELSE
                                   acd.numeracao is null
                        END
                ) as busca_carne

                LEFT JOIN arrecadacao.pagamento as apag
                ON apag.numeracao = busca_carne.numeracao
                AND apag.cod_convenio = busca_carne.cod_convenio

            '|| stFiltroSituacao ||'

            GROUP BY
                inscricao, tipo_inscricao
                , cod_lancamento, total_parcelas, info_parcela , baixa_manual_unica
                , busca_carne.numeracao, busca_carne.cod_convenio, busca_carne.exercicio, busca_carne.origem
                , busca_carne.contribuinte_numcgm, busca_carne.contribuinte_nomcgm
                , busca_carne.cod_grupo, valor_desconto, cod_motivo, descricao_devolucao
                , cod_condominio , nom_condominio
                , busca_carne.cod_credito, busca_carne.cod_especie, busca_carne.cod_genero, busca_carne.cod_natureza
                , cod_parcela, nr_parcela, parcela_valor_normal, parcela_valor_desconto, parcela_vencimento, parcela_situacao
                , apag.data_pagamento, apag.valor, atividade

        ) as busca_pagamento

    ) as busca_soma

ORDER BY
    cod_condominio, inscricao, exercicio, cod_lancamento, nr_parcela ASC
) as busca_situacoes
WHERE
    (
        CASE WHEN busca_situacoes.pagamento_data = ''Anulação'' OR busca_situacoes.parcela_valida = false  THEN
            FALSE
        ELSE TRUE END
    )

    ';
    
    raise notice '%', stSqlPrincipal;

        FOR reRecord IN EXECUTE stSqlPrincipal LOOP
            return next reRecord;
        END LOOP;

    RETURN ;

END;
$$ LANGUAGE 'plpgsql';