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
* $Id: GRH_1960.sql 59612 2014-09-02 12:00:51Z gelson $
*
* Versão 1.96.0
*/



----------------
-- Ticket #14357
----------------

INSERT INTO administracao.relatorio
          ( cod_gestao
          , cod_modulo
          , cod_relatorio
          , nom_relatorio
          , arquivo )
     VALUES ( 4
          , 22
          , 1
          , 'Cargos'
          , 'cargos.rptdesign'
          );


----------------
-- Ticket #14301
----------------

SELECT atualizarbanco('
CREATE TABLE ima.codigo_dirf(
    exercicio           CHAR(4)                 NOT NULL,
    cod_dirf            INTEGER                 NOT NULL,
    tipo                CHAR(1)                 NOT NULL,
    descricao           VARCHAR(250)            NOT NULL,
    CONSTRAINT pk_codigo_dirf                   PRIMARY KEY (exercicio, cod_dirf, tipo),
    CONSTRAINT ck_codigo_dirf_1                 CHECK (tipo in (\'F\',\'J\'))
 );
');

SELECT atualizarbanco('GRANT ALL ON ima.codigo_dirf TO GROUP urbem;');

SELECT atualizarbanco('INSERT INTO ima.codigo_dirf (exercicio, cod_dirf, tipo, descricao) VALUES (\'2009\', 1561, \'F\', \'Trabalho Assalariado no País e Ausentes no Exterior a Serviço do País.\'                                                                                                                                                                   );'); 
SELECT atualizarbanco('INSERT INTO ima.codigo_dirf (exercicio, cod_dirf, tipo, descricao) VALUES (\'2009\', 0588, \'F\', \'TrabalhoTrabalho Sem Vínculo Empregatício (Importâncias pagas por pessoa jurídica à pessoa física).\'                                                                                                                                      );');
SELECT atualizarbanco('INSERT INTO ima.codigo_dirf (exercicio, cod_dirf, tipo, descricao) VALUES (\'2009\', 3208, \'F\', \'TrabalhoTrabalhoAluguéis e Royalties Pagos a Pessoa Física\'                                                                                                                                                                               );');
SELECT atualizarbanco('INSERT INTO ima.codigo_dirf (exercicio, cod_dirf, tipo, descricao) VALUES (\'2009\', 3223, \'F\', \'Resgate de Previdência Privada e Fapi\'                                                                                                                                                                                                    );');
SELECT atualizarbanco('INSERT INTO ima.codigo_dirf (exercicio, cod_dirf, tipo, descricao) VALUES (\'2009\', 5565, \'F\', \'ResgateRetenção do Imposto de Renda na Fonte sobre pagamento de resgate ou benefícios de caráter previdenciário, cujos beneficiários optaram pelo regime de tributação de que trata o art. 1º da Lei nº 11.053, de 29 de dezembro de 2004\');');
SELECT atualizarbanco('INSERT INTO ima.codigo_dirf (exercicio, cod_dirf, tipo, descricao) VALUES (\'2009\', 6891, \'F\', \'ResgateRetençãoCobertura por Sobrevivência em Seguro de Vida (VGBL)\'                                                                                                                                                                      );');
SELECT atualizarbanco('INSERT INTO ima.codigo_dirf (exercicio, cod_dirf, tipo, descricao) VALUES (\'2009\', 6904, \'F\', \'ResgateRetençãoCoberturaIndenizações por Danos Morais\'                                                                                                                                                                                    );');
SELECT atualizarbanco('INSERT INTO ima.codigo_dirf (exercicio, cod_dirf, tipo, descricao) VALUES (\'2009\', 8053, \'F\', \'ResgateRetençãoCoberturaIndenizaçõesAplicações Financeiras de Renda Fixa, Exceto em Fundos de Investimento\'                                                                                                                               );');
SELECT atualizarbanco('INSERT INTO ima.codigo_dirf (exercicio, cod_dirf, tipo, descricao) VALUES (\'2009\', 1708, \'J\', \'Remuneração de Serviços Profissionais Prestados por Pessoa Jurídica\'                                                                                                                                                                      );');
SELECT atualizarbanco('INSERT INTO ima.codigo_dirf (exercicio, cod_dirf, tipo, descricao) VALUES (\'2009\', 3280, \'J\', \'Remuneração de Serviços Pessoais Prestados por Associados de Cooperativas de Trabalho\'                                                                                                                                                    );');
SELECT atualizarbanco('INSERT INTO ima.codigo_dirf (exercicio, cod_dirf, tipo, descricao) VALUES (\'2009\', 3426, \'J\', \'RemuneraçãoAplicações Financeiras de Renda Fixa, Exceto em Fundos de Investimento\'                                                                                                                                                        );');
SELECT atualizarbanco('INSERT INTO ima.codigo_dirf (exercicio, cod_dirf, tipo, descricao) VALUES (\'2009\', 3746, \'J\', \'RemuneraçãoAplicaçõesRetenção de Cofins sobre Pagamentos Referentes à Aquisição de Autopeças - IN SRF 594, de 2005\'                                                                                                                       );');
SELECT atualizarbanco('INSERT INTO ima.codigo_dirf (exercicio, cod_dirf, tipo, descricao) VALUES (\'2009\', 4770, \'J\', \'RemuneraçãoAplicaçõesRetençãoRetenção de PIS/Pasep sobre Pagamentos Referentes à Aquisição de Autopeças - IN SRF 594, de 2005\'                                                                                                            );');
SELECT atualizarbanco('INSERT INTO ima.codigo_dirf (exercicio, cod_dirf, tipo, descricao) VALUES (\'2009\', 4085, \'J\', \'RemuneraçãoAplicaçõesRetençãoRetençãoRetenção de CSLL, Cofins e PIS/Pasep sobre pagamentos efetuados por órgãos, autarquias e fundações dos Estados, Distrito Federal e Municípios\'                                                       );');
SELECT atualizarbanco('INSERT INTO ima.codigo_dirf (exercicio, cod_dirf, tipo, descricao) VALUES (\'2009\', 4397, \'J\', \'RemuneraçãoAplicaçõesRetençãoRetençãoRetençãoRetenção de CSLL sobre pagamentos efetuados por órgãos, autarquias e fundações dos Estados, Distrito Federal e Municípios\'                                                                   );');
SELECT atualizarbanco('INSERT INTO ima.codigo_dirf (exercicio, cod_dirf, tipo, descricao) VALUES (\'2009\', 4407, \'J\', \'RemuneraçãoAplicaçõesRetençãoRetençãoRetençãoRetençãoRetenção de Cofins sobre pagamentos efetuados por órgãos, autarquias e fundações dos Estados, Distrito Federal e Municípios\'                                                         );');
SELECT atualizarbanco('INSERT INTO ima.codigo_dirf (exercicio, cod_dirf, tipo, descricao) VALUES (\'2009\', 4409, \'J\', \'RemuneraçãoAplicaçõesRetençãoRetençãoRetençãoRetençãoRetençãoRetenção de PIS/Pasep sobre pagamentos efetuados por órgãos, autarquias e fundações dos Estados, Distrito Federal e Municípios\'                                              );');
SELECT atualizarbanco('INSERT INTO ima.codigo_dirf (exercicio, cod_dirf, tipo, descricao) VALUES (\'2009\', 5944, \'J\', \'RemuneraçãoAplicaçõesRetençãoRetençãoRetençãoRetençãoRetençãoRetençãoRetenção de Imposto de Renda sobre Pagamentos Efetuados por Pessoas Jurídicas pela Prestação de Serviços Relacionados com a Atividade de Factoring\'                  );');
SELECT atualizarbanco('INSERT INTO ima.codigo_dirf (exercicio, cod_dirf, tipo, descricao) VALUES (\'2009\', 5952, \'J\', \'Retenção de Cofins, CSLL e PIS/Pasep sobre Pagamentos Efetuados por Pessoas Jurídicas de Direito Privado\'                                                                                                                                 );');
SELECT atualizarbanco('INSERT INTO ima.codigo_dirf (exercicio, cod_dirf, tipo, descricao) VALUES (\'2009\', 5960, \'J\', \'Retenção de Cofins sobre Pagamentos Efetuados por Pessoas Jurídicas de Direito Privado\'                                                                                                                                                   );');
SELECT atualizarbanco('INSERT INTO ima.codigo_dirf (exercicio, cod_dirf, tipo, descricao) VALUES (\'2009\', 5979, \'J\', \'Retenção de PIS/Pasep sobre Pagamentos efetuados por Pessoas Jurídicas de Direito Privado\'                                                                                                                                                );');
SELECT atualizarbanco('INSERT INTO ima.codigo_dirf (exercicio, cod_dirf, tipo, descricao) VALUES (\'2009\', 5987, \'J\', \'Retenção de CSLL sobre Pagamentos Efetuados por Pessoas Jurídicas de Direito Privado\'                                                                                                                                                     );');
SELECT atualizarbanco('INSERT INTO ima.codigo_dirf (exercicio, cod_dirf, tipo, descricao) VALUES (\'2009\', 8045, \'J\', \'Serviços de Propaganda Prestados por Pessoa Jurídica, Comissões e Corretagens Pagas a Pessoa Jurídica\'                                                                                                                                    );');

SELECT atualizarbanco('INSERT INTO ima.codigo_dirf SELECT 2003 AS exercicio, cod_dirf, tipo, descricao FROM ima.codigo_dirf WHERE exercicio = \'2009\';'); 
SELECT atualizarbanco('INSERT INTO ima.codigo_dirf SELECT 2004 AS exercicio, cod_dirf, tipo, descricao FROM ima.codigo_dirf WHERE exercicio = \'2009\';');
SELECT atualizarbanco('INSERT INTO ima.codigo_dirf SELECT 2005 AS exercicio, cod_dirf, tipo, descricao FROM ima.codigo_dirf WHERE exercicio = \'2009\';');
SELECT atualizarbanco('INSERT INTO ima.codigo_dirf SELECT 2006 AS exercicio, cod_dirf, tipo, descricao FROM ima.codigo_dirf WHERE exercicio = \'2009\';');
SELECT atualizarbanco('INSERT INTO ima.codigo_dirf SELECT 2007 AS exercicio, cod_dirf, tipo, descricao FROM ima.codigo_dirf WHERE exercicio = \'2009\';');
SELECT atualizarbanco('INSERT INTO ima.codigo_dirf SELECT 2008 AS exercicio, cod_dirf, tipo, descricao FROM ima.codigo_dirf WHERE exercicio = \'2009\';');

SELECT atualizarbanco('
CREATE TABLE ima.configuracao_dirf_prestador(
    exercicio           CHAR(4)                 NOT NULL,
    cod_prestador       INTEGER                 NOT NULL,
    cod_dirf            INTEGER                 NOT NULL,
    tipo                CHAR(1)                 NOT NULL,
    cod_conta           INTEGER                 NOT NULL,
    CONSTRAINT pk_configuracao_dirf_prestador   PRIMARY KEY                         (exercicio, cod_prestador),
    CONSTRAINT fk_configuracao_dirf_prestador_1 FOREIGN KEY                         (exercicio)
                                                REFERENCES ima.configuracao_dirf    (exercicio),
    CONSTRAINT fk_configuracao_dirf_prestador_2 FOREIGN KEY                         (exercicio, cod_dirf, tipo)
                                                REFERENCES ima.codigo_dirf          (exercicio, cod_dirf, tipo),
    CONSTRAINT fk_configuracao_dirf_prestador_3 FOREIGN KEY                         (exercicio, cod_conta)
                                                REFERENCES orcamento.conta_despesa  (exercicio, cod_conta)
 );
');

SELECT atualizarbanco('GRANT ALL ON ima.configuracao_dirf_prestador TO GROUP urbem;');

SELECT atualizarbanco('
CREATE TABLE ima.configuracao_dirf_irrf(
    exercicio           CHAR(4)                 NOT NULL,
    cod_conta           INTEGER                 NOT NULL,
    CONSTRAINT pk_configuracao_dirf_irrf        PRIMARY KEY                         (exercicio),
    CONSTRAINT fk_configuracao_dirf_irrf_1      FOREIGN KEY                         (exercicio)
                                                REFERENCES ima.configuracao_dirf    (exercicio),
    CONSTRAINT fk_configuracao_dirf_irrf_2      FOREIGN KEY                         (exercicio, cod_conta)
                                                REFERENCES contabilidade.plano_conta(exercicio, cod_conta)
 );
');

SELECT atualizarbanco('GRANT ALL ON ima.configuracao_dirf_irrf TO GROUP urbem;');

SELECT atualizarbanco('
CREATE TABLE ima.configuracao_dirf_inss(
    exercicio           CHAR(4)                 NOT NULL,
    cod_conta           INTEGER                 NOT NULL,
    CONSTRAINT pk_configuracao_dirf_inss        PRIMARY KEY                         (exercicio),
    CONSTRAINT fk_configuracao_dirf_inss_1      FOREIGN KEY                         (exercicio)
                                                REFERENCES ima.configuracao_dirf    (exercicio),
    CONSTRAINT fk_configuracao_dirf_inss_2      FOREIGN KEY                         (exercicio, cod_conta)
                                                REFERENCES contabilidade.plano_conta(exercicio, cod_conta)
 );
');

SELECT atualizarbanco('GRANT ALL ON ima.configuracao_dirf_inss TO GROUP urbem;');

INSERT INTO administracao.tabelas_rh VALUES (7,'codigo_dirf',1);


---------------------
-- RENOMEANDO TABELAS
---------------------

ALTER TABLE concurso.concurso       RENAME TO edital;
ALTER TABLE ponto.calendario        RENAME TO calendario_ponto;
ALTER TABLE calendario.calendario   RENAME TO calendario_cadastro;
ALTER TABLE beneficio.beneficio     RENAME TO beneficio_cadastro;


----------------
-- Ticket #14256
----------------

ALTER TABLE pessoal.configuracao_ferias ADD   COLUMN ferias_proporcionais INTEGER;
UPDATE      pessoal.configuracao_ferias SET          ferias_proporcionais = 12;
ALTER TABLE pessoal.configuracao_ferias ALTER COLUMN ferias_proporcionais SET NOT NULL;
ALTER TABLE pessoal.configuracao_ferias ALTER COLUMN dias_gozo TYPE NUMERIC(3,1);

INSERT INTO pessoal.configuracao_ferias VALUES ( 6,  0,  5, 27.5, 11);
INSERT INTO pessoal.configuracao_ferias VALUES ( 7,  6, 14, 22.0, 11);
INSERT INTO pessoal.configuracao_ferias VALUES ( 8, 15, 23, 16.5, 11);
INSERT INTO pessoal.configuracao_ferias VALUES ( 9, 24, 32, 11.0, 11);
  
INSERT INTO pessoal.configuracao_ferias VALUES (10,  0,  5, 25.0, 10);
INSERT INTO pessoal.configuracao_ferias VALUES (11,  6, 14, 20.0, 10);
INSERT INTO pessoal.configuracao_ferias VALUES (12, 15, 23, 15.0, 10);
INSERT INTO pessoal.configuracao_ferias VALUES (13, 24, 32, 10.0, 10);
  
INSERT INTO pessoal.configuracao_ferias VALUES (14,  0,  5, 22.5,  9);
INSERT INTO pessoal.configuracao_ferias VALUES (15,  6, 14, 18.0,  9);
INSERT INTO pessoal.configuracao_ferias VALUES (16, 15, 23, 13.5,  9);
INSERT INTO pessoal.configuracao_ferias VALUES (17, 24, 32,  9.0,  9);
  
INSERT INTO pessoal.configuracao_ferias VALUES (18,  0,  5, 20.0,  8);
INSERT INTO pessoal.configuracao_ferias VALUES (19,  6, 14, 16.0,  8);
INSERT INTO pessoal.configuracao_ferias VALUES (20, 15, 23, 12.0,  8);
INSERT INTO pessoal.configuracao_ferias VALUES (21, 24, 32,  8.0,  8);
  
INSERT INTO pessoal.configuracao_ferias VALUES (22,  0,  5, 17.5,  7);
INSERT INTO pessoal.configuracao_ferias VALUES (23,  6, 14, 14.0,  7);
INSERT INTO pessoal.configuracao_ferias VALUES (24, 15, 23, 10.5,  7);
INSERT INTO pessoal.configuracao_ferias VALUES (25, 24, 32,  7.0,  7);
  
INSERT INTO pessoal.configuracao_ferias VALUES (26,  0,  5, 15.0,  6);
INSERT INTO pessoal.configuracao_ferias VALUES (27,  6, 14, 12.0,  6);
INSERT INTO pessoal.configuracao_ferias VALUES (28, 15, 23,  9.0,  6);
INSERT INTO pessoal.configuracao_ferias VALUES (29, 24, 32,  6.0,  6);
  
INSERT INTO pessoal.configuracao_ferias VALUES (30,  0,  5, 12.5,  5);
INSERT INTO pessoal.configuracao_ferias VALUES (31,  6, 14, 10.0,  5);
INSERT INTO pessoal.configuracao_ferias VALUES (32, 15, 23,  7.5,  5);
INSERT INTO pessoal.configuracao_ferias VALUES (33, 24, 32,  5.0,  5);
  
INSERT INTO pessoal.configuracao_ferias VALUES (34,  0,  5, 10.0,  4);
INSERT INTO pessoal.configuracao_ferias VALUES (35,  6, 14,  8.0,  4);
INSERT INTO pessoal.configuracao_ferias VALUES (36, 15, 23,  6.0,  4);
INSERT INTO pessoal.configuracao_ferias VALUES (37, 24, 32,  4.0,  4);
  
INSERT INTO pessoal.configuracao_ferias VALUES (38,  0,  5,  7.5,  3);
INSERT INTO pessoal.configuracao_ferias VALUES (39,  6, 14,  6.0,  3);
INSERT INTO pessoal.configuracao_ferias VALUES (40, 15, 23,  4.5,  3);
INSERT INTO pessoal.configuracao_ferias VALUES (41, 24, 32,  3.0,  3);
  
INSERT INTO pessoal.configuracao_ferias VALUES (42,  0,  5,  5.0,  2);
INSERT INTO pessoal.configuracao_ferias VALUES (43,  6, 14,  4.0,  2);
INSERT INTO pessoal.configuracao_ferias VALUES (44, 15, 23,  3.0,  2);
INSERT INTO pessoal.configuracao_ferias VALUES (45, 24, 32,  2.0,  2);
  
INSERT INTO pessoal.configuracao_ferias VALUES (46,  0,  5,  2.5,  1);
INSERT INTO pessoal.configuracao_ferias VALUES (47,  6, 14,  2.0,  1);
INSERT INTO pessoal.configuracao_ferias VALUES (48, 15, 23,  1.5,  1);
INSERT INTO pessoal.configuracao_ferias VALUES (49, 24, 32,  1.0,  1);
 

----------------
-- Ticket #14376
----------------

SELECT atualizarbanco('
CREATE TABLE ima.configuracao_convenio_bb (
    cod_convenio            INTEGER         NOT NULL,
    cod_convenio_banco      VARCHAR(20)     NOT NULL,
    cod_banco               INTEGER         NOT NULL,
    CONSTRAINT pk_configuracao_convenio_bb   PRIMARY KEY                (cod_convenio, cod_banco),
    CONSTRAINT fk_configuracao_convenio_bb_1 FOREIGN KEY                (cod_banco)
                                             REFERENCES monetario.banco (cod_banco)
);
');

SELECT atualizarbanco('
GRANT ALL ON ima.configuracao_convenio_bb TO GROUP urbem;
');

SELECT atualizarbanco('
CREATE TABLE ima.configuracao_bb_conta (
    cod_convenio            INTEGER         NOT NULL,
    cod_banco               INTEGER         NOT NULL,
    cod_agencia             INTEGER         NOT NULL,
    cod_conta_corrente      INTEGER         NOT NULL,
    descricao               VARCHAR(60)     NOT NULL,
    CONSTRAINT pk_configuracao_bb_conta     PRIMARY KEY                             (cod_convenio, cod_banco, cod_agencia, cod_conta_corrente),
    CONSTRAINT fk_configuracao_bb_conta_1   FOREIGN KEY                             (cod_convenio, cod_banco)
                                            REFERENCES ima.configuracao_convenio_bb (cod_convenio, cod_banco),
    CONSTRAINT fk_configuracao_bb_conta_2   FOREIGN KEY                             (cod_banco, cod_agencia, cod_conta_corrente)
                                            REFERENCES monetario.conta_corrente     (cod_banco, cod_agencia, cod_conta_corrente)
);
');

SELECT atualizarbanco('
GRANT ALL ON ima.configuracao_bb_conta TO GROUP urbem;
');

SELECT atualizarbanco('
CREATE TABLE ima.configuracao_bb_orgao (
    cod_convenio            INTEGER         NOT NULL,
    cod_banco               INTEGER         NOT NULL,
    cod_agencia             INTEGER         NOT NULL,
    cod_conta_corrente      INTEGER         NOT NULL,
    cod_orgao               INTEGER         NOT NULL,
    CONSTRAINT pk_configuracao_bb_orgao     PRIMARY KEY                             (cod_convenio, cod_banco, cod_agencia, cod_conta_corrente, cod_orgao),
    CONSTRAINT fk_configuracao_bb_orgao_1   FOREIGN KEY                             (cod_convenio, cod_banco, cod_agencia, cod_conta_corrente)
                                            REFERENCES  ima.configuracao_bb_conta   (cod_convenio, cod_banco, cod_agencia, cod_conta_corrente),
    CONSTRAINT fk_configuracao_bb_orgao_2   FOREIGN KEY                             (cod_orgao)
                                            REFERENCES organograma.orgao            (cod_orgao)
 );
');

SELECT atualizarbanco('
GRANT ALL ON ima.configuracao_bb_orgao TO GROUP urbem;
');

SELECT atualizarbanco('
CREATE TABLE ima.configuracao_bb_local (
    cod_convenio            INTEGER         NOT NULL,
    cod_banco               INTEGER         NOT NULL,
    cod_agencia             INTEGER         NOT NULL,
    cod_conta_corrente      INTEGER         NOT NULL,
    cod_local               INTEGER         NOT NULL,
    CONSTRAINT pk_configuracao_bb_local     PRIMARY KEY                             (cod_convenio, cod_banco, cod_agencia, cod_conta_corrente, cod_local),
    CONSTRAINT fk_configuracao_bb_local_1   FOREIGN KEY                             (cod_convenio, cod_banco, cod_agencia, cod_conta_corrente)
                                            REFERENCES  ima.configuracao_bb_conta   (cod_convenio, cod_banco, cod_agencia, cod_conta_corrente),
    CONSTRAINT fk_configuracao_bb_local_2   FOREIGN KEY                             (cod_local)
                                            REFERENCES organograma.local            (cod_local)
 );
');

SELECT atualizarbanco('
GRANT ALL ON ima.configuracao_bb_local TO GROUP urbem;
');


create or replace function manutencao() returns void as $$
declare
    stSql varchar;
    stEntidade varchar:='';
    reRegistro record;
    reEntidades record;
    inCodValor integer;
    inCodEntidadePrincipal integer;
begin
    stSql := 'SELECT valor
                FROM administracao.configuracao
               WHERE parametro = \'cod_entidade_prefeitura\'
                 AND exercicio = \'2009\'';
    inCodEntidadePrincipal := selectIntoInteger(stSql);

    stSql := 'SELECT entidade.cod_entidade
                  FROM orcamento.entidade
                 WHERE exercicio = \'2009\'
                   AND cod_entidade IN (SELECT cod_entidade
                                          FROM administracao.entidade_rh
                                         WHERE exercicio = \'2009\'
                                        GROUP BY cod_entidade)';

    for reEntidades in execute stSql loop
        if reEntidades.cod_entidade != inCodEntidadePrincipal then
            stEntidade := '_'||reEntidades.cod_entidade;
        else
            stEntidade := '';
        end if;
        
        stSql := 'SELECT *
                    FROM ima'||stEntidade||'.configuracao_convenio';
    
        for reRegistro in execute stSql loop
        
            stSql := 'INSERT INTO ima.configuracao_convenio_bb 
                      SELECT '||reRegistro.cod_convenio||' as cod_convenio
                           , '||reRegistro.cod_convenio_banco||' as cod_convenio_banco
                           , '||reRegistro.cod_banco||' as cod_banco';
            execute stSql;
            
            stSql := 'INSERT INTO ima.configuracao_bb_conta
                      SELECT '||reRegistro.cod_convenio||' as cod_convenio
                           , '||reRegistro.cod_banco||' as cod_banco
                           , '||reRegistro.cod_agencia||' as cod_agencia
                           , '||reRegistro.cod_conta_corrente||' as cod_conta_corrente
                           , \'Migração Automática GRH 1.96.0\' as descricao';
            execute stSql;

            stSql := '  INSERT INTO ima.configuracao_bb_orgao
                        SELECT '||reRegistro.cod_convenio||' as cod_convenio
                             , '||reRegistro.cod_banco||' as cod_banco
                             , '||reRegistro.cod_agencia||' as cod_agencia
                             , '||reRegistro.cod_conta_corrente||' as cod_conta_corrente
                             , orgao.cod_orgao
                          FROM organograma.orgao                                                                
                             , organograma.organograma                                                          
                             , organograma.orgao_nivel                                                          
                             , organograma.nivel                                                                
                             , organograma.vw_orgao_nivel as ovw                                                
                             , (SELECT MAX(cod_organograma) as cod_organograma                                  
                                     , MAX(implantacao) AS data                                                 
                                  FROM organograma.organograma oo                                               
                                 WHERE implantacao <= to_char(now(), \'yyyy-mm-dd\')) as ultima_data       
                        WHERE organograma.cod_organograma = nivel.cod_organograma                               
                          AND nivel.cod_organograma       = orgao_nivel.cod_organograma                         
                          AND nivel.cod_nivel             = orgao_nivel.cod_nivel                               
                          AND orgao_nivel.cod_orgao       = orgao.cod_orgao                                     
                          AND orgao.cod_orgao             = ovw.cod_orgao                                       
                          AND orgao_nivel.cod_organograma = ovw.cod_organograma                                 
                          AND orgao_nivel.cod_organograma = ultima_data.cod_organograma                         
                          AND nivel.cod_nivel             = ovw.nivel                                            
                          AND organograma.ativo           = true
                      GROUP BY orgao.cod_orgao';
            execute stSql;
    
        end loop;
        
    end loop;
end
$$ language 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();

SELECT atualizarbanco('DROP TABLE ima.configuracao_convenio;');

----------------
-- 
----------------


CREATE TYPE colunasDirfPrestadoresServicoReduzida AS (
    uso_declarante       INTEGER,
    sequencia            INTEGER,
    nome_beneficiario    VARCHAR(200),
    beneficiario         VARCHAR(14),
    ident_especializacao VARCHAR(1),
    codigo_retencao      VARCHAR(4),
    ident_especie_beneficiario INTEGER,
    jan         VARCHAR(45),
    fev         VARCHAR(45),
    mar         VARCHAR(45),
    abr         VARCHAR(45),
    mai         VARCHAR(45),
    jun         VARCHAR(45),
    jul         VARCHAR(45),
    ago         VARCHAR(45),
    set         VARCHAR(45),
    out         VARCHAR(45),
    nov         VARCHAR(45),
    dez         VARCHAR(45),
    dec         VARCHAR(45)
);

CREATE TYPE colunasDirfPrestadoresServico AS (
    nome_beneficiario        VARCHAR(200),
    beneficiario             VARCHAR(14),
    cod_retencao             INTEGER,
    ident_especie_beneficiario INTEGER,
    uso_declarante           INTEGER,
    jan1                     NUMERIC,
    jan2                     NUMERIC,
    jan3                     NUMERIC,
    fev1                     NUMERIC,
    fev2                     NUMERIC,
    fev3                     NUMERIC,
    mar1                     NUMERIC,
    mar2                     NUMERIC,
    mar3                     NUMERIC,
    abr1                     NUMERIC,
    abr2                     NUMERIC,
    abr3                     NUMERIC,
    mai1                     NUMERIC,
    mai2                     NUMERIC,
    mai3                     NUMERIC,
    jun1                     NUMERIC,
    jun2                     NUMERIC,
    jun3                     NUMERIC,
    jul1                     NUMERIC,
    jul2                     NUMERIC,
    jul3                     NUMERIC,
    ago1                     NUMERIC,
    ago2                     NUMERIC,
    ago3                     NUMERIC,
    set1                     NUMERIC,
    set2                     NUMERIC,
    set3                     NUMERIC,
    out1                     NUMERIC,
    out2                     NUMERIC,
    out3                     NUMERIC,
    nov1                     NUMERIC,
    nov2                     NUMERIC,
    nov3                     NUMERIC,
    dez1                     NUMERIC,
    dez2                     NUMERIC,
    dez3                     NUMERIC,
    dec1                     NUMERIC,
    dec2                     NUMERIC,
    dec3                     NUMERIC
);

CREATE TYPE colunasDirfPrestadoresServicoValorEmpenhoExercicio AS (
    numcgm     INTEGER
  , vl_empenho NUMERIC
);
