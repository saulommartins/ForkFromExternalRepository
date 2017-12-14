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
* $Id: fn_rel_divida_ativa.plsql 59913 2014-09-19 20:19:05Z lisiane $
*
* Caso de uso: 
*/
CREATE OR REPLACE FUNCTION divida.fn_rel_divida_ativa(
    stGrupoCredito              VARCHAR,
    stCredito                   VARCHAR,
    inInscIni                   INTEGER,
    stExerInscIni               VARCHAR,
    inInscFim                   INTEGER,
    stExerInscFim               VARCHAR,
    inNumCGM                    INTEGER,
    inInscImobIni               INTEGER,
    inInscImobFim               INTEGER,
    inInscEconIni               INTEGER,
    inInscEconFim               INTEGER,
    inLogradouro                INTEGER,
    flValorIni                  NUMERIC, 
    flValorFim                  NUMERIC, 
    inCodSituacao               INTEGER,
    stCriterio                  VARCHAR,
    stDataInicialCobranca       VARCHAR,
    stDataFinalCobranca         VARCHAR
) RETURNS SETOF RECORD AS $$
DECLARE
    stTabelaTmp         VARCHAR;
    stSQLDividaAtiva    VARCHAR;
    stSQLParcelamento   VARCHAR;
    stSQLTotaliza       VARCHAR;
    stSQLRelatorio      VARCHAR;
    stSQLConsulta       VARCHAR;
    stCase              VARCHAR;
    reRegistro          RECORD;
BEGIN
stTabelaTmp= to_char(now(),'ddmmyyyyhhmiss');
stSQLDividaAtiva:='
CREATE TEMP TABLE tmp_1'|| stTabelaTmp ||' AS
       SELECT DIVIDA_ATIVA.COD_INSCRICAO
                 ,DIVIDA_ATIVA.EXERCICIO
                 ,DIVIDA_ATIVA.DT_INSCRICAO
                 ,DIVIDA_ATIVA.DT_VENCIMENTO_ORIGEM
                 ,SW_CGM.NUMCGM
                 ,SW_CGM.NOM_CGM
                 ,DIVIDA_IMOVEL.INSCRICAO_MUNICIPAL
                 ,DIVIDA_EMPRESA.INSCRICAO_ECONOMICA
                 ,DIVIDA_REMISSAO.DT_REMISSAO
                 ,DIVIDA_REMISSAO.OBSERVACAO
        ,CASE WHEN DIVIDA_CANCELADA.COD_INSCRICAO IS NOT NULL THEN
            true
         ELSE
            false
         END AS DA_CANCELADA
        ,CASE WHEN DIVIDA_ESTORNO.COD_INSCRICAO IS NOT NULL THEN
            true
         ELSE
            false
         END AS DA_ESTORNO
        FROM DIVIDA.DIVIDA_ATIVA
INNER JOIN DIVIDA.DIVIDA_CGM
ON DIVIDA_ATIVA.COD_INSCRICAO = DIVIDA_CGM.COD_INSCRICAO
    AND DIVIDA_ATIVA.EXERCICIO = DIVIDA_CGM.EXERCICIO
INNER JOIN
    SW_CGM
ON
    DIVIDA_CGM.NUMCGM = SW_CGM.NUMCGM
LEFT JOIN
    DIVIDA.DIVIDA_IMOVEL
ON
    DIVIDA_ATIVA.COD_INSCRICAO = DIVIDA_IMOVEL.COD_INSCRICAO
    AND DIVIDA_ATIVA.EXERCICIO = DIVIDA_IMOVEL.EXERCICIO
LEFT JOIN
    DIVIDA.DIVIDA_EMPRESA
ON
    DIVIDA_ATIVA.COD_INSCRICAO = DIVIDA_EMPRESA.COD_INSCRICAO
    AND DIVIDA_ATIVA.EXERCICIO = DIVIDA_EMPRESA.EXERCICIO
