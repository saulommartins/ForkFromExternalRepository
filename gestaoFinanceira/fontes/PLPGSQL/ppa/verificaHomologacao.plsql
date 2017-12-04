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
/**
    * Verifica se o PPA está homologado
    * Data de Criação: 19/05/2009


    * @author Analista: Tonismar Bernardo <tonismar.bernardo@cnm.org.br>
    * @author Desenvolvedor: Eduardo Paculski Schitz <eduardo.schitz@cnm.org.br>

    * @package      URBEM
    * @subpackage   PPA

    * $Id: $
*/

CREATE OR REPLACE FUNCTION ppa.fn_verifica_homologacao(inCodPPA INTEGER) RETURNS BOOLEAN AS $$
DECLARE
    reRegistro                  RECORD;
    boRetorno                   BOOLEAN = false;
    stSql                       VARCHAR := '';
    tpTimestampPublicacao       TIMESTAMP;
    tpTimestampMacroObjetivo    TIMESTAMP;
    tpTimestampProgramaSetorial TIMESTAMP;
    tpTimestampPrograma         TIMESTAMP;
    tpTimestampAcao             TIMESTAMP;
BEGIN

    SELECT MAX(timestamp)
      INTO tpTimestampPublicacao
      FROM ppa.ppa_publicacao 
     WHERE cod_ppa = inCodPPA;

    IF (tpTimestampPublicacao IS NOT NULL) THEN
        SELECT MAX(macro_objetivo.timestamp) 
          INTO tpTimestampMacroObjetivo
          FROM ppa.macro_objetivo
         WHERE macro_objetivo.cod_ppa = inCodPPA;

        SELECT MAX(programa_setorial.timestamp) 
          INTO tpTimestampProgramaSetorial
          FROM ppa.macro_objetivo
          JOIN ppa.programa_setorial
            ON programa_setorial.cod_macro = macro_objetivo.cod_macro
         WHERE macro_objetivo.cod_ppa = inCodPPA;

        SELECT MAX(programa.ultimo_timestamp_programa_dados) 
          INTO tpTimestampPrograma
          FROM ppa.macro_objetivo
          JOIN ppa.programa_setorial
            ON programa_setorial.cod_macro = macro_objetivo.cod_macro
          JOIN ppa.programa
            ON programa.cod_setorial = programa_setorial.cod_setorial
         WHERE macro_objetivo.cod_ppa = inCodPPA;

        SELECT MAX(acao.ultimo_timestamp_acao_dados)
          INTO tpTimestampAcao
          FROM ppa.macro_objetivo
          JOIN ppa.programa_setorial
            ON programa_setorial.cod_macro = macro_objetivo.cod_macro
          JOIN ppa.programa
            ON programa.cod_setorial = programa_setorial.cod_setorial
          JOIN ppa.acao
            ON acao.cod_programa = programa.cod_programa
         WHERE macro_objetivo.cod_ppa = inCodPPA;

        IF (tpTimestampPublicacao > tpTimestampMacroObjetivo 
        AND tpTimestampPublicacao > tpTimestampProgramaSetorial
        AND tpTimestampPublicacao > tpTimestampPrograma
        AND tpTimestampPublicacao > tpTimestampAcao) THEN
            -- o PPA está homologado
            boRetorno = true;
        ELSE
            -- o PPA não está homologado
            boRetorno = false;
        END IF;
    
    END IF;

    RETURN boRetorno;

END;

$$ LANGUAGE 'plpgsql';
