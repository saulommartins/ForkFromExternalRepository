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
* Casos de uso: uc-02.02.02
*/

/*
$Log$
Revision 1.6  2006/07/05 20:37:31  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION contabilidade.fn_tipo_conta_plano(varchar,varchar) RETURNS VARCHAR AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    stMask              ALIAS FOR $2;
    stMascaraReduzida   VARCHAR := '';
    stSql               VARCHAR := '';
    inOut               INTEGER := 0 ;
    stRetorno           VARCHAR := '';
BEGIN

 stMascaraReduzida := publico.fn_mascarareduzida(stMask);

       SELECT count(*) into inOut
         FROM contabilidade.plano_conta
   INNER JOIN contabilidade.plano_analitica
           ON plano_analitica.cod_conta = plano_conta.cod_conta
          AND plano_analitica.exercicio = plano_conta.exercicio
        WHERE plano_conta.cod_estrutural  =  stMask
          AND plano_conta.exercicio       =  stExercicio;
        
    IF inOut > 0 THEN
     stRetorno := 'A';
    ELSE
     stRetorno := 'S';
    END IF;
    RETURN stRetorno;

END;
$$ LANGUAGE 'plpgsql';