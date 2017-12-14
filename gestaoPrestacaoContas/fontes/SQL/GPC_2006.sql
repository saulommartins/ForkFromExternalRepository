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
* URBEM Solucoes de Gestao Publica Ltda
* www.urbem.cnm.org.br
*
* $Id:$
*
* Versão 2.00.6
*/

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE
    stSQL               VARCHAR;
    reRecord            RECORD;

BEGIN

    PERFORM 1
       FROM administracao.configuracao
      WHERE exercicio  = '2012'
        AND cod_modulo = 2
        AND parametro  = 'cnpj'
        AND valor      = '24854234000164'
          ;
          
    IF NOT FOUND THEN

        ----------------
        -- Ticket #18306
        ----------------
        
        ALTER TABLE tcmgo.nota_fiscal ADD COLUMN chave_acesso NUMERIC(44);
        
        ----------------
        -- Ticket #
        ----------------
        
        CREATE TABLE tcmgo.tipo_responsavel (
            cod_tipo    INTEGER             NOT NULL,
            descricao   VARCHAR(80)         NOT NULL,
            CONSTRAINT pk_tipo_responsavel  PRIMARY KEY (cod_tipo)
        );
        GRANT ALL ON tcmgo.tipo_responsavel TO urbem;
        
        INSERT INTO tcmgo.tipo_responsavel VALUES (1, 'Gestor e Ordenador de Despesa (Quando não há delegação de competência)');
        INSERT INTO tcmgo.tipo_responsavel VALUES (2, 'Gestor'                                                                );
        INSERT INTO tcmgo.tipo_responsavel VALUES (3, 'Ordenador de Despesa'                                                  );
        
        
        CREATE TABLE tcmgo.provimento_juridico (
            cod_provimento  INTEGER         NOT NULL,
            descricao       VARCHAR(80)     NOT NULL,
            CONSTRAINT pk_provimento_juridico   PRIMARY KEY (cod_provimento)
        );
        GRANT ALL ON tcmgo.provimento_juridico TO urbem;
        
        INSERT INTO tcmgo.provimento_juridico VALUES (1, 'Provimento Efetivo em Cargo de Advogado');
        INSERT INTO tcmgo.provimento_juridico VALUES (2, 'Provimento em Cargo em Comissão');
        INSERT INTO tcmgo.provimento_juridico VALUES (3, 'Terceirização Pessoa Física');
        INSERT INTO tcmgo.provimento_juridico VALUES (4, 'Terceirização Pessoa Jurídica');
        
        
        CREATE TABLE tcmgo.provimento_contabil (
            cod_provimento  INTEGER         NOT NULL,
            descricao       VARCHAR(80)     NOT NULL,
            CONSTRAINT pk_provimento_contabil   PRIMARY KEY (cod_provimento)
        );
        GRANT ALL ON tcmgo.provimento_contabil TO urbem;
        
        INSERT INTO tcmgo.provimento_contabil VALUES (1, 'Provimento Efetivo em Cargo de Contador');
        INSERT INTO tcmgo.provimento_contabil VALUES (2, 'Provimento em Cargo em Comissão');
        INSERT INTO tcmgo.provimento_contabil VALUES (3, 'Terceirização Pessoa Física');
        INSERT INTO tcmgo.provimento_contabil VALUES (4, 'Terceirização Pessoa Jurídica');
        
        
        CREATE TABLE tcmgo.unidade_responsavel (
            exercicio                   CHAR(4)     NOT NULL,
            num_unidade                 INTEGER     NOT NULL,
            num_orgao                   INTEGER     NOT NULL,
            timestamp                   TIMESTAMP   NOT NULL DEFAULT ('now'::text)::timestamp(3) with time zone,
            cgm_gestor                  INTEGER     NOT NULL,
            gestor_dt_inicio            DATE        NOT NULL,
            gestor_dt_fim               DATE                ,
            tipo_responsavel            INTEGER     NOT NULL,
            gestor_cargo                VARCHAR(50)         ,
            cgm_contador                INTEGER     NOT NULL,
            contador_dt_inicio          DATE        NOT NULL,
            contador_dt_fim             DATE                ,
            contador_crc                VARCHAR(11)         ,
            uf_crc                      INTEGER             ,
            cod_provimento_contabil     INTEGER             ,
            cgm_controle_interno        INTEGER     NOT NULL,
            controle_interno_dt_inicio  DATE        NOT NULL,
            controle_interno_dt_fim     DATE                ,
            cgm_juridico                INTEGER     NOT NULL,
            juridico_dt_inicio          DATE        NOT NULL,
            juridico_dt_fim             DATE                ,
            juridico_oab                NUMERIC(8)          ,
            uf_oab                      INTEGER             ,
            cod_provimento_juridico     INTEGER             ,
        
            CONSTRAINT pk_unidade_responsavel       PRIMARY KEY                         (exercicio, num_unidade, num_orgao, timestamp),
            CONSTRAINT fk_unidade_responsavel_1     FOREIGN KEY                         (exercicio, num_unidade, num_orgao)
                                                    REFERENCES orcamento.unidade        (exercicio, num_unidade, num_orgao),
            CONSTRAINT fk_unidade_responsavel_2     FOREIGN KEY                         (cgm_gestor)
                                                    REFERENCES sw_cgm                   (numcgm),
            CONSTRAINT fk_unidade_responsavel_3     FOREIGN KEY                         (tipo_responsavel)
                                                    REFERENCES tcmgo.tipo_responsavel   (cod_tipo),
            CONSTRAINT fk_unidade_responsavel_4     FOREIGN KEY                         (cgm_contador)
                                                    REFERENCES sw_cgm                   (numcgm),
            CONSTRAINT fk_unidade_responsavel_5     FOREIGN KEY                         (uf_crc)
                                                    REFERENCES sw_uf                    (cod_uf),
            CONSTRAINT fk_unidade_responsavel_6     FOREIGN KEY                         (cod_provimento_contabil)
                                                    REFERENCES tcmgo.provimento_contabil(cod_provimento),
            CONSTRAINT fk_unidade_responsavel_7     FOREIGN KEY                         (cgm_controle_interno)
                                                    REFERENCES sw_cgm                   (numcgm),
            CONSTRAINT fk_unidade_responsavel_8     FOREIGN KEY                         (cgm_juridico)
                                                    REFERENCES sw_cgm                   (numcgm),
            CONSTRAINT fk_unidade_responsavel_9     FOREIGN KEY                         (uf_oab)
                                                    REFERENCES sw_uf                    (cod_uf),
            CONSTRAINT fk_unidade_responsavel_10    FOREIGN KEY                         (cod_provimento_juridico)
                                                    REFERENCES tcmgo.provimento_juridico(cod_provimento)
        
        );
        GRANT ALL ON tcmgo.unidade_responsavel TO urbem;
        
        
        CREATE TABLE tcmgo.contador_terceirizado (
            exercicio   CHAR(4)     NOT NULL,
            num_unidade INTEGER     NOT NULL,
            num_orgao   INTEGER     NOT NULL,
            timestamp   TIMESTAMP   NOT NULL DEFAULT ('now'::text)::timestamp(3) with time zone,
            numcgm      INTEGER     NOT NULL,
            CONSTRAINT pk_contador_terceirizado     PRIMARY KEY                         (exercicio, num_unidade, num_orgao, timestamp, numcgm),
            CONSTRAINT fk_contador_terceirizado_1   FOREIGN KEY                         (exercicio, num_unidade, num_orgao, timestamp)
                                                    REFERENCES tcmgo.unidade_responsavel(exercicio, num_unidade, num_orgao, timestamp),
            CONSTRAINT fk_contador_terceirizado_2   FOREIGN KEY                         (numcgm)
                                                    REFERENCES sw_cgm_pessoa_juridica   (numcgm)
        );
        GRANT ALL ON tcmgo.contador_terceirizado TO urbem;
        
        
        CREATE TABLE tcmgo.juridico_terceirizado (
            exercicio   CHAR(4)     NOT NULL,
            num_unidade INTEGER     NOT NULL,
            num_orgao   INTEGER     NOT NULL,
            timestamp   TIMESTAMP   NOT NULL DEFAULT ('now'::text)::timestamp(3) with time zone,
            numcgm      INTEGER     NOT NULL,
            CONSTRAINT pk_juridico_terceirizado     PRIMARY KEY                         (exercicio, num_unidade, num_orgao, timestamp, numcgm),
            CONSTRAINT fk_juridico_terceirizado_1   FOREIGN KEY                         (exercicio, num_unidade, num_orgao, timestamp)
                                                    REFERENCES tcmgo.unidade_responsavel(exercicio, num_unidade, num_orgao, timestamp),
            CONSTRAINT fk_juridico_terceirizado_2   FOREIGN KEY                         (numcgm)
                                                    REFERENCES sw_cgm_pessoa_juridica   (numcgm)
        );
        GRANT ALL ON tcmgo.juridico_terceirizado TO urbem;
        
        
        UPDATE administracao.acao  SET ordem = 2 where cod_acao = 1753;
        UPDATE administracao.acao  SET ordem = 3 where cod_acao = 1754;
        
        INSERT
          INTO administracao.acao
             ( cod_acao
             , cod_funcionalidade
             , nom_arquivo
             , parametro
             , ordem
             , complemento_acao
             , nom_acao )
             VALUES
             ( 2812
             , 364
             , 'FMManterConfiguracaoUnidadeOrcamentaria.php'
             , 'confgUnOrc'
             , 4
             , ''
             , 'Configurar Unidade Orçamentaria'
             );

        
        ----------------
        -- Ticket #18357
        ----------------
        
        UPDATE administracao.acao
           SET ordem = ordem + 1
         WHERE cod_funcionalidade = 364
           AND ordem > 4
             ;
        
        INSERT
          INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
          VALUES
          ( 2813
          , 364
          , 'FMManterConfiguracaoDividaConsolidada.php'
          , 'confDivCons'
          , 5
          , ''
          , 'Configurar Dívida Consolidada'
          );
        
        
        CREATE TABLE tcmgo.tipo_lancamento (
            cod_lancamento  INTEGER         NOT NULL,
            descricao       VARCHAR(80)     NOT NULL,
            CONSTRAINT pk_tipo_lancamento   PRIMARY KEY (cod_lancamento)
        );
        GRANT ALL ON tcmgo.tipo_lancamento TO urbem;
        
        
        INSERT INTO tcmgo.tipo_lancamento VALUES ( 1, 'Dívida Mobiliária'                                                      );
        INSERT INTO tcmgo.tipo_lancamento VALUES ( 2, 'Dívida Contratual de PPP'                                               );
        INSERT INTO tcmgo.tipo_lancamento VALUES ( 3, 'Demais Dívidas Contratuais Internas'                                    );
        INSERT INTO tcmgo.tipo_lancamento VALUES ( 4, 'Dívidas Contratuais Externas'                                           );
        INSERT INTO tcmgo.tipo_lancamento VALUES ( 5, 'Precatórios Posteriores a 05/05/2000 (inclusive) - Vencidos e não Pagos');
        INSERT INTO tcmgo.tipo_lancamento VALUES ( 6, 'Parcelamento de Dívidas de Tributos'                                    );
        INSERT INTO tcmgo.tipo_lancamento VALUES ( 7, 'Parcelamento de Dívidas Previdenciárias'                                );
        INSERT INTO tcmgo.tipo_lancamento VALUES ( 8, 'Parcelamento de Dívidas das Demais Contribuições Sociais'               );
        INSERT INTO tcmgo.tipo_lancamento VALUES ( 9, 'Parcelamento de Dívidas do FGTS'                                        );
        INSERT INTO tcmgo.tipo_lancamento VALUES (10, 'Outras Dívidas'                                                         );
        INSERT INTO tcmgo.tipo_lancamento VALUES (11, 'Passivos Reconhecidos'                                                  );
        
        
        CREATE TABLE tcmgo.divida_consolidada (
            exercicio           CHAR(4)     NOT NULL,
            dt_inicio           DATE        NOT NULL,
            dt_fim              DATE        NOT NULL,
            num_unidade         INTEGER     NOT NULL,
            num_orgao           INTEGER     NOT NULL,
            numcgm              INTEGER             ,
            tipo_lancamento     INTEGER     NOT NULL,
            nro_lei_autorizacao CHAR(7)     NOT NULL,
            dt_lei_autorizacao  DATE        NOT NULL,
            vl_saldo_anterior   NUMERIC(14,2)       ,
            vl_contratacao      NUMERIC(14,2)       ,
            vl_amortizacao      NUMERIC(14,2)       ,
            vl_cancelamento     NUMERIC(14,2)       ,
            vl_encampacao       NUMERIC(14,2)       ,
            vl_atualizacao      NUMERIC(14,2)       ,
            vl_saldo_atual      NUMERIC(14,2)       ,
            CONSTRAINT pk_divida_consolidada     PRIMARY KEY                      (exercicio, dt_inicio, dt_fim, num_unidade, num_orgao, tipo_lancamento),
            CONSTRAINT fk_divida_consolidada_1   FOREIGN KEY                      (tipo_lancamento)
                                                 REFERENCES tcmgo.tipo_lancamento (cod_lancamento),
            CONSTRAINT fk_divida_consolidada_2   FOREIGN KEY                      (numcgm)
                                                 REFERENCES sw_cgm                (numcgm)
        );
        GRANT ALL ON tcmgo.divida_consolidada TO urbem;
        
        
        ----------------
        -- Ticket #18361
        ----------------
        
        UPDATE administracao.acao
           SET ordem = ordem + 1
         WHERE cod_funcionalidade = 364
           AND ordem > 4
             ;
        
        INSERT
          INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
          VALUES
          ( 2814
          , 364
          , 'FMManterConfiguracaoProjecaoAtuarial.php'
          , 'projAtuarial'
          , 5
          , ''
          , 'Configurar Projeção Atuarial'
          );
        
        CREATE TABLE tcmgo.projecao_atuarial (
            exercicio       CHAR(4)             NOT NULL,
            num_orgao       INTEGER             NOT NULL,
            exercicio_orgao CHAR(4)             NOT NULL,
            vl_receita      NUMERIC(14,2)               ,
            vl_despesa      NUMERIC(14,2)               ,
            vl_saldo        NUMERIC(14,2)               ,
            CONSTRAINT pk_projecao_atuarial     PRIMARY KEY                 (exercicio, num_orgao),
            CONSTRAINT fk_projecao_atuarial_1   FOREIGN KEY                 (num_orgao, exercicio_orgao)
                                                REFERENCES orcamento.orgao  (num_orgao, exercicio)
        );
        GRANT ALL ON tcmgo.projecao_atuarial TO urbem;
        
        
        ALTER TABLE tcmgo.contrato DROP  CONSTRAINT uk_contrato   ;
        ALTER TABLE tcmgo.contrato add  CONSTRAINT uk_contrato UNIQUE (nro_contrato,exercicio,cod_entidade);
    END IF;    
        
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();

