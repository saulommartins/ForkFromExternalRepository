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
    * Data de Criação   : 18/10/2005


    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Er Galvão Abbott

    * @ignore

    * Casos de uso: uc-03.03.05

    $Id: JSManterClassificacao.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $

*/

?>

<script type="text/javascript">

var stJSMascara = '';

function goOculto(stControle, listaInclusao, preencheNivel)
{
    document.frm.stCtrl.value = stControle;
        document.frm.stListaInclusao.value = listaInclusao;

    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&preencheNivel='+preencheNivel;
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>&preencheNivel='+preencheNivel;
}

function goOcultoLista(stControle, listaInclusao)
{
    document.frm.stCtrl.value = stControle;

   if (listaInclusao)
    {
        document.frm.stListaInclusao.value = listaInclusao;
    }
    document.frm.target = 'oculto';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = 'telaPrincipal';
    document.frm.action = '<?=$pgList;?>?<?=Sessao::getId();?>';
}

function mudaCatalogo(combo, preencheNivel)
{
    if (combo.value != '')
    {
            if (document.getElementById('spnListaClassificacao'))
            {
                document.getElementById('spnListaClassificacao').innerHTML = '';
            }
            
            goOculto('MontaNiveisCombo','',preencheNivel);
    }
    else
    {
        limpaSelect(document.frm.inCodNivel ,0);
        document.frm.inCodNivel.options[0] = new Option;
        document.frm.inCodNivel.options[0].value = '';
        document.frm.inCodNivel.options[0].text = 'Selecione';
        document.frm.inCodNivel.options[0].selected = 'true';
        document.getElementById('spnCodEstrutural').innerHTML = '&nbsp;';
        document.getElementById('spnListaAtributos').innerHTML = '&nbsp;';

    }
}

function mudaCatalogo2(combo)
{
    if (!combo.options[0].selected)
    {
            if (document.getElementById('spnListaClassificacao'))
            {
                document.getElementById('spnListaClassificacao').innerHTML = '';
            }
            
            goOcultoLista('MontaNiveisCombo2');
    }
    else
    {
        limpaSelect(document.frm.inCodNivel ,0);
        document.frm.inCodNivel.options[0] = new Option;
        document.frm.inCodNivel.options[0].value = '';
        document.frm.inCodNivel.options[0].text = 'Selecione';

        document.frm.reset();
    }
}

function mudaCatalogoTxt()
{
            if (document.getElementById('spnListaClassificacao'))
            {
                document.getElementById('spnListaClassificacao').innerHTML = '';
            }
            
            goOcultoLista('MontaNiveisCombo2');
}
function verificaNivel(combo)
{
    if (!combo.options[0].selected)
    {
        if (combo.options[1].selected)
        {
            if (document.getElementById('spnListaClassificacao'))
            {
                document.getElementById('spnListaClassificacao').innerHTML = '';
            }

           if (combo.options[ (combo.length - 1) ].selected)
            {
                var listaInclusao = true;
            }
            else
            {
                var listaInclusao = false;
                if (document.getElementById('spnListaAtributos'))
                {
                   document.getElementById('spnListaAtributos').innerHTML = '';
                   document.frm.stValida.value = '';
                }
            } 

            goOculto('MontaListaClassificacao', listaInclusao);

        }
        else
        {
            if (combo.options[ (combo.length - 1) ].selected)
            {
                var listaInclusao = true;
            }
            else
            {
                var listaInclusao = false;
                if (document.getElementById('spnListaAtributos'))
                {
                   document.getElementById('spnListaAtributos').innerHTML = '';
                   document.frm.stValida.value = '';
                }

            }   
            
            goOculto('MontaListaClassificacao', listaInclusao);
        }
    }else{
        document.getElementById('spnListaClassificacao').innerHTML = '';
        document.getElementById('spnListaAtributos').innerHTML = '';
        document.frm.stValida.value = '';
    }
}

