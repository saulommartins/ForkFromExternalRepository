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
--
-- script de funcao PLSQL
-- 
-- URBEM Soluções de Gestão Pública Ltda
-- www.urbem.cnm.org.br
--
-- $Revision: 23095 $
-- $Name$
-- $Autor: Marcia $
-- Date: 2005/12/28 10:50:00 $
--
-- Caso de uso: uc-04.05.48
--
-- Objetivo: 
--
-- O retorno da funcao inteiro e apresenta a quantidade total dos vales.
-- Mantem montada uma tabela temporaria com os dados de vale, quantidade e valor individualizado.
-- Nota: apos o uso para avaliacao dropar a tmp ou esta sera destruida ao final da transacao.
--
--
--drop function pega1QtdTotalDeValesTransportePorContratoNaDataCriandoTmp(integer,varchar);



CREATE OR REPLACE FUNCTION pega1QtdTotalDeValesTransportePorContratoNaDataCriandoTmp(integer,varchar) RETURNS integer as '

DECLARE
    inCodContrato              ALIAS FOR $1;
    stTimestamp                ALIAS FOR $2;

    inQtdValesTotal            INTEGER;
    nuCriaTemporario           NUMERIC;
    reRegistro                 RECORD;

    stSqlTmp                   VARCHAR;

    stDelete                   VARCHAR := '''';

stEntidade VARCHAR := recuperarBufferTexto(''stEntidade'');
 BEGIN


  SELECT tablename
    INTO stDelete
    FROM pg_tables
   WHERE tablename = ''tmp_vales_trasporte_do_contrato'';

   IF stDelete <> '''' THEN
        DROP TABLE tmp_vales_trasporte_do_contrato;
   END IF;


   nuCriaTemporario := pega0ValorTotalDeValesTransportePorContratoNaDataCriandoTmp(inCodcontrato,stTimestamp);

   stSqlTmp := '' SELECT sum(quantidade) as soma FROM tmp_vales_trasporte_do_contrato ''; 
   EXECUTE stSqlTmp;


   FOR reRegistro IN  EXECUTE stSqltMP LOOP
      inQtdValesTotal := reRegistro.soma;
   END LOOP;

   RETURN inQtdValesTotal;

END;

' LANGUAGE 'plpgsql';



