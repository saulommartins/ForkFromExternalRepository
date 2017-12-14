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

----
-- Tiket 12045
-----
ALTER TABLE arrecadacao.compensacao ADD COLUMN aplicar_acrescimos BOOLEAN;
UPDATE arrecadacao.compensacao SET aplicar_acrescimos = TRUE;
ALTER TABLE arrecadacao.compensacao ALTER COLUMN aplicar_acrescimos SET NOT NULL;

----
-- Tiket 12052
-----
CREATE OR REPLACE function public.manutencao() RETURNS VOID as $$

DECLARE

   intCodFuncao         INTEGER;

   intCodVariavel       INTEGER;

BEGIN

   SELECT (max(cod_funcao) + 1)
     INTO intCodFuncao
     FROM administracao.funcao
    WHERE cod_modulo     = 25
      AND cod_biblioteca = 1;

   IF intCodFuncao IS NULL THEN

      intCodFuncao := 1;

   END IF;

   SELECT (max(cod_variavel) + 1)
     INTO intCodVariavel
     FROM administracao.variavel
    WHERE cod_modulo     = 25
      AND cod_biblioteca = 1
      AND cod_funcao     = intCodFuncao;

   IF intCodVariavel IS NULL THEN

      intCodVariavel := 1;

   END IF;

   INSERT INTO administracao.funcao
               (cod_modulo
             , cod_biblioteca
             , cod_funcao
             , cod_tipo_retorno
             , nom_funcao)
        VALUES (25
             , 1
             , intCodFuncao
             , 1
             , 'buscaMesAberturaEmpresa');

   INSERT INTO administracao.variavel
               (cod_modulo
             , cod_biblioteca
             , cod_funcao
             , cod_variavel
             , nom_variavel
             , cod_tipo
             , valor_inicial)
        VALUES (25
             , 1
             , intCodFuncao
             , intCodVariavel
             , 'inInscricaoEconomica'
             , 1
             ,'');

   INSERT INTO administracao.parametro
               (cod_modulo
             , cod_biblioteca
             , cod_funcao
             , cod_variavel
             , ordem)
        VALUES (25
             , 1
             , intCodFuncao
             , intCodVariavel
             , 1);

   RETURN;

END;

$$ language 'plpgsql';

------
-- Ticket # 12057
------

 INSERT INTO administracao.arquivos_documento
      VALUES (
               ( SELECT MAX(cod_arquivo) + 1   from administracao.arquivos_documento )
             , 'alvara_diversos_sanitario.odt'
             , '60315c6ab27d6b2b0a2cd5d946867de4'
             , true
             );
 INSERT INTO administracao.modelo_documento
      VALUES (
               ( SELECT MAX(cod_documento) + 1 from administracao.modelo_documento )
             , 'Alvará Sanitário - diversos'
             , 'alvara_diversos_sanitario.odt'
             , 1
             );
 INSERT INTO administracao.modelo_arquivos_documento
      VALUES (
               464
             , ( SELECT MAX(cod_documento)     from administracao.modelo_documento )
             , ( SELECT MAX(cod_arquivo)       from administracao.arquivos_documento )
             , true
             , true
             , 1
             );
 INSERT INTO administracao.modelo_arquivos_documento
      VALUES (
               467
             , ( SELECT MAX(cod_documento)     from administracao.modelo_documento )
             , ( SELECT MAX(cod_arquivo)       from administracao.arquivos_documento )
             , true
             , true
             , 1
             );


 INSERT INTO administracao.arquivos_documento
      VALUES (
               ( SELECT MAX(cod_arquivo) + 1   from administracao.arquivos_documento )
             , 'alvara_atividade_sanitario.odt'
             , '60315c6ab27d6b2b0a2cd5d946867de4'
             , true
             );
 INSERT INTO administracao.modelo_documento
      VALUES (
               ( SELECT MAX(cod_documento) + 1 from administracao.modelo_documento)
             , 'Alvará Sanitário - por atividade'
             , 'alvara_atividade_sanitario.odt'
             , 1
             );
 INSERT INTO administracao.modelo_arquivos_documento
      VALUES (
               462
             , ( SELECT MAX(cod_documento)     from administracao.modelo_documento )
             , ( SELECT MAX(cod_arquivo)       from administracao.arquivos_documento )
             , true
             , true
             , 1);
 INSERT INTO administracao.modelo_arquivos_documento
      VALUES (
               465
             , ( SELECT MAX(cod_documento)     from administracao.modelo_documento )
             , ( SELECT MAX(cod_arquivo)       from administracao.arquivos_documento )
             , true
             , true
             , 1
             );


 INSERT INTO administracao.arquivos_documento
      VALUES (
               ( SELECT MAX(cod_arquivo) + 1   from administracao.arquivos_documento )
             , 'alvara_horario_especial_sanitario.odt'
             , '60315c6ab27d6b2b0a2cd5d946867de4'
             , true
             );
 INSERT INTO administracao.modelo_documento
      VALUES (
               ( SELECT MAX(cod_documento) + 1 from administracao.modelo_documento )
             , 'Alvará Sanitário - horário especial'
             , 'alvara_horario_especial_sanitario.odt'
             , 1
             );
 INSERT INTO administracao.modelo_arquivos_documento
      VALUES (
               463
             , ( SELECT MAX(cod_documento)     from administracao.modelo_documento )
             , ( SELECT MAX(cod_arquivo)       from administracao.arquivos_documento )
             , true
             , true
             , 1
             );
 INSERT INTO administracao.modelo_arquivos_documento
      VALUES (
               466
             , ( SELECT MAX(cod_documento)     from administracao.modelo_documento )
             , ( SELECT MAX(cod_arquivo)       from administracao.arquivos_documento )
             , true
             , true
             , 1
             );

----------
-- Ticket 12064
----------

CREATE TABLE imobiliario.tipo_baixa (
    cod_tipo                    integer                 NOT NULL,
    nom_tipo                    varchar(100)            NOT NULL,
    CONSTRAINT pk_tipo_baixa                            PRIMARY KEY (cod_tipo)
);

CREATE TABLE imobiliario.tipo_licenca (
    cod_tipo                    integer                 NOT NULL,
    nom_tipo                    varchar(80)             NOT NULL,
    CONSTRAINT pk_tipo_licenca                          PRIMARY KEY (cod_tipo)
);

CREATE TABLE imobiliario.tipo_licenca_documento (
    cod_tipo                    integer                 NOT NULL,
    cod_tipo_documento          integer                 NOT NULL,
    cod_documento               integer                 NOT NULL,
    CONSTRAINT pk_tipo_licenca_documento                PRIMARY KEY (cod_tipo, cod_tipo_documento, cod_documento),
    CONSTRAINT fk_tipo_licenca_documento_1              FOREIGN KEY (cod_tipo) REFERENCES  imobiliario.tipo_licenca(cod_tipo),
    CONSTRAINT fk_tipo_licenca_documento_2              FOREIGN KEY (cod_tipo_documento,cod_documento)
                                                        REFERENCES administracao.modelo_documento (cod_tipo_documento,cod_documento)
);

