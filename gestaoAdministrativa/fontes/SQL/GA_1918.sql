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
* $Id: GA_1917.sql 36403 2008-12-10 16:30:40Z fabio $
*
* Versão 1.91.8
*/

----------------
-- Ticket #12563
----------------

ALTER TABLE organograma.organograma ADD COLUMN   ativo BOOLEAN;
UPDATE      organograma.organograma SET          ativo = FALSE;

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE
    inOrganograma   INTEGER;
BEGIN

    SELECT cod_organograma
      INTO inOrganograma
      FROM organograma.organograma
     WHERE implantacao = ( SELECT MAX(implantacao)
                             FROM organograma.organograma
                            WHERE implantacao <= CAST(now() AS DATE)
                         );

    UPDATE organograma.organograma
       SET ativo           = TRUE
     WHERE cod_organograma = inOrganograma;

END;
$$ LANGUAGE 'plpgsql';

SELECT manutencao();
DROP FUNCTION manutencao();

ALTER TABLE organograma.organograma ALTER COLUMN ativo SET NOT NULL;
ALTER TABLE organograma.organograma ALTER COLUMN ativo SET DEFAULT TRUE;


----------------------------------------------------------------------------------
-- ALTERACAO P/ IMPEDIR DUPLICIDADE DE cod_orgao NA TABELA organograma.orgao_nivel
-- TONISMAR E GELSON - 20081203 --------------------------------------------------

CREATE OR REPLACE FUNCTION organograma.unicidade_orgao_nivel( ) RETURNS TRIGGER AS $$
DECLARE

BEGIN

    PERFORM 1
       FROM organograma.orgao_nivel
      WHERE cod_orgao = NEW.cod_orgao
        AND cod_nivel = NEW.cod_nivel;

    IF FOUND THEN
        RAISE EXCEPTION 'Órgão já em uso no sistema. Contate suporte!';    
    ELSE
        RETURN NEW;
    END IF;

END;
$$ LANGUAGE 'plpgsql';

CREATE TRIGGER tr_restringe_orgao_duplicado BEFORE INSERT OR UPDATE ON organograma.orgao_nivel FOR EACH ROW EXECUTE PROCEDURE organograma.unicidade_orgao_nivel();



----------------
-- Ticket #12563
----------------

CREATE TABLE organograma.orgao_descricao (
    cod_orgao       INTEGER         NOT NULL,
    timestamp       TIMESTAMP       NOT NULL DEFAULT ('now'::text)::timestamp(3) with time zone,
    descricao       VARCHAR(100)    NOT NULL,
    CONSTRAINT pk_orgao_descricao    PRIMARY KEY                     (cod_orgao, timestamp),
    CONSTRAINT fk_orgao_descricao_1  FOREIGN KEY                     (cod_orgao)
                                     REFERENCES organograma.orgao    (cod_orgao)
);

GRANT ALL ON organograma.orgao_descricao TO GROUP urbem;

INSERT
  INTO organograma.orgao_descricao
     ( cod_orgao
     , timestamp
     , descricao )
SELECT cod_orgao
     , criacao::timestamp(3)
     , descricao
  FROM organograma.orgao;

----------------
-- Ticket #12663
----------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2426
          , 170
          , 'FMConfigurarOrganograma.php'
          , 'configurar'
          , 0
          , ''
          , 'Configuração'
          );


----------------------
-- DE-PARA ORGANOGRAMA
----------------------

CREATE TABLE organograma.de_para_setor (
    ano_exercicio           CHAR(4)     NOT NULL,
    cod_orgao               INTEGER     NOT NULL,
    cod_unidade             INTEGER     NOT NULL,
    cod_departamento        INTEGER     NOT NULL,
    cod_setor               INTEGER     NOT NULL,
    cod_orgao_organograma   INTEGER             ,
    CONSTRAINT pk_de_para_setor     PRIMARY KEY                     (ano_exercicio, cod_orgao, cod_unidade, cod_departamento, cod_setor),
    CONSTRAINT fk_de_para_setor_1   FOREIGN KEY                     (ano_exercicio, cod_orgao, cod_unidade, cod_departamento, cod_setor)
                                    REFERENCES administracao.setor  (ano_exercicio, cod_orgao, cod_unidade, cod_departamento, cod_setor),
    CONSTRAINT fk_de_para_setor_2   FOREIGN KEY                     (cod_orgao_organograma)
                                    REFERENCES organograma.orgao    (cod_orgao)
);

