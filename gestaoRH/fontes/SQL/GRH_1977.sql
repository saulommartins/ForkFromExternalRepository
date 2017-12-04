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
* $Id: $
*
* Versão 1.97.7
*/
-----------------------------------------
-- ADICIONANDO RELATORIO Recibo de Ferias
-----------------------------------------

INSERT INTO administracao.relatorio
          ( cod_gestao
          , cod_modulo
          , cod_relatorio
          , nom_relatorio
          , arquivo )
     VALUES ( 4
          , 27
          , 22
          , 'Recibo de Férias'
          , 'reciboDeFerias.rptdesign'
          );


----------------
-- Ticket #15808
----------------

UPDATE administracao.funcionalidade
   SET nom_funcionalidade = 'Causa de Rescisão'
 WHERE cod_funcionalidade = 245
     ;


------------------------------------------------------------------------------------------------------------------------------------
-- ALTERAÇÔES P/ MANUTENCAO DE HISTORICO EM ima.configuracao_bb_conta, ima.configuracao_besc_conta E ima.configuracao_banrisul_conta
------------------------------------------------------------------------------------------------------------------------------------

-- ima.configuracao_bb_conta
--
SELECT atualizarbanco('ALTER TABLE ima.configuracao_bb_local DROP CONSTRAINT fk_configuracao_bb_local_1;');
SELECT atualizarbanco('ALTER TABLE ima.configuracao_bb_local DROP CONSTRAINT pk_configuracao_bb_local;  ');
SELECT atualizarbanco('ALTER TABLE ima.configuracao_bb_orgao DROP CONSTRAINT fk_configuracao_bb_orgao_1;');
SELECT atualizarbanco('ALTER TABLE ima.configuracao_bb_orgao DROP CONSTRAINT pk_configuracao_bb_orgao;  ');
SELECT atualizarbanco('ALTER TABLE ima.configuracao_bb_conta DROP CONSTRAINT pk_configuracao_bb_conta;  ');