LEFT JOIN
    DIVIDA.DIVIDA_CANCELADA
ON
    DIVIDA_ATIVA.COD_INSCRICAO = DIVIDA_CANCELADA.COD_INSCRICAO
    AND DIVIDA_ATIVA.EXERCICIO = DIVIDA_CANCELADA.EXERCICIO
LEFT JOIN
    DIVIDA.DIVIDA_ESTORNO
ON
    DIVIDA_ATIVA.COD_INSCRICAO = DIVIDA_ESTORNO.COD_INSCRICAO
    AND DIVIDA_ATIVA.EXERCICIO = DIVIDA_ESTORNO.EXERCICIO
LEFT JOIN
    DIVIDA.DIVIDA_REMISSAO
ON
    DIVIDA_ATIVA.COD_INSCRICAO = DIVIDA_REMISSAO.COD_INSCRICAO
    AND DIVIDA_ATIVA.EXERCICIO = DIVIDA_REMISSAO.EXERCICIO
LEFT JOIN
    (
        SELECT
             SW_NOME_LOGRADOURO.COD_LOGRADOURO
            ,IMOVEL_CONFRONTACAO.INSCRICAO_MUNICIPAL
        FROM
            IMOBILIARIO.IMOVEL_CONFRONTACAO
        INNER JOIN
            IMOBILIARIO.CONFRONTACAO_TRECHO
        ON
                IMOVEL_CONFRONTACAO.COD_LOTE = CONFRONTACAO_TRECHO.COD_LOTE
            AND IMOVEL_CONFRONTACAO.COD_CONFRONTACAO = CONFRONTACAO_TRECHO.COD_CONFRONTACAO
        INNER JOIN
            SW_NOME_LOGRADOURO
        ON
            SW_NOME_LOGRADOURO.COD_LOGRADOURO = CONFRONTACAO_TRECHO.COD_LOGRADOURO
            AND SW_NOME_LOGRADOURO.TIMESTAMP = (SELECT MAX(TIMESTAMP) FROM SW_NOME_LOGRADOURO WHERE COD_LOGRADOURO = CONFRONTACAO_TRECHO.COD_LOGRADOURO)
    ) AS LOGRADOURO
ON
    DIVIDA_IMOVEL.INSCRICAO_MUNICIPAL = LOGRADOURO.INSCRICAO_MUNICIPAL    
WHERE
    ( 1=1 )
';
--DIVIDA_ESTORNO.COD_INSCRICAO IS NULL
IF inCodSituacao = 5 THEN --divida cancelada
    stSQLDividaAtiva := stSQLDividaAtiva || ' AND DIVIDA_CANCELADA.COD_INSCRICAO IS NOT NULL ';
--ELSE
--    stSQLDividaAtiva := stSQLDividaAtiva || ' AND DIVIDA_CANCELADA.COD_INSCRICAO IS NULL ';
END IF;

IF inNumCGM is not null then
    stSQLDividaAtiva:= stSQLDividaAtiva|| ' and SW_CGM.NUMCGM='|| inNumCGM;
end if;

IF inLogradouro IS NOT NULL THEN
    stSQLDividaAtiva:= stSQLDividaAtiva|| ' and LOGRADOURO.COD_LOGRADOURO='|| inLogradouro;
END IF;


IF inInscIni IS NOT NULL AND inInscFim IS NULL THEN
    stSQLDividaAtiva:= stSQLDividaAtiva|| '   AND divida_ativa.cod_inscricao='||inInscIni;
    stSQLDividaAtiva:= stSQLDividaAtiva|| '   AND divida_ativa.exercicio=''||stExerInscIni||''';
