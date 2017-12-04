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
/**
    * Página de Oculto do Registro de Evento de Férias
    * Data de Criação: 19/06/2006


    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 22670 $
    $Name$
    $Author: leandro.zis $
    $Date: 2007-05-17 16:56:30 -0300 (Qui, 17 Mai 2007) $

    * Casos de uso: uc-04.05.53
*/

/*
$Log$
Revision 1.1  2007/05/17 19:56:04  leandro.zis
uc - 01.01.08

Revision 1.3  2007/04/16 19:43:06  souzadl
Bug #9122#

Revision 1.2  2006/08/08 17:42:47  vandre
Adicionada tag log.

*/
?>
<script type="text/javascript">
function limpaFormularioExtra(){
    document.getElementById('stNomCGM').innerHTML = '&nbsp;';
}

function array_search(valorBusca,arrayObjetosCombo){
    for(var i in arrayObjetosCombo){        
        if(arrayObjetosCombo[i].value == valorBusca){return i;}    
    }
    return false;
}

</script>



