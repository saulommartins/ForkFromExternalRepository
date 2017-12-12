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
* Casos de uso: uc-02.04.04
* Casos de uso: uc-02.04.04
*/

/*
$Log$
Revision 1.6  2006/07/05 20:38:11  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION tesouraria.fn_recupera_carne(varchar,varchar) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    stNumeracao         ALIAS FOR $2;

    stDtVencimento      VARCHAR   := '';
    nuVlParcela         NUMERIC   := 0;
    nuVlDesconto        NUMERIC   := 0;
    nuVlJuros           NUMERIC   := 0;
    nuVlMulta           NUMERIC   := 0;
    nuVlJurosTotal      NUMERIC   := 0;
    nuVlMultaTotal      NUMERIC   := 0;

    stSql               VARCHAR   := '';
    stSqlFuncao         VARCHAR   := '';
    reRegistro          RECORD;
    crCursor            REFCURSOR;

BEGIN

    SELECT TO_CHAR( AP.vencimento, 'dd/mm/yyyy' )
          ,AP.valor
          ,APD.valor AS vl_desconto
          INTO stDtVencimento,nuVlParcela,nuVlDesconto
    FROM arrecadacao.carne   AS AC
        -- Join com carne_devolucao
        LEFT JOIN( SELECT ACD.numeracao
                         ,ACD.exercicio
                   FROM arrecadacao.carne_devolucao AS ACD
        ) AS ACD ON( AC.numeracao   = ACD.numeracao
                 AND AC.exercicio   = ACD.exercicio )
        ,arrecadacao.parcela AS AP
      -- Join com parcela desconto
        LEFT JOIN arrecadacao.parcela_desconto AS APD
        ON( AP.cod_parcela = APD.cod_parcela )
        -- Join com pagamento
        LEFT JOIN( SELECT AC.cod_parcela
                   FROM arrecadacao.carne           AS AC
                       ,arrecadacao.pagamento       AS APG
                   WHERE AC.numeracao = APG.numeracao
                     AND AC.exercicio = APG.exercicio
        ) AS APG ON( AP.cod_parcela = APG.cod_parcela )
      -- Join com parcela
    WHERE AC.cod_parcela  = AP.cod_parcela
      AND AC.numeracao    = stNumeracao
      AND AC.exercicio    = stExercicio
      AND APG.cod_parcela IS NULL
      AND ACD.numeracao   IS NULL
    ;


    stSql := '
        SELECT MTA.nom_tipo as tipo_acrescimo
              ,AF.nom_funcao
        FROM arrecadacao.carne              AS ACA
            ,arrecadacao.parcela            AS AP
            ,arrecadacao.lancamento         AS AL
            ,arrecadacao.lancamento_calculo AS ALC
            ,arrecadacao.calculo            AS AC
            ,monetario.credito              AS MC
            ,monetario.credito_acrescimo    AS MCA
            ,monetario.acrescimo            AS MA
            ,monetario.tipo_acrescimo       AS MTA
            ,monetario.formula_acrescimo    AS MFA
            ,administracao.funcao           AS AF
          -- Join com parcela
        WHERE ACA.cod_parcela     = AP.cod_parcela
          -- Join com lancamento
          AND AP.cod_lancamento  = AL.cod_lancamento
          -- Join com lancamento_calculo
          AND AL.cod_lancamento  = ALC.cod_lancamento
          -- Join com calculo
          AND ALC.cod_calculo    = AC.cod_calculo
          -- Join com monetario credito
          AND AC.cod_credito     = MC.cod_credito
          AND AC.cod_natureza    = MC.cod_natureza
          AND AC.cod_genero      = MC.cod_genero
          AND AC.cod_especie     = MC.cod_especie
          -- Join com credito acrescimo
          AND MC.cod_especie     = MCA.cod_especie
          AND MC.cod_genero      = MCA.cod_genero
          AND MC.cod_natureza    = MCA.cod_natureza
          AND MC.cod_credito     = MCA.cod_credito
          -- Join com acrescimo
          AND MCA.cod_acrescimo  = MA.cod_acrescimo
          -- Join com tipo_acrescimo
          AND MA.cod_tipo        = MTA.cod_tipo
          -- Tipo = multa
          AND (   LOWER( MTA.nom_tipo ) = ''juros''
               OR LOWER( MTA.nom_tipo ) = ''multa'' )
          -- Join com forma de acrescimo
          AND MA.cod_acrescimo   = MFA.cod_acrescimo
          -- Join com funcao
          AND MFA.cod_modulo     = AF.cod_modulo
          AND MFA.cod_biblioteca = AF.cod_biblioteca
          AND MFA.cod_funcao     = AF.cod_funcao
          -- Filtros
          AND ACA.exercicio      = '|| quote_literal(stExercicio) ||'
          AND ACA.numeracao      = '|| quote_literal(stNumeracao) ||'
        GROUP BY MTA.nom_tipo
                ,AF.nom_funcao
        ORDER BY MTA.nom_tipo
                ,AF.nom_funcao
        ;
    ';


FOR reRegistro IN EXECUTE stSql
LOOP
    IF reRegistro.nom_funcao IS NOT NULL THEN
        stSqlFuncao := 'SELECT '|| reRegistro.nom_funcao ||'( '|| quote_literal(stNumeracao) ||' , TO_CHAR( now(), ''dd/mm/yyyy'' ), '|| quote_literal(stExercicio) ||' ) ';

        OPEN crCursor FOR EXECUTE stSqlFuncao;
          IF( reRegistro.tipo_acrescimo = 'juros' ) THEN
            FETCH crCursor INTO nuVlJuros;
            nuVlJurosTotal := nuVlJurosTotal + nuVlJuros;
          END IF;
          IF( reRegistro.tipo_acrescimo = 'multa' ) THEN
            FETCH crCursor INTO nuVlMulta;
            nuVlMultaTotal := nuVlMultaTotal + nuVlMulta;
          END IF;
        CLOSE crCursor;
     END IF;

END LOOP;

    stSql := '
        SELECT CAST( '|| quote_literal( stNumeracao     ) ||' AS VARCHAR ) AS numeracao
              ,CAST( '|| quote_literal( stExercicio     ) ||' AS VARCHAR ) AS exercicio
              ,CAST( '|| quote_literal( stDtVencimento  ) ||' AS VARCHAR ) AS dt_vencimento
              ,CAST( '|| COALESCE( nuVlParcela   , 0.00 ) ||' AS NUMERIC ) AS vl_parcela
              ,CAST( '|| COALESCE( nuVlDesconto  , 0.00 ) ||' AS NUMERIC ) AS vl_desconto
              ,CAST( '|| COALESCE( nuVlMultaTotal, 0.00 ) ||' AS NUMERIC ) AS vl_multa
              ,CAST( '|| COALESCE( nuVlJurosTotal, 0.00 ) ||' AS NUMERIC ) AS vl_juros
        ;
    ';


IF stSql IS NULL THEN
    RAISE EXCEPTION 'Carne inválido( % )', stNumeracao;
ELSE
    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;
END IF;

RETURN;

END;

$$ language 'plpgsql';