CREATE TABLE imobiliario.permissao (
    cod_tipo                    integer                 NOT NULL,
    numcgm                      integer                 NOT NULL,
    timestamp                   timestamp               DEFAULT ('now'::text)::timestamp(3) with time zone,
    CONSTRAINT pk_permissao                             PRIMARY KEY (cod_tipo, numcgm, timestamp),
    CONSTRAINT fk_permissao_1                           FOREIGN KEY (cod_tipo) REFERENCES  imobiliario.tipo_licenca(cod_tipo),
    CONSTRAINT fk_permissao_2                           FOREIGN KEY (numcgm) REFERENCES  administracao.usuario(numcgm)
);

CREATE TABLE imobiliario.licenca (
    cod_licenca                 integer                 NOT NULL,
    exercicio                   char(4)                 NOT NULL,
    cod_tipo                    integer                 NOT NULL,
    numcgm                      integer                 NOT NULL,
    timestamp                   timestamp               NOT NULL,
    dt_inicio                   date                    NOT NULL,
    dt_termino                  date,
    observacao                  text,
    CONSTRAINT pk_licenca                               PRIMARY KEY (cod_licenca, exercicio),
    CONSTRAINT fk_licenca_1                             FOREIGN KEY (cod_tipo, numcgm, timestamp) REFERENCES  imobiliario.permissao(cod_tipo, numcgm, timestamp)
);

CREATE TABLE imobiliario.licenca_baixa (
    cod_licenca                 integer                 NOT NULL,
    exercicio                   char(4)                 NOT NULL,
    dt_inicio                   date                    NOT NULL,
    cod_tipo                    integer                 NOT NULL,
    dt_termino                  date                            ,
    timestamp                   timestamp               NOT NULL    DEFAULT ('now'::text)::timestamp(3) with time zone,
    motivo                      text                    NOT NULL,
    CONSTRAINT pk_licenca_baixa                         PRIMARY KEY (cod_licenca, exercicio, dt_inicio),
    CONSTRAINT fk_licenca_baixa_1                       FOREIGN KEY (cod_licenca, exercicio) REFERENCES  imobiliario.licenca(cod_licenca, exercicio),
    CONSTRAINT fk_licenca_baixa_2                       FOREIGN KEY (cod_tipo) REFERENCES  imobiliario.tipo_baixa(cod_tipo)
);

CREATE TABLE imobiliario.licenca_documento (
    cod_licenca                 integer                 NOT NULL,
    exercicio                   char(4)                 NOT NULL,
    timestamp                   timestamp               NOT NULL    DEFAULT ('now'::text)::timestamp(3) with time zone,
    cod_tipo_documento          integer                 NOT NULL,
    cod_documento               integer                 NOT NULL,
    num_documento               integer                 NOT NULL,
    CONSTRAINT pk_licenca_documento                     PRIMARY KEY (exercicio, cod_licenca, timestamp),
    CONSTRAINT fk_licenca_documento_1                   FOREIGN KEY (cod_licenca, exercicio) REFERENCES  imobiliario.licenca(cod_licenca, exercicio),
    CONSTRAINT fk_licenca_documento_2                   FOREIGN KEY (cod_documento, cod_tipo_documento) REFERENCES  administracao.modelo_documento(cod_documento, cod_tipo_documento)
);

CREATE TABLE imobiliario.emissao_documento (
    cod_licenca                 integer                 NOT NULL,
    exercicio                   char(4)                 NOT NULL,
    timestamp                   timestamp               NOT NULL,
    numcgm                      integer                 NOT NULL,
    dt_emissao                  date                    NOT NULL,
    timestamp_emissao           timestamp               NOT NULL    DEFAULT ('now'::text)::timestamp(3) with time zone,
    CONSTRAINT pk_emissao_documento                     PRIMARY KEY (cod_licenca, exercicio, timestamp),
    CONSTRAINT fk_emissao_documento_1                   FOREIGN KEY (cod_licenca, exercicio, timestamp) REFERENCES  imobiliario.licenca_documento(cod_licenca, exercicio, timestamp),
    CONSTRAINT fk_emissao_documento_2                   FOREIGN KEY (numcgm) REFERENCES  administracao.usuario(numcgm)
);

