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
* $Revision: 59612 $
* $Name$
* $Author:  $
* $Date:  $
*
* Versão 006.
*/

----------------
-- Ticket #12925
----------------

DELETE FROM administracao.auditoria
 WHERE cod_acao = 454;

DELETE FROM administracao.permissao
 WHERE cod_acao = 454;

DELETE FROM administracao.acao
 WHERE cod_acao = 454;

DELETE FROM administracao.auditoria
 WHERE cod_acao = 456;

DELETE FROM administracao.permissao
 WHERE cod_acao = 456;

DELETE FROM administracao.acao
 WHERE cod_acao = 456;


----------------
-- Ticket #12913
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
      AND valor     = '13805528000180';

   IF FOUND THEN

        INSERT INTO arrecadacao.modelo_carne VALUES (9, 'Carne I.P.T.U. Complementar 2008', 'RCarneIPTUComplementarMataSaoJoao2008.class.php');
        INSERT INTO arrecadacao.acao_modelo_carne VALUES (9, 963);
        INSERT INTO arrecadacao.acao_modelo_carne VALUES (9, 964);
        INSERT INTO arrecadacao.acao_modelo_carne VALUES (9, 978);
        INSERT INTO arrecadacao.acao_modelo_carne VALUES (9, 979);

   END IF;

END;

$$ LANGUAGE 'plpgsql';
SELECT manutencao();
DROP FUNCTION manutencao();


----------------
-- Recriar Views
----------------

CREATE OR REPLACE VIEW economico.vw_licenca_ativa AS
   SELECT DISTINCT ON (lc.cod_licenca, lc.exercicio)
        lc.cod_licenca,
        lc.exercicio,
        lc.dt_inicio,
        lc.dt_termino,
        pl.cod_processo,
        tld.nom_tipo,
        pl.exercicio_processo,
         CASE
             WHEN lca.inscricao_economica::character varying IS NOT NULL THEN 'Atividade'::text
             WHEN lce.inscricao_economica::character varying IS NOT NULL THEN 'Especial'::text
             WHEN lcd.numcgm::character varying IS NOT NULL THEN 'Diversa'::text
             ELSE NULL::text
         END AS especie_licenca,
         lcd.cod_tipo AS cod_tipo_diversa,
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
         END AS numcgm,
         cgm.nom_cgm
    FROM
        economico.licenca lc

    LEFT JOIN (
        SELECT
            bl.cod_licenca,
            bl.exercicio,
            bl.dt_inicio,
            bl.dt_termino,
            bl.cod_tipo,
            bl."timestamp",
            bl.motivo

        FROM
            economico.baixa_licenca bl, (
                SELECT
                    baixa_licenca.cod_licenca,
                    max(baixa_licenca."timestamp") AS "timestamp"
                FROM
                    economico.baixa_licenca
                GROUP BY
                    baixa_licenca.cod_licenca
            ) ml
        WHERE
            bl.cod_licenca = ml.cod_licenca
            AND bl."timestamp" = ml."timestamp"
    ) bl
    ON
        lc.cod_licenca = bl.cod_licenca
        AND lc.exercicio = bl.exercicio
    LEFT JOIN economico.processo_licenca pl ON lc.cod_licenca = pl.cod_licenca AND lc.exercicio = pl.exercicio
    LEFT JOIN economico.licenca_atividade lca ON lca.cod_licenca = lc.cod_licenca AND lca.exercicio = lc.exercicio
    LEFT JOIN economico.licenca_especial lce ON lce.cod_licenca = lc.cod_licenca AND lce.exercicio = lc.exercicio
    LEFT JOIN economico.licenca_diversa lcd ON lcd.cod_licenca = lc.cod_licenca AND lcd.exercicio = lc.exercicio
    LEFT JOIN economico.tipo_licenca_diversa tld ON lcd.cod_tipo = tld.cod_tipo
    LEFT JOIN economico.cadastro_economico_empresa_fato ceef ON ceef.inscricao_economica = lca.inscricao_economica OR ceef.inscricao_economica = lce.inscricao_economica
    LEFT JOIN economico.cadastro_economico_empresa_direito ceed ON ceed.inscricao_economica = lca.inscricao_economica OR ceed.inscricao_economica = lce.inscricao_economica
    LEFT JOIN economico.cadastro_economico_autonomo cea ON cea.inscricao_economica = lca.inscricao_economica OR cea.inscricao_economica = lce.inscricao_economica
    LEFT JOIN sw_cgm cgm ON lcd.numcgm = cgm.numcgm OR cea.numcgm = cgm.numcgm OR ceef.numcgm = cgm.numcgm OR ceed.numcgm = cgm.numcgm
   WHERE

