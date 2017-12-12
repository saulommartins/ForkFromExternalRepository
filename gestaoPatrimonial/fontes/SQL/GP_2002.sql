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
* Versão 2.00.2
*/

----------------
-- Ticket #17778
----------------

CREATE TABLE patrimonio.bem_marca(
    cod_bem         INTEGER     NOT NULL,
    cod_marca       INTEGER     NOT NULL,
    CONSTRAINT pk_bem_marca     PRIMARY KEY                  (cod_bem),
    CONSTRAINT fk_bem_marca_1   FOREIGN KEY                  (cod_bem)
                                REFERENCES patrimonio.bem    (cod_bem),
    CONSTRAINT fk_bem_marca_2   FOREIGN KEY                  (cod_marca)
                                REFERENCES almoxarifado.marca(cod_marca)
);
GRANT ALL ON patrimonio.bem_marca TO GROUP urbem;


----------------
-- Ticket #17774
----------------

INSERT
  INTO administracao.relatorio
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo )
     VALUES
     ( 3
     , 6
     , 20
     , 'Relatorio de Bens Baixados'
     , 'relatorioBemBaixado.rptdesign'
     );


----------------
-- Ticket #17577
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN

    PERFORM 1
       FROM pg_class
          , pg_attribute
          , pg_type
      WHERE pg_class.relname      = 'natureza_lancamento'
        AND pg_attribute.attname  = 'numcgm_usuario'
        AND pg_attribute.attnum   > 0
        AND pg_attribute.attrelid = pg_class.oid
        AND pg_attribute.atttypid = pg_type.oid
          ;

    IF NOT FOUND THEN

        ALTER TABLE almoxarifado.natureza_lancamento ADD COLUMN numcgm_usuario INTEGER;
        UPDATE almoxarifado.natureza_lancamento  SET numcgm_usuario = 0;
        ALTER  TABLE almoxarifado.natureza_lancamento ALTER COLUMN numcgm_usuario SET NOT NULL;
        ALTER  TABLE almoxarifado.natureza_lancamento ADD CONSTRAINT fk_natureza_lancamento_3 FOREIGN KEY                      (numcgm_usuario)
                                                                                              REFERENCES administracao.usuario (numcgm);

    END IF;

END;
$$ LANGUAGE 'plpgsql';

SELECT manutencao();
DROP FUNCTION manutencao();

