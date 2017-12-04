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
 * Titulo do arquivo Formata o código do PISPASEP
 * Data de Criação   : 16/09/2008


 * @author Analista      Dagiane
 * @author Desenvolvedor Diego 
 
 * @package URBEM
 * @subpackage 

 * @ignore # só use se for paginas que o cliente visualiza, se for mapeamento ou classe de negocio não se usa

 $Id:$
 */

CREATE OR REPLACE FUNCTION diff_datas_ano_mes_dia(DATE,DATE) RETURNS VARCHAR as $$
DECLARE
    dtInicialPar alias for $1;
    dtFinalPar   alias for $2;
    dtInicial date;
    dtFinal   date;    
    stRetorno varchar:='';
    inAnos   integer:=0;
    inMeses  integer:=0;
    inDias   integer:=0;
BEGIN
    dtInicial := dtInicialPar;
    dtFinal := dtFinalPar;

    WHILE dtInicial < dtFinal LOOP
        inAnos := inAnos + 1;
        dtInicial := (to_char(dtInicial,'yyyy')::integer+1||'-'||to_char(dtInicial,'mm-dd'))::date;        
    END LOOP;

    IF dtInicial > dtFinal THEN
        inAnos := inAnos - 1;
        dtInicial := (to_char(dtInicial,'yyyy')::integer-1||'-'||to_char(dtInicial,'mm-dd'))::date;
    END IF;    
        
    WHILE dtInicial < dtFinal LOOP    
        inMeses := inMeses + 1;
        dtInicial := adiciona_meses(dtInicial,1);
    END LOOP;

    IF dtInicial > dtFinal THEN
        inMeses := inMeses - 1;
        --dtInicial := (to_char(dtInicial,'yyyy')||'-'||to_char(dtInicial,'mm')::integer-1||'-'||to_char(dtInicial,'dd'))::date;
        dtInicial := remove_meses(dtInicial,1);
    END IF;
    
    WHILE dtInicial < dtFinal LOOP    
        inDias := inDias + 1;
        dtInicial := dtInicial+1;
    END LOOP;
    
    stRetorno := inAnos||' ano(s) '||inMeses||' mes(es) e '||inDias||' dia(s)';
    return stRetorno;
END;
$$ LANGUAGE 'plpgsql';
