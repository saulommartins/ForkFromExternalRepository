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
    * script de funcao PLSQL
    * 
    * URBEM Soluções de Gestão Pública Ltda
    * www.urbem.cnm.org.br
    
    * $Revision: 28532 $
    * $Author: anasilvia $
    * Date: 2006/06/12 10:50:00 $
    *
    
    * Caso de uso: uc-01.03.95
    * Objetivo: Copiar uma função já existente 
    
    $Id: replicaFuncoesExternas.plsql 59612 2014-09-02 12:00:51Z gelson $

*/

CREATE OR REPLACE FUNCTION replicaFuncoesExternas(integer,integer,integer,integer,integer,varchar) RETURNS boolean AS $$

DECLARE

    inCodModuloOrigem       ALIAS FOR $1 ;
    inCodBibliotecaOrigem   ALIAS FOR $2 ;
    inCodFuncaoOrigem       ALIAS FOR $3 ;

    inCodModuloDestino      ALIAS FOR $4 ;
    inCodBibliotecaDestino  ALIAS FOR $5 ;
    stNomeDestino           ALIAS FOR $6;
    stNomeDestinoFuncao     VARCHAR := '';

    boRetorno               BOOLEAN := TRUE;
    stSql                   VARCHAR := '';
    nomFuncao               VARCHAR := '';
    inCodFuncaoDestino      INTEGER;
    reRegistro              record;
    sqlFuncao               VARCHAR :=''; 

    existeFuncao            VARCHAR := '';
    existeCorpoPl           VARCHAR := '';