----------------
-- Ticket #18458
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN
	PERFORM 1
	   FROM pg_tables
	  WHERE tablename  = 'fundeb'
	    AND schemaname = 'tcern'
	      ;
	IF NOT FOUND THEN

		CREATE SEQUENCE tcern.fundeb_cod_fundeb_seq;
		CREATE SEQUENCE tcern.royalties_cod_royalties_seq;
		
		CREATE TABLE tcern.fundeb (
		    cod_fundeb              INTEGER     NOT NULL DEFAULT nextval('tcern.fundeb_cod_fundeb_seq'),
		    codigo                  CHAR(2)     NOT NULL,
		    CONSTRAINT pk_fundeb PRIMARY KEY (cod_fundeb)
		);
		
		CREATE TABLE tcern.royalties (
		    cod_royalties           INTEGER     NOT NULL DEFAULT nextval('tcern.royalties_cod_royalties_seq'),
		    codigo                  CHAR(2)     NOT NULL,
		    CONSTRAINT pk_royalties PRIMARY KEY (cod_royalties)
		);
		
		CREATE TABLE tcern.fundeb_empenho (
		    cod_empenho             INTEGER     NOT NULL,
		    cod_entidade            INTEGER     NOT NULL,
		    exercicio               VARCHAR(4)  NOT NULL,
		    cod_fundeb              INTEGER     NOT NULL,
		    CONSTRAINT pk_fundeb_empenho PRIMARY KEY (cod_empenho, cod_entidade, exercicio, cod_fundeb),
		    CONSTRAINT fk_fundeb_empenho_empenho FOREIGN KEY (cod_empenho, cod_entidade, exercicio) REFERENCES empenho.empenho (cod_empenho, cod_entidade, exercicio),
		    CONSTRAINT fk_fundeb_empenho_fundeb  FOREIGN KEY (cod_fundeb)                           REFERENCES tcern.fundeb    (cod_fundeb)
		);
		
		CREATE TABLE tcern.royalties_empenho (
		    cod_empenho             INTEGER     NOT NULL,
		    cod_entidade            INTEGER     NOT NULL,
		    exercicio               VARCHAR(4)  NOT NULL,
		    cod_royalties           INTEGER     NOT NULL,
		    CONSTRAINT pk_royalties_empenho PRIMARY KEY (cod_empenho, cod_entidade, exercicio, cod_royalties),
		    CONSTRAINT fk_royalties_empenho_empenho FOREIGN KEY (cod_empenho, cod_entidade, exercicio) REFERENCES empenho.empenho (cod_empenho, cod_entidade, exercicio),
		    CONSTRAINT fk_fundeb_empenho_royalties  FOREIGN KEY (cod_royalties)                        REFERENCES tcern.royalties (cod_royalties)
		);
		
		INSERT INTO tcern.fundeb (codigo) VALUES ('00');
		INSERT INTO tcern.fundeb (codigo) VALUES ('40');
		INSERT INTO tcern.fundeb (codigo) VALUES ('60');
		
		
		INSERT INTO tcern.royalties (codigo) VALUES ('05');
		INSERT INTO tcern.royalties (codigo) VALUES ('10');
		INSERT INTO tcern.royalties (codigo) VALUES ('25');
		INSERT INTO tcern.royalties (codigo) VALUES ('FE');
		INSERT INTO tcern.royalties (codigo) VALUES ('00');
		
		GRANT ALL ON tcern.fundeb_cod_fundeb_seq       TO GROUP urbem;
		GRANT ALL ON tcern.royalties_cod_royalties_seq TO GROUP urbem;
		GRANT ALL ON tcern.fundeb                      TO GROUP urbem;
		GRANT ALL ON tcern.royalties                   TO GROUP urbem;
		GRANT ALL ON tcern.fundeb_empenho              TO GROUP urbem;
		GRANT ALL ON tcern.royalties_empenho           TO GROUP urbem;

	END IF;

