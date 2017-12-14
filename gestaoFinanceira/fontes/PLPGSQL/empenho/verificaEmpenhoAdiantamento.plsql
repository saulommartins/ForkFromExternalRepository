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
* $Revision: 28959 $
* $Name$
* $Author: eduardoschitz $
* $Date: 2008-04-02 15:52:08 -0300 (Qua, 02 Abr 2008) $
*
* Casos de uso: uc-02.03.12,uc-02.03.16,uc-02.03.05,uc-02.04.05,uc-02.03.31
*/

/*
$Log$
Revision 1.4  2007/08/20 20:18:52  luciano
Bug#9663#,Bug#9921#

Revision 1.3  2007/06/27 20:31:56  luciano
Bug#9108# 

Revision 1.2  2007/05/18 14:32:40  luciano
Bug#9108#

Revision 1.1  2007/05/18 14:21:28  luciano
adicionado ao repositorio

*/

CREATE OR REPLACE FUNCTION empenho.verifica_adiantamento(VARCHAR,INTEGER,INTEGER) RETURNS BOOLEAN as $$
DECLARE
    stExercicio         ALIAS FOR $1;
    inCodOrdem          ALIAS FOR $2;
    inCodEntidade       ALIAS FOR $3;
    boSaida             BOOLEAN  := false;
    stSql               VARCHAR  := '';
    reRegistro          RECORD;
BEGIN
    stSql := '
            select 
                em.cod_categoria 
            from     
            empenho.pagamento_liquidacao as pl 
            join empenho.nota_liquidacao as nl   
            on (    nl.exercicio    = pl.exercicio_liquidacao   
                and nl.cod_entidade = pl.cod_entidade   
                and nl.cod_nota     = pl.cod_nota   
              )   
            join empenho.empenho as em   
            on (     em.cod_empenho  = nl.cod_empenho   
                and em.cod_entidade = nl.cod_entidade   
                and em.exercicio    = nl.exercicio_empenho   
              )
            WHERE 
                    pl.exercicio    = ''' || stExercicio   || '''   
                and pl.cod_entidade = ' || inCodEntidade || '   
                and pl.cod_ordem    = ' || inCodOrdem    || '           
            ';
    FOR reRegistro IN EXECUTE stSql
    LOOP
        IF reRegistro.cod_categoria = 2 OR reRegistro.cod_categoria = 3
            THEN boSaida := true ;
        END IF;
 
    END LOOP;

    RETURN boSaida;
END;
$$ LANGUAGE 'plpgsql';
