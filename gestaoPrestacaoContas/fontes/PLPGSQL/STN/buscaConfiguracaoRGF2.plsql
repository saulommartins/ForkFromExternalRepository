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
* Script de função PLPGSQL
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: buscaConfiguracaoRGF2.plsql 66677 2016-11-01 19:38:55Z carlos.silva $

* Casos de uso: uc-06.01.02
*/

CREATE OR REPLACE FUNCTION stn.busca_configuracao_rgf2(VARCHAR, VARCHAR) RETURNS SETOF RECORD AS $$
DECLARE
  stExercicio  ALIAS FOR $1;
  stCodConta   ALIAS FOR $2;

  stSql        VARCHAR := '';
  reRegistro   RECORD;

BEGIN
  stSql := '  SELECT cod_estrutural AS estrutural
               FROM stn.vinculo_contas_rgf_2
          INNER JOIN contabilidade.plano_analitica
                  ON vinculo_contas_rgf_2.cod_plano = plano_analitica.cod_plano
                 AND vinculo_contas_rgf_2.exercicio = plano_analitica.exercicio
          INNER JOIN contabilidade.plano_conta
                  ON plano_analitica.cod_conta = plano_conta.cod_conta
                 AND plano_analitica.exercicio = plano_conta.exercicio
               WHERE vinculo_contas_rgf_2.exercicio = '''||stExercicio||'''
                 AND vinculo_contas_rgf_2.cod_conta IN ('||stCodConta||')
                 AND vinculo_contas_rgf_2.timestamp = (SELECT MAX(timestamp)
                                                         FROM stn.vinculo_contas_rgf_2 tbl
                                                        WHERE vinculo_contas_rgf_2.exercicio = tbl.exercicio); ';


  FOR reRegistro IN EXECUTE stSql
  LOOP
      RETURN next reRegistro;
  END LOOP;

END;
$$ language 'plpgsql';