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
* Script de DDL e DML
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: GF_1932.sql 59612 2014-09-02 12:00:51Z gelson $
*
* Versão 1.94.0
*/

----------------
-- Ticket #13851
----------------

INSERT INTO administracao.relatorio
         ( cod_gestao
         , cod_modulo
         , cod_relatorio
         , nom_relatorio
         , arquivo )
    VALUES ( 2
         , 9
         , 3
         , 'Razão'
         , 'razao.rptdesign'
         );



----------------
-- Ticket #13903
----------------

DELETE FROM administracao.parametro            WHERE cod_modulo = 10 AND cod_biblioteca = 1 AND cod_funcao = (SELECT cod_funcao FROM administracao.funcao WHERE nom_funcao = 'OrcamentoSuplementacoesCreditoEspecial');
DELETE FROM administracao.variavel             WHERE cod_modulo = 10 AND cod_biblioteca = 1 AND cod_funcao = (SELECT cod_funcao FROM administracao.funcao WHERE nom_funcao = 'OrcamentoSuplementacoesCreditoEspecial');
DELETE FROM administracao.corpo_funcao_externa WHERE cod_modulo = 10 AND cod_biblioteca = 1 AND cod_funcao = (SELECT cod_funcao FROM administracao.funcao WHERE nom_funcao = 'OrcamentoSuplementacoesCreditoEspecial');
DELETE FROM administracao.funcao_externa       WHERE cod_modulo = 10 AND cod_biblioteca = 1 AND cod_funcao = (SELECT cod_funcao FROM administracao.funcao WHERE nom_funcao = 'OrcamentoSuplementacoesCreditoEspecial');
----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN

    PERFORM 1
       FROM administracao.funcao
      WHERE nom_funcao = 'OrcamentoSuplementacoesCreditoEspecial';

    IF NOT FOUND THEN
        INSERT INTO administracao.funcao
                  ( cod_modulo
                  , cod_biblioteca
                  , cod_funcao
                  , cod_tipo_retorno
                  , nom_funcao)
             VALUES ( 10
                  , 1
                  , (SELECT MAX (cod_funcao) + 1 FROM administracao.funcao WHERE cod_modulo = 10 AND cod_biblioteca = 1)
                  , 1
                  , 'OrcamentoSuplementacoesCreditoEspecial');
    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT manutencao();
DROP FUNCTION manutencao();