SELECT atualizarbanco('ALTER TABLE ima.configuracao_bb_conta ADD   COLUMN timestamp TIMESTAMP;          ');
SELECT atualizarbanco('ALTER TABLE ima.configuracao_bb_conta ADD   COLUMN vigencia  DATE;               ');
SELECT atualizarbanco('
                        UPDATE ima.configuracao_bb_conta 
                           SET timestamp = now()::timestamp(3)
                             , vigencia = (
                                            SELECT dt_inicial
                                              FROM folhapagamento.periodo_movimentacao
                                             WHERE (
                                                     SELECT min(timestamp)::date
                                                       FROM pessoal.contrato_servidor_orgao
                                                   ) BETWEEN dt_inicial AND dt_final
                                          )
                             ;
');

SELECT atualizarbanco('ALTER TABLE ima.configuracao_bb_conta ALTER COLUMN vigencia  SET NOT NULL;       ');
SELECT atualizarbanco('ALTER TABLE ima.configuracao_bb_conta ALTER COLUMN timestamp SET NOT NULL;');
SELECT atualizarbanco('ALTER TABLE ima.configuracao_bb_conta ALTER COLUMN timestamp SET DEFAULT (\'now\'::text)::timestamp(3) WITH TIME ZONE;');


SELECT atualizarbanco('ALTER TABLE ima.configuracao_bb_orgao ADD   COLUMN timestamp TIMESTAMP;          ');
SELECT atualizarbanco('
                        UPDATE ima.configuracao_bb_orgao 
                           SET timestamp = ( SELECT timestamp
                                               FROM ima.configuracao_bb_conta
                                              WHERE ima.configuracao_bb_conta.cod_convenio = ima.configuracao_bb_orgao.cod_convenio
                                                AND ima.configuracao_bb_conta.cod_banco = ima.configuracao_bb_orgao.cod_banco
                                                AND ima.configuracao_bb_conta.cod_agencia = ima.configuracao_bb_orgao.cod_agencia
                                                AND ima.configuracao_bb_conta.cod_conta_corrente = ima.configuracao_bb_orgao.cod_conta_corrente
                                           )
                             ;
');

SELECT atualizarbanco('ALTER TABLE ima.configuracao_bb_orgao ALTER COLUMN timestamp SET NOT NULL;       ');


SELECT atualizarbanco('ALTER TABLE ima.configuracao_bb_local ADD   COLUMN timestamp TIMESTAMP;          ');
SELECT atualizarbanco('
                        UPDATE ima.configuracao_bb_local
                           SET timestamp = ( SELECT timestamp
                                               FROM ima.configuracao_bb_conta
                                              WHERE ima.configuracao_bb_conta.cod_convenio       = ima.configuracao_bb_local.cod_convenio
                                                AND ima.configuracao_bb_conta.cod_banco          = ima.configuracao_bb_local.cod_banco
                                                AND ima.configuracao_bb_conta.cod_agencia        = ima.configuracao_bb_local.cod_agencia
                                                AND ima.configuracao_bb_conta.cod_conta_corrente = ima.configuracao_bb_local.cod_conta_corrente
                                            )
                             ;
');

SELECT atualizarbanco('ALTER TABLE ima.configuracao_bb_local ALTER COLUMN timestamp SET NOT NULL;       ');


SELECT atualizarbanco('ALTER TABLE ima.configuracao_bb_conta ADD CONSTRAINT pk_configuracao_bb_conta   PRIMARY KEY                          (cod_convenio, cod_banco, cod_agencia, cod_conta_corrente, timestamp);');
SELECT atualizarbanco('ALTER TABLE ima.configuracao_bb_orgao ADD CONSTRAINT pk_configuracao_bb_orgao   PRIMARY KEY                          (cod_convenio, cod_banco, cod_agencia, cod_conta_corrente, timestamp, cod_orgao);');
SELECT atualizarbanco('ALTER TABLE ima.configuracao_bb_orgao ADD CONSTRAINT fk_configuracao_bb_orgao_1 FOREIGN KEY                          (cod_convenio, cod_banco, cod_agencia, cod_conta_corrente, timestamp)
                                                                                REFERENCES ima.configuracao_bb_conta (cod_convenio, cod_banco, cod_agencia, cod_conta_corrente, timestamp);
');
SELECT atualizarbanco('ALTER TABLE ima.configuracao_bb_local ADD CONSTRAINT pk_configuracao_bb_local   PRIMARY KEY                          (cod_convenio, cod_banco, cod_agencia, cod_conta_corrente, timestamp, cod_local);');
SELECT atualizarbanco('ALTER TABLE ima.configuracao_bb_local ADD CONSTRAINT fk_configuracao_bb_local_1 FOREIGN KEY                          (cod_convenio, cod_banco, cod_agencia, cod_conta_corrente, timestamp)
                                                                                REFERENCES ima.configuracao_bb_conta (cod_convenio, cod_banco, cod_agencia, cod_conta_corrente, timestamp);
');

-- ima.configuracao_besc_conta
--
SELECT atualizarbanco('ALTER TABLE ima.configuracao_besc_local DROP CONSTRAINT fk_configuracao_besc_local_1;');
SELECT atualizarbanco('ALTER TABLE ima.configuracao_besc_local DROP CONSTRAINT pk_configuracao_besc_local;  ');
SELECT atualizarbanco('ALTER TABLE ima.configuracao_besc_orgao DROP CONSTRAINT fk_configuracao_besc_orgao_1;');
SELECT atualizarbanco('ALTER TABLE ima.configuracao_besc_orgao DROP CONSTRAINT pk_configuracao_besc_orgao;  '); 
SELECT atualizarbanco('ALTER TABLE ima.configuracao_besc_conta DROP CONSTRAINT pk_configuracao_besc_conta;  ');


SELECT atualizarbanco('ALTER TABLE ima.configuracao_besc_conta ADD   COLUMN timestamp TIMESTAMP;            ');
SELECT atualizarbanco('ALTER TABLE ima.configuracao_besc_conta ADD   COLUMN vigencia  DATE;                 ');
SELECT atualizarbanco('
                        UPDATE ima.configuracao_besc_conta 
                           SET timestamp = now()::timestamp(3)
                             , vigencia = ( 
                                            SELECT dt_inicial
                                              FROM folhapagamento.periodo_movimentacao
                                             WHERE (
                                                     SELECT min(timestamp)::date
                                                       FROM pessoal.contrato_servidor_orgao
                                                   ) BETWEEN dt_inicial AND dt_final
                                          )
                             ;
');

SELECT atualizarbanco('ALTER TABLE ima.configuracao_besc_conta ALTER COLUMN vigencia  SET NOT NULL;         ');
SELECT atualizarbanco('ALTER TABLE ima.configuracao_besc_conta ALTER COLUMN timestamp SET NOT NULL;');
SELECT atualizarbanco('ALTER TABLE ima.configuracao_besc_conta ALTER COLUMN timestamp SET DEFAULT (\'now\'::text)::timestamp(3) WITH TIME ZONE;');


SELECT atualizarbanco('ALTER TABLE ima.configuracao_besc_orgao ADD   COLUMN timestamp TIMESTAMP;            ');
SELECT atualizarbanco('
                        UPDATE ima.configuracao_besc_orgao 
                           SET timestamp = ( SELECT timestamp
                                               FROM ima.configuracao_besc_conta
                                              WHERE ima.configuracao_besc_conta.cod_convenio       = ima.configuracao_besc_orgao.cod_convenio
                                                AND ima.configuracao_besc_conta.cod_banco          = ima.configuracao_besc_orgao.cod_banco
                                                AND ima.configuracao_besc_conta.cod_agencia        = ima.configuracao_besc_orgao.cod_agencia
                                                AND ima.configuracao_besc_conta.cod_conta_corrente = ima.configuracao_besc_orgao.cod_conta_corrente
                                           )
                             ;
');
SELECT atualizarbanco('ALTER TABLE ima.configuracao_besc_orgao ALTER COLUMN timestamp SET NOT NULL;         ');


SELECT atualizarbanco('ALTER TABLE ima.configuracao_besc_local ADD   COLUMN timestamp TIMESTAMP;            ');
SELECT atualizarbanco('
                        UPDATE ima.configuracao_besc_local
                           SET timestamp = ( SELECT timestamp
                                               FROM ima.configuracao_besc_conta
                                              WHERE ima.configuracao_besc_conta.cod_convenio       = ima.configuracao_besc_local.cod_convenio
                                                AND ima.configuracao_besc_conta.cod_banco          = ima.configuracao_besc_local.cod_banco
                                                AND ima.configuracao_besc_conta.cod_agencia        = ima.configuracao_besc_local.cod_agencia
                                                AND ima.configuracao_besc_conta.cod_conta_corrente = ima.configuracao_besc_local.cod_conta_corrente
                                            )
                             ;
');
SELECT atualizarbanco('ALTER TABLE ima.configuracao_besc_local ALTER COLUMN timestamp SET NOT NULL;         ');


SELECT atualizarbanco('ALTER TABLE ima.configuracao_besc_conta ADD CONSTRAINT pk_configuracao_besc_conta   PRIMARY KEY                            (cod_convenio, cod_banco, cod_agencia, cod_conta_corrente, timestamp);');
SELECT atualizarbanco('ALTER TABLE ima.configuracao_besc_orgao ADD CONSTRAINT pk_configuracao_besc_orgao   PRIMARY KEY                            (cod_convenio, cod_banco, cod_agencia, cod_conta_corrente, timestamp, cod_orgao);');
SELECT atualizarbanco('ALTER TABLE ima.configuracao_besc_orgao ADD CONSTRAINT fk_configuracao_besc_orgao_1 FOREIGN KEY                            (cod_convenio, cod_banco, cod_agencia, cod_conta_corrente, timestamp)
                                                                                                           REFERENCES ima.configuracao_besc_conta (cod_convenio, cod_banco, cod_agencia, cod_conta_corrente, timestamp);
');
SELECT atualizarbanco('ALTER TABLE ima.configuracao_besc_local ADD CONSTRAINT pk_configuracao_besc_local   PRIMARY KEY                            (cod_convenio, cod_banco, cod_agencia, cod_conta_corrente, timestamp, cod_local);');
SELECT atualizarbanco('ALTER TABLE ima.configuracao_besc_local ADD CONSTRAINT fk_configuracao_besc_local_1 FOREIGN KEY                            (cod_convenio, cod_banco, cod_agencia, cod_conta_corrente, timestamp)
                                                                                                           REFERENCES ima.configuracao_besc_conta (cod_convenio, cod_banco, cod_agencia, cod_conta_corrente, timestamp);
');

-- ima.configuracao_banrisul_conta
--
SELECT atualizarbanco('ALTER TABLE ima.configuracao_banrisul_local DROP CONSTRAINT fk_configuracao_banrisul_local_1;');
SELECT atualizarbanco('ALTER TABLE ima.configuracao_banrisul_local DROP CONSTRAINT pk_configuracao_banrisul_local;  ');
SELECT atualizarbanco('ALTER TABLE ima.configuracao_banrisul_orgao DROP CONSTRAINT fk_configuracao_banrisul_orgao_1;');
SELECT atualizarbanco('ALTER TABLE ima.configuracao_banrisul_orgao DROP CONSTRAINT pk_configuracao_banrisul_orgao;  ');
SELECT atualizarbanco('ALTER TABLE ima.configuracao_banrisul_conta DROP CONSTRAINT pk_configuracao_banrisul_conta;  ');


SELECT atualizarbanco('ALTER TABLE ima.configuracao_banrisul_conta ADD   COLUMN timestamp TIMESTAMP;                ');
SELECT atualizarbanco('ALTER TABLE ima.configuracao_banrisul_conta ADD   COLUMN vigencia  DATE;                     ');
SELECT atualizarbanco('
                        UPDATE ima.configuracao_banrisul_conta 
                           SET timestamp = now()::timestamp(3)
                             , vigencia = (
                                            SELECT dt_inicial
                                              FROM folhapagamento.periodo_movimentacao
                                             WHERE (
                                                     SELECT min(timestamp)::date
                                                       FROM pessoal.contrato_servidor_orgao
                                                   ) BETWEEN dt_inicial AND dt_final
                                          )
                             ;
');
SELECT atualizarbanco('ALTER TABLE ima.configuracao_banrisul_conta ALTER COLUMN vigencia  SET NOT NULL;             ');
SELECT atualizarbanco('ALTER TABLE ima.configuracao_banrisul_conta ALTER COLUMN timestamp SET NOT NULL;');
SELECT atualizarbanco('ALTER TABLE ima.configuracao_banrisul_conta ALTER COLUMN timestamp SET DEFAULT (\'now\'::text)::timestamp(3) WITH TIME ZONE;');


SELECT atualizarbanco('ALTER TABLE ima.configuracao_banrisul_orgao ADD   COLUMN timestamp TIMESTAMP;                ');
SELECT atualizarbanco('
                        UPDATE ima.configuracao_banrisul_orgao 
                           SET timestamp = ( SELECT timestamp
                                               FROM ima.configuracao_banrisul_conta
                                              WHERE ima.configuracao_banrisul_conta.cod_convenio       = ima.configuracao_banrisul_orgao.cod_convenio
                                                AND ima.configuracao_banrisul_conta.cod_banco          = ima.configuracao_banrisul_orgao.cod_banco
                                                AND ima.configuracao_banrisul_conta.cod_agencia        = ima.configuracao_banrisul_orgao.cod_agencia
                                                AND ima.configuracao_banrisul_conta.cod_conta_corrente = ima.configuracao_banrisul_orgao.cod_conta_corrente
                                           )
                             ;
');
SELECT atualizarbanco('ALTER TABLE ima.configuracao_banrisul_orgao ALTER COLUMN timestamp SET NOT NULL;             ');


SELECT atualizarbanco('ALTER TABLE ima.configuracao_banrisul_local ADD   COLUMN timestamp TIMESTAMP;                ');
SELECT atualizarbanco('
                        UPDATE ima.configuracao_banrisul_local
                           SET timestamp = ( SELECT timestamp
                                               FROM ima.configuracao_banrisul_conta
                                              WHERE ima.configuracao_banrisul_conta.cod_convenio       = ima.configuracao_banrisul_local.cod_convenio
                                                AND ima.configuracao_banrisul_conta.cod_banco          = ima.configuracao_banrisul_local.cod_banco
                                                AND ima.configuracao_banrisul_conta.cod_agencia        = ima.configuracao_banrisul_local.cod_agencia
                                                AND ima.configuracao_banrisul_conta.cod_conta_corrente = ima.configuracao_banrisul_local.cod_conta_corrente
                                            )
                             ;
');
SELECT atualizarbanco('ALTER TABLE ima.configuracao_banrisul_local ALTER COLUMN timestamp SET NOT NULL;             ');


SELECT atualizarbanco('ALTER TABLE ima.configuracao_banrisul_conta ADD CONSTRAINT pk_configuracao_banrisul_conta   PRIMARY KEY                                (cod_convenio, cod_banco, cod_agencia, cod_conta_corrente, timestamp);');
SELECT atualizarbanco('ALTER TABLE ima.configuracao_banrisul_orgao ADD CONSTRAINT pk_configuracao_banrisul_orgao   PRIMARY KEY                                (cod_convenio, cod_banco, cod_agencia, cod_conta_corrente, timestamp, cod_orgao);');
SELECT atualizarbanco('ALTER TABLE ima.configuracao_banrisul_orgao ADD CONSTRAINT fk_configuracao_banrisul_orgao_1 FOREIGN KEY                                (cod_convenio, cod_banco, cod_agencia, cod_conta_corrente, timestamp)
                                                                                                                   REFERENCES ima.configuracao_banrisul_conta (cod_convenio, cod_banco, cod_agencia, cod_conta_corrente, timestamp);
');
SELECT atualizarbanco('ALTER TABLE ima.configuracao_banrisul_local ADD CONSTRAINT pk_configuracao_banrisul_local   PRIMARY KEY                                (cod_convenio, cod_banco, cod_agencia, cod_conta_corrente, timestamp, cod_local);');
SELECT atualizarbanco('ALTER TABLE ima.configuracao_banrisul_local ADD CONSTRAINT fk_configuracao_banrisul_local_1 FOREIGN KEY                                (cod_convenio, cod_banco, cod_agencia, cod_conta_corrente, timestamp)
                                                                                                                   REFERENCES ima.configuracao_banrisul_conta (cod_convenio, cod_banco, cod_agencia, cod_conta_corrente, timestamp);
');

-- AÇÔES incluir, alterar E excluir DE CADA BANCO
--
UPDATE administracao.acao
   SET ordem = ordem + 2
 WHERE cod_funcionalidade = 354
   AND ordem > 1
     ;
INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2756
          , 354
          , 'FMManterConfiguracaoExportacao.php'
          , 'incluir'
          , 1
          , ''
          , 'Incluir Exportação Banco Brasil'
          );
INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2757
          , 354
          , 'LSManterConfiguracaoExportacao.php'
          , 'alterar'
          , 2
          , ''
          , 'Alterar Exportação Banco Brasil'
          );
INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2758
          , 354
          , 'LSManterConfiguracaoExportacao.php'
          , 'excluir'
          , 3
          , ''
          , 'Excluir Exportação Banco Brasil'
          );

UPDATE administracao.acao
   SET ordem = ordem + 2
 WHERE cod_funcionalidade = 354
   AND ordem > 6
     ;
INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2759
          , 354
          , 'FMExportacaoBancoBESC.php'
          , 'incluir'
          , 6
          , ''
          , 'Incluir Exportação BESC'
          );
INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2760
          , 354
          , 'LSExportacaoBancoBESC.php'
          , 'alterar'
          , 7
          , ''
          , 'Alterar Exportação BESC'
          );
INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2761
          , 354
          , 'LSExportacaoBancoBESC.php'
          , 'excluir'
          , 8
          , ''
          , 'Excluir Exportação BESC'
          );

UPDATE administracao.acao
   SET ordem = ordem + 2
 WHERE cod_funcionalidade = 354
   AND ordem > 10
     ;
INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2762
          , 354
          , 'FMExportacaoBancoBanrisul.php'
          , 'incluir'
          , 10
          , ''
          , 'Incluir Exportação Banrisul'
          );
INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2763
          , 354
          , 'LSExportacaoBancoBanrisul.php'
          , 'alterar'
          , 11
          , ''
          , 'Alterar Exportação Banrisul'
          );
INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2764
          , 354
          , 'LSExportacaoBancoBanrisul.php'
          , 'excluir'
          , 12
          , ''
          , 'Excluir Exportação Banrisul'
          );

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE
    stSQL       VARCHAR;
    reRecord    RECORD;
BEGIN

    stSQL := '
                SELECT numcgm
                     , ano_exercicio
                  FROM administracao.permissao
                 WHERE cod_acao = 1705
                     ;
             ';

    FOR reRecord IN EXECUTE stSQL LOOP
        INSERT INTO administracao.permissao (numcgm, cod_acao, ano_exercicio) VALUES (reRecord.numcgm, 2756, reRecord.ano_exercicio);
        INSERT INTO administracao.permissao (numcgm, cod_acao, ano_exercicio) VALUES (reRecord.numcgm, 2757, reRecord.ano_exercicio);
        INSERT INTO administracao.permissao (numcgm, cod_acao, ano_exercicio) VALUES (reRecord.numcgm, 2758, reRecord.ano_exercicio);
    END LOOP;

    stSQL := '
                SELECT numcgm
                     , ano_exercicio
                  FROM administracao.permissao
                 WHERE cod_acao = 1864
                     ;
             ';

    FOR reRecord IN EXECUTE stSQL LOOP
        INSERT INTO administracao.permissao (numcgm, cod_acao, ano_exercicio) VALUES (reRecord.numcgm, 2759, reRecord.ano_exercicio);
        INSERT INTO administracao.permissao (numcgm, cod_acao, ano_exercicio) VALUES (reRecord.numcgm, 2760, reRecord.ano_exercicio);
        INSERT INTO administracao.permissao (numcgm, cod_acao, ano_exercicio) VALUES (reRecord.numcgm, 2761, reRecord.ano_exercicio);
    END LOOP;

    stSQL := '
                SELECT numcgm
                     , ano_exercicio
                  FROM administracao.permissao
                 WHERE cod_acao = 2177
                     ;
             ';

    FOR reRecord IN EXECUTE stSQL LOOP
        INSERT INTO administracao.permissao (numcgm, cod_acao, ano_exercicio) VALUES (reRecord.numcgm, 2762, reRecord.ano_exercicio);
        INSERT INTO administracao.permissao (numcgm, cod_acao, ano_exercicio) VALUES (reRecord.numcgm, 2763, reRecord.ano_exercicio);
        INSERT INTO administracao.permissao (numcgm, cod_acao, ano_exercicio) VALUES (reRecord.numcgm, 2764, reRecord.ano_exercicio);
    END LOOP;

END;
$$ LANGUAGE 'plpgsql';
SELECT        manutencao();
DROP FUNCTION manutencao();

DELETE
  FROM administracao.permissao
 WHERE cod_acao IN (1705, 1864, 2177);

DELETE
  FROM administracao.auditoria
 WHERE cod_acao IN (1705, 1864, 2177);

DELETE
  FROM administracao.acao
 WHERE cod_acao IN (1705, 1864, 2177);


-- ima.banpara_orgao
--
SELECT atualizarbanco('ALTER TABLE ima.banpara_local   DROP CONSTRAINT fk_banpara_local_1;  ');
SELECT atualizarbanco('ALTER TABLE ima.banpara_local   DROP CONSTRAINT fk_banpara_local_2;  ');
SELECT atualizarbanco('ALTER TABLE ima.banpara_local   DROP CONSTRAINT pk_banpara_local;    ');
SELECT atualizarbanco('ALTER TABLE ima.banpara_lotacao DROP CONSTRAINT fk_banpara_lotacao_1;');
SELECT atualizarbanco('ALTER TABLE ima.banpara_lotacao DROP CONSTRAINT fk_banpara_lotacao_2;');
SELECT atualizarbanco('ALTER TABLE ima.banpara_lotacao DROP CONSTRAINT pk_banpara_lotacao;  ');
SELECT atualizarbanco('ALTER TABLE ima.banpara_orgao   DROP CONSTRAINT pk_banpara_orgao;    ');


SELECT atualizarbanco('ALTER TABLE ima.banpara_orgao   ADD   COLUMN num_orgao_banpara INTEGER;  ');
SELECT atualizarbanco('ALTER TABLE ima.banpara_orgao   ADD   COLUMN timestamp         TIMESTAMP;');
SELECT atualizarbanco('ALTER TABLE ima.banpara_orgao   ADD   COLUMN vigencia          DATE;     ');

SELECT atualizarbanco(' UPDATE ima.banpara_orgao 
                           SET timestamp = now()::timestamp(3)
                             , vigencia = ( 
                                            SELECT dt_inicial
                                              FROM folhapagamento.periodo_movimentacao
                                             WHERE (
                                                     SELECT min(timestamp)::date
                                                       FROM pessoal.contrato_servidor_orgao
                                                   ) BETWEEN dt_inicial AND dt_final
                                          )
                             , num_orgao_banpara = cod_orgao
                             ;
');

SELECT atualizarbanco('ALTER TABLE ima.banpara_orgao   ALTER COLUMN vigencia          SET NOT NULL;');
SELECT atualizarbanco('ALTER TABLE ima.banpara_orgao   ALTER COLUMN timestamp         SET NOT NULL;');
SELECT atualizarbanco('ALTER TABLE ima.banpara_orgao   ALTER COLUMN timestamp         SET DEFAULT (\'now\'::text)::timestamp(3) WITH TIME ZONE;');
SELECT atualizarbanco('ALTER TABLE ima.banpara_orgao   ALTER COLUMN num_orgao_banpara SET NOT NULL;');
SELECT atualizarbanco('ALTER TABLE ima.banpara_orgao   DROP  COLUMN cod_orgao;                     ');


SELECT atualizarbanco('ALTER TABLE ima.banpara_lotacao ADD   COLUMN timestamp         TIMESTAMP;');
SELECT atualizarbanco('ALTER TABLE ima.banpara_lotacao ADD   COLUMN num_orgao_banpara INTEGER;  ');

SELECT atualizarbanco('UPDATE ima.banpara_lotacao 
                          SET timestamp = ( SELECT timestamp
                                              FROM ima.banpara_orgao
                                             WHERE ima.banpara_orgao.cod_empresa       = ima.banpara_lotacao.cod_empresa
                                               AND ima.banpara_orgao.num_orgao_banpara = ima.banpara_lotacao.cod_orgao
                                          )
                            , num_orgao_banpara = cod_orgao
                            ;
');

SELECT atualizarbanco('UPDATE ima.banpara_lotacao 
                          SET cod_orgao = cod_lotacao
                            ;
');

SELECT atualizarbanco('ALTER TABLE ima.banpara_lotacao ALTER COLUMN timestamp         SET NOT NULL;');
SELECT atualizarbanco('ALTER TABLE ima.banpara_lotacao ALTER COLUMN num_orgao_banpara SET NOT NULL;');
SELECT atualizarbanco('ALTER TABLE ima.banpara_lotacao DROP  COLUMN cod_lotacao;                   '); 


SELECT atualizarbanco('ALTER TABLE ima.banpara_local   ADD   COLUMN timestamp         TIMESTAMP;');
SELECT atualizarbanco('ALTER TABLE ima.banpara_local   ADD   COLUMN num_orgao_banpara INTEGER;  ');

SELECT atualizarbanco('UPDATE ima.banpara_local 
                          SET timestamp = ( SELECT timestamp
                                              FROM ima.banpara_orgao
                                             WHERE ima.banpara_orgao.cod_empresa       = ima.banpara_local.cod_empresa
                                               AND ima.banpara_orgao.num_orgao_banpara = ima.banpara_local.cod_orgao
                                          )
                            , num_orgao_banpara = cod_orgao
                            ;
');

SELECT atualizarbanco('ALTER TABLE ima.banpara_local   ALTER COLUMN timestamp         SET NOT NULL;');
SELECT atualizarbanco('ALTER TABLE ima.banpara_local   ALTER COLUMN num_orgao_banpara SET NOT NULL;');
SELECT atualizarbanco('ALTER TABLE ima.banpara_local   DROP  COLUMN cod_orgao;                     ');

SELECT atualizarbanco('ALTER TABLE ima.banpara_local   RENAME TO configuracao_banpara_local;');
SELECT atualizarbanco('ALTER TABLE ima.banpara_lotacao RENAME TO configuracao_banpara_orgao;');
SELECT atualizarbanco('ALTER TABLE ima.banpara_orgao   RENAME TO configuracao_banpara      ;');

SELECT atualizarbanco('ALTER TABLE ima.configuracao_banpara         ADD  CONSTRAINT pk_configuracao_banpara           PRIMARY KEY                               (cod_empresa, num_orgao_banpara, timestamp);           ');

SELECT atualizarbanco('ALTER TABLE ima.configuracao_banpara_orgao   ADD  CONSTRAINT pk_configuracao_banpara_orgao     PRIMARY KEY                               (cod_empresa, num_orgao_banpara, timestamp, cod_orgao);');

SELECT atualizarbanco('ALTER TABLE ima.configuracao_banpara_orgao   ADD  CONSTRAINT fk_configuracao_banpara_orgao_1   FOREIGN KEY                               (cod_empresa, num_orgao_banpara, timestamp)
                                                                                                                      REFERENCES ima.configuracao_banpara       (cod_empresa, num_orgao_banpara, timestamp);
');
SELECT atualizarbanco('ALTER TABLE ima.configuracao_banpara_orgao   ADD  CONSTRAINT fk_configuracao_banpara_orgao_2   FOREIGN KEY                               (cod_orgao)
                                                                                                                      REFERENCES organograma.orgao              (cod_orgao);
');
SELECT atualizarbanco('ALTER TABLE ima.configuracao_banpara_local   ADD  CONSTRAINT pk_configuracao_banpara_local     PRIMARY KEY                               (cod_empresa, num_orgao_banpara, timestamp, cod_local);');
SELECT atualizarbanco('ALTER TABLE ima.configuracao_banpara_local   ADD  CONSTRAINT fk_configuracao_banpara_local_1   FOREIGN KEY                               (cod_empresa, num_orgao_banpara, timestamp)
                                                                                                                      REFERENCES ima.configuracao_banpara       (cod_empresa, num_orgao_banpara, timestamp);
');
SELECT atualizarbanco('ALTER TABLE ima.configuracao_banpara_local   ADD  CONSTRAINT fk_configuracao_banpara_local_2   FOREIGN KEY                               (cod_local)
                                                                                                                      REFERENCES organograma.local              (cod_local);
');

SELECT atualizarbanco('ALTER TABLE ima.banpara_empresa RENAME to configuracao_banpara_empresa;                                                                                          ');
SELECT atualizarbanco('ALTER TABLE ima.configuracao_banpara_empresa DROP CONSTRAINT pk_empresa_banpara;                                                                                 ');
SELECT atualizarbanco('ALTER TABLE ima.configuracao_banpara_empresa ADD CONSTRAINT pk_configuracao_banpara_empresa PRIMARY KEY (cod_empresa);                                           ');
SELECT atualizarbanco('ALTER TABLE ima.configuracao_banpara ADD CONSTRAINT pk_configuracao_banpara_1 FOREIGN KEY (cod_empresa) REFERENCES ima.configuracao_banpara_empresa(cod_empresa);');

SELECT atualizarbanco('ALTER TABLE ima.configuracao_banpara DROP COLUMN codigo;');

----------------------------------------------------------------
-- ALTERADO ARQUIVO DA ACAO 2187 P/ LSExportacaoBancoBanPara.php
----------------------------------------------------------------

UPDATE administracao.acao SET nom_arquivo = 'LSExportacaoBancoBanPara.php' where cod_acao = 2187;
UPDATE administracao.acao SET nom_arquivo = 'LSExportacaoBancoBanPara.php' where cod_acao = 2188;


-------------------------------------------------------------------------------------------------------------------------------------------------------
-- ADICIONANDO COLUNAS timestamp E vigencia EM folhapagamento.configuracao_empenho E folhapagamento.configuracao_empenho_lla P/ MANUTENCAO DE HISTORICO
-------------------------------------------------------------------------------------------------------------------------------------------------------

SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_atributo_valor DROP CONSTRAINT fk_configuracao_empenho_atributo_valor_1;');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_atributo_valor DROP CONSTRAINT pk_configuracao_empenho_atributo_valor  ;');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_atributo       DROP CONSTRAINT fk_configuracao_empenho_atributo_1      ;');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_atributo       DROP CONSTRAINT pk_configuracao_empenho_atributo        ;');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_conta_despesa  DROP CONSTRAINT fk_configuracao_empenho_conta_despesa_1 ;');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_conta_despesa  DROP CONSTRAINT pk_configuracao_empenho_conta_despesa   ;');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_evento         DROP CONSTRAINT fk_configuracao_empenho_evento_1        ;');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_evento         DROP CONSTRAINT pk_configuracao_empenho_evento          ;');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_situacao       DROP CONSTRAINT fk_configuracao_empenho_situacao_1      ;');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_situacao       DROP CONSTRAINT pk_configuracao_empenho_situacao        ;');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_subdivisao     DROP CONSTRAINT fk_configuracao_empenho_subdivisao_1    ;');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_subdivisao     DROP CONSTRAINT pk_configuracao_empenho_subdivisao      ;');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_local          DROP CONSTRAINT fk_configuracao_empenho_local_1         ;');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_local          DROP CONSTRAINT pk_configuracao_empenho_local           ;');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_lotacao        DROP CONSTRAINT fk_configuracao_empenho_lotacao_1       ;');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_lotacao        DROP CONSTRAINT pk_configuracao_empenho_lotacao         ;');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho                DROP CONSTRAINT pk_configuracao_empenho                 ;');



SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho                ADD   COLUMN timestamp TIMESTAMP;');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho                ADD   COLUMN vigencia  DATE;     '); 
SELECT atualizarbanco('
UPDATE folhapagamento.configuracao_empenho
   SET timestamp = now()::timestamp(3)
     , vigencia  = CAST(exercicio || \'-01-01\' AS DATE)
     ;
');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho                ALTER COLUMN timestamp SET NOT NULL;');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho                ALTER COLUMN timestamp SET DEFAULT (\'now\'::text)::timestamp(3) WITH TIME ZONE;');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho                ALTER COLUMN vigencia  SET NOT NULL;');

SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_atributo       ADD   COLUMN timestamp TIMESTAMP;');
SELECT atualizarbanco('
UPDATE folhapagamento.configuracao_empenho_atributo
   SET timestamp = ( SELECT timestamp
                       FROM folhapagamento.configuracao_empenho
                      WHERE folhapagamento.configuracao_empenho.exercicio        = folhapagamento.configuracao_empenho_atributo.exercicio
                        AND folhapagamento.configuracao_empenho.cod_configuracao = folhapagamento.configuracao_empenho_atributo.cod_configuracao
                        AND folhapagamento.configuracao_empenho.sequencia        = folhapagamento.configuracao_empenho_atributo.sequencia
                   )
     ;
');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_atributo       ALTER COLUMN timestamp SET NOT NULL;');

SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_atributo_valor ADD   COLUMN timestamp TIMESTAMP;');
SELECT atualizarbanco('
UPDATE folhapagamento.configuracao_empenho_atributo_valor
   SET timestamp = ( SELECT timestamp
                       FROM folhapagamento.configuracao_empenho_atributo
                      WHERE folhapagamento.configuracao_empenho_atributo.exercicio        = folhapagamento.configuracao_empenho_atributo_valor.exercicio
                        AND folhapagamento.configuracao_empenho_atributo.cod_configuracao = folhapagamento.configuracao_empenho_atributo_valor.cod_configuracao
                        AND folhapagamento.configuracao_empenho_atributo.sequencia        = folhapagamento.configuracao_empenho_atributo_valor.sequencia
                        AND folhapagamento.configuracao_empenho_atributo.cod_atributo     = folhapagamento.configuracao_empenho_atributo_valor.cod_atributo
                        AND folhapagamento.configuracao_empenho_atributo.cod_cadastro     = folhapagamento.configuracao_empenho_atributo_valor.cod_cadastro
                        AND folhapagamento.configuracao_empenho_atributo.cod_modulo       = folhapagamento.configuracao_empenho_atributo_valor.cod_modulo
                   )
     ;
');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_atributo_valor ALTER COLUMN timestamp SET NOT NULL;');

SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_conta_despesa  ADD   COLUMN timestamp TIMESTAMP;');
SELECT atualizarbanco('
UPDATE folhapagamento.configuracao_empenho_conta_despesa
   SET timestamp = ( SELECT timestamp
                       FROM folhapagamento.configuracao_empenho
                      WHERE folhapagamento.configuracao_empenho.exercicio        = folhapagamento.configuracao_empenho_conta_despesa.exercicio
                        AND folhapagamento.configuracao_empenho.cod_configuracao = folhapagamento.configuracao_empenho_conta_despesa.cod_configuracao
                        AND folhapagamento.configuracao_empenho.sequencia        = folhapagamento.configuracao_empenho_conta_despesa.sequencia
                   )
     ;
');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_conta_despesa  ALTER COLUMN timestamp SET NOT NULL;');

SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_evento         ADD   COLUMN timestamp TIMESTAMP;');
SELECT atualizarbanco('
UPDATE folhapagamento.configuracao_empenho_evento
   SET timestamp = ( SELECT timestamp
                       FROM folhapagamento.configuracao_empenho
                      WHERE folhapagamento.configuracao_empenho.exercicio        = folhapagamento.configuracao_empenho_evento.exercicio
                        AND folhapagamento.configuracao_empenho.cod_configuracao = folhapagamento.configuracao_empenho_evento.cod_configuracao
                        AND folhapagamento.configuracao_empenho.sequencia        = folhapagamento.configuracao_empenho_evento.sequencia
                   )
     ;
');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_evento         ALTER COLUMN timestamp SET NOT NULL;');

SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_situacao       ADD   COLUMN timestamp TIMESTAMP;');
SELECT atualizarbanco('
UPDATE folhapagamento.configuracao_empenho_situacao
   SET timestamp = ( SELECT timestamp
                       FROM folhapagamento.configuracao_empenho
                      WHERE folhapagamento.configuracao_empenho.exercicio        = folhapagamento.configuracao_empenho_situacao.exercicio
                        AND folhapagamento.configuracao_empenho.cod_configuracao = folhapagamento.configuracao_empenho_situacao.cod_configuracao
                        AND folhapagamento.configuracao_empenho.sequencia        = folhapagamento.configuracao_empenho_situacao.sequencia
                   )
     ;
');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_situacao       ALTER COLUMN timestamp SET NOT NULL;');

SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_subdivisao     ADD   COLUMN timestamp TIMESTAMP;');
SELECT atualizarbanco('
UPDATE folhapagamento.configuracao_empenho_subdivisao
   SET timestamp = ( SELECT timestamp
                       FROM folhapagamento.configuracao_empenho
                      WHERE folhapagamento.configuracao_empenho.exercicio        = folhapagamento.configuracao_empenho_subdivisao.exercicio
                        AND folhapagamento.configuracao_empenho.cod_configuracao = folhapagamento.configuracao_empenho_subdivisao.cod_configuracao
                        AND folhapagamento.configuracao_empenho.sequencia        = folhapagamento.configuracao_empenho_subdivisao.sequencia
                   )
     ;
');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_subdivisao     ALTER COLUMN timestamp SET NOT NULL;');

SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_local          ADD   COLUMN timestamp TIMESTAMP;');
SELECT atualizarbanco('
UPDATE folhapagamento.configuracao_empenho_local
   SET timestamp = ( SELECT timestamp
                       FROM folhapagamento.configuracao_empenho
                      WHERE folhapagamento.configuracao_empenho.exercicio        = folhapagamento.configuracao_empenho_local.exercicio
                        AND folhapagamento.configuracao_empenho.cod_configuracao = folhapagamento.configuracao_empenho_local.cod_configuracao
                        AND folhapagamento.configuracao_empenho.sequencia        = folhapagamento.configuracao_empenho_local.sequencia
                   )
     ;
');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_local          ALTER COLUMN timestamp SET NOT NULL;');

SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_lotacao        ADD   COLUMN timestamp TIMESTAMP;');
SELECT atualizarbanco('
UPDATE folhapagamento.configuracao_empenho_lotacao
   SET timestamp = ( SELECT timestamp
                       FROM folhapagamento.configuracao_empenho
                      WHERE folhapagamento.configuracao_empenho.exercicio        = folhapagamento.configuracao_empenho_lotacao.exercicio
                        AND folhapagamento.configuracao_empenho.cod_configuracao = folhapagamento.configuracao_empenho_lotacao.cod_configuracao
                        AND folhapagamento.configuracao_empenho.sequencia        = folhapagamento.configuracao_empenho_lotacao.sequencia
                   )
     ;
');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_lotacao        ALTER COLUMN timestamp SET NOT NULL;');



SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho                ADD  CONSTRAINT pk_configuracao_empenho                  PRIMARY KEY                                             (exercicio, cod_configuracao, sequencia, timestamp);');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_atributo       ADD  CONSTRAINT pk_configuracao_empenho_atributo         PRIMARY KEY                                             (cod_cadastro, cod_modulo, cod_atributo, exercicio, cod_configuracao, sequencia, timestamp);');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_atributo       ADD  CONSTRAINT fk_configuracao_empenho_atributo_1       FOREIGN KEY                                             (exercicio, cod_configuracao, sequencia, timestamp)
                                                                                                                                               REFERENCES folhapagamento.configuracao_empenho          (exercicio, cod_configuracao, sequencia, timestamp);');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_atributo_valor ADD  CONSTRAINT pk_configuracao_empenho_atributo_valor   PRIMARY KEY                                             (exercicio, cod_configuracao, sequencia, timestamp, cod_atributo, cod_modulo, cod_cadastro, valor);');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_atributo_valor ADD  CONSTRAINT fk_configuracao_empenho_atributo_valor_1 FOREIGN KEY                                             (exercicio, cod_configuracao, sequencia, timestamp, cod_atributo, cod_modulo, cod_cadastro)
                                                                                                                                               REFERENCES folhapagamento.configuracao_empenho_atributo (exercicio, cod_configuracao, sequencia, timestamp, cod_atributo, cod_modulo, cod_cadastro);');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_conta_despesa  ADD  CONSTRAINT pk_configuracao_empenho_conta_despesa    PRIMARY KEY                                             (exercicio, cod_configuracao, sequencia, timestamp);');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_conta_despesa  ADD  CONSTRAINT fk_configuracao_empenho_conta_despesa_1  FOREIGN KEY                                             (exercicio, cod_configuracao, sequencia, timestamp)
                                                                                                                                               REFERENCES folhapagamento.configuracao_empenho          (exercicio, cod_configuracao, sequencia, timestamp);');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_evento         ADD  CONSTRAINT pk_configuracao_empenho_evento           PRIMARY KEY                                             (exercicio, cod_configuracao, sequencia, timestamp, cod_evento);');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_evento         ADD  CONSTRAINT fk_configuracao_empenho_evento_1         FOREIGN KEY                                             (exercicio, cod_configuracao, sequencia, timestamp)
                                                                                                                                               REFERENCES folhapagamento.configuracao_empenho          (exercicio, cod_configuracao, sequencia, timestamp);');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_situacao       ADD  CONSTRAINT pk_configuracao_empenho_situacao         PRIMARY KEY                                             (exercicio, cod_configuracao, sequencia, timestamp, situacao);');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_situacao       ADD  CONSTRAINT fk_configuracao_empenho_situacao_1       FOREIGN KEY                                             (exercicio, cod_configuracao, sequencia, timestamp)
                                                                                                                                               REFERENCES folhapagamento.configuracao_empenho          (exercicio, cod_configuracao, sequencia, timestamp);');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_subdivisao     ADD  CONSTRAINT pk_configuracao_empenho_subdivisao       PRIMARY KEY                                             (exercicio, cod_configuracao, sequencia, timestamp, cod_sub_divisao);');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_subdivisao     ADD  CONSTRAINT fk_configuracao_empenho_subdivisao_1     FOREIGN KEY                                             (exercicio, cod_configuracao, sequencia, timestamp)
                                                                                                                                               REFERENCES folhapagamento.configuracao_empenho          (exercicio, cod_configuracao, sequencia, timestamp);');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_local          ADD  CONSTRAINT pk_configuracao_empenho_local            PRIMARY KEY                                             (exercicio, cod_configuracao, sequencia, timestamp, cod_local);');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_local          ADD  CONSTRAINT fk_configuracao_empenho_local_1          FOREIGN KEY                                             (exercicio, cod_configuracao, sequencia, timestamp)
                                                                                                                                               REFERENCES folhapagamento.configuracao_empenho          (exercicio, cod_configuracao, sequencia, timestamp);');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_lotacao        ADD  CONSTRAINT pk_configuracao_empenho_lotacao          PRIMARY KEY                                             (exercicio, cod_configuracao, sequencia, timestamp, cod_orgao);');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_lotacao        ADD  CONSTRAINT fk_configuracao_empenho_lotacao_1        FOREIGN KEY                                             (exercicio, cod_configuracao, sequencia, timestamp)
                                                                                                                        REFERENCES folhapagamento.configuracao_empenho          (exercicio, cod_configuracao, sequencia, timestamp);');


SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_lla_local          DROP CONSTRAINT fk_configuracao_empenho_lla_local_3         ;');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_lla_local          DROP CONSTRAINT pk_configuracao_empenho_lla_local           ;');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_lla_atributo_valor DROP CONSTRAINT fk_configuracao_empenho_lla_atributo_valor_2;');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_lla_atributo_valor DROP CONSTRAINT pk_configuracao_empenho_lla_atributo_valor  ;');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_lla_atributo       DROP CONSTRAINT fk_configuracao_empenho_lla_atributo_2      ;');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_lla_atributo       DROP CONSTRAINT pk_configuracao_empenho_lla_atributo        ;');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_lla_lotacao        DROP CONSTRAINT fk_configuracao_empenho_lla_lotacao_3       ;');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_lla_lotacao        DROP CONSTRAINT pk_configuracao_empenho_lla_lotacao         ;');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_lla                DROP CONSTRAINT pk_configuracao_empenho_lla                 ;');


SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_lla                ADD   COLUMN timestamp TIMESTAMP;');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_lla                ADD   COLUMN vigencia  DATE;     ');
SELECT atualizarbanco('
UPDATE folhapagamento.configuracao_empenho_lla
   SET timestamp = now()::timestamp(3)
     , vigencia  = CAST(exercicio || \'-01-01\' AS DATE)
     ;
');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_lla                ALTER COLUMN timestamp SET NOT NULL;');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_lla                ALTER COLUMN timestamp SET DEFAULT (\'now\'::text)::timestamp(3) WITH TIME ZONE;');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_lla                ALTER COLUMN vigencia  SET NOT NULL;');

SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_lla_local          ADD   COLUMN timestamp TIMESTAMP;');
SELECT atualizarbanco('
UPDATE folhapagamento.configuracao_empenho_lla_local
   SET timestamp = ( SELECT timestamp
                       FROM folhapagamento.configuracao_empenho_lla 
                      WHERE folhapagamento.configuracao_empenho_lla.exercicio            = folhapagamento.configuracao_empenho_lla_local.exercicio
                        AND folhapagamento.configuracao_empenho_lla.cod_configuracao_lla = folhapagamento.configuracao_empenho_lla_local.cod_configuracao_lla
                   )
     ;
');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_lla_local          ALTER COLUMN timestamp SET NOT NULL;');

SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_lla_atributo       ADD   COLUMN timestamp TIMESTAMP;');
SELECT atualizarbanco('
UPDATE folhapagamento.configuracao_empenho_lla_atributo
   SET timestamp = ( SELECT timestamp
                       FROM folhapagamento.configuracao_empenho_lla 
                      WHERE folhapagamento.configuracao_empenho_lla.exercicio            = folhapagamento.configuracao_empenho_lla_atributo.exercicio
                        AND folhapagamento.configuracao_empenho_lla.cod_configuracao_lla = folhapagamento.configuracao_empenho_lla_atributo.cod_configuracao_lla
                   )
     ;
');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_lla_atributo       ALTER COLUMN timestamp SET NOT NULL;');

SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_lla_atributo_valor ADD   COLUMN timestamp TIMESTAMP;');
SELECT atualizarbanco('
UPDATE folhapagamento.configuracao_empenho_lla_atributo_valor
   SET timestamp = ( SELECT timestamp
                       FROM folhapagamento.configuracao_empenho_lla_atributo 
                      WHERE folhapagamento.configuracao_empenho_lla_atributo.exercicio            = folhapagamento.configuracao_empenho_lla_atributo_valor.exercicio
                        AND folhapagamento.configuracao_empenho_lla_atributo.cod_configuracao_lla = folhapagamento.configuracao_empenho_lla_atributo_valor.cod_configuracao_lla
                        AND folhapagamento.configuracao_empenho_lla_atributo.cod_atributo         = folhapagamento.configuracao_empenho_lla_atributo_valor.cod_atributo
                        AND folhapagamento.configuracao_empenho_lla_atributo.cod_modulo           = folhapagamento.configuracao_empenho_lla_atributo_valor.cod_modulo
                        AND folhapagamento.configuracao_empenho_lla_atributo.cod_cadastro         = folhapagamento.configuracao_empenho_lla_atributo_valor.cod_cadastro
                   )
     ;
');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_lla_atributo_valor ALTER COLUMN timestamp SET NOT NULL;');

SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_lla_lotacao        ADD   COLUMN timestamp TIMESTAMP;');
SELECT atualizarbanco('
UPDATE folhapagamento.configuracao_empenho_lla_lotacao
   SET timestamp = ( SELECT timestamp
                       FROM folhapagamento.configuracao_empenho_lla 
                      WHERE folhapagamento.configuracao_empenho_lla.exercicio            = folhapagamento.configuracao_empenho_lla_lotacao.exercicio
                        AND folhapagamento.configuracao_empenho_lla.cod_configuracao_lla = folhapagamento.configuracao_empenho_lla_lotacao.cod_configuracao_lla
                   )
     ;
');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_lla_lotacao        ALTER COLUMN timestamp SET NOT NULL;');


SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_lla                ADD  CONSTRAINT pk_configuracao_empenho_lla                      PRIMARY KEY                                                 (exercicio, cod_configuracao_lla, timestamp);');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_lla_local          ADD  CONSTRAINT pk_configuracao_empenho_lla_local                PRIMARY KEY                                                 (exercicio, cod_local, cod_configuracao_lla, timestamp);');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_lla_local          ADD  CONSTRAINT fk_configuracao_empenho_lla_local_3              FOREIGN KEY                                                 (exercicio, cod_configuracao_lla, timestamp)
                                                                                                                                                           REFERENCES folhapagamento.configuracao_empenho_lla          (exercicio, cod_configuracao_lla, timestamp);');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_lla_atributo       ADD  CONSTRAINT pk_configuracao_empenho_lla_atributo             PRIMARY KEY                                                 (cod_atributo, cod_modulo, cod_cadastro, exercicio, cod_configuracao_lla, timestamp);');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_lla_atributo       ADD  CONSTRAINT fk_configuracao_empenho_lla_atributo_2           FOREIGN KEY                                                 (exercicio, cod_configuracao_lla, timestamp)
                                                                                                                                                           REFERENCES folhapagamento.configuracao_empenho_lla          (exercicio, cod_configuracao_lla, timestamp);');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_lla_atributo_valor ADD  CONSTRAINT pk_configuracao_empenho_lla_atributo_valor       PRIMARY KEY                                                 (num_pao, exercicio, cod_configuracao_lla, timestamp, cod_cadastro, cod_modulo, cod_atributo, valor);');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_lla_atributo_valor ADD  CONSTRAINT fk_configuracao_empenho_lla_atributo_valor_2     FOREIGN KEY                                                 (cod_atributo, cod_modulo, cod_cadastro, exercicio, cod_configuracao_lla, timestamp)
                                                                                                                                                           REFERENCES folhapagamento.configuracao_empenho_lla_atributo (cod_atributo, cod_modulo, cod_cadastro, exercicio, cod_configuracao_lla, timestamp);');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_lla_lotacao        ADD  CONSTRAINT pk_configuracao_empenho_lla_lotacao              PRIMARY KEY                                                 (cod_orgao, exercicio, cod_configuracao_lla, timestamp);');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_empenho_lla_lotacao        ADD  CONSTRAINT fk_configuracao_empenho_lla_lotacao_3            FOREIGN KEY                                                 (exercicio, cod_configuracao_lla, timestamp)
                                                                                                                                                           REFERENCES folhapagamento.configuracao_empenho_lla          (exercicio, cod_configuracao_lla, timestamp);');



SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_autorizacao_empenho_historico   DROP CONSTRAINT fk_configuracao_autorizacao_empenho_historico_1;  ');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_autorizacao_empenho_historico   DROP CONSTRAINT pk_configuracao_autorizacao_empenho_historico;    ');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_autorizacao_empenho_descricao   DROP CONSTRAINT fk_configuracao_autorizacao_empenho_descricao_1;  ');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_autorizacao_empenho_descricao   DROP CONSTRAINT pk_configuracao_autorizacao_empenho_descricao;    ');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_autorizacao_empenho_complemento DROP CONSTRAINT fk_configuracao_autorizacao_empenho_complemento_1;');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_autorizacao_empenho_complemento DROP CONSTRAINT pk_configuracao_autorizacao_empenho_complemento;  ');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_autorizacao_empenho             DROP CONSTRAINT pk_configuracao_autorizacao_empenho;              ');

SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_autorizacao_empenho             ADD   COLUMN timestamp TIMESTAMP;');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_autorizacao_empenho             ADD   COLUMN vigencia  DATE;     ');
SELECT atualizarbanco('
UPDATE folhapagamento.configuracao_autorizacao_empenho
   SET timestamp = now()::timestamp(3)
     , vigencia  = CAST(exercicio || \'-01-01\' AS DATE)
     ;
');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_autorizacao_empenho             ALTER COLUMN timestamp SET NOT NULL;');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_autorizacao_empenho             ALTER COLUMN timestamp SET DEFAULT (\'now\'::text)::timestamp(3) WITH TIME ZONE;');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_autorizacao_empenho             ALTER COLUMN vigencia  SET NOT NULL;');


SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_autorizacao_empenho_historico   ADD   COLUMN timestamp TIMESTAMP;');
SELECT atualizarbanco('
UPDATE folhapagamento.configuracao_autorizacao_empenho_historico
   SET timestamp = ( SELECT timestamp
                       FROM folhapagamento.configuracao_autorizacao_empenho
                      WHERE folhapagamento.configuracao_autorizacao_empenho.exercicio                    = folhapagamento.configuracao_autorizacao_empenho_historico.exercicio
                        AND folhapagamento.configuracao_autorizacao_empenho.cod_configuracao_autorizacao = folhapagamento.configuracao_autorizacao_empenho_historico.cod_configuracao_autorizacao
                   )
     ;
');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_autorizacao_empenho_historico   ALTER COLUMN timestamp SET NOT NULL;');


SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_autorizacao_empenho_complemento ADD   COLUMN timestamp TIMESTAMP;');
SELECT atualizarbanco('
UPDATE folhapagamento.configuracao_autorizacao_empenho_complemento
   SET timestamp = ( SELECT timestamp
                       FROM folhapagamento.configuracao_autorizacao_empenho
                      WHERE folhapagamento.configuracao_autorizacao_empenho.exercicio                    = folhapagamento.configuracao_autorizacao_empenho_complemento.exercicio
                        AND folhapagamento.configuracao_autorizacao_empenho.cod_configuracao_autorizacao = folhapagamento.configuracao_autorizacao_empenho_complemento.cod_configuracao_autorizacao
                   )
     ;
');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_autorizacao_empenho_complemento ALTER COLUMN timestamp SET NOT NULL;');


SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_autorizacao_empenho_descricao   ADD   COLUMN timestamp TIMESTAMP;');
SELECT atualizarbanco('
UPDATE folhapagamento.configuracao_autorizacao_empenho_descricao
   SET timestamp = ( SELECT timestamp
                       FROM folhapagamento.configuracao_autorizacao_empenho
                      WHERE folhapagamento.configuracao_autorizacao_empenho.exercicio                    = folhapagamento.configuracao_autorizacao_empenho_descricao.exercicio
                        AND folhapagamento.configuracao_autorizacao_empenho.cod_configuracao_autorizacao = folhapagamento.configuracao_autorizacao_empenho_descricao.cod_configuracao_autorizacao
                   )
     ;
');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_autorizacao_empenho_descricao   ALTER COLUMN timestamp SET NOT NULL;');


SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_autorizacao_empenho             ADD  CONSTRAINT pk_configuracao_autorizacao_empenho                 PRIMARY KEY                                                 (cod_configuracao_autorizacao, exercicio, timestamp);');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_autorizacao_empenho_complemento ADD  CONSTRAINT pk_configuracao_autorizacao_empenho_complemento     PRIMARY KEY                                                 (cod_configuracao_autorizacao, exercicio, timestamp);');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_autorizacao_empenho_complemento ADD  CONSTRAINT fk_configuracao_autorizacao_empenho_complemento_1   FOREIGN KEY                                                 (cod_configuracao_autorizacao, exercicio, timestamp)
                                                                                                                                                                   REFERENCES folhapagamento.configuracao_autorizacao_empenho  (cod_configuracao_autorizacao, exercicio, timestamp);
