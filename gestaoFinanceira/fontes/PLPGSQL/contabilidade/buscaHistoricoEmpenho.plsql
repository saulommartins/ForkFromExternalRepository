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
* Script de função PLPGSQL
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Revision: 15438 $
* $Name$
* $Author: jose.eduardo $
* $Date: 2006-09-14 13:38:39 -0300 (Qui, 14 Set 2006) $
*
* Casos de uso: uc-02.02.27
*/

/*
$Log$
Revision 1.3  2006/09/14 16:36:47  jose.eduardo
Bug #6815#

Revision 1.2  2006/08/24 14:13:57  jose.eduardo
Bug #6765#

Revision 1.1  2006/08/23 17:00:37  jose.eduardo
Bug #6765#


*/

CREATE OR REPLACE FUNCTION contabilidade.fn_busca_historico_empenho(INTEGER, INTEGER, VARCHAR, VARCHAR) RETURNS varchar AS $$
DECLARE
    inCodLote           ALIAS FOR $1;
    inCodEntidade       ALIAS FOR $2;
    stExercicio         ALIAS FOR $3;
    stTipo              ALIAS FOR $4;
    stSql               VARCHAR   := '';
    arRetorno           VARCHAR   := '';
    crCursor            REFCURSOR;


BEGIN

    stSql := '
          SELECT descricao || credor
          FROM (
              SELECT CASE WHEN REPLACE(TRIM(COALESCE(pe.descricao,'''')),''\r\n'','''') = '''' 
                          THEN ''''
                          ELSE REPLACE(TRIM(COALESCE(pe.descricao,'''')),''\r\n'','''') || '' - ''
                     END AS descricao
                   , CASE WHEN REPLACE(TRIM(COALESCE(cgm.nom_cgm,'''')),''\r\n'','''') = '''' 
                          THEN ''''
                          ELSE ''Credor: '' || REPLACE(TRIM(COALESCE(cgm.nom_cgm,'''')),''\r\n'','''')
                   END AS credor              
                  FROM empenho.pre_empenho AS pe
            INNER JOIN sw_cgm AS cgm 
                    ON cgm.numcgm = pe.cgm_beneficiario
            INNER JOIN empenho.empenho AS e 
                    ON e.cod_pre_empenho = pe.cod_pre_empenho
                   AND e.exercicio       = pe.exercicio      
            INNER JOIN contabilidade.empenhamento AS ce 
                    ON ce.cod_entidade      = e.cod_entidade   
                   AND ce.exercicio_empenho = e.exercicio       
                   AND ce.cod_empenho       = e.cod_empenho    
            INNER JOIN contabilidade.lancamento_empenho AS le 
                    ON le.cod_entidade = ce.cod_entidade   
                   AND le.exercicio    = ce.exercicio       
                   AND le.cod_lote     = ce.cod_lote       
                   AND le.tipo         = ce.tipo           
                   AND le.sequencia    = ce.sequencia      
                   AND NOT le.estorno
                 WHERE le.exercicio    = ''' || stExercicio || '''
                   AND le.cod_entidade = ' || inCodEntidade || '
                   AND le.cod_lote     = ' || inCodLote || '
                   AND le.tipo         = ''' || stTipo || '''

                ) AS tabela
    ';

    OPEN  crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO arRetorno;
    CLOSE crCursor;

    RETURN arRetorno;
END;
$$language 'plpgsql';
