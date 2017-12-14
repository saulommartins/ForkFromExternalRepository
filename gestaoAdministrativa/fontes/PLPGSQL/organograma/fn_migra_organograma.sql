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
/* fn_migra_organograma
 *
 * Data de Criação : 29/12/2008


 * @author Analista : Gelson Wolowski Gonçalves
 * @author Desenvolvedor : Fábio Bertoldi

 * @package URBEM
 * @subpackage

 * $Id: fn_migra_organograma.sql 59612 2014-09-02 12:00:51Z gelson $
 */

CREATE OR REPLACE FUNCTION fn_migra_organograma( ) RETURNS BOOLEAN AS $$
DECLARE

    stSQL1                  VARCHAR;
    reRecord1               RECORD;
    crCursor1               REFCURSOR;

    stSQL2                  VARCHAR;
    reRecord2               RECORD;
    crCursor2               REFCURSOR;

    stSQL3                  VARCHAR;
    reRecord3               RECORD;
    crCursor3               REFCURSOR;

    boSucesso               BOOLEAN;
    boAbortar               BOOLEAN := FALSE;
    inCountSetor            INTEGER;
    inCountLocal            INTEGER;

BEGIN

    PERFORM 1
       FROM administracao.configuracao
      WHERE cod_modulo = 19
        AND exercicio  = '2009'
        AND parametro  = 'migra_organograma'
        AND valor      = 'true';

    IF NOT FOUND THEN

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
               AND aset.ano_exercicio    = phbe.ano_exercicio
        RIGHT JOIN (
                        SELECT sto.cod_orgao
                             , sto.cod_unidade
                             , sto.cod_departamento
                             , sto.cod_setor
                             , sto.ano_exercicio
                          FROM administracao.setor          AS sto
                     LEFT JOIN organograma.de_para_setor    AS dps
                            ON dps.cod_orgao        = sto.cod_orgao
                           AND dps.cod_unidade      = sto.cod_unidade
                           AND dps.cod_departamento = sto.cod_departamento
                           AND dps.cod_setor        = sto.cod_setor
                           AND dps.ano_exercicio    = sto.ano_exercicio
                         WHERE dps.cod_orgao        IS NULL
                           AND dps.cod_unidade      IS NULL
                           AND dps.cod_departamento IS NULL
                           AND dps.cod_setor        IS NULL
                           AND dps.ano_exercicio    IS NULL
                   )                                        AS RESTO
                ON aset.cod_orgao        = RESTO.cod_orgao
               AND aset.cod_unidade      = RESTO.cod_unidade
               AND aset.cod_departamento = RESTO.cod_departamento
               AND aset.cod_setor        = RESTO.cod_setor
               AND aset.ano_exercicio    = RESTO.ano_exercicio;

        GET DIAGNOSTICS inCountSetor = ROW_COUNT;

        IF inCountSetor > 0 THEN

            UPDATE administracao.configuracao
               SET valor     = 'false'
             WHERE parametro = 'migra_setor';

            boAbortar := TRUE;

        END IF;


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
              FROM administracao.local                      AS aloc
         LEFT JOIN administracao.impressora                 AS aimp
                ON aloc.cod_orgao        = aimp.cod_orgao
               AND aloc.cod_unidade      = aimp.cod_unidade
               AND aloc.cod_departamento = aimp.cod_departamento
               AND aloc.cod_setor        = aimp.cod_setor
               AND aloc.cod_local        = aimp.cod_local
               AND aloc.ano_exercicio    = aimp.exercicio
         LEFT JOIN frota.terceiros_historico                AS fter
                ON aloc.cod_orgao        = fter.cod_orgao
               AND aloc.cod_unidade      = fter.cod_unidade
               AND aloc.cod_departamento = fter.cod_departamento
               AND aloc.cod_setor        = fter.cod_setor
               AND aloc.cod_local        = fter.cod_local
               AND aloc.ano_exercicio    = fter.ano_exercicio
         LEFT JOIN patrimonio.historico_bem                 AS phis
                ON aloc.cod_orgao        = phis.cod_orgao
               AND aloc.cod_unidade      = phis.cod_unidade
               AND aloc.cod_departamento = phis.cod_departamento
               AND aloc.cod_setor        = phis.cod_setor
               AND aloc.cod_local        = phis.cod_local
               AND aloc.ano_exercicio    = phis.ano_exercicio
        RIGHT JOIN (
                        SELECT loc.cod_orgao
                             , loc.cod_unidade
                             , loc.cod_departamento
                             , loc.cod_setor
                             , loc.cod_local
                             , loc.ano_exercicio
                          FROM administracao.local          AS loc
                     LEFT JOIN organograma.de_para_local    AS dpl
                            ON dpl.cod_orgao        = loc.cod_orgao
                           AND dpl.cod_unidade      = loc.cod_unidade
                           AND dpl.cod_departamento = loc.cod_departamento
                           AND dpl.cod_setor        = loc.cod_setor
                           AND dpl.cod_local        = loc.cod_local
                           AND dpl.ano_exercicio    = loc.ano_exercicio
                         WHERE dpl.cod_orgao        IS NULL
                           AND dpl.cod_unidade      IS NULL
                           AND dpl.cod_departamento IS NULL
                           AND dpl.cod_setor        IS NULL
                           AND dpl.cod_local        IS NULL
                           AND dpl.ano_exercicio    IS NULL
                   )                                        AS RESTO
                ON aloc.cod_orgao        = RESTO.cod_orgao
               AND aloc.cod_unidade      = RESTO.cod_unidade
               AND aloc.cod_departamento = RESTO.cod_departamento
               AND aloc.cod_setor        = RESTO.cod_setor
               AND aloc.cod_local        = RESTO.cod_local
               AND aloc.ano_exercicio    = RESTO.ano_exercicio;

        GET DIAGNOSTICS inCountLocal = ROW_COUNT;

        IF inCountLocal > 0 THEN

            UPDATE administracao.configuracao
               SET valor     = 'false'
             WHERE parametro = 'migra_local';

            boAbortar := TRUE;

        END IF;

        IF boAbortar = FALSE THEN

                -- ADICIONANDO COLUNAS cod_orgao_organograma NAS TABELAS A SEREM CONVERTIDAS --
                -------------------------------------------------------------------------------
                ALTER TABLE administracao.comunicado    ADD COLUMN cod_orgao_organograma INTEGER;
                ALTER TABLE administracao.usuario       ADD COLUMN cod_orgao_organograma INTEGER;
                ALTER TABLE public.sw_andamento         ADD COLUMN cod_orgao_organograma INTEGER;
                ALTER TABLE public.sw_andamento_padrao  ADD COLUMN cod_orgao_organograma INTEGER;
                ALTER TABLE public.sw_ultimo_andamento  ADD COLUMN cod_orgao_organograma INTEGER;
                
                ALTER TABLE administracao.impressora    ADD COLUMN cod_orgao_organograma INTEGER;
                ALTER TABLE administracao.impressora    ADD COLUMN cod_local_organograma INTEGER;
                ALTER TABLE frota.terceiros_historico   ADD COLUMN cod_orgao_organograma INTEGER;
                ALTER TABLE frota.terceiros_historico   ADD COLUMN cod_local_organograma INTEGER;
                ALTER TABLE patrimonio.historico_bem    ADD COLUMN cod_orgao_organograma INTEGER;
                ALTER TABLE patrimonio.historico_bem    ADD COLUMN cod_local_organograma INTEGER;
                

                -- SETOR --
                -----------

                -- POVOAR ADMINISTRACAO.COMUNICADO
                ----------------------------------
                UPDATE administracao.comunicado
                   SET cod_orgao_organograma = de_para_setor.cod_orgao_organograma
                  FROM organograma.de_para_setor
                 WHERE comunicado.exercicio_setor       = de_para_setor.ano_exercicio
                   AND comunicado.cod_orgao             = de_para_setor.cod_orgao
                   AND comunicado.cod_unidade           = de_para_setor.cod_unidade
                   AND comunicado.cod_departamento      = de_para_setor.cod_departamento
                   AND comunicado.cod_setor             = de_para_setor.cod_setor;
        
        
                -- POVOAR ADMINISTRACAO.USUARIO
                -------------------------------
                UPDATE administracao.usuario
                   SET cod_orgao_organograma = de_para_setor.cod_orgao_organograma
                  FROM organograma.de_para_setor
