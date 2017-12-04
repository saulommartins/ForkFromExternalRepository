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
* $Id: GF_1931.sql 59612 2014-09-02 12:00:51Z gelson $
*
* Versão 1.93.1
*/

----------------
-- Ticket #13839
----------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2390
          , 162
          , 'FLContaRecurso.php'
          , 'incluir'
          , 4
          , ''
          , 'Criar Contas Contábeis'
          );
          
----------------
-- Ticket #11669
----------------

    INSERT INTO administracao.funcionalidade VALUES (446,30,'Emissão e Controle de Cheques','instancias/cheques/',99);

    INSERT INTO administracao.acao VALUES (2393,446,'FMManterCheque.php'      ,'incluir'  ,1,'','Incluir Cheque'  );
    INSERT INTO administracao.acao VALUES (2394,446,'FLManterCheque.php'      ,'excluir'  ,3,'','Excluir Cheque'  );
    INSERT INTO administracao.acao VALUES (2395,446,'FLManterCheque.php'      ,'consultar',4,'','Consultar Cheque');
    INSERT INTO administracao.acao VALUES (2396,446,'FMManterTalaoCheque.php' ,'incluir'  ,5,'','Incluir Talão'   );
    INSERT INTO administracao.acao VALUES (2397,446,'FLManterEmitirCheque.php','emitir'   ,6,'','Emitir Cheque'   );
    INSERT INTO administracao.acao VALUES (2398,446,'FLManterAnularEmissao.php','anular'  ,7,'','Anular Emissão'  );
    INSERT INTO administracao.acao VALUES (2399,446,'FLManterChequeEmissaoBaixa.php','baixar' ,8,'','Baixa de Transferência');
    INSERT INTO administracao.acao VALUES (2400,446,'FLManterChequeEmissaoBaixa.php','anular' ,9,'','Anular Baixa de Transferência');
    
    
    INSERT INTO administracao.acao VALUES (2401,266,'FMVincularImpressoraCheque.php','incluir' ,10,'','Vincular Impressora Cheques Terminal');

     

    CREATE TABLE tesouraria.cheque (
        cod_agencia         INTEGER     NOT NULL,
        cod_banco           INTEGER     NOT NULL,
        cod_conta_corrente  INTEGER     NOT NULL,
        num_cheque          VARCHAR(15) NOT NULL,
        data_entrada        DATE        NOT NULL DEFAULT ('NOW'::TEXT)::DATE,

        CONSTRAINT pk_cheque PRIMARY KEY(cod_agencia,cod_banco,cod_conta_corrente,num_cheque),
        CONSTRAINT fk_cheque_1 FOREIGN KEY(cod_agencia,cod_banco,cod_conta_corrente) REFERENCES monetario.conta_corrente(cod_agencia,cod_banco,cod_conta_corrente)
    );

    CREATE TABLE tesouraria.cheque_emissao (
        cod_agencia         INTEGER         NOT NULL,
        cod_banco           INTEGER         NOT NULL,
        cod_conta_corrente  INTEGER         NOT NULL,
        num_cheque          VARCHAR(15)     NOT NULL,   
        timestamp_emissao   TIMESTAMP       NOT NULL DEFAULT ('NOW'::TEXT)::TIMESTAMP(3) WITH TIME ZONE,
        data_emissao        DATE            NOT NULL DEFAULT ('NOW'::TEXT)::DATE,
        valor               NUMERIC(14,2)   NOT NULL,
        descricao           TEXT            NULL,

        CONSTRAINT pk_cheque_emissao PRIMARY KEY(cod_agencia,cod_banco,cod_conta_corrente,num_cheque,timestamp_emissao),
        CONSTRAINT fk_cheque_emissao FOREIGN KEY(cod_agencia,cod_banco,cod_conta_corrente,num_cheque) REFERENCES tesouraria.cheque(cod_agencia,cod_banco,cod_conta_corrente,num_cheque)
     );

    CREATE TABLE tesouraria.cheque_emissao_ordem_pagamento (
        cod_ordem           INTEGER     NOT NULL,
        exercicio           CHAR(4)     NOT NULL,
        cod_entidade        INTEGER     NOT NULL,
        cod_agencia         INTEGER     NOT NULL,
        cod_banco           INTEGER     NOT NULL,
        cod_conta_corrente  INTEGER     NOT NULL,
        num_cheque          VARCHAR(15) NOT NULL,   
        timestamp_emissao   TIMESTAMP   NOT NULL DEFAULT ('NOW'::TEXT)::TIMESTAMP(3) WITH TIME ZONE,
        
        CONSTRAINT pk_cheque_emissao_ordem_pagamento PRIMARY KEY(cod_ordem,exercicio,cod_entidade,cod_agencia,cod_banco,cod_conta_corrente,num_cheque,timestamp_emissao),
        CONSTRAINT fk_cheque_emissao_ordem_pagamento_1 FOREIGN KEY(cod_ordem,exercicio,cod_entidade) REFERENCES empenho.ordem_pagamento(cod_ordem,exercicio,cod_entidade),
        CONSTRAINT fk_cheque_emissao_ordem_pagamento_2 FOREIGN KEY(cod_agencia,cod_banco,cod_conta_corrente,num_cheque,timestamp_emissao) REFERENCES tesouraria.cheque_emissao(cod_agencia,cod_banco,cod_conta_corrente,num_cheque,timestamp_emissao)
    );

    CREATE TABLE tesouraria.cheque_emissao_transferencia (
        cod_lote            INTEGER     NOT NULL,
        cod_entidade        INTEGER     NOT NULL,
        exercicio           CHAR(4)     NOT NULL,
        tipo                CHAR(1)     NOT NULL,
        cod_banco           INTEGER     NOT NULL,
        cod_agencia         INTEGER     NOT NULL,
        cod_conta_corrente  INTEGER     NOT NULL,
        num_cheque          VARCHAR(15) NOT NULL,
        timestamp_emissao   TIMESTAMP   NOT NULL DEFAULT ('NOW'::TEXT)::TIMESTAMP(3) WITH TIME ZONE,

        CONSTRAINT pk_cheque_emissao_transferencia PRIMARY KEY(cod_lote, cod_entidade, exercicio, tipo, cod_banco, cod_agencia, cod_conta_corrente, num_cheque,timestamp_emissao),
        CONSTRAINT fk_cheque_emissao_transferencia_1 FOREIGN KEY(cod_lote, cod_entidade, exercicio, tipo) REFERENCES tesouraria.transferencia(cod_lote, cod_entidade, exercicio, tipo),
        CONSTRAINT fk_cheque_emissao_transferencia_2 FOREIGN KEY(cod_agencia,cod_banco,cod_conta_corrente,num_cheque,timestamp_emissao) REFERENCES tesouraria.cheque_emissao(cod_agencia,cod_banco,cod_conta_corrente,num_cheque,timestamp_emissao)
    );

    CREATE TABLE tesouraria.cheque_emissao_recibo_extra (
        cod_entidade        INTEGER     NOT NULL,
        exercicio           CHAR(4)     NOT NULL,
        cod_recibo_extra    INTEGER     NOT NULL,
        tipo_recibo         CHAR(1)     NOT NULL,
        cod_banco           INTEGER     NOT NULL,
        cod_agencia         INTEGER     NOT NULL,
        cod_conta_corrente  INTEGER     NOT NULL,
        num_cheque          VARCHAR(15) NOT NULL,
        timestamp_emissao   TIMESTAMP   NOT NULL DEFAULT ('NOW'::TEXT)::TIMESTAMP(3) WITH TIME ZONE,

        CONSTRAINT pk_cheque_emissao_recibo_extra PRIMARY KEY(cod_entidade, exercicio, cod_recibo_extra, tipo_recibo, cod_banco, cod_agencia, cod_conta_corrente, num_cheque,timestamp_emissao),
        CONSTRAINT fk_cheque_emissao_recibo_extra_1 FOREIGN KEY(cod_recibo_extra, cod_entidade, exercicio, tipo_recibo) REFERENCES tesouraria.recibo_extra(cod_recibo_extra, cod_entidade, exercicio, tipo_recibo),
        CONSTRAINT fk_cheque_emissao_recibo_extra_2 FOREIGN KEY(cod_agencia,cod_banco,cod_conta_corrente,num_cheque,timestamp_emissao) REFERENCES tesouraria.cheque_emissao(cod_agencia,cod_banco,cod_conta_corrente,num_cheque,timestamp_emissao)
    );

    CREATE TABLE tesouraria.cheque_emissao_anulada (
        cod_agencia         INTEGER         NOT NULL,
        cod_banco           INTEGER         NOT NULL,
        cod_conta_corrente  INTEGER         NOT NULL,
        num_cheque          VARCHAR(15)     NOT NULL,   
        timestamp_emissao   TIMESTAMP       NOT NULL, 
        data_anulacao       DATE            NOT NULL DEFAULT ('NOW'::TEXT)::DATE,

        CONSTRAINT pk_cheque_emissao_anulada PRIMARY KEY(cod_agencia,cod_banco,cod_conta_corrente,num_cheque,timestamp_emissao),
        CONSTRAINT fk_cheque_emissao_anulada FOREIGN KEY(cod_agencia,cod_banco,cod_conta_corrente,num_cheque) REFERENCES tesouraria.cheque(cod_agencia,cod_banco,cod_conta_corrente,num_cheque)
     );

    CREATE TABLE tesouraria.cheque_emissao_baixa (
        cod_agencia         INTEGER         NOT NULL,
        cod_banco           INTEGER         NOT NULL,
        cod_conta_corrente  INTEGER         NOT NULL,
        num_cheque          VARCHAR(15)     NOT NULL,   
        timestamp_emissao   TIMESTAMP       NOT NULL, 
        timestamp_baixa     TIMESTAMP       NOT NULL DEFAULT ('NOW'::TEXT)::TIMESTAMP(3) WITH TIME ZONE,

        CONSTRAINT pk_cheque_emissao_baixa PRIMARY KEY(cod_agencia,cod_banco,cod_conta_corrente,num_cheque,timestamp_emissao,timestamp_baixa),
        CONSTRAINT fk_cheque_emissao_baixa FOREIGN KEY(cod_agencia,cod_banco,cod_conta_corrente,num_cheque) REFERENCES tesouraria.cheque(cod_agencia,cod_banco,cod_conta_corrente,num_cheque)
    );

    CREATE TABLE tesouraria.cheque_emissao_baixa_anulada (
        cod_agencia         INTEGER         NOT NULL,
        cod_banco           INTEGER         NOT NULL,
        cod_conta_corrente  INTEGER         NOT NULL,
        num_cheque          VARCHAR(15)     NOT NULL,   
        timestamp_emissao   TIMESTAMP       NOT NULL, 
        timestamp_baixa     TIMESTAMP       NOT NULL, 
        data_anulacao       DATE            NOT NULL DEFAULT ('NOW'::TEXT)::DATE,

        CONSTRAINT pk_cheque_emissao_baixa_anulada PRIMARY KEY(cod_agencia,cod_banco,cod_conta_corrente,num_cheque,timestamp_emissao,timestamp_baixa),
        CONSTRAINT fk_cheque_emissao_baixa_anulada FOREIGN KEY(cod_agencia,cod_banco,cod_conta_corrente,num_cheque) REFERENCES tesouraria.cheque(cod_agencia,cod_banco,cod_conta_corrente,num_cheque)
    );


    CREATE TABLE tesouraria.banco_cheque_layout (
        cod_banco INTEGER NOT NULL,
        col_valor_numerico INTEGER NOT NULL,
        col_extenso_1 INTEGER NOT NULL,
        col_extenso_2 INTEGER NOT NULL,
        col_favorecido INTEGER NOT NULL,
        col_cidade INTEGER NOT NULL,
        col_dia INTEGER NOT NULL,
        col_mes INTEGER NOT NULL,
        col_ano INTEGER NOT NULL,
        lin_valor_numerico INTEGER NOT NULL,
        lin_extenso_1 INTEGER NOT NULL,
        lin_extenso_2 INTEGER NOT NULL,
        lin_favorecido INTEGER NOT NULL,
        lin_cidade_data INTEGER NOT NULL,

        CONSTRAINT pk_banco_cheque_layout PRIMARY KEY(cod_banco),
        CONSTRAINT fk_banco_cheque_layout_1 FOREIGN KEY(cod_banco) REFERENCES monetario.banco(cod_banco)
        
    );

    CREATE TABLE tesouraria.cheque_impressora_terminal (
        cod_terminal        INTEGER     NOT NULL,
        timestamp_terminal  TIMESTAMP   NOT NULL,
        cod_impressora      INTEGER     NOT NULL,
         
        CONSTRAINT pk_cheque_impressora_terminal    PRIMARY KEY(cod_terminal,timestamp_terminal,cod_impressora),
        CONSTRAINT fk_cheque_impressora_terminal_1  FOREIGN KEY(cod_terminal,timestamp_terminal) REFERENCES tesouraria.terminal(cod_terminal,timestamp_terminal),
        CONSTRAINT fk_cheque_impressora_terminal_2  FOREIGN KEY(cod_impressora) REFERENCES administracao.impressora(cod_impressora)
    );

    GRANT INSERT, UPDATE, SELECT, DELETE ON tesouraria.cheque                         TO GROUP urbem;
    GRANT INSERT, UPDATE, SELECT, DELETE ON tesouraria.cheque_emissao                 TO GROUP urbem;
    GRANT INSERT, UPDATE, SELECT, DELETE ON tesouraria.cheque_emissao_ordem_pagamento TO GROUP urbem;
    GRANT INSERT, UPDATE, SELECT, DELETE ON tesouraria.cheque_emissao_transferencia   TO GROUP urbem;
    GRANT INSERT, UPDATE, SELECT, DELETE ON tesouraria.cheque_emissao_recibo_extra    TO GROUP urbem;
    GRANT INSERT, UPDATE, SELECT, DELETE ON tesouraria.cheque_emissao_anulada         TO GROUP urbem;
    GRANT INSERT, UPDATE, SELECT, DELETE ON tesouraria.cheque_emissao_baixa           TO GROUP urbem;
    GRANT INSERT, UPDATE, SELECT, DELETE ON tesouraria.cheque_emissao_baixa_anulada   TO GROUP urbem;
    GRANT INSERT, UPDATE, SELECT, DELETE ON tesouraria.banco_cheque_layout            TO GROUP urbem;
    GRANT INSERT, UPDATE, SELECT, DELETE ON tesouraria.cheque_impressora_terminal     TO GROUP urbem;

INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '001'),92,20,6,8,45,64,69,90,2,4,6,9,11);
INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '041'),81,17,7,8,49,75,81,92,2,4,7,9,11);

--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '003'),90,21,9,11,62,23,28,56,1,4,6,8,11);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '004'),93,22,9,11,62,21,28,56,2,5,8,10,12);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '006'),97,23,8,11,62,14,23,56,2,5,7,9,12);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '008'),97,30,10,13,62,21,30,55,3,5,8,10,12);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '021'),93,25,8,10,62,20,28,55,2,6,8,10,12);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '022'),93,20,8,10,62,19,25,56,2,5,7,9,12);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '024'),92,20,9,11,62,19,27,56,1,4,6,8,11);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '027'),93,25,10,12,62,16,30,55,1,4,7,9,11);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '028'),99,19,9,11,62,20,28,55,1,4,7,9,11);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '029'),96,25,8,10,62,23,30,55,1,5,7,9,12);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '031'),96,26,10,10,62,14,24,53,1,4,7,9,11);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '032'),97,27,9,10,62,14,22,55,2,4,6,8,11);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '033'),89,30,9,12,62,17,25,55,2,5,7,10,12);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '034'),90,27,10,10,62,16,32,55,1,4,6,8,10);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '035'),100,20,9,12,62,23,31,56,2,5,7,9,12);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '036'),99,24,9,11,62,21,28,56,2,5,7,9,11);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '037'),99,21,8,11,62,22,29,56,2,5,7,9,11);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '038'),97,23,8,10,62,22,31,56,2,6,9,11,13);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '039'),90,37,7,10,62,16,31,54,1,4,6,8,10);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '047'),93,21,10,11,62,18,25,56,1,4,6,9,11);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '048'),95,25,8,10,62,11,24,52,2,4,7,9,12);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '059'),91,28,9,11,62,26,34,56,1,4,6,8,10);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '070'),95,18,8,11,62,19,27,56,2,5,7,9,11);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '104'),96,35,8,10,62,24,33,56,2,5,7,9,12);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '106'),93,25,9,11,62,23,30,55,2,6,8,10,12);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '151'),95,19,8,10,62,18,27,55,1,4,6,9,11);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '153'),92,22,9,11,62,22,30,56,1,4,7,9,12);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '168'),94,18,8,11,62,25,32,55,2,5,7,10,12);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '200'),93,19,8,11,62,18,27,55,1,4,6,9,11);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '201'),93,24,8,10,62,18,26,55,1,4,6,8,10);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '206'),97,27,8,12,62,24,31,56,1,5,7,9,12);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '207'),91,17,8,11,62,19,27,55,2,5,7,10,12);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '211'),89,24,8,11,62,23,31,55,3,6,8,11,13);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '215'),96,19,9,11,62,22,29,55,2,4,7,9,12);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '220'),97,22,9,11,62,20,28,55,2,4,7,9,11);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '230'),91,25,9,11,62,25,33,55,2,4,7,9,12);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '231'),93,25,8,11,62,24,33,56,2,5,7,9,11);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '237'),93,20,10,10,62,16,24,55,2,5,8,10,13);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '244'),89,27,9,10,62,20,28,55,3,5,8,10,12);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '254'),92,22,10,11,62,24,31,55,1,4,7,10,13);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '275'),92,20,8,10,62,17,27,52,3,7,9,11,13);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '282'),97,25,9,11,62,21,29,55,2,5,7,9,12);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '291'),93,25,8,11,62,18,24,55,2,5,7,9,11);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '294'),91,18,8,11,62,25,31,55,2,4,6,9,11);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '308'),98,24,10,12,62,18,25,56,2,5,7,9,11);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '320'),95,19,8,10,62,19,26,56,2,4,7,9,12);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '341'),95,21,9,11,62,21,32,56,2,5,8,11,14);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '346'),95,25,9,11,62,25,32,55,2,4,7,9,11);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '347'),94,28,8,10,62,18,26,56,2,5,8,10,13);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '351'),93,27,9,11,62,26,33,56,1,4,6,9,11);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '353'),93,20,9,11,62,24,33,55,2,4,6,9,11);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '356'),93,24,9,10,62,16,24,55,1,4,6,9,11);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '369'),88,20,8,11,62,22,30,55,2,5,7,9,11);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '370'),93,19,9,11,62,18,25,55,1,4,6,9,11);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '372'),92,20,9,10,62,17,24,55,2,5,7,10,12);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '376'),95,20,9,10,62,25,33,56,2,5,7,9,11);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '388'),87,22,9,12,62,19,27,56,2,5,8,10,13);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '389'),93,19,10,11,62,24,33,56,2,6,8,11,13);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '392'),90,25,9,11,62,25,33,56,2,4,6,10,12);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '394'),92,18,9,11,62,22,30,55,1,4,6,8,12);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '399'),95,25,8,10,62,23,32,56,1,4,6,9,11);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '409'),93,25,9,12,62,21,30,55,2,5,7,10,12);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '420'),95,21,9,10,62,21,29,56,2,5,7,9,12);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '422'),99,30,12,13,62,23,33,56,3,5,7,10,12);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '424'),97,25,8,10,62,13,24,55,1,5,8,10,12);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '434'),97,21,9,11,62,21,29,56,2,5,8,10,12);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '453'),95,25,9,11,62,22,31,56,3,6,9,11,13);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '456'),89,24,10,11,62,18,25,55,2,5,7,9,11);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '464'),92,29,9,11,62,27,33,56,2,5,8,10,12);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '472'),94,25,9,11,62,21,28,55,2,5,8,9,13);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '477'),96,21,8,11,62,23,32,56,3,6,8,10,13);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '479'),94,20,8,11,62,21,28,55,2,5,7,9,11);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '483'),93,21,9,11,62,18,25,55,2,4,6,8,10);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '487'),99,30,9,11,62,19,27,56,2,5,7,10,12);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '494'),92,22,9,11,62,21,28,55,2,5,7,9,12);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '601'),1,1,1,1,62,32,36,60,1,2,3,4,5);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '602'),97,23,8,9,62,18,27,50,2,4,6,9,12);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '603'),93,18,6,8,62,22,31,56,3,7,9,12,16);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '607'),92,22,8,11,62,24,31,56,2,4,7,9,11);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '610'),96,28,8,11,62,24,33,55,1,5,7,9,11);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '630'),90,18,8,11,62,18,27,55,1,5,7,9,12);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '718'),92,20,9,11,62,19,28,55,1,5,7,9,12);
--INSERT INTO tesouraria.banco_cheque_layout VALUES ((SELECT cod_banco FROM monetario.banco WHERE num_banco = '999'),86,23,08,09,76,01,05,09,18,03,05,08,08);          