ELSE
    IF inInscIni IS NOT NULL AND inInscFim IS NOT NULL THEN
        stSQLDividaAtiva:= stSQLDividaAtiva||' AND (divida_ativa.exercicio||lpad(divida_ativa.cod_inscricao::text,14,''0''))::bigint BETWEEN';
        stSQLDividaAtiva:= stSQLDividaAtiva||'('''||stExerInscIni||'''||lpad('''||inInscIni||''',14,''0''))::bigint AND ';
        stSQLDividaAtiva:= stSQLDividaAtiva||'('''||stExerInscFim||'''||lpad('''||inInscFim||''',14,''0''))::bigint ';
    ELSE
        IF inInscIni IS NULL AND inInscFim IS NOT NULL THEN
            stSQLDividaAtiva:= stSQLDividaAtiva|| '   AND divida_ativa.cod_inscricao='||inInscFim;
            stSQLDividaAtiva:= stSQLDividaAtiva|| '   AND divida_ativa.exercicio=''||stExerInscFim||''';
        END IF;
    END IF;
END IF;

IF inInscImobIni IS NOT NULL AND inInscImobFim IS NULL THEN
    stSQLDividaAtiva:= stSQLDividaAtiva ||' AND DIVIDA_IMOVEL.INSCRICAO_MUNICIPAL='|| inInscImobIni;
ELSE
    IF inInscImobIni IS NOT NULL AND inInscImobFim IS NOT NULL THEN
        stSQLDividaAtiva:= stSQLDividaAtiva ||' AND DIVIDA_IMOVEL.INSCRICAO_MUNICIPAL BETWEEN '|| inInscImobIni ||' AND '|| inInscImobFim;
    ELSE
        IF inInscImobIni IS NULL AND inInscImobFim IS NOT NULL THEN
            stSQLDividaAtiva:= stSQLDividaAtiva ||' AND DIVIDA_IMOVEL.INSCRICAO_MUNICIPAL='|| inInscImobFim;
        END IF;
    END IF;
END IF;


IF inInscEconIni IS NOT NULL AND inInscEconFim IS NULL THEN
    stSQLDividaAtiva:= stSQLDividaAtiva ||' AND DIVIDA_EMPRESA.INSCRICAO_ECONOMICA='|| inInscEconIni;
ELSE
    IF inInscEconIni IS NOT NULL AND inInscEconFim IS NOT NULL THEN
        stSQLDividaAtiva:= stSQLDividaAtiva ||' AND DIVIDA_EMPRESA.INSCRICAO_ECONOMICA BETWEEN '|| inInscEconIni ||' AND '|| inInscEconFim;
    ELSE
        IF inInscEconIni IS NULL AND inInscEconFim IS NOT NULL THEN
           stSQLDividaAtiva:= stSQLDividaAtiva ||' AND DIVIDA_EMPRESA.INSCRICAO_ECONOMICA='|| inInscEconFim;
        END IF;
    END IF;
END IF;

EXECUTE stSQLDividaAtiva;

