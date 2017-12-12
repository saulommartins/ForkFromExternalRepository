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
* $Id: GT_1910.sql 59612 2014-09-02 12:00:51Z gelson $
*
* Versão 1.90.5.
*/

----------------
-- Ticket #13121
----------------

INSERT INTO arrecadacao.motivo_devolucao VALUES (14,'Lançamento Remido','Remido'  ) ;

CREATE TABLE divida.divida_remissao(
    cod_inscricao       INTEGER         NOT NULL,
    exercicio           CHAR(4)         NOT NULL,
    cod_norma           INTEGER         NOT NULL,
    numcgm              INTEGER         NOT NULL,
    dt_remissao         DATE            NOT NULL,
    observacao          TEXT,
    CONSTRAINT pk_divida_remissao       PRIMARY KEY                      (cod_inscricao, exercicio),
    CONSTRAINT fk_divida_remissao_1     FOREIGN KEY                      (cod_inscricao, exercicio)
                                        REFERENCES divida.divida_ativa   (cod_inscricao, exercicio),
    CONSTRAINT fk_divida_remissao_2     FOREIGN KEY                      (cod_norma)
                                        REFERENCES normas.norma          (cod_norma),
    CONSTRAINT fk_divida_remissao_3     FOREIGN KEY                      (numcgm)
                                        REFERENCES administracao.usuario (numcgm)
);

GRANT ALL ON divida.divida_remissao TO GROUP urbem;

UPDATE administracao.funcionalidade SET ordem =  99 WHERE cod_funcionalidade = 358;
UPDATE administracao.funcionalidade SET ordem = 100 WHERE cod_funcionalidade = 366;

INSERT INTO administracao.configuracao
          ( exercicio
          , cod_modulo
          , parametro
          , valor )
     VALUES ( 2008
          , 33
          , 'lancamento_ativo'
          , ''
          );

INSERT INTO administracao.configuracao
          ( exercicio
          , cod_modulo
          , parametro
          , valor )
     VALUES ( 2008
          , 33
          , 'inscricao_automatica'
          , ''
          );

INSERT INTO administracao.configuracao
          ( exercicio
          , cod_modulo
          , parametro
          , valor )
     VALUES ( 2008
          , 33
          , 'validacao'
          , ''
          );

INSERT INTO administracao.configuracao
          ( exercicio
          , cod_modulo
          , parametro
          , valor )
     VALUES ( 2008
          , 33
          , 'limites'
          , ''
          );

INSERT INTO administracao.relatorio
         ( cod_gestao
         , cod_modulo
         , cod_relatorio
         , nom_relatorio
         , arquivo )
    VALUES ( 5
         , 33
         , 3
         , 'Remissão Automática'
         , 'remissaoAutomatica.rptdesign'
         );

----------------
-- Ticket #11559
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$

DECLARE

   varAux     VARCHAR;

BEGIN

   SELECT valor
     INTO varAux
     FROM administracao.configuracao
    WHERE exercicio = '2008'
      AND parametro = 'cnpj'
      AND valor     = '13805528000180'
       OR valor     = '94068418000184';

   IF NOT FOUND THEN

        CREATE TABLE divida.parcelamento_cancelamento(
            num_parcelamento        INTEGER             NOT NULL,
            numcgm                  INTEGER             NOT NULL,
            motivo                  TEXT                NOT NULL,
            timestamp               TIMESTAMP           NOT NULL    DEFAULT ('now'::text)::timestamp(3) with time zone,
            CONSTRAINT pk_parcelamento_cancelamento     PRIMARY KEY                     (num_parcelamento),
            CONSTRAINT fk_parcelamento_cancelamento_1   FOREIGN KEY                     (num_parcelamento)
                                                        REFERENCES divida.parcelamento  (num_parcelamento)
        );

        GRANT ALL ON divida.parcelamento_cancelamento TO GROUP urbem;

   END IF;

END;
$$ LANGUAGE 'plpgsql';

SELECT manutencao();
DROP FUNCTION manutencao();


----------------
-- Ticket #12096
----------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2314
          , 305
          , 'FLRelatorioFichaCadastral.php'
          , 'incluir'
          , 6
          , ''
          , 'Ficha Cadastral'
          );

INSERT INTO administracao.relatorio
         ( cod_gestao
         , cod_modulo
         , cod_relatorio
         , nom_relatorio
         , arquivo )
    VALUES ( 5 
         , 25
         , 1
         , 'Relatório de Ficha Cadastral'
         , 'fichaCadastral.rptdesign'
         );


