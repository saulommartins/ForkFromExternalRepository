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

* $Revision: 23095 $
* $Name$
* $Autor: MArcia $
* Date: 2006/06/12 10:50:00 $
*
* Caso de uso:*
* Objetivo: 

*/

CREATE OR REPLACE FUNCTION replicaFuncoesExternasComBibliotecaOrigemDestino(integer,varchar,integer,varchar) RETURNS boolean as $$

DECLARE

    inBibliotecaOrigem            ALIAS FOR $1 ;
    stNomeOrigem                  ALIAS FOR $2 ;
    inBibliotecaDestino           ALIAS FOR $3 ;
    stNomeDestino                 ALIAS FOR $4 ;


    boRetorno                     BOOLEAN := TRUE;
    stSql                         VARCHAR := '';
    inCodFuncaoOrigem             INTEGER;
    inCodFuncaoDestino            INTEGER;
    reRegistro      RECORD;

stEntidade VARCHAR := recuperarBufferTexto('stEntidade');
 BEGIN



stSql := '
        SELECT cod_funcao
        FROM administracao.funcao
       WHERE cod_modulo = 27 
         AND cod_biblioteca = '||inBibliotecaOrigem||'
         AND nom_funcao = '''||stNomeOrigem||'''
      ';

FOR reRegistro IN EXECUTE stSql
LOOP
   inCodFuncaoOrigem :=  reRegistro.cod_funcao;
END LOOP;



IF inCodFuncaoOrigem is not null THEN


   SELECT INTO inCodFuncaoDestino (
           SELECT max(cod_funcao)+1
             FROM administracao.funcao
             WHERE cod_modulo = 27 
              AND cod_biblioteca = inBibliotecaDestino
           );


    stSql := '
    INSERT INTO administracao.funcao 
    (SELECT cod_modulo
          , '||inBibliotecaDestino||'
          , '||inCodFuncaoDestino||'
          ,cod_tipo_retorno
          , '''||stNomeDestino||''' 
    FROM administracao.funcao
   WHERE cod_modulo = 27 
     AND cod_biblioteca = '||inBibliotecaOrigem||'
     AND cod_funcao = '||inCodFuncaoOrigem||')
               ';
    EXECUTE stSql;



    stSql := '
    INSERT INTO administracao.variavel 
    (SELECT cod_modulo
           , '||inBibliotecaDestino||'
           ,'||inCodFuncaoDestino||'
           ,cod_variavel
           ,nom_variavel
           ,cod_tipo,valor_inicial 
       FROM administracao.variavel
      WHERE cod_modulo = 27 
     AND cod_biblioteca = '||inBibliotecaOrigem||'
        AND cod_funcao = '||inCodFuncaoOrigem||')
               ';
    EXECUTE stSql;




    stSql := '
    INSERT INTO administracao.parametro 
    (SELECT cod_modulo
           , '||inBibliotecaDestino||'
           , '||inCodFuncaoDestino||'
           ,cod_variavel
           ,ordem
      FROM administracao.parametro
     WHERE cod_modulo = 27
     AND cod_biblioteca = '||inBibliotecaOrigem||'
       AND cod_funcao = '||inCodFuncaoOrigem||')
               ';
    EXECUTE stSql;




    stSql := '
    INSERT INTO administracao.funcao_externa 
    (SELECT cod_modulo
           , '||inBibliotecaDestino||'
           ,'||inCodFuncaoDestino||'
           ,comentario
           ,corpo_pl
           ,corpo_ln 
     FROM administracao.funcao_externa
    WHERE cod_modulo = 27 
     AND cod_biblioteca = '||inBibliotecaOrigem||'
      AND cod_funcao = '||inCodFuncaoOrigem||')
               ';
    EXECUTE stSql;





    stSql := '
    INSERT INTO administracao.corpo_funcao_externa 
    (SELECT cod_modulo
           , '||inBibliotecaDestino||'
           ,'||inCodFuncaoDestino||'
           ,cod_linha
           ,nivel
           ,linha 
      FROM administracao.corpo_funcao_externa
     WHERE cod_modulo = 27
     AND cod_biblioteca = '||inBibliotecaOrigem||'
       AND cod_funcao = '||inCodFuncaoOrigem||')
               ';
    EXECUTE stSql;

    boRetorno := 't';



ELSE

    boRetorno := 'f';

END IF;

RETURN boRetorno;

END;
$$ LANGUAGE 'plpgsql';

