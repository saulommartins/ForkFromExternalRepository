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
* $Id:  $
*
* Versão 1.93.4
*/


----------------
-- Ticket #14083
----------------

CREATE TABLE orcamento.organograma_nivel (
    cod_organograma     INTEGER         NOT NULL,
    cod_nivel           INTEGER         NOT NULL,
    tipo                CHAR(1)         NOT NULL,
    timestamp           TIMESTAMP       NOT NULL DEFAULT ('now'::text)::timestamp(3) WITH time zone,
    CONSTRAINT pk_organograma_nivel     PRIMARY KEY                         (cod_organograma, cod_nivel, timestamp),
    CONSTRAINT fk_organograma_nivel_1   FOREIGN KEY                         (cod_organograma)
                                        REFERENCES organograma.organograma  (cod_organograma),
    CONSTRAINT fk_organograma_nivel_2   FOREIGN KEY                         (cod_organograma,cod_nivel)
                                        REFERENCES organograma.nivel        (cod_organograma,cod_nivel),
    CONSTRAINT uk_organograma_nivel_1   UNIQUE                              (cod_organograma ,tipo, timestamp),
    CONSTRAINT ck_organograma_nivel_1   CHECK( tipo IN ('O','U'))
);

GRANT ALL ON orcamento.organograma_nivel TO GROUP urbem;


-- ??? --
---------

ALTER TABLE contabilidade.nota_explicativa ADD COLUMN dt_inicial DATE  NOT NULL DEFAULT ('NOW'::TEXT)::DATE;
ALTER TABLE contabilidade.nota_explicativa ADD COLUMN dt_final DATE  NOT NULL DEFAULT ('NOW'::TEXT)::DATE;
ALTER TABLE contabilidade.nota_explicativa DROP CONSTRAINT pk_nota_explicativa;
ALTER TABLE contabilidade.nota_explicativa ADD CONSTRAINT pk_nota_explicativa PRIMARY KEY (cod_acao, dt_inicial, dt_final);

----------------
-- Ticket #13867
----------------

INSERT INTO administracao.relatorio
         ( cod_gestao
         , cod_modulo
         , cod_relatorio
         , nom_relatorio
         , arquivo )
    VALUES ( 2
         , 10
         , 8
         , 'RelatÃ³rio Empenhos a Pagar'
         , 'empenhoEmpenhoPagar.rptdesign'
         );
