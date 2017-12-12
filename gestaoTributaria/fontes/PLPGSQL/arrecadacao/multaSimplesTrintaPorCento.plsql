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
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: multaSimplesTrintaPorCento.plsql
*
* Caso de uso: uc-05.03.00
*
*   Função de Calculo de Multa 
*    - Dado data de vencimento e de calculo, a função deve retornor o percentual a ser aplicado
*/

CREATE OR REPLACE FUNCTION fn_multa_simples_trinta_por_cento( dtVencimento      DATE
                                                            , dtCalculo         DATE
                                                            , flValor           NUMERIC
                                                            , inCodAcrescimo    INTEGER
                                                            , inCodTipo         INTEGER
                                                            ) RETURNS           NUMERIC AS $$

DECLARE
    flMulta         NUMERIC;
    inDiff          INTEGER;    
BEGIN
    -- recupera diferença em dias das datas
    inDiff := diff_datas_em_dias( dtVencimento, dtCalculo );
    flMulta := 0.00;

    IF dtVencimento <= dtCalculo  THEN
        IF ( inDiff > 0 ) THEN
            flMulta := ( flValor * 30 ) / 100;
        END IF;
    END IF;

    RETURN flMulta::NUMERIC(14,2);
END;
$$ LANGUAGE 'plpgsql';
