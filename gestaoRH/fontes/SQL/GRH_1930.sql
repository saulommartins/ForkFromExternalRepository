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
* $Id: GRH_1930.sql 59612 2014-09-02 12:00:51Z gelson $
*
* Versão 1.93.0
*/

----------------
-- Ticket #13631
----------------

select atualizarBanco('
CREATE TABLE pessoal.dias_turno (
    cod_dia     INTEGER         NOT NULL,
    nom_dia     CHAR(15)        NOT NULL,
    CONSTRAINT pk_dias_turno    PRIMARY KEY (cod_dia)
);
');

select atualizarBanco('
GRANT ALL ON pessoal.dias_turno TO GROUP urbem;
');

select atualizarBanco(' INSERT INTO pessoal.dias_turno (cod_dia, nom_dia) VALUES (1,\'Domingo\'      ); '); 
select atualizarBanco(' INSERT INTO pessoal.dias_turno (cod_dia, nom_dia) VALUES (2,\'Segunda-feira\'); ');
select atualizarBanco(' INSERT INTO pessoal.dias_turno (cod_dia, nom_dia) VALUES (3,\'Terça-feira\'  ); ');
select atualizarBanco(' INSERT INTO pessoal.dias_turno (cod_dia, nom_dia) VALUES (4,\'Quarta-feira\' ); ');
select atualizarBanco(' INSERT INTO pessoal.dias_turno (cod_dia, nom_dia) VALUES (5,\'Quinta-feira\' ); ');
select atualizarBanco(' INSERT INTO pessoal.dias_turno (cod_dia, nom_dia) VALUES (6,\'Sexta-feira\'  ); ');
select atualizarBanco(' INSERT INTO pessoal.dias_turno (cod_dia, nom_dia) VALUES (7,\'Sábado\'       ); ');
select atualizarBanco(' INSERT INTO pessoal.dias_turno (cod_dia, nom_dia) VALUES (8,\'Feriado\'      ); ');

INSERT INTO administracao.tabelas_rh VALUES (1,'dias_turno',1);

select atualizarBanco(' ALTER TABLE pessoal.faixa_turno ADD COLUMN cod_dia        INTEGER; ');
select atualizarBanco(' ALTER TABLE pessoal.faixa_turno ADD COLUMN hora_entrada_2 TIME;    ');
select atualizarBanco(' ALTER TABLE pessoal.faixa_turno ADD COLUMN hora_saida_2   TIME;    ');

select atualizarBanco(' UPDATE      pessoal.faixa_turno SET cod_dia = 1; ');

select atualizarBanco(' ALTER TABLE pessoal.faixa_turno DROP CONSTRAINT pk_faixa_turno;                                                                 ');
select atualizarBanco(' ALTER TABLE pessoal.faixa_turno ADD  CONSTRAINT pk_faixa_turno   PRIMARY KEY (cod_turno, cod_grade, timestamp, cod_dia);        ');
select atualizarBanco(' ALTER TABLE pessoal.faixa_turno ADD  CONSTRAINT fk_faixa_turno_2 FOREIGN KEY (cod_dia) REFERENCES pessoal.dias_turno (cod_dia); ');

CREATE OR REPLACE FUNCTION manutencao( ) RETURNS VOID AS $$
DECLARE

    stSQL                   VARCHAR;
    reRECORD                RECORD;
    stSQL1                  VARCHAR;
    reRECORD1               RECORD;
    inCodEntidadePrefeitura INTEGER;
    stSQLEntidade           VARCHAR;
    stSQLUpdate             VARCHAR;
    reRegistro              RECORD;

BEGIN

    stSQL := 'SELECT * FROM pessoal.faixa_turno;';

    FOR reRECORD IN EXECUTE stSQL LOOP

        stSQL1 := 'SELECT * FROM pessoal.dias_turno WHERE cod_dia > 1';

        FOR reRECORD1 IN EXECUTE stSQL1 LOOP

            INSERT 
              INTO pessoal.faixa_turno 
            VALUES ( reRECORD.cod_turno
                 , reRECORD.cod_grade
                 , reRECORD.timestamp
                 , reRECORD.hora_entrada
                 , reRECORD.hora_saida
                 , reRECORD1.cod_dia
                 );

        END LOOP;

    END LOOP;


    inCodEntidadePrefeitura := selectIntoInteger('SELECT valor FROM administracao.configuracao WHERE parametro = \'cod_entidade_prefeitura\' AND exercicio = \'2008\'');
    stSQLEntidade := '   SELECT cod_entidade
                           FROM administracao.entidade_rh
                          WHERE exercicio     = 2008
                            AND cod_entidade != '||inCodEntidadePrefeitura||'
                       GROUP BY cod_entidade
                     ';

    FOR reRegistro IN EXECUTE stSQLEntidade LOOP

            stSQL := 'SELECT * FROM pessoal_'||reRegistro.cod_entidade||'.faixa_turno;';
        
            FOR reRECORD IN EXECUTE stSQL LOOP
        
                stSQL1 := 'SELECT * FROM pessoal_'||reRegistro.cod_entidade||'.dias_turno WHERE cod_dia > 1';
        
                FOR reRECORD1 IN EXECUTE stSQL1 LOOP

                    stSQLUpdate := 'INSERT INTO pessoal_'||reRegistro.cod_entidade||'.faixa_turno
                                              ( cod_turno, cod_grade, timestamp, hora_entrada, hora_saida, cod_dia )
                                   VALUES ( '|| reRECORD.cod_turno    || '
                                        , '  || reRECORD.cod_grade    || '
                                        , \''  || reRECORD.timestamp    || '\'
                                        , \''  || reRECORD.hora_entrada || '\'
                                        , \''  || reRECORD.hora_saida   || '\'
                                        , '  || reRECORD1.cod_dia     || '
                                        );
                              ';
                    EXECUTE stSQLUpdate;

                END LOOP;
        
            END LOOP;

    END LOOP;


    RETURN;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();

select atualizarBanco(' ALTER TABLE pessoal.faixa_turno ALTER COLUMN cod_dia SET NOT NULL; ');


----------------
-- Ticket #12647
----------------

INSERT INTO administracao.modulo
         ( cod_modulo
         , cod_responsavel
         , nom_modulo
         , nom_diretorio
         , ordem
         , cod_gestao
         )
    VALUES ( 51
         , 0
         , 'Relógio Ponto'
         , 'ponto/'
         , 60
         , 4
         );

INSERT INTO administracao.funcionalidade
         ( cod_funcionalidade
         , cod_modulo
         , nom_funcionalidade
         , nom_diretorio
         , ordem )
    VALUES ( 429
         , 51
         , 'Justificativas'
         , 'instancias/justificativas/'
         , 2
         );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2341
          , 429
          , 'FMManterJustificativa.php'
          , 'incluir'
          , 1
          , ''
          , 'Incluir Justificativa'
          );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2342
          , 429
          , 'FLManterJustificativa.php'
          , 'alterar'
          , 2
          , ''
          , 'Alterar Justificativa'
          );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2343
          , 429
          , 'FLManterJustificativa.php'
          , 'excluir'
          , 3
          , ''
          , 'Excluir Justificativa'
          );


