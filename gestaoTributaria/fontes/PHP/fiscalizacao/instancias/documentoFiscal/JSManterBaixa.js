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
<?
/**
    * JavaScript para o Formulario de Baixa de Notas Fiscais

    * Data de Criação   : 01/08/2007


    * @author Analista      : Fábio Bertoldi Rodrigues
    * @author Desenvolvedor : Rodrigo D.S.
    * @ignore

	* $Id: JSManterBaixa.js 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.07.04
*/
?>
<script>
function incluirInutilizacao(){
   var nota        = new Number( document.frm.inCodNotaFiscal.value );
   var notaInicial = new Number( document.frm.inNotaInicial.value   );
   var notaFinal   = new Number( document.frm.inNotaFinal.value     );
   var mensagem    = new String();

   var combo = document.frm.cmbCodInutilizacao;

   document.frm.stTipoInutilizacao.value = combo.options[combo.options.selectedIndex].text;
   if(document.frm.inCodInutilizacao.value!=""){
      if((nota>=notaInicial)&&(nota<=notaFinal)){
          montaParametrosGET( 'incluirAtributoNotas', '', true); 
      }else{
          mensagem = "Nota Fiscal inválida.("+nota+")";
          alertaAviso(mensagem,'form','erro','<?=Sessao::getId()?>');
      }
   }else{
          alertaAviso('Campo Tipo de Inutilização inválido!()','form','erro','<?=Sessao::getId()?>');
   }
}  

function Limpar(){
    document.frm.inCodInutilizacao.value = "";
    document.frm.cmbCodInutilizacao.options.selectedIndex=0;
    document.frm.inCodNotaFiscal.value   = ""; 
}

function Cancelar(){
    <?
        $link = Sessao::read( "link" );
        $stLink = "&pg=".$link["pg"]."&pos=".$link["pos"];
    ?>
    document.frm.target = "telaPrincipal";
    document.frm.action = "<?=$pgList.'?'.Sessao::getId();?>";
    document.frm.submit();
}
</script>
