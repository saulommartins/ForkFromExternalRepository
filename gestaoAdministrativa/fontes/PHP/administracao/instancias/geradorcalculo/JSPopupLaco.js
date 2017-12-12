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
* Arquivo de instância para manutenção de funções
* Data de Criação: 25/07/2005


* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3075 $
$Name$
$Author: pablo $
$Date: 2005-11-29 14:45:45 -0200 (Ter, 29 Nov 2005) $

Casos de uso: uc-01.03.95
*/
?>

<script type="text/javascript">
function AdicionaValorVariavel(stControle){
    var mensagem = '';
    var valor   = document.frm.stValor.value;
    var variavel= document.frm.stVariavel.value;
    var stSelecionado = '';
    //valor = valor.replace(' ','');

    if (variavel == 0 && valor.length == 0) {
        mensagem += "@Campo Valor / Variável deve ser preenchido/selecionado!( )";
    }else{
        if(variavel == 0){
            stSelecionado = valor;
        }else{
            stSelecionado = variavel;
        }
    }
    if (mensagem == ''){
        document.frm.stCtrl.value = stControle;
        document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&stSelecionado='+stSelecionado;
        document.frm.submit();
    } else {
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');
        return false;
    }
}
function Adiciona(stSelecionado){
    document.frm.stCtrl.value = 'MontaCondicaoBotoes';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&stSelecionado='+stSelecionado;
    document.frm.submit();
}
function LimpaValorVariavel(){
    document.frm.stValor.value     = '';
    document.frm.stVariavel.options[0].selected=true;
}
function Ok(){
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    document.frm.submit();
}
</script>
