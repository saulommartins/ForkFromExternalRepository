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
* Busca o valor ultimo CALCULADO  para um determinado credito e imovel
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: buscaValorCalculoCredito.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.00
*/

CREATE OR REPLACE FUNCTION arrecadacao.buscaValorCalculoCredito (INTEGER, INTEGER, VARCHAR, INTEGER, INTEGER, INTEGER, INTEGER ) returns NUMERIC as '
declare
    inImovel        ALIAS FOR $1;
    inCodGrupo      ALIAS FOR $2;
    stExercicio     ALIAS FOR $3;
    inCodCredito    ALIAS FOR $4;
    inCodEspecie    ALIAS FOR $5;
    inCodGenero     ALIAS FOR $6;
    inCodNatureza   ALIAS FOR $7;
    nuValor         NUMERIC;
begin
-- Lancamento do Calculo do Imovel    
        SELECT ac.valor
          INTO nuValor     
          FROM arrecadacao.calculo                          AS ac
    INNER JOIN (

                 SELECT MAX( ac.cod_calculo )               AS cod_calculo
                   FROM arrecadacao.calculo_grupo_credito   AS acgc
             INNER JOIN arrecadacao.calculo                 AS ac
                     ON ac.cod_calculo = acgc.cod_calculo
                    AND ac.ativo = TRUE
                    AND ac.exercicio             = stExercicio
                    AND ac.cod_credito           = inCodCredito
                    AND ac.cod_especie           = inCodEspecie
                    AND ac.cod_genero            = inCodGenero
                    AND ac.cod_natureza          = inCodNatureza
                    AND acgc.cod_grupo           = inCodGrupo
             INNER JOIN arrecadacao.imovel_calculo          AS aic
                     ON aic.cod_calculo = ac.cod_calculo
                  WHERE aic.inscricao_municipal  = inImovel --acgc.cod_grupo           = inCodGrupo
--                 AND  aic.inscricao_municipal  = inImovel

               ) AS sub
            ON sub.cod_calculo = ac.cod_calculo
    --LEFT  JOIN arrecadacao.lancamento_calculo      AS alc
      --      ON alc.cod_calculo = ac.cod_calculo
        -- WHERE alc.cod_calculo IS NULL
         ;

   return nuValor;
end;
'language 'plpgsql';
