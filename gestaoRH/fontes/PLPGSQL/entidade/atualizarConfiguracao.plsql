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
CREATE OR REPLACE FUNCTION atualizarConfiguracao(INTEGER,VARCHAR,VARCHAR) RETURNS VOID AS $$
DECLARE
    inCodModulo                 ALIAS FOR $1;
    stParametro                 ALIAS FOR $2;
    stValorParametro            ALIAS FOR $3;
    inExercicio                 INTEGER;
    stSql                       VARCHAR;
    stInsert                    VARCHAR;
    reRegistro                  RECORD;
BEGIN
    inExercicio := selectIntoInteger('SELECT valor FROM administracao.configuracao WHERE parametro = ''ano_exercicio'' ORDER BY exercicio desc LIMIT 1');
    stInsert := 'INSERT INTO administracao.configuracao (exercicio,cod_modulo,parametro,valor) 
                       VALUES ('|| quote_literal(inExercicio) ||','|| inCodModulo ||','|| quote_literal(stParametro) ||','|| quote_literal(stValorParametro) ||');';
    EXECUTE stInsert;                       
    stSql := 'SELECT entidade.cod_entidade
                  FROM orcamento.entidade
                 WHERE exercicio = '|| quote_literal(inExercicio) ||'
                   AND cod_entidade IN (SELECT cod_entidade
                                          FROM administracao.entidade_rh
                                         WHERE exercicio = '|| quote_literal(inExercicio) ||'
                                        GROUP BY cod_entidade)
                   AND cod_entidade <> (SELECT valor
                                          FROM administracao.configuracao
                                         WHERE parametro = ''cod_entidade_prefeitura''
                                           AND exercicio = '|| quote_literal(inExercicio) ||')::integer';
                                               
    FOR reRegistro IN EXECUTE stSql LOOP
        stInsert := 'INSERT INTO administracao.configuracao (exercicio,cod_modulo,parametro,valor) 
                           VALUES ('|| quote_literal(inExercicio) ||','|| inCodModulo ||','|| quote_literal(stParametro||'_'||reRegistro.cod_entidade) ||','|| quote_literal(stValorParametro) ||');';        
        EXECUTE stInsert;                       
        stInsert := 'INSERT INTO administracao.configuracao_entidade (exercicio,cod_modulo,parametro,valor,cod_entidade) 
                           VALUES ('|| quote_literal(inExercicio) ||','|| inCodModulo ||','|| quote_literal(stParametro||'_'||reRegistro.cod_entidade) ||','|| quote_literal(stValorParametro) ||','||reRegistro.cod_entidade||');';        
        EXECUTE stInsert;                               
    END LOOP;                                               
END
$$ LANGUAGE 'plpgsql';
