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
* $Revision: 59612 $
* $Name$
* $Author: gelson $
* $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $
*
* Casos de uso: uc-02.00.00
* Casos de uso: uc-02.00.00
*/

/*
$Log$
Revision 1.1  2007/09/26 14:43:57  gris
-- O módulo TCE-SC  funcionalidades deverá ir para a gestãp estação de contas.

Revision 1.6  2007/07/30 18:28:33  tonismar
alteração de uc-00.00.00 para uc-02.00.00

Revision 1.5  2006/07/05 20:37:45  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION tcers.tipo_conta_rubrica(VARCHAR,VARCHAR) RETURNS VARCHAR AS $$
DECLARE
    stExercicio          ALIAS FOR $1;
    stCodEstrutural      ALIAS FOR $2;
    stRetorno            VARCHAR := '';
    stSql                VARCHAR := '';
    inFormaExecucao      INTEGER := 0;
    inCount              INTEGER := 0;

BEGIN
    SELECT  valor
       INTO inFormaExecucao
    FROM    administracao.configuracao
    WHERE   exercicio   = stExercicio
    AND     parametro   = 'forma_execucao_orcamento'
    AND     cod_modulo  = 8;

    IF inFormaExecucao = 0 THEN
        SELECT  count(*)
           INTO inCount
        FROM     orcamento.despesa          as de
                ,orcamento.conta_despesa    as cd
        WHERE   de.cod_conta        = cd.cod_conta
        AND     de.exercicio        = cd.exercicio
        AND     cd.exercicio        = stExercicio
        AND     cd.cod_estrutural   = stCodEstrutural;

    ELSIF inFormaExecucao = 1 THEN

        SELECT  count(*)
           INTO inCount
        FROM     orcamento.despesa              as de
                ,orcamento.conta_despesa        as cd
                ,empenho.pre_empenho_despesa    as pd
        WHERE   de.exercicio        = cd.exercicio
        AND     de.cod_conta        = cd.cod_conta
        AND     de.exercicio        = pd.exercicio
        AND     de.cod_despesa      = pd.cod_despesa
        AND     cd.exercicio        = pd.exercicio
        AND     cd.cod_conta        = pd.cod_conta
        AND     cd.exercicio        = stExercicio
        AND     cd.cod_estrutural   = stCodEstrutural;
    END IF;

    IF inCount = 0 THEN
        stRetorno := 'S';
    ELSE
        stRetorno := 'A';
    END IF;

    RETURN stRetorno;
END;
$$ LANGUAGE 'plpgsql';