stSQLParcelamento:= '
CREATE TEMP TABLE tmp_2'|| stTabelaTmp ||' AS
    SELECT PARCELAMENTO.NUM_PARCELAMENTO
         , DIVIDA_PARCELAMENTO.NUM_PARCELAMENTO_MAX
         , PARCELAMENTO.TIMESTAMP_MODALIDADE
         , PARCELAMENTO.COD_MODALIDADE
         , PARCELAMENTO.NUMERO_PARCELAMENTO
         , PARCELAMENTO.EXERCICIO
         , DIVIDA_PARCELAMENTO.COD_INSCRICAO
         , DIVIDA_PARCELAMENTO.EXERCICIO AS EXERCICIO_INSC
         , DIVIDA_PARCELAMENTO.VALOR
         , tmp_1'||stTabelaTmp||'.inscricao_municipal
         , tmp_1'||stTabelaTmp||'.inscricao_economica
         , tmp_1'||stTabelaTmp||'.numcgm
         , tmp_1'||stTabelaTmp||'.nom_cgm
         , tmp_1'||stTabelaTmp||'.dt_vencimento_origem
         , tmp_1'||stTabelaTmp||'.dt_inscricao
         , tmp_1'||stTabelaTmp||'.dt_remissao
         , tmp_1'||stTabelaTmp||'.observacao
         , tmp_1'||stTabelaTmp||'.DA_ESTORNO
         , tmp_1'||stTabelaTmp||'.DA_CANCELADA
         , ( SELECT ARRAY_TO_STRING(ARRAY(SELECT distinct cod_credito||'||quote_literal('.')||'||cod_especie||'||quote_literal('.')||'||cod_genero||'||quote_literal('.')||'||cod_natureza AS credito
                                          FROM divida.parcela_origem
                                         WHERE num_parcelamento=PARCELAMENTO.NUM_PARCELAMENTO),'||quote_literal(', ')||') ) AS creditos
         , DIVIDA_PARCELAMENTO.DESCRICAO
         , DIVIDA_PARCELAMENTO.COD_GRUPO
         , DIVIDA_PARCELAMENTO.ANO_EXERCICIO
      FROM ( SELECT SUM(PARCELA_ORIGEM.VALOR) AS VALOR
                  , DIVIDA_PARCELAMENTO.NUM_PARCELAMENTO
                  , DIVIDA_PARCELAMENTO.COD_INSCRICAO
                  , DIVIDA_PARCELAMENTO.EXERCICIO
                  , DIVIDA_PARCELAMENTO_MAX.NUM_PARCELAMENTO_MAX
                  , GRUPO_CREDITO.DESCRICAO ';
                  
IF stGrupoCredito != 'null' THEN
    stSQLParcelamento := stSQLParcelamento ||', GRUPO_CREDITO.COD_GRUPO ';
ELSE
    IF stGrupoCredito = 'null' THEN
        stSQLParcelamento := stSQLParcelamento ||', 0 as COD_GRUPO';
    END IF;  
END IF;                  
                  
  stSQLParcelamento := stSQLParcelamento ||'   
                  , GRUPO_CREDITO.ANO_EXERCICIO
               FROM DIVIDA.DIVIDA_PARCELAMENTO

         INNER JOIN tmp_1'|| stTabelaTmp ||'
                 ON DIVIDA_PARCELAMENTO.COD_INSCRICAO = tmp_1'|| stTabelaTmp ||'.COD_INSCRICAO
                AND DIVIDA_PARCELAMENTO.EXERCICIO = tmp_1'|| stTabelaTmp ||'.EXERCICIO

         INNER JOIN ( SELECT MAX(NUM_PARCELAMENTO) AS NUM_PARCELAMENTO_MAX
                  , DIVIDA_PARCELAMENTO.COD_INSCRICAO
                  , DIVIDA_PARCELAMENTO.EXERCICIO
               FROM DIVIDA.DIVIDA_PARCELAMENTO
           GROUP BY DIVIDA_PARCELAMENTO.COD_INSCRICAO
                  , DIVIDA_PARCELAMENTO.EXERCICIO
                    ) AS DIVIDA_PARCELAMENTO_MAX
                 ON DIVIDA_PARCELAMENTO.COD_INSCRICAO = DIVIDA_PARCELAMENTO_MAX.COD_INSCRICAO
                AND DIVIDA_PARCELAMENTO.EXERCICIO = DIVIDA_PARCELAMENTO_MAX.EXERCICIO

         INNER JOIN DIVIDA.PARCELAMENTO
                 ON PARCELAMENTO.NUM_PARCELAMENTO = DIVIDA_PARCELAMENTO.NUM_PARCELAMENTO

         INNER JOIN DIVIDA.PARCELA_ORIGEM
                 ON PARCELA_ORIGEM.NUM_PARCELAMENTO = PARCELAMENTO.NUM_PARCELAMENTO

         INNER JOIN ARRECADACAO.PARCELA
                 ON PARCELA_ORIGEM.COD_PARCELA = PARCELA.COD_PARCELA

         INNER JOIN ARRECADACAO.LANCAMENTO_CALCULO
                 ON LANCAMENTO_CALCULO.COD_LANCAMENTO = PARCELA.COD_LANCAMENTO

         INNER JOIN arrecadacao.calculo
                 ON calculo.cod_calculo = LANCAMENTO_CALCULO.cod_calculo
                AND calculo.cod_credito = PARCELA_ORIGEM.cod_credito
                AND calculo.cod_especie = PARCELA_ORIGEM.cod_especie
                AND calculo.cod_genero = PARCELA_ORIGEM.cod_genero
                AND calculo.cod_natureza = PARCELA_ORIGEM.cod_natureza

          LEFT JOIN ARRECADACAO.CALCULO_GRUPO_CREDITO
                 ON calculo.COD_CALCULO = CALCULO_GRUPO_CREDITO.COD_CALCULO

          LEFT JOIN ARRECADACAO.GRUPO_CREDITO
                 ON CALCULO_GRUPO_CREDITO.COD_GRUPO = GRUPO_CREDITO.COD_GRUPO
                AND CALCULO_GRUPO_CREDITO.ANO_EXERCICIO = GRUPO_CREDITO.ANO_EXERCICIO

              WHERE PARCELAMENTO.NUMERO_PARCELAMENTO = -1
                AND PARCELAMENTO.EXERCICIO = ''-1''
';

IF stGrupoCredito != 'null' THEN
    stSQLParcelamento := stSQLParcelamento ||' AND '|| stGrupoCredito;
END IF;
IF stCredito != 'null' THEN
    stSQLParcelamento := stSQLParcelamento ||' AND '|| stCredito;
END IF;
stSQLParcelamento := stSQLParcelamento ||'

           GROUP BY
                   DIVIDA_PARCELAMENTO.NUM_PARCELAMENTO
                  , DIVIDA_PARCELAMENTO.COD_INSCRICAO
                  , DIVIDA_PARCELAMENTO.EXERCICIO
                  , DIVIDA_PARCELAMENTO_MAX.NUM_PARCELAMENTO_MAX
                  , GRUPO_CREDITO.DESCRICAO
                  , GRUPO_CREDITO.COD_GRUPO
                  , GRUPO_CREDITO.ANO_EXERCICIO
           ) AS DIVIDA_PARCELAMENTO

