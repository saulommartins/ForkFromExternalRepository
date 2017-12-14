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
--
-- Script de DDL e DML
--
-- URBEM Soluções de Gestão Pública Ltda
-- www.urbem.cnm.org.br
--
-- $Revision: 23160 $
-- $Name$
-- $Author: souzadl $
-- $Date: 2007-06-11 14:58:23 -0300 (Seg, 11 Jun 2007) $
--
-- Caso de uso: uc-0.0.0
-- Caso de uso: uc-00.00.00
--
--
CREATE OR REPLACE FUNCTION tipo_feriado( VARCHAR,varchar ) RETURNS VARCHAR AS '
DECLARE

    dtFeriado          ALIAS FOR $1;
    stEntidade      ALIAS FOR $2;
    stTipoFeriado      VARCHAR     := '''';
    stSQL              VARCHAR     := '''';
    reRegistro         RECORD;
BEGIN
    stSQL :=''
              SELECT
                  tipoferiado
                  ,max(dt_feriado)
               FROM
                  calendario''||stEntidade||''.feriado
              WHERE
                  dt_feriado = to_date('' || quote_literal(dtFeriado) || '',''''dd/mm/yyyy'''')
              GROUP by tipoferiado
              ORDER by tipoferiado
            '';


    FOR reRegistro IN EXECUTE stSQL LOOP
         stTipoFeriado := stTipoFeriado||''-''||reRegistro.tipoferiado;
    END LOOP;

    RETURN stTipoFeriado;
END;
' LANGUAGE 'plpgsql';
