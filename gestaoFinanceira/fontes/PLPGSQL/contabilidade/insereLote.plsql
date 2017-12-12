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

CREATE OR REPLACE FUNCTION contabilidade.fn_insere_lote(VARCHAR,INTEGER,VARCHAR,VARCHAR,VARCHAR) RETURNS INTEGER AS $$
DECLARE
    reRecord        RECORD;
    inCodLote       INTEGER;
    chTipo          CHAR := '';
    stExercicio     ALIAS FOR $1;
    inCodEntidade   ALIAS FOR $2;
    stTipo          ALIAS FOR $3;
    stNomeLote      ALIAS FOR $4;
    stDataLote      ALIAS FOR $5;
    dtDataLote      DATE;
    stFiltro        VARCHAR := '';

BEGIN
    chTipo      := substr(trim(stTipo),1,1);
    dtDataLote  := to_date(stDataLote,'dd/mm/yyyy');
    stFiltro    := 'WHERE exercicio=' || quote_literal(stExercicio);
    stFiltro    := stFiltro || ' AND tipo = ' || quote_literal(chTipo);
    stFiltro    := stFiltro || ' AND cod_entidade = ' || inCodEntidade;
    inCodLote   := publico.fn_proximo_cod('cod_lote','contabilidade.lote',stFiltro);

    INSERT INTO contabilidade.lote
        (cod_lote,exercicio,tipo,cod_entidade,nom_lote,dt_lote)
    VALUES
        (inCodLote,stExercicio,chTipo,inCodEntidade,stNomeLote,dtDataLote)
        --(1,'2005','I',1,'AA',to_date('01/02/2006','dd/mm/yyyy'))
    ;

    RETURN inCodLote;
END;
$$ LANGUAGE 'plpgsql';
