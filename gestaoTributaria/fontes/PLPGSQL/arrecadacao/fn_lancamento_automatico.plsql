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
* $Id: fn_lancamento_automatico.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.05
*/

CREATE OR REPLACE FUNCTION arrecadacao.fn_lancamento_automatico(integer, integer) RETURNS varchar AS '
DECLARE
    inCodGrupo          ALIAS FOR $1;
    inAnoExercicio      ALIAS FOR $2;
    stRetorno           VARCHAR;
    stSql               VARCHAR;
    stSql2              VARCHAR;
    stExecuta           VARCHAR;
    stSql3              VARCHAR;
    stFuncaoNumeracao   VARCHAR;
    reRegistro          RECORD;
    reRegistro2         RECORD;
    inCodModulo         INTEGER;
    varCursor           REFCURSOR;
    flValorDesoneracao  NUMERIC; --guarda valor de desoneracao do credito
    flValorTotal        NUMERIC; --soma o valor dos creditos do imovel
    flValorTotalGeral   NUMERIC; --soma o valor dos creditos do imovel ja utilizando a desoneracao e abono
    flValorAbono        NUMERIC; --soma do valor de abono cod_desoneracao = 1
    flValorPercent      NUMERIC; --serve apenas pra realizar calculos percentuais
    flValorMinimo       NUMERIC; --guarda valor que esta na tabela calendario fiscal
    flValorMinimoLanc   NUMERIC; --guarda valor que esta na tabela calendario fiscal
    flValorMinimoParc   NUMERIC; --guarda valor que esta na tabela calendario fiscal
    flTMPval            NUMERIC;
    flTMPval2           NUMERIC;
    inTotalParcelasNorm INTEGER; --total de parcelas normais
    inTotalParcNormUsar INTEGER; --total de parcelas normais que serao utilizadas
    boUtilUnica         BOOLEAN; --flag avisa se deve utilizar parcela unica
    dtVencParcUnica     DATE;    --guarda data de vencimento da parcela unica
    inCodLancamento     INTEGER; --codigo do lancamento
    inCodCalculo        INTEGER; --codigo do calculo
    inCodParcela        INTEGER; --codigo da parcela
    inOcorrenciaAbono   INTEGER; --numero de ocorrencia do abono
    inNumCGMAbono       INTEGER; --numero do cgm do abono
    inCodVenc           INTEGER; --codigo de vencimento para tabela vencimento parcelas
    inLaco              INTEGER; --usado apenas para contar dados em lacos (temporario)
    inLaco2             INTEGER; --usado apenas para lacos
    inLaco3             INTEGER; --usado apenas para lacos
    flValorParcela      NUMERIC; --valor a pagar na parcela normal
    flValorParcelaUm    NUMERIC; --valor a pagar na parcela normal 1
    flValorParcelaDesc  NUMERIC; --valor a pagar na parcela normal com desconto
    inNumeroCarne       VARCHAR; --numero do carne a utilizar
    inCodConvenio       VARCHAR; --necessario para recuperar numeracao do carne
    inCodCarteira       VARCHAR; --necessario para recuperar numeracao do carne
    flValorPorCredito   NUMERIC[];
    inTotaldeCreditos   INTEGER;
    boUtilizarDesconto  BOOLEAN[];