lc.dt_inicio <= now()::date
 AND CASE
     WHEN lc.dt_termino IS NOT NULL AND lc.dt_termino <= now()::date  THEN false
     ELSE true
 END

AND
 CASE
     WHEN bl.cod_licenca IS NOT NULL THEN
     CASE
         WHEN bl.cod_tipo = 2 THEN
         CASE
              WHEN bl.dt_termino IS NULL THEN FALSE
              WHEN bl.dt_termino IS NOT NULL AND bl.dt_termino > now()::date THEN FALSE
              ELSE TRUE
         END
         ELSE false
     END
     ELSE true
 END
 ORDER BY lc.cod_licenca;



CREATE OR REPLACE VIEW economico.vw_licenca_suspensa_ativa AS
 SELECT DISTINCT ON (lc.cod_licenca) lc.cod_licenca, lc.exercicio, lc.dt_inicio, lc.dt_termino, pl.cod_processo, pl.exercicio_processo,
        CASE
            WHEN lca.inscricao_economica::text::character varying IS NOT NULL THEN 'Atividade'::text
            WHEN lce.inscricao_economica::text::character varying IS NOT NULL THEN 'Especial'::text
            WHEN lcd.numcgm::text::character varying IS NOT NULL THEN 'Diversa'::text
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
        END AS numcgm
        , cgm.nom_cgm
        , pbl.cod_processo AS cod_processo_baixa
        , pbl.exercicio_processo AS exercicio_processo_baixa
        , bl.dt_inicio AS dt_susp_inicio
        , bl.dt_termino AS dt_susp_termino
        , bl.motivo
   FROM economico.licenca lc

   LEFT JOIN (
        SELECT
            bl.cod_licenca,
            bl.exercicio,
            bl.dt_inicio,
            bl.dt_termino,
            bl.cod_tipo,
            bl."timestamp",
            bl.motivo

        FROM
            economico.baixa_licenca bl, (
                SELECT
                    baixa_licenca.cod_licenca,
                    max(baixa_licenca."timestamp") AS "timestamp"
                FROM
                    economico.baixa_licenca
                GROUP BY
                    baixa_licenca.cod_licenca
            ) ml
        WHERE
            bl.cod_licenca = ml.cod_licenca
            AND bl."timestamp" = ml."timestamp"
    ) bl
   ON
      lc.cod_licenca = bl.cod_licenca
      AND lc.exercicio = bl.exercicio
   LEFT JOIN economico.processo_licenca pl ON lc.cod_licenca = pl.cod_licenca AND lc.exercicio = pl.exercicio
   LEFT JOIN economico.licenca_atividade lca ON lca.cod_licenca = lc.cod_licenca AND lca.exercicio = lc.exercicio
   LEFT JOIN economico.licenca_especial lce ON lce.cod_licenca = lc.cod_licenca AND lce.exercicio = lc.exercicio
   LEFT JOIN economico.licenca_diversa lcd ON lcd.cod_licenca = lc.cod_licenca AND lcd.exercicio = lc.exercicio
   LEFT JOIN economico.cadastro_economico_empresa_fato ceef ON ceef.inscricao_economica = lca.inscricao_economica OR ceef.inscricao_economica = lce.inscricao_economica
   LEFT JOIN economico.cadastro_economico_empresa_direito ceed ON ceed.inscricao_economica = lca.inscricao_economica OR ceed.inscricao_economica = lce.inscricao_economica
   LEFT JOIN economico.cadastro_economico_autonomo cea ON cea.inscricao_economica = lca.inscricao_economica OR cea.inscricao_economica = lce.inscricao_economica
   LEFT JOIN sw_cgm cgm ON lcd.numcgm = cgm.numcgm OR cea.numcgm = cgm.numcgm OR ceef.numcgm = cgm.numcgm OR ceed.numcgm = cgm.numcgm
   LEFT JOIN economico.processo_baixa_licenca pbl ON pbl.cod_licenca = lc.cod_licenca AND pbl.exercicio = lc.exercicio
  WHERE lc.dt_inicio <= now()::date AND