');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_autorizacao_empenho_descricao   ADD  CONSTRAINT pk_configuracao_autorizacao_empenho_descricao       PRIMARY KEY                                                 (cod_configuracao_autorizacao, exercicio, timestamp);');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_autorizacao_empenho_descricao   ADD  CONSTRAINT fk_configuracao_autorizacao_empenho_descricao_1     FOREIGN KEY                                                 (cod_configuracao_autorizacao, exercicio, timestamp)
                                                                                                                                                                   REFERENCES folhapagamento.configuracao_autorizacao_empenho  (cod_configuracao_autorizacao, exercicio, timestamp);
');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_autorizacao_empenho_historico   ADD  CONSTRAINT pk_configuracao_autorizacao_empenho_historico       PRIMARY KEY                                                 (cod_configuracao_autorizacao, exercicio, timestamp);');
SELECT atualizarbanco('ALTER TABLE folhapagamento.configuracao_autorizacao_empenho_historico   ADD  CONSTRAINT fk_configuracao_autorizacao_empenho_historico_1     FOREIGN KEY                                                 (cod_configuracao_autorizacao, exercicio, timestamp)
                                                                                                                                                                   REFERENCES folhapagamento.configuracao_autorizacao_empenho  (cod_configuracao_autorizacao, exercicio, timestamp);
');


----------------------------------------------------------------
-- REMOVENDO COLUNA vigencia DE ima.configuracao_banpara_empresa
----------------------------------------------------------------

SELECT atualizarbanco('ALTER TABLE ima.configuracao_banpara_empresa DROP COLUMN vigencia;');


------------------------------------------------------------------------
-- ALTERANDO E INCLUINDO ACOES P/ CONFIGURACAO DE AUTORIZACAO DE EMPENHO
------------------------------------------------------------------------

UPDATE administracao.acao
   SET ordem = ordem + 2
 WHERE cod_funcionalidade = 240
   AND ordem > 9
     ;

UPDATE administracao.acao
   SET nom_acao  = 'Incluir Configuração de Autorização de Empenho'
     , parametro = 'incluir'
 WHERE cod_acao  = 1816
     ;

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2768
          , 240
          , 'LSManterAutorizacaoEmpenho.php'
          , 'alterar'
          , 10
          , ''
          , 'Alterar Configuração de Autorização de Empenho'
          );
INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2769
          , 240
          , 'LSManterAutorizacaoEmpenho.php'
          , 'excluir'
          , 11
          , ''
          , 'Excluir Configuração de Autorização de Empenho'
          );

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE
    stSQL       VARCHAR;
    reRecord    RECORD;
BEGIN

    stSQL := '
                SELECT numcgm
                     , ano_exercicio
                  FROM administracao.permissao
                 WHERE cod_acao = 1816
                     ;
             ';

    FOR reRecord IN EXECUTE stSQL LOOP
        INSERT INTO administracao.permissao (numcgm, cod_acao, ano_exercicio) VALUES (reRecord.numcgm, 2768, reRecord.ano_exercicio);
        INSERT INTO administracao.permissao (numcgm, cod_acao, ano_exercicio) VALUES (reRecord.numcgm, 2769, reRecord.ano_exercicio);
    END LOOP;

END;
$$ LANGUAGE 'plpgsql';
SELECT        manutencao();
DROP FUNCTION manutencao();

