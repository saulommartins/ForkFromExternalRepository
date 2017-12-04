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
* $Revision: 13028 $
* $Name$
* $Author: diego $
* $Date: 2006-07-20 11:25:24 -0300 (Qui, 20 Jul 2006) $
*
* Casos de uso: uc-03.03.02 
*/

/*
$Log$
Revision 1.6  2006/07/20 14:25:24  diego
Retirada tag de log com erro.

Revision 1.5  2006/07/06 12:11:41  diego


*/
CREATE OR REPLACE FUNCTION publico.concat_nova_linha(text, text) RETURNS text AS '
    DECLARE
        t text;
    BEGIN
        IF character_length($1) > 0 THEN
            t = $1 ||'' <br> ''||  $2;
        ELSE
            t = $2;
        END IF;
        RETURN t;
     END;
' LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
BEGIN
    PERFORM 1
       FROM pg_proc
      WHERE proname = 'concatenar_nova_linha';

    IF FOUND THEN
        DROP AGGREGATE publico.concatenar_nova_linha (text ) ;
    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();

CREATE AGGREGATE publico.concatenar_nova_linha (
 sfunc = publico.concat_nova_linha,
 basetype = text,
 stype = text,
 initcond = ''
);
