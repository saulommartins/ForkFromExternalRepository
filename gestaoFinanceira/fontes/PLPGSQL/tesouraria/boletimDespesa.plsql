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
* $Revision: 17649 $
* $Name$
* $Author: cleisson $
* $Date: 2006-11-13 20:17:22 -0200 (Seg, 13 Nov 2006) $
*
* Casos de uso: uc-02.04.14
*/

/*
$Log$
Revision 1.3  2006/11/13 22:17:22  cleisson
Bug #7237#

Revision 1.2  2006/07/05 20:38:11  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION tesouraria.fn_boletim_despesa(VARCHAR,VARCHAR,VARCHAR,VARCHAR,INTEGER) RETURNS SETOF RECORD AS ' 

DECLARE

    stEntidade              ALIAS FOR $1;
    stExercicio             ALIAS FOR $2;
    stDtInicial             ALIAS FOR $3;
    stDtFinal               ALIAS FOR $4;
    inNumCgm                ALIAS FOR $5;

    stSql                   VARCHAR   := '''';
    stAux                   VARCHAR   := '''';

    reRegistro              RECORD;

BEGIN


IF (stDtInicial = stDtFinal ) THEN
    stAux := '' AND TO_DATE(TO_CHAR(TP.timestamp,''''dd/mm/yyyy''''),''''dd/mm/yyyy'''') = to_date('''''' || stDtInicial || '''''',''''dd/mm/yyyy'''') '';
ELSE
    stAux := '' AND TO_DATE(TO_CHAR(TP.timestamp,''''dd/mm/yyyy''''),''''dd/mm/yyyy'''') BETWEEN to_date('''''' || stDtInicial || '''''',''''dd/mm/yyyy'''')  AND to_date('''''' || stDtFinal || '''''',''''dd/mm/yyyy'''') '';

END IF;


stSql := ''                                                        

    SELECT  MIN (TP.cod_boletim)
            ,MAX(TP.cod_boletim)

    FROM

        tesouraria.pagamento        as TP

    WHERE

        TP.cgm_usuario         = ''|| inNumCgm ||''
    AND TP.exercicio           = '''''' || stExercicio || ''''''
    AND TP.cod_entidade        IN (''|| stEntidade||'')
    
    '' || stAux || ''

'';


    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;

RETURN;

END;
' LANGUAGE 'plpgsql';

