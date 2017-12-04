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
    * Consulta do código de conciliacao das movimentacoes correntes tcmba
    * Data de Criação: 06/06/2016

    * @author Michel Teixeira

    $Id: FTCMBACodConcilia.plsql 65719 2016-06-10 17:24:41Z michel $ 
*/

CREATE OR REPLACE FUNCTION tcmba.fn_cod_concilia( VARCHAR,INTEGER,VARCHAR,BOOLEAN) RETURNS INTEGER AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    inMes               ALIAS FOR $2;
    stChaveConciliacao  ALIAS FOR $3;
    boInsereChave       ALIAS FOR $4;

    recRegistro       RECORD;
    intCodConciliacao INTEGER;
BEGIN

    IF boInsereChave IS TRUE THEN

        PERFORM 1
           FROM tcmba.arquivo_concilia
          WHERE exercicio          = stExercicio
            AND mes                = inMes
            AND chave_conciliacao  = stChaveConciliacao;

        IF NOT FOUND THEN
            SELECT (COALESCE(MAX(cod_conciliacao),0) + 1) AS cod_conciliacao
              INTO intCodConciliacao
              FROM tcmba.arquivo_concilia
             WHERE exercicio = stExercicio
               AND mes       = inMes;

            INSERT
              INTO tcmba.arquivo_concilia
            VALUES ( intCodConciliacao
                   , stChaveConciliacao
                   , stExercicio
                   , inMes
                   , ''
                   , 0.00
                   );
        END IF;

    END IF;

    SELECT cod_conciliacao
      INTO intCodConciliacao
      FROM tcmba.arquivo_concilia
     WHERE exercicio         = stExercicio
       AND mes               = inMes
       AND chave_conciliacao = stChaveConciliacao;

   RETURN intCodConciliacao;
END;

$$ LANGUAGE 'plpgsql' SECURITY DEFINER;