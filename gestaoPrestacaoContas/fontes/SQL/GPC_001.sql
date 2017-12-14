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
* Versão 001.
*/

   -- Ticket #11299 - Criado link relatório Anexo 14.
   UPDATE administracao.relatorio SET arquivo = 'RREUAnexo14.rptdesign' WHERE cod_gestao = 6 and cod_modulo = 36 AND cod_relatorio = 23;

   UPDATE administracao.modulo set nom_modulo = 'TCE - RN' WHERE cod_modulo=49;
   UPDATE administracao.acao   set nom_acao ='Arquivos SAGRES', complemento_acao='' WHERE cod_acao = 1726 ;

   --  Ticket #11299 - Criação do link de menu relatório Anexo 14
      INSERT INTO administracao.acao ( cod_acao
                                     , cod_funcionalidade
                                     , nom_arquivo
                                     , parametro
                                     , ordem
                                     , complemento_acao
                                     , nom_acao)
      VALUES ( 2189
             , 314
             , 'FLModelosRREO.php'
             , 'anexo14'
             , 40
             , ''
             , 'Anexo 14');




    -- Ticket #11552 - Criada link Anexo 6.
      INSERT INTO  administracao.relatorio  ( cod_gestao, cod_modulo, cod_relatorio, nom_relatorio, arquivo) values ( 6 ,36 ,24, 'Anexo 6', 'RREOAnexo6.rptdesign' );

      INSERT INTO administracao.acao ( cod_acao
                                     , cod_funcionalidade
                                     , nom_arquivo
                                     , parametro
                                     , ordem
                                     , complemento_acao
                                     , nom_acao)
      VALUES ( 2190
             , 314
             , 'FLModelosRREO.php'
             , 'anexo6'
             , 50
             , ''
             , 'Anexo 6');

INSERT INTO administracao.funcionalidade ( cod_funcionalidade,
cod_modulo, nom_funcionalidade, nom_diretorio, ordem )
VALUES ( 403 --verificar novo número
,49
,'Configuração'
,'instancias/configuracao/'
,'1'
);

INSERT INTO administracao.acao ( cod_acao, cod_funcionalidade,
nom_arquivo, parametro, ordem, complemento_acao, nom_acao)
VALUES ( 2191 --verificar novo número
,403
,'FMManterProcessoFundamento.php'
,'manter'
,1
,''
,'Relacionamento Processo - Fundamento Legal'
);

update administracao.acao set cod_funcionalidade=403 , nom_acao='Configurar Órgão' where cod_acao=2159;

CREATE OR REPLACE FUNCTION manutencao() RETURNS BOOLEAN AS $$
DECLARE
   varSchema     VARCHAR;
BEGIN

   SELECT nspname
      INTO varSchema
      FROM pg_namespace
   WHERE nspname   = 'tcern'
   ;

   If varSchema Is Null THEN
      Create Schema tcern;
      Grant Usage On Schema tcern to Group urbem;
   End If;

   RETURN true;
END;
$$ LANGUAGE 'plpgsql';

select manutencao();
Drop Function manutencao();


CREATE TABLE tcern.processo_fundamento (
    cod_licitacao       integer        not null
   ,cod_modalidade      integer        not null
   ,cod_entidade        integer        not null
   ,exercicio           character(4)   not null
   ,fundamento_legal    character(4)   not null
   ,PRIMARY KEY (cod_licitacao, cod_modalidade, cod_entidade, exercicio)
);

GRANT ALL ON tcern.processo_fundamento TO urbem;

insert into administracao.acao values (2192,365,'FMManterOrcamento.php','configurar',3,'Orçamento','Definir as Configurações do Orçamento');

insert into administracao.acao values (2193,365,'FMManterRelacionamentoDespesaExtra.php','configurar',4,'','Relacionar Despesa Extra');

insert into administracao.acao values (2194,365,'FMManterRelacionamentoReceitaExtra.php','configurar',5,'','Relacionar Receita Extra');

insert into administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     values (2195
          , 314
          , 'FLModelosRREO.php'
          , 'anexo10'
          , 38
          , ''
          , 'Anexo 10');

-- 2008
CREATE TABLE tcepb.tipo_categoria_obra (
    exercicio   char(4)         not null,
    cod_tipo    integer         not null,
    descricao   varchar(100)    not null,
    PRIMARY KEY (exercicio,cod_tipo)
);

CREATE TABLE tcepb.tipo_obra (
    exercicio   char(4)         not null,
    cod_tipo    integer         not null,
    descricao   varchar(200)    not null,
    PRIMARY KEY (exercicio,cod_tipo)
);

CREATE TABLE tcepb.tipo_fonte_obras (
    exercicio   char(4)         not null,
    cod_tipo    integer         not null,
    descricao   varchar(100)    not null,
    PRIMARY KEY (exercicio,cod_tipo)
);

