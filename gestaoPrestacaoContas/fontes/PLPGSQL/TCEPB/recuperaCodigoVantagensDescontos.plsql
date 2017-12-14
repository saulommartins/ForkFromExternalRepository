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
    * Arquivo de consulta do arquivo Codigo_VantagensDescontos
    * Data de Criação   : 29/07/2009
    
    
    * @author Analista      Tonismar Régis Bernardo
    * @author Desenvolvedor Eduardo Paculski Schitz
    
    * @package URBEM
    * @subpackage 
    
    $Id:$
*/

CREATE OR REPLACE FUNCTION tcepb.recuperaCodigoVantagensDescontos(VARCHAR, VARCHAR) RETURNS SETOF RECORD AS $$
DECLARE
    stEntidade                      ALIAS FOR $1;
    stMesAnoReferencia              ALIAS FOR $2;
    stSql                           VARCHAR;
    stSqlAux                        VARCHAR;
    reRegistro                      RECORD;
    inIdentificador                 INTEGER;
    inCodVantagensDescontos         INTEGER;
BEGIN

    --verifica se a sequence codigo_vantagens_descontos_tce existe
    IF ((SELECT 1 FROM pg_catalog.pg_statio_user_sequences WHERE relname='codigo_vantagens_descontos_tce') IS NOT NULL) THEN
        SELECT NEXTVAL('tcepb.codigo_vantagens_descontos_tce')
          INTO inIdentificador;
    ELSE
        CREATE SEQUENCE tcepb.codigo_vantagens_descontos_tce START 1;
        SELECT NEXTVAL('tcepb.codigo_vantagens_descontos_tce')
          INTO inIdentificador;
    END IF;

    stSql := '
    CREATE TEMPORARY TABLE tmp_retorno_'||inIdentificador||' (
          cod_vantagem_desconto   VARCHAR
        , nome_vantagem_desconto  VARCHAR
        , tipo_lancamento         INTEGER
        , tipo_contabilizacao     INTEGER
    ) ';

    EXECUTE stSql;

    stSql := '
      SELECT evento.codigo as cod_vantagem_desconto    
           , evento.descricao as nome_vantagem_desconto
           , ( CASE WHEN evento.natureza = ''P'' THEN 0
                    WHEN evento.natureza = ''D'' THEN 1
               END ) as tipo_lancamento              
           , ( CASE WHEN evento.natureza = ''P'' THEN 0
                    WHEN evento.natureza = ''D'' THEN 1
               END ) as tipo_contabilizacao          
        FROM folhapagamento'||stEntidade||'.evento
       WHERE evento.natureza in (''P'', ''D'')
         AND NOT EXISTS ( SELECT 1
                            FROM pessoal'||stEntidade||'.arquivo_codigo_vantagens_descontos
                           WHERE arquivo_codigo_vantagens_descontos.cod_vantagem_desconto = evento.codigo
                             AND arquivo_codigo_vantagens_descontos.periodo              <> '''||stMesAnoReferencia||''')
    ORDER BY evento.codigo 
    ';

    FOR reRegistro IN EXECUTE stSql LOOP
        inCodVantagensDescontos := selectIntoInteger('SELECT cod_vantagem_desconto
                                                        FROM pessoal'||stEntidade||'.arquivo_codigo_vantagens_descontos
                                                       WHERE arquivo_codigo_vantagens_descontos.cod_vantagem_desconto = '''||reRegistro.cod_vantagem_desconto||''' ');

        IF (inCodVantagensDescontos IS NULL) THEN
            stSqlAux := '
            INSERT INTO pessoal'||stEntidade||'.arquivo_codigo_vantagens_descontos ( cod_vantagem_desconto
                                                                                   , periodo 
                                                                          ) VALUES ( '''||reRegistro.cod_vantagem_desconto||'''
                                                                                   , '''||stMesAnoReferencia||''' ) ';

            EXECUTE stSqlAux;
        END IF;

        stSqlAux := ' 
        INSERT INTO tmp_retorno_'||inIdentificador||' ( cod_vantagem_desconto
                                                      , nome_vantagem_desconto
                                                      , tipo_lancamento       
                                                      , tipo_contabilizacao )
                                               VALUES ( '''||reRegistro.cod_vantagem_desconto||'''
                                                      , '''||reRegistro.nome_vantagem_desconto||'''
                                                      , '||reRegistro.tipo_lancamento||'
                                                      , '||reRegistro.tipo_contabilizacao||' )';

        EXECUTE stSqlAux;
    END LOOP;

    stSql := ' SELECT * 
                 FROM tmp_retorno_'||inIdentificador||' 
             ORDER BY cod_vantagem_desconto ';

    FOR reRegistro IN EXECUTE stSql LOOP
        RETURN NEXT reRegistro;
    END LOOP;

    EXECUTE 'DROP TABLE tmp_retorno_'||inIdentificador;
    
END;
$$ LANGUAGE 'plpgsql';