select atualizarBanco(' CREATE SCHEMA ponto; ');

select atualizarBanco(' CREATE SEQUENCE ponto.seq_justificativa; ');

select atualizarBanco('
CREATE TABLE ponto.justificativa(
    cod_justificativa       INTEGER         NOT NULL DEFAULT nextval(\'ponto.seq_justificativa\'),
    descricao               VARCHAR(80)     NOT NULL,
    anular_faltas           BOOLEAN         NOT NULL,
    lancar_dias_trabalho    BOOLEAN         NOT NULL,
    CONSTRAINT pk_justificativa             PRIMARY KEY                     (cod_justificativa)
 );
');

select atualizarBanco('
CREATE TABLE ponto.justificativa_horas(
    cod_justificativa       INTEGER         NOT NULL,
    horas_falta             VARCHAR(6)      NOT NULL,
    horas_abono             VARCHAR(6)      NOT NULL,    
    CONSTRAINT pk_justificativa_horas       PRIMARY KEY                     (cod_justificativa),
    CONSTRAINT fk_justificativa_horas_1     FOREIGN KEY                     (cod_justificativa)
                                            REFERENCES ponto.justificativa  (cod_justificativa)
 );
');

select atualizarBanco(' GRANT ALL ON ponto.seq_justificativa        TO GROUP urbem; ');
select atualizarBanco(' GRANT ALL ON ponto.justificativa            TO GROUP urbem; ');
select atualizarBanco(' GRANT ALL ON ponto.justificativa_horas      TO GROUP urbem; ');


-------------------------------------------------
-- solicitado por DIEGO LEMOS DE SOUZA - 20081001
-------------------------------------------------

DROP FUNCTION geraregistroferias(integer,integer);
DROP FUNCTION geraregistroferias(integer,integer,varchar);


----------------
-- Ticket #13592
----------------

insert into administracao.funcao
          ( cod_modulo
          , cod_biblioteca
          , cod_funcao
          , cod_tipo_retorno
          , nom_funcao )
     values ( 27
          , 1
          , ( select max(cod_funcao)+1 from administracao.funcao where cod_modulo=27 and cod_biblioteca=1 )
          , 4
          , 'mediaFeriasValorQuantidadeParcelasEmprestimos'
          );

insert into folhapagamento.tipo_media
          ( cod_tipo
          , codigo
          , descricao
          , observacao
          , cod_funcao
          , cod_biblioteca
          , cod_modulo,desdobramento ) 
     values ( 18
          , 18
          , 'Eventos em Parcelas/Empréstimos nas Férias'
          , 'Considera eventos em parcelas / Empréstimos nas Férias'
          , ( select max(cod_funcao) from administracao.funcao where cod_modulo = 27 and cod_biblioteca = 1 )
          , 1
          , 27
          , 'F'
          );


----------------
-- Ticket #12646
----------------

select atualizarBanco('
   CREATE OR REPLACE FUNCTION ponto.fn_atualiza_ultimo_timestamp_escala( )
      RETURNS TRIGGER AS $$
   DECLARE
      rEscalaAtual      RECORD;
      iCodEscala        INTEGER;
      tNewTimestamp     TIMESTAMP;
      cAux              VARCHAR;

   BEGIN

      If TG_OP=\'INSERT\' then
         --
         -- Define a escala a ser inserida
         --
         iCodEscala      := new.cod_escala;
         tNewTimestamp   := new.timestamp;

         --
         -- Verifica a existencia da ultima escala
         --
         Select escala.*
           Into rEscalaAtual
           From ponto.escala
          Where escala.cod_escala = iCodEscala
         ;

         If Found Then
            Raise notice \'Tem Ultima escala %\', TG_OP;
            Raise notice \' => % \', iCodEscala;
            Raise notice \' => % \', rEscalaAtual.ultimo_timestamp;

            tNewTimestamp := (\'now\'::text)::timestamp(3) with time zone ;
            If Coalesce(rEscalaAtual.ultimo_timestamp, \'1800-01-01\') <= tNewTimestamp  Then
               --raise notice \' ==> %\', tNewTimestamp;
               Update ponto.escala
                  Set ultimo_timestamp =  tNewTimestamp
                Where cod_escala       = iCodEscala
               ;
            Else
               cAux := To_char(iCodEscala,\'9999\');
               raise exception \'Tabela ponto.escala inconsistente, contate suporte. Escala:%\', cAux;
            End If;
         Else
            raise exception \'Falha de integridade referencial, tabela ponto.escala.: %\', TG_OP;
            raise exception \'Código escala: %\', iCodEscala;
         End If;
      Else
         raise exception \'Operação não permitida para tabela ponto.escala.: %\', TG_OP;
      End If;

      Return new;

   END;
   $$ LANGUAGE plpgsql;
');


select atualizarBanco('CREATE SEQUENCE ponto.seq_escala;');

select atualizarBanco('
CREATE TABLE ponto.escala (
    cod_escala          INTEGER                 NOT NULL DEFAULT nextval(\'ponto.seq_escala\'),
    descricao           VARCHAR(80)             NOT NULL,
    ultimo_timestamp    TIMESTAMP               NOT NULL,
    CONSTRAINT pk_escala                        PRIMARY KEY                         (cod_escala)
);
');

select atualizarBanco('
CREATE TABLE ponto.escala_turno (
    cod_escala          INTEGER                 NOT NULL,
    cod_turno           INTEGER                 NOT NULL,
    timestamp           TIMESTAMP               NOT NULL DEFAULT (\'now\'::text)::timestamp(3) with time zone,
    dt_turno            DATE                    NOT NULL,
    hora_entrada_1      TIME                    NOT NULL,
    hora_saida_1        TIME                    NOT NULL,
    hora_entrada_2      TIME                    NOT NULL,
    hora_saida_2        TIME                    NOT NULL,
    tipo                CHAR(1)                 NOT NULL,
    CONSTRAINT pk_escala_turno                  PRIMARY KEY                         (cod_escala, cod_turno, timestamp),
    CONSTRAINT fk_escala_turno_1                FOREIGN KEY                         (cod_escala)
                                                REFERENCES ponto.escala             (cod_escala),
    CONSTRAINT chk_escala_turno                 CHECK (tipo in (\'F\',\'T\'))
);
');

select atualizarBanco('CREATE TRIGGER tr_atualiza_ultimo_timestamp_escala BEFORE INSERT OR UPDATE ON ponto.escala_turno FOR EACH ROW EXECUTE PROCEDURE ponto.fn_atualiza_ultimo_timestamp_escala();');

select atualizarBanco('
CREATE TABLE ponto.escala_contrato (
    cod_contrato        INTEGER                 NOT NULL,
    cod_escala          INTEGER                 NOT NULL,
    timestamp           TIMESTAMP               NOT NULL DEFAULT (\'now\'::text)::timestamp(3) with time zone,
    CONSTRAINT pk_escala_contrato               PRIMARY KEY                         (cod_contrato, cod_escala, timestamp),
    CONSTRAINT fk_escala_contrato_1             FOREIGN KEY                         (cod_escala)
                                                REFERENCES ponto.escala             (cod_escala),
    CONSTRAINT fk_escala_contrato_2             FOREIGN KEY                         (cod_contrato)
                                                REFERENCES pessoal.contrato         (cod_contrato)
);
');

select atualizarBanco('
CREATE TABLE ponto.escala_exclusao (
    cod_escala          INTEGER                 NOT NULL,
    CONSTRAINT pk_escala_exclusao               PRIMARY KEY                         (cod_escala),
    CONSTRAINT fk_escala_exclusao_1             FOREIGN KEY                         (cod_escala)
                                                REFERENCES ponto.escala             (cod_escala)
);
');

select atualizarBanco('
CREATE TABLE ponto.escala_contrato_exclusao (
    cod_contrato        INTEGER                 NOT NULL,
    cod_escala          INTEGER                 NOT NULL,
    timestamp           TIMESTAMP               NOT NULL,
    numcgm              INTEGER                 NOT NULL,
    timestamp_exclusao  TIMESTAMP               NOT NULL DEFAULT (\'now\'::text)::timestamp(3) with time zone,
    CONSTRAINT pk_escala_contrato_esclusao      PRIMARY KEY                         (cod_contrato, cod_escala, timestamp),
    CONSTRAINT fk_escala_contrato_esclusao_1    FOREIGN KEY                         (cod_contrato, cod_escala, timestamp)
                                                REFERENCES ponto.escala_contrato    (cod_contrato, cod_escala, timestamp),
    CONSTRAINT fk_escala_contrato_esclusao_2    FOREIGN KEY                         (numcgm)
                                                REFERENCES administracao.usuario    (numcgm)
);
');


select atualizarBanco('GRANT ALL ON ponto.escala                   TO GROUP urbem;'); 
select atualizarBanco('GRANT ALL ON ponto.escala_turno             TO GROUP urbem;');
select atualizarBanco('GRANT ALL ON ponto.escala_contrato          TO GROUP urbem;');
select atualizarBanco('GRANT ALL ON ponto.escala_contrato_exclusao TO GROUP urbem;');
select atualizarBanco('GRANT ALL ON ponto.escala_exclusao          TO GROUP urbem;');

INSERT INTO administracao.funcionalidade
         ( cod_funcionalidade
         , cod_modulo
         , nom_funcionalidade
         , nom_diretorio
         , ordem )
    VALUES ( 439
         , 51
         , 'Escalas de Horário'
         , 'instancias/escalaHorario/'
         , 3
         );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2363
          , 439
          , 'FMManterEscala.php'
          , 'incluir'
          , 1
          , ''
          , 'Incluir Escalas'
          );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2364
          , 439
          , 'FLManterEscala.php'
          , 'alterar'
          , 2
          , ''
          , 'Alterar Escalas'
          );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2365
          , 439
          , 'FLManterEscala.php'
          , 'excluir'
          , 3
          , ''
          , 'Excluir Escalas'
          );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2366
          , 439
          , 'FLManterVinculo.php'
          , 'incluir'
          , 4
          , ''
          , 'Vincular Escala'
          );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2367
          , 439
          , 'FLManterVinculo.php'
          , 'consultar'
          , 5
          , ''
          , 'Consultar Escalas Vinculadas'
          );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2368
          , 439
          , 'FLManterVinculo.php'
          , 'excluir'
          , 6
          , ''
          , 'Excluir Vínculo Escala/Servidor'
          );


----------------
-- Ticket #13676
----------------

select atualizarBanco('CREATE SEQUENCE ponto.seq_formato_importacao;');

select atualizarBanco('
CREATE TABLE ponto.formato_importacao (
    cod_formato         INTEGER         NOT NULL DEFAULT nextval(\'ponto.seq_formato_importacao\'),
    descricao           VARCHAR(50)     NOT NULL,
    referencia_cadastro CHAR(1)         NOT NULL,
    formato_colunas     CHAR(1)         NOT NULL,
    CONSTRAINT pk_formato_importacao    PRIMARY KEY (cod_formato),
    CONSTRAINT chk_formato_importacao_1 CHECK (referencia_cadastro in (\'M\',\'C\')),
    CONSTRAINT chk_formato_importacao_2 CHECK (formato_colunas in (\'T\',\'D\'))
);
');

select atualizarBanco('
CREATE TABLE ponto.formato_campos (
    cod_campo           INTEGER     NOT NULL ,
    nom_campo           VARCHAR(30) NOT NULL,
    CONSTRAINT pk_formato_campos    PRIMARY KEY (cod_campo)
);
');

select atualizarBanco('INSERT INTO ponto.formato_campos (cod_campo,nom_campo) VALUES ( 1,\'Matrícula / Cartão Ponto\');'); 
select atualizarBanco('INSERT INTO ponto.formato_campos (cod_campo,nom_campo) VALUES ( 2,\'Dia (dd)\'                );');
select atualizarBanco('INSERT INTO ponto.formato_campos (cod_campo,nom_campo) VALUES ( 3,\'Mês (mm)\'                );');
select atualizarBanco('INSERT INTO ponto.formato_campos (cod_campo,nom_campo) VALUES ( 4,\'Ano (aaaa)\'              );');
select atualizarBanco('INSERT INTO ponto.formato_campos (cod_campo,nom_campo) VALUES ( 5,\'Hora 1 (hh)\'             );');
select atualizarBanco('INSERT INTO ponto.formato_campos (cod_campo,nom_campo) VALUES ( 6,\'Minuto 1 (mm)\'           );');
select atualizarBanco('INSERT INTO ponto.formato_campos (cod_campo,nom_campo) VALUES ( 7,\'Hora 2 (hh)\'             );');
select atualizarBanco('INSERT INTO ponto.formato_campos (cod_campo,nom_campo) VALUES ( 8,\'Minuto 2 (mm)\'           );');
select atualizarBanco('INSERT INTO ponto.formato_campos (cod_campo,nom_campo) VALUES ( 9,\'Hora 3 (hh)\'             );');
select atualizarBanco('INSERT INTO ponto.formato_campos (cod_campo,nom_campo) VALUES (10,\'Minuto 3 (mm)\'           );');
select atualizarBanco('INSERT INTO ponto.formato_campos (cod_campo,nom_campo) VALUES (11,\'Hora 4 (hh)\'             );');
select atualizarBanco('INSERT INTO ponto.formato_campos (cod_campo,nom_campo) VALUES (12,\'Minuto 4 (mm)\'           );');


INSERT INTO administracao.tabelas_rh VALUES (9,'formato_campos',1);

select atualizarBanco('
CREATE TABLE ponto.formato_delimitador (
    cod_formato         INTEGER     NOT NULL,
    formato_delimitador CHAR(1)     NOT NULL,
    CONSTRAINT pk_formato_delimitador   PRIMARY KEY                         (cod_formato),
    CONSTRAINT fk_formato_delimitador_1 FOREIGN KEY                         (cod_formato)
                                        REFERENCES ponto.formato_importacao (cod_formato)
);
');

select atualizarBanco('
CREATE TABLE ponto.delimitador_colunas (
    cod_formato         INTEGER     NOT NULL,
    cod_campo           INTEGER     NOT NULL,
    coluna              INTEGER     NOT NULL,
    CONSTRAINT pk_delimitador_colunas   PRIMARY KEY                          (cod_formato, cod_campo),
    CONSTRAINT fk_delimitador_colunas_1 FOREIGN KEY                          (cod_formato)
                                        REFERENCES ponto.formato_delimitador (cod_formato),
    CONSTRAINT fk_delimitador_colunas_2 FOREIGN KEY                          (cod_campo)
                                        REFERENCES ponto.formato_campos      (cod_campo)

);
');

select atualizarBanco('
CREATE TABLE ponto.formato_tamanho_fixo (
    cod_formato         INTEGER     NOT NULL,
    cod_campo           INTEGER     NOT NULL,
    posicao_inicial     INTEGER     NOT NULL,
    posicao_final       INTEGER     NOT NULL,
    CONSTRAINT pk_tamanho_fixo      PRIMARY KEY                         (cod_formato, cod_campo),
    CONSTRAINT fk_tamanho_fixo_1    FOREIGN KEY                         (cod_formato)
                                    REFERENCES ponto.formato_importacao (cod_formato),
    CONSTRAINT fk_tamanho_fixo_2    FOREIGN KEY                         (cod_campo)
                                    REFERENCES ponto.formato_campos     (cod_campo)
);
');


select atualizarBanco('GRANT ALL ON TABLE ponto.formato_importacao   TO GROUP urbem;'); 
select atualizarBanco('GRANT ALL ON TABLE ponto.formato_campos       TO GROUP urbem;');
select atualizarBanco('GRANT ALL ON TABLE ponto.formato_delimitador  TO GROUP urbem;');
select atualizarBanco('GRANT ALL ON TABLE ponto.delimitador_colunas  TO GROUP urbem;');
select atualizarBanco('GRANT ALL ON TABLE ponto.formato_tamanho_fixo TO GROUP urbem;');



INSERT INTO administracao.funcionalidade
         ( cod_funcionalidade
         , cod_modulo
         , nom_funcionalidade
         , nom_diretorio
         , ordem )
    VALUES ( 440
         , 51
         , 'Configuração'
         , 'instancias/configuracao/'
         , 1
         );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2369
          , 440
          , 'FMManterConfiguracaoFormato.php'
          , 'configurar'
          , 1
          , ''
          , 'Formatos de Importação'
          );


----------------
-- Ticket #13567
----------------
select atualizarBanco('CREATE SEQUENCE ponto.seq_importacao_ponto;');

select atualizarBanco('
CREATE TABLE ponto.importacao_ponto(
    cod_ponto           INTEGER                 NOT NULL DEFAULT nextval(\'ponto.seq_importacao_ponto\'),
    cod_contrato        INTEGER                 NOT NULL,
    cod_importacao      INTEGER                 NOT NULL,
    cod_formato         INTEGER                 NOT NULL,
    dt_ponto            DATE                    NOT NULL,
    CONSTRAINT pk_importacao_ponto              PRIMARY KEY                         (cod_ponto, cod_contrato, cod_importacao),
    CONSTRAINT fk_importacao_ponto_1            FOREIGN KEY                         (cod_contrato)
                                                REFERENCES pessoal.contrato         (cod_contrato),
    CONSTRAINT fk_importacao_ponto_2            FOREIGN KEY                         (cod_formato)
                                                REFERENCES ponto.formato_importacao (cod_formato)
);
');

select atualizarBanco('
CREATE TABLE ponto.importacao_ponto_horario(
    cod_contrato        INTEGER                 NOT NULL,
    cod_ponto           INTEGER                 NOT NULL,
    cod_importacao      INTEGER                 NOT NULL,
    cod_hora            INTEGER                 NOT NULL,
    horario             TIME                    NOT NULL,
    CONSTRAINT pk_importacao_ponto_horario      PRIMARY KEY                         (cod_contrato, cod_ponto, cod_importacao, cod_hora),
    CONSTRAINT fk_importacao_ponto_horario_1    FOREIGN KEY                         (cod_contrato, cod_ponto, cod_importacao)
                                                REFERENCES ponto.importacao_ponto   (cod_contrato, cod_ponto, cod_importacao)
);
');

select atualizarBanco('CREATE SEQUENCE ponto.seq_importacao_ponto_erro;');

select atualizarBanco('
CREATE TABLE ponto.importacao_ponto_erro(
    cod_ponto_erro      INTEGER                 NOT NULL DEFAULT nextval(\'ponto.seq_importacao_ponto_erro\'),
    cod_formato         INTEGER                 NOT NULL,
    cod_importacao_erro INTEGER                 NOT NULL,
    linha               VARCHAR(150)            NOT NULL,
    CONSTRAINT pk_importacao_ponto_erro         PRIMARY KEY                         (cod_ponto_erro),
    CONSTRAINT fk_importacao_ponto_erro_        FOREIGN KEY                         (cod_formato)
                                                REFERENCES ponto.formato_importacao (cod_formato)
);
');

select atualizarBanco('GRANT ALL ON TABLE ponto.importacao_ponto         TO GROUP urbem;');
select atualizarBanco('GRANT ALL ON TABLE ponto.importacao_ponto_horario TO GROUP urbem;');
select atualizarBanco('GRANT ALL ON TABLE ponto.importacao_ponto_erro    TO GROUP urbem;');

INSERT INTO administracao.funcionalidade
         ( cod_funcionalidade
         , cod_modulo
         , nom_funcionalidade
         , nom_diretorio
         , ordem )
    VALUES ( 441
         , 51
         , 'Importação'
         , 'instancias/importacao/'
         , 4
         );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2370
          , 441
          , 'FLManterImportacaoPonto.php'
          , 'importar'
          , 1
          , ''
          , 'Importar Pontos'
          );

INSERT INTO administracao.relatorio
         ( cod_gestao
         , cod_modulo
         , cod_relatorio
         , nom_relatorio
         , arquivo )
    VALUES ( 4
         , 51
         , 1
         , 'Importação do Ponto'
         , 'importacaoPonto.rptdesign'
         );

INSERT INTO administracao.relatorio
         ( cod_gestao
         , cod_modulo
         , cod_relatorio
         , nom_relatorio
         , arquivo )
    VALUES ( 4
         , 51
         , 2
         , 'Erros Importação do Ponto'
         , 'errosImportacaoPonto.rptdesign'
         );

----------------
-- Ticket #13573
----------------
select atualizarBanco('CREATE SEQUENCE ponto.seq_compensacao_horas;');

select atualizarBanco('
CREATE TABLE ponto.compensacao_horas(
    cod_compensacao     INTEGER                 NOT NULL DEFAULT nextval(\'ponto.seq_compensacao_horas\'),
    cod_contrato        INTEGER                 NOT NULL,
    dt_falta            DATE                    NOT NULL,
    dt_compensacao      DATE                    NOT NULL,
    CONSTRAINT pk_compensacao_horas             PRIMARY KEY                         (cod_compensacao, cod_contrato),
    CONSTRAINT fk_compensacao_horas_1           FOREIGN KEY                         (cod_contrato)
                                                REFERENCES pessoal.contrato         (cod_contrato)
);
');

select atualizarBanco('
CREATE TABLE ponto.compensacao_horas_exclusao(
    cod_compensacao     INTEGER                 NOT NULL,
    cod_contrato        INTEGER                 NOT NULL,
    numcgm              INTEGER                 NOT NULL,
    timestamp           TIMESTAMP               NOT NULL DEFAULT (\'now\'::text)::timestamp(3) with time zone,
    CONSTRAINT pk_compensacao_horas_exclusao    PRIMARY KEY                         (cod_compensacao, cod_contrato),
    CONSTRAINT fk_compensacao_horas_exclusao_1  FOREIGN KEY                         (cod_compensacao, cod_contrato)
                                                REFERENCES ponto.compensacao_horas  (cod_compensacao, cod_contrato),
    CONSTRAINT fk_compensacao_horas_exclusao_2  FOREIGN KEY                         (numcgm)
                                                REFERENCES administracao.usuario    (numcgm)
);
');

select atualizarBanco('GRANT ALL ON TABLE ponto.compensacao_horas           TO GROUP urbem;');
select atualizarBanco('GRANT ALL ON TABLE ponto.compensacao_horas_exclusao  TO GROUP urbem;');


INSERT INTO administracao.funcionalidade
         ( cod_funcionalidade
         , cod_modulo
         , nom_funcionalidade
         , nom_diretorio
         , ordem )
    VALUES ( 442
         , 51
         , 'Compensações'
         , 'instancias/compensacoes/'
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
     VALUES ( 2371
          , 442
          , 'FMManterCompensacaoHoras.php'
          , 'incluir'
          , 1
          , ''
          , 'Incluir Compensação de Horas'
          );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2372
          , 442
          , 'FLManterCompensacaoHoras.php'
          , 'alterar'
          , 2
          , ''
          , 'Alterar Compensação de Horas'
          );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2373
          , 442
          , 'FLManterCompensacaoHoras.php'
          , 'consultar'
          , 3
          , ''
          , 'Consultar Compensação de Horas'
          );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2374
          , 442
          , 'FLManterCompensacaoHoras.php'
          , 'excluir'
          , 4
          , ''
          , 'Excluir Compensação de Horas'
          );


-------------------------------------------------
-- Solicitado por DIEGO LEMOS DE SOUZA - 20081013
-------------------------------------------------

INSERT INTO administracao.relatorio
         ( cod_gestao
         , cod_modulo
         , cod_relatorio
         , nom_relatorio
         , arquivo )
    VALUES ( 4
         , 51
         , 3
         , 'Programação de Escalas'
         , 'programacaoDeEscalas.rptdesign'
         );

