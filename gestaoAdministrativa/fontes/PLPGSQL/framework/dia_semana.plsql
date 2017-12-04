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
    * Formulário
    * Data de Criação: 17/10/2008
    
    
    * @author Analista      Dagiane Vieira
    * @author Desenvolvedor Rafael Garbin
    
    * @package URBEM
    * @subpackage 
       
    $Id: 
*/
CREATE OR REPLACE FUNCTION dia_semana(DATE) RETURNS VARCHAR AS $$
DECLARE
    dt        DATE:=$1;
    retorno   VARCHAR;
    stSQL     VARCHAR:='';     
    crCursor  REFCURSOR;
BEGIN
    stSQL := 'SELECT
                CASE extract(dow from '''||dt||'''::date)
                  WHEN 0 THEN ''Domingo''
                  WHEN 1 THEN ''Segunda''
                  WHEN 2 THEN ''Terça''
                  WHEN 3 THEN ''Quarta''
                  WHEN 4 THEN ''Quinta''
                  WHEN 5 THEN ''Sexta''
                  WHEN 6 THEN ''Sábado''
                  ELSE ''''
                END';
                
    OPEN crCursor FOR EXECUTE stSql;
        FETCH crCursor INTO retorno;
    CLOSE crCursor;    
    
    RETURN trim(retorno);
END;
$$ LANGUAGE 'plpgsql';
