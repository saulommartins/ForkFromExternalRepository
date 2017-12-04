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
/* recuperar_dirf_prestadores_servico
 * 
 * Data de Criação : 23/01/2009


 * @author Analista : Dagiane   
 * @author Desenvolvedor : Rafael Garbin
 
 * @package URBEM
 * @subpackage 

 $Id:$
 */

CREATE OR REPLACE FUNCTION recuperar_dirf_prestadores_servico(VARCHAR, INTEGER, INTEGER, INTEGER) RETURNS SETOF colunasDirfPrestadoresServico AS $$
DECLARE
    stEntidade                          ALIAS FOR $1;
    inExercicio                         ALIAS FOR $2;    
    inCodEntidade                       ALIAS FOR $3;    
    inSequenciaEventos                  ALIAS FOR $4;
    rwDirf                              colunasDirfPrestadoresServico%ROWTYPE;
    stSql                               VARCHAR;
    reRegistro                          RECORD;
    rePeriodos                          RECORD;
    nuTotalEmpenhoCGM                   NUMERIC :=0.00;
    nuTotalRetencoesCGM                 NUMERIC :=0.00;
    inNumCGMAux                         INTEGER :=0;    
    inMes                               INTEGER;
    arMeses1                            NUMERIC[];
    arMeses2                            NUMERIC[];
    arMeses3                            NUMERIC[];