BEGIN
    stRetorno := '''';

    SELECT
        grupo_credito.cod_modulo,
        calendario_fiscal.valor_minimo,
        calendario_fiscal.valor_minimo_lancamento,
        calendario_fiscal.valor_minimo_parcela

    INTO inCodModulo, flValorMinimo, flValorMinimoLanc, flValorMinimoParc

    FROM
        arrecadacao.grupo_credito

    INNER JOIN
        arrecadacao.calendario_fiscal
    ON
        calendario_fiscal.cod_grupo = grupo_credito.cod_grupo
        AND calendario_fiscal.ano_exercicio = grupo_credito.ano_exercicio

    WHERE
        grupo_credito.cod_grupo = inCodGrupo
        AND grupo_credito.ano_exercicio = inAnoExercicio;


    IF inCodModulo = 12 THEN
            stSql := ''
                        SELECT DISTINCT imovel.inscricao_municipal                                          AS inscricao
                          FROM imobiliario.imovel
                     LEFT JOIN (
                                     SELECT tmp.*
                                       FROM imobiliario.baixa_imovel                                        AS tmp
                                 INNER JOIN (
                                              SELECT MAX(baixa_imovel.timestamp)                            AS timestamp
                                                   , baixa_imovel.inscricao_municipal
                                                FROM imobiliario.baixa_imovel
                                            GROUP BY inscricao_municipal
                                            )                                                               AS tmp2
                                         ON tmp.inscricao_municipal = tmp2.inscricao_municipal
                                        AND tmp.timestamp           = tmp2.timestamp
                               )                                                                            AS bi --baixa_imovel
                            ON bi.inscricao_municipal               = imovel.inscricao_municipal
                    INNER JOIN arrecadacao.imovel_calculo                                                   AS AIC
                            ON AIC.inscricao_municipal              = imovel.inscricao_municipal
                    INNER JOIN arrecadacao.calculo_grupo_credito                                            AS ACGC
                            ON ACGC.cod_calculo                     = AIC.cod_calculo
                           AND ACGC.cod_grupo                       = '' || inCodGrupo     || ''
                           AND ACGC.ano_exercicio                   = '' || inAnoExercicio || ''
                     LEFT JOIN arrecadacao.lancamento_calculo                                               AS ALC
                            ON ALC.cod_calculo                      = AIC.cod_calculo
                           AND ALC.cod_calculo                      IS NULL
                         WHERE ( ( bi.inscricao_municipal IS NULL ) OR ( (bi.inscricao_municipal IS NOT NULL) AND (bi.dt_termino IS NOT NULL) ) )

--                AND imovel.inscricao_municipal = 818359 --para testes remover depois
                --LIMIT 10 --para testes remover depois

                --AND imovel.inscricao_municipal BETWEEN     1 AND 200
--                AND imovel.inscricao_municipal BETWEEN  1001 AND 2000
--                AND imovel.inscricao_municipal BETWEEN  2001 AND 3000
--                AND imovel.inscricao_municipal BETWEEN  3001 AND 4000
--                AND imovel.inscricao_municipal BETWEEN  4001 AND 5000
--                AND imovel.inscricao_municipal BETWEEN  5001 AND 6000
--                AND imovel.inscricao_municipal BETWEEN  6001 AND 7000
--                AND imovel.inscricao_municipal BETWEEN  7001 AND 8000
--                AND imovel.inscricao_municipal BETWEEN  8001 AND 9000
--                AND imovel.inscricao_municipal BETWEEN  9001 AND 10000
--                AND imovel.inscricao_municipal BETWEEN 10001 AND 11000
--                AND imovel.inscricao_municipal BETWEEN 11001 AND 12000
--                AND imovel.inscricao_municipal BETWEEN 12001 AND 13000
--                AND imovel.inscricao_municipal BETWEEN 13001 AND 14000
--                AND imovel.inscricao_municipal BETWEEN 14001 AND 15000
--                AND imovel.inscricao_municipal BETWEEN 15001 AND 16000
--                AND imovel.inscricao_municipal BETWEEN 16001 AND 17000
--                AND imovel.inscricao_municipal BETWEEN 17001 AND 18000
--                AND imovel.inscricao_municipal BETWEEN 18001 AND 19000
--                AND imovel.inscricao_municipal BETWEEN 19001 AND 31000
--                AND imovel.inscricao_municipal >       31000

            '';
    ELSE
            stSql := ''
                        SELECT DISTINCT cadastro_economico.inscricao_economica                              AS inscricao
                          FROM economico.cadastro_economico
                     LEFT JOIN (
                                     SELECT tmp.*
                                       FROM economico.baixa_cadastro_economico                              AS tmp
                                 INNER JOIN (
                                              SELECT MAX(baixa_cadastro_economico.timestamp)                AS timestamp
                                                   , baixa_cadastro_economico.inscricao_economica
                                                FROM economico.baixa_cadastro_economico
                                            GROUP BY inscricao_economica
                                            )                                                               AS tmp2
                                         ON tmp.inscricao_economica = tmp2.inscricao_economica
                                        AND tmp.timestamp           = tmp2.timestamp
                               )                                                                            AS bc --baixa_empresa
                            ON bc.inscricao_economica               = cadastro_economico.inscricao_economica
                    INNER JOIN arrecadacao.cadastro_economico_calculo                                       AS ACEC
                            ON ACEC.inscricao_economica             = cadastro_economico.inscricao_economica
                    INNER JOIN arrecadacao.calculo_grupo_credito                                            AS ACGC
                            ON ACGC.cod_calculo                     = ACEC.cod_calculo
                           AND ACGC.cod_grupo                       = '' || inCodGrupo     || ''
                           AND ACGC.ano_exercicio                   = '' || inAnoExercicio || ''
                     LEFT JOIN arrecadacao.lancamento_calculo                                               AS ALC
                            ON ALC.cod_calculo                      = ACEC.cod_calculo
                           AND ALC.cod_calculo                      IS NULL
                         WHERE ( ( bc.inscricao_economica IS NULL ) OR ( (bc.inscricao_economica IS NOT NULL) AND (bc.dt_termino IS NOT NULL) ) )

                --LIMIT 1 --para testes remover depois
            '';
    END IF;

    FOR reRegistro IN EXECUTE stSql LOOP

        if ( inCodModulo = 12 ) THEN
            stSql2 := ''
                SELECT
                    calculo.cod_calculo,
                    calculo.valor,
                    calculo.timestamp AS timestamp_calculo,
                    imovel_calculo.inscricao_municipal AS inscricao,
                    desonerar.funcao_desoneracao,
                    desonerar.cod_desoneracao,
                    (
                        SELECT
                            desonerado.numcgm
                        FROM
                            arrecadacao.desonerado
                        WHERE
                            desonerado.cod_desoneracao = desonerar.cod_desoneracao
                            AND desonerado.ocorrencia = desonerar.desoneracao_ocorrido
                        LIMIT 1
                    )AS numcgm_desonerado,
                    desonerar.desoneracao_ocorrido,
                    credito_grupo.desconto

                FROM
                    arrecadacao.calculo_grupo_credito

                INNER JOIN
                    arrecadacao.calculo
                ON
                    calculo.cod_calculo = calculo_grupo_credito.cod_calculo
                    AND calculo.ativo = true

                INNER JOIN
                    arrecadacao.credito_grupo
                ON
                    credito_grupo.cod_grupo = calculo_grupo_credito.cod_grupo
                    AND credito_grupo.ano_exercicio = calculo_grupo_credito.ano_exercicio
                    AND credito_grupo.cod_credito = calculo.cod_credito
                    AND credito_grupo.cod_especie = calculo.cod_especie
                    AND credito_grupo.cod_genero = calculo.cod_genero
                    AND credito_grupo.cod_natureza = calculo.cod_natureza

                INNER JOIN
                    arrecadacao.imovel_calculo
                ON
                    imovel_calculo.cod_calculo = calculo.cod_calculo

                LEFT JOIN
                    (
                        SELECT DISTINCT
                            desoneracao.cod_credito,
                            desoneracao.cod_especie,
                            desoneracao.cod_genero,
                            desoneracao.cod_natureza,
                            desonerado_imovel.inscricao_municipal,

                            CASE WHEN ( desonerado_imovel.cod_desoneracao IS NOT NULL ) AND ( desoneracao.cod_desoneracao IS NOT NULL ) THEN
                                CASE WHEN ( ( desoneracao.revogavel = true ) AND ( desonerado.data_revogacao <= now() ) ) THEN
                                    ''''vazio''''
                                ELSE
                                    CASE WHEN ( ( desoneracao.expiracao >= now() ) OR ( ( desoneracao.prorrogavel = true ) AND ( desonerado.data_prorrogacao >= now() ) ) ) THEN
                                        (
                                            SELECT
                                                funcao.nom_funcao
                                            FROM
                                                administracao.funcao
                                            WHERE
                                                funcao.cod_funcao = desoneracao.cod_funcao
                                                AND funcao.cod_modulo = desoneracao.cod_modulo
                                                AND funcao.cod_biblioteca = desoneracao.cod_biblioteca
                                        )
                                    ELSE
                                        ''''vazio''''
                                    END
                                END
                            ELSE
                                ''''vazio''''
                            END AS funcao_desoneracao,
                            desonerado.cod_desoneracao,
                            desonerado.ocorrencia AS desoneracao_ocorrido

                        FROM
                            arrecadacao.desoneracao

                        INNER JOIN
                            arrecadacao.desonerado_imovel
                        ON
                            desoneracao.cod_desoneracao = desonerado_imovel.cod_desoneracao

                        INNER JOIN
                            arrecadacao.desonerado
                        ON
                            desonerado.cod_desoneracao = desoneracao.cod_desoneracao
                            AND desonerado.numcgm = desonerado_imovel.numcgm
                            AND desonerado.ocorrencia = desonerado.ocorrencia

                    )AS desonerar
                ON
                    desonerar.cod_credito = calculo.cod_credito
                    AND desonerar.cod_especie = calculo.cod_especie
                    AND desonerar.cod_genero = calculo.cod_genero
                    AND desonerar.cod_natureza = calculo.cod_natureza
                    AND desonerar.inscricao_municipal = imovel_calculo.inscricao_municipal

                LEFT JOIN
                    arrecadacao.lancamento_calculo
                ON
                    lancamento_calculo.cod_calculo = calculo.cod_calculo

                WHERE
                    lancamento_calculo IS NULL
                    AND calculo_grupo_credito.cod_grupo = ''||inCodGrupo||''
                    AND calculo_grupo_credito.ano_exercicio = ''||inAnoExercicio||''
                    AND imovel_calculo.inscricao_municipal = ''||reRegistro.inscricao||''
            '';

        ELSE
           stSql2 := ''
                SELECT
                    calculo.cod_calculo,
                    calculo.valor,
                    calculo.timestamp AS timestamp_calculo,
                    cadastro_economico_calculo.inscricao_economica AS inscricao,
        
                    desonerar.funcao_desoneracao,
                    desonerar.cod_desoneracao,
                    (
                        SELECT
                            desonerado.numcgm
                        FROM
                            arrecadacao.desonerado
                        WHERE
                            desonerado.cod_desoneracao = desonerar.cod_desoneracao
                            AND desonerado.ocorrencia = desonerar.desoneracao_ocorrido
                        LIMIT 1
                    )AS numcgm_desonerado,
                    desonerar.desoneracao_ocorrido,
                    credito_grupo.desconto

                FROM
                    arrecadacao.calculo_grupo_credito
        
                INNER JOIN
                    arrecadacao.calculo
                ON
                    calculo.cod_calculo = calculo_grupo_credito.cod_calculo
                    AND calculo.ativo = true
        
                INNER JOIN
                    arrecadacao.cadastro_economico_calculo
                ON
                    cadastro_economico_calculo.cod_calculo = calculo.cod_calculo
   
                INNER JOIN
                    arrecadacao.credito_grupo
                ON
                    credito_grupo.cod_grupo = calculo_grupo_credito.cod_grupo
                    AND credito_grupo.ano_exercicio = calculo_grupo_credito.ano_exercicio
                    AND credito_grupo.cod_credito = calculo.cod_credito
                    AND credito_grupo.cod_especie = calculo.cod_especie
                    AND credito_grupo.cod_genero = calculo.cod_genero
                    AND credito_grupo.cod_natureza = calculo.cod_natureza

                LEFT JOIN
                    (
                        SELECT DISTINCT
                            desoneracao.cod_credito,
                            desoneracao.cod_especie,
                            desoneracao.cod_genero,
                            desoneracao.cod_natureza,
                            desonerado_cad_economico.inscricao_economica,

                            CASE WHEN ( desonerado_cad_economico.cod_desoneracao IS NOT NULL ) AND ( desoneracao.cod_desoneracao IS NOT NULL ) THEN
                                CASE WHEN ( ( desoneracao.revogavel = true ) AND ( desonerado.data_revogacao <= now() ) ) THEN
                                    ''''vazio''''
                                ELSE
                                    CASE WHEN ( ( desoneracao.expiracao >= now() ) OR ( ( desoneracao.prorrogavel = true ) AND ( desonerado.data_prorrogacao >= now() ) ) ) THEN
                                        (
                                            SELECT
                                                funcao.nom_funcao
                                            FROM
                                                administracao.funcao
                                            WHERE
                                                funcao.cod_funcao = desoneracao.cod_funcao
                                                AND funcao.cod_modulo = desoneracao.cod_modulo
                                                AND funcao.cod_biblioteca = desoneracao.cod_biblioteca
                                        )
                                    ELSE
                                        ''''vazio''''
                                    END
                                END
                            ELSE
                                ''''vazio''''
                            END AS funcao_desoneracao,
                            desonerado.cod_desoneracao,
                            desonerado.ocorrencia AS desoneracao_ocorrido

                        FROM
                            arrecadacao.desoneracao

                        INNER JOIN
                            arrecadacao.desonerado_cad_economico
                        ON
                            desoneracao.cod_desoneracao = desonerado_cad_economico.cod_desoneracao

                        INNER JOIN
                            arrecadacao.desonerado
                        ON
                            desonerado.cod_desoneracao = desoneracao.cod_desoneracao
                            AND desonerado.numcgm = desonerado_cad_economico.numcgm
                            AND desonerado.ocorrencia = desonerado.ocorrencia

                    )AS desonerar
                ON
                    desonerar.cod_credito = calculo.cod_credito
                    AND desonerar.cod_especie = calculo.cod_especie
                    AND desonerar.cod_genero = calculo.cod_genero
                    AND desonerar.cod_natureza = calculo.cod_natureza
                    AND desonerar.inscricao_economica = cadastro_economico_calculo.inscricao_economica
        
                LEFT JOIN
                    arrecadacao.lancamento_calculo
                ON
                    lancamento_calculo.cod_calculo = calculo.cod_calculo
        
        
        
                LEFT JOIN
                    arrecadacao.desonerado_cad_economico
                ON
                    desonerado_cad_economico.inscricao_economica = cadastro_economico_calculo.inscricao_economica
                    AND desonerar.cod_desoneracao = desonerado_cad_economico.cod_desoneracao
        
                WHERE
                    lancamento_calculo IS NULL
                    AND calculo_grupo_credito.cod_grupo = ''||inCodGrupo||''
                    AND calculo_grupo_credito.ano_exercicio = ''||inAnoExercicio||''
                    AND cadastro_economico_calculo.inscricao_economica = ''||reRegistro.inscricao||''
           '';
        END IF;


        flValorTotal := 0.00;
        FOR reRegistro2 IN EXECUTE stSql2 LOOP --laco para encontrar a soma do valor dos creditos do imovel
            flValorTotal := flValorTotal + reRegistro2.valor;
        END LOOP;

        --consulta para trazer o valor do abono por imovel
        SELECT
            sum(atributo_desoneracao_valor.valor::numeric) AS valor,
            atributo_desoneracao_valor.ocorrencia,
            atributo_desoneracao_valor.numcgm

        INTO
            flValorAbono,
            inOcorrenciaAbono,
            inNumCGMAbono

        FROM
            arrecadacao.desonerado

        INNER JOIN
            arrecadacao.desonerado_imovel
        ON
            desonerado_imovel.cod_desoneracao = desonerado.cod_desoneracao
            AND desonerado_imovel.numcgm = desonerado.numcgm
            AND desonerado_imovel.ocorrencia = desonerado.ocorrencia

        INNER JOIN
            arrecadacao.atributo_desoneracao_valor
        ON
            atributo_desoneracao_valor.cod_desoneracao = desonerado.cod_desoneracao
            AND atributo_desoneracao_valor.numcgm = desonerado.numcgm
            AND atributo_desoneracao_valor.ocorrencia = desonerado.ocorrencia

        LEFT JOIN
            arrecadacao.lancamento_usa_desoneracao
        ON
            lancamento_usa_desoneracao.cod_desoneracao = desonerado.cod_desoneracao
            AND lancamento_usa_desoneracao.numcgm = desonerado.numcgm
            AND lancamento_usa_desoneracao.ocorrencia = desonerado.ocorrencia

        WHERE
            desonerado.cod_desoneracao = 1
            AND lancamento_usa_desoneracao.cod_desoneracao IS NULL
            AND desonerado_imovel.inscricao_municipal = reRegistro.inscricao

        GROUP BY
            atributo_desoneracao_valor.ocorrencia,
            atributo_desoneracao_valor.numcgm;


        --aqui vamos criar o lancamento com o valor_lancamento zerado para poder inserir dados nas demais tabelas
        SELECT
            COALESCE(MAX(cod_lancamento)+1,1)

        INTO
            inCodLancamento

        FROM
            arrecadacao.lancamento;

        stRetorno := stRetorno||inCodLancamento||'','';

        INSERT INTO arrecadacao.lancamento ( cod_lancamento, vencimento, valor, total_parcelas, ativo, divida ) VALUES ( inCodLancamento, now(), 0.00, 0, true, false );

        --no final de tudo precisa atualizar o valor lancado
        inTotaldeCreditos := 0;
        flValorTotalGeral := 0.00;
        FOR reRegistro2 IN EXECUTE stSql2 LOOP

            --teste se existe desoneracao tipo ABONO e em caso afirmativo insere na tabela de desoneracao
            IF ( flValorAbono IS NOT NULL ) THEN
                flValorPercent := (reRegistro2.valor * 100) / flValorTotal;
                flValorPercent := (flValorAbono * flValorPercent) / 100;
            ELSE
                flValorPercent := reRegistro2.valor;
            END IF;

            --teste se existe desoneracao e em caso afirmativo insere na tabela de desoneracao
            IF ( reRegistro2.funcao_desoneracao != ''vazio'' ) THEN
                --stExecuta :=  ''SELECT ''||reRegistro2.funcao_desoneracao||''(''||reRegistro2.inscricao||'',''||flValorPercent||'',''||flValorTotal||'') AS valor '';
                stExecuta :=  ''SELECT ''||reRegistro2.funcao_desoneracao||''(''||reRegistro2.inscricao||'',''||flValorPercent||'') AS valor '';
                OPEN  varCursor FOR EXECUTE stExecuta;
                FETCH varCursor INTO flValorDesoneracao;
                CLOSE varCursor;
            ELSE
                flValorDesoneracao := flValorPercent;
            END IF;

            flValorPorCredito[inTotaldeCreditos] := flValorDesoneracao;
            boUtilizarDesconto[inTotaldeCreditos] := reRegistro2.desconto;
            inTotaldeCreditos := inTotaldeCreditos + 1;

            INSERT INTO arrecadacao.lancamento_calculo ( cod_calculo, cod_lancamento, dt_lancamento, valor ) VALUES ( reRegistro2.cod_calculo, inCodLancamento, now(), flValorDesoneracao );

            IF ( flValorAbono IS NOT NULL ) THEN
                INSERT INTO arrecadacao.lancamento_usa_desoneracao ( cod_lancamento, cod_calculo, cod_desoneracao, numcgm, ocorrencia ) VALUES ( inCodLancamento, reRegistro2.cod_calculo, 1, inNumCGMAbono, inOcorrenciaAbono );
            END IF;

            IF ( reRegistro2.funcao_desoneracao != ''vazio'' ) THEN
                INSERT INTO arrecadacao.lancamento_usa_desoneracao ( cod_lancamento, cod_calculo, cod_desoneracao, numcgm, ocorrencia ) VALUES ( inCodLancamento, reRegistro2.cod_calculo, reRegistro2.cod_desoneracao, reRegistro2.numcgm_desonerado, reRegistro2.desoneracao_ocorrido );
            END IF;

            inCodCalculo := reRegistro2.cod_calculo;
            flValorTotalGeral := flValorTotalGeral + flValorDesoneracao;

        END LOOP;

        IF ( flValorTotalGeral >= flValorMinimoLanc ) THEN
            --determinar numero de parcelas normais do lancamento
            SELECT
                (
                    SELECT
                        count(vencimento_parcela.cod_parcela)
                    FROM
                        arrecadacao.vencimento_parcela
                    WHERE
                        vencimento_parcela.cod_grupo = grupo_vencimento.cod_grupo
                        AND vencimento_parcela.ano_exercicio = grupo_vencimento.ano_exercicio
                        AND vencimento_parcela.cod_vencimento = grupo_vencimento.cod_vencimento
                )AS total_parcelas,

                grupo_vencimento.utilizar_unica,
                grupo_vencimento.data_vencimento_parcela_unica,
                grupo_vencimento.cod_vencimento

            INTO
                inTotalParcelasNorm,
                boUtilUnica,
                dtVencParcUnica,
                inCodVenc

            FROM
                arrecadacao.grupo_vencimento

            WHERE
                grupo_vencimento.cod_grupo = inCodGrupo
                AND grupo_vencimento.ano_exercicio = inAnoExercicio
                AND grupo_vencimento.limite_inicial <= flValorTotalGeral
                AND grupo_vencimento.limite_final >= flValorTotalGeral;


            IF ( flValorMinimoParc > 0 ) THEN
                inTotalParcNormUsar := (flValorTotalGeral / flValorMinimoParc)::integer; --calculando a quantidade de parcelas normais a serem utilizadas
                IF ( inTotalParcNormUsar > inTotalParcelasNorm ) THEN
                    inTotalParcNormUsar := inTotalParcelasNorm;
                END IF;
            ELSE
                inTotalParcNormUsar := inTotalParcelasNorm;
            END IF;

            flValorParcela := flValorTotalGeral / inTotalParcNormUsar; --calculando o valor das parcelas normais
            --sistema para arredondar o valor das parcelas de acordo com a regra de lancamento

            flValorParcelaUm := 0.00;
            inLaco := 0; --laco para somar valor das parcelas
            WHILE ( inLaco < inTotalParcNormUsar ) LOOP
                flValorParcelaUm := flValorParcelaUm::numeric(14,2) + flValorParcela::numeric(14,2);
                inLaco := inLaco + 1;
            END LOOP;


            IF ( flValorParcelaUm != flValorTotalGeral ) THEN
                flValorParcelaUm := flValorParcela - ( flValorParcelaUm - flValorTotalGeral );
            ELSE
                flValorParcelaUm := flValorParcela;
            END IF;



            --atualizar valor do lancamento = flValorTotalGeral
            UPDATE
                arrecadacao.lancamento

            SET
                vencimento = dtVencParcUnica,
                valor = flValorTotalGeral,
                total_parcelas = inTotalParcNormUsar

            WHERE
                cod_lancamento = inCodLancamento;


            --pegando o proximo codigo parcela disponivel para usar
            SELECT
                COALESCE(MAX(cod_parcela)+1,1)

            INTO
                inCodParcela

            FROM
                arrecadacao.parcela;


            IF ( boUtilUnica = true ) THEN --flag indica que devo utilizar parcela unica
                --consulta para retornar dados para parcela unica
                stSql3 := ''
                    SELECT
                        desconto.data_vencimento,
                        desconto.valor,
                        desconto.percentual

                    FROM
                        arrecadacao.desconto

                    WHERE
                        desconto.cod_grupo = ''||inCodGrupo||''
                        AND desconto.ano_exercicio = ''||inAnoExercicio||''
                        AND desconto.cod_vencimento = ''||inCodVenc||''
                '';

                inLaco := 0; --contando quantidade de parcelas unicas
                FOR reRegistro2 IN EXECUTE stSql3 LOOP
                    inLaco := inLaco + 1;

                    inLaco2 := 0;
                    flValorParcelaDesc := 0;
                    WHILE ( inLaco2 < inTotaldeCreditos ) LOOP
                        IF ( boUtilizarDesconto[inLaco2] = true ) THEN
                            IF ( reRegistro2.percentual = true ) THEN
                                flTMPval := ( flValorPorCredito[inLaco2] * reRegistro2.valor ) / 100;
                            ELSE
                                flTMPval := flValorPorCredito[inLaco2] - reRegistro2.valor;
                            END IF;

                            flValorParcelaDesc := flValorParcelaDesc + (flValorPorCredito[inLaco2] - flTMPval);
                        ELSE
                            flValorParcelaDesc := flValorParcelaDesc + flValorPorCredito[inLaco2];
                        END IF;

                        inLaco2 := inLaco2 + 1;
                    END LOOP;


                    INSERT INTO arrecadacao.parcela ( cod_parcela, cod_lancamento, nr_parcela, vencimento, valor ) VALUES ( inCodParcela, inCodLancamento, 0, dtVencParcUnica, flValorTotalGeral );
                    INSERT INTO arrecadacao.parcela_desconto ( cod_parcela, vencimento, valor ) VALUES ( inCodParcela, reRegistro2.data_vencimento, flValorParcelaDesc );

                    --recupera funcao para recuperar numeracao
                    SELECT
                        funcao.nom_funcao,
                        carteira.cod_carteira::varchar,
                        convenio.cod_convenio::varchar
    
                    INTO
                        stFuncaoNumeracao,
                        inCodCarteira,
                        inCodConvenio
    
                    FROM
                        arrecadacao.calculo
    
                    INNER JOIN
                        monetario.credito
                    ON
                        credito.cod_credito = calculo.cod_credito
                        AND credito.cod_especie = calculo.cod_especie
                        AND credito.cod_natureza = calculo.cod_natureza
                        AND credito.cod_genero = calculo.cod_genero
    
                    INNER JOIN
                        monetario.convenio
                    ON
                        convenio.cod_convenio = credito.cod_convenio
    
                    LEFT JOIN
                        monetario.carteira
                    ON
                        carteira.cod_convenio = convenio.cod_convenio
    
                    INNER JOIN
                        monetario.tipo_convenio
                    ON
                        tipo_convenio.cod_tipo = convenio.cod_tipo
    
                    INNER JOIN
                        administracao.funcao
                    ON
                        funcao.cod_funcao = tipo_convenio.cod_funcao
                        AND funcao.cod_biblioteca = tipo_convenio.cod_biblioteca
                        AND funcao.cod_modulo = tipo_convenio.cod_modulo
    
                    WHERE
                        calculo.cod_calculo = inCodCalculo;
    
    
                    --recupera numeracao do carne
                    IF ( inCodCarteira IS NOT NULL ) THEN
                        stExecuta :=  ''SELECT ''||stFuncaoNumeracao||''(''||inCodCarteira||'',''||inCodConvenio||'') AS valor '';
                    ELSE
                        stExecuta :=  ''SELECT ''||stFuncaoNumeracao||''(''''''''::varchar,''||inCodConvenio||''::varchar) AS valor '';
                    END IF;
    
                    OPEN  varCursor FOR EXECUTE stExecuta;
                    FETCH varCursor INTO inNumeroCarne;
                    CLOSE varCursor;
    
                    IF ( inNumeroCarne IS NOT NULL ) THEN
                        --dexei o exercicio fixo, depois pensar se vale apena colocar dinamico
                        INSERT INTO arrecadacao.carne ( numeracao, cod_convenio, cod_parcela, cod_carteira, exercicio, impresso ) VALUES ( inNumeroCarne, inCodConvenio::integer, inCodParcela, inCodCarteira::integer, extract(year from now()), false );
                    END IF;

                    inCodParcela := inCodParcela + 1;
                END LOOP;

                IF ( inLaco = 0 ) THEN
                    INSERT INTO arrecadacao.parcela ( cod_parcela, cod_lancamento, nr_parcela, vencimento, valor ) VALUES ( inCodParcela, inCodLancamento, 0, dtVencParcUnica, flValorTotalGeral );

                    --recupera funcao para recuperar numeracao
                    SELECT
                        funcao.nom_funcao,
                        carteira.cod_carteira::varchar,
                        convenio.cod_convenio::varchar
    
                    INTO
                        stFuncaoNumeracao,
                        inCodCarteira,
                        inCodConvenio
    
                    FROM
                        arrecadacao.calculo
    
                    INNER JOIN
                        monetario.credito
                    ON
                        credito.cod_credito = calculo.cod_credito
                        AND credito.cod_especie = calculo.cod_especie
                        AND credito.cod_natureza = calculo.cod_natureza
                        AND credito.cod_genero = calculo.cod_genero
    
                    INNER JOIN
                        monetario.convenio
                    ON
                        convenio.cod_convenio = credito.cod_convenio
    
                    LEFT JOIN
                        monetario.carteira
                    ON
                        carteira.cod_convenio = convenio.cod_convenio
    
                    INNER JOIN
                        monetario.tipo_convenio
                    ON
                        tipo_convenio.cod_tipo = convenio.cod_tipo
    
                    INNER JOIN
                        administracao.funcao
                    ON
                        funcao.cod_funcao = tipo_convenio.cod_funcao
                        AND funcao.cod_biblioteca = tipo_convenio.cod_biblioteca
                        AND funcao.cod_modulo = tipo_convenio.cod_modulo
    
                    WHERE
                        calculo.cod_calculo = inCodCalculo;
    
    
                    --recupera numeracao do carne
                    IF ( inCodCarteira IS NOT NULL ) THEN
                        stExecuta :=  ''SELECT ''||stFuncaoNumeracao||''(''||inCodCarteira||'',''||inCodConvenio||'') AS valor '';
                    ELSE
                        stExecuta :=  ''SELECT ''||stFuncaoNumeracao||''(''''''''::varchar,''||inCodConvenio||''::varchar) AS valor '';
                    END IF;
    
                    OPEN  varCursor FOR EXECUTE stExecuta;
                    FETCH varCursor INTO inNumeroCarne;
                    CLOSE varCursor;
    
                    IF ( inNumeroCarne IS NOT NULL ) THEN
                        --dexei o exercicio fixo, depois pensar se vale apena colocar dinamico
                        INSERT INTO arrecadacao.carne ( numeracao, cod_convenio, cod_parcela, cod_carteira, exercicio, impresso ) VALUES ( inNumeroCarne, inCodConvenio::integer, inCodParcela, inCodCarteira::integer, extract(year from now()), false );
                    END IF;

                END IF;
            END IF;

            --consulta para retornar dados para parcelas normais
            stSql3 := ''
                SELECT
                    vencimento_parcela.data_vencimento,
                    vencimento_parcela.valor AS valor_desconto,
                    vencimento_parcela.percentual,
                    vencimento_parcela.data_vencimento_desconto

                FROM
                    arrecadacao.vencimento_parcela

                WHERE
                    vencimento_parcela.cod_vencimento = ''||inCodVenc||''
                    AND vencimento_parcela.cod_grupo = ''||inCodGrupo||''
                    AND vencimento_parcela.ano_exercicio = ''||inAnoExercicio||''
                ORDER BY
                    vencimento_parcela.cod_parcela
            '';

            --inserindo parcelas normais
            inLaco := 1;
            FOR reRegistro2 IN EXECUTE stSql3 LOOP
                EXIT WHEN ( inLaco > inTotalParcNormUsar ); --fazendo somente o numero necessario de parcelas do lancamento

                IF ( inLaco = 1 ) THEN --a primeira parcela a ser inserida usa o valor dos restos de arredondamento
                    --inserindo parcela normal
                    INSERT INTO arrecadacao.parcela ( cod_parcela, cod_lancamento, nr_parcela, vencimento, valor ) VALUES ( inCodParcela, inCodLancamento, inLaco, reRegistro2.data_vencimento, flValorParcelaUm );

                    --verificando se existem descontos para a parcela normal
                    IF ( reRegistro2.valor_desconto > 0 ) THEN
                        inLaco2 := 0;
                        flValorParcelaDesc := 0;
                        WHILE ( inLaco2 < inTotaldeCreditos ) LOOP
                            flTMPval := flValorPorCredito[inLaco2] / inTotalParcNormUsar; --calculando o valor das parcelas normais
                            flTMPval2 := 0.00;
                            inLaco3 := 0; --laco para somar valor das parcelas
                            WHILE ( inLaco3 < inTotalParcNormUsar ) LOOP
                                flTMPval2 := flTMPval2::numeric(14,2) + flTMPval::numeric(14,2);
                                inLaco3 := inLaco3 + 1;
                            END LOOP;

                            IF ( flTMPval2 != flValorPorCredito[inLaco2] ) THEN
                                flTMPval2 := flTMPval - ( flTMPval2 - flValorPorCredito[inLaco2] );
                            ELSE
                                flTMPval2 := flTMPval;
                            END IF;

                            IF ( boUtilizarDesconto[inLaco2] = true ) THEN
                                IF ( reRegistro2.percentual = true ) THEN
                                    flTMPval := ( flTMPval2 * reRegistro2.valor_desconto ) / 100;
                                ELSE
                                    flTMPval := flTMPval2 - reRegistro2.valor_desconto;
                                END IF;

                                flValorParcelaDesc := flValorParcelaDesc + (flTMPval2 - flTMPval);
                            ELSE
                                flValorParcelaDesc := flValorParcelaDesc + flTMPval2;
                            END IF;

                            inLaco2 := inLaco2 + 1;
                        END LOOP;

                        INSERT INTO arrecadacao.parcela_desconto ( cod_parcela, vencimento, valor ) VALUES ( inCodParcela, reRegistro2.data_vencimento_desconto, flValorParcelaDesc );
                    END IF;
                ELSE -- as demais parcelas usam o valor normal
                    --inserindo parcela normal
                    INSERT INTO arrecadacao.parcela ( cod_parcela, cod_lancamento, nr_parcela, vencimento, valor ) VALUES ( inCodParcela, inCodLancamento, inLaco, reRegistro2.data_vencimento, flValorParcela );

                    --verificando se existem descontos para a parcela normal
                    IF ( reRegistro2.valor_desconto > 0 ) THEN
                        inLaco2 := 0;
                        flValorParcelaDesc := 0;
                        WHILE ( inLaco2 < inTotaldeCreditos ) LOOP
                            flTMPval2 := flValorPorCredito[inLaco2] / inTotalParcNormUsar; --calculando o valor das parcelas normais

                            IF ( boUtilizarDesconto[inLaco2] = true ) THEN
                                IF ( reRegistro2.percentual = true ) THEN
                                    flTMPval := ( flTMPval2 * reRegistro2.valor_desconto ) / 100;
                                ELSE
                                    flTMPval := flTMPval2 - reRegistro2.valor_desconto;
                                END IF;

                                flValorParcelaDesc := flValorParcelaDesc + (flTMPval2 - flTMPval);
                            ELSE
                                flValorParcelaDesc := flValorParcelaDesc + flTMPval2;
                            END IF;

                            inLaco2 := inLaco2 + 1;
                        END LOOP;

                        INSERT INTO arrecadacao.parcela_desconto ( cod_parcela, vencimento, valor ) VALUES ( inCodParcela, reRegistro2.data_vencimento_desconto, flValorParcelaDesc );
                    END IF;
                END IF;

                --recupera funcao para recuperar numeracao
                SELECT
                    funcao.nom_funcao,
                    carteira.cod_carteira::varchar,
                    convenio.cod_convenio::varchar

                INTO
                    stFuncaoNumeracao,
                    inCodCarteira,
                    inCodConvenio

                FROM
                    arrecadacao.calculo

                INNER JOIN
                    monetario.credito
                ON
                    credito.cod_credito = calculo.cod_credito
                    AND credito.cod_especie = calculo.cod_especie
                    AND credito.cod_natureza = calculo.cod_natureza
                    AND credito.cod_genero = calculo.cod_genero

                INNER JOIN
                    monetario.convenio
                ON
                    convenio.cod_convenio = credito.cod_convenio

                LEFT JOIN
                    monetario.carteira
                ON
                    carteira.cod_convenio = convenio.cod_convenio

                INNER JOIN
                    monetario.tipo_convenio
                ON
                    tipo_convenio.cod_tipo = convenio.cod_tipo

                INNER JOIN
                    administracao.funcao
                ON
                    funcao.cod_funcao = tipo_convenio.cod_funcao
                    AND funcao.cod_biblioteca = tipo_convenio.cod_biblioteca
                    AND funcao.cod_modulo = tipo_convenio.cod_modulo

                WHERE
                    calculo.cod_calculo = inCodCalculo;


                --recupera numeracao do carne
                IF ( inCodCarteira IS NOT NULL ) THEN
                    stExecuta :=  ''SELECT ''||stFuncaoNumeracao||''(''||inCodCarteira||'',''||inCodConvenio||'') AS valor '';
                ELSE
                    stExecuta :=  ''SELECT ''||stFuncaoNumeracao||''(''''''''::varchar,''||inCodConvenio||''::varchar) AS valor '';
                END IF;

                OPEN  varCursor FOR EXECUTE stExecuta;
                FETCH varCursor INTO inNumeroCarne;
                CLOSE varCursor;

                IF ( inNumeroCarne IS NOT NULL ) THEN
                    --dexei o exercicio fixo, depois pensar se vale apena colocar dinamico
                    INSERT INTO arrecadacao.carne ( numeracao, cod_convenio, cod_parcela, cod_carteira, exercicio, impresso ) VALUES ( inNumeroCarne, inCodConvenio::integer, inCodParcela, inCodCarteira::integer, extract(year from now()), false );
                END IF;

                inCodParcela := inCodParcela + 1;
                inLaco := inLaco + 1;
            END LOOP;
        ELSE
            --setar lancamento (ativo = false e o valor = flValorTotalGeral)
            UPDATE
                arrecadacao.lancamento

            SET
                valor = flValorTotalGeral,
                ativo = false

            WHERE
                cod_lancamento = inCodLancamento;
        END IF;


    END LOOP;

    return stRetorno;
END;
' LANGUAGE 'plpgsql';
