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
* Arquivo de instância para manutenção de locais
* Data de Criação: 25/07/2005


* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 30773 $
$Name$
$Author: melo $
$Date: 2007-07-12 17:12:39 -0300 (Qui, 12 Jul 2007) $

Casos de uso: uc-01.05.03
*/
/*
$Log$
Revision 1.3  2007/07/12 20:12:39  melo
Bug #9639#

*/
?>
<script type="text/javascript">

function buscaLogradouro(){
    document.frm.stCtrl.value = 'buscaLogradouro';
    var stTraget = document.frm.target;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    document.frm.target = stTraget;
}

function buscaLogradouroFiltro(){
    document.frm.stCtrl.value = 'buscaLogradouroFiltro';
    var stTraget = document.frm.target;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProx;?>?<?=Sessao::getId();?>';
    document.frm.target = stTraget;
}

function focusIncluir(){
    document.frm.stDescricao.focus();
}
    
function Cancelar(){
<?php
    $link = Sessao::read("link");
    $stLink = "&pg=".$link["pg"]."&pos=".$link["pos"];
?>
    document.frm.target = "telaPrincipal";
    document.frm.action = "<?=$pgList.'?'.Sessao::getId().$stLink;?>";
    document.frm.submit();
}

function Limpar(){
    document.frm.reset();
    document.getElementById("campoInner").innerHTML = "&nbsp;";
}
</script>
