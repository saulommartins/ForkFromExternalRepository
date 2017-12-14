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
* $Id: fn_busca_modalidade_atividade.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.00
*/

/*
$Log$
Revision 1.2  2006/09/15 10:19:58  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION economico.fn_busca_modalidade_atividade ( integer, integer, integer ) RETURNS VARCHAR AS '
declare
    inInscricaoEconomica      ALIAS FOR $1;
    inCodAtividade                  ALIAS FOR $2;
    inOcorrenciaAtividade                  ALIAS FOR $3;
    
    stModalidade                  VARCHAR;
    inCodModalidade                     INTEGER;
begin
            
    SELECT 
        mod.cod_modalidade, mod.nom_modalidade
    INTO 
        inCodModalidade, stModalidade
    FROM 
        economico.modalidade_lancamento as mod
        INNER JOIN economico.cadastro_economico_modalidade_lancamento as ceml
            ON ceml.cod_modalidade = mod.cod_modalidade
    WHERE 
        ceml.inscricao_economica = inInscricaoEconomica
        and ceml.ocorrencia_atividade = inOcorrenciaAtividade
        and ceml.cod_atividade = inCodAtividade;
        
    
    IF ( inCodModalidade is not null ) THEN
            SELECT 
                mod.cod_modalidade, mod.nom_modalidade
              INTO
                inCodModalidade, stModalidade
              FROM
                   economico.modalidade_lancamento as mod
                   INNER JOIN economico.atividade_modalidade_lancamento as aml
                    ON aml.cod_modalidade = mod.cod_modalidade
                WHERE
                    aml.cod_atividade = inCodAtividade ;
    END IF;
    
    IF ( stModalidade is null ) THEN
        stModalidade := ''Não Informado'';
    END IF;
    return stModalidade;
    --
end;
'language 'plpgsql';
