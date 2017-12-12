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
* $Revision: 28905 $
* $Name$
* $Author: eduardoschitz $
* $Date: 2008-03-31 17:14:04 -0300 (Seg, 31 Mar 2008) $
*
* Casos de uso: uc-02.03.12,uc-02.03.16,uc-02.03.05,uc-02.04.05
*/

/*
$Log$
Revision 1.6  2006/07/05 20:37:38  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION empenho.retorna_notas_empenhos(VARCHAR,INTEGER,INTEGER) RETURNS VARCHAR as '
DECLARE
    stExercicio         ALIAS FOR $1;
    inCodOrdem          ALIAS FOR $2;
    inCodEntidade       ALIAS FOR $3;
    stSaida             VARCHAR   := '''';
    stSql               VARCHAR   := '''';
    reRegistro          RECORD;
BEGIN
    stSql := ''
                SELECT
                         pl.cod_nota
                        ,pl.exercicio_liquidacao
                        ,nl.cod_empenho
                        ,nl.exercicio_empenho
                FROM
                        empenho.pagamento_liquidacao    as pl
                       ,empenho.nota_liquidacao         as nl
                WHERE
                        pl.exercicio_liquidacao = nl.exercicio
                AND     pl.cod_nota             = nl.cod_nota
                AND     pl.cod_entidade         = nl.cod_entidade
                AND     pl.exercicio            = '''''' || stExercicio     || ''''''
                AND     pl.cod_ordem            = '' || inCodOrdem      || ''
                AND     pl.cod_entidade         = '' || inCodEntidade   || ''
            '';
    FOR reRegistro IN EXECUTE stSql
    LOOP
        stSaida := stSaida || reRegistro.cod_nota || ''/'' || reRegistro.exercicio_liquidacao || ''   '';
        stSaida := stSaida || reRegistro.cod_empenho || ''/''|| reRegistro.exercicio_empenho  || ''
 '';
    END LOOP;

    RETURN stSaida;
END;
'LANGUAGE 'plpgsql';
