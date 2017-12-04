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
    * Arquivo JS - Notas Explicativas
    * Data de Criação   : 03/09/2007


    * @author Analista      : Gelson Gonçalves 
    * @author Desenvolvedor : Rodrigo S. Rodrigues

    * @ignore
    
    * Casos de uso: uc-02.02.34
*/

?>

<script type="text/javascript">

function incluirCadastro(prm){
    var erro     = new Boolean(false);
    var mensagem = new String();
    var campo    = new String();
    var cmp      = new Number(0);
    
    document.forms[0].stCtrl.value = prm;

    campo  = document.frm.stNomAcao.value;

    if(campo == "Selecione" || campo == ''){
        mensagem += "@Selecione um anexo!";
        erro = true;
    }

    stDataInicial = document.frm.stDtInicial.value;
    DiaInicial = stDataInicial.substr(0,2);
    MesInicial = stDataInicial.substr(3,2);
    AnoInicial = stDataInicial.substr(6,4);

    var dataInicial = AnoInicial+""+MesInicial+""+DiaInicial;

    stDataFinal = document.frm.stDtFinal.value;
    DiaFinal = stDataFinal.substr(0,2);
    MesFinal = stDataFinal.substr(3,2);
    AnoFinal = stDataFinal.substr(6,4);

    var dataFinal = AnoFinal+""+MesFinal+""+DiaFinal;

    if (dataInicial == ''){
        mensagem += "@Informe a data Inicial!";
        erro = true;
    }

    if (dataFinal == ''){
        mensagem += "@Informe a data Final!";
        erro = true;
    }

    if (dataInicial > dataFinal){
        mensagem += "@A Data Inicial não pode ser maior que a Data Final!";
        erro = true;
    }

    campoNotaExplicativa  = document.frm.stNotaExplicativa.value;
    campoNotaExplicativa=campoNotaExplicativa.split("\n").join("\\n");

    if(campoNotaExplicativa == ''){
        mensagem += "@Escreva a nota explicativa";
        erro = true;
    }

    if(erro!=true){
        while(cmp in document.forms[0].elements){
            if(document.forms[0].elements[cmp].type!="radio"){
                campo+="&"+document.forms[0].elements[cmp].name+"="+document.forms[0].elements[cmp].value;
            }else{
                if(document.forms[0].elements[cmp].checked==true){
                    campo+="&"+document.forms[0].elements[cmp].name+"="+document.forms[0].elements[cmp].value;
                }
            }
            cmp++;
        }
        ajaxJavaScript('<?=$pgOcul."?".Sessao::getId()?>'+campo+'&stNotaExplicativaQuebra='+campoNotaExplicativa,document.frm.stCtrl.value);
    }else{
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId()?>');
    }
}

function alterarCadastro(prm,id){
    var erro     = new Boolean(false);
    var campo    = new String();
    var mensagem = new String();
    document.forms[0].stCtrl.value = prm;
    
    campo  = document.frm.stNotaExplicativa.value;
    campo=campo.split("\n").join("\\n");

    if(campo == ''){
        mensagem += "@Escreva uma nota explicativa para a alteração!";
        erro = true;
    }

    stDataInicial = document.frm.stDtInicial.value;
    DiaInicial = stDataInicial.substr(0,2);
    MesInicial = stDataInicial.substr(3,2);
    AnoInicial = stDataInicial.substr(6,4);

    var dataInicial = AnoInicial+""+MesInicial+""+DiaInicial;

    stDataFinal = document.frm.stDtFinal.value;
    DiaFinal = stDataFinal.substr(0,2);
    MesFinal = stDataFinal.substr(3,2);
    AnoFinal = stDataFinal.substr(6,4);

    var dataFinal = AnoFinal+""+MesFinal+""+DiaFinal;

    if (dataInicial == ''){
        mensagem += "@Informe a data Inicial!";
        erro = true;
    }

    if (dataFinal == ''){
        mensagem += "@Informe a data Final!";
        erro = true;
    }

    if (dataInicial > dataFinal){
        mensagem += "@A Data Inicial não pode ser maior que a Data Final!";
        erro = true;
    }

    stCtrl = document.frm.stCtrl.value;
    if(erro!=true){
        ajaxJavaScript('<?=$pgOcul."?".Sessao::getId().'&id=';?>'+id+'&stNotaExplicativa='+campo+'&stDtInicial='+stDataInicial+'&stDtFinal='+stDataFinal,document.frm.stCtrl.value);
    }else{
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId()?>');
    }
}


function consultarCadastro(prm,id){
    var campo    = new String();
    document.forms[0].stCtrl.value = prm;
    
    campo  = document.frm.stNotaExplicativa.value;
    ajaxJavaScript('<?=$pgOcul."?".Sessao::getId().'&id=';?>'+id+'&stNotaExplicativa='+campo,document.frm.stCtrl.value);
    document.frm.action                                       = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    document.frm.stCtrl.value                                 = "";
    document.frm.stNomAcao.value                              = "";
    document.frm.stNotaExplicativa.value                      = "";
    document.frm.stDtInicial.value                            = "";
    document.frm.stDtFinal.value                              = "";
    document.getElementById('incluir').value                  = 'Incluir';
    document.getElementById('incluir').setAttribute('onclick','JavaScript:incluirCadastro(\'incluirListaCadastro\',true,\'false\');');
    document.getElementById("stNomAcao").disabled             = false;
    document.getElementById("stNotaExplicativa").disabled     = false;
    document.getElementById("stDtInicial").disabled           = false;
    document.getElementById("stDtFinal").disabled             = false;
    document.getElementById("limpar").disabled                = false;
}
  

function consultarItem(inIndice){
    ajaxJavaScript('<?=$pgOcul."?".Sessao::getId()?>&id='+inIndice,'consultarItem');
    
}


function alterarItem(inIndice){
    ajaxJavaScript('<?=$pgOcul."?".Sessao::getId()?>&id='+inIndice,'alterarItem');
    
}


function excluirItemLista(inIndice){
    ajaxJavaScript('<?=$pgOcul."?".Sessao::getId()?>&id='+inIndice,'excluirItemLista');
}


function limparCadastro() {
    document.frm.stNomAcao.value                    = '';
    document.frm.stNotaExplicativa.value            = '';
    document.frm.stDtInicial.value                  = '';
    document.frm.stDtFinal.value                    = '';
    document.getElementById('incluir').value        = 'Incluir';
    document.getElementById('incluir').setAttribute('onclick','JavaScript:incluirCadastro(\'incluirListaCadastro\',true,\'false\');');
    document.getElementById("stNomAcao").disabled   = false;
}
  
</script>
