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
* $Revision: 12203 $
* $Name$
* $Author: cleisson $
* $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $
*
* Casos de uso: uc-02.02.04
*/

/*
$Log$
Revision 1.6  2006/07/05 20:37:31  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION contabilidade.fn_recupera_contra_partida(VARCHAR,INTEGER,CHAR,INTEGER,CHAR,INTEGER) RETURNS NUMERIC AS '

DECLARE
    crCursor            REFCURSOR;
    stExercicio         ALIAS FOR $1;
    inCodLote           ALIAS FOR $2;
    chTipo              ALIAS FOR $3;
    inSequencia         ALIAS FOR $4;
    chTipoValor         ALIAS FOR $5;
    inCodEntidade       ALIAS FOR $6;
    inCodPlano          INTEGER;

BEGIN

    IF chTipoValor = ''D'' THEN
        SELECT      cod_plano
            INTO    inCodPlano
        FROM    contabilidade.conta_credito
        WHERE   exercicio   = stExercicio
        AND     cod_lote    = inCodLote
        AND     tipo        = chTipo
        AND     sequencia   = inSequencia
        AND     tipo_valor  = ''C''
        AND     cod_entidade= inCodEntidade
        ;
    ELSE
        SELECT      cod_plano
            INTO    inCodPlano
        FROM    contabilidade.conta_debito
        WHERE   exercicio   = stExercicio
        AND     cod_lote    = inCodLote
        AND     tipo        = chTipo
        AND     sequencia   = inSequencia
        AND     tipo_valor  = ''D''
        AND     cod_entidade= inCodEntidade
        ;
    END IF;

    RETURN inCodPlano;

END;
'LANGUAGE 'plpgsql';
