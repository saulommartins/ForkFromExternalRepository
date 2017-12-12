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
* $Id: GPC_1916.sql 59612 2014-09-02 12:00:51Z gelson $
*
* VersÃ£o 1.91.6
*/

----------------
-- Ticket #15080
----------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2709
          , 364
          , 'FMVincularTipoRetencao.php'
          , 'configurar'
          , 28
          , ''
          , 'Vincular Tipo Retenção'
          );

CREATE TABLE tcmgo.tipo_retencao(
    exercicio   CHAR(4)         NOT NULL,
    cod_tipo    INTEGER         NOT NULL,
    descricao   VARCHAR         NOT NULL,
    CONSTRAINT pk_tipo_retencao PRIMARY KEY (exercicio, cod_tipo)
);

GRANT ALL ON tcmgo.tipo_retencao TO GROUP urbem;

INSERT INTO tcmgo.tipo_retencao VALUES ('2009',2 ,'IRRF'     );
INSERT INTO tcmgo.tipo_retencao VALUES ('2009',3 ,'ISS'      );
INSERT INTO tcmgo.tipo_retencao VALUES ('2009',98,'Proventos');
INSERT INTO tcmgo.tipo_retencao VALUES ('2009',99,'Outros'   );

CREATE TABLE tcmgo.de_para_tipo_retencao(
    exercicio_tipo  CHAR(4)     NOT NULL,
    cod_tipo        INTEGER     NOT NULL,
    exercicio       CHAR(4)     NOT NULL,
    cod_plano       INTEGER     NOT NULL,
    CONSTRAINT pk_de_para_tipo_retencao     PRIMARY KEY                                 (exercicio_tipo, cod_tipo,exercicio, cod_plano),
    CONSTRAINT fk_de_para_tipo_retencao_1   FOREIGN KEY                                 (exercicio_tipo, cod_tipo)
                                            REFERENCES tcmgo.tipo_retencao              (exercicio, cod_tipo),
    CONSTRAINT fk_de_para_tipo_retencao_2   FOREIGN KEY                                 (exercicio, cod_plano)
                                            REFERENCES contabilidade.plano_analitica    (exercicio, cod_plano)
);

GRANT ALL ON tcmgo.de_para_tipo_retencao TO GROUP urbem;

------------------------
--  Ticket #14935
------------------------
CREATE OR REPLACE FUNCTION replicaTipoObra() returns boolean as $$
DECLARE
  reTemp RECORD;

  stSql  VARCHAR;
BEGIN
  
  FOR reTemp  IN SELECT * FROM tcepb.tipo_obra
  LOOP
	stSql := 'INSERT INTO tcepb.tipo_obra VALUES (''2009'', '||reTemp.cod_tipo||','''||reTemp.descricao||''')';
	execute stSql;
  END LOOP;

FOR reTemp  IN SELECT * FROM tcepb.tipo_categoria_obra
  LOOP
	stSql := 'INSERT INTO tcepb.tipo_categoria_obra VALUES (''2009'', '||reTemp.cod_tipo||','''||reTemp.descricao||''')';
	execute stSql;
  END LOOP;

FOR reTemp  IN SELECT * FROM tcepb.tipo_fonte_obras
  LOOP
	stSql := 'INSERT INTO tcepb.tipo_fonte_obras VALUES (''2009'', '||reTemp.cod_tipo||','''||reTemp.descricao||''')';
	execute stSql;
  END LOOP;

FOR reTemp  IN SELECT * FROM tcepb.tipo_situacao
  LOOP
	stSql := 'INSERT INTO tcepb.tipo_situacao VALUES (''2009'', '||reTemp.cod_tipo||','''||reTemp.descricao||''')';
	execute stSql;
  END LOOP;

return true;
END;
$$ LANGUAGE 'plpgsql';

begin;
select replicaTipoObra();
drop function replicatipoobra();
