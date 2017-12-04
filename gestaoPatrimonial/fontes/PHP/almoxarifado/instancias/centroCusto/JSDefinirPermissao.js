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
    * Página de Oculto de Centro de Custo
    * Data de Criação   : 22/11/2005


    * @author Analista      : Diego
    * @author Desenvolvedor : Rodrigo Schreiner

    * Casos de uso: uc-03.03.07
*/

/*
$Log$
Revision 1.4  2006/07/06 14:00:30  diego
Retirada tag de log com erro.

Revision 1.3  2006/07/06 12:09:52  diego


*/
?>

<script type="text/javascript">

function buscaDado( BuscaDado , codCGM){
 var stTarget              = document.frm.target;
 document.frm.stCtrl.value = BuscaDado;
 document.frm.codCGM.value = codCGM;
 document.frm.target       = "oculto";
 document.frm.action       = '<?=$pgOcul;?>?<?=Sessao::getId();?>' ;
 document.frm.submit();
 document.frm.action       = '<?=$pgProc?>?<?=Sessao::getId();?>';
}


function Limpar(){
    document.frm.reset();
    x = document.frm.disponiveis.options.length-1 ;
    while (x >= 0){
        document.frm.disponiveis.options[x] = null;
        x--;
    }

    x = document.frm.selecionados.options.length-1 ;
    while (x >= 0){
        document.frm.selecionados.options[x] = null;
        x--;
    }

}

function selecionarTodos(){
    var cont = 0;
    var campoT = document.frm.boTodos.checked;
    if (campoT == true){
        while(cont < document.frm.elements.length){
            if( (document.frm.elements[cont].type == 'checkbox') && ( document.frm.elements[cont].name != 'boTodos') && (document.frm.elements[cont].disabled != true)){
                document.frm.elements[cont].checked = true;
            }
            cont++;
        }
    }
    else{
        while(cont < document.frm.elements.length){
            if( (document.frm.elements[cont].type == 'checkbox') && ( document.frm.elements[cont].name != 'boTodos') && (document.frm.elements[cont].disabled != true) ){
                document.frm.elements[cont].checked = false;
            }
            cont++;
        }
    }
}
</script>
