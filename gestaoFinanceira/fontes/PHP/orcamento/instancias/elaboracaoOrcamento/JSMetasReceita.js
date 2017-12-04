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
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $
    
    * Casos de uso: uc-02.01.06
*/

/*
$Log$
Revision 1.3  2006/07/05 20:43:03  cleisson
Adicionada tag Log aos arquivos

*/
?>
<script type="text/javascript">

function Cancelar(){
    document.frm.target = 'telaPrincipal';
    window.location = '<?=$pgList;?>?<?=Sessao::getId();?>&stAcao=<?=$stAcao;?>&pg=<?=$_GET["pg"]?>&pos=<?=$_GET["pos"]?>';
}

function somatorio( inQtdLinhas, inQtdColunas, inLinhaCorrente, inColunaCorrente, stTipoSoma, stNomCelula, stNomCampoTotal ){
    var i;
    var total = 0;
    
    if ( stTipoSoma == "l" ){
        for ( i = 0; i < inQtdColunas; i++ ){
            var campoDin = "document.frm." + stNomCelula + "_" + i + "_" + inLinhaCorrente + ".value";
            total2 = eval(campoDin);
            totalR = total2.replace( "," , "." );
//            totalZ = .replace( "" , "0" );
//            alert(totalZ);
            total = total + parseFloat(totalR);
        }
    }else{
        for ( i = 0; i < inQtdLinhas; i++ ){
            var campoDin = "document.frm." + stNomCelula + "_" + inColunaCorrente + "_" + i + ".value";
            total = total + eval(campoDin);
        }
    }
    
//    obCampoTotal = eval( "document.frm." + stNomCampoTotal + "_" + inLinhaCorrente +".value");
    obCampoTotal = total;
//    alert(obCampoTotal);
    
    
    
    return obCampoTotal;
    
}

function validaRequest(){    
    jQuery("input[name^='inCelula']").each(function(){            
        if (!jQuery(this).val() || jQuery(this).val() == '0.00' || jQuery(this).val() == '0,00' || jQuery(this).val() == 0.00){
            jQuery(this).attr('disabled', 'disabled');
        }
    });
}

</script>
