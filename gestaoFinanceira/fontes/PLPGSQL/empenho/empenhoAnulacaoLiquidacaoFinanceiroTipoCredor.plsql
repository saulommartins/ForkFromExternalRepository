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
CREATE OR REPLACE FUNCTION empenhoanulacaoliquidacaofinanceirotipocredor(character varying, numeric, character varying, integer, character varying, integer, integer, character varying, integer) RETURNS INTEGER AS $$

DECLARE
    EXERCICIO       ALIAS FOR $1;
    VALOR           ALIAS FOR $2;
    COMPLEMENTO     ALIAS FOR $3;
    CODLOTE         ALIAS FOR $4;
    TIPOLOTE        ALIAS FOR $5;
    CODENTIDADE     ALIAS FOR $6;
    CODNOTA         ALIAS FOR $7;
    CLASDESPESA     ALIAS FOR $8;
    NUMORGAO        ALIAS FOR $9;

    SEQUENCIA               INTEGER;
    TIPOCREDOR              VARCHAR := '';
    CONTACONTABIL           VARCHAR := '';
    CONTACONTABILBENEFICIOS VARCHAR := '';
    CONTACREDITO            VARCHAR := '';

    crTeste refCursor;
    stSql varchar;
    inCount INTEGER:=0;
BEGIN

    stSql := 'SELECT count(*) 
                FROM administracao.configuracao
               WHERE exercicio  = '|| quote_literal(EXERCICIO) ||'
                 AND cod_modulo = 8
                 AND parametro  = ''cod_entidade_rpps''
                 AND valor      = '|| quote_literal(CODENTIDADE) ||';';

    OPEN crTeste FOR EXECUTE stSql ;
        FETCH crTeste INTO inCount;
    CLOSE crTeste;

    -- ROTINA PARA RPPS 
    IF EXERCICIO::integer > 2008 THEN 
        IF inCount  > 0  THEN                      -- EXERCICIO > 2008 and stParametro is not null THEN    -- AND RPPS
            CONTACREDITO   := '3.' || CLASDESPESA || '';
            CONTACONTABIL := '' || CLASDESPESA || '';
            CONTACONTABIL := substr(CONTACONTABIL, 1, 10);

            IF CONTACONTABIL = '3.3.9.0.01' THEN
                SEQUENCIA :=  FAZERLANCAMENTO( '21219080101', CONTACREDITO, 905, EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
            END IF;
            IF CONTACONTABIL = '3.3.9.0.03' THEN
                SEQUENCIA :=  FAZERLANCAMENTO( '21219080102', CONTACREDITO, 905, EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  ); 
            END IF;
            IF CONTACONTABIL = '3.3.9.0.09' THEN
                SEQUENCIA :=  FAZERLANCAMENTO( '21219080106', CONTACREDITO, 905, EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  ); 
            END IF;
            IF CONTACONTABIL = '3.3.9.0.08' THEN
                SEQUENCIA :=  FAZERLANCAMENTO( '21219080199', CONTACREDITO, 905, EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
            END IF;
            IF CONTACONTABIL = '3.3.9.0.13' THEN
                SEQUENCIA :=  FAZERLANCAMENTO( '21219080199', CONTACREDITO, 905, EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
            END IF;
            IF CONTACONTABIL = '3.3.9.0.05' THEN
                CONTACONTABIL := '' || CLASDESPESA || '';
                CONTACONTABILBENEFICIOS := substr(CONTACONTABIL, 1, 16);
                CONTACONTABIL := substr(CONTACONTABIL, 1, 13);
                IF CONTACONTABIL = '3.3.9.0.05.51' THEN
                    SEQUENCIA := FAZERLANCAMENTO( '21219080103', CONTACREDITO, 905, EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
                END IF;
                IF CONTACONTABIL = '3.3.9.0.05.53' THEN
                    SEQUENCIA := FAZERLANCAMENTO( '21219080104', CONTACREDITO, 905, EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
                END IF;
                IF CONTACONTABIL = '3.3.9.0.05.54' THEN
                    SEQUENCIA := FAZERLANCAMENTO( '21219080105', CONTACREDITO, 905, EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
                END IF;
                IF CONTACONTABIL = '3.3.9.0.05.55' THEN
                    SEQUENCIA := FAZERLANCAMENTO( '21219080106', CONTACREDITO, 905, EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
                END IF;
                IF CONTACONTABIL = '3.3.9.0.05.56' THEN
                    SEQUENCIA := FAZERLANCAMENTO( '21219080107', CONTACREDITO, 905, EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
                END IF;
                IF (CONTACONTABILBENEFICIOS = '3.3.9.0.05.01.01') THEN
                    SEQUENCIA := FAZERLANCAMENTO( '21219080103', CONTACREDITO, 905, EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
                END IF;
                IF (CONTACONTABILBENEFICIOS = '3.3.9.0.05.01.02') THEN
                    SEQUENCIA := FAZERLANCAMENTO( '21219080104', CONTACREDITO, 905, EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
                END IF;
                IF (CONTACONTABILBENEFICIOS = '3.3.9.0.05.02.01') THEN
                    SEQUENCIA := FAZERLANCAMENTO( '21219080108', CONTACREDITO, 905, EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
                END IF;
                IF (CONTACONTABILBENEFICIOS = '3.3.9.0.05.02.02') THEN
                    SEQUENCIA := FAZERLANCAMENTO( '21219080109', CONTACREDITO, 905, EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
                END IF;
                IF (CONTACONTABILBENEFICIOS = '3.3.9.0.05.03.01') THEN
                    SEQUENCIA := FAZERLANCAMENTO( '21219080110', CONTACREDITO, 905, EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
                END IF;
                IF (CONTACONTABILBENEFICIOS = '3.3.9.0.05.03.02') THEN
                    SEQUENCIA := FAZERLANCAMENTO( '21219080111', CONTACREDITO, 905, EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
                END IF;
                IF (CONTACONTABILBENEFICIOS = '3.3.9.0.05.04.01') THEN
                    SEQUENCIA := FAZERLANCAMENTO( '21219080107', CONTACREDITO, 905, EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
                END IF;
                IF (CONTACONTABILBENEFICIOS = '3.3.9.0.05.04.02') THEN
                    SEQUENCIA := FAZERLANCAMENTO( '21219080112', CONTACREDITO, 905, EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
                END IF;
                IF (CONTACONTABILBENEFICIOS = '3.3.9.0.05.05.01') THEN
                    SEQUENCIA := FAZERLANCAMENTO( '21219080106', CONTACREDITO, 905, EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
                END IF;
            END IF;
            IF CONTACONTABIL = '3.1.9.1.13' THEN
                CONTACONTABIL := '' || CLASDESPESA || '';
                CONTACONTABIL := substr(CONTACONTABIL, 1, 13);
                IF CONTACONTABIL = '3.1.9.1.13.03' THEN
                    SEQUENCIA := FAZERLANCAMENTO( '21213160001',  CONTACREDITO, 905, EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
                END IF;
            END IF;
        END IF;
        -- fim da rotina rpps

        -- ROTINA PARA ELEMENTOS NAO RPPS 
        IF SEQUENCIA is null THEN
            CONTACREDITO   := '3.' || CLASDESPESA || '';
            CONTACONTABIL := '' || CLASDESPESA || '';
            CONTACONTABIL := substr(CONTACONTABIL, 1, 13);

            IF CONTACONTABIL = '3.1.9.0.13.02' THEN
                SEQUENCIA := FAZERLANCAMENTO( '212130100010000', CONTACREDITO, 905, EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE );
            END IF;
            IF CONTACONTABIL = '3.3.9.0.13.02' THEN
                SEQUENCIA := FAZERLANCAMENTO( '212130100010000', CONTACREDITO, 905, EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE );
            END IF;
            IF CONTACONTABIL = '3.3.9.0.13.01' THEN
                SEQUENCIA := FAZERLANCAMENTO( '212130300010000', CONTACREDITO, 905, EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE );
            END IF;
            IF CONTACONTABIL = '3.3.9.0.47.12' THEN
                SEQUENCIA := FAZERLANCAMENTO( '212130400010000', CONTACREDITO, 905, EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE );
            END IF;
            IF CONTACONTABIL = '3.3.9.1.47.12' THEN
                SEQUENCIA := FAZERLANCAMENTO( '212130400010000', CONTACREDITO, 905, EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE );
            END IF;
            IF CONTACONTABIL = '3.1.9.0.13.01' THEN
                SEQUENCIA := FAZERLANCAMENTO( '212130300010000', CONTACREDITO, 905, EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE );
            END IF;
            IF SEQUENCIA is null AND substr(CONTACONTABIL, 1, 5) = '3.1.9' THEN
                SEQUENCIA := FAZERLANCAMENTO( '212120100010000', CONTACREDITO, 905, EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE );
            END IF;
            IF SEQUENCIA is null THEN
                SEQUENCIA := FAZERLANCAMENTO( '212110100010000', CONTACREDITO, 905, EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE );
            END IF;
        END IF;
        -- fim da rotina nao rpps

        -- ROTINA PARA QUANDO Exercicio FOR MENOR, IGUAL a 2008(tiver Atributos Dinâmicos)
    ELSE
        TIPOCREDOR := RECUPERAEMPENHOTIPOCREDOR(  EXERCICIO , CLASDESPESA , NUMORGAO  );
        CONTACONTABIL := '3.' || CLASDESPESA || ' ';
        IF TIPOCREDOR  =  'Fornecedor' THEN
            SEQUENCIA := FAZERLANCAMENTO(  '212110100010000' , CONTACONTABIL , 905 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
        IF TIPOCREDOR  =  'Pessoal' THEN
            SEQUENCIA := FAZERLANCAMENTO(  '212120100010000' , CONTACONTABIL , 905 ,EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
        IF TIPOCREDOR  =  'INSS' THEN
            SEQUENCIA := FAZERLANCAMENTO(  '212130100010000' , CONTACONTABIL , 905 ,EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
        IF TIPOCREDOR  =  'FGTS' THEN
            SEQUENCIA := FAZERLANCAMENTO(  '212130300010000' , CONTACONTABIL , 905 ,EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
        IF TIPOCREDOR  =  'PIS/PASEP' THEN
            SEQUENCIA := FAZERLANCAMENTO(  '212130400010000' , CONTACONTABIL , 905 ,EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
        IF TIPOCREDOR  =  'RPPS/Executivo' THEN
            SEQUENCIA := FAZERLANCAMENTO(  '212130800010100' , CONTACONTABIL , 905 ,EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
        IF TIPOCREDOR  =  'RPPS/Legislativo' THEN
            SEQUENCIA := FAZERLANCAMENTO(  '212130800010200' , CONTACONTABIL , 905 ,EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
    END IF;

    RETURN SEQUENCIA;
    END;

$$ LANGUAGE 'plpgsql';