END;
$$ LANGUAGE 'plpgsql';

SELECT manutencao();
DROP FUNCTION manutencao();

----------------
-- Ticket #18457
----------------
CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE
	stTeste 	VARCHAR;
BEGIN
	PERFORM 1
	   FROM pg_tables
	  WHERE tablename = 'nota_fiscal'
	      ;
	IF NOT FOUND THEN
		CREATE TABLE tcern.nota_fiscal (
		    cod_nota_liquidacao     INTEGER     NOT NULL,
		    cod_entidade            INTEGER     NOT NULL,
		    exercicio               CHAR(4)     NOT NULL,
		    nro_nota                VARCHAR(12)    NOT NULL,
		    nro_serie               VARCHAR(12)    NOT NULL,
		    data_emissao            DATE        NOT NULL,
		    cod_validacao           VARCHAR(50) NOT NULL,
		    modelo                  VARCHAR(3)  NOT NULL,
		    CONSTRAINT pk_nota_fiscal           PRIMARY KEY                  (cod_nota_liquidacao,cod_entidade,exercicio),
		    CONSTRAINT fk_nota_fiscal_nota_liquidacao_2 FOREIGN KEY (cod_nota_liquidacao,cod_entidade,exercicio) REFERENCES empenho.nota_liquidacao (cod_nota,cod_entidade,exercicio)
		
		);
		
		GRANT ALL ON tcern.nota_fiscal                      TO GROUP urbem;

	END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT manutencao();
