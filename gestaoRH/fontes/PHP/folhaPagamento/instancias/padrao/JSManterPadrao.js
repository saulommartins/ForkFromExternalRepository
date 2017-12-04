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
    * Página de JavaScrit
    * Data de Criação   : ???


    * @author Analista: ???
    * @author Desenvolvedor: ???

    * @ignore

    $Revision: 30840 $
    $Name$
    $Author: souzadl $
    $Date: 2007-07-04 12:26:42 -0300 (Qua, 04 Jul 2007) $

    * Casos de uso :uc-04.05.05

*/

/*
$Log$
Revision 1.10  2007/07/04 15:26:42  souzadl
Bug #9557#

Revision 1.9  2006/08/08 17:43:20  vandre
Adicionada tag log.

*/
?>
<script type="text/javascript">
function validaProgressao(){
    var mensagem = '';
    var stCampo = trim(document.frm.stDescricaoNivel.value);
    var stCampo2 = document.frm.stMeses.value;
    var stCampo3 = document.frm.stValorCorrecao.value;
    var stCampo4 = document.frm.stPercentual.value;


    if ( !stCampo  )  { mensagem += '@Descricao da progressão inválida','form','erro','<?=Sessao::getId();?>'; }
  
    re = "-";
    if(stCampo3.search(re) != '-1'){mensagem += '@Valor da correção negativo inválido','form','erro','<?=Sessao::getId();?>';};
    stCampo3 = stCampo3.replace(',','');
    stCampo3 = parseFloat(stCampo3);
    if ( stCampo3  == '0' || !stCampo3 ) { mensagem += '@Valor da correção  inválido','form','erro','<?=Sessao::getId();?>'; }
    
    if(stCampo4.search(re) != '-1'){ mensagem += '@Percentual negativo inválido ','form','erro','<?=Sessao::getId();?>'; };
    stCampo4 = stCampo4.replace(',','');
    stCampo4 = parseFloat(stCampo4);
    if ( stCampo4  == '0' || !stCampo4 )  { mensagem += '@Percentual inválido','form','erro','<?=Sessao::getId();?>'; }


    if(stCampo2.search(re) != '-1'){ mensagem += '@Mês negativo inválido ','form','erro','<?=Sessao::getId();?>'; };
    stCampo2 = parseFloat(stCampo2);
    if ( !stCampo2 )  { mensagem += '@Mês para incidência  inválido','form','erro','<?=Sessao::getId();?>'; }
 
   return mensagem;
}