INNER JOIN DIVIDA.PARCELAMENTO
        ON PARCELAMENTO.NUM_PARCELAMENTO = DIVIDA_PARCELAMENTO.NUM_PARCELAMENTO

INNER JOIN tmp_1'|| stTabelaTmp ||'
        ON DIVIDA_PARCELAMENTO.COD_INSCRICAO = tmp_1'|| stTabelaTmp ||'.COD_INSCRICAO
       AND DIVIDA_PARCELAMENTO.EXERCICIO = tmp_1'|| stTabelaTmp ||'.EXERCICIO

     WHERE (1=1)
';


IF flValorIni IS NOT NULL AND flValorFim IS NULL THEN
    stSQLParcelamento:= stSQLParcelamento ||' AND DIVIDA_PARCELAMENTO.VALOR ='|| flValorIni;
ELSE
    IF flValorIni IS NULL AND flValorFim IS NOT NULL THEN
        stSQLParcelamento:= stSQLParcelamento ||' AND DIVIDA_PARCELAMENTO.VALOR ='|| flValorFim;
    ELSE
        IF flValorIni IS NOT NULL AND flValorFim IS NOT NULL THEN
            stSQLParcelamento:= stSQLParcelamento ||' AND DIVIDA_PARCELAMENTO.VALOR BETWEEN '|| flValorIni ||' AND '|| flValorFim;
        END IF;
    END IF;
END IF;

EXECUTE stSQLParcelamento;