----------------
-- Ticket #13326
----------------

ALTER TABLE arrecadacao.servico_sem_retencao ADD COLUMN valor_deducao_legal NUMERIC(14,2);

CREATE OR REPLACE FUNCTION vl_mercadoria( ) RETURNS INTEGER AS $$
DECLARE

    reRecord    RECORD;
    stSql       VARCHAR;
    inRetorno   INTEGER := 0;

BEGIN

    stSql := '    SELECT SSR.cod_atividade
                       , SSR.cod_servico
                       , SSR.inscricao_economica
                       , SSR.timestamp
                       , SSR.ocorrencia
                       , AN.valor_mercadoria
                    FROM arrecadacao.servico_sem_retencao               AS SSR
              INNER JOIN arrecadacao.nota_servico                       AS ANS
                      ON ANS.cod_atividade          = SSR.cod_atividade
                     AND ANS.cod_servico            = SSR.cod_servico
                     AND ANS.inscricao_economica    = SSR.inscricao_economica
                     AND ANS.timestamp              = SSR.timestamp
                     AND ANS.ocorrencia             = SSR.ocorrencia
              INNER JOIN arrecadacao.nota                               AS AN
                      ON AN.cod_nota                = ANS.cod_nota
             ';

    FOR reRecord IN EXECUTE stSql LOOP

        UPDATE arrecadacao.servico_sem_retencao
           SET valor_deducao_legal  = reRecord.valor_mercadoria
         WHERE cod_atividade        = reRecord.cod_atividade
           AND cod_servico          = reRecord.cod_servico
           AND inscricao_economica  = reRecord.inscricao_economica
           AND timestamp            = reRecord.timestamp
           AND ocorrencia           = reRecord.ocorrencia;

        inRetorno := inRetorno + 1;

    END LOOP;

    RETURN inRetorno;
END;

$$ LANGUAGE 'plpgsql';

SELECT          vl_mercadoria();
DROP FUNCTION   vl_mercadoria();

UPDATE arrecadacao.servico_sem_retencao SET valor_deducao_legal = 0.00 WHERE valor_deducao_legal IS NULL;

ALTER TABLE arrecadacao.servico_sem_retencao ALTER COLUMN valor_deducao_legal SET NOT NULL;

ALTER TABLE arrecadacao.nota DROP COLUMN valor_mercadoria;


----------------
-- Ticket #12556
----------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2315
          , 280
          , 'FLConsultarNotaAvulsa.php'
          , 'consultar'
          , 5
          , ''
          , 'Consulta de Nota Avulsa'
          );


----------------
-- Ticket #13121
----------------


--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
INSERT INTO administracao.funcao
                     (cod_modulo
                  , cod_biblioteca
                  , cod_funcao
                  , cod_tipo_retorno
                  , nom_funcao)
               VALUES (33,1,(select max(cod_funcao)+1 from administracao.funcao where cod_modulo = 33 and cod_biblioteca = 1),3,'regraReemissaoMata2008');
