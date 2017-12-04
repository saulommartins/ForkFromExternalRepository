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
 * PL do Relatório  de Insuficiencia
 * Data de Criação   : 17/12/2008


 * @author Analista      Tonismar Régis Bernardo
 * @author Desenvolvedor Henrique Boaventura
 
 * @package URBEM

 *  $Id:$
*/

CREATE OR REPLACE FUNCTION contabilidade.saldo_conta_banco_recurso(VARCHAR, INTEGER, INTEGER) RETURNS NUMERIC AS $$
DECLARE

    stExercicio   ALIAS FOR $1;
    inCodRecurso  ALIAS FOR $2;
    inCodEntidade ALIAS FOR $3;

    flValor      NUMERIC := 0;

BEGIN
    
    IF(inCodRecurso = 0)
    THEN
         SELECT SUM(COALESCE(contabilidade.fn_saldo_conta_banco(plano_analitica.exercicio,plano_analitica.cod_plano),0)) AS saldo
          INTO flValor
          FROM contabilidade.plano_analitica
    INNER JOIN contabilidade.plano_banco
            ON plano_analitica.exercicio = plano_banco.exercicio
           AND plano_analitica.cod_plano = plano_banco.cod_plano
         WHERE plano_analitica.exercicio = stExercicio
           AND plano_banco.cod_entidade  = inCodEntidade
           AND NOT EXISTS ( SELECT 1
                              FROM contabilidade.plano_recurso
                             WHERE plano_analitica.exercicio = plano_recurso.exercicio
                               AND plano_analitica.cod_plano = plano_recurso.cod_plano
                          );

    ELSE
        SELECT SUM(COALESCE(contabilidade.fn_saldo_conta_banco(plano_analitica.exercicio,plano_analitica.cod_plano),0)) AS saldo
          INTO flValor
          FROM contabilidade.plano_recurso
    INNER JOIN contabilidade.plano_analitica
            ON plano_recurso.exercicio = plano_analitica.exercicio
           AND plano_recurso.cod_plano = plano_analitica.cod_plano
    INNER JOIN contabilidade.plano_banco
            ON plano_analitica.exercicio = plano_banco.exercicio
           AND plano_analitica.cod_plano = plano_banco.cod_plano
         WHERE plano_recurso.exercicio   = stExercicio
           AND plano_recurso.cod_recurso = inCodRecurso
           AND plano_banco.cod_entidade  = inCodEntidade; 

    END IF;

    RETURN flValor;

END;

$$ language 'plpgsql';

