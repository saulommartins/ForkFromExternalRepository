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
    * Arquivo JavaScript - Entidade
    * Data de Criação   : 20/07/2004


    * @author Desenvolvedor: Roberto Pawelski Rodrigues

    * @ignore
    
    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2007-02-27 11:02:24 -0300 (Ter, 27 Fev 2007) $
    
    * Casos de uso: uc-02.01.02
*/

/*
$Log$
Revision 1.10  2007/02/27 13:59:59  luciano
#8284#

Revision 1.9  2006/07/05 20:42:39  cleisson
Adicionada tag Log aos arquivos

*/
?>
<script type="text/javascript">

function buscaCGM(BuscaValor){
    document.frm.stCtrl.value = BuscaValor;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function buscaCGM_Filtro(BuscaValor){
    document.frm.stCtrl.value = BuscaValor;
    document.frm.target = 'oculto';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = 'telaPrincipal';
    document.frm.action = '<?=$pgList;?>?<?=Sessao::getId();?>';
    
}

function Cancelar(){
    document.frm.target = 'telaPrincipal';
    window.location = '<?=$pgList;?>?<?=Sessao::getId();?>&stAcao=<?=$stAcao;?>&pg=<?=$_GET["pg"]?>&pos=<?=$_GET["pos"]?>';
}

function validaForm(){
      var erro = false;
      var mensagem = "";
      if( trim(document.frm.inNumCGM.value) == "" ){
         erro = true;
         mensagem += "@Campo Entidade inválido!()";
     }
     if( trim(document.getElementById('campoInner').innerHTML) == "&nbsp;" ){
         erro = true;
         mensagem += "@Campo Descrição de Entidade inválido!()";
     }
     if( trim(document.frm.inCodigoResponsavel.value) == "" ){
         erro = true;
         mensagem += "@Campo Responsável inválido!()";
     }
     if( trim(document.getElementById('campoInner2').innerHTML) == "&nbsp;" ){
         erro = true;
         mensagem += "@Campo Descrição de Responsável inválido!()";
     }
     if( trim(document.frm.inCodigoResponsavelTecnico.value) == "" ){
         erro = true;
         mensagem += "@Campo Responsável Técnico inválido!()";
     }
     if( trim(document.getElementById('campoInner3').innerHTML) == "&nbsp;" ){
         erro = true;
         mensagem += "@Campo Descrição de Responsável Técnico inválido!()";
     }

     selecionaTodosSelect(document.frm.inCodigoUsuariosSelecionados);
     var inLength = document.frm.inCodigoUsuariosSelecionados.options.length;

     if( ( inLength == 1 && trim(document.frm.inCodigoUsuariosSelecionados.options[inLength - 1].value) == '') || inLength == 0 ){
         erro = true;
         mensagem += "@Campo Usuários Entidades Selecionados inválido!()";
     }
     if( erro ){ 
          alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
     }
 return !erro;
 }
 function inclui(){
     if( validaForm() ){
        document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
        document.frm.stCtrl.value = 'incluiEntidade';
        document.frm.submit();
        document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
        //document.getElementById("CodigoEntidade").innerHTML= parseInt(document.frm.inCodigoEntidade.value) + 1;
        //document.frm.inCodigoEntidade.value = trim(document.getElementById("CodigoEntidade").innerHTML);
     }
 }

function limpa(){
/*     document.frm.inNumCGM.value= "";
     document.getElementById('campoInner').innerHTML = "&nbsp;";
     document.frm.inCodigoResponsavel.value = "";
     document.getElementById('campoInner2').innerHTML = "&nbsp;";
     document.frm.inCodigoResponsavelTecnico.value = ""; 
     document.getElementById('campoInner3').innerHTML = "&nbsp;";
     document.frm.stArquivoBrasao.value = "";
     passaItem('document.frm.inCodigoUsuariosSelecionados','document.frm.inCodigoUsuariosDisponiveis','tudo');
     */

}
</script>