--               WHERE usuario.ano_exercicio    = de_para_setor.ano_exercicio
                 WHERE usuario.cod_orgao        = de_para_setor.cod_orgao
                   AND usuario.cod_unidade      = de_para_setor.cod_unidade
                   AND usuario.cod_departamento = de_para_setor.cod_departamento
                   AND usuario.cod_setor        = de_para_setor.cod_setor;
        
        
                -- POVOAR SW_ANDAMENTO
                ----------------------
                DROP TRIGGER tr_atualiza_ultimo_andamento ON sw_andamento;
       

                UPDATE sw_andamento
                   SET cod_orgao_organograma = de_para_setor.cod_orgao_organograma
                  FROM organograma.de_para_setor
--               WHERE sw_andamento.ano_exercicio_setor = de_para_setor.ano_exercicio
                 WHERE sw_andamento.cod_orgao           = de_para_setor.cod_orgao
                   AND sw_andamento.cod_unidade         = de_para_setor.cod_unidade
                   AND sw_andamento.cod_departamento    = de_para_setor.cod_departamento
                   AND sw_andamento.cod_setor           = de_para_setor.cod_setor;
        

                UPDATE sw_ultimo_andamento
                   SET cod_orgao_organograma = sw_andamento.cod_orgao_organograma
                  FROM public.sw_andamento
--               WHERE sw_ultimo_andamento.ano_exercicio = sw_andamento.ano_exercicio
                 WHERE sw_ultimo_andamento.cod_processo  = sw_andamento.cod_processo
                   AND sw_ultimo_andamento.cod_andamento = sw_andamento.cod_andamento;
                    
        
                -- POVOAR SW_ANDAMENTO_PADRAO
                -----------------------------
                UPDATE sw_andamento_padrao
                   SET cod_orgao_organograma = de_para_setor.cod_orgao_organograma
                  FROM organograma.de_para_setor
