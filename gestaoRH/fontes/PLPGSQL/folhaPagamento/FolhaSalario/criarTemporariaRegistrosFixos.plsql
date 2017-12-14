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
--    * Data de Criação: 18/04/2006
--
--
--    * @author Analista: Vandré Miguel Ramos
--    * @author Desenvolvedor: Diego Lemos de Souza
--
--    * @package URBEM
--    * @subpackage
--
--    $Revision: 23133 $
--    $Name$
--    $Author: souzadl $
--    $Date: 2007-06-07 12:40:10 -0300 (Qui, 07 Jun 2007) $
--
--    * Casos de uso: uc-04.05.09
--*/

CREATE OR REPLACE FUNCTION criarTemporariaRegistrosFixos() RETURNS BOOLEAN as $$

DECLARE
stSql                       VARCHAR := '';
stNatureza                  VARCHAR := '';
boRetorno                   BOOLEAN := TRUE;
inCodContrato               INTEGER;
inCodEspecialidade          INTEGER;
inCodFuncao                 INTEGER;
inCodSubDivisao             INTEGER;   
inCodPeriodoMovimentacao    INTEGER;  
reRegistro                  RECORD;
stEntidade               VARCHAR;
BEGIN
    stEntidade              := recuperarBufferTexto('stEntidade');
    inCodContrato              := recuperarBufferInteiro('inCodContrato');
    inCodPeriodoMovimentacao   := recuperarBufferInteiro('inCodPeriodoMovimentacao');
    inCodSubDivisao            := recuperarBufferInteiro('inCodSubDivisao');
    inCodFuncao                := recuperarBufferInteiro('inCodFuncao');
    inCodEspecialidade         := recuperarBufferInteiro('inCodEspecialidade');
    
    stSql := 'DELETE FROM folhapagamento'||stEntidade||'.registro_evento_fixos';
    execute stSql;
    

    stSql := ' INSERT INTO folhapagamento'||stEntidade||'.registro_evento_fixos(
                SELECT
                      fpre.cod_evento
                     ,evento.codigo
                     ,fpre.cod_registro 
                     ,fprepe.cod_contrato
                     ,COALESCE(fpre.valor,0.00) as valor
                     ,COALESCE(fpre.quantidade,0.00) as quantidade
                     ,fpre.proporcional
                     , (SELECT parcela FROM folhapagamento'||stEntidade||'.registro_evento_parcela
                          WHERE fpure.cod_registro = registro_evento_parcela.cod_registro
                            AND fpure.cod_evento = registro_evento_parcela.cod_evento
                            AND fpure.timestamp = registro_evento_parcela.timestamp) as parcela
                     ,fprepe.cod_periodo_movimentacao
                     ,fpure.timestamp
                     ,pega1FormulaEvento(fpre.cod_evento,1,'||inCodSubDivisao||','||inCodFuncao||','||inCodEspecialidade||',evento.natureza) as formula
                     ,evento.natureza
                     ,1 as cod_configuracao      
                FROM                                                     
                      folhapagamento'||stEntidade||'.registro_evento_periodo as fprepe             
                JOIN  folhapagamento'||stEntidade||'.ultimo_registro_evento  as fpure
                  ON  fprepe.cod_registro = fpure.cod_registro
          
                JOIN  folhapagamento'||stEntidade||'.registro_evento         as fpre                      
                  ON  fpure.cod_registro = fpre.cod_registro
                 AND  fpure.timestamp    = fpre.timestamp
    
                JOIN  folhapagamento'||stEntidade||'.evento as evento
                  ON  evento.cod_evento = fpre.cod_evento
         
          
               WHERE  fprepe.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                 AND  fprepe.cod_registro             = fpre.cod_registro
                 AND  fprepe.cod_contrato             = '||inCodContrato||' 
                 AND  fpre.proporcional               = FALSE
            ORDER BY  fpre.cod_evento);';

    EXECUTE stSql;

    RETURN boRetorno;
END;
$$ LANGUAGE 'plpgsql';
