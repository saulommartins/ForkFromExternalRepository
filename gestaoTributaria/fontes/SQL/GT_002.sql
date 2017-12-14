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
* $Revision: 28350 $
* $Name$
* $Author: gris $
* $Date: 2008-03-05 09:57:44 -0300 (Qua, 05 Mar 2008) $
*
* Versão 002.
*/

---------
-- Ticket 12262
---------

----- Já aplicado em Mata de São João. Script testa e aplica só se não for em Mata

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$

DECLARE

   varAux     VARCHAR;

BEGIN

   SELECT valor
     INTO varAux
     FROM administracao.configuracao
    WHERE exercicio = '2008'
      AND parametro = 'cnpj'
      AND valor     = '13805528000180';

   IF NOT FOUND THEN

      ALTER TABLE divida.documento_parcela DROP CONSTRAINT fk_documento_parcela_1;
      ALTER TABLE divida.documento_parcela DROP CONSTRAINT pk_documento_parcela;

      ALTER TABLE divida.emissao_documento DROP CONSTRAINT fk_emissao_documento_1;
      ALTER TABLE divida.emissao_documento DROP CONSTRAINT pk_emissao_documento;

      ALTER TABLE divida.documento         DROP CONSTRAINT pk_documento;


-- AJUSTE DAS COLUNAS

      ALTER TABLE divida.documento_parcela DROP COLUMN num_documento;
      ALTER TABLE divida.documento_parcela DROP COLUMN exercicio;

      ALTER TABLE divida.documento         DROP COLUMN num_documento;
      ALTER TABLE divida.documento         DROP COLUMN exercicio;

      CREATE TABLE temp_doc AS SELECT * FROM divida.documento         GROUP BY num_parcelamento, cod_tipo_documento, cod_documento;
      CREATE TABLE temp_par AS SELECT * FROM divida.documento_parcela GROUP BY num_parcelamento, cod_tipo_documento, cod_documento, num_parcela;

      DELETE FROM divida.documento;
      DELETE FROM divida.documento_parcela;

      INSERT INTO divida.documento         SELECT * FROM temp_doc;
      INSERT INTO divida.documento_parcela SELECT * FROM temp_par;

-- RECRIAR AS CHAVES
      ALTER TABLE divida.documento         ADD CONSTRAINT pk_documento            PRIMARY KEY (num_parcelamento, cod_tipo_documento, cod_documento);

      ALTER TABLE divida.emissao_documento ADD CONSTRAINT pk_emissao_documento    PRIMARY KEY (num_parcelamento, cod_tipo_documento, cod_documento, num_documento, exercicio, num_emissao);
      ALTER TABLE divida.emissao_documento ADD CONSTRAINT fk_emissao_documento_1  FOREIGN KEY                     (num_parcelamento, cod_tipo_documento, cod_documento)
                                                                                 REFERENCES divida.documento     (num_parcelamento, cod_tipo_documento, cod_documento);

      ALTER TABLE divida.documento_parcela ADD CONSTRAINT pk_documento_parcela    PRIMARY KEY (num_parcelamento, cod_tipo_documento, cod_documento, num_parcela);
      ALTER TABLE divida.documento_parcela ADD CONSTRAINT fk_documento_parcela_1  FOREIGN KEY                     (num_parcelamento, cod_tipo_documento, cod_documento)
                                                                                 REFERENCES divida.documento     (num_parcelamento, cod_tipo_documento, cod_documento);
   END IF;

END;

$$ LANGUAGE 'plpgsql';
SELECT manutencao();
DROP FUNCTION manutencao();

----------
-- Ticket 12261
----------

--Já aplicado em Mariana. Script testa e aplica só se não for em Mariana

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$

DECLARE

   varAux     VARCHAR;

BEGIN

   SELECT valor
     INTO varAux
     FROM administracao.configuracao
    WHERE exercicio = '2008'
      AND parametro = 'cnpj'
      AND valor     = '94068418000184';

   IF NOT FOUND THEN

   raise notice '--------------sim-----------------';

      INSERT INTO administracao.configuracao
                  (exercicio
                , cod_modulo
                , parametro
                , valor)
           VALUES ('2008'
                , 14
                , 'sanit_departamento'
                ,'');

      INSERT INTO administracao.configuracao
                  (exercicio
                , cod_modulo
                , parametro
                , valor)
           VALUES ('2008'
                , 14
                , 'sanit_secretaria'
                , '');

 END IF;

END;

$$ LANGUAGE 'plpgsql';
SELECT manutencao();
DROP FUNCTION manutencao();

--------------
-- Ticket #12401
--------------

INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     VALUES (2232
          , 380
          , 'FMManterRecadastro.php'
          , 'recadastra'
          , 4
          , ''
          , 'Recadastrar Tabelas de Conversão');
