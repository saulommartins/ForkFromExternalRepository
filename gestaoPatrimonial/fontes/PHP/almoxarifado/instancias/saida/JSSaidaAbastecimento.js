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
<script>
  
    function Contador(textarea, limit){
    
        var text = textarea.value; 
        var textlength = text.length;
    
        if(textlength > limit){
            textarea.value = text.substr(0,limit);
            return false;
        } else {
            return true;
        }
    }
 
 
    // Função que valida se o almoxarifado está setado, caso sim, busca o ítem.
   function validaAlmoxarifado()
   {
       if (jQuery('#inCodAlmoxarifado').val() == ''){
            alertaAviso('Selecione o Almoxarifado.','form','erro','<?=Sessao::getId();?>', '../');
            jQuery('#inSaldo').html('');
            jQuery('#nuQuantidade').html('');
            limpaSelect(document.frm.inCodMarca,1);
            limpaSelect(document.frm.inCodCentroCusto,1);
            return false;
       }else
           return true;
   }

    function validaItem()
    {
        if (jQuery('#inCodItem').val() == ''){
            return false;
        } else {
            return true;
        }
    }

</script>