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
* $Revision: 3077 $
* $Name$
* $Author: pablo $
* $Date: 2005-11-29 14:53:37 -0200 (Ter, 29 Nov 2005) $
*
* Casos de uso: uc-01.05.01
*/

CREATE OR REPLACE FUNCTION organograma.niveis_organograma(integer,integer) RETURNS SETOF RECORD AS '
DECLARE
   inCodOrganograma   ALIAS FOR $1;
    inCodOrgao         ALIAS FOR $2;

    boIniciar   BOOLEAN     := false;
    inPosicao   INTEGER     := 0;
    stProxOrgao VARCHAR     := '''';
    reRegistro  RECORD;
BEGIN
    FOR reRegistro IN
        SELECT
            cod_orgao
            ,orgao
            ,orgao_reduzido
            ,descricao
            ,nivel
        FROM
            organograma.vw_orgao_nivel
        WHERE
            cod_organograma = inCodOrganograma
        ORDER BY orgao desc
    LOOP
        IF boIniciar THEN
            IF reRegistro.orgao_reduzido = stProxOrgao THEN
                inPosicao := publico.fn_lposition(reRegistro.orgao_reduzido,''.'')-1;
                IF inPosicao <= 0 THEN
                    stProxOrgao := reRegistro.orgao_reduzido;
                ELSE
                    stProxOrgao := substr(reRegistro.orgao_reduzido,1, inPosicao);
                END IF;
                RETURN NEXT reRegistro;
            END IF;
        END IF;
        IF reRegistro.cod_orgao = inCodOrgao and boIniciar = false THEN
            inPosicao := publico.fn_lposition(reRegistro.orgao_reduzido,''.'')-1;
            IF inPosicao <= 0 THEN
                stProxOrgao := reRegistro.orgao_reduzido;
            ELSE
                stProxOrgao := substr(reRegistro.orgao_reduzido,1, inPosicao);
            END IF;
            boIniciar = true;
            RETURN NEXT reRegistro;
        END IF;
    END LOOP;

    RETURN;
END;
' LANGUAGE 'plpgsql';