CASE
    WHEN lc.dt_termino IS NOT NULL THEN lc.dt_termino >= now()::date
    ELSE true
END AND
CASE
    WHEN bl.dt_termino IS NOT NULL THEN bl.dt_termino > now()::date
    ELSE true
END AND bl.cod_licenca IS NOT NULL AND bl.cod_tipo = 2
  ORDER BY lc.cod_licenca;


---------------------------------------------------------------------------
-- EXCLUINDO ACAO 1830 ('Extrato de debitos' no monetario > conta corrente)
---------------------------------------------------------------------------

DELETE FROM administracao.auditoria
 WHERE cod_acao = 1830;

DELETE FROM administracao.permissao
 WHERE cod_acao = 1830;

DELETE FROM administracao.acao
 WHERE cod_acao = 1830;


---------------------------------------------------------
-- CORRECAO DE VALORES LANCADOS - ESCRITURACAO DE RECEITA
---------------------------------------------------------

CREATE OR REPLACE FUNCTION correcao_escrituracao_valor_lancamento() RETURNS INTEGER AS $$

    DECLARE
        stLaco              VARCHAR;
        stLaco2             VARCHAR;
        stLaco3             VARCHAR;
        reRecord            RECORD;
        reRecord2           RECORD;
        reRecord3           RECORD;
        total               integer;
        codNota             integer;
        codRetencao         integer;
        flTotalnota         NUMERIC;
        flTotalgeralNota    NUMERIC;

    BEGIN

        stLaco := '
            SELECT DISTINCT
                servico_sem_retencao.*

            FROM
                arrecadacao.faturamento_servico

            INNER JOIN
                arrecadacao.servico_sem_retencao
            ON
                servico_sem_retencao.cod_servico = faturamento_servico.cod_servico
                AND servico_sem_retencao.inscricao_economica = faturamento_servico.inscricao_economica
                AND servico_sem_retencao.timestamp = faturamento_servico.timestamp
                AND servico_sem_retencao.ocorrencia = faturamento_servico.ocorrencia
                AND servico_sem_retencao.cod_atividade = faturamento_servico.cod_atividade

            LEFT JOIN
                arrecadacao.nota_servico
            ON
                servico_sem_retencao.cod_servico = nota_servico.cod_servico
                AND servico_sem_retencao.inscricao_economica = nota_servico.inscricao_economica
                AND servico_sem_retencao.timestamp = nota_servico.timestamp
                AND servico_sem_retencao.ocorrencia = nota_servico.ocorrencia
                AND servico_sem_retencao.cod_atividade = nota_servico.cod_atividade

            WHERE
                nota_servico.cod_servico IS NULL
        ';

        total := 0;
        FOR reRecord IN EXECUTE stLaco LOOP

            UPDATE
                arrecadacao.servico_sem_retencao
            SET
                valor_lancado = reRecord.valor_declarado - reRecord.valor_deducao
            WHERE
                cod_servico = reRecord.cod_servico
                AND inscricao_economica = reRecord.inscricao_economica
                AND timestamp = reRecord.timestamp
                AND ocorrencia = reRecord.ocorrencia
                AND cod_atividade = reRecord.cod_atividade;

            total := total + 1;
        END LOOP;

        stLaco := '
            SELECT DISTINCT
                nota_servico.cod_nota

            FROM
                arrecadacao.nota_servico

            INNER JOIN
                arrecadacao.servico_sem_retencao
            ON
                servico_sem_retencao.cod_servico = nota_servico.cod_servico
                AND servico_sem_retencao.inscricao_economica = nota_servico.inscricao_economica
                AND servico_sem_retencao.timestamp = nota_servico.timestamp
                AND servico_sem_retencao.ocorrencia = nota_servico.ocorrencia
                AND servico_sem_retencao.cod_atividade = nota_servico.cod_atividade
        ';

        
        FOR reRecord IN EXECUTE stLaco LOOP
             stLaco2 := '
                SELECT
                    nota_servico.*,
                    servico_sem_retencao.*

                FROM
                    arrecadacao.nota_servico

                INNER JOIN
                    arrecadacao.servico_sem_retencao
                ON
                    servico_sem_retencao.cod_servico = nota_servico.cod_servico
                    AND servico_sem_retencao.inscricao_economica = nota_servico.inscricao_economica
                    AND servico_sem_retencao.timestamp = nota_servico.timestamp
                    AND servico_sem_retencao.ocorrencia = nota_servico.ocorrencia
                    AND servico_sem_retencao.cod_atividade = nota_servico.cod_atividade

                WHERE
                    nota_servico.cod_nota = '||reRecord.cod_nota;

            codNota := reRecord.cod_nota;
            flTotalnota := 0.00;
            FOR reRecord2 IN EXECUTE stLaco2 LOOP
                flTotalnota := flTotalnota + ( reRecord2.valor_declarado - reRecord2.valor_deducao );

                UPDATE 
                    arrecadacao.servico_sem_retencao 
                SET 
                    valor_lancado = (reRecord2.valor_declarado - reRecord2.valor_deducao) 
                WHERE 
                    servico_sem_retencao.timestamp = reRecord2.timestamp
                    AND servico_sem_retencao.cod_servico = reRecord2.cod_servico
                    AND servico_sem_retencao.cod_atividade = reRecord2.cod_atividade
                    AND servico_sem_retencao.ocorrencia = reRecord2.ocorrencia
                    AND servico_sem_retencao.inscricao_economica = reRecord2.inscricao_economica;

            END LOOP;

            UPDATE 
                arrecadacao.nota
            SET 
                valor_nota = flTotalnota
            WHERE 
                cod_nota = codNota;

            total := total + 1;
        END LOOP;


        stLaco := '
            SELECT DISTINCT
                retencao_nota.cod_retencao

            FROM
                arrecadacao.retencao_nota
        ';

        FOR reRecord IN EXECUTE stLaco LOOP
            stLaco2 := '
                SELECT
                    retencao_nota.*

                FROM
                    arrecadacao.retencao_nota

                WHERE
                    retencao_nota.cod_retencao = '||reRecord.cod_retencao;

            codRetencao := reRecord.cod_retencao;
            flTotalgeralNota := 0.00;
            FOR reRecord2 IN EXECUTE stLaco2 LOOP
                stLaco3 := '
                    SELECT
                        retencao_servico.*

                    FROM
                        arrecadacao.retencao_servico

                    WHERE
                        retencao_servico.cod_retencao = '||reRecord2.cod_retencao||'
                        AND retencao_servico.cod_nota = '||reRecord2.cod_nota;

                codNota := reRecord2.cod_nota;

                flTotalnota := 0.00;
                FOR reRecord3 IN EXECUTE stLaco3 LOOP
                    flTotalnota := flTotalnota + ( reRecord3.valor_declarado - reRecord3.valor_deducao );
    
                    UPDATE 
                        arrecadacao.retencao_servico 
                    SET 
                        valor_lancado = (reRecord3.valor_declarado - reRecord3.valor_deducao) 
                    WHERE 
                        retencao_servico.timestamp = reRecord3.timestamp
                        AND retencao_servico.cod_retencao = reRecord3.cod_retencao
                        AND retencao_servico.num_servico = reRecord3.num_servico
                        AND retencao_servico.cod_nota = reRecord3.cod_nota
                        AND retencao_servico.inscricao_economica = reRecord3.inscricao_economica;
                END LOOP;

                UPDATE 
                    arrecadacao.retencao_nota
                SET 
                    valor_nota = flTotalnota
                WHERE 
                    cod_nota = codNota AND
                    cod_retencao = codRetencao;

                flTotalgeralNota := flTotalgeralNota + flTotalnota;
            END LOOP;

            UPDATE 
                arrecadacao.retencao_fonte
            SET 
                valor_retencao = flTotalgeralNota
            WHERE 
                cod_retencao = codRetencao;

            total := total + 1;
        END LOOP;

        RETURN total;

    END;
$$ LANGUAGE 'plpgsql';

SELECT correcao_escrituracao_valor_lancamento();
DROP FUNCTION correcao_escrituracao_valor_lancamento();