CREATE TABLE tcepb.tipo_situacao (
    exercicio   char(4)         not null,
    cod_tipo    integer         not null,
    descricao   varchar(50)    not null,
    PRIMARY KEY (exercicio,cod_tipo)
);

GRANT ALL ON tcepb.tipo_categoria_obra TO urbem;
GRANT ALL ON tcepb.tipo_obra TO urbem;
GRANT ALL ON tcepb.tipo_fonte_obras TO urbem;
GRANT ALL ON tcepb.tipo_situacao TO urbem;

INSERT INTO tcepb.tipo_obra (exercicio, cod_tipo, descricao) VALUES ( '2008', 0,'Nenhuma');
INSERT INTO tcepb.tipo_obra (exercicio, cod_tipo, descricao) VALUES ( '2008', 1,'Abastecimento d`agua');
INSERT INTO tcepb.tipo_obra (exercicio, cod_tipo, descricao) VALUES ( '2008', 2,' Açude / Barragem');
INSERT INTO tcepb.tipo_obra (exercicio, cod_tipo, descricao) VALUES ( '2008', 3,'Bueiro');
INSERT INTO tcepb.tipo_obra (exercicio, cod_tipo, descricao) VALUES ( '2008', 4,'Pronto Socorro');
INSERT INTO tcepb.tipo_obra (exercicio, cod_tipo, descricao) VALUES ( '2008', 5,'Creche');
INSERT INTO tcepb.tipo_obra (exercicio, cod_tipo, descricao) VALUES ( '2008', 6,'Eletrificação');
INSERT INTO tcepb.tipo_obra (exercicio, cod_tipo, descricao) VALUES ( '2008', 7,'Escola');
INSERT INTO tcepb.tipo_obra (exercicio, cod_tipo, descricao) VALUES ( '2008', 8,'Estrada de Terra');
INSERT INTO tcepb.tipo_obra (exercicio, cod_tipo, descricao) VALUES ( '2008', 9,'Galeria Pluvial');
INSERT INTO tcepb.tipo_obra (exercicio, cod_tipo, descricao) VALUES ( '2008',10,'Ginásio Poliesportivo / Quadra de Esporte');
INSERT INTO tcepb.tipo_obra (exercicio, cod_tipo, descricao) VALUES ( '2008',11,'Hospital');
INSERT INTO tcepb.tipo_obra (exercicio, cod_tipo, descricao) VALUES ( '2008',12,'Mercado Público / Abatedouro');
INSERT INTO tcepb.tipo_obra (exercicio, cod_tipo, descricao) VALUES ( '2008',13,'Passagem Molhada');
INSERT INTO tcepb.tipo_obra (exercicio, cod_tipo, descricao) VALUES ( '2008',14,'Pavimentação em Asfalto');
INSERT INTO tcepb.tipo_obra (exercicio, cod_tipo, descricao) VALUES ( '2008',15,'Pavimentação em Paralelepípedo');
INSERT INTO tcepb.tipo_obra (exercicio, cod_tipo, descricao) VALUES ( '2008',16,'Poço Amazonas');
INSERT INTO tcepb.tipo_obra (exercicio, cod_tipo, descricao) VALUES ( '2008',17,'Poço Artesiano');
INSERT INTO tcepb.tipo_obra (exercicio, cod_tipo, descricao) VALUES ( '2008',18,'Ponte');
INSERT INTO tcepb.tipo_obra (exercicio, cod_tipo, descricao) VALUES ( '2008',19,'Posto de Saúde');
INSERT INTO tcepb.tipo_obra (exercicio, cod_tipo, descricao) VALUES ( '2008',20,'Praça');
INSERT INTO tcepb.tipo_obra (exercicio, cod_tipo, descricao) VALUES ( '2008',21,'Presídio');
INSERT INTO tcepb.tipo_obra (exercicio, cod_tipo, descricao) VALUES ( '2008',22,'Saneamento Básico');
INSERT INTO tcepb.tipo_obra (exercicio, cod_tipo, descricao) VALUES ( '2008',24,'Outras');



INSERT INTO tcepb.tipo_categoria_obra (exercicio, cod_tipo, descricao) VALUES ( '2008', 0,'Nenhuma');
INSERT INTO tcepb.tipo_categoria_obra (exercicio, cod_tipo, descricao) VALUES ( '2008', 1,'Ampliação');
INSERT INTO tcepb.tipo_categoria_obra (exercicio, cod_tipo, descricao) VALUES ( '2008', 2,'Construção');
INSERT INTO tcepb.tipo_categoria_obra (exercicio, cod_tipo, descricao) VALUES ( '2008', 3,'Recuperação');
INSERT INTO tcepb.tipo_categoria_obra (exercicio, cod_tipo, descricao) VALUES ( '2008', 4,'Reforma');