INSERT INTO administracao.funcao_externa
                  (cod_modulo
                  , cod_biblioteca
                  , cod_funcao
                  , comentario
                  , corpo_pl
                  , corpo_ln)
            VALUES (10,1,(SELECT cod_funcao FROM administracao.funcao WHERE nom_funcao = 'OrcamentoSuplementacoesCreditoEspecial'),'Realiza lançamento de suplementação para crédito especial','FUNCTION OrcamentoSuplementacoesCreditoEspecial(VARCHAR,NUMERIC,VARCHAR,INTEGER,VARCHAR,INTEGER,VARCHAR) RETURNS INTEGER as ''
   DECLARE
   EXERCICIO ALIAS FOR $1;
   VALOR ALIAS FOR $2;
   COMPLEMENTO ALIAS FOR $3;
   CODLOTE ALIAS FOR $4;
   TIPOLOTE ALIAS FOR $5;
   CODENTIDADE ALIAS FOR $6;
   CREDSUPLEMENTAR ALIAS FOR $7;

   SEQUENCIA INTEGER;
   TIPOCREDSUPLEMENTAR VARCHAR := '''''''';
   BEGIN
   TIPOCREDSUPLEMENTAR := '''''''' || CREDSUPLEMENTAR || '''''''';
   IF     TIPOCREDSUPLEMENTAR  =   ''''Reducao'''' THEN
      SEQUENCIA := FAZERLANCAMENTO(  ''''192130100020000'''' , ''''292110000000000'''' , 909 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
      SEQUENCIA := FAZERLANCAMENTO(  ''''292110000000000'''' , ''''192190209000000'''' , 909 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
   END IF;
   IF   TIPOCREDSUPLEMENTAR  =  ''''Excesso'''' THEN
      SEQUENCIA := FAZERLANCAMENTO(  ''''192130100030000'''' , ''''292110000000000'''' , 909 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
      SEQUENCIA := FAZERLANCAMENTO(  ''''191110000000000'''' , ''''291120000000000'''' , 909 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
   END IF;
   IF     TIPOCREDSUPLEMENTAR  =   ''''Operacao de Credito'''' THEN
      SEQUENCIA := FAZERLANCAMENTO(  ''''192130100040000'''' , ''''292110000000000'''' , 909 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
   END IF;
   IF     TIPOCREDSUPLEMENTAR  =   ''''Superavit'''' THEN
      SEQUENCIA := FAZERLANCAMENTO(  ''''192130101010000'''' , ''''292110000000000'''' , 909 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
   END IF;
   IF   TIPOCREDSUPLEMENTAR  =  ''''Doacoes'''' THEN
      SEQUENCIA := FAZERLANCAMENTO(  ''''192130100060000'''' , ''''292110000000000'''' , 909 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
   END IF;
   IF   TIPOCREDSUPLEMENTAR  =  ''''Auxilios'''' THEN
      SEQUENCIA := FAZERLANCAMENTO(  ''''192130100050000'''' , ''''292110000000000'''' , 909 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
   END IF;
   IF   TIPOCREDSUPLEMENTAR  =  ''''Especial Reaberto'''' THEN
      SEQUENCIA := FAZERLANCAMENTO(  ''''192130200000000'''' , ''''292110000000000'''' , 909 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
   END IF;
   RETURN SEQUENCIA;
   END;
   '' LANGUAGE ''plpgsql'';
   ','');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,(SELECT cod_funcao FROM administracao.funcao WHERE nom_funcao = 'OrcamentoSuplementacoesCreditoEspecial'),1,'0','#TipoCredSuplementar <- "#CredSuplementar";');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,(SELECT cod_funcao FROM administracao.funcao WHERE nom_funcao = 'OrcamentoSuplementacoesCreditoEspecial'),2,'1','SE     #TipoCredSuplementar  =   "Reducao" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,(SELECT cod_funcao FROM administracao.funcao WHERE nom_funcao = 'OrcamentoSuplementacoesCreditoEspecial'),3,'1','#Sequencia <- FazerLancamento(  "192130100020000" , "292110000000000" , 909 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,(SELECT cod_funcao FROM administracao.funcao WHERE nom_funcao = 'OrcamentoSuplementacoesCreditoEspecial'),4,'1','#Sequencia <- FazerLancamento(  "292110000000000" , "192190209000000" , 909 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,(SELECT cod_funcao FROM administracao.funcao WHERE nom_funcao = 'OrcamentoSuplementacoesCreditoEspecial'),5,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,(SELECT cod_funcao FROM administracao.funcao WHERE nom_funcao = 'OrcamentoSuplementacoesCreditoEspecial'),6,'1','SE   #TipoCredSuplementar  =  "Excesso" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,(SELECT cod_funcao FROM administracao.funcao WHERE nom_funcao = 'OrcamentoSuplementacoesCreditoEspecial'),7,'1','#Sequencia <- FazerLancamento(  "192130100030000" , "292110000000000" , 909 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,(SELECT cod_funcao FROM administracao.funcao WHERE nom_funcao = 'OrcamentoSuplementacoesCreditoEspecial'),8,'1','#Sequencia <- FazerLancamento(  "191110000000000" , "291120000000000" , 909 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,(SELECT cod_funcao FROM administracao.funcao WHERE nom_funcao = 'OrcamentoSuplementacoesCreditoEspecial'),9,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,(SELECT cod_funcao FROM administracao.funcao WHERE nom_funcao = 'OrcamentoSuplementacoesCreditoEspecial'),10,'1','SE     #TipoCredSuplementar  =   "Operacao de Credito" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,(SELECT cod_funcao FROM administracao.funcao WHERE nom_funcao = 'OrcamentoSuplementacoesCreditoEspecial'),11,'1','#Sequencia <- FazerLancamento(  "192130100040000" , "292110000000000" , 909 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,(SELECT cod_funcao FROM administracao.funcao WHERE nom_funcao = 'OrcamentoSuplementacoesCreditoEspecial'),12,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,(SELECT cod_funcao FROM administracao.funcao WHERE nom_funcao = 'OrcamentoSuplementacoesCreditoEspecial'),13,'1','SE     #TipoCredSuplementar  =   "Superavit" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
--               VALUES (10,1,(SELECT cod_funcao FROM administracao.funcao WHERE nom_funcao = 'OrcamentoSuplementacoesCreditoEspecial'),14,'1','#Sequencia <- FazerLancamento(  "192130100010000" , "292110000000000" , 909 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
               VALUES (10,1,(SELECT cod_funcao FROM administracao.funcao WHERE nom_funcao = 'OrcamentoSuplementacoesCreditoEspecial'),14,'1','#Sequencia <- FazerLancamento(  "192130101010000" , "292110000000000" , 909 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,(SELECT cod_funcao FROM administracao.funcao WHERE nom_funcao = 'OrcamentoSuplementacoesCreditoEspecial'),15,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,(SELECT cod_funcao FROM administracao.funcao WHERE nom_funcao = 'OrcamentoSuplementacoesCreditoEspecial'),16,'1','SE   #TipoCredSuplementar  =  "Doacoes" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,(SELECT cod_funcao FROM administracao.funcao WHERE nom_funcao = 'OrcamentoSuplementacoesCreditoEspecial'),17,'1','#Sequencia <- FazerLancamento(  "192130100060000" , "292110000000000" , 909 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,(SELECT cod_funcao FROM administracao.funcao WHERE nom_funcao = 'OrcamentoSuplementacoesCreditoEspecial'),18,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,(SELECT cod_funcao FROM administracao.funcao WHERE nom_funcao = 'OrcamentoSuplementacoesCreditoEspecial'),19,'1','SE   #TipoCredSuplementar  =  "Auxilios" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,(SELECT cod_funcao FROM administracao.funcao WHERE nom_funcao = 'OrcamentoSuplementacoesCreditoEspecial'),20,'1','#Sequencia <- FazerLancamento(  "192130100050000" , "292110000000000" , 909 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,(SELECT cod_funcao FROM administracao.funcao WHERE nom_funcao = 'OrcamentoSuplementacoesCreditoEspecial'),21,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,(SELECT cod_funcao FROM administracao.funcao WHERE nom_funcao = 'OrcamentoSuplementacoesCreditoEspecial'),22,'1','SE   #TipoCredSuplementar  =  "Especial Reaberto" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,(SELECT cod_funcao FROM administracao.funcao WHERE nom_funcao = 'OrcamentoSuplementacoesCreditoEspecial'),23,'1','#Sequencia <- FazerLancamento(  "192130200000000" , "292110000000000" , 909 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,(SELECT cod_funcao FROM administracao.funcao WHERE nom_funcao = 'OrcamentoSuplementacoesCreditoEspecial'),24,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,(SELECT cod_funcao FROM administracao.funcao WHERE nom_funcao = 'OrcamentoSuplementacoesCreditoEspecial'),25,'0','RETORNA #Sequencia');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,(SELECT cod_funcao FROM administracao.funcao WHERE nom_funcao = 'OrcamentoSuplementacoesCreditoEspecial'),1,1,'Sequencia','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,(SELECT cod_funcao FROM administracao.funcao WHERE nom_funcao = 'OrcamentoSuplementacoesCreditoEspecial'),2,2,'TipoCredSuplementar','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,(SELECT cod_funcao FROM administracao.funcao WHERE nom_funcao = 'OrcamentoSuplementacoesCreditoEspecial'),3,2,'Exercicio','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,(SELECT cod_funcao FROM administracao.funcao WHERE nom_funcao = 'OrcamentoSuplementacoesCreditoEspecial'),4,4,'Valor','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,(SELECT cod_funcao FROM administracao.funcao WHERE nom_funcao = 'OrcamentoSuplementacoesCreditoEspecial'),5,2,'Complemento','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,(SELECT cod_funcao FROM administracao.funcao WHERE nom_funcao = 'OrcamentoSuplementacoesCreditoEspecial'),6,1,'CodLote','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,(SELECT cod_funcao FROM administracao.funcao WHERE nom_funcao = 'OrcamentoSuplementacoesCreditoEspecial'),7,2,'TipoLote','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,(SELECT cod_funcao FROM administracao.funcao WHERE nom_funcao = 'OrcamentoSuplementacoesCreditoEspecial'),8,1,'CodEntidade','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,(SELECT cod_funcao FROM administracao.funcao WHERE nom_funcao = 'OrcamentoSuplementacoesCreditoEspecial'),9,2,'CredSuplementar','');
INSERT INTO administracao.parametro
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , ordem)
               VALUES (10,1,(SELECT cod_funcao FROM administracao.funcao WHERE nom_funcao = 'OrcamentoSuplementacoesCreditoEspecial'),3,0);
INSERT INTO administracao.parametro
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , ordem)
               VALUES (10,1,(SELECT cod_funcao FROM administracao.funcao WHERE nom_funcao = 'OrcamentoSuplementacoesCreditoEspecial'),4,1);
INSERT INTO administracao.parametro
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , ordem)
               VALUES (10,1,(SELECT cod_funcao FROM administracao.funcao WHERE nom_funcao = 'OrcamentoSuplementacoesCreditoEspecial'),5,2);
INSERT INTO administracao.parametro
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , ordem)
               VALUES (10,1,(SELECT cod_funcao FROM administracao.funcao WHERE nom_funcao = 'OrcamentoSuplementacoesCreditoEspecial'),6,3);
INSERT INTO administracao.parametro
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , ordem)
               VALUES (10,1,(SELECT cod_funcao FROM administracao.funcao WHERE nom_funcao = 'OrcamentoSuplementacoesCreditoEspecial'),7,4);
INSERT INTO administracao.parametro
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , ordem)
               VALUES (10,1,(SELECT cod_funcao FROM administracao.funcao WHERE nom_funcao = 'OrcamentoSuplementacoesCreditoEspecial'),8,5);
INSERT INTO administracao.parametro
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , ordem)
               VALUES (10,1,(SELECT cod_funcao FROM administracao.funcao WHERE nom_funcao = 'OrcamentoSuplementacoesCreditoEspecial'),9,6);




CREATE OR REPLACE FUNCTION OrcamentoSuplementacoesCreditoEspecial( VARCHAR
                                                                 , NUMERIC
                                                                 , VARCHAR
                                                                 , INTEGER
                                                                 , VARCHAR
                                                                 , INTEGER
                                                                 , VARCHAR
                                                                 ) RETURNS INTEGER AS $$
DECLARE
    Exercicio           ALIAS FOR $1;
    Valor               ALIAS FOR $2;
    Complemento         ALIAS FOR $3;
    CodLote             ALIAS FOR $4;
    TipoLote            ALIAS FOR $5;
    CodEntidade         ALIAS FOR $6;
    CredSuplementar     ALIAS FOR $7;

    Sequencia           INTEGER;
    TipoCredSuplementar VARCHAR := '';
BEGIN

    TipoCredSuplementar := CredSuplementar;
    IF     TipoCredSuplementar  =   'Reducao' THEN
        Sequencia := FazerLancamento(  '192130100020000' , '292110000000000' , 909 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        Sequencia := FazerLancamento(  '292110000000000' , '192190209000000' , 909 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
    END IF;
    IF   TipoCredSuplementar  =  'Excesso' THEN
        Sequencia := FazerLancamento(  '192130100030000' , '292110000000000' , 909 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        Sequencia := FazerLancamento(  '191110000000000' , '291120000000000' , 909 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
    END IF;
    IF     TipoCredSuplementar  =   'Operacao de Credito' THEN
        Sequencia := FazerLancamento(  '192130100040000' , '292110000000000' , 909 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
    END IF;
    IF     TipoCredSuplementar  =   'Superavit' THEN
        Sequencia := FazerLancamento(  '192130101010000' , '292110000000000' , 909 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
    END IF;
    IF   TipoCredSuplementar  =  'Doacoes' THEN
        Sequencia := FazerLancamento(  '192130100060000' , '292110000000000' , 909 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
    END IF;
    IF   TipoCredSuplementar  =  'Auxilios' THEN
        Sequencia := FazerLancamento(  '192130100050000' , '292110000000000' , 909 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
    END IF;
    IF   TipoCredSuplementar  =  'Especial Reaberto' THEN
        Sequencia := FazerLancamento(  '192130200000000' , '292110000000000' , 909 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
    END IF;

    RETURN Sequencia;

END;
$$ LANGUAGE 'plpgsql';