INSERT INTO administracao.funcao_externa
                  (cod_modulo
                  , cod_biblioteca
                  , cod_funcao
                  , comentario
                  , corpo_pl
                  , corpo_ln)
            VALUES (33,1,(select max(cod_funcao) from administracao.funcao where cod_modulo = 33 and cod_biblioteca = 1),'','FUNCTION regraReemissaoMata2008(INTEGER,NUMERIC) RETURNS BOOLEAN as \\'' 
DECLARE
INCODLANCAMENTO ALIAS FOR $1;
NUVALORCREDITO ALIAS FOR $2;

  BOPREDIAL BOOLEAN;
  BORETORNO BOOLEAN := TRUE;
  ININSCRICAOMUNICIPAL INTEGER;
  NUVALOR NUMERIC;
BEGIN
ININSCRICAOMUNICIPAL := ARRECADACAO.BUSCAINSCRICAOLANCAMENTO(  INCODLANCAMENTO  ); 
BOPREDIAL := ARRECADACAO.VERIFICAEDIFICACAOIMOVEL(  ININSCRICAOMUNICIPAL  ); 
IF   BOPREDIAL  =  TRUE THEN
NUVALOR := ARRECADACAO.BUSCAVALORCREDITOLANCAMENTO(  INCODLANCAMENTO , 3 , 1 , 1 , 1  ); 
IF   NUVALOR  >  NUVALORCREDITO THEN
    BORETORNO := FALSE;
END IF;
NUVALOR := ARRECADACAO.BUSCAVALORCREDITOLANCAMENTO(  INCODLANCAMENTO , 2 , 1 , 1 , 1  ); 
IF   NUVALOR  >  NUVALORCREDITO THEN
    BORETORNO := FALSE;
END IF;
NUVALOR := ARRECADACAO.BUSCAVALORCREDITOLANCAMENTO(  INCODLANCAMENTO , 14 , 1 , 2 , 1  ); 
IF   NUVALOR  >  NUVALORCREDITO THEN
    BORETORNO := FALSE;
END IF;
ELSE
    NUVALOR := ARRECADACAO.BUSCAVALORCREDITOLANCAMENTO(  INCODLANCAMENTO , 2 , 1 , 1 , 1  ); 
    IF   NUVALOR  >  NUVALORCREDITO THEN
        BORETORNO := FALSE;
    END IF;
    NUVALOR := ARRECADACAO.BUSCAVALORCREDITOLANCAMENTO(  INCODLANCAMENTO , 14 , 1 , 2 , 1  ); 
    IF   NUVALOR  >  NUVALORCREDITO THEN
        BORETORNO := FALSE;
    END IF;
    NUVALOR := ARRECADACAO.BUSCAVALORCREDITOLANCAMENTO(  INCODLANCAMENTO , 16 , 1 , 2 , 1  ); 
    IF   NUVALOR  >  NUVALORCREDITO THEN
        BORETORNO := FALSE;
    END IF;
END IF;
RETURN BORETORNO;
END;
 \\'' LANGUAGE \\''plpgsql\\''; 
','');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (33,1,(select max(cod_funcao) from administracao.funcao where cod_modulo = 33 and cod_biblioteca = 1),1,'0','#inInscricaoMunicipal <- arrecadacao.buscaInscricaoLancamento(  #inCodLancamento  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (33,1,(select max(cod_funcao) from administracao.funcao where cod_modulo = 33 and cod_biblioteca = 1),2,'0','#boPredial <- arrecadacao.verificaEdificacaoImovel(  #inInscricaoMunicipal  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (33,1,(select max(cod_funcao) from administracao.funcao where cod_modulo = 33 and cod_biblioteca = 1),3,'1','SE   #boPredial  =  VERDADEIRO ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (33,1,(select max(cod_funcao) from administracao.funcao where cod_modulo = 33 and cod_biblioteca = 1),4,'0','#nuValor <- arrecadacao.buscaValorCreditoLancamento(  #inCodLancamento , 3 , 1 , 1 , 1  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (33,1,(select max(cod_funcao) from administracao.funcao where cod_modulo = 33 and cod_biblioteca = 1),5,'1','SE   #nuValor  >  #nuValorCredito ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (33,1,(select max(cod_funcao) from administracao.funcao where cod_modulo = 33 and cod_biblioteca = 1),6,'1','#boRetorno <- FALSO;');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (33,1,(select max(cod_funcao) from administracao.funcao where cod_modulo = 33 and cod_biblioteca = 1),7,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (33,1,(select max(cod_funcao) from administracao.funcao where cod_modulo = 33 and cod_biblioteca = 1),8,'0','#nuValor <- arrecadacao.buscaValorCreditoLancamento(  #inCodLancamento , 2 , 1 , 1 , 1  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (33,1,(select max(cod_funcao) from administracao.funcao where cod_modulo = 33 and cod_biblioteca = 1),9,'1','SE   #nuValor  >  #nuValorCredito ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (33,1,(select max(cod_funcao) from administracao.funcao where cod_modulo = 33 and cod_biblioteca = 1),10,'1','#boRetorno <- FALSO;');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (33,1,(select max(cod_funcao) from administracao.funcao where cod_modulo = 33 and cod_biblioteca = 1),11,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (33,1,(select max(cod_funcao) from administracao.funcao where cod_modulo = 33 and cod_biblioteca = 1),12,'0','#nuValor <- arrecadacao.buscaValorCreditoLancamento(  #inCodLancamento , 14 , 1 , 2 , 1  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (33,1,(select max(cod_funcao) from administracao.funcao where cod_modulo = 33 and cod_biblioteca = 1),13,'1','SE   #nuValor  >  #nuValorCredito ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (33,1,(select max(cod_funcao) from administracao.funcao where cod_modulo = 33 and cod_biblioteca = 1),14,'1','#boRetorno <- FALSO;');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (33,1,(select max(cod_funcao) from administracao.funcao where cod_modulo = 33 and cod_biblioteca = 1),15,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (33,1,(select max(cod_funcao) from administracao.funcao where cod_modulo = 33 and cod_biblioteca = 1),16,'1','SENAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (33,1,(select max(cod_funcao) from administracao.funcao where cod_modulo = 33 and cod_biblioteca = 1),17,'1','#nuValor <- arrecadacao.buscaValorCreditoLancamento(  #inCodLancamento , 2 , 1 , 1 , 1  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (33,1,(select max(cod_funcao) from administracao.funcao where cod_modulo = 33 and cod_biblioteca = 1),18,'2','SE   #nuValor  >  #nuValorCredito ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (33,1,(select max(cod_funcao) from administracao.funcao where cod_modulo = 33 and cod_biblioteca = 1),19,'2','#boRetorno <- FALSO;');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (33,1,(select max(cod_funcao) from administracao.funcao where cod_modulo = 33 and cod_biblioteca = 1),20,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (33,1,(select max(cod_funcao) from administracao.funcao where cod_modulo = 33 and cod_biblioteca = 1),21,'1','#nuValor <- arrecadacao.buscaValorCreditoLancamento(  #inCodLancamento , 14 , 1 , 2 , 1  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (33,1,(select max(cod_funcao) from administracao.funcao where cod_modulo = 33 and cod_biblioteca = 1),22,'2','SE   #nuValor  >  #nuValorCredito ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (33,1,(select max(cod_funcao) from administracao.funcao where cod_modulo = 33 and cod_biblioteca = 1),23,'2','#boRetorno <- FALSO;');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (33,1,(select max(cod_funcao) from administracao.funcao where cod_modulo = 33 and cod_biblioteca = 1),24,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (33,1,(select max(cod_funcao) from administracao.funcao where cod_modulo = 33 and cod_biblioteca = 1),25,'1','#nuValor <- arrecadacao.buscaValorCreditoLancamento(  #inCodLancamento , 16 , 1 , 2 , 1  ); ');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (33,1,(select max(cod_funcao) from administracao.funcao where cod_modulo = 33 and cod_biblioteca = 1),26,'2','SE   #nuValor  >  #nuValorCredito ENTAO');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (33,1,(select max(cod_funcao) from administracao.funcao where cod_modulo = 33 and cod_biblioteca = 1),27,'2','#boRetorno <- FALSO;');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (33,1,(select max(cod_funcao) from administracao.funcao where cod_modulo = 33 and cod_biblioteca = 1),28,'1','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (33,1,(select max(cod_funcao) from administracao.funcao where cod_modulo = 33 and cod_biblioteca = 1),29,'0','FIMSE');
INSERT INTO administracao.corpo_funcao_externa
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_linha
                     , nivel
                     , linha)
               VALUES (33,1,(select max(cod_funcao) from administracao.funcao where cod_modulo = 33 and cod_biblioteca = 1),30,'0','RETORNA #boRetorno');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (33,1,(select max(cod_funcao) from administracao.funcao where cod_modulo = 33 and cod_biblioteca = 1),1,3,'boPredial','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (33,1,(select max(cod_funcao) from administracao.funcao where cod_modulo = 33 and cod_biblioteca = 1),2,3,'boRetorno','VERDADEIRO');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (33,1,(select max(cod_funcao) from administracao.funcao where cod_modulo = 33 and cod_biblioteca = 1),3,1,'inInscricaoMunicipal','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (33,1,(select max(cod_funcao) from administracao.funcao where cod_modulo = 33 and cod_biblioteca = 1),4,4,'nuValor','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (33,1,(select max(cod_funcao) from administracao.funcao where cod_modulo = 33 and cod_biblioteca = 1),5,1,'inCodLancamento','');
INSERT INTO administracao.variavel
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , cod_tipo
                     , nom_variavel
                     , valor_inicial)
               VALUES (33,1,(select max(cod_funcao) from administracao.funcao where cod_modulo = 33 and cod_biblioteca = 1),6,4,'nuValorCredito','');
INSERT INTO administracao.parametro
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , ordem)
               VALUES (33,1,(select max(cod_funcao) from administracao.funcao where cod_modulo = 33 and cod_biblioteca = 1),5,0);
