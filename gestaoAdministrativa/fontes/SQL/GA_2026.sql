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
*
* Script de DDL e DML
*
* Versao 2.02.6
*
* Fabio Bertoldi - 20140606
*
*/

----------------
-- Ticket #21797
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE
    inProximo   INTEGER;
BEGIN
    PERFORM 1
       FROM administracao.configuracao
      WHERE cod_modulo = 2
        AND exercicio  = '2014'
        AND parametro  = 'cnpj'
        AND valor      = '18301002000186'
          ;
    IF NOT FOUND THEN

        SELECT COALESCE(MAX(cod_atributo)+1,1)
          INTO inProximo
          FROM administracao.atributo_dinamico
         WHERE cod_modulo   = 15
           AND cod_cadastro = 1
             ;
        INSERT INTO administracao.atributo_dinamico (cod_modulo, cod_cadastro, cod_atributo, cod_tipo, nao_nulo, nom_atributo, valor_padrao, ajuda, mascara, ativo, interno, indexavel) VALUES (15, 1, inProximo, 2, FALSE, 'Artigo da Lei'                   , NULL, 'Artigo da Lei que autorizou a  alteração'                       , NULL, TRUE, NULL, FALSE);
        INSERT INTO administracao.atributo_valor_padrao (cod_modulo, cod_cadastro, cod_atributo, cod_valor, ativo, valor_padrao) VALUES (15, 1, inProximo, 1, TRUE, NULL );
        INSERT INTO administracao.atributo_integridade (cod_modulo, cod_cadastro, cod_atributo, cod_integridade, regra) VALUES ( 15, 1, inProximo, 1, inProximo::VARCHAR ||',,'            );
        INSERT INTO administracao.atributo_integridade (cod_modulo, cod_cadastro, cod_atributo, cod_integridade, regra) VALUES ( 15, 1, inProximo, 2, ' SELECT  FROM  WHERE  = VLR_VALIDA0');


        SELECT COALESCE(MAX(cod_atributo)+1,1)
          INTO inProximo
          FROM administracao.atributo_dinamico
         WHERE cod_modulo   = 15
           AND cod_cadastro = 1
             ;
        INSERT INTO administracao.atributo_dinamico (cod_modulo, cod_cadastro, cod_atributo, cod_tipo, nao_nulo, nom_atributo, valor_padrao, ajuda, mascara, ativo, interno, indexavel) VALUES (15, 1, inProximo, 7, FALSE, 'Descrição do Artigo'             , NULL, 'Descrição do artigo   que autorizou a   alteração  orçamentária', NULL, TRUE, NULL, FALSE);
        INSERT INTO administracao.atributo_valor_padrao (cod_modulo, cod_cadastro, cod_atributo, cod_valor, ativo, valor_padrao) VALUES (15, 1, inProximo, 1, TRUE, NULL );
        INSERT INTO administracao.atributo_integridade (cod_modulo, cod_cadastro, cod_atributo, cod_integridade, regra) VALUES ( 15, 1, inProximo, 1, inProximo::VARCHAR ||',,'            );
        INSERT INTO administracao.atributo_integridade (cod_modulo, cod_cadastro, cod_atributo, cod_integridade, regra) VALUES ( 15, 1, inProximo, 2, ' SELECT  FROM  WHERE  = VLR_VALIDA0');


        SELECT COALESCE(MAX(cod_atributo)+1,1)
          INTO inProximo
          FROM administracao.atributo_dinamico
         WHERE cod_modulo   = 15
           AND cod_cadastro = 1
             ;
        INSERT INTO administracao.atributo_dinamico (cod_modulo, cod_cadastro, cod_atributo, cod_tipo, nao_nulo, nom_atributo, valor_padrao, ajuda, mascara, ativo, interno, indexavel) VALUES (15, 1, inProximo, 3, FALSE, 'Altera Percentual Suplementações', NULL, ''                                                               , NULL, TRUE, NULL, FALSE);
        INSERT INTO administracao.atributo_valor_padrao (cod_modulo, cod_cadastro, cod_atributo, cod_valor, ativo, valor_padrao) VALUES (15, 1, inProximo, 1, TRUE, 'Sim'); 
        INSERT INTO administracao.atributo_valor_padrao (cod_modulo, cod_cadastro, cod_atributo, cod_valor, ativo, valor_padrao) VALUES (15, 1, inProximo, 2, TRUE, 'Não'); 
        INSERT INTO administracao.atributo_integridade (cod_modulo, cod_cadastro, cod_atributo, cod_integridade, regra) VALUES ( 15, 1, inProximo, 1, inProximo::VARCHAR ||',,'            );
        INSERT INTO administracao.atributo_integridade (cod_modulo, cod_cadastro, cod_atributo, cod_integridade, regra) VALUES ( 15, 1, inProximo, 2, ' SELECT  FROM  WHERE  = VLR_VALIDA0');


        SELECT COALESCE(MAX(cod_atributo)+1,1)
          INTO inProximo
          FROM administracao.atributo_dinamico
         WHERE cod_modulo   = 15
           AND cod_cadastro = 1
             ;
        INSERT INTO administracao.atributo_dinamico (cod_modulo, cod_cadastro, cod_atributo, cod_tipo, nao_nulo, nom_atributo, valor_padrao, ajuda, mascara, ativo, interno, indexavel) VALUES (15, 1, inProximo, 2, TRUE , 'Novo Percentual Suplementações'  , NULL, ''                                                               , NULL, TRUE, NULL, FALSE);
        INSERT INTO administracao.atributo_valor_padrao (cod_modulo, cod_cadastro, cod_atributo, cod_valor, ativo, valor_padrao) VALUES (15, 1, inProximo, 1, TRUE, NULL );
        INSERT INTO administracao.atributo_integridade (cod_modulo, cod_cadastro, cod_atributo, cod_integridade, regra) VALUES ( 15, 1, inProximo, 1, inProximo::VARCHAR ||',,'            );
        INSERT INTO administracao.atributo_integridade (cod_modulo, cod_cadastro, cod_atributo, cod_integridade, regra) VALUES ( 15, 1, inProximo, 2, ' SELECT  FROM  WHERE  = VLR_VALIDA0');

    END IF;

    SELECT COALESCE(MAX(cod_atributo)+1,1)
      INTO inProximo
      FROM administracao.atributo_dinamico
     WHERE cod_modulo   = 15
       AND cod_cadastro = 1
         ;
    INSERT INTO administracao.atributo_dinamico (cod_modulo, cod_cadastro, cod_atributo, cod_tipo, nao_nulo, nom_atributo, valor_padrao, ajuda, mascara, ativo, interno, indexavel) VALUES (15, 1, inProximo, 3, TRUE , 'Tipo de Autorização'             , NULL, ''                                                               , NULL, TRUE, NULL, FALSE);
    INSERT INTO administracao.atributo_valor_padrao (cod_modulo, cod_cadastro, cod_atributo, cod_valor, ativo, valor_padrao) VALUES (15, 1, inProximo, 1, TRUE, '1 – Abertura de créditos suplementares'                             );
    INSERT INTO administracao.atributo_valor_padrao (cod_modulo, cod_cadastro, cod_atributo, cod_valor, ativo, valor_padrao) VALUES (15, 1, inProximo, 2, TRUE, '2 – Contratação de operações de crédito'                            );
    INSERT INTO administracao.atributo_valor_padrao (cod_modulo, cod_cadastro, cod_atributo, cod_valor, ativo, valor_padrao) VALUES (15, 1, inProximo, 3, TRUE, '3 – Contratação de operações de crédito por antecipação de receita.');
    INSERT INTO administracao.atributo_integridade (cod_modulo, cod_cadastro, cod_atributo, cod_integridade, regra) VALUES ( 15, 1, inProximo, 1, inProximo::VARCHAR ||',,'            );
    INSERT INTO administracao.atributo_integridade (cod_modulo, cod_cadastro, cod_atributo, cod_integridade, regra) VALUES ( 15, 1, inProximo, 2, ' SELECT  FROM  WHERE  = VLR_VALIDA0');

END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();
