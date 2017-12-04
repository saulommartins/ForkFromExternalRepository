<script type="text/javascript">
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
</script>
<?php
/*    * Página de Filtro de Feriado
    * Data de Criação   : 05/04/2005


    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Vandré Miguel Ramos

    * @ignore

    $Revision: 30547 $
    $Name$
    $Author: melo $
    $Date: 2007-06-29 12:44:55 -0300 (Sex, 29 Jun 2007) $

    * Casos de uso :uc-04.02.03
*/


/*
$Log$
Revision 1.3  2007/06/29 15:44:50  melo
Bug #9524#

Revision 1.2  2006/08/08 17:38:47  vandre
Adicionada tag log.

*/
?>

<script type="text/javascript">
function insere(inCodigo,stDescricao){
    window.opener.parent.frames['telaPrincipal'].document.getElementById('stDescricao').innerHTML = stDescricao; 
    window.opener.parent.frames['telaPrincipal'].document.frm.inCodCalendar.value = inCodigo; 
    window.opener.parent.frames['telaPrincipal'].document.frm.inCodCalendar.focus(); 
    window.close();            
}
</script>