DROP FUNCTION manutencao();


        ----------------
        -- Ticket #18323
        ----------------
       
CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE
	stTeste 	VARCHAR;
BEGIN
	PERFORM 1
 	   FROM pg_tables
 	  WHERE tablename = 'tipo_vinculo_recurso'
	      ;

	IF NOT FOUND THEN
 
	        CREATE TABLE stn.tipo_vinculo_recurso (
	            cod_tipo        INTEGER             NOT NULL,
	            descricao       VARCHAR(80)         NOT NULL,
	            CONSTRAINT pk_tipo_vinculo_recurso  PRIMARY KEY (cod_tipo)
	        );
	        GRANT ALL ON stn.tipo_vinculo_recurso TO urbem;
	        
	        INSERT INTO stn.tipo_vinculo_recurso VALUES (1, 'Recursos Pagamento Profissionais Magistério');
	        INSERT INTO stn.tipo_vinculo_recurso VALUES (2, 'Recursos Outras Despesas');
	        
	        ALTER TABLE stn.vinculo_recurso DROP CONSTRAINT pk_vinculo_recurso;
	        ALTER TABLE stn.vinculo_recurso ADD COLUMN   cod_tipo INTEGER;
	        UPDATE      stn.vinculo_recurso SET          cod_tipo = 2;
	        ALTER TABLE stn.vinculo_recurso ALTER COLUMN cod_tipo SET NOT NULL;
	        ALTER TABLE stn.vinculo_recurso ADD  CONSTRAINT pk_vinculo_recurso   PRIMARY KEY (exercicio, cod_entidade, num_orgao, num_unidade, cod_recurso, cod_vinculo, cod_tipo);
	        ALTER TABLE stn.vinculo_recurso ADD  CONSTRAINT fk_vinculo_recurso_5 FOREIGN KEY (cod_tipo) REFERENCES stn.tipo_vinculo_recurso(cod_tipo);

	END IF;

END;
$$ LANGUAGE 'plpgsql';

SELECT manutencao();
DROP FUNCTION manutencao();


----------------
-- Ticket #18540
----------------

CREATE TABLE tceam.empenho_incorporacao (
    cod_empenho_incorporado INTEGER     NOT NULL,
    cod_empenho             INTEGER     NOT NULL,
    cod_entidade            INTEGER     NOT NULL,
    exercicio               VARCHAR(4)  NOT NULL,
    descricao               VARCHAR(10) NOT NULL,
    CONSTRAINT pk_empenho_incorporacao PRIMARY KEY (cod_empenho, cod_entidade, exercicio),
    CONSTRAINT fk_empenho_incorporacao_empenho FOREIGN KEY (cod_empenho, cod_entidade, exercicio) REFERENCES empenho.empenho(cod_empenho, cod_entidade, exercicio)
);

GRANT ALL ON tceam.empenho_incorporacao TO GROUP urbem;
