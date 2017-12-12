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
* Versão 2.00.0
*/

----------------
-- Ticket #17737
----------------

UPDATE administracao.atributo_valor_padrao SET ativo = FALSE WHERE cod_atributo = 101 and cod_cadastro = 1 and cod_modulo = 10 and cod_valor = 10;

INSERT INTO administracao.atributo_valor_padrao (
    cod_modulo,
    cod_cadastro,
    cod_atributo,
    cod_valor,
    ativo,
    valor_padrao
 ) VALUES (
    10,
    1,
    101,
    (SELECT  (max(cod_valor) + 1) from  administracao.atributo_valor_padrao  where cod_modulo = 10 and cod_atributo = 101 and cod_cadastro = 1),
    TRUE,
    'Pregão Presencial'
);

INSERT INTO administracao.atributo_valor_padrao (
    cod_modulo,
    cod_cadastro,
    cod_atributo,
    cod_valor,
    ativo,
    valor_padrao
 ) VALUES (
    10,
    1,
    101,
    (SELECT  (max(cod_valor) + 1) from  administracao.atributo_valor_padrao  where cod_modulo = 10 and cod_atributo = 101 and cod_cadastro = 1),
    TRUE,
    'Pregão Eletrônico'
);

INSERT INTO administracao.atributo_valor_padrao (
    cod_modulo,
    cod_cadastro,
    cod_atributo,
    cod_valor,
    ativo,
    valor_padrao
 ) VALUES (
    10,
    1,
    101,
    (SELECT  (max(cod_valor) + 1) from  administracao.atributo_valor_padrao  where cod_modulo = 10 and cod_atributo = 101 and cod_cadastro = 1),
    TRUE,
    'Chamada Pública'
);

INSERT INTO administracao.atributo_valor_padrao (
    cod_modulo,
    cod_cadastro,
    cod_atributo,
    cod_valor,
    ativo,
    valor_padrao
 ) VALUES (
    10,
    1,
    101,
    (SELECT  (max(cod_valor) + 1) from  administracao.atributo_valor_padrao  where cod_modulo = 10 and cod_atributo = 101 and cod_cadastro = 1),
    TRUE,
    'Registro de Preços'
);

INSERT INTO contabilidade.plano_conta (
    cod_conta,
    exercicio,
    nom_conta,
    cod_classificacao,
    cod_sistema,
    cod_estrutural
)VALUES(
    (SELECT (max(cod_conta) + 1) from contabilidade.plano_conta) ,
    '2011',
    'CHAMADA PÚBLICA',
    3 ,
    1 ,
    '1.9.2.4.1.02.13.00.00.00'
);

INSERT INTO contabilidade.classificacao_plano (
    cod_classificacao,
    exercicio,
    cod_conta,
    cod_posicao
)VALUES(
    1 ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    1 
);

INSERT INTO contabilidade.classificacao_plano (
    cod_classificacao,
    exercicio,
    cod_conta,
    cod_posicao
)VALUES(
    9 ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    2 
);

INSERT INTO contabilidade.classificacao_plano (
    cod_classificacao,
    exercicio,
    cod_conta,
    cod_posicao
)VALUES(
    2 ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    3 
);

INSERT INTO contabilidade.classificacao_plano (
    cod_classificacao,
    exercicio,
    cod_conta,
    cod_posicao
)VALUES(
    4 ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    4 
);

INSERT INTO contabilidade.classificacao_plano (
    cod_classificacao,
    exercicio,
    cod_conta,
    cod_posicao
)VALUES(
    1 ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    5 
);

INSERT INTO contabilidade.classificacao_plano (
    cod_classificacao,
    exercicio,
    cod_conta,
    cod_posicao
)VALUES(
    02 ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    6 
);

INSERT INTO contabilidade.classificacao_plano (
    cod_classificacao,
    exercicio,
    cod_conta,
    cod_posicao
)VALUES(
    13 ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    7 
);

INSERT INTO contabilidade.classificacao_plano (
    cod_classificacao,
    exercicio,
    cod_conta,
    cod_posicao
)VALUES(
    00 ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    8 
);

INSERT INTO contabilidade.classificacao_plano (
    cod_classificacao,
    exercicio,
    cod_conta,
    cod_posicao
)VALUES(
    00 ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    9 
);

INSERT INTO contabilidade.classificacao_plano (
    cod_classificacao,
    exercicio,
    cod_conta,
    cod_posicao
)VALUES(
    00 ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    10 
);

INSERT INTO contabilidade.plano_analitica (
    cod_plano,
    exercicio,
    cod_conta,
    natureza_saldo
)VALUES(
    (SELECT max(cod_plano) + 1 from contabilidade.plano_analitica) ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    'D'
);

INSERT INTO contabilidade.plano_conta (
    cod_conta,
    exercicio,
    nom_conta,
    cod_classificacao,
    cod_sistema,
    cod_estrutural
)VALUES(
    (SELECT (max(cod_conta) + 1) from contabilidade.plano_conta) ,
    '2011',
    'CHAMADA PÚBLICA',
    3 ,
    1 ,
    '2.9.2.4.1.02.13.00.00.00'
);

INSERT INTO contabilidade.classificacao_plano (
    cod_classificacao,
    exercicio,
    cod_conta,
    cod_posicao
)VALUES(
    2 ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    1
);

INSERT INTO contabilidade.classificacao_plano (
    cod_classificacao,
    exercicio,
    cod_conta,
    cod_posicao
)VALUES(
    9 ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    2
);

INSERT INTO contabilidade.classificacao_plano (
    cod_classificacao,
    exercicio,
    cod_conta,
    cod_posicao
)VALUES(
    2 ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    3
);

INSERT INTO contabilidade.classificacao_plano (
    cod_classificacao,
    exercicio,
    cod_conta,
    cod_posicao
)VALUES(
    4 ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    4
);

INSERT INTO contabilidade.classificacao_plano (
    cod_classificacao,
    exercicio,
    cod_conta,
    cod_posicao
)VALUES(
    1 ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    5
);

INSERT INTO contabilidade.classificacao_plano (
    cod_classificacao,
    exercicio,
    cod_conta,
    cod_posicao
)VALUES(
    02 ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    6
);

INSERT INTO contabilidade.classificacao_plano (
    cod_classificacao,
    exercicio,
    cod_conta,
    cod_posicao
)VALUES(
    13 ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    7
);

INSERT INTO contabilidade.classificacao_plano (
    cod_classificacao,
    exercicio,
    cod_conta,
    cod_posicao
)VALUES(
    00 ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    8 
);

INSERT INTO contabilidade.classificacao_plano (
    cod_classificacao,
    exercicio,
    cod_conta,
    cod_posicao
)VALUES(
    00 ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    9 
);

INSERT INTO contabilidade.classificacao_plano (
    cod_classificacao,
    exercicio,
    cod_conta,
    cod_posicao
)VALUES(
    00 ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    10 
);

INSERT INTO contabilidade.plano_analitica (
    cod_plano,
    exercicio,
    cod_conta,
    natureza_saldo
)VALUES(
    (SELECT max(cod_plano) + 1 from contabilidade.plano_analitica) ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    'D'
);

INSERT INTO contabilidade.plano_conta (
    cod_conta,
    exercicio,
    nom_conta,
    cod_classificacao,
    cod_sistema,
    cod_estrutural
)VALUES(
    (SELECT (max(cod_conta) + 1) from contabilidade.plano_conta) ,
    '2011',
    'CHAMADA PÚBLICA',
    3 ,
    1 ,
    '2.9.2.4.1.03.13.00.00.00'
);

INSERT INTO contabilidade.classificacao_plano (
    cod_classificacao,
    exercicio,
    cod_conta,
    cod_posicao
)VALUES(
    2 ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    1
);

INSERT INTO contabilidade.classificacao_plano (
    cod_classificacao,
    exercicio,
    cod_conta,
    cod_posicao
)VALUES(
    9 ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    2
);

INSERT INTO contabilidade.classificacao_plano (
    cod_classificacao,
    exercicio,
    cod_conta,
    cod_posicao
)VALUES(
    2 ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    3
);

INSERT INTO contabilidade.classificacao_plano (
    cod_classificacao,
    exercicio,
    cod_conta,
    cod_posicao
)VALUES(
    4 ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    4
);

INSERT INTO contabilidade.classificacao_plano (
    cod_classificacao,
    exercicio,
    cod_conta,
    cod_posicao
)VALUES(
    1 ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    5
);

INSERT INTO contabilidade.classificacao_plano (
    cod_classificacao,
    exercicio,
    cod_conta,
    cod_posicao
)VALUES(
    03 ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    6
);

INSERT INTO contabilidade.classificacao_plano (
    cod_classificacao,
    exercicio,
    cod_conta,
    cod_posicao
)VALUES(
    13 ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    7
);

INSERT INTO contabilidade.classificacao_plano (
    cod_classificacao,
    exercicio,
    cod_conta,
    cod_posicao
)VALUES(
    00 ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    8 
);

INSERT INTO contabilidade.classificacao_plano (
    cod_classificacao,
    exercicio,
    cod_conta,
    cod_posicao
)VALUES(
    00 ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    9 
);

