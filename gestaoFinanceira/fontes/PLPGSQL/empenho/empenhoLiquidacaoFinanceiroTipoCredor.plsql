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
CREATE OR REPLACE FUNCTION  empenholiquidacaofinanceirotipocredor(character varying, numeric, character varying, integer, character varying, integer, integer, character varying, integer) RETURNS INTEGER AS $$

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
    
    CONTACONTABIL           VARCHAR := '';
    CONTACONTABILBENEFICIOS VARCHAR := '';
    SEQUENCIA               INTEGER;
    TIPOCREDOR              VARCHAR := '';
    CONTADEBITO             VARCHAR := '';
    crTeste                 refCursor;
    stSql                   VARCHAR;
    inCount                 INTEGER:=0;
BEGIN

    stSql := 'SELECT count(*) 
                FROM administracao.configuracao
               WHERE exercicio  = '|| quote_literal(EXERCICIO) ||'
                 AND cod_modulo = 8
                 AND parametro  = '|| quote_literal('cod_entidade_rpps') ||' 
                 AND valor      = '|| quote_literal(CODENTIDADE);

    OPEN crTeste FOR EXECUTE stSql ;
       FETCH crTeste INTO inCount;
    CLOSE crTeste;

    -- ROTINA PARA RPPS 
    IF EXERCICIO::integer > 2008 THEN
        IF inCount  > 0 THEN                                         --   EXERCICIO > 2008 and stParametro is not null THEN    -- AND RPPS
            CONTADEBITO   := '3.' || CLASDESPESA || '';
            CONTACONTABIL := '' || CLASDESPESA || '';
            CONTACONTABIL := substr(CONTACONTABIL, 1, 10);
            IF CONTACONTABIL = '3.3.9.0.01' THEN
                SEQUENCIA :=  FAZERLANCAMENTO( '' || CONTADEBITO || '', '21219080101', 902, EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
            END IF;
            IF CONTACONTABIL = '3.3.9.0.03' THEN
                SEQUENCIA :=  FAZERLANCAMENTO( '' || CONTADEBITO || '', '21219080102', 902, EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  ); 
            END IF;
            IF CONTACONTABIL = '3.3.9.0.09' THEN
                SEQUENCIA :=  FAZERLANCAMENTO( '' || CONTADEBITO || '', '21219080106', 902, EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  ); 
            END IF;
            IF CONTACONTABIL = '3.3.9.0.08' THEN
                SEQUENCIA :=  FAZERLANCAMENTO( '' || CONTADEBITO || '', '21219080199', 902, EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
            END IF;
            IF CONTACONTABIL = '3.3.9.0.13' THEN   
                SEQUENCIA :=  FAZERLANCAMENTO( '' || CONTADEBITO || '', '21219080199', 902, EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
            END IF; 
            IF CONTACONTABIL = '3.3.9.0.05' THEN
                CONTACONTABIL := '' || CLASDESPESA || '';
                CONTACONTABILBENEFICIOS := substr(CONTACONTABIL, 1, 16);
                CONTACONTABIL := substr(CONTACONTABIL, 1, 13);
                IF CONTACONTABIL = '3.3.9.0.05.51' THEN 
                    SEQUENCIA := FAZERLANCAMENTO( '' || CONTADEBITO || '', '21219080103', 902, EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
                END IF;
                IF CONTACONTABIL = '3.3.9.0.05.53' THEN
                    SEQUENCIA := FAZERLANCAMENTO( '' || CONTADEBITO || '', '21219080104', 902, EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
                END IF;
                IF CONTACONTABIL = '3.3.9.0.05.54' THEN
                    SEQUENCIA := FAZERLANCAMENTO( '' || CONTADEBITO || '', '21219080105', 902, EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
                END IF;
                IF CONTACONTABIL = '3.3.9.0.05.55' THEN 
                    SEQUENCIA := FAZERLANCAMENTO( '' || CONTADEBITO || '', '21219080106', 902, EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
                END IF;
                IF CONTACONTABIL = '3.3.9.0.05.56' THEN 
                    SEQUENCIA := FAZERLANCAMENTO( '' || CONTADEBITO || '', '21219080107', 902, EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
                END IF;
                IF (CONTACONTABILBENEFICIOS = '3.3.9.0.05.01.01') THEN 
                    SEQUENCIA := FAZERLANCAMENTO( '' || CONTADEBITO || '', '21219080103', 902, EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
                END IF;
                IF (CONTACONTABILBENEFICIOS = '3.3.9.0.05.01.02') THEN 
                    SEQUENCIA := FAZERLANCAMENTO( '' || CONTADEBITO || '', '21219080104', 902, EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
                END IF;
                IF (CONTACONTABILBENEFICIOS = '3.3.9.0.05.02.01') THEN 
                    SEQUENCIA := FAZERLANCAMENTO( '' || CONTADEBITO || '', '21219080108', 902, EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
                END IF;
                IF (CONTACONTABILBENEFICIOS = '3.3.9.0.05.02.02') THEN 
                    SEQUENCIA := FAZERLANCAMENTO( '' || CONTADEBITO || '', '21219080109', 902, EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
                END IF;
                IF (CONTACONTABILBENEFICIOS = '3.3.9.0.05.03.01') THEN 
                    SEQUENCIA := FAZERLANCAMENTO( '' || CONTADEBITO || '', '21219080110', 902, EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
                END IF;
                IF (CONTACONTABILBENEFICIOS = '3.3.9.0.05.03.02') THEN 
                    SEQUENCIA := FAZERLANCAMENTO( '' || CONTADEBITO || '', '21219080111', 902, EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
                END IF;
                IF (CONTACONTABILBENEFICIOS = '3.3.9.0.05.04.01') THEN 
                    SEQUENCIA := FAZERLANCAMENTO( '' || CONTADEBITO || '', '21219080107', 902, EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
                END IF;
                IF (CONTACONTABILBENEFICIOS = '3.3.9.0.05.04.02') THEN 
                    SEQUENCIA := FAZERLANCAMENTO( '' || CONTADEBITO || '', '21219080112', 902, EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
                END IF;
                IF (CONTACONTABILBENEFICIOS = '3.3.9.0.05.05.01') THEN 
                    SEQUENCIA := FAZERLANCAMENTO( '' || CONTADEBITO || '', '21219080106', 902, EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
                END IF;
            END IF;
            IF CONTACONTABIL = '3.1.9.1.13' THEN
                CONTACONTABIL := '' || CLASDESPESA || '';
                CONTACONTABIL := substr(CONTACONTABIL, 1, 13);
                IF CONTACONTABIL = '3.1.9.1.13.03' THEN
                    SEQUENCIA := FAZERLANCAMENTO( '' || CONTADEBITO || '', '21213160001', 902, EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
                END IF;
            END IF;

        END IF; -- FINAL IN COUNT
        -- fim da rotina rpps

        -- ROTINA PARA ELEMENTOS NAO RPPS 
        IF SEQUENCIA is null THEN
            CONTADEBITO   := '3.' || CLASDESPESA || '';
            CONTACONTABIL := '' || CLASDESPESA || '';
            CONTACONTABIL := substr(CONTACONTABIL, 1, 13);

            IF CONTACONTABIL = '3.1.9.0.13.02' THEN
                SEQUENCIA := FAZERLANCAMENTO( '' || CONTADEBITO || '', '212130100010000', 902, EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE );
            END IF;
            IF CONTACONTABIL = '3.3.9.0.13.02' THEN
                SEQUENCIA := FAZERLANCAMENTO( '' || CONTADEBITO || '', '212130100010000', 902, EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE );
            END IF;
            IF CONTACONTABIL = '3.3.9.0.13.01' THEN
                SEQUENCIA := FAZERLANCAMENTO( '' || CONTADEBITO || '', '212130300010000', 902, EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE );
            END IF;
            IF CONTACONTABIL = '3.3.9.0.47.12' THEN
                SEQUENCIA := FAZERLANCAMENTO( '' || CONTADEBITO || '', '212130400010000', 902, EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE );
            END IF;
            IF CONTACONTABIL = '3.3.9.1.47.12' THEN
                SEQUENCIA := FAZERLANCAMENTO( '' || CONTADEBITO || '', '212130400010000', 902, EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE );
            END IF;
            IF CONTACONTABIL = '3.1.9.0.13.01' THEN
                SEQUENCIA := FAZERLANCAMENTO( '' || CONTADEBITO || '', '212130300010000', 902, EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE );
            END IF;
            IF SEQUENCIA is null AND substr(CONTACONTABIL, 1, 5) = '3.1.9' THEN
                SEQUENCIA := FAZERLANCAMENTO( '' || CONTADEBITO || '', '212120100010000', 902, EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE ); 
            END IF;
            IF SEQUENCIA is null THEN
                SEQUENCIA := FAZERLANCAMENTO( '' || CONTADEBITO || '', '212110100010000', 902, EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE );
            END IF;

        END IF;
        -- fim da rotina nao rpps

        -- ROTINA para exercicio menor, igual a 2008(tipo credor)

    ELSE -- ELSE PARA 2008
        TIPOCREDOR := PEGAEMPENHOLIQUIDACAOTIPOCREDOR(  EXERCICIO , CODNOTA , CODENTIDADE);
        CONTACONTABIL := '3.' || CLASDESPESA || ' ';
        IF TIPOCREDOR  =  'Fornecedor' THEN
           SEQUENCIA := FAZERLANCAMENTO(  CONTACONTABIL , '212110100010000' , 902 ,EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
        IF TIPOCREDOR  =  'Pessoal' THEN
           SEQUENCIA := FAZERLANCAMENTO(  CONTACONTABIL , '212120100010000' , 902 ,EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
        IF TIPOCREDOR  =  'INSS' THEN
           SEQUENCIA := FAZERLANCAMENTO(  CONTACONTABIL , '212130100010000' , 902 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
        IF TIPOCREDOR  =  'FGTS' THEN
           SEQUENCIA := FAZERLANCAMENTO(  CONTACONTABIL , '212130300010000' , 902 ,EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
        IF TIPOCREDOR  =  'PIS/PASEP' THEN
           SEQUENCIA := FAZERLANCAMENTO(  CONTACONTABIL , '212130400010000' , 902 ,EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
        IF TIPOCREDOR  =  'RPPS/Executivo' THEN
           SEQUENCIA := FAZERLANCAMENTO(  CONTACONTABIL , '212130800010100' , 902 ,EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
        IF TIPOCREDOR  =  'RPPS/Legislativo' THEN
           SEQUENCIA := FAZERLANCAMENTO(  CONTACONTABIL , '212130800010200' , 902 ,EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
   END IF; 

   RETURN SEQUENCIA;
   END;

$$ LANGUAGE 'plpgsql';
