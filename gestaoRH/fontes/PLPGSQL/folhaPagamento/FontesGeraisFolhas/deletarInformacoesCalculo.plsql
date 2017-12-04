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
--/**
--    * Função PLSQL
--    * Data de Criação: 11/07/2006
--
--
--    * @author Analista: Vandré Miguel Ramos
--    * @author Desenvolvedor: Diego Lemos de Souza
--
--    * @package URBEM
--    * @subpackage
--
--    $Revision: 25971 $
--    $Name$
--    $Author: souzadl $
--    $Date: 2007-10-10 13:08:17 -0300 (Qua, 10 Out 2007) $
--
--    * Casos de uso: uc-04.05.09
--*/
CREATE OR REPLACE FUNCTION  deletarInformacoesCalculo(VARCHAR,VARCHAR,INTEGER,VARCHAR) RETURNS BOOLEAN as $$ 
DECLARE
    stCodContratos                  ALIAS FOR $1;
    stTipoFolha                     ALIAS FOR $2;
    inCodComplementar               ALIAS FOR $3;
    stEntidadeParametro          ALIAS FOR $4;
    inCodPeriodoMovimentacao        INTEGER;
    inCodContrato                   INTEGER;
    inContador                      INTEGER;
    arCodContrato                   VARCHAR[];
    boRetorno                       BOOLEAN;
    stEntidade                   VARCHAR;
BEGIN
    stEntidade               := criarBufferEntidade(stEntidadeParametro);
    inCodPeriodoMovimentacao    := pega0CodigoPeriodoMovimentacaoAberta(); 
    arCodContrato               := string_to_array(stCodContratos,',');
    inContador := 1;
    LOOP
        inCodContrato = arCodContrato[inContador];
        IF inCodContrato IS NULL THEN
            EXIT;
        END IF;
        inContador := inContador + 1;
        IF stTipoFolha = 'S' THEN
            --Deleta os registros de eventos da folha salário
            --que foram copiados para da folha de férias para a salário
            --no momento que é feito o cálculo.
            --Esses eventos são copiados de férias no caso do pagamento de férias
            --tenha sido registrado para a folha salário
            boRetorno := deletarRegistroEventoDeFerias(inCodContrato,inCodPeriodoMovimentacao);

            --Deleta log de erro do calculo do contrato que está sendo calculado
            boRetorno := deletarLogErroCalculo(inCodContrato,inCodPeriodoMovimentacao);

            --Deleta eventos calculados do contrato que está sendo calculado
            boRetorno := deletarEventoCalculado(inCodContrato,inCodPeriodoMovimentacao);

            --Deleta bases vinculadas a evento registrados no contrato
            --Posteriormente inserindo as bases novamente
            boRetorno := deletarBaseDeRegistroEvento(inCodContrato,inCodPeriodoMovimentacao);
        END IF;
        IF stTipoFolha = 'C' THEN
            --Deleta os registros de eventos da folha complementar
            --que foram copiados para da folha de férias para a complementar
            --no momento que é feito o cálculo.
            --Esses eventos são copiados de férias no caso do pagamento de férias
            --tenha sido registrado para a folha complementar
            boRetorno := deletarRegistroEventoComplementarDeFerias(inCodContrato,inCodPeriodoMovimentacao,inCodComplementar);

            --Deleta log de erro do calculo do contrato que está sendo calculado
            boRetorno := deletarLogErroCalculoComplementar(inCodContrato,inCodPeriodoMovimentacao,inCodComplementar);

            --Deleta eventos calculados do contrato que está sendo calculado
            boRetorno := deletarEventoCalculadoComplementar(inCodContrato,inCodPeriodoMovimentacao,inCodComplementar);

            --Deleta bases vinculadas a evento registrados no contrato
            --Posteriormente inserindo as bases novamente
            boRetorno := deletarBaseDeRegistroEventoComplementar(inCodContrato,inCodPeriodoMovimentacao,inCodComplementar);
        END IF;
        IF stTipoFolha = 'F' THEN
            --Deleta log de erro do calculo do contrato que está sendo calculado
            boRetorno := deletarLogErroCalculoFerias(inCodContrato,inCodPeriodoMovimentacao);
        
            --Deleta eventos calculados do contrato que está sendo calculado
            boRetorno := deletarEventoCalculadoFerias(inCodContrato,inCodPeriodoMovimentacao);
        
            --Deleta bases vinculadas a evento registrados no contrato
            --Posteriormente inserindo as bases novamente
            boRetorno := deletarBaseDeRegistroEventoFerias(inCodContrato,inCodPeriodoMovimentacao);
        END IF;
        IF stTipoFolha = 'D' THEN
            --Deleta log de erro do calculo do contrato que está sendo calculado
            boRetorno := deletarLogErroCalculoDecimo(inCodContrato,inCodPeriodoMovimentacao);

            --Deleta eventos calculados do contrato que está sendo calculado
            boRetorno := deletarEventoCalculadoDecimo(inCodContrato,inCodPeriodoMovimentacao);

            --Deleta bases vinculadas a evento registrados no contrato
            --Posteriormente inserindo as bases novamente
            boRetorno := deletarBaseDeRegistroEventoDecimo(inCodContrato,inCodPeriodoMovimentacao);
        END IF;
        IF stTipoFolha = 'R' THEN
            --Deleta log de erro do calculo do contrato que está sendo calculado
            boRetorno := deletarLogErroCalculoRescisao(inCodContrato,inCodPeriodoMovimentacao);

            --Deleta eventos calculados do contrato que está sendo calculado
            boRetorno := deletarEventoCalculadoRescisao(inCodContrato,inCodPeriodoMovimentacao);

            --Deleta bases vinculadas a evento registrados no contrato
            --Posteriormente inserindo as bases novamente
            boRetorno := deletarBaseDeRegistroEventoRescisao(inCodContrato,inCodPeriodoMovimentacao);
        END IF;
    END LOOP;
    RETURN boRetorno;
END;
$$ LANGUAGE 'plpgsql'; 