INSERT INTO contabilidade.classificacao_plano (
    cod_classificacao,
    exercicio,
    cod_conta,
    cod_posicao
)VALUES(
    00 ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    10 
);

INSERT INTO contabilidade.plano_analitica (
    cod_plano,
    exercicio,
    cod_conta,
    natureza_saldo
)VALUES(
    (SELECT max(cod_plano) + 1 from contabilidade.plano_analitica) ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    'D'
);

INSERT INTO contabilidade.plano_conta (
    cod_conta,
    exercicio,
    nom_conta,
    cod_classificacao,
    cod_sistema,
    cod_estrutural
)VALUES(
    (SELECT (max(cod_conta) + 1) from contabilidade.plano_conta) ,
    '2011',
    'REGISTRO DE PREÇOS',
    3 ,
    1 ,
    '1.9.2.4.1.02.14.00.00.00'
);

INSERT INTO contabilidade.classificacao_plano (
    cod_classificacao,
    exercicio,
    cod_conta,
    cod_posicao
)VALUES(
    1 ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    1
);

INSERT INTO contabilidade.classificacao_plano (
    cod_classificacao,
    exercicio,
    cod_conta,
    cod_posicao
)VALUES(
    9 ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    2
);

INSERT INTO contabilidade.classificacao_plano (
    cod_classificacao,
    exercicio,
    cod_conta,
    cod_posicao
)VALUES(
    2 ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    3
);

INSERT INTO contabilidade.classificacao_plano (
    cod_classificacao,
    exercicio,
    cod_conta,
    cod_posicao
)VALUES(
    4 ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    4
);

INSERT INTO contabilidade.classificacao_plano (
    cod_classificacao,
    exercicio,
    cod_conta,
    cod_posicao
)VALUES(
    1 ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    5
);

INSERT INTO contabilidade.classificacao_plano (
    cod_classificacao,
    exercicio,
    cod_conta,
    cod_posicao
)VALUES(
    02 ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    6
);

INSERT INTO contabilidade.classificacao_plano (
    cod_classificacao,
    exercicio,
    cod_conta,
    cod_posicao
)VALUES(
    14 ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    7
);

INSERT INTO contabilidade.classificacao_plano (
    cod_classificacao,
    exercicio,
    cod_conta,
    cod_posicao
)VALUES(
    00 ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    8
);

INSERT INTO contabilidade.classificacao_plano (
    cod_classificacao,
    exercicio,
    cod_conta,
    cod_posicao
)VALUES(
    00 ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    9
);

INSERT INTO contabilidade.classificacao_plano (
    cod_classificacao,
    exercicio,
    cod_conta,
    cod_posicao
)VALUES(
    00 ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    10
);

INSERT INTO contabilidade.plano_analitica (
    cod_plano,
    exercicio,
    cod_conta,
    natureza_saldo
)VALUES(
    (SELECT max(cod_plano) + 1 from contabilidade.plano_analitica) ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    'D'
);

INSERT INTO contabilidade.plano_conta (
    cod_conta,
    exercicio,
    nom_conta,
    cod_classificacao,
    cod_sistema,
    cod_estrutural
)VALUES(
    (SELECT (max(cod_conta) + 1) from contabilidade.plano_conta) ,
    '2011',
    'REGISTRO DE PREÇOS',
    3 ,
    1 ,
    '2.9.2.4.1.02.14.00.00.00'
);

INSERT INTO contabilidade.classificacao_plano (
    cod_classificacao,
    exercicio,
    cod_conta,
    cod_posicao
)VALUES(
    2 ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    1
);

INSERT INTO contabilidade.classificacao_plano (
    cod_classificacao,
    exercicio,
    cod_conta,
    cod_posicao
)VALUES(
    9 ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    2
);

INSERT INTO contabilidade.classificacao_plano (
    cod_classificacao,
    exercicio,
    cod_conta,
    cod_posicao
)VALUES(
    2 ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    3
);

INSERT INTO contabilidade.classificacao_plano (
    cod_classificacao,
    exercicio,
    cod_conta,
    cod_posicao
)VALUES(
    4 ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    4
);

INSERT INTO contabilidade.classificacao_plano (
    cod_classificacao,
    exercicio,
    cod_conta,
    cod_posicao
)VALUES(
    1 ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    5
);

INSERT INTO contabilidade.classificacao_plano (
    cod_classificacao,
    exercicio,
    cod_conta,
    cod_posicao
)VALUES(
    02 ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    6
);

INSERT INTO contabilidade.classificacao_plano (
    cod_classificacao,
    exercicio,
    cod_conta,
    cod_posicao
)VALUES(
    14 ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    7
);

INSERT INTO contabilidade.classificacao_plano (
    cod_classificacao,
    exercicio,
    cod_conta,
    cod_posicao
)VALUES(
    00 ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    8
);

INSERT INTO contabilidade.classificacao_plano (
    cod_classificacao,
    exercicio,
    cod_conta,
    cod_posicao
)VALUES(
    00 ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    9
);

INSERT INTO contabilidade.classificacao_plano (
    cod_classificacao,
    exercicio,
    cod_conta,
    cod_posicao
)VALUES(
    00 ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    10
);

INSERT INTO contabilidade.plano_analitica (
    cod_plano,
    exercicio,
    cod_conta,
    natureza_saldo
)VALUES(
    (SELECT max(cod_plano) + 1 from contabilidade.plano_analitica) ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    'D'
);

INSERT INTO contabilidade.plano_conta (
    cod_conta,
    exercicio,
    nom_conta,
    cod_classificacao,
    cod_sistema,
    cod_estrutural
)VALUES(
    (SELECT (max(cod_conta) + 1) from contabilidade.plano_conta) ,
    '2011',
    'REGISTRO DE PREÇOS',
    3 ,
    1 ,
    '2.9.2.4.1.03.14.00.00.00'
);

INSERT INTO contabilidade.classificacao_plano (
    cod_classificacao,
    exercicio,
    cod_conta,
    cod_posicao
)VALUES(
    2 ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    1
);

INSERT INTO contabilidade.classificacao_plano (
    cod_classificacao,
    exercicio,
    cod_conta,
    cod_posicao
)VALUES(
    9 ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    2
);

INSERT INTO contabilidade.classificacao_plano (
    cod_classificacao,
    exercicio,
    cod_conta,
    cod_posicao
)VALUES(
    2 ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    3
);

INSERT INTO contabilidade.classificacao_plano (
    cod_classificacao,
    exercicio,
    cod_conta,
    cod_posicao
)VALUES(
    4 ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    4
);

INSERT INTO contabilidade.classificacao_plano (
    cod_classificacao,
    exercicio,
    cod_conta,
    cod_posicao
)VALUES(
    1 ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    5
);

INSERT INTO contabilidade.classificacao_plano (
    cod_classificacao,
    exercicio,
    cod_conta,
    cod_posicao
)VALUES(
    03 ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    6
);

INSERT INTO contabilidade.classificacao_plano (
    cod_classificacao,
    exercicio,
    cod_conta,
    cod_posicao
)VALUES(
    14 ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    7
);

INSERT INTO contabilidade.classificacao_plano (
    cod_classificacao,
    exercicio,
    cod_conta,
    cod_posicao
)VALUES(
    00 ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    8
);

INSERT INTO contabilidade.classificacao_plano (
    cod_classificacao,
    exercicio,
    cod_conta,
    cod_posicao
)VALUES(
    00 ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    9
);

INSERT INTO contabilidade.classificacao_plano (
    cod_classificacao,
    exercicio,
    cod_conta,
    cod_posicao
)VALUES(
    00 ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    10
);

INSERT INTO contabilidade.plano_analitica (
    cod_plano,
    exercicio,
    cod_conta,
    natureza_saldo
)VALUES(
    (SELECT max(cod_plano) + 1 from contabilidade.plano_analitica) ,
    '2011',
    (SELECT max(cod_conta) from contabilidade.plano_conta) ,
    'D'
);


DELETE FROM administracao.parametro             WHERE cod_funcao = 101 AND cod_modulo = 10 AND cod_biblioteca = 1;
DELETE FROM administracao.variavel              WHERE cod_funcao = 101 AND cod_modulo = 10 AND cod_biblioteca = 1;
DELETE FROM administracao.corpo_funcao_externa  WHERE cod_funcao = 101 AND cod_modulo = 10 AND cod_biblioteca = 1;
DELETE FROM administracao.funcao_externa        WHERE cod_funcao = 101 AND cod_modulo = 10 AND cod_biblioteca = 1;

