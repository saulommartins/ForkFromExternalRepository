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
    * Arquivo JavaScript
    * Data de Criação   : 07/10/2005


    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @ignore
    
    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2007-08-30 12:57:58 -0300 (Qui, 30 Ago 2007) $
    
    * Casos de uso: uc-02.04.06

*/

/*
$Log$
Revision 1.3  2007/08/30 15:57:58  cako
Bug#10008#

Revision 1.2  2006/07/05 20:39:03  cleisson
Adicionada tag Log aos arquivos

*/
?>
<script type="text/javascript">

function buscaDado( stTipoBusca ){
    var stAction = document.frm.action;
    var stTarget = document.frm.target;
    var stCtrl   = document.frm.stCtrl.value;
    document.frm.stCtrl.value = stTipoBusca;
    document.frm.target = 'oculto';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTarget;
    document.frm.stCtrl.value = stCtrl;
}

function selecionarTodos(){
    if(document.frm.boTodos.checked==true){
        for (i=0;i<document.frm.elements.length;i++) {
            if(document.frm.elements[i].type == "checkbox" && document.frm.elements[i].name.substring( 0, 9 ) == "boLiberar" ) {
                if(document.frm.elements[i].checked == false){
                    document.frm.elements[i].checked=1;
                }
            }
        }
    }else{
        for (i=0;i<document.frm.elements.length;i++)
            if(document.frm.elements[i].type == "checkbox" && document.frm.elements[i].name.substring( 0, 9 ) == "boLiberar")
                if(document.frm.elements[i].checked == true){
                    document.frm.elements[i].checked=0;
                }
    }
}

</script>
                
