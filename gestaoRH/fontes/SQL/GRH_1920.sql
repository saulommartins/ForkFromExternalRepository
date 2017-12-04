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
* URBEM Soluções de Gestï¿½o Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: GRH_1920.sql 59612 2014-09-02 12:00:51Z gelson $
*
* Versão 1.92.0
*/

----------------
-- Ticket #13307
----------------

select atualizarBanco('
CREATE TABLE diarias.diaria_empenho (
    cod_diaria          INTEGER     NOT NULL,
    cod_contrato        INTEGER     NOT NULL,
    timestamp           TIMESTAMP   NOT NULL,
    exercicio           CHAR(4)     NOT NULL,
    cod_entidade        INTEGER     NOT NULL,
    cod_autorizacao     INTEGER     NOT NULL,
    CONSTRAINT pk_diarias_empenho   PRIMARY KEY                                 (cod_diaria, cod_contrato, timestamp),
    CONSTRAINT fk_diarias_empenho_1 FOREIGN KEY                                 (cod_diaria, cod_contrato, timestamp)
                                    REFERENCES diarias.diaria                   (cod_diaria, cod_contrato, timestamp),
    CONSTRAINT fk_diarias_empenho_2 FOREIGN KEY                                 (exercicio, cod_entidade, cod_autorizacao)
                                    REFERENCES empenho.autorizacao_empenho      (exercicio, cod_entidade, cod_autorizacao)
);
');

select atualizarBanco('
GRANT ALL ON diarias.diaria_empenho TO GROUP urbem;
');

select atualizarBanco('
ALTER TABLE diarias.diaria ADD COLUMN vl_unitario NUMERIC(14,2) NOT NULL;
');

select atualizarBanco('
ALTER TABLE diarias.tipo_diaria_despesa DROP CONSTRAINT fk_tipo_diaria_despesa_1;
');
select atualizarBanco('
ALTER TABLE diarias.tipo_diaria_despesa DROP CONSTRAINT pk_tipo_diaria_despesa;
');
select atualizarBanco('
ALTER TABLE diarias.diaria              DROP CONSTRAINT fk_diaria_2;
');
select atualizarBanco('
ALTER TABLE diarias.tipo_diaria         DROP CONSTRAINT pk_tipo_diaria;
');

select atualizarBanco('
ALTER TABLE diarias.tipo_diaria_despesa DROP COLUMN timestamp;
');
select atualizarBanco('
ALTER TABLE diarias.tipo_diaria         DROP COLUMN timestamp;
');
select atualizarBanco('
ALTER TABLE diarias.diaria              DROP COLUMN timestamp_tipo;
');

select atualizarBanco('
ALTER TABLE diarias.tipo_diaria         ADD CONSTRAINT pk_tipo_diaria PRIMARY KEY(cod_tipo);
');
select atualizarBanco('
ALTER TABLE diarias.tipo_diaria_despesa ADD CONSTRAINT pk_tipo_diaria_despesa PRIMARY KEY (cod_tipo);
');
select atualizarBanco('
ALTER TABLE diarias.tipo_diaria_despesa ADD CONSTRAINT fk_tipo_diaria_despesa_1 FOREIGN KEY (cod_tipo) REFERENCES diarias.tipo_diaria;
');
select atualizarBanco('
ALTER TABLE diarias.diaria              ADD CONSTRAINT fk_diaria_2              FOREIGN KEY (cod_tipo) REFERENCES diarias.tipo_diaria;
');

----------------------------------------------------------------------
-- ADICIONANDO COLUNA reajuste - 20080904 - Solicitado por Diego Souza
----------------------------------------------------------------------
select atualizarBanco('
ALTER TABLE pessoal.contrato_servidor_salario ADD COLUMN   reajuste BOOLEAN DEFAULT false;
');
select atualizarBanco('
UPDATE      pessoal.contrato_servidor_salario SET          reajuste = FALSE;
');
select atualizarBanco('
ALTER TABLE pessoal.contrato_servidor_salario ALTER COLUMN reajuste SET NOT NULL;
');

select atualizarBanco('
ALTER TABLE pessoal.contrato_servidor_nivel_padrao ADD COLUMN   reajuste BOOLEAN DEFAULT false;
');
select atualizarBanco('
UPDATE      pessoal.contrato_servidor_nivel_padrao SET          reajuste = FALSE;
');
select atualizarBanco('
ALTER TABLE pessoal.contrato_servidor_nivel_padrao ALTER COLUMN reajuste SET NOT NULL;
');


----------------
-- Ticket #16643
----------------
select atualizarBanco('
ALTER TABLE pessoal.assentamento_assentamento ADD COLUMN abreviacao CHAR(3);
');


----------------
-- Ticket #12643
----------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2316
          , 233
          , 'FLCertidaoTempoServico.php'
          , 'imprimir'
          , 11
          , ''
          , 'Certidão Tempo Serviço'
          );

INSERT INTO administracao.relatorio
         ( cod_gestao
         , cod_modulo
         , cod_relatorio
         , nom_relatorio
         , arquivo )
    VALUES ( 4
         , 22
         , 9
         , 'Certidão Tempo Serviço Completa'
         , 'certidaoTempoServicoCompleta.rptdesign'
         );

INSERT INTO administracao.relatorio
         ( cod_gestao
         , cod_modulo
         , cod_relatorio
         , nom_relatorio
         , arquivo )
    VALUES ( 4
         , 22
         , 10
         , 'Certidão Tempo Serviço Descritiva'
         , 'certidaoTempoServicoDescritiva.rptdesign'
         );

INSERT INTO administracao.relatorio
         ( cod_gestao
         , cod_modulo
         , cod_relatorio
         , nom_relatorio
         , arquivo )
    VALUES ( 4
         , 22
         , 11
         , 'Certidão Tempo Serviço Modelo INSS'
         , 'certidaoTempoServicoINSS.rptdesign'
         );


----------------
-- Ticket #13234
----------------

select atualizarBanco('
ALTER TABLE estagio.curso_instituicao_ensino_mes DROP CONSTRAINT pk_curso_instituicao_ensino_mes;
');
select atualizarBanco('
ALTER TABLE estagio.curso_instituicao_ensino_mes ADD  CONSTRAINT pk_curso_instituicao_ensino_mes PRIMARY KEY (numcgm, cod_curso);
');


------------------------------------------------------
-- MANUTENCAO P/ OPERADORES /ESFERA - DIEGO - 20080912
------------------------------------------------------

create or replace function manutencao() returns void as $$
declare
    stSql varchar;
    reRegistro record;
begin
    stSql := 'select assentamento.cod_assentamento
                   , assentamento.timestamp
                   , assentamento.cod_esfera
                   , assentamento_assentamento.cod_operador
                from pessoal.assentamento
                   , pessoal.assentamento_assentamento
               where assentamento.cod_assentamento = assentamento_assentamento.cod_assentamento';

    for reRegistro in execute stSql loop
        if reRegistro.cod_esfera != 3 and reRegistro.cod_operador != 1 then
            stSql := 'update pessoal.assentamento_assentamento
                         set cod_operador = 1
                       where cod_assentamento = '||reRegistro.cod_assentamento;
            execute stSql;
        end if;
    end loop;
end;
$$ language 'plpgsql';
 
select manutencao();
drop function manutencao(); 



CREATE TYPE colunasDadosCertidaoModeloINSS
    AS (
      periodo_inicial      VARCHAR
    , periodo_final        VARCHAR
    , lotacao              VARCHAR
    , funcao               VARCHAR
);

CREATE TYPE colunasDadosCertidaoTempoServidoCompleta 
    AS (
       cod_contrato             integer     
     , registro                 integer     
     , nom_cgm                  varchar     
     , dt_nascimento            varchar     
     , sexo                     varchar     
     , rg                       varchar     
     , dt_emissao_rg            varchar     
     , orgao_emissor_rg         varchar     
     , servidor_pis_pasep       varchar     
     , cpf                      varchar     
     , nacionalidade            varchar     
     , escolaridade             varchar     
     , nome_pai                 varchar     
     , nome_mae                 varchar     
     , nr_titulo_eleitor        varchar     
     , zona_titulo              varchar     
     , secao_titulo             varchar     
     , nom_estado               varchar     
     , nom_municipio            varchar     
     , sigla_uf                 varchar     
     , sigla_cid                varchar     
     , descricao_cid            varchar     
     , numero                   varchar     
     , serie                    varchar     
     , orgao_expedidor          varchar     
     , dt_emissao               varchar     
     , sigla_uf_ctps            varchar     
     , dt_pis_pasep             varchar     
     , nr_carteira_res          varchar     
     , cat_reservista           varchar     
     , origem_reservista        varchar     
     , dt_nomeacao              varchar     
     , dt_posse                 varchar     
     , dt_admissao              varchar     
     , exercicio                varchar     
     , num_norma                varchar     
     , nom_norma                varchar     
     , cod_tipo_admissao        varchar     
     , tipo_admissao            varchar     
     , num_ocorrencia           varchar     
     , ocorrencia               varchar     
     , cargo                    varchar     
     , regime                   varchar     
     , sub_divisao              varchar     
     , especialidade            varchar     
     , funcao                   varchar     
     , regime_funcao            varchar     
     , sub_divisao_funcao       varchar     
     , especialidade_funcao     varchar     
     , horas_mensais            varchar     
     , horas_semanais           varchar     
     , salario                  varchar     
     , padrao                   varchar     
     , orgao                    varchar     
     , descricao_orgao          varchar     
     , local                    varchar     
     , dt_rescisao              varchar     
     , num_causa                varchar     
     , descricao_causa          varchar 
);

CREATE TYPE colunasDadosCertidaoTempoServidoDescritiva 
    AS (
      nom_cgm              VARCHAR
    , registro             INTEGER
    , regime               VARCHAR
    , regime_formatado     VARCHAR
    , cargo                VARCHAR
    , nom_cgm_entidade     VARCHAR
    , dt_regime            VARCHAR
    , previdencia          VARCHAR
    , exonerado_rescindido VARCHAR
    , efetividade          TEXT
    , nome_cidade          VARCHAR
    , lotacao              VARCHAR
    , dt_emissao           VARCHAR
);

CREATE TYPE colunasGradeEfetividade
    AS (
       ano      integer     
     , jan      varchar
     , fev      varchar     
     , mar      varchar     
     , abr      varchar     
     , mai      varchar     
     , jun      varchar     
     , jul      varchar     
     , ago      varchar     
     , set      varchar     
     , out      varchar     
     , nov      varchar     
     , dez      varchar     
     , total    integer
);

CREATE TYPE colunasDadosResumoOcorrencias
    AS (
      assentamento      varchar
    , operador          varchar
    , qtd_dias          integer   
); 


CREATE TYPE colunasResumoDiarias AS (
    orgao               VARCHAR(100),
    unidade             VARCHAR(100),
    red_dotacao         VARCHAR,
    rubrica_despesa     VARCHAR(150),
    descricao_despesa   VARCHAR(160),
    valor               NUMERIC,
    quantidade          NUMERIC,
    num_pao             VARCHAR,
    numcgm              VARCHAR,
    fornecedor          VARCHAR,
    periodo             VARCHAR,
    ato                 VARCHAR,
    cargo               VARCHAR,
    cod_diaria          INTEGER,
    cod_contrato        INTEGER,
    timestamp           VARCHAR,
    motivo_viagem       VARCHAR
);
