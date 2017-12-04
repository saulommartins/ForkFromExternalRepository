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
* $Id: GT_1976.sql 59612 2014-09-02 12:00:51Z gelson $
*
* Versão 1.97.6
*/

----------------
-- Ticket #15335
----------------

CREATE TABLE monetario.regra_desoneracao_credito (
    cod_natureza    INTEGER     NOT NULL,
    cod_genero      INTEGER     NOT NULL,
    cod_especie     INTEGER     NOT NULL,
    cod_credito     INTEGER     NOT NULL,
    cod_modulo      INTEGER     NOT NULL,
    cod_biblioteca  INTEGER     NOT NULL,
    cod_funcao      INTEGER     NOT NULL,
    CONSTRAINT pk_regra_desoneracao_credito     PRIMARY KEY                     (cod_natureza, cod_genero, cod_especie, cod_credito),
    CONSTRAINT fk_regra_desoneracao_credito_1   FOREIGN KEY                     (cod_natureza, cod_genero, cod_especie, cod_credito)
                                                REFERENCES monetario.credito    (cod_natureza, cod_genero, cod_especie, cod_credito),
    CONSTRAINT fk_regra_desoneracao_credito_2   FOREIGN KEY                     (cod_modulo, cod_biblioteca, cod_funcao)
                                                REFERENCES administracao.funcao (cod_modulo, cod_biblioteca, cod_funcao)
);

GRANT ALL ON monetario.regra_desoneracao_credito TO GROUP urbem;


--------------------------------------------------------------------
-- DROPANDO FUNCAO divida.fn_rel_divida_ativa P/ ADICAO DE PARAMETRO
--------------------------------------------------------------------

DROP FUNCTION divida.fn_rel_divida_ativa(INTEGER,VARCHAR,INTEGER,VARCHAR,INTEGER,VARCHAR,INTEGER,INTEGER,INTEGER,INTEGER,INTEGER,INTEGER,NUMERIC,NUMERIC);


-----------------------------------
-- ADICIONANDO COLUNAS base_calculo
-----------------------------------

ALTER TABLE fiscalizacao.auto_infracao_multa ADD COLUMN base_calculo NUMERIC(14,2) NOT NULL;
ALTER TABLE fiscalizacao.notificacao_infracao ADD COLUMN base_calculo NUMERIC(14,2) NOT NULL;


----------------
-- Ticket #15470
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE
    stAux   VARCHAR;
BEGIN
    SELECT valor
      INTO stAux
      FROM administracao.configuracao
     WHERE parametro  = 'cnpj'
       AND exercicio  = '2009'
       AND cod_modulo = 2
       AND valor      = '13805528000180';

    IF FOUND THEN
        UPDATE arrecadacao.modelo_carne
           SET nom_modelo ='Carne I.P.T.U. Complementar 2009'
         WHERE cod_modelo = 9;
    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();

