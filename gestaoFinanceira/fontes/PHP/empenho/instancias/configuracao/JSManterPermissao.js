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
    * Arquivo JavaScript utilizado na Permissão 
    * Data de Criação : 04/12/2004


    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Gelson W. Gonçalves

    * @ignore
    
    $Revision: 30668 $
    $Name$
    $Author: vitor $
    $Date: 2007-04-05 15:11:46 -0300 (Qui, 05 Abr 2007) $

    * Casos de uso: uc-02.03.01
*/

/*
$Log$
Revision 1.5  2007/04/05 18:10:31  vitor
8264

Revision 1.4  2006/07/05 20:47:34  cleisson
Adicionada tag Log aos arquivos

*/
?>
<script type="text/javascript">
function buscaDado( BuscaDado ){
    var stTarget = document.frm.target;
    document.frm.stCtrl.value = BuscaDado;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>' ;
    document.frm.submit();
    document.frm.target = stTarget;
    document.frm.action = '<?=$pgProximo;?>?<?=Sessao::getId();?>';
}
function selecionarTodos(){
if(document.frm.boTodos.checked==true){
         for (i=0;i<document.frm.elements.length;i++) {
             if(document.frm.elements[i].type == 'checkbox' && document.frm.elements[i].name != 'boTodos' && document.frm.elements[i].checked == false ) {
                 document.frm.elements[i].checked =1;
                 document.frm.boTodos.checked=1;
                 var x = document.frm.elements[i].name;
             }
        }
    }
    else {
            for (i=0;i<document.frm.elements.length;i++) {
            if(document.frm.elements[i].type == 'checkbox' && document.frm.elements[i].name != 'boTodos' && document.frm.elements[i].checked == true ) {
                document.frm.elements[i].checked =0;
                document.frm.boTodos.checked=0;
                var x = document.frm.elements[i].name;
             }
         }
     }
}
</script>
