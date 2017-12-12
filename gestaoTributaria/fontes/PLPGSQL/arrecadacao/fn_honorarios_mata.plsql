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
* $Id: fn_honorarios_mata.plsql 63888 2015-10-30 15:35:08Z evandro $
*
* Caso de uso: uc-05.03.00
*/

/*
$Log$
Revision 1.3  2007/10/04 21:07:11  fabio
ajuste para considerar as reducoes nos acrescimos, quando necessario

Revision 1.2  2007/09/28 20:55:51  fabio
alterada p/ considerar valor total, inclusive acrescimos

Revision 1.1  2007/09/24 15:02:55  fabio
funcao para calculo de honorarios advocaticios para cobrancas de divida ativa


*/

CREATE OR REPLACE FUNCTION fn_honorarios(date,date,float,integer,integer) RETURNS numeric as $$

    DECLARE
        dtVencimento    ALIAS FOR $1;
        dtDataCalculo   ALIAS FOR $2;
        flCorrigido     ALIAS FOR $3;
        inCodAcrescimo  ALIAS FOR $4;
        inCodTipo       ALIAS FOR $5;

        inCodInscricao  INTEGER;
        inExercicio     INTEGER;
        inCodModalidade INTEGER;
        inRegistro      INTEGER;
        inQtdParcelas   INTEGER;
        boIncidencia    TEXT;

        stSplit1        VARCHAR;
        stSplit2        VARCHAR;
        stSplit3        VARCHAR;
        nuSplit1        FLOAT = 0;
        nuSplit2        FLOAT = 0;
        nuSplit3        FLOAT = 0;

        lnSplit         VARCHAR;
        lnSplitRep      VARCHAR;
        btSplit         VARCHAR;

        stSQL           VARCHAR;
        reRECORD        RECORD;
        nuAcrescimo     FLOAT = 0;
        nuReducao       FLOAT = 0;
        iLimite         INTEGER = 0;
        iCount          INTEGER = 2;
        wCount          INTEGER = 0;
        inJudicial      INTEGER;
        inTmpTipo       INTEGER;
        inTmpAcrescimo  INTEGER;

        stTabela        VARCHAR;
        flValorTotal    FLOAT = 0;
        flHonorario     FLOAT = 0;
        inCGM           INTEGER;

    BEGIN

        inCodInscricao  := recuperarbufferinteiro( 'inCodInscricao'  );
        inExercicio     := recuperarbufferinteiro( 'inExercicio'     );
        inCodModalidade := recuperarbufferinteiro( 'inCodModalidade' );
        inRegistro      := recuperarbufferinteiro( 'inRegistro'      );
        boIncidencia    := recuperarbuffertexto  ( 'boIncidencia'    );

        SELECT qtd_parcela
          INTO inQtdParcelas
          FROM divida.modalidade_parcela
         WHERE cod_modalidade = inCodModalidade
           AND timestamp =  (
                              SELECT ultimo_timestamp
                                FROM divida.modalidade
                               WHERE cod_modalidade = inCodModalidade
                            );
        
        inJudicial := recuperarBufferInteiro( 'judicial' );
        -- RECUPERACAO DO VALOR DOS ACRESCIMOS QUE INCIDEM SOBRE O VALOR ORIGINAL
        stSplit1        := aplica_acrescimo_modalidade_honorarios_mata( inJudicial, inCodInscricao, inExercicio, inCodModalidade, 1, inRegistro, flCorrigido::numeric, dtVencimento, dtDataCalculo, boIncidencia ); 
        stSplit2        := aplica_acrescimo_modalidade_honorarios_mata( inJudicial, inCodInscricao, inExercicio, inCodModalidade, 2, inRegistro, flCorrigido::numeric, dtVencimento, dtDataCalculo, boIncidencia ); 
        stSplit3        := aplica_acrescimo_modalidade_honorarios_mata( inJudicial, inCodInscricao, inExercicio, inCodModalidade, 3, inRegistro, flCorrigido::numeric, dtVencimento, dtDataCalculo, boIncidencia ); 

        -- RECUPERACAO DOS VALORES DO ACRESCIMO TIPO 1
        lnSplit         := length(stSplit1);
        btSplit         := replace(stSplit1,';','');
        lnSplitRep      := length(btSplit);

        wCount := ( lnSplit::integer - lnSplitRep::integer ) / 3;

        WHILE ( iLimite < wCount ) LOOP

            nuAcrescimo := split_part(stSplit1,';',iCount);

            inTmpAcrescimo := split_part(stSplit1,';',iCount+1)::integer;
            inTmpTipo      := split_part(stSplit1,';',iCount+2)::integer;

            nuReducao := aplica_reducao_modalidade_acrescimo( inCodModalidade, inRegistro, nuAcrescimo::numeric, inTmpAcrescimo, inTmpTipo, dtVencimento, inQtdParcelas );
            nuSplit1  := nuSplit1 + ( nuAcrescimo - nuReducao );
        
            iCount := iCount + 3;
            iLimite := iLimite + 1;

        END LOOP;

        iCount := 2;
        iLimite := 0;

        -- RECUPERACAO DOS VALORES DO ACRESCIMO TIPO 2
        lnSplit         := length(stSplit2);
        btSplit         := replace(stSplit2,';','');
        lnSplitRep      := length(btSplit);

        wCount := ( lnSplit::integer - lnSplitRep::integer ) / 3;

        WHILE ( iLimite < wCount ) LOOP

            nuAcrescimo := split_part(stSplit2,';',iCount);

            inTmpAcrescimo := split_part(stSplit2,';',iCount+1)::integer;
            inTmpTipo      := split_part(stSplit2,';',iCount+2)::integer;

            nuReducao := aplica_reducao_modalidade_acrescimo( inCodModalidade, inRegistro, nuAcrescimo::numeric, inTmpAcrescimo, inTmpTipo, dtVencimento, inQtdParcelas );
            nuSplit2  := nuSplit2 + ( nuAcrescimo - nuReducao );
        
            iCount := iCount + 3;
            iLimite := iLimite + 1;

        END LOOP;

        iCount := 2;
        iLimite := 0;


        -- RECUPERACAO DOS VALORES DO ACRESCIMO TIPO 3
        lnSplit         := length(stSplit3);
        btSplit         := replace(stSplit3,';','');
        lnSplitRep      := length(btSplit);

        wCount := ( lnSplit::integer - lnSplitRep::integer ) / 3;

        WHILE ( iLimite < wCount ) LOOP

            nuAcrescimo := split_part(stSplit3,';',iCount);

            inTmpAcrescimo := split_part(stSplit3,';',iCount+1)::integer;
            inTmpTipo      := split_part(stSplit3,';',iCount+2)::integer;

            nuReducao := aplica_reducao_modalidade_acrescimo( inCodModalidade, inRegistro, nuAcrescimo::numeric, inTmpAcrescimo, inTmpTipo, dtVencimento, inQtdParcelas );
            nuSplit3  := nuSplit3 + ( nuAcrescimo - nuReducao );
        

            iCount := iCount + 3;
            iLimite := iLimite + 1;

        END LOOP;

        -- SOMA DO VALOR ORIGINAL + ACRESCIMOS - REDUCAO
        flValorTotal := flCorrigido + nuSplit1 + nuSplit2 + nuSplit3;

        -- CALCULO DO VALOR DOS HONORARIOS - 10% OU 20%
        inJudicial := recuperarBufferInteiro( 'judicial' );
        IF ( inJudicial = 1 ) THEN
            flHonorario := ( flValorTotal * 20) / 100 ;
        ELSE

            SELECT numcgm
              INTO inCGM
              FROM divida.cobranca_judicial
             WHERE cod_inscricao = inCodInscricao
               AND exercicio     = inExercicio::VARCHAR; 

            IF NOT FOUND THEN
                flHonorario := ( flValorTotal * 10) / 100 ;
            ELSE
                flHonorario := ( flValorTotal * 20) / 100 ;
            END IF;
        
        END IF;

        RETURN flHonorario::numeric(14,2);
    END;
$$ language 'plpgsql';
           
