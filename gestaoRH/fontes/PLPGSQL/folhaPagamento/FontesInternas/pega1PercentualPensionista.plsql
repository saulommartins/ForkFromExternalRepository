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
--    * Data de Criação: 26/08/2010
--
--
--    * @author Fabio Bertoldi Rodrigues
--
--    * Casos de uso: uc-04.00.00
--
--    $Id:  $ 
--*/
CREATE OR REPLACE FUNCTION pega1PercentualPensionista() RETURNS NUMERIC as $$
DECLARE
    stSql                       VARCHAR;
    nuPercentual                NUMERIC := 0.00;
    stEntidade                  VARCHAR;
    inPensionista               INTEGER := 0;
	inCodContrato               INTEGER := 0;
	inCodPeriodoMovimentacao    INTEGER;
	stDataInicialCompetencia    VARCHAR := '';
BEGIN
    stEntidade := recuperarBufferTexto('stEntidade');
	inPensionista := recuperarBufferInteiro('inPensionista');
	inCodContrato := recuperarBufferInteiro('inCodContrato');
    inCodPeriodoMovimentacao := recuperarBufferInteiro('inCodPeriodoMovimentacao'); 
	stDataInicialCompetencia := pega0datainicialcompetenciadoperiodomovimento(inCodPeriodoMovimentacao);

    stSql := '  SELECT percentual_pagamento::numeric
                  FROM  pessoal'||stEntidade||'.contrato_pensionista
                 WHERE  pessoal'||stEntidade||'.contrato_pensionista.cod_contrato = '|| inCodContrato ||'
				   AND (pessoal'||stEntidade||'.contrato_pensionista.dt_encerramento is NULL OR
				        pessoal'||stEntidade||'.contrato_pensionista.dt_encerramento < '|| stDataInicialCompetencia ||'
						)
				 ;
	         ';

    IF inPensionista = 1 THEN
	
       EXECUTE stSql
          INTO nuPercentual;
		  
    END IF;
		
    IF nuPercentual IS NULL THEN
        RETURN 0.00;
    ELSE
        RETURN nuPercentual;
    END IF;
END;
$$ LANGUAGE 'plpgsql';