GRANT ALL ON organograma.de_para_setor TO GROUP urbem;

    INSERT
      INTO organograma.de_para_setor
         ( cod_orgao       
         , cod_unidade     
         , cod_departamento
         , cod_setor
         , ano_exercicio
         )
    SELECT DISTINCT ON ( aset.cod_orgao
         , aset.cod_unidade
         , aset.cod_departamento
         , aset.cod_setor
           )
           aset.cod_orgao
         , aset.cod_unidade
         , aset.cod_departamento
         , aset.cod_setor
         , aset.ano_exercicio
      FROM administracao.setor              AS aset
 LEFT JOIN orcamento.orgao                  AS oorg
        ON aset.cod_orgao        = oorg.cod_orgao 
       AND aset.ano_exercicio    = oorg.ano_exercicio
 LEFT JOIN orcamento.unidade                AS ouni
        ON aset.cod_orgao        = ouni.cod_orgao
       AND aset.cod_unidade      = ouni.cod_unidade
       AND aset.ano_exercicio    = ouni.ano_exercicio
 LEFT JOIN administracao.comunicado         AS acom
        ON aset.cod_orgao        = acom.cod_orgao
       AND aset.cod_unidade      = acom.cod_unidade
       AND aset.cod_departamento = acom.cod_departamento
       AND aset.cod_setor        = acom.cod_setor
       AND aset.ano_exercicio    = acom.exercicio_setor
 LEFT JOIN administracao.usuario            AS ausu
        ON aset.cod_orgao        = ausu.cod_orgao
       AND aset.cod_unidade      = ausu.cod_unidade
       AND aset.cod_departamento = ausu.cod_departamento
       AND aset.cod_setor        = ausu.cod_setor
       AND aset.ano_exercicio    = ausu.ano_exercicio
 LEFT JOIN frota.terceiros_historico        AS fthi
        ON aset.cod_orgao        = fthi.cod_orgao
       AND aset.cod_unidade      = fthi.cod_unidade
       AND aset.cod_departamento = fthi.cod_departamento
       AND aset.cod_setor        = fthi.cod_setor
       AND aset.ano_exercicio    = fthi.ano_exercicio
 LEFT JOIN patrimonio.historico_bem         AS phbe
        ON aset.cod_orgao        = phbe.cod_orgao
       AND aset.cod_unidade      = phbe.cod_unidade
       AND aset.cod_departamento = phbe.cod_departamento
       AND aset.cod_setor        = phbe.cod_setor
       AND aset.ano_exercicio    = phbe.ano_exercicio;


CREATE TABLE organograma.de_para_local (
    ano_exercicio           CHAR(4)     NOT NULL,
    cod_orgao               INTEGER     NOT NULL,
    cod_unidade             INTEGER     NOT NULL,
    cod_departamento        INTEGER     NOT NULL,
    cod_setor               INTEGER     NOT NULL,
    cod_local               INTEGER     NOT NULL,
    cod_local_organograma   INTEGER             ,
    CONSTRAINT pk_de_para_local     PRIMARY KEY                     (ano_exercicio, cod_orgao, cod_unidade, cod_departamento, cod_setor, cod_local),
    CONSTRAINT fk_de_para_local_1   FOREIGN KEY                     (ano_exercicio, cod_orgao, cod_unidade, cod_departamento, cod_setor, cod_local)
                                    REFERENCES administracao.local  (ano_exercicio, cod_orgao, cod_unidade, cod_departamento, cod_setor, cod_local),
    CONSTRAINT fk_de_para_local_2   FOREIGN KEY                     (cod_local_organograma)
                                    REFERENCES organograma.local    (cod_local)
);

GRANT ALL ON organograma.de_para_local TO GROUP urbem;

    INSERT
      INTO organograma.de_para_local
         ( cod_orgao
         , cod_unidade
         , cod_departamento
         , cod_setor
         , cod_local
         , ano_exercicio
         )
    SELECT DISTINCT ON ( aloc.cod_orgao
         , aloc.cod_unidade
         , aloc.cod_departamento
         , aloc.cod_setor
         , aloc.cod_local
         )
           aloc.cod_orgao
         , aloc.cod_unidade
         , aloc.cod_departamento
         , aloc.cod_setor
         , aloc.cod_local
         , aloc.ano_exercicio
      FROM administracao.local              AS aloc
 LEFT JOIN administracao.impressora         AS aimp
        ON aloc.cod_orgao        = aimp.cod_orgao
       AND aloc.cod_unidade      = aimp.cod_unidade
       AND aloc.cod_departamento = aimp.cod_departamento
       AND aloc.cod_setor        = aimp.cod_setor
       AND aloc.cod_local        = aimp.cod_local
       AND aloc.ano_exercicio    = aimp.exercicio
 LEFT JOIN frota.terceiros_historico        AS fter
        ON aloc.cod_orgao        = fter.cod_orgao
       AND aloc.cod_unidade      = fter.cod_unidade
       AND aloc.cod_departamento = fter.cod_departamento
       AND aloc.cod_setor        = fter.cod_setor
       AND aloc.cod_local        = fter.cod_local
       AND aloc.ano_exercicio    = fter.ano_exercicio
 LEFT JOIN patrimonio.historico_bem         AS phis
        ON aloc.cod_orgao        = phis.cod_orgao
       AND aloc.cod_unidade      = phis.cod_unidade
       AND aloc.cod_departamento = phis.cod_departamento
       AND aloc.cod_setor        = phis.cod_setor
       AND aloc.cod_local        = phis.cod_local
       AND aloc.ano_exercicio    = phis.ano_exercicio;


