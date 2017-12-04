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
* Versão 1.92.3
*/

--------------------------------------------------------------------
-- ADICIONADOS MODULOS - solicitado por TONISMAR BERNARDO - 20081014
--------------------------------------------------------------------

INSERT 
  INTO administracao.modulo 
     ( cod_modulo
     , cod_responsavel
     , nom_modulo
     , nom_diretorio
     , ordem
     , cod_gestao ) 
VALUES ( 52
     , 0
     , 'TCE - SC'
     , 'TCESC'
     , 70
     , 6
     );

INSERT 
  INTO administracao.modulo 
     ( cod_modulo
     , cod_responsavel
     , nom_modulo
     , nom_diretorio
     , ordem
     , cod_gestao ) 
VALUES ( 53
     , 0
     , 'SIACE - LRF/MG'
     , 'LRFMG'
     , 80
     , 6
     );

INSERT 
  INTO administracao.modulo 
     ( cod_modulo
     , cod_responsavel
     , nom_modulo
     , nom_diretorio
     , ordem
     , cod_gestao ) 
VALUES ( 54
     , 0
     , 'SIACE - PCA/MG'
     , 'PCAMG'
     , 90
     , 6
     );


-----------------------
-- Ticket #15252 #15393
-----------------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN

    PERFORM 1
       FROM administracao.configuracao
      WHERE cod_modulo = 2
        AND exercicio  = '2009'
        AND parametro  = 'cnpj'
        AND valor      = '08924078000104'
          ;

    IF NOT FOUND THEN
        INSERT INTO tcepb.elemento_tribunal (estrutural, exercicio, descricao) VALUES ('39.86', '2009', 'Seguros'                                  );
        INSERT INTO tcepb.elemento_tribunal (estrutural, exercicio, descricao) VALUES ('39.87', '2009', 'Serviços Postais'                         );
        INSERT INTO tcepb.elemento_tribunal (estrutural, exercicio, descricao) VALUES ('30.88', '2009', 'Multas e Juros'                           );
        INSERT INTO tcepb.elemento_tribunal (estrutural, exercicio, descricao) VALUES ('32.88', '2009', 'Multas e Juros'                           );
        INSERT INTO tcepb.elemento_tribunal (estrutural, exercicio, descricao) VALUES ('33.88', '2009', 'Multas e Juros'                           );
        INSERT INTO tcepb.elemento_tribunal (estrutural, exercicio, descricao) VALUES ('35.88', '2009', 'Multas e Juros'                           );
        INSERT INTO tcepb.elemento_tribunal (estrutural, exercicio, descricao) VALUES ('36.88', '2009', 'Multas e Juros'                           );
        INSERT INTO tcepb.elemento_tribunal (estrutural, exercicio, descricao) VALUES ('37.88', '2009', 'Multas e Juros'                           );
        INSERT INTO tcepb.elemento_tribunal (estrutural, exercicio, descricao) VALUES ('38.88', '2009', 'Multas e Juros'                           );
        INSERT INTO tcepb.elemento_tribunal (estrutural, exercicio, descricao) VALUES ('39.88', '2009', 'Multas e Juros'                           );
        INSERT INTO tcepb.elemento_tribunal (estrutural, exercicio, descricao) VALUES ('47.88', '2009', 'Multas e Juros'                           );
        INSERT INTO tcepb.elemento_tribunal (estrutural, exercicio, descricao) VALUES ('51.88', '2009', 'Multas e Juros'                           );
        INSERT INTO tcepb.elemento_tribunal (estrutural, exercicio, descricao) VALUES ('52.88', '2009', 'Multas e Juros'                           );
        INSERT INTO tcepb.elemento_tribunal (estrutural, exercicio, descricao) VALUES ('61.88', '2009', 'Multas e Juros'                           );
        INSERT INTO tcepb.elemento_tribunal (estrutural, exercicio, descricao) VALUES ('91.88', '2009', 'Multas e Juros'                           );
        INSERT INTO tcepb.elemento_tribunal (estrutural, exercicio, descricao) VALUES ('92.88', '2009', 'Multas e Juros'                           );
        INSERT INTO tcepb.elemento_tribunal (estrutural, exercicio, descricao) VALUES ('39.89', '2009', 'Serviço Notarial e Registral'             );
        INSERT INTO tcepb.elemento_tribunal (estrutural, exercicio, descricao) VALUES ('36.90', '2009', 'Serviço Técnico Profissional não Eventual');
        INSERT INTO tcepb.elemento_tribunal (estrutural, exercicio, descricao) VALUES ('39.91', '2009', 'Serviço de Acesso a Internet'             );
    END IF;

END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


----------------
-- Ticket #15393
----------------

DELETE
  FROM tcepb.elemento_de_para
 WHERE estrutural = '39.88';