CREATE TABLE imobiliario.licenca_processo (
    cod_licenca                 integer                 NOT NULL,
    exercicio                   char(4)                 NOT NULL,
    cod_processo                integer                 NOT NULL,
    ano_exercicio               char(4)                 NOT NULL,
    CONSTRAINT pk_licenca_processo                      PRIMARY KEY (cod_licenca, exercicio, cod_processo, ano_exercicio),
    CONSTRAINT fk_licenca_processo_1                    FOREIGN KEY (cod_licenca, exercicio) REFERENCES  imobiliario.licenca(cod_licenca, exercicio),
    CONSTRAINT fk_licenca_processo_2                    FOREIGN KEY (cod_processo, ano_exercicio) REFERENCES  sw_processo(cod_processo, ano_exercicio)
   );

   CREATE TABLE imobiliario.licenca_responsavel_tecnico (
       cod_licenca                 integer                 NOT NULL,
       exercicio                   char(4)                 NOT NULL,
       numcgm                      integer                 NOT NULL,
       sequencia                   integer                 NOT NULL,
       timestamp                   timestamp               NOT NULL    DEFAULT ('now'::text)::timestamp(3) with time zone,
       CONSTRAINT pk_licenca_responsavel_tecnico           PRIMARY KEY (cod_licenca, exercicio, numcgm, sequencia, timestamp),
       CONSTRAINT fk_licenca_responsavel_tecnico_1         FOREIGN KEY (cod_licenca, exercicio) REFERENCES  imobiliario.licenca(cod_licenca, exercicio),
       CONSTRAINT fk_licenca_responsavel_tecnico_2         FOREIGN KEY (numcgm, sequencia) REFERENCES  economico.responsavel(numcgm, sequencia)
   );

   CREATE TABLE imobiliario.licenca_imovel (
       cod_licenca                 integer                 NOT NULL,
       exercicio                   char(4)                 NOT NULL,
       inscricao_municipal         integer                 NOT NULL,
       CONSTRAINT pk_licenca_imovel                        PRIMARY KEY (cod_licenca, exercicio, inscricao_municipal),
       CONSTRAINT fk_licenca_imovel_1                      FOREIGN KEY (cod_licenca, exercicio) REFERENCES  imobiliario.licenca(cod_licenca, exercicio),
       CONSTRAINT fk_licenca_imovel_2                      FOREIGN KEY (inscricao_municipal) REFERENCES  imobiliario.imovel(inscricao_municipal)
   );

   CREATE TABLE imobiliario.licenca_imovel_area (
       cod_licenca                 integer                 NOT NULL,
       exercicio                   char(4)                 NOT NULL,
       inscricao_municipal         integer                 NOT NULL,
       area                        numeric(14,2)           NOT NULL,
       CONSTRAINT pk_licenca_imovel_area                   PRIMARY KEY (cod_licenca, exercicio, inscricao_municipal),
       CONSTRAINT fk_licenca_imovel_area_1                 FOREIGN KEY (cod_licenca, exercicio, inscricao_municipal) REFERENCES  imobiliario.licenca_imovel(cod_licenca, exercicio, inscricao_municipal)
   );

   CREATE TABLE imobiliario.licenca_imovel_nova_construcao (
       cod_licenca                 integer                 NOT NULL,
       exercicio                   char(4)                 NOT NULL,
       inscricao_municipal         integer                 NOT NULL,
       CONSTRAINT pk_licenca_imovel_nova_construcao        PRIMARY KEY (cod_licenca, exercicio, inscricao_municipal),
       CONSTRAINT fk_licenca_imovel_nova_construcao_1      FOREIGN KEY (cod_licenca, exercicio, inscricao_municipal) REFERENCES  imobiliario.licenca_imovel(cod_licenca, exercicio, inscricao_municipal)
   );

   CREATE TABLE imobiliario.licenca_imovel_nova_edificacao (
       cod_licenca                 integer                 NOT NULL,
       exercicio                   char(4)                 NOT NULL,
       inscricao_municipal         integer                 NOT NULL,
       cod_tipo                    integer                 NOT NULL,
       CONSTRAINT pk_licenca_imovel_nova_edificacao        PRIMARY KEY (cod_licenca, exercicio, inscricao_municipal),
       CONSTRAINT fk_licenca_imovel_nova_edificacao_1      FOREIGN KEY (cod_licenca, exercicio, inscricao_municipal) REFERENCES  imobiliario.licenca_imovel_nova_construcao(cod_licenca, exercicio, inscricao_municipal),
       CONSTRAINT fk_licenca_imovel_nova_edificacao_2      FOREIGN KEY (cod_tipo) REFERENCES  imobiliario.tipo_edificacao(cod_tipo)
   );

   CREATE TABLE imobiliario.licenca_imovel_unidade_autonoma (
       cod_licenca                 integer                 NOT NULL,
       exercicio                   char(4)                 NOT NULL,
       inscricao_municipal         integer                 NOT NULL,
       cod_construcao              integer                 NOT NULL,
       cod_tipo                    integer                 NOT NULL,
       CONSTRAINT pk_licenca_imovel_unidade_autonoma       PRIMARY KEY (cod_licenca, exercicio, inscricao_municipal, cod_construcao, cod_tipo),
       CONSTRAINT fk_licenca_imovel_unidade_autonoma_1     FOREIGN KEY (cod_licenca, exercicio, inscricao_municipal) REFERENCES  imobiliario.licenca_imovel(cod_licenca, exercicio, inscricao_municipal),
       CONSTRAINT fk_licenca_imovel_unidade_autonoma_2     FOREIGN KEY (inscricao_municipal, cod_tipo, cod_construcao) REFERENCES  imobiliario.unidade_autonoma(inscricao_municipal, cod_tipo, cod_construcao)
   );

   CREATE TABLE imobiliario.licenca_imovel_unidade_dependente (
       cod_licenca                 integer                 NOT NULL,
       exercicio                   char(4)                 NOT NULL,
       inscricao_municipal         integer                 NOT NULL,
       cod_construcao              integer                 NOT NULL,
       cod_tipo                    integer                 NOT NULL,
       cod_construcao_dependente   integer                 NOT NULL,
       CONSTRAINT pk_licenca_imovel_unidade_dependente     PRIMARY KEY (cod_licenca, exercicio, inscricao_municipal, cod_construcao, cod_tipo, cod_construcao_dependente),
       CONSTRAINT fk_licenca_imovel_unidade_dependente_1   FOREIGN KEY (cod_licenca, exercicio, inscricao_municipal) REFERENCES  imobiliario.licenca_imovel(cod_licenca, exercicio, inscricao_municipal),
       CONSTRAINT fk_licenca_imovel_unidade_dependente_2   FOREIGN KEY (inscricao_municipal, cod_construcao_dependente, cod_tipo, cod_construcao) REFERENCES  imobiliario.unidade_dependente(inscricao_municipal, cod_construcao_dependente, cod_tipo, cod_construcao)
   );

   CREATE TABLE imobiliario.licenca_lote (
       cod_licenca                 integer                 NOT NULL,
       exercicio                   char(4)                 NOT NULL,
       cod_lote                    integer                 NOT NULL,
       CONSTRAINT pk_licenca_lote                          PRIMARY KEY (cod_licenca, exercicio, cod_lote),
       CONSTRAINT fk_licenca_lote_1                        FOREIGN KEY (cod_licenca, exercicio) REFERENCES  imobiliario.licenca(cod_licenca, exercicio),
       CONSTRAINT fk_licenca_lote_2                        FOREIGN KEY (cod_lote) REFERENCES  imobiliario.lote(cod_lote)
   );

   CREATE TABLE imobiliario.licenca_lote_area (
       cod_licenca                 integer                 NOT NULL,
       exercicio                   char(4)                 NOT NULL,
       cod_lote                    integer                 NOT NULL,
       area                        numeric(14,2)           NOT NULL,
       CONSTRAINT pk_licenca_lote_area                     PRIMARY KEY (cod_licenca, exercicio, cod_lote),
       CONSTRAINT fk_licenca_lote_area_1                   FOREIGN KEY (cod_licenca, exercicio, cod_lote) REFERENCES  imobiliario.licenca_lote(cod_licenca, exercicio, cod_lote)
   );

   CREATE TABLE imobiliario.licenca_lote_loteamento (
       cod_licenca                 integer                 NOT NULL,
       exercicio                   char(4)                 NOT NULL,
       cod_lote                    integer                 NOT NULL,
       cod_loteamento              integer                 NOT NULL,
       CONSTRAINT pk_licenca_lote_loteamento               PRIMARY KEY (cod_licenca, exercicio, cod_lote, cod_loteamento),
       CONSTRAINT fk_licenca_lote_loteamento_1             FOREIGN KEY (cod_licenca, exercicio, cod_lote) REFERENCES  imobiliario.licenca_lote(cod_licenca, exercicio, cod_lote),
       CONSTRAINT fk_licenca_lote_loteamento_2             FOREIGN KEY (cod_loteamento) REFERENCES  imobiliario.loteamento(cod_loteamento)
   );

   CREATE TABLE imobiliario.licenca_lote_parcelamento_solo (
       cod_licenca                 integer                 NOT NULL,
       exercicio                   char(4)                 NOT NULL,
       cod_lote                    integer                 NOT NULL,
       cod_parcelamento            integer                 NOT NULL,
       CONSTRAINT pk_licenca_lote_parcelamento_solo        PRIMARY KEY (cod_licenca, exercicio, cod_lote, cod_parcelamento),
       CONSTRAINT fk_licenca_lote_parcelamento_solo_1      FOREIGN KEY (cod_licenca, exercicio, cod_lote) REFERENCES  imobiliario.licenca_lote(cod_licenca, exercicio, cod_lote),
       CONSTRAINT fk_licenca_lote_parcelamento_solo_2      FOREIGN KEY (cod_parcelamento) REFERENCES  imobiliario.parcelamento_solo(cod_parcelamento)
   );

   CREATE TABLE imobiliario.atributo_tipo_licenca (
       cod_tipo                    integer                 NOT NULL,
       cod_atributo                integer                 NOT NULL,
       cod_cadastro                integer                 NOT NULL,
       cod_modulo                  integer                 NOT NULL,
       ativo                       boolean                 NOT NULL,
       CONSTRAINT pk_atributo_tipo_licenca                 PRIMARY KEY (cod_tipo, cod_atributo, cod_cadastro, cod_modulo),
       CONSTRAINT fk_atributo_tipo_licenca_1               FOREIGN KEY (cod_tipo) REFERENCES  imobiliario.tipo_licenca(cod_tipo),
       CONSTRAINT fk_atributo_tipo_licenca_2               FOREIGN KEY (cod_modulo, cod_cadastro, cod_atributo) REFERENCES  administracao.atributo_dinamico(cod_modulo, cod_cadastro, cod_atributo)
   );

   CREATE TABLE imobiliario.atributo_tipo_licenca_imovel_valor (
       cod_licenca                 integer                 NOT NULL,
       exercicio                   char(4)                 NOT NULL,
       inscricao_municipal         integer                 NOT NULL,
       cod_tipo                    integer                 NOT NULL,
       cod_modulo                  integer                 NOT NULL,
       cod_cadastro                integer                 NOT NULL,
       cod_atributo                integer                 NOT NULL,
       timestamp                   timestamp               NOT NULL DEFAULT ('now'::text)::timestamp(3) with time zone,
       valor                       varchar(500)            NOT NULL,
       CONSTRAINT pk_atributo_tipo_licenca_imovel_valor    PRIMARY KEY (cod_licenca, exercicio, inscricao_municipal, cod_tipo, cod_modulo, cod_cadastro, cod_atributo, timestamp),
       CONSTRAINT fk_atributo_tipo_licenca_imovel_valor_1  FOREIGN KEY (cod_licenca, exercicio, inscricao_municipal) REFERENCES  imobiliario.licenca_imovel(cod_licenca, exercicio, inscricao_municipal),
       CONSTRAINT fk_atributo_tipo_licenca_imovel_valor_2  FOREIGN KEY (cod_tipo, cod_modulo, cod_cadastro, cod_atributo) REFERENCES  imobiliario.atributo_tipo_licenca(cod_tipo, cod_modulo, cod_cadastro, cod_atributo)
   );

   CREATE TABLE imobiliario.atributo_tipo_licenca_lote_valor (
       cod_licenca                 integer                 NOT NULL,
       exercicio                   char(4)                 NOT NULL,
       cod_lote                    integer                 NOT NULL,
       cod_tipo                    integer                 NOT NULL,
       cod_modulo                  integer                 NOT NULL,
       cod_cadastro                integer                 NOT NULL,
       cod_atributo                integer                 NOT NULL,
       timestamp                   timestamp               NOT NULL DEFAULT ('now'::text)::timestamp(3) with time zone,
       valor                       varchar(500)            NOT NULL,
       CONSTRAINT pk_atributo_tipo_licenca_lote_valor      PRIMARY KEY (cod_licenca, exercicio, cod_lote, cod_tipo, cod_modulo, cod_cadastro, cod_atributo, timestamp),
       CONSTRAINT fk_atributo_tipo_licenca_lote_valor_1    FOREIGN KEY (cod_licenca, exercicio, cod_lote) REFERENCES  imobiliario.licenca_lote(cod_licenca, exercicio, cod_lote),
       CONSTRAINT fk_atributo_tipo_licenca_lote_valor_2    FOREIGN KEY (cod_tipo, cod_modulo, cod_cadastro, cod_atributo) REFERENCES  imobiliario.atributo_tipo_licenca(cod_tipo, cod_modulo, cod_cadastro, cod_atributo)
   );


   -- INSERCAO DE DADOS PRE CADASTRADOS
   INSERT INTO imobiliario.tipo_licenca VALUES ( 1, 'Nova Edificação' );
   INSERT INTO imobiliario.tipo_licenca VALUES ( 2, 'Habite-se'       );
   INSERT INTO imobiliario.tipo_licenca VALUES ( 3, 'Reforma'         );
   INSERT INTO imobiliario.tipo_licenca VALUES ( 4, 'Reparos'         );
   INSERT INTO imobiliario.tipo_licenca VALUES ( 5, 'Reconstrução'    );
   INSERT INTO imobiliario.tipo_licenca VALUES ( 6, 'Demolição'       );
   INSERT INTO imobiliario.tipo_licenca VALUES ( 7, 'Loteamento'      );
   INSERT INTO imobiliario.tipo_licenca VALUES ( 8, 'Desmembramento'  );
   INSERT INTO imobiliario.tipo_licenca VALUES ( 9, 'Aglutinação'     );

   INSERT INTO imobiliario.tipo_baixa   VALUES ( 1, 'Baixa'           );
   INSERT INTO imobiliario.tipo_baixa   VALUES ( 2, 'Suspenção'       );
   INSERT INTO imobiliario.tipo_baixa   VALUES ( 3, 'Cassação'        );

   -- GRANTS
   GRANT ALL ON imobiliario.permissao                          TO GROUP urbem;
   GRANT ALL ON imobiliario.tipo_baixa                         TO GROUP urbem;
   GRANT ALL ON imobiliario.tipo_licenca                       TO GROUP urbem;
   GRANT ALL ON imobiliario.tipo_licenca_documento             TO GROUP urbem;
   GRANT ALL ON imobiliario.licenca                            TO GROUP urbem;
   GRANT ALL ON imobiliario.licenca_baixa                      TO GROUP urbem;
   GRANT ALL ON imobiliario.licenca_documento                  TO GROUP urbem;
   GRANT ALL ON imobiliario.emissao_documento                  TO GROUP urbem;
   GRANT ALL ON imobiliario.licenca_processo                   TO GROUP urbem;
   GRANT ALL ON imobiliario.licenca_responsavel_tecnico        TO GROUP urbem;
   GRANT ALL ON imobiliario.licenca_imovel                     TO GROUP urbem;
   GRANT ALL ON imobiliario.licenca_imovel_area                TO GROUP urbem;
   GRANT ALL ON imobiliario.licenca_imovel_nova_edificacao     TO GROUP urbem;
   GRANT ALL ON imobiliario.licenca_imovel_nova_construcao     TO GROUP urbem;
   GRANT ALL ON imobiliario.licenca_imovel_unidade_autonoma    TO GROUP urbem;
   GRANT ALL ON imobiliario.licenca_imovel_unidade_dependente  TO GROUP urbem;
   GRANT ALL ON imobiliario.licenca_lote                       TO GROUP urbem;
   GRANT ALL ON imobiliario.licenca_lote_area                  TO GROUP urbem;
   GRANT ALL ON imobiliario.licenca_lote_loteamento            TO GROUP urbem;
   GRANT ALL ON imobiliario.licenca_lote_parcelamento_solo     TO GROUP urbem;
   GRANT ALL ON imobiliario.atributo_tipo_licenca              TO GROUP urbem;
   GRANT ALL ON imobiliario.atributo_tipo_licenca_imovel_valor TO GROUP urbem;
   GRANT ALL ON imobiliario.atributo_tipo_licenca_lote_valor   TO GROUP urbem;


   -- NOVAS FUNCIONALIDADES E ACOES
   INSERT INTO administracao.funcionalidade VALUES ( 405, 12, 'Licenças', 'instancias/licencas/', 17 );
   UPDATE      administracao.funcionalidade SET ordem = 18 WHERE cod_funcionalidade = 234;
   UPDATE      administracao.funcionalidade SET ordem = 19 WHERE cod_funcionalidade = 211;

   INSERT INTO administracao.acao VALUES ( 2203, 405, 'FMDefinirPermissao.php'      , 'definir'          , 1, '', 'Definir Permissão para Concessão de Licenças' );
   INSERT INTO administracao.acao VALUES ( 2204, 405, 'FMDefinirCaracteristicas.php', 'definir_tp'       , 2, '', 'Definir Características para Tipo de Licença' );
   INSERT INTO administracao.acao VALUES ( 2205, 405, 'FMConcederLicenca.php'       , 'incluir'          , 3, '', 'Conceder Licença'                             );
   INSERT INTO administracao.acao VALUES ( 2206, 405, 'FLConcederLicenca.php'       , 'alterar'          , 4, '', 'Alterar Licença'                              );
   INSERT INTO administracao.acao VALUES ( 2207, 405, 'FLConcederLicenca.php'       , 'excluir'          , 5, '', 'Excluir Licença'                              );
   INSERT INTO administracao.acao VALUES ( 2208, 405, 'FLConcederLicenca.php'       , 'baixar'           , 6, '', 'Baixar Licença'                               );
   INSERT INTO administracao.acao VALUES ( 2209, 405, 'FLConcederLicenca.php'       , 'suspender'        , 7, '', 'Suspender Licença'                            );
   INSERT INTO administracao.acao VALUES ( 2210, 405, 'FLConcederLicenca.php'       , 'cancelar'         , 8, '', 'Cancelar Suspensão'                           );
   INSERT INTO administracao.acao VALUES ( 2211, 405, 'FLConcederLicenca.php'       , 'cassar'           , 9, '', 'Cassar Licença'                               );


   INSERT INTO administracao.cadastro VALUES (12,10,'Licenças','');