----------------------------------
-- Inserção do órgão não informado
-- 20081219 ----------------------

CREATE OR REPLACE FUNCTION organograma.fn_insere_orgao_nao_informado() RETURNS BOOLEAN AS $$

DECLARE

    boSucesso        BOOLEAN := false;

    inCodOrganograma INTEGER;
    inCodOrgao       INTEGER;
    inCodOrgaoAux    INTEGER;
    inCodLogradouro  INTEGER;
    inCodCalendario  INTEGER;
    inCodLocal       INTEGER;

    reRegistro       RECORD;

    stNomLogradouro  VARCHAR;
    stDataCriacao    DATE;

    stSQL            VARCHAR := '';

BEGIN

    -- Recupera o Organograma Ativo.
    SELECT cod_organograma INTO inCodOrganograma FROM organograma.organograma WHERE ativo = true;

    -- Recupera o cod_orgao auxiliar para buscar informações como Calendário e Norma.
    SELECT cod_orgao INTO inCodOrgaoAux FROM organograma.orgao_nivel WHERE orgao_nivel.cod_organograma = inCodOrganograma;

    -- Recupera o cod_calendario para ser inserido o novo órgão.
    SELECT cod_calendar INTO inCodCalendario FROM organograma.orgao WHERE cod_orgao = inCodOrgaoAux;

    -- Recupera o maior cod_orgao.
    SELECT MAX(cod_orgao)+1 INTO inCodOrgao FROM organograma.orgao;

    -- Recupera a data para ser inserido no novo órgão.
    SELECT CURRENT_DATE INTO stDataCriacao;

    -- INSERE o novo órgão (Não informado) para atualizar a tabela de_para_setor com o antigo setor (Não informado).
    INSERT INTO organograma.orgao
                (cod_orgao, num_cgm_pf, cod_calendar, cod_norma, descricao, criacao)
         VALUES
                (inCodOrgao, 0, inCodCalendario, 0, 'Não Informado', stDataCriacao);

    -- INSERE o nome do novo órgão em organograma.orgao_descricao
    INSERT INTO organograma.orgao_descricao
                (cod_orgao, timestamp, descricao)
         VALUES
                (inCodOrgao, stDataCriacao, 'Não Informado');

    -- Recupera o nro de níveis do Organograma.
    stSql := 'SELECT * FROM organograma.nivel WHERE cod_organograma = '||inCodOrganograma;

    FOR reRegistro IN EXECUTE stSql LOOP

        INSERT
          INTO organograma.orgao_nivel
             (   cod_orgao
               , cod_nivel
               , cod_organograma
               , valor
             )
        VALUES
             (   inCodOrgao
               , reRegistro.cod_nivel
               , reRegistro.cod_organograma
               , '0'
             );

    END LOOP;

    -- Recupera o cod_logradouro para futura inserção na organograma.local.
    SELECT MIN(cod_logradouro) INTO inCodLogradouro FROM organograma.local;

    -- Recupera o maior cod_local.
    SELECT MAX(cod_local)+1 INTO inCodLocal FROM organograma.local;

    -- INSERE o novo Local.
    INSERT INTO organograma.local
                (cod_local, cod_logradouro, dificil_acesso, insalubre, descricao)
         VALUES
                (inCodLocal, inCodLogradouro, false, false, 'Não Informado');

    -- ATUALIZAÇÃO NAS TABELAS DE-PARA.

    -- ATUALIZA a tabela de_para_setor com o novo Órgão cadastrado (Não Informado) para o antigo setor (Não informado).
    UPDATE  organograma.de_para_setor
       SET  cod_orgao_organograma = inCodOrgao
     WHERE  ano_exercicio    = '0000'
       AND  cod_orgao        = 0
       AND  cod_unidade      = 0
       AND  cod_departamento = 0
       AND  cod_setor        = 0;

    -- ATUALIZA a tabela de_para_local com o novo Local cadastrado (Não Informado) para o antigo local (Não informado).
    UPDATE  organograma.de_para_local
       SET  cod_local_organograma = inCodLocal
     WHERE  ano_exercicio    = '0000'
       AND  cod_orgao        = 0
       AND  cod_unidade      = 0
       AND  cod_departamento = 0
       AND  cod_setor        = 0
       AND  cod_local        = 0;

    IF (inCodLocal > 0 AND inCodOrgao > 0) THEN
        boSucesso := true;
    END IF;

RETURN boSucesso;

END;

$$ LANGUAGE 'plpgsql';

SELECT        organograma.fn_insere_orgao_nao_informado();
DROP FUNCTION organograma.fn_insere_orgao_nao_informado();
