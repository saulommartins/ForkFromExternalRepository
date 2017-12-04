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
* $Revision: 59612 $
* $Name$
* $Author: gelson $
* $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $
*
* Casos de uso: uc-02.08.08
* Casos de uso: uc-02.08.08
*/

/*
$Log$
Revision 1.1  2007/09/26 14:43:57  gris
-- O módulo TCE-SC  funcionalidades deverá ir para a gestãp estação de contas.

Revision 1.10  2006/07/28 14:18:27  cako
Bug #6568#

Revision 1.9  2006/07/05 20:37:44  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION tcerj.fn_exportacao_contacont(varchar) RETURNS SETOF RECORD AS '
DECLARE
    stExercicio             ALIAS FOR $1;
    stSql                   VARCHAR   := '''';
    reRegistro              RECORD;
BEGIN

stSql := ''
    SELECT
        pc.exercicio            as exercicio,
        replace(pc.cod_estrutural,''''.'''','''''''') as cod_estrutural,
        CASE WHEN pb.cod_banco IS NOT NULL THEN
            1
        ELSE
            CASE
                WHEN  substr(pc.cod_estrutural,1,1) = 3  THEN
                    2
                WHEN  substr(pc.cod_estrutural,1,1) = 4  THEN
                    3
            ELSE
                9
            END
        END as tipo_conta,
	CASE WHEN pc.cod_estrutural = ''''0.0.0.0.0.00.00.00.00.00'''' THEN
	    ''''0''''
	ELSE
            replace(publico.fn_codigo_superior(pc.cod_estrutural),''''.'''','''''''')
	END as cod_superior,
        pc.cod_conta            as cod_conta,
        pc.nom_conta            as nom_conta,
        publico.fn_nivel(pc.cod_estrutural) as nivel,
        CASE WHEN pa.cod_plano IS NOT NULL THEN
            ''''sim''''
        ELSE
            ''''nao''''
        END as recebe_lancamento,
        pb.cod_banco            as cod_banco,
        pb.cod_agencia          as cod_agencia,
        pb.conta_corrente       as conta_corrente,
        tc.cod_sequencial       as sequencial
    FROM
        contabilidade.plano_conta as pc
            LEFT OUTER JOIN
                contabilidade.plano_analitica as pa ON
                    ( pc.exercicio = pa.exercicio AND
                      pc.cod_conta = pa.cod_conta )
            LEFT OUTER JOIN
                contabilidade.plano_banco as pb ON
                    ( pa.exercicio = pb.exercicio AND
                      pa.cod_plano = pb.cod_plano )
            JOIN
                tcerj.plano_conta as tc ON
                    ( tc.cod_conta = pc.cod_conta AND
                      tc.exercicio = pc.exercicio )
            
    WHERE
        pc.exercicio    =   '''''' || stExercicio || ''''''
    GROUP BY
        pc.exercicio,
        pc.cod_estrutural,
        pc.cod_conta,
        pc.nom_conta,
        pa.cod_plano,
        pb.cod_banco,
        pb.cod_agencia,
        pb.conta_corrente,
        tc.cod_sequencial

'';


FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;

    RETURN;
END;
'language 'plpgsql';