-----------
-- Ticket #12065
-----------

CREATE TABLE economico.utilizacao (
     cod_utilizacao              INTEGER                 NOT NULL,
     nom_utilizacao              VARCHAR(80)             NOT NULL,
     CONSTRAINT pk_utilizacao                            PRIMARY KEY (cod_utilizacao)
 );

 CREATE TABLE economico.uso_solo_empresa (
     cod_licenca                 INTEGER                 NOT NULL,
     exercicio                   CHAR(4)                 NOT NULL,
     inscricao_economica         INTEGER                 NOT NULL,
     CONSTRAINT pk_uso_solo_empresa                      PRIMARY KEY (cod_licenca, exercicio),
     CONSTRAINT fk_uso_solo_empresa_1                    FOREIGN KEY (cod_licenca, exercicio) REFERENCES  economico.licenca_diversa(cod_licenca, exercicio),
     CONSTRAINT fk_uso_solo_empresa_2                    FOREIGN KEY (inscricao_economica) REFERENCES  economico.cadastro_economico (inscricao_economica)
 );

 CREATE TABLE economico.uso_solo_imovel (
     cod_licenca                 INTEGER                 NOT NULL,
     exercicio                   CHAR(4)                 NOT NULL,
     inscricao_municipal         INTEGER                 NOT NULL,
     CONSTRAINT pk_uso_solo_imovel                       PRIMARY KEY (cod_licenca, exercicio),
     CONSTRAINT fk_uso_solo_imovel_1                     FOREIGN KEY (cod_licenca, exercicio) REFERENCES  economico.licenca_diversa(cod_licenca, exercicio),
     CONSTRAINT fk_uso_solo_imovel_2                     FOREIGN KEY (inscricao_municipal) REFERENCES  imobiliario.imovel(inscricao_municipal)
 );

 CREATE TABLE economico.uso_solo_logradouro (
     cod_licenca                 INTEGER                 NOT NULL,
     exercicio                   CHAR(4)                 NOT NULL,
     cod_logradouro              INTEGER                 NOT NULL,
     CONSTRAINT pk_uso_solo_logradouro                   PRIMARY KEY (cod_licenca, exercicio),
     CONSTRAINT fk_uso_solo_logradouro_1                 FOREIGN KEY (cod_licenca, exercicio) REFERENCES  economico.licenca_diversa(cod_licenca, exercicio),
     CONSTRAINT fk_uso_solo_logradouro_2                 FOREIGN KEY (cod_logradouro) REFERENCES  sw_logradouro(cod_logradouro)
 );

 CREATE TABLE economico.uso_solo_area (
     cod_licenca                 INTEGER                 NOT NULL,
     exercicio                   CHAR(4)                 NOT NULL,
     area                        NUMERIC(14,2)           NOT NULL,
     CONSTRAINT pk_uso_solo_area                         PRIMARY KEY (cod_licenca, exercicio),
     CONSTRAINT fk_uso_solo_area_1                       FOREIGN KEY (cod_licenca, exercicio) REFERENCES  economico.licenca_diversa(cod_licenca, exercicio)
 );

 INSERT INTO economico.utilizacao VALUES (1,'Diversas'              );
 INSERT INTO economico.utilizacao VALUES (2,'Uso e Ocupação do Solo');

 ALTER TABLE economico.tipo_licenca_diversa ADD COLUMN cod_utilizacao INTEGER;
 ALTER TABLE economico.tipo_licenca_diversa ADD CONSTRAINT fk_tipo_licenca_diversa_1 FOREIGN KEY (cod_utilizacao) REFERENCES economico.utilizacao (cod_utilizacao);
 UPDATE      economico.tipo_licenca_diversa SET cod_utilizacao = 1;
 ALTER TABLE economico.tipo_licenca_diversa ALTER COLUMN cod_utilizacao SET NOT NULL;

 -- GRANTS
 GRANT ALL ON economico.utilizacao                           TO GROUP urbem;
 GRANT ALL ON economico.uso_solo_empresa                     TO GROUP urbem;
 GRANT ALL ON economico.uso_solo_imovel                      TO GROUP urbem;
 GRANT ALL ON economico.uso_solo_logradouro                  TO GROUP urbem;
 GRANT ALL ON economico.uso_solo_area                        TO GROUP urbem;