INSERT INTO administracao.funcao_externa
                  ( cod_modulo
                  , cod_biblioteca
                  , cod_funcao
                  , comentario
                  , corpo_pl
                  , corpo_ln)
            VALUES (10,1,101,'','FUNCTION EmpenhoEmissaoModalidadesLicitacao(VARCHAR,NUMERIC,VARCHAR,INTEGER,VARCHAR,INTEGER,INTEGER) RETURNS INTEGER as ''
   DECLARE
   EXERCICIO ALIAS FOR $1;
   VALOR ALIAS FOR $2;
   COMPLEMENTO ALIAS FOR $3;
   CODLOTE ALIAS FOR $4;
   TIPOLOTE ALIAS FOR $5;
   CODENTIDADE ALIAS FOR $6;
   CODPREEMPENHO ALIAS FOR $7;

   MODALIDADE VARCHAR := '''''''';
   SEQUENCIA INTEGER;
   BEGIN
   MODALIDADE := PEGAEMPENHOEMPENHOMODALIDADE(  EXERCICIO , CODPREEMPENHO  );
   IF   MODALIDADE  =  ''''Concurso'''' THEN
      SEQUENCIA := FAZERLANCAMENTO(  ''''192410201000000'''' , ''''292410101000000'''' , 901 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
      SEQUENCIA := FAZERLANCAMENTO(  ''''292419900000000'''' , ''''292410201000000'''' , 901 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
   END IF;
   IF   MODALIDADE  =  ''''Convite'''' THEN
      SEQUENCIA := FAZERLANCAMENTO(  ''''192410202000000'''' , ''''292410101000000'''' , 901 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
      SEQUENCIA := FAZERLANCAMENTO(  ''''292419900000000'''' , ''''292410202000000'''' , 901 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
   END IF;
   IF   MODALIDADE  =  ''''Tomada'''' THEN
      SEQUENCIA := FAZERLANCAMENTO(  ''''192410203000000'''' , ''''292410101000000'''' , 901 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
      SEQUENCIA := FAZERLANCAMENTO(  ''''292419900000000'''' , ''''292410203000000'''' , 901 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
   END IF;
   IF   MODALIDADE  =  ''''Concorrência'''' THEN
      SEQUENCIA := FAZERLANCAMENTO(  ''''192410204000000'''' , ''''292410101000000'''' , 901 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
      SEQUENCIA := FAZERLANCAMENTO(  ''''292419900000000'''' , ''''292410204000000'''' , 901 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
   END IF;
   IF   MODALIDADE  =  ''''Dispensa'''' THEN
      SEQUENCIA := FAZERLANCAMENTO(  ''''192410206000000'''' , ''''292410101000000'''' , 901 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
      SEQUENCIA := FAZERLANCAMENTO(  ''''292419900000000'''' , ''''292410206000000'''' , 901 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
   END IF;
   IF   MODALIDADE  =  ''''Inexigível'''' THEN
      SEQUENCIA := FAZERLANCAMENTO(  ''''192410207000000'''' , ''''292410101000000'''' , 901 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
      SEQUENCIA := FAZERLANCAMENTO(  ''''292419900000000'''' , ''''292410207000000'''' , 901 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
   END IF;
   IF   MODALIDADE  =  ''''Não Aplicável'''' THEN
      SEQUENCIA := FAZERLANCAMENTO(  ''''192410208000000'''' , ''''292410101000000'''' , 901 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
      SEQUENCIA := FAZERLANCAMENTO(  ''''292419900000000'''' , ''''292410208000000'''' , 901 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
   END IF;
   IF   MODALIDADE  =  ''''Suprimentos'''' THEN
      SEQUENCIA := FAZERLANCAMENTO(  ''''192410209000000'''' , ''''292410101000000'''' , 901 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
      SEQUENCIA := FAZERLANCAMENTO(  ''''292419900000000'''' , ''''292410209000000'''' , 901 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
   END IF;
   IF     MODALIDADE  =  ''''Integração'''' THEN
      SEQUENCIA := FAZERLANCAMENTO(  ''''192410210000000'''' , ''''292410101000000'''' , 901 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
      SEQUENCIA := FAZERLANCAMENTO(  ''''292419900000000'''' , ''''292410210000000'''' , 901 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
   END IF;
   IF   MODALIDADE  =  ''''Pregão'''' THEN
      SEQUENCIA := FAZERLANCAMENTO(  ''''192410212000000'''' , ''''292410101000000'''' , 901 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
      SEQUENCIA := FAZERLANCAMENTO(  ''''292419900000000'''' , ''''292410212000000'''' , 901 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
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
               VALUES (10,1,101,1,'0','#Modalidade <- pegaEmpenhoEmpenhoModalidade(  #Exercicio , #CodPreEmpenho  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,101,2,'1','SE   #Modalidade  =  "Concurso" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,101,3,'1','#Sequencia <- FazerLancamento(  "192410201000000" , "292410101000000" , 901 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,101,4,'1','#Sequencia <- FazerLancamento(  "292419900000000" , "292410201000000" , 901 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,101,5,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,101,6,'1','SE   #Modalidade  =  "Convite" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,101,7,'1','#Sequencia <- FazerLancamento(  "192410202000000" , "292410101000000" , 901 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,101,8,'1','#Sequencia <- FazerLancamento(  "292419900000000" , "292410202000000" , 901 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,101,9,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,101,10,'1','SE   #Modalidade  =  "Tomada" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,101,11,'1','#Sequencia <- FazerLancamento(  "192410203000000" , "292410101000000" , 901 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,101,12,'1','#Sequencia <- FazerLancamento(  "292419900000000" , "292410203000000" , 901 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,101,13,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,101,14,'1','SE   #Modalidade  =  "Concorrência" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,101,15,'1','#Sequencia <- FazerLancamento(  "192410204000000" , "292410101000000" , 901 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,101,16,'1','#Sequencia <- FazerLancamento(  "292419900000000" , "292410204000000" , 901 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,101,17,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,101,18,'1','SE   #Modalidade  =  "Dispensa" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,101,19,'1','#Sequencia <- FazerLancamento(  "192410206000000" , "292410101000000" , 901 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,101,20,'1','#Sequencia <- FazerLancamento(  "292419900000000" , "292410206000000" , 901 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,101,21,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,101,22,'1','SE   #Modalidade  =  "Inexigível" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,101,23,'1','#Sequencia <- FazerLancamento(  "192410207000000" , "292410101000000" , 901 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,101,24,'1','#Sequencia <- FazerLancamento(  "292419900000000" , "292410207000000" , 901 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,101,25,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,101,26,'1','SE   #Modalidade  =  "Não Aplicável" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,101,27,'1','#Sequencia <- FazerLancamento(  "192410208000000" , "292410101000000" , 901 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,101,28,'1','#Sequencia <- FazerLancamento(  "292419900000000" , "292410208000000" , 901 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,101,29,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,101,30,'1','SE   #Modalidade  =  "Suprimentos" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,101,31,'1','#Sequencia <- FazerLancamento(  "192410209000000" , "292410101000000" , 901 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,101,32,'1','#Sequencia <- FazerLancamento(  "292419900000000" , "292410209000000" , 901 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,101,33,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,101,34,'1','SE     #Modalidade  =  "Integração" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,101,35,'1','#Sequencia <- FazerLancamento(  "192410210000000" , "292410101000000" , 901 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,101,36,'1','#Sequencia <- FazerLancamento(  "292419900000000" , "292410210000000" , 901 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,101,37,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,101,38,'1','SE   #Modalidade  =  "Pregão" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,101,39,'1','#Sequencia <- FazerLancamento(  "192410212000000" , "292410101000000" , 901 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,101,40,'1','#Sequencia <- FazerLancamento(  "292419900000000" , "292410212000000" , 901 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,101,41,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,101,1001,'1','SE   #Modalidade  =  "Pregão Presencial" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,101,1002,'1','#Sequencia <- FazerLancamento(  "192410212000000" , "292410101000000" , 901 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,101,1003,'1','#Sequencia <- FazerLancamento(  "292419900000000" , "292410212000000" , 901 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,101,1004,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,101,1005,'1','SE   #Modalidade  =  "Pregão Eletrônico" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,101,1006,'1','#Sequencia <- FazerLancamento(  "192410212000000" , "292410101000000" , 901 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,101,1007,'1','#Sequencia <- FazerLancamento(  "292419900000000" , "292410212000000" , 901 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,101,1008,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,101,1009,'1','SE   #Modalidade  =  "Chamada Pública" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,101,1010,'1','#Sequencia <- FazerLancamento(  "192410213000000" , "292410101000000" , 901 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,101,1011,'1','#Sequencia <- FazerLancamento(  "292419900000000" , "292410213000000" , 901 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,101,1012,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,101,1013,'1','SE   #Modalidade  =  "Registro de Preços" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,101,1014,'1','#Sequencia <- FazerLancamento(  "192410214000000" , "292410101000000" , 901 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,101,1015,'1','#Sequencia <- FazerLancamento(  "292419900000000" , "292410214000000" , 901 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,101,1016,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,101,1017,'0','RETORNA #Sequencia');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,101,1,2,'Modalidade','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,101,2,1,'Sequencia','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,101,3,2,'Exercicio','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,101,4,4,'Valor','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,101,5,2,'Complemento','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,101,6,1,'CodLote','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,101,7,2,'TipoLote','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,101,8,1,'CodEntidade','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,101,9,1,'CodPreEmpenho','');
INSERT INTO administracao.parametro
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , ordem)
               VALUES (10,1,101,3,0);
INSERT INTO administracao.parametro
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , ordem)
               VALUES (10,1,101,4,1);
INSERT INTO administracao.parametro
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , ordem)
               VALUES (10,1,101,5,2);
INSERT INTO administracao.parametro
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , ordem)
               VALUES (10,1,101,6,3);
INSERT INTO administracao.parametro
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , ordem)
               VALUES (10,1,101,7,4);
INSERT INTO administracao.parametro
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , ordem)
               VALUES (10,1,101,8,5);
INSERT INTO administracao.parametro
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , ordem)
               VALUES (10,1,101,9,6);


CREATE OR REPLACE FUNCTION EmpenhoEmissaoModalidadesLicitacao(VARCHAR,NUMERIC,VARCHAR,INTEGER,VARCHAR,INTEGER,INTEGER) RETURNS INTEGER as $$
DECLARE
    EXERCICIO ALIAS FOR $1;
    VALOR ALIAS FOR $2;
    COMPLEMENTO ALIAS FOR $3;
    CODLOTE ALIAS FOR $4;
    TIPOLOTE ALIAS FOR $5;
    CODENTIDADE ALIAS FOR $6;
    CODPREEMPENHO ALIAS FOR $7;

    MODALIDADE VARCHAR := '';
    SEQUENCIA INTEGER;
    BEGIN
    MODALIDADE := PEGAEMPENHOEMPENHOMODALIDADE(  EXERCICIO , CODPREEMPENHO  );
    IF   MODALIDADE  =  'Concurso' THEN
       SEQUENCIA := FAZERLANCAMENTO(  '192410201000000' , '292410101000000' , 901 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
       SEQUENCIA := FAZERLANCAMENTO(  '292419900000000' , '292410201000000' , 901 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
    END IF;
    IF   MODALIDADE  =  'Convite' THEN
       SEQUENCIA := FAZERLANCAMENTO(  '192410202000000' , '292410101000000' , 901 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
       SEQUENCIA := FAZERLANCAMENTO(  '292419900000000' , '292410202000000' , 901 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
    END IF;
    IF   MODALIDADE  =  'Tomada' THEN
       SEQUENCIA := FAZERLANCAMENTO(  '192410203000000' , '292410101000000' , 901 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
       SEQUENCIA := FAZERLANCAMENTO(  '292419900000000' , '292410203000000' , 901 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
    END IF;
    IF   MODALIDADE  =  'Concorrência' THEN
       SEQUENCIA := FAZERLANCAMENTO(  '192410204000000' , '292410101000000' , 901 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
       SEQUENCIA := FAZERLANCAMENTO(  '292419900000000' , '292410204000000' , 901 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
    END IF;
    IF   MODALIDADE  =  'Dispensa' THEN
       SEQUENCIA := FAZERLANCAMENTO(  '192410206000000' , '292410101000000' , 901 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
       SEQUENCIA := FAZERLANCAMENTO(  '292419900000000' , '292410206000000' , 901 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
    END IF;
    IF   MODALIDADE  =  'Inexigível' THEN
       SEQUENCIA := FAZERLANCAMENTO(  '192410207000000' , '292410101000000' , 901 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
       SEQUENCIA := FAZERLANCAMENTO(  '292419900000000' , '292410207000000' , 901 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
    END IF;
    IF   MODALIDADE  =  'Não Aplicável' THEN
       SEQUENCIA := FAZERLANCAMENTO(  '192410208000000' , '292410101000000' , 901 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
       SEQUENCIA := FAZERLANCAMENTO(  '292419900000000' , '292410208000000' , 901 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
    END IF;
    IF   MODALIDADE  =  'Suprimentos' THEN
       SEQUENCIA := FAZERLANCAMENTO(  '192410209000000' , '292410101000000' , 901 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
       SEQUENCIA := FAZERLANCAMENTO(  '292419900000000' , '292410209000000' , 901 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
    END IF;
    IF   MODALIDADE  =  'Integração' THEN
       SEQUENCIA := FAZERLANCAMENTO(  '192410210000000' , '292410101000000' , 901 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
       SEQUENCIA := FAZERLANCAMENTO(  '292419900000000' , '292410210000000' , 901 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
    END IF;
    IF   MODALIDADE  =  'Pregão' THEN
       SEQUENCIA := FAZERLANCAMENTO(  '192410212000000' , '292410101000000' , 901 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
       SEQUENCIA := FAZERLANCAMENTO(  '292419900000000' , '292410212000000' , 901 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
    END IF;
    IF   MODALIDADE  =  'Pregão Presencial' THEN
       SEQUENCIA := FAZERLANCAMENTO(  '192410212000000' , '292410101000000' , 901 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
       SEQUENCIA := FAZERLANCAMENTO(  '292419900000000' , '292410212000000' , 901 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
    END IF;
    IF   MODALIDADE  =  'Pregão Eletrônico' THEN
       SEQUENCIA := FAZERLANCAMENTO(  '192410212000000' , '292410101000000' , 901 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
       SEQUENCIA := FAZERLANCAMENTO(  '292419900000000' , '292410212000000' , 901 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
    END IF;
    IF   MODALIDADE  =  'Chamada Pública' THEN
       SEQUENCIA := FAZERLANCAMENTO(  '192410213000000' , '292410101000000' , 901 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
       SEQUENCIA := FAZERLANCAMENTO(  '292419900000000' , '292410213000000' , 901 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
    END IF;
    IF   MODALIDADE  =  'Registro de Preços' THEN
       SEQUENCIA := FAZERLANCAMENTO(  '192410214000000' , '292410101000000' , 901 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
       SEQUENCIA := FAZERLANCAMENTO(  '292419900000000' , '292410214000000' , 901 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
    END IF;
raise notice '%', MODALIDADE;
    RETURN SEQUENCIA;
    END;
$$ LANGUAGE 'plpgsql';


-- ALTERANDO FUNCOES EmpenhoAnulacaoLiquidacaoModalidadesLicitacao, EmpenhoLiquidacaoModalidadesLicitacao E EmpenhoModalidadesLicitacao

DELETE FROM administracao.parametro             WHERE cod_funcao IN (110, 102, 108) AND cod_modulo = 10 AND cod_biblioteca = 1;
DELETE FROM administracao.variavel              WHERE cod_funcao IN (110, 102, 108) AND cod_modulo = 10 AND cod_biblioteca = 1;
DELETE FROM administracao.corpo_funcao_externa  WHERE cod_funcao IN (110, 102, 108) AND cod_modulo = 10 AND cod_biblioteca = 1;
DELETE FROM administracao.funcao_externa        WHERE cod_funcao IN (110, 102, 108) AND cod_modulo = 10 AND cod_biblioteca = 1;

INSERT INTO administracao.funcao_externa
                  (cod_modulo
                  , cod_biblioteca
                  , cod_funcao
                  , comentario
                  , corpo_pl
                  , corpo_ln)
            VALUES (10,1,110,'','FUNCTION EmpenhoAnulacaoLiquidacaoModalidadesLicitacao(VARCHAR,NUMERIC,VARCHAR,INTEGER,VARCHAR,INTEGER,INTEGER) RETURNS INTEGER as \\'' 
DECLARE
Exercicio ALIAS FOR $1;
Valor ALIAS FOR $2;
Complemento ALIAS FOR $3;
CodLote ALIAS FOR $4;
TipoLote ALIAS FOR $5;
CodEntidade ALIAS FOR $6;
CodNota ALIAS FOR $7;

  Modalidade VARCHAR := \\''\\''\\''\\'';
  Sequencia INTEGER;
BEGIN
Modalidade := pegaEmpenhoLiquidacaoModalidade(  Exercicio , CodNota , CodEntidade  ); 
IF   Modalidade  =  \\''\\''Concurso\\''\\'' THEN
    Sequencia := FazerLancamento(  \\''\\''292410301000000\\''\\'' , \\''\\''292410201000000\\''\\'' , 905 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
END IF;
IF   Modalidade  =  \\''\\''Convite\\''\\'' THEN
    Sequencia := FazerLancamento(  \\''\\''292410302000000\\''\\'' , \\''\\''292410202000000\\''\\'' , 905 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
END IF;
IF   Modalidade  =  \\''\\''Tomada\\''\\'' THEN
    Sequencia := FazerLancamento(  \\''\\''292410303000000\\''\\'' , \\''\\''292410203000000\\''\\'' , 905 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
END IF;
IF   Modalidade  =  \\''\\''Concorrência\\''\\'' THEN
    Sequencia := FazerLancamento(  \\''\\''292410304000000\\''\\'' , \\''\\''292410204000000\\''\\'' , 905 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
END IF;
IF   Modalidade  =  \\''\\''Dispensa\\''\\'' THEN
    Sequencia := FazerLancamento(  \\''\\''292410306000000\\''\\'' , \\''\\''292410206000000\\''\\'' , 905 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
END IF;
IF   Modalidade  =  \\''\\''Inexigível\\''\\'' THEN
    Sequencia := FazerLancamento(  \\''\\''292410307000000\\''\\'' , \\''\\''292410207000000\\''\\'' , 905 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
END IF;
IF   Modalidade  =  \\''\\''Não Aplicável\\''\\'' THEN
    Sequencia := FazerLancamento(  \\''\\''292410308000000\\''\\'' , \\''\\''292410208000000\\''\\'' , 905 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
END IF;
IF   Modalidade  =  \\''\\''Suprimentos\\''\\'' THEN
    Sequencia := FazerLancamento(  \\''\\''292410309000000\\''\\'' , \\''\\''292410209000000\\''\\'' , 905 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
END IF;
IF    Modalidade  =  \\''\\''Integração\\''\\'' THEN
    Sequencia := FazerLancamento(  \\''\\''292410310000000\\''\\'' , \\''\\''292410210000000\\''\\'' , 905 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
END IF;
IF   Modalidade  =  \\''\\''Pregão\\''\\'' THEN
    Sequencia := FazerLancamento(  \\''\\''292410312000000\\''\\'' , \\''\\''292410212000000\\''\\'' , 905 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
END IF;
IF   Modalidade  =  \\''\\''Pregão Presencial\\''\\'' THEN
    Sequencia := FazerLancamento(  \\''\\''292410312000000\\''\\'' , \\''\\''292410212000000\\''\\'' , 905 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
END IF;
IF   Modalidade  =  \\''\\''Pregão Eletrônico\\''\\'' THEN
    Sequencia := FazerLancamento(  \\''\\''292410312000000\\''\\'' , \\''\\''292410212000000\\''\\'' , 905 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
END IF;
IF   Modalidade  =  \\''\\''Chamada Pública\\''\\'' THEN
    Sequencia := FazerLancamento(  \\''\\''292410313000000\\''\\'' , \\''\\''292410213000000\\''\\'' , 905 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
END IF;
IF   Modalidade  =  \\''\\''Registro de Preços\\''\\'' THEN
    Sequencia := FazerLancamento(  \\''\\''292410314000000\\''\\'' , \\''\\''292410214000000\\''\\'' , 905 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
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
               VALUES (10,1,110,1,'0','#Modalidade <- pegaEmpenhoLiquidacaoModalidade(  #Exercicio , #CodNota , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,110,2,'1','SE   #Modalidade  =  "Concurso" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,110,3,'1','#Sequencia <- FazerLancamento(  "292410301000000" , "292410201000000" , 905 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,110,4,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,110,5,'1','SE   #Modalidade  =  "Convite" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,110,6,'1','#Sequencia <- FazerLancamento(  "292410302000000" , "292410202000000" , 905 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,110,7,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,110,8,'1','SE   #Modalidade  =  "Tomada" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,110,9,'1','#Sequencia <- FazerLancamento(  "292410303000000" , "292410203000000" , 905 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,110,10,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,110,11,'1','SE   #Modalidade  =  "Concorrência" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,110,12,'1','#Sequencia <- FazerLancamento(  "292410304000000" , "292410204000000" , 905 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,110,13,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,110,14,'1','SE   #Modalidade  =  "Dispensa" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,110,15,'1','#Sequencia <- FazerLancamento(  "292410306000000" , "292410206000000" , 905 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,110,16,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,110,17,'1','SE   #Modalidade  =  "Inexigível" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,110,18,'1','#Sequencia <- FazerLancamento(  "292410307000000" , "292410207000000" , 905 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,110,19,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,110,20,'1','SE   #Modalidade  =  "Não Aplicável" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,110,21,'1','#Sequencia <- FazerLancamento(  "292410308000000" , "292410208000000" , 905 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,110,22,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,110,23,'1','SE   #Modalidade  =  "Suprimentos" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,110,24,'1','#Sequencia <- FazerLancamento(  "292410309000000" , "292410209000000" , 905 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,110,25,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,110,26,'1','SE    #Modalidade  =  "Integração" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,110,27,'1','#Sequencia <- FazerLancamento(  "292410310000000" , "292410210000000" , 905 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,110,28,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,110,29,'1','SE   #Modalidade  =  "Pregão" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,110,30,'1','#Sequencia <- FazerLancamento(  "292410312000000" , "292410212000000" , 905 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,110,31,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,110,32,'1','SE   #Modalidade  =  "Pregão Presencial" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,110,33,'1','#Sequencia <- FazerLancamento(  "292410312000000" , "292410212000000" , 905 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,110,34,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,110,35,'1','SE   #Modalidade  =  "Pregão Eletrônico" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,110,36,'1','#Sequencia <- FazerLancamento(  "292410312000000" , "292410212000000" , 905 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,110,37,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,110,38,'1','SE   #Modalidade  =  "Chamada Pública" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,110,39,'1','#Sequencia <- FazerLancamento(  "292410313000000" , "292410213000000" , 905 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,110,40,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,110,41,'1','SE   #Modalidade  =  "Registro de Preços" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,110,42,'1','#Sequencia <- FazerLancamento(  "292410314000000" , "292410214000000" , 905 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,110,43,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,110,44,'0','RETORNA #Sequencia');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,110,1,2,'Modalidade','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,110,2,1,'Sequencia','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,110,3,2,'Exercicio','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,110,4,4,'Valor','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,110,5,2,'Complemento','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,110,6,1,'CodLote','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,110,7,2,'TipoLote','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,110,8,1,'CodEntidade','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,110,9,1,'CodNota','');
INSERT INTO administracao.parametro
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , ordem)
               VALUES (10,1,110,3,0);
INSERT INTO administracao.parametro
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , ordem)
               VALUES (10,1,110,4,1);
INSERT INTO administracao.parametro
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , ordem)
               VALUES (10,1,110,5,2);
INSERT INTO administracao.parametro
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , ordem)
               VALUES (10,1,110,6,3);
INSERT INTO administracao.parametro
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , ordem)
               VALUES (10,1,110,7,4);
INSERT INTO administracao.parametro
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , ordem)
               VALUES (10,1,110,8,5);
INSERT INTO administracao.parametro
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , ordem)
               VALUES (10,1,110,9,6);


------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
INSERT INTO administracao.funcao_externa
                  (cod_modulo
                  , cod_biblioteca
                  , cod_funcao
                  , comentario
                  , corpo_pl
                  , corpo_ln)
            VALUES (10,1,102,'','FUNCTION EmpenhoLiquidacaoModalidadesLicitacao(VARCHAR,NUMERIC,VARCHAR,INTEGER,VARCHAR,INTEGER,INTEGER) RETURNS INTEGER as \\'' 
DECLARE
Exercicio ALIAS FOR $1;
Valor ALIAS FOR $2;
Complemento ALIAS FOR $3;
CodLote ALIAS FOR $4;
TipoLote ALIAS FOR $5;
CodEntidade ALIAS FOR $6;
CodNota ALIAS FOR $7;

  Modalidade VARCHAR := \\''\\''\\''\\'';
  Sequencia INTEGER;
BEGIN
Modalidade := pegaEmpenhoLiquidacaoModalidade(  Exercicio , CodNota , CodEntidade  ); 
IF   Modalidade  =  \\''\\''Concurso\\''\\'' THEN
    Sequencia := FazerLancamento(  \\''\\''292410201000000\\''\\'' , \\''\\''292410301000000\\''\\'' , 902 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
END IF;
IF   Modalidade  =  \\''\\''Convite\\''\\'' THEN
    Sequencia := FazerLancamento(  \\''\\''292410202000000\\''\\'' , \\''\\''292410302000000\\''\\'' , 902 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
END IF;
IF   Modalidade  =  \\''\\''Tomada\\''\\'' THEN
    Sequencia := FazerLancamento(  \\''\\''292410203000000\\''\\'' , \\''\\''292410303000000\\''\\'' , 902 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
END IF;
IF   Modalidade  =  \\''\\''Concorrência\\''\\'' THEN
    Sequencia := FazerLancamento(  \\''\\''292410204000000\\''\\'' , \\''\\''292410304000000\\''\\'' , 902 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
END IF;
IF   Modalidade  =  \\''\\''Dispensa\\''\\'' THEN
    Sequencia := FazerLancamento(  \\''\\''292410206000000\\''\\'' , \\''\\''292410306000000\\''\\'' , 902 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
END IF;
IF   Modalidade  =  \\''\\''Inexigível\\''\\'' THEN
    Sequencia := FazerLancamento(  \\''\\''292410207000000\\''\\'' , \\''\\''292410307000000\\''\\'' , 902 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
END IF;
IF   Modalidade  =  \\''\\''Não Aplicável\\''\\'' THEN
    Sequencia := FazerLancamento(  \\''\\''292410208000000\\''\\'' , \\''\\''292410308000000\\''\\'' , 902 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
END IF;
IF   Modalidade  =  \\''\\''Suprimentos\\''\\'' THEN
    Sequencia := FazerLancamento(  \\''\\''292410209000000\\''\\'' , \\''\\''292410309000000\\''\\'' , 902 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
END IF;
IF     Modalidade  =  \\''\\''Integração\\''\\'' THEN
    Sequencia := FazerLancamento(  \\''\\''292410210000000\\''\\'' , \\''\\''292410310000000\\''\\'' , 902 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
END IF;
IF   Modalidade  =  \\''\\''Pregão\\''\\'' THEN
    Sequencia := FazerLancamento(  \\''\\''292410212000000\\''\\'' , \\''\\''292410312000000\\''\\'' , 902 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
END IF;
IF   Modalidade  =  \\''\\''Pregão Presencial\\''\\'' THEN
    Sequencia := FazerLancamento(  \\''\\''292410212000000\\''\\'' , \\''\\''292410312000000\\''\\'' , 902 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
END IF;
IF   Modalidade  =  \\''\\''Pregão Eletrônico\\''\\'' THEN
    Sequencia := FazerLancamento(  \\''\\''292410212000000\\''\\'' , \\''\\''292410312000000\\''\\'' , 902 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
END IF;
IF   Modalidade  =  \\''\\''Chamada Pública\\''\\'' THEN
    Sequencia := FazerLancamento(  \\''\\''292410213000000\\''\\'' , \\''\\''292410313000000\\''\\'' , 902 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
END IF;
IF   Modalidade  =  \\''\\''Registro de Preços\\''\\'' THEN
    Sequencia := FazerLancamento(  \\''\\''292410214000000\\''\\'' , \\''\\''292410314000000\\''\\'' , 902 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
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
               VALUES (10,1,102,1,'0','#Modalidade <- pegaEmpenhoLiquidacaoModalidade(  #Exercicio , #CodNota , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,102,2,'1','SE   #Modalidade  =  "Concurso" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,102,3,'1','#Sequencia <- FazerLancamento(  "292410201000000" , "292410301000000" , 902 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,102,4,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,102,5,'1','SE   #Modalidade  =  "Convite" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,102,6,'1','#Sequencia <- FazerLancamento(  "292410202000000" , "292410302000000" , 902 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,102,7,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,102,8,'1','SE   #Modalidade  =  "Tomada" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,102,9,'1','#Sequencia <- FazerLancamento(  "292410203000000" , "292410303000000" , 902 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,102,10,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,102,11,'1','SE   #Modalidade  =  "Concorrência" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,102,12,'1','#Sequencia <- FazerLancamento(  "292410204000000" , "292410304000000" , 902 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,102,13,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,102,14,'1','SE   #Modalidade  =  "Dispensa" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,102,15,'1','#Sequencia <- FazerLancamento(  "292410206000000" , "292410306000000" , 902 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,102,16,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,102,17,'1','SE   #Modalidade  =  "Inexigível" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,102,18,'1','#Sequencia <- FazerLancamento(  "292410207000000" , "292410307000000" , 902 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,102,19,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,102,20,'1','SE   #Modalidade  =  "Não Aplicável" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,102,21,'1','#Sequencia <- FazerLancamento(  "292410208000000" , "292410308000000" , 902 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,102,22,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,102,23,'1','SE   #Modalidade  =  "Suprimentos" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,102,24,'1','#Sequencia <- FazerLancamento(  "292410209000000" , "292410309000000" , 902 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,102,25,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,102,26,'1','SE     #Modalidade  =  "Integração" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,102,27,'1','#Sequencia <- FazerLancamento(  "292410210000000" , "292410310000000" , 902 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,102,28,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,102,29,'1','SE   #Modalidade  =  "Pregão" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,102,30,'1','#Sequencia <- FazerLancamento(  "292410212000000" , "292410312000000" , 902 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,102,31,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,102,32,'1','SE   #Modalidade  =  "Pregão Presencial" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,102,33,'1','#Sequencia <- FazerLancamento(  "292410212000000" , "292410312000000" , 902 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,102,34,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,102,35,'1','SE   #Modalidade  =  "Pregão Eletrônico" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,102,36,'1','#Sequencia <- FazerLancamento(  "292410212000000" , "292410312000000" , 902 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,102,37,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,102,38,'1','SE   #Modalidade  =  "Chamada Pública" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,102,39,'1','#Sequencia <- FazerLancamento(  "292410213000000" , "292410313000000" , 902 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,102,40,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,102,41,'1','SE   #Modalidade  =  "Registro de Preços" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,102,42,'1','#Sequencia <- FazerLancamento(  "292410214000000" , "292410314000000" , 902 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,102,43,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,102,44,'0','RETORNA #Sequencia');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,102,1,2,'Modalidade','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,102,2,1,'Sequencia','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,102,3,2,'Exercicio','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,102,4,4,'Valor','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,102,5,2,'Complemento','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,102,6,1,'CodLote','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,102,7,2,'TipoLote','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,102,8,1,'CodEntidade','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,102,9,1,'CodNota','');
INSERT INTO administracao.parametro
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , ordem)
               VALUES (10,1,102,3,0);
INSERT INTO administracao.parametro
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , ordem)
               VALUES (10,1,102,4,1);
INSERT INTO administracao.parametro
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , ordem)
               VALUES (10,1,102,5,2);
INSERT INTO administracao.parametro
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , ordem)
               VALUES (10,1,102,6,3);
INSERT INTO administracao.parametro
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , ordem)
               VALUES (10,1,102,7,4);
INSERT INTO administracao.parametro
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , ordem)
               VALUES (10,1,102,8,5);
INSERT INTO administracao.parametro
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , ordem)
               VALUES (10,1,102,9,6);


-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
INSERT INTO administracao.funcao_externa
                  (cod_modulo
                  , cod_biblioteca
                  , cod_funcao
                  , comentario
                  , corpo_pl
                  , corpo_ln)
            VALUES (10,1,108,'','FUNCTION EmpenhoModalidadesLicitacao(VARCHAR,NUMERIC,VARCHAR,INTEGER,VARCHAR,INTEGER,INTEGER) RETURNS INTEGER as \\'' 
DECLARE
Exercicio ALIAS FOR $1;
Valor ALIAS FOR $2;
Complemento ALIAS FOR $3;
CodLote ALIAS FOR $4;
TipoLote ALIAS FOR $5;
CodEntidade ALIAS FOR $6;
CodPreEmpenho ALIAS FOR $7;

  Modalidade VARCHAR := \\''\\''\\''\\'';
  Sequencia INTEGER;
BEGIN
Modalidade := pegaEmpenhoEmpenhoModalidade(  Exercicio , CodPreEmpenho  ); 
IF   Modalidade  =  \\''\\''Concurso\\''\\'' THEN
    Sequencia := FazerLancamento(  \\''\\''192419900000000\\''\\'' , \\''\\''192410201000000\\''\\'' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
    Sequencia := FazerLancamento(  \\''\\''292410201000000\\''\\'' , \\''\\''292419900000000\\''\\'' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
END IF;
IF   Modalidade  =  \\''\\''Convite\\''\\'' THEN
    Sequencia := FazerLancamento(  \\''\\''192419900000000\\''\\'' , \\''\\''192410202000000\\''\\'' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
    Sequencia := FazerLancamento(  \\''\\''292410202000000\\''\\'' , \\''\\''292419900000000\\''\\'' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
END IF;
IF   Modalidade  =  \\''\\''Tomada\\''\\'' THEN
    Sequencia := FazerLancamento(  \\''\\''192419900000000\\''\\'' , \\''\\''192410203000000\\''\\'' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
    Sequencia := FazerLancamento(  \\''\\''292410203000000\\''\\'' , \\''\\''292419900000000\\''\\'' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
END IF;
IF   Modalidade  =  \\''\\''Concorrência\\''\\'' THEN
    Sequencia := FazerLancamento(  \\''\\''192419900000000\\''\\'' , \\''\\''192410204000000\\''\\'' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
    Sequencia := FazerLancamento(  \\''\\''292410204000000\\''\\'' , \\''\\''292419900000000\\''\\'' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
END IF;
IF   Modalidade  =  \\''\\''Dispensa\\''\\'' THEN
    Sequencia := FazerLancamento(  \\''\\''192419900000000\\''\\'' , \\''\\''192410206000000\\''\\'' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
    Sequencia := FazerLancamento(  \\''\\''292410206000000\\''\\'' , \\''\\''292419900000000\\''\\'' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
END IF;
IF   Modalidade  =  \\''\\''Inexigível\\''\\'' THEN
    Sequencia := FazerLancamento(  \\''\\''192419900000000\\''\\'' , \\''\\''192410207000000\\''\\'' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
    Sequencia := FazerLancamento(  \\''\\''292410207000000\\''\\'' , \\''\\''292419900000000\\''\\'' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
END IF;
IF   Modalidade  =  \\''\\''Não Aplicável\\''\\'' THEN
    Sequencia := FazerLancamento(  \\''\\''192419900000000\\''\\'' , \\''\\''192410208000000\\''\\'' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
    Sequencia := FazerLancamento(  \\''\\''292410208000000\\''\\'' , \\''\\''292419900000000\\''\\'' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
END IF;
IF   Modalidade  =  \\''\\''Suprimentos\\''\\'' THEN
    Sequencia := FazerLancamento(  \\''\\''192419900000000\\''\\'' , \\''\\''192410209000000\\''\\'' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
    Sequencia := FazerLancamento(  \\''\\''292410209000000\\''\\'' , \\''\\''292419900000000\\''\\'' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
END IF;
IF    Modalidade  =  \\''\\''Integração\\''\\'' THEN
    Sequencia := FazerLancamento(  \\''\\''192419900000000\\''\\'' , \\''\\''192410210000000\\''\\'' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
    Sequencia := FazerLancamento(  \\''\\''292410210000000\\''\\'' , \\''\\''292419900000000\\''\\'' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
END IF;
IF   Modalidade  =  \\''\\''Pregão\\''\\'' THEN
    Sequencia := FazerLancamento(  \\''\\''192419900000000\\''\\'' , \\''\\''192410212000000\\''\\'' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
    Sequencia := FazerLancamento(  \\''\\''292410212000000\\''\\'' , \\''\\''292419900000000\\''\\'' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
END IF;
IF   Modalidade  =  \\''\\''Pregão Presencial\\''\\'' THEN
    Sequencia := FazerLancamento(  \\''\\''192419900000000\\''\\'' , \\''\\''192410212000000\\''\\'' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
    Sequencia := FazerLancamento(  \\''\\''292410212000000\\''\\'' , \\''\\''292419900000000\\''\\'' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
END IF;
IF   Modalidade  =  \\''\\''Pregão Eletrônico\\''\\'' THEN
    Sequencia := FazerLancamento(  \\''\\''192419900000000\\''\\'' , \\''\\''192410212000000\\''\\'' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
    Sequencia := FazerLancamento(  \\''\\''292410212000000\\''\\'' , \\''\\''292419900000000\\''\\'' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
END IF;
IF   Modalidade  =  \\''\\''Chamada Pública\\''\\'' THEN
    Sequencia := FazerLancamento(  \\''\\''192419900000000\\''\\'' , \\''\\''192410213000000\\''\\'' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
    Sequencia := FazerLancamento(  \\''\\''292410213000000\\''\\'' , \\''\\''292419900000000\\''\\'' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
END IF;
IF   Modalidade  =  \\''\\''Registro de Preços\\''\\'' THEN
    Sequencia := FazerLancamento(  \\''\\''192419900000000\\''\\'' , \\''\\''192410214000000\\''\\'' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
    Sequencia := FazerLancamento(  \\''\\''292410214000000\\''\\'' , \\''\\''292419900000000\\''\\'' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
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
               VALUES (10,1,108,1,'0','#Modalidade <- pegaEmpenhoEmpenhoModalidade(  #Exercicio , #CodPreEmpenho  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,108,2,'1','SE   #Modalidade  =  "Concurso" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,108,3,'1','#Sequencia <- FazerLancamento(  "192419900000000" , "192410201000000" , 904 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,108,4,'1','#Sequencia <- FazerLancamento(  "292410201000000" , "292419900000000" , 904 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,108,5,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,108,6,'1','SE   #Modalidade  =  "Convite" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,108,7,'1','#Sequencia <- FazerLancamento(  "192419900000000" , "192410202000000" , 904 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,108,8,'1','#Sequencia <- FazerLancamento(  "292410202000000" , "292419900000000" , 904 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,108,9,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,108,10,'1','SE   #Modalidade  =  "Tomada" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,108,11,'1','#Sequencia <- FazerLancamento(  "192419900000000" , "192410203000000" , 904 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,108,12,'1','#Sequencia <- FazerLancamento(  "292410203000000" , "292419900000000" , 904 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,108,13,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,108,14,'1','SE   #Modalidade  =  "Concorrência" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,108,15,'1','#Sequencia <- FazerLancamento(  "192419900000000" , "192410204000000" , 904 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,108,16,'1','#Sequencia <- FazerLancamento(  "292410204000000" , "292419900000000" , 904 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,108,17,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,108,18,'1','SE   #Modalidade  =  "Dispensa" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,108,19,'1','#Sequencia <- FazerLancamento(  "192419900000000" , "192410206000000" , 904 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,108,20,'1','#Sequencia <- FazerLancamento(  "292410206000000" , "292419900000000" , 904 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,108,21,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,108,22,'1','SE   #Modalidade  =  "Inexigível" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,108,23,'1','#Sequencia <- FazerLancamento(  "192419900000000" , "192410207000000" , 904 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,108,24,'1','#Sequencia <- FazerLancamento(  "292410207000000" , "292419900000000" , 904 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,108,25,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,108,26,'1','SE   #Modalidade  =  "Não Aplicável" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,108,27,'1','#Sequencia <- FazerLancamento(  "192419900000000" , "192410208000000" , 904 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,108,28,'1','#Sequencia <- FazerLancamento(  "292410208000000" , "292419900000000" , 904 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,108,29,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,108,30,'1','SE   #Modalidade  =  "Suprimentos" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,108,31,'1','#Sequencia <- FazerLancamento(  "192419900000000" , "192410209000000" , 904 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,108,32,'1','#Sequencia <- FazerLancamento(  "292410209000000" , "292419900000000" , 904 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,108,33,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,108,34,'1','SE    #Modalidade  =  "Integração" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,108,35,'1','#Sequencia <- FazerLancamento(  "192419900000000" , "192410210000000" , 904 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,108,36,'1','#Sequencia <- FazerLancamento(  "292410210000000" , "292419900000000" , 904 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,108,37,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,108,38,'1','SE   #Modalidade  =  "Pregão" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,108,39,'1','#Sequencia <- FazerLancamento(  "192419900000000" , "192410212000000" , 904 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,108,40,'1','#Sequencia <- FazerLancamento(  "292410212000000" , "292419900000000" , 904 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,108,41,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,108,42,'1','SE   #Modalidade  =  "Pregão Presencial" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,108,43,'1','#Sequencia <- FazerLancamento(  "192419900000000" , "192410212000000" , 904 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,108,44,'1','#Sequencia <- FazerLancamento(  "292410212000000" , "292419900000000" , 904 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,108,45,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,108,46,'1','SE   #Modalidade  =  "Pregão Eletrônico" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,108,47,'1','#Sequencia <- FazerLancamento(  "192419900000000" , "192410212000000" , 904 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,108,48,'1','#Sequencia <- FazerLancamento(  "292410212000000" , "292419900000000" , 904 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,108,49,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,108,50,'1','SE   #Modalidade  =  "Chamada Pública" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,108,51,'1','#Sequencia <- FazerLancamento(  "192419900000000" , "192410213000000" , 904 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,108,52,'1','#Sequencia <- FazerLancamento(  "292410213000000" , "292419900000000" , 904 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,108,53,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,108,54,'1','SE   #Modalidade  =  "Registro de Preços" ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,108,55,'1','#Sequencia <- FazerLancamento(  "192419900000000" , "192410214000000" , 904 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,108,56,'1','#Sequencia <- FazerLancamento(  "292410214000000" , "292419900000000" , 904 , #Exercicio , #Valor , #Complemento , #CodLote , #TipoLote , #CodEntidade  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,108,57,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (10,1,108,58,'0','RETORNA #Sequencia');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,108,1,2,'Modalidade','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,108,2,1,'Sequencia','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,108,3,2,'Exercicio','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,108,4,4,'Valor','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,108,5,2,'Complemento','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,108,6,1,'CodLote','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,108,7,2,'TipoLote','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,108,8,1,'CodEntidade','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (10,1,108,9,1,'CodPreEmpenho','');
INSERT INTO administracao.parametro
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , ordem)
               VALUES (10,1,108,3,0);
INSERT INTO administracao.parametro
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , ordem)
               VALUES (10,1,108,4,1);
INSERT INTO administracao.parametro
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , ordem)
               VALUES (10,1,108,5,2);
INSERT INTO administracao.parametro
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , ordem)
               VALUES (10,1,108,6,3);
INSERT INTO administracao.parametro
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , ordem)
               VALUES (10,1,108,7,4);
INSERT INTO administracao.parametro
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , ordem)
               VALUES (10,1,108,8,5);
INSERT INTO administracao.parametro
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , ordem)
               VALUES (10,1,108,9,6);


DROP FUNCTION EmpenhoAnulacaoLiquidacaoModalidadesLicitacao(VARCHAR,NUMERIC,VARCHAR,INTEGER,VARCHAR,INTEGER,INTEGER);
CREATE OR REPLACE FUNCTION EmpenhoAnulacaoLiquidacaoModalidadesLicitacao(VARCHAR,NUMERIC,VARCHAR,INTEGER,VARCHAR,INTEGER,INTEGER) RETURNS INTEGER AS $$
DECLARE
Exercicio ALIAS FOR $1;
Valor ALIAS FOR $2;
Complemento ALIAS FOR $3;
CodLote ALIAS FOR $4;
TipoLote ALIAS FOR $5;
CodEntidade ALIAS FOR $6;
CodNota ALIAS FOR $7;

  Modalidade VARCHAR := '';
  Sequencia INTEGER;
BEGIN
Modalidade := pegaEmpenhoLiquidacaoModalidade(  Exercicio , CodNota , CodEntidade  );
IF   Modalidade  =  'Concurso' THEN
    Sequencia := FazerLancamento(  '292410301000000' , '292410201000000' , 905 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
END IF;
IF   Modalidade  =  'Convite' THEN
    Sequencia := FazerLancamento(  '292410302000000' , '292410202000000' , 905 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
END IF;
IF   Modalidade  =  'Tomada' THEN
    Sequencia := FazerLancamento(  '292410303000000' , '292410203000000' , 905 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
END IF;
IF   Modalidade  =  'Concorrência' THEN
    Sequencia := FazerLancamento(  '292410304000000' , '292410204000000' , 905 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
END IF;
IF   Modalidade  =  'Dispensa' THEN
    Sequencia := FazerLancamento(  '292410306000000' , '292410206000000' , 905 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
END IF;
IF   Modalidade  =  'Inexigível' THEN
    Sequencia := FazerLancamento(  '292410307000000' , '292410207000000' , 905 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
END IF;
IF   Modalidade  =  'Não Aplicável' THEN
    Sequencia := FazerLancamento(  '292410308000000' , '292410208000000' , 905 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
END IF;
IF   Modalidade  =  'Suprimentos' THEN
    Sequencia := FazerLancamento(  '292410309000000' , '292410209000000' , 905 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
END IF;
IF    Modalidade  =  'Integração' THEN
    Sequencia := FazerLancamento(  '292410310000000' , '292410210000000' , 905 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
END IF;
IF   Modalidade  =  'Pregão' THEN
    Sequencia := FazerLancamento(  '292410312000000' , '292410212000000' , 905 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
END IF;
IF   Modalidade  =  'Pregão Presencial' THEN
    Sequencia := FazerLancamento(  '292410312000000' , '292410212000000' , 905 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
END IF;
IF   Modalidade  =  'Pregão Eletrônico' THEN
    Sequencia := FazerLancamento(  '292410312000000' , '292410212000000' , 905 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
END IF;
IF   Modalidade  =  'Chamada Pública' THEN
    Sequencia := FazerLancamento(  '292410313000000' , '292410213000000' , 905 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
END IF;
IF   Modalidade  =  'Registro de Preços' THEN
    Sequencia := FazerLancamento(  '292410314000000' , '292410214000000' , 905 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
END IF;
RETURN Sequencia;
END;
$$ LANGUAGE 'plpgsql';


DROP FUNCTION EmpenhoModalidadesLicitacao(VARCHAR,NUMERIC,VARCHAR,INTEGER,VARCHAR,INTEGER,INTEGER);
CREATE OR REPLACE FUNCTION EmpenhoModalidadesLicitacao(VARCHAR,NUMERIC,VARCHAR,INTEGER,VARCHAR,INTEGER,INTEGER) RETURNS INTEGER AS $$
DECLARE
Exercicio ALIAS FOR $1;
Valor ALIAS FOR $2;
Complemento ALIAS FOR $3;
CodLote ALIAS FOR $4;
TipoLote ALIAS FOR $5;
CodEntidade ALIAS FOR $6;
CodPreEmpenho ALIAS FOR $7;

  Modalidade VARCHAR := '';
  Sequencia INTEGER;
BEGIN
Modalidade := pegaEmpenhoEmpenhoModalidade(  Exercicio , CodPreEmpenho  );
IF   Modalidade  =  'Concurso' THEN
    Sequencia := FazerLancamento(  '192419900000000' , '192410201000000' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
    Sequencia := FazerLancamento(  '292410201000000' , '292419900000000' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
END IF;
IF   Modalidade  =  'Convite' THEN
    Sequencia := FazerLancamento(  '192419900000000' , '192410202000000' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
    Sequencia := FazerLancamento(  '292410202000000' , '292419900000000' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
END IF;
IF   Modalidade  =  'Tomada' THEN
    Sequencia := FazerLancamento(  '192419900000000' , '192410203000000' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
    Sequencia := FazerLancamento(  '292410203000000' , '292419900000000' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
END IF;
IF   Modalidade  =  'Concorrência' THEN
    Sequencia := FazerLancamento(  '192419900000000' , '192410204000000' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
    Sequencia := FazerLancamento(  '292410204000000' , '292419900000000' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
END IF;
IF   Modalidade  =  'Dispensa' THEN
    Sequencia := FazerLancamento(  '192419900000000' , '192410206000000' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
    Sequencia := FazerLancamento(  '292410206000000' , '292419900000000' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
END IF;
IF   Modalidade  =  'Inexigível' THEN
    Sequencia := FazerLancamento(  '192419900000000' , '192410207000000' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
    Sequencia := FazerLancamento(  '292410207000000' , '292419900000000' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
END IF;
IF   Modalidade  =  'Não Aplicável' THEN
    Sequencia := FazerLancamento(  '192419900000000' , '192410208000000' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
    Sequencia := FazerLancamento(  '292410208000000' , '292419900000000' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
END IF;
IF   Modalidade  =  'Suprimentos' THEN
    Sequencia := FazerLancamento(  '192419900000000' , '192410209000000' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
    Sequencia := FazerLancamento(  '292410209000000' , '292419900000000' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
END IF;
IF    Modalidade  =  'Integração' THEN
    Sequencia := FazerLancamento(  '192419900000000' , '192410210000000' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
    Sequencia := FazerLancamento(  '292410210000000' , '292419900000000' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
END IF;
IF   Modalidade  =  'Pregão' THEN
    Sequencia := FazerLancamento(  '192419900000000' , '192410212000000' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
    Sequencia := FazerLancamento(  '292410212000000' , '292419900000000' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
END IF;
IF   Modalidade  =  'Pregão Presencial' THEN
    Sequencia := FazerLancamento(  '192419900000000' , '192410212000000' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
    Sequencia := FazerLancamento(  '292410212000000' , '292419900000000' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
END IF;
IF   Modalidade  =  'Pregão Eletrônico' THEN
    Sequencia := FazerLancamento(  '192419900000000' , '192410212000000' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
    Sequencia := FazerLancamento(  '292410212000000' , '292419900000000' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
END IF;
IF   Modalidade  =  'Chamada Pública' THEN
    Sequencia := FazerLancamento(  '192419900000000' , '192410213000000' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
    Sequencia := FazerLancamento(  '292410213000000' , '292419900000000' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
END IF;
IF   Modalidade  =  'Registro de Preços' THEN
    Sequencia := FazerLancamento(  '192419900000000' , '192410214000000' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
    Sequencia := FazerLancamento(  '292410214000000' , '292419900000000' , 904 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
END IF;
RETURN Sequencia;
END;
$$ LANGUAGE 'plpgsql';


---------------INSERT DOS ATRIBUTOS DINAMICOS
UPDATE administracao.atributo_valor_padrao SET ativo = FALSE WHERE cod_atributo = 101 and cod_cadastro = 2 and cod_modulo = 10 and cod_valor = 10;

INSERT INTO administracao.atributo_valor_padrao (
    cod_modulo,
    cod_cadastro,
    cod_atributo,
    cod_valor,
    ativo,
    valor_padrao
 ) VALUES (
    10,
    2,
    101,
    (SELECT  (max(cod_valor) + 1) from  administracao.atributo_valor_padrao  where cod_modulo = 10 and cod_atributo = 101 and cod_cadastro = 2),
    TRUE,
    'Pregão Presencial'
);

---------------INSERT Pregão Eletrônico
INSERT INTO administracao.atributo_valor_padrao (
    cod_modulo,
    cod_cadastro,
    cod_atributo,
    cod_valor,
    ativo,
    valor_padrao
 ) VALUES (
    10,
    2,
    101,
    (SELECT  (max(cod_valor) + 1) from  administracao.atributo_valor_padrao  where cod_modulo = 10 and cod_atributo = 101 and cod_cadastro = 2),
    TRUE,
    'Pregão Eletrônico'
);

---------------INSERT Chamada Pública
INSERT INTO administracao.atributo_valor_padrao (
    cod_modulo,
    cod_cadastro,
    cod_atributo,
    cod_valor,
    ativo,
    valor_padrao
 ) VALUES (
    10,
    2,
    101,
    (SELECT  (max(cod_valor) + 1) from  administracao.atributo_valor_padrao  where cod_modulo = 10 and cod_atributo = 101 and cod_cadastro = 2),
    TRUE,
    'Chamada Pública'
);

---------------INSERT Registro de Preços
INSERT INTO administracao.atributo_valor_padrao (
    cod_modulo,
    cod_cadastro,
    cod_atributo,
    cod_valor,
    ativo,
    valor_padrao
 ) VALUES (
    10,
    2,
    101,
    (SELECT  (max(cod_valor) + 1) from  administracao.atributo_valor_padrao  where cod_modulo = 10 and cod_atributo = 101 and cod_cadastro = 2),
    TRUE,
    'Registro de Preços'
);