INSERT INTO administracao.parametro
                     (cod_modulo
                     , cod_biblioteca
                     , cod_funcao
                     , cod_variavel
                     , ordem)
               VALUES (33,1,(select max(cod_funcao) from administracao.funcao where cod_modulo = 33 and cod_biblioteca = 1),6,1);


----------------
-- Ticket #13321
----------------

CREATE OR REPLACE FUNCTION arruma_competencia_arrecadacao() RETURNS BOOLEAN AS $$
DECLARE
  reRecord RECORD;
BEGIN
  FOR reRecord IN
    SELECT
           *
      FROM arrecadacao.cadastro_economico_faturamento
     WHERE length(competencia) = 6
  LOOP
    UPDATE arrecadacao.cadastro_economico_faturamento
       SET competencia = '0'||reRecord.competencia
     WHERE inscricao_economica = reRecord.inscricao_economica
       AND timestamp = reRecord.timestamp;
  END LOOP;
--raise notice 'DA COMMIT; ';
RETURN TRUE;
END
$$LANGUAGE 'plpgsql';

SELECT arruma_competencia_arrecadacao();
DROP FUNCTION arruma_competencia_arrecadacao();

-----------------------------------------------------------------------------------------------------------------------
-- AJUSTE DE MIGRACAO - PARCELAS CANCELADAS SEM ESTERM INSCRITAS EM DIVIDA - MATA DE SAO JOAO - COD_LANCAMENTO = 146703 
-----------------------------------------------------------------------------------------------------------------------

DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000445490 AND cod_motivo = 11 and timestamp ='2007-01-01 00:00:00' ;
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000445489 AND cod_motivo = 11 and timestamp ='2007-01-01 00:00:00' ;

DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000371575 AND cod_motivo = 11 and timestamp ='2007-01-01 00:00:00' ;
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000371574 AND cod_motivo = 11 and timestamp ='2007-01-01 00:00:00' ;

DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000288426 AND cod_motivo = 11 and timestamp = '2007-10-11 09:08:29.277765'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000317672 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000317673 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000317674 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000317806 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000317807 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000339565 AND cod_motivo = 11 and timestamp = '2007-10-11 09:08:29.277765'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000439887 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000439888 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000456440 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000456441 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000456442 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000456443 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000456444 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000456445 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000457458 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000457459 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000457460 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000461421 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000461422 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000461423 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000461424 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000463729 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000463730 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000463731 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000463732 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000463733 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000463734 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000467468 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000467469 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000467470 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000467471 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000467472 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000468048 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000468049 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000468050 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000468051 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000468052 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000468399 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000468400 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000468401 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000468402 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000468403 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000468404 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000468405 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000468406 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000468407 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000468408 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000470497 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000470498 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000470499 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000470500 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000470501 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000509527 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000509528 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000509562 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000509563 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000509564 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000509565 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000509566 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000509576 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000509577 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000509597 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000509598 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000509726 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000509727 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000534117 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000534118 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000534119 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000534120 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000535615 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000535616 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000535617 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000535618 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000535642 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000535643 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000535644 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000535645 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000535646 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000536544 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000536545 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000536546 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000537815 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000537816 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000537817 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000537818 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000537819 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000537820 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000537821 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000537822 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000538267 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000538268 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000538269 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000538270 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000538271 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000539627 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000539628 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000539629 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000539630 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000539631 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000539632 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000541661 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000541662 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000541663 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000541664 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000545931 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000545932 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000545933 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000545934 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000545935 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000545936 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000547842 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000547843 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000547844 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000547845 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000547846 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000551044 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000551045 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000551046 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000551047 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000551048 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000551049 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000551076 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000551077 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000551078 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000551079 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000553623 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000553624 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000553625 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000553626 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000553627 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000554042 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000554043 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000554044 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000554045 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000554046 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000554047 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000560350 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000560351 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000560399 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000560400 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000560401 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000560402 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000560403 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000560404 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000574222 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 
DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = 99990000000574223 AND cod_motivo = 11 and timestamp = '2007-01-01 00:00:00'; 


-------------------------------------------------
-- ALTERACAO P/ BUFFERS TEXTO - TAMANHO DE STRING - Fabio - 20080909
-------------------------------------------------

SELECT removertodosbuffers();
ALTER TABLE administracao.buffers_texto DROP COLUMN valor;
ALTER TABLE administracao.buffers_texto ADD  COLUMN valor varchar(200) NOT NULL;