----------
-- Ticket #12089
----------

CREATE TABLE arrecadacao.tipo_compensacao (
     cod_tipo                    INTEGER         NOT NULL,
     descricao                   VARCHAR(50)     NOT NULL,
     CONSTRAINT pk_tipo_compensacao              PRIMARY KEY                                 (cod_tipo)
 );

 CREATE TABLE arrecadacao.pagamento_diferenca_compensacao (
     cod_compensacao             INTEGER         NOT NULL,
     numeracao                   VARCHAR(17)     NOT NULL,
     ocorrencia_pagamento        INTEGER         NOT NULL,
     cod_convenio                INTEGER         NOT NULL,
     cod_calculo                 INTEGER         NOT NULL,
     CONSTRAINT pk_pagamento_diferenca_compensacao   PRIMARY KEY                                 (cod_compensacao, numeracao, ocorrencia_pagamento, cod_convenio),
     CONSTRAINT fk_pagamento_diferenca_compensacao_1 FOREIGN KEY                                 (cod_compensacao) REFERENCES arrecadacao.compensacao(cod_compensacao),
     CONSTRAINT fk_pagamento_diferenca_compensacao_2 FOREIGN KEY                                 (numeracao, ocorrencia_pagamento, cod_convenio, cod_calculo) REFERENCES arrecadacao.pagamento_diferenca  (numeracao, ocorrencia_pagamento, cod_convenio, cod_calculo)
 );

 GRANT ALL ON arrecadacao.pagamento_diferenca_compensacao TO GROUP urbem;
 GRANT ALL ON arrecadacao.tipo_compensacao                TO GROUP urbem;

 INSERT INTO arrecadacao.tipo_compensacao VALUES (1,'Pagamentos Duplicados' );
 INSERT INTO arrecadacao.tipo_compensacao VALUES (2,'Pagamentos a Maior'    );

 ALTER TABLE arrecadacao.compensacao ADD   COLUMN cod_tipo INTEGER;
 UPDATE      arrecadacao.compensacao SET          cod_tipo = 1;
 ALTER TABLE arrecadacao.compensacao ALTER COLUMN cod_tipo SET NOT NULL;
 ALTER TABLE arrecadacao.compensacao ADD CONSTRAINT fk_compensacao_2 FOREIGN KEY (cod_tipo) REFERENCES arrecadacao.tipo_compensacao (cod_tipo);

 ----------
 -- Ticket 12151
 ----------

   --
   -- Insere a função.
   --
   CREATE OR REPLACE function public.manutencao_funcao( intCodmodulo       INTEGER
                                                      , intCodBiblioteca   INTEGER
                                                      , varNomeFunc        VARCHAR
                                                      , intCodTiporetorno INTEGER)
   RETURNS integer as $$
   DECLARE
      intCodFuncao INTEGER := 0;
      varAux       VARCHAR;
   BEGIN

      SELECT cod_funcao
        INTO intCodFuncao
        FROM administracao.funcao
       WHERE cod_modulo                = intCodmodulo
         AND cod_biblioteca            = intCodBiblioteca
         AND Lower(Btrim(nom_funcao))  = Lower(Btrim(varNomeFunc))
      ;

      IF FOUND THEN
         DELETE FROM administracao.corpo_funcao_externa  WHERE cod_modulo = intCodmodulo AND cod_biblioteca = intCodBiblioteca AND cod_funcao = intCodFuncao;
         DELETE FROM administracao.funcao_externa        WHERE cod_modulo = intCodmodulo AND cod_biblioteca = intCodBiblioteca AND cod_funcao = intCodFuncao;
         DELETE FROM administracao.funcao_referencia     WHERE cod_modulo = intCodmodulo AND cod_biblioteca = intCodBiblioteca AND cod_funcao = intCodFuncao;
         DELETE FROM administracao.parametro             WHERE cod_modulo = intCodmodulo AND cod_biblioteca = intCodBiblioteca AND cod_funcao = intCodFuncao;
         DELETE FROM administracao.variavel              WHERE cod_modulo = intCodmodulo AND cod_biblioteca = intCodBiblioteca AND cod_funcao = intCodFuncao;
         DELETE FROM administracao.funcao                WHERE cod_modulo = intCodmodulo AND cod_biblioteca = intCodBiblioteca AND cod_funcao = intCodFuncao;
      END IF;

     -- Raise Notice ' Entrou 1 ';

     SELECT (max(cod_funcao)+1)
       INTO intCodFuncao
       FROM administracao.funcao
      WHERE cod_modulo       = intCodmodulo
        AND cod_biblioteca   = intCodBiblioteca
     ;

     --varAux := varNomeFunc || '  -   ' || To_Char( intCodFuncao, '999999') ;
     --RAise Notice '=> % ', varAux;

     IF intCodFuncao IS NULL OR intCodFuncao = 0 THEN
        intCodFuncao := 1;
     END IF;

     INSERT INTO administracao.funcao  ( cod_modulo
                                       , cod_biblioteca
                                       , cod_funcao
                                       , cod_tipo_retorno
                                       , nom_funcao)
                                VALUES ( intCodmodulo
                                       , intCodBiblioteca
                                       , intCodFuncao
                                       , intCodTiporetorno
                                       , varNomeFunc);

      RETURN intCodFuncao;

   END;
   $$ LANGUAGE 'plpgsql';

   --
   -- Inclusão de Váriaveis.
   --
   CREATE OR REPLACE function public.manutencao_variavel( intCodmodulo       INTEGER
                                                        , intCodBiblioteca   INTEGER
                                                        , intCodFuncao       INTEGER
                                                        , varNomVariavel     VARCHAR
                                                        , intTipoVariavel    INTEGER)
   RETURNS integer as $$
   DECLARE
      intCodVariavel INTEGER := 0;
   BEGIN

      If intCodFuncao != 0 THEN
         SELECT COALESCE((max(cod_variavel)+1),1)
           INTO intCodVariavel
           FROM administracao.variavel
          WHERE cod_modulo       = intCodmodulo
            AND cod_biblioteca   = intCodBiblioteca
            AND cod_funcao       = intCodFuncao
         ;

         INSERT INTO administracao.variavel ( cod_modulo
                                            , cod_biblioteca
                                            , cod_funcao
                                            , cod_variavel
                                            , nom_variavel
                                            , cod_tipo )
                                     VALUES ( intCodmodulo
                                            , intCodBiblioteca
                                            , intCodFuncao
                                            , intCodVariavel
                                            , varNomVariavel
                                            , intTipoVariavel
                                            );
      END IF;

         RETURN intCodVariavel;
      END;
      $$ LANGUAGE 'plpgsql';


      --
      -- Inclusão de parametro.
      --
      CREATE OR REPLACE function public.manutencao_parametro( intCodmodulo       INTEGER
                                                            , intCodBiblioteca   INTEGER
                                                            , intCodFuncao       INTEGER
                                                            , intCodVariavel     INTEGER)
      RETURNS VOID as $$
      DECLARE
         intOrdem INTEGER := 0;
      BEGIN
         If intCodFuncao != 0 THEN
            SELECT COALESCE((max(ordem)+1),1)
              INTO intOrdem
              FROM administracao.parametro
             WHERE cod_modulo       = intCodmodulo
               AND cod_biblioteca   = intCodBiblioteca
               AND cod_funcao       = intCodFuncao
            ;

            INSERT INTO administracao.parametro ( cod_modulo
                                                , cod_biblioteca
                                                , cod_funcao
                                                , cod_variavel
                                                , ordem)
                                         VALUES ( intCodmodulo
                                                , intCodBiblioteca
                                                , intCodFuncao
                                                , intCodVariavel
                                                , intOrdem );
         End If;

         RETURN;
      END;
      $$ LANGUAGE 'plpgsql';

      --
      -- Inclusão de parametro.
      --
      CREATE OR REPLACE function public.manutencao_funcao_externa( intCodmodulo       INTEGER
                                                                 , intCodBiblioteca   INTEGER
                                                                 , intCodFuncao       INTEGER )
      RETURNS VOID as $$
      DECLARE
         --intCodFuncao INTEGER;
      BEGIN

         -- RAise Notice ' =====> % ', intCodFuncao;

         If intCodFuncao != 0 THEN
            INSERT INTO administracao.funcao_externa ( cod_modulo
                                                     , cod_biblioteca
                                                     , cod_funcao
                                                     , comentario
                                                     )
                                              VALUES ( intCodmodulo
                                                     , intCodBiblioteca
                                                     , intCodFuncao
                                                     , ''
                                                     );
         END IF;
         RETURN;
      END;
      $$ LANGUAGE 'plpgsql';


      --
      -- Função principal de inclusão no Gerador de Calculo.
      --
      CREATE OR REPLACE function public.manutencao() RETURNS VOID as $$
      DECLARE
         intCodFuncao   INTEGER;
         intCodVariavel INTEGER;
      BEGIN

         -- 1 | INTEIRO
         -- 2 | TEXTO
         -- 3 | BOOLEANO
         -- 4 | NUMERICO
         -- 5 | DATA

         intCodFuncao   := public.manutencao_funcao   (  14, 1, 'economico.fn_busca_atributos_elementos', 2 );
         intCodVariavel := public.manutencao_variavel (  14, 1, intCodFuncao, 'inInscricaoEconomica'    , 1 ); PERFORM public.manutencao_parametro(  14, 1, intCodFuncao, intCodVariavel );
         intCodVariavel := public.manutencao_variavel (  14, 1, intCodFuncao, 'inCodAtividade'          , 1 ); PERFORM public.manutencao_parametro(  14, 1, intCodFuncao, intCodVariavel );
         intCodVariavel := public.manutencao_variavel (  14, 1, intCodFuncao, 'inCodElemento'           , 1 ); PERFORM public.manutencao_parametro(  14, 1, intCodFuncao, intCodVariavel );
         intCodVariavel := public.manutencao_variavel (  14, 1, intCodFuncao, 'inOcorrenciaElemento'    , 1 ); PERFORM public.manutencao_parametro(  14, 1, intCodFuncao, intCodVariavel );
         intCodVariavel := public.manutencao_variavel (  14, 1, intCodFuncao, 'inOcorrenciaAtividade'   , 1 ); PERFORM public.manutencao_parametro(  14, 1, intCodFuncao, intCodVariavel );

         intCodFuncao   := public.manutencao_funcao   (  14, 1, 'buscaAliquotaAtividade'                , 4 );
         intCodVariavel := public.manutencao_variavel (  14, 1, intCodFuncao, 'intCodAtividade'         , 1 ); PERFORM public.manutencao_parametro(  14, 1, intCodFuncao, intCodVariavel );

         intCodFuncao   := public.manutencao_funcao   (  14, 1, 'atributoElementoAtividade'             , 2 );
         intCodVariavel := public.manutencao_variavel (  14, 1, intCodFuncao, 'inInscricaoEconomica'    , 1 ); PERFORM public.manutencao_parametro(  14, 1, intCodFuncao, intCodVariavel );
         intCodVariavel := public.manutencao_variavel (  14, 1, intCodFuncao, 'inCodAtributo'           , 1 ); PERFORM public.manutencao_parametro(  14, 1, intCodFuncao, intCodVariavel );
         intCodVariavel := public.manutencao_variavel (  14, 1, intCodFuncao, 'inCodAtividade'          , 1 ); PERFORM public.manutencao_parametro(  14, 1, intCodFuncao, intCodVariavel );
         intCodVariavel := public.manutencao_variavel (  14, 1, intCodFuncao, 'inCodElemento'           , 1 ); PERFORM public.manutencao_parametro(  14, 1, intCodFuncao, intCodVariavel );

         intCodFuncao   := public.manutencao_funcao   (  14, 1, 'buscaOcorrenciaAtividade'              , 1 );
         intCodVariavel := public.manutencao_variavel (  14, 1, intCodFuncao, 'inInscricaoEconomica'    , 1 ); PERFORM public.manutencao_parametro(  14, 1, intCodFuncao, intCodVariavel );
         intCodVariavel := public.manutencao_variavel (  14, 1, intCodFuncao, 'inCodAtividade'          , 1 ); PERFORM public.manutencao_parametro(  14, 1, intCodFuncao, intCodVariavel );

         intCodFuncao   := public.manutencao_funcao   (  14, 1, 'buscaCodigoElemento'                   , 1 );
         intCodVariavel := public.manutencao_variavel (  14, 1, intCodFuncao, 'inCodAtividade'          , 1 ); PERFORM public.manutencao_parametro(  14, 1, intCodFuncao, intCodVariavel );

         intCodFuncao   := public.manutencao_funcao   (  14, 1, 'buscaOcorrenciaElemento'               , 1 );
         intCodVariavel := public.manutencao_variavel (  14, 1, intCodFuncao, 'inInscricaoEconomica'    , 1 ); PERFORM public.manutencao_parametro(  14, 1, intCodFuncao, intCodVariavel );
         intCodVariavel := public.manutencao_variavel (  14, 1, intCodFuncao, 'inCodAtividade'          , 1 ); PERFORM public.manutencao_parametro(  14, 1, intCodFuncao, intCodVariavel );
         intCodVariavel := public.manutencao_variavel (  14, 1, intCodFuncao, 'inOcorrenciaAtividade'   , 1 ); PERFORM public.manutencao_parametro(  14, 1, intCodFuncao, intCodVariavel );
         intCodVariavel := public.manutencao_variavel (  14, 1, intCodFuncao, 'inCodElemento'           , 1 ); PERFORM public.manutencao_parametro(  14, 1, intCodFuncao, intCodVariavel );

         intCodFuncao   := public.manutencao_funcao   (  14, 1, 'buscaValorAtributoElemento'            , 2 );
         intCodVariavel := public.manutencao_variavel (  14, 1, intCodFuncao, 'inInscricaoEconomica'    , 1 ); PERFORM public.manutencao_parametro(  14, 1, intCodFuncao, intCodVariavel );
         intCodVariavel := public.manutencao_variavel (  14, 1, intCodFuncao, 'inCodAtividade'          , 1 ); PERFORM public.manutencao_parametro(  14, 1, intCodFuncao, intCodVariavel );
         intCodVariavel := public.manutencao_variavel (  14, 1, intCodFuncao, 'inCodElemento'           , 1 ); PERFORM public.manutencao_parametro(  14, 1, intCodFuncao, intCodVariavel );
         intCodVariavel := public.manutencao_variavel (  14, 1, intCodFuncao, 'inOcorrenciaElemento'    , 1 ); PERFORM public.manutencao_parametro(  14, 1, intCodFuncao, intCodVariavel );
         intCodVariavel := public.manutencao_variavel (  14, 1, intCodFuncao, 'inOcorrenciaAtividade'   , 1 ); PERFORM public.manutencao_parametro(  14, 1, intCodFuncao, intCodVariavel );
         intCodVariavel := public.manutencao_variavel (  14, 1, intCodFuncao, 'inCodModulo'             , 1 ); PERFORM public.manutencao_parametro(  14, 1, intCodFuncao, intCodVariavel );
         intCodVariavel := public.manutencao_variavel (  14, 1, intCodFuncao, 'stParametro'             , 1 ); PERFORM public.manutencao_parametro(  14, 1, intCodFuncao, intCodVariavel );



         RETURN;
      END;
      $$ LANGUAGE 'plpgsql';

      --
      -- Execuçao  função.
      --
      Select public.manutencao();
      Drop Function public.manutencao();
      Drop Function public.manutencao_funcao(integer, integer, varchar, integer );
      Drop Function public.manutencao_variavel( integer, integer, integer, varchar, integer );
      Drop Function public.manutencao_parametro( integer, integer, integer, integer );
      Drop Function public.manutencao_funcao_externa( integer, integer, integer );