stSQLTotaliza :='
CREATE TEMP TABLE tmp_3'|| stTabelaTmp ||' AS
    SELECT tmp_2'|| stTabelaTmp ||'.num_parcelamento
         , tmp_2'|| stTabelaTmp ||'.num_parcelamento_max
         , tmp_2'|| stTabelaTmp ||'.cod_inscricao
         , tmp_2'|| stTabelaTmp ||'.exercicio_insc
         , tmp_2'|| stTabelaTmp ||'.timestamp_modalidade
         , tmp_2'|| stTabelaTmp ||'.cod_modalidade
         , tmp_2'|| stTabelaTmp ||'.valor
         , tmp_2'|| stTabelaTmp ||'.inscricao_municipal
         , tmp_2'|| stTabelaTmp ||'.inscricao_economica
         , tmp_2'|| stTabelaTmp ||'.numcgm
         , tmp_2'|| stTabelaTmp ||'.nom_cgm
         , tmp_2'|| stTabelaTmp ||'.dt_vencimento_origem
         , tmp_2'|| stTabelaTmp ||'.dt_inscricao
         , tmp_2'|| stTabelaTmp ||'.dt_remissao
         , tmp_2'|| stTabelaTmp ||'.CREDITOS
         , tmp_2'|| stTabelaTmp ||'.DESCRICAO
         , tmp_2'|| stTabelaTmp ||'.COD_GRUPO
         , tmp_2'|| stTabelaTmp ||'.ANO_EXERCICIO
         , tmp_2'|| stTabelaTmp ||'.DA_ESTORNO
         , tmp_2'|| stTabelaTmp ||'.DA_CANCELADA
         , PARCELA.DT_VENCIMENTO_PARCELA
         , PARCELA.CANCELADA
         , SITUACAO.PAGA
         , CONTADOR.QTD_PARCELAS
         , CASE WHEN CONTADOR.QTD_PARCELAS = ANULADAS.QTD_PARCELAS_ANULADAS AND CONTADOR.QTD_PARCELAS > 0 THEN 
                TRUE
           ELSE 
                FALSE 
           END AS PARCELAMENTO_ANULADO
         , retorna_acrescimos_inscricao_relatorio_divida_ativa( tmp_2'|| stTabelaTmp ||'.cod_inscricao, tmp_2'|| stTabelaTmp ||'.exercicio_insc::integer, tmp_2'|| stTabelaTmp ||'.num_parcelamento_max, tmp_2'|| stTabelaTmp ||'.num_parcelamento != tmp_2'|| stTabelaTmp ||'.num_parcelamento_max, 3 ) AS multa
         , retorna_acrescimos_inscricao_relatorio_divida_ativa( tmp_2'|| stTabelaTmp ||'.cod_inscricao, tmp_2'|| stTabelaTmp ||'.exercicio_insc::integer, tmp_2'|| stTabelaTmp ||'.num_parcelamento_max, tmp_2'|| stTabelaTmp ||'.num_parcelamento != tmp_2'|| stTabelaTmp ||'.num_parcelamento_max, 2 ) AS juros
         , retorna_acrescimos_inscricao_relatorio_divida_ativa( tmp_2'|| stTabelaTmp ||'.cod_inscricao, tmp_2'|| stTabelaTmp ||'.exercicio_insc::integer, tmp_2'|| stTabelaTmp ||'.num_parcelamento_max, tmp_2'|| stTabelaTmp ||'.num_parcelamento != tmp_2'|| stTabelaTmp ||'.num_parcelamento_max, 1 ) AS correcao
      FROM tmp_2'|| stTabelaTmp ||'
 LEFT JOIN DIVIDA.PARCELA --finado PARCELAMENTO
        ON PARCELA.NUM_PARCELAMENTO = tmp_2'|| stTabelaTmp ||'.NUM_PARCELAMENTO_MAX
       AND PARCELA.NUM_PARCELA = 1
       AND PARCELA.CANCELADA = false

 LEFT JOIN ( SELECT MAX(PARCELA.NUM_PARCELA) AS NUM_PARCELA
                  , PARCELA.NUM_PARCELAMENTO
                  , PARCELA.PAGA
               FROM DIVIDA.PARCELA
              WHERE PARCELA.CANCELADA = false
           GROUP BY PARCELA.NUM_PARCELAMENTO
                  , PARCELA.PAGA
            )AS SITUACAO
        ON SITUACAO.NUM_PARCELAMENTO = tmp_2'|| stTabelaTmp ||'.NUM_PARCELAMENTO_MAX

 LEFT JOIN ( SELECT PARCELA.NUM_PARCELAMENTO
                  , count(*) AS qtd_parcelas
               FROM DIVIDA.PARCELA
           GROUP BY PARCELA.NUM_PARCELAMENTO
           ) AS CONTADOR
        ON CONTADOR.NUM_PARCELAMENTO = tmp_2'|| stTabelaTmp ||'.NUM_PARCELAMENTO_MAX

 LEFT JOIN ( SELECT PARCELA.NUM_PARCELAMENTO
                  , count(*) AS qtd_parcelas_anuladas
               FROM DIVIDA.PARCELA
              WHERE PARCELA.CANCELADA = true
           GROUP BY PARCELA.NUM_PARCELAMENTO
           ) AS ANULADAS
        ON ANULADAS.NUM_PARCELAMENTO = tmp_2'|| stTabelaTmp ||'.NUM_PARCELAMENTO_MAX

     WHERE (1=1)
