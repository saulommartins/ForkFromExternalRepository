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
* Casos de uso: uc-02.00.00
* Casos de uso: uc-02.08.01
*/

/*
$Log$
Revision 1.1  2007/09/26 14:43:57  gris
-- O módulo TCE-SC  funcionalidades deverá ir para a gestãp estação de contas.

Revision 1.11  2007/07/30 18:28:33  tonismar
alteração de uc-00.00.00 para uc-02.00.00

Revision 1.10  2006/07/05 20:37:45  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION tcers.totaliza_valor_movimentacao(VARCHAR,VARCHAR,VARCHAR,VARCHAR) RETURNS NUMERIC AS '
DECLARE
    stCodEstrutural     ALIAS FOR $1;
    stExercicio         ALIAS FOR $2;
    stCodEntidades      ALIAS FOR $3;
    stIdentificador     ALIAS FOR $4;
    
    nuTotal             NUMERIC := 0.00;
    nuVlCredito         NUMERIC := 0.00;
    nuVlDebito          NUMERIC := 0.00;
    crCursor            REFCURSOR;
    stSql               VARCHAR;

BEGIN

IF stIdentificador = ''R'' THEN
    stSql := ''
    SELECT
        coalesce(sum(vlc.vl_lancamento),0.00)
    FROM    contabilidade.plano_conta           AS pc
           ,contabilidade.plano_analitica       AS pa
           ,contabilidade.conta_credito         AS cc
           ,contabilidade.valor_lancamento      AS vlc
    WHERE pa.cod_conta        = pc.cod_conta
    AND   pa.exercicio        = pc.exercicio
    AND   cc.cod_plano        = pa.cod_plano
    AND   cc.exercicio        = pa.exercicio
    AND   vlc.cod_lote        = cc.cod_lote
    AND   vlc.tipo            = cc.tipo
    AND   vlc.sequencia       = cc.sequencia
    AND   vlc.exercicio       = cc.exercicio
    AND   vlc.tipo_valor      = cc.tipo_valor
    AND   vlc.cod_entidade    = cc.cod_entidade
    AND   vlc.tipo           != ''''I''''
    AND   pc.exercicio        = ''''''||stExercicio||''''''
    AND   vlc.cod_entidade   IN ( ''||stCodEntidades||'' )
    AND   pc.cod_estrutural   like  publico.fn_mascarareduzida( ''''''||stCodEstrutural||'''''' ) || ''''%''''
'';
    OPEN crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO nuVlCredito;
    CLOSE crCursor;

    RETURN nuVlCredito;
END IF;

IF stIdentificador = ''D'' THEN
    stSql := ''
    SELECT
        coalesce(sum(vld.vl_lancamento),0.00)
    FROM    contabilidade.plano_conta           AS pc
           ,contabilidade.plano_analitica       AS pa
           ,contabilidade.conta_debito          AS cd
           ,contabilidade.valor_lancamento      AS vld
    WHERE pa.cod_conta      = pc.cod_conta
    AND   pa.exercicio      = pc.exercicio
    AND   cd.cod_plano      = pa.cod_plano
    AND   cd.exercicio      = pa.exercicio
    AND   vld.cod_lote      = cd.cod_lote
    AND   vld.tipo          = cd.tipo
    AND   vld.sequencia     = cd.sequencia
    AND   vld.exercicio     = cd.exercicio
    AND   vld.tipo_valor    = cd.tipo_valor
    AND   vld.cod_entidade  = cd.cod_entidade
    AND   vld.tipo         != ''''I''''
    AND   pc.exercicio      = ''''''||stExercicio||''''''
    AND   vld.cod_entidade  IN ( ''||stCodEntidades||'' )
    AND   pc.cod_estrutural like  publico.fn_mascarareduzida( ''''''||stCodEstrutural||'''''' ) || ''''%''''
'';
    OPEN crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO nuVlDebito;
    CLOSE crCursor;
    
    RETURN nuVlDebito;
END IF;    
/*

    IF( substr( stCodEstrutural, 1, 5 ) = ''1.1.2'' ) THEN
        nuTotal := nuVlDebito + nuVlCredito;
    END IF;

    IF( substr( stCodEstrutural, 1, 3 ) = ''2.1'' ) THEN
        nuTotal := nuVlCredito + nuVlDebito;
    END IF;

    RETURN nuTotal;
*/    

END;
'LANGUAGE 'plpgsql'
