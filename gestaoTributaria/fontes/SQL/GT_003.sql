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
* Versão 003.
*/

--------------
-- Ticket #12498  e Ticket #12542
--------------

INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     VALUES (2240
          , 225
          , 'FLManterNotaAvulsa.php'
          , 'incluir'
          , 7
          , ''
          , 'Emitir Nota Avulsa');

INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     VALUES (2241
          , 225
          , 'FLAnularNotaAvulsa.php'
          , 'anular'
          , 8
          , ''
          , 'Anular Nota Avulsa');

-- Função notaAvulsa

INSERT INTO administracao.funcao
                      (cod_modulo
                   , cod_biblioteca
                   , cod_funcao
                   , cod_tipo_retorno
                   , nom_funcao)
                VALUES (25,1,(select max(cod_funcao) + 1 from administracao.funcao),4,'notaAvulsa');
 INSERT INTO administracao.funcao_externa
                   (cod_modulo
                   , cod_biblioteca
                   , cod_funcao
                   , comentario
                   , corpo_pl
                   , corpo_ln)
             VALUES (25,1,(select max(cod_funcao) from administracao.funcao),'Cálculo da Nota Avulsa','FUNCTION notaAvulsa() RETURNS NUMERIC as \\''
 DECLARE

   inExercicio INTEGER;
   nuValor NUMERIC;
 BEGIN
 inExercicio := RecuperarBufferInteiro(  \\''\\''inExercicio\\''\\''  );
 nuValor := arrecadacao.fn_busca_tabela_conversao(  0 , inExercicio , \\''\\''valor\\''\\'' , \\''\\''\\''\\'' , \\''\\''\\''\\'' , \\''\\''\\''\\''  );
 RETURN nuValor;
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
                VALUES (25,1,(select max(cod_funcao) from administracao.funcao),1,'0','#inExercicio <- RecuperarBufferInteiro(  "inExercicio"  ); ');
 INSERT INTO administracao.corpo_funcao_externa
                      (cod_modulo
                      , cod_biblioteca
                      , cod_funcao
                      , cod_linha
                      , nivel
                      , linha)
                VALUES (25,1,(select max(cod_funcao) from administracao.funcao),2,'0','#nuValor <- arrecadacao.fn_busca_tabela_conversao(  0 , #inExercicio , "valor" , VAZIO , VAZIO , VAZIO  ); ');
 INSERT INTO administracao.corpo_funcao_externa
                      (cod_modulo
                      , cod_biblioteca
                      , cod_funcao
                      , cod_linha
                      , nivel
                      , linha)
                VALUES (25,1,(select max(cod_funcao) from administracao.funcao),3,'0','RETORNA #nuValor');
 INSERT INTO administracao.variavel
                      (cod_modulo
                      , cod_biblioteca
                      , cod_funcao
                      , cod_variavel
                      , cod_tipo
                      , nom_variavel
                      , valor_inicial)
                VALUES (25,1,(select max(cod_funcao) from administracao.funcao),1,1,'inExercicio','');
 INSERT INTO administracao.variavel
                      (cod_modulo
                      , cod_biblioteca
                      , cod_funcao
                      , cod_variavel
                      , cod_tipo
                      , nom_variavel
                      , valor_inicial)
                VALUES (25,1,(select max(cod_funcao) from administracao.funcao),2,4,'nuValor','');

   INSERT INTO monetario.credito
               (cod_credito
             , cod_natureza
             , cod_genero
             , descricao_credito
             , cod_especie)
        VALUES (99
             , 1
             , 2
             ,'Nota Avulsa'
             , 1);

-- Configuração e fórmula de cálculo
INSERT INTO administracao.configuracao VALUES (2008,25,'nota_avulsa','');

INSERT INTO arrecadacao.arrecadacao_modulos VALUES (25);

INSERT INTO arrecadacao.tabela_conversao VALUES (0,'2008',25,'Nota Avulsa','Valor nota avulsa','','','');

INSERT INTO arrecadacao.tabela_conversao_valores VALUES (0,'2008','valor','','','','0.00');

INSERT INTO arrecadacao.parametro_calculo VALUES ( 99,1,2,1,1,(select cod_funcao from administracao.funcao where nom_funcao = 'notaAvulsa'),('now'::text)::timestamp(3) with time zone,25,1,'');

-- Documentos

CREATE OR REPLACE FUNCTION manutencao() RETURNS boolean as $$

DECLARE

   varCNPJ       VARCHAR;

BEGIN

   Select valor Into varCNPJ
     From administracao.configuracao
    Where exercicio = '2008'
      and parametro = 'cnpj';

   IF varCNPJ = '13646005000138' THEN

      INSERT INTO administracao.arquivos_documento
         VALUES ( (SELECT MAX(cod_arquivo)+1
                     from administracao.arquivos_documento)
               , 'NotaFiscalAvulsaAlagoinhas.odt'
               , '60315c6ab27d6b2b0a2cd5d946867de4'
               , true );

      INSERT INTO administracao.modelo_documento
         VALUES ( (SELECT MAX(cod_documento)+1
                     from administracao.modelo_documento)
               , 'Nota fiscal avulsa'
               , 'notafiscalavulsaalagoinhas.agt'
               , 2);

   ELSEIF varCNPJ = '01613321000124' THEN

      INSERT INTO administracao.arquivos_documento
         VALUES ( (SELECT MAX(cod_arquivo)+1
                     from administracao.arquivos_documento)
               , 'NotaFiscalAvulsaCanaa.odt'
               , '60315c6ab27d6b2b0a2cd5d946867de4'
               , true );

      INSERT INTO administracao.modelo_documento
         VALUES ( (SELECT MAX(cod_documento)+1
                     from administracao.modelo_documento)
               , 'Nota fiscal avulsa'
               , 'notafiscalavulsacanaa.agt'
               , 2);

      INSERT INTO arrecadacao.acao_modelo_carne VALUES (1,2240);

   ELSEIF varCNPJ = '94068418000184' THEN

      INSERT INTO administracao.arquivos_documento
         VALUES ( (SELECT MAX(cod_arquivo)+1
                     from administracao.arquivos_documento)
               , 'NotaFiscalAvulsaMariana.odt'
               , '60315c6ab27d6b2b0a2cd5d946867de4'
               , true );

      INSERT INTO administracao.modelo_documento
         VALUES ( (SELECT MAX(cod_documento)+1
                     from administracao.modelo_documento)
               , 'Nota fiscal avulsa'
               , 'notafiscalavulsamariana.agt'
               , 2);

       INSERT INTO arrecadacao.acao_modelo_carne VALUES (1,2240);

   ELSEIF varCNPJ = '13805528000180' THEN

      INSERT INTO administracao.arquivos_documento
         VALUES ( (SELECT MAX(cod_arquivo)+1
                     from administracao.arquivos_documento)
               , 'NotaFiscalAvulsaMata.odt'
               , '60315c6ab27d6b2b0a2cd5d946867de4'
               , true );

      INSERT INTO administracao.modelo_documento
         VALUES ( (SELECT MAX(cod_documento)+1
                     from administracao.modelo_documento)
               , 'Nota fiscal avulsa'
               , 'notafiscalavulsamata.agt'
               , 2);

   INSERT INTO arrecadacao.acao_modelo_carne VALUES (3,2240);

   ELSE

      INSERT INTO administracao.arquivos_documento
         VALUES ( (SELECT MAX(cod_arquivo)+1
                     from administracao.arquivos_documento)
               , 'NotaFiscalAvulsa.odt'
               , '60315c6ab27d6b2b0a2cd5d946867de4'
               , true );

      INSERT INTO administracao.modelo_documento
         VALUES ( (SELECT MAX(cod_documento)+1
                     from administracao.modelo_documento)
               , 'Nota fiscal avulsa'
               , 'notafiscalavulsa.agt'
               , 2);

   END IF;

RETURN TRUE;

END;

$$ LANGUAGE 'plpgsql';

SELECT manutencao();
DROP FUNCTION manutencao();

   INSERT INTO administracao.modelo_arquivos_documento
      VALUES (2240
            , (SELECT MAX(cod_documento)
                  from administracao.modelo_documento)
            , (SELECT MAX(cod_arquivo)
                  from administracao.arquivos_documento)
            , true
            , true
            , 2);


CREATE TABLE arrecadacao.nota_fiscal (
    cod_nota            INTEGER             NOT NULL,
    nro_serie           VARCHAR(10)         NOT NULL,
    nro_nota            INTEGER             NOT NULL,
    CONSTRAINT pk_nota_fiscal PRIMARY KEY (cod_nota),
    CONSTRAINT fk_nota_fiscal_1 FOREIGN KEY (cod_nota) REFERENCES arrecadacao.nota (cod_nota)
);

CREATE TABLE arrecadacao.nota_avulsa (
    cod_nota            INTEGER             NOT NULL,
    numcgm_tomador      INTEGER             NOT NULL,
    numcgm_usuario      INTEGER             NOT NULL,
    nro_serie           VARCHAR(10)         NOT NULL,
    nro_nota            INTEGER             NOT NULL,
    exercicio           CHAR(4)             NOT NULL,
    CONSTRAINT pk_nota_avulsa PRIMARY KEY(cod_nota),
    CONSTRAINT uk_nota_avulsa UNIQUE(nro_serie,nro_nota,exercicio),
    CONSTRAINT fk_nota_avulsa_1 FOREIGN KEY(cod_nota) REFERENCES arrecadacao.nota(cod_nota),
    CONSTRAINT fk_nota_avulsa_2 FOREIGN KEY(numcgm_tomador) REFERENCES sw_cgm(numcgm),
    CONSTRAINT fk_nota_avulsa_3 FOREIGN KEY(numcgm_usuario) REFERENCES administracao.usuario(numcgm)
);

CREATE TABLE arrecadacao.nota_avulsa_cancelada (
    cod_nota            INTEGER NOT NULL,
    numcgm_usuario      INTEGER NOT NULL,
    dt_cancelamento     DATE    NOT NULL,
    observacao          TEXT    NOT NULL,
    CONSTRAINT pk_nota_avulsa_cancelada PRIMARY KEY(cod_nota),
    CONSTRAINT fk_nota_avulsa_cancelada_1 FOREIGN KEY(cod_nota) REFERENCES arrecadacao.nota_avulsa  (cod_nota),
    CONSTRAINT fk_nota_avulsa_cancelada_2 FOREIGN KEY(numcgm_usuario) REFERENCES administracao.usuario(numcgm)
);

INSERT INTO arrecadacao.nota_fiscal
     SELECT cod_nota
          , nro_serie
          , nro_nota
       FROM arrecadacao.nota;

ALTER TABLE arrecadacao.nota DROP COLUMN nro_serie;
ALTER TABLE arrecadacao.nota DROP COLUMN nro_nota;

GRANT ALL ON TABLE arrecadacao.nota_fiscal           TO GROUP urbem;
GRANT ALL ON TABLE arrecadacao.nota_avulsa           TO GROUP urbem;
GRANT ALL ON TABLE arrecadacao.nota_avulsa_cancelada TO GROUP urbem;

INSERT INTO arrecadacao.motivo_devolucao VALUES (99,'Nota Avulsa anulada','Anulada');

---------------
-- Ticket #12515
---------------

CREATE OR REPLACE VIEW economico.vw_licenca_ativa AS
   SELECT DISTINCT ON (lc.cod_licenca) lc.cod_licenca, lc.exercicio, lc.dt_inicio, lc.dt_termino, pl.cod_processo, tld.nom_tipo, pl.exercicio_processo,
         CASE
             WHEN lca.inscricao_economica::character varying IS NOT NULL THEN 'Atividade'::text
             WHEN lce.inscricao_economica::character varying IS NOT NULL THEN 'Especial'::text
             WHEN lcd.numcgm::character varying IS NOT NULL THEN 'Diversa'::text
             ELSE NULL::text
         END AS especie_licenca, lcd.cod_tipo AS cod_tipo_diversa,
         CASE
             WHEN lca.inscricao_economica IS NOT NULL THEN lca.inscricao_economica
             WHEN lce.inscricao_economica IS NOT NULL THEN lce.inscricao_economica
             ELSE NULL::integer
         END AS inscricao_economica,
         CASE
             WHEN ceef.inscricao_economica IS NOT NULL THEN ceef.numcgm
             WHEN ceed.inscricao_economica IS NOT NULL THEN ceed.numcgm
             WHEN cea.inscricao_economica IS NOT NULL THEN cea.numcgm
             ELSE lcd.numcgm
         END AS numcgm, cgm.nom_cgm
    FROM economico.licenca lc
    LEFT JOIN ( SELECT bl.cod_licenca, bl.exercicio, bl.dt_inicio, bl.dt_termino, bl.cod_tipo, bl."timestamp", bl.motivo
            FROM economico.baixa_licenca bl, ( SELECT baixa_licenca.cod_licenca, max(baixa_licenca."timestamp") AS "timestamp"
                    FROM economico.baixa_licenca
                   GROUP BY baixa_licenca.cod_licenca) ml
           WHERE bl.cod_licenca = ml.cod_licenca AND bl."timestamp" = ml."timestamp") bl ON lc.cod_licenca = bl.cod_licenca AND lc.exercicio = bl.exercicio
    LEFT JOIN economico.processo_licenca pl ON lc.cod_licenca = pl.cod_licenca AND lc.exercicio = pl.exercicio
    LEFT JOIN economico.licenca_atividade lca ON lca.cod_licenca = lc.cod_licenca AND lca.exercicio = lc.exercicio
    LEFT JOIN economico.licenca_especial lce ON lce.cod_licenca = lc.cod_licenca AND lce.exercicio = lc.exercicio
    LEFT JOIN economico.licenca_diversa lcd ON lcd.cod_licenca = lc.cod_licenca AND lcd.exercicio = lc.exercicio
    LEFT JOIN economico.tipo_licenca_diversa tld ON lcd.cod_tipo = tld.cod_tipo
    LEFT JOIN economico.cadastro_economico_empresa_fato ceef ON ceef.inscricao_economica = lca.inscricao_economica OR ceef.inscricao_economica = lce.inscricao_economica
    LEFT JOIN economico.cadastro_economico_empresa_direito ceed ON ceed.inscricao_economica = lca.inscricao_economica OR ceed.inscricao_economica = lce.inscricao_economica
    LEFT JOIN economico.cadastro_economico_autonomo cea ON cea.inscricao_economica = lca.inscricao_economica OR cea.inscricao_economica = lce.inscricao_economica
    LEFT JOIN sw_cgm cgm ON lcd.numcgm = cgm.numcgm OR cea.numcgm = cgm.numcgm OR ceef.numcgm = cgm.numcgm OR ceed.numcgm = cgm.numcgm
   WHERE lc.dt_inicio <= now()::date AND
 CASE
     WHEN lc.dt_termino IS NOT NULL AND lc.dt_termino >= now()::date  THEN false
     ELSE true
 END AND
 CASE
     WHEN bl.cod_licenca IS NOT NULL THEN
     CASE
         WHEN bl.cod_tipo = 2 THEN
         CASE
             WHEN bl.dt_termino IS NOT NULL AND bl.dt_termino <= now()::date THEN FALSE
             ELSE true
         END
         ELSE false
     END
     ELSE true
 END
   ORDER BY lc.cod_licenca;