function buscaValor(stControle){
    document.frm.stCtrl.value = stControle;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function excluiDado(stControle, inId){
    document.frm.stCtrl.value = stControle;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inId=' + inId;
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function alteraDado(stControle, inId, descricao, percentual, valor, qtdmeses){
    document.frm.inIdProgressao.value    = inId;
    document.frm.stDescricaoNivel.value = descricao;
    document.frm.stValorCorrecao.value  = percentual;
    document.frm.stPercentual.value     = valor;
    document.frm.stMeses.value          = qtdmeses;
}
function IncluiFaixa(){
    var mensagem = "";
    if(document.frm.inIdProgressao.value!=''){
        mensagem += "@Alteração em processo! Clique em alterar para concluir ou limpar para cancelar!"
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
        return false;
    }else{
        mensagem += validaProgressao();

        if ( mensagem == '' ){
            buscaValor('MontaFaixa');
        } else {
            alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');
            return false;
        }
    }
}

function AlteraFaixa(){
    var mensagem = "";
    if(document.frm.inIdProgressao.value==''){
        mensagem += "@Inclusão em processo! Clique em incluir para concluir ou limpar para cancelar!"
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
        return false;
    }else{
        mensagem += validaProgressao();

        if ( mensagem == '' ){
            buscaValor('MontaFaixaAlteracao');
        } else {
            alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');
            return false;
        }
    }
}
/*************************************************/
function limparProgressao () {
    document.frm.inIdProgressao.value = '';
    document.frm.stDescricaoNivel.value = '';
    document.frm.stValorCorrecao.value  = '';
    document.frm.stPercentual.value     = '';
    document.frm.stMeses.value          = '';
    document.frm.stDescricaoNivel.focus ();
}

function modificaDado(stControle, inId){
    document.frm.stCtrl.value = stControle;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inId=' + inId;
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}


function buscaValor(tipoBusca){
    document.frm.stCtrl.value = tipoBusca;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>'
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function validaPercentual( campo ){
    valor = campo.value.replace(".","");
    valor = valor.replace(",",".");
    if( parseFloat(valor) > 100 ){
        campo.value = "";
        alertaAviso('@Campo Percentual de Correção deve ser de 0 - 100!','form','erro','<?=Sessao::getId();?>');
    }
}

function calculaCorrecao(){
    valor   = document.frm.stValorPadrao.value.replace('.','') ;
    valor   = valor.replace(',','.') ;

    porcent = document.frm.stPercentual.value.replace('.','');
    porcent = porcent.replace(',','.');

    document.frm.stValorCorrecao.value = 0;

    if ( valor.length  != 0 ) {
        if ( porcent.length != 0) {            
           if ( porcent  != 0){

                 valor =   (valor * (1+ porcent/100))   ;
                 valor = '' + valor; // transformando a variavel em string pra poder usar o replace
    
                 if ( valor == 'NaN' ){
                     // se a condição acima for verdadeira o percentual não pode ser calculado pq o valor é muito alto !!!!!!!!!!!!!
                     valor = 0;
                     alertaAviso('@Valor inválido!','form','erro','<?=Sessao::getId();?>');   
                 }else {

                     valor = valor.replace('.',',') ;
                 }   
                
                 document.frm.stValorCorrecao.value = valor ; 
                 
                 floatDecimal( document.frm.stValorCorrecao  , '2', 'onBlur' ) ; // aplicando a mascara no campo
                 //if ( document.frm.stValorCorrecao.value == ',00'
           } 
        }
    }


}


function Limpar () {
    document.frm.stCtrl.value = 'limparFormulario';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>'
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    document.frm.stDescricao.focus ();
}

function Cancelar(){
<?php
$link =Sessao::read("link");
$stLink = "&pg=".$link["pg"]."&pos=".$link["pos"];
?>
    document.frm.target = "telaPrincipal";
    document.frm.action = "<?=$pgList.'?'.Sessao::getId().$stLink;?>";
    document.frm.submit();
}


function validaHoraMensal(valorHoraMensal){
    var mensagem = '';
    var d = document.frm;
    var valor = valorHoraMensal;

    valor = valor.replace('.','');
    valor = valor.replace(',','.');

    if( valor <=0 ){
        mensagem += "@Campo Valor Hora Mensal inválido.( " + d.stHorasMensais.value + " )";
        d.stHorasMensais.value = '';
        d.stHorasMensais.focus();
    }
    if(mensagem != ''){
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');
    }
}

function validaHoraSemanal(valorHoraSemanal){
    var mensagem = '';
    var d = document.frm;
    var valor = valorHoraSemanal;

    valor = valor.replace('.','');
    valor = valor.replace(',','.');

    if( valor <=0 ){
        mensagem += "@Campo Valor Hora Semanal inválido.( " + d.stHorasMensais.value + " )";
        d.stHorasSemanais.value = '';
        d.stHorasSemanais.focus();
    }
    if(mensagem != ''){
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');
    }
}

function validaValorPadrao(valorPadrao){
    var mensagem = '';
    var d = document.frm;
    var valor = valorPadrao;

    valor = valor.replace('.','');
    valor = valor.replace(',','.');

    if( valor <=0 ){
        mensagem += "@Campo Valor do Padrão inválido.( " + d.stValorPadrao.value + " )";
        d.stValorPadrao.value = '';
        d.stValorPadrao.focus();
    }
    if(mensagem != ''){
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');
    }
}


</script>