INSERT INTO tcepb.tipo_fonte_obras (exercicio, cod_tipo, descricao) VALUES ( '2008', 0,'Nenhum');
INSERT INTO tcepb.tipo_fonte_obras (exercicio, cod_tipo, descricao) VALUES ( '2008', 1,'Próprios');
INSERT INTO tcepb.tipo_fonte_obras (exercicio, cod_tipo, descricao) VALUES ( '2008', 2,'Estatuais');
INSERT INTO tcepb.tipo_fonte_obras (exercicio, cod_tipo, descricao) VALUES ( '2008', 3,'Federais');
INSERT INTO tcepb.tipo_fonte_obras (exercicio, cod_tipo, descricao) VALUES ( '2008', 4,'Próprios / Estaduais');
INSERT INTO tcepb.tipo_fonte_obras (exercicio, cod_tipo, descricao) VALUES ( '2008', 5,'Próprios / Federais');
INSERT INTO tcepb.tipo_fonte_obras (exercicio, cod_tipo, descricao) VALUES ( '2008', 6,'Outros');



INSERT INTO tcepb.tipo_situacao (exercicio, cod_tipo, descricao) VALUES ( '2008', 0,'Nenhum');
INSERT INTO tcepb.tipo_situacao (exercicio, cod_tipo, descricao) VALUES ( '2008', 1,'Em andamento');
INSERT INTO tcepb.tipo_situacao (exercicio, cod_tipo, descricao) VALUES ( '2008', 2,'Paralisada');
INSERT INTO tcepb.tipo_situacao (exercicio, cod_tipo, descricao) VALUES ( '2008', 3,'Em ritmo lento');



CREATE TABLE tcepb.obras (
    exercicio           char(4)         not null,
    num_obra            integer         not null,
    dt_cadastro         date            ,
    patrimonio          char(1)         ,
    localidade          varchar(200)    ,
    descricao           varchar(300)    ,
    cod_tipo_obra       integer         ,
    cod_tipo_categoria  integer         ,
    cod_tipo_fonte      integer         ,
    --obra_inicio
    mes_ano_estimado_fim char(6)        ,
    dt_inicio           date            ,
    --obra_conclusao
    dt_conclusao        date            ,
    dt_recebimento      date            ,
    --obra_situacao
    cod_tipo_situacao   integer         ,

    PRIMARY KEY (exercicio,num_obra),
    FOREIGN KEY (exercicio,cod_tipo_obra) REFERENCES tcepb.tipo_obra (exercicio,cod_tipo),
    FOREIGN KEY (exercicio,cod_tipo_categoria) REFERENCES tcepb.tipo_categoria_obra (exercicio,cod_tipo),
    FOREIGN KEY (exercicio,cod_tipo_fonte) REFERENCES tcepb.tipo_fonte_obras (exercicio,cod_tipo),
    FOREIGN KEY (exercicio,cod_tipo_situacao) REFERENCES tcepb.tipo_situacao (exercicio,cod_tipo)
);

GRANT ALL ON tcepb.obras TO urbem;

INSERT INTO administracao.acao ( cod_acao, cod_funcionalidade, nom_arquivo, parametro, ordem, complemento_acao, nom_acao)
VALUES ( 2196
,365
,'FMManterObras.php'
,'incluir'
,3
,''
,'Incluir Obra'
);

INSERT INTO administracao.acao ( cod_acao, cod_funcionalidade, nom_arquivo, parametro, ordem, complemento_acao, nom_acao)
VALUES ( 2197
,365
,'FLManterObras.php'
,'alterar'
,4
,''
,'Alterar Obra'
);

INSERT INTO administracao.acao ( cod_acao, cod_funcionalidade, nom_arquivo, parametro, ordem, complemento_acao, nom_acao)
VALUES ( 2198
,365
,'FLManterObras.php'
,'excluir'
,5
,''
,'Excluir Obra'
);


CREATE TABLE tcern.receita_tc
   (exercicio      char(4) not null,
   cod_receita     integer not null,
   cod_tc          char(9) not null,
   CONSTRAINT pk_receita PRIMARY KEY (exercicio, cod_receita),
   CONSTRAINT fk_receita_1 FOREIGN KEY (exercicio, cod_receita) REFERENCES orcamento.receita(exercicio,cod_receita));

GRANT ALL ON tcern.receita_tc TO GROUP urbem;

INSERT INTO administracao.acao ( cod_acao, cod_funcionalidade, nom_arquivo, parametro, ordem, complemento_acao, nom_acao)
VALUES (2199
,403
,'FMManterConfiguracaoReceita.php'
,'configura'
,30
,''
,'Configurar Receita'
);
