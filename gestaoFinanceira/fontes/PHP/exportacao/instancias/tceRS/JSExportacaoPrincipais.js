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
    * Página JavaScript - Exportação Arquivos Principais

    * Data de Criação   : 31/01/2005


    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Lucas Texeira Stephanou

    * @ignore

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.08.01
*/

/*
$Log$
Revision 1.4  2006/07/05 20:46:25  cleisson
Adicionada tag Log aos arquivos

*/
?>

<script type="text/javascript">
function rd_extra(){ 
/**
* Function rd_extra 
* @ Desc    Inclui o arquivo RD_EXTRA.TXT 
* no select de arquivos disponiveis para exportacao se o periodo selecionado for igual a 6
*/
        var inPer  = document.getElementById("inPeriodo")                                                 
        var selBim = inPer.options[inPer.selectedIndex].value;
        
        if (selBim == 6)
        {
            var o   =new Option("RD_EXTRA.TXT","RD_EXTRA.TXT");
            var os  =document.getElementById("arCodArqDisponiveis").options;                                               
            os[os.length]=o;                                                                           
        }                                                                                                         
        if (selBim < 6)
        {
                var x=document.getElementById("arCodArqDisponiveis")
                if(x.length == 9)
                {
                    x.options[8]=null
                }
        }
}

function Limpar(){
    limpaFormulario();
}
</script>
