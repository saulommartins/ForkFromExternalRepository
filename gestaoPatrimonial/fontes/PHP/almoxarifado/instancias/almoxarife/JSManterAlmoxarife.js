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
* Página de JavaScript
* Data de Criação   : 07/11/2005


* @author Analista: Diego Barbosa Victoria
* @author Desenvolvedor: Leandro André Zis

* @ignore

* Casos de uso: uc-03.03.02
*/

/*
$Log$
Revision 1.5  2006/07/06 14:00:21  diego
Retirada tag de log com erro.

Revision 1.4  2006/07/06 12:09:52  diego


*/
?>
<script type="text/javascript">

function buscaValor(tipoBusca){
    document.frm.stCtrl.value = tipoBusca;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function selecionaAlmoxarifados(boSelected) {
    i = 0;
    while (eval('document.frm.inCodAlmoxarifado[i]')) {
        eval('document.frm.inCodAlmoxarifado[i].selected = '+boSelected+';');
        i++;
    }
}

function disabilitaCGM(){
    if(document.frm.inCodCGMAlmoxarife){
       document.frm.inCodCGMAlmoxarife.disabled = true;
    }
    x = 0; 
    while(x<document.body.getElementsByTagName('*').length){
       if(document.body.getElementsByTagName('img')[x]){
           if(document.body.getElementsByTagName('img')[x].src.indexOf('botao_popup.png')>=0){
               imagem = document.getElementsByTagName('img')[x].style.display='none';
           }
       }
        x+=1;
    }
}

</script>
