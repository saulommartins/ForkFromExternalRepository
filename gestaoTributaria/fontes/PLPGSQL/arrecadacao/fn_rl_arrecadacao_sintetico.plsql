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
* $Id: fn_rl_arrecadacao_sintetico.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.13
*/

/*
$Log$
Revision 1.3  2007/05/21 18:35:59  dibueno
raise comentado

Revision 1.2  2007/05/02 19:31:24  dibueno
Bug #9168#

Revision 1.1  2007/04/20 13:24:46  dibueno
Bug #9168#


*/

CREATE OR REPLACE FUNCTION arrecadacao.fn_rl_arrecadacao_sintetico ( VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR )
--RETURNS NUMERIC(14,2) AS '
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

    inCodLogradouroInicial  ALIAS FOR $9;
    inCodLogradouroFinal    ALIAS FOR $10;

    inCodCondominioInicial  ALIAS FOR $11;
    inCodCondominioFinal    ALIAS FOR $12;

    inExercicio             ALIAS FOR $13;

    inCodLogradouroTMP      VARCHAR := '''';
    stAuxiliarEDI           VARCHAR := '''';
    nuResultado             NUMERIC := 0.00;
    flSomatorio             NUMERIC := 0.00;

    stFiltro                VARCHAR := '''';
    stAuxiliar              VARCHAR := '''';
    stExercicio             VARCHAR := '''';
    stExercicioWhere        VARCHAR := '''';
    stExercicioAnd          VARCHAR := '''';
    stSqlCreditos           VARCHAR := '''';
    stAuxiliarCond          VARCHAR := '''';
    stAuxiliarCondTMP       VARCHAR := '''';
    stAuxiliarLogradouro    VARCHAR := '''';
    stAuxiliarLogradouroTMP VARCHAR := '''';

    stCalcImoveis           VARCHAR := '''';
    stCalcEmpresas          VARCHAR := '''';

    boCondominio            BOOLEAN := false;
    boLogradouro            BOOLEAN := false;
    boCredito               BOOLEAN := false;

    reRecord         RECORD;