CREATE OR REPLACE function public.manutencao() RETURNS VOID as $$

DECLARE

   intCodFuncao        INTEGER := 0;

   intCodModulo        INTEGER := 0;

   intCodBiblioteca    INTEGER := 0;

BEGIN

   SELECT cod_funcao, cod_modulo, cod_biblioteca  INTO intCodFuncao, intCodModulo, intCodBiblioteca
     FROM administracao.funcao
    WHERE nom_funcao = 'fn_juros_simples_um_porcento';

   INSERT INTO administracao.funcao_externa
               (cod_modulo
             , cod_biblioteca
             , cod_funcao
             , comentario
             , corpo_pl
             , corpo_ln)
        VALUES (intCodmodulo
             , intCodBiblioteca
             , intCodFuncao
             , ''
             , ''
             , '');

   SELECT cod_funcao, cod_modulo, cod_biblioteca  INTO intCodFuncao, intCodModulo, intCodBiblioteca
     FROM administracao.funcao
    WHERE nom_funcao = 'fn_multa_020_por_cento_ao_dia';

   INSERT INTO administracao.funcao_externa
               (cod_modulo
             , cod_biblioteca
             , cod_funcao
             , comentario
             , corpo_pl
             , corpo_ln)
        VALUES (intCodmodulo
             , intCodBiblioteca
             , intCodFuncao
             , ''
             , ''
             , '');