';

IF inCodSituacao = 7 THEN --parcela vencida
    stSQLTotaliza := stSQLTotaliza ||' AND PARCELA.DT_VENCIMENTO_PARCELA < now() AND SITUACAO.paga = false '; --esquema para listar parcelas vencidas
END IF;

EXECUTE stSQLTotaliza;


--CASES para tipo de situacao COM COBRANCA ou SEM COBRANCA
IF inCodSituacao = 2 OR inCodSituacao = 6 THEN
    --COM COBRANCA
    IF inCodSituacao = 2 THEN
        stCase := ' CASE WHEN DA_ESTORNO = true THEN
                            ''Estornada''
                         WHEN NUM_PARCELAMENTO = NUM_PARCELAMENTO_MAX  THEN
                            ''Aberta''
                         WHEN DA_CANCELADA = true THEN -- divida
                            ''Cancelada''
                    END AS SITUACAO ';
    END IF;
    --SEM COBRANCA
    IF inCodSituacao = 6 THEN
        stCase := ' CASE WHEN paga = true  THEN 
                            ''Pago''
                         WHEN tmp_3'||stTabelaTmp||'.PARCELAMENTO_ANULADO = true THEN -- cobranca
                            ''Cancelada''
                         WHEN DT_VENCIMENTO_PARCELA < now() AND tmp_3'|| stTabelaTmp ||'.PAGA = false THEN
                            ''Vencida''
                    END AS SITUACAO ';
    END IF;

ELSE
    stCase := ' CASE WHEN DT_REMISSAO IS NOT NULL THEN 
                        ''Remida''
                     WHEN DA_ESTORNO = true THEN
                        ''Estornada''
                     WHEN paga = true  THEN 
                        ''Pago''        
                     WHEN DA_CANCELADA = true THEN -- divida
                        ''Cancelada''
                     WHEN DT_VENCIMENTO_PARCELA < now() AND tmp_3'|| stTabelaTmp ||'.PAGA = false THEN
                        ''Vencida''
                     WHEN tmp_3'||stTabelaTmp||'.PARCELAMENTO_ANULADO = true THEN -- cobranca
                        ''Cancelada''
                     WHEN NUM_PARCELAMENTO = NUM_PARCELAMENTO_MAX  THEN
                        ''Aberta'' 
                END AS SITUACAO ';
END IF;

