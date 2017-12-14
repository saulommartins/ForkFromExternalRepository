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
* Versão 003.
*/

/*-----------
VERSÃO 1.52.0
*/



---- ticket 12031 Criação das tabelas de configuração da exportação CAGED

select atualizarBanco('
CREATE TABLE ima.configuracao_caged (
  cod_configuracao INTEGER NOT NULL,
  cod_cnae         INTEGER NOT NULL,
  tipo_declaracao  CHAR(1) NOT NULL,
  CONSTRAINT pk_configuracao_caged PRIMARY KEY(cod_configuracao),
  CONSTRAINT fk_configuracao_caged_1  FOREIGN KEY(cod_cnae) REFERENCES economico.cnae_fiscal(cod_cnae));
');
select atualizarBanco('
CREATE TABLE ima.caged_autorizado_cei (
  cod_configuracao  INTEGER  NOT NULL,
  num_cei           CHAR(12) NOT NULL,
  CONSTRAINT pk_caged_autorizado_cei PRIMARY KEY(cod_configuracao),
  CONSTRAINT fk_caged_autorizado_cei_1 FOREIGN KEY(cod_configuracao)  REFERENCES ima.configuracao_caged(cod_configuracao));
');
select atualizarBanco('
CREATE TABLE ima.caged_autorizado_cgm (
  cod_configuracao INTEGER NOT NULL,
  numcgm           INTEGER NOT NULL,
  num_autorizacao  CHAR(7) NOT NULL,
  CONSTRAINT pk_caged_autorizado_cgm PRIMARY KEY(cod_configuracao),
  CONSTRAINT fk_caged_autorizado_cgm_1 FOREIGN KEY(cod_configuracao) REFERENCES ima.configuracao_caged(cod_configuracao),
  CONSTRAINT fk_caged_autorizado_cgm_2 FOREIGN KEY(numcgm) REFERENCES sw_cgm(numcgm));
');
select atualizarBanco('
CREATE TABLE ima.caged_estabelecimento (
  cod_configuracao INTEGER  NOT NULL,
  num_cei          CHAR(12) NOT NULL,
  CONSTRAINT pk_caged_estabelecimento PRIMARY KEY(cod_configuracao),
  CONSTRAINT fk_caged_estabelecimento_1 FOREIGN KEY(cod_configuracao) REFERENCES ima.configuracao_caged(cod_configuracao));
');
select atualizarBanco('
CREATE TABLE ima.caged_evento (
  cod_configuracao INTEGER NOT NULL,
  cod_evento       INTEGER NOT NULL,
  CONSTRAINT pk_caged_evento PRIMARY KEY(cod_configuracao, cod_evento),
  CONSTRAINT fk_caged_evento_1 FOREIGN KEY(cod_configuracao) REFERENCES ima.configuracao_caged(cod_configuracao),
  CONSTRAINT fk_caged_evento_2 FOREIGN KEY(cod_evento) REFERENCES folhapagamento.evento(cod_evento));
');

select atualizarBanco('
CREATE TABLE ima.caged_sub_divisao (
  cod_configuracao INTEGER NOT NULL,
  cod_sub_divisao  INTEGER NOT NULL,
  CONSTRAINT pk_caged_sub_divisao PRIMARY KEY(cod_configuracao, cod_sub_divisao),
  CONSTRAINT fk_caged_sub_divisao_1 FOREIGN KEY(cod_configuracao) REFERENCES ima.configuracao_caged(cod_configuracao),
  CONSTRAINT fk_caged_sub_divisao_2 FOREIGN KEY(cod_sub_divisao)  REFERENCES pessoal.sub_divisao(cod_sub_divisao));
');

select atualizarBanco('
GRANT INSERT, DELETE, UPDATE, SELECT ON ima.configuracao_caged  TO GROUP urbem;
');
select atualizarBanco('
GRANT INSERT, DELETE, UPDATE, SELECT ON ima.caged_autorizado_cei  TO GROUP urbem;
');
select atualizarBanco('
GRANT INSERT, DELETE, UPDATE, SELECT ON ima.caged_autorizado_cgm  TO GROUP urbem;
');
select atualizarBanco('
GRANT INSERT, DELETE, UPDATE, SELECT ON ima.caged_estabelecimento TO GROUP urbem;
');
select atualizarBanco('
GRANT INSERT, DELETE, UPDATE, SELECT ON ima.caged_evento          TO GROUP urbem;
');
select atualizarBanco('
GRANT INSERT, DELETE, UPDATE, SELECT ON ima.caged_sub_divisao     TO GROUP urbem;
');

UPDATE administracao.funcionalidade SET ordem = 2 WHERE cod_funcionalidade = 353;
UPDATE administracao.funcionalidade SET ordem = 3 WHERE cod_funcionalidade = 357;


INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     VALUES (2200
          , 354
          , 'FMConfiguracaoCAGED.php'
          , 'configurar'
          , 16
          , ''
          , 'Exportação CAGED');

INSERT INTO administracao.funcionalidade
            (cod_funcionalidade
          , cod_modulo
          , nom_funcionalidade
          , nom_diretorio
          , ordem )
     VALUES (404
          , 40
          , 'CAGED'
          , 'instancias/caged/'
          , 8);

INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     VALUES (2201
          , 404
          , 'FLExportarCAGED.php'
          , 'exportar'
          , 1
          , ''
          , 'Exportação CAGED');



select atualizarBanco('
CREATE TABLE pessoal.caged (
  cod_caged INTEGER     NOT NULL,
  num_caged INTEGER     NOT NULL,
  descricao VARCHAR(60) NOT NULL,
  tipo      CHAR(1)     NOT NULL,
  CONSTRAINT pk_caged PRIMARY KEY(cod_caged)
);
');

select atualizarBanco('
INSERT INTO pessoal.caged VALUES (1,31, \'Dispensa sem justa causa\',\'D\');
');
select atualizarBanco('
INSERT INTO pessoal.caged VALUES (2,32, \'Dispensa por justa causa\',\'D\');
');
select atualizarBanco('
INSERT INTO pessoal.caged VALUES (3,40, \'A pedido (espontâneo)\',\'D\');
');
select atualizarBanco('
INSERT INTO pessoal.caged VALUES (4,43, \'Término de contrato por prazo determinado\',\'D\');
');
select atualizarBanco('
INSERT INTO pessoal.caged VALUES (5,45, \'Término de contrato\',\'D\');
');
select atualizarBanco('
INSERT INTO pessoal.caged VALUES (6,50, \'Aposentado\',\'D\');
');
select atualizarBanco('
INSERT INTO pessoal.caged VALUES (7,60, \'Morte\',\'D\');
');

select atualizarBanco('
INSERT INTO pessoal.caged VALUES (8,80, \'Transferência de saída\',\'D\');
');

select atualizarBanco('
INSERT INTO pessoal.caged VALUES (9,10, \'Primeiro emprego\',\'A\');
');

select atualizarBanco('
INSERT INTO pessoal.caged VALUES (10,20, \'Reemprego\',\'A\');
');

select atualizarBanco('
INSERT INTO pessoal.caged VALUES (11,25, \'Contrato por prazo determinado\',\'A\');
');

select atualizarBanco('
INSERT INTO pessoal.caged VALUES (12,35, \'Reintegração\',\'A\');
');

select atualizarBanco('
INSERT INTO pessoal.caged VALUES (13,70, \'Transferência de entrada\',\'A\');
');

select atualizarBanco('
CREATE TABLE pessoal.tipo_admissao_caged (
  cod_tipo_admissao INTEGER NOT NULL,
  cod_caged         INTEGER NOT NULL,
  CONSTRAINT pk_tipo_admissao_caged PRIMARY KEY(cod_tipo_admissao, cod_caged),
  CONSTRAINT fk_tipo_admissao_caged_1 FOREIGN KEY(cod_tipo_admissao) REFERENCES pessoal.tipo_admissao(cod_tipo_admissao),
  CONSTRAINT fk_tipo_admissao_caged_2 FOREIGN KEY(cod_caged) REFERENCES pessoal.caged(cod_caged)
);
');

select atualizarBanco('
CREATE TABLE pessoal.causa_rescisao_caged (
  cod_causa_rescisao INTEGER NOT NULL,
  cod_caged          INTEGER NOT NULL,
  CONSTRAINT pk_causa_rescisao_caged PRIMARY KEY(cod_causa_rescisao, cod_caged),
  CONSTRAINT fk_causa_rescisao_caged_1 FOREIGN KEY(cod_causa_rescisao) REFERENCES pessoal.causa_rescisao(cod_causa_rescisao),
  CONSTRAINT fk_causa_rescisao_caged_2 FOREIGN KEY(cod_caged) REFERENCES pessoal.caged(cod_caged)
);
');

select atualizarBanco('
CREATE TABLE pessoal.tipo_deficiencia (
  cod_tipo_deficiencia INTEGER     NOT NULL,
  num_deficiencia      INTEGER     NOT NULL,
  descricao            VARCHAR(20) NOT NULL,
  CONSTRAINT pk_tipo_deficiencia PRIMARY KEY(cod_tipo_deficiencia)
);
');

alter table administracao.tabelas_rh add column sequencia integer;

update administracao.tabelas_rh set sequencia = 1;

alter table administracao.tabelas_rh alter column sequencia set not null;

INSERT INTO administracao.tabelas_rh (schema_cod,nome_tabela,sequencia) VALUES (1,'tipo_deficiencia',1);

update administracao.tabelas_rh set sequencia = 2 where schema_cod = 1 and nome_tabela = 'cid';


select atualizarBanco('
INSERT INTO pessoal.tipo_deficiencia VALUES (1,0, \'Não Informado\');
');

select atualizarBanco('
INSERT INTO pessoal.tipo_deficiencia VALUES (2,1, \'Física\');
');

select atualizarBanco('
INSERT INTO pessoal.tipo_deficiencia VALUES (3,2, \'Auditiva\');
');

select atualizarBanco('
INSERT INTO pessoal.tipo_deficiencia VALUES (4,3, \'Visual\');
');

select atualizarBanco('
INSERT INTO pessoal.tipo_deficiencia VALUES (5,4, \'Mental\');
');

select atualizarBanco('
INSERT INTO pessoal.tipo_deficiencia VALUES (6,5, \'Múltipla\');
');

select atualizarBanco('
INSERT INTO pessoal.tipo_deficiencia VALUES (7,6, \'Reabilitado\');
');

select atualizarBanco('
ALTER TABLE pessoal.cid ADD COLUMN cod_tipo_deficiencia INTEGER;
');

select atualizarBanco('
UPDATE pessoal.cid SET cod_tipo_deficiencia = 1 WHERE cod_tipo_deficiencia IS NULL;
');

select atualizarBanco('
ALTER TABLE pessoal.cid ALTER COLUMN cod_tipo_deficiencia SET NOT NULL;
');

select atualizarBanco('
ALTER TABLE pessoal.cid ADD CONSTRAINT fk_cid_1 FOREIGN KEY(cod_tipo_deficiencia) REFERENCES pessoal.tipo_deficiencia(cod_tipo_deficiencia);
');

select atualizarBanco('
GRANT INSERT, DELETE, UPDATE, SELECT ON pessoal.caged TO GROUP urbem;
');

select atualizarBanco('
GRANT INSERT, DELETE, UPDATE, SELECT ON pessoal.tipo_deficiencia TO GROUP urbem;
');

select atualizarBanco('
GRANT INSERT, DELETE, UPDATE, SELECT ON pessoal.causa_rescisao_caged TO GROUP urbem;
');

select atualizarBanco('
GRANT INSERT, DELETE, UPDATE, SELECT ON pessoal.tipo_admissao_caged TO GROUP urbem;
');

INSERT INTO administracao.tabelas_rh VALUES (1,'caged',1);

------
-- Ticket 12062
------
select atualizarBanco('
INSERT INTO pessoal.tipo_admissao
            (cod_tipo_admissao
          , descricao   )
     VALUES (5
          , \'Reintegração\');
');
select atualizarBanco('
INSERT INTO pessoal.tipo_admissao
            (cod_tipo_admissao
          , descricao   )
     VALUES (6
          , \'Recondução (específico para servidor público)\');
');
select atualizarBanco('
INSERT INTO pessoal.tipo_admissao
            (cod_tipo_admissao
          , descricao   )
     VALUES (7
          , \'Reversão ou readaptação (específico para servidor público)\');
');

select atualizarBanco('
INSERT INTO pessoal.tipo_admissao_caged VALUES(1,9);
');

select atualizarBanco('
INSERT INTO pessoal.tipo_admissao_caged VALUES(2,10);
');

select atualizarBanco('
INSERT INTO pessoal.tipo_admissao_caged VALUES(3,13);
');

select atualizarBanco('
INSERT INTO pessoal.tipo_admissao_caged VALUES(4,13);
');

select atualizarBanco('
INSERT INTO pessoal.tipo_admissao_caged VALUES(5,12);
');

----------
-- Ticket 12085
----------

INSERT INTO administracao.relatorio
            (cod_gestao
          , cod_modulo
          , cod_relatorio
          , nom_relatorio
          , arquivo)
     VALUES (4
          , 40
          , 3
          , 'Totais Arquivo CAGED'
          , 'totalArquivoCaged.rptdesign');

----------
-- Ticket 12282
----------


select atualizarBanco('
CREATE TABLE pessoal.contrato_servidor_local_historico (
  cod_local    integer   NOT NULL,
  timestamp    timestamp NOT NULL,
  cod_contrato integer   NOT NULL,
  CONSTRAINT pk_contrato_servidor_local_historico PRIMARY KEY(cod_local, timestamp, cod_contrato),
  CONSTRAINT fk_contrato_servidor_local_historico_1 FOREIGN KEY(cod_local) REFERENCES organograma.local (cod_local),
  CONSTRAINT fk_contrato_servidor_local_historico_2 FOREIGN KEY(cod_contrato) REFERENCES pessoal.contrato_servidor(cod_contrato)
);
');

select atualizarBanco('
GRANT INSERT, DELETE, UPDATE, SELECT ON pessoal.contrato_servidor_local_historico TO GROUP urbem;
');

--Manutenção para adicionar campos de configuração na tabela administracao.configuracao_entidade
create or replace function manutencao() returns void as $$
declare
    stSql       varchar;
    reRegistro  RECORD;
    inCodEntidade integer;
begin
    stSql := 'select *
                from administracao.configuracao
               where exercicio = 2008
                 and cod_modulo = 40
                 and (parametro ilike \'%num_sequencial_arquivo_banrisul_%\' 
                   or parametro ilike \'%dt_num_sequencial_arquivo_banrisul_%\')                
                 ';
    FOR reRegistro IN EXECUTE stSql LOOP                 
        --raise notice '%',reRegistro;
        inCodEntidade := translate(reRegistro.parametro,'num_sequencial_arquivo_banrisul_ dt_num_sequencial_arquivo_banrisul_','');
        stSql := 'INSERT INTO administracao.configuracao_entidade (exercicio,cod_entidade,cod_modulo,parametro,valor) 
                  VALUES ('||reRegistro.exercicio||','||inCodEntidade||','||reRegistro.cod_modulo||',\''||reRegistro.parametro||'\',\''||reRegistro.valor||'\')';
        execute stSql;
        --raise notice '%',stSql;                  
    end loop;
end
$$ LANGUAGE 'plpgsql';

select manutencao();
drop function manutencao();


-- Ticket #12365
INSERT INTO administracao.tabelas_rh
            (schema_cod
          , nome_tabela
          , sequencia)
     VALUES (1
          , 'rais_afastamento'
          ,1);
          
create or replace function manutencao() returns void as $$
declare
    stSql                   varchar;
    reRegistro              RECORD;
    inCodEntidade           integer;
    inCodEntidadePrefeitura integer;
    inContador              integer;
begin
    inCodEntidadePrefeitura := selectIntoInteger('select valor from administracao.configuracao where parametro = \'cod_entidade_prefeitura\' and exercicio = \'2008\'');
    stSql := 'select cod_entidade
                from administracao.entidade_rh
               where exercicio = 2008
                 and cod_entidade != '||inCodEntidadePrefeitura||'
              group by cod_entidade
                 ';
    FOR reRegistro IN EXECUTE stSql LOOP                 
        inContador := selectIntoInteger('select count(1) from pessoal_'||reRegistro.cod_entidade||'.rais_afastamento');
        if inContador = 0 then
            stSql := 'INSERT INTO pessoal_'||reRegistro.cod_entidade||'.rais_afastamento SELECT * FROM pessoal.rais_afastamento';
            execute stSql;
            --raise notice '%',stSql;                  
        end if;
    end loop;
end
$$ LANGUAGE 'plpgsql';

select manutencao();
drop function manutencao();          
          
          