RETURN;

END;

$$ LANGUAGE 'plpgsql';

SELECT manutencao();
DROP FUNCTION manutencao();



CREATE OR REPLACE function public.manutencao() RETURNS VOID as $$

DECLARE

   intCodFuncao     INTEGER := 0;

   intCodVariavel   INTEGER := 0;

BEGIN

   SELECT max(cod_funcao) + 1 INTO intCodFuncao
     FROM administracao.funcao
    WHERE cod_modulo     = 14
      AND cod_biblioteca = 1;

   IF intCodFuncao IS NULL THEN

      intCodFuncao := 1;

   END IF;

   INSERT INTO administracao.funcao
               (cod_modulo
             , cod_biblioteca
             , cod_funcao
             , cod_tipo_retorno
             , nom_funcao)
        VALUES (14
             , 1
             , intCodFuncao
             , 1
             , 'buscaBairroEmpresa');

   SELECT max(cod_variavel) + 1 INTO intCodVariavel
     FROM administracao.variavel
    WHERE cod_modulo = 14
      AND cod_biblioteca = 1
      AND cod_funcao = intCodFuncao;

   IF intCodVariavel IS NULL THEN

      intCodVariavel := 1;

   END IF;

   INSERT INTO administracao.variavel
               (cod_modulo
             , cod_biblioteca
             , cod_funcao
             , cod_variavel
             , nom_variavel
             , cod_tipo
             , valor_inicial)
        VALUES (14
             , 1
             , intCodFuncao
             , intCodVariavel
             , 'inInscricaoEconomica'
             , 1
             , '');

   INSERT INTO administracao.parametro
               (cod_modulo
             , cod_biblioteca
             , cod_funcao
             , cod_variavel
             , ordem)
        VALUES (14
             , 1
             , intCodFuncao
             , intCodVariavel
             , 1);

