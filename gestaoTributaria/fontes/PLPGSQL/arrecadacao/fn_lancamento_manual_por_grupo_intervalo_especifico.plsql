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
* $Id: fn_lancamento_automatico.plsql 29203 2008-04-15 14:45:04Z fabio $
*
* Caso de uso: uc-05.03.05
*/

CREATE OR REPLACE FUNCTION arrecadacao.fn_lancamento_manual_por_grupo_intervalo_especifico( integer, integer, varchar, integer, varchar, varchar, varchar, varchar, varchar, integer ) RETURNS varchar AS '
DECLARE
    inCodGrupo          ALIAS FOR $1;
    inAnoExercicio      ALIAS FOR $2;
    stIntervalo         ALIAS FOR $3;
    inQTDparcelas       ALIAS FOR $4;
    stDTvencParc        ALIAS FOR $5;
    stTipoDescParc      ALIAS FOR $6;
    stDescParc          ALIAS FOR $7;
    stDTdescParc        ALIAS FOR $8;
    stNumeroParc        ALIAS FOR $9;
    inTipoInscricao     ALIAS FOR $10; --1 = insc imob 2 = insc eco 3 = cgm
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
    inCodDesoneracao    INTEGER;
    inOcorrenciaAbono   INTEGER; --numero de ocorrencia do abono
    inOcorrenciaAbonoMax INTEGER; --numero de ocorrencia max do abono
    inNumCGMAbono       INTEGER; --numero do cgm do abono
    inCodVenc           INTEGER; --codigo de vencimento para tabela vencimento parcelas
    inLaco              INTEGER; --usado apenas para contar dados em lacos (temporario)
    inLaco2             INTEGER; --usado apenas para lacos
    inLaco3             INTEGER; --usado apenas para lacos
    inLaco4             INTEGER; --usado apenas para lacos
    flValorParcela      NUMERIC; --valor a pagar na parcela normal
    flValorParcelaUm    NUMERIC; --valor a pagar na parcela normal 1
    flValorParcelaDesc  NUMERIC; --valor a pagar na parcela normal com desconto
    inNumeroCarne       VARCHAR; --numero do carne a utilizar
    inCodConvenio       VARCHAR; --necessario para recuperar numeracao do carne
    inCodCarteira       VARCHAR; --necessario para recuperar numeracao do carne
    stNomeFuncaoDeson   VARCHAR;
    stNomeRegraDeson    VARCHAR;
    flValorPorCredito   NUMERIC[];
    inTotaldeCreditos   INTEGER;
    boUtilizarDesconto  BOOLEAN[];
    inCodDeson          INTEGER;
    inOcorrenciaDeson   INTEGER;
    inNumCGMDeson       INTEGER;
    stBuffer            varchar;