--               WHERE sw_andamento_padrao.ano_exercicio    = de_para_setor.ano_exercicio
                 WHERE sw_andamento_padrao.cod_orgao        = de_para_setor.cod_orgao
                   AND sw_andamento_padrao.cod_unidade      = de_para_setor.cod_unidade
                   AND sw_andamento_padrao.cod_departamento = de_para_setor.cod_departamento
                   AND sw_andamento_padrao.cod_setor        = de_para_setor.cod_setor;



                -- LOCAL --
                -----------

                -- POVOAR ADMINISTRACAO.IMPRESSORA
                ----------------------------------
                UPDATE administracao.impressora
                   SET cod_orgao_organograma = de_para_setor.cod_orgao_organograma
                  FROM organograma.de_para_setor
                 WHERE impressora.exercicio        = de_para_setor.ano_exercicio
                   AND impressora.cod_orgao        = de_para_setor.cod_orgao
                   AND impressora.cod_unidade      = de_para_setor.cod_unidade
                   AND impressora.cod_departamento = de_para_setor.cod_departamento
                   AND impressora.cod_setor        = de_para_setor.cod_setor;
        
                UPDATE administracao.impressora
                   SET cod_local_organograma = de_para_local.cod_local_organograma
                  FROM organograma.de_para_local
                 WHERE impressora.exercicio        = de_para_local.ano_exercicio
                   AND impressora.cod_orgao        = de_para_local.cod_orgao
                   AND impressora.cod_unidade      = de_para_local.cod_unidade
                   AND impressora.cod_departamento = de_para_local.cod_departamento
                   AND impressora.cod_setor        = de_para_local.cod_setor
                   AND impressora.cod_local        = de_para_local.cod_local;
        
        
                -- POVOAR FROTA.TERCEIROS_HISTORICO
                -----------------------------------
                UPDATE frota.terceiros_historico
                   SET cod_orgao_organograma = de_para_setor.cod_orgao_organograma
                  FROM organograma.de_para_setor
--               WHERE terceiros_historico.ano_exercicio    = de_para_setor.ano_exercicio
                 WHERE terceiros_historico.cod_orgao        = de_para_setor.cod_orgao
                   AND terceiros_historico.cod_unidade      = de_para_setor.cod_unidade
                   AND terceiros_historico.cod_departamento = de_para_setor.cod_departamento
                   AND terceiros_historico.cod_setor        = de_para_setor.cod_setor;
        
                UPDATE frota.terceiros_historico
                   SET cod_local_organograma = de_para_local.cod_local_organograma
                  FROM organograma.de_para_local
--               WHERE terceiros_historico.ano_exercicio    = de_para_local.ano_exercicio
                 WHERE terceiros_historico.cod_orgao        = de_para_local.cod_orgao
                   AND terceiros_historico.cod_unidade      = de_para_local.cod_unidade
                   AND terceiros_historico.cod_departamento = de_para_local.cod_departamento
                   AND terceiros_historico.cod_setor        = de_para_local.cod_setor
                   AND terceiros_historico.cod_local        = de_para_local.cod_local;
        
        
                -- POVOAR PATRIMONIO.HISTORICO_BEM
                ----------------------------------
                UPDATE patrimonio.historico_bem
                   SET cod_orgao_organograma = de_para_setor.cod_orgao_organograma
                  FROM organograma.de_para_setor
--               WHERE historico_bem.ano_exercicio    = de_para_setor.ano_exercicio
                 WHERE historico_bem.cod_orgao        = de_para_setor.cod_orgao
                   AND historico_bem.cod_unidade      = de_para_setor.cod_unidade
                   AND historico_bem.cod_departamento = de_para_setor.cod_departamento
                   AND historico_bem.cod_setor        = de_para_setor.cod_setor;

                UPDATE patrimonio.historico_bem
                   SET cod_local_organograma = de_para_local.cod_local_organograma
                  FROM organograma.de_para_local