DELETE
  FROM tcepb.elemento_tribunal
 WHERE estrutural = '39.88';


----------------
-- Ticket #15445
----------------

CREATE TABLE stn.riscos_fiscais (
    cod_risco       INTEGER         NOT NULL,
    cod_entidade    INTEGER         NOT NULL,
    exercicio       CHAR(4)         NOT NULL,
    descricao       VARCHAR(100)    NOT NULL,
    valor           NUMERIC(14,2)   NOT NULL,
    CONSTRAINT pk_riscos_fiscais    PRIMARY KEY                     (cod_risco, cod_entidade, exercicio),
    CONSTRAINT fk_riscos_fiscais_1  FOREIGN KEY                     (cod_entidade, exercicio)
                                    REFERENCES orcamento.entidade   (cod_entidade, exercicio)
);

GRANT ALL ON stn.riscos_fiscais TO GROUP urbem;

CREATE TABLE stn.providencias (
    cod_providencia     INTEGER         NOT NULL,
    cod_risco           INTEGER         NOT NULL,
    cod_entidade        INTEGER         NOT NULL,
    exercicio           CHAR(4)         NOT NULL,
    descricao           VARCHAR(450)    NOT NULL,
    valor           NUMERIC(14,2)   NOT NULL,
    CONSTRAINT pk_providencias          PRIMARY KEY                     (cod_providencia, cod_risco, cod_entidade, exercicio),
    CONSTRAINT fk_providencias_1        FOREIGN KEY                     (cod_risco, cod_entidade, exercicio)
                                        REFERENCES stn.riscos_fiscais   (cod_risco, cod_entidade, exercicio)
);

GRANT ALL ON stn.providencias TO GROUP urbem;


----------------
-- Ticket #15448
----------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2733
          , 406
          , 'FMManterRiscosFiscais.php'
          , 'incluir'
          , 13
          , ''
          , 'Incluir Riscos Fiscais'
          );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2734
          , 406
          , 'FLManterRiscosFiscais.php'
          , 'alterar'
          , 14
          , ''
          , 'Alterar Riscos Fiscais'
          );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2735
          , 406
          , 'FLManterRiscosFiscais.php'
          , 'excluir'
          , 15
          , ''
          , 'Excluir Riscos Fiscais'
          );


----------------
-- Ticket #15478
----------------

INSERT INTO administracao.funcionalidade
          ( cod_funcionalidade
          , cod_modulo
          , nom_funcionalidade
          , nom_diretorio
          , ordem )
     VALUES ( 464
          , 36
          , 'ARF'
          , 'instancias/relatorios/'
          , 5
          );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2741
          , 464
          , 'FLModelosARF.php'
          , 'demons2'
          , 1
          , ''
          , 'Demonstrativo II'
          );


-----------------------------------------------------------
-- ADICIONANDO RELATORIO Dem. Riscos Fiscais e Providências
-----------------------------------------------------------

INSERT INTO administracao.relatorio
          ( cod_gestao
          , cod_modulo
          , cod_relatorio
          , nom_relatorio
          , arquivo )
     VALUES ( 6
          , 36
          , 36
          , 'ARF - Dem. Riscos Fiscais e Providências'
          , 'relatorioRiscosFiscais.rptdesign'
          );


--------------------------------------------
-- ADICIONANDO RELATORIO 'demonstrativo vii'
--------------------------------------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2742
          , 411
          , 'FLModelosAMF.php'
          , 'demons7'
          , 7
          , ''
          , 'Demonstrativo VII'
          );

INSERT INTO administracao.relatorio
          ( cod_gestao
          , cod_modulo
          , cod_relatorio
          , nom_relatorio
          , arquivo )
     VALUES ( 6
          , 36
          , 37
          , 'AMF - Demonstrativo VII'
          , 'AMFDemonstrativo7.rptdesign'
);


----------------
-- Ticket #15512
----------------

CREATE TABLE stn.nota_explicativa(
    cod_acao        INTEGER         NOT NULL,
    dt_inicial          DATE        NOT NULL DEFAULT ('NOW'::text)::date,
    dt_final            DATE        NOT NULL DEFAULT ('NOW'::text)::date,
    nota_explicativa    TEXT        NOT NULL,
    CONSTRAINT pk_nota_explicativa      PRIMARY KEY                     (cod_acao, dt_inicial, dt_final),
    CONSTRAINT fk_nota_explicativa_1    FOREIGN KEY                     (cod_acao)
                                        REFERENCES administracao.acao   (cod_acao)
);
GRANT ALL ON stn.nota_explicativa TO GROUP urbem;


------------------------------------------
-- ADICIONADA ACAO Manter Nota Explicativa
------------------------------------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2744
          , 406
          , 'FMManterNotasExplicativas.php'
          , 'incluir'
          , 16
          , ''
          , 'Manter Notas Explicativas'
          );


