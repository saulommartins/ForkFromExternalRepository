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
* $Id:$
*
* Versão 1.99.3
*/

---------------------------------------------
-- RECRIACAO DE FUNCOES NO GERADOR DE CALCULO
---------------------------------------------

-----------------------------------------
-- EmpenhoLiquidacaoRestosAPagarExercicio
-----------------------------------------

DELETE FROM administracao.parametro             WHERE cod_modulo = 10 AND cod_biblioteca = 1 AND cod_funcao = 118;
DELETE FROM administracao.variavel              WHERE cod_modulo = 10 AND cod_biblioteca = 1 AND cod_funcao = 118;
DELETE FROM administracao.corpo_funcao_externa  WHERE cod_modulo = 10 AND cod_biblioteca = 1 AND cod_funcao = 118;
DELETE FROM administracao.funcao_externa        WHERE cod_modulo = 10 AND cod_biblioteca = 1 AND cod_funcao = 118;

-- INSERT INTO administracao.funcao
--                      (cod_modulo
--                   , cod_biblioteca
--                   , cod_funcao
--                   , cod_tipo_retorno
--                   , nom_funcao)
--                VALUES (10,1,118,1,'EmpenhoLiquidacaoRestosAPagarExercicio');
INSERT INTO administracao.funcao_externa
                  (cod_modulo
                  , cod_biblioteca
                  , cod_funcao
                  , comentario
                  , corpo_pl
                  , corpo_ln)
            VALUES (10,1,118,'','FUNCTION EmpenhoLiquidacaoRestosAPagarExercicio(VARCHAR,NUMERIC,VARCHAR,INTEGER,VARCHAR,INTEGER,INTEGER,VARCHAR) RETURNS INTEGER as \\'' 
DECLARE
Exercicio ALIAS FOR $1;
Valor ALIAS FOR $2;
Complemento ALIAS FOR $3;
CodLote ALIAS FOR $4;
TipoLote ALIAS FOR $5;
CodEntidade ALIAS FOR $6;
CodNota ALIAS FOR $7;
ExercRP ALIAS FOR $8;

  Restos VARCHAR := \\''\\''\\''\\'';
  Sequencia INTEGER;
BEGIN
Restos := pegaEmpenhoLiquidacaoRestos( Exercicio , CodNota , CodEntidade ); 
IF Restos = \\''\\''Executivo\\''\\'' THEN
    IF ExercRP = 1994 THEN
        Sequencia := FazerLancamento( \\''\\''212160202020100\\''\\'' , \\''\\''212110200020100\\''\\'' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF ExercRP = 1995 THEN
        Sequencia := FazerLancamento( \\''\\''212160202020200\\''\\'' , \\''\\''212110200020200\\''\\'' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF ExercRP = 1996 THEN
        Sequencia := FazerLancamento( \\''\\''212160202020300\\''\\'' , \\''\\''212110200020300\\''\\'' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF ExercRP = 1997 THEN
        Sequencia := FazerLancamento( \\''\\''212160202020400\\''\\'' , \\''\\''212110200020400\\''\\'' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF ExercRP = 1998 THEN
        Sequencia := FazerLancamento( \\''\\''212160202020500\\''\\'' , \\''\\''212110200020500\\''\\'' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF ExercRP = 1999 THEN
        Sequencia := FazerLancamento( \\''\\''212160202020600\\''\\'' , \\''\\''212110200020600\\''\\'' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF ExercRP = 2000 THEN
        Sequencia := FazerLancamento( \\''\\''212160202020700\\''\\'' , \\''\\''212110200020700\\''\\'' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF ExercRP = 2001 THEN
        Sequencia := FazerLancamento( \\''\\''212160202020800\\''\\'' , \\''\\''212110200020800\\''\\'' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF ExercRP = 2002 THEN
        Sequencia := FazerLancamento( \\''\\''212160202020900\\''\\'' , \\''\\''212110200020900\\''\\'' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF ExercRP = 2003 THEN
        Sequencia := FazerLancamento( \\''\\''212160202021000\\''\\'' , \\''\\''212110200021000\\''\\'' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF ExercRP = 2004 THEN
        Sequencia := FazerLancamento( \\''\\''212160202021100\\''\\'' , \\''\\''212110200021100\\''\\'' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF ExercRP = 2005 THEN
        Sequencia := FazerLancamento( \\''\\''212160202021200\\''\\'' , \\''\\''212110200021200\\''\\'' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF   ExercRP  =  2006 THEN
        Sequencia := FazerLancamento( \\''\\''212160202021300\\''\\'' , \\''\\''212110200021300\\''\\'' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF   ExercRP  =  2007 THEN
        Sequencia := FazerLancamento( \\''\\''212160202021400\\''\\'' , \\''\\''212110200021400\\''\\'' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF   ExercRP  =  2008 THEN
        Sequencia := FazerLancamento( \\''\\''212160202021500\\''\\'' , \\''\\''212110200021500\\''\\'' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF   ExercRP  =  2009 THEN
        Sequencia := FazerLancamento( \\''\\''212160202021600\\''\\'' , \\''\\''212110200021600\\''\\'' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF     ExercRP  =  2010 THEN
        Sequencia := FazerLancamento( \\''\\''212160202021700\\''\\'' , \\''\\''212110200021700\\''\\'' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
END IF;
IF Restos = \\''\\''Legislativo\\''\\'' THEN
    IF ExercRP = 1994 THEN
        Sequencia := FazerLancamento( \\''\\''212160202030100\\''\\'' , \\''\\''212110200030100\\''\\'' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF ExercRP = 1995 THEN
        Sequencia := FazerLancamento( \\''\\''212160202030200\\''\\'' , \\''\\''212110200030200\\''\\'' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF ExercRP = 1996 THEN
        Sequencia := FazerLancamento( \\''\\''212160202030300\\''\\'' , \\''\\''212110200030300\\''\\'' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF ExercRP = 1997 THEN
        Sequencia := FazerLancamento( \\''\\''212160202030400\\''\\'' , \\''\\''212110200030400\\''\\'' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF ExercRP = 1998 THEN
        Sequencia := FazerLancamento( \\''\\''212160202030500\\''\\'' , \\''\\''212110200030500\\''\\'' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF ExercRP = 1999 THEN
        Sequencia := FazerLancamento( \\''\\''212160202030600\\''\\'' , \\''\\''212110200030600\\''\\'' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF ExercRP = 2000 THEN
        Sequencia := FazerLancamento( \\''\\''212160202030700\\''\\'' , \\''\\''212110200030700\\''\\'' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF ExercRP = 2001 THEN
        Sequencia := FazerLancamento( \\''\\''212160202030800\\''\\'' , \\''\\''212110200030800\\''\\'' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF ExercRP = 2002 THEN
        Sequencia := FazerLancamento( \\''\\''212160202030900\\''\\'' , \\''\\''212110200030900\\''\\'' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF ExercRP = 2003 THEN
        Sequencia := FazerLancamento( \\''\\''212160202031000\\''\\'' , \\''\\''212110200031000\\''\\'' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF ExercRP = 2004 THEN
        Sequencia := FazerLancamento( \\''\\''212160202031100\\''\\'' , \\''\\''212110200031100\\''\\'' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF ExercRP = 2005 THEN
        Sequencia := FazerLancamento( \\''\\''212160202031200\\''\\'' , \\''\\''212110200031200\\''\\'' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF   ExercRP  =  2006 THEN
        Sequencia := FazerLancamento( \\''\\''212160202031300\\''\\'' , \\''\\''212110200031300\\''\\'' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF   ExercRP  =  2007 THEN
        Sequencia := FazerLancamento( \\''\\''212160202031400\\''\\'' , \\''\\''212110200031400\\''\\'' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF   ExercRP  =  2008 THEN
        Sequencia := FazerLancamento( \\''\\''212160202031500\\''\\'' , \\''\\''212110200031500\\''\\'' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF   ExercRP  =  2009 THEN
        Sequencia := FazerLancamento( \\''\\''212160202031600\\''\\'' , \\''\\''212110200031600\\''\\'' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF   ExercRP  =  2010 THEN
        Sequencia := FazerLancamento( \\''\\''212160202031700\\''\\'' , \\''\\''212110200031700\\''\\'' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
END IF;
IF Restos = \\''\\''RPPS\\''\\'' THEN
    IF ExercRP = 1994 THEN
        Sequencia := FazerLancamento( \\''\\''212160202040100\\''\\'' , \\''\\''212110200040100\\''\\'' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF ExercRP = 1995 THEN
        Sequencia := FazerLancamento( \\''\\''212160202040200\\''\\'' , \\''\\''212110200040200\\''\\'' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF ExercRP = 1996 THEN
        Sequencia := FazerLancamento( \\''\\''212160202040300\\''\\'' , \\''\\''212110200040300\\''\\'' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF ExercRP = 1997 THEN
        Sequencia := FazerLancamento( \\''\\''212160202040400\\''\\'' , \\''\\''212110200040400\\''\\'' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF ExercRP = 1998 THEN
        Sequencia := FazerLancamento( \\''\\''212160202040500\\''\\'' , \\''\\''212110200040500\\''\\'' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF ExercRP = 1999 THEN
        Sequencia := FazerLancamento( \\''\\''212160202040600\\''\\'' , \\''\\''212110200040600\\''\\'' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF ExercRP = 2000 THEN
        Sequencia := FazerLancamento( \\''\\''212160202040700\\''\\'' , \\''\\''212110200040700\\''\\'' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF ExercRP = 2001 THEN
        Sequencia := FazerLancamento( \\''\\''212160202040800\\''\\'' , \\''\\''212110200040800\\''\\'' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF ExercRP = 2002 THEN
        Sequencia := FazerLancamento( \\''\\''212160202040900\\''\\'' , \\''\\''212110200040900\\''\\'' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF ExercRP = 2003 THEN
        Sequencia := FazerLancamento( \\''\\''212160202041000\\''\\'' , \\''\\''212110200041000\\''\\'' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF ExercRP = 2004 THEN
        Sequencia := FazerLancamento( \\''\\''212160202041100\\''\\'' , \\''\\''212110200041100\\''\\'' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF ExercRP = 2005 THEN
        Sequencia := FazerLancamento( \\''\\''212160202041200\\''\\'' , \\''\\''212110200041200\\''\\'' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF   ExercRP  =  2006 THEN
        Sequencia := FazerLancamento( \\''\\''212160202041300\\''\\'' , \\''\\''212110200041300\\''\\'' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF   ExercRP  =  2007 THEN
        Sequencia := FazerLancamento( \\''\\''212160202041400\\''\\'' , \\''\\''212110200041400\\''\\'' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF   ExercRP  =  2008 THEN
        Sequencia := FazerLancamento( \\''\\''212160202041500\\''\\'' , \\''\\''212110200041500\\''\\'' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF   ExercRP  =  2009 THEN
        Sequencia := FazerLancamento( \\''\\''212160202041600\\''\\'' , \\''\\''212110200041600\\''\\'' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF   ExercRP  =  2010 THEN
        Sequencia := FazerLancamento( \\''\\''212160202041700\\''\\'' , \\''\\''212110200041700\\''\\'' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
END IF;
RETURN Sequencia;
END;
 \\'' LANGUAGE \\''plpgsql\\''; 
','');

INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,1,'0','#Restos <- pegaEmpenhoLiquidacaoRestos( #Exercicio , #CodNota , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,2,'1','SE #Restos = "Executivo" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,3,'2','SE #ExercRP = 1994 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,4,'2','#Sequencia <- FazerLancamento( "212160202020100" , "212110200020100" , 916 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,5,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,6,'2','SE #ExercRP = 1995 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,7,'2','#Sequencia <- FazerLancamento( "212160202020200" , "212110200020200" , 916 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,8,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,9,'2','SE #ExercRP = 1996 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,10,'2','#Sequencia <- FazerLancamento( "212160202020300" , "212110200020300" , 916 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,11,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,12,'2','SE #ExercRP = 1997 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,13,'2','#Sequencia <- FazerLancamento( "212160202020400" , "212110200020400" , 916 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,14,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,15,'2','SE #ExercRP = 1998 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,16,'2','#Sequencia <- FazerLancamento( "212160202020500" , "212110200020500" , 916 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,17,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,18,'2','SE #ExercRP = 1999 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,19,'2','#Sequencia <- FazerLancamento( "212160202020600" , "212110200020600" , 916 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,20,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,21,'2','SE #ExercRP = 2000 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,22,'2','#Sequencia <- FazerLancamento( "212160202020700" , "212110200020700" , 916 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,23,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,24,'2','SE #ExercRP = 2001 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,25,'2','#Sequencia <- FazerLancamento( "212160202020800" , "212110200020800" , 916 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,26,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,27,'2','SE #ExercRP = 2002 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,28,'2','#Sequencia <- FazerLancamento( "212160202020900" , "212110200020900" , 916 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,29,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,30,'2','SE #ExercRP = 2003 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,31,'2','#Sequencia <- FazerLancamento( "212160202021000" , "212110200021000" , 916 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,32,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,33,'2','SE #ExercRP = 2004 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,34,'2','#Sequencia <- FazerLancamento( "212160202021100" , "212110200021100" , 916 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,35,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,36,'2','SE #ExercRP = 2005 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,37,'2','#Sequencia <- FazerLancamento( "212160202021200" , "212110200021200" , 916 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,38,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,39,'2','SE   #ExercRP  =  2006 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,40,'2','#Sequencia <- FazerLancamento(  "212160202021300" , "212110200021300" , 916 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,41,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,42,'2','SE   #ExercRP  =  2007 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,43,'2','#Sequencia <- FazerLancamento(  "212160202021400" , "212110200021400" , 916 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,44,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,45,'2','SE   #ExercRP  =  2008 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,46,'2','#Sequencia <- FazerLancamento(  "212160202021500" , "212110200021500" , 916 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,47,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,48,'2','SE   #ExercRP  =  2009 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,49,'2','#Sequencia <- FazerLancamento(  "212160202021600" , "212110200021600" , 916 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,50,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,51,'2','SE     #ExercRP  =  2010 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,52,'2','#Sequencia <- FazerLancamento(  "212160202021700" , "212110200021700" , 916 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,53,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,54,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,55,'1','SE #Restos = "Legislativo" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,56,'2','SE #ExercRP = 1994 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,57,'2','#Sequencia <- FazerLancamento( "212160202030100" , "212110200030100" , 916 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,58,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,59,'2','SE #ExercRP = 1995 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,60,'2','#Sequencia <- FazerLancamento( "212160202030200" , "212110200030200" , 916 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,61,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,62,'2','SE #ExercRP = 1996 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,63,'2','#Sequencia <- FazerLancamento( "212160202030300" , "212110200030300" , 916 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,64,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,65,'2','SE #ExercRP = 1997 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,66,'2','#Sequencia <- FazerLancamento( "212160202030400" , "212110200030400" , 916 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,67,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,68,'2','SE #ExercRP = 1998 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,69,'2','#Sequencia <- FazerLancamento( "212160202030500" , "212110200030500" , 916 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,70,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,71,'2','SE #ExercRP = 1999 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,72,'2','#Sequencia <- FazerLancamento( "212160202030600" , "212110200030600" , 916 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,73,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,74,'2','SE #ExercRP = 2000 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,75,'2','#Sequencia <- FazerLancamento( "212160202030700" , "212110200030700" , 916 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,76,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,77,'2','SE #ExercRP = 2001 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,78,'2','#Sequencia <- FazerLancamento( "212160202030800" , "212110200030800" , 916 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,79,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,80,'2','SE #ExercRP = 2002 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,81,'2','#Sequencia <- FazerLancamento( "212160202030900" , "212110200030900" , 916 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,82,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,83,'2','SE #ExercRP = 2003 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,84,'2','#Sequencia <- FazerLancamento( "212160202031000" , "212110200031000" , 916 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,85,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,86,'2','SE #ExercRP = 2004 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,87,'2','#Sequencia <- FazerLancamento( "212160202031100" , "212110200031100" , 916 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,88,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,89,'2','SE #ExercRP = 2005 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,90,'2','#Sequencia <- FazerLancamento( "212160202031200" , "212110200031200" , 916 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,91,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,92,'2','SE   #ExercRP  =  2006 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,93,'2','#Sequencia <- FazerLancamento(  "212160202031300" , "212110200031300" , 916 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,94,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,95,'2','SE   #ExercRP  =  2007 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,96,'2','#Sequencia <- FazerLancamento(  "212160202031400" , "212110200031400" , 916 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,97,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,98,'2','SE   #ExercRP  =  2008 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,99,'2','#Sequencia <- FazerLancamento(  "212160202031500" , "212110200031500" , 916 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,100,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,101,'2','SE   #ExercRP  =  2009 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,102,'2','#Sequencia <- FazerLancamento(  "212160202031600" , "212110200031600" , 916 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,103,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,104,'2','SE   #ExercRP  =  2010 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,105,'2','#Sequencia <- FazerLancamento(  "212160202031700" , "212110200031700" , 916 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,106,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,107,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,108,'1','SE #Restos = "RPPS" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,109,'2','SE #ExercRP = 1994 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,110,'2','#Sequencia <- FazerLancamento( "212160202040100" , "212110200040100" , 916 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,111,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,112,'2','SE #ExercRP = 1995 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,113,'2','#Sequencia <- FazerLancamento( "212160202040200" , "212110200040200" , 916 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,114,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,115,'2','SE #ExercRP = 1996 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,116,'2','#Sequencia <- FazerLancamento( "212160202040300" , "212110200040300" , 916 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,117,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,118,'2','SE #ExercRP = 1997 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,119,'2','#Sequencia <- FazerLancamento( "212160202040400" , "212110200040400" , 916 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,120,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,121,'2','SE #ExercRP = 1998 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,122,'2','#Sequencia <- FazerLancamento( "212160202040500" , "212110200040500" , 916 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,123,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,124,'2','SE #ExercRP = 1999 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,125,'2','#Sequencia <- FazerLancamento( "212160202040600" , "212110200040600" , 916 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,126,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,127,'2','SE #ExercRP = 2000 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,128,'2','#Sequencia <- FazerLancamento( "212160202040700" , "212110200040700" , 916 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,129,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,130,'2','SE #ExercRP = 2001 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,131,'2','#Sequencia <- FazerLancamento( "212160202040800" , "212110200040800" , 916 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,132,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,133,'2','SE #ExercRP = 2002 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,134,'2','#Sequencia <- FazerLancamento( "212160202040900" , "212110200040900" , 916 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,135,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,136,'2','SE #ExercRP = 2003 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,137,'2','#Sequencia <- FazerLancamento( "212160202041000" , "212110200041000" , 916 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,138,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,139,'2','SE #ExercRP = 2004 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,140,'2','#Sequencia <- FazerLancamento( "212160202041100" , "212110200041100" , 916 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,141,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,142,'2','SE #ExercRP = 2005 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,143,'2','#Sequencia <- FazerLancamento( "212160202041200" , "212110200041200" , 916 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,144,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,145,'2','SE   #ExercRP  =  2006 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,146,'2','#Sequencia <- FazerLancamento(  "212160202041300" , "212110200041300" , 916 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,147,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,148,'2','SE   #ExercRP  =  2007 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,149,'2','#Sequencia <- FazerLancamento(  "212160202041400" , "212110200041400" , 916 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,150,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,151,'2','SE   #ExercRP  =  2008 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,152,'2','#Sequencia <- FazerLancamento(  "212160202041500" , "212110200041500" , 916 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,153,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,154,'2','SE   #ExercRP  =  2009 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,155,'2','#Sequencia <- FazerLancamento(  "212160202041600" , "212110200041600" , 916 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,156,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,157,'2','SE   #ExercRP  =  2010 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,158,'2','#Sequencia <- FazerLancamento(  "212160202041700" , "212110200041700" , 916 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,159,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,160,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,118,161,'0','RETORNA #Sequencia');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,118,1,2,'Restos','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,118,2,1,'Sequencia','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,118,3,2,'Exercicio','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,118,4,4,'Valor','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,118,5,2,'Complemento','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,118,6,1,'CodLote','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,118,7,2,'TipoLote','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,118,8,1,'CodEntidade','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,118,9,1,'CodNota','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,118,10,2,'ExercRP','');
INSERT INTO administracao.parametro
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , ordem)
               VALUES (10,1,118,3,0);
INSERT INTO administracao.parametro
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , ordem)
               VALUES (10,1,118,4,1);
INSERT INTO administracao.parametro
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , ordem)
               VALUES (10,1,118,5,2);
INSERT INTO administracao.parametro
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , ordem)
               VALUES (10,1,118,6,3);
INSERT INTO administracao.parametro
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , ordem)
               VALUES (10,1,118,7,4);
INSERT INTO administracao.parametro
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , ordem)
               VALUES (10,1,118,8,5);
INSERT INTO administracao.parametro
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , ordem)
               VALUES (10,1,118,9,6);
INSERT INTO administracao.parametro
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , ordem)
               VALUES (10,1,118,10,7);


CREATE OR REPLACE FUNCTION EmpenhoLiquidacaoRestosAPagarExercicio(VARCHAR,NUMERIC,VARCHAR,INTEGER,VARCHAR,INTEGER,INTEGER,VARCHAR) RETURNS INTEGER as $$
DECLARE
Exercicio ALIAS FOR $1;
Valor ALIAS FOR $2;
Complemento ALIAS FOR $3;
CodLote ALIAS FOR $4;
TipoLote ALIAS FOR $5;
CodEntidade ALIAS FOR $6;
CodNota ALIAS FOR $7;
ExercRP ALIAS FOR $8;

  Restos VARCHAR := '';
  Sequencia INTEGER;
BEGIN
Restos := pegaEmpenhoLiquidacaoRestos( Exercicio , CodNota , CodEntidade );
IF Restos = 'Executivo' THEN
    IF ExercRP = 1994 THEN
        Sequencia := FazerLancamento( '212160202020100' , '212110200020100' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF ExercRP = 1995 THEN
        Sequencia := FazerLancamento( '212160202020200' , '212110200020200' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF ExercRP = 1996 THEN
        Sequencia := FazerLancamento( '212160202020300' , '212110200020300' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF ExercRP = 1997 THEN
        Sequencia := FazerLancamento( '212160202020400' , '212110200020400' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF ExercRP = 1998 THEN
        Sequencia := FazerLancamento( '212160202020500' , '212110200020500' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF ExercRP = 1999 THEN
        Sequencia := FazerLancamento( '212160202020600' , '212110200020600' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF ExercRP = 2000 THEN
        Sequencia := FazerLancamento( '212160202020700' , '212110200020700' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF ExercRP = 2001 THEN
        Sequencia := FazerLancamento( '212160202020800' , '212110200020800' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF ExercRP = 2002 THEN
        Sequencia := FazerLancamento( '212160202020900' , '212110200020900' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF ExercRP = 2003 THEN
        Sequencia := FazerLancamento( '212160202021000' , '212110200021000' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF ExercRP = 2004 THEN
        Sequencia := FazerLancamento( '212160202021100' , '212110200021100' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF ExercRP = 2005 THEN
        Sequencia := FazerLancamento( '212160202021200' , '212110200021200' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF   ExercRP  =  2006 THEN
        Sequencia := FazerLancamento( '212160202021300' , '212110200021300' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF   ExercRP  =  2007 THEN
        Sequencia := FazerLancamento( '212160202021400' , '212110200021400' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF   ExercRP  =  2008 THEN
        Sequencia := FazerLancamento( '212160202021500' , '212110200021500' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF   ExercRP  =  2009 THEN
        Sequencia := FazerLancamento( '212160202021600' , '212110200021600' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF     ExercRP  =  2010 THEN
        Sequencia := FazerLancamento( '212160202021700' , '212110200021700' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
END IF;
IF Restos = 'Legislativo' THEN
    IF ExercRP = 1994 THEN
        Sequencia := FazerLancamento( '212160202030100' , '212110200030100' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF ExercRP = 1995 THEN
        Sequencia := FazerLancamento( '212160202030200' , '212110200030200' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF ExercRP = 1996 THEN
        Sequencia := FazerLancamento( '212160202030300' , '212110200030300' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF ExercRP = 1997 THEN
        Sequencia := FazerLancamento( '212160202030400' , '212110200030400' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF ExercRP = 1998 THEN
        Sequencia := FazerLancamento( '212160202030500' , '212110200030500' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF ExercRP = 1999 THEN
        Sequencia := FazerLancamento( '212160202030600' , '212110200030600' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF ExercRP = 2000 THEN
        Sequencia := FazerLancamento( '212160202030700' , '212110200030700' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF ExercRP = 2001 THEN
        Sequencia := FazerLancamento( '212160202030800' , '212110200030800' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF ExercRP = 2002 THEN
        Sequencia := FazerLancamento( '212160202030900' , '212110200030900' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF ExercRP = 2003 THEN
        Sequencia := FazerLancamento( '212160202031000' , '212110200031000' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF ExercRP = 2004 THEN
        Sequencia := FazerLancamento( '212160202031100' , '212110200031100' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF ExercRP = 2005 THEN
        Sequencia := FazerLancamento( '212160202031200' , '212110200031200' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF   ExercRP  =  2006 THEN
        Sequencia := FazerLancamento( '212160202031300' , '212110200031300' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF   ExercRP  =  2007 THEN
        Sequencia := FazerLancamento( '212160202031400' , '212110200031400' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF   ExercRP  =  2008 THEN
        Sequencia := FazerLancamento( '212160202031500' , '212110200031500' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF   ExercRP  =  2009 THEN
        Sequencia := FazerLancamento( '212160202031600' , '212110200031600' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF   ExercRP  =  2010 THEN
        Sequencia := FazerLancamento( '212160202031700' , '212110200031700' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
END IF;
IF Restos = 'RPPS' THEN
    IF ExercRP = 1994 THEN
        Sequencia := FazerLancamento( '212160202040100' , '212110200040100' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF ExercRP = 1995 THEN
        Sequencia := FazerLancamento( '212160202040200' , '212110200040200' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF ExercRP = 1996 THEN
        Sequencia := FazerLancamento( '212160202040300' , '212110200040300' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF ExercRP = 1997 THEN
        Sequencia := FazerLancamento( '212160202040400' , '212110200040400' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF ExercRP = 1998 THEN
        Sequencia := FazerLancamento( '212160202040500' , '212110200040500' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF ExercRP = 1999 THEN
        Sequencia := FazerLancamento( '212160202040600' , '212110200040600' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF ExercRP = 2000 THEN
        Sequencia := FazerLancamento( '212160202040700' , '212110200040700' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF ExercRP = 2001 THEN
        Sequencia := FazerLancamento( '212160202040800' , '212110200040800' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF ExercRP = 2002 THEN
        Sequencia := FazerLancamento( '212160202040900' , '212110200040900' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF ExercRP = 2003 THEN
        Sequencia := FazerLancamento( '212160202041000' , '212110200041000' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF ExercRP = 2004 THEN
        Sequencia := FazerLancamento( '212160202041100' , '212110200041100' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF ExercRP = 2005 THEN
        Sequencia := FazerLancamento( '212160202041200' , '212110200041200' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF   ExercRP  =  2006 THEN
        Sequencia := FazerLancamento( '212160202041300' , '212110200041300' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF   ExercRP  =  2007 THEN
        Sequencia := FazerLancamento( '212160202041400' , '212110200041400' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF   ExercRP  =  2008 THEN
        Sequencia := FazerLancamento( '212160202041500' , '212110200041500' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF   ExercRP  =  2009 THEN
        Sequencia := FazerLancamento( '212160202041600' , '212110200041600' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF   ExercRP  =  2010 THEN
        Sequencia := FazerLancamento( '212160202041700' , '212110200041700' , 916 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
END IF;
RETURN Sequencia;
END;
$$ LANGUAGE 'plpgsql';




------------------------------
-- EmpenhoPagamentoRPLiquidado
------------------------------

DELETE FROM administracao.parametro             WHERE cod_modulo = 10 AND cod_biblioteca = 1 AND cod_funcao = 126;
DELETE FROM administracao.variavel              WHERE cod_modulo = 10 AND cod_biblioteca = 1 AND cod_funcao = 126;
DELETE FROM administracao.corpo_funcao_externa  WHERE cod_modulo = 10 AND cod_biblioteca = 1 AND cod_funcao = 126;
DELETE FROM administracao.funcao_externa        WHERE cod_modulo = 10 AND cod_biblioteca = 1 AND cod_funcao = 126;

--   INSERT INTO administracao.funcao
--                      (cod_modulo
--                   , cod_biblioteca
--                   , cod_funcao
--                   , cod_tipo_retorno
--                   , nom_funcao)
--                VALUES (10,1,126,1,'EmpenhoPagamentoRPLiquidado');
INSERT INTO administracao.funcao_externa
                  (cod_modulo
                  , cod_biblioteca
                  , cod_funcao
                  , comentario
                  , corpo_pl
                  , corpo_ln)
            VALUES (10,1,126,'','FUNCTION EmpenhoPagamentoRPLiquidado(VARCHAR,NUMERIC,VARCHAR,INTEGER,VARCHAR,INTEGER,INTEGER,VARCHAR,VARCHAR,VARCHAR,VARCHAR) RETURNS INTEGER as \\'' 
DECLARE
Exercicio ALIAS FOR $1;
Valor ALIAS FOR $2;
Complemento ALIAS FOR $3;
CodLote ALIAS FOR $4;
TipoLote ALIAS FOR $5;
CodEntidade ALIAS FOR $6;
CodNota ALIAS FOR $7;
ContaPG ALIAS FOR $8;
ExercRP ALIAS FOR $9;
Restos ALIAS FOR $10;
ExercLiquidacao ALIAS FOR $11;

  Sequencia INTEGER;
BEGIN
Sequencia := FazerLancamento( \\''\\''295400000000000\\''\\'' , \\''\\''295200000000000\\''\\'' , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
IF ExercLiquidacao < Exercicio THEN
    Sequencia := FazerLancamento( \\''\\''292410508000000\\''\\'' , \\''\\''292410510000000\\''\\'' , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
END IF;
IF ExercLiquidacao = Exercicio THEN
    Sequencia := FazerLancamento( \\''\\''292410502000000\\''\\'' , \\''\\''292410503000000\\''\\'' , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
END IF;
IF Restos = \\''\\''Executivo\\''\\'' THEN
    IF ExercRP = 1994 THEN
        Sequencia := FazerLancamento( \\''\\''212110200020100\\''\\'' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF ExercRP = 1995 THEN
        Sequencia := FazerLancamento( \\''\\''212110200020200\\''\\'' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF ExercRP = 1996 THEN
        Sequencia := FazerLancamento( \\''\\''212110200020300\\''\\'' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF Exercicio = 1997 THEN
        Sequencia := FazerLancamento( \\''\\''212110200020400\\''\\'' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF Exercicio = 1998 THEN
        Sequencia := FazerLancamento( \\''\\''212110200020500\\''\\'' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF ExercRP = 1999 THEN
        Sequencia := FazerLancamento( \\''\\''212110200020600\\''\\'' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF ExercRP = 2000 THEN
        Sequencia := FazerLancamento( \\''\\''212110200020700\\''\\'' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF ExercRP = 2001 THEN
        Sequencia := FazerLancamento( \\''\\''212110200020800\\''\\'' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF ExercRP = 2002 THEN
        Sequencia := FazerLancamento( \\''\\''212110200020900\\''\\'' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF ExercRP = 2003 THEN
        Sequencia := FazerLancamento( \\''\\''212110200021000\\''\\'' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF ExercRP = 2004 THEN
        Sequencia := FazerLancamento( \\''\\''212110200021100\\''\\'' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF ExercRP = 2005 THEN
        Sequencia := FazerLancamento( \\''\\''212110200021200\\''\\'' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF   ExercRP  =  2006 THEN
        Sequencia := FazerLancamento(  \\''\\''212110200021300\\''\\'' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
    END IF;
    IF   ExercRP  =  2007 THEN
        Sequencia := FazerLancamento(  \\''\\''212110200021400\\''\\'' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
    END IF;
    IF   ExercRP  =  2008 THEN
        Sequencia := FazerLancamento(  \\''\\''212110200021500\\''\\'' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
    END IF;
    IF   ExercRP  =  2009 THEN
        Sequencia := FazerLancamento(  \\''\\''212110200021600\\''\\'' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
    END IF;
    IF   ExercRP  =  2010 THEN
        Sequencia := FazerLancamento(  \\''\\''212110200021700\\''\\'' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
    END IF;
END IF;
IF Restos = \\''\\''Legislativo\\''\\'' THEN
    IF ExercRP = 1994 THEN
        Sequencia := FazerLancamento( \\''\\''212110200030100\\''\\'' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF ExercRP = 1995 THEN
        Sequencia := FazerLancamento( \\''\\''212110200030200\\''\\'' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF ExercRP = 1996 THEN
        Sequencia := FazerLancamento( \\''\\''212110200030300\\''\\'' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF ExercRP = 1997 THEN
        Sequencia := FazerLancamento( \\''\\''212110200030400\\''\\'' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF ExercRP = 1998 THEN
        Sequencia := FazerLancamento( \\''\\''212110200030500\\''\\'' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF ExercRP = 1999 THEN
        Sequencia := FazerLancamento( \\''\\''212110200030600\\''\\'' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF ExercRP = 2000 THEN
        Sequencia := FazerLancamento( \\''\\''212110200030700\\''\\'' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF ExercRP = 2001 THEN
        Sequencia := FazerLancamento( \\''\\''212110200030800\\''\\'' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF Exercicio = 2002 THEN
        Sequencia := FazerLancamento( \\''\\''212110200030900\\''\\'' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF ExercRP = 2003 THEN
        Sequencia := FazerLancamento( \\''\\''212110200031000\\''\\'' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF ExercRP = 2004 THEN
        Sequencia := FazerLancamento( \\''\\''212110200031100\\''\\'' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF ExercRP = 2005 THEN
        Sequencia := FazerLancamento( \\''\\''212110200031200\\''\\'' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF   ExercRP  =  2006 THEN
        Sequencia := FazerLancamento(  \\''\\''212110200031300\\''\\'' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
    END IF;
    IF   ExercRP  =  2007 THEN
        Sequencia := FazerLancamento(  \\''\\''212110200031400\\''\\'' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
    END IF;
    IF   ExercRP  =  2008 THEN
        Sequencia := FazerLancamento(  \\''\\''212110200031500\\''\\'' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
    END IF;
    IF   ExercRP  =  2009 THEN
        Sequencia := FazerLancamento(  \\''\\''212110200031600\\''\\'' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
    END IF;
    IF   ExercRP  =  2010 THEN
        Sequencia := FazerLancamento(  \\''\\''212110200031700\\''\\'' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
    END IF;
END IF;
IF Restos = \\''\\''RPPS\\''\\'' THEN
    IF ExercRP = 1994 THEN
        Sequencia := FazerLancamento( \\''\\''212110200040100\\''\\'' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF ExercRP = 1995 THEN
        Sequencia := FazerLancamento( \\''\\''212110200040200\\''\\'' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF ExercRP = 1996 THEN
        Sequencia := FazerLancamento( \\''\\''212110200040300\\''\\'' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF ExercRP = 1997 THEN
        Sequencia := FazerLancamento( \\''\\''212110200040400\\''\\'' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF ExercRP = 1998 THEN
        Sequencia := FazerLancamento( \\''\\''212110200040500\\''\\'' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF ExercRP = 1999 THEN
        Sequencia := FazerLancamento( \\''\\''212110200040600\\''\\'' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF ExercRP = 2000 THEN
        Sequencia := FazerLancamento( \\''\\''212110200040700\\''\\'' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF ExercRP = 2001 THEN
        Sequencia := FazerLancamento( \\''\\''212110200040800\\''\\'' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF ExercRP = 2002 THEN
        Sequencia := FazerLancamento( \\''\\''212110200040900\\''\\'' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF ExercRP = 2003 THEN
        Sequencia := FazerLancamento( \\''\\''212110200041000\\''\\'' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF ExercRP = 2004 THEN
        Sequencia := FazerLancamento( \\''\\''212110200041100\\''\\'' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF ExercRP = 2005 THEN
        Sequencia := FazerLancamento( \\''\\''212110200041200\\''\\'' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade ); 
    END IF;
    IF   ExercRP  =  2006 THEN
        Sequencia := FazerLancamento(  \\''\\''212110200041300\\''\\'' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
    END IF;
    IF   ExercRP  =  2007 THEN
        Sequencia := FazerLancamento(  \\''\\''212110200041400\\''\\'' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
    END IF;
    IF   ExercRP  =  2008 THEN
        Sequencia := FazerLancamento(  \\''\\''212110200041500\\''\\'' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
    END IF;
    IF   ExercRP  =  2009 THEN
        Sequencia := FazerLancamento(  \\''\\''212110200041600\\''\\'' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
    END IF;
    IF   ExercRP  =  2010 THEN
        Sequencia := FazerLancamento(  \\''\\''212110200041700\\''\\'' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
    END IF;
END IF;
RETURN Sequencia;
END;
 \\'' LANGUAGE \\''plpgsql\\''; 
','');

INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,1,'0','#Sequencia <- FazerLancamento( "295400000000000" , "295200000000000" , 917 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,2,'1','SE #ExercLiquidacao < #Exercicio ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,3,'1','#Sequencia <- FazerLancamento( "292410508000000" , "292410510000000" , 917 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,4,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,5,'1','SE #ExercLiquidacao = #Exercicio ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,6,'1','#Sequencia <- FazerLancamento( "292410502000000" , "292410503000000" , 917 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,7,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,8,'1','SE #Restos = "Executivo" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,9,'2','SE #ExercRP = 1994 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,10,'2','#Sequencia <- FazerLancamento( "212110200020100" , #ContaPG , 917 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,11,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,12,'2','SE #ExercRP = 1995 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,13,'2','#Sequencia <- FazerLancamento( "212110200020200" , #ContaPG , 917 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,14,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,15,'2','SE #ExercRP = 1996 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,16,'2','#Sequencia <- FazerLancamento( "212110200020300" , #ContaPG , 917 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,17,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,18,'2','SE #Exercicio = 1997 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,19,'2','#Sequencia <- FazerLancamento( "212110200020400" , #ContaPG , 917 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,20,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,21,'2','SE #Exercicio = 1998 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,22,'2','#Sequencia <- FazerLancamento( "212110200020500" , #ContaPG , 917 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,23,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,24,'2','SE #ExercRP = 1999 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,25,'2','#Sequencia <- FazerLancamento( "212110200020600" , #ContaPG , 917 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,26,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,27,'2','SE #ExercRP = 2000 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,28,'2','#Sequencia <- FazerLancamento( "212110200020700" , #ContaPG , 917 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,29,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,30,'2','SE #ExercRP = 2001 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,31,'2','#Sequencia <- FazerLancamento( "212110200020800" , #ContaPG , 917 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,32,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,33,'2','SE #ExercRP = 2002 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,34,'2','#Sequencia <- FazerLancamento( "212110200020900" , #ContaPG , 917 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,35,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,36,'2','SE #ExercRP = 2003 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,37,'2','#Sequencia <- FazerLancamento( "212110200021000" , #ContaPG , 917 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,38,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,39,'2','SE #ExercRP = 2004 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,40,'2','#Sequencia <- FazerLancamento( "212110200021100" , #ContaPG , 917 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,41,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,42,'2','SE #ExercRP = 2005 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,43,'2','#Sequencia <- FazerLancamento( "212110200021200" , #ContaPG , 917 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,44,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,45,'2','SE   #ExercRP  =  2006 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,46,'2','#Sequencia <- FazerLancamento(  "212110200021300" , #ContaPG , 917 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,47,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,48,'2','SE   #ExercRP  =  2007 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,49,'2','#Sequencia <- FazerLancamento(  "212110200021400" , #ContaPG , 917 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,50,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,51,'2','SE   #ExercRP  =  2008 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,52,'2','#Sequencia <- FazerLancamento(  "212110200021500" , #ContaPG , 917 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,53,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,54,'2','SE   #ExercRP  =  2009 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,55,'2','#Sequencia <- FazerLancamento(  "212110200021600" , #ContaPG , 917 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,56,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,57,'2','SE   #ExercRP  =  2010 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,58,'2','#Sequencia <- FazerLancamento(  "212110200021700" , #ContaPG , 917 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,59,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,60,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,61,'1','SE #Restos = "Legislativo" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,62,'2','SE #ExercRP = 1994 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,63,'2','#Sequencia <- FazerLancamento( "212110200030100" , #ContaPG , 917 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,64,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,65,'2','SE #ExercRP = 1995 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,66,'2','#Sequencia <- FazerLancamento( "212110200030200" , #ContaPG , 917 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,67,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,68,'2','SE #ExercRP = 1996 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,69,'2','#Sequencia <- FazerLancamento( "212110200030300" , #ContaPG , 917 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,70,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,71,'2','SE #ExercRP = 1997 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,72,'2','#Sequencia <- FazerLancamento( "212110200030400" , #ContaPG , 917 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,73,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,74,'2','SE #ExercRP = 1998 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,75,'2','#Sequencia <- FazerLancamento( "212110200030500" , #ContaPG , 917 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,76,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,77,'2','SE #ExercRP = 1999 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,78,'2','#Sequencia <- FazerLancamento( "212110200030600" , #ContaPG , 917 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,79,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,80,'2','SE #ExercRP = 2000 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,81,'2','#Sequencia <- FazerLancamento( "212110200030700" , #ContaPG , 917 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,82,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,83,'2','SE #ExercRP = 2001 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,84,'2','#Sequencia <- FazerLancamento( "212110200030800" , #ContaPG , 917 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,85,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,86,'2','SE #Exercicio = 2002 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,87,'2','#Sequencia <- FazerLancamento( "212110200030900" , #ContaPG , 917 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,88,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,89,'2','SE #ExercRP = 2003 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,90,'2','#Sequencia <- FazerLancamento( "212110200031000" , #ContaPG , 917 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,91,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,92,'2','SE #ExercRP = 2004 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,93,'2','#Sequencia <- FazerLancamento( "212110200031100" , #ContaPG , 917 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,94,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,95,'2','SE #ExercRP = 2005 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,96,'2','#Sequencia <- FazerLancamento( "212110200031200" , #ContaPG , 917 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,97,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,98,'2','SE   #ExercRP  =  2006 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,99,'2','#Sequencia <- FazerLancamento(  "212110200031300" , #ContaPG , 917 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,100,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,101,'2','SE   #ExercRP  =  2007 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,102,'2','#Sequencia <- FazerLancamento(  "212110200031400" , #ContaPG , 917 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,103,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,104,'2','SE   #ExercRP  =  2008 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,105,'2','#Sequencia <- FazerLancamento(  "212110200031500" , #ContaPG , 917 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,106,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,107,'2','SE   #ExercRP  =  2009 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,108,'2','#Sequencia <- FazerLancamento(  "212110200031600" , #ContaPG , 917 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,109,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,110,'2','SE   #ExercRP  =  2010 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,111,'2','#Sequencia <- FazerLancamento(  "212110200031700" , #ContaPG , 917 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,112,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,113,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,114,'1','SE #Restos = "RPPS" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,115,'2','SE #ExercRP = 1994 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,116,'2','#Sequencia <- FazerLancamento( "212110200040100" , #ContaPG , 917 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,117,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,118,'2','SE #ExercRP = 1995 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,119,'2','#Sequencia <- FazerLancamento( "212110200040200" , #ContaPG , 917 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,120,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,121,'2','SE #ExercRP = 1996 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,122,'2','#Sequencia <- FazerLancamento( "212110200040300" , #ContaPG , 917 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,123,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,124,'2','SE #ExercRP = 1997 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,125,'2','#Sequencia <- FazerLancamento( "212110200040400" , #ContaPG , 917 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,126,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,127,'2','SE #ExercRP = 1998 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,128,'2','#Sequencia <- FazerLancamento( "212110200040500" , #ContaPG , 917 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,129,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,130,'2','SE #ExercRP = 1999 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,131,'2','#Sequencia <- FazerLancamento( "212110200040600" , #ContaPG , 917 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,132,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,133,'2','SE #ExercRP = 2000 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,134,'2','#Sequencia <- FazerLancamento( "212110200040700" , #ContaPG , 917 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,135,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,136,'2','SE #ExercRP = 2001 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,137,'2','#Sequencia <- FazerLancamento( "212110200040800" , #ContaPG , 917 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,138,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,139,'2','SE #ExercRP = 2002 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,140,'2','#Sequencia <- FazerLancamento( "212110200040900" , #ContaPG , 917 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,141,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,142,'2','SE #ExercRP = 2003 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,143,'2','#Sequencia <- FazerLancamento( "212110200041000" , #ContaPG , 917 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,144,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,145,'2','SE #ExercRP = 2004 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,146,'2','#Sequencia <- FazerLancamento( "212110200041100" , #ContaPG , 917 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,147,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,148,'2','SE #ExercRP = 2005 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,149,'2','#Sequencia <- FazerLancamento( "212110200041200" , #ContaPG , 917 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,150,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,151,'2','SE   #ExercRP  =  2006 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,152,'2','#Sequencia <- FazerLancamento(  "212110200041300" , #ContaPG , 917 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,153,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,154,'2','SE   #ExercRP  =  2007 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,155,'2','#Sequencia <- FazerLancamento(  "212110200041400" , #ContaPG , 917 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,156,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,157,'2','SE   #ExercRP  =  2008 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,158,'2','#Sequencia <- FazerLancamento(  "212110200041500" , #ContaPG , 917 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,159,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,160,'2','SE   #ExercRP  =  2009 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,161,'2','#Sequencia <- FazerLancamento(  "212110200041600" , #ContaPG , 917 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,162,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,163,'2','SE   #ExercRP  =  2010 ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,164,'2','#Sequencia <- FazerLancamento(  "212110200041700" , #ContaPG , 917 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,165,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,166,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,126,167,'0','RETORNA #Sequencia');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,126,1,1,'Sequencia','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,126,2,2,'Exercicio','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,126,3,4,'Valor','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,126,4,2,'Complemento','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,126,5,1,'CodLote','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,126,6,2,'TipoLote','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,126,7,1,'CodEntidade','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,126,8,1,'CodNota','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,126,9,2,'ContaPG','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,126,10,2,'ExercRP','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,126,11,2,'Restos','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,126,12,2,'ExercLiquidacao','');
INSERT INTO administracao.parametro
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , ordem)
               VALUES (10,1,126,2,0);
INSERT INTO administracao.parametro
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , ordem)
               VALUES (10,1,126,3,1);
INSERT INTO administracao.parametro
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , ordem)
               VALUES (10,1,126,4,2);
INSERT INTO administracao.parametro
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , ordem)
               VALUES (10,1,126,5,3);
INSERT INTO administracao.parametro
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , ordem)
               VALUES (10,1,126,6,4);
INSERT INTO administracao.parametro
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , ordem)
               VALUES (10,1,126,7,5);
INSERT INTO administracao.parametro
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , ordem)
               VALUES (10,1,126,8,6);
INSERT INTO administracao.parametro
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , ordem)
               VALUES (10,1,126,9,7);
INSERT INTO administracao.parametro
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , ordem)
               VALUES (10,1,126,10,8);
INSERT INTO administracao.parametro
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , ordem)
               VALUES (10,1,126,11,9);
INSERT INTO administracao.parametro
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , ordem)
               VALUES (10,1,126,12,10);


CREATE OR REPLACE FUNCTION EmpenhoPagamentoRPLiquidado(VARCHAR,NUMERIC,VARCHAR,INTEGER,VARCHAR,INTEGER,INTEGER,VARCHAR,VARCHAR,VARCHAR,VARCHAR) RETURNS INTEGER as $$
DECLARE
Exercicio ALIAS FOR $1;
Valor ALIAS FOR $2;
Complemento ALIAS FOR $3;
CodLote ALIAS FOR $4;
TipoLote ALIAS FOR $5;
CodEntidade ALIAS FOR $6;
CodNota ALIAS FOR $7;
ContaPG ALIAS FOR $8;
ExercRP ALIAS FOR $9;
Restos ALIAS FOR $10;
ExercLiquidacao ALIAS FOR $11;

  Sequencia INTEGER;
BEGIN
Sequencia := FazerLancamento( '295400000000000' , '295200000000000' , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
IF ExercLiquidacao < Exercicio THEN
    Sequencia := FazerLancamento( '292410508000000' , '292410510000000' , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
END IF;
IF ExercLiquidacao = Exercicio THEN
    Sequencia := FazerLancamento( '292410502000000' , '292410503000000' , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
END IF;
IF Restos = 'Executivo' THEN
    IF ExercRP = 1994 THEN
        Sequencia := FazerLancamento( '212110200020100' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF ExercRP = 1995 THEN
        Sequencia := FazerLancamento( '212110200020200' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF ExercRP = 1996 THEN
        Sequencia := FazerLancamento( '212110200020300' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF Exercicio = 1997 THEN
        Sequencia := FazerLancamento( '212110200020400' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF Exercicio = 1998 THEN
        Sequencia := FazerLancamento( '212110200020500' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF ExercRP = 1999 THEN
        Sequencia := FazerLancamento( '212110200020600' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF ExercRP = 2000 THEN
        Sequencia := FazerLancamento( '212110200020700' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF ExercRP = 2001 THEN
        Sequencia := FazerLancamento( '212110200020800' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF ExercRP = 2002 THEN
        Sequencia := FazerLancamento( '212110200020900' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF ExercRP = 2003 THEN
        Sequencia := FazerLancamento( '212110200021000' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF ExercRP = 2004 THEN
        Sequencia := FazerLancamento( '212110200021100' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF ExercRP = 2005 THEN
        Sequencia := FazerLancamento( '212110200021200' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF   ExercRP  =  2006 THEN
        Sequencia := FazerLancamento(  '212110200021300' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
    END IF;
    IF   ExercRP  =  2007 THEN
        Sequencia := FazerLancamento(  '212110200021400' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
    END IF;
    IF   ExercRP  =  2008 THEN
        Sequencia := FazerLancamento(  '212110200021500' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
    END IF;
    IF   ExercRP  =  2009 THEN
        Sequencia := FazerLancamento(  '212110200021600' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
    END IF;
    IF   ExercRP  =  2010 THEN
        Sequencia := FazerLancamento(  '212110200021700' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
    END IF;
END IF;
IF Restos = 'Legislativo' THEN
    IF ExercRP = 1994 THEN
        Sequencia := FazerLancamento( '212110200030100' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF ExercRP = 1995 THEN
        Sequencia := FazerLancamento( '212110200030200' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF ExercRP = 1996 THEN
        Sequencia := FazerLancamento( '212110200030300' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF ExercRP = 1997 THEN
        Sequencia := FazerLancamento( '212110200030400' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF ExercRP = 1998 THEN
        Sequencia := FazerLancamento( '212110200030500' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF ExercRP = 1999 THEN
        Sequencia := FazerLancamento( '212110200030600' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF ExercRP = 2000 THEN
        Sequencia := FazerLancamento( '212110200030700' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF ExercRP = 2001 THEN
        Sequencia := FazerLancamento( '212110200030800' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF Exercicio = 2002 THEN
        Sequencia := FazerLancamento( '212110200030900' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF ExercRP = 2003 THEN
        Sequencia := FazerLancamento( '212110200031000' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF ExercRP = 2004 THEN
        Sequencia := FazerLancamento( '212110200031100' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF ExercRP = 2005 THEN
        Sequencia := FazerLancamento( '212110200031200' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF   ExercRP  =  2006 THEN
        Sequencia := FazerLancamento(  '212110200031300' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
    END IF;
    IF   ExercRP  =  2007 THEN
        Sequencia := FazerLancamento(  '212110200031400' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
    END IF;
    IF   ExercRP  =  2008 THEN
        Sequencia := FazerLancamento(  '212110200031500' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
    END IF;
    IF   ExercRP  =  2009 THEN
        Sequencia := FazerLancamento(  '212110200031600' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
    END IF;
    IF   ExercRP  =  2010 THEN
        Sequencia := FazerLancamento(  '212110200031700' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
    END IF;
END IF;
IF Restos = 'RPPS' THEN
    IF ExercRP = 1994 THEN
        Sequencia := FazerLancamento( '212110200040100' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF ExercRP = 1995 THEN
        Sequencia := FazerLancamento( '212110200040200' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF ExercRP = 1996 THEN
        Sequencia := FazerLancamento( '212110200040300' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF ExercRP = 1997 THEN
        Sequencia := FazerLancamento( '212110200040400' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF ExercRP = 1998 THEN
        Sequencia := FazerLancamento( '212110200040500' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF ExercRP = 1999 THEN
        Sequencia := FazerLancamento( '212110200040600' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF ExercRP = 2000 THEN
        Sequencia := FazerLancamento( '212110200040700' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF ExercRP = 2001 THEN
        Sequencia := FazerLancamento( '212110200040800' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF ExercRP = 2002 THEN
        Sequencia := FazerLancamento( '212110200040900' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF ExercRP = 2003 THEN
        Sequencia := FazerLancamento( '212110200041000' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF ExercRP = 2004 THEN
        Sequencia := FazerLancamento( '212110200041100' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF ExercRP = 2005 THEN
        Sequencia := FazerLancamento( '212110200041200' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade );
    END IF;
    IF   ExercRP  =  2006 THEN
        Sequencia := FazerLancamento(  '212110200041300' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
    END IF;
    IF   ExercRP  =  2007 THEN
        Sequencia := FazerLancamento(  '212110200041400' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
    END IF;
    IF   ExercRP  =  2008 THEN
        Sequencia := FazerLancamento(  '212110200041500' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
    END IF;
    IF   ExercRP  =  2009 THEN
        Sequencia := FazerLancamento(  '212110200041600' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
    END IF;
    IF   ExercRP  =  2010 THEN
        Sequencia := FazerLancamento(  '212110200041700' , ContaPG , 917 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
    END IF;
END IF;
RETURN Sequencia;
END;
$$ LANGUAGE 'plpgsql';