BEGIN

    IF inExercicio != '''' THEN
        stExercicio := '' ac.exercicio = ''|| inExercicio ||'' AND'';
        stExercicioWhere := '' WHERE calc.exercicio = ''|| inExercicio;
        stExercicioAnd   := '' AND calc.exercicio = ''|| inExercicio;
    END IF;



    IF ( inCodCreditoInicial != '''' OR inCodCreditoFinal != '''' ) THEN

        IF ( inCodCreditoInicial != '''' AND inCodCreditoFinal != '''' ) THEN

            stAuxiliar := '' ( ac.cod_credito BETWEEN ''||inCodCreditoInicial;
            stAuxiliar := stAuxiliar ||'' AND ''||inCodCreditoFinal||'' ) '';

            stAuxiliar := stAuxiliar ||'' AND ( ac.cod_especie between ''||inCodEspecieInicial;
            stAuxiliar := stAuxiliar ||'' AND ''||inCodEspecieFinal||'' ) '';

            stAuxiliar := stAuxiliar ||'' AND ( ac.cod_genero between ''||inCodGeneroInicial;
            stAuxiliar := stAuxiliar ||'' AND ''||inCodGeneroFinal||'' ) '';

            stAuxiliar := stAuxiliar ||'' AND ( ac.cod_natureza between ''||inCodNaturezaInicial;
            stAuxiliar := stAuxiliar ||'' AND ''||inCodNaturezaFinal||'' ) '';

        ELSE

            stAuxiliar := '' ac.cod_credito = ''|| inCodCreditoInicial || inCodCreditoFinal;
            stAuxiliar := stAuxiliar ||'' AND ac.cod_especie    = ''||inCodEspecieInicial||inCodEspecieFinal;
            stAuxiliar := stAuxiliar ||'' AND ac.cod_genero     = ''||inCodGeneroInicial||inCodGeneroFinal;
            stAuxiliar := stAuxiliar ||'' AND ac.cod_natureza   = ''||inCodNaturezaInicial||inCodNaturezaFinal;

        END IF;

            stAuxiliar := stAuxiliar ||'' AND'';

    END IF;


    IF ( inCodCondominioInicial != '''' OR inCodCondominioFinal != '''' ) THEN

        IF ( inCodCreditoInicial != '''' AND inCodCondominioFinal != '''' ) THEN
            stAuxiliarCondTMP := '' IC.cod_condominio between ''||inCodCondominioInicial;
            stAuxiliarCondTMP := stAuxiliarCondTMP ||'' AND '' || inCodCondominioFinal;
        ELSE
            stAuxiliarCondTMP := '' IC.cod_condominio = ''||inCodCondominioInicial||inCodCondominioFinal;
        END IF;


        stAuxiliarCond := ''
            ac.cod_calculo IN  (
                SELECT aic.cod_calculo
                FROM
                    arrecadacao.imovel_calculo as aic
                    INNER JOIN arrecadacao.calculo as ac
                    ON ac.cod_calculo = aic.cod_calculo

                WHERE
                    ''|| stExercicio || stAuxiliar || ''
                    aic.inscricao_municipal IN (
                        SELECT
                            IIC.inscricao_municipal
                        FROM
                            imobiliario.imovel_condominio as IIC
                            INNER JOIN imobiliario.condominio as IC
                            ON IIC.cod_condominio = IC.cod_condominio

                        WHERE
                            ''|| stAuxiliarCondTMP ||''

                        ORDER BY IIC.inscricao_municipal
                    )
            )
        AND'';

    END IF;



    IF inCodLogradouroInicial != '''' OR inCodLogradouroFinal != '''' THEN


        IF ( inCodLogradouroInicial != '''' AND inCodLogradouroFinal != '''' ) THEN
            -- eh intervalo
            stAuxiliarLogradouroTMP := '' ( LO.cod_logradouro between ''|| inCodLogradouroInicial ||'' AND ''|| inCodLogradouroFinal ||'' ) '';
            stAuxiliarEDI := '' ( EDI.cod_logradouro between ''|| inCodLogradouroInicial ||'' AND ''|| inCodLogradouroFinal ||'' ) '';
        ELSE
            inCodLogradouroTMP := inCodLogradouroInicial || inCodLogradouroFinal ;
            stAuxiliarLogradouroTMP := '' LO.cod_logradouro = ''|| inCodLogradouroInicial ;
            stAuxiliarEDI := '' EDI.cod_logradouro = ''|| inCodLogradouroInicial || inCodLogradouroFinal ;
        END IF;



        stAuxiliarLogradouro := ''
            ac.cod_calculo IN (

                    SELECT
                        aic.cod_calculo
                    FROM
                        sw_logradouro as LO

                        INNER JOIN imobiliario.confrontacao_trecho as CT
                        ON CT.cod_logradouro = LO.cod_logradouro

                        INNER JOIN imobiliario.imovel_confrontacao as IC
                        ON IC.cod_lote = CT.cod_lote

                        INNER JOIN arrecadacao.imovel_calculo as aic
                        ON aic.inscricao_municipal = IC.inscricao_municipal

                        INNER JOIN arrecadacao.calculo as ac
                        ON ac.cod_calculo = aic.cod_calculo

                    WHERE
                        ''|| stExercicio || stAuxiliar || stAuxiliarLogradouroTMP || ''

                UNION

                    SELECT
                        cec.cod_calculo
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
                                        ''|| stAuxiliarLogradouroTMP ||''

                            UNION
                                        select
                                            EDI.inscricao_economica
                                            , EDI.timestamp
                                        from
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
                                            
                                        where
                                            ''|| stAuxiliarEDI ||''
                            ) as res

                            LEFT JOIN economico.baixa_cadastro_economico as ebce
                            ON ebce.inscricao_economica = res.inscricao_economica
                            and ebce.dt_termino is null

                            WHERE
                                ebce.inscricao_economica is null
                        ) as temp

                        INNER JOIN arrecadacao.cadastro_economico_calculo as cec
                        ON cec.inscricao_economica = temp.inscricao_economica


            ) AND '';


    END IF;



    stFiltro := stExercicio || stAuxiliar || stAuxiliarCond || stAuxiliarLogradouro;
    stFiltro := substring( stFiltro from 1 for ( char_length(stFiltro) - 4 ));



    /****========================================================================*/
    /****===============          QUERY PRINCIPAL        ========================*/
    /****========================================================================*/

    stSqlCreditos = ''

        SELECT

            mc.descricao_credito
            , split_part ( monetario.fn_busca_mascara_credito( mc.cod_credito, mc.cod_especie, mc.cod_genero, mc.cod_natureza ), ''''§'''', 1 )::varchar as credito_formatado

            , mc.cod_credito
            , mc.cod_especie
            , mc.cod_genero
            , mc.cod_natureza

            , coalesce ( sum (apagc.valor), 0.00 ) as pago

            , coalesce ( sum (apagJuros.valor), 0.00 ) as pagoJuros
            , coalesce ( sum (apagMulta.valor), 0.00 ) as pagoMulta
            , coalesce ( sum (apagCorrecao.valor), 0.00 ) as pagoCorrecao

            , coalesce ( sum (apagDiff.valor), 0.00 ) as pagoDiferenca

            , coalesce ( coalesce ( sum (apagc.valor), 0.00 ) + coalesce ( sum (apagJuros.valor), 0.00 )
                + coalesce ( sum (apagMulta.valor), 0.00 ) + coalesce ( sum (apagCorrecao.valor), 0.00 )
                + coalesce ( sum (apagDiff.valor), 0.00 )
            ) as pago_total

            , coalesce ( sum (alc.valor), 0.00 ) as lancado

            , coalesce ( ( sum(alc.valor) - coalesce ( sum (apagc.valor), 0.00 ) - coalesce ( sum (apagJuros.valor), 0.00 ) - coalesce ( sum (apagMulta.valor), 0.00 ) - coalesce ( sum (apagCorrecao.valor), 0.00 ) - coalesce ( sum (apagDiff.valor), 0.00 ) ), 0.00 ) as em_aberto
            --, sum(ap.valor) as soma_parcelas

        FROM

            arrecadacao.calculo as ac

            INNER JOIN monetario.credito as mc
            ON mc.cod_credito = ac.cod_credito
            AND mc.cod_especie = ac.cod_especie
            AND mc.cod_genero = ac.cod_genero
            AND mc.cod_natureza = ac.cod_natureza

            INNER JOIN arrecadacao.lancamento_calculo as alc
            ON alc.cod_calculo = ac.cod_calculo

            LEFT JOIN (
                SELECT
                    apagc.cod_calculo
                    , calc.exercicio
                    , apagc.ocorrencia_pagamento
                    , sum ( apagc.valor ) as valor
                FROM
                    arrecadacao.pagamento_calculo as apagc
                    INNER JOIN arrecadacao.calculo as calc
                    ON calc.cod_calculo = apagc.cod_calculo

                    INNER JOIN arrecadacao.pagamento as apag
                    ON apag.numeracao = apagc.numeracao
                    AND apag.cod_convenio = apagc.cod_convenio
                    AND apag.ocorrencia_pagamento = apagc.ocorrencia_pagamento

                    INNER JOIN (
                        SELECT
                            max(ocorrencia_pagamento) as ocorrencia_pagamento
                            , numeracao
                            , cod_convenio
                        FROM arrecadacao.pagamento
                        GROUP BY numeracao, cod_convenio
                    ) as apag2
                    ON apag2.numeracao = apag.numeracao
                    AND apag2.cod_convenio = apag.cod_convenio
                    AND apag2.ocorrencia_pagamento = apag.ocorrencia_pagamento

                ''|| stExercicioWhere ||''
                GROUP BY apagc.cod_calculo, calc.exercicio, apagc.ocorrencia_pagamento
            ) as apagc
            ON apagc.cod_calculo = ac.cod_calculo
            AND apagc.exercicio = ac.exercicio

            LEFT JOIN (
                SELECT
                    apagj.cod_calculo
                    , calc.exercicio
                    , apagj.ocorrencia_pagamento
                    , sum ( apagj.valor ) as valor
                FROM
                    arrecadacao.pagamento_acrescimo as apagj
                    INNER JOIN arrecadacao.calculo as calc
                    ON calc.cod_calculo = apagj.cod_calculo
                WHERE apagj.cod_tipo = 2 ''|| stExercicioAnd ||''
                GROUP BY apagj.cod_calculo, calc.exercicio, apagj.ocorrencia_pagamento
            ) as apagJuros
            ON apagJuros.cod_calculo = ac.cod_calculo
            AND apagJuros.exercicio = ac.exercicio
            AND apagJuros.ocorrencia_pagamento = apagc.ocorrencia_pagamento

            LEFT JOIN (
                SELECT
                    apagm.cod_calculo
                    , calc.exercicio
                    , apagm.ocorrencia_pagamento
                    , sum ( apagm.valor ) as valor
                FROM
                    arrecadacao.pagamento_acrescimo as apagm
                    INNER JOIN arrecadacao.calculo as calc
                    ON calc.cod_calculo = apagm.cod_calculo
                WHERE apagm.cod_tipo = 3 ''|| stExercicioAnd ||''
                GROUP BY apagm.cod_calculo, calc.exercicio, apagm.ocorrencia_pagamento
            ) as apagMulta
            ON apagMulta.cod_calculo = ac.cod_calculo
            AND apagMulta.exercicio = ac.exercicio
            AND apagMulta.ocorrencia_pagamento = apagc.ocorrencia_pagamento

            LEFT JOIN (
                SELECT
                    apagcorrecao.cod_calculo
                    , calc.exercicio
                    , apagcorrecao.ocorrencia_pagamento
                    , sum ( apagcorrecao.valor ) as valor
                FROM
                    arrecadacao.pagamento_acrescimo as apagcorrecao
                    INNER JOIN arrecadacao.calculo as calc
                    ON calc.cod_calculo = apagcorrecao.cod_calculo
                WHERE apagcorrecao.cod_tipo = 1 ''|| stExercicioAnd ||''
                GROUP BY apagcorrecao.cod_calculo, calc.exercicio, apagcorrecao.ocorrencia_pagamento
            ) as apagCorrecao
            ON apagCorrecao.cod_calculo = ac.cod_calculo
            AND apagCorrecao.exercicio = ac.exercicio
            AND apagCorrecao.ocorrencia_pagamento = apagc.ocorrencia_pagamento


            LEFT JOIN (
                SELECT
                    apagDiff.cod_calculo
                    , calc.exercicio
                    , apagDiff.ocorrencia_pagamento
                    , sum ( apagDiff.valor ) as valor
                FROM
                    arrecadacao.pagamento_diferenca as apagDiff
                    INNER JOIN arrecadacao.calculo as calc
                    ON calc.cod_calculo = apagDiff.cod_calculo
                ''|| stExercicioWhere ||''
                GROUP BY apagDiff.cod_calculo, calc.exercicio, apagDiff.ocorrencia_pagamento
            ) as apagDiff
            ON apagDiff.cod_calculo = ac.cod_calculo
            AND apagDiff.exercicio = ac.exercicio
            AND apagDiff.ocorrencia_pagamento = apagc.ocorrencia_pagamento

        WHERE

            ''|| stFiltro ||''

        GROUP BY
            mc.cod_credito, mc.cod_especie, mc.cod_genero, mc.cod_natureza, mc.descricao_credito

   
    '';



        FOR reRecord IN EXECUTE stSqlCreditos LOOP
                
            return next reRecord;

        END LOOP;

    
    RETURN ;


END;
' LANGUAGE 'plpgsql';