BEGIN

    SELECT INTO existeFuncao (
                                SELECT  funcao.nom_funcao as nom_funcao
                                  FROM  administracao.funcao
                            INNER JOIN  administracao.funcao_externa
                                    ON  funcao_externa.cod_funcao     = funcao.cod_funcao
                                   AND  funcao_externa.cod_modulo     = funcao.cod_modulo
                                   AND  funcao_externa.cod_biblioteca = funcao.cod_biblioteca
                                 WHERE  funcao.cod_funcao             = inCodFuncaoOrigem
                                   AND  funcao.cod_modulo             = inCodModuloOrigem
                                   AND  funcao.cod_biblioteca         = inCodBibliotecaOrigem
                              );

    SELECT INTO existeCorpoPl (
                                SELECT  UPPER(REPLACE(REPLACE(funcao_externa.corpo_pl,'\\''', ''''), '''','\\''')) as corpo_pl
                                  FROM  administracao.funcao
                            INNER JOIN  administracao.funcao_externa
                                    ON  funcao_externa.cod_funcao     = funcao.cod_funcao
                                   AND  funcao_externa.cod_modulo     = funcao.cod_modulo
                                   AND  funcao_externa.cod_biblioteca = funcao.cod_biblioteca
                                 WHERE  funcao.cod_funcao             = inCodFuncaoOrigem
                                   AND  funcao.cod_modulo             = inCodModuloOrigem
                                   AND  funcao.cod_biblioteca         = inCodBibliotecaOrigem
                              );

    IF ((TRIM(existeFuncao) = '' OR  existeFuncao IS NULL) OR (TRIM(existeCorpoPl) = '' OR existeCorpoPl IS NULL)) THEN
        boRetorno := false;
    ELSE
        SELECT INTO inCodFuncaoDestino
                    (
                        SELECT  COALESCE(MAX(cod_funcao),0)+1
                          FROM  administracao.funcao
                         WHERE  cod_modulo = inCodModuloDestino 
                           AND  cod_biblioteca = inCodBibliotecaDestino
                    );

        stSql := 'INSERT INTO administracao.funcao 
                      (SELECT '||inCodModuloDestino||'
                              , '||inCodBibliotecaDestino||'
                              , '||inCodFuncaoDestino||'
                              ,cod_tipo_retorno
                              , '''||stNomeDestino||'''
                        FROM  administracao.funcao
                       WHERE  cod_modulo = '||inCodModuloOrigem||' 
                         AND  cod_biblioteca = '||inCodBibliotecaOrigem||'
                         AND  cod_funcao = '||inCodFuncaoOrigem||')';
        EXECUTE stSql;
    
        stSql :='INSERT INTO administracao.variavel 
                      (SELECT  '||inCodModuloDestino||'
                              , '||inCodBibliotecaDestino||'
                              ,'||inCodFuncaoDestino||'
                              ,cod_variavel
                              ,nom_variavel
                              ,cod_tipo,valor_inicial 
                         FROM  administracao.variavel
                        WHERE  cod_modulo = '||inCodModuloOrigem||'
                          AND  cod_biblioteca = '||inCodBibliotecaOrigem||'
                          AND  cod_funcao = '||inCodFuncaoOrigem||')';
        EXECUTE stSql;
    
        stSql :='INSERT INTO administracao.parametro 
                      (SELECT '||inCodModuloDestino||'
                             , '||inCodBibliotecaDestino||'
                             ,'||inCodFuncaoDestino||'
                             ,cod_variavel
                             ,ordem
                        FROM administracao.parametro
                       WHERE cod_modulo = '||inCodModuloOrigem||'
                         AND cod_biblioteca = '||inCodBibliotecaOrigem||'
                         AND cod_funcao = '||inCodFuncaoOrigem||')';
        EXECUTE stSql;

        existeFuncao        := UPPER(existeFuncao)||'(';
        stNomeDestinoFuncao := UPPER(stNomeDestino)||'(';
    
        existeCorpoPl       := REPLACE(existeCorpoPl,existeFuncao,stNomeDestinoFuncao);
        
        stSql :='INSERT INTO administracao.funcao_externa 
                      (SELECT '||inCodModuloDestino||'
                              , '||inCodBibliotecaDestino||'
                              ,'||inCodFuncaoDestino||'
                              ,comentario
                              ,'||quote_literal(existeCorpoPl)||'
                              ,corpo_ln 
                        FROM  administracao.funcao_externa
                       WHERE  cod_modulo = '||inCodModuloOrigem||' 
                         AND  cod_biblioteca = '||inCodBibliotecaOrigem||'
                         AND  cod_funcao = '||inCodFuncaoOrigem||')';

        EXECUTE stSql;
    
        stSql :=' INSERT INTO administracao.corpo_funcao_externa 
                       (SELECT '||inCodModuloDestino||'
                               , '||inCodBibliotecaDestino||'
                               ,'||inCodFuncaoDestino||'
                               ,cod_linha
                               ,nivel
                               ,linha 
                         FROM  administracao.corpo_funcao_externa
                        WHERE  cod_modulo = '||inCodModuloOrigem||'
                          AND  cod_biblioteca = '||inCodBibliotecaOrigem||'
                          AND  cod_funcao = '||inCodFuncaoOrigem||')';
        EXECUTE stSql;
    
        SELECT INTO nomFuncao  ( SELECT funcao.nom_funcao as nom_funcao
                                   FROM  administracao.funcao
                                        ,administracao.funcao_externa
                                  WHERE funcao.cod_funcao       = funcao_externa.cod_funcao
                                    AND funcao.cod_modulo       = funcao_externa.cod_modulo
                                    AND funcao.cod_biblioteca   = funcao_externa.cod_biblioteca
                                    AND funcao.cod_funcao       = inCodFuncaoOrigem
                                    AND funcao.cod_modulo       = inCodModuloOrigem
                                    AND funcao.cod_biblioteca   = inCodBibliotecaOrigem);
    
        SELECT INTO sqlFuncao  ( SELECT ' CREATE  OR REPLACE ' ||REPLACE(funcao_externa.corpo_pl, '\', '') as comando
                                   FROM  administracao.funcao
                                        ,administracao.funcao_externa
                                  WHERE funcao.cod_funcao       = funcao_externa.cod_funcao
                                    AND funcao.cod_modulo       = funcao_externa.cod_modulo
                                    AND funcao.cod_biblioteca   = funcao_externa.cod_biblioteca
                                    AND funcao.cod_funcao       = inCodFuncaoDestino
                                    AND funcao.cod_modulo       = inCodModuloDestino
                                    AND funcao.cod_biblioteca   = inCodBibliotecaDestino);
       
        nomFuncao           := UPPER(nomFuncao)||'(';
        stNomeDestinoFuncao := UPPER(stNomeDestino)||'(';
    
        sqlFuncao           := REPLACE(sqlFuncao,nomFuncao,stNomeDestinoFuncao);

        EXECUTE sqlFuncao;
    END IF;
   
    RETURN  boRetorno;
END;
$$ LANGUAGE 'plpgsql';
