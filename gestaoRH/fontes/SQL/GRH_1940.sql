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
* $Id: GRH_1930.sql 34383 2008-10-10 19:32:10Z souzadl $
*
* Versão 1.94.0
*/

----------------
-- Ticket #12645
----------------
select atualizarBanco('
CREATE TABLE ponto.configuracao_relogio_ponto (
    cod_configuracao    INTEGER                 NOT NULL,
    ultimo_timestamp    TIMESTAMP               NOT NULL DEFAULT (\'now\'::text)::timestamp(3) with time zone,
    CONSTRAINT pk_configuracao_relogio_ponto    PRIMARY KEY                                     (cod_configuracao)
);
');

select atualizarBanco('
GRANT ALL ON TABLE ponto.configuracao_relogio_ponto   TO GROUP urbem;
');

select atualizarBanco('
CREATE TABLE ponto.configuracao_relogio_ponto_exclusao(
    cod_configuracao    INTEGER                 NOT NULL,
    timestamp_exclusao  TIMESTAMP               NOT NULL DEFAULT (\'now\'::text)::timestamp(3) with time zone,
    numcgm              INTEGER                 NOT NULL,
    CONSTRAINT pk_configuracao_relogio_ponto_exclusao   PRIMARY KEY                                 (cod_configuracao),
    CONSTRAINT fk_configuracao_relogio_ponto_exclusao_1 FOREIGN KEY                                 (cod_configuracao)
                                                        REFERENCES ponto.configuracao_relogio_ponto (cod_configuracao),
    CONSTRAINT fk_configuracao_relogio_ponto_exclusao_2 FOREIGN KEY                                 (numcgm)
                                                        REFERENCES administracao.usuario            (numcgm)
);
');

select atualizarBanco('
GRANT ALL ON ponto.configuracao_relogio_ponto_exclusao TO GROUP urbem;
');

select atualizarBanco('
CREATE TABLE ponto.configuracao_parametros_gerais (
    cod_configuracao    INTEGER                     NOT NULL,
    timestamp           TIMESTAMP                   NOT NULL DEFAULT (\'now\'::text)::timestamp(3) with time zone,
    cod_dia_dsr         INTEGER                     NOT NULL,
    descricao           VARCHAR(100)                NOT NULL,
    limitar_atrasos     BOOLEAN                     NOT NULL DEFAULT \'FALSE\',
    hora_noturno1       TIME                        NOT NULL,
    hora_noturno2       TIME                        NOT NULL,
    separar_adicional   BOOLEAN                     NOT NULL DEFAULT \'FALSE\',
    lancar_abono        BOOLEAN                     NOT NULL DEFAULT \'FALSE\',
    lancar_desconto     BOOLEAN                     NOT NULL DEFAULT \'FALSE\',
    trabalho_feriado    BOOLEAN                     NOT NULL DEFAULT \'FALSE\',
    somar_extras        BOOLEAN                     NOT NULL DEFAULT \'FALSE\',
    vigencia            DATE                        NOT NULL,
    CONSTRAINT pk_configuracao_parametros_gerais    PRIMARY KEY                                     (cod_configuracao, timestamp),
    CONSTRAINT fk_configuracao_parametros_gerais_1  FOREIGN KEY                                     (cod_configuracao)
                                                    REFERENCES ponto.configuracao_relogio_ponto     (cod_configuracao),
    CONSTRAINT fk_configuracao_parametros_gerais_2  FOREIGN KEY                                     (cod_dia_dsr)
                                                    REFERENCES administracao.dias_semana            (cod_dia)
);
');

select atualizarBanco('
GRANT ALL ON TABLE ponto.configuracao_parametros_gerais   TO GROUP urbem;
');

select atualizarBanco('
CREATE TABLE ponto.configuracao_lotacao(
    cod_configuracao    INTEGER                     NOT NULL,
    timestamp           TIMESTAMP                   NOT NULL,
    cod_orgao           INTEGER                     NOT NULL,
    CONSTRAINT pk_configuracao_lotacao              PRIMARY KEY                                     (cod_configuracao, timestamp, cod_orgao),
    CONSTRAINT fk_configuracao_lotacao_1            FOREIGN KEY                                     (cod_configuracao, timestamp)
                                                    REFERENCES ponto.configuracao_parametros_gerais (cod_configuracao, timestamp),
    CONSTRAINT fk_configuracao_lotacao_2            FOREIGN KEY                                     (cod_orgao)
                                                    REFERENCES organograma.orgao                    (cod_orgao)
 );
');

select atualizarBanco('GRANT ALL ON ponto.configuracao_lotacao TO GROUP urbem;');

select atualizarBanco('                                                
CREATE OR REPLACE FUNCTION ponto.fn_atualiza_ultimo_timestamp_conf_ponto( )
   RETURNS TRIGGER AS $$                         
DECLARE                                          
   rPontoAtual       RECORD;                     
   iCodConfiguracao  INTEGER;                    
   tNewTimestamp     TIMESTAMP;                  
   cAux              VARCHAR;                    
                                                 
BEGIN                                            
                                                 
   If TG_OP=\'INSERT\' then                        
      --
      -- Define a configuracao a ser inserida
      --
      iCodConfiguracao  := new.cod_configuracao;
      tNewTimestamp     := new.timestamp;

      --
      -- Verifica a existencia da ultima configuracao
      --
      Select configuracao_relogio_ponto.*
        Into rPontoAtual
        From ponto.configuracao_relogio_ponto
       Where configuracao_relogio_ponto.cod_configuracao = iCodConfiguracao;

      If Found Then
         tNewTimestamp := (\'now\'::text)::timestamp(3) with time zone ;
         If Coalesce(rPontoAtual.ultimo_timestamp, \'1800-01-01\') <= tNewTimestamp  Then
            Update ponto.configuracao_relogio_ponto
               Set ultimo_timestamp = tNewTimestamp
             Where cod_configuracao = iCodConfiguracao;
         Else
            cAux := To_char(iCodConfiguracao,\'9999\');
            raise exception \'Tabela ponto.configuracao_relogio_ponto inconsistente, contate suporte. Configuração:%\', cAux;
         End If;
      Else
         raise exception \'Falha de integridade referencial, tabela ponto.configuracao_relogio_ponto.: %\', TG_OP;
         raise exception \'Código configuração: %\', iCodConfiguracao;
      End If;
   Else
      raise exception \'Operação não permitida para tabela ponto.configuracao_relogio_ponto.: %\', TG_OP;
   End If;

   Return new;

END;
$$ LANGUAGE plpgsql;
');

select atualizarBanco('
CREATE TRIGGER tr_configuracao_parametros_gerais BEFORE INSERT OR UPDATE ON ponto.configuracao_parametros_gerais FOR EACH ROW EXECUTE PROCEDURE ponto.fn_atualiza_ultimo_timestamp_conf_ponto();
');

select atualizarBanco('
CREATE TABLE ponto.arredondar_tempo (
    cod_configuracao    INTEGER         NOT NULL,
    timestamp           TIMESTAMP       NOT NULL,
    hora_entrada1       TIME            NOT NULL,
    hora_saida1         TIME            NOT NULL,
    hora_entrada2       TIME            NOT NULL,
    hora_saida2         TIME            NOT NULL,
    CONSTRAINT pk_arredondar_tempo      PRIMARY KEY                                     (cod_configuracao, timestamp),
    CONSTRAINT fk_arredondar_tempo_1    FOREIGN KEY                                     (cod_configuracao, timestamp)
                                        REFERENCES ponto.configuracao_parametros_gerais (cod_configuracao, timestamp)
);
');

select atualizarBanco('
GRANT ALL ON TABLE ponto.arredondar_tempo   TO GROUP urbem;
');

select atualizarBanco('
CREATE TABLE ponto.atrasos (
    cod_configuracao    INTEGER         NOT NULL,
    timestamp           TIMESTAMP       NOT NULL,
    minutos             INTEGER         NOT NULL ,
    CONSTRAINT pk_atrasos               PRIMARY KEY                                     (cod_configuracao, timestamp),
    CONSTRAINT fk_atrasos_1             FOREIGN KEY                                     (cod_configuracao, timestamp)
                                        REFERENCES ponto.configuracao_parametros_gerais (cod_configuracao, timestamp)
);
');

select atualizarBanco('
GRANT ALL ON TABLE ponto.atrasos   TO GROUP urbem;
');

select atualizarBanco('
CREATE TABLE ponto.calendario (
    cod_configuracao    INTEGER         NOT NULL,
    timestamp           TIMESTAMP       NOT NULL,
    cod_calendar        INTEGER         NOT NULL,
    CONSTRAINT pk_calendario            PRIMARY KEY                                     (cod_configuracao, timestamp),
    CONSTRAINT fk_calendario_1          FOREIGN KEY                                     (cod_configuracao, timestamp)
                                        REFERENCES ponto.configuracao_parametros_gerais (cod_configuracao, timestamp),
    CONSTRAINT fk_calendario_2          FOREIGN KEY                                     (cod_calendar)
                                        REFERENCES calendario.calendario                (cod_calendar)
);
');

select atualizarBanco('
GRANT ALL ON TABLE ponto.calendario   TO GROUP urbem;
');

select atualizarBanco('
CREATE TABLE ponto.faltas (
    cod_configuracao    INTEGER                 NOT NULL,
    timestamp           TIMESTAMP               NOT NULL,
    minutos             INTEGER                 NOT NULL ,
    CONSTRAINT pk_faltas                        PRIMARY KEY                                     (cod_configuracao, timestamp),
    CONSTRAINT fk_faltas_1                      FOREIGN KEY                                     (cod_configuracao, timestamp)
                                                REFERENCES ponto.configuracao_parametros_gerais (cod_configuracao, timestamp)
);
');

select atualizarBanco('
GRANT ALL ON TABLE ponto.faltas   TO GROUP urbem;
');

select atualizarBanco('
CREATE TABLE ponto.falta_dsr (
    cod_configuracao    INTEGER                 NOT NULL,
    timestamp           TIMESTAMP               NOT NULL,
    horas               TIME                    NOT NULL,
    CONSTRAINT pk_falta_dsr                     PRIMARY KEY                                     (cod_configuracao, timestamp),
    CONSTRAINT fk_falta_dsr_1                   FOREIGN KEY                                     (cod_configuracao, timestamp)
                                                REFERENCES ponto.configuracao_parametros_gerais (cod_configuracao, timestamp)
);
');

select atualizarBanco('
GRANT ALL ON TABLE ponto.falta_dsr   TO GROUP urbem;
');

select atualizarBanco('
CREATE TABLE ponto.fator_multiplicacao (
    cod_configuracao    INTEGER                 NOT NULL,
    timestamp           TIMESTAMP               NOT NULL,
    fator               NUMERIC(14,2)           NOT NULL,
    CONSTRAINT pk_fator_multiplicacao           PRIMARY KEY                                     (cod_configuracao, timestamp),
    CONSTRAINT fk_fator_multiplicacao_1         FOREIGN KEY                                     (cod_configuracao, timestamp)
                                                REFERENCES ponto.configuracao_parametros_gerais (cod_configuracao, timestamp)
);
');

select atualizarBanco('
GRANT ALL ON TABLE ponto.fator_multiplicacao   TO GROUP urbem;
');

select atualizarBanco('
CREATE TABLE ponto.horas_anterior (
    cod_configuracao    INTEGER                 NOT NULL,
    timestamp           TIMESTAMP               NOT NULL,
    horas               TIME                    NOT NULL,
    CONSTRAINT pk_horas_anterior                PRIMARY KEY                                     (cod_configuracao, timestamp),
    CONSTRAINT fk_horas_anterior_1              FOREIGN KEY                                     (cod_configuracao, timestamp)
                                                REFERENCES ponto.configuracao_parametros_gerais (cod_configuracao, timestamp)
);
');

select atualizarBanco('
GRANT ALL ON TABLE ponto.horas_anterior   TO GROUP urbem;
');

select atualizarBanco('
CREATE TABLE ponto.horas_desconto_dsr (
    cod_configuracao    INTEGER                 NOT NULL,
    timestamp           TIMESTAMP               NOT NULL,
    horas               TIME                    NOT NULL,
    CONSTRAINT pk_horas_desconto_dsr            PRIMARY KEY                                     (cod_configuracao, timestamp),
    CONSTRAINT fk_horas_desconto_dsr_1          FOREIGN KEY                                     (cod_configuracao, timestamp)
                                                REFERENCES ponto.configuracao_parametros_gerais (cod_configuracao, timestamp)
);
');

select atualizarBanco('
GRANT ALL ON TABLE ponto.horas_desconto_dsr   TO GROUP urbem;
');

select atualizarBanco('
CREATE TABLE ponto.horas_extras (
    cod_configuracao    INTEGER                 NOT NULL,
    timestamp           TIMESTAMP               NOT NULL,
    minutos             INTEGER                 NOT NULL,
    periodo             CHAR(1)                 NOT NULL,
    CONSTRAINT pk_horas_extras                  PRIMARY KEY                                     (cod_configuracao, timestamp),
    CONSTRAINT fk_horas_extras_1                FOREIGN KEY                                     (cod_configuracao, timestamp)
                                                REFERENCES ponto.configuracao_parametros_gerais (cod_configuracao, timestamp),
    CONSTRAINT chk_horas_extras_1               CHECK(periodo in (\'D\',\'S\'))
);
');

select atualizarBanco('
GRANT ALL ON TABLE ponto.horas_extras   TO GROUP urbem;
');

select atualizarBanco('
CREATE TABLE ponto.horas_posterior (
    cod_configuracao    INTEGER                 NOT NULL,
    timestamp           TIMESTAMP               NOT NULL,
    horas               TIME                    NOT NULL,
    CONSTRAINT pk_horas_posterior               PRIMARY KEY                                     (cod_configuracao, timestamp),
    CONSTRAINT fk_horas_posterior_1             FOREIGN KEY                                     (cod_configuracao, timestamp)
                                                REFERENCES ponto.configuracao_parametros_gerais (cod_configuracao, timestamp)
);
');

select atualizarBanco('
GRANT ALL ON TABLE ponto.horas_posterior   TO GROUP urbem;
');

select atualizarBanco('
CREATE TABLE ponto.remarcacoes_consecutivas (
    cod_configuracao    INTEGER                 NOT NULL,
    timestamp           TIMESTAMP               NOT NULL,
    minutos             INTEGER                 NOT NULL,
    CONSTRAINT pk_remarcacoes_consecutivas      PRIMARY KEY                                     (cod_configuracao, timestamp),
    CONSTRAINT fk_remarcacoes_consecutivas_1    FOREIGN KEY                                     (cod_configuracao, timestamp)
                                                REFERENCES ponto.configuracao_parametros_gerais (cod_configuracao, timestamp)
);
');

select atualizarBanco('
GRANT ALL ON TABLE ponto.remarcacoes_consecutivas   TO GROUP urbem;
');

select atualizarBanco('
CREATE TABLE ponto.dias_uteis (
    cod_configuracao    INTEGER                 NOT NULL,
    timestamp           TIMESTAMP               NOT NULL,
    cod_dia             INTEGER                 NOT NULL,
    CONSTRAINT pk_dias_uteis                    PRIMARY KEY                                     (cod_configuracao, timestamp, cod_dia),
    CONSTRAINT fk_dias_uteis_1                  FOREIGN KEY                                     (cod_configuracao, timestamp)
                                                REFERENCES ponto.configuracao_parametros_gerais (cod_configuracao, timestamp),
    CONSTRAINT fk_dias_uteis_2                  FOREIGN KEY                                     (cod_dia)
                                                REFERENCES pessoal.dias_turno                   (cod_dia)
);
');

select atualizarBanco('
GRANT ALL ON TABLE ponto.dias_uteis   TO GROUP urbem;
');

select atualizarBanco('
CREATE TABLE ponto.configuracao_banco_horas (
    cod_configuracao    INTEGER                 NOT NULL,
    timestamp           TIMESTAMP               NOT NULL DEFAULT (\'now\'::text)::timestamp(3) with time zone,
    ativar_banco        BOOLEAN                 NOT NULL DEFAULT \'FALSE\',
    contagem_limites    CHAR(1)                 NOT NULL,
    horas_excesso       CHAR(1)                 NOT NULL,
    CONSTRAINT pk_configuracao_banco_horas      PRIMARY KEY                                     (cod_configuracao, timestamp),
    CONSTRAINT fk_configuracao_banco_horas_1    FOREIGN KEY                                     (cod_configuracao)
                                                REFERENCES ponto.configuracao_relogio_ponto     (cod_configuracao),
    CONSTRAINT chk_configuracao_banco_horas_1   CHECK(horas_excesso in (\'B\',\'H\')),
    CONSTRAINT chk_configuracao_banco_horas_2   CHECK(contagem_limites in (\'D\',\'M\',\'S\'))
);
');

select atualizarBanco('
GRANT ALL ON TABLE ponto.configuracao_banco_horas   TO GROUP urbem;
');

select atualizarBanco('
CREATE TRIGGER tr_configuracao_banco_horas      BEFORE INSERT OR UPDATE ON ponto.configuracao_banco_horas FOR EACH ROW EXECUTE PROCEDURE ponto.fn_atualiza_ultimo_timestamp_conf_ponto();
');

select atualizarBanco('
CREATE TABLE ponto.banco_horas_maximo_debito (
    cod_configuracao    INTEGER                 NOT NULL,
    timestamp           TIMESTAMP               NOT NULL,
    horas               TIME                    NOT NULL ,
    CONSTRAINT pk_banco_horas_maximo_debito     PRIMARY KEY                                     (cod_configuracao, timestamp),
    CONSTRAINT fk_banco_horas_maximo_debito_1   FOREIGN KEY                                     (cod_configuracao, timestamp)
                                                REFERENCES ponto.configuracao_banco_horas       (cod_configuracao, timestamp)
);
');

select atualizarBanco('
GRANT ALL ON TABLE ponto.banco_horas_maximo_debito   TO GROUP urbem;
');

select atualizarBanco('
CREATE TABLE ponto.banco_horas_maximo_extras (
    cod_configuracao    INTEGER                 NOT NULL,
    timestamp           TIMESTAMP               NOT NULL,
    horas               TIME                    NOT NULL ,
    CONSTRAINT pk_banco_horas_maximo_extras     PRIMARY KEY                                     (cod_configuracao, timestamp),
    CONSTRAINT fk_banco_horas_maximo_extras_1   FOREIGN KEY                                     (cod_configuracao, timestamp)
                                                REFERENCES ponto.configuracao_banco_horas       (cod_configuracao, timestamp)
);
');

select atualizarBanco('
GRANT ALL ON TABLE ponto.banco_horas_maximo_extras   TO GROUP urbem;
');

select atualizarBanco('
CREATE TABLE ponto.banco_horas_dias (
    cod_configuracao    INTEGER                 NOT NULL,
    timestamp           TIMESTAMP               NOT NULL,
    cod_dia             INTEGER                 NOT NULL,
    CONSTRAINT pk_banco_horas_dias              PRIMARY KEY                                     (cod_configuracao, timestamp, cod_dia),
    CONSTRAINT fk_banco_horas_dias_1            FOREIGN KEY                                     (cod_configuracao, timestamp)
                                                REFERENCES ponto.configuracao_banco_horas       (cod_configuracao, timestamp),
    CONSTRAINT fk_banco_horas_dias_2            FOREIGN KEY                                     (cod_dia)
                                                REFERENCES pessoal.dias_turno                   (cod_dia)
);
');

select atualizarBanco('
GRANT ALL ON TABLE ponto.banco_horas_dias   TO GROUP urbem;
');

select atualizarBanco('
CREATE TABLE ponto.configuracao_horas_extras_2 (
    cod_configuracao    INTEGER                 NOT NULL,
    timestamp           TIMESTAMP               NOT NULL DEFAULT (\'now\'::text)::timestamp(3) with time zone,
    anterior_periodo_1  BOOLEAN                 NOT NULL DEFAULT \'FALSE\',
    entre_periodo_1_2   BOOLEAN                 NOT NULL DEFAULT \'FALSE\',
    posterior_periodo_2 BOOLEAN                 NOT NULL DEFAULT \'FALSE\',
    autorizacao         BOOLEAN                 NOT NULL DEFAULT \'FALSE\',
    atrasos             BOOLEAN                 NOT NULL DEFAULT \'FALSE\',
    faltas              BOOLEAN                 NOT NULL DEFAULT \'FALSE\',
    CONSTRAINT pk_configuracao_horas_extras_2   PRIMARY KEY                                     (cod_configuracao, timestamp),
    CONSTRAINT fk_configuracao_horas_extras_2_1 FOREIGN KEY                                     (cod_configuracao)
                                                REFERENCES ponto.configuracao_relogio_ponto     (cod_configuracao)
);
');

select atualizarBanco('
GRANT ALL ON TABLE ponto.configuracao_horas_extras_2   TO GROUP urbem;
');

select atualizarBanco('
CREATE TRIGGER tr_configuracao_horas_extras_2 BEFORE INSERT OR UPDATE ON ponto.configuracao_horas_extras_2 FOR EACH ROW EXECUTE PROCEDURE ponto.fn_atualiza_ultimo_timestamp_conf_ponto();
');

select atualizarBanco('
CREATE TABLE ponto.faixas_horas_extra (
    cod_configuracao    INTEGER                 NOT NULL,
    timestamp           TIMESTAMP               NOT NULL,
    cod_faixa           INTEGER                 NOT NULL,
    percentual          NUMERIC(14,2)           NOT NULL,
    horas               TIME                    NOT NULL,
    calculo_horas_extra CHAR(1)                 NOT NULL,
    CONSTRAINT pk_faixas_horas_extra            PRIMARY KEY                                     (cod_configuracao, timestamp, cod_faixa),
    CONSTRAINT fk_faixas_horas_extra_1          FOREIGN KEY                                     (cod_configuracao, timestamp)
                                                REFERENCES ponto.configuracao_horas_extras_2    (cod_configuracao, timestamp),
    CONSTRAINT chk_faixas_horas_extra_1         CHECK(calculo_horas_extra IN (\'D\',\'M\',\'S\'))
);
');

select atualizarBanco('
GRANT ALL ON TABLE ponto.faixas_horas_extra   TO GROUP urbem;
');

select atualizarBanco('
CREATE TABLE ponto.faixas_dias (
    cod_configuracao    INTEGER                 NOT NULL,
    timestamp           TIMESTAMP               NOT NULL,
    cod_faixa           INTEGER                 NOT NULL,
    cod_dia             INTEGER                 NOT NULL,
    CONSTRAINT pk_faixas_dias                   PRIMARY KEY                                     (cod_configuracao, timestamp, cod_faixa, cod_dia),
    CONSTRAINT fk_faixas_dias_1                 FOREIGN KEY                                     (cod_configuracao, timestamp, cod_faixa)
                                                REFERENCES ponto.faixas_horas_extra             (cod_configuracao, timestamp, cod_faixa),
    CONSTRAINT fk_faixas_dias_2                 FOREIGN KEY                                     (cod_dia)
                                                REFERENCES pessoal.dias_turno                   (cod_dia)
);
');

select atualizarBanco('
GRANT ALL ON TABLE ponto.faixas_dias   TO GROUP urbem;
');


----------------
-- Ticket #12645
----------------
select atualizarBanco('
CREATE TABLE ponto.dados_relogio_ponto (
    cod_contrato        INTEGER                 NOT NULL,
    CONSTRAINT pk_dados_relogio_ponto           PRIMARY KEY                                     (cod_contrato),
    CONSTRAINT fk_dados_relogio_ponto_1         FOREIGN KEY                                     (cod_contrato)
                                                REFERENCES pessoal.contrato                     (cod_contrato)
);
');

select atualizarBanco(' GRANT ALL ON TABLE ponto.dados_relogio_ponto   TO GROUP urbem;');

select atualizarBanco('
CREATE TABLE ponto.dados_relogio_ponto_extras (
    cod_contrato        INTEGER                 NOT NULL,
    timestamp           TIMESTAMP               NOT NULL DEFAULT (\'now\'::text)::timestamp(3) with time zone,
    autorizar_horas_extras BOOLEAN              NOT NULL DEFAULT \'FALSE\',
    CONSTRAINT pk_dados_relogio_ponto_extra     PRIMARY KEY                                     (cod_contrato, timestamp),
    CONSTRAINT fk_dados_relogio_ponto_extra_1   FOREIGN KEY                                     (cod_contrato)
                                                REFERENCES ponto.dados_relogio_ponto            (cod_contrato)
);
');

select atualizarBanco(' GRANT ALL ON TABLE ponto.dados_relogio_ponto_extras   TO GROUP urbem;');

select atualizarBanco('
CREATE TABLE ponto.relogio_ponto_dias (
    cod_contrato        INTEGER                 NOT NULL,
    cod_ponto           INTEGER                 NOT NULL,
    dt_ponto            DATE                    NOT NULL,
    CONSTRAINT pk_relogio_ponto_dias            PRIMARY KEY                                     (cod_contrato, cod_ponto),
    CONSTRAINT fk_relogio_ponto_dias_1          FOREIGN KEY                                     (cod_contrato)
                                                REFERENCES ponto.dados_relogio_ponto            (cod_contrato)
);
');

select atualizarBanco('
GRANT ALL ON TABLE ponto.relogio_ponto_dias   TO GROUP urbem;
');

select atualizarBanco('
CREATE TABLE ponto.relogio_ponto_horario (
    cod_contrato        INTEGER                 NOT NULL,
    cod_ponto           INTEGER                 NOT NULL,
    cod_horario         INTEGER                 NOT NULL,
    timestamp           TIMESTAMP               NOT NULL DEFAULT (\'now\'::text)::timestamp(3) with time zone,
    hora                TIME                    NOT NULL,
    CONSTRAINT pk_relogio_ponto_horario         PRIMARY KEY                                     (cod_contrato, cod_ponto, cod_horario, timestamp),
    CONSTRAINT fk_relogio_ponto_horario_1       FOREIGN KEY                                     (cod_contrato, cod_ponto)
                                                REFERENCES ponto.relogio_ponto_dias             (cod_contrato, cod_ponto)
);
');

select atualizarBanco('
GRANT ALL ON TABLE ponto.relogio_ponto_horario   TO GROUP urbem;
');

select atualizarBanco('
CREATE TABLE ponto.relogio_ponto_justificativa (
    cod_contrato        INTEGER                 NOT NULL,
    timestamp           TIMESTAMP               NOT NULL DEFAULT (\'now\'::text)::timestamp(3) with time zone,
    cod_justificativa   INTEGER                 NOT NULL,
    sequencia           INTEGER                 NOT NULL,
    periodo_inicio      DATE                    NOT NULL,
    periodo_termino     DATE                    NOT NULL,
    horas_falta         TIME                    NOT NULL,
    horas_abonar        TIME                    NOT NULL,
    observacao          TEXT                    NULL,
    CONSTRAINT pk_relogio_ponto_justificativa   PRIMARY KEY                                     (cod_contrato, timestamp, cod_justificativa, sequencia),
    CONSTRAINT fk_relogio_ponto_justificativa_1 FOREIGN KEY                                     (cod_contrato)
                                                REFERENCES ponto.dados_relogio_ponto            (cod_contrato),
    CONSTRAINT fk_relogio_ponto_justificativa_2 FOREIGN KEY                                     (cod_justificativa)
                                                REFERENCES ponto.justificativa                  (cod_justificativa)
);
');

select atualizarBanco('
GRANT ALL ON TABLE ponto.relogio_ponto_justificativa   TO GROUP urbem;
');

select atualizarBanco('
CREATE TABLE ponto.relogio_ponto_justificativa_exclusao (
    cod_contrato        INTEGER                 NOT NULL,
    timestamp           TIMESTAMP               NOT NULL,
    cod_justificativa   INTEGER                 NOT NULL,
    sequencia           INTEGER                 NOT NULL,
    timestamp_exclusao  TIMESTAMP               NOT NULL DEFAULT (\'now\'::text)::timestamp(3) with time zone,
    CONSTRAINT pk_relogio_ponto_justificativa_exclusao   PRIMARY KEY                                     (cod_contrato, timestamp, cod_justificativa, sequencia),
    CONSTRAINT fk_relogio_ponto_justificativa_exclusao_1 FOREIGN KEY                                     (cod_contrato, timestamp, cod_justificativa, sequencia)
                                                         REFERENCES ponto.relogio_ponto_justificativa    (cod_contrato, timestamp, cod_justificativa, sequencia)
);
');

select atualizarBanco('
GRANT ALL ON TABLE ponto.relogio_ponto_justificativa_exclusao   TO GROUP urbem;
');
---------------------------------------------------------
-- ACOES - solicitado por DIEGO LEMOS DE SOUZA - 20081013
---------------------------------------------------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2379
          , 440
          , 'FMManterConfiguracaoPonto.php'
          , 'incluir'
          , 2
          , ''
          , 'Incluir Configuração Ponto'
          );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2380
          , 440
          , 'FLManterConfiguracaoPonto.php'
          , 'alterar'
          , 3
          , ''
          , 'Alterar Configuração Ponto'
          );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2381
          , 440
          , 'FLManterConfiguracaoPonto.php'
          , 'excluir'
          , 4
          , ''
          , 'Excluir Configuração Ponto'
          );


INSERT INTO administracao.funcionalidade
         ( cod_funcionalidade
         , cod_modulo
         , nom_funcionalidade
         , nom_diretorio
         , ordem )
    VALUES ( 443
         , 51
         , 'Manutenção'
         , 'instancias/manutencao/'
         , 6
         );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2382
          , 443
          , 'FLManterPonto.php'
          , 'alterar'
          , 1
          , ''
          , 'Alterar Ponto'
          );


-------------------------------------------------
-- SOLICITADO POR DIEGO LEMOS DE SOUZA - 20081015
-------------------------------------------------

INSERT INTO administracao.funcionalidade
         ( cod_funcionalidade
         , cod_modulo
         , nom_funcionalidade
         , nom_diretorio
         , ordem )
    VALUES ( 444
         , 51
         , 'Relatórios'
         , 'instancias/relatorios/'
         , 20
         );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2383
          , 444
          , 'FLRelatorioEspelhoPonto.php'
          , 'emitir'
          , 1
          , ''
          , 'Espelho Ponto'
          );

INSERT INTO administracao.relatorio
         ( cod_gestao
         , cod_modulo
         , cod_relatorio
         , nom_relatorio
         , arquivo )
    VALUES ( 4
         , 51
         , 4
         , 'Espelho Ponto'
         , 'espelhoPonto.rptdesign'
         );


----------------
-- Ticket #13570
----------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2384
          , 444
          , 'FLGradeHorarios.php'
          , 'emitir'
          , 2
          , ''
          , 'Grade de Horários'
          );

INSERT INTO administracao.relatorio
         ( cod_gestao
         , cod_modulo
         , cod_relatorio
         , nom_relatorio
         , arquivo )
    VALUES ( 4
         , 51
         , 5
         , 'Grade de Horários'
         , 'gradeHorario.rptdesign'
         );

INSERT INTO administracao.relatorio
         ( cod_gestao
         , cod_modulo
         , cod_relatorio
         , nom_relatorio
         , arquivo )
    VALUES ( 4
         , 51
         , 6
         , 'Servidor / Grade'
         , 'servidoresGrade.rptdesign'
         );

INSERT INTO administracao.relatorio
         ( cod_gestao
         , cod_modulo
         , cod_relatorio
         , nom_relatorio
         , arquivo )
    VALUES ( 4
         , 51
         , 8
         , 'Servidor / Grade'
         , 'servidoresGradeAgrupamento.rptdesign'
         );

----------------
-- Ticket #13788
----------------
select atualizarBanco('
CREATE TABLE ponto.tipo_informacao(
    cod_tipo            INTEGER                 NOT NULL,
    descricao           VARCHAR(30)             NOT NULL,
    CONSTRAINT pk_tipo_informacao               PRIMARY KEY                             (cod_tipo)
 );
');

select atualizarBanco('
CREATE TABLE ponto.formato_exportacao(
    cod_formato         INTEGER                 NOT NULL,
    descricao           VARCHAR(60)             NOT NULL,
    formato_minutos     CHAR(1)                 NOT NULL,
    CONSTRAINT pk_formato_exportacao            PRIMARY KEY                             (cod_formato),
    CONSTRAINT ck_formato_exportacao_1          CHECK (formato_minutos IN (\'D\',\'H\'))
 );
');

select atualizarBanco('
CREATE TABLE ponto.dados_exportacao(
    cod_formato         INTEGER                 NOT NULL,
    cod_dado            INTEGER                 NOT NULL,
    cod_tipo            INTEGER                 NOT NULL,
    cod_evento          INTEGER                 NOT NULL,
    CONSTRAINT pk_dados_exportacao              PRIMARY KEY                             (cod_formato, cod_dado),
    CONSTRAINT fk_dados_exportacao_1            FOREIGN KEY                             (cod_formato)
                                                REFERENCES ponto.formato_exportacao     (cod_formato),
    CONSTRAINT fk_dados_exportacao_2            FOREIGN KEY                             (cod_tipo)
                                                REFERENCES ponto.tipo_informacao        (cod_tipo),
    CONSTRAINT fk_dados_exportacao_3            FOREIGN KEY                             (cod_evento)
                                                REFERENCES folhapagamento.evento        (cod_evento)
 );
');

select atualizarBanco('
CREATE TABLE ponto.formato_informacao(
    cod_formato         INTEGER                 NOT NULL,
    cod_dado            INTEGER                 NOT NULL,
    formato             CHAR(1)                 NOT NULL,
    CONSTRAINT pk_formato_informacao            PRIMARY KEY                             (cod_formato, cod_dado),
    CONSTRAINT fk_formato_informacao_1          FOREIGN KEY                             (cod_formato, cod_dado)
                                                REFERENCES  ponto.dados_exportacao      (cod_formato, cod_dado),
    CONSTRAINT ck_formato_informacao_1          CHECK (formato IN (\'D\',\'H\'))
 );
');

select atualizarBanco('
CREATE TABLE ponto.formato_faixas_horas_extras(
    cod_formato         INTEGER                 NOT NULL,
    cod_dado            INTEGER                 NOT NULL,
    cod_configuracao    INTEGER                 NOT NULL,
    timestamp           TIMESTAMP               NOT NULL,
    cod_faixa           INTEGER                 NOT NULL,
    CONSTRAINT pk_formato_faixas_horas_extras   PRIMARY KEY                             (cod_formato, cod_dado, cod_configuracao, timestamp, cod_faixa),
    CONSTRAINT fk_formato_faixas_horas_extras_1 FOREIGN KEY                             (cod_formato, cod_dado)
                                                REFERENCES ponto.dados_exportacao       (cod_formato, cod_dado),
    CONSTRAINT fk_formato_faixas_horas_extras_2 FOREIGN KEY                             (cod_configuracao, timestamp, cod_faixa)
                                                REFERENCES ponto.faixas_horas_extra     (cod_configuracao, timestamp, cod_faixa)
 );
');


select atualizarBanco('GRANT ALL ON ponto.tipo_informacao              TO GROUP urbem;');
select atualizarBanco('GRANT ALL ON ponto.formato_exportacao           TO GROUP urbem;');
select atualizarBanco('GRANT ALL ON ponto.dados_exportacao             TO GROUP urbem;');
select atualizarBanco('GRANT ALL ON ponto.formato_informacao           TO GROUP urbem;');
select atualizarBanco('GRANT ALL ON ponto.formato_faixas_horas_extras  TO GROUP urbem;');


INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2385
          , 440
          , 'FMManterFormatoExportacao.php'
          , 'incluir'
          , 5
          , ''
          , 'Incluir Formato de Exportação'
          );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2386
          , 440
          , 'FLManterFormatoExportacao.php'
          , 'alterar'
          , 6
          , ''
          , 'Alterar Formato de Exportação'
          );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2387
          , 440
          , 'FLManterFormatoExportacao.php'
          , 'excluir'
          , 7
          , ''
          , 'Excluir Formato de Exportação'
          );


select atualizarBanco('INSERT INTO ponto.tipo_informacao(cod_tipo,descricao) VALUES (1,\'Horas Trabalhadas\');');
select atualizarBanco('INSERT INTO ponto.tipo_informacao(cod_tipo,descricao) VALUES (2,\'Adicional Noturno\');');
select atualizarBanco('INSERT INTO ponto.tipo_informacao(cod_tipo,descricao) VALUES (3,\'Atrasos\');');
select atualizarBanco('INSERT INTO ponto.tipo_informacao(cod_tipo,descricao) VALUES (4,\'Faltas\');');
select atualizarBanco('INSERT INTO ponto.tipo_informacao(cod_tipo,descricao) VALUES (5,\'Abonos DSR\');');
select atualizarBanco('INSERT INTO ponto.tipo_informacao(cod_tipo,descricao) VALUES (6,\'Descontos DSR\');');
select atualizarBanco('INSERT INTO ponto.tipo_informacao(cod_tipo,descricao) VALUES (7,\'Horas Extras\');');

INSERT INTO administracao.tabelas_rh (schema_cod,nome_tabela,sequencia) VALUES (9,'tipo_informacao',1);


UPDATE administracao.funcionalidade
   SET ordem = ordem + 1
 WHERE ordem > 4
   AND cod_modulo = 51;

INSERT INTO administracao.funcionalidade
         ( cod_funcionalidade
         , cod_modulo
         , nom_funcionalidade
         , nom_diretorio
         , ordem )
    VALUES ( 445
         , 51
         , 'Exportação'
         , 'instancias/exportacao/'
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
     VALUES ( 2388
          , 445
          , 'FLManterExportacao.php'
          , 'exportar'
          , 1
          , ''
          , 'Exportar Pontos'
          );

INSERT INTO administracao.relatorio
         ( cod_gestao
         , cod_modulo
         , cod_relatorio
         , nom_relatorio
         , arquivo )
    VALUES ( 4
         , 51
         , 7
         , 'Exportação do Ponto'
         , 'exportacaoPonto.rptdesign'
         );


CREATE TYPE colunasRelatorioCartaoPonto AS (
    dsr             BOOLEAN,
    data            VARCHAR(10),
    dia             VARCHAR(10),
    tipo            VARCHAR(10),
    origem          VARCHAR(15),
    horarios        VARCHAR,
    justificativa   VARCHAR(30),
    carga_horaria   VARCHAR(5),
    hs_trab         VARCHAR(10),
    ad_not          VARCHAR(10),
    extras          VARCHAR(10),
    ext_not         VARCHAR(10),
    atrasos         VARCHAR(10),
    faltas          VARCHAR(10),
    hs_tot          VARCHAR(10)
);

CREATE TYPE colunasTipoOrigemTurnoServidor AS (
    compensacao             BOOLEAN,
    compensacao_dt_falta    VARCHAR(10),
    calendario              BOOLEAN,
    escala                  BOOLEAN,
    grade                   BOOLEAN,
    dia_trabalho            BOOLEAN,
    descricao_tipo          VARCHAR(15),
    descricao_origem        VARCHAR(15)
);


CREATE TYPE colunasRelatorioCartaoPontoResumo AS (
    qtd_dsr             INTEGER,
    abono_dsr           VARCHAR(10),
    desc_dsr            VARCHAR(10),
    abono_justificado   VARCHAR(10),
    faltas              INTEGER,
    extras              VARCHAR(10),
    banco_horas         VARCHAR(10)
);

CREATE TYPE colunasRelatorioCartaoPontoResumoExtra AS (
    dias                VARCHAR,
    calculo             VARCHAR(10),
    faixa               VARCHAR(10),
    percentual          VARCHAR(10),
    horas               VARCHAR(10)
);

CREATE TYPE colunasExportarPonto AS (
    registro            INTEGER,
    codigo_evento       VARCHAR(5),
    valor               INTEGER,
    quantidade          VARCHAR(10),
    quantidade_parcelas INTEGER
);


CREATE TYPE colunasRelogioPontoPeriodo AS (
    cod_contrato               INTEGER,
    data                       VARCHAR,
    dia                        VARCHAR,
    horario                    VARCHAR,
    horario_padrao             VARCHAR,
    carga_horaria_padrao       VARCHAR,
    tipo                       VARCHAR,
    justificativa_afastamento  VARCHAR,
    horas_faltas_anuladas      VARCHAR,
    horas_abonadas             VARCHAR,
    horas_trabalho             VARCHAR,
    horas_faltas               VARCHAR,
    origem                     VARCHAR
);

-------------------------------------------------
-- SOLICITADO POR DIEGO LEMOS DE SOUZA - 20081024
-------------------------------------------------

select atualizarBanco('
CREATE TABLE ponto.exportacao_ponto(
    cod_contrato        INTEGER                 NOT NULL,
    cod_evento          INTEGER                 NOT NULL,
    cod_tipo            INTEGER                 NOT NULL,
    lancamento          VARCHAR(20)             NOT NULL,
    formato             CHAR(1)                 NOT NULL,
    CONSTRAINT pk_exportacao_ponto              PRIMARY KEY                             (cod_contrato, cod_evento),
    CONSTRAINT fk_exportacao_ponto_1            FOREIGN KEY                             (cod_contrato)
                                                REFERENCES pessoal.contrato             (cod_contrato),
    CONSTRAINT fk_exportacao_ponto_2            FOREIGN KEY                             (cod_evento)
                                                REFERENCES folhapagamento.evento        (cod_evento),
    CONSTRAINT fk_exportacao_ponto_3            FOREIGN KEY                             (cod_tipo)
                                                REFERENCES ponto.tipo_informacao        (cod_tipo),
    CONSTRAINT ck_formato_informacao_1          CHECK (formato IN (\'D\',\'H\'))
 );
');

select atualizarBanco('GRANT ALL ON ponto.exportacao_ponto TO GROUP urbem;');


select atualizarBanco('
CREATE TABLE ponto.relatorio_espelho_ponto(
    cod_contrato    INTEGER         NOT NULL,
    sequencia       INTEGER         NOT NULL,        
    data            VARCHAR(10)     ,
    dia             VARCHAR(10)     ,
    tipo            VARCHAR(10)     ,
    origem          VARCHAR(15)     ,
    horarios        VARCHAR         ,
    justificativa   VARCHAR(30)     ,
    carga_horaria   VARCHAR(5)      ,
    hs_trab         VARCHAR(10)     ,
    ad_not          VARCHAR(10)     ,
    extras          VARCHAR(10)     ,
    ext_not         VARCHAR(10)     ,
    atrasos         VARCHAR(10)     ,
    faltas          VARCHAR(10)     ,
    hs_tot          VARCHAR(10)     ,
    CONSTRAINT pk_relatorio_espelho_ponto   PRIMARY KEY                     (cod_contrato, sequencia),
    CONSTRAINT fk_relatorio_espelho_ponto_1 FOREIGN KEY                     (cod_contrato)
                                            REFERENCES  pessoal.contrato    (cod_contrato)    
 );
');

select atualizarBanco('GRANT ALL ON ponto.relatorio_espelho_ponto TO GROUP urbem;');