--               WHERE historico_bem.ano_exercicio    = de_para_local.ano_exercicio
                 WHERE historico_bem.cod_orgao        = de_para_local.cod_orgao
                   AND historico_bem.cod_unidade      = de_para_local.cod_unidade
                   AND historico_bem.cod_departamento = de_para_local.cod_departamento
                   AND historico_bem.cod_setor        = de_para_local.cod_setor
                   AND historico_bem.cod_local        = de_para_local.cod_local;



                -- TESTES P/ VERIFICAR SE TODOS AS TABELAS FORAM PREENCHIDAS
                ------------------------------------------------------------

                PERFORM 1 FROM administracao.comunicado WHERE cod_orgao_organograma IS NULL;
                IF NOT FOUND THEN
                    PERFORM 1 FROM administracao.usuario WHERE cod_orgao_organograma IS NULL;
                    IF NOT FOUND THEN
                        PERFORM 1 FROM public.sw_andamento WHERE cod_orgao_organograma IS NULL;
                        IF NOT FOUND THEN
                            PERFORM 1 FROM public.sw_andamento_padrao WHERE cod_orgao_organograma IS NULL;
                            IF NOT FOUND THEN
                                PERFORM 1 FROM public.sw_ultimo_andamento WHERE cod_orgao_organograma IS NULL;
                                IF NOT FOUND THEN
                                    PERFORM 1 FROM administracao.impressora WHERE cod_orgao_organograma IS NULL OR cod_local_organograma IS NULL;
                                    IF NOT FOUND THEN
                                        PERFORM 1 FROM frota.terceiros_historico WHERE cod_orgao_organograma IS NULL OR cod_local_organograma IS NULL;
                                        IF NOT FOUND THEN
                                            PERFORM 1 FROM patrimonio.historico_bem WHERE cod_orgao_organograma IS NULL OR cod_local_organograma IS NULL;
                                            IF NOT FOUND THEN
                                                   boSucesso := TRUE;
                                            ELSE   boSucesso := FALSE;
                                            END IF;
                                        ELSE   boSucesso := FALSE;
                                        END IF;
                                    ELSE   boSucesso := FALSE;
                                    END IF;
                                ELSE   boSucesso := FALSE;
                                END IF;
                            ELSE   boSucesso := FALSE;
                            END IF;
                        ELSE   boSucesso := FALSE;
                        END IF;    
                    ELSE   boSucesso := FALSE;
                    END IF;
                ELSE   boSucesso := FALSE;
                END IF;

                -- DROPA COLUNAS cod_orgao, cod_unidade, cod_departamento, cod_setor E cod_local DE FKs ANTIGAS --
                --------------------------------------------------------------------------------------------------
                DROP VIEW orcamento.vw_rl_relacao_despesa;
                
                ALTER TABLE administracao.comunicado   DROP CONSTRAINT fk_comunicado_2;
                ALTER TABLE administracao.comunicado   DROP COLUMN cod_setor;
                ALTER TABLE administracao.comunicado   DROP COLUMN cod_departamento;
                ALTER TABLE administracao.comunicado   DROP COLUMN cod_unidade;
                UPDATE      administracao.comunicado   SET         cod_orgao = cod_orgao_organograma;
                ALTER TABLE administracao.comunicado   DROP COLUMN cod_orgao_organograma;
                ALTER TABLE administracao.comunicado   ADD  CONSTRAINT fk_comunicado_2 FOREIGN KEY (cod_orgao) REFERENCES organograma.orgao (cod_orgao);
              
                ALTER TABLE administracao.usuario      DROP CONSTRAINT fk_usuario_2;
                ALTER TABLE administracao.usuario      DROP COLUMN cod_setor;
                ALTER TABLE administracao.usuario      DROP COLUMN cod_departamento;
                ALTER TABLE administracao.usuario      DROP COLUMN cod_unidade;
                ALTER TABLE administracao.usuario      DROP COLUMN ano_exercicio;
                UPDATE      administracao.usuario      SET         cod_orgao = cod_orgao_organograma;
                ALTER TABLE administracao.usuario      DROP COLUMN cod_orgao_organograma;
                ALTER TABLE administracao.usuario      ADD  CONSTRAINT fk_usuario_2 FOREIGN KEY (cod_orgao) REFERENCES organograma.orgao (cod_orgao);
                
                DROP INDEX ix_sw_andamento_1;
                
                PERFORM 1
                   FROM pg_views
                  WHERE viewname = 'sw_vw_consultaprocessopenultimoandamento';

                IF FOUND THEN
                    DROP VIEW sw_vw_consultaprocessopenultimoandamento;
                END IF;

                PERFORM 1
                   FROM pg_views
                  WHERE viewname = 'sw_vw_consultaprocesso';

                IF FOUND THEN
                    DROP VIEW sw_vw_consultaprocesso;
                END IF;
     
                ALTER TABLE public.sw_andamento        DROP CONSTRAINT fk_andamento_3;
                ALTER TABLE public.sw_andamento        DROP COLUMN cod_setor;
                ALTER TABLE public.sw_andamento        DROP COLUMN cod_departamento;
                ALTER TABLE public.sw_andamento        DROP COLUMN cod_unidade;
                ALTER TABLE public.sw_andamento        DROP COLUMN ano_exercicio_setor;
                UPDATE      public.sw_andamento        SET         cod_orgao = cod_orgao_organograma;
                ALTER TABLE public.sw_andamento        DROP COLUMN cod_orgao_organograma;
                ALTER TABLE public.sw_andamento        ADD  CONSTRAINT fk_andamento_3 FOREIGN KEY (cod_orgao) REFERENCES organograma.orgao (cod_orgao);
                
                CREATE TRIGGER tr_atualiza_ultimo_andamento AFTER INSERT OR UPDATE ON sw_andamento FOR EACH ROW EXECUTE PROCEDURE fn_atualiza_ultimo_andamento();
                
                ALTER TABLE public.sw_ultimo_andamento DROP COLUMN cod_setor;
                ALTER TABLE public.sw_ultimo_andamento DROP COLUMN cod_departamento;
                ALTER TABLE public.sw_ultimo_andamento DROP COLUMN cod_unidade;
                ALTER TABLE public.sw_ultimo_andamento DROP COLUMN ano_exercicio_setor;
                UPDATE      public.sw_ultimo_andamento SET         cod_orgao = cod_orgao_organograma;
                ALTER TABLE public.sw_ultimo_andamento DROP COLUMN cod_orgao_organograma;
                ALTER TABLE public.sw_ultimo_andamento ADD  CONSTRAINT fk_ultimo_andamento_3 FOREIGN KEY (cod_orgao) REFERENCES organograma.orgao (cod_orgao);
               
                ALTER TABLE public.sw_andamento_padrao DROP CONSTRAINT fk_andamento_padrao_1;
                ALTER TABLE public.sw_andamento_padrao DROP CONSTRAINT pk_andamento_padrao;
                ALTER TABLE public.sw_andamento_padrao DROP COLUMN cod_setor;
                ALTER TABLE public.sw_andamento_padrao DROP COLUMN cod_departamento;
                ALTER TABLE public.sw_andamento_padrao DROP COLUMN cod_unidade;
                ALTER TABLE public.sw_andamento_padrao DROP COLUMN ano_exercicio;
                UPDATE      public.sw_andamento_padrao SET         cod_orgao = cod_orgao_organograma;
                ALTER TABLE public.sw_andamento_padrao DROP COLUMN cod_orgao_organograma;
                ALTER TABLE public.sw_andamento_padrao ADD  CONSTRAINT pk_andamento_padrao   PRIMARY KEY (num_passagens, cod_classificacao, cod_assunto, cod_orgao, ordem);
                ALTER TABLE public.sw_andamento_padrao ADD  CONSTRAINT fk_andamento_padrao_1 FOREIGN KEY (cod_orgao) REFERENCES organograma.orgao (cod_orgao);

                ALTER TABLE administracao.impressora   DROP CONSTRAINT fk_impressora_1;
                ALTER TABLE administracao.impressora   DROP COLUMN cod_setor;
                ALTER TABLE administracao.impressora   DROP COLUMN cod_departamento;
                ALTER TABLE administracao.impressora   DROP COLUMN cod_unidade;
                ALTER TABLE administracao.impressora   DROP COLUMN exercicio;
                UPDATE      administracao.impressora   SET         cod_local = cod_local_organograma;
                UPDATE      administracao.impressora   SET         cod_orgao = cod_orgao_organograma;
                ALTER TABLE administracao.impressora   DROP COLUMN cod_local_organograma;
                ALTER TABLE administracao.impressora   DROP COLUMN cod_orgao_organograma;
                ALTER TABLE administracao.impressora   ADD  CONSTRAINT fk_impressora_1 FOREIGN KEY (cod_local) REFERENCES organograma.local (cod_local);
                ALTER TABLE administracao.impressora   ADD  CONSTRAINT fk_impressora_2 FOREIGN KEY (cod_orgao) REFERENCES organograma.orgao (cod_orgao);
                
                ALTER TABLE patrimonio.historico_bem   DROP CONSTRAINT fk_historico_bem_3;
                ALTER TABLE patrimonio.historico_bem   DROP COLUMN cod_setor;
                ALTER TABLE patrimonio.historico_bem   DROP COLUMN cod_departamento;
                ALTER TABLE patrimonio.historico_bem   DROP COLUMN cod_unidade;
                ALTER TABLE patrimonio.historico_bem   DROP COLUMN ano_exercicio;
                UPDATE      patrimonio.historico_bem   SET         cod_local = cod_local_organograma;
                UPDATE      patrimonio.historico_bem   SET         cod_orgao = cod_orgao_organograma;
                ALTER TABLE patrimonio.historico_bem   DROP COLUMN cod_local_organograma;
                ALTER TABLE patrimonio.historico_bem   DROP COLUMN cod_orgao_organograma;
                ALTER TABLE patrimonio.historico_bem   ADD  CONSTRAINT fk_historico_bem_3 FOREIGN KEY (cod_local) REFERENCES organograma.local (cod_local);
                ALTER TABLE patrimonio.historico_bem   ADD  CONSTRAINT fk_historico_bem_4 FOREIGN KEY (cod_orgao) REFERENCES organograma.orgao (cod_orgao);
                
                ALTER TABLE frota.terceiros_historico  DROP CONSTRAINT fk_terceiros_historico_2;
                ALTER TABLE frota.terceiros_historico  DROP COLUMN cod_setor;
                ALTER TABLE frota.terceiros_historico  DROP COLUMN cod_departamento;
                ALTER TABLE frota.terceiros_historico  DROP COLUMN cod_unidade;
                ALTER TABLE frota.terceiros_historico  DROP COLUMN ano_exercicio;
                UPDATE      frota.terceiros_historico  SET         cod_local = cod_local_organograma;
                UPDATE      frota.terceiros_historico  SET         cod_orgao = cod_orgao_organograma;
                ALTER TABLE frota.terceiros_historico  DROP COLUMN cod_local_organograma;
                ALTER TABLE frota.terceiros_historico  DROP COLUMN cod_orgao_organograma;
                ALTER TABLE frota.terceiros_historico  ADD  CONSTRAINT fk_terceiros_historico_2 FOREIGN KEY (cod_local) REFERENCES organograma.local (cod_local);
                ALTER TABLE frota.terceiros_historico  ADD  CONSTRAINT fk_terceiros_historico_3 FOREIGN KEY (cod_orgao) REFERENCES organograma.orgao (cod_orgao);
                
                
                -- DROPA COLUNA descricao DE ORGANOGRAMA.ORGAO --
                -------------------------------------------------
                DROP   VIEW organograma.vw_orgao_nivel;
                
                ALTER TABLE organograma.orgao DROP COLUMN descricao;
                
                
                -- CRIACAO DAS VIEW --
                ----------------------
                CREATE VIEW organograma.vw_orgao_nivel AS
                    SELECT o.cod_orgao
                         , o.num_cgm_pf
                         , o.cod_calendar
                         , o.cod_norma
                         , o.criacao
                         , o.inativacao
                         , o.sigla_orgao
                         , orn.cod_organograma
                         , organograma.fn_consulta_orgao(orn.cod_organograma, o.cod_orgao) AS orgao
                         , publico.fn_mascarareduzida(organograma.fn_consulta_orgao(orn.cod_organograma, o.cod_orgao)) AS orgao_reduzido
                         , publico.fn_nivel(organograma.fn_consulta_orgao(orn.cod_organograma, o.cod_orgao)) AS nivel
                         , orn.cod_nivel
                      FROM organograma.orgao o, organograma.orgao_nivel orn
                     WHERE o.cod_orgao = orn.cod_orgao
                  ORDER BY o.cod_orgao;


                CREATE VIEW sw_vw_consultaprocessopenultimoandamento AS
                    SELECT p.cod_processo                                       AS codprocesso
                         , p.ano_exercicio                                      AS exercicio
                         , p.cod_situacao                                       AS codsituacao
                         , sw_situacao_processo.nom_situacao                    AS nomsituacao
                         , p."timestamp"                                        AS datainclusao
                         , p.cod_usuario                                        AS codusuarioinclusao
                         , sw_usuario_inclusao.username                         AS usuarioinclusao
                         , p.numcgm                                             AS codinteressado
                         , sw_cgm.nom_cgm                                       AS nominteressado
                         , p.cod_classificacao                                  AS codclassificacao
                         , sw_classificacao.nom_classificacao                   AS nomclassificacao
                         , p.cod_assunto                                        AS codassunto
                         , sw_assunto.nom_assunto                               AS nomassunto
                         , CASE
                               WHEN sw_assinatura_digital.cod_andamento IS NULL THEN 'Off-Line'::character varying
                               ELSE sw_usuario_assinatura_digital.username
                           END                                                  AS usuariorecebimento
                         , CASE
                               WHEN sw_assinatura_digital.cod_usuario IS NULL THEN 0
                               ELSE sw_assinatura_digital.cod_usuario
                           END                                                  AS codusuariorecebimento
                         , sw_recebimento."timestamp"                           AS datarecebimento
                         , CASE
                               WHEN sw_recebimento."timestamp" IS NULL THEN 'f'::text
                               ELSE 't'::text
                           END                                                  AS recebido
                         , sw_penultimo_andamento.cod_andamento                 AS codpenultimoandamento
                         , sw_penultimo_andamento.cod_usuario                   AS codusuariopenultimoandamento
                         , sw_usuario_penultimo_andamento.username              AS usuariopenultimoandamento
                         , sw_penultimo_andamento.cod_orgao                     AS codpenultimoorgao
                --       , sw_penultimo_andamento.cod_unidade                   AS codpenultimounidade
                --       , sw_penultimo_andamento.cod_departamento              AS codpenultimodepartamento
                --       , sw_penultimo_andamento.cod_setor                     AS codpenultimosetor
                --       , sw_setor_penultimo_andamento.nom_setor               AS nompenultimosetor
                         , sw_penultimo_andamento."timestamp"                   AS datapenultimoandamento
                         , sw_processo_apensado.cod_processo_pai                AS codprocessoapensado
                         , sw_processo_apensado.exercicio_pai                   AS exercicioprocessoapensado
                         , CASE
                               WHEN sw_processo_apensado.cod_processo_filho IS NULL THEN 'f'::text
                               ELSE 't'::text
                           END                                                  AS apensado
                         , CASE
                               WHEN sw_processo_arquivado.cod_processo IS NULL THEN 'f'::text
                               ELSE 't'::text
                           END                                                  AS arquivado
                         , sw_historico_arquivamento.nom_historico              AS nomhistoricoarquivamento
                         , sw_recibo_impresso.cod_recibo                        AS numreciboimpresso
                      FROM sw_processo p
                 LEFT JOIN ( 
                               SELECT max(sw_andamento.cod_andamento) - 1       AS cod_andamento
                                    , sw_andamento.cod_processo
                                    , sw_andamento.ano_exercicio
                                 FROM sw_andamento
                             GROUP BY sw_andamento.cod_processo
                                    , sw_andamento.ano_exercicio
                           )                                                    AS sw_codigo_penultimo_andamento
                        ON sw_codigo_penultimo_andamento.cod_processo    = p.cod_processo
                       AND sw_codigo_penultimo_andamento.ano_exercicio   = p.ano_exercicio
                 LEFT JOIN sw_andamento                                         AS sw_penultimo_andamento
                        ON sw_penultimo_andamento.cod_andamento          = sw_codigo_penultimo_andamento.cod_andamento
                       AND sw_penultimo_andamento.cod_processo           = p.cod_processo
                       AND sw_penultimo_andamento.ano_exercicio          = p.ano_exercicio
                 LEFT JOIN administracao.usuario                                AS sw_usuario_penultimo_andamento
                        ON sw_usuario_penultimo_andamento.numcgm         = sw_penultimo_andamento.cod_usuario
                -- LEFT JOIN administracao.setor                                  AS sw_setor_penultimo_andamento
                --        ON sw_setor_penultimo_andamento.cod_setor        = sw_penultimo_andamento.cod_setor
                --       AND sw_setor_penultimo_andamento.cod_departamento = sw_penultimo_andamento.cod_departamento
                --       AND sw_setor_penultimo_andamento.cod_unidade      = sw_penultimo_andamento.cod_unidade
                --       AND sw_setor_penultimo_andamento.cod_orgao        = sw_penultimo_andamento.cod_orgao
                --       AND sw_setor_penultimo_andamento.ano_exercicio    = sw_penultimo_andamento.ano_exercicio_setor
                 LEFT JOIN sw_cgm
                        ON sw_cgm.numcgm = p.numcgm
                 LEFT JOIN sw_classificacao
                        ON sw_classificacao.cod_classificacao            = p.cod_classificacao
                 LEFT JOIN sw_assunto
                        ON sw_assunto.cod_assunto                        = p.cod_assunto
                       AND sw_assunto.cod_classificacao                  = p.cod_classificacao
                 LEFT JOIN sw_situacao_processo
                        ON sw_situacao_processo.cod_situacao             = p.cod_situacao
                 LEFT JOIN administracao.usuario                                AS sw_usuario_inclusao
                        ON sw_usuario_inclusao.numcgm                    = p.cod_usuario
                 LEFT JOIN sw_recebimento
                        ON sw_recebimento.cod_andamento                  = sw_penultimo_andamento.cod_andamento
                       AND sw_recebimento.cod_processo                   = p.cod_processo
                       AND sw_recebimento.ano_exercicio                  = p.ano_exercicio
                 LEFT JOIN sw_recibo_impresso
                        ON sw_recibo_impresso.cod_andamento              = sw_penultimo_andamento.cod_andamento
                       AND sw_recibo_impresso.cod_processo               = p.cod_processo
                       AND sw_recibo_impresso.ano_exercicio              = p.ano_exercicio
                 LEFT JOIN sw_assinatura_digital
                        ON sw_assinatura_digital.cod_andamento           = sw_penultimo_andamento.cod_andamento
                       AND sw_assinatura_digital.cod_processo            = p.cod_processo
                       AND sw_assinatura_digital.ano_exercicio           = p.ano_exercicio
                 LEFT JOIN administracao.usuario                                AS sw_usuario_assinatura_digital
                        ON sw_usuario_assinatura_digital.numcgm          = sw_assinatura_digital.cod_usuario
                 LEFT JOIN sw_processo_apensado
                        ON sw_processo_apensado.cod_processo_filho       = p.cod_processo
                       AND sw_processo_apensado.exercicio_filho          = p.ano_exercicio
                       AND sw_processo_apensado.timestamp_desapensamento IS NULL
                 LEFT JOIN sw_processo_arquivado
                        ON sw_processo_arquivado.cod_processo            = p.cod_processo
                       AND sw_processo_arquivado.ano_exercicio           = p.ano_exercicio
                 LEFT JOIN sw_historico_arquivamento
                        ON sw_historico_arquivamento.cod_historico       = sw_processo_arquivado.cod_historico
                         ;
                
                
                CREATE VIEW sw_vw_consultaprocesso AS
                    SELECT p.cod_processo                               AS codprocesso
                         , p.ano_exercicio                              AS exercicio
                         , p.cod_situacao                               AS codsituacao
                         , p."timestamp"                                AS datainclusao
                         , p.cod_usuario                                AS codusuarioinclusao
                         , p.numcgm                                     AS codinteressado
                         , p.cod_classificacao                          AS codclassificacao
                         , p.cod_assunto                                AS codassunto
                         , p.resumo_assunto
                         , p.confidencial
                         , sw_situacao_processo.nom_situacao            AS nomsituacao
                         , usuario_inclusao.username                    AS usuarioinclusao
                         , sw_cgm.nom_cgm                               AS nominteressado
                         , ultimo_andamento.cod_andamento               AS codultimoandamento
                         , ultimo_andamento.cod_usuario                 AS codusuarioultimoandamento
                         , ultimo_andamento.cod_orgao                   AS codorgao
                --       , ultimo_andamento.cod_unidade                 AS codunidade
                --       , ultimo_andamento.cod_departamento            AS coddepartamento
                --       , ultimo_andamento.cod_setor                   AS codsetor
                --       , ultimo_andamento.ano_exercicio_setor         AS exerciciosetor
                         , ultimo_andamento."timestamp"                 AS dataultimoandamento
                --       , setor_ultimo_andamento.nom_setor             AS nomsetor
                         , usuario_ultimo_andamento.username            AS usuarioultimoandamento
                         , classificacao.nom_classificacao              AS nomclassificacao
                         , assunto.nom_assunto                          AS nomassunto
                         , sw_recebimento."timestamp"                   AS datarecebimento
                         , CASE
                               WHEN sw_recebimento."timestamp" IS NULL THEN 'f'::text
                               ELSE 't'::text
                           END                                          AS recebido
                         , CASE
                               WHEN sw_assinatura_digital.cod_andamento IS NULL THEN 'Off-Line'::character varying
                               ELSE sw_usuario_assinatura_digital.username
                           END                                          AS usuariorecebimento
                         , CASE
                               WHEN sw_assinatura_digital.cod_usuario IS NULL THEN 0
                               ELSE sw_assinatura_digital.cod_usuario
                           END                                          AS codusuariorecebimento
                         , CASE
                               WHEN sw_processo_arquivado.cod_processo IS NULL THEN 'f'::text
                               ELSE 't'::text
                           END                                          AS arquivado
                         , sw_historico_arquivamento.nom_historico      AS nomhistoricoarquivamento
                         , sw_recibo_impresso.cod_recibo                AS numreciboimpresso
                         , sw_processo_apensado.cod_processo_pai        AS codprocessoapensado
                         , sw_processo_apensado.exercicio_pai           AS exercicioprocessoapensado
                         , sw_processo_apensado.timestamp_apensamento   AS data_processo_apensado
                         , sw_processo_arquivado.timestamp_arquivamento
                         , CASE
                               WHEN sw_processo_apensado.cod_processo_filho IS NULL THEN 'f'::text
                               ELSE 't'::text
                           END                                          AS apensado
                      FROM sw_processo                                  AS p
                 LEFT JOIN sw_processo_apensado
                        ON sw_processo_apensado.cod_processo_filho = p.cod_processo
                       AND sw_processo_apensado.exercicio_filho    = p.ano_exercicio
                       AND sw_processo_apensado.timestamp_desapensamento IS NULL
                 LEFT JOIN sw_processo_arquivado
                        ON sw_processo_arquivado.cod_processo      = p.cod_processo
                       AND sw_processo_arquivado.ano_exercicio     = p.ano_exercicio
                 LEFT JOIN sw_historico_arquivamento
                        ON sw_historico_arquivamento.cod_historico = sw_processo_arquivado.cod_historico
                         , sw_cgm
                         , administracao.usuario                        AS usuario_inclusao
                         , sw_situacao_processo
                         , sw_ultimo_andamento                          AS ultimo_andamento
                 LEFT JOIN sw_recebimento
                        ON sw_recebimento.cod_andamento            = ultimo_andamento.cod_andamento
                       AND sw_recebimento.cod_processo             = ultimo_andamento.cod_processo
                       AND sw_recebimento.ano_exercicio            = ultimo_andamento.ano_exercicio
                 LEFT JOIN sw_recibo_impresso
                        ON sw_recibo_impresso.cod_andamento        = ultimo_andamento.cod_andamento
                       AND sw_recibo_impresso.cod_processo         = ultimo_andamento.cod_processo
                       AND sw_recibo_impresso.ano_exercicio        = ultimo_andamento.ano_exercicio
                 LEFT JOIN sw_assinatura_digital
                        ON sw_assinatura_digital.cod_andamento     = ultimo_andamento.cod_andamento
                       AND sw_assinatura_digital.cod_processo      = ultimo_andamento.cod_processo
                       AND sw_assinatura_digital.ano_exercicio     = ultimo_andamento.ano_exercicio
                 LEFT JOIN administracao.usuario                        AS sw_usuario_assinatura_digital
                        ON sw_usuario_assinatura_digital.numcgm    = sw_assinatura_digital.cod_usuario
                --       , administracao.setor                          AS setor_ultimo_andamento
                         , administracao.usuario                        AS usuario_ultimo_andamento
                         , sw_classificacao                             AS classificacao
                         , sw_assunto                                   AS assunto
                     WHERE p.numcgm                                = sw_cgm.numcgm
                       AND p.cod_usuario                           = usuario_inclusao.numcgm
                       AND p.cod_situacao                          = sw_situacao_processo.cod_situacao
                       AND p.cod_processo                          = ultimo_andamento.cod_processo
                       AND p.ano_exercicio                         = ultimo_andamento.ano_exercicio
                       AND p.cod_assunto                           = assunto.cod_assunto
                       AND p.cod_classificacao                     = assunto.cod_classificacao
                --     AND ultimo_andamento.cod_setor              = setor_ultimo_andamento.cod_setor
                --     AND ultimo_andamento.cod_departamento       = setor_ultimo_andamento.cod_departamento
                --     AND ultimo_andamento.cod_unidade            = setor_ultimo_andamento.cod_unidade
                --     AND ultimo_andamento.cod_orgao              = setor_ultimo_andamento.cod_orgao
                --     AND ultimo_andamento.ano_exercicio_setor    = setor_ultimo_andamento.ano_exercicio
                       AND ultimo_andamento.cod_usuario            = usuario_ultimo_andamento.numcgm
                       AND p.cod_classificacao                     = classificacao.cod_classificacao
                         ;


                IF boSucesso = TRUE THEN

                    UPDATE administracao.configuracao
                       SET valor     = 'true'
                     WHERE exercicio = '2009'
                       AND parametro = 'migra_organograma';

                    RETURN TRUE;

                ELSE

                    RAISE EXCEPTION 'Foram encontrados erros durante a migração!';

                END IF;

        ELSE

            IF    inCountSetor > 0 AND inCountLocal = 0 THEN

                RAISE NOTICE 'De-Para de Setor deve ser revisto!';
                RETURN TRUE;

            ELSIF inCountSetor = 0 AND inCountLocal > 0 THEN

                RAISE NOTICE 'De-Para de Local deve ser revisto!';
                RETURN TRUE;

            ELSIF inCountSetor > 0 AND inCountLocal > 0 THEN

                RAISE NOTICE 'De-Para de Setor e Local deve ser revisto!';
                RETURN TRUE;

            END IF; 

        END IF;

    ELSE

        RAISE EXCEPTION 'Migração do Organograma já foi executada anteriormente!';

    END IF;

END;
$$ LANGUAGE 'plpgsql';