stSQLRelatorio :='
            create temp table tmp_4'|| stTabelaTmp ||' as
            SELECT * FROM(
                SELECT cod_inscricao as inscricao_da
                    , exercicio_insc as exercicio_da
                    , inscricao_municipal
                    , inscricao_economica
                    , numcgm
                    , nom_cgm
                    , valor as valor_original
                    , qtd_parcelas::integer
                    , CREDITOS
                    , cod_grupo
                    , ANO_EXERCICIO
                    , DESCRICAO
                    , multa||''#''||juros||''#''||correcao AS array_acrescimos
                    , '|| stCase ||' 
                    , CAST(
                       CASE WHEN DT_REMISSAO IS NOT NULL THEN 
                            1
                      WHEN DA_ESTORNO = true THEN
                            2
                      WHEN paga = true  THEN 
                            3        
                      WHEN DA_CANCELADA = true THEN -- divida
                            4
                      WHEN DT_VENCIMENTO_PARCELA < now() AND tmp_3'|| stTabelaTmp ||'.PAGA = false THEN
                            5
                      WHEN tmp_3'||stTabelaTmp||'.PARCELAMENTO_ANULADO = true THEN -- cobranca
                            6
                       WHEN NUM_PARCELAMENTO = NUM_PARCELAMENTO_MAX  THEN
                            7
                      END AS INTEGER )AS COD_SITUACAO
                 FROM tmp_3'|| stTabelaTmp ||'
                WHERE ( 1=1 )
';

IF stDataInicialCobranca IS NOT NULL OR stDataInicialCobranca != ' ' THEN
        stSQLRelatorio := stSQLRelatorio || ' AND  tmp_3'|| stTabelaTmp ||'.DT_VENCIMENTO_PARCELA  > cast('||quote_literal(stDataInicialCobranca) ||' as date ) AND tmp_3'|| stTabelaTmp ||'.DT_VENCIMENTO_PARCELA < cast('||quote_literal(stDataFinalCobranca) ||' as date) ' ;
END IF ;

IF inCodSituacao = 2 THEN --sem cobrança

    IF stCriterio = 'cancelada' THEN 
        stSQLRelatorio := stSQLRelatorio || ' AND DA_CANCELADA = true' ;
    END IF ;   

    IF stCriterio = 'aberta' THEN
        stSQLRelatorio := stSQLRelatorio || ' AND NUM_PARCELAMENTO = NUM_PARCELAMENTO_MAX  AND DA_ESTORNO = false ';
              
    END IF ;
 
    IF stCriterio = 'estornada' THEN
        stSQLRelatorio := stSQLRelatorio || ' AND DA_ESTORNO = true ';
    END IF ;

END IF ;   

IF inCodSituacao = 6 THEN --divida com cobranca

    IF stCriterio = 'pago' THEN 
        stSQLRelatorio :=  stSQLRelatorio || ' AND tmp_3'|| stTabelaTmp ||'.PAGA = true ';
    END IF ;

    IF stCriterio = 'cancelada' THEN 
        stSQLRelatorio := stSQLRelatorio || ' AND tmp_3'||stTabelaTmp||'.PARCELAMENTO_ANULADO = true ' ;
    END IF ;   
 
    IF stCriterio = 'vencida' THEN 
        stSQLRelatorio := stSQLRelatorio || ' AND tmp_3'|| stTabelaTmp ||'.DT_VENCIMENTO_PARCELA < now() AND tmp_3'|| stTabelaTmp ||'.PAGA = false '; 
    END IF ;
    
END IF;
stSQLRelatorio := stSQLRelatorio || ') as resultado';

IF stCriterio = 'null' THEN
    stSQLRelatorio := stSQLRelatorio || ' WHERE SITUACAO IS NOT NULL ';
END IF ;

EXECUTE stSQLRelatorio;

stSQLConsulta := 'SELECT * FROM tmp_4'|| stTabelaTmp;

FOR reRegistro IN EXECUTE stSQLConsulta LOOP
    return next reRegistro;
END LOOP;

execute 'drop table tmp_1'|| stTabelaTmp;
execute 'drop table tmp_2'|| stTabelaTmp;
execute 'drop table tmp_3'|| stTabelaTmp;
execute 'drop table tmp_4'|| stTabelaTmp;

return;
END;
$$ LANGUAGE 'plpgsql';