function setCurrNivel(combo)
{
    if (!combo.options[0].selected)
    {
        arrName = combo.name.split('_');

        document.frm.inCurrCombo.value = parseInt(arrName[1], 10);

        document.frm.inNextCombo.value = (parseInt(arrName[1], 10) + 1);
    }
}


function addNivel(combo)
{
    if (!combo.options[0].selected)
    {
        arrName = combo.name.split('_');

        document.frm.inCurrCombo.value = parseInt(arrName[1], 10);
        document.frm.inNextCombo.value = (parseInt(arrName[1], 10) + 1);
        goOculto('MontaClassificacaoCombo');
    }
    else
    {/*
        for (f = 0; f < document.frm.elements.length; f++)
        {
            if (document.frm.elements[f].name == combo.name)
            {
                for (g = f; g < document.frm.elements.length; g++)
                {
                    if (document.frm.elements[g].type == 'select-one')
                    {
                        eval("limpaSelect(" + document.frm.elements[g].name + ", 0);");
                    }
                }
            }
        }*/
    }
}


function buscaCadastro(){
    var stAction = document.frm.action;
    var stTarget = document.frm.target;
    document.frm.stCtrl.value = "MontaCadastro";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.target = 'oculto';
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTarget;
}


function AdicionaValores(stControle)
{
/*var stDebug = '';

for(f = 0; f < document.frm.elements.length; f++)
{
    stDebug += document.frm.elements[f].name + ' = ' + document.frm.elements[f].value + '\n';
}


alert(stDebug);
*/

    var stMensagem = '';

    var stDescricaoCatalogo = document.frm.stDescricaoCatalogo.value;
    var stMascara = document.frm.stMascara.value;
    var stDescricaoNivel = document.frm.stDescricaoNivel.value;
    
    if (stDescricaoCatalogo.length == 0) 
    {
        stMensagem += "@Campo descrição de catálogo inválido!( )";
    }

    if (stMascara.length == 0) 
    {
        stMensagem += "@Campo máscara inválido!( )";
    }

    if (stDescricaoNivel.length == 0) 
    {
        stMensagem += "@Campo descrição de nível inválido!( )";
    }

    if ( trim (stDescricaoCatalogo) == "" || trim(stMascara) == "" || trim(stDescricaoNivel) == "") 
    {
        stMensagem += "@Impossível inserir valores em branco!";
    }
    
    if (stMensagem == '')
    {
        document.frm.btnAlterar.disabled = true;
        goOculto(stControle);
    } 
    else 
    {
        alertaAviso(stMensagem,'form','erro','<?=Sessao::getId();?>');
        return false;
    }
}

function modificaDado(stControle, inId){
    document.frm.stCtrl.value = stControle;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inId=' + inId;
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function limpaValores(){
    document.frm.stMascara.value = '';
    document.frm.stDescricaoNivel.value  = '';
}

function limpaDescricao(){
    document.frm.stDescricaoNivel.value  = '';
    passaItem('document.frm.inCodAtributosSelecionados','document.frm.inCodAtributosDisponiveis','tudo');
}

function preencheProxCombo( inPosicao  ){
    document.frm.stCtrl.value = 'preencheProxCombo';
    var target = document.frm.target;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inPosicao='+inPosicao;
    document.frm.submit();
    document.frm.action = '<?=$pgProx;?>?<?=Sessao::getId();?>';
    document.frm.target = target;
}

function preencheCombos(){
    document.frm.stCtrl.value = 'preencheCombos';
    var target = document.frm.target;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProx;?>?<?=Sessao::getId();?>';
    document.frm.target = target;
}

function mudaCombo(txtfield,combo)
{
    var i = 0;
    var avisoNaoExiste = true;

    for(i=0; i<combo.length;i++) {

        if(combo.options[i].value == txtfield.value) {
            combo.options[i].selected = 'true';
            avisoNaoExiste = false;
            mudaCatalogo(combo, 'false')
        }    
    }
}

</script>
