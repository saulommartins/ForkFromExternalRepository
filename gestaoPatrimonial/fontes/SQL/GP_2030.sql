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
* Versao 2.03.0
*
* Fabio Bertoldi - 20140908
*
*/

----------------
-- Ticket #20200
----------------

INSERT INTO contabilidade.historico_contabil VALUES (962, '2014', 'Vlr ref Depreciação Acumulada mês', true, true);

ALTER TABLE patrimonio.depreciacao DROP CONSTRAINT fk_depreciacao_1;
ALTER TABLE patrimonio.depreciacao ADD  CONSTRAINT fk_depreciacao_1 FOREIGN KEY              (cod_bem)
                                                                    REFERENCES patrimonio.bem(cod_bem);
ALTER TABLE patrimonio.depreciacao ALTER COLUMN timestamp SET DEFAULT ('now'::text)::timestamp(3) with time zone;

----------------
-- Ticket #22145
----------------

ALTER TABLE licitacao.convenio      ADD   COLUMN cod_norma_autorizativa INTEGER;
UPDATE      licitacao.convenio               SET cod_norma_autorizativa = 0;
ALTER TABLE licitacao.convenio      ALTER COLUMN cod_norma_autorizativa SET NOT NULL;

ALTER TABLE licitacao.convenio      ADD  CONSTRAINT fk_convenio_5    FOREIGN KEY            (cod_norma_autorizativa)
                                                                     REFERENCES normas.norma(cod_norma);

ALTER TABLE licitacao.convenio_aditivos      ADD   COLUMN cod_norma_autorizativa INTEGER;
UPDATE      licitacao.convenio_aditivos               SET cod_norma_autorizativa = 0;
ALTER TABLE licitacao.convenio_aditivos      ALTER COLUMN cod_norma_autorizativa SET NOT NULL;

ALTER TABLE licitacao.convenio_aditivos      ADD  CONSTRAINT fk_convenio_aditivos_3 FOREIGN KEY            (cod_norma_autorizativa)
                                                                                    REFERENCES normas.norma(cod_norma);

ALTER TABLE licitacao.tipo_convenio ADD   COLUMN cod_uf_tipo_convenio INTEGER;
UPDATE      licitacao.tipo_convenio          SET cod_uf_tipo_convenio = 1;
ALTER TABLE licitacao.tipo_convenio ALTER COLUMN cod_uf_tipo_convenio SET NOT NULL;

ALTER TABLE licitacao.convenio      ADD   COLUMN cod_uf_tipo_convenio INTEGER;
UPDATE      licitacao.convenio               SET cod_uf_tipo_convenio = 1;
ALTER TABLE licitacao.convenio      ALTER COLUMN cod_uf_tipo_convenio SET NOT NULL;

ALTER TABLE licitacao.convenio      DROP CONSTRAINT fk_convenio_2;
ALTER TABLE licitacao.tipo_convenio DROP CONSTRAINT pk_tipo_convenio;
ALTER TABLE licitacao.tipo_convenio ADD  CONSTRAINT pk_tipo_convenio PRIMARY KEY                       (cod_tipo_convenio, cod_uf_tipo_convenio);
ALTER TABLE licitacao.convenio      ADD  CONSTRAINT fk_convenio_2    FOREIGN KEY                       (cod_tipo_convenio, cod_uf_tipo_convenio)
                                                                     REFERENCES licitacao.tipo_convenio(cod_tipo_convenio, cod_uf_tipo_convenio);

ALTER TABLE licitacao.tipo_convenio ALTER COLUMN descricao TYPE VARCHAR(60);

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE
    stSQL       VARCHAR;
    reRecord    RECORD;
BEGIN
    stSQL := 'SELECT cod_uf
                FROM sw_uf
               WHERE cod_pais = 1
                 AND cod_uf   > 1
                 AND cod_uf  != 3
                   ;
             ';
    FOR reRecord IN EXECUTE stSQL LOOP
        INSERT
          INTO licitacao.tipo_convenio
             ( cod_tipo_convenio
             , cod_uf_tipo_convenio
             , descricao
             )
        SELECT cod_tipo_convenio
             , reRecord.cod_uf
             , descricao
          FROM licitacao.tipo_convenio
         WHERE cod_uf_tipo_convenio = 1
             ;
    END LOOP;
END;
$$ LANGUAGE 'plpgsql';

SELECT         manutencao();
DROP FUNCTION manutencao();

INSERT INTO licitacao.tipo_convenio(cod_tipo_convenio, cod_uf_tipo_convenio, descricao) VALUES ( 1, 3, 'Delegação de recursos e encargos'                );
INSERT INTO licitacao.tipo_convenio(cod_tipo_convenio, cod_uf_tipo_convenio, descricao) VALUES ( 2, 3, 'Transferência voluntária'                        );
INSERT INTO licitacao.tipo_convenio(cod_tipo_convenio, cod_uf_tipo_convenio, descricao) VALUES ( 3, 3, 'Termo de Convênio'                               );
INSERT INTO licitacao.tipo_convenio(cod_tipo_convenio, cod_uf_tipo_convenio, descricao) VALUES ( 4, 3, 'Termo de Denúncia'                               );
INSERT INTO licitacao.tipo_convenio(cod_tipo_convenio, cod_uf_tipo_convenio, descricao) VALUES ( 5, 3, 'Termo de Cooperação Técnico e Científico'        );
INSERT INTO licitacao.tipo_convenio(cod_tipo_convenio, cod_uf_tipo_convenio, descricao) VALUES ( 6, 3, 'Termo de Cooperação Técnico e Financeiro'        );
INSERT INTO licitacao.tipo_convenio(cod_tipo_convenio, cod_uf_tipo_convenio, descricao) VALUES ( 7, 3, 'Termo de Parceria'                               );
INSERT INTO licitacao.tipo_convenio(cod_tipo_convenio, cod_uf_tipo_convenio, descricao) VALUES ( 8, 3, 'Termo Aditivo de Convênio'                       );
INSERT INTO licitacao.tipo_convenio(cod_tipo_convenio, cod_uf_tipo_convenio, descricao) VALUES ( 9, 3, 'Termo Aditivo de Cooperação Técnico e Científico');
INSERT INTO licitacao.tipo_convenio(cod_tipo_convenio, cod_uf_tipo_convenio, descricao) VALUES (10, 3, 'Termo Aditivo de Cooperação Técnico e Financeiro');
INSERT INTO licitacao.tipo_convenio(cod_tipo_convenio, cod_uf_tipo_convenio, descricao) VALUES (11, 3, 'Termo Aditivo de Parceria'                       );
INSERT INTO licitacao.tipo_convenio(cod_tipo_convenio, cod_uf_tipo_convenio, descricao) VALUES (12, 3, 'Cessão'                                          );
INSERT INTO licitacao.tipo_convenio(cod_tipo_convenio, cod_uf_tipo_convenio, descricao) VALUES (13, 3, 'Aditivo de cessão'                               );
INSERT INTO licitacao.tipo_convenio(cod_tipo_convenio, cod_uf_tipo_convenio, descricao) VALUES (14, 3, 'Termo de Responsabilidade'                       );
INSERT INTO licitacao.tipo_convenio(cod_tipo_convenio, cod_uf_tipo_convenio, descricao) VALUES (15, 3, 'Termo Aditivo de Responsabilidade'               );


---------------------------------------------------------
-- ELIMINANDO '\' DA DESCRICAO DOS BENS - Silvia 20140919
---------------------------------------------------------

UPDATE patrimonio.bem set descricao = replace(descricao, '\', '');

