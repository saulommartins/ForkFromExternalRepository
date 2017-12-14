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
-- /**
--     * Pl que retorna quando o servidor/pensionista irá completar a idade passada por parametro. 
--     * Data de Criação: 14/03/2007
-- 
-- 
--     * @author Diego Lemos de Souza
-- 
--     * Casos de uso: uc-04.05.00
-- 
--     $Id: retornaDataAniversarioCom.sql 31697 2008-08-04 19:33:31Z souzadl $
-- 
-- */
CREATE OR REPLACE FUNCTION retornaDataAniversarioCom(INTEGER,INTEGER) RETURNS DATE AS $$
DECLARE
    inIdade         ALIAS FOR $1;
    inCodContrato   ALIAS FOR $2;
    stEntidade   VARCHAR := recuperarBufferTexto('stEntidade');
    stSql           VARCHAR;
BEGIN
    stSql := '  SELECT (sw_cgm_pessoa_fisica.dt_nascimento + INTERVAL '||quote_literal(inIdade)||' YEAR)::date as data_65
                  FROM ( SELECT servidor.numcgm
                           FROM pessoal'||stEntidade||'.servidor_contrato_servidor
                              , pessoal'||stEntidade||'.servidor
                              , pessoal'||stEntidade||'.contrato_servidor
                          WHERE servidor_contrato_servidor.cod_contrato = contrato_servidor.cod_contrato
                            AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                            AND servidor_contrato_servidor.cod_contrato = '||inCodContrato||'
                            AND NOT EXISTS (SELECT 1
                                              FROM pessoal'||stEntidade||'.contrato_servidor_caso_causa
                                             WHERE contrato_servidor_caso_causa.cod_contrato = contrato_servidor.cod_contrato)    
                          UNION ALL
                         SELECT pensionista.numcgm
                           FROM pessoal'||stEntidade||'.contrato_pensionista
                              , pessoal'||stEntidade||'.pensionista
                          WHERE contrato_pensionista.cod_pensionista = pensionista.cod_pensionista
                            AND contrato_pensionista.cod_contrato = '||inCodContrato||'
                            AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente) as servidor_pensionista
                    , sw_cgm_pessoa_fisica
                WHERE servidor_pensionista.numcgm = sw_cgm_pessoa_fisica.numcgm
             GROUP BY sw_cgm_pessoa_fisica.numcgm
                    , sw_cgm_pessoa_fisica.dt_nascimento';
    return selectIntoVarchar(stSql);    
END;
$$ LANGUAGE 'plpgsql';