BEGIN
    
    stSql := 'SELECT * FROM tmp_prestador_servico';

    FOR reRegistro IN EXECUTE stSql LOOP    
        
        FOR inMes IN 1..12 LOOP
            IF reRegistro.mes = inMes THEN
                IF inSequenciaEventos = 1 THEN                    
                    arMeses1[inMes] := reRegistro.vl_empenhado;
                    IF reRegistro.vl_retencao_irrf > 0 THEN
                        IF reRegistro.ident_especie_beneficiario = 1 THEN
                            arMeses2[inMes] := reRegistro.vl_retencao_inss;
                        ELSE
                            arMeses2[inMes] := 0.00;
                        END IF;
                    ELSE
                        arMeses2[inMes] := 0.00;
                    END IF;
                    arMeses3[inMes] := reRegistro.vl_retencao_irrf;
                END IF;

                IF inSequenciaEventos = 2 THEN                    
                    IF reRegistro.vl_retencao_irrf > 0 THEN
                        IF reRegistro.ident_especie_beneficiario = 1 THEN
                            arMeses1[inMes] := 0.00;
                        ELSE
                            arMeses1[inMes] := reRegistro.vl_retencao_inss;
                        END IF;
                    ELSE 
                        arMeses1[inMes] := 0.00;
                    END IF;
                    arMeses2[inMes] := 0.00;
                    arMeses3[inMes] := 0.00;
                END IF;
            ELSE 
                arMeses1[inMes] := 0.00;
                arMeses2[inMes] := 0.00;
                arMeses3[inMes] := 0.00;
            END IF;
        END LOOP;

        IF reRegistro.numcgm != inNumCGMAux THEN

            stSql := 'SELECT vl_empenho
                        FROM recuperarDirfPrestadoresServicoValorEmpenhoExercicio('|| quote_literal(stEntidade) ||', '|| inExercicio ||', '|| inCodEntidade ||', '|| reRegistro.numcgm ||', '|| quote_literal(reRegistro.tipo) ||')';
    
            nuTotalEmpenhoCGM   := selectintonumeric(stSql);

            stSql := ' SELECT consultar_total_retencoes_cgm('|| inExercicio ||', '|| reRegistro.numcgm ||', '|| inCodEntidade ||', '|| reRegistro.cod_conta ||', '|| quote_literal(stEntidade) ||','''||reRegistro.tipo_conta||''')';

            nuTotalRetencoesCGM := selectintonumeric(stSql);

        END IF;

        inNumCGMAux                := reRegistro.numcgm;

        IF nuTotalRetencoesCGM != 0.00 THEN
            rwDirf.nome_beneficiario        := reRegistro.nom_cgm;
            rwDirf.beneficiario             := reRegistro.beneficiario;
            rwDirf.cod_retencao             := reRegistro.cod_dirf;
            rwDirf.ident_especie_beneficiario := reRegistro.ident_especie_beneficiario;
            rwDirf.uso_declarante           := NULL;
            rwDirf.jan1                     := arMeses1[1];
            rwDirf.jan2                     := arMeses2[1];
            rwDirf.jan3                     := arMeses3[1];
            rwDirf.fev1                     := arMeses1[2];
            rwDirf.fev2                     := arMeses2[2];
            rwDirf.fev3                     := arMeses3[2];
            rwDirf.mar1                     := arMeses1[3];
            rwDirf.mar2                     := arMeses2[3];
            rwDirf.mar3                     := arMeses3[3];
            rwDirf.abr1                     := arMeses1[4];
            rwDirf.abr2                     := arMeses2[4];
            rwDirf.abr3                     := arMeses3[4];
            rwDirf.mai1                     := arMeses1[5];
            rwDirf.mai2                     := arMeses2[5];
            rwDirf.mai3                     := arMeses3[5];
            rwDirf.jun1                     := arMeses1[6];
            rwDirf.jun2                     := arMeses2[6];
            rwDirf.jun3                     := arMeses3[6];
            rwDirf.jul1                     := arMeses1[7];
            rwDirf.jul2                     := arMeses2[7];
            rwDirf.jul3                     := arMeses3[7];
            rwDirf.ago1                     := arMeses1[8];
            rwDirf.ago2                     := arMeses2[8];
            rwDirf.ago3                     := arMeses3[8];
            rwDirf.set1                     := arMeses1[9];
            rwDirf.set2                     := arMeses2[9];
            rwDirf.set3                     := arMeses3[9];
            rwDirf.out1                     := arMeses1[10];
            rwDirf.out2                     := arMeses2[10];
            rwDirf.out3                     := arMeses3[10];
            rwDirf.nov1                     := arMeses1[11];
            rwDirf.nov2                     := arMeses2[11];
            rwDirf.nov3                     := arMeses3[11];
            rwDirf.dez1                     := arMeses1[12];
            rwDirf.dez2                     := arMeses2[12];
            rwDirf.dez3                     := arMeses3[12];
            rwDirf.dec1                     := 0.00;
            rwDirf.dec2                     := 0.00;
            rwDirf.dec3                     := 0.00;
            
            RETURN NEXT rwDirf;               
        ELSE
            IF nuTotalEmpenhoCGM >= 6000.00 THEN
                rwDirf.nome_beneficiario        := reRegistro.nom_cgm;
                rwDirf.beneficiario             := reRegistro.beneficiario;
                rwDirf.cod_retencao             := reRegistro.cod_dirf;
                rwDirf.ident_especie_beneficiario := reRegistro.ident_especie_beneficiario;
                rwDirf.uso_declarante           := NULL;
                rwDirf.jan1                     := arMeses1[1];
                rwDirf.jan2                     := arMeses2[1];
                rwDirf.jan3                     := arMeses3[1];
                rwDirf.fev1                     := arMeses1[2];
                rwDirf.fev2                     := arMeses2[2];
                rwDirf.fev3                     := arMeses3[2];
                rwDirf.mar1                     := arMeses1[3];
                rwDirf.mar2                     := arMeses2[3];
                rwDirf.mar3                     := arMeses3[3];
                rwDirf.abr1                     := arMeses1[4];
                rwDirf.abr2                     := arMeses2[4];
                rwDirf.abr3                     := arMeses3[4];
                rwDirf.mai1                     := arMeses1[5];
                rwDirf.mai2                     := arMeses2[5];
                rwDirf.mai3                     := arMeses3[5];
                rwDirf.jun1                     := arMeses1[6];
                rwDirf.jun2                     := arMeses2[6];
                rwDirf.jun3                     := arMeses3[6];
                rwDirf.jul1                     := arMeses1[7];
                rwDirf.jul2                     := arMeses2[7];
                rwDirf.jul3                     := arMeses3[7];
                rwDirf.ago1                     := arMeses1[8];
                rwDirf.ago2                     := arMeses2[8];
                rwDirf.ago3                     := arMeses3[8];
                rwDirf.set1                     := arMeses1[9];
                rwDirf.set2                     := arMeses2[9];
                rwDirf.set3                     := arMeses3[9];
                rwDirf.out1                     := arMeses1[10];
                rwDirf.out2                     := arMeses2[10];
                rwDirf.out3                     := arMeses3[10];
                rwDirf.nov1                     := arMeses1[11];
                rwDirf.nov2                     := arMeses2[11];
                rwDirf.nov3                     := arMeses3[11];
                rwDirf.dez1                     := arMeses1[12];
                rwDirf.dez2                     := arMeses2[12];
                rwDirf.dez3                     := arMeses3[12];
                rwDirf.dec1                     := 0.00;
                rwDirf.dec2                     := 0.00;
                rwDirf.dec3                     := 0.00;
                
                RETURN NEXT rwDirf;               
            END IF;
        END IF;
    
    END LOOP;    
    
END;
$$ LANGUAGE 'plpgsql';