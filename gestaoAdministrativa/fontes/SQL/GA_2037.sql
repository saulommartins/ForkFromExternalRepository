/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - SoluÃ§Ãµes em GestÃ£o PÃºblica                                *
    * @copyright (c) 2013 ConfederaÃ§Ã£o Nacional de MunicÃ­pos                         *
    * @author ConfederaÃ§Ã£o Nacional de MunicÃ­pios                                    *
    *                                                                                *
    * O URBEM CNM Ã© um software livre; vocÃª pode redistribuÃ­-lo e/ou modificÃ¡-lo sob *
    * os  termos  da LicenÃ§a PÃºblica Geral GNU conforme  publicada  pela FundaÃ§Ã£o do *
    * Software Livre (FSF - Free Software Foundation); na versÃ£o 2 da LicenÃ§a.       *
    *                                                                                *
    * Este  programa  Ã©  distribuÃ­do  na  expectativa  de  que  seja  Ãºtil,   porÃ©m, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implÃ­cita  de  COMERCIABILIDADE  OU *
    * ADEQUAÃÃO A UMA FINALIDADE ESPECÃFICA. Consulte a LicenÃ§a PÃºblica Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * VocÃª deve ter recebido uma cÃ³pia da LicenÃ§a PÃºblica Geral do GNU "LICENCA.txt" *
    * com  este  programa; se nÃ£o, escreva para  a  Free  Software Foundation  Inc., *
    * no endereÃ§o 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
/*
*
* Script de DDL e DML
*
* Versao 2.03.7
*
* Fabio Bertoldi - 20150226
*
*/

----------------
-- Ticket #22713
----------------

UPDATE administracao.acao SET nom_arquivo = 'FMManterClassificacao.php', parametro = 'incluir', ordem = 1 WHERE cod_acao = 115;
UPDATE administracao.acao SET nom_arquivo = 'LSManterClassificacao.php', parametro = 'alterar', ordem = 2 WHERE cod_acao = 116;
UPDATE administracao.acao SET nom_arquivo = 'LSManterClassificacao.php', parametro = 'excluir', ordem = 3 WHERE cod_acao = 114;

UPDATE administracao.funcionalidade SET nom_diretorio = 'instancias/classificacao/' WHERE cod_funcionalidade = 29;

INSERT
  INTO administracao.configuracao
     ( cod_modulo
     , exercicio
     , parametro
     , valor
     )
     VALUES
     ( 5
     , '2015'
     , 'tipo_numeracao_classificacao_assunto'
     , 'automatico'
     );


----------------
-- Ticket #21749
----------------

INSERT
  INTO administracao.configuracao
     ( cod_modulo
     , exercicio
     , parametro
     , valor
     )
     VALUES
     ( 2
     , '2015'
     , 'tempo_inatividade_usuario'
     , '30'
     );


------------------------------------------------------------------------------------------------
-- INSERSAO EM tcmgo.plano_contas_tcmgo QUE DEVERIA ESTAR NA ROTINA DE VIRADA - DAGIANE 20150409
------------------------------------------------------------------------------------------------

INSERT
  INTO tcmgo.plano_contas_tcmgo
     ( exercicio
     , cod_plano
     , estrutural
     , titulo
     , natureza
     )
SELECT '2015'
     , cod_plano
     , estrutural
     , titulo
     , natureza
  FROM tcmgo.plano_contas_tcmgo AS proximo
 WHERE exercicio = '2014'
   AND NOT EXISTS (
                    SELECT 1
                      FROM tcmgo.plano_contas_tcmgo
                     WHERE exercicio = '2015'
                       AND cod_plano = proximo.cod_plano
                  )
     ;

