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
* $Revision: 12710 $
* $Name$
* $Author: andre.almeida $
* $Date: 2006-07-14 14:58:46 -0300 (Sex, 14 Jul 2006) $
*
* Casos de uso: uc-02.04.02
* Casos de uso: uc-02.04.02
*/

/*
$Log$
Revision 1.5  2006/07/14 17:58:46  andre.almeida
Bug #6556#

Alterado scripts de NOT IN para NOT EXISTS.

Revision 1.4  2006/07/05 20:38:11  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION tesouraria.fn_valida_inclusao_terminal() RETURNS TRIGGER AS '
DECLARE
    boExisteIpExercicio    BOOLEAN := false;
    boExisteCodExercicio   BOOLEAN := false;
    stSql                  VARCHAR := '''';
BEGIN

    SELECT CASE WHEN TT.cod_terminal IS NOT NULL
             THEN true
             ELSE false
           END
        INTO boExisteIpExercicio
    FROM tesouraria.terminal AS TT
    WHERE NOT EXISTS ( SELECT 1
                         FROM tesouraria.terminal_desativado AS TTD
                        WHERE TTD.cod_terminal = TT.cod_terminal
                          AND TTD.exercicio    = TT.exercicio
                          AND TTD.timestamp    = TT.timestamp
                     )
    AND TT.ip        = NEW.ip
    AND TT.exercicio = NEW.exercicio
    ;


    SELECT CASE WHEN TT.cod_terminal IS NOT NULL
             THEN true
             ELSE false
           END
        INTO boExisteCodExercicio
    FROM tesouraria.terminal AS TT
    WHERE NOT EXISTS ( SELECT 1
                         FROM tesouraria.terminal_desativado AS TTD
                        WHERE TTD.cod_terminal = TT.cod_terminal
                          AND TTD.exercicio    = TT.exercicio
                          AND TTD.timestamp    = TT.timestamp
                     )
     AND TT.cod_terminal = NEW.cod_terminal
     AND TT.exercicio    = NEW.exercicio
     ;


    IF( boExisteIpExercicio OR boExisteCodExercicio) THEN
        RETURN NULL;
    ELSE
        RETURN NEW;
    END IF;
END;
' LANGUAGE 'plpgsql';
