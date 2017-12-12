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
    * Página Javascript - Parâmetros do Arquivo CREDOR
    * Data de Criação   : 11/02/2005


    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Cleisson da Silva Barboza

    * @ignore

    $Revision: 27052 $
    $Name$
    $Autor: $
    $Date: 2007-12-05 17:12:56 +0000 (Qua, 05 Dez 2007) $

    * Casos de uso: uc-02.08.06
*/

/*
$Log$
Revision 1.7  2006/07/05 20:46:25  cleisson
Adicionada tag Log aos arquivos

*/
?>

<script type="text/javascript">

function addCmb(){
    if (document.frm.stPeriodo.value == 't') {
        document.frm.stAno.style.visibility = 'hidden';
        document.frm.reset();
    }
    if (document.frm.stPeriodo.value == 'a') {
        document.frm.stAno.style.visibility = 'visible';
        document.frm.stAno.style.display = 'inline';
    }
}

function buscaDado( BuscaDado ){
    document.frm.stCtrl.value = BuscaDado;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function mudaSelects( valor ){
    for( i=0; i<document.frm.length; i++){
        if( document.frm.elements[i].type == 'select-one' ){
            document.frm.elements[i].selectedIndex = valor;
        }
    }
}

</script>
