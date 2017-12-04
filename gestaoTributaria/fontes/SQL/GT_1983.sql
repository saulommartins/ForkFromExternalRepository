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
* Versão 1.98.3
*/

----------------------------------------------------------------------------------
-- TIPO DE CONVENIO P/ Ficha de Compensação BB - convênio sem registro 17 posições
----------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE
    inCodFuncao         INTEGER;
    inCodBiblioteca     INTEGER;
    inCodModulo         INTEGER;

    inCodTipo           INTEGER;
BEGIN

    SELECT cod_funcao
         , cod_biblioteca
         , cod_modulo
      INTO inCodFuncao
         , inCodBiblioteca
         , inCodModulo
      FROM administracao.funcao
     WHERE nom_funcao = 'NumeracaoFebraban'
         ;

    SELECT MAX(cod_tipo) + 1
      INTO inCodTipo
      FROM monetario.tipo_convenio
         ;

    PERFORM 1
       FROM monetario.tipo_convenio
      WHERE nom_tipo = 'Ficha de Compensação BB - 17 pos.'
          ;

    IF NOT FOUND THEN

        INSERT
          INTO monetario.tipo_convenio
             ( cod_tipo
             , nom_tipo
             , cod_modulo
             , cod_biblioteca
             , cod_funcao
             )
        VALUES
             ( inCodTipo
             , 'Ficha de Compensação BB - 17 pos.'
             , inCodModulo
             , inCodBiblioteca
             , inCodFuncao
             );

    END IF;

END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


------------------------------------------------
-- TABELA DE INFORMAÇÔES P/ FICHA DE COMPENSAÇÂO
------------------------------------------------

CREATE OR REPLACE FUNCTION manutencao_t() RETURNS VOID AS $$
DECLARE

BEGIN

    PERFORM 1
       FROM pg_tables
      WHERE tablename = 'convenio_ficha_compensacao'
          ;

    IF NOT FOUND THEN

        CREATE TABLE monetario.convenio_ficha_compensacao(
            cod_convenio        INTEGER                 NOT NULL,
            local_pagamento     VARCHAR(80)                     ,
            especie_doc         VARCHAR(20)                     ,
            aceite              VARCHAR(1)                      ,
            especie             VARCHAR(20)                     ,
            quantidade          VARCHAR(20)                     ,
            CONSTRAINT pk_convenio_ficha_compensacao    PRIMARY KEY                     (cod_convenio),
            CONSTRAINT fk_convenio_ficha_compensacao_1  FOREIGN KEY                     (cod_convenio)
                                                        REFERENCES monetario.convenio   (cod_convenio),
            CONSTRAINT ck_convenio_ficha_compensacao_1  CHECK(aceite IN ('A','N'))
        );

        GRANT ALL ON monetario.convenio_ficha_compensacao TO GROUP urbem;

    END IF;

END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao_t();
DROP FUNCTION manutencao_t();

