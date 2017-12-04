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
    

    * @ignore
    
    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2007-03-05 16:30:19 -0300 (Seg, 05 Mar 2007) $
    
    * Casos de uso: uc-02.01.06
*/

/*
$Log$
Revision 1.4  2007/03/05 19:30:19  cako
Bug #8592#

Revision 1.3  2006/07/05 20:43:03  cleisson
Adicionada tag Log aos arquivos

*/
?>
<script type="text/javascript">

function buscaValor(BuscaValor){
    document.frm.stCtrl.value = BuscaValor;
    document.frm.target = 'oculto';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = 'telaPrincipal';
    document.frm.action = '<?=$pgList;?>?<?=Sessao::getId();?>';
}

function buscaValor_Filtro(BuscaValor){
    document.frm.stCtrl.value = BuscaValor;
    document.frm.target = 'oculto';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = 'telaPrincipal';
    document.frm.action = '<?=$pgList;?>?<?=Sessao::getId();?>';
}

function Cancelar2(){
    document.frm.target = 'telaPrincipal';
    window.location = '<?=$pgFilt;?>?<?=Sessao::getId();?>';
}

function somatorio( campo, numColunas ){
    var arSeiLa = campo.name.split("_");
    var campoTotal = "total_" + arSeiLa[2];
    var total = 0;
    for( var i = 1; i <= numColunas; i++ ){
       var campoDin =  arSeiLa[0] + '_' + i + '_' + arSeiLa[2].value;
       total = total + campoDin;
    }
    return document.frm.campoTotal.value = total;
}

function validaRequest(){    
    jQuery("input[name^='inCelula']").each(function(){            
        if (!jQuery(this).val() || jQuery(this).val() == '0.00' || jQuery(this).val() == '0,00' || jQuery(this).val() == 0.00){
            jQuery(this).attr('disabled', 'disabled');
        }
    });
}

</script>
