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
* $Revision: 8291 $
* $Name$
* $Author: gris $
* $Date: 2006-04-06 09:54:46 -0300 (Qui, 06 Abr 2006) $
*
* Casos de uso: uc-01.01.00
*/
CREATE OR REPLACE FUNCTION publico.concat_ponto(text, text) RETURNS text AS '
    DECLARE
        t text;
    BEGIN
        IF character_length($1) > 0 THEN
            t = $1;
            IF character_length($2) > 0 THEN
                t = $1 ||''.''||  $2;
            END IF;
        ELSE
            t = $2;
        END IF;
        RETURN t;
     END;
' LANGUAGE 'plpgsql';

create or replace function criar_agg_gp() returns boolean as $$
declare
  varAchouAgg varchar;
begin
  select proname into varAchouAgg
    from pg_proc
   where proname = 'concatenar_ponto';
  
  IF NOT FOUND THEN
    CREATE AGGREGATE publico.concatenar_ponto (
     sfunc = publico.concat_ponto,
     basetype = text,
     stype = text,
     initcond = ''
  );
  
  
  ELSE
    Drop AGGREGATE publico.concatenar_ponto (text ) ;
  
    CREATE AGGREGATE publico.concatenar_ponto (
     sfunc = publico.concat_ponto,
     basetype = text,
     stype = text,
     initcond = ''
    );
  END IF;
  return true;
end;
$$ LANGUAGE 'plpgsql';

SELECT criar_agg_gp();