RETURN;

END;

$$ LANGUAGE 'plpgsql';

SELECT manutencao();
DROP FUNCTION manutencao();


CREATE OR REPLACE function public.manutencao() RETURNS VOID as $$

DECLARE

   intCodFuncao     INTEGER := 0;

   intCodVariavel   INTEGER := 0;

BEGIN

   SELECT max(cod_funcao) + 1 INTO intCodFuncao
     FROM administracao.funcao
    WHERE cod_modulo     = 12
      AND cod_biblioteca = 1;

   IF intCodFuncao IS NULL THEN

      intCodFuncao := 1;

   END IF;

   INSERT INTO administracao.funcao
               (cod_modulo
             , cod_biblioteca
             , cod_funcao
             , cod_tipo_retorno
             , nom_funcao)
        VALUES (12
             , 1
             , intCodFuncao
             , 2
             , 'recuperaTrechoValorMetroQuadradoPredial');

   SELECT max(cod_variavel) + 1 INTO intCodVariavel
     FROM administracao.variavel
    WHERE cod_modulo = 12
      AND cod_biblioteca = 1
      AND cod_funcao = intCodFuncao;

   IF intCodVariavel IS NULL THEN

      intCodVariavel := 1;

   END IF;

   INSERT INTO administracao.variavel
               (cod_modulo
             , cod_biblioteca
             , cod_funcao
             , cod_variavel
             , nom_variavel
             , cod_tipo
             , valor_inicial)
        VALUES (12
             , 1
             , intCodFuncao
             , intCodVariavel
             , 'intInscricaoMunicipal'
             , 1
             , '');

   INSERT INTO administracao.parametro
               (cod_modulo
             , cod_biblioteca
             , cod_funcao
             , cod_variavel
             , ordem)
        VALUES (12
             , 1
             , intCodFuncao
             , intCodVariavel
             , 1);

RETURN;

END;

$$ LANGUAGE 'plpgsql';

SELECT manutencao();
DROP FUNCTION manutencao();


CREATE OR REPLACE function public.manutencao() RETURNS VOID as $$

DECLARE

   intCodFuncao     INTEGER := 0;

   intCodVariavel   INTEGER := 0;

BEGIN

   SELECT max(cod_funcao) + 1 INTO intCodFuncao
     FROM administracao.funcao
    WHERE cod_modulo     = 12
      AND cod_biblioteca = 1;

   IF intCodFuncao IS NULL THEN

      intCodFuncao := 1;

   END IF;

   INSERT INTO administracao.funcao
               (cod_modulo
             , cod_biblioteca
             , cod_funcao
             , cod_tipo_retorno
             , nom_funcao)
        VALUES (12
             , 1
             , intCodFuncao
             , 2
             , 'recuperaTrechoValorMetroQuadradoTerritorial');

   SELECT max(cod_variavel) + 1 INTO intCodVariavel
     FROM administracao.variavel
    WHERE cod_modulo = 12
      AND cod_biblioteca = 1
      AND cod_funcao = intCodFuncao;

   IF intCodVariavel IS NULL THEN

      intCodVariavel := 1;

   END IF;

   INSERT INTO administracao.variavel
               (cod_modulo
             , cod_biblioteca
             , cod_funcao
             , cod_variavel
             , nom_variavel
             , cod_tipo
             , valor_inicial)
        VALUES (12
             , 1
             , intCodFuncao
             , intCodVariavel
             , 'intInscricaoMunicipal'
             , 1
             , '');

   INSERT INTO administracao.parametro
               (cod_modulo
             , cod_biblioteca
             , cod_funcao
             , cod_variavel
             , ordem)
        VALUES (12
             , 1
             , intCodFuncao
             , intCodVariavel
             , 1);

RETURN;

END;

$$ LANGUAGE 'plpgsql';

SELECT manutencao();
DROP FUNCTION manutencao();
