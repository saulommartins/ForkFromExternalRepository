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
--    * Data de Criação: 01/11/2006 
--
--
--    * @author Analista: Vandré Miguel Ramos
--
--    * @package URBEM
--    * @subpackage
--
--    $Revision: 23157 $
--    $Name$
--    $Author: souzadl $
--    $Date: 2007-06-11 14:19:50 -0300 (Seg, 11 Jun 2007) $
--
--    * Casos de uso: uc-04.05.18
--*/



CREATE OR REPLACE FUNCTION copiarRegistroEventoSalarioParaRegistroEventoRescisao(integer,integer) RETURNS BOOLEAN as $$

DECLARE

   inCodContrato                ALIAS FOR $1;
   inCodPeriodoMovimentacao     ALIAS FOR $2;
   stDesdobramento              VARCHAR :='S';
   inContador                   INTEGER := 0;
   inCodRegistro                INTEGER := 0;
   inNumCGM                     INTEGER := 0; 
   stTimestamp                  TIMESTAMP := now();

   boRetorno                    BOOLEAN := TRUE;
   stSql                        VARCHAR := '';
   stSql1                       VARCHAR := '';
   reRegistro                   RECORD;
    stEntidade VARCHAR := recuperarBufferTexto('stEntidade');
BEGIN
   stSql := 'CREATE TEMPORARY TABLE tmp_registro_evento_proporcional as
               SELECT  registro_evento.cod_evento
                      ,registro_evento.valor
                      ,registro_evento.quantidade
                      ,registro_evento.proporcional
               
                 FROM  folhapagamento'||stEntidade||'.registro_evento_periodo
                      ,folhapagamento'||stEntidade||'.registro_evento
                      ,folhapagamento'||stEntidade||'.ultimo_registro_evento
                      ,folhapagamento'||stEntidade||'.evento
               
                WHERE registro_evento_periodo.cod_registro = registro_evento.cod_registro
                  AND registro_evento.cod_registro         = ultimo_registro_evento.cod_registro
                  AND registro_evento.timestamp            = ultimo_registro_evento.timestamp
                  AND registro_evento.cod_evento           = ultimo_registro_evento.cod_evento
                  AND registro_evento.cod_evento           = evento.cod_evento
               
                  AND registro_evento_periodo.cod_contrato             = '||inCodContrato||'
                  AND registro_evento_periodo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                  AND evento.natureza                                  IN (''P'',''D'')
                  AND evento.evento_sistema                            = FALSE ';
    EXECUTE stSql;

    stSql := 'SELECT count(cod_evento) as contador, cod_evento FROM tmp_registro_evento_proporcional GROUP BY cod_evento';
    FOR reRegistro IN  EXECUTE stSql
    LOOP
        IF reRegistro.contador > 1 THEN
            stSql1 := 'DELETE FROM tmp_registro_evento_proporcional
                              WHERE proporcional = false AND cod_evento = '||reRegistro.cod_evento;
            EXECUTE stSql1;
        END IF;
    END LOOP;

   stSql := 'SELECT *  FROM tmp_registro_evento_proporcional ';

   FOR reRegistro IN EXECUTE stSql
   LOOP
     boRetorno := gravaRegistroEventoRescisao( inCodContrato,inCodPeriodoMovimentacao,reRegistro.cod_evento,reRegistro.valor, reRegistro.quantidade, stDesdobramento );
   END LOOP;

    --OBS para processarValorDeducaoDependente.:
    --Para funcionamento correto dessa PL, foi inserido no registro de evento uma verificação
    --que identifica se o contrato possui registros de eventos, caso não possua, é excluído
    --o dado da tabela folhapagamento.deducao_dependente que identifica a utilização de valor
    --de dedução de dependente.
    stSql := '    SELECT servidor.numcgm
                    FROM pessoal'||stEntidade||'.servidor_contrato_servidor
              INNER JOIN pessoal'||stEntidade||'.servidor
                      ON servidor.cod_servidor = servidor_contrato_servidor.cod_servidor
                   WHERE servidor_contrato_servidor.cod_contrato = '||inCodContrato;
    inNumCGM := selectIntoInteger(stSql);

    stSql := 'DELETE FROM folhapagamento'||stEntidade||'.deducao_dependente 
               WHERE numcgm = '||inNumCGM||'
                 AND cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                 AND cod_tipo = 2';

   RETURN TRUE;

END;
$$LANGUAGE 'plpgsql';


