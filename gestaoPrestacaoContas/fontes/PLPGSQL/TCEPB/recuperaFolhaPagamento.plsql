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
CREATE OR REPLACE FUNCTION tcepb.recuperaFolhaPagamento(VARCHAR, VARCHAR) RETURNS SETOF RECORD AS $$
DECLARE
    stEntidade                      ALIAS FOR $1;
    stMesAnoReferencia              ALIAS FOR $2;
    stSql                           VARCHAR;
    stSqlAux                        VARCHAR;
    stSqlOcorrenciasComplementares  VARCHAR;
    reRegistro                      RECORD;
    reRegistroRetorno               RECORD;
    reRegistroAnterior              RECORD;
    inCodContratoAnterior           INTEGER;
    inIdentificador                 INTEGER;
    inCodPeriodoMovimentacao        INTEGER;
    inAcumulacao                    INTEGER;
    inCountContratosServidor        INTEGER;
    boPrimeiroRegistro              BOOLEAN := TRUE;
BEGIN

    --verifica se a sequence folha_pagamento_tce existe
    IF ((SELECT 1 FROM pg_catalog.pg_statio_user_sequences WHERE relname='folha_pagamento_tce') IS NOT NULL) THEN
        SELECT NEXTVAL('tcepb.folha_pagamento_tce')
          INTO inIdentificador;
    ELSE
        CREATE SEQUENCE tcepb.folha_pagamento_tce START 1;
        SELECT NEXTVAL('tcepb.folha_pagamento_tce')
          INTO inIdentificador;
    END IF;

    stSql := '
    CREATE TEMPORARY TABLE tmp_retorno_'|| inIdentificador ||' (
          cpf                     VARCHAR
        , cod_cargo               INTEGER
        , matricula               INTEGER
        , acumulacao              INTEGER
        , mes_ano_referencia      VARCHAR
        , cod_operacao            INTEGER
        , cod_vantagem_desconto   VARCHAR
        , tipo_folha              INTEGER
        , valor                   NUMERIC(14,2)
    ) ';

    EXECUTE stSql;

    inCodPeriodoMovimentacao := selectIntoInteger('SELECT cod_periodo_movimentacao
                                                     FROM folhapagamento'|| stEntidade ||'.periodo_movimentacao
                                                    WHERE to_char(dt_final, ''mmyyyy'') = '|| quote_literal(stMesAnoReferencia) ||' ');

    stSql := '';

    stSqlOcorrenciasComplementares := '  SELECT cod_complementar
                                           FROM folhapagamento'|| stEntidade ||'.complementar
                                          WHERE cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                       GROUP BY cod_complementar';
                                             
    FOR reRegistro IN EXECUTE stSqlOcorrenciasComplementares LOOP
        stSql := stSql ||'SELECT cod_contrato
                              , cod_evento
                              , codigo
                              , sequencia
                              , valor
                              , 2 as tipo_folha
                              , CASE WHEN natureza = ''D'' THEN
                                    1
                                ELSE
                                    0
                                END as cod_operacao
                           FROM recuperarEventosCalculados(0, '|| inCodPeriodoMovimentacao ||', 0, '|| reRegistro.cod_complementar ||', '|| quote_literal(stEntidade) ||', '''')
                          WHERE natureza IN (''P'', ''D'')
                          
                          UNION ALL ';
    END LOOP;
        
    stSql := stSql ||'SELECT cod_contrato
                          , cod_evento
                          , codigo
                          , sequencia
                          , valor
                          , 0 as tipo_folha 
                          , CASE WHEN natureza = ''D'' THEN
                                1
                            ELSE
                                0 
                            END as cod_operacao
                       FROM recuperarEventosCalculados(1, '|| inCodPeriodoMovimentacao ||', 0, 0, '|| quote_literal(stEntidade) ||', '''')
                      WHERE natureza IN (''P'', ''D'')
                      
                      UNION ALL ';
                           
    stSql := stSql ||'SELECT cod_contrato
                          , cod_evento
                          , codigo
                          , sequencia
                          , valor
                          , 2 as tipo_folha 
                          , CASE WHEN natureza = ''D'' THEN
                                1
                            ELSE
                                0 
                            END as cod_operacao
                       FROM recuperarEventosCalculados(2, '|| inCodPeriodoMovimentacao ||', 0, 0, '|| quote_literal(stEntidade) ||', '''')
                      WHERE natureza IN (''P'', ''D'')
                      
                      UNION ALL ';
                           
   
    stSql := stSql ||'SELECT cod_contrato
                          , cod_evento
                          , codigo
                          , sequencia
                          , valor
                          , 1 as tipo_folha
                          , CASE WHEN natureza = ''D'' THEN
                                1
                            ELSE
                                0 
                            END as cod_operacao
                       FROM recuperarEventosCalculados(3, '|| inCodPeriodoMovimentacao ||', 0, 0, '|| quote_literal(stEntidade) ||', '''')
                      WHERE natureza IN (''P'', ''D'')
                      
                      UNION ALL ';
                           
    stSql := stSql ||'SELECT cod_contrato
                          , cod_evento
                          , codigo
                          , sequencia
                          , valor
                          , 2 as tipo_folha
                          , CASE WHEN natureza = ''D'' THEN
                                1
                            ELSE
                                0 
                            END as cod_operacao
                       FROM recuperarEventosCalculados(4, '|| inCodPeriodoMovimentacao ||', 0, 0, '|| quote_literal(stEntidade) ||', '''')
                      WHERE natureza IN (''P'', ''D'')';
                      
    stSql := '  SELECT contratos.cod_contrato 
                     , contratos.cod_servidor
                     , contratos.cpf
                     , contratos.cod_cargo
                     , contratos.matricula
                     , folhas.cod_operacao
                     , folhas.cod_evento
                     , folhas.codigo
                     , folhas.sequencia
                     , folhas.tipo_folha
                     , sum(folhas.valor) as valor
                  FROM (     SELECT sw_cgm_pessoa_fisica.cpf
                                  , contrato.cod_contrato
                                  , contrato.registro AS matricula
                                  , CASE WHEN tcepb.fn_retorna_assentamento(contrato.cod_contrato, '|| quote_literal(stMesAnoReferencia) ||', '''') = 7 then
                                           9999
                                         ELSE
                                           cargo.cod_cargo
                                    END AS cod_cargo
                                  , servidor.cod_servidor
                               FROM pessoal.servidor
                         INNER JOIN sw_cgm
                                 ON sw_cgm.numcgm = servidor.numcgm
                         INNER JOIN sw_cgm_pessoa_fisica
                                 ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
                         INNER JOIN pessoal.servidor_contrato_servidor
                                 ON servidor.cod_servidor = servidor_contrato_servidor.cod_servidor 
                         INNER JOIN pessoal.contrato_servidor
                                 ON servidor_contrato_servidor.cod_contrato = contrato_servidor.cod_contrato 
                         INNER JOIN pessoal.contrato
                                 ON contrato_servidor.cod_contrato = contrato.cod_contrato 
--                         INNER JOIN ultimo_contrato_servidor_funcao('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||')
--                                 AS contrato_servidor_funcao
--                                 ON contrato_servidor_funcao.cod_contrato = contrato_servidor.cod_contrato
                         INNER JOIN pessoal.cargo
                                 ON cargo.cod_cargo = contrato_servidor.cod_cargo
                                 
                              UNION          
                        
                             SELECT sw_cgm_pessoa_fisica.cpf
                                  , contrato.cod_contrato
                                  , contrato.registro AS matricula
                                  , cargo.cod_cargo
                                  , 0 as cod_servidor
                               FROM pessoal.pensionista
                         INNER JOIN sw_cgm
                                 ON pensionista.numcgm = sw_cgm.numcgm
                         INNER JOIN sw_cgm_pessoa_fisica
                                 ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
                         INNER JOIN pessoal.contrato_pensionista
                                 ON contrato_pensionista.cod_pensionista      = pensionista.cod_pensionista
                                AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente
                         INNER JOIN pessoal.contrato                                                                                             
                                 ON contrato_pensionista.cod_contrato = contrato.cod_contrato
--                         INNER JOIN ultimo_contrato_servidor_funcao('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||')
--                                 AS contrato_servidor_funcao
--                                 ON contrato_servidor_funcao.cod_contrato = contrato_pensionista.cod_contrato_cedente
                         INNER JOIN pessoal.contrato_servidor
                                 ON contrato_servidor.cod_contrato = contrato_pensionista.cod_contrato_cedente
                         INNER JOIN pessoal.cargo
                                 ON cargo.cod_cargo = contrato_servidor.cod_cargo
                       ) AS contratos
            INNER JOIN (
                         '|| stSql ||'
                       ) as folhas
                    ON contratos.cod_contrato = folhas.cod_contrato
              GROUP BY contratos.cod_contrato 
                     , contratos.cod_servidor
                     , contratos.cpf
                     , contratos.cod_cargo
                     , contratos.matricula
                     , folhas.cod_operacao
                     , folhas.cod_evento
                     , folhas.codigo
                     , folhas.sequencia
                     , folhas.tipo_folha
              ORDER BY contratos.cpf
                     , contratos.cod_contrato
                     , contratos.cod_cargo
                     , folhas.tipo_folha
                     , folhas.cod_operacao
                     , folhas.sequencia';
                     
    FOR reRegistro IN EXECUTE stSql LOOP
        stSqlAux := ' 
        INSERT INTO tmp_retorno_'|| inIdentificador ||' ( cpf
                                                      , cod_cargo               
                                                      , matricula               
                                                      , acumulacao              
                                                      , mes_ano_referencia      
                                                      , cod_operacao            
                                                      , cod_vantagem_desconto   
                                                      , tipo_folha              
                                                      , valor )
                                               VALUES ( '|| reRegistro.cpf ||'
                                                      , '|| reRegistro.cod_cargo ||'
                                                      , '|| reRegistro.matricula ||'
                                                      , '|| tcepb.recuperaAcumulacaoContratoServidor(stEntidade, inCodPeriodoMovimentacao, reRegistro.cod_servidor, reRegistro.cod_contrato) ||'
                                                      , '|| stMesAnoReferencia ||'
                                                      , '|| reRegistro.cod_operacao ||'
                                                      , '|| reRegistro.codigo ||'
                                                      , '|| reRegistro.tipo_folha ||'
                                                      , '|| reRegistro.valor ||' )';

        EXECUTE stSqlAux;
    END LOOP;

    stSql := ' SELECT * FROM tmp_retorno_'|| inIdentificador;

    FOR reRegistro IN EXECUTE stSql LOOP
        RETURN NEXT reRegistro;
    END LOOP;

    EXECUTE 'DROP TABLE tmp_retorno_'|| inIdentificador;
    
END;
$$ LANGUAGE 'plpgsql';
