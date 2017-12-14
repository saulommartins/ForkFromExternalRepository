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
*
* Script de DDL e DML
*
* Versao 2.03.0
*
* Fabio Bertoldi - 20140827
*
*/

----------------
-- Ticket #21958
----------------

SELECT atualizarbanco('ALTER TABLE folhapagamento.registro_evento_parcela ADD COLUMN mes_carencia INTEGER NOT NULL DEFAULT 0;');


----------------
-- Ticket #22018
----------------

SELECT atualizarbanco('
CREATE TABLE pessoal.causa_afastamento_mte (
    cod_causa_afastamento VARCHAR(10)           NOT NULL,
    nom_causa_afastamento VARCHAR(120)          NOT NULL,
    CONSTRAINT pk_pessoal_causa_afastamento_mte PRIMARY KEY (cod_causa_afastamento)
);
');

SELECT atualizarbanco('GRANT ALL ON TABLE pessoal.causa_afastamento_mte TO urbem;');

SELECT atualizarbanco('INSERT INTO pessoal.causa_afastamento_mte (cod_causa_afastamento, nom_causa_afastamento) VALUES (''SJ2'',''Despedida sem justa causa, pelo empregador''                                                                       );');
SELECT atualizarbanco('INSERT INTO pessoal.causa_afastamento_mte (cod_causa_afastamento, nom_causa_afastamento) VALUES (''JC2'',''Despedida por justa causa, pelo empregador''                                                                       );');
SELECT atualizarbanco('INSERT INTO pessoal.causa_afastamento_mte (cod_causa_afastamento, nom_causa_afastamento) VALUES (''RA2'',''Rescisão antecipada, pelo empregador, do contrato de trabalho por prazo determinado''                              );');
SELECT atualizarbanco('INSERT INTO pessoal.causa_afastamento_mte (cod_causa_afastamento, nom_causa_afastamento) VALUES (''FE2'',''Rescisão do contrato de trabalho por falecimento do empregador individual sem continuação da atividade da empresa'');');
SELECT atualizarbanco('INSERT INTO pessoal.causa_afastamento_mte (cod_causa_afastamento, nom_causa_afastamento) VALUES (''FE1'',''Rescisão do contrato de trabalho por falecimento do empregador individual por opção do empregado''                 );');
SELECT atualizarbanco('INSERT INTO pessoal.causa_afastamento_mte (cod_causa_afastamento, nom_causa_afastamento) VALUES (''RA1'',''Rescisão antecipada, pelo empregado, do contrato de trabalho por prazo determinado''                               );');
SELECT atualizarbanco('INSERT INTO pessoal.causa_afastamento_mte (cod_causa_afastamento, nom_causa_afastamento) VALUES (''SJ1'',''Rescisão contratual a pedido do empregado''                                                                        );');
SELECT atualizarbanco('INSERT INTO pessoal.causa_afastamento_mte (cod_causa_afastamento, nom_causa_afastamento) VALUES (''FT1'',''Rescisão do contrato de trabalho por falecimento do empregado''                                                    );');
SELECT atualizarbanco('INSERT INTO pessoal.causa_afastamento_mte (cod_causa_afastamento, nom_causa_afastamento) VALUES (''PD0'',''Extinção normal do contrato de trabalho por prazo determinado''                                                    );');
SELECT atualizarbanco('INSERT INTO pessoal.causa_afastamento_mte (cod_causa_afastamento, nom_causa_afastamento) VALUES (''RI2'',''Rescisão Indireta''                                                                                                );');
SELECT atualizarbanco('INSERT INTO pessoal.causa_afastamento_mte (cod_causa_afastamento, nom_causa_afastamento) VALUES (''CR0'',''Rescisão por culpa recíproca''                                                                                     );');
SELECT atualizarbanco('INSERT INTO pessoal.causa_afastamento_mte (cod_causa_afastamento, nom_causa_afastamento) VALUES (''FM0'',''Rescisão por força maior''                                                                                         );');
SELECT atualizarbanco('INSERT INTO pessoal.causa_afastamento_mte (cod_causa_afastamento, nom_causa_afastamento) VALUES (''NC0'',''Rescisão por nulidade do contrato de trabalho, declarada em decisão judicial''                                     );');


SELECT atualizarbanco('ALTER TABLE pessoal.causa_rescisao ADD COLUMN cod_causa_afastamento VARCHAR(10);');

SELECT atualizarbanco('UPDATE pessoal.causa_rescisao SET cod_causa_afastamento = ''JC2'' WHERE cod_causa_rescisao IN (1);');
SELECT atualizarbanco('UPDATE pessoal.causa_rescisao SET cod_causa_afastamento = ''SJ2'' WHERE cod_causa_rescisao IN (2,25);');
SELECT atualizarbanco('UPDATE pessoal.causa_rescisao SET cod_causa_afastamento = ''PD0'' WHERE cod_causa_rescisao IN (3);');
SELECT atualizarbanco('UPDATE pessoal.causa_rescisao SET cod_causa_afastamento = ''RI2'' WHERE cod_causa_rescisao IN (4);');
SELECT atualizarbanco('UPDATE pessoal.causa_rescisao SET cod_causa_afastamento = ''SJ1'' WHERE cod_causa_rescisao IN (5,6,7,23);');
SELECT atualizarbanco('UPDATE pessoal.causa_rescisao SET cod_causa_afastamento = ''FM0'' WHERE cod_causa_rescisao IN (8,9,13,14,15,16,17,18,19,20,21,24,26,27,28);');
SELECT atualizarbanco('UPDATE pessoal.causa_rescisao SET cod_causa_afastamento = ''FT1'' WHERE cod_causa_rescisao IN (10,11,12);');
SELECT atualizarbanco('UPDATE pessoal.causa_rescisao SET cod_causa_afastamento = ''NC0'' WHERE cod_causa_rescisao IN (22);');

SELECT atualizarbanco('ALTER TABLE pessoal.causa_rescisao ALTER COLUMN cod_causa_afastamento SET NOT NULL;');
SELECT atualizarbanco('
ALTER TABLE pessoal.causa_rescisao ADD CONSTRAINT fk_causa_afastamento_mte FOREIGN KEY                              (cod_causa_afastamento)
                                                                           REFERENCES pessoal.causa_afastamento_mte (cod_causa_afastamento);
');

INSERT INTO administracao.tabelas_rh VALUES (1, 'causa_afastamento_mte', 1);

----------------
-- Ticket #21889
----------------

DELETE FROM administracao.tabelas_rh WHERE schema_cod = 1 AND nome_tabela = 'arquivo_cargos'                       ;
DELETE FROM administracao.tabelas_rh WHERE schema_cod = 1 AND nome_tabela = 'conselho'                             ;
DELETE FROM administracao.tabelas_rh WHERE schema_cod = 1 AND nome_tabela = 'contrato_servidor_historico_funcional';
DELETE FROM administracao.tabelas_rh WHERE schema_cod = 1 AND nome_tabela = 'de_para_tipo_cargo'                   ;
DELETE FROM administracao.tabelas_rh WHERE schema_cod = 1 AND nome_tabela = 'de_para_tipo_regime_trabalho'         ;
DELETE FROM administracao.tabelas_rh WHERE schema_cod = 1 AND nome_tabela = 'de_para_tipo_regime_trabalho'         ;
DELETE FROM administracao.tabelas_rh WHERE schema_cod = 5 AND nome_tabela = 'beneficiario'                         ;
DELETE FROM administracao.tabelas_rh WHERE schema_cod = 5 AND nome_tabela = 'beneficiario_lancamento'              ;
DELETE FROM administracao.tabelas_rh WHERE schema_cod = 6 AND nome_tabela = 'calendario_cadastro'                  ;
DELETE FROM administracao.tabelas_rh WHERE schema_cod = 6 AND nome_tabela = 'feriado'                              ;
DELETE FROM administracao.tabelas_rh WHERE schema_cod = 7 AND nome_tabela = 'configuracao_banrisul_emprestimo'     ;


----------------
-- Ticket #22040
----------------

SELECT atualizarbanco('
CREATE TABLE folhapagamento.verba_rescisoria_mte (
    cod_verba           VARCHAR(10)     NOT NULL,
    nom_verba           VARCHAR(60)     NOT NULL,
    natureza            CHAR(1)         NOT NULL,
    CONSTRAINT pk_verba_rescisoria_mte  PRIMARY KEY  (cod_verba)
);
');
SELECT atualizarbanco('GRANT ALL ON TABLE folhapagamento.verba_rescisoria_mte TO urbem;');


SELECT atualizarbanco('ALTER TABLE folhapagamento.evento ADD COLUMN cod_verba VARCHAR(10);');
SELECT atualizarbanco('ALTER TABLE folhapagamento.evento ADD CONSTRAINT fk_evento_1 FOREIGN KEY (cod_verba) REFERENCES folhapagamento.verba_rescisoria_mte (cod_verba);');
SELECT atualizarbanco('CREATE INDEX fki_evento_1 ON folhapagamento.evento(cod_verba);');


SELECT atualizarbanco('INSERT INTO folhapagamento.verba_rescisoria_mte VALUES(''50''   ,''Salário''                                              , ''P'');');
SELECT atualizarbanco('INSERT INTO folhapagamento.verba_rescisoria_mte VALUES(''51''   ,''Comissões''                                            , ''P'');');
SELECT atualizarbanco('INSERT INTO folhapagamento.verba_rescisoria_mte VALUES(''52''   ,''Gratificação''                                         , ''P'');');
SELECT atualizarbanco('INSERT INTO folhapagamento.verba_rescisoria_mte VALUES(''53''   ,''Adicional de Insalubridade''                           , ''P'');');
SELECT atualizarbanco('INSERT INTO folhapagamento.verba_rescisoria_mte VALUES(''54''   ,''Adicional de Periculosidade''                          , ''P'');');
SELECT atualizarbanco('INSERT INTO folhapagamento.verba_rescisoria_mte VALUES(''55''   ,''Adicional Noturno 20%''                                , ''P'');');
SELECT atualizarbanco('INSERT INTO folhapagamento.verba_rescisoria_mte VALUES(''56.1'' ,''Horas-Extras 50%''                                     , ''P'');');
SELECT atualizarbanco('INSERT INTO folhapagamento.verba_rescisoria_mte VALUES(''56.2'' ,''Horas-Extras 70%''                                     , ''P'');');
SELECT atualizarbanco('INSERT INTO folhapagamento.verba_rescisoria_mte VALUES(''56.3'' ,''Horas-Extras 100%''                                    , ''P'');');
SELECT atualizarbanco('INSERT INTO folhapagamento.verba_rescisoria_mte VALUES(''57''   ,''Gorjetas''                                             , ''P'');');
SELECT atualizarbanco('INSERT INTO folhapagamento.verba_rescisoria_mte VALUES(''58''   ,''Descanso Semanal Remunerado (DSR)''                    , ''P'');');
SELECT atualizarbanco('INSERT INTO folhapagamento.verba_rescisoria_mte VALUES(''59''   ,''Reflexo do DSR sobre Salário Variável''                , ''P'');');
SELECT atualizarbanco('INSERT INTO folhapagamento.verba_rescisoria_mte VALUES(''60''   ,''Multa Art. 477, § 8º/CLT''                             , ''P'');');
SELECT atualizarbanco('INSERT INTO folhapagamento.verba_rescisoria_mte VALUES(''61''   ,''Multa Art. 479/CLT''                                   , ''P'');');
SELECT atualizarbanco('INSERT INTO folhapagamento.verba_rescisoria_mte VALUES(''62''   ,''Salário-Família''                                      , ''P'');');
SELECT atualizarbanco('INSERT INTO folhapagamento.verba_rescisoria_mte VALUES(''64.1'' ,''13º Salário–Exerc. Anteriores''                        , ''P'');');
SELECT atualizarbanco('INSERT INTO folhapagamento.verba_rescisoria_mte VALUES(''72''   ,''Percentagem de Afastamento''                           , ''P'');');
SELECT atualizarbanco('INSERT INTO folhapagamento.verba_rescisoria_mte VALUES(''73''   ,''Prêmios''                                              , ''P'');');
SELECT atualizarbanco('INSERT INTO folhapagamento.verba_rescisoria_mte VALUES(''74''   ,''Viagens''                                              , ''P'');');
SELECT atualizarbanco('INSERT INTO folhapagamento.verba_rescisoria_mte VALUES(''75''   ,''Sobreaviso''                                           , ''P'');');
SELECT atualizarbanco('INSERT INTO folhapagamento.verba_rescisoria_mte VALUES(''76''   ,''Prontidão''                                            , ''P'');');
SELECT atualizarbanco('INSERT INTO folhapagamento.verba_rescisoria_mte VALUES(''77''   ,''Adicional Tempo Serviço''                              , ''P'');');
SELECT atualizarbanco('INSERT INTO folhapagamento.verba_rescisoria_mte VALUES(''78''   ,''Adicional por Transferência de Localidade de Trabalho'', ''P'');');
SELECT atualizarbanco('INSERT INTO folhapagamento.verba_rescisoria_mte VALUES(''79''   ,''Salário Família Excedente ao Valor Legal''             , ''P'');');
SELECT atualizarbanco('INSERT INTO folhapagamento.verba_rescisoria_mte VALUES(''80''   ,''Abono/Gratificação de Férias Excedente''               , ''P'');');
SELECT atualizarbanco('INSERT INTO folhapagamento.verba_rescisoria_mte VALUES(''81''   ,''Valor Global Diárias para Viagem (acima 50% salário)'' , ''P'');');
SELECT atualizarbanco('INSERT INTO folhapagamento.verba_rescisoria_mte VALUES(''82''   ,''Ajuda de Custo Art. 470/CLT''                          , ''P'');');
SELECT atualizarbanco('INSERT INTO folhapagamento.verba_rescisoria_mte VALUES(''83''   ,''Etapas. Marítimos''                                    , ''P'');');
SELECT atualizarbanco('INSERT INTO folhapagamento.verba_rescisoria_mte VALUES(''84''   ,''Licença-Prêmio Indenizada''                            , ''P'');');
SELECT atualizarbanco('INSERT INTO folhapagamento.verba_rescisoria_mte VALUES(''85''   ,''Quebra de Caixa''                                      , ''P'');');
SELECT atualizarbanco('INSERT INTO folhapagamento.verba_rescisoria_mte VALUES(''86''   ,''Participação nos Lucros ou Resultados''                , ''P'');');
SELECT atualizarbanco('INSERT INTO folhapagamento.verba_rescisoria_mte VALUES(''87''   ,''Indenização a Título de Incentivo à Demissão''         , ''P'');');
SELECT atualizarbanco('INSERT INTO folhapagamento.verba_rescisoria_mte VALUES(''88''   ,''Salário Aprendizagem''                                 , ''P'');');
SELECT atualizarbanco('INSERT INTO folhapagamento.verba_rescisoria_mte VALUES(''89''   ,''Abonos Desvinculados do Salário''                      , ''P'');');
SELECT atualizarbanco('INSERT INTO folhapagamento.verba_rescisoria_mte VALUES(''90''   ,''Ganhos Eventuais Desvinculados do Salário''            , ''P'');');
SELECT atualizarbanco('INSERT INTO folhapagamento.verba_rescisoria_mte VALUES(''91''   ,''Reembolso Creche''                                     , ''P'');');
SELECT atualizarbanco('INSERT INTO folhapagamento.verba_rescisoria_mte VALUES(''92''   ,''Reembolso Babá''                                       , ''P'');');
SELECT atualizarbanco('INSERT INTO folhapagamento.verba_rescisoria_mte VALUES(''93''   ,''Gratificação Semestral''                               , ''P'');');
SELECT atualizarbanco('INSERT INTO folhapagamento.verba_rescisoria_mte VALUES(''94''   ,''Salário do Mês Anterior à Rescisão''                   , ''P'');');
SELECT atualizarbanco('INSERT INTO folhapagamento.verba_rescisoria_mte VALUES(''95''   ,''Outras verbas''                                        , ''P'');');
SELECT atualizarbanco('INSERT INTO folhapagamento.verba_rescisoria_mte VALUES(''96''   ,''Indenização Art. 9º, Lei nº 7.238/84''                 , ''P'');');
SELECT atualizarbanco('INSERT INTO folhapagamento.verba_rescisoria_mte VALUES(''97''   ,''Indenização Férias Escolares''                         , ''P'');');
SELECT atualizarbanco('INSERT INTO folhapagamento.verba_rescisoria_mte VALUES(''98''   ,''Multa do Art. 476-A, §5° da CLT''                      , ''P'');');
SELECT atualizarbanco('INSERT INTO folhapagamento.verba_rescisoria_mte VALUES(''99''   ,''Ajuste Saldo Negativo''                                , ''P'');');

SELECT atualizarbanco('INSERT INTO folhapagamento.verba_rescisoria_mte VALUES(''50.1'' ,''Faltas''                                               , ''D'');');
SELECT atualizarbanco('INSERT INTO folhapagamento.verba_rescisoria_mte VALUES(''50.2'' ,''Desconto DSR''                                         , ''D'');');
SELECT atualizarbanco('INSERT INTO folhapagamento.verba_rescisoria_mte VALUES(''100''  ,''Pensão Alimentícia''                                   , ''D'');');
SELECT atualizarbanco('INSERT INTO folhapagamento.verba_rescisoria_mte VALUES(''101''  ,''Adiantamento Salarial''                                , ''D'');');
SELECT atualizarbanco('INSERT INTO folhapagamento.verba_rescisoria_mte VALUES(''102''  ,''Adiantamento de 13º salário''                          , ''D'');');
SELECT atualizarbanco('INSERT INTO folhapagamento.verba_rescisoria_mte VALUES(''103''  ,''Aviso Prévio Indenizado''                              , ''D'');');
SELECT atualizarbanco('INSERT INTO folhapagamento.verba_rescisoria_mte VALUES(''104''  ,''Indenização Art. 480 CLT''                             , ''D'');');
SELECT atualizarbanco('INSERT INTO folhapagamento.verba_rescisoria_mte VALUES(''105''  ,''Empréstimo em consignação''                            , ''D'');');
SELECT atualizarbanco('INSERT INTO folhapagamento.verba_rescisoria_mte VALUES(''106''  ,''Vale-transporte adiantado''                            , ''D'');');
SELECT atualizarbanco('INSERT INTO folhapagamento.verba_rescisoria_mte VALUES(''107''  ,''Reembolso do Vale-Transporte''                         , ''D'');');
SELECT atualizarbanco('INSERT INTO folhapagamento.verba_rescisoria_mte VALUES(''108''  ,''Vale-Alimentação adiantado''                           , ''D'');');
SELECT atualizarbanco('INSERT INTO folhapagamento.verba_rescisoria_mte VALUES(''109''  ,''Reembolso do Vale-Alimentação''                        , ''D'');');
SELECT atualizarbanco('INSERT INTO folhapagamento.verba_rescisoria_mte VALUES(''110''  ,''Contribuição para o FAPI''                             , ''D'');');
SELECT atualizarbanco('INSERT INTO folhapagamento.verba_rescisoria_mte VALUES(''111''  ,''Contr. Sindical Laboral''                              , ''D'');');
SELECT atualizarbanco('INSERT INTO folhapagamento.verba_rescisoria_mte VALUES(''113''  ,''Contr.Previdencia Complementar''                       , ''D'');');
SELECT atualizarbanco('INSERT INTO folhapagamento.verba_rescisoria_mte VALUES(''115''  ,''Outros Descontos''                                     , ''D'');');
SELECT atualizarbanco('INSERT INTO folhapagamento.verba_rescisoria_mte VALUES(''116''  ,''Valor Líquido de TRCT Quitado – Decisão Judicial''     , ''D'');');
SELECT atualizarbanco('INSERT INTO folhapagamento.verba_rescisoria_mte VALUES(''118''  ,''Comp. Dias Salário Férias – Mês Anterior Rescisão''    , ''D'');');

INSERT INTO administracao.tabelas_rh VALUES (2, 'verba_rescisoria_mte', 1);

