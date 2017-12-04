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
* Página de javascript do vale transporte
* Data de Criação: 11/07/2005


* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @ignore

$Revision: 30566 $
$Name$
$Author: vandre $
$Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

* Casos de uso: uc-04.06.03
*/

/*
$Log$
Revision 1.3  2006/08/08 17:32:35  vandre
Adicionada tag log.

*/
?>
<script type="text/javascript">

function buscaCGM(BuscaValor){
    var d = document.frm;
    d.stCtrl.value = BuscaValor;
    d.target = "oculto";
    d.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    d.submit();
    d.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function buscaValor(tipoBusca){
    var d = document.frm;
    d.stCtrl.value = tipoBusca;
    d.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    d.submit();
    d.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function buscaDado(BuscaValor){
    document.frm.stCtrl.value = BuscaValor;
    var stTarget = document.frm.target;
    document.frm.target = 'oculto';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    document.frm.target = stTarget;
}

function preencheMunicipioIncluir(){
    var d = document.frm;
    d.stCtrl.value = 'preencheMunicipioIncluir';
    d.target = "oculto";
    d.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    d.submit();
    d.target = "";
    d.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function preencheMunicipioOrigem(){
    var d = document.frm;
    d.stCtrl.value = 'preencheMunicipioOrigem';
    d.target = "oculto";
    d.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    d.submit();
    d.target = "";
    d.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function preencheMunicipioDestino(){
    var d = document.frm;
    d.stCtrl.value = 'preencheMunicipioDestino';
    d.target = "oculto";
    d.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    d.submit();
    d.target = "oculto";
    d.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}
</script>