BEGIN
    stRetorno := '''';

    IF ( inQTDparcelas = -1 ) THEN
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
    ELSE
        flValorMinimo := 0.00;
        flValorMinimoLanc := 0.01;
        flValorMinimoParc := 0.01;
    END IF;

    IF ( inTipoInscricao = 1 ) THEN
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

                    INNER JOIN arrecadacao.calculo
                            ON AIC.cod_calculo  = calculo.cod_calculo
                               AND calculo.ativo = true

                    INNER JOIN arrecadacao.calculo_grupo_credito                                            AS ACGC
                            ON ACGC.cod_calculo                     = AIC.cod_calculo
                           AND ACGC.cod_grupo                       = '' || inCodGrupo     || ''
                           AND ACGC.ano_exercicio                   = '' || inAnoExercicio || ''
                     LEFT JOIN arrecadacao.lancamento_calculo                                               AS ALC
                            ON ALC.cod_calculo                      = AIC.cod_calculo
                         WHERE ( ( bi.inscricao_municipal IS NULL ) OR ( (bi.inscricao_municipal IS NOT NULL) AND (bi.dt_termino IS NOT NULL) ) )
                           AND imovel.inscricao_municipal IN (  ''||stIntervalo||'' )
                           AND ALC.cod_calculo IS NULL
                           AND COALESCE( 
                                (
                                    SELECT
                                        count(lancamento_calculo.cod_lancamento)
                    
                                    FROM
                                        arrecadacao.imovel_calculo
                    
                                    INNER JOIN
                                        arrecadacao.lancamento_calculo
                                    ON
                                        lancamento_calculo.cod_calculo = imovel_calculo.cod_calculo
                    
                                    INNER JOIN 
                                        arrecadacao.calculo_grupo_credito
                                    ON 
                                        calculo_grupo_credito.cod_calculo = lancamento_calculo.cod_calculo
                                        AND calculo_grupo_credito.cod_grupo = ACGC.cod_grupo
                                        AND calculo_grupo_credito.ano_exercicio = ACGC.ano_exercicio
                    
                                    INNER JOIN
                                        arrecadacao.parcela
                                    ON
                                        parcela.cod_lancamento = lancamento_calculo.cod_lancamento
                    
                                    INNER JOIN
                                        arrecadacao.carne
                                    ON
                                        carne.cod_parcela = parcela.cod_parcela
                    
                                    LEFT JOIN
                                        arrecadacao.pagamento
                                    ON
                                        pagamento.numeracao = carne.numeracao
                    
                                    LEFT JOIN
                                        arrecadacao.carne_devolucao
                                    ON
                                        carne_devolucao.numeracao = carne.numeracao
                    
                                    WHERE
                                        carne_devolucao.numeracao IS NULL
                                        AND pagamento.numeracao IS NULL
                                        AND imovel_calculo.inscricao_municipal = AIC.inscricao_municipal 
                                ), 0 
                            ) = 0
            '';
    ELSIF ( inTipoInscricao = 2 ) THEN
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

                    INNER JOIN arrecadacao.calculo
                            ON ACEC.cod_calculo  = calculo.cod_calculo
                               AND calculo.ativo = true

                    INNER JOIN arrecadacao.calculo_grupo_credito                                            AS ACGC
                            ON ACGC.cod_calculo                     = ACEC.cod_calculo
                           AND ACGC.cod_grupo                       = '' || inCodGrupo     || ''
                           AND ACGC.ano_exercicio                   = '' || inAnoExercicio || ''
                     LEFT JOIN arrecadacao.lancamento_calculo                                               AS ALC
                            ON ALC.cod_calculo                      = ACEC.cod_calculo

                         WHERE ( ( bc.inscricao_economica IS NULL ) OR ( (bc.inscricao_economica IS NOT NULL) AND (bc.dt_termino IS NOT NULL) ) )
                            AND cadastro_economico.inscricao_economica IN ( ''||stIntervalo||'' )
                --LIMIT 1 --para testes remover depois
            '';
/*
AND ALC.cod_calculo IS NULL
AND COALESCE( 
    (
        SELECT
            count(lancamento_calculo.cod_lancamento)

        FROM
            arrecadacao.cadastro_economico_calculo

        INNER JOIN
            arrecadacao.lancamento_calculo
        ON
            lancamento_calculo.cod_calculo = cadastro_economico_calculo.cod_calculo

        INNER JOIN 
            arrecadacao.calculo_grupo_credito
        ON 
            calculo_grupo_credito.cod_calculo = lancamento_calculo.cod_calculo
            AND calculo_grupo_credito.cod_grupo = ACGC.cod_grupo
            AND calculo_grupo_credito.ano_exercicio = ACGC.ano_exercicio

        INNER JOIN
            arrecadacao.parcela
        ON
            parcela.cod_lancamento = lancamento_calculo.cod_lancamento

        INNER JOIN
            arrecadacao.carne
        ON
            carne.cod_parcela = parcela.cod_parcela

        LEFT JOIN
            arrecadacao.pagamento
        ON
            pagamento.numeracao = carne.numeracao

        LEFT JOIN
            arrecadacao.carne_devolucao
        ON
            carne_devolucao.numeracao = carne.numeracao

        WHERE
            carne_devolucao.numeracao IS NULL
            AND pagamento.numeracao IS NULL
            AND cadastro_economico_calculo.inscricao_economica = ACEC.inscricao_economica
    ), 0 
) = 0
*/

    ELSE
        stSql := ''
                SELECT DISTINCT 
                    acc.numcgm AS inscricao

                FROM 
                    arrecadacao.calculo_cgm AS acc

                INNER JOIN 
                    arrecadacao.calculo_grupo_credito AS ACGC
                ON 
                    ACGC.cod_calculo = acc.cod_calculo
                    AND ACGC.cod_grupo = '' || inCodGrupo || ''
                    AND ACGC.ano_exercicio = '' || inAnoExercicio || ''

                INNER JOIN arrecadacao.calculo
                ON ACGC.cod_calculo  = calculo.cod_calculo
                   AND calculo.ativo = true

                LEFT JOIN 
                    arrecadacao.lancamento_calculo AS ALC
                ON 
                    ALC.cod_calculo = AIC.cod_calculo

                WHERE 
                    ALC.cod_calculo IS NULL
                    AND acc.numcgm IN (  ''||stIntervalo||'' )
                    AND COALESCE( 
                        (
                            SELECT
                                count(lancamento_calculo.cod_lancamento)
            
                            FROM
                                arrecadacao.calculo_cgm
            
                            INNER JOIN
                                arrecadacao.lancamento_calculo
                            ON
                                lancamento_calculo.cod_calculo = calculo_cgm.cod_calculo
            
                            INNER JOIN 
                                arrecadacao.calculo_grupo_credito
                            ON 
                                calculo_grupo_credito.cod_calculo = lancamento_calculo.cod_calculo
                                AND calculo_grupo_credito.cod_grupo = ACGC.cod_grupo
                                AND calculo_grupo_credito.ano_exercicio = ACGC.ano_exercicio
            
                            INNER JOIN
                                arrecadacao.parcela
                            ON
                                parcela.cod_lancamento = lancamento_calculo.cod_lancamento
            
                            INNER JOIN
                                arrecadacao.carne
                            ON
                                carne.cod_parcela = parcela.cod_parcela
            
                            LEFT JOIN
                                arrecadacao.pagamento
                            ON
                                pagamento.numeracao = carne.numeracao
            
                            LEFT JOIN
                                arrecadacao.carne_devolucao
                            ON
                                carne_devolucao.numeracao = carne.numeracao
            
                            WHERE
                                carne_devolucao.numeracao IS NULL
                                AND pagamento.numeracao IS NULL
                                AND calculo_cgm.numcgm = acc.numcgm
                        ), 0 
                    ) = 0
        '';

    END IF;

    FOR reRegistro IN EXECUTE stSql LOOP
        if ( inTipoInscricao = 1 ) THEN
            stSql2 := ''
                SELECT
                    calculo.cod_calculo,
                    calculo.cod_credito,
                    calculo.cod_especie,
                    calculo.cod_genero,
                    calculo.cod_natureza,
                    calculo.valor,
                    calculo.timestamp AS timestamp_calculo,
                    imovel_calculo.inscricao_municipal AS inscricao,
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
                    arrecadacao.lancamento_calculo
                ON
                    lancamento_calculo.cod_calculo = calculo.cod_calculo

                WHERE
                    lancamento_calculo IS NULL
                    AND calculo_grupo_credito.cod_grupo = ''||inCodGrupo||''
                    AND calculo_grupo_credito.ano_exercicio = ''||inAnoExercicio||''
                    AND imovel_calculo.inscricao_municipal = ''||reRegistro.inscricao||''
            '';

        ELSIF ( inTipoInscricao = 2 ) THEN
           stSql2 := ''
                SELECT
                    calculo.cod_calculo,
                    calculo.cod_credito,
                    calculo.cod_especie,
                    calculo.cod_genero,
                    calculo.cod_natureza,
                    calculo.valor,
                    calculo.timestamp AS timestamp_calculo,
                    cadastro_economico_calculo.inscricao_economica AS inscricao,
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
                    arrecadacao.lancamento_calculo
                ON
                    lancamento_calculo.cod_calculo = calculo.cod_calculo

                WHERE
                    lancamento_calculo IS NULL
                    AND calculo_grupo_credito.cod_grupo = ''||inCodGrupo||''
                    AND calculo_grupo_credito.ano_exercicio = ''||inAnoExercicio||''
                    AND cadastro_economico_calculo.inscricao_economica = ''||reRegistro.inscricao||''
           '';
        ELSE
            stSql2 := ''
                SELECT
                    calculo.cod_calculo,
                    calculo.cod_credito,
                    calculo.cod_especie,
                    calculo.cod_genero,
                    calculo.cod_natureza,
                    calculo.valor,
                    calculo.timestamp AS timestamp_calculo,
                    calculo_cgm.numcgm AS inscricao,
                    credito_grupo.desconto

                FROM
                    arrecadacao.calculo_grupo_credito
        
                INNER JOIN
                    arrecadacao.calculo
                ON
                    calculo.cod_calculo = calculo_grupo_credito.cod_calculo
                    AND calculo.ativo = true
        
                INNER JOIN
                    arrecadacao.calculo_cgm
                ON
                    calculo_cgm.cod_calculo = calculo.cod_calculo
   
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
                    arrecadacao.lancamento_calculo
                ON
                    lancamento_calculo.cod_calculo = calculo.cod_calculo

                WHERE
                    lancamento_calculo IS NULL
                    AND calculo_grupo_credito.cod_grupo = ''||inCodGrupo||''
                    AND calculo_grupo_credito.ano_exercicio = ''||inAnoExercicio||''
                    AND calculo_cgm.numcgm = ''||reRegistro.inscricao||''
           '';
        END IF;

        flValorTotal := 0.00;
        FOR reRegistro2 IN EXECUTE stSql2 LOOP --laco para encontrar a soma do valor dos creditos do imovel
            flValorTotal := flValorTotal + reRegistro2.valor;
        END LOOP;

        --consulta para trazer o valor do abono por imovel

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


        SELECT
            funcao.nom_funcao
        INTO
            stNomeRegraDeson

        FROM
            arrecadacao.regra_desoneracao_grupo

        INNER JOIN
            administracao.funcao
        ON
            funcao.cod_funcao = regra_desoneracao_grupo.cod_funcao
            AND funcao.cod_modulo = regra_desoneracao_grupo.cod_modulo
            AND funcao.cod_biblioteca = regra_desoneracao_grupo.cod_biblioteca

        WHERE
            regra_desoneracao_grupo.cod_grupo = inCodGrupo
            AND regra_desoneracao_grupo.ano_exercicio = inAnoExercicio;
    
        FOR reRegistro2 IN EXECUTE stSql2 LOOP
            IF ( stNomeRegraDeson IS NOT NULL ) THEN
                SELECT
                    desoneracao.cod_desoneracao
                INTO
                    inCodDesoneracao

                FROM
                    arrecadacao.desoneracao
                WHERE
                    desoneracao.cod_credito = reRegistro2.cod_credito
                    AND desoneracao.cod_especie = reRegistro2.cod_especie
                    AND desoneracao.cod_natureza = reRegistro2.cod_natureza
                    AND desoneracao.cod_genero = reRegistro2.cod_genero
                    AND desoneracao.inicio <= now()::date
                    AND desoneracao.termino >= now()::date;

                IF ( inTipoInscricao = 1 ) THEN
                    SELECT
                        fn_conceder_desoneracao_grupo_por_inscricao ( inCodDesoneracao, stNomeRegraDeson, ''II'', reRegistro2.inscricao )
                    INTO
                        stBuffer;
                ELSIF ( inTipoInscricao = 2 ) THEN
                    SELECT
                        fn_conceder_desoneracao_grupo_por_inscricao ( inCodDesoneracao, stNomeRegraDeson, ''IE'', reRegistro2.inscricao )
                    INTO
                        stBuffer;
                ELSE
                    SELECT
                        fn_conceder_desoneracao_grupo_por_inscricao ( inCodDesoneracao, stNomeRegraDeson, ''IC'', reRegistro2.inscricao )
                    INTO
                        stBuffer;
                END IF;
            END IF;

            IF ( inTipoInscricao = 1 ) THEN --abono eh soh pra imovel
                SELECT
                    sum(atributo_desoneracao_valor.valor::numeric) AS valor,
                    atributo_desoneracao_valor.ocorrencia,
                    atributo_desoneracao_valor.numcgm,
                    desonerado.cod_desoneracao
    
                INTO
                    flValorAbono,
                    inOcorrenciaAbono,
                    inNumCGMAbono,
                    inCodDesoneracao
    
                FROM
                    arrecadacao.desonerado
    
                INNER JOIN
                    arrecadacao.lancamento_concede_desoneracao --ligar esta tabela pra pegar credito e bater pra ver se deve aplicar ou nao o abono
                ON
                    lancamento_concede_desoneracao.cod_desoneracao = desonerado.cod_desoneracao
                    AND lancamento_concede_desoneracao.numcgm = desonerado.numcgm
                    AND lancamento_concede_desoneracao.ocorrencia = desonerado.ocorrencia
        
                INNER JOIN
                    arrecadacao.calculo
                ON
                    calculo.cod_calculo = lancamento_concede_desoneracao.cod_calculo
        
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
                    AND calculo.cod_credito = reRegistro2.cod_credito
                    AND calculo.cod_especie = reRegistro2.cod_especie
                    AND calculo.cod_genero = reRegistro2.cod_genero
                    AND calculo.cod_natureza = reRegistro2.cod_natureza
    
                GROUP BY
                    atributo_desoneracao_valor.ocorrencia,
                    atributo_desoneracao_valor.numcgm,
                    desonerado.cod_desoneracao;
            END IF;

            --teste se existe desoneracao tipo ABONO e em caso afirmativo insere na tabela de desoneracao
            IF ( flValorAbono IS NOT NULL ) THEN
                IF reRegistro2.valor - flValorAbono >= 0 THEN
                    flValorPercent := reRegistro2.valor - flValorAbono;
                ELSE
                    flValorPercent := 0.00;

                    SELECT
                        COALESCE(MAX( desoneracao.ocorrencia ) + 1,1)

                    INTO
                        inOcorrenciaAbonoMax

                    FROM
                        arrecadacao.desoneracao

                    WHERE
                        desoneracao.numcgm = inNumCGMAbono
                        AND desoneracao.cod_desoneracao = inCodDesoneracao;

                    INSERT INTO arrecadacao.desonerado ( cod_desoneracao, ocorrencia, numcgm, data_concessao ) VALUES ( inCodDesoneracao, inOcorrenciaAbonoMax, inNumCGMAbono, now()::date );

                    IF ( inTipoInscricao = 1 ) THEN
                        INSERT INTO arrecadacao.desonerado_imovel ( cod_desoneracao, ocorrencia, numcgm, inscricao_municipal ) VALUES ( inCodDesoneracao, inOcorrenciaAbonoMax, inNumCGMAbono, reRegistro2.inscricao );
                    ELSIF ( inTipoInscricao = 2 ) THEN
                        INSERT INTO arrecadacao.desonerado_cad_economico ( cod_desoneracao, ocorrencia, numcgm, inscricao_economica ) VALUES ( inCodDesoneracao, inOcorrenciaAbonoMax, inNumCGMAbono, reRegistro2.inscricao );
                    END IF;

                    INSERT INTO arrecadacao.atributo_desoneracao_valor ( cod_desoneracao, numcgm, cod_modulo, cod_atributo, cod_cadastro, valor, ocorrencia ) VALUES ( inCodDesoneracao, inNumCGMAbono, 25, 1, 3, flValorAbono - reRegistro2.valor, inOcorrenciaAbonoMax );
                    INSERT INTO arrecadacao.lancamento_concede_desoneracao ( cod_lancamento, cod_calculo, cod_desoneracao, numcgm, ocorrencia ) VALUES ( inCodLancamento, reRegistro2.cod_calculo, inCodDesoneracao, inNumCGMAbono, inOcorrenciaAbonoMax );
                END IF;
            ELSE
                flValorPercent := reRegistro2.valor;
            END IF;


            IF ( inTipoInscricao = 1 AND stBuffer != ''0'' ) THEN
                SELECT DISTINCT
                    desonerado.numcgm,
                    CASE WHEN ( desonerado_imovel.cod_desoneracao IS NOT NULL ) AND ( desoneracao.cod_desoneracao IS NOT NULL ) THEN
                        CASE WHEN ( ( desoneracao.revogavel = true ) AND ( desonerado.data_revogacao <= now() ) ) THEN
                            ''vazio''
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
                                ''vazio''
                            END
                        END
                    ELSE
                        ''vazio''
                    END AS funcao_desoneracao,
                    desonerado.cod_desoneracao,
                    desonerado.ocorrencia
                INTO
                    inNumCGMDeson,
                    stNomeFuncaoDeson,
                    inCodDeson,
                    inOcorrenciaDeson

                FROM
                    arrecadacao.desoneracao

                INNER JOIN
                    arrecadacao.desonerado_imovel
                ON
                    desoneracao.cod_desoneracao = desonerado_imovel.cod_desoneracao

                INNER JOIN
                    (
                        SELECT
                            tmp.*
                        FROM
                            arrecadacao.desonerado AS tmp
                
                        INNER JOIN
                            (
                            SELECT 
                                max(desonerado.ocorrencia) AS ocorrencia,
                                desonerado.numcgm,
                                desonerado.cod_desoneracao
                            FROM
                                arrecadacao.desonerado
                    
                            GROUP BY
                                desonerado.numcgm,
                                desonerado.cod_desoneracao
                            )AS tmp2
                        ON
                            tmp2.numcgm = tmp.numcgm
                            AND tmp2.cod_desoneracao = tmp.cod_desoneracao
                            AND tmp2.ocorrencia = tmp.ocorrencia
                    )AS desonerado
                ON
                    desonerado.cod_desoneracao = desoneracao.cod_desoneracao
                    AND desonerado.numcgm = desonerado_imovel.numcgm
                    AND desonerado.ocorrencia = desonerado.ocorrencia

                WHERE
                    desoneracao.cod_credito = reRegistro2.cod_credito
                    AND desoneracao.cod_especie = reRegistro2.cod_especie
                    AND desoneracao.cod_genero = reRegistro2.cod_genero
                    AND desoneracao.cod_natureza = reRegistro2.cod_natureza
                    AND desonerado_imovel.inscricao_municipal = reRegistro2.inscricao
                    AND desonerado.data_concessao = now()::date;
            ELSIF ( inTipoInscricao = 2 AND stBuffer != ''0'' ) THEN
                SELECT DISTINCT
                    desonerado.numcgm,
                    CASE WHEN ( desonerado_cad_economico.cod_desoneracao IS NOT NULL ) AND ( desoneracao.cod_desoneracao IS NOT NULL ) THEN
                        CASE WHEN ( ( desoneracao.revogavel = true ) AND ( desonerado.data_revogacao <= now() ) ) THEN
                            ''vazio''
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
                                ''vazio''
                            END
                        END
                    ELSE
                        ''vazio''
                    END AS funcao_desoneracao,
                    desonerado.cod_desoneracao,
                    desonerado.ocorrencia
                INTO
                    inNumCGMDeson,
                    stNomeFuncaoDeson,
                    inCodDeson,
                    inOcorrenciaDeson

                FROM
                    arrecadacao.desoneracao

                INNER JOIN
                    arrecadacao.desonerado_cad_economico
                ON
                    desoneracao.cod_desoneracao = desonerado_cad_economico.cod_desoneracao

                INNER JOIN
                    (
                        SELECT
                            tmp.*
                        FROM
                            arrecadacao.desonerado AS tmp
                
                        INNER JOIN
                            (
                            SELECT 
                                max(desonerado.ocorrencia) AS ocorrencia,
                                desonerado.numcgm,
                                desonerado.cod_desoneracao
                            FROM
                                arrecadacao.desonerado
                    
                            GROUP BY
                                desonerado.numcgm,
                                desonerado.cod_desoneracao
                            )AS tmp2
                        ON
                            tmp2.numcgm = tmp.numcgm
                            AND tmp2.cod_desoneracao = tmp.cod_desoneracao
                            AND tmp2.ocorrencia = tmp.ocorrencia
                    )AS desonerado
                ON
                    desonerado.cod_desoneracao = desoneracao.cod_desoneracao
                    AND desonerado.numcgm = desonerado_cad_economico.numcgm
                    AND desonerado.ocorrencia = desonerado.ocorrencia

                WHERE
                    desoneracao.cod_credito = reRegistro2.cod_credito
                    AND desoneracao.cod_especie = reRegistro2.cod_especie
                    AND desoneracao.cod_genero = reRegistro2.cod_genero
                    AND desoneracao.cod_natureza = reRegistro2.cod_natureza
                    AND desonerado_cad_economico.inscricao_economica = reRegistro2.inscricao
                    AND desonerado.data_concessao = now()::date;
            ELSIF ( inTipoInscricao = 3 AND stBuffer != ''0'' ) THEN
                SELECT DISTINCT
                    desonerado.numcgm,
                    CASE WHEN ( desoneracao.cod_desoneracao IS NOT NULL ) THEN
                        CASE WHEN ( ( desoneracao.revogavel = true ) AND ( desonerado.data_revogacao <= now() ) ) THEN
                            ''vazio''
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
                                ''vazio''
                            END
                        END
                    ELSE
                        ''vazio''
                    END AS funcao_desoneracao,
                    desonerado.cod_desoneracao,
                    desonerado.ocorrencia
                INTO
                    inNumCGMDeson,
                    stNomeFuncaoDeson,
                    inCodDeson,
                    inOcorrenciaDeson

                FROM
                    arrecadacao.desoneracao

                INNER JOIN
                    (
                        SELECT
                            tmp.*
                        FROM
                            arrecadacao.desonerado AS tmp
                
                        INNER JOIN
                            (
                            SELECT 
                                max(desonerado.ocorrencia) AS ocorrencia,
                                desonerado.numcgm,
                                desonerado.cod_desoneracao
                            FROM
                                arrecadacao.desonerado
                    
                            GROUP BY
                                desonerado.numcgm,
                                desonerado.cod_desoneracao
                            )AS tmp2
                        ON
                            tmp2.numcgm = tmp.numcgm
                            AND tmp2.cod_desoneracao = tmp.cod_desoneracao
                            AND tmp2.ocorrencia = tmp.ocorrencia
                    )AS desonerado
                ON
                    desonerado.cod_desoneracao = desoneracao.cod_desoneracao
                    AND desonerado.ocorrencia = desonerado.ocorrencia

                WHERE
                    desoneracao.cod_credito = reRegistro2.cod_credito
                    AND desoneracao.cod_especie = reRegistro2.cod_especie
                    AND desoneracao.cod_genero = reRegistro2.cod_genero
                    AND desoneracao.cod_natureza = reRegistro2.cod_natureza
                    AND desonerado.numcgm = reRegistro2.inscricao
                    AND desonerado.data_concessao = now()::date;
            END IF;

            --teste se existe desoneracao e em caso afirmativo insere na tabela de desoneracao
            IF ( stNomeFuncaoDeson != ''vazio'' ) THEN
                stExecuta :=  ''SELECT ''||stNomeFuncaoDeson||''(''||reRegistro2.inscricao||'',''||flValorPercent||'') AS valor '';
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

            IF ( stNomeFuncaoDeson != ''vazio'' ) THEN
                INSERT INTO arrecadacao.lancamento_usa_desoneracao ( cod_lancamento, cod_calculo, cod_desoneracao, numcgm, ocorrencia ) VALUES ( inCodLancamento, reRegistro2.cod_calculo, inCodDeson, inNumCGMDeson, inOcorrenciaDeson );
            END IF;

            inCodCalculo := reRegistro2.cod_calculo;
            flValorTotalGeral := flValorTotalGeral + flValorDesoneracao;

        END LOOP;

        IF ( flValorTotalGeral >= flValorMinimoLanc ) THEN
            --determinar numero de parcelas normais do lancamento

            IF ( inQTDparcelas = -1 ) THEN -- usando calendario
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
            ELSE
                inTotalParcelasNorm := 0;
                inLaco := 1;
                boUtilUnica := false;
                dtVencParcUnica := split_part ( stDTvencParc, '';'', 1 ); --pega o vencimento da primeira
                WHILE ( inLaco <= inQTDparcelas ) LOOP
                    IF ( split_part ( stNumeroParc, '';'', inLaco ) != 0 ) THEN
                        inTotalParcelasNorm := inTotalParcelasNorm + 1;
                    ELSE
                        boUtilUnica := true;
                    END IF;

                    inLaco := inLaco + 1;
                END LOOP;
            END IF;

            IF ( flValorMinimoParc > 0 ) THEN
                inTotalParcNormUsar := trunc(flValorTotalGeral / flValorMinimoParc)::integer; --calculando a quantidade de parcelas normais a serem utilizadas
                IF ( inTotalParcNormUsar > inTotalParcelasNorm ) THEN
                    inTotalParcNormUsar := inTotalParcelasNorm;
                END IF;
            ELSE
                inTotalParcNormUsar := inTotalParcelasNorm;
            END IF;

            IF ( inTotalParcNormUsar > 0 ) THEN
                flValorParcela := flValorTotalGeral / inTotalParcNormUsar; --calculando o valor das parcelas normais
            END IF;

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
                IF ( inQTDparcelas = -1 ) THEN
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

                        INSERT INTO arrecadacao.parcela ( cod_parcela, cod_lancamento, nr_parcela, vencimento, valor ) VALUES ( inCodParcela, inCodLancamento, 0, reRegistro2.data_vencimento, flValorTotalGeral );
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
                            stExecuta :=  ''SELECT ''||stFuncaoNumeracao||''(''||inCodCarteira||''::varchar,''||inCodConvenio||''::varchar) AS valor '';
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
                    END LOOP; --FOR reRegistro2 IN EXECUTE stSql3 LOOP

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
                            stExecuta :=  ''SELECT ''||stFuncaoNumeracao||''(''||inCodCarteira||''::varchar,''||inCodConvenio||''::varchar) AS valor '';
                        ELSE
                            stExecuta :=  ''SELECT ''||stFuncaoNumeracao||''(''''''''::varchar,''||inCodConvenio||''::varchar) AS valor '';
                        END IF;
        
                        OPEN  varCursor FOR EXECUTE stExecuta;
                        FETCH varCursor INTO inNumeroCarne;
                        CLOSE varCursor;
        
                        IF ( inNumeroCarne IS NOT NULL ) THEN
                            --dexei o exercicio fixo, depois pensar se vale apena colocar dinamico
                            INSERT INTO arrecadacao.carne ( numeracao, cod_convenio, cod_parcela, cod_carteira, exercicio, impresso ) VALUES ( inNumeroCarne, inCodConvenio::integer, inCodParcela, inCodCarteira::integer, extract(year from now()), false );
                        END IF; --IF ( inNumeroCarne IS NOT NULL )
                    END IF; --IF ( inLaco = 0 ) THEN
                ELSE -- IF ( inQTDparcelas = -1 ) THEN
                    inLaco := 1;
                    WHILE ( inLaco <= inQTDparcelas ) LOOP
                        IF ( split_part ( stNumeroParc, '';'', inLaco ) = 0 ) THEN --soh entra para parcelas unicas
                            INSERT INTO arrecadacao.parcela ( cod_parcela, cod_lancamento, nr_parcela, vencimento, valor ) 
                                VALUES ( inCodParcela, inCodLancamento, 0, split_part( stDTvencParc, '';'', inLaco )::date, flValorTotalGeral );

                            flTMPval := split_part( stDescParc, '';'', inLaco );
                            IF ( flTMPval > 0.00 ) THEN --verificando se tem valor de desconto pra aplicar desconto
                                inLaco2 := 0;
                                flValorParcelaDesc := 0;
                                WHILE ( inLaco2 < inTotaldeCreditos ) LOOP
                                    IF ( boUtilizarDesconto[inLaco2] = true ) THEN
                                        IF ( split_part( stTipoDescParc, '';'', inLaco ) = ''true'' ) THEN --verificando se eh desconto percentual (true = percentual, false = valor)
                                            flTMPval2 := ( flValorPorCredito[inLaco2] * flTMPval ) / 100;
                                        ELSE
                                            flTMPval2 := flValorPorCredito[inLaco2] - flTMPval;
                                        END IF;

                                        flValorParcelaDesc := flValorParcelaDesc + (flValorPorCredito[inLaco2] - flTMPval2);
                                    ELSE
                                        flValorParcelaDesc := flValorParcelaDesc + flValorPorCredito[inLaco2];
                                    END IF;

                                    inLaco2 := inLaco2 + 1;
                                END LOOP;

                                INSERT INTO arrecadacao.parcela_desconto ( cod_parcela, vencimento, valor ) 
                                    VALUES ( inCodParcela, split_part( stDTdescParc, '';'', inLaco )::date, flValorParcelaDesc );
                            END IF; --IF ( flTMPval > 0.00 ) THEN

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
                                stExecuta :=  ''SELECT ''||stFuncaoNumeracao||''(''||inCodCarteira||''::varchar,''||inCodConvenio||''::varchar) AS valor '';
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

                            inCodParcela := inCodParcela + 1; --incrementando pq podem existir mais de uma unica
                        END IF; --IF ( split_part ( stNumeroParc, '';'', inLaco ) = 0 ) THEN

                        inLaco := inLaco + 1;
                    END LOOP;

                END IF; -- IF ( inQTDparcelas = -1 ) THEN
            END IF; --IF ( boUtilUnica = true ) THEN



            --consulta para retornar dados para parcelas normais
            IF ( inQTDparcelas = -1 ) THEN
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
                        END IF; --IF ( reRegistro2.valor_desconto > 0 )
                    ELSE --IF ( inLaco = 1 ) THEN -- as demais parcelas usam o valor normal
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
                        END IF; --IF ( reRegistro2.valor_desconto > 0 ) THEN
                    END IF; --IF ( inLaco = 1 ) THEN
    
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
                        stExecuta :=  ''SELECT ''||stFuncaoNumeracao||''(''||inCodCarteira||''::varchar,''||inCodConvenio||''::varchar) AS valor '';
                    ELSE
                        stExecuta :=  ''SELECT ''||stFuncaoNumeracao||''(''''''''::varchar,''||inCodConvenio||''::varchar) AS valor '';
                    END IF;

                    OPEN  varCursor FOR EXECUTE stExecuta;
                    FETCH varCursor INTO inNumeroCarne;
                    CLOSE varCursor;

                    IF ( inNumeroCarne IS NOT NULL ) THEN
                        --deixei o exercicio fixo, depois pensar se vale apena colocar dinamico
                        INSERT INTO arrecadacao.carne ( numeracao, cod_convenio, cod_parcela, cod_carteira, exercicio, impresso ) VALUES ( inNumeroCarne, inCodConvenio::integer, inCodParcela, inCodCarteira::integer, extract(year from now()), false );
                    END IF;

                    inCodParcela := inCodParcela + 1;
                    inLaco := inLaco + 1;
                END LOOP; --FOR reRegistro2 IN EXECUTE stSql3 LOOP
            ELSE --IF ( inQTDparcelas = -1 ) THEN
                --usando informacoes dos paramentros em vez das do calendario fiscal
                inLaco4 := 1;
                inLaco := 1;
                WHILE ( inLaco4 <= inQTDparcelas ) LOOP
                    IF ( split_part ( stNumeroParc, '';'', inLaco4 ) > 0 ) THEN --soh entra para parcelas diferentes de unicas
                        IF ( inLaco = 1 ) THEN --a primeira parcela a ser inserida usa o valor dos restos de arredondamento
                            --inserindo parcela normal
                            INSERT INTO arrecadacao.parcela ( cod_parcela, cod_lancamento, nr_parcela, vencimento, valor )
                                VALUES ( inCodParcela, inCodLancamento, inLaco, split_part ( stDTvencParc, '';'', inLaco4 )::date, flValorParcelaUm );

                            --verificando se existem descontos para a parcela normal
                            IF ( split_part ( stDescParc, '';'', inLaco4 )::numeric > 0 ) THEN
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
                                        IF ( split_part ( stTipoDescParc, '';'', inLaco4 ) = ''true'' ) THEN --tipo de desconto da parcela (true = percentual, false = por valor)
                                            flTMPval := ( flTMPval2 * split_part ( stDescParc, '';'', inLaco4 )::numeric ) / 100;
                                        ELSE
                                            flTMPval := flTMPval2 - split_part ( stDescParc, '';'', inLaco4 )::numeric;
                                        END IF;

                                        flValorParcelaDesc := flValorParcelaDesc + (flTMPval2 - flTMPval);
                                    ELSE
                                        flValorParcelaDesc := flValorParcelaDesc + flTMPval2;
                                    END IF;

                                    inLaco2 := inLaco2 + 1;
                                END LOOP;

                                INSERT INTO arrecadacao.parcela_desconto ( cod_parcela, vencimento, valor ) 
                                    VALUES ( inCodParcela, split_part ( stDTdescParc, '';'', inLaco4 )::date, flValorParcelaDesc );
                            END IF; --IF ( reRegistro2.valor_desconto > 0 )
                        ELSE --IF ( inLaco = 1 ) THEN -- as demais parcelas usam o valor normal
                            --inserindo parcela normal
                            INSERT INTO arrecadacao.parcela ( cod_parcela, cod_lancamento, nr_parcela, vencimento, valor ) 
                                VALUES ( inCodParcela, inCodLancamento, inLaco, split_part ( stDTvencParc, '';'', inLaco4 )::date, flValorParcela );

                            --verificando se existem descontos para a parcela normal
                            IF ( split_part ( stDescParc, '';'', inLaco4 ) > 0 ) THEN
                                inLaco2 := 0;
                                flValorParcelaDesc := 0;
                                WHILE ( inLaco2 < inTotaldeCreditos ) LOOP
                                    flTMPval2 := flValorPorCredito[inLaco2] / inTotalParcNormUsar; --calculando o valor das parcelas normais

                                    IF ( boUtilizarDesconto[inLaco2] = true ) THEN
                                        IF ( split_part ( stTipoDescParc, '';'', inLaco4 ) = ''true'' ) THEN --tipo de desconto da parcela (true = percentual, false = por valor)
                                            flTMPval := ( flTMPval2 * split_part ( stDescParc, '';'', inLaco4 )::numeric ) / 100;
                                        ELSE
                                            flTMPval := flTMPval2 - split_part ( stDescParc, '';'', inLaco4 )::numeric;
                                        END IF;

                                        flValorParcelaDesc := flValorParcelaDesc + (flTMPval2 - flTMPval);
                                    ELSE
                                        flValorParcelaDesc := flValorParcelaDesc + flTMPval2;
                                    END IF;
        
                                    inLaco2 := inLaco2 + 1;
                                END LOOP;

                                INSERT INTO arrecadacao.parcela_desconto ( cod_parcela, vencimento, valor ) 
                                    VALUES ( inCodParcela, split_part ( stDTdescParc, '';'', inLaco4 )::date, flValorParcelaDesc );
                            END IF; --IF ( split_part ( stDescParc, '';'', inLaco4 ) > 0 ) THEN
                        END IF; --IF ( inLaco = 1 ) THEN -- as demais parcelas usam o valor normal

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
                            stExecuta :=  ''SELECT ''||stFuncaoNumeracao||''(''||inCodCarteira||''::varchar,''||inCodConvenio||''::varchar) AS valor '';
                        ELSE
                            stExecuta :=  ''SELECT ''||stFuncaoNumeracao||''(''''''''::varchar,''||inCodConvenio||''::varchar) AS valor '';
                        END IF;
    
                        OPEN  varCursor FOR EXECUTE stExecuta;
                        FETCH varCursor INTO inNumeroCarne;
                        CLOSE varCursor;
    
                        IF ( inNumeroCarne IS NOT NULL ) THEN
                            --deixei o exercicio fixo, depois pensar se vale apena colocar dinamico
                            INSERT INTO arrecadacao.carne ( numeracao, cod_convenio, cod_parcela, cod_carteira, exercicio, impresso ) VALUES ( inNumeroCarne, inCodConvenio::integer, inCodParcela, inCodCarteira::integer, extract(year from now()), false );
                        END IF;
    
                        inCodParcela := inCodParcela + 1;
                        inLaco := inLaco + 1;
                    END IF; --IF ( split_part ( stNumeroParc, '';'', inLaco4 ) > 0 ) THEN --soh entra para parcelas diferentes de unicas

                    inLaco4 := inLaco4 + 1;
                END LOOP; --WHILE ( inLaco4 <= inQTDparcelas ) LOOP
            END IF; --IF ( inQTDparcelas = -1 ) THEN
        ELSE --IF ( flValorTotalGeral >= flValorMinimoLanc ) THEN
            --setar lancamento (ativo = false e o valor = flValorTotalGeral)
            UPDATE
                arrecadacao.lancamento

            SET
                valor = flValorTotalGeral,
                ativo = false

            WHERE
                cod_lancamento = inCodLancamento;
        END IF; --IF ( flValorTotalGeral >= flValorMinimoLanc ) THEN


    END LOOP; --FOR reRegistro IN EXECUTE stSql LOOP

    return stRetorno;
END;
' LANGUAGE 'plpgsql';
